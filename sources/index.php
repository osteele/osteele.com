<?php
$title='Oliver Steele : Sources';
include('header.php');
?>

<div class="style">
  .section {gradient-start-color: #eef; border-radius: 10}
</div>

<div style="float:left; padding-left:5em">
<div style="color:gray">Section:</div>
<ul style="list-style:none; padding-left:0; margin-top:0">
<li><a href="#javascript">JavaScript</a></li>
<li><a href="#ruby">Ruby</a></li>
<li><a href="#rails">Rails</a></li>
<li><a href="#python">Python</a></li>
<li><a href="#openlaszlo">OpenLaszlo</a></li>
</div>

<div class="section">
<h2><a id="javascript">JavaScript Libraries</a></h2>
<?php include('javascript/index.php'); ?>
</div>

<div class="section">
<h2><a id="ruby">Ruby Gems</a></h2>
<?php include('ruby.html'); ?>
</div>

<div class="section">
<h2><a id="rails">Rails Plugins</a></h2>
<?php include('rails.html'); ?>
</div>

<div class="section">
<h2><a id="python">Python Libraries</a></h2>
<?php include('python.html'); ?>
</div>

<div class="section">
<h2><a id="openlaszlo">OpenLaszlo Libraries</a></h2>
<?php include('openlaszlo/index.php'); ?>
</div>

<hr/>
<div class="section">
<h2><a id="older">Obsolete</a></h2>
<?php include('older.html'); ?>
</div>

  <div id="footer">
      Copyright 2006-2009 by <a href="/about">Oliver Steele</a>.  All rights reserved.
    </div>
  
<?php include('../includes/footer-banner.php'); ?>
<?php include('../includes/footer.php'); ?>
