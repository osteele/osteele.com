<?php
$content_for_header = <<<END
    <script type="text/javascript" src="/javascripts/behaviour.js"></script>
    <script type="text/javascript" src="/javascripts/divstyle.js"></script>
    <script type="text/javascript" src="/javascripts/gradients.js"></script>
    <link href='/stylesheets/banner.css' rel='stylesheet' type='text/css' />
    <style type="text/css">
	  .style {display: none}
	  .section, h1, .nav, #footer {width: 728px; margin-left: auto; margin-right: auto}
      #footer {border-top: 1px solid}
	</style>
END;
include('../includes/header.php');
?>

	<h1><?php echo $title; ?></h1>

<ul class="nav">
<li><a href="/">Home</a></li>
<li><a href="/about/">About</a></li>
<li><a href="/archives/">Archives</a></li>
<li><b>Sources</b></li>
<li><a href="/projects/">Projects</a></li>
<li><a href="/blog/">Blog</a></li>
</ul>
