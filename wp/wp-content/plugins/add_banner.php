<?php
/*
Plugin Name: Add Banner
Plugin URI: http://osteele.com/sources/php/wp_add_banner
Description: Add a banner to the bottom of each page.
Version: 0.1
Date: February 10, 2009
Author: Oliver Steele
Author URI: http://osteele.com
*/ 

function add_banner_add_post_header() {
	global $add_banner_added_header;
	if ($add_banner_added_header) return;
	$add_banner_added_header = true;
	echo("<link href='/stylesheets/banner.css' rel='stylesheet' type='text/css' />");
	echo("<link href='/stylesheets/banner.iphone.css' rel='stylesheet' type='text/css' media='only screen and (max-device-width: 480px)' />");

}

function add_banner_add_post_footer() {
	global $add_banner_added_footer;
	if ($add_banner_added_footer) return;
	$add_banner_added_footer = true;
	include(ABSPATH . '../includes/footer-banner.php');
}

add_action('wp_head', 'add_banner_add_post_header', 0);
add_action('wp_footer', 'add_banner_add_post_footer', 0);
?>
