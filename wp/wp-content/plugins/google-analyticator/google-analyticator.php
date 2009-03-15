<?php
/*
 * Plugin Name: Google Analyticator
 * Version: 2.40
 * Plugin URI: http://cavemonkey50.com/code/google-analyticator/
 * Description: Adds the necessary JavaScript code to enable <a href="http://www.google.com/analytics/">Google's Analytics</a>. After enabling this plugin visit <a href="options-general.php?page=google-analyticator.php">the options page</a> and enter your Google Analytics' UID and enable logging.
 * Author: Ronald Heft, Jr.
 * Author URI: http://cavemonkey50.com/
 */

// Constants for enabled/disabled state
define("ga_enabled", "enabled", true);
define("ga_disabled", "disabled", true);

// Defaults, etc.
define("key_ga_uid", "ga_uid", true);
define("key_ga_status", "ga_status", true);
define("key_ga_admin", "ga_admin_status", true);
define("key_ga_admin_level", "ga_admin_level", true);
define("key_ga_extra", "ga_extra", true);
define("key_ga_extra_after", "ga_extra_after", true);
define("key_ga_outbound", "ga_outbound", true);
define("key_ga_downloads", "ga_downloads", true);
define("key_ga_footer", "ga_footer", true);
define("key_ga_specify_http", "ga_specify_http", true);

define("ga_uid_default", "XX-XXXXX-X", true);
define("ga_status_default", ga_disabled, true);
define("ga_admin_default", ga_enabled, true);
define("ga_admin_level_default", 8, true);
define("ga_extra_default", "", true);
define("ga_extra_after_default", "", true);
define("ga_outbound_default", ga_enabled, true);
define("ga_downloads_default", "", true);
define("ga_footer_default", ga_disabled, true);
define("ga_specify_http_default", "auto", true);

// Create the default key and status
add_option(key_ga_status, ga_status_default, 'If Google Analytics logging in turned on or off.');
add_option(key_ga_uid, ga_uid_default, 'Your Google Analytics UID.');
add_option(key_ga_admin, ga_admin_default, 'If WordPress admins are counted in Google Analytics.');
add_option(key_ga_admin_level, ga_admin_level_default, 'The level to consider a user a WordPress admin.');
add_option(key_ga_extra, ga_extra_default, 'Addition Google Analytics tracking options');
add_option(key_ga_extra_after, ga_extra_after_default, 'Addition Google Analytics tracking options');
add_option(key_ga_outbound, ga_outbound_default, 'Add tracking of outbound links');
add_option(key_ga_downloads, ga_downloads_default, 'Download extensions to track with Google Analyticator');
add_option(key_ga_footer, ga_footer_default, 'If Google Analyticator is outputting in the footer');
add_option(key_ga_specify_http, ga_specify_http_default, 'Automatically detect the http/https settings');

// Create a option page for settings
add_action('admin_init', 'ga_admin_init');
add_action('admin_menu', 'add_ga_option_page');

// Initialize the options
function ga_admin_init() {
	// Register out options so WordPress knows about them
	if ( function_exists('register_setting') ) {
		register_setting('google-analyticator', key_ga_status, '');
		register_setting('google-analyticator', key_ga_uid, '');
		register_setting('google-analyticator', key_ga_admin, '');
		register_setting('google-analyticator', key_ga_admin_level, '');
		register_setting('google-analyticator', key_ga_extra, '');
		register_setting('google-analyticator', key_ga_extra_after, '');
		register_setting('google-analyticator', key_ga_outbound, '');
		register_setting('google-analyticator', key_ga_downloads, '');
		register_setting('google-analyticator', key_ga_footer, '');
		register_setting('google-analyticator', key_ga_specify_http, '');
	}
}

// Initialize outbound link tracking
add_action('init', 'ga_outgoing_links');

// Hook in the options page function
function add_ga_option_page() {
	global $wpdb;
	add_options_page('Google Analyticator Options', 'Google Analytics', 8, basename(__FILE__), 'ga_options_page');
}

function ga_options_page() {
	// If we are a postback, store the options
	if (isset($_POST['info_update'])) {
//		if ( wp_verify_nonce($_POST['ga-nonce-key'], 'google-analyticator') ) {
			
			// Update the status
			$ga_status = $_POST[key_ga_status];
			if (($ga_status != ga_enabled) && ($ga_status != ga_disabled))
				$ga_status = ga_status_default;
			update_option(key_ga_status, $ga_status);

			// Update the UID
			$ga_uid = $_POST[key_ga_uid];
			if ($ga_uid == '')
				$ga_uid = ga_uid_default;
			update_option(key_ga_uid, $ga_uid);

			// Update the admin logging
			$ga_admin = $_POST[key_ga_admin];
			if (($ga_admin != ga_enabled) && ($ga_admin != ga_disabled))
				$ga_admin = ga_admin_default;
			update_option(key_ga_admin, $ga_admin);
			
			// Update the admin level
			$ga_admin_level = $_POST[key_ga_admin_level];
			if ( $ga_admin_level == '' )
				$ga_admin_level = ga_admin_level_default;
			update_option(key_ga_admin_level, $ga_admin_level);

			// Update the extra tracking code
			$ga_extra = $_POST[key_ga_extra];
			update_option(key_ga_extra, $ga_extra);

			// Update the extra after tracking code
			$ga_extra_after = $_POST[key_ga_extra_after];
			update_option(key_ga_extra_after, $ga_extra_after);

			// Update the outbound tracking
			$ga_outbound = $_POST[key_ga_outbound];
			if (($ga_outbound != ga_enabled) && ($ga_outbound != ga_disabled))
				$ga_outbound = ga_outbound_default;
			update_option(key_ga_outbound, $ga_outbound);

			// Update the download tracking code
			$ga_downloads = $_POST[key_ga_downloads];
			update_option(key_ga_downloads, $ga_downloads);

			// Update the footer
			$ga_footer = $_POST[key_ga_footer];
			if (($ga_footer != ga_enabled) && ($ga_footer != ga_disabled))
				$ga_footer = ga_footer_default;
			update_option(key_ga_footer, $ga_footer);
			
			// Update the HTTP status
			$ga_specify_http = $_POST[key_ga_specify_http];
			if ( $ga_specify_http == '' )
				$ga_specify_http = 'auto';
			update_option(key_ga_specify_http, $ga_specify_http);

			// Give an updated message
			echo "<div class='updated fade'><p><strong>Google Analyticator settings saved.</strong></p></div>";
//		}
	}

	// Output the options page
	?>

		<div class="wrap">
		<form method="post" action="options-general.php?page=google-analyticator.php">
			<h2>Google Analyticator Settings</h2>
			<h3>Basic Options</h3>
			<?php if (get_option(key_ga_status) == ga_disabled) { ?>
				<div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
				Google Analytics integration is currently <strong>DISABLED</strong>.
				</div>
			<?php } ?>
			<?php if ((get_option(key_ga_uid) == "XX-XXXXX-X") && (get_option(key_ga_status) != ga_disabled)) { ?>
				<div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
				Google Analytics integration is currently enabled, but you did not enter a UID. Tracking will not occur.
				</div>
			<?php } ?>
			<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_status ?>">Google Analytics logging is:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_status."' id='".key_ga_status."'>\n";
						
						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_status) == ga_enabled)
							echo " selected='selected'";
						echo ">Enabled</option>\n";
						
						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_status) == ga_disabled)
							echo" selected='selected'";
						echo ">Disabled</option>\n";
						
						echo "</select>\n";
						?>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_uid; ?>">Your Google Analytics' UID:</label>
					</th>
					<td>
						<?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_uid."' ";
						echo "id='".key_ga_uid."' ";
						echo "value='".get_option(key_ga_uid)."' />\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Enter your Google Analytics' UID in this box. The UID is needed for Google Analytics to log your website stats. Your UID can be found by looking in the JavaScript Google Analytics gives you to put on your page. Look for your UID in between <strong>_uacct = "UA-11111-1";</strong> in the JavaScript. In this example you would put <strong>UA-11111-1</strong> in the UID box.</p>
					</td>
				</tr>
			</table>
			<h3>Advanced Options</h3>
				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_admin ?>">WordPress admin logging:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_admin."' id='".key_ga_admin."'>\n";
						
						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_admin) == ga_enabled)
							echo " selected='selected'";
						echo ">Enabled</option>\n";
						
						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_admin) == ga_disabled)
							echo" selected='selected'";
						echo ">Disabled</option>\n";
						
						echo "</select>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Disabling this option will prevent all logged in WordPress admins from showing up on your Google Analytics reports. A WordPress admin is defined as a user with a level <?php
						echo "<input type='text' size='2' ";
						echo "name='".key_ga_admin_level."' ";
						echo "id='".key_ga_admin_level."' ";
						echo "value='".stripslashes(get_option(key_ga_admin_level))."' />\n";
						?> or higher. Your user level is <?php
						if ( current_user_can('level_10') )
							echo '10';
						elseif ( current_user_can('level_9') )
							echo '9';
						elseif ( current_user_can('level_8') )
							echo '8';
						elseif ( current_user_can('level_7') )
							echo '7';
						elseif ( current_user_can('level_6') )
							echo '6';
						elseif ( current_user_can('level_5') )
							echo '5';
						elseif ( current_user_can('level_4') )
							echo '4';
						elseif ( current_user_can('level_3') )
							echo '3';
						elseif ( current_user_can('level_2') )
							echo '2';
						elseif ( current_user_can('level_1') )
							echo '1';
						else
							echo '0';
						?>.</p>
					</td>
				</tr>
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_footer ?>">Footer tracking code:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_footer."' id='".key_ga_footer."'>\n";
						
						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_footer) == ga_enabled)
							echo " selected='selected'";
						echo ">Enabled</option>\n";
						
						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_footer) == ga_disabled)
							echo" selected='selected'";
						echo ">Disabled</option>\n";
						
						echo "</select>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Enabling this option will insert the Google Analytics tracking code in your site's footer instead of your header. This will speed up your page loading if turned on. Not all themes support code in the footer, so if you turn this option on, be sure to check the Analytics code is still displayed on your site.</p>
					</td>
				</tr>
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_outbound ?>">Outbound link tracking:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_outbound."' id='".key_ga_outbound."'>\n";
						
						echo "<option value='".ga_enabled."'";
						if(get_option(key_ga_outbound) == ga_enabled)
							echo " selected='selected'";
						echo ">Enabled</option>\n";
						
						echo "<option value='".ga_disabled."'";
						if(get_option(key_ga_outbound) == ga_disabled)
							echo" selected='selected'";
						echo ">Disabled</option>\n";
						
						echo "</select>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Disabling this option will turn off the tracking of outbound links. It's recommended not to disable this option unless you're a privacy advocate (now why would you be using Google Analytics in the first place?) or it's causing some kind of weird issue.</p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_downloads; ?>">Download extensions to track:</label>
					</th>
					<td>
						<?php
						echo "<input type='text' size='50' ";
						echo "name='".key_ga_downloads."' ";
						echo "id='".key_ga_downloads."' ";
						echo "value='".stripslashes(get_option(key_ga_downloads))."' />\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Enter any extensions of files you would like to be tracked as a download. For example to track all MP3s and PDFs enter <strong>mp3,pdf</strong>. <em>Outbound link tracking must be enabled for downloads to be tracked.</em></p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_extra; ?>">Additional tracking code<br />(before tracker initialization):</label>
					</th>
					<td>
						<?php
						echo "<textarea cols='50' rows='8' ";
						echo "name='".key_ga_extra."' ";
						echo "id='".key_ga_extra."'>";
						echo stripslashes(get_option(key_ga_extra))."</textarea>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Enter any additional lines of tracking code that you would like to include in the Google Analytics tracking script. The code in this section will be displayed <strong>before</strong> the Google Analytics tracker is initialized. Read <a href="http://www.google.com/analytics/InstallingGATrackingCode.pdf">Google Analytics tracker manual</a> to learn what code goes here and how to use it.</p>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_extra_after; ?>">Additional tracking code<br />(after tracker initialization):</label>
					</th>
					<td>
						<?php
						echo "<textarea cols='50' rows='8' ";
						echo "name='".key_ga_extra_after."' ";
						echo "id='".key_ga_extra_after."'>";
						echo stripslashes(get_option(key_ga_extra_after))."</textarea>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Enter any additional lines of tracking code that you would like to include in the Google Analytics tracking script. The code in this section will be displayed <strong>after</strong> the Google Analytics tracker is initialized. Read <a href="http://www.google.com/analytics/InstallingGATrackingCode.pdf">Google Analytics tracker manual</a> to learn what code goes here and how to use it.</p>
					</td>
				</tr>
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_ga_specify_http; ?>">Specify HTTP detection:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_ga_specify_http."' id='".key_ga_specify_http."'>\n";
						
						echo "<option value='auto'";
						if(get_option(key_ga_specify_http) == 'auto')
							echo " selected='selected'";
						echo ">Auto Detect</option>\n";
						
						echo "<option value='http'";
						if(get_option(key_ga_specify_http) == 'http')
							echo " selected='selected'";
						echo ">HTTP</option>\n";
						
						echo "<option value='https'";
						if(get_option(key_ga_specify_http) == 'https')
							echo " selected='selected'";
						echo ">HTTPS</option>\n";
						
						echo "</select>\n";
						?>
						<p style="margin: 5px 10px;" class="setting-description">Explicitly set the type of HTTP connection your website uses. Setting this option instead of relying on the auto detect may resolve the _gat is undefined error message.</p>
					</td>
				</tr>
				</table>
			<p class="submit">
				<?php if ( function_exists('settings_fields') ) settings_fields('google-analyticator'); ?>
				<input type='submit' name='info_update' value='Save Changes' />
			</p>
		</div>
		</form>

<?php
}

// Add the script
if (get_option(key_ga_footer) == ga_enabled) {
	add_action('wp_footer', 'add_google_analytics');
} else {
	add_action('wp_head', 'add_google_analytics');
}

// The guts of the Google Analytics script
function add_google_analytics() {
	$uid = stripslashes(get_option(key_ga_uid));
	$extra = stripslashes(get_option(key_ga_extra));
	$extra_after = stripslashes(get_option(key_ga_extra_after));
	$extensions = str_replace (",", "|", get_option(key_ga_downloads));
	
	// If GA is enabled and has a valid key
	if ((get_option(key_ga_status) != ga_disabled) && ($uid != "XX-XXXXX-X")) {
		
		// Track if admin tracking is enabled or disabled and less than user level 8
		if ((get_option(key_ga_admin) == ga_enabled) || ((get_option(key_ga_admin) == ga_disabled) && ( !current_user_can('level_' . get_option(key_ga_admin_level)) ))) {
			
			echo "<!-- Google Analytics Tracking by Google Analyticator: http://cavemonkey50.com/code/google-analyticator/ -->\n";
			// Pick the HTTP connection
			if ( get_option(key_ga_specify_http) == 'http' ) {
				echo "	<script type=\"text/javascript\" src=\"http://www.google-analytics.com/ga.js\"></script>\n\n";
			} elseif ( get_option(key_ga_specify_http) == 'https' ) {
				echo "	<script type=\"text/javascript\" src=\"https://ssl.google-analytics.com/ga.js\"></script>\n\n";
			} else {
				echo "	<script type=\"text/javascript\">\n";
				echo "		var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\n";
				echo "		document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\n";
				echo "	</script>\n\n";
			}
			
			echo "	<script type=\"text/javascript\">\n";
			echo "		try {\n";
			echo "		var pageTracker = _gat._getTracker(\"$uid\");\n";
			
			// Insert extra before tracker code
			if ( '' != $extra )
				echo "		" . $extra . "\n";
			
			// Initialize the tracker
			echo "		pageTracker._initData();\n";
			echo "		pageTracker._trackPageview();\n";
			
			// Insert extra after tracker code
			if ( '' != $extra_after )
				echo "		" . $extra_after . "\n";
			
			echo "	} catch(err) {}</script>\n";
			
			
			$extensions = explode(',', stripslashes(get_option(key_ga_downloads)));
			foreach ( $extensions AS $extension )
				$ext .= "'$extension',";
			$ext = substr($ext, 0, -1); ?>
			<script type="text/javascript">var fileTypes = [<?php echo $ext; ?>];</script>
			<?php
		}
	}
}

/**
 * Adds outbound link tracking to Google Analyticator
 *
 * @author Ronald Heft
 **/
function ga_outgoing_links()
{
	// If outbound tracking is enabled
	if (get_option(key_ga_outbound) == ga_enabled) {
		// If admin tracking is enabled or not an admin user
		if ((get_option(key_ga_admin) == ga_enabled) || ((get_option(key_ga_admin) == ga_disabled) && ( !current_user_can('level_' . get_option(key_ga_admin_level)) ))) {
			add_action('wp_print_scripts', 'ga_external_tracking_js');
			add_action('wp_head', 'ga_file_extensions');
		}
	}
}

/**
 * Adds the scripts required for outbound link tracking
 *
 * @author Ronald Heft
 **/
function ga_external_tracking_js()
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('ga-external-tracking', plugins_url('/google-analyticator/external-tracking.js'));
}

/**
 * Adds download tracking via jQuery to Google Analyticator
 *
 * @author Ronald Heft
 **/
function ga_file_extensions()
{ 
	$extensions = explode(',', stripslashes(get_option(key_ga_downloads)));
	foreach ( $extensions AS $extension )
		$ext .= "'$extensions',";
	$ext = substr($ext, 0, -1);
?>
	<script type="text/javascript">var fileTypes = [<?php echo $ext; ?>];</script>
<?php }

?>