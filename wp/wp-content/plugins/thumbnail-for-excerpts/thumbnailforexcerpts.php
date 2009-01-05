<?php
/**
Plugin Name: Thumbnail For Excerpts
Author URI: http://familia.capan.ro
Plugin URI: http://www.cnet.ro/wordpress/thumbnailforexcerpts
Description: Thumbnail For Excerpts allow easily, without any further work, to add thumbnails wherever you show excerpts (archive page, feed...).
Author: Radu Capan
Version: 1.3

CHANGELOG
See readme.txt
*/
require_once(ABSPATH .'/wp-admin/includes/image.php'); 
require_once(ABSPATH .'/wp-admin/includes/media.php'); 
define("TFE_ALIGN","left"); //can be left or right
define("TFE_SIZE","100"); //the size of the thumbnail; modify it for better integration with your design; if you set it as 0 it will be than the default size of your WP thumbnails, from admin area
define("TFE_MAXSIZE","no"); //if yes, than the above indicated size will be used as maximum size for widht and height; if no, than the above indicated size is used only to limit the width
define("TFE_SPACE","5"); //for the HSPACE parameter of the IMG tag
define("TFE_LINK","yes"); //can be yes or no; if yes, the image will link to the post
define("TFE_CLASS","imgtfe"); //the class for the thumbnail images; you can change it or use this class in you CSS file
define("TFE_CREATETH","no"); //if yes, the images without thumbnails will have one created now (based on default values for thumbnail from admin area, or on TFE_SIZE if in admin area thumbanil size is set to zero)
define("TFE_TITLE","no"); //if yes, it will use titles for pictures (when you move mouse over the picture you will see the alt text)

add_filter("get_the_excerpt", "putThumbnailForExcerpts");

function putThumbnailForExcerpts($excerpt){
	if(is_single()) return;
	global $wp_query, $wpdb;
	$id = $wp_query->post->ID;
	if(!TFE_SIZE)
		$tfesize=get_option("thumbnail_size_w");
	else
		$tfesize=TFE_SIZE;
	/* - an excellent code, found on WordPress.org, but it's working only if you upload images from WP administration area
	$files = get_children("post_parent=$id&post_type=attachment&post_mime_type=image");
	if($files){
	        $keys = array_keys($files);
	        $num=$keys[0];
	        $thumb=wp_get_attachment_thumb_url($num);
	        echo "<img src=$thumb width=150 align=right>";
	}*/
	$content = $wpdb->get_var('select post_content from '.$wpdb->prefix.'posts where id='.$id);
	$pos = stripos($content,"<img");
	if($pos!==false){
		$content=substr($content,$pos,stripos($content,">",$pos));
		$pos = stripos($content,"src=")+4;
		$stopchar=" ";
		if("".substr($content,$pos,1)=='"'){
			$stopchar = '"';
			$pos++;
		}
		if("".substr($content,$pos,1)=="'"){
			$stopchar = "'";
			$pos++;
		}
		$img1 = "";
		do{
			$char = substr($content,$pos++,1);
			if($char != $stopchar)
				$img1 .= $char;
		}while(($char != $stopchar) && ($pos < strlen($content)));
		$tit = "";
		if(stripos($content,"title=")!==false){
			$pos = stripos($content,"title=")+6;
			$stopchar="|";
			if("".substr($content,$pos,1)=='"'){
				$stopchar = '"';
				$pos++;
			}
			if("".substr($content,$pos,1)=="'"){
				$stopchar = "'";
				$pos++;
			}
			do{
				$char = substr($content,$pos++,1);
				if($char != $stopchar)
					$tit .= $char;
			}while(($char != $stopchar) && ($pos < strlen($content)));
		}
		$alt = "";
		if(stripos($content,"alt=")!==false){
			$tit1="";
			$pos = stripos($content,"alt=")+4;
			$stopchar="|";
			if("".substr($content,$pos,1)=='"'){
				$stopchar = '"';
				$pos++;
			}
			if("".substr($content,$pos,1)=="'"){
				$stopchar = "'";
				$pos++;
			}
			do{
				$char = substr($content,$pos++,1);
				if($char != $stopchar)
					$alt .= $char;
			}while(($char != $stopchar) && ($pos < strlen($content)));
		}
		if($alt!="")
			$tit1=$alt;
		else if($tit!="")
			$tit1=$tit;
		else
			$tit1="";
		$img2 = str_replace(".jpg","-".get_option("thumbnail_size_w")."x".get_option("thumbnail_size_h").".jpg",$img1);
		$img2 = str_replace(".png","-".get_option("thumbnail_size_w")."x".get_option("thumbnail_size_h").".png",$img2);
		$img2 = str_replace(".gif","-".get_option("thumbnail_size_w")."x".get_option("thumbnail_size_h").".gif",$img2);
		if(!file_exists(realpath(".")."/".substr($img2,stripos($img2,"wp-content"))) && (TFE_CREATETH=="yes")){
			if(get_option("thumbnail_size_w")>0)
				image_make_intermediate_size( realpath(".")."/".substr($img1,stripos($img1,"wp-content")), get_option("thumbnail_size_w"),get_option("thumbnail_size_h"),true);
			else
				image_make_intermediate_size( realpath(".")."/".substr($img1,stripos($img1,"wp-content")), TFE_SIZE,TFE_SIZE,true);
			$img2 = str_replace(".jpg","-".TFE_SIZE."x".TFE_SIZE.".jpg",$img1);	
			$img2 = str_replace(".png","-".TFE_SIZE."x".TFE_SIZE.".png",$img2);	
			$img2 = str_replace(".gif","-".TFE_SIZE."x".TFE_SIZE.".gif",$img2);	
		}
		if (file_exists(realpath(".")."/".substr($img2,stripos($img2,"wp-content")))){
			$condsize = "width";
			if(TFE_MAXSIZE=="yes" && (get_option("thumbnail_size_h") > get_option("thumbnail_size_w"))){
				$condsize = "height";
				if(!TFE_SIZE)
					$tfesize=get_option("thumbnail_size_h");
			}
		    echo (TFE_LINK=="yes"?"<a href=".get_permalink($id).">":"")."<img src=".$img2." class=".TFE_CLASS." hspace=".TFE_SPACE." align=".TFE_ALIGN." $condsize=".$tfesize." ".(TFE_TITLE=="yes"?"alt='".$tit1."' title='".$tit1."'":"")." border=0>".(TFE_LINK=="yes"?"</a>":"");
		}
		else {
			$condsize = "width";
			if ((TFE_MAXSIZE=="yes") && extension_loaded('gd') && function_exists('gd_info')) {
				$im = imagecreatefromjpeg(realpath(".")."/".substr($img1,stripos($img1,"wp-content")));
				if(imagesx($im)<imagesy($im))
					$condsize = "height";
			}
		    echo (TFE_LINK=="yes"?"<a href=".get_permalink($id).">":"")."<img src=".$img1." class=".TFE_CLASS." hspace=".TFE_SPACE." align=".TFE_ALIGN." $condsize=".$tfesize." ".(TFE_TITLE=="yes"?"alt='".$tit1."' title='".$tit1."'":"")." border=0>".(TFE_LINK=="yes"?"</a>":"");
		}
	}
	return $img.$excerpt;
}
?>