<?php
/*
Plugin Name: Live Post Preview
Plugin URI: http://dev.wp-plugins.org/wiki/LiveCommentPreview
Description: Shows your post as you write it in real time. Based on <a href="http://dev.wp-plugins.org/wiki/LiveCommentPreview">Live Comment Preview</a> by <a href="http://thecodepro.com/">Jeff Minard</a> &amp; <a href="http://www.softius.net/">Iacovos Constantinou</a>.
Author: <a href="http://www.slaad.com">Lucas Richardson</a>
Version: 1.1
*/ 

//NOTE: v1.1 is based on v1.6 of LCP
//Many, many thanks to Jeff Minard (http://thecodepro.com/)& Iacovos Constantinou (http://www.softius.net/) as most of this code was written by them.

//Last Modified 8-04-2005

// Customize this string if you want to modify the preview output 
// %2 - post text
$previewFormat         = '<fieldset id="previewdiv"><p><strong><legend>Preview:</legend></strong></p><p><div>%2</div></p></fieldset>';

// If you have changed the ID's on your form field elements
// You should make them match here
$postTextID = 'content';


// You shouldn't need to edit anything else.

$livePreviewDivAdded == false;

if( stristr($_SERVER['REQUEST_URI'], 'postPreview.js') ) {
	header('Content-type: text/javascript');
	?>

function wptexturize(text) {
	text 		= ' '+text+' ';
	var textarr = text.split(/(<[^>]+?>)/g)
	var istop	= textarr.length;
	var next 	= true;
	var output 	= '';
	for ( var i=0; i<istop; i++ ) {
		var curl = textarr[i];			
		if ( curl.substr(0,1) != '<' && next == true ) {
			curl = curl.replace(/---/g, '&#8212;');
			curl = curl.replace(/--/g, '&#8211;');			
			curl = curl.replace(/\.{3}/g, '&#8230;');			
			curl = curl.replace(/``/g, '&#8220;');						
			
			curl = curl.replace(/'s/g, '&#8217;s');
			curl = curl.replace(/'(\d\d(?:&#8217;|')?s)/g, '&#8217;$1');
			curl = curl.replace(/([\s"])'/g, '$1&#8216;');			
			curl = curl.replace(/(\d+)"/g, '$1&Prime;');						
			curl = curl.replace(/(\d+)'/g, '$1&prime;');									
			curl = curl.replace(/([^\s])'([^'\s])/g, '$1&#8217;$2');	
			curl = curl.replace(/(\s)"([^\s])/g, '$1&#8220;$2');				
			curl = curl.replace(/"(\s)/g, '&#8221;$1');						
			curl = curl.replace(/'(\s|.)/g, '&#8217;$1');	
			curl = curl.replace(/\(tm\)/ig, '&#8482;');	
			curl = curl.replace(/\(c\)/ig, '&#169;');
			curl = curl.replace(/\(r\)/ig, '&#174;');
			curl = curl.replace(/''/g, '&#8221;');	
			
			curl = curl.replace(/(\d+)x(\d+)/g, '$1&#215;$2');	
		} else if ( curl.substr(0,5) == '<code' ) {
			next = false;
		} else {
			next = true;
		}
		output += curl; 
	}
	return output.substr(1, output.length-2);
}

function wpautop(pee) {
	pee = pee + '\n\n';
	
	pee = pee.replace(/(<blockquote[^>]*>)/g, '\n$1');
	pee = pee.replace(/(<\/blockquote[^>]*>)/g, '$1\n');
		
	pee = pee.replace(/\r\n/g, '\n');
	pee = pee.replace(/\r/g, '\n');
	pee = pee.replace(/\n\n+/g, '\n\n');
	pee = pee.replace(/\n?(.+?)(?:\n\s*\n)/g, '<p>$1</p>');
	pee = pee.replace(/<p>\s*?<\/p>/g, '');

	pee = pee.replace(/<p>\s*(<\/?blockquote[^>]*>)\s*<\/p>/g, '$1');
	pee = pee.replace(/<p><blockquote([^>]*)>/ig, '<blockquote$1><p>');
	pee = pee.replace(/<\/blockquote><\/p>/ig, '<p></blockquote>');	
	pee = pee.replace(/<p>\s*<blockquote([^>]*)>/ig, '<blockquote$1>');
	pee = pee.replace(/<\/blockquote>\s*<\/p>/ig, '</blockquote>');			
	
	pee = pee.replace(/\s*\n\s*/g, '<br />');
	return pee;
}

function updateLivePreview() {
	
	var postArea = document.getElementById('<?php echo $postTextID ?>');
	
	if( postArea )
		var post = wpautop(wptexturize(postArea.value));
	
	
    <?php
    $previewFormat = str_replace("'", "\'", $previewFormat);    
    $previewFormat = str_replace("%2", "' + post + '", $previewFormat);
    $previewFormat = "'" . $previewFormat . "';\n";
    ?>
    document.getElementById('postPreview').innerHTML = <?php echo $previewFormat; ?>
}

function initLivePreview() {
	if(!document.getElementById)
		return false;

	var postArea = document.getElementById('<?php echo $postTextID ?>');
	updateLivePreview();
	if ( postArea )
		postArea.onkeyup = updateLivePreview;
	
}

//========================================================
// Event Listener by Scott Andrew - http://scottandrew.com
// edited by Mark Wubben, <useCapture> is now set to false
//========================================================
function addEvent(obj, evType, fn){
	if(obj.addEventListener){
		obj.addEventListener(evType, fn, false); 
		return true;
	} else if (obj.attachEvent){
		var r = obj.attachEvent('on'+evType, fn);
		return r;
	} else {
		return false;
	}
}

addEvent(window, "load", initLivePreview);

<?php die(); }


function live_post_preview($before='', $after='') {
	global $livePreviewDivAdded;
	if($livePreviewDivAdded == false) {
		echo $before.'<div id="postPreview"></div>'.$after;
		$livePreviewDivAdded = true;
	}
}

function lpp_add_preview_div($post_id) {
	global $postTextID, $livePreviewDivAdded;
	if($livePreviewDivAdded == false) {
		echo '<div id="postPreview"></div>';
		$livePreviewDivAdded = true;
   	}
	return $post_id;
}
function lpp_add_js($ret) {
	echo('<script src="' . get_settings('siteurl') . '/wp-content/plugins/live-post-preview.php/postPreview.js" type="text/javascript"></script>');
	return $ret;
}

add_action('simple_edit_form', 'lpp_add_preview_div');
add_action('edit_form_advanced', 'lpp_add_preview_div');
add_action('admin_head', 'lpp_add_js');

?>
