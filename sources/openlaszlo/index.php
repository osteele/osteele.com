<?php 
if (!isset($title)) {
	$standalone = $title = 'OpenLaszlo Sources';
	include('../../includes/header.php');
?>
<h1><?php echo $title ?></h1>

<div style="float:right"><iframe src="/sources/openlaszlo/bezier-demo.swf" width="310" height="300"></iframe><br/>
<iframe src="/sources/openlaszlo/textdrawview-example.swf" width="320" height="130"></iframe></div>
<?php } ?>

<h3>Utilities</h3>
<dl>
  <dt><a href="http://github.com/osteele/collections-js">Collections-js</a></dt>
  <dd>JavaScript collection methods.  Provides the functionality of
  the <a
  href="http://developer.mozilla.org/en/docs/New_in_JavaScript_1.6">ECMAScript
  1.7 Array extensions</a>, and the <a
  href="http://www.prototypejs.org/">Prototype</a> collection methods,
  in a lightweight form that can be used with other frameworks,
  including OpenLaszlo.</dd>
  
  <dt><a href="http://github.com/osteele/lzosutils">LzOsUtils</a></dt>
  <dd>Miscellaneous utilities for OpenLaszlo, including
  jQuery-compatible AJAX functions and a <a
  href="/archives/2008/04/flashbridge">Flash&harr;JavaScript
  bridge</a>.  <a
  href="http://github.com/osteele/lzosutils">Download</a>.</dd>

  <dt><a href="http://github.com/osteele/lztestkit">LzTestKit</a>
  <dd>Extensions to LzUnit (the OpenLaszlo test framework): define
  mocks, stub, and expectations; run individual test cases; run all
  the test applets in a directory; test asynchronous functionality
  within a single test case.</dd>

<h3>JSON</h3>
<dl>
  <dt><a href="/sources/openlaszlo/json">JSON for OpenLaszlo</a></dt>
  <dd>An implementation of <a href="http://json.org">JSON</a> for the OpenLaszlo platform.</dd>
</dl>

<h3>Rails</h3>
<dl>
<dt><a href="http://ropenlaszlo.rubyforge.org">OpenLaszlo Ruby Gem</a></dt>
<dd>A Ruby interface to the OpenLaszlo compiler. This library allows you to compile OpenLaszlo programs from within Ruby, in order to integrate OpenLaszlo development into Rake or Rails applications.</dd>

<dt><a href="http://laszlo-plugin.rubyforge.org">OpenLaszlo Rails Plugin</a></dt>
<dd>The OpenLaszlo Rails plugin makes it easy to use OpenLaszlo client-side applications with Rails. It includes generators and scaffolding for creating OpenLaszlo applications, connecting them to Rails REST controllers, and displaying them within Rails views.</dd>
</dl>


<h3>Older Code</h3>
<p>This was developed in the OpenLaszlo 3.0 timeframe.  Some or all of it might be
included in OpenLaszlo 4.0.</p>

<dl>
  <dt><a href="/archives/2006/02/javascript-beziers">Bezier Library</a></dt>
  <dd>Measure and subdivide beziers, and animate points along a path composed of single or multiple beziers.  <a href="/sources/javascript/bezier.js">bezier.js</a>, <a href="/sources/javascript/path.js">path.js</a>, <a href="/sources/openlaszlo/bezier-demo.swf">demo</a>, <a href="/archives/2006/02/javascript-beziers">blog</a>.  (This library also works in DHTML.)</dd>

<dt><a href="/sources/openlaszlo/drawview-patches.js">LzDrawView patches</a></dt>
<dd>Patches to the OpenLaszlo <tt>LzDrawView</tt> class to make it (more) compatible with the WHATWG <tt>&lt;canvas&gt;</tt> element, including cubic beziers and CSS colors.  This shares the API of the DHTML <a href="/sources/javascript">TextCanvas library</a>.</dd>

<dt><a href="/sources/openlaszlo/textdrawview.lzx">Textdrawview</a></dt>
<dd>An extension to <code>drawview</code> that adds a <tt>drawString</tt> method for labeling graphs.  <a href="/sources/openlaszlo/textdrawview.lzx">Source</a>, <a href="/sources/openlaszlo/textdrawview-example.swf">demo</a>, <a href="/sources/openlaszlo/textdrawview-example.lzx">demo source</a>, <a href="/archives/2006/02/textcanvas">blog</a>.</dd>

<dt><a href="/sources/openlaszlo/simple-logging.js">Simple logging</a></dt>
<dd>A simple logging facility.  This library defines <tt>info</tt>,
<tt>debug</tt>, <tt>warn</tt>, and <tt>error</tt> functions that are
compatible with the logging methods in <a
href="/sources/javascript/docs/readable">readable</a>, <a
href="/sources/javascript/docs/inline-console">inline console</a>, and
<a href="http://www.alistapart.com/articles/jslogging">fvlogger</a>.
This allows libraries that use these functions to be used in both
OpenLaszlo programs and in DHTML.</dd>

<dt><a href="/sources/openlaszlo/laszlo-utils.js">laszlo-utils</a></dt>
<dd>Miscellaneous patches and utilities.  Currently defines <tt>LzKeys.fromEventCode</tt> for converting event codes to string characters.</dd>
</dl>

<?php if($standalone && false) { ?>
	<p>OpenLaszlo sources (which are written in JavaScript, but use the OpenLaszlo APIs) are <a href="/sources/openlaszlo">here</a>.</p>
<?php
  include('../../includes/footer.php');
  } ?>
