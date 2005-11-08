<?php
require_once("../../wp-config.php");

if (! mysql_connect(DB_HOST, DB_USER, DB_PASSWORD))
	die("Can't connect.");

if (! mysql_select_db(DB_NAME))
	die("Can't select DB.");

if ($res = mysql_query("SELECT * FROM `blacklist` WHERE `regex_type` != 'auto-url' AND `regex_type` != 'auto-ip' AND `regex_type` != 'option'"))
{
	while($row = mysql_fetch_assoc($res))
	{
		echo $row['regex'] . " #:" . $row['regex_type'] . ":\n";
	}
}
else
	echo mysql_error();
?>