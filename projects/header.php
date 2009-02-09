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
<?php if ($_SERVER['HTTP_HOST'] == 'osteele.dev') { ?>
  <script type="text/javascript" src="/javascripts/swfobject-2.1.js"></script>
  <script type="text/javascript" src="/javascripts/jquery-1.3.1.min.js"></script>
<?php } else { ?>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js"></script>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script>
<?php } ?>
<script type="text/javascript">//<![CDATA[
function selectProjects(indices) {
  var ids = {};
  for (var i in indices) ids['project-' + indices[i]] = true;
  $('.project').each(function(i) {
    $(this)[ids[this.id] ? 'show' : 'hide'](300);
  });
  var message = '';
  if (arguments.length > 1) {
    message = indices.length == 0 ? 'No matches' : ''+indices.length+ ' match';
    if (indices.length > 1)  message += 'es';
    if (indices.length) message += ':';
  }
  var status = document.getElementById('nomatches');
  status.style.display = message == '' ? 'none' : '';
  status.innerHTML = message;
}
//]]></script>
</head>
<body>

<h1>Oliver's Projects</h1>

<ul class="nav">
<li><a href="/">Home</a></li>
<li><a href="/about/">About</a></li>
<li><a href="/archives/">Archives</a></li>
<li><a href="/sources/">Sources</a></li>
<li><b>Projects</b></li>
<li><a href="/blog/">Blog</a></li>
</ul>

<div id="flashcontent"><div id="flashcontent-swf"></div></div>
<script type="text/javascript">//<![CDATA[
var flashvars = {lzproxied: "false"};
var match = window.location.search.match(/\bcategory=([^&]*)/);
if (match) flashvars.category = match[1];
swfobject.embedSWF("nav.swf", "flashcontent-swf", "100%", "36", "7", null, flashvars, {scale: "noscale"});
//]]></script>

<span id="nomatches" style="display: none">No matches</span>
