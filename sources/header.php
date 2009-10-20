<?php
$content_for_header = <<<END
    <script type="text/javascript" src="/js/behaviour.js"></script>
    <script type="text/javascript" src="/js/divstyle.js"></script>
    <script type="text/javascript" src="/js/gradients.js"></script>
    <link href='/stylesheets/banner.css' rel='stylesheet' type='text/css' />
    <link href='/stylesheets/banner.iphone.css' rel='stylesheet' type='text/css' media="only screen and (max-device-width: 480px)" />
    <style type="text/css">
	  .style {display: none}
	  .section, h1, .nav, #footer {width: 728px; margin-left: auto; margin-right: auto}
      #footer {border-top: 1px solid}
	</style>
END;
include('../includes/header.php');
?>
<h1><?php echo $title; ?></h1>
