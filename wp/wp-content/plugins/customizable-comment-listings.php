<?php
/*
Plugin Name: Customizable Comment Listings
Version: 0.9
Plugin URI: http://www.coffee2code.com/wp-plugins/
Author: Scott Reilly
Author URI: http://www.coffee2code.com
Description: Display Recent Comments, Pingbacks, and/or Trackbacks, as well as other comment listings using the comment and/or post information of your choosing in an easily customizable manner.  You can narrow comment searches by specifying post IDs, comment types, and/or comment status, among other things.

=>> Visit the plugin's homepage for more information and latest updates  <<=


Installation:

1. Download the file http://www.coffee2code.com/wp-plugins/customizable-comment-listings.zip and unzip it into your 
/wp-content/plugins/ directory.
-OR-
Copy and paste the the code ( http://www.coffee2code.com/wp-plugins/customizable-comment-listings.phps ) into a file called 
customizable-comment-listings.php, and put that file into your /wp-content/plugins/ directory.
2. Activate the plugin from your WordPress admin 'Plugins' page.
3. In your sidebar.php (or other template file), insert calls to comment listings functions provided by the plugin.

*/

/*
Copyright (c) 2005 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

//
// ************************ START TEMPLATE TAGS ******************************************************************
//

if (! isset($wpdb->comments) ) {
	global $tablecomments;
	$wpdb->comments = $tablecomments;
}
if (! isset($wpdb->posts) ) {
	global $tableposts;
	$wpdb->posts = $tableposts;
}

// $mode can be 'comments', 'topcommenters', or 'recentcommenters'
function c2c_get_comments( $limit = 5,
	$format = "",
	$mode = 'comments',		// can be "comments", "commenters", "commented"
	$types = 'comment pingback trackback',
	$status = '1',			// space-separated list of comment statuses to be required; leave blank for all; values could be one or more of '1', '0', 'spam'
	$ids = '',			// space-separated list of post IDs that should be the only ones searched
	$order = 'DESC',		// either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,			// number of posts to skip
	$date_format = 'm/d/Y',		// Date format, php-style, if different from blog's date-format setting
	$include_passworded_posts = false )
{
	global $wpdb;
	if ( $orderby != 'ASC' ) $orderby = 'DESC';

	
	/* ================= CONFIGURATION SECTION ============================ */
	
	// The field you want to base the identity of the commenter on; must be one of 
	//	'comment_author', 'comment_author_url', or 'comment_author_email'
	$identity_field = 'comment_author_email';
	// If you would like to omit yourself or others from the listings, put the $identity_field values to exclude here:
	//	i.e. if the $identity_field is 'comment_author', then use names; if 'comment_author_email', use email addresses
	//	examples: 
	//		$exclude_from_listing = array('Me', 'Joe Bob', 'Sue');
	//		$exclude_from_listing = array('me@mysite.org', 'somebody@else.com');
	// Should exclusions be done in the first place?
	$do_exclusions = true;
	// NOTE: Exclusions are not performed if $do_exclusions is false
	$exclude_from_listing = array();
	// Open links in new browser window?
	$open_in_new_window = false;
	
	/* ================= CONFIGURATION SECTION ============================ */
	
	$is_commenters = (strpos($mode, 'commenters') !== false) ? 1 : 0;
	$sql  = "SELECT *";
	if ( $is_commenters )
		$sql .= ('topcommenters' == $mode) ? ",COUNT(comment_ID) AS total_comments" : ",MAX(comment_date) AS most_recent_comment";
	$sql .= " FROM {$wpdb->comments} WHERE ";
	
	// Handle approval status limit
	$status = explode(' ', $status);
	$first = 1;
	if ( $status ) {
		$sql .= '( ';
		foreach ($status as $stat) {
			if ($first) $first = 0;
			else $sql .= 'OR ';
			$sql .= "comment_approved = '$stat' ";
		}
		$sql .= ') ';
	}
	
	// Handle comment types
	$types = explode(' ', $types);
	$first = 1;
	if ( $types ) {
		$sql .= ' AND ( ';
		foreach ($types as $type) {
			if ($first) $first = 0;
			else $sql .= 'OR ';
			$sql .= "comment_type = '$type' ";
			if ( 'comment' == $type ) $sql .= "OR comment_type = '' ";
		}
		$sql .= ') ';
	}
	
	// Handle post IDs limit
	if ( $ids )
	 	$sql .= "AND comment_post_ID IN (" . str_replace(' ',',',$ids) . ") ";
	
	// Commenters-related stuff
	if ( $is_commenters ) {
		if ( $do_exclusions && !empty($exclude_from_listing) )
			foreach ($exclude_from_listing as $exclude) { $sql .= "AND $identity_field != '$exclude' "; }
		if ($identity_field != 'comment_author') $sql .= "AND $identity_field != '' ";
	
		$sql .= "GROUP BY $identity_field ORDER BY ";
		$sql .= ('topcommenters' == $mode) ? "total_comments " : "most_recent_comment ";
	} else {
		$sql .= 'ORDER BY comment_date ';
	}
	
	$sql .= "$order LIMIT $offset, $limit";
	
	$comments = array();
	$comments = $wpdb->get_results($sql);
	if ( empty($comments) ) return;
	return c2c_get_recent_comments_handler($comments, $format, $date_format);
// end c2c_get_comments
}

// Use this for: Recent Comments and Total Comments
function c2c_get_recent_comments( $limit = 5,
	$format = "<li>%comment_author%: %comment_excerpt_URL%</li>",
	$types = 'comment pingback trackback',
	$status = '1',			// space-separated list of comment statuses to be required; leave blank for all; values could be one or more of '1', '0', 'spam'
	$ids = '',			// space-separated list of post IDs that should be the only ones searched
	$order = 'DESC',		// either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,			// number of posts to skip
	$date_format = 'm/d/Y',		// Date format, php-style, if different from blog's date-format setting
	$include_passworded_posts = false )
{
	return c2c_get_comments($limit, $format, 'comments', $types, $status, $ids, $order, $offset, $date_format, $include_passworded_posts);
} // end c2c_get_recent_comments()

if (! function_exists('c2c_get_top_commenters') ) {
function c2c_get_top_commenters( $limit = 5,
	$format = "<li>%comment_author_URL% (%total_comments%)</li>",
	$types = 'comment pingback trackback',
	$status = '1',			// space-separated list of comment statuses to be required; leave blank for all; values could be one or more of '1', '0', 'spam'
	$ids = '',			// space-separated list of post IDs that should be the only ones searched
	$order = 'DESC',		// either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,			// number of posts to skip
	$date_format = 'm/d/Y',		// Date format, php-style, if different from blog's date-format setting
	$include_passworded_posts = false )
{
	return c2c_get_comments($limit, $format, 'topcommenters', $types, $status, $ids, $order, $offset, $date_format, $include_passworded_posts);
} // end c2c_get_commenters()
}

if (! function_exists('c2c_get_recent_commenters') ) {
function c2c_get_recent_commenters( $limit = 5,
	$format = "<li>%comment_author%: %comment_excerpt_URL%</li>",
	$types = 'comment pingback trackback',
	$status = '1',			// space-separated list of comment statuses to be required; leave blank for all; values could be one or more of '1', '0', 'spam'
	$ids = '',			// space-separated list of post IDs that should be the only ones searched
	$order = 'DESC',		// either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,			// number of posts to skip
	$date_format = 'm/d/Y',		// Date format, php-style, if different from blog's date-format setting
	$include_passworded_posts = false )
{
	return c2c_get_comments($limit, $format, 'recentcommenters', $types, $status, $ids, $order, $offset, $date_format, $include_passworded_posts);
} // end c2c_get_commenters()
}
//
// ************************ END TEMPLATE TAGS ********************************************************************
//

if (! function_exists('c2c_comment_count') ) {
	// Leave $comment_types blank to count all comment types (comment, trackback, and pingback).  Otherwise, specify $comment_types
	//	as a space-separated list of any combination of those three comment types (only valid for WP 1.5+)
	function c2c_comment_count( $post_id, $comment_types='' ) {
		global $wpdb;
		if (!isset($wpdb->comments)) {
			global $tablecomments;
			$wpdb->comments = $tablecomments;
		}
		$sql = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = '$post_id' AND comment_approved = '1'";
		if (!empty($comment_type)) {
			$sql .= " AND ( comment_type = '" . str_replace(" ", "' OR comment_type = '", $comment_types) . "' ";
			if (strpos($comment_types,'comment') !== false)
				$sql .= "OR comment_type = '' ";		//WP allows a comment_type of '' to be == 'comment'
			$sql .= ")";
		}
		return $wpdb->get_var($sql);
	} //end function c2c_comment_count()
}

function c2c_get_recent_comments_tagmap( $comments, $format, $tags, $date_format ) {
	if (!$tags) return $format;
	global $authordata, $comment, $post;
	
	//-- Some things you might want to configure -----
	$excerpt_words = 6;	// Number of words to use for %post_excerpt_short%
	$excerpt_length = 50; 	// Number of characters to use for %post_excerpt_short%, only used if $excerpt_words is 0
	$comment_excerpt_words = 6;	// Number or words to use for %comment_excerpt%
	$comment_excerpt_length = 15;	// Numbler of characters to use for %comment_excerpt%, only used if $comment_excerpt_words is 0
	$idmode = 'nickname';	// how to present post author name
	$time_format = '';
	$comment_fancy = array('No comments', '1 Comment', '%comments_count% Comments');
	$pingback_fancy = array('No pingbacks', '1 Pingback', '%pingbacks_count% Pingbacks');
	$trackback_fancy = array('No trackbacks', '1 Trackback', '%trackbacks_count% Trackbacks');
	//-- END configuration section -----

	if (!$date_format) $date_format = get_settings('date_format');
	
	// Now process the comments
	$orig_authordata = $authordata; $orig_comment = $comment; $orig_post = $post;
	// If want post information, then need to make a special db request
	$using_post = 0;
	foreach ($tags as $tag) {
		if (	strpos($tag, 'post') !== false || 
			strpos($tag, 'comments') !== false ||
			strpos($tag, 'pingbacks') != false ||
			strpos($tag, 'trackbacks') != false 	) { global $wpdb; $postscache = array(); $using_post = 1; break; }
	}
	foreach ($comments as $comment) {
		$text = $format;
		$comment_count = ''; $pingback_count = ''; $trackback_count = ''; $allcomment_count = '';
		$authordata = '';
		$title = '';

		if ($using_post) {
			// Only make db call if we don't already know about the post
			if ( !isset($postscache[$comment->comment_post_ID]) ) {
				$postscache[$comment->comment_post_ID] = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '$comment->comment_post_ID' LIMIT 1");
			}
			$post = $postscache[$comment->comment_post_ID];
		}
		
		// Perform percent substitutions
		foreach ($tags as $tag) {
			switch ($tag) {
				case '%comment_author%':
					$new = apply_filters('comment_author', get_comment_author());
					break;
				case '%comment_author_email%':
					$new = apply_filters('author_email', get_comment_author_email());
					break;
				case '%comment_author_ip%':
					$new = get_comment_author_IP();
					break;
				case '%comment_author_url%':
					$new = get_comment_author_url();
					break;
				case '%comment_author_URL%':
					$new = get_comment_author_link();
					break;
				case '%comment_date%':
					$new = get_comment_date($date_format);
					break;
				case '%comment_excerpt%':
				case '%comment_excerpt_URL%':
					$new = ltrim(strip_tags(apply_filters('comment_text', get_comment_text())));
					if ($comment_excerpt_words) {
  						$words = explode(" ", $new);
  						$new = join(" ", array_slice($words, 0, $comment_excerpt_words));
  						if (count($words) > $comment_excerpt_words) $new .= "...";
					} elseif ($comment_excerpt_length) {
   						if (strlen($new) > $comment_excerpt_length) $new = substr($new,0,$comment_excerpt_length) . "...";
					}
					$new = apply_filters('comment_excerpt', $new);
					if ( '%comment_excerpt_URL%' == $tag )
						$new = "<a href='".get_comment_link()."' title='View the entire comment'>$new</a>";
					break;
				case '%comment_id%':
					$new = get_comment_ID();
					break;
				case '%comment_text%':
					$new = apply_filters('comment_text', get_comment_text());
					break;
				case '%comment_time%':
					$new = get_comment_time($time_format);
					break;
				case '%comment_type%':
					$new = get_comment_type();
					break;
				case '%comment_link%':
				case '%comment_url%':
					$new = get_comment_link();
					break;
				
				/* THESE PERTAIN TO commenters MODE */
				case '%total_comments%':
					$new = $comment->total_comments;
					break;
				
				/* THESE ALL PERTAIN TO THE RESPECTIVE COMMENT'S POST */
				
				case '%allcomments_count%':
					if (!$allcomment_count) { $allcomment_count = c2c_comment_count($post->ID); }
					$new = $allcomment_count;
					break;
				case '%allcomments_fancy%':
					if (!$allcomment_count) { $allcomment_count = c2c_comment_count($post->ID); }
					if ($allcomment_count < 2) $new = $comment_fancy[$allcomment_count];
					else $new = str_replace('%comments_count%', $allcomment_count, $comment_fancy[2]);
					break;
				case '%comments_count%':
					if (!$comment_count) { $comment_count = c2c_comment_count($post->ID, 'comment'); }
					$new = $comment_count;
					break;
				case '%comments_count_URL%':
					if (!$title) { $title = the_title('', '', false); }
					if (!$comment_count) { $comment_count = c2c_comment_count($post->ID, 'comment'); }
					$new = '<a href="'.get_permalink().'#comments" title="View all comments for '.wp_specialchars(strip_tags($title), 1).'">'.$comment_count.'</a>';
					break;
				case '%comments_fancy%':
				case '%comments_fancy_URL%':
					if (!$comment_count) { $comment_count = c2c_comment_count($post->ID, 'comment'); }
					if ($comment_count < 2) $new = $comment_fancy[$comment_count];
					else $new = str_replace('%comments_count%', $comment_count, $comment_fancy[2]);
					if ( '%comments_fancy_URL%' == $tag )
						$new = '<a href="'.get_permalink().'#comments" title="View all comments for '.wp_specialchars(strip_tags($title), 1).'">'.$new.'</a>';
					break;
				case '%comments_url%':
					$new = get_permalink() . "#postcomment";
					break;
				case '%comments_URL%':
					if (!$title) { $title = the_title('', '', false); }
					$new = '<a href="'.get_permalink().'#comments" title="View all comments for '.wp_specialchars(strip_tags($title), 1).'">'.$title.'</a>';
					break;
				case '%pingbacks_count%':
					if (!$pingback_count) { $pingback_count = c2c_comment_count($post->ID, 'pingback'); }
					$new = $pingback_count;
					break;
				case '%pingbacks_fancy%':
					if (!$pingback_count) { $pingback_count = c2c_comment_count($post->ID, 'pingback'); }
					if ($pingback_count < 2) $new = $pingback_fancy[$pingback_count];
					else $new = str_replace('%pingbacks_count%', $pingback_count, $pingback_fancy[2]);
					break;
				case '%post_author%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					$new = the_author($idmode, false);
					break;
				case '%post_author_count%':
					$new = get_usernumposts($post->post_author);
					break;
				case '%post_author_posts%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					$new = '<a href="'.get_author_link(0, $authordata->ID, $authordata->user_nicename).'" title="';
					$new .= sprintf(__("Posts by %s"), wp_specialchars(the_author($idmode, false), 1)).'">'.stripslashes(the_author($idmode, false)).'</a>';
					break;
				case '%post_author_url%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					if ($authordata->user_url)
						$new = '<a href="'.$authordata->user_url.'" title="Visit '.the_author($idmode, false).'\'s site">'.the_author($idmode, false).'</a>';
					else
						$new = the_author($idmode, false);
					break;
				case '%post_content%':
					$new = apply_filters('the_content', $post->post_content);
					break;
				case '%post_date%':
					$new = apply_filters('the_date', mysql2date($date_format, $post->post_date));
					break;
				case '%post_excerpt%':
					$new = apply_filters('the_excerpt', get_the_excerpt());
					break;
				case '%post_excerpt_short%':
                                        $new = ltrim(strip_tags(apply_filters('the_excerpt', get_the_excerpt())));
					if ($excerpt_words) {
  						$words = explode(" ", $new);
  						$new = join(" ", array_slice($words, 0, $excerpt_words));
  						if (count($words) > $excerpt_words) $new .= "...";
					} elseif ($excerpt_length) {
   						if (strlen($new) > $excerpt_length) $new = substr($new,0,$excerpt_length) . "...";
					}
                                        break;
				case '%post_id%':
					$new = $post->ID;
					break;
				case '%post_modified%':
					$new = mysql2date($date_format, $post->post_modified);
					break;
				case '%post_status%':
					$new = apply_filters('post_status', $post->post_status);
					break;
				case '%post_time%':
					$new = apply_filters('get_the_time', get_post_time($time_format));
					break;
				case '%post_title%':
					if (!$title) { $title = the_title('', '', false); }
					$new = $title;
					break;
				case '%post_url%':
					$new = get_permalink();
					break;
				case '%post_URL%':		
					if (!$title) { $title = the_title('', '', false); }
					$new = '<a href="'.get_permalink().'" title="View post '.wp_specialchars(strip_tags($title), 1).'">'.$title.'</a>';
					break;
				case '%trackbacks_count%':
					if (!$trackback_count) { $trackback_count = c2c_comment_count($post->ID, 'trackback'); }
					$new = $trackback_count;
					break;
				case '%trackbacks_fancy%':
					if (!$trackback_count) { $trackback_count = c2c_comment_count($post->ID, 'trackback'); }
					if ($trackback_count < 2) $new = $trackback_fancy[$trackback_count];
					else $new = str_replace('%trackbacks_count%', $trackback_count, $trackback_fancy[2]);
					break;
			}
			$text = str_replace($tag, $new, $text);
		}
		echo $text . "\n";
	}
	$authordata = $orig_authordata; $comment = $orig_comment; $post = $orig_post;
	return;
} // end function c2c_get_recent_comments_tagmap()

function c2c_get_recent_comments_handler( $comments, $format = '', $date_format = '' ) {
	if ( !$format ) { return $comments; }
	
	// Determine the format of the listing
	$percent_tags = array(
		"%comment_author%",	// Name of commenter
		"%comment_author_email%", // E-mail of commenter
		"%comment_author_ip%",  // IP of commenter
		"%comment_author_url%",	// URL of commenter
		"%comment_author_URL%", // Linked (if URL provided) name of commenter
		"%comment_date%",	// Date for comment
		"%comment_excerpt%",	// Excerpt for comment
		"%comment_excerpt_URL%", // Excerpt for comment hyperlink to full comment
		"%comment_id%",		// ID for content
		"%comment_link%",	// URL to comment (same as %comment_url%)
		"%comment_text%",	// Full content of the comment
		"%comment_time%",	// Time for comment
		"%comment_type%",	// Type of comment ('comment', 'pingback, or 'trackback')
		"%comment_url%",	// URL to comment
		/* THESE PERTAIN TO commenters MODE (and only work in commenters mode) */
		"%total_comments%",	// Total number of comments for a commenter
		/* THESE ALL PERTAIN TO THE RESPECTIVE COMMENT'S POST */
		"%allcomments_count%",	// Number of comments + pingbacks + trackbacks for post
		"%allcomments_fancy%",	// Fancy reporting of allcomments
		"%comments_count%",	// Number of comments for post
		"%comments_count_URL%",	// Count of number of comments linked to the top of the comments section
		"%comments_fancy%",	// Fancy reporting of comments: (see get_recent_tagmap())
		"%comments_fancy_URL%",	// Fancy reporting of comments linked to comments section
		"%comments_url%", 	// URL to top of comments section for post
		"%comments_URL%",	// Post title linked to the top of the comments section on post's permalink page
		"%pingbacks_count%",	// Number of pingbacks for post
		"%pingbacks_fancy%",	// Fancy report of trackbacks
		"%post_author%",	// Author for post
		"%post_author_count%",  // Number of posts made by post author
		"%post_author_posts%",  // Link to page of all of post author's posts
		"%post_author_url%",    // Linked (if URL provided) name of post author
		"%post_content%",	// Full content of the post
		"%post_date%",		// Date for post
		"%post_excerpt%",	// Excerpt for post
		"%post_excerpt_short%",	// Customizably shorter excerpt, suitable for sidebar usage
		"%post_id%",		// ID for post
		"%post_modified%",	// Last modified date for post
		"%post_status%",	// Post status for post
		"%post_time%",		// Time for post
		"%post_title%",		// Title for post
		"%post_url%",		// URL for post
		"%post_URL%",		// Post title linked to post's permalink page
		"%trackbacks_count",	// Number of trackbacks for post
		"%trackbacks_fancy",	// Fancy reporting of trackbacks
	);
	$ptags = array();
	foreach ($percent_tags as $tag) { if (strpos($format, $tag) !== false) $ptags[] = $tag; }
	return c2c_get_recent_comments_tagmap($comments, $format, $ptags, $date_format);
} //end function c2c_get_recent_comments_handler()

?>