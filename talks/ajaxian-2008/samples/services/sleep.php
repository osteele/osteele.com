<?php
$time = $_GET['time'];
if ($time == '') $time = 1;
sleep($time);
echo strftime('%H:%M:%S', $_SERVER['REQUEST_TIME']);
?>
