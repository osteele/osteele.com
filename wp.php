<?php
$url = 'http://'.$_SERVER['HTTP_HOST'].'/wp/index.php';
$ch = curl_init($url);
curl_exec($ch);
curl_close();
?>
