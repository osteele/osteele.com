<?php

$this->check_user_permissions('manage_categories');

global $wp_http_referer, $user_ID, $wp_roles;

if ( file_exists( ABSPATH . 'wp-admin/admin-functions.php' ) )
	require_once( ABSPATH . 'wp-admin/admin-functions.php' );
else
	require_once( ABSPATH . 'wp-admin/includes/taxonomy.php' );

$cat_ID = (int) $_GET['cat_ID'];
$category = get_category_to_edit($cat_ID);

if ( !empty($cat_ID) ) {
	$heading = __('Edit Category');
	$submit_text = __('Edit Category &raquo;');
	$action = 'editedcat';
	$form_id = 'editcat';
	$nonce_action = 'update-category_' . $cat_ID;
	do_action('edit_category_form_pre', $category);
	if ( $this->iscompat ) $selected_form = ' selected="true"';
} else {
	$heading = __('Add Category');
	$submit_text = __('Add Category &raquo;');
	$action = 'addcat';
	$form_id = $action;
	$nonce_action = 'add-category';
	do_action('add_category_form_pre', $category);
}

if ( ! $this->iscompat ) {
	// note we need this one conditional because the same form is used in multiple contexts
	echo '<h2 class="accessible">' . $form_title . "</h2>\n";
}

?>
<form id="<?php echo $form_id; ?>" title="<?php echo $heading; ?>" action="categories.php" method="post" class="panel" target="_self"<?php echo $selected_form; ?> >

	<input type="hidden" name="action" value="<?php echo $action; ?>" />
	<input type="hidden" name="cat_ID" value="<?php echo $category->cat_ID; ?>" />
<?php wp_nonce_field($nonce_action); ?>

	<fieldset>
		<div class="row">
			<label for="cat_name"><?php _e('Name:', 'wphone'); ?></label>
			<br class="accessible" />
			<input class="widefield" type="text" name="cat_name" id="cat_name" value="<?php echo attribute_escape($category->cat_name); ?>" />
		</div>
		<div class="row">
			<label for="category_nicename"><?php _e('Slug:', 'wphone'); ?></label>
			<br class="accessible" />
			<input class="widefield" type="text" name="category_nicename" id="category_nicename" value="<?php echo attribute_escape( $category->category_nicename ); ?>"/>
		</div>
		<div class="row">
			<label for="category_parent"><?php _e('Parent:', 'wphone') ?></label>
			<br class="accessible" />
<?php
			// Adapted from /wp-admin/categories.php (WP 2.3)
			wp_dropdown_categories('exclude=' . $category->cat_ID . '&hide_empty=0&class=widefield&orderby=name&hierarchical=1&name=category_parent&orderby=name&hierarchical=1&selected=' . $category->category_parent . '&hierarchical=1&show_option_none=' . __('None'));
?>
		</div>
	</fieldset>
	<fieldset>
		<div class="labelrow">
			<label for="category_description"><?php _e('Description: (optional)'); ?></label>
			<br class="accessible" />
		</div>
		<div class="inputrow">
			<textarea rows="10" cols="25" name="category_description" id="category_description"><?php echo wp_specialchars($category->category_description) ?></textarea>
		</div>
	</fieldset>
<?php
	$this->panel_button( 'submit', 'submit', $submit_text );

	if ($this->context == 'edit') {
		$default_cat_id = (int) get_option( 'default_category' );
		$default_link_cat_id = (int) get_option( 'default_link_category' );
		$question = sprintf( __("You are about to delete the category '%s'.\nAll posts that were only assigned to this category will be assigned to the '%s' category.\nAll links that were only assigned to this category will be assigned to the '%s' category.\n'OK' to delete, 'Cancel' to stop." ), $category->name, get_catname( $default_cat_id ), get_catname( $default_link_cat_id ) );
		$url = wp_nonce_url( "categories.php?action=delete&amp;cat_ID=$category->term_id", 'delete-category_' . $category->term_id );
		$this->panel_delete_button(__('Delete'), $question, $url );
	}
?>
</form>
