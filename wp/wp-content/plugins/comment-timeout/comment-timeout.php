<?php
/*
Plugin Name: Comment Timeout
Plugin URI: http://bitbucket.org/jammycakes/comment-timeout/
Description: Automatically closes comments on blog entries after a user-configurable period of time. It has options which allow you to keep the discussion open for longer on older posts which have had recent comments accepted, or to place a fixed limit on the total number of comments in the discussion. Activate the plugin and go to <a href="options-general.php?page=comment-timeout">Options &gt;&gt; Comment Timeout</a> to configure.
Version: 2.4.0
Author: James McKay
Author URI: http://jamesmckay.net/
*/

define('COMMENT_TIMEOUT_VERSION', '2.4.0');

if (version_compare(phpversion(), '5.2', '<')) {
	add_action('admin_notices',
		create_function('',
			'echo \'<div class="error">' +
			'<p>Comment Timeout no longer supports PHP 4. ' +
			'Please upgrade your server to PHP 5.2 or later.</p></div>\';'
		)
	);
}
else {
	require_once(dirname(__FILE__) . '/php/class.core.php');
}