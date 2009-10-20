	<form id="primarynav" class="dialog" action="#">
		<fieldset>
			<h2 class="accessible"><?php _e('Navigation', 'wphone') ?></h2>
<?php if ( $this->iscompat ) : ?>
			<h1><?php _e('Go...', 'wphone'); ?></h1>
			<a class="button leftButton" type="cancel"><?php _e('Cancel', 'wphone'); ?></a>
			<a class="button blueButton" type="cancel"><?php _e('Go...', 'wphone'); ?></a>
<?php endif; ?>
			<ul id="dropnav">
<?php
	if ( !$this->iscompat && 'dashboard' != $this->context ) :
		$parent = ( !empty($_GET['parent']) ) ? $_GET['parent'] . '.php' : '';
?>
				<li><a href="<?php echo $this->admin_url . '/' . urlencode( $parent ); ?>?wphone=ajax"<?php $this->htmltarget('_self'); ?>><?php _e('&laquo; Back', 'wphone') ?></a></li>
<?php
	endif;
?>
				<li><a href="<?php echo $this->admin_url; ?>/"<?php $this->htmltarget('_self'); ?> title="<?php _e('Dashboard') ?>" id="goDashboard"><?php _e('Dashboard') ?></a></li>
				<li><a href="<?php echo $this->blog_url; ?>/"<?php $this->htmltarget('_blank'); ?> title="<?php _e('View site &raquo;') ?>" id="goSite"><?php _e('View site', 'wphone') ?></a></li>
<?php if ( function_exists( 'wp_logout_url' ) ) : ?>
				<li><a href="<?php echo wp_logout_url(); ?>"<?php $this->htmltarget('_self'); ?> title="<?php _e('Log out of this account') ?>" id="goSignout"><?php _e('Sign Out') ?></a></li>
<?php else : ?>
				<li><a href="<?php echo $this->site_url; ?>/wp-login.php?action=logout"<?php $this->htmltarget('_self'); ?> title="<?php _e('Log out of this account') ?>" id="goSignout"><?php _e('Sign Out') ?></a></li>
<?php endif; ?>
			</ul>
			<ul id="quicklinks">
<?php
	// Outputs quick links to the main features
	if ( 'dashboard' != $this->context || $this->iscompat )
		$this->quick_links( 'navigation' );
?>
				<li style="display:none"></li>
			</ul>
		</fieldset>
	</form>
</body>
</html>