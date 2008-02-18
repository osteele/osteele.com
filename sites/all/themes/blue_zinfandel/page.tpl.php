<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language; ?>">
<head>
  <title><?php print $head_title; ?></title>
  <?php print $head; ?>
  <?php print $styles; ?>
  <?php print $scripts; ?>
</head>
<body <?php print theme("onload_attribute"); ?>>

<div id="header">
  <a href="<?php print url(); ?>"><?php print $site_name; ?></a><br />
  <?php print $site_slogan; ?>
</div>

<div id="navbar">
  <?php print theme('links', $primary_links); ?>
</div>

<div id="wrap">
  <div id="content">

<!-- begin l_sidebar -->

	  <div id="l_sidebar">
      <?php print $sidebar_left ; ?>
    </div>

<!-- end l_sidebar -->

    <div id="contentmiddle">
      <?php print $messages; ?>
      <?php print $help; ?>
      <?php print $tabs; ?>
      <?php print $content; ?>
    </div>

<!-- The main column ends  -->

<!-- begin footer -->

    <div style="clear: both;"></div>
    <div style="clear: both;"></div>

    <div id="footer">
      <?php print $footer_message; ?>
    </div>
  </div>
</div>

</body>
</html>