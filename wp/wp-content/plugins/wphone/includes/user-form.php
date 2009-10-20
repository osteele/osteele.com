<?php

/**
 * @note: Important: user.form.php to always be loaded by user.php
 * 
 * @note FOR VIPER:
 * Because WP does not have a user-new.php, I'm not sure if we can
 * use one form for the edit or add like the rest of the app.'
 * If needed, the $subcontext variable, which is set in user.php
 * can help you define if it's an add or edit or profile form.
 */

global $wp_http_referer, $user_ID, $wp_roles, $add_user_errors;

if ( file_exists( ABSPATH . 'wp-admin/admin-functions.php' ) ) {
	require_once( ABSPATH . 'wp-admin/admin-functions.php' );
} else {
	require_once( ABSPATH . 'wp-admin/includes/user.php' );
}

if ($this->context == 'edit') {

	// @note USER EDIT OR PROFILE FORM
	if ( $this->iscompat ) $selected_form = ' selected="true"';
	$disabled_field = ' disabled="disabled"';

	$pass_title = __('New Pass:', 'wphone');

	if ( ($this->current_basename == 'profile.php') || !current_user_can('edit_users') ) {
		
		// @note PROFILE FORM
		global $userdata;
		
		$subcontext = 'profile';
		$edit_user = get_user_to_edit($user_ID);
		$form_title = __('Your Profile');
		$nonceid = 'update-profile_' . $edit_user->ID;
		$formaction = 'profile-update.php';
	}
	else {
		
		// // @note USER EDIT FORM
		$this->check_user_permissions('edit_users');
		
		$subcontext = 'edit';
		$edit_id = (int) $_GET['user_id'];
		$edit_user = ($edit_id) ? get_user_to_edit($edit_id) : null;
		$form_title = __('Edit User');
		$nonceid = 'update-user_' . $edit_user->ID;
		$formaction = 'user-edit.php';
	}

} else {

	// @note ADD FORM, LIKE WP AFTER USER LIST
	$this->check_user_permissions('edit_users');
	
	$this->context = 'list';
	$subcontext = 'add';
	$form_title = __('Add New User');
	$pass_title = __('Password:', 'wphone');
	$nonceid = 'add-user';
	$formaction = 'users.php';

	if ( is_wp_error($add_user_errors) ) $selected_form = ' selected="true"';
}

if ( ! $this->iscompat ) {
	// note we need this one conditional because the same form is used in multiple contexts
	echo '<h2 class="accessible">' . $form_title . "</h2>\n";
}

?>
<form id="user<?php echo $subcontext; ?>formmenu" title="<?php echo $form_title; ?>" class="panel" action="<?php echo $formaction; ?>" method="post" target="_self" <?php echo $selected_form; ?> >

<?php wp_nonce_field($nonceid) ?>

<?php if ( $wp_http_referer ) : ?>
		<input type="hidden" name="wp_http_referer" value="<?php echo clean_url($wp_http_referer); ?>" />
<?php endif; ?>

<?php if ($this->context == 'edit') : ?>
		<input type="hidden" name="from" value="profile" />
		<input type="hidden" name="checkuser_id" value="<?php echo $edit_user->ID ?>" />
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="update" value="true" />
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $edit_user->ID; ?>" />
<?php else: ?>
		<input type="hidden" name="action" value="adduser" />
<?php endif; ?>


<?php
	// Display errors
	if ( is_wp_error($add_user_errors) ) {
		echo '<fieldset><div>';

		foreach ( $add_user_errors->get_error_messages() as $message )
			echo '<p class="errormsg">' . $message . '</p>';

		echo '</div></fieldset>';

		// Map $_POST to $edit_user
		$edit_user->user_login   = $_POST['user_login'];
		$edit_user->first_name   = $_POST['first_name'];
		$edit_user->last_name    = $_POST['last_name'];
		$edit_user->nickname     = $_POST['nickname'];
		$edit_user->user_email   = $_POST['email'];
		$edit_user->user_url     = $_POST['url'];
		$edit_user->rich_editing = $_POST['rich_editing'];
		$edit_user->description  = $_POST['description'];
	}
?>


	<fieldset>
		<div class="row">
			<label for="user_login"><?php _e('Username:', 'wphone'); ?></label>
			<br class="accessible" />
			<input class="narrowfield" type="text" name="user_login" id="user_login" value="<?php echo attribute_escape( $edit_user->user_login ); ?>"<?php echo $disabled_field; ?> />
		</div>
<?php if ( 'profile' !=  $subcontext) : ?>
		<div class="row">
			<label for="role"><?php _e('Role:') ?></label>
			<br class="accessible" />
			<select name="role" id="role" class="">
<?php
			if ($this->context == 'edit') {
				
				// Adapted from /wp-admin/user-edit.php (WP 2.3)
				
				$user_has_role = false;
				
				foreach($wp_roles->role_names as $role => $name) {
					if ( in_array($role, $edit_user->roles) ) {
						$selected = ' selected="selected"';
						$user_has_role = true;
					} else {
						$selected = '';
					}
					echo "<option value=\"{$role}\"{$selected}>{$name}</option>";
				}
				
				if ( $user_has_role )
					echo '<option value="">' . __('&mdash; No role for this blog &mdash;') . '</option>';
				else
					echo '<option value="" selected="selected">' . __('&mdash; No role for this blog &mdash;') . '</option>';
				
			} else {

				// Adapted from /wp-admin/users.php (WP 2.3)
				
				$new_user_role = ( $_POST['role'] ) ? $_POST['role'] : get_option('default_role');
				wp_dropdown_roles($new_user_role);
			}
?>
			</select>
		</div>
<?php endif; ?>
		<div class="row">
			<label for="first_name"><?php _e('First name:'); ?></label>
			<br class="accessible" />
			<input class="narrowfield" type="text" name="first_name" id="first_name" value="<?php echo attribute_escape( $edit_user->first_name ); ?>"/>
		</div>
		<div class="row">
			<label for="last_name"><?php _e('Last name:'); ?></label>
			<br class="accessible" />
			<input class="narrowfield" type="text" name="last_name" id="last_name" value="<?php echo attribute_escape( $edit_user->last_name ); ?>"/>
		</div>
		<div class="row">
			<label for="nickname"><?php _e('Nickname:'); ?></label>
			<br class="accessible" />
			<input class="narrowfield" type="text" name="nickname" id="nickname" value="<?php echo attribute_escape( $edit_user->nickname ); ?>"/>
		</div>
<?php if ( $this->context == 'edit') : ?>
		<div class="row">
			<label for="display_name"><?php _e('Display:', 'wphone'); ?></label>
			<br class="accessible" />
			<select name="display_name">
			<option value="<?php echo $edit_user->display_name; ?>"><?php echo $edit_user->display_name; ?></option>
			<option value="<?php echo $edit_user->nickname ?>"><?php echo $edit_user->nickname ?></option>
			<option value="<?php echo $edit_user->user_login ?>"><?php echo $edit_user->user_login ?></option>
			<?php if ( !empty( $edit_user->first_name ) ) : ?>
			<option value="<?php echo $edit_user->first_name ?>"><?php echo $edit_user->first_name ?></option>
			<?php endif; ?>
			<?php if ( !empty( $edit_user->last_name ) ) : ?>
			<option value="<?php echo $edit_user->last_name ?>"><?php echo $edit_user->last_name ?></option>
			<?php endif; ?>
			<?php if ( !empty( $edit_user->first_name ) && !empty( $edit_user->last_name ) ) : ?>
			<option value="<?php echo $edit_user->first_name." ".$edit_user->last_name ?>"><?php echo $edit_user->first_name." ".$edit_user->last_name ?></option>
			<option value="<?php echo $edit_user->last_name." ".$edit_user->first_name ?>"><?php echo $edit_user->last_name." ".$edit_user->first_name ?></option>
			<?php endif; ?>
			</select>
		</div>
<?php endif; ?>
	</fieldset>
	<fieldset>
		<div class="row">
<?php if ( $edit_user->user_email ) : ?>
			<label for="email"><a href="mailto:<?php echo attribute_escape( $edit_user->user_email ); ?>" <?php $this->htmltarget('_self'); ?>><?php _e('E-mail:', 'wphone'); ?></a></label>
<?php else: ?>
			<label for="url"><?php _e('E-mail:', 'wphone'); ?></label>
<?php endif; ?>
			<br class="accessible" />
			<input class="narrowfield" type="text" name="email" id="email" value="<?php echo attribute_escape( $edit_user->user_email ); ?>"/>
		</div>
		<div class="row">
<?php if ( $edit_user->user_url && ('http://' != $edit_user->user_url) ) : ?>
			<label for="url"><a href="<?php echo attribute_escape( $edit_user->user_url ); ?>" <?php $this->htmltarget('_blank'); ?>><?php _e('Website:'); ?></a></label>
<?php else: ?>
			<label for="url"><?php _e('Website:'); ?></label>
<?php endif; ?>
			<br class="accessible" />
			<input class="narrowfield" type="text" name="url" id="url" value="<?php echo attribute_escape( $edit_user->user_url ); ?>"/>
		</div>
	</fieldset>
	<fieldset>
		<div class="row">
			<label for="pass1"><?php echo $pass_title; ?></label>
			<br class="accessible" />
			<input class="narrowfield" type="password" name="pass1" id="pass1" />
		</div>
		<div class="row">
			<label for="pass2"><?php _e('Confirm:'); ?></label>
			<br class="accessible" />
			<input class="narrowfield" type="password" name="pass2" id="pass2" />
		</div>
	</fieldset>
	<fieldset>
		<div class="row">
			<label for="rich_editing"><?php _e('Use visual editor:', 'wphone'); ?></label>
			<br class="accessible" />
<?php
			$checked = ( $edit_user->rich_editing == 'true') ? 'checked="checked"' : '';
			echo '<input id="rich_editing" class="accessible" type="checkbox" name="rich_editing" value="true" ' . $checked . '/>' . "\n";
			if ($this->iscompat) {
				$toggled = ( $checked ) ? 'true' : 'false';
				echo '<div id="rich_editing-toggle" class="toggle" onclick="WPhone.toggleCheckbox(this);" toggled="' . $toggled . '"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>' . "\n";
			}
?>
		</div>
	</fieldset>
	<fieldset>
		<div class="labelrow">
			<label for="description"><?php _e('About:', 'wphone'); ?></label>
			<br class="accessible" />
		</div>
		<div class="inputrow">
			<textarea rows="10" cols="25" name="description" id="description"><?php echo $edit_user->description ?></textarea>
		</div>
	</fieldset>
<?php
	switch ( $subcontext ) {
		case 'edit':
			$button_name  = 'submit';
			$button_value = __('Update User &raquo;');
			break;
		case 'profile':
			$button_name  = 'submit';
			$button_value = __('Update Profile &raquo;');
			break;
		default:
			$button_name  = 'adduser';
			$button_value = __('Add User &raquo;');
	}

	$this->panel_button( 'submit', $button_name, $button_value );

	/*
	Note: User delete not supported via _GET. Keeping for future
	if ($this->context == 'edit') {
		$question = sprintf( __("You are about to delete this user '%s'\n  'Cancel' to stop, 'OK' to delete.", 'wphone'), $edit_user->user_login );
		$url = wp_nonce_url('users.php?action=delete&amp;users[]=' . $edit_user->ID, 'delete-users');
		$this->panel_delete_button(__('Delete'), $question, $url );
	}
	*/
?>
</form>
