<?php
/*
Plugin Name: Auto-Close Comments
Version: 0.1
Plugin URI: http://wiki.wordpress.org/Auto%20shutoff%20comments
Description: Autoclose comments after XX number of days without a cron job
Author: Scott Hanson
Author URI: http://www.papascott.de/
*/

function autoclose_comments() {
	global $wpdb, $tableposts;
	// Set $age_cutoff to the age at which a post should become stale
	$age_cutoff = '21 DAY';
	$cutoff_date = $wpdb->get_var ("SELECT DATE_ADD(DATE_SUB(CURDATE(), INTERVAL $age_cutoff), INTERVAL 1 DAY)");
	$wpdb->query ("UPDATE $tableposts SET comment_status = 'closed' WHERE post_date < '$cutoff_date' AND post_status = 'publish'");
}
add_action('publish_post', 'autoclose_comments', 7);
add_action('edit_post', 'autoclose_comments', 7);
add_action('delete_post', 'autoclose_comments', 7);
add_action('comment_post', 'autoclose_comments', 7);
add_action('trackback_post', 'autoclose_comments', 7);
add_action('pingback_post', 'autoclose_comments', 7);
add_action('edit_comment', 'autoclose_comments', 7);
add_action('delete_comment', 'autoclose_comments', 7);
add_action('template_save', 'autoclose_comments', 7);
?>
