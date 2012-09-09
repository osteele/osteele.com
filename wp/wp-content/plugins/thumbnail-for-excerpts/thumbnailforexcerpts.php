<?php
/**
Plugin Name: Thumbnail For Excerpts
Author URI: http://familia.capan.ro
Plugin URI: http://www.cnet.ro/wordpress/thumbnailforexcerpts
Description: Thumbnail For Excerpts allow easily, without any further work, to add thumbnails wherever you show excerpts (archive page, feed...).
Author: Radu Capan
Version: 2.1

CHANGELOG
See readme.txt
*/

if(!defined('WP_CONTENT_URL'))
	define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if(!defined('WP_PLUGIN_URL'))
	define('WP_PLUGIN_URL',WP_CONTENT_URL.'/plugins');

add_filter("the_excerpt","tfe_put_image");
add_filter("the_excerpt_rss","tfe_put_image");
add_action("admin_menu","tfe_add_admin");
if(function_exists('add_theme_support'))
	add_theme_support('post-thumbnails');

function tfe_add_admin() {
	global $optionsTfE;
	if($_GET['page']=='thumbsexcerpts'){
		if('save'==$_REQUEST['action']){
			foreach($optionsTfE as $value){
				tfe_update_option($value['id'],$_REQUEST[$value['id']]);
			}
			header("Location: options-general.php?page=thumbsexcerpts&saved=true");
			die;
		}
	}
	add_options_page('ThumbsExcerpts', 'Thumbs Excerpts', 'administrator', 'thumbsexcerpts', 'tfe_administration');
}

function tfe_update_option($key, $value){
	update_option($key, (get_magic_quotes_gpc()) ? stripslashes($value) : $value);
}

$optionsTfE=array(
	array(	"name" => "Width",
			"desc" => '<strong>Width:</strong> By default 150, but you can choose a smaller value also. Better below thumbnail defined size.',
			"id" => "tfe_width",
			"std" => "150",
			"type" => "text"
		),
	array(	"name" => "Height",
			"desc" => '<strong>Height:</strong> By default 150, but you can choose a smaller value also. Better below thumbnail defined size.',
			"id" => "tfe_height",
			"std" => "150",
			"type" => "text"
		),
	array(	"name" => "Align",
			"desc" => '<strong>Align:</strong> You can choose between left, right and center align.',
			"id" => "tfe_align",
			"std" => "left",
			"options" => array('left','right','center'),
			"type" => "select"
		),
	array(	"name" => "Default Image",
			"desc" => '<strong>Default Image:</strong> You can choose to use the default image, or not.',
			"id" => "tfe_default_image",
			"std" => "yes",
			"options" => array('yes','no'),
			"type" => "select"
		),
	array(	"name" => "Default image",
			"desc" => '<strong>Default image:</strong> We provide you one, by default. You can put any image you want.<br>'.WP_PLUGIN_URL.'/thumbnail-for-excerpts/tfe_no_thumb.png',
			"id" => "tfe_default_image_src",
			"std" => WP_PLUGIN_URL.'/thumbnail-for-excerpts/tfe_no_thumb.png',
			"type" => "text"
		),
	array(	"name" => "With Link",
			"desc" => '<strong>Whith Link:</strong> Choose yes if you want the thumbnails to be clickable.',
			"id" => "tfe_withlink",
			"std" => "yes",
			"options" => array('yes','no'),
			"type" => "select"
		),
	array(	"name" => "Exclusion",
			"desc" => '<strong>Exclusion:</strong> If you want this plugin to be inactive for some categories (on categories pages!!!), indicate them with their IDs (numbers), separated with comma.<br>Leave empty otherwise. Examples in you need exclusion: 3,4,9',
			"id" => "tfe_exclusion",
			"std" => "",
			"type" => "text"
		),
	array(	"name" => "Regenerate",
			"desc" => '<strong>Regenerate:</strong> Useful is for some reason, for a limited number of post, you have lost the generated thumbnails.<br>To regenerate all the thumbnails, use <strong>Regenerate Thumbnails</strong> plugin, signed by Viper007Bond. BETTER TO BE SET ON NO.',
			"id" => "tfe_regenerate",
			"std" => "no",
			"options" => array('yes','no'),
			"type" => "select"
		),
	array(	"name" => "Apply on home",
			"desc" => '<strong>Home:</strong> If you do not already use excerpts in your home page, choosing yes here will force from full content to excerpts. Your choice.',
			"id" => "tfe_on_home",
			"std" => "no",
			"options" => array('yes','no'),
			"type" => "select"
		),
	array(	"name" => "Apply on archives",
			"desc" => '<strong>Archives:</strong> If you do not already use excerpts in your archives pages (for categories, time periods or authors), choosing yes here will force from full content to excerpts. Your choice.',
			"id" => "tfe_on_archives",
			"std" => "no",
			"options" => array('yes','no'),
			"type" => "select"
		),
	array(	"name" => "Apply on search",
			"desc" => '<strong>Search:</strong> If you do not already use excerpts in your search results page, choosing yes here will force from full content to excerpts. Your choice.',
			"id" => "tfe_on_search",
			"std" => "no",
			"options" => array('yes','no'),
			"type" => "select"
		)
	);

function tfe_administration(){
	global $optionsTfE;
	if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
?>
<div class="wrap">
	<h2>Thumbnail for Excerpts</h2>
	<form method="post">
		<table class="optiontable" style="width:100%;">
	<?php 
	foreach ($optionsTfE as $value) {
    switch ( $value['type'] ) {
        case 'text': ?>
		<tr valign="top"><th scope="row" style="width:1%;white-space: nowrap;"><?php echo $value['name']; ?>:</th><td>
		<input style="width:100%;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" 
		value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" />
		</td></tr><tr valign="top"><td>&nbsp;</td><td><small><?php echo $value['desc']; ?></small></td></tr><?php
		break;
		case 'textarea': ?>
		<tr valign="top"><th scope="row" style="width:1%;white-space: nowrap;"><?php echo $value['name']; ?>:</th><td>
		<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" style="width:100%;height:100px;">
		<?php if( get_settings($value['id']) !== false) echo get_settings($value['id']); else echo $value['std']; ?></textarea>
		</td></tr><tr valign="top"><td>&nbsp;</td><td><small><?php echo $value['desc']; ?></small></td></tr><?php
		break;
		case 'select': ?>
		<tr valign="top"><th scope="row" style="width:1%;white-space: nowrap;"><?php echo $value['name']; ?>:</th><td>
		<select style="width:70%;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
        <?php foreach ($value['options'] as $option) { ?>
        <option<?php if(get_settings($value['id'])==$option) echo ' selected="selected"';
		else if ($option==$value['std'] && get_settings($value['id'])=='') echo ' selected="selected"'; ?>><?php echo $option; ?></option>
        <?php } ?>
		</select>
		</td></tr><tr valign="top"><td>&nbsp;</td><td><small><?php echo $value['desc']; ?></small></td></tr><?php
		break;
		default:

        break;
    }
}
?>
		</table>
		<p class="submit">
			<input name="save" type="submit" value="Save changes" />
			<input type="hidden" name="action" value="save" />
		</p>
	</form>
<h2>Explanation</h2>
<p>Welcome to <i>Thumbnail for Excerpts</i>, version 2. The idea of this plugin is to make excerpts have an image near. By default, WordPress strip every tag in excerpts, including images, so the look can be boring. Since WordPress 2.9 there is a mechanism for thumbnails, which still ask for some coding. Not with this plugin! With this plugin your life is easier! <b>Keep in mind that this plugin use the new feature from WordPress 2.9, but works with previous versions too.</b> It will use any specific thumbnail you have indicated manually. In the same time, it will provide automatically thumbnails for all the posts (previous posts for example) which have at least one image, but no manually-indicated thumbnail.
<p>What do you need for this plugin to work? Short version: just activate it (it is activated, since you are reading this) and it will work. Long version: it works 100% for websites which host images locally (not linked remotely) with valid HTML. If you publish with Visual Editor or with programs as Windows Live Writer you don't have to worry. If images from your blog are hosted somewhere else (stealing bandwidth? or using a better hosting solution for media?) than this plugin may not provide the best results - test it to be sure.
<p>What's new from the previous version? In 2.0 the plugin was rewritten to be XHTML valid, smarter and better. For example now it will work even with templates which do not use excerpts (will force the design to use excerpts, see last three options from above). It has a setting page in WordPress back-end (which you see now) from where you can easily setup different aspects (previously you had to edit the PHP file). And yes, there is a default image for posts without images (but you can choose not to use it or to use your own image).

<h2>Questions you may ask</h2>

<p><b>I don't use WordPress 2.9. It will crash my blog?</b>
<br>No, but it's better to use latest WordPress. Should work from 2.5, but I've only tested on 2.8 and above.

<p><b>I use the new feature from WordPress 2.9!</b>
<br>Good. This plugin will use what you define as thumbnails for posts.

<p><b>I don't use the new feature from WordPress 2.9!</b>
<br>Well, no problem. I guess it's difficult to come everytime in back-end to define a thumbnail for a post. This plugin will provide first image from the post as thumbnail.

<p><b>I don't see the images!</b>
<br>Check first if your images have thumbnails. WordPress create 2 thumbnails for every image you upload in your posts (you can go with FTP in your-blog/wp-content/uploads/ - eventually year/month/ - and check if each image has also small versions, meaning picture.jpg to have also picture-150x150.jpg). If you go to http://themes.cnet.ro/blog/wp-admin/options-media.php this page you should see 150x150 for thumbnail size, 300x300 for medium size... You can choose other values, but keep in mind that what you choose for WordPress thumbnails should be equal or greater that what you choose above in this page. If you put (in the past) 0x0 not to have thumbnails generated for each image, than the plugin will try to use the original (large image). You still can return to using apropiate thumbnails. You can activate above Regenerate, or better use the plugin indicated as a description near Regenerate option.

<p><b>The thumbnail image is too close to text!</b>
<br>Use some CSS! All thumbnail images have a class: wp-post-image (and also tfe). You can indicate <i>margin: 0px 5px 0px 0px;</i> for example, if images is left align. In your theme directory you have a <i>style.css</i> where you need to write some rules for thumbnail images.

<p><b>This plugin do not put thumbnails for galleries!</b>
<br>Well, from WordPress 2.9 you can attach to a post with a gallery a thumbnail with whatever image you want from that gallery.


</strong>
	<p>
</div>
<?php
}

function tfe_get_image($id,$content,$align,$width,$height,$default,$regenerate,$default_src) {
	$first_img = '';
	//extract image
	preg_match_all('~<img [^\>]*>~', $content, $matches);
	$first_image = $matches[0][0];
	//extract alt
	preg_match_all('~alt=[\'"]([^\'"]+)[\'"]~', $first_image, $matches);
	$alt = $matches[1][0];
	//extract title
	preg_match_all('~title=[\'"]([^\'"]+)[\'"]~', $first_image, $matches);
	$title = $matches[1][0];
	//extract src
	preg_match_all('~src=[\'"]([^\'"]+)[\'"]~', $first_image, $matches);
	$src=$matches[1][0];
	$srcorig=$matches[1][0];
	//last try, for images with src without quotes
	if(empty($src)){
		preg_match_all('~src=([^\'"]+) ~', $first_image, $matches);
		$src=$matches[1][0];
		$srcorig=$matches[1][0];
	}
	//find base image for jpg
	$imgtype=array("jpg","jpeg","gif","png");
	foreach($imgtype as $type){
		preg_match_all('~(\w*)-([0-9]*)x([0-9]*).('.$type.')~', $src, $matches);
		if(!empty($matches[1][0]))
			$src = substr($src,0,strrpos($src,'/')).'/'.$matches[1][0].'.'.$matches[4][0];
		$src = str_replace(".".$type,"-".get_option("thumbnail_size_w")."x".get_option("thumbnail_size_h").".".$type,$src);
	}
	$noimg=false;
	if(empty($src)){ //use the default image
		$src = $default_src;
		$noimg=true;
	}
	if($default=='no' && $noimg==true){
		//not to show default? ok! nothing to show!
	}
	else {
		if(stripos($src,WP_CONTENT_URL)!==false){
			//the image is hosted on the blog
			$rest = str_replace(WP_CONTENT_URL,'',$src);
			if(!file_exists(realpath(".").'/wp-content'.$rest))
				if($regenerate=="yes"){
					//thumbnail not found and I'm allowed to regenerate
					$images =& get_children( array(
									'post_parent' => $id,
									'post_type' => 'attachment',
									'post_mime_type' => 'image',
									'numberposts' => -1,
									'post_status' => 'inherit',
									'post_parent' => null, // any parent
									'output' => 'object',
								) );
					foreach($images as $image)
						$ids[] = $image->ID;
					require_once(ABSPATH .'/wp-includes/post.php'); 
					require_once(ABSPATH .'/wp-admin/includes/image.php'); 
					foreach($ids as $idimg){
						$fullsizepath = get_attached_file($idimg);
						if(false === $fullsizepath || !file_exists($fullsizepath))
							die('-1');
						set_time_limit(60);
						wp_update_attachment_metadata( $idimg, wp_generate_attachment_metadata( $idimg, $fullsizepath));
					}
				}
			if(!file_exists(realpath(".").'/wp-content'.$rest))
				//thumbnail still not found: this means the image is below 150x150
				//or is a problem with regeneration; anyway, we use the original image
				$src=$srcorig;
		}
		else
			//the image is hosted outside the blog!
			$src=$srcorig;
		if($src==$srcorig)
			return '<img width="'.$width.'" src="'.$src.'" class="align'.$align.' wp-post-image tfe" alt="'.$alt.'" title="'.$title.'" />';
		else
			return '<img width="'.$width.'" height="'.$height.'" src="'.$src.'" class="align'.$align.' wp-post-image tfe" alt="'.$alt.'" title="'.$title.'" />';
	}
}

function tfe_put_image($excerpt){
	if(is_single()) return;
	global $wp_query, $wpdb;
	$id = $wp_query->post->ID;
	//get the options you've choose
	$content = $wpdb->get_var('select post_content from '.$wpdb->prefix.'posts where id='.$id);
	$align=(get_settings('tfe_align')==''?'left':get_settings('tfe_align'));
	$default=(get_settings('tfe_default_image')==''?'yes':get_settings('tfe_default_image'));
	$withlink=(get_settings('tfe_withlink')==''?'yes':get_settings('tfe_withlink'));
	$exclusion=(get_settings('tfe_exclusion')==''?'':get_settings('tfe_exclusion'));
	$regenerate=(get_settings('tfe_regenerate')==''?'no':get_settings('tfe_regenerate'));
	$width=(get_settings('tfe_width')==''?'150':get_settings('tfe_width'));
	$height=(get_settings('tfe_height')==''?'150':get_settings('tfe_height'));
	$default_src=(get_settings('tfe_default_image_src')==''?WP_PLUGIN_URL.'/thumbnail-for-excerpts/tfe_no_thumb.png':get_settings('tfe_default_image_src'));
	if(!empty($exclusion)){
		$categ=explode(",",$exclusion);
		if(is_category() && in_category($categ,$id))
			return $excerpt;
	}
	$find=false;
	if(function_exists('has_post_thumbnail'))
		//you have WP 2.9 - do you use thumbnail feature, than I use it
		if(has_post_thumbnail($id)){
			if($withlink=="yes")
				$plus='<a href="'.get_permalink().'">'.get_the_post_thumbnail($id,array($width,$height),array('class'=>'align'.$align.' tfe')).'</a>';
			else
				$plus=get_the_post_thumbnail($id,array($width,$height),array('class'=>'align'.$align.' tfe'));
			$find=true;
		}
	if(!$find){
		//no thumbnail defined with 2.9 feature... than we go the old way
		$post_thumbnail = tfe_get_image($id,$content,$align,$width,$height,$default,$regenerate,$default_src);
		if(!empty($post_thumbnail))
			if($withlink=="yes")
				$plus='<a href="'.get_permalink().'">'.$post_thumbnail.'</a>';
			else
				$plus=$post_thumbnail;
	}
	if(is_feed())
		//this is going on feed, so it needs a simple formatting, no classes
		if($align=="center")
			$plus = '<p align="center">'.$plus.'</p>';
		else
			$plus = str_replace('<img ','<img align="'.$align.'" hspace="5" ',$plus);
	return $plus.$excerpt;
}

add_filter("the_content","tfe_change_to_excerpts");

function tfe_change_to_excerpts($content){
	//for single articles and full feeds, we don't want to force the excerpt
	if(is_single() || is_feed()) return $content;
	$on_home=(get_settings('tfe_on_home')==''?'no':get_settings('tfe_on_home'));
	$on_archives=(get_settings('tfe_on_archives')==''?'no':get_settings('tfe_on_archives'));
	$on_search=(get_settings('tfe_on_search')==''?'no':get_settings('tfe_on_search'));
	//cases for which to apply the force content->excerpt
	if((is_home() && $on_home=="yes") || (is_archive() && $on_archives=="yes") || (is_search() && $on_search=="yes")){
		global $post;
		$content = $post->post_excerpt;
		if($content)
			$content=apply_filters('the_excerpt', $content);
		else {
			$content = $post->post_content;
			$content = strip_shortcodes($content);
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = strip_tags($content);
			$excerpt_length = 55;
			$words = explode(' ',$content,$excerpt_length+1);
			if(count($words) > $excerpt_length){
				array_pop($words);
				array_push($words,'...');
				$content=implode(' ',$words);
			}
			$content = '<p>'.$content.'</p>';
		}
		return tfe_put_image($content);
	}
	else return $content;
}
?>