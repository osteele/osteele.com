<?php header("Content-type: text/html; charset=utf-8"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>Oliver Steele: Projects</title>
  <link rel="shortcut icon" href="/favicon.ico" />
  <link rel="alternate" type="application/rss+xml" title="Oliver Steele RSS Feed" href="http://osteele.com/feed" />
  <link rel="pingback" href="http://osteele.com/wp/xmlrpc.php" />
  <meta name="description" content="Oliver Steele's software projects, software libraries, writings, web sites, and web applications."/>
  <link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="/javascripts/flashobject.js"></script>
<script type="text/javascript" src="/javascripts/jquery-1.3.1.min.js"></script>
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
<li><a href="/about/">About</a></li>
<li><a href="/archives/">Archives</a></li>
<li><a href="/sources/">Sources</a></li>
<li><b>Projects</b></li>
<li><a href="/blog/">Blog</a></li>
</ul>

<span style="background:yellow;color:dark-gray;size:small">Pardon our appearance.  I'm in the process of fixing up the links and formatting. &mdash; ows 2009-02-04</span>

<div id="flashcontent">
</div>
<script type="text/javascript">
var fo = new FlashObject("nav.swf", "cloud", "100%", "36", "7", "#FFFFFF");
fo.addParam("scale", "noscale");
fo.addVariable("lzproxied", "false");
var qcat = com.deconcept.util.getRequestParameter('category');
if (qcat) fo.addVariable('category', qcat);
fo.write("flashcontent");
</script>

<span id="nomatches" style="display: none">No matches</span>
