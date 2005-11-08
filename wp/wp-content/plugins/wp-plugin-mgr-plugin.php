<?php
/*
Plugin Name: WP Plugin Manager
Plugin URI: http://unknowngenius.com/wp-plugins/
Version: 1.6.4.b
Author: dr Dave
Author URI: http://unknowngenius.com/blog/
Description: WPPM auto-installed through One-click Install. <a href="../wp-plugin-mgr.php">Click here</a> to use it.
*/

function add_wppm_menu()
{
 if (function_exists('add_options_page')) {
  add_options_page('WP Plugin Mgr', 'WP Plugin Mgr', 9, '../wp-plugin-mgr.php');
 }
 
}

add_action('admin_head', 'add_wppm_menu');

?>