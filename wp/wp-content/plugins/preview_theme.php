<?php

/*
Plugin Name:  Preview Theme
Plugin URI: http://boren.nu/
Description: Preview installed themes.
Author: Ryan Boren
Version: 0.9
Author URI: http://boren.nu/
*/

/*

By default, if you are logged in as a level 8 or above user, you can specify
"preview_theme=Theme Name" in the URI query string to preview an installed
theme.  Regular blog readers will not be affected by this setting.  They will
still see the theme selected in Options->Presentation or the Theme Switcher.

Example:

http://blog.wp/index.php?preview_theme=WordPress%20Default

*/

//  Set this to the user level required to preview themes.
$preview_theme_user_level = 8;
// Set this to the name of the GET variable you want to use.
$preview_theme_query_arg = 'preview_theme';

function preview_theme_stylesheet($stylesheet) {
    global $user_level, $preview_theme_user_level, $preview_theme_query_arg;

    get_currentuserinfo();
  
    if ($user_level  < $preview_theme_user_level) {
        return $stylesheet;
    }

    $theme = $_GET[$preview_theme_query_arg];

    if (empty($theme)) {
        return $stylesheet;
    }
    
    $theme = get_theme($theme);
    
    if (empty($theme)) {
        return $stylesheet;
    }
    
    return $theme['Stylesheet'];
}

function preview_theme_template($template) {
    global $user_level, $preview_theme_user_level, $preview_theme_query_arg;

    get_currentuserinfo();
  
    if ($user_level  < $preview_theme_user_level) {
        return $template;
    }

    $theme = $_GET[$preview_theme_query_arg];

    if (empty($theme)) {
        return $template;
    }
    
    $theme = get_theme($theme);
    
    if (empty($theme)) {
        return $template;
    }
    
    return $theme['Template'];
}

add_filter('stylesheet', 'preview_theme_stylesheet');
add_filter('template', 'preview_theme_template');
?>
