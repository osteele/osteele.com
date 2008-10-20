<?php
$file = realpath($_GET['file']);
if (strpos($file, getcwd()) !== 0) {
	die('access denied');
 }

if (!$file || !file_exists($file)) {
	die('File not found: '.$file);
 }

$content = file_get_contents($file);
if ($_GET['section']) {
	//$content = preg_replace('|(// section .*?):.*|', '$1', $content);
	$content = preg_replace('|.*// section '.$_GET['section'].'(?::[^\n]*)?\n|s', '', $content);
	if ($_GET['until']) {
		$content = preg_replace('|// section '.($_GET['until']+1).'(?::[^\n]*)?\n.*|s', '', $content);
	} else
		$content = preg_replace('|// section .*|s', '', $content);
 }
$content = preg_replace('|\n*// section.*\n*|', '[hr]', $content);
$content = preg_replace('|\n*// hr\n*|', '[hr]', $content);

echo preg_replace('|\[hr\]|', '', $content);
?>
