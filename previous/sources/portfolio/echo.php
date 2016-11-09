<?php
$file = $_GET['source'];
$formatted = 'formatted/'.$file;

if (file_exists($formatted) && $_GET['format']!='plain') {
	$content = file_get_contents($formatted);
	$content = preg_replace('|</h1>|i', ' <span style="font-size: large">(<a href="?format=plain" style="font-size: large">plain text</a><span style="font-size: large">)</span>\0', $content, 1);
	echo $content;
 } else {
	header("Content-type: text/plain; charset=utf-8");
	echo(file_get_contents($file));
 }
?>
 