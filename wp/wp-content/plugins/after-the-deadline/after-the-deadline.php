<?php
/*
   Plugin Name: After The Deadline
   Plugin URI:  http://www.afterthedeadline.com
   Description: Contextual spelling, style, and grammar check for Wordpress.  Write better and spend less time editing.
   Author:      Raphael Mudge
   Version:     0.41091
   Author URI:  http://blog.afterthedeadline.com

   Credits:
   - API Key configuration code adapted from Akismet plugin
   - AtD_http_post adopted from Akismet...  
*/


/*
 *  Make sure some useful constants are defined.  I'd say this is for pre-2.6 compatability but AtD requires WP 2.8+
 */
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

/*  
 *  Load necessary include files
 */
include( 'config-options.php' );
include( 'config-unignore.php' );
include( 'config-key.php' );
include( 'utils.php' );
include( 'proxy.php' );

/*
 * Display the AtD configuration options (or not supported if the language id is not English [1])
 */
function AtD_config() {
	if ( get_user_option('rich_editing') == 'true' ) {
		AtD_display_options_form();
		AtD_display_unignore_form();
	}
	else {
		AtD_process_not_supported();
	}
}

/* 
 * Setup the AtD configure key menu hook...
 */
function AtD_setup_admin_menu() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page( 'plugins.php', __('After the Deadline'), __('After the Deadline'), 'manage_options', 'atd-key-config', 'AtD_display_key_form' );
}

/*
 *  Code to update the toolbar with the AtD Button and Install the AtD TinyMCE Plugin
 */
function AtD_addbuttons() {

	/* Don't bother doing this stuff if the current user lacks permissions */
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;
   
	/* Add only in Rich Editor mode w/ Blog language ID set to English */
	if ( get_user_option('rich_editing') == 'true' ) {
		add_filter( 'mce_external_plugins', 'add_AtD_tinymce_plugin' );
		add_filter( 'mce_buttons', 'register_AtD_button' );
	}

	add_action( 'personal_options_update', 'AtD_process_options_update' );
	add_action( 'personal_options_update', 'AtD_process_unignore_update' );
	add_action( 'profile_personal_options', 'AtD_config' );
}

/*
 * Hook into the TinyMCE buttons and replace the current spellchecker
 */
function register_AtD_button( $buttons ) {

	/* kill the spellchecker.. don't need no steenkin PHP spell checker */
	foreach ( $buttons as $key => $button ) {
		if ( $button == 'spellchecker' ) {
			$buttons[$key] = 'AtD';
			return $buttons;
		}
	}

	/* hrm... ok add us last plz */
	array_push( $buttons, 'separator', 'AtD' );
	return $buttons;
}
 
/*
 * Load the TinyMCE plugin : editor_plugin.js (wp2.5) 
 */
function add_AtD_tinymce_plugin( $plugin_array ) {
	$plugin_array['AtD'] = WP_PLUGIN_URL . '/after-the-deadline/tinymce/editor_plugin.js';
	return $plugin_array;
}

/* 
 * Update the TinyMCE init block with AtD specific settings
 */
function AtD_change_mce_settings( $init_array ) {

        /* grab our user and validate their existence */
        $user = wp_get_current_user();
        if ( ! $user || $user->ID == 0 )
                return;

	$init_array['atd_rpc_url']        = admin_url() . 'admin-ajax.php?action=proxy_atd&url=';
	$init_array['atd_ignore_rpc_url'] = admin_url() . 'admin-ajax.php?action=atd_ignore&phrase=';
	$init_array['atd_rpc_id']         = get_option('AtD_api_key');
	$init_array['atd_theme']          = 'wordpress';
	$init_array['atd_ignore_enable']  = 'true';
	$init_array['atd_strip_on_get']   = 'true';
	$init_array['atd_ignore_strings'] = get_usermeta( $user->ID, 'AtD_ignored_phrases' );
	$init_array['atd_show_types']     = get_usermeta( $user->ID, 'AtD_options' );
	$init_array['gecko_spellcheck']   = 'false';

	return $init_array;
}

/* add some vars into the AtD plugin */
add_filter( 'tiny_mce_before_init', 'AtD_change_mce_settings' );

/* init process for button control */
add_action( 'init', 'AtD_addbuttons' );

/* setup hooks for our PHP functions we want to make available via an AJAX call */
add_action( 'wp_ajax_proxy_atd', 'AtD_redirect_call' );
add_action( 'wp_ajax_atd_ignore', 'AtD_ignore_call' );    

/* add the AtD menu under the plugins menu */
add_action( 'admin_menu', 'AtD_setup_admin_menu' );
?>
