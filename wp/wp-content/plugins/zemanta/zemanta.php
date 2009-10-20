<?php
/*
Copyright (c) 2007 - 2009, Zemanta Ltd.
The copyrights to the software code in this file are licensed under the (revised) BSD open source license.

Plugin Name: Zemanta
Plugin URI: http://wordpress.org/extend/plugins/zemanta/
Description: Contextual suggestions of links, pictures, related content and SEO tags that makes your blogging fun and efficient.
Version: 0.6.3
Author: Zemanta Ltd.
Author URI: http://www.zemanta.com/
*/

function zem_is_pro() {
	if (defined("ZEMANTA_API_KEY") && defined("ZEMANTA_SECRET")) return true;
	if (file_exists(dirname(__FILE__) . "/zemantapro.php"))
		require_once(dirname(__FILE__) . "/zemantapro.php");
	if (function_exists('zem_load_pro')) zem_load_pro();
	else return false;
}

function zem_check_dependencies() {
	// Return true if CURL and DOM XML modules exist and false otherwise
	return ( ( function_exists( 'curl_init' ) || ini_get('allow_url_fopen') ) &&
		( function_exists( 'preg_match' ) || function_exists( 'ereg' ) ) );
}

function zem_activate() {
	chmod(dirname(__FILE__) . "/json-proxy.php", 0755);
}

function zem_reg_match( $rstr, $str ) {
	// Make a regex match independantly of library available. Might work only
	// for simple cases like ours.
	if ( function_exists( 'preg_match' ) )
		preg_match( $rstr, $str, $matches );
	elseif ( function_exists( 'ereg' ) )
		ereg( $rstr, $str, $matches );
	else
		$matches = array('', '');
	return $matches;
}

function zem_do_get_request($url) {
	$fp = @fopen($url, 'rb');
	if (!$fp) {
		return array(1, "Problem connecting to $url : @$php_errormsg\n");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		return array(2, "Problem reading data from $url : @$php_errormsg\n");
	}
	return array(0, $response);
}

function zem_do_post_request($url, $data, $optional_headers = null) {
	$params = array('http' => array(
				'method' => 'POST',
				'content' => $data
					));
	if ($optional_headers !== null) {
		$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if (!$fp) {
		return("Problem connecting to $url : $php_errormsg\n");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		return("Problem reading data from $url : $php_errormsg\n");
	}
	return $response;
}

function zem_download($url) {
	if ( function_exists( 'curl_init' ) ) {
		$session = curl_init( $url );

		// Don't return HTTP headers. Do return the contents of the call
		curl_setopt( $session, CURLOPT_HEADER, false );
		curl_setopt( $session, CURLOPT_RETURNTRANSFER, true );

		// Make the call
		$rsp = curl_exec( $session );
		curl_close( $session );
		if ($rsp === false)
			return array(1, "Problem reading data from $url : @$php_errormsg\n");
		else
			return array(0, $rsp);
	} else if ( ini_get( 'allow_url_fopen' ) ) {
		return zem_do_get_request($url);
	}
}

function zem_image_upload_dir() {
	$upload_dir = zem_get_option( "zemanta_image_uploader_dir" );
	if ($upload_dir == null) {
		$uploads = wp_upload_dir();
		return $uploads['path'];
	}
	return $upload_dir;
}

function zem_image_upload_url() {
	$upload_url = zem_get_option( "zemanta_image_uploader_url" );
	if ($upload_url == null) {
		$uploads = wp_upload_dir();
		return $uploads['url'];
	}
	return $upload_url;
}

function zem_upload_image($url, $post, $desc) {
	$upload_dir = zem_image_upload_dir();
	$filename = wp_unique_filename($upload_dir, basename($url));
	$new_file = $upload_dir . "/" . basename(urldecode($filename));
	if (!file_exists($new_file)) {
		$img = @fopen($new_file, "w");
		if ($img === false) {
			$_SESSION['image_download_error_string'] = __('Your image upload directory (' . $upload_dir . ') is not writable. Zemanta cannot upload images there.');
			return false;
		}
		list($res, $data) = zem_download($url);
		if ($res > 0) {
			$_SESSION['image_download_error_string'] = __('Zemanta could not download some or all of the images referenced in your post to your server. Please, try again later.');
			return false;
		}
		fwrite($img, $data);
		fclose($img);
		chmod($new_file, 0644);
		$upload_url = zem_image_upload_url();
		return $upload_url . "/" . $filename;
	}
}

function zem_uploader_enabled() {
	return zem_get_option("zemanta_image_uploader");
}

function zem_image_downloader($post_id) {
	global $zem_images_downloaded;
	if (!zem_uploader_enabled() || $zem_images_downloaded) return false;
	$post = get_post($post_id);
	$content = $post->post_content;
	// zemanta images
	$nlcontent = str_replace("\n", "", $content);
	$urls = array();
	$descs = array();
	while (true) {
		$matches = zem_reg_match("/<div[^>]+zemanta-img[^>]+>.+?<\/div>/", $nlcontent);
		if (!sizeof($matches)) break;
		$srcurl = zem_reg_match('/src="([^"]+)"/', $matches[0]);
		$desc = zem_reg_match('/href="([^"]+)"/', $matches[0]);
		$urls[] = $srcurl[1];
		$descs[] = $desc[1];
		$nlcontent = substr($nlcontent, strpos($nlcontent, $matches[0]) + strlen($matches[0]));
	}
	// other images
	$nlcontent = str_replace("\n", "", $content);
	if (zem_get_option("zemanta_image_uploader_promisc")) while (true) {
		$matches = zem_reg_match('/<img .*?src="[^"]+".*?>/', $nlcontent);
		if (!sizeof($matches)) break;
		$srcurl = zem_reg_match('/src="([^"]+)"/', $matches[0]);
		if (in_array($srcurl, $urls)) continue;
		$desc = zem_reg_match('/alt="([^"]+)"/', $matches[0]);
		$urls[] = $srcurl[1];
		if (strlen($desc[1])) $descs[] = $desc[1];
		else $descs[] = $srcurl[1];
		$nlcontent = substr($nlcontent, strpos($nlcontent, $matches[0]) + strlen($matches[0]));
	}
	$upload_url = zem_image_upload_url();
	$_SESSION['dbghaha'] = print_r($urls, 1);
	if (sizeof($urls) == 0) return true;
	for ($i=0; $i<sizeof($urls); $i++) {
		$url = $urls[$i];
		$desc = $descs[$i];
		// skip images already downloaded and zemanta pixie
		if (strpos($url, $upload_url) !== false || strpos($url, "http://img.zemanta.com/") !== false) {
			continue;
		}
		$localurl = zem_upload_image($url, $post_id, $desc);
		if ($localurl !== false) {
			$content = str_replace($url, $localurl, $content);
		} else {
			$_SESSION['image_download_errors'] = true;
		}
	}
	$post->post_content = $content;
	$zem_images_downloaded = true;
	wp_update_post($post);
	return true;
}

function zem_api_key_fetch() {
	if (zem_is_pro()) {
		return "";
	}
	// Fetch fresh API key used with Zemanta calls
	$api = '';
	$url = 'http://api.zemanta.com/services/rest/0.0/';
	$postvars = 'method=zemanta.auth.create_user';

	if ( function_exists( 'curl_init' ) ) {
		$session = curl_init( $url );
		curl_setopt ( $session, CURLOPT_POST, true );
		curl_setopt ( $session, CURLOPT_POSTFIELDS, $postvars );

		// Don't return HTTP headers. Do return the contents of the call
		curl_setopt( $session, CURLOPT_HEADER, false );
		curl_setopt( $session, CURLOPT_RETURNTRANSFER, true );

		// Make the call
		$rsp = curl_exec( $session );
		curl_close( $session );
	} else if ( ini_get( 'allow_url_fopen' ) ) {
		$rsp = zem_do_post_request($url, $postvars);
	}

	// Parse returned result
	$matches = zem_reg_match( '/<status>(.+?)<\/status>/', $rsp );
	if ( 'ok' == $matches[1] ) {
		$matches = zem_reg_match( '/<apikey>(.+?)<\/apikey>/', $rsp );
		$api = $matches[1];
	}

	return $api;
}

function zem_proxy_url() {
	return get_option('siteurl') . '/wp-content/plugins/zemanta/json-proxy.php';
}

function zem_test_proxy() {

	$url = zem_proxy_url();
	$api_key = zem_api_key();
	$args = array(
	'method'=> 'zemanta.suggest',
	'api_key'=> $api_key,
	'text'=> '',
	'format'=> 'xml'
	);

	$data = "";
	foreach($args as $key=>$value)
	{
	$data .= ($data != "")?"&":"";
	$data .= urlencode($key)."=".urlencode($value);
	}

	if ( function_exists( 'curl_init' ) ) {
		$session = curl_init( $url );
		curl_setopt ( $session, CURLOPT_POST, true );
		curl_setopt ( $session, CURLOPT_POSTFIELDS, $data );

		// Don't return HTTP headers. Do return the contents of the call
		curl_setopt( $session, CURLOPT_HEADER, false );
		curl_setopt( $session, CURLOPT_RETURNTRANSFER, true );

		// Make the call
		$rsp = curl_exec( $session );
		curl_close( $session );
	} else if ( ini_get( 'allow_url_fopen' ) ) {
		$rsp = zem_do_post_request($url, $data);
	} else {
		return _("Zemanta needs either the cURL PHP module or allow_url_fopen enabled to work. Please ask your server administrator to set either of these up.");
	}

	$matches = zem_reg_match( '/<status>(.+?)<\/status>/', $rsp );
	if (!$matches)
		return _("Invalid response: ") . '"' . htmlspecialchars($rsp) . '"';
	return $matches[1];
}

function zem_get_option($name) {
	if (zem_is_pro()) return zem_get_pro_option($name);
	return get_option($name, null);
}

function zem_set_option($name, $value) {
	if (zem_is_pro()) return zem_set_pro_option($name, $value);
	if ($value === null) return delete_option($name);
	return update_option($name, $value);
}

function zem_api_key() {
	if (zem_is_pro()) return zem_pro_api_key();
	return zem_get_option('zemanta_api_key');
}

function zem_set_api_key($api_key) {
	if (zem_is_pro()) return zem_set_pro_api_key($api_key);
	update_option('zemanta_api_key', $api_key);
}

function zem_test_api() {

	$url = 'http://api.zemanta.com/services/rest/0.0/';
	$api_key = zem_api_key();
	$args = array(
	'method'=> 'zemanta.suggest',
	'api_key'=> $api_key,
	'text'=> '',
	'format'=> 'xml'
	);

	$data = "";
	foreach($args as $key=>$value)
	{
	$data .= ($data != "")?"&":"";
	$data .= urlencode($key)."=".urlencode($value);
	}

	if ( function_exists( 'curl_init' ) ) {
		$session = curl_init( $url );
		curl_setopt ( $session, CURLOPT_POST, true );
		curl_setopt ( $session, CURLOPT_POSTFIELDS, $data );

		// Don't return HTTP headers. Do return the contents of the call
		curl_setopt( $session, CURLOPT_HEADER, false );
		curl_setopt( $session, CURLOPT_RETURNTRANSFER, true );

		// Make the call
		$rsp = curl_exec( $session );
		curl_close( $session );
	} else if ( ini_get( 'allow_url_fopen' ) ) {
		$rsp = zem_do_post_request($url, $data);
	} else {
		return _("Zemanta needs either the cURL PHP module or allow_url_fopen enabled to work. Please ask your server administrator to set either of these up.");
	}

	$matches = zem_reg_match( '/<status>(.+?)<\/status>/', $rsp );
	if (!$matches)
		return _("Invalid response: ") . '"' . htmlspecialchars($rsp) . '"';
	return $matches[1];
}

function zem_wp_debug() {
	$api_key = zem_api_key();
	if ( !$api_key ) {
		$api_key = zem_api_key_fetch();
		update_option( 'zemanta_api_key', $api_key );
	}

	echo '<div class="wrap">';
	echo "<h2>" . __( 'Zemanta Plugin Status', 'zemanta' ) . "</h2>";

	$apitest = zem_test_api();
	$proxytest = zem_test_proxy();

	if ($apitest == "ok") $apiresult = '<span style="color: green;">OK</span>';
	else $apiresult = '<span style="color: red;">Invalid</span>';

	if ($proxytest == "ok") $proxyresult = '<span style="color: green;">OK</span>';
	else $proxyresult = '<a style="color: red; font-weight: bold;" href="'. zem_proxy_url() .'">Invalid, click here to see the error message</a>';

	?>
	<p>Api key (if you have one, your wordpress can talk to Zemanta): <strong><?php echo $api_key; ?></strong></p>
	<p>Zemanta response: <strong><?php echo $apiresult; ?></strong></p>
	<p>Your ajax proxy response: <strong><?php echo $proxyresult; ?></strong></p>

	</div>
	<?php
}

function zem_wp_head() {
	// Insert Zemanta widget in sidebar
	$api_key = zem_api_key();

	print '<script type="text/javascript">window.ZemantaGetAPIKey = function () { return "' . $api_key . '"; }</script>';
	print '<script type="text/javascript">window.ZemantaPluginVersion = function () { return "0.6.3"; }</script>';
	print '<script id="zemanta-loader" type="text/javascript" src="http://fstatic.zemanta.com/plugins/wordpress/2.x/loader.js"></script>';
};

function zem_config_page() {
	if ( function_exists( 'add_submenu_page' ) )
		add_submenu_page( 'plugins.php', __('Zemanta Configuration'), __('Zemanta Configuration'), 'manage_options', 'zemanta', 'zem_wp_admin' );
}

function zem_wp_admin() {
	global $wp_version;

	if (zem_is_pro()) return zem_pro_wp_admin();

	// variables for the field and option names
	$hidden_field_name = 'zemanta_submit_hidden';
	$key_field = 'zemanta_api_key';
	$uploader_field = 'zemanta_image_uploader';
	$uploader_promisc_field = 'zemanta_image_uploader_promisc';
	$uploader_custom_path_field = 'zemanta_image_uploader_custom_path';
	$uploader_dir_field = 'zemanta_image_uploader_dir';
	$uploader_url_field = 'zemanta_image_uploader_url';

	// Read in existing option value from database
	$key_val = zem_api_key();
	$uploader_val = zem_get_option($uploader_field);
	$uploader_promisc_val = zem_get_option($uploader_promisc_field);
	$uploader_custom_path_val = zem_get_option($uploader_custom_path_field);
	$uploader_dir_val = zem_image_upload_dir();
	$uploader_url_val = zem_image_upload_url();

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( 'Y' == $_POST[ $hidden_field_name ] ) {
		// Read their posted value
		$key_val = $_POST[ $key_field ];
		$uploader_val = $_POST[ $uploader_field ];
		$uploader_promisc_val = $_POST[ $uploader_promisc_field ];
		$uploader_custom_path_val = $_POST[ $uploader_custom_path_field ];
		$uploader_dir_val = $_POST[ $uploader_dir_field ];
		$uploader_url_val = $_POST[ $uploader_url_field ];

		// Save the posted value in the database
		zem_set_api_key( $key_val );
		zem_set_option( $uploader_field, $uploader_val );
		zem_set_option( $uploader_promisc_field, $uploader_promisc_val );
		if ( $uploader_val && !$uploader_custom_path_val ) {
			$uploads = wp_upload_dir();
			$upload_dir = $uploads['path'];
			if ( !is_writable($upload_dir) ) {
				echo '<div class="error"><p><strong>' . __('Your wordpress upload directory (' . $upload_dir . ') cannot be written to. Zemanta will not be able to upload images there.', 'zemanta' ) . '</strong></p></div>';
			}
		}
		if ( $uploader_val && $uploader_custom_path_val ) {
			if ( !is_writable($uploader_dir_val) ) {
				echo '<div class="error"><p><strong>' . __('Upload directory you have set (' . $uploader_dir_val . ') cannot be written to. Zemanta will not be able to upload images there.', 'zemanta' ) . '</strong></p></div>';
			}
			zem_set_option( $uploader_dir_field, $uploader_dir_val );
			zem_set_option( $uploader_url_field, $uploader_url_val );
			zem_set_option( $uploader_custom_path_field, $uploader_custom_path_val );
		} else {
			zem_set_option( $uploader_dir_field, null );
			zem_set_option( $uploader_url_field, null );
			zem_set_option( $uploader_custom_path_field, $uploader_custom_path_val );
		}
		// Put an options updated message on the screen
?>
<div class="updated"><p><strong><?php _e('Configuration saved.', 'zemanta' ); ?></strong></p></div>
<?php
    }

	// Now display the options editing screen
	echo '<div class="wrap">';

	// header
	echo "<h2>" . __( 'Zemanta Plugin Configuration', 'zemanta' ) . "</h2>";

	// options form
	?>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
		function zemsettings_togglepanel(id, onoff) {
			document.getElementById(id).style.display = onoff ? 'block' : 'none';
			return true;
		}
	//--><!]]>
	</script>
	<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
        <p>API key is an authentication token that allows Zemanta service to know who you are. We automatically assigned you one when you first used this plug-in.</p>
        <p>If you would like to use a different API key you can enter it here.</p>
        <p><?php _e('Zemanta API key:', 'zemanta' ); ?>
			<input type="text" name="<?php echo $key_field; ?>" value="<?php echo $key_val; ?>" size="25">
		</p>
		<h2>Image uploading</h2>
        <p>Zemanta gets images from a number of 3rd party hosts (as set in your preferences). To ensure
		the best experience of your readers you should mirror images on your server. This option turns
		on automatic mirroring to your server of images included in published post.</p>
		<p>If you decide not to download images, they can be removed from 3rd party hosts at any time
		and loading performance can be effected by their reliability.</p>
        <p><?php _e('Enable Zemanta image uploader:', 'zemanta' ); ?>
			<input id="zemsettings_uploader_checkbox" type="checkbox" name="<?php echo $uploader_field; ?>" <?php if ($uploader_val) echo "checked=\"checked\""; ?> onclick="zemsettings_togglepanel('zemsettings_pathinfo', !document.getElementById('zemsettings_advanced_checkbox').checked); return zemsettings_togglepanel('zemsettings_uploader', this.checked);" <?php if ($wp_version <= '2.5') echo 'disabled="disabled" '; ?>/>
		</p>
		<p class="updated"><?php if ($wp_version <= '2.5') _e('Zemanta image uploader is only supported with Wordpress 2.5 or above.', 'zemanta'); ?></p>
		<div id="zemsettings_uploader" style="display: none;">
			<p><?php _e('Allow Zemanta uploader to upload any image referenced by your post to your blog:', 'zemanta' ); ?>
				<input id="zemsettings_promisc_checkbox" type="checkbox" name="<?php echo $uploader_promisc_field; ?>" <?php if ($uploader_promisc_val) echo "checked=\"checked\""; ?> onclick="return zemsettings_togglepanel('zemsettings_disclaim', (this.checked&&document.getElementById('zemsettings_uploader_checkbox').checked));" />
			</p>
			<div id="zemsettings_disclaim" style="display: none;">
			<p class="error">Using Zemanta image uploader in this way may download copyrighted images to your blog. Make sure you and your blog writers check and understand licenses of each and every image before using them in your blog posts and delete them if they infringe on author's rights.</p>
			</div>
			<p><?php _e('Use custom path for automatically uploaded images:', 'zemanta' ); ?>
				<input id="zemsettings_advanced_checkbox" type="checkbox" name="<?php echo $uploader_custom_path_field; ?>" <?php if ($uploader_custom_path_val) echo "checked=\"checked\""; ?> onclick="zemsettings_togglepanel('zemsettings_pathinfo', (!this.checked&&document.getElementById('zemsettings_uploader_checkbox').checked)); return zemsettings_togglepanel('zemsettings_advanced', this.checked);" />
			</p>
			<div id="zemsettings_pathinfo" style="display: none;">
				<p>Wordpress will by default save images to its media directories, which according to settings may change monthly:</p>
				<p class="updated">Path: <?php echo zem_image_upload_dir(); ?></p>
				<p class="updated">URL: <?php echo zem_image_upload_url(); ?></p>
			</div>
			<div id="zemsettings_advanced" style="display: none;">
				<p>You may set the path for downloader to save images to and the url where these images will
				be available via the web. Pre-filled are defaults that wordpress sets up for you. These
				generally change with each new month, depending on your wordpress preferences.</p>
				<p><?php _e('Uploader should save images to this directory:', 'zemanta' ); ?>
					<input type="text" name="<?php echo $uploader_dir_field; ?>" value="<?php echo $uploader_dir_val; ?>" size="60" />
				</p>
				<p><?php _e('The contents of the directory above are accessible through this url:', 'zemanta' ); ?>
					<input type="text" name="<?php echo $uploader_url_field; ?>" value="<?php echo $uploader_url_val; ?>" size="60" />
				</p>
			</div>
		</div>
		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options', 'zemanta' ) ?>" />
		</p>
	</form>
	<script type="text/javascript">
		zemsettings_togglepanel('zemsettings_uploader', document.getElementById('zemsettings_uploader_checkbox').checked);
		zemsettings_togglepanel('zemsettings_disclaim', (document.getElementById('zemsettings_uploader_checkbox').checked && document.getElementById('zemsettings_promisc_checkbox').checked));
		zemsettings_togglepanel('zemsettings_pathinfo', (document.getElementById('zemsettings_uploader_checkbox').checked && !document.getElementById('zemsettings_advanced_checkbox').checked));
		zemsettings_togglepanel('zemsettings_advanced', document.getElementById('zemsettings_advanced_checkbox').checked);
	</script>
</div>
<?php
	zem_wp_debug();
}

// Check dependencies
if ( !zem_check_dependencies() ) {
	function zem_warning () {
		echo "
		<div class='updated fade'><p>".__('Zemanta needs either the cURL PHP module or allow_url_fopen enabled to work. Please ask your server administrator to set either of these up.')."</p></div>";
	}

	add_action('admin_notices', 'zem_warning');
	return;
}

// Insert Zemanta to the top-right position if it was not positioned previously
function zem_check_zemanta_position($metabox_positions) { //Checks if zemanta-sidebar has position
	if (!$metabox_positions) return false;
	foreach ($metabox_positions as $position) {
		foreach (split(',',$position) as $p) {
			if ($p == "zemanta-sidebar") {
				return true;
			}
		}
	}
	return false;
}

function zem_set_default_zemanta_position() {
	zem_set_default_zemanta_post_position();
	zem_set_default_zemanta_page_position();
}

function zem_set_default_zemanta_post_position() {
	global $table_prefix, $current_user;
	wp_get_current_user();
	$metabox_positions = get_usermeta($current_user->ID, $table_prefix . 'metaboxorder_post');
	if (!zem_check_zemanta_position($metabox_positions)) {
		if ($metabox_positions) {
			if (array_key_exists('side', $metabox_positions)) {
				if ($metabox_positions['side']) {
					$metabox_positions['side'] = 'zemanta-sidebar,' . $metabox_positions['side'];
				} else {
					$metabox_positions['side'] = 'zemanta-sidebar';
				}
			} else {
				$metabox_positions['side'] = 'zemanta-sidebar';
			}
		} else {
			$metabox_positions = array('side'=>'zemanta-sidebar');
		}
		update_user_option( $current_user->ID, "meta-box-order_post", $metabox_positions );
	}
}

function zem_set_default_zemanta_page_position() {
	global $table_prefix, $current_user;
	wp_get_current_user();
	$metabox_positions = get_usermeta($current_user->ID, $table_prefix . 'metaboxorder_page');
	if (!zem_check_zemanta_position($metabox_positions)) {
		if ($metabox_positions) {
			if (array_key_exists('side', $metabox_positions)) {
				if ($metabox_positions['side']) {
					$metabox_positions['side'] = 'zemanta-sidebar,' . $metabox_positions['side'];
				} else {
					$metabox_positions['side'] = 'zemanta-sidebar';
				}
			} else {
				$metabox_positions['side'] = 'zemanta-sidebar';
			}
		} else {
			$metabox_positions = array('side'=>'zemanta-sidebar');
		}
		update_user_option( $current_user->ID, "meta-box-order_page", $metabox_positions );
	}
}

// Custom Meta Box
function zem_add_custom_box() {
  if( function_exists( 'add_meta_box' )) {
    add_meta_box( 'zemanta-sidebar', __( 'Zemanta', 'myplugin_textdomain' ), 
                'zem_inner_custom_box', 'post', 'advanced' );
    add_meta_box( 'zemanta-sidebar', __( 'Zemanta', 'myplugin_textdomain' ), 
                'zem_inner_custom_box', 'page', 'advanced' );
   }
}

/* Zemanta on the custom post/page section */
function zem_inner_custom_box() {
  // Use nonce for verification
  echo '<div id="zemanta-control" class="zemanta"></div><div id="zemanta-message" class="zemanta">Loading Zemanta...</div><div id="zemanta-filter" class="zemanta"></div><div id="zemanta-gallery" class="zemanta"></div><div id="zemanta-articles" class="zemanta"></div><div id="zemanta-preferences" class="zemanta"></div>';
}

// Check for image downloader errors

session_start();
if (isset($_SESSION['image_download_errors'])) {
    function zem_image_download_errors () {
        echo "<div class='updated fade'><p>". $_SESSION['image_download_error_string'] ."</p></div>";
    }
    add_action('admin_notices', 'zem_image_download_errors');
    unset($_SESSION['image_download_errors']);
}

// Fetch an API key on first run, if it doesn't exist yet or is empty

if (zem_is_pro()) { // Zemanta Pro
	$zemanta_secret = zem_secret();
	if ($zemanta_secret === false) {
		function zem_pro_secret_error () {
			echo "
			<div class='updated fade'><p>".__('Your Zemanta Pro secret is missing. Please, contact <a href="mailto:support@zemanta.com">support@zemanta.com</a> for help.')."</p></div>";
		}

		add_action('admin_notices', 'zem_pro_secret_error');
		add_action( 'admin_menu', 'zem_config_page' );
		return;
	}
	$api_key = zem_api_key();
	if (!$api_key) {
		return;
	}
} else { // Zemanta Basic
	$api_key = zem_api_key();
	if ( !$api_key ) {
		$api_key = zem_api_key_fetch();
		update_option( 'zemanta_api_key', $api_key );
	}
	if ( !$api_key ) {
		function zem_api_key_error () {
			echo "
			<div class='updated fade'><p>".__('Zemanta failed to obtain the neccessary information. Please, try again after a short while.')."</p></div>";
		}

			add_action('admin_notices', 'zem_api_key_error');
			add_action( 'admin_menu', 'zem_config_page' );
			return;
	}
}

//Hook for WP2.7 - MetaBoxes generated with native Wordpress functions to enable storing of their position
global $wp_version;
if (substr($wp_version,0,3) >= '2.7') {
	/* Set Zemanta default position (if it is not already set) */
	require_once(ABSPATH . '/wp-includes/pluggable.php');
	zem_set_default_zemanta_position();
	/* Use the admin_menu action to define the custom boxes */
	add_action('admin_menu', 'zem_add_custom_box');
}

// Register actions
add_action( 'dbx_post_sidebar', 'zem_wp_head', 1 );
add_action( 'admin_menu', 'zem_config_page' );
add_action( 'activate_zemanta', 'zem_activate' );
add_action( 'edit_page_form', 'zem_wp_head', 1 );
add_filter( 'publish_post', 'zem_image_downloader');
register_activation_hook(__FILE__, 'zem_activate');

?>
