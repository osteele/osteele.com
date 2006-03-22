<?php 
$_SERVER['REQUEST_URI'] = preg_replace('|^/blog(.*)|', '\1', $_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI'] = preg_replace('|^/fb-(feed.*)|', '\1', $_SERVER['REQUEST_URI']);
//die($_SERVER['REQUEST_URI']);

/* Short and sweet */
define('WP_USE_THEMES', true);
require('./wp-blog-header.php');
?>