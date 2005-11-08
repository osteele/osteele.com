<?php 
$startTime = microtime();
if (! isset($title)):
  $title = 'Oliver Steele';
endif;

if (!isset($bodyClass)):
  $bodyClass='static';
endif;

if (isset($isblog)):
  require('./wp-blog-header.php');
//$title = bloginfo('name') . wp_title();
  $title = 'Oliver Steele: ' . wp_title();
endif;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/1">
  <title><?php echo($title); ?></title>

  <style type="text/css" media="screen">
    @import url( /wp/wp-layout.css );
  </style>
  <link rel="stylesheet" type="text/css" media="print" href="/css/print.css" />
  
<?php if (isset($isblog)):
  include('wp-header.php');
endif; ?>

  <script type="text/javascript" language="JavaScript" src="/includes/embed.js"></script>
</head>

<body class="<?php echo($bodyClass)?>">
<!-- BEGIN #container -->
<div id="container" class="<?php echo($bodyClass)?>">

<!-- BEGIN #header -->
<div id="header">
<h1><a href="/">Oliver Steele</a></h1>

<ul id="nav">
  <li><a class="home-button" href="/">Home</a></li>
  <li><a class="blog-button" href="/blog/">Blog</a></li>
  <li><a class="software-button" href="/software/">Software</a></li>
  <li><a class="portfolio-button" href="/portfolio/">Portfolio</a></li>
  <li><a class="bio-button" href="/bio/">Bio</a></li>
  <li><a class="contact-button" href="/contact/">Contact</a></li>
  <li><a class="site-button" href="/site/">Site</a></li>
</ul>
<br style="clear: both" />

<!-- END #header -->
</div>

<!-- BEGIN #content -->
<div id="content">
