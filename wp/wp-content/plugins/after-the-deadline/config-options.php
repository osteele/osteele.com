<?php
/*
 *   Display the configuration options for AtD
 */

/*
 *   A convienence function to display the HTML for an AtD option
 */
function AtD_print_option( $name, $value, $options ) {
?>
   <input type="checkbox" name="atd_options[<?php echo $name; ?>]" value="1" <?php checked( '1', $options[$name] ); ?>> <?php echo $value; ?>
<?php
}

/*
 *  Print a message saying AtD s not available due to the language settings
 */
function AtD_process_not_supported() {
?>
   <p><?php _e( 'WordPress checks your grammar, spelling, and misused words with <a href="http://www.afterthedeadline.com">After the Deadline</a>. This feature is available to blogs set to the English language. Blogs in other languages will continue to have access to the old spellchecker.' ); ?></p>
<?php
}

/*
 *  Save AtD options
 */
function AtD_process_options_update() {

	$user = wp_get_current_user();

	if ( ! $user || $user->ID == 0 )
		return;

	if ( is_array( $_POST['atd_options'] ) ) 
		update_usermeta( $user->ID, 'AtD_options',  implode( ',', array_keys($_POST['atd_options']) )  );
	else
		update_usermeta( $user->ID, 'AtD_options', '');
}

/*
 *  Display the various AtD options
 */
function AtD_display_options_form() {

	/* grab our user and validate their existence */
	$user = wp_get_current_user();
	if ( ! $user || $user->ID == 0 )
		return;

	$options_raw = get_usermeta($user->ID, 'AtD_options', 'single');

	$options = array();

	if ( $options_raw )
		foreach ( explode( ',', $options_raw ) as $option ) 
			$options[ $option ] = 1;         

?>
   <table class="form-table">
      <tr valign="top">
         <th scope="row"> After the Deadline</th>
         <td>
   <p>Enable proofreading for the following grammar and style rules when writing posts and pages:</p>

   <p><?php 
		AtD_print_option( 'Bias Language', 'Bias Language', $options );
		echo '<br />';
		AtD_print_option( 'Cliches', 'Clich&eacute;s', $options );
		echo '<br />';
		AtD_print_option( 'Complex Expression', 'Complex Phrases', $options ); 
		echo '<br />';
		AtD_print_option( 'Double Negative', 'Double Negatives', $options );
		echo '<br />';
		AtD_print_option( 'Hidden Verbs', 'Hidden Verbs', $options );
		echo '<br />';
		AtD_print_option( 'Jargon Language', 'Jargon', $options ); 
		echo '<br />';
		AtD_print_option( 'Passive voice', 'Passive Voice', $options ); 
		echo '<br />';
		AtD_print_option( 'Phrases to Avoid', 'Phrases to Avoid', $options ); 
		echo '<br />';
		AtD_print_option( 'Redundant Expression', 'Redundant Phrases', $options ); 
   ?></p>
<?php
}
