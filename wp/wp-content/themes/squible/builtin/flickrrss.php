<?php
/*
Plugin Name: flickrRSS
Plugin URI: http://eightface.com/code/wp-flickrrss/
Description: Integrates the photos from a flickr rss feed into your site. Configurable via <a href="admin.php?page=flickrrss.php">Options -> flickrRSS</a>. Remember to add a get_flickrRSS() function to your templates.
Version: 2.3
License: GPL
Author: Dave Kellam
Author URI: http://eightface.com
*/


if (is_plugin_page()) { 
     if (isset($_POST['update_flickrrss'])) {
       $option_tag = $_POST['tag'];
       $option_tag2 = $_POST['tag2'];
       $option_tagtype = $_POST['tagtype'];
       $option_numitems = $_POST['num_items'];
       $option_mediumimages = $_POST['mediumImages'];
       $option_before = $_POST['before_image'];
       $option_after = $_POST['after_image'];
       update_option('flickrRSS_tag', $option_tag);
       update_option('flickrRSS_tag2', $option_tag2);
       update_option('flickrRSS_tagtype', $option_tagtype);
       update_option('flickrRSS_numitems', $option_numitems);
       update_option('flickrRSS_mediumimages', $option_mediumimages);
       update_option('flickrRSS_before', $option_before);
       update_option('flickrRSS_after', $option_after);
       ?> <div class="updated"><p>Options changes saved.</p></div> <?php
     }
     if (isset($_POST['save_cache_settings'])) {
       $option_useimagecache = $_POST['use_image_cache'];
       $option_imagecacheuri = $_POST['image_cache_uri'];
       $option_imagecachedest = $_POST['image_cache_dest'];
       update_option('flickrRSS_use_image_cache', $option_useimagecache);
       update_option('flickrRSS_image_cache_uri', $option_imagecacheuri);
       update_option('flickrRSS_image_cache_dest', $option_imagecachedest);
       ?> <div class="updated"><p>Cache settings saved.</p></div> <?php
     }
     if (isset($_POST['clear_cache'])) {
        $del_cache_path = get_option('flickrRSS_image_cache_dest') . "*";
        
        foreach (glob( $del_cache_path) as $filename) {
			unlink($filename);				// delete it
	    }
	    ?> <div class="updated"><p><strong>Cache emptied</strong> (<?php echo $del_cache_path; ?>)</p></div> <?php
      }

?>

	<div class="wrap">
		<h2>flickrRSS Badge Options</h2>
		<form method="post">
		
		<fieldset class="options">
		<p><strong>Type:</strong>
        <select name="tagtype" id="tagtype">
		      <option <?php if(get_option('flickrRSS_tagtype') == 'tag') { echo 'selected'; } ?> value="tag">tag</option>
		      <option <?php if(get_option('flickrRSS_tagtype') == 'userid') { echo 'selected'; } ?> value="userid">user id#</option>
		      <option <?php if(get_option('flickrRSS_tagtype') == 'usertag') { echo 'selected'; } ?> value="usertag">user id# & tag</option>
		      <option <?php if(get_option('flickrRSS_tagtype') == 'group') { echo 'selected'; } ?> value="group">group pool</option>
		      </select>
        - <label for="tag"></label>
        <input name="tag" type="text" id="tag" value="<?php echo get_option('flickrRSS_tag'); ?>" size="25" />
        - <label for="tag2"></label>
        <input name="tag2" type="text" id="tag2" value="<?php echo get_option('flickrRSS_tag2'); ?>" size="25" /><br /><em> e.g. tag - "cats", user id - "44124462494@N01", user id & tag - "44124462494@N01" -"blackandwhite", group pool - "circle"</em></li>
        </p>
		<p><strong>Display:</strong>
        <select name="num_items" id="num_items">
		      <option <?php if(get_option('flickrRSS_numitems') == '1') { echo 'selected'; } ?> value="1">1</option>
		      <option <?php if(get_option('flickrRSS_numitems') == '2') { echo 'selected'; } ?> value="2">2</option>
		      <option <?php if(get_option('flickrRSS_numitems') == '3') { echo 'selected'; } ?> value="3">3</option>
		      <option <?php if(get_option('flickrRSS_numitems') == '4') { echo 'selected'; } ?> value="4">4</option>
		      <option <?php if(get_option('flickrRSS_numitems') == '5') { echo 'selected'; } ?> value="5">5</option>
		      <option <?php if(get_option('flickrRSS_numitems') == '6') { echo 'selected'; } ?> value="6">6</option>
		      <option <?php if(get_option('flickrRSS_numitems') == '7') { echo 'selected'; } ?> value="7">7</option>
		      <option <?php if(get_option('flickrRSS_numitems') == '8') { echo 'selected'; } ?> value="8">8</option>
		      <option <?php if(get_option('flickrRSS_numitems') == '9') { echo 'selected'; } ?> value="9">9</option>
		      <option <?php if(get_option('flickrRSS_numitems') == '10') { echo 'selected'; } ?> value="10">10</option>
		      </select>
            <select name="mediumImages" id="mediumImages">
		      <option <?php if(get_option('flickrRSS_mediumimages') == 'small') { echo 'selected'; } ?> value="small">small</option>
		      <option <?php if(get_option('flickrRSS_mediumimages') == 'thumbnail') { echo 'selected'; } ?> value="thumbnail">thumbnail</option>
		      <option <?php if(get_option('flickrRSS_mediumimages') == 'medium') { echo 'selected'; } ?> value="medium">medium</option>
		    </select>
            <label for="mediumImages">images</label></p>
		  
          <p><label for="before_image">Before Image: </label>
        <input name="before_image" type="text" id="before_image" value="<?php echo htmlspecialchars(stripslashes(get_option('flickrRSS_before'))); ?>" size="10" /> <em> e.g. &lt;li&gt;, &lt;p&gt; </em></p>
        <p><label for="after_image">After Image: </label>
        <input name="after_image" type="text" id="after_image" value="<?php echo htmlspecialchars(stripslashes(get_option('flickrRSS_after'))); ?>" size="10" /> <em> e.g. &lt;/li&gt;, &lt;/p&gt;, &lt;br /&gt;</em></p>
            </p>
            
        </fieldset>

		  <p><div class="submit"><input type="submit" name="update_flickrrss" value="<?php _e('Update flickrRSS', 'update_flickrrss') ?>"  style="font-weight:bold;" /></div></p>
        </form>       
    </div>
    
    <div class="wrap">   
        <h2>Cache Settings</h2>

		<form method="post">
                <p><input name="use_image_cache" type="checkbox" id="use_image_cache" value="true" <?php if(get_option('flickrRSS_use_image_cache') == 'true') { echo 'checked="checked"'; } ?> />  <label for="image_cache"><strong>Use image cache</strong> (stores thumbnails on your server)</label></p>
                <fieldset class="options">
                <p><label for="image_cache_uri">URL: </label>
        <input name="image_cache_uri" type="text" id="image_cache_uri" value="<?php echo get_option('flickrRSS_image_cache_uri'); ?>" size="50" /> <em>e.g. http://url.com/cache/</em></p>
                <p><label for="image_cache_dest">Full Path: </label>

        <input name="image_cache_dest" type="text" id="image_cache_dest" value="<?php echo get_option('flickrRSS_image_cache_dest'); ?>" size="50" /> <em>e.g. /home/path/to/wp-content/flickrrss/cache/</em>
                </p>
         </fieldset>
         <p>
		  <div class="submit">
                <input type="submit" name="clear_cache" value="<?php _e('Empty Cache Now', 'clear_cache') ?>" /> 
                <input type="submit" name="save_cache_settings" value="<?php _e('Save Cache Settings', 'save_cache_settings') ?>" style="font-weight:bold;" /></div>
        </p></form>
        
        </fieldset>   

    </div>

<?php
}

else {
	
	#string $tag, string $tagtype, int $num_items, string $mediumPics, string $useImageCache
	
function get_flickrRSS() {
  for($i = 0 ; $i < func_num_args(); $i++) {
    $args[] = func_get_arg($i);
  }
  if (!isset($args[0])) $tag = trim(get_option('flickrRSS_tag')); else $tag = trim($args[0]);
  if (!isset($args[1])) $tagtype = get_option('flickrRSS_tagtype'); else $tagtype = $args[1];
  if (!isset($args[2])) $num_items = get_option('flickrRSS_numitems'); else $num_items = $args[2];
  if (!isset($args[3])) $mediumPics = get_option('flickrRSS_mediumimages'); else $mediumPics = $args[3];
  if (!isset($args[4])) $before = stripslashes(get_option('flickrRSS_before')); else $before_image = $args[4];
  if (!isset($args[5])) $after = stripslashes(get_option('flickrRSS_after')); else $after_image = $args[5];
  if (!isset($args[6])) $tag2 = trim(get_option('flickrRSS_tag2')); else $tag2 = trim($args[6]);
  if (!isset($args[7])) $useImageCache = get_option('flickrRSS_use_image_cache'); else $useImageCache = $args[7];
        
# use image cache & set location
$cachePath = get_option('flickrRSS_image_cache_uri'); #"/wp-content/flickrrss/cache/";
$fullPath = get_option('flickrRSS_image_cache_dest'); #ABSPATH . "wp-content/flickrrss/cache/";

if (!function_exists('MagpieRSS')) {
	require_once (ABSPATH . WPINC . '/rss-functions.php');
	error_reporting(E_ERROR);
}


// get the feeds
if ($tagtype == "tag") { $rss_url = 'http://www.flickr.com/services/feeds/photos_public.gne?tags=' . $tag . '&format=rss_200'; }
elseif ($tagtype == "userid") { $rss_url = 'http://www.flickr.com/services/feeds/photos_public.gne?id=' . $tag . '&format=rss_200'; }
elseif ($tagtype == "usertag") { $rss_url = 'http://www.flickr.com/services/feeds/photos_public.gne?id=' . $tag . '&tags=' . $tag2 . '&format=rss_200'; }
elseif ($tagtype == "group") { $rss_url = 'http://www.flickr.com/groups/' . $tag . '/pool/feed/?format=rss_200'; }
else { print "Invalid tagtype"; }

# get rss file
$rss = @ fetch_rss($rss_url);

if ($rss) {
    $imgurl = "";
    # specifies number of pictures
	$items = array_slice($rss->items, 0, $num_items);

    # builds html from array
    foreach ( $items as $item ) {
       if(preg_match('<img src="([^"]*)" [^/]*/>', $item['description'],$imgUrlMatches)) {

           $imgurl = $imgUrlMatches[1];
 
           #change image size         
           if ($mediumPics=="small") {
             $imgurl = str_replace("m.jpg", "s.jpg", $imgurl);
           }
           elseif ($mediumPics=="thumbnail") {
             $imgurl = str_replace("m.jpg", "t.jpg", $imgurl);
           }
           $title = htmlspecialchars(stripslashes($item['title']));
           $url = $item['link'];
	
	       preg_match('<http://static.flickr\.com/\d\d?\/([^.]*)\.jpg>', $imgurl, $flickrSlugMatches);
	       $flickrSlug = $flickrSlugMatches[1];
	       
	       # cache images 
	       if ($useImageCache) {
                       
               # check if file already exists in cache
               # if not, grab a copy of it
               if (!file_exists("$fullPath$flickrSlug.jpg")) {   
                 if ( function_exists('curl_init') ) {
                    $curl = curl_init();
                    $localimage = fopen("$fullPath$flickrSlug.jpg", "wb");
                    curl_setopt($curl, CURLOPT_URL, $imgurl);
                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
                    curl_setopt($curl, CURLOPT_FILE, $localimage);
                    curl_exec($curl);
                    curl_close($curl);
                   } else {
                 	$filedata = "";
                    $remoteimage = fopen($imgurl, 'rb');
                  	if ($remoteimage) {
                    	 while(!feof($remoteimage)) {
                         	$filedata.= fread($remoteimage,1024*8);
                       	 }
                  	}
                	fclose($remoteimage);
                	$localimage = fopen("$fullPath$flickrSlug.jpg", 'wb');
                	fwrite($localimage,$filedata);
                	fclose($localimage);
                 }
                }
                # use cached image
                print $before . "<a href=\"$url\" title=\"$title\"><img src=\"$cachePath$flickrSlug.jpg\" alt=\"$title\" /></a>" . $after;
                }
            else {
                # grab image direct from flickr
                print $before . "<a href=\"$url\" title=\"$title\"><img src=\"$imgurl\" alt=\"$title\" /></a>" . $after;      
            }
       } 
    }
} 

else {
    #print "Flickr is having a massage (Flickr Blog)";
 }
} # end get_flickrRSS() function

function fR_admin_menu() {
    $pagefile = basename(__FILE__);
        add_options_page('flickrRSS Options Page', 'flickrRSS', 8, $pagefile);
        }
        
add_action('admin_menu', 'fR_admin_menu');
}
?>