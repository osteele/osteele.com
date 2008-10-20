<?php 

function preprocess_redirected_uri($uri) {
	$uri = preg_replace('|^/blog(.*)|', '\1', $uri);
	$uri = preg_replace('|^/fb-(feed.*)|', '\1', $uri);
	$uri = preg_replace('|^$|', '/', $uri);
	return $uri;
  }

$_SERVER['REQUEST_URI'] = preprocess_redirected_uri($_SERVER['REQUEST_URI']);
include('./index.php');
?>
