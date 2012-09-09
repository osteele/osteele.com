<?php

$base = substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'wp-content'));

require($base . 'wp-load.php');

$zemanta->proxy();

?>
