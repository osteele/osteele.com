<?php 
if (!isset($title)) {
	$standalone = $title = 'JavaScript sources';
	include('../../includes/header.php');
?>
<h1><?php echo $title ?></h1>
<?php } ?>

<dl>
  <dt>Bezier Library</dt>
  <dd>Measure and subdivide beziers, and animate points along a path composed of single or multiple beziers.  <a href="/sources/javascript/bezier.js">bezier.js</a>, <a href="/sources/javascript/path.js">path.js</a>, <a href="/sources/javascript/bezier-demo.html">demo</a>, <a href="/archives/2006/02/javascript-beziers">blog</a>.  (There's also an <a href="/sources/openlaszlo">OpenLaszlo version</a>, with a demo <a href="/sources/javascript/bezier-demo.swf">here</a>.)</dd>
  
  <dt>TextCanvas library</dt>
  <dd>TextCanvas is a wrapper for the WHATWG <tt>canvas</tt> element, that adds a <tt>drawString</tt> method for labeling graphs.  <a href="/sources/javascript/textcanvas.js">Source</a>, <a href="/sources/javascript/docs/textcanvas">docs</a>, <a href="/sources/javascript/textcanvas-example.html">demo</a>.  (There's also an <a href="/sources/openlaszlo">OpenLaszlo version</a>, with a demo <a href="/sources/openlaszlo/textdrawview-example.swf">here</a>.)</dd>
  
  <dt>Readable</dt>
  <dd><dfn>Readable</dfn> is a JavaScript library for printing string representations that are useful for debugging.  For example, <code>{a: 1}</code> prints as its <tt>{a: 1}</tt> instead of than as <tt>[object Object]</tt>, and <code>[1, null, '', [2, 3]]</code> prints as <tt>[1, null, '', [2, 3]]</tt> source representation instead of <tt>1,,,2,3</tt>.  <a href="/sources/javascript/readable.js">Source</a>, <a href="/sources/javascript/docs/readable">docs</a>, <a href="/archives/2006/03/readable-javascript-values">blog</a>.</dd>
  
  <dt>Inline Console</dt>
  <dd><dfn>Inline Console</dfn> adds a console with an evaluation field to the web page that includes it.  <a href="/sources/javascript/inline-console.js">Source</a>, <a href="/sources/javascript/docs/inline-console">docs</a>, <a href="/sources/javascript/demos/inline-console.html">demo</a>, <a href="/archives/2006/03/inline-console">blog</a>.</dd>
</dl>

<?php if($standalone) { ?>
	<p>OpenLaszlo libraries are <a href="/sources/openlaszlo">here</a>. These are written in JavaScript too, but use the OpenLaszlo APIs.</p>
<?php
  include('../../includes/footer.php');
  } ?>