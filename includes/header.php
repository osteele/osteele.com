<?php header("Content-type: text/html; charset=utf-8"); ?>
<?php if (!isset($nodtd)) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		  <?php } ?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title><?php echo $title; ?></title>
  <link rel="shortcut icon" href="/favicon.ico" />
  <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://osteele.com/feed/" />
  <link rel="alternate" type="text/xml" title="RSS .92" href="http://osteele.com/feed/rss/" />
  <link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="http://osteele.com/feed/atom/" />
	  <?php if (!isset($nostyle)) { ?>
  <link rel="stylesheet" type="text/css" href="style.css" />
	   <?php } ?>
  <?php echo $content_for_header ?>
</head>
<body>
