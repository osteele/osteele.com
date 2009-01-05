<?php

$this->test_interface('header');

if ( !isset($this->context) || ( ($this->context != 'edit') && ($this->context != 'list') ) ) $this->context = 'edit';

$fancy_text = ucfirst($this->context);

if ( $this->context == 'edit' ) {

	// EDIT FORM

	require_once( ABSPATH . $this->folder . '/includes/category-form.php' );

} elseif ( $_GET['add'] ) {

	// ADD FORM

	require_once( ABSPATH . $this->folder . '/includes/category-form.php' );

} else {

	// CATEGORY LIST

	if ( !$this->iscompat ) echo '<h2 class="accessible">' . __('Categories') . "</h2>\n";

	echo '<ul id="categorylistmenu" title="' . __('Categories') . '"';
	if ( $this->iscompat ) echo ' selected="true"';
	echo ">\n";

	if ( current_user_can('manage_categories') ) {

		// Display errors
		$messages[4] = __('Category not added.');
		$messages[5] = __('Category not updated.');
		if ( isset( $messages[$_GET['message']] ) ) echo '<div><p class="errormsg">' . $messages[$_GET['message']] . '</p></div>';


		$categories = get_categories('orderby=name&hide_empty=0&hierarchical=0');

		$categories_count = count($categories);

		if ( 0 < count($categories) ) {
			foreach ( $categories as $category ) {
				echo '<li><a href="' . $this->admin_url . '/categories.php?wphone=ajax&amp;action=edit&amp;cat_ID=' . $category->cat_ID . '&amp;parent=categories">' . strip_tags($category->cat_name) . "</a></li>\n";
			}
		} else {
			echo '<li>' . __('No categories found.', 'wphone') . "</li>\n";
		}

	}

	echo "</ul>\n";

}

$this->test_interface('footer');

?>