<?php
$text0 = $_GET['text0'];
$text1 = $_GET['text1'];

$t0 = escapeshellcmd($text0);
$t1 = escapeshellcmd($text1);

$ruby = "/usr/local/bin/ruby";
header('Content-Type: text/plain');
passthru("{$ruby} suggest.rb '{$t0}' '{$t1}'");
?>
