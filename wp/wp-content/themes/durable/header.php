<? define("DURABLE_VERSION", "0.2"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

<link rel="stylesheet" href="<? echo get_template_directory_uri(); ?>/cssstyles.php" type="text/css" media="screen" />
<link rel="stylesheet" href="<? echo get_template_directory_uri(); ?>/colourmod/colourModStyle.php" type="text/css" media="screen" />

<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<script type="text/javascript" src="<? echo get_template_directory_uri(); ?>/jscript/effects/prototype.js"></script>
<script type="text/javascript" src="<? echo get_template_directory_uri(); ?>/jscript/effects/scriptaculous.js"></script>

<? 
global $durable;
// Don't include these scripts if color changing is disabled. 
if ($durable->option['colorchange'] != "false") { 
?>
<script type="text/javascript" src="<? echo get_template_directory_uri(); ?>/jscript/effects/draggable.js"></script>
<script type="text/javascript" src="<? echo get_template_directory_uri(); ?>/colourmod/ColourModScript.js"></script>
<script type="text/javascript" src="<? echo get_template_directory_uri(); ?>/colourmod/StyleModScript.js"></script>
<? } ?>
<script type="text/javascript" src="<? echo get_template_directory_uri(); ?>/jscript/globaljs.php"></script>

<?php wp_head(); ?>
</head>

<body<? if ($durable->option['colorchange'] != "false") { ?> onload="cookiecss()"<? } ?>>

<div id="page">

<div id="header">
		<h1 onclick="location.href='<?php echo get_settings('home'); ?>';"><?php bloginfo('name'); ?></h1>
		<p><?php bloginfo('description'); ?></p>
		<div class="bottomTear"></div>
</div>

<div id="topMenu">
	<ul id="menuItems">
		<li><a href="<?php echo get_settings('home'); ?>/" title="Home">Home</a>&nbsp;&brvbar;&nbsp;</li>
		<li id="archivesLink"><a href="javascript: toggleMenu('archives', 'menuItems');" title="Show the Archives Section">Archives</a>&nbsp;&brvbar;&nbsp;</li>
		<li id="linksLink"><a href="javascript: toggleMenu('links', 'menuItems');" title="Show the Pages &amp; Links Section">Pages &amp; Links</a>&nbsp;&brvbar;&nbsp;</li>
		<li id="searchLink"><a href="javascript: toggleMenu('search', 'menuItems');" title="Show the Search Section">Live Search</a></li>
		<? if ($durable->option['colorchange'] != "false") { ?><li>&nbsp;&brvbar;&nbsp;<a href="javascript:;" onmousedown="togglePanel('colourControl');" title="Set your personal options">Options &nbsp;<img src="<? echo get_template_directory_uri(); ?>/images/wheel.gif" alt="Colour Wheel" /></a></li><? } ?>
	</ul>
</div>

<? if (rand(0,1) == 1) { ?>
<div style="width:728px; margin-left: auto; margin-right: auto">
<script type="text/javascript"><!--
	google_ad_client = "pub-7558884554835464";
google_ad_width = 728;
google_ad_height = 15;
google_ad_format = "728x15_0ads_al";
google_ad_channel ="3072123568";
google_color_border = "FFFFFF";
google_color_bg = "FFFFFF";
google_color_link = "707070";
google_color_url = "F5F5F5";
google_color_text = "707070";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
<? } ?>

<?php get_sidebar(); ?>
	
<div id="mainContent">
	<div class="topTear"></div>