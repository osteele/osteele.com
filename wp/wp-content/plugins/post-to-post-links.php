<?php
/*
Plugin Name: Easy Post-to-Post Links
Version: 0.9
Plugin URI: http://www.coffee2code.com/wp-plugins/
Author: Scott Reilly
Author URI: http://www.coffee2code.com
Description: Easily reference another post in your blog using a shortcut, either by id or post slug.  The shortcut is replaced with the hyperlinked title of the referenced post

=>> Visit the plugin's homepage for more information and latest updates  <<=


Installation:

1. Download the file http://www.coffee2code.com/wp-plugins/post-to-post-links.zip and unzip it into your 
/wp-content/plugins/ directory.
-OR-
Copy and paste the the code ( http://www.coffee2code.com/wp-plugins/post-to-post-links.phps ) into a file called 
post-to-post-links.php, and put that file into your /wp-content/plugins/ directory.
2. Optional: Change configuration options in the file to your liking
3. Activate the plugin from your WordPress admin 'Plugins' page.


Usage:

* When writing your posts, you can refer to other posts either by ID, like so: <!--post="20"-->, or by the post slug/name,
like so: <!--post="hello-world"-->.

* A quicktag button labeled "post link" is created by default, which will automatically insert <!--post=""--> into the post
textarea.  Insert the ID/post slug between the double-quotes.

*/

/*
Copyright (c) 2005 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

if ( ! isset($wpdb->posts) )		// For WP 1.2 compatibility
	$wpdb->posts = $tableposts;

$make_quicktag_button = 1;		// Set this to 0 if you don't want the quicktag button created


/* This is a helper function. */
function c2c_post_to_post_link_handler( $post_id_or_name ) {
	global $wpdb;

	// ==== USER CONFIGURABLE OPTIONS ====
	$before_text = "\"";	// text to appear before title of referenced post
	$after_text = "\"";	// text to appear after title of referenced post
	// ==== END CONFIGURABLE OPTIONS =====
	
	if ( empty($post_id_or_name) ) return '';
	$field = (is_numeric($post_id_or_name)) ? 'ID' : 'post_name';
	$post = $wpdb->get_row("SELECT ID, post_title FROM $wpdb->posts WHERE $field = '$post_id_or_name'");
	if ( empty($post->post_title) ) return '';
	return $before_text . '<a href="' . get_permalink($post->ID) . '">' . apply_filters('the_title', $post->post_title) . '</a>' . $after_text;
} //end c2c_post_to_post_link_handler

function c2c_post_to_post_link( $text ) {
	return preg_replace(
		"#(<!--[ ]*post[ ]*=[ ]*['\"]?([^'\"\- ]+)['\"]?[ ]*-->)#ismeU",
		"c2c_post_to_post_link_handler(\"$2\")",
		$text
	);
} //end c2c_post_to_post_links

add_filter('the_content', 'c2c_post_to_post_link', 10);
add_filter('the_excerpt', 'c2c_post_to_post_link', 10);

// Comment out this next line if you don't want the quicktags button to be created
if ( $make_quicktag_button )
	add_filter('admin_footer', 'c2c_add_postlink_button');

function c2c_add_postlink_button() {
        if(strpos($_SERVER['REQUEST_URI'], 'post.php')) {
?>
<script language="JavaScript" type="text/javascript"><!--
function js_c2c_add_postlink_button () {
	var edspell = document.getElementById("ed_spell");
	if (edspell == null) return;
	var edpostlink = document.getElementById("ed_postlink");
	if (edpostlink != null) return;
	edButtons[edButtons.length] =
	new edButton('ed_postlink'
	,'post link'
	,'<!--post=""-->'
	,''
	,''
	);
	n = edButtons.length - 1;
	edShowButton(edButtons[n], n);
	var newbutton = document.getElementById(edButtons[n].id);
	edspell.parentNode.insertBefore(newbutton, edspell);
	return;
}        
js_c2c_add_postlink_button();

//--></script>
<?php
	}
}

?>