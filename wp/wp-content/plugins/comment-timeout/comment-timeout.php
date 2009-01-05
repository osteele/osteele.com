<?php
/*
Plugin Name: Comment Timeout
Plugin URI: http://www.jamesmckay.net/code/comment-timeout/
Description: Automatically closes comments on blog entries after a user-configurable period of time. It has options which allow you to keep the discussion open for longer on older posts which have had recent comments accepted, or to place a fixed limit on the total number of comments in the discussion. Activate the plugin and go to <a href="options-general.php?page=comment-timeout">Options &gt;&gt; Comment Timeout</a> to configure.
Version: 2.0.1
Author: James McKay
Author URI: http://www.jamesmckay.net/
*/

/* ========================================================================== */

/*
 * Copyright (c) 2007 James McKay
 * http://www.jamesmckay.net/
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/*
function debug($str) {
	print "<!-- $str -->\n";
}

function debug_r($obj) {
	print "<!--\n";
	print $obj;
	print "\n-->\n";
}
*/
define('COMMENT_TIMEOUT_VERSION', '2.0.1');

// For compatibility with WP 2.0

if (!function_exists('wp_die')) {
	function wp_die($msg) {
		die($msg);
	}
}


class jm_CommentTimeout
{
	var $settings;

	/* ====== Constructor ====== */

	/**
	 * Initialises the plugin, setting up the actions and filters.
	 */

	function jm_CommentTimeout()
	{
		add_filter('the_posts', array(&$this, 'process_posts'));
		add_action('admin_menu', array(&$this, 'add_config_page'));
		add_action('dbx_post_sidebar', array(&$this, 'post_sidebar'));
		add_action('dbx_page_sidebar', array(&$this, 'post_sidebar'));
		// Needs to be called before Akismet
		add_filter('preprocess_comment', array(&$this, 'preprocess_comment'), 0);
		add_action('save_post', array(&$this, 'save_post'));
		add_action('comment_form', array(&$this, 'comment_form'));
	}

	/* ====== get_settings ====== */

	/**
	 * Retrieves the settings from the WordPress settings database.
	 */

	function get_settings()
	{
		// Defaults for the settings

		$this->defaultSettings = array(
			// Number of days from posting before post is stale
			'PostAge' => 120,
			// Number of days from last comment before post is stale
			'CommentAge' => 60,
			// Number of days from last comment before popular post is stale
			'CommentAgePopular' => 365,
			// Definition of a popular post (number of approved comments)
			'PopularityThreshold' => 20,
			// Indicates whether to 'close' (default) or 'moderate' comments on old posts
			'Mode' => 'close',
			// Whether to treat pings 'together' with posts (true or default),
			// 'independent' of posts, or 'ignore' (false)
			'DoPings' => 'together',
			// Whether to apply these rules to pages, images and file uploads
			'DoPages' => FALSE,
			// Whether to allow overrides
			'AllowOverride' => true
		);

		if (!isset($this->settings)) {

			$this->settings = get_option('jammycakes_comment_locking');
			if (FALSE === $this->settings) {
				$this->settings = $this->defaultSettings;
				add_option('jammycakes_comment_locking', $this->settings);
			}
			else if (!isset($this->settings['UniqueID'])) {
				$this->settings = array_merge($this->defaultSettings, $this->settings);
				update_option('jammycakes_comment_locking', $this->settings);
			}
			else {
				$this->settings = array_merge($this->defaultSettings, $this->settings);
			}
			$this->sanitize_settings();
		}
		return $this->settings;
	}

	/* ====== save_settings ====== */

	/**
	 * Saves the settings
	 */

	function save_settings()
	{
		$this->get_settings();

		// Insert the new settings, with validation and type coercion

		foreach ($this->defaultSettings as $k=>$v) {
			$this->settings[$k] = $_POST[$k];
		}
		$this->sanitize_settings();
		update_option('jammycakes_comment_locking', $this->settings);
	}

	/* ====== sanitize_settings ====== */

	/**
	 * Makes sure settings are all in the correct format,
	 * also converts CT 1.0 versions to CT 2.0
	 */

	function sanitize_settings()
	{
		foreach (array_keys($this->settings) as $k) { // iterator safe
			$v = $this->settings[$k];
			switch ($k) {
				case 'PostAge':
				case 'CommentAge':
				case 'CommentAgePopular':
				case 'PopularityThreshold':
					$this->settings[$k] = (int) $v;
					break;
				case 'AllowOverride':
				case 'DoPages':
					$this->settings[$k] = (bool) $v;
					break;
				case 'DoPings':
					if ('ignore' !== $v && 'independent' !== $v && 'together' !== $v) {
						$this->settings[$k] = 'together';
					}
					break;
				case 'Mode':
					$v = (string) $v;
					if ($v != 'moderate') {
						$this->settings['Mode'] = 'close';
					}
					break;
				default:
					unset ($this->settings[$k]);
			}
		}
	}


	/* ====== get_post_metainfo ====== */

	/**
	 * Gets comment, trackback and individual setting information for comments
	 * @param $first The numerical ID of the first post to examine
	 * @param $last The numerical ID of the last post to examine
	 * @param $what 'comments', 'pings' or something else
	 * @param $overrides true or false
	 * @returns An array of objects containing the results of the query
	 */

	function get_post_metainfo($first, $last, $what, $overrides)
	{
		global $wpdb;
		$sql = 'select p.ID as ID, ' .
			($overrides ? 'pm.meta_value as comment_timeout, ' : '') .
			'count(c.comment_ID) as comments, max(c.comment_date_gmt) as last_comment ' .
			"from $wpdb->posts p " .
			"left join $wpdb->comments c on p.ID=c.comment_post_ID and c.comment_approved='1' ";
		switch ($what) {
			case 'comments':
				$sql .= 'and c.comment_type=\'\' ';
				break;
			case 'pings':
				$sql .= 'and c.comment_type<>\'\' ';
				break;
		}
		if ($overrides) {
			$sql .= "left join $wpdb->postmeta pm on p.ID=pm.post_id and pm.meta_key='_comment_timeout' ";
		}
		$sql .= 'where p.ID>=' . (int) $first . ' and p.ID<=' . (int) $last .
			' group by p.ID';
		if ($overrides) {
			$sql .= ', pm.meta_value';
		}

		$results = $wpdb->get_results($sql);

		// Set it up as an associative array indexed by ID
		$meta = array();
		foreach ($results as $r) {
			$meta[$r->ID] = $r;
		}

		return $meta;
	}

	/* ====== process_posts ====== */

	/**
	 * Goes through the list of posts, checking each one to see if it should
	 * have comments closed.
	 */

	function process_posts($posts)
	{
		// Check that we have an array of posts

		if (!is_array($posts)) {
			// Is it a single post? If so, process it as an array of posts
			if (is_object($posts) && isset($posts->comment_status)) {
				$p = $this->process_posts(array($posts));
				return $p[0];
			}
			else {
				// Otherwise don't do anything
				return $posts;
			}
		}

		// OK so now we have an array, let's process the posts
		// First, get the minimum and maximum post IDs

		$this->get_settings();

		$minID = $maxID = 0;
		foreach ($posts as $p) {
			if ($maxID < $p->ID) {
				$maxID = $p->ID;
			}
			if ($minID == 0 || $minID > $p->ID) {
				$minID = $p->ID;
			}
		}

		// Get the metainfo for the posts

		switch($this->settings['DoPings']) {
			case 'ignore':
			case false:	// for CT 1.x compatibility
				$commentmeta = $this->get_post_metainfo
					($minID, $maxID, 'comments', $this->settings['AllowOverride']);
				$pingmeta = null;
				break;
			case 'independent':
				$commentmeta = $this->get_post_metainfo
					($minID, $maxID, 'comments', $this->settings['AllowOverride']);
				$pingmeta = $this->get_post_metainfo
					($minID, $maxID, 'pings', $this->settings['AllowOverride']);
				break;
			case 'together':
			case true:
			default:
				$commentmeta = $this->get_post_metainfo
					($minID, $maxID, '', $this->settings['AllowOverride']);
				$pingmeta =& $commentmeta;
		}

		// Now calculate the date and time (UTC) of when to close the post

		// NB need to get the keys and values this way because PHP 4 gets funny
		// about references if you do foreach ($posts as $k => $p)

		foreach (array_keys($posts) as $k) {
			$p =& $posts[$k];
			$cm = $commentmeta[$p->ID];

			/*
			 * Preconditions: skip if either of the following are true:
			 * 1. Is a non-post and we are only checking posts
			 * 2. Is flagged for ignore and we are allowing overrides
			 */

			$isPost = ($p->post_status == 'publish' || $p->post_status == 'private')
				&& ($p->post_type == '' || $p->post_type == 'post');

			$proceed = ($isPost || $this->settings['DoPages']) &&
				(@$cm->comment_timeout != 'ignore' || !$this->settings['AllowOverride']);

			/*
			 * Per-post settings are stored in a post meta field called
			 * _comment_timeout. This can have one of three values:
			 * "ignore" means we don't close comments
			 * "default" (or nothing) means we use the default settings
			 * two integers separated by a comma means we use per-post settings
			 * - in this case the integers represent the days from the post and
			 *   the last comment respectively
			 */

			if ($proceed) {

				if (@preg_match('|^(\d+),(\d+)$|', $cm->comment_timeout, $matches)) {
					list($dummy, $postAge, $commentAge) = $matches;
					$commentAgePopular = $commentAge;
					$popularityThreshold = 0;
				}
				else {
					// These are the global settings
					$postAge = $this->settings['PostAge'];
					$commentAge = $this->settings['CommentAge'];
					$commentAgePopular = $this->settings['CommentAgePopular'];
					$popularityThreshold = $this->settings['PopularityThreshold'];
				}

				$cutoff = strtotime($p->post_date_gmt) + 86400 * $postAge;
				if ($cm->last_comment != '') {
					$cutoffComment = strtotime($cm->last_comment) + 86400 *
						($cm->comments >= $popularityThreshold
						? $commentAgePopular : $commentAge);
					if ($cutoffComment > $cutoff) $cutoff = $cutoffComment;
				}
				// Cutoff for comments
				$p->cutoff_comments = $cutoff;

				if (isset($pingmeta)) {
					$pm =& $pingmeta[$p->ID];
					$cutoff = strtotime($p->post_date_gmt) + 86400 * $postAge;
					if ($pm->last_comment != '') {
						$cutoffPing = strtotime($pm->last_comment) + 86400 *
							($pm->comments >= $popularityThreshold
							? $commentAgePopular : $commentAge);
						if ($cutoffPing > $cutoff) $cutoff = $cutoffPing;
					}
					// Cutoff for pings
					$p->cutoff_pings = $cutoff;
				}

				/*
				 * Now set the comment status. We only do this if we are
				 * closing comments -- if we are moderating instead, we need to
				 * leave the comment form open
				 */

				if ($this->settings['Mode'] != 'moderate') {
					$now = time();
					if (isset($p->cutoff_comments) && $now > $p->cutoff_comments) {
						$p->comment_status = 'closed';
					}
					if (isset($p->cutoff_pings) && $now > $p->cutoff_pings) {
						$p->ping_status = 'closed';
					}
				}
			} // Post processing ends here
		}

		return $posts;
	}

	/* ====== preprocess_comment filter ====== */

	/**
	 * Process a submitted comment. Die if it's not OK
	 */

	function preprocess_comment($comment)
	{
		global $wpdb;
		$this->get_settings();
		$post = get_post($comment['comment_post_ID']);
		$post = $this->process_posts($post);
		
		$now = time();
		$isPing = ($comment['comment_type'] == 'trackback' || $comment['comment_type'] == 'pingback');
		$isClosed = $isPing ? ($post->ping_status == 'closed') : ($post->comment_status == 'closed');
		if ($isPing) {
			$timedOut = isset($post->cutoff_pings) && ($now > $post->cutoff_pings);
		}
		else {
			$timedOut = isset($post->cutoff_comments) && ($now > $post->cutoff_comments);
		}

		switch ($this->settings['Mode']) {
			case 'moderate':
				if ($timedOut) {
					// This filter needs to run before the one inserted by Akismet
					add_filter('pre_comment_approved', create_function('$a', 'return 0;'), 0);
				}
				break;
			case 'close':
			default:
				if ($isClosed || $timedOut) {				 
					do_action('comment_closed', $comment->comment_post_ID);
					wp_die('Sorry, comments are closed for this item.');
				}
				break;
		}
		return $comment;
	}

	/* ====== save_post ====== */

	/**
	 * Called when a post or page is saved. Updates CT's per-post settings
	 * from the bit in the sidebar.
	 */

	function save_post($postID)
	{
		$this->get_settings();
		if ($this->settings['AllowOverride']) {
			switch(@$_POST['CommentTimeout']) {
				case 'ignore':
					$setting = 'ignore';
					break;
				case 'custom':
					$setting = (int)$_POST['ctPostAge'] . ',' . (int)$_POST['ctCommentAge'];
					break;
				case 'default':
				default:
					$setting = false;
					break;
			}

			if ($setting !== false) {
				if (!update_post_meta($postID, '_comment_timeout', $setting)) {
					add_post_meta($postID, '_comment_timeout', $setting);
				}
			}
			else {
				delete_post_meta($postID, '_comment_timeout');
			}
		}
	}

	/* ====== add_config_page ====== */

	/**
	 * Adds the configuration page to the submenu
	 */

	function add_config_page()
	{
		add_submenu_page('options-general.php', __('Comment Timeout'), __('Comment Timeout'), 'manage_options', 'comment-timeout', array(&$this, 'config_page'));
	}

	/* ====== config_page ====== */

	/**
	 * Loads in and renders the configuration page in the dashboard.
	 */

	function config_page()
	{
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$this->save_settings();
			echo '<div id="comment-locking-saved" class="updated fade-ffff00"">';
			echo '<p><strong>';
			_e('Options saved.');
			echo '</strong></p></div>';
		}
		else {
			$this->get_settings();
		}
		require_once(dirname(__FILE__) . '/comment-timeout.config.php');
	}

	/* ====== post_sidebar ====== */

	/**
	 * Adds an entry to the post's sidebar to allow us to set simple comment
	 * settings on a post-by-post basis.
	 */

	function post_sidebar()
	{
		$this->get_settings();
		if ($this->settings['AllowOverride']) {
			require_once(dirname(__FILE__) . '/comment-timeout.post.php');
		}
	}


	/* ====== comment_form ====== */

	function comment_form()
	{
		global $post;
		$this->get_settings();
		if (isset ($post->cutoff_comments)) {
			$ct = $post->cutoff_comments - time();
			if ($ct < 0 && $this->settings['Mode'] == 'moderate') {
				echo '<p class="comment-timeout">Comments will be sent to the moderation queue.</p>';
			}
			elseif ($ct >= 0 && $this->settings['Mode'] == 'close') {
				$ct1 = $post->cutoff_comments + (get_option('gmt_offset') * 3600);
				echo '<p class="comment-timeout">Comments for this post will be closed ';
				if ($ct >= 604800) {
					echo 'on ' . date('j F Y', $ct1);
				}
				else if ($ct >= 172800) {
					echo 'in ' . (int) ($ct/86400) . ' days';
				}
				else if ($ct >= 7200) {
					echo 'in ' . (int) ($ct/3600) . ' hours';
				}
				else if ($ct >= 60) {
					echo 'in ' . (int) ($ct/60) . ' minutes';
				}
				else {
					echo 'within one minute.';
				}
				echo '.</p>';
			}
		}
	}
}

$myCommentTimeout = new jm_CommentTimeout();

?>