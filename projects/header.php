<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>Oliver Steele: Projects</title>
  <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://osteele.com/feed/" />
  <link rel="alternate" type="text/xml" title="RSS .92" href="http://osteele.com/feed/rss/" />
  <link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="http://osteele.com/feed/atom/" />
  <link rel="stylesheet" type="text/css" href="style.css" />
<?php if ($_SERVER['HTTP_HOST'] != 'osteele.dev') { ?>
  <script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
  </script>
<script type="text/javascript">
_uacct = "UA-202010-1";
urchinTracker();
</script>
<?php } ?>
<script>
function getProjectBlocks() {
  var es = document.getElementById('projects').getElementsByTagName('div');
  var projects = [];
  for (var i in es)
    if (es[i].className == 'project') projects.push(es[i]);
  return projects;
}
function selectProjects(indices) {
  var projects = getProjectBlocks();
  for (var i in projects)
    projects[i].style.display = 'none';
  for (var j in indices)
    projects[indices[j]].style.display = null;
  document.getElementById('nomatches').style.display = indices.length ? 'none' : null;
}
</script>
</head>
<body>

<h1>Oliver's Projects</h1>

<ul class="nav">
<li><a href="/about/">About</a></li>
<li><a href="/archives/">Archives</a></li>
<li><b>Projects</b></li>
<li><a href="/blog/">Blog</a></li>
</ul>

<object type="application/x-shockwave-flash" data="nav.lzx.swf?lzproxied=false" width="100%" height="25">
<param name="movie" value="nav.lzx.swf?lzproxied=false">
<param name="quality" value="high">
<param name="scale" value="noscale">
<param name="salign" value="LT">
<param name="menu" value="false"
></object>

<span id="nomatches" style="display: none">No matches</span>
