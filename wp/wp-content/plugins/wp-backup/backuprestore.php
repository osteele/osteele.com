<?php
/*
Plugin Name: Backup Restore
Plugin URI: http://dinki.mine.nu/word/
Description: Backup and restore your weblog's database, files and folders.
Author: LaughingLizard (dinki@mac.com)
Version: 1.5
Author URI: http://dinki.mine.nu/weblog/
*/

if ( ! is_plugin_page() ) {
	function br_add_menu() {
		add_menu_page('Backup/Restore', 'Backup/Restore', 9, dirname(__FILE__) . '/backuprestoreAdmin.php');
	}
	
	add_action('admin_menu', 'br_add_menu');
 }
?>