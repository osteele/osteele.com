<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php require('squible_options.php'); ?>
<?php require("builtin/wp_cats_as_tags.php"); ?>
<?php require('builtin/browser_detection.php'); ?>

<?php 
if ($builtin_plugins) {
	if (!function_exists('the_content_limit')) {
		require("builtin/limit-post.php");
	}

	if (!function_exists('c2c_get_recently_commented')) {
		require("builtin/customizable-post-listings.php");
	}

	if (!function_exists('get_flickrRSS')) {
		require("builtin/flickrrss.php");
	}
} 
?>

<head profile="http://gmpg.org/xfn/11">

	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->

        <link rel="alternate" type="text/xml" title="RDF" href="<?php bloginfo('rdf_url'); ?>" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/scripts/prototype.js"></script>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/scripts/effects.js"></script>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/scripts/ajax_comments.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('stylesheet_directory'); ?>/style.css" />

        <script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/nifty.js"></script>
        <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/nifty.css" />

        <script type="text/javascript">//<![CDATA[
                <?php include "livesearch.js.php"; ?>
        //]]></script>

	<?php if ( find('MSIE') ) { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('stylesheet_directory'); ?>/style_ie.css" />
	<?php } else if (find('Opera')) { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('stylesheet_directory'); ?>/style_opera.css" />
	<?php } ?>

	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('stylesheet_directory'); ?>/livesearch.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('stylesheet_directory'); ?>/custom.css" />

	<?php wp_head(); ?>

<script type="text/javascript">
window.onload=function(){
if(!NiftyCheck())
    return;
Rounded("div#nifty","tl tr br","#FFF","#eee","smooth");
Rounded("div.commentheader","tl tr br","#5087BD","#fff","smooth");
liveSearchInit();
}
</script>

</head>

<body>
<div id="main">


<div id="top">
<div id="themenu">
<ul class="menu"><li class="<?if (((is_home()) && !(is_paged())) or (is_archive()) or (is_single()) or (is_paged()) or (is_search())) { ?>current_page_item<?php } ?>"><a href="<?php echo get_settings('home'); ?>">Blog</a></li>
                        <?php wp_list_pages('sort_column=menu_order&depth=1&title_li='); ?>
                        <?php wp_register('<li>','</li>'); ?>
                </ul>

</div>

<div id="blogtitle">
<h1><a href="<?php echo get_settings('home'); ?>"><?php bloginfo('name'); ?></a></h1>
<p class="description"><?php bloginfo('description'); ?></p>
</div>
</div> <!-- /top -->

<div style="clear:both;"></div>

<div id="content">
<!-- end header -->
