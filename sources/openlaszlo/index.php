<?php 
if (!isset($title)) {
	$standalone = $title = 'OpenLaszlo Sources';
	include('../../includes/header.php');
?>
<h1><?php echo $title ?></h1>
<?php } ?>

<dl>
  <dt><a href="/sources/openlaszlo/json">JSON for OpenLaszlo</a></dt>

  <dt><a href="/archives/2006/02/javascript-beziers">Bezier Library</a></dt>
  <dd>The <a href="/archives/2006/02/javascript-beziers">JavaScript Bezier Library</a> works with OpenLaszlo too.  (<a href="/sources/javascript/bezier-demo.swf">demo</a>).</dd>

<dt><a href="/sources/openlaszlo/drawview-patches.js">LzDrawView patches</a></dt>
<dd>Patches to the OpenLaszlo <tt>LzDrawView</tt> class to make it (more) compatible with WHATWG <tt>&lt;canvas&gt;</tt>: a fix to <tt>arc()</tt>; an implementation of <tt>bezierCurveTo()</tt>; and patches to <tt>stroke()</tt> and <tt>fill()</tt> to accept (some) CSS color strings.</dd>

<dt><a href="http://ropenlaszlo.rubyforge.org">OpenLaszlo Ruby Gem</a></dt>

<dt><a href="http://laszlo-plugin.rubyforge.org">OpenLaszlo Rails Plugin</a></dt>

<dt><a href="/sources/openlaszlo/simple-log.js">Simple log</a></dt>
<dd>A simple logging facility.  This adds <tt>info</tt>, <tt>debug</tt>, <tt>warn</tt>, and <tt>error</tt> functions, for compatability for the logging methods in <a href="/sources/javascript/docs/readable">readable</a>, <a href="/sources/javascript/docs/inline-console">inline console</a>, and <a href="http://www.alistapart.com/articles/jslogging">fvlogger</a> (for JavaScript libraries which are intended for use with both OpenLaszlo and Rhino, or OpenLaszlo and DHTML).</dd>
</dl>

<?php if($standalone && false) { ?>
	<p>OpenLaszlo sources (which are written in JavaScript, but use the OpenLaszlo APIs) are <a href="/sources/openlaszlo">here</a>.</p>
<?php
  include('../../includes/footer.php');
  } ?>