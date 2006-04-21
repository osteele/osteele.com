<?php
/*
Plugin Name: Code Markup
Plugin URI: http://www.thunderguy.com/semicolon/wordpress/code-markup-wordpress-plugin/
Description: A filter that displays &lt;code&gt; blocks nicely while still allowing formatting.
Version: 1.1.1
Author: Bennett McElwee
Author URI: http://www.thunderguy.com/semicolon/

INSTRUCTIONS

1. Copy this file into the plugins directory in your WordPress installation (wp-content/plugins).
2. Log in to WordPress administration. Go to the Plugins page and Activate this plugin.
3. Go to the Options page and click Writing. Make sure "WordPress should correct invalidly nested XHTML automatically" is NOT checked. (Otherwise it may do funny things to your code listings.)
4. Go to the Users page and click Your Profile. Make sure "Use the visual rich editor when writing" is NOT checked. (The visual rich editor does not like Code Markup.)

Tested with PHP 4.3.8, WordPress 1.5 and 2.


Copyright (C) 2005-06 Bennett McElwee (bennett at thunderguy dotcom)

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


// ===== Add filters ==================================================

// Priority 1 - encode XML before we do anything else
add_filter('the_content', 'tguy_cmu_encode_xml', '1');
// Priority 11 - fix escaped slashes after wpautop() has added them
add_filter('the_content', 'tguy_cmu_fix_quotes', '11');


// ===== Filter functions ==================================================

function tguy_cmu_encode_xml($content) {
/*
	Look for <code> sections in the content and escape certain characters,
	depending on the allow and lang attributes.
	Also remove newlines after <code...> and before </code> so code displays
	nicely in <pre> blocks.
*/
	return preg_replace_callback('!<code([^>]*)>(?:\r\n|\n|\r|)(.*?)(?:\r\n|\n|\r|)</code>!ims', 'tguy_cmu_encode_xml_callback', $content);
}

function tguy_cmu_fix_quotes($content) {
/*
	This fixes two potential WordPress problems: when a post is displayed,
	- Double quotes inside a <pre> are prepended with a backslash.
	- Contents inside a <code> block after the first tag are texturized.
	To fix this, we unescape double quotes inside <pre> blocks, and
	untexturize the contents of <code> blocks.
*/
	$content = preg_replace_callback('!<pre([^>]*)>(.*?)</pre>!ims', 'tguy_cmu_unescape_qq_callback', $content);
	$content = preg_replace_callback('!<code([^>]*)>(.*?)</code>!ims', 'tguy_cmu_untexturize_code_callback', $content);
	return $content;
}


// ===== Callback functions ==================================================

function tguy_cmu_encode_xml_callback($matches) {
/*
	Encode XML in a <code> tag.
*/
	$attributes = $matches[1];
	$escapedContent = $matches[2];
	$attrMatches = array();

	// $allow tells us what HTML special chars are allowed to remain unescaped.
	// This can be set to a space-separated list of tags. Can also be set to the
	// special values all, none or default. If missing, same as default.
	// Also remove the attribute once we've used it.
	$allow = 'default';
	if (0 < preg_match('!^(.*)allow="([^"]*)"(.*)$!i', $attributes, $attrMatches)) {
		$allow = strtolower($attrMatches[2]);
		$attributes = $attrMatches[1] . $attrMatches[3];
	}
	// Depending on language, default handling may change
	if ($allow == 'default') {
		// See if lang is specified; also remove the attribute once we've used it.
		if (0 < preg_match('!^(.*)lang="([^"]*)"(.*)$!i', $attributes, $attrMatches)) {
			$lang = strtolower($attrMatches[2]);
			$attributes = $attrMatches[1] . $attrMatches[3];
			if ($lang == 'html' || $lang == 'xhtml') {
				$allow = 'none';
			}
		}
	}
	if ($allow == 'all') {
		// Nothing to do -- allow anything through.
	} else {
		// Could be default, none, or (possibly blank) space-separated list.
		if ($allow == 'none' || $allow == '') {
			$allowedTags = '';
		} else if ($allow == 'default' || $allow == 'tags') { // 'tags' allowed for backward compatibility
			$allowedTags = 'em|strong|b|i|ins|del|a|span|comment';
		} else {
			$allowedTags = preg_replace('!\s+!', '|', trim($allow));
		}
		// Escape html special chars
		$escapedContent = htmlspecialchars($escapedContent, ENT_NOQUOTES);
		if ($allowedTags != '') {
			// Certain HTML tags are allowed: translate them back.
			$escapedContent = preg_replace_callback('!&lt;/?('.$allowedTags.')( .*?)?&gt;!i',
//			$escapedContent = preg_replace_callback('!&lt;/?('.$allowedTags.').*?&gt;!i',
				'tguy_cmu_unescape_tag', $escapedContent);
			if (false !== strpos($allowedTags, 'comment')) {
				$escapedContent = preg_replace_callback('|&lt;!--.*?--&gt;|i',
					'tguy_cmu_unescape_tag', $escapedContent);
			}
		}
	}
	return "<code$attributes>$escapedContent</code>";
}

function tguy_cmu_unescape_tag($matches) {
	return str_replace(
		array("&gt;", "&lt;", "&quot;", "&amp;"),
		array(">", "<", "\"", "&"),
		$matches[0]);
}

function tguy_cmu_unescape_qq_callback($matches) {
/*
	Unescape double quotes in a <pre> tag.
*/
	return "<pre{$matches[1]}>".str_replace('\"', '"', $matches[2])."</pre>";
}

function tguy_cmu_untexturize_code_callback($matches) {
/*
	Undo the effect of wptexturize() within a <code> element.
	wptexturize() is meant to handle this but is buggy...
	BUGS: Turns --- into -- and `` into "
*/
	$fancy = array('&#215;', '&#8216;', '&#8217;', '&#8242;', '&#8220;', '&#8221;', '&#8243;', '&#8212;', '&#8211;', '&#8230;', '&#8220;');
	$plain = array('x'     ,'\''     , '\''     , '\''     , '"'      , '"'      , '"'      , '--'     , '--'     , '...'    , '``'     );
	return "<code{$matches[1]}>".str_replace($fancy, $plain, $matches[2])."</code>";
}

?>