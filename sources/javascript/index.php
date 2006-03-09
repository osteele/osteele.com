<?php 
if (!isset($title)) {
	$standalone = $title = 'JavaScript Libraries';
	include('../../includes/header.php');
?>
<h1><?php echo $title ?></h1>

<div style="float:right"><iframe src="/sources/javascript/bezier-demo.html" width="320" height="300"></iframe><br/>
<iframe src="/sources/javascript/textcanvas-example.html" width="320" height="140"></iframe><br/>
<!--iframe src="/sources/javascript/demos/inline-console.html" width="320" height="140"></iframe><br/-->
</div>
<?php } ?>

<dl>
  <dt>Bezier Library</dt>
  <dd>Measure and subdivide beziers, and animate points along a path composed of single or multiple beziers.  <a href="/sources/javascript/bezier.js">bezier.js</a>, <a href="/sources/javascript/path.js">path.js</a>, <a href="/sources/javascript/bezier-demo.html">demo</a>, <a href="/archives/2006/02/javascript-beziers">blog</a>.  (This library also works in OpenLaszlo.)</dd>
  
  <dt>TextCanvas library</dt>
  <dd><dfn>TextCanvas</dfn> is a wrapper for the WHATWG <tt>canvas</tt> element, that adds a <tt>drawString</tt> method for labeling graphs.  <a href="/sources/javascript/textcanvas.js">Source</a>, <a href="/sources/javascript/docs/textcanvas">docs</a>, <a href="/sources/javascript/textcanvas-example.html">demo</a>.  This shares the API of the OpenLaszlo <a href="/sources/openlaszlo">textdrawview library</a>.)</dd>
  
  <dt>Readable</dt>
  <dd><dfn>Readable</dfn> is a JavaScript library for displaying string representations that are useful for debugging.  For example, <code>{a: 1}</code> displays as <tt>{a: 1}</tt> instead of as <tt>[object Object]</tt>; and <code>[1, null, '', [2, 3]]</code> displays as <tt>[1, null, '', [2, 3]]</tt> instead of as <tt>1,,,2,3</tt>.  <a href="/sources/javascript/readable.js">Source</a>, <a href="/sources/javascript/docs/readable">docs</a>, <a href="/archives/2006/03/readable-javascript-values">blog</a>.</dd>
  
  <dt>Inline Console</dt>
  <dd><dfn>Inline Console</dfn> adds a console with an evaluation field to the web page that includes it.  <a href="/sources/javascript/inline-console.js">Source</a>, <a href="/sources/javascript/docs/inline-console">docs</a>, <a href="/sources/javascript/demos/inline-console.html">demo</a>, <a href="/archives/2006/03/inline-console">blog</a>.</dd>
</dl>

<?php if($standalone) { ?>
	<p>OpenLaszlo libraries are <a href="/sources/openlaszlo">here</a>. These are written in JavaScript too, but use the OpenLaszlo APIs.</p>
<?php
  include('../../includes/footer.php');
  } ?>