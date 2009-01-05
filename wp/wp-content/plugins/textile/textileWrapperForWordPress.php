<?php
/*
Plugin Name: TextileWrapper
Version: 2.8
Plugin URI: http://www.huddledmasses.org/category/development/wordpress/textile/
Description: This is a simple wrapper for <a href="http://textism.com/?wp">Dean Allen's</a> <a href="http://www.textism.com/tools/textile/">Textile Markup</a>. This version matches the version included with <a href="http://textpattern.com/">TextPattern 4.3</a>. If you use this plugin you should disable other markup plugins like Textile 1, Textile 2, and Markdown, as they don't play well together.
Author: Joel Bennett
Author URI: http://www.huddledmasses.org/

NOTE: I had to significantly modify the glyphs() function to get it to cooperate with beautifier
      Perhaps I need to go a step further, as brad choate did, and have textile itself call
      beautifier, if it can find it...  (in the meantime, search for "Joel" to find my mods)
      -- Joel "Jaykul" Bennett
*/

require_once( "classTextile.php" );

function textile( $string ) {
	$textile = new Textile;
	return $textile->TextileThis($string, false, false, false, false);//$string, $lite='', $encode='', $noimage='', $strict=''
}


// WordPress users.  If you want to change what is textiled, do so here!
// Default filters we don't want because of Textile 2
remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');
remove_filter('comment_text', 'wpautop');

remove_filter('the_content', 'wptexturize');
remove_filter('the_excerpt', 'wptexturize');
remove_filter('comment_text', 'wptexturize');

// Comment out these two lines to allow html in your comments
// You can have HTML *with* textile or HTML without, but I recommend using *just* Textile
define('CUSTOM_TAGS', true);
$allowedtags = array();

add_filter('the_content', 'textile', 6);
add_filter('the_excerpt', 'textile', 6);
add_filter('comment_text', 'textile', 6);
// add_filter('the_excerpt_rss', 'textile', 6);

?>
