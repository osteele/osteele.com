<?php

if (! isset($wp_version))
{
	include_once (dirname(dirname(dirname(__FILE__))) . "/wp-config.php");
	global $wp_version;
}

global $wp_13;

if (substr($wp_version, 0, 3) == "1.1" || substr($wp_version, 0, 3) == "1.0")
{
	echo "SPAM KARMA NEEDS AT LEAST WP VERSION 1.2";
	return;

}
elseif (substr($wp_version, 0, 3) == "1.2")
{ // ADD WP 1.2 Compatibility

	global $tablecomments, $tablepostmeta, $tableposts;
	$wp_13 = false;
	$wpdb->comments = $tablecomments;
	$wpdb->postmeta = $tablepostmeta;
	$wpdb->posts = $tableposts;
	if (! function_exists('is_single'))
	{
		function is_single () 
		{
			global $posts;

			return (count($posts) <= 1);
		}
	}
	
	$insert_html = true;

    $comment_hook = false;
}
else
{

    $comment_hook = true;

    if (($pos = strpos($wp_version, "alpha")) !== false)
        if ((int) $wp_version{$pos+6} < 5)
            $comment_hook = false;
	$wp_13 = true;
	$insert_html = false;
}	

if (! function_exists('spamk_update_option'))
{
	if ($wp_13) 
	{
		function spamk_update_option($option, $new_settings) 
		{
			update_option($option, $new_settings);
		}
		function spamk_get_settings($option)
		{
			return get_settings($option);
		}
	}
	else
	{
		function spamk_update_option($option, $new_settings) 
		{
			update_option($option, base64_encode(serialize($new_settings)));
		}
		function spamk_get_settings($option)
		{
			return unserialize(base64_decode(get_settings($option)));
		}
	}
}

?>