<?php 
if (!isset($title)) {
	$standalone = $title = 'JavaScript sources';
	include('../../includes/header.php');
?>
<h1><?php echo $title ?></h1>
<?php } ?>

<dl>
<dt><a href="/archives/2006/02/javascript-beziers">Bezier Library</a></dt>
	  <dd><a href="/archives/2006/02/javascript-beziers">This page</a> describes the bezier library and lists the files.  Demos are <a href="/sources/javascript/bezier-demo.swf">here</a> (OpenLaszlo) and <a href="/sources/javascript/bezier-demo.html">here</a> (DHTML).</dd>
<dt>
</dl>

<?php if($standalone) { ?>
	<p>OpenLaszlo sources (which are written in JavaScript, but use the OpenLaszlo APIs) are <a href="/sources/openlaszlo">here</a>.</p>
<?php
  include('../../includes/footer.php');
  } ?>