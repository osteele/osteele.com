<?php
/*
Plugin Name: Object Tags
Plugin URI: http://osteele.com/sources/wordpress/object-tags
Description: Embed Quicktime via &lt;wp:quicktime src="<var>filename</var>" width="<var>width</var>" height="<var>height</var>"/>
Version: 0.1
Author: Oliver Steele
Author URI: http://osteele.com
*/
#/

function extract_attribute($tag, $aname, $default=null) {
	preg_match('|\s'.$aname.'\s*=\s*"([^"]*)"|', $tag, $match);
	if ($match) return $match[1];
	return $default;
}

function flash_encode_tag($text) {
	$text = $text[0];
	$src = extract_attribute($text, 'src');
	$width = extract_attribute($text, 'width');
	$height = extract_attribute($text, 'height');
	
	$classid = "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000";
	$codebase = "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0";
	
	$embed = '<embed src="'.$src.'" width="'.$width.'" height="'.$height.'" quality="high" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">';
	$object = '<object width="'.$width.'" height="'.$height.'" classid="'.$classid.'" codebase="'.$codebase.'">';
	$object .= '<param name="movie" value="'.$src.'">';
	$object .= '<param name="quality" value="high">';
	$object .= '<param name="controller" value="'.$controller.'">'.$embed.'</object>';
	return $object;
}

function quicktime_encode_tag($text) {
	$text = $text[0];
	$src = extract_attribute($text, 'src');
	$width = extract_attribute($text, 'width');
	$height = extract_attribute($text, 'height');
	$controller = true;
	$autoplay = false;
	if ($controller) $height += 24;
	
	// autoplay="'.$autoplay.'" 
	$embed = '<embed src="'.$src.'" width="'.$width.'" height="'.$height.'" controller="'.$controller.'" pluginspage="http://www.apple.com/quicktime/download/">';
	$object = '<object width="'.$width.'" height="'.$height.'" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab">';
	$object .= '<param name="src" value="'.$src.'">';
	#$object .= '<param name="autoplay" value="'.$autoplay.'">';
	$object .= '<param name="controller" value="'.$controller.'">'.$embed.'</object>';
	return $object;
}

function object_tags_encode_the_content($text) {
    $text = preg_replace_callback('|<wp:quicktime\s+[^>]*/?>|',
								  'quicktime_encode_tag', $text);
    return preg_replace_callback('|<wp:flash\s+[^>]*/?>|',
								 'flash_encode_tag', $text);
}

# Turn on the flashifying process.
add_filter('the_content', 'object_tags_encode_the_content');
?>