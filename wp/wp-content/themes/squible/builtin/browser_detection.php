<?php
function find($component) {
	global $HTTP_USER_AGENT;
	$result = stristr($HTTP_USER_AGENT,$component);
	return $result;
} 
?>
