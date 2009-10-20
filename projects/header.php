<?php header("Content-type: text/html; charset=utf-8"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>Oliver Steele: Projects</title>
  <link rel="shortcut icon" href="/favicon.ico" />
  <link rel="alternate" type="application/rss+xml" title="Oliver Steele RSS Feed" href="http://osteele.com/feed" />
  <link rel="pingback" href="http://osteele.com/wp/xmlrpc.php" />
  <meta name="description" content="Oliver Steele's software projects, software libraries, writings, web sites, and web applications."/>
  <link rel="stylesheet" type="text/css" href="styles.css" />
  <link href='/stylesheets/banner.css' rel='stylesheet' type='text/css' />
  <link href="/stylesheets/banner.iphone.css" rel="stylesheet" type="text/css" media="only screen and (max-device-width: 480px)"/>
<?php if ($_SERVER['HTTP_HOST'] == 'osteele.dev') { ?>
  <script type="text/javascript" src="/js/swfobject-2.1.js"></script>
  <script type="text/javascript" src="/js/jquery-1.3.1.min.js"></script>
<?php } else { ?>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js"></script>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script>
<?php } ?>
  <script type="text/javascript" src="/js/shadowbox-2.0-min.js"></script>
  <script type="text/javascript" src="projects.js"></script>
</head>
<body class="projects">

<h1>Oliver's Projects</h1>
<?php include('../includes/nav.php'); ?>

<div id="flashcontent"><div id="flashcontent-swf"></div></div>
<script type="text/javascript">//<![CDATA[
var flashvars = {lzproxied: "false"};
var match = window.location.search.match(/\bcategory=([^&]*)/);
if (match) flashvars.category = match[1];
swfobject.embedSWF("nav.swf", "flashcontent-swf", "100%", "36", "7", null, flashvars, {scale: "noscale"});
//]]></script>

<span id="nomatches" style="display: none">No matches</span>
