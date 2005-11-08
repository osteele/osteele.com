<?php
/*
Plugin Name: LiveCalendar
Plugin URI: http://www.jonabad.com/2005/01/08/livecalendar-10/
Description: Calendar widget energized with the xmlhttprequest magic. Allows calendar navigation without refreshing the entire page.
Author: Jon Abad
Author URI: http://www.jonabad.com
Version: 1.3
*/ 
//Original implementation by Kae Verens, polishing and plugin packaging by Jon Abad

//define function to spit out include in head
function livecal_head()
{
	echo"
		<script type='text/javascript' src='" . get_settings("siteurl") . "/wp-content/livecalendar/xmlhttprequest.js'></script>
		<script type='text/javascript' src='" . get_settings("siteurl") . "/wp-content/livecalendar/kcalendarscript.php'></script>
	";
}

//add action when the head is written
add_action('wp_head', 'livecal_head');

?>