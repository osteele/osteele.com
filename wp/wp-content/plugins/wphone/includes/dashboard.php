<?php

// This action is called for non-known URLs
// If you want to add a dynamic custom page, use this hook
do_action( 'wphone_dashboard_init' );

$this->load_interface('header');

$can_edit_posts = current_user_can('edit_posts');
$can_edit_pages = current_user_can('edit_pages');
$can_edit_categories = current_user_can('manage_categories');
$can_edit_users = current_user_can('edit_users');

$valid_scroll = array(
	'writemenu',
	'managemenu',
	'usersmenu',
	'activitymenu'
	);

if ( TRUE == $profileupdated )
	$goto = 'usersmenu';
else
	$goto = $_GET['goto'];

if ( ! in_array($goto, $valid_scroll) )
	$goto = 'adminmenu';

/**
 * DASHBOARD MENU
 */

echo '<h2 class="accessible">' . __('Admin Options', 'wphone') . "</h2>\n";
echo '<ul id="adminmenu" title="' . __('WP Admin', 'wphone') . '"';
if ( $goto == 'adminmenu' && $this->iscompat ) echo ' selected="true"';
echo ">\n";

// outputs dashboard links
$this->quick_links( 'dashboard' );

echo "</ul>\n";


/**
 * WRITE MENU
 */

if ( $can_edit_posts || $can_edit_pages ) {
	echo '<h2 class="accessible">' . __('Write') . "</h2>\n";
	echo '<ul id="writemenu" title="' . __('Write') . '"';
	if ( $goto == 'writemenu' && $this->iscompat ) echo ' selected="true"';
	echo ">\n";

	$this->show_submenu('write');

	echo "</ul>\n";
}


/**
 * MANAGE MENU
 */

if ( $can_edit_posts || $can_edit_pages || $can_edit_categories) {
	echo '<h2 class="accessible">' . __('Manage') . "</h2>\n";
	echo '<ul id="managemenu" title="' . __('Manage') . '"';
	if ( $goto == 'managemenu' && $this->iscompat ) echo ' selected="true"';
	echo ">\n";

	if ( file_exists( ABSPATH . 'wp-admin/includes/admin.php' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
	} else {
		require_once( ABSPATH . 'wp-admin/admin-db.php' );
	}

	$count_info = array();
	
	$count_info[20] = count(get_users_drafts($userdata->ID));
	$count_info[30] = ( function_exists('get_others_pending') ) ? count(get_others_pending($userdata->ID)) : 0;
	$count_info[40] = ( function_exists('get_others_drafts') ) ? count(get_others_drafts($userdata->ID)) : 0;

	// Allows plugin developers to add or overwrite the count info 
	$count_info = apply_filters( 'wphone_managemenu_countlist', $count_info );

	$this->show_submenu('manage', $count_info, FALSE);

	echo "</ul>\n";
}


/**
 * USER MENU
 */

if ( $can_edit_users ) {
	echo '<h2 class="accessible">' . __('Users') . "</h2>\n";
	echo '<ul id="usersmenu" title="' . __('Users') . '"';
	if ( $goto == 'usersmenu' && $this->iscompat ) echo ' selected="true"';
	
	$this->show_submenu('users');
	
	echo "</ul>\n";
}


/**
 * LATEST ACTIVITY MENU
 */

echo '<h2 class="accessible">' . __('Latest Activity') . "</h2>\n";
echo '<ul id="activitymenu" title="' . __('Latest Activity') . '"';
if ( $goto == 'activitymenu' && $this->iscompat ) echo ' selected="true"';
echo ">\n";


# Blog stats

if ( $this->iscompat )
	echo '<li class="group">'.__('Blog Stats') . "</li>\n";

$numposts = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'");
$numcomms = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");

if ( function_exists('wp_count_terms') )
	$numcats  = wp_count_terms('category');
else
	$numcats = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->categories");

if ( function_exists('number_format_i18n') ) {
	$numpostsformat = number_format_i18n($numposts);
	$numcommsformat = number_format_i18n($numcomms);
	$numcatsformat = number_format_i18n($numcats);
} else {
	if ( 0 < $numposts ) $numpostsformat = number_format($numposts);
	if ( 0 < $numcomms ) $numcommsformat = number_format($numcomms);
	if ( 0 < $numcats ) $numcatsformat = number_format($numcats);
}

$post_str = sprintf( __ngettext('%1$s post', '%1$s posts', $numposts), $numpostsformat );
$comm_str = sprintf( __ngettext('%1$s comment', '%1$s comments', $numcomms), $numcommsformat );
$cat_str  = sprintf( __ngettext('%1$s category', '%1$s categories', $numcats), $numcatsformat );

echo '<li>';
if ( 2.3 <= floatval($wp_version) ) {
	$numtags = wp_count_terms('post_tag');
	$tag_str  = sprintf( __ngettext('%1$s tag', '%1$s tags', $numtags), number_format_i18n($numtags) );
	printf(__('There are currently %1$s and %2$s, contained within %3$s and %4$s.'), $post_str, $comm_str, $cat_str, $tag_str);
} else {
	printf(__('There are currently %1$s and %2$s, contained within %3$s.'), $post_str, $comm_str, $cat_str);
}
echo '<br class="accessible" /><br class="accessible" /></li>' . "\n";


# Incoming links, adapted from /wp-admin/index-extra.php

require_once (ABSPATH . WPINC . '/rss.php');
$rss_feed = apply_filters( 'dashboard_incoming_links_feed', 'http://blogsearch.google.com/blogsearch_feeds?hl=en&scoring=d&ie=utf-8&num=10&output=rss&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) );
$more_link = apply_filters( 'dashboard_incoming_links_link', 'http://blogsearch.google.com/blogsearch?hl=en&scoring=d&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) );
$rss = @fetch_rss( $rss_feed );

if ( isset($rss->items) && 1 < count($rss->items) ) { // Technorati returns a 1-item feed when it has no results
	$has_incoming_links = TRUE;
	echo '<li class="group">'.__('Incoming Links');

	if ( ! $this->iscompat)
		echo "\n<ul>\n";
	else
		echo "</li>\n";

	$rss->items = array_slice($rss->items, 0, 5);
	foreach ( $rss->items as $item )
		echo '<li><a href="' . wp_filter_kses($item['link']) . '" ' . $this->htmltarget('_blank', TRUE) . '>' . wptexturize(wp_specialchars($item['title'])) . "</a></li>\n";

	echo '<li><a href="' . htmlspecialchars( $more_link ) . '" ' . $this->htmltarget('_blank', TRUE) . '>' . __('More &raquo;') . "</a></li>\n";

	if ( !$this->iscompat )
		echo "</ul>\n</li>\n";
}


$numitems = 6; // Items to list below PLUS ONE


# Latest comments, adapted from /wp-admin/index.php

if ( $comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date DESC LIMIT $numitems") ) {
	echo '<li class="group">' . __('Latest Comments', 'wphone');

	if ( !$this->iscompat)
		echo "\n<ul>\n";
	else
		echo "</li>\n";

	$count = 0;
	foreach ( $comments as $comment ) {
		$count++; if ( $count >= $numitems ) break;

		echo '<li>';
		
		if ( current_user_can('moderate_comments') )
			echo '<a href="'. $this->admin_url . '/comment.php?wphone=ajax&amp;c=' . $comment->comment_ID . '&amp;parent=edit-comments">';
		else
			echo '<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '" ' . $this->htmltarget( '_blank', TRUE ) . '>';
		
		echo sprintf( __('%1$s on %2$s'), strip_tags($comment->comment_author), get_the_title($comment->comment_post_ID) );
		echo "</a></li>\n";
	}

	if ( count($comments) == $numitems && current_user_can('moderate_comments') )
		echo '<li><a href="' . $this->admin_url . '/edit-comments.php?wphone=ajax">' . __('More &raquo;') . "</a></li>\n";

	if ( !$this->iscompat )
		echo "</ul>\n</li>\n";
}


# Recent posts

if ( function_exists('get_private_posts_cap_sql') )
	$recentpostssql = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND " . get_private_posts_cap_sql('post') . " AND post_date_gmt < '" . current_time('mysql', 1) . "' ORDER BY post_date DESC LIMIT $numitems";
else
	$recentpostssql = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' AND post_date_gmt < '" . current_time('mysql', 1) . "' ORDER BY post_date DESC LIMIT $numitems";

if ( $recentposts = $wpdb->get_results($recentpostssql) ) {
	echo '<li class="group">' . __('Recent Posts', 'wphone');

	if ( !$this->iscompat )
		echo "\n<ul>\n";
	else
		echo "</li>\n";

	$count = 0;
	foreach ( $recentposts as $post ) {
		$count++; if ( $count >= $numitems ) break;

		if ( $post->post_title == '' ) $post->post_title = sprintf(__('Post #%s'), $post->ID);

		echo '<li><a href="post.php?wphone=ajax&amp;action=edit&amp;post=' . $post->ID . '">' . $post->post_title . "</a></li>\n";
	}

	if ( count($recentposts) == $numitems && ( $can_edit_posts || $can_edit_pages || $can_edit_categories ) )
		echo '<li><a href="#managemenu">' . __('More &raquo;') . "</a></li>\n";

	if ( !$this->iscompat )
		echo "</ul>\n</li>\n";
}


# Scheduled posts

if ( $can_edit_posts ) {
	if ( $scheduled = $wpdb->get_results("SELECT ID, post_title, post_date_gmt FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'future' ORDER BY post_date ASC LIMIT $numitems") ) {
		echo '<li class="group">'.__('Scheduled Entries', 'wphone');
	
		if ( !$this->iscompat )
			echo "\n<ul>\n";
		else
			echo "</li>\n";

		$count = 0;
		foreach ( $scheduled as $post ) {
			$count++; if ( $count >= $numitems ) break;
	
			if ( $post->post_title == '' ) $post->post_title = sprintf(__('Post #%s'), $post->ID);

			echo '<li><a href="post.php?wphone=ajax&amp;action=edit&amp;post=' . $post->ID . '">';
			echo sprintf(__('%1$s in %2$s'), '"' . $post->post_title . '"', human_time_diff( current_time('timestamp', 1), strtotime($post->post_date_gmt. ' GMT') ));
			echo "</a></li>\n";
		}
	
		echo '<li><a href="#managemenu">' . __('More &raquo;') . "</a></li>\n";
	
		if ( !$this->iscompat )
			echo "</ul>\n</li>\n";
	}
}

// Lets plugin developers add modules to the Latest Activity screen
do_action( 'wphone_activity' );

echo "</ul>\n";


/**
 * PLUGIN MENUS
 */

// If you want to add new non-AJAX pages (such as the ones listed above), this is the hook to use
do_action( 'wphone_dashboard' );

$this->load_interface('footer');

?>