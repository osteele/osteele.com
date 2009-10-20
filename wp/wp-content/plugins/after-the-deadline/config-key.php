<?php
/*
 * post an admin message if no AtD key is set.
 */
if ( !get_option('AtD_api_key') && !$atd_api_key && !isset($_POST['submit']) ) {

	function AtD_warning() {
		echo "<div id='atd-warning' class='updated fade'><p><strong>" . __('After the Deadline is almost ready.') . '</strong> ' . sprintf(  __( 'You must <a href="%1$s">enter your API key</a> for it to work.' ), 'plugins.php?page=atd-key-config'  )  . '</p></div>';
	}
	add_action( 'admin_notices', 'AtD_warning' );
}

/*
 * connect to the AtD service and verify the key the user entered
 */
function AtD_verify_key( $key ) {
	$session = AtD_http_get( 'service.afterthedeadline.com', '/verify?blog=' . urlencode( get_option( 'home' ) ) . '&key=' . urlencode( $key ));
	return $session[1];
}

/*
 * display the form for the user to enter the key, also saving of the key option is handled here as well.
 */
function AtD_display_key_form() {

	if ( isset($_POST['submit']) ) {
		if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {
			die(  __('Cheatin&#8217; uh?')  );
		}

		$key = preg_replace( '/[^a-h0-9]/i', '', $_POST['key'] );
	} else {
		$key = get_option( 'AtD_api_key' );
	}

	if ( empty($key) ) {
		$key_status = 'empty';
		$ms[] = isset( $_POST['submit'] ) ? 'new_key_empty' : 'key_empty';
		delete_option( 'AtD_api_key' );
	} else {
		$key_status = trim(  AtD_verify_key( $key )  );

		if ( strcmp( $key_status, 'valid' ) == 0 || strcmp( $key_status, 'Got it!' ) == 0) {

			update_option( 'AtD_api_key', $key );
			$ms[] = isset( $_POST['submit'] ) ? 'new_key_valid' : 'key_valid';

		} else if ( strcmp( $key_status, 'invalid' ) == 0 ) {
			delete_option( 'AtD_api_key' );
			$ms[] = isset( $_POST['submit'] ) ? 'new_key_invalid' : 'key_invalid';
		} else {
			$ms[] = isset( $_POST['submit'] ) ? 'new_key_failed' : 'key_failed';
		}
	}

	/* potential messages */

	$messages = array(
		'new_key_empty' => array('color' => 'aa0', 'text' => __('Your key is cleared.')),
		'new_key_valid' => array('color' => '2d2', 'text' => __('Your key is now verified. Enjoy!')),
		'new_key_invalid' => array('color' => 'd22', 'text' => __('The key you entered is invalid. Please check it.')),
		'new_key_failed' => array('color' => 'd22', 'text' => __('Unable to connect to afterthedeadline.com to verify your key.  Please check your server configuration.')),
		'key_empty' => array('color' => 'aa0', 'text' => sprintf(__('Please enter an API key. (<a href="%s" target="_new" style="color:#fff">Get your key.</a>)'), 'http://www.afterthedeadline.com/profile.slp')),
		'key_valid' => array('color' => '2d2', 'text' => __('Your key is valid.')),
		'key_failed' => array('color' => 'aa0', 'text' => __('Your key was valid but I was unable to connect to afterthedeadline.com and verify it.'))
	);
   ?>

<!-- show off the options saved -->

<?php if ( !empty($_POST ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>

<di class="wrap">
<h2><?php _e('After the Deadline: Proofreading Software'); ?></h2>
<div class="narrow">
<form action="" method="post" id="atd-conf" style="width: 400px; margin: auto">
      <p>
         Write better and spend less time editing with After the Deadline.  To use After the Deadline you'll need an API key.  You can get this from the <a href="http://www.afterthedeadline.com/profile.slp">After the Deadline</a> website.
      </p>

      <h3><label for="key">API Key</label></h3>

      <?php
	if ( count($ms) > 0 ) {
		foreach ( $ms as $m ) {
      ?>
        <p style="padding: .5em; background-color: #<?php echo $messages[$m]['color']; ?>; color: #fff; font-weight: bold;"><?php echo $messages[$m]['text']; ?></p>
      <?php
		}
	}
      ?>

     <p>        <input id="key" name="key" type="text" size="32" maxlength="32" value="<?php echo get_option('AtD_api_key'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" />
      </p>

      <p class="submit"><input type="submit" name="submit" value="<?php _e('Update key &raquo;'); ?>" /></p>

      <?php
	if ( $invalid_key ) {
      ?>
          <h3><?php _e('Why might my key be invalid?'); ?></h3>
         <p><?php _e('This can mean one of two things, either you copied the key wrong or that the plugin is unable to reach the Akismet servers, which is most often caused by an issue with your web host around firewalls or similar.'); ?></p>
      <?php
	}
      ?>

<h3>Using</h3>

<p>Click <img src="<?php echo WP_PLUGIN_URL . '/after-the-deadline/button.gif'; ?>" border="0" alt="the AtD button"> in the editor to check 
spelling, style, and grammar.</p>

<h3>Options</h3>

<p>Visit your <a href="profile.php">profile page</a> to configure After the Deadline proofreading software options.</p>

<h3>Feedback</h3>

<p>Visit <a href="http://www.afterthedeadline.com/support">After the Deadline</a> to give feedback, report bugs, and offer suggestions.</p>

</form>
</div>
</div>


<?php 
}
