<?php

/*
 * This page needs translation.
 * Due to the way it currently works, there's a few strings that are hard to translate as they are broken up.
 * Let's talk and figure a good way to do this.
 *
 * -Viper
 */

$this->test_interface('header');

// set in wphone.php plugin
if( !isset($this->context) || ( ($this->context != 'post') && ($this->context != 'page') ) ) {
	$this->context = 'post';
}

$this->check_user_permissions('edit_' . $this->context . 's');

if ( file_exists( ABSPATH . 'wp-admin/includes/admin.php' ) )
	require_once( ABSPATH . 'wp-admin/includes/admin.php' );
else
	require_once( ABSPATH . 'wp-admin/admin-functions.php' );

$fancy_text = __( ucfirst($this->context), 'wphone' );

global $post, $user_ID, $wp_version;

$subcontext      = 'add';
$post_ID_field   = '';
$post_categories = array();
$post_tags       = '';

$post_ID = (int) $_GET['post'];

$tags_compat = ( ($this->context == 'post') && ( function_exists('get_tags_to_edit') ) ) ? TRUE : FALSE;

// Generate form details
if ( 0 == $post_ID ) {
	$form_action = 'post';
	$temp_ID = -1 * time(); // don't change this formula without looking at wp_write_post()
	$form_extra = "<input type='hidden' id='post_ID' name='temp_ID' value='$temp_ID' />";
} else {
	$post_ID = (int) $post_ID;
	$form_action = 'editpost';
	$form_extra = "<input type='hidden' id='post_ID' name='post_ID' value='$post_ID' />";
}

// Generate nonce ID and stuff
if ( 'page' == $this->context ) {
	if ( 0 == $post_ID )
		$nonce_action = 'add-page';
	else
		$nonce_action = 'update-page_' . $post_ID;

	$fancy_type = __('Page', 'wphone');
	$redirect = 'edit-pages.php';
} else {
	if ( 0 == $post_ID ) {
		$nonce_action = 'add-post';
		$fancy_type = __('Draft', 'wphone');
	} else {
		$nonce_action = 'update-post_' . $post_ID;
		$fancy_type = __('Post', 'wphone');
	}

	$redirect = 'edit.php';
}

if ( !empty($post_ID) ) {
	$this->check_user_permissions('edit_post', $post_ID);

	if ( $post = get_post($post_ID) ) {
		$subcontext	= 'edit';
		$post_categories = wp_get_post_categories($post_ID);
		if ($tags_compat) $post_tags = get_tags_to_edit( $post_ID );
	}
}

$form_id = $this->context . '-' . $nonce_action;

if ( ! $this->iscompat ) {
	// note we need this one conditional because the same form is used in multiple contexts
	echo '<h2 class="accessible">' . $fancy_text . "</h2>\n";
}

?>
<form id="<?php echo $form_id ?>" name="post" title="<?php echo $fancy_text; ?>" class="panel" action="<?php echo $this->context; ?>.php" method="post"<?php if ($this->iscompat) echo ' selected="true" target="_self"'; ?>>

<?php echo $form_extra; ?>
	<input type="hidden" name="mode" value="bookmarklet" />
	<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID; ?>" />
	<input type="hidden" name="action" value="<?php echo $form_action ?>" />
	<input type="hidden" name="originalaction" value="<?php echo $form_action ?>" />
	<input type="hidden" name="referredby" value="<?php echo $this->referer($redirect); ?>" />
	<input type="hidden" name="post_author" value="<?php echo attribute_escape( $post->post_author ); ?>" />
	<input type="hidden" name="post_type" value="<?php echo $this->context ?>" />
<?php wp_nonce_field($nonce_action); ?>

<?php if ( $this->context == 'post' ) : ?>
	<input type="hidden" name="trackback_url" value="<?php echo attribute_escape( str_replace("\n", ' ', $post->to_ping) ); ?>"/>
	<input type="hidden" name="excerpt" value="<?php echo attribute_escape( $post->post_excerpt ); ?>"/>
<?php endif; ?>


	<fieldset>
		<div class="row">
			<label for="post_title"><?php _e('Title'); ?></label>
			<br class="accessible" />
			<input class="widefield" type="text" name="post_title" id="post_title" value="<?php echo attribute_escape( $post->post_title ); ?>"/>
		</div>
		<div class="labelrow">
			<label for="content"><?php _e('Content', 'wphone'); ?></label>
			<br class="accessible" />
		</div>
		<div class="inputrow">
			<textarea rows="10" cols="25" name="content" id="content"><?php echo attribute_escape( $post->post_content ); ?></textarea>
		</div>

<?php if ($tags_compat) : ?>
			<div class="row">
				<label for="tags_input"><?php _e('Tags', 'wphone'); ?></label>
				<br class="accessible" />
				<input class="widefield" type="text" name="tags_input" id="tags_input" value="<?php echo $post_tags ?>"/>
			</div>
<?php endif; ?>
	</fieldset>
	
<?php
	if ( 'publish' == $post->post_status ) {
		echo '<a class="whiteButton" href="' . clean_url(get_permalink($post->ID)) . '" ' . $this->htmltarget('_blank', TRUE) . '>' . __('View &raquo;') . "</a>\n";
	} elseif ( 'edit' == $subcontext ) {
		echo '<a class="whiteButton" href="' . clean_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', get_permalink($post->ID)))) . '" ' . $this->htmltarget('_blank', TRUE) . '>' . __('Preview &raquo;') . "</a>\n";
	}

	if ( $this->context == 'post' ) {
		$identifier = $this->context.'-categories-'.$post_ID;
		if ($this->iscompat) {
			$onclick = "WPhone.toggleElement('$identifier-container');";
			$this->panel_button('button', $identifier.'-toggle', __('Categories'), $onclick);
		}
		else {
			echo '<h2>' . __('Categories') . "</h2>\n";
		}
		
		// Important: must have "accessible" as a css class to be auto-hidden.
		// Can be reuse for other panels hide/show features.
		// Multiple classes allowed.
		echo '<fieldset id="' . $identifier . '-container" class="accessible">'."\n";
		
		$categories = get_categories('orderby=name&hide_empty=0');
	
		foreach ($categories as $category) {
			echo '<div class="row">' . "\n";
			echo '<input id="cat-' . $category->cat_ID . '" class="accessible" type="checkbox" name="post_category[]" value="' . $category->cat_ID . '"';
			checked( in_array($category->cat_ID, $post_categories), TRUE );
			echo " />\n<label>" . $category->cat_name . '</label>' . "\n";
		
			if ($this->iscompat) {
				$toggled = ( in_array($category->cat_ID, $post_categories) ) ? 'true' : 'false';
				echo '<div id="cat-' . $category->cat_ID . '-toggle" class="toggle" onclick="WPhone.toggleCheckbox(this);" toggled="' . $toggled . '"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>' . "\n";
			}
		
			echo "</div>\n";
		}
		echo "</fieldset>\n";
	}

	$identifier = $this->context.'-advanced-'.$post_ID;
	if ($this->iscompat) {
		$onclick = "WPhone.toggleElement('$identifier-container');";
		$this->panel_button('button', $identifier.'-toggle', __('Advanced', 'wphone'), $onclick);
	}
	else {
		echo '<h2>' . __('Advanced', 'wphone') . "</h2>\n";
	}
?>
	<fieldset id="<?php echo $identifier; ?>-container" class="accessible">

		<div class="row">
			<label for="post_password"><?php _e('Password', 'wphone'); ?></label>
			<br class="accessible" />
			<input class="narrowfield" type="text" name="post_password" id="post_password" value="<?php echo attribute_escape( $post->post_password ); ?>"/>
		</div>

		<div class="row">
			<label for="post_name"><?php _e('Slug', 'wphone'); ?></label>
			<br class="accessible" />
			<input class="narrowfield" type="text" name="post_name" id="post_name" value="<?php echo attribute_escape( $post->post_name ); ?>"/>
		</div>

		<div class="row">
			<label for="post_status"><?php _e('Status:', 'wphone') ?></label>
			<br class="accessible" />
			<select name="post_status" id="post_status" class="widefield">
<?php

				$states = array(
					'publish' => __('Published'),
					'pending' => __('Pending Review'),
					'draft'   => __('Draft'),
					'private' => __('Private')
				);
				
				foreach($states as $value => $caption) {
					if ( ('publish' != $value) || current_user_can('publish_posts') )
						echo '<option value="' . $value . '"';
						selected($post->post_status, $value);
						echo '>' . $caption . '</option>';
				}
?>
			</select>
		</div>

		<div class="row">
			<label for="comment_status"><?php _e('Allow Comments'); ?></label>
			<br class="accessible" />
<?php
			$checked = ( $post->comment_status == 'open') ? 'checked="checked"' : '';
			echo '<input id="comment_status" class="accessible" type="checkbox" name="comment_status" value="open" ' . $checked . '/>' . "\n";
			if ($this->iscompat) {
				$toggled = ( $checked ) ? 'true' : 'false';
				echo '<div id="comment_status-toggle" class="toggle" onclick="WPhone.toggleCheckbox(this);" toggled="' . $toggled . '"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>' . "\n";
			}
?>
		</div>

		<div class="row">
			<label for="ping_status"><?php _e('Allow Pings'); ?></label>
			<br class="accessible" />
<?php
			$checked = ( $post->ping_status == 'open') ? 'checked="checked"' : '';
			echo '<input id="ping_status" class="accessible" type="checkbox" name="ping_status" value="open" ' . $checked . '/>' . "\n";
			if ($this->iscompat) {
				$toggled = ( $checked ) ? 'true' : 'false';
				echo '<div id="ping_status-toggle" class="toggle" onclick="WPhone.toggleCheckbox(this);" toggled="' . $toggled . '"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>' . "\n";
			}
?>
		</div>

<?php
		global $current_user;
		$authors = get_editable_user_ids( $current_user->id );
		if ( $post->post_author && !in_array($post->post_author, $authors) )
			$authors[] = $post->post_author;
		if ( $authors && count( $authors ) > 1 ) {
			echo '<div class="row">';
			echo '<label>' . __('Author', 'wphone') . '</label><br class="accessible" />' . "\n";
			if ( 2.3 <= floatval($wp_version) ) {
				// adapted from /wp-admin/edit-form-advanced.php (2.3)
				wp_dropdown_users( array('include' => $authors, 'name' => 'post_author_override', 'selected' => empty($post_ID) ? $user_ID : $post->post_author) );
			} else {
				// adapted from /wp-admin/edit-form-advanced.php (2.2.2)
				echo '<select name="post_author_override" id="post_author_override">' . "\n";
				foreach ($authors as $o) {
					$o = get_userdata( $o );
					if ( $post->post_author == $o->ID || ( empty($post_ID) && $user_ID == $o->ID ) ) $selected = 'selected="selected"';
					else $selected = '';
					echo "<option value='" . (int) $o->ID . "' $selected>" . wp_specialchars( $o->display_name ) . "</option>";
				}
				echo '</select>' . "\n";
			}
			echo "</div>\n";
		}

?>

<?php if ( $this->context == 'page' ) : ?>

		<div class="row">
			<label for="parent_id"><?php _e('Parent', 'wphone'); ?></label>
			<br class="accessible" />
			<select name="parent_id" id="parent_id" class="widefield">
				<option value='0'><?php _e('Main Page (no parent)'); ?></option>
				<?php parent_dropdown($post->post_parent); ?>
			</select>
		</div>

		<?php if ( 0 != count( get_page_templates() ) ) : ?>
			<div class="row">
				<label for="page_template"><?php _e('Template', 'wphone'); ?></label>
				<br class="accessible" />
				<select name="page_template" id="page_template" class="widefield">
					<option value='default'><?php _e('Default Template'); ?></option>
					<?php page_template_dropdown($post->page_template); ?>
				</select>
			</div>
		<?php endif; ?>

		<div class="row">
			<label for="menu_order"><?php _e('Page Order'); ?></label>
			<br class="accessible" />
			<input class="" type="text" name="menu_order" id="menu_order" value="<?php echo $post->menu_order; ?>"/>
		</div>
<?php endif; ?>

	</fieldset>

<?php
	
	$this->panel_button('submit', 'save', __('Save and Continue Editing'));
	$this->panel_button('submit', 'submit', __('Save'));

	if ( !in_array( $post->post_status, array('publish', 'future') ) || 0 == $post_ID ) {
		if ( current_user_can('publish_posts') ) {
			$this->panel_button('submit', 'publish', __('Publish'));
		}
		else{
			$this->panel_button('submit', 'publish', __('Submit for Review'));
		}
	}
	
	if ( $subcontext == 'edit' ) {
		$question = sprintf( ('draft' == $post->post_status) ? __("You are about to delete this draft '%s'\n  'Cancel' to stop, 'OK' to delete.") : __("You are about to delete this post '%s'\n  'Cancel' to stop, 'OK' to delete."), $post->post_title );
		$url = wp_nonce_url('post.php?action=delete&amp;post=' . $post->ID . '&amp;_wp_http_referer=' . $redirect, 'delete-post_' . $post->ID);
		$this->panel_delete_button(sprintf( __('Delete This %s'), $fancy_type), $question, $url );
	}
?>


</form>

<?php $this->test_interface('footer'); ?>