<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-EMail 2.20										|
|	Copyright (c) 2005 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://lesterchan.net															|
|																							|
|	File Information:																	|
|	- E-Mail Post/Page To A Friend (Popup Window)							|
|	- wp-content/plugins/email/wp-email-popup.php						|
|																							|
+----------------------------------------------------------------+
*/


### Session Start
@session_start();

### Filters
add_filter('wp_title', 'email_pagetitle');
add_filter('the_title', 'email_title');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
	<meta name="robots" content="noindex, nofollow" />
	<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; <?php _e('Blog Archive', 'wp-email'); ?> <?php } ?> <?php wp_title(); ?></title>
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<style type="text/css" media="screen">
		BODY {
			/* background: #ffffff; */
		}
		P {
			margin-left: 10px;
			text-align: left;
		}
		input.Forms, textarea.Forms { border-top: 1px solid #999; border-left: 1px solid #999; border-bottom: 1px solid #dadada; border-right: 1px solid #dadada; padding: 2px; }
		
		input.buttonem { background: #dadada; }
		
	</style>
	<?php wp_head(); ?>
</head>
<body>
	<?php email_form(true); ?>
	</p><p style="text-align: left; padding-top: 50px; font-size: 11px;"><a href="#" onclick="window.close();"><?php _e('Close This Window', 'wp-email'); ?></a></p>
	<?php wp_footer(); ?>
</body>
</html>