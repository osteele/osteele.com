<?php 
if (!isset($title)) {
	$standalone = $title = 'OpenLaszlo Sources';
	include('../../includes/header.php');
?>
<h1><?php echo $title ?></h1>
<?php } ?>

<dl>
  <dt><a href="/sources/openlaszlo/json">JSON for OpenLaszlo</a></dt>
  <dd>An implementation of <a href="http://json.org">JSON</a> for the OpenLaszlo platform.</dd>

  <dt><a href="/archives/2006/02/javascript-beziers">Bezier Library</a></dt>
  <dd>Measure and subdivide beziers, and animate points along a path composed of single or multiple beziers.  <a href="/sources/javascript/bezier.js">bezier.js</a>, <a href="/sources/javascript/path.js">path.js</a>, <a href="/sources/javascript/bezier-demo.swf">demo</a>, <a href="/archives/2006/02/javascript-beziers">blog</a>.  (There's also an <a href="/sources/javascript">DHTML version</a>, with a demo <a href="/sources/javascript/bezier-demo.html">here</a>.)</dd>

<dt><a href="/sources/openlaszlo/drawview-patches.js">LzDrawView patches</a></dt>
<dd>Patches to the OpenLaszlo <tt>LzDrawView</tt> class to make it (more) compatible with WHATWG <tt>&lt;canvas&gt;</tt>: a fix to <tt>arc()</tt>; an implementation of <tt>bezierCurveTo()</tt>; and patches to <tt>stroke()</tt> and <tt>fill()</tt> to accept (some) CSS color strings.</dd>

<dt><a href="http://ropenlaszlo.rubyforge.org">OpenLaszlo Ruby Gem</a></dt>
<dd>A Ruby interface to the OpenLaszlo compiler. This library allows you to compile OpenLaszlo programs from within Ruby, in order to integrate OpenLaszlo development into Rake or Rails applications.</dd>

<dt><a href="http://laszlo-plugin.rubyforge.org">OpenLaszlo Rails Plugin</a></dt>
<dd>The OpenLaszlo Rails plugin makes it easy to use OpenLaszlo client-side applications with Rails. It includes generators and scaffolding for creating OpenLaszlo applications, connecting them to Rails REST controllers, and displaying them within Rails views.</dd>

<dt><a href="/sources/openlaszlo/simple-logging.js">Simple logging</a></dt>
<dd>A simple logging facility.  This library defines <tt>info</tt>, <tt>debug</tt>, <tt>warn</tt>, and <tt>error</tt> functions.  These are compatible with the logging methods in <a href="/sources/javascript/docs/readable">readable</a>, <a href="/sources/javascript/docs/inline-console">inline console</a>, and <a href="http://www.alistapart.com/articles/jslogging">fvlogger</a>, and facilitate the development of JavaScript libraries that are intended for use with both OpenLaszlo and Rhino, or OpenLaszlo and DHTML.</dd>

<dt><a href="/sources/openlaszlo/laszlo-utils.js">laszlo-utils</a></dt>
<dd>Miscellaneous patches and utilities.  Currently contains <tt>LzKeys.fromEventCode</tt>.</dd>
</dl>

<?php if($standalone && false) { ?>
	<p>OpenLaszlo sources (which are written in JavaScript, but use the OpenLaszlo APIs) are <a href="/sources/openlaszlo">here</a>.</p>
<?php
  include('../../includes/footer.php');
  } ?>