<?php
/*
Plugin Name: Customizable Post Listings
Version: 1.1
Plugin URI: http://www.coffee2code.com/wp-plugins/
Author: Scott Reilly
Author URI: http://www.coffee2code.com
Description: Display Recent Posts, Recently Commented Posts, Recently Modified Posts, Random Posts, and other post listings using the post information of your choosing in an easily customizable manner.  You can narrow post searches by specifying categories and/or authors, among other things.

*/

/*
Copyright (c) 2004 by Scott Reilly (aka coffee2code)

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

function c2c_get_recent_posts ($num_posts = 5,
	$format = "<li>%post_date%: %post_URL%</li>",
	$categories = '',		// space separated list of category IDs -- leave empty to get all
	$orderby = 'date',
	$order = 'DESC',		// either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,			// number of posts to skip
	$date_format = 'm/d/Y',		// Date format, php-style, if different from blog's date-format setting
	$authors = '',			// space separated list of author IDs -- leave empty to get all
	$include_passworded_posts = false) 
{
	global $wpdb, $tablecomments, $tableposts, $tablepost2cat;
	if ($add_recent_comment_to_sql && !isset($tablecomments)) $tablecomments = $wpdb->comments;
	if (!isset($tablepost2cat)) $tablepost2cat = $wpdb->post2cat;
	if (!isset($tableposts)) $tableposts = $wpdb->posts;
	if ($order != 'ASC') $order = 'DESC';
	if ('max_comment_date' == $orderby) { $add_recent_comment_to_sql = 1; }
	else {
		if ($orderby != 'rand()') $orderby = "$tableposts.post_$orderby";
		$add_recent_comment_to_sql = 0;
	}
	$include_sticky_posts = true;
	
	$now = current_time('mysql');
	if ($add_recent_comment_to_sql) $sql = "SELECT $tableposts.*, MAX(comment_date) AS max_comment_date FROM $tablecomments, $tableposts ";
	else $sql = "SELECT DISTINCT * FROM $tableposts ";
	if ($categories) {
		$sql .= "LEFT JOIN $tablepost2cat ON ($tableposts.ID = $tablepost2cat.post_id) ";
		$cats = explode(' ', $categories);
	}
	$sql .= "WHERE $tableposts.post_date <= '$now' AND ( $tableposts.post_status = 'publish' ";
	if ($include_sticky_posts) $sql .= "OR $tableposts.post_status = 'sticky' ";
	$sql .= ") ";
	if (!$include_passworded_posts) $sql .= "AND $tableposts.post_password = '' ";
	if ($add_recent_comment_to_sql) $sql .= "AND $tableposts.ID = $tablecomments.comment_post_ID AND $tablecomments.comment_approved = '1' ";
	if ($categories) {
		$first = 1;
		$sql .= "AND ( ";
		foreach ($cats as $cat) {
			if ($first) $first = 0;
			else $sql .= "OR ";
			$sql .= "$tablepost2cat.category_id = '$cat' ";
		}
		$sql .= ") ";
	}
	if ($authors) {
		$auths = explode(' ', $authors);
		$first = 1;
		$sql .= "AND ( ";
		foreach ($auths as $author) {
			if ($first) $first = 0;
			else $sql .= "OR ";
			$sql .= "$tableposts.post_author = '$author' ";
		}
		$sql .= ") ";
	}
	if ('modified' == $orderby) $sql .= "AND $tableposts.post_modified_gmt <= '$now' ";
	$sql .= "GROUP BY $tableposts.ID ORDER BY $orderby $order";
	if ($num_posts) $sql .= " LIMIT $offset, $num_posts";
	$posts = array();
	$posts = $wpdb->get_results($sql);
	if (empty($posts)) return;
	return c2c_get_recent_handler($posts, $format, $date_format);
} //end function c2c_get_recent_posts()

function c2c_get_random_posts($num_posts = 5,
        $format = "<li>%post_date%: %post_URL%</li>",
        $categories = '',               // space separated list of category IDs -- leave empty to get all
        $order = 'DESC',                // either 'ASC' (ascending) or 'DESC' (descending)
        $offset = 0,                    // number of posts to skip
        $date_format = 'm/d/Y',         // Date format, php-style, if different from blog's date-format setting
        $authors = '',                  // space separated list of author IDs -- leave empty to get all
        $include_passworded_posts = false)
{
        return c2c_get_recent_posts($num_posts, $format, $categories, 'rand()', $order, $offset, $date_format, $authors, $include_passworded_posts);
} //end function get_random_post()

function c2c_get_recently_commented ($num_posts = 5, 
	$format = "<li>%comments_URL%<br />%last_comment_date%<br />%comments_fancy%</li>",
	$categories = '',		// space separated list of category IDs -- leave empty to get all
	$order = 'DESC',		// either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,			// number of posts to skip
	$date_format = 'm/d/Y h:i a',	// Date format, php-style, if different from blog's date-format setting
	$authors = '',			// space separated list of author IDs -- leave empty to get all
	$include_passworded_posts = false)
{
	return c2c_get_recent_posts($num_posts, $format, $categories, 'max_comment_date', $order, $offset, $date_format, $authors, $include_passworded_posts);
} //end function get_recently_commented()

function c2c_get_recently_modified ($num_posts = 5,
	$format = "<li>%post_URL%<br />Updated: %post_modified%</li>",
	$categories = '',		// space separated list of category IDs -- leave empty to get all
	$order = 'DESC',		// either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,			// number of posts to skip
	$date_format = 'm/d/Y',		// Date format, php-style, if different from blog's date-format setting
	$authors = '',			// space separated list of author IDs -- leave empty to get all
	$include_passworded_posts = false)
{
	return c2c_get_recent_posts($num_posts, $format, $categories, 'modified', $order, $offset, $date_format, $authors, $include_passworded_posts);
} //end function c2c_get_recently_modified()

//
// ************************ END TEMPLATE TAGS ********************************************************************
//

/*function c2c_comment_count ($post_id) {
	global $wpdb, $tablecomments;
	if (!isset($tablecomments)) $tablecomments = $wpdb->comments;
	return $wpdb->get_var("SELECT COUNT(*) FROM $tablecomments WHERE comment_post_ID = '$post_id'");
} //end function c2c_comment_count()
*/

function c2c_get_recent_tagmap ($posts, $format, $tags, $date_format) {
	if (!$tags) return $format;
	global $authordata, $post;
	
	//-- Some things you might want to configure -----
	$excerpt_words = 6;	// Number of words to use for %post_excerpt_short%
	$excerpt_length = 50; 	// Number of characters to use for %post_excerpt_short%, only used if $excerpt_words is 0
	$idmode = 'nickname';	// how to present post author name
	$comment_fancy = array('No comments', '1 Comment', '%comments_count% Comments');
	//-- END configuration section -----

	if (!$date_format) $date_format = get_settings('date_format');
	
	// Now process the posts
	$orig_post = $post; $orig_authordata = $authordata;
	foreach ($posts as $post) {
		$text = $format;
		$comment_count = '';
		$authordata = '';
		$title = '';

		// If want last_comment information, then need to make a special db request
		$using_last_comment = 0;
		foreach ($tags as $tag) {
			if (strpos($tag, 'last_comment') !== false) { $using_last_comment = 1; break; }
		}
		if ($using_last_comment) {
			global $wpdb, $tablecomments, $comment;
			if (!isset($tablecomments)) $tablecomments = $wpdb->comments;
			$comment = $wpdb->get_row("SELECT * FROM $tablecomments WHERE comment_post_ID = '$post->ID' AND comment_approved = '1' ORDER BY comment_date DESC LIMIT 1");
		}
		
		// Perform percent substitutions
		foreach ($tags as $tag) {
			switch ($tag) {
				case '%comments_count%':
					if (!$comment_count) { $comment_count = c2c_comment_count($post->ID); }
					$new = $comment_count;
					break;
				case '%comments_fancy%':
					if (!$comment_count) { $comment_count = c2c_comment_count($post->ID); }
					if ($comment_count < 2) $new = $comment_fancy[$comment_count];
					else $new = str_replace('%comments_count%', $comment_count, $comment_fancy[2]);
					break;
				case '%comments_url%':
					$new = get_permalink() . "#postcomment";
					break;
				case '%comments_URL%':
					if (!$title) { $title = the_title('', '', false); }
					$new = '<a href="'.get_permalink().'#comments" title="View all comments for '.htmlspecialchars(strip_tags($title)).'">'.$title.'</a>';
					break;
				case '%last_comment_date%':
					$new = mysql2date($date_format, $comment->comment_date);
					break;
				case '%last_comment_id%':
					$new = $comment->comment_ID;
					break;
				case '%last_comment_url%':
					$new = get_permalink().'#comment-'.$comment->comment_ID;
					break;
				case '%last_commenter%':
					$new = apply_filters('comment_author', $comment->comment_author);
					break;
				case '%last_commenter_URL%':
					$author = apply_filters('comment_author', $comment->comment_author);
					$new = '<a href="'.apply_filters('comment_url', $comment->comment_author_url).'" title="Visit '.$author.'\'s site">'.$author.'</a>';
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
					$new .= sprintf(__("Posts by %s"), htmlspecialchars(the_author($idmode, false))).'">'.stripslashes(the_author($idmode, false)).'</a>';
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
					$new = mysql2date($date_format, $post->post_date);
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
				case '%post_title%':
					if (!$title) { $title = the_title('', '', false); }
					$new = $title;
					break;
				case '%post_url%':
					$new = get_permalink();
					break;
				case '%post_URL%':		
					if (!$title) { $title = the_title('', '', false); }
					$new = $new = '<a href="'.get_permalink().'" title="View post '.htmlspecialchars(strip_tags($title)).'">'.$title.'</a>';
					break;
			}
			$text = str_replace($tag, $new, $text);
		}
		echo $text . "\n";
	}
	$post = $orig_post; $authordata = $orig_authordata;
	return;
} // end function c2c_get_recent_tagmap()

function c2c_get_recent_handler ($posts, $format = '', $date_format = '') {
	if (!$format) { return $posts; }
	
	// Determine the format of the listing
	$percent_tags = array(
		"%comments_count%",	// Number of comments for post
		"%comments_fancy%",	// Fancy reporting of comments: (see get_recent_tagmap())
		"%comments_url%", 	// URL to top of comments section for post
		"%comments_URL%",	// Post title linked to the top of the comments section on post's permalink page
		"%last_comment_date%",  // Date of last comment for post
		"%last_comment_id%",	// ID for last comment for post
		"%last_comment_URL%",	// URL to most recent comment for post
		"%last_commenter%",	// Author of last comment for post
		"%last_commenter_URL%", // Linked (if author URL provided) of author of last comment for post
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
		"%post_title%",		// Title for post
		"%post_url%",		// URL for post
		"%post_URL%",		// Post title linked to post's permalink page
	);
	$ptags = array();
	foreach ($percent_tags as $tag) { if (strpos($format, $tag) !== false) $ptags[] = $tag; }
	return c2c_get_recent_tagmap($posts, $format, $ptags, $date_format);
} //end function c2c_get_recent_handler()

?>