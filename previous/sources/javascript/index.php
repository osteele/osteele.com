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

<div style="font-size:small">Unless otherwise noted, these have been tested in Firefox 1.5, Safari 2.0, and IE 6.</div>

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

  <dt><a href="/sources/javascript/functional/">Functional</a></dt>
  <dd><dfn>Functional</dfn> is a library for functional programming in
  JavaScript.  It defines the standard higher-order functions such as
  <code>map</code>, <code>filter</code>, and <code>reduce</code>.  It
  defines functions such as <code>curry</code>, <code>rcurry</code>,
  and <code>partial</code> for partial function application; and
  <code>compose</code>, <code>guard</code>, and <code>until</code> for
  <a
  href="http://en.wikipedia.org/wiki/Function-level_programming">function-level
  programming</a>.  It also defines <dfn>string lambdas</dfn>, which
  allow strings such as <code>'x -> x+1'</code>, <code>'x+1'</code>,
  or <code>'+1'</code> as synonyms for the more verbose
  <code>function(x) {return x+1}</code>.
  <a href="/sources/javascript/functional/functional.js">Source</a>,
  <a href="http://osteele.com/archives/2007/07/functional-javascript">blog</a>.
  <a href="/sources/javascript/functional/">Demo &amp; docs</a>.
  </dd>
</dl>
  
<h3>Concurrent JavaScript</h3>
<dl>
  <dt><a href="/sources/javascript/sequentially/">Sequentially</a></dt>
  <dd><dfn>Sequentially</dfn> is a library of temporal and frequency
  adverbs for JavaScript.  It provides methods to queue a function for
  deferred or periodic execution, and to throttle the rate or number
  of times that a function can be called.  You could think of it as a
  kind of memoization, where instead of caching the result it modifies
  <em>when</em> and <em>whether</em> a function is called.
  </dd>
  
  <dt><a href="/sources/javascript/concurrent/">Concurrent</a></dt>
  <dd>Currently just a partial port of Haskell's <dfn><a
  href="http://www.haskell.org/ghc/docs/latest/html/libraries/base-3.0.0.0/Control-Concurrent-MVar.html">MVars</a></dfn>,
  that can each be used to coordinate data flow among periodic
  functions, and asynchronous callback.</dd>
</dl>
  
<h3>Metaobject Programming</h3>
<dl>
  <dt><a href="http://github.com/osteele/mop-js">MOP</a></dt>
  <dd><dfn>MOP</dfn> is a short metaobject programming toolkit.  It
  currently contains functions that temporarily replace the methods on
  an object or class, or that capture calls to a set of methods and
  replay them later (for asynchronous AJAX).  It also defines a
  function that defines delegating methods.</dd>
  
  <dt><a href="http://github.com/osteele/fluently">Fluently</a></dt>
  <dd>A construction kit for chained method calls (<a href="http://martinfowler.com/bliki/FluentInterface.html">fluent interfaces</a>), for JavaScript.</dd>
</dl>

<h3>jQuery Plugin</h3>
<dl>
<dt><a href="/archives/2008/05/jquery-profile-plugin">jQuery Profile Plugin</a></dt>
<dd>Stick this in your page, call <code>$.profile.start()</code> to start profiling calls to <code>$(selector)</code>, and then <code>$.profile.done()</code> to stop profiling.  It will display statistics for selector calls, by selector. , <a href="http://github.com/osteele/jquery-profile">Source</a>, <a href="/archives/2008/05/jquery-profile-plugin">blog</a>.</dd>
</dl>

<h3>Graphics</h3>
<dl>
  <dt><a href="/sources/javascript/docs/gradients">JavaScript Gradient Roundrects</a></dt>
  <dd>Draws gradient roundrects without images.  Gradients can be applied procedurally or, via the <a href="/sources/javascript/docs/divstyle">divstyle library</a>, through CSS.  This uses the WHATWG <code>canvas</code> element if it's available, and a stack of <code>div</code> elements otherwise.  <a href="/sources/javascript/gradients.js">Source</a>, <a href="/sources/javascript/demos/gradients.html">demo</a>, <a href="/sources/javascript/docs/gradients">docs</a>.</dd>
  
  <dt><a href="/sources/javascript/docs/bezier">Bezier Library</a></dt>
  <dd>Measure and subdivide beziers, and animate points along a path composed of single or multiple beziers.  <a href="/sources/javascript/bezier.js">bezier.js</a>, <a href="/sources/javascript/path.js">path.js</a>, <a href="/sources/javascript/bezier-demo.html">demo</a>, <a href="/archives/2006/02/javascript-beziers">blog</a>.  (This library also works in OpenLaszlo.)</dd>
  
  <dt><a href="/sources/javascript/docs/textcanvas">TextCanvas library</a></dt>
  <dd><tt>TextCanvas</tt> is a wrapper for the WHATWG <tt>canvas</tt> element, that adds a <tt>drawString()</tt> method for labeling graphs.  <a href="/sources/javascript/textcanvas.js">Source</a>, <a href="/sources/javascript/docs/textcanvas">docs</a>, <a href="/sources/javascript/textcanvas-example.html">demo</a>, <a href="/archives/2006/02/textcanvas">blog</a>.  This shares the API of the OpenLaszlo <a href="/sources/openlaszlo">textdrawview library</a>.)</dd>
  
  <dt><a href="http://github.com/osteele/cfdg-js">CFDF-JS</a>
  <dd>A JavaScript implementation of Chris Coyne's <a
  href="http://www.chriscoyne.com/cfdg/">Context Free Design
  Grammar</a>.  This one's in progress; I'm slowly plugging away at
  it.</dd>
</dl>

<?php if($standalone) { ?>
	<p>OpenLaszlo libraries are <a href="/sources/openlaszlo">here</a>. These are written in JavaScript too, but use the OpenLaszlo APIs.</p>
<?php
  include('../../includes/footer.php');
  } ?>