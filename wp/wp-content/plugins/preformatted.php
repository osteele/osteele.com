<?php

/*
Plugin Name: PreFormatted
Plugin URI: http://vapourtrails.ca/wp-preformatted
Version: 2.0-rc1
Description: Saves formatted versions of posts & comments to the database so that they are not formatted with every page view.
Author: Jerome Lavigne
Author URI: http://vapourtrails.ca
*/

/*  Copyright 2005  Jerome Lavigne  (email : jerome@vapourtrails.ca)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* Credits:
	Many thanks to Denis de Bernardy (http://semiologic.com) for ideas, patches and the courage to test this on his site!
*/

/* ChangeLog:

5-May-2005:  Version 2.0-rc1 released
	- Fixed bug where the edit screen preview was not getting formatted at all.

25-Apr-2005:  Version 2.0-beta released
	- Complete rewrite of plugin to store formatted data separately from original.
	- Formatting filters are no longer hardcoded, allowing PreFormatted to work with plugins such as Markdown.

25-Mar-2005:  Version1.0 released


TODO List:
	- Pre-format comment_excerpt()
	- Add filtering for other database values that pass through wptexturize(), etc.
*/

class PreFormatted {
	var $version = "2.0-beta";
	
	var $filterprefix = 'preformatted_';
	var $formatfilters = array('content' => 'the_content',
	                           'excerpt' => 'the_excerpt',
	                           'comment' => 'comment_text');
	var $movedfilters = array();
	
	var $formattedval = array('content' => '', 'excerpt' => '', 'comment' => '');
	var $doingexcerpt = false;

	var $batchmode = true;			// save all queries for execution during shutdown hook
	var $updateQueries = array(); 	// array of arrays('query', 'filter', 'id')

	
	function PreFormatted() {
		/* filters & actions for preformatting post content
			'content_save_pre' and 'phone_content' are used to grab the post content,
			'publish_post' and 'edit_post' provide the post number and trigger the save */
		add_filter('content_save_pre', array(&$this, 'grabContent'), 99);
		add_filter('phone_content',    array(&$this, 'grabContent'), 99);
		add_action('publish_post',     array(&$this, 'saveContent'), 99);
		add_action('edit_post',        array(&$this, 'saveContent'), 99);
		
		/* filters & actions for preformatting post excerpt
			'excerpt_save_pre' _should_ be used to grab the post content, but this skips some
			processing that WP does on the fly, so the excerpt is simply cleared here
			'publish_post' and 'edit_post' provide the post number and trigger the save */
		//add_filter('excerpt_save_pre', array(&$this, 'grabExcerpt'), 99);
		add_action('publish_post',     array(&$this, 'saveExcerpt'), 99);
		add_action('edit_post',        array(&$this, 'saveExcerpt'), 99);

		/* filters & actions for preformatting comment content
			'comment_save_pre' and 'pre_comment_content' are used to grab the comment content,
			'comment_post' and 'edit_comment' provide the comment number and trigger the save */
		add_filter('comment_save_pre',    array(&$this, 'grabComment'), 99);
		add_filter('pre_comment_content', array(&$this, 'grabComment'), 99);
		add_action('comment_post',        array(&$this, 'saveComment'), 99);
		add_action('edit_comment',        array(&$this, 'saveComment'), 99);
		
		/* these filters trap the post or comment display, which triggers the preformatting magic */
		/*	ideally 'the_content' filter would be used, but this filter is sadly crippled by WP's paging functions
			so we have to swap in the preformatted content for the actual content when the posts are queried */
		//add_filter('the_content',  array(&$this, 'renderContent'), 0);
		add_filter('the_posts',    array(&$this, 'replacePostContent'), 1);
		add_filter('the_excerpt',  array(&$this, 'renderExcerpt'), 0);
		add_filter('comment_text', array(&$this, 'renderComment'), 0);
		
		/* these filters are necessary for excerpt generation
			the default WP filters try to process the filter 'the_content' when creating an excerpt so the moved
			filter stream has to be made available in this case.  A filter is also added to 'the_content'  in rearrangeFilters() */
		add_filter('get_the_excerpt',  array(&$this, 'startExcerptRedirect'), 1);
		add_filter('get_the_excerpt',  array(&$this, 'endExcerptRedirect'), 99);
				
		// this will get moved to the appropriate PreFormatted filter stream
		add_filter('the_content', array(&$this, 'fixWpTags'), 15);
		
		/* once all plugins have been loaded it should be safe to divert the filter streams 
			any plugins that add filters dynamically may still get missed, however */
		add_action('plugins_loaded', array(&$this, 'rearrangeFilters'), 99);
		
		/* to speed things up a bit more, do updates only after the page is displayed */
		if ($this->batchmode)
			add_action('shutdown', array(&$this, 'executeQueries'), 99);

		/* upgrade database if necessary */
		if (get_option('preformatted_version') < $this->version)
			$this->upgradeDatabase();
		
		/* for testing purposes - remove for final version */
		if (isset($_REQUEST['preformattedclearall']))
			$this->clearAll();
	}

	function upgradeDatabase() {
		global $wpdb;
		
		if (get_option('preformatted_version') < '1.9') {
			// basic upgrade
			$wpdb->query("ALTER TABLE $wpdb->posts ADD COLUMN post_excerpt_filtered text NOT NULL");
			$wpdb->query("ALTER TABLE $wpdb->comments ADD COLUMN comment_content_filtered text NOT NULL");
			add_option('preformatted_version', $this->version, "PreFormatted Plugin Version", 'yes');
		}
		// version upgrade only
		update_option('preformatted_version', $this->version);
	}

	function clearAll() {
		global $wpdb;
		$wpdb->query("UPDATE $wpdb->posts SET post_content_filtered = '', post_excerpt_filtered = ''");
		$wpdb->query("UPDATE $wpdb->comments SET comment_content_filtered = ''");
	}

	function rearrangeFilters() {
		foreach($this->formatfilters as $filter)
			$this->moveFilters($filter, $this->filterprefix . $filter);
		/* used for excerpt generation or post previews in the editing screen */
		add_filter('the_content',  array(&$this, 'doContentRedirect'), 10);
	}
	
	function showFilters($tag) {		// used for debugging only
		global $wp_filter;
		
		$output = "<p> Filters for $tag: <ul>\n";
		if (isset($wp_filter[$tag]) && is_array($wp_filter[$tag])) {
			foreach($wp_filter[$tag] as $priority => $filters) {
				foreach($filters as $f) {
					$output .= "<li> $priority ";
					$output .= is_array($f['function']) ? get_class($f['function'][0]) . '->' . $f['function'][1] : $f['function'];
					$output .= " ({$f['accepted_args']}) </li>\n";
				}
			}
		} else
			$output .= "none";
		$output .= "</ul></p>\n";	
		return $output;
	}
	
	function moveFilters($oldtag, $newtag) {
		global $wp_filter;
		if (!isset($this->movedfilters[$oldtag])) {
			// move all non-zero priority filters
			if (isset($wp_filter[$oldtag]) && is_array($wp_filter[$oldtag])) {
				foreach($wp_filter[$oldtag] as $priority => $filters) {
					if ($priority > 0) {
						$wp_filter[$newtag]["$priority"] = $wp_filter[$oldtag]["$priority"];
						unset($GLOBALS['wp_filter'][$oldtag]["$priority"]);
					}
				}
			}
			$this->movedfilters[$oldtag] = $newtag;
		}
	}

	function grabContent(&$content) {
		$this->applyFormatting('content', stripslashes($content));
		return $content;	// return original
	}

	function grabExcerpt(&$excerpt) {
		$this->applyFormatting('excerpt', stripslashes($excerpt));
		return $excerpt;	// return original
	}

	function grabComment(&$comment) {
		$this->applyFormatting('comment', stripslashes($comment));
		return $comment;	// return original
	}
	
	function applyFormatting($type, &$content) {
		// generate formatted content
		$output = apply_filters($this->filterprefix . $this->formatfilters[$type], $content);
		$this->formattedval[$type] = addslashes($output);
		return $output;
	}

	function saveContent($postid) {
		$this->saveFormattedValue('content', $postid);
	}

	function saveExcerpt($postid) {
		$this->saveFormattedValue('excerpt', $postid);
	}

	function saveComment($commentid) {
		$this->saveFormattedValue('comment', $commentid);
	}

	function saveFormattedValue($type, $id) {
		global $wpdb;
		
		// create update query
		switch ($type) {
			case 'content':
				$query = "UPDATE $wpdb->posts SET post_content_filtered = '{$this->formattedval['content']}' WHERE ID = '$id'";
				break;
			case 'excerpt':
				$query = "UPDATE $wpdb->posts SET post_excerpt_filtered = '{$this->formattedval['excerpt']}' WHERE ID = '$id'";
				break;
			case 'comment':
				$query = "UPDATE $wpdb->comments SET comment_content_filtered = '{$this->formattedval['comment']}' WHERE comment_ID = '$id'";
				break;
		}
		
		// set updated filter
		$filter = $this->filterprefix . $type . '_updated';
		
		// save queries for execution later or run now, depending on mode
		if ($this->batchmode) {
			$this->updateQueries[] = array('query' => $query, 'filter' => $filter, 'id' => $id);
		} else {
			$wpdb->query($query);
			do_action($filter, $id);
		}
	}

	function executeQueries() {
		global $wpdb;
		foreach ($this->updateQueries as $update) {
			$wpdb->query($update['query']);
			do_action($update['filter'], $update['id']);
		}
	}

	function getFormattedValue($type, $id, &$original, &$formatted) {
		// apply user filters
		$formatted = apply_filters($this->filterprefix . $type . '_filtered', $formatted);
		
		// check for preformatted content
		if (empty($formatted)) {	// generate formatted content
			$formatted = $this->applyFormatting($type, $original);
			$this->saveFormattedValue($type, $id);
		}
		return $formatted;
	}
	
	function replacePostContent(&$posts) {
		if ($posts) {
			foreach($posts as $key=>$post) {
				if (isset($post->post_content_filtered))
					$posts[$key]->post_content = $this->getFormattedValue('content', $post->ID, $post->post_content, $posts[$key]->post_content_filtered);
			}
		}
		return $posts;
	}
	
	function renderContent(&$text) {	// unused due to paging issue, replacePostContent is used instead
		global $post;
		
		if (!isset($post->post_content_filtered) || ($post->post_content_filtered === false))
			return $text;		// abort if we are somehow missing this database field
		else
			return $this->getFormattedValue('content', $post->ID, $text, $post->post_content_filtered);
	}
	
	function renderExcerpt(&$text) {
		global $post;
		
		if (!isset($post->post_excerpt_filtered) || ($post->post_excerpt_filtered === false))
			return $text;		// abort if we are somehow missing this database field
		else
			return $this->getFormattedValue('excerpt', $post->ID, $text, $post->post_excerpt_filtered);
	}

	function renderComment(&$text) {
		global $comment;
		
		if (!isset($comment->comment_content_filtered) || ($comment->comment_content_filtered === false))
			return $text;		// abort if we are somehow missing this database field
		else
			return $this->getFormattedValue('comment', $comment->comment_ID, $text, $comment->comment_content_filtered);
	}

	function startExcerptRedirect(&$text) {
		global $post;
		// if we have a preformatted excerpt then return it and skip the filter redirect for 'the_content'
		if (isset($post) && isset($post->post_excerpt_filtered) && !empty($post->post_excerpt_filtered))
			$text = $post->post_excerpt_filtered;
		else
			$this->doingexcerpt = true;		// flag to redirect 'the_content' filter
		return $text;
	}

	function endExcerptRedirect(&$text) {
		$this->doingexcerpt = false;
		return $text;	// return original
	}

	function doContentRedirect(&$text) {
		if ($this->doingexcerpt) {
			// if we have preformatted content then return it and skip the filter redirect for 'the_content'
			global $post;
			if (isset($post) && isset($post->post_content_filtered) && !empty($post->post_content_filtered))
				return $post->post_content_filtered;
			else	// otherwise process content via post content filters (will this ever happen?)
				return apply_filters($this->filterprefix . $this->formatfilters['content'], $text);
		} elseif (strstr($_SERVER['PHP_SELF'], 'wp-admin/post.php')) {
			// in the editing page the formatted data has to be pulled from $postdata (if it exists)
			global $postdata;
			if (isset($postdata) && isset($postdata->post_content_filtered) && !empty($postdata->post_content_filtered))
				return $postdata->post_content_filtered;
			else	// otherwise process content via post content filters (will this ever happen?)
				return apply_filters($this->filterprefix . $this->formatfilters['content'], $text);
		} else
			return $text;
	}
	
	function fixWpTags($text) {
		/* quick fix for a common WP problem made worse by this plugin
			we attempt to move <!--more--> and <!--nextpage--> tags outside of their own <p> blocks
			can't do much with ones embedded in other paragraphs without breaking more stuff */
		$wptags = array('more', 'nextpage', 'noteaser');	//are there more?
		foreach($wptags as $tag)
			$text = preg_replace('%<p>\s*(<!--' . $tag . '-->)\s*</p>%', '$1', $text);
		return($text);
	}

}
$preformatted_instance = new PreFormatted();	// where it all begins

?>