<?php
/*
Copyright (c) 2007 - 2008, Zemanta Ltd.
The copyrights to the software code in this file are licensed under the (revised) BSD open source license.

Plugin Name: Zemanta
Plugin URI: http://www.zemanta.com/welcome/wordpress/
Description: Contextually relevant suggestions of links, pictures, related content and tags will make your blogging fun again.
Version: 0.5.5
Author: Zemanta Ltd. <info@zemanta.com>
Author URI: http://www.zemanta.com/
*/

function zem_check_dependencies() {
	// Return true if CURL and DOM XML modules exist and false otherwise
	return ( ( function_exists( 'curl_init' ) || ini_get('allow_url_fopen') ) &&
		( function_exists( 'preg_match' ) || function_exists( 'ereg' ) ) );
}

function zemanta_activate() {
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

function do_post_request($url, $data, $optional_headers = null)
{
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
		die("Problem connecting to $url : $php_errormsg\n");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		die("Problem reading data from $url : $php_errormsg\n");
	}
	return $response;
}

function zem_api_key_fetch() {
	// Fetch API key used with Zemanta calls
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
		$rsp = do_post_request($url, $postvars);
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
	$url = ($_SERVER['HTTPS'] == 'off' || !$_SERVER['HTTPS'])?'http://':'https://';
	$url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/../wp-content/plugins/zemanta/json-proxy.php';
	return $url;
}

function zem_test_proxy() {

	$url = zem_proxy_url();
	$api_key = get_option( 'zemanta_api_key' );
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
		$rsp = do_post_request($url, $data);
	} else {
		return _("Zemanta needs either the cURL PHP module or allow_url_fopen enabled to work. Please ask your server administrator to set either of these up.");
	}

	$matches = zem_reg_match( '/<status>(.+?)<\/status>/', $rsp );
	if (!$matches)
		return _("Invalid response: ") . '"' . htmlspecialchars($rsp) . '"';
	return $matches[1];
}

function zem_test_api() {

	$url = 'http://api.zemanta.com/services/rest/0.0/';
	$api_key = get_option( 'zemanta_api_key' );
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
		$rsp = do_post_request($url, $data);
	} else {
		return _("Zemanta needs either the cURL PHP module or allow_url_fopen enabled to work. Please ask your server administrator to set either of these up.");
	}

	$matches = zem_reg_match( '/<status>(.+?)<\/status>/', $rsp );
	if (!$matches)
		return _("Invalid response: ") . '"' . htmlspecialchars($rsp) . '"';
	return $matches[1];
}

function zem_wp_debug() {
	$api_key = get_option( 'zemanta_api_key' );
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
	$opt_val = get_option( 'zemanta_api_key' );

	print '<script type="text/javascript">window.ZemantaGetAPIKey = function () { return "' . $opt_val . '"; }</script>';
	print '<script type="text/javascript">window.ZemantaWPPluginVersion = function () { return "0.5.5"; }</script>';
	print '<script id="zemanta-loader" type="text/javascript" src="http://static.zemanta.com/plugins/wordpress/2.x/loader.js"></script>';
};

function zem_config_page() {
	if ( function_exists( 'add_submenu_page' ) )
		add_submenu_page( 'plugins.php', __('Zemanta Configuration'), __('Zemanta Configuration'), 'manage_options', 'zemanta', 'zem_wp_admin' );
}

function zem_wp_admin() {
	// variables for the field and option names
	$opt_name = 'zemanta_api_key';
	$hidden_field_name = 'zemanta_submit_hidden';
	$data_field_name = 'zemanta_api_key';

	// Read in existing option value from database
	$opt_val = get_option( $opt_name );

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( 'Y' == $_POST[ $hidden_field_name ] ) {
		// Read their posted value
		$opt_val = $_POST[ $data_field_name ];

		// Save the posted value in the database
		update_option( $opt_name, $opt_val );
		// Put an options updated message on the screen
?>
<div class="updated"><p><strong><?php _e('New API key saved.', 'zemanta' ); ?></strong></p></div>
<?php
    }

	// Now display the options editing screen
	echo '<div class="wrap">';

	// header
	echo "<h2>" . __( 'Zemanta Plugin Configuration', 'zemanta' ) . "</h2>";

	// options form
	?>
	<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
        <p>API key is an authentication token that allows Zemanta service to know who you are. We automatically assigned you one when you first used this plug-in.</p>
        <p>If you would like to use a different API key you can enter it here.</p>
        <p><?php _e('Zemanta API key:', 'zemanta' ); ?>
			<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="25">
		</p>
        
		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options', 'zemanta' ) ?>" />
		</p>
	</form>
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
function check_zemanta_position($metabox_positions) { //Checks if zemanta-sidebar has position
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

function set_default_zemanta_position() {
	set_default_zemanta_post_position();
	set_default_zemanta_page_position();
}

function set_default_zemanta_post_position() {
	global $table_prefix, $current_user;
	wp_get_current_user();
	$metabox_positions = get_usermeta($current_user->ID, $table_prefix . 'metaboxorder_post');
	if (!check_zemanta_position($metabox_positions)) {
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

function set_default_zemanta_page_position() {
	global $table_prefix, $current_user;
	wp_get_current_user();
	$metabox_positions = get_usermeta($current_user->ID, $table_prefix . 'metaboxorder_page');
	if (!check_zemanta_position($metabox_positions)) {
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
function zemanta_add_custom_box() {
  if( function_exists( 'add_meta_box' )) {
    add_meta_box( 'zemanta-sidebar', __( 'Zemanta', 'myplugin_textdomain' ), 
                'zemanta_inner_custom_box', 'post', 'advanced' );
    add_meta_box( 'zemanta-sidebar', __( 'Zemanta', 'myplugin_textdomain' ), 
                'zemanta_inner_custom_box', 'page', 'advanced' );
   }
}

/* Zemanta on the custom post/page section */
function zemanta_inner_custom_box() {
  // Use nonce for verification
  echo '<div id="zemanta-control" class="zemanta"></div><div id="zemanta-message" class="zemanta">Loading Zemanta...</div><div id="zemanta-filter" class="zemanta"></div><div id="zemanta-gallery" class="zemanta"></div><div id="zemanta-articles" class="zemanta"></div><div id="zemanta-preferences" class="zemanta"></div>';
}

// Fetch an API key on first run, if it doesn't exist yet or is empty
$api_key = get_option( 'zemanta_api_key' );
if ( !$api_key ) {
	$api_key = zem_api_key_fetch();
	update_option( 'zemanta_api_key', $api_key );

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
	set_default_zemanta_position();
	/* Use the admin_menu action to define the custom boxes */
	add_action('admin_menu', 'zemanta_add_custom_box');
}

// Register actions
//add_action( 'dbx_page_sidebar', 'zem_wp_head', 1 );
add_action( 'dbx_post_sidebar', 'zem_wp_head', 1 );
add_action( 'admin_menu', 'zem_config_page' );
add_action( 'activate_zemanta', 'zemanta_activate' );
add_action( 'edit_page_form', 'zem_wp_head', 1 );
register_activation_hook(__FILE__, 'zemanta_activate');

?>
