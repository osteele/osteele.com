<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-Print 2.20										|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://lesterchan.net															|
|																							|
|	File Information:																	|
|	- Printer Friendly Page															|
|	- wp-print.php																		|
|																							|
+----------------------------------------------------------------+
*/


### Variables
$links_text = '';

### Actions
add_action('init', 'print_content');

### Filters
add_filter('wp_title', 'print_pagetitle');
add_filter('comments_template', 'print_template_comments');

### Print Options
$print_options = get_option('print_options');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="Robots" content="noindex, nofollow" />
	<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-content/plugins/print/wp-print-css.css" type="text/css" media="screen, print" />
</head>
<body>
<p style="text-align: center;"><strong>- <?php bloginfo('name'); ?> - <?php bloginfo('url')?> -</strong></p>
<center>
	<div id="Outline">
		<?php if (have_posts()): ?>
			<?php while (have_posts()): the_post(); ?>
					<p id="BlogTitle"><?php the_title(); ?></p>
					<p id="BlogDate"><?php _e('Posted By', 'wp-print'); ?> <u><?php the_author(); ?></u> <?php _e('On', 'wp-print'); ?> <?php the_time(sprintf(__('%s @ %s', 'wp-print'), get_option('date_format'), get_option('time_format'))); ?> <?php _e('In', 'wp-print'); ?> <?php print_categories('<u>', '</u>'); ?> | <u><a href='#comments_controls'><?php print_comments_number(); ?></a></u></p>
					<div id="BlogContent"><?php print_content(); ?></div>
			<?php endwhile; ?>
			<hr class="Divider" style="text-align: center;" />
			<?php if(print_can('comments')): ?>
				<?php comments_template(); ?>
			<?php endif; ?>
			<p style="text-align: left;"><?php _e('Article printed from', 'wp-print'); ?> <?php bloginfo('name'); ?>: <strong><?php bloginfo('url'); ?></strong></p>
			<p style="text-align: left;"><?php _e('URL to article', 'wp-print'); ?>: <strong><?php the_permalink(); ?></strong></p>
			<?php if(print_can('links')): ?>
				<p style="text-align: left;"><?php print_links(); ?></p>
			<?php endif; ?>
			<p style="text-align: right;" id="print-link"><?php _e('Click', 'wp-print'); ?> <a href="#Print" onclick="window.print(); return false;" title="<?php _e('Click here to print.', 'wp-print'); ?>"><?php _e('here', 'wp-print'); ?></a> <?php _e('to print.', 'wp-print'); ?></p>
		<?php else: ?>
				<p style="text-align: left;"><?php _e('No posts matched your criteria.', 'wp-print'); ?></p>
		<?php endif; ?>
	</div>
</center>
<p style="text-align: center;"><?php echo stripslashes($print_options['disclaimer']); ?></p>
</body>
</html>