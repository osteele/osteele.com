<?php

global $add_user_errors;

$this->test_interface('header');

// set in wphone.php plugin
if ( !isset($this->context) || ( ($this->context != 'edit') && ($this->context != 'list') ) ) $this->context = 'edit';

$fancy_text = ucfirst($this->context);

if ( $this->context == 'edit' ) {

	// EDIT FORM / PROFILE

	require_once( ABSPATH . $this->folder . '/includes/user-form.php' );

} elseif ( $_GET['add'] || is_wp_error($add_user_errors) ) {

	// ADD FORM

	require_once( ABSPATH . $this->folder . '/includes/user-form.php' );

} else {

	// USER LIST
	
	$user_search = strip_tags(trim($_GET['usersearch']));

	$target_script = 'users.php';

	$offset = (int) $_GET['offset'];
	$limit  = 10;

	// Note: Getting $limit + 1 to know if we do need a next button.
	$query_limit = $limit + 1;

	$previous_caption = __('&laquo; Previous Users', 'wphone');
	$next_caption = __('Next Users &raquo;', 'wphone');
	$more_caption = __('More Users', 'wphone');

	if ( !$this->iscompat )
		echo '<h2 class="accessible">' . __('Users') . "</h2>\n";

	if ( ( !$this->iscompat ) || ( $offset < $limit ) ) {
		echo '<ul id="userlistmenu" title="' . __('Users') . '"';
		if ( $this->iscompat ) echo ' selected="true"';
		echo ">\n";
?>
		<li>
			<form action="./users.php?wphone=ajax" name="usersearchform" id="usersearchform" method="GET">
				<input type="text" class="widefield" name="usersearch" id="usersearch" value="<?php echo attribute_escape($user_search); ?>" />
				<?php $this->panel_button( 'submit', 'submit', __('Search Users &raquo;') ); ?>
			</form>
		</li>
<?php
	
		echo '<li class="group">'.__('Results', 'wphone');

		if ( ! $this->iscompat)
			echo "\n<ul>\n";
		else
			echo "</li>\n";
	}

	if ( current_user_can('edit_users') ) {

		if ( '' != $user_search ) {
			$searches = array();
			$search_sql = 'WHERE (';
			foreach ( array('user_login', 'user_nicename', 'user_email', 'user_url', 'display_name') as $col )
				$searches[] = $col . " LIKE '%$user_search%'";
			$search_sql .= implode(' OR ', $searches);
			$search_sql .= ')';
		} else {
			$search_sql = '';
		}

		$users = $wpdb->get_results( "SELECT ID, display_name FROM $wpdb->users $search_sql ORDER BY display_name LIMIT $query_limit OFFSET $offset" );

		$users_count = count($users);

		if ( !empty($users_count) ) {

			$loop_limit = ($users_count < $limit) ? $users_count : $limit;

			for ( $i=0; $i<$loop_limit; $i++ ) {
				$user = $users[$i];
				echo '<li><a href="' . $this->admin_url . '/user-edit.php?wphone=ajax&amp;user_id=' . $user->ID . '&amp;parent=users">' . strip_tags($user->display_name) . "</a></li>\n";
			}

			$next_link = $this->admin_url . '/' . $target_script . '?usersearch='.urlencode($user_search).'&amp;offset=' . ($offset+$limit) . '&amp;wphone=ajax';

			$needs_previous = ($offset >= $limit) ? TRUE : FALSE;
			$needs_next = ($users_count > $limit) ? TRUE : FALSE;

			if ( TRUE == $needs_previous || TRUE == $needs_next ) {
				if ( !$this->iscompat ) {
					echo '<li><strong>';

					if ( TRUE == $needs_previous ) {
						$previous_offset = ($offset - $limit <= 0) ? 0 : ($offset - $limit);
						echo '<a href="'.$this->admin_url.'/'.$target_script.'?usersearch='.urlencode($user_search).'&amp;offset='.$previous_offset.'&amp;wphone=ajax" target="_replace">'.$previous_caption.'</a></li>'."\n";
					}

					if ( TRUE == $needs_next ) {
						if ( TRUE == $needs_previous ) echo '</strong></li><li><strong>';
						echo '<a href="'. $next_link . '" target="_replace">'.$next_caption.'</a>'."\n";
					}

					echo '</strong></li>';
				} elseif ( TRUE == $needs_next ) {
					echo '<li><a href="'. $next_link . '" target="_replace">'.$more_caption.'</a></li>'."\n";
				}
			}
		} else {
			echo '<li>' . __('No users found.', 'wphone') . "</li>\n";
		}
	} else {
		echo '<li>' . __('Access denied.', 'wphone') . "</li>\n";
	}

	if ( ( !$this->iscompat ) || ( $offset < $limit ) ){
		if ( !$this->iscompat )
			echo "</ul>\n</li>\n";
		echo "</ul>\n";

	}
}

$this->test_interface('footer');

?>