<?php
require_once('geshi/geshi.php');

$file = 'samples/'.$_GET['file'];
$section = $_GET['section'];
$font_size = (isset($_GET['size']) ? $_GET['size'] : 24);
$short_name = $_GET['file'];

if (!$file || !file_exists($file)) {
	header("HTTP/1.0 404 Not Found");
	die('Not Found');
 }
if (strpos(realpath($file), getcwd()) !== 0) {
	header("HTTP/1.0 403 Forbidden");
	die('Forbidden');
 }

$title = '';
$content = file_get_contents($file);
if ($section) {
	if (preg_match('|// section '.$section.':(.*)|', $content, $matches))
		$title = $matches[1];
	if (preg_match('|.*// section ([^\n]*).*?// section '.$section.'[:\n]|s', $content, $matches)) {
		$prev_section = $matches[1];
		$prev_title = $prev_section;
		if (preg_match('|(.*):(.*)|', $prev_section, $matches)) {
			$prev_section = $matches[1];
			$prev_title = $matches[2];
		}
	}
	$content = preg_replace('|.*// section '.$section.'(?::[^\n]*)?\n|s', '', $content);
	if ($_GET['until']) {
		$content = preg_replace('|// section '.($_GET['until']+1).'(?::[^\n]*)?\n.*|s', '', $content);
	}
	else {
		if (preg_match('|// section (.*?)(?::(.*))|', $content, $matches)) {
			$next_section = $matches[1];
			$next_title = $matches[2];
		}
		$content = preg_replace('|// section .*|s', '', $content);
	}
 }
$content = preg_replace('|\n*// section.*\n*|', '[hr]', $content);
$content = preg_replace('|\n*// hr\n*|', '[hr]', $content);

if (preg_match('|^((?:///[^\n]*\n)+)(.*)|s', $content, $matches)) {
	$comment = $matches[1];
	$content = $matches[2];
	$comment = preg_replace('|///|', '', $comment);
	$comment = preg_replace('|\\\\br|', '<br/>', $comment);
	$comment = preg_replace('|`(.*?)`|', "<code>\\1</code>", $comment);
	$comment = preg_replace('|\*(\w+?)\*|', "<em>\\1</em>", $comment);
 }

$jsurl = preg_replace('|\.js|', '', $short_name).'-'.$section.'.js';
$basename = preg_replace('|\.js$|', '', $short_name);
$prev_url = $basename.'-'.$prev_section.'.js.html';
$next_url = $basename.'-'.$next_section.'.js.html';

$geshi = new GeSHi('', '');
$geshi->enable_classes();
$geshi->enable_keyword_links(false);
$geshi->set_language('javascript'); 
$geshi->set_overall_style('font-size:'.$font_size.'px');
$geshi->set_source($content);
?>
<html>
<head>
<title><?php echo $title; ?></title>
<style type="text/css"><!--
<?php echo $geshi->get_stylesheet(); ?>
body {margin:0}
#header {width:100%; height:8ex; background:#aaa}
#header a {display:block; position:absolute; opacity:.5; font-size:20px; padding:10px; margin:10px}
#header a:hover {opacity:1; background:#8f8; -moz-border-radius:10px; -webkit-border-radius:10px}
#header .previous {position:absolute; left:0}
#header .next {position:absolute; top:0px; right:10px}
#header .up {position:absolute; left:45%}
#comment {padding:15px; font-style:italic; background:#ccf; margin:0}
#comment em {font-weight:bold}
#output-area {position:fixed; z-layer:5; right:10px; bottom:10px; font-size:20px; background:#88f; padding:10px; -moz-border-radius:10px; display:none; -webkit-border-radius:10px}
#output-area.clickable h3 {cursor:pointer; text-decoration:underline;color:blue}
#output-area.clickable h3:hover {background:#8f8; -moz-border-radius:10px; -webkit-border-radius:10px}
#results .timestamp {color:#888; display:none}
--></style>
<script src="javascripts/jquery-1.2.1.min.js"></script>
<script src="support.js"></script>
</head>
<body>
<div id="header">
	<?php if ($prev_section) { ?>
<a class="previous" href="<?php echo $prev_url; ?>" title="<?php echo $prev_title; ?>">previous</a>
    <?php } ?>
<a class="up" href="." title="Up">up</a>
	<?php if ($next_section) { ?>
<a class="next" href="<?php echo $next_url; ?>" title="<?php echo $next_title; ?>">next</a>
	<?php } ?>
</div>
	<?php if ($comment) { ?>
<p id="comment"><?php echo $comment; ?></p>
						  <?php } ?>
<div id="output-area"><h3>Results</h3><div id="results"></div></div>
<div id="code" style="margin-left: 15px; width: 880px; text-wrap: suppress">
<?php
	echo preg_replace('|<span class="br0">&#91;</span>hr<span class="br0">&#93;</span>|', '<hr/>', $geshi->parse_code());
?>
</div>
<?php include('analytics.php'); ?>
<script src="<?php echo $jsurl;?>"></script>
</body>
</html>
