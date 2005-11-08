<?php
/*
Plugin Name: Honeypot
Plugin URI: http://lordrich.com
Description: displays hidden links in every post to your honeypot.
Version: 0.1
Author URI: http://lordrich.com/
*/
function config_honey_pot() {
	if (function_exists('add_options_page')) {
		add_submenu_page('plugins.php', 'Project Honeypot', 'Project Honeypot', 7, basename(__FILE__), 'my_options_subpanel');
//		add_options_page('Project Honeypot', 'Project Honeypot', 1, basename(__FILE__), 'my_options_subpanel()')

		}
	}

function my_options_subpanel() {
echo '
<form name="example" action="' . $_SERVER[PHP_SELF] . '" method="GET">
Honeypot Location: <input type="text" name="hp" value="'.get_option('script-location').'" /><br />
Honeypot Text: <input type="text" name="hp-location" value="'.get_option('script-text').'" /><br />
<input type="hidden" name="page" value="project-honeypot.php" />
<input type="hidden" name="submitted" />
<input type="submit" value="Go!" />
<form>
';

if(isset($_REQUEST['submitted'])) {
	update_option('script-location', $_REQUEST['hp']);
	update_option('script-text', $_REQUEST['hp-location']);
	echo('options saved');
	}

}

function linkhoneypot() {
$hp = get_option('script-location');
$hptext = get_option('script-text');

$links = array(1 => '<a href="'.$hp.'" style="display:none;">'.$hptext.'</a>',
2 => '<a href="'.$hp.'"><!-- '.$hptext.' --></a>',
3 => '<a href="'.$hp.'"></a>',
4 => '<!-- <a href="'.$hp.'">'.$hptext.'</a> -->'
);
echo $links[array_rand($links)];

}

add_option('script-location', '', 'Location of your honeypot');
add_option('script-text', '', 'Text to use in the links to the honeypot');
add_action('admin_menu', 'config_honey_pot');
?>
