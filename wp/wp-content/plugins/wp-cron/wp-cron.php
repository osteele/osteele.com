<?php
/*
Plugin Name: WP-Cron
Plugin URI: http://www.skippy.net/blog/plugins/
Description: periodic execution of actions
Version: 1.3
Author: Scott Merrill
Author URI: http://www.skippy.net/

Copyright (c) 2005 Scott Merrill (skippy@skippy.net)
Released under the terms of the GNU GPL
*/

add_action('plugins_loaded', 'wp_cron_init');

///////////////////////
function wp_cron_init() {
	// first, get the current time
	$wp_cron_now = time();

	$this_year = date('Y', $wp_cron_now);
	$this_month = date('m', $wp_cron_now);
	$this_day = date('d', $wp_cron_now);
	$this_hour = date('h', $wp_cron_now);
	$daily = mktime(0, 0, 1, $this_month, $this_day, $this_year);
	$hourly = mktime($this_hour, 0, 0, $this_month, $this_day, $this_year);

	// fetch the timestamps
	if ( (FALSE === get_option('wp_cron_15_lastrun')) 
		|| (FALSE === get_option('wp_cron_hourly_lastrun'))
		|| (FALSE === get_option('wp_cron_daily_lastrun')) )
	{
		update_option('wp_cron_15_lastrun', $wp_cron_now);
		update_option('wp_cron_hourly_lastrun', $hourly);
		update_option('wp_cron_daily_lastrun', $daily);
	}
	$wp_cron_15_lastrun = intval(get_option('wp_cron_15_lastrun'));
	$wp_cron_hourly_lastrun = intval(get_option('wp_cron_hourly_lastrun'));
	$wp_cron_daily_lastrun = intval(get_option('wp_cron_daily_lastrun'));

	if ($wp_cron_now > ($wp_cron_daily_lastrun + 86400)) {
		update_option('wp_cron_daily_lastrun', $daily);
		add_action('shutdown', 'wp_cron_daily_exec');
	}
	if ($wp_cron_now > ($wp_cron_hourly_lastrun + 3600)) {
		update_option('wp_cron_hourly_lastrun', $hourly);
		add_action('shutdown', 'wp_cron_hourly_exec');
	}
	if ($wp_cron_now > ($wp_cron_15_lastrun + 900)) {
		update_option('wp_cron_15_lastrun', $wp_cron_now);
		add_action('shutdown', 'wp_cron_15_exec');
	}
}

//////////////////////////
// these execute the various hooks
function wp_cron_15_exec() {
	do_action('wp_cron_15');
}

function wp_cron_hourly_exec() {
	do_action('wp_cron_hourly');
}

function wp_cron_daily_exec() {
	do_action('wp_cron_daily');
}

?>
