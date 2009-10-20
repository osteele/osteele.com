<?php

$this->test_interface('header');

// set in wphone.php plugin
if( !isset($this->context) || ( ($this->context != 'edit') && ($this->context != 'list') ) ) {
	$this->context = 'list';
}

$fancy_text = ucfirst($this->context);

if ( 'edit' == $this->context ) {

	// @note EDIT FORM (lists in else)

	$comment_ID = (int) $_GET['c'];

	if ( !$comment = get_comment($comment_ID) ) wp_die( __('Oops, no comment with this ID.') ); // Good enough

	$this->check_user_permissions('edit_post', $comment->comment_post_ID);

	if ( file_exists( ABSPATH . 'wp-admin/includes/admin.php' ) )
		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
	else
		require_once( ABSPATH . 'wp-admin/admin-functions.php' );

	$comment = get_comment_to_edit($comment_ID);

	$heading - sprintf(__('Editing Comment # %s'), $comment->comment_ID);

	// @note: "referredby" is a cheat of sorts so we head to the comments list rather than edit.php or whatever
?>
	<h2 class="accessible"><?php echo $heading; ?></h2>
	<form name="post" title="<?php echo $heading; ?>" class="panel" action="comment.php" method="post" target="_self" selected="true">
<?php wp_nonce_field('update-comment_' . $comment->comment_ID); ?>
		<input type="hidden" name="user_ID" value="<?php echo (int) $userdata->ID; ?>" />
		<input type="hidden" name="action" value="editedcomment" />
		<input type="hidden" name="comment_ID" value="<?php echo $comment->comment_ID; ?>" />
		<input type="hidden" name="comment_post_ID" value="<?php echo $comment->comment_post_ID; ?>" />
		<input type="hidden" name="referredby" value="<?php echo $this->referer('edit-comments.php'); ?>">

		<fieldset>
			<div class="row">
				<label for="newcomment_author"><?php _e('Name:') ?></label>
				<br class="accessible" />
				<input class="widefield" type="text" name="newcomment_author" id="newcomment_author" value="<?php echo attribute_escape( $comment->comment_author ); ?>" />
			</div>
			<div class="row">
<?php if ( $comment->comment_author_email ) : ?>
				<label for="newcomment_author_email"><a href="mailto:<?php echo attribute_escape( $comment->comment_author_email ); ?>"<?php $this->htmltarget('_self'); ?>><?php _e('E-mail:') ?></a></label>
<?php else: ?>
				<label for="newcomment_author_email"><?php _e('E-mail:') ?></label>
<?php endif; ?>
				<br class="accessible" />
				<input class="widefield" type="text" name="newcomment_author_email" id="newcomment_author_email" value="<?php echo attribute_escape( $comment->comment_author_email ); ?>" />
			</div>
			<div class="row">
<?php if ( $comment->comment_author_url && ('http://' != $comment->comment_author_url) ) : ?>
				<label for="newcomment_author_url"><a href="<?php echo attribute_escape( $comment->comment_author_url ); ?>" <?php $this->htmltarget('_blank'); ?>><?php _e('URL:') ?></a></label>
<?php else: ?>
				<label for="newcomment_author_url"><?php _e('URL:') ?></label>
<?php endif; ?>
				<br class="accessible" />
				<input class="widefield" type="text" name="newcomment_author_url" id="newcomment_author_url" value="<?php echo attribute_escape( $comment->comment_author_url ); ?>" />
			</div>
		</fieldset>
		<fieldset>
			<div class="labelrow">
				<label for="content"><?php _e('Comment:', 'wphone') ?></label>
				<br class="accessible" />
		</div>
		<div class="inputrow">
				<textarea rows="10" name="content" id="content"><?php echo $comment->comment_content; ?></textarea>
			</div>
		</fieldset>
		<fieldset>
			<div class="row">
				<label><?php _e('Status', 'wphone'); ?>:</label>
				<br class="accessible" />
				<select name="comment_status" class="widefield">
<?php
				$statuses = array(
					0      => __('Moderated'),
					1      => __('Approved'),
					'spam' => __('Spam')
				);
				foreach ($statuses as $key => $value) {
					$selected = ( $comment->comment_approved == $key ) ? 'selected' : '';
					echo '<option value="' . $key . '" ' . $selected . ' >' . $value . '</option>';
				}
?>
				</select>
			</div>
		</fieldset>
<?php
		$this->panel_button('submit', 'submit', __('Edit Comment &raquo;'));

		$question = __("You are about to delete this comment. \n  'Cancel' to stop, 'OK' to delete.");
		$url = wp_nonce_url('comment.php?action=deletecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID);
		$this->panel_delete_button(sprintf( __('Delete this comment'), $fancy_type), $question, $url );
?>
	</form>
<?php
		echo "</form>\n";

} else {

	$this->check_user_permissions('moderate_comments');

	// @note COMMENT LISTINGS

	$all_com_head   = __('Edit Comments');
	$mod_com_head   = __('Awaiting Moderation', 'wphone');
	$spam_com_head  = __('Spam Comments', 'wphone');

	$approved_sql   = "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date DESC";
	$moderation_sql = "SELECT * FROM $wpdb->comments WHERE comment_approved = '0'";
	$spam_sql       = "SELECT * FROM $wpdb->comments WHERE comment_approved = 'spam'";

	$target_script  = 'edit-comments.php';

	if ( isset($_GET['type']) ) {

		$offset = (int) $_GET['offset'];
		$limit  = 5;

		// Note: Getting $limit + 1 to know if we do need a next button.
		$query_limit = ($limit + 1);

		$previous_caption = __('&laquo; Previous Comments', 'wphone');
		$next_caption = __('Next Comments &raquo;', 'wphone');
		$more_caption = __('More Comments', 'wphone');

		switch ($_GET['type']) {
			case 'moderation':
				$type = $_GET['type'];
				$header = $mod_com_head;
				$comments = $wpdb->get_results( $moderation_sql . " LIMIT $query_limit OFFSET $offset" );
				break;
			case 'spam':
				$type = $_GET['type'];
				$header = $spam_com_head;
				$comments = $wpdb->get_results( $spam_sql . " LIMIT $query_limit OFFSET $offset" );
				break;
			default:
				$type = 'approved';
				$header = $all_com_head;
				$comments = $wpdb->get_results( $approved_sql . " LIMIT $query_limit OFFSET $offset" );
		}

		$comments_count = count($comments);

		if ( !$this->iscompat )
			echo '<h2 class="accessible">' . $header . "</h2>\n";

		if ( ( !$this->iscompat ) || ( $offset < $limit ) )
			echo '<ul id="commentlist' . $type . 'menu" title="' . $header . '"  selected="true" >' . "\n";

		if ( $comments_count ) {

			$loop_limit = ($comments_count < $limit) ? $comments_count : $limit;

			for ( $i=0; $i<$loop_limit; $i++ ) {
				unset( $comment );

				$comment = $comments[$i];

				$comment_excerpt = strip_tags( $comment->comment_content );
				$comment_excerpt = ( 256 < strlen($comment_excerpt) ) ? substr($comment_excerpt, 0, 253) . '...' : $comment_excerpt;

				echo '<li>';
				echo '<a href="'. $this->admin_url . '/comment.php?wphone=ajax&amp;c='.$comment->comment_ID .'&amp;parent=edit-comments" class="comment_approved">' . $comment->comment_author . '</a> ';
				echo $comment->comment_author_email .'<br />';
				echo '<span class="commentmeta">'. $comment->comment_date;
				echo '<br />"' . $comment_excerpt . '"</span>';
				echo "</li>\n";
			}

			$next_link = $this->admin_url.'/'.$target_script.'?offset='.($offset+$limit).'&amp;type=' . $type . '&amp;wphone=ajax';

			$needs_previous = ($offset >= $limit) ? TRUE : FALSE;
			$needs_next = ($comments_count > $limit) ? TRUE : FALSE;

			if ( $needs_previous || $needs_next ) {
				if ( !$this->iscompat ) {
					echo '<li><strong>';

					if ( $needs_previous ) {
						$previous_offset = ($offset - $limit <= 0) ? 0 : ($offset - $limit);
						echo '<a href="'.$this->admin_url.'/'.$target_script.'?offset='.$previous_offset.'&amp;type=' . $type . '&amp;wphone=ajax">'.$previous_caption.'</a></li>'."\n";
					}

					if ( $needs_next ) {
						if ( TRUE == $needs_previous ) echo '</strong></li><li><strong>';
						echo '<li><a href="'. $next_link . '">'.$next_caption.'</a></li>'."\n";
					}

					echo '</strong></li>';
				} elseif ( $needs_next ) {
					echo '<li><a href="'. $next_link . '" target="_replace">'.$more_caption.'</a></li>'."\n";
				}
			}
		} else {
			echo '<li>' . __('No comments found.', 'wphone') . "</li>\n";
		}

		if ( ( !$this->iscompat ) || ( $offset < $limit ) )
			echo "</ul>\n";

	} else {

		$count_info = array();
		
		$approved_comments = $wpdb->get_results( $approved_sql );
		$count_info[10] = $wpdb->get_var( "SELECT FOUND_ROWS()" );

		$moderation_comments = $wpdb->get_results( $moderation_sql );
		$count_info[20] = count( $moderation_comments );

		$count_info[30] = ( function_exists('akismet_spam_count') ) ? akismet_spam_count() : count( $wpdb->get_results( $spam_sql ) );

		// Allows plugin developers to add or overwrite the count info
		$count_info = apply_filters( 'wphone_commentsmenu_countlist', $count_info );

		if ( !$this->iscompat )
			echo '<h2 class="accessible">' . __('Comments') . "</h2>\n";

		echo '<ul id="commentmenu" title="' . __('Comments') . '"  selected="true">';
		
		$this->show_submenu('comments', $count_info, TRUE);
		
		echo "</ul>\n";
	}
}

$this->test_interface('footer');

?>