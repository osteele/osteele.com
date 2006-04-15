<?

require('../../../wp-blog-header.php');

global $user_level;

if($_GET['mode'] == "setNew")
{
	if($user_level > 8)
	{
		saveColors('setNew');
		wp_redirect($_SERVER['HTTP_REFERER']);
	}
	else
	{
		wp_redirect($_SERVER['HTTP_REFERER']);
	}
}
else if($_GET['mode'] == "default")
{
	if($user_level > 8)
	{
		saveColors('default');
		wp_redirect($_SERVER['HTTP_REFERER']);
	}
	else
	{
		wp_redirect($_SERVER['HTTP_REFERER']);
	}
}
else
{
	wp_redirect($_SERVER['HTTP_REFERER']);
}

?>