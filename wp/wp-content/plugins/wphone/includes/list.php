<?php

$this->test_interface('header');

if( !isset($this->context) || ( ($this->context != 'post') && ($this->context != 'page') ) ) {
	$this->context = 'post';
}

$this->check_user_permissions('edit_' . $this->context . 's');

$fancy_text = ucfirst($this->context);

$offset = (int) $_GET['offset'];
$limit  = 10;

// Note: Getting $limit + 1 to know if we do need a next button.
$query_limit = ($limit + 1);

$entries_query_param = 'numberposts=' . $query_limit . "&offset=$offset&order=DESC";

if ( $this->context == 'page' ) {
	// get_pages() ignores unpublished pages
	$entries = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'page' ORDER BY post_title ASC LIMIT $query_limit OFFSET $offset");
	$page_header = __('Pages');
	$previous_caption = __('&laquo; Previous Pages', 'wphone');
	$next_caption = __('Next Pages &raquo;', 'wphone');
	$more_caption = __('More Pages &raquo;', 'wphone');
	$parent = 'edit-pages';
} else {
	global $user_ID;

	if ( file_exists( ABSPATH . 'wp-admin/includes/admin.php' ) )
		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
	else
		require_once( ABSPATH . 'wp-admin/admin-db.php' );

	switch ( $_GET['post_status'] ) {
		case 'draft':
			$page_header = ( function_exists('_c') ) ? _c('Drafts|manage posts header') : __('Drafts');
			$author = (int) $_GET['author'];
			if ( 0 < $author ) {
				$limit   = 50; // get_users_drafts does not support offset
				$entries = get_users_drafts($user_ID);
				if ( $author == '-' . $user_ID ) { // author exclusion
					$page_header .= ' ' . __('by other authors');
				} else {
					$author_user = get_userdata( $author );
					$page_header .= ' ' . sprintf(__('by %s'), wp_specialchars( $author_user->display_name ));
				}
			} else {
				$limit   = 50; // get_others_drafts does not support offset
				$entries = get_others_drafts($user_ID);
			}
			break;
		case 'pending':
			$limit       = 50; // get_others_pending does not support offset
			$entries     = get_others_pending($user_ID);
			$page_header = __('Pending Review');
			break;
		default:
			$entries     = get_posts('numberposts=' . $query_limit . "&offset=$offset&order=DESC");
			$page_header = __('Posts');
	}
	$previous_caption = __('&laquo; Previous Entries');
	$next_caption = __('Next Entries &raquo;'); 
	$more_caption = __('More Entries', 'wphone');
	$parent = 'edit';
}

if ( ( !$this->iscompat ) || ( $offset < $limit ) ) {
	echo '<h2 class="accessible">' . $page_header . '</h2>'."\n";
	echo '<ul id="'.$this->context.'listmenu" title="' . $page_header . '"';
	if ( $this->iscompat ) echo ' selected="true"';
	echo ">\n";
}

if ( $entries ) {
	$entry_count = count($entries);
	$loop_limit = ($entry_count < $limit) ? $entry_count : $limit;
	
	for ($i=0; $i<$loop_limit; $i++) {
		$entry = $entries[$i];
		
		if ($entry->post_title == '')
			$entry->post_title = sprintf(__('%s #%s'), $fancy_text, $entry->ID);
		
		if ( current_user_can('edit_post', $entry->ID) ) {
			echo '<li><a href="' . $this->admin_url . '/' . $this->context . '.php?action=edit&amp;post=' . $entry->ID . '&amp;wphone=ajax&amp;parent=' . $parent . '" class="' . $this->post_status . '">';
			echo wp_specialchars($entry->post_title);
			echo "</a></li>\n";
		} else {
			echo '<li>' . $entry->post_title . "</li>\n";
		}
	}
	
	$target_script = ($this->context == 'page') ? 'edit-pages.php' : 'edit.php';
	
	$next_link = $this->admin_url . '/' . $target_script . '?offset=' . ($offset+$limit) . '&amp;wphone=ajax';
	
	$needs_previous = ($offset >= $limit) ? TRUE : FALSE;
	$needs_next = ($entry_count > $limit) ? TRUE : FALSE;

	if ( $needs_previous || $needs_next ) {
		if ( !$this->iscompat ) {
			echo '<li><strong>';
			
			if ( $needs_previous ) {
				$previous_offset = ($offset - $limit <= 0) ? 0 : ($offset - $limit);
				echo '<a href="' . $this->admin_url . '/' . $target_script . '?offset=' . $previous_offset . '&amp;wphone=ajax">' . $previous_caption . "</a></li>\n";
			}
		
			if ( $needs_next ) {
				if ( TRUE == $needs_previous ) echo '</strong></li><li><strong>';
				echo '<a href="' .  $next_link . '">' . $next_caption . "</a>\n";
			}
			
			echo '</strong></li>';
		} elseif ($needs_next) {
			echo '<li><a href="'. $next_link . '" target="_replace">' . $more_caption . "</a></li>\n";
		}
	}
}
else {
	echo '<li>' . __('No items to edit', 'wphone') . "</li>\n";
}

if ( ( ! $this->iscompat) || ( $offset < $limit ) )
	echo "</ul>\n";

$this->test_interface('footer');

?>