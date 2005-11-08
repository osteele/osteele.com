<?php
/*
Plugin Name: Evermore
Plugin URI: http://www.thunderguy.com/semicolon/wordpress/evermore-wordpress-plugin/
Description: Abbreviate all posts when viewed on multiple post pages. This makes all posts behave as if there is a "&lt;!--more--&gt;" after the first paragraph.
Version: 1.0
Author: Bennett McElwee
Author URI: http://www.thunderguy.com/semicolon/

INSTRUCTIONS

1. Copy this file into the plugins directory in your WordPress installation (wp-content/plugins).
2. Log in to WordPress administration. Go to the Plugins page and Activate this plugin.

Evermore automatically abbreviates all posts when they appear on a multiple-post page such as the main blog page. It has the same effect as putting  <!--more--> after the first paragraph of every post. All formatting and HTML tags are preserved in the abbreviated post.

If the post already has a <!--more--> in it, then this plugin does nothing to it and the existing <!--more--> will behave as usual.

If you want to disable the plugin for any specific post, then include the codeword <!--nevermore--> in the post. This won't show up in the post, but it will prevent the post from being abbreviated by Evermore. 

Tested with PHP 4.3.8, WordPress 1.5.


Copyright (C) 2005 Bennett McElwee (bennett at thunderguy dotcom)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
The license is also available at http://www.gnu.org/copyleft/gpl.html
*/

// Add the "more" link immediately after reading the post from the database
add_filter('the_posts', 'tguy_em_addmoreall');

function tguy_em_addmoreall($posts) {
/*	Add a "more" link immediately after reading posts from the database.
*/
	$count = count($posts);
	for ($i = 0; $i < $count; ++$i) {
		$posts[$i]->post_content = tguy_em_addmore($posts[$i]->post_content);
	}
	return $posts;
}

function tguy_em_addmore($post_content) {
/*	Add a "more" comment between the first and second paragraphs, unless
	there is already a "<!--more-->" (we don't add an extra one) or
	a "<!--nevermore-->" (user has disabled evermore for this post).
*/
	// Only continue if content has no "more" and no "nevermore"
	if ((false === strpos($post_content, '<!--more-->'))
	&&  (false === strpos($post_content, '<!--nevermore-->'))) {
		// Get the first paragraph including all surrounding whitespace.
		// First paragraph is the first closing block tag or the first
		// double newline, whichever comes first.
		// Important to add 2 newlines after the more, to avoid WP adding
		// a <br> after the more which leaves a spurious blank line.
		 if (preg_match('!^(\s*.*?(?:</(?:p|pre|blockquote|div|ol|ul)>|(?:\r\n|\r|\n){2})\s*)\S!', $post_content, $matches)) {
			$firstPara = $matches[1];
			return $firstPara . "<!--more-->\n\n" . substr($post_content, strlen($firstPara));
		}
	}
	return $post_content;
}

?>