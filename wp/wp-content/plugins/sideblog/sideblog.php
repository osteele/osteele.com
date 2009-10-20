<?php
/* 
Plugin Name: Sideblog Wordpress Plugin
Plugin URI: http://katesgasis.com/2005/10/24/sideblog/
Description: A simple aside plugin. <br/>Licensed under the <a href="http://www.fsf.org/licensing/licenses/gpl.txt">GPL</a>
Version: 6.0
Author: Kates Gasis
Author URI: http://katesgasis.com
*/


$sb_defaultformat = "<li>%content% - %permalink%</li>";
$sb_defaultposts = 10;

function sideblog_post_groupby($groupby){
	return '';
}
add_filter('posts_groupby', 'sideblog_post_groupby');


function sideblog_post_filter($query) {
	global $parent_file, $wpdb;

	$sideblog_options = get_option('sideblog_options');	
	
	if((isset($parent_file)||!empty($parent_file))){
		return $query;
	}
	
	if(is_feed()){
		if(isset($sideblog_options['excludefromfeeds']) && !empty($sideblog_options['excludefromfeeds'])){
			$query = sideblog_remove_category($query,$sideblog_options['excludefromfeeds']);
		}		
	} else {
		if(is_home()){	
			if(isset($sideblog_options['setaside']) && !empty($sideblog_options['setaside'])){
				$query = sideblog_remove_category($query,$sideblog_options['setaside']);
			}
		}
	}
	return $query;
}

function sideblog_remove_category($query,$category){
	$cat = $query->get('category__in');
	$cat2 = array_merge($query->get('category__not_in'),$category);
	if($cat && $cat2){
		foreach($cat2 as $k=>$c){
			if(in_array($c,$cat)){
				unset($cat2[$k]);
			}
		}
	}
	$query->set('category__not_in',$cat2);
 
	return $query;
}

function sideblog_recent_entries($args) {
	global $wpdb;
	$sideblog_options = get_option('sideblog_options');
	if(isset($sideblog_options['setaside']) && !empty($sideblog_options['setaside'])){
		$setasides = implode(",",$sideblog_options['setaside']);
	}
	extract($args);
	$title = __('Recent Posts');
	if(strstr($query,"$wpdb->terms")===FALSE && isset($setasides)){
		$wp_query = new WP_Query();
		$wp_query->set('category__not_in', $sideblog_options['setaside']);
		$wp_query->set('posts_per_page', 10);
		$rows = $wp_query->get_posts();
	}
	if ($rows) :
?>
		<?php echo $before_widget; ?>
			<?php if(!empty($title)): ?>
			<?php echo $before_title . $title . $after_title; ?>
			<?php endif; ?>
			<ul>
			<?php  foreach($rows as $row): ?>
			<li><a href="<?php echo get_permalink($row->ID); ?>"><?php if ($row->post_title) echo $row->post_title; else echo $row->ID; ?> </a></li>
			<?php endforeach; ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
	endif;
}

function sideblog($asidecategory=''){
	global $wpdb, $sb_defaultformat,$sb_defaultposts;
	$limit = 5;
	$sideblog_options = get_option('sideblog_options');
	
	if(!isset($asidecategory) || empty($asidecategory)) {
		echo "Aside category not selected. Please provide a category slug if you're using non-dynamic sidebar.";
		return;
	}

	if(!$asidecategory){
		$asidecount = count($sideblog_options['setaside']);
		if($asidecount < 1){
			echo "No aside category selected. Please select an aside category in Options &raquo; Sideblog Panel.";
			return;
		}
		$asideid = '';
		if(isset($sideblog_options['setaside']) && !empty($sideblog_options['setaside'])){
			foreach($sideblog_options['setaside'] as $aside){
				if($asideid!=''){
					break;
				}
				$asideid = $aside;
			}
		}
	} else {
		$asideid = $wpdb->get_var("SELECT term_id FROM " . $wpdb->terms . " WHERE slug='" . $asidecategory . "'");
		if(isset($sideblog_options['setaside']) && !empty($sideblog_options['setaside'])){
			if(!in_array($asideid,$sideblog_options['setaside'])){
				echo "Aside category not selected.";
				return;
			}
		} else {
			echo "Aside category not selected.";
			return;
		}
	}
	$asidecategory = $asideid;
	$limit = $sideblog_options['numentries'][$asideid];
	if(!$limit){
		$limit = $sb_defaultposts;
	}

	$displayformat = stripslashes($sideblog_options['displayformat'][$asideid]);
	if(!$displayformat){
		$displayformat = $sb_defaultformat;
	}

	$now = current_time('mysql');
	$wp_query = new WP_Query();
	$wp_query->set('category__in', array($asideid));
	$wp_query->set('posts_per_page', $limit);
	$wp_query->set('category__not_in',array());
	$sideblog_contents = $wp_query->get_posts();
	$patterns[] = "%title%";
	$patterns[] = "%content%";
	$patterns[] = "%permalink%";
	$patterns[] = "%title_url%";
	$patterns[] = "%postdate%";
	$patterns[] = "%postdate_url%";
	$patterns[] = "%excerpt%";

	preg_match("/\%excerpt\_\d+\%/",$displayformat,$matches);
	$patterns[] = $matches[0];
	preg_match("/\d+/",$matches[0],$excerptcut);
	
	if($sideblog_contents){
		if($sideblog_options['order'][$asideid] == 'ASC'){
			
			$sideblog_contents = array_reverse($sideblog_contents);
		}
		foreach($sideblog_contents as $sideblog_content){			
			$permalink = get_permalink($sideblog_content->ID);
			
			$excerpt = sideblog_excerpt($sideblog_content->post_content,15);
			$excerpt2 = sideblog_excerpt($sideblog_content->post_content,$excerptcut[0]);

			$sideblog_content = apply_filters('sideblog_entry', $sideblog_content);

			$replacements[] = $sideblog_content->post_title;
			$replacements[] = wpautop($sideblog_content->post_content);
			$replacements[] = "<a href=\"" . $permalink . "\">#</a>";
			$replacements[] = "<a href=\"" . $permalink . "\" title=\"" . $sideblog_content->post_title . "\">" . $sideblog_content->post_title . "</a>";
			$replacements[] = $sideblog_content->post_date;
			$replacements[] = "<a href=\"" . $permalink . "\">" . $sideblog_content->post_date . "</a>";
			$replacements[] = $excerpt;
			$replacements[] = $excerpt2;
			
			$output = str_replace($patterns,$replacements,$displayformat);
			
			if(preg_match_all("/\%(\w)\%/",$output,$matches)){
				foreach($matches[1] as $match){
					$output = str_replace("%" . $match . "%",date($match,strtotime($sideblog_content->post_date)),$output);
				}
			}
		
			if(preg_match_all("/\%url\%([^\%]*)\%url\%/",$output,$matches)){
				foreach($matches[1] as $match){
					$output = str_replace("%url%" . $match . "%url%","<a href=\"" . $permalink . "\">" . $match . "</a>",$output);
				}
			}
			unset($matches);
			if(function_exists('Markdown')){
				$output =  Markdown($output);
			}
			echo $output;
			unset($replacements);
		}
	}
}


function sideblog_youtube_thumbnail($entry){
	if(!preg_match("/http:\/\/www\.youtube\.com/",$entry->post_content)){
		return $entry;
	}
	$permalink = get_permalink($entry->ID);
	preg_match('/\<embed.*?http:\/\/www\.youtube\.com\/v\/(.*?)\&[^\>]*?\>\<\/embed\>/', $entry->post_content, $matches);
	$youtube_thumbnail = "<a href='" . $permalink . "'><img src='http://img.youtube.com/vi/" . $matches[1] . "/default.jpg'/></a>";
	$object_pattern = "/\<object[^\>]*?\>\<param\s+.*?value=.*?www\.youtube\.com.*?\<\/object\>/";
	$entry->post_content = preg_replace($object_pattern, $youtube_thumbnail, $entry->post_content);
	return $entry;
}

function sideblog_metacafe_thumbnail($entry){
	if(!preg_match("/http:\/\/www\.metacafe\.com/", $entry->post_content)){
		return $entry;
	}
	$permalink = get_permalink($entry->ID);
	preg_match("/\<embed src=\"http:\/\/www\.metacafe\.com\/fplayer\/([^\/]*)\/.*?\<\/embed\>/", $entry->post_content, $matches);
	$metacafe_thumbnail = "<a href='" . $permalink . "'><img src='http://www.metacafe.com/thumb/" . $matches[1] . ".jpg'/></a>";
	$object_pattern = "/\<embed\s+src=[\"\']http:\/\/www\.metacafe\.com.*?\<\/embed\>\<br\>\<font.*?\<\/font\>/";
	$entry->post_content = preg_replace($object_pattern, $metacafe_thumbnail, $entry->post_content);
	return $entry;
}

function sideblog_vimeo_thumbnail($entry){
	$vimeo_api_key = "change this to your vimeo api key";
	if(!preg_match("/http:\/\/www\.vimeo\.com/", $entry->post_content)){
		return $entry;
	}
	$permalink = get_permalink($entry->ID);
	preg_match("/\<param.*?clip_id\=(\d+)/", $entry->post_content, $matches);

	$vimeo_thumbnail = "<script type='text/javascript'>" .
			"var clipUrl" . $entry->ID . " = 'http://www.vimeo.com/" . $matches[1] . "';" .
			"var endpoint" . $entry->ID . " = 'http://www.vimeo.com/api/oembed.json';" .
			"var callback" . $entry->ID . " = 'embedVimeo" . $entry->ID . "';" .
			"var url" . $entry->ID . " = endpoint" . $entry->ID . " + '?url=' + encodeURIComponent(clipUrl" . $entry->ID . ") + '&callback=' + callback" . $entry->ID . ";" .
			"var thumbnailEmbedCode = '';" .
			"function embedVimeo" . $entry->ID . "(video){" .
				"thumbnailEmbedCode = video.thumbnail_url;" .
				"document.getElementById('vimeo_" . $entry->ID ."').innerHTML = '<a href=\"$permalink\"><img src=\"' + unescape(video.thumbnail_url) + '\" /></a>';" .
			"}" .
			"function init" . $entry->ID . "(){" .
				"var js" . $entry->ID . " = document.createElement('script');" .
				"js" . $entry->ID . ".setAttribute('type', 'text/javascript');" .
				"js" . $entry->ID . ".setAttribute('src', url" . $entry->ID . ");" .
				"document.getElementsByTagName('head').item(0).appendChild(js" . $entry->ID . ");" .
			"}" .
			"(function(){init" . $entry->ID . "();})();".
			"</script>";
	$vimeo_thumbnail .=	"<span id='vimeo_" . $entry->ID . "'></span>";
	$object_pattern = "/\<object.*?movie.*?value=[\'\"]http:\/\/www\.vimeo\.com.*?\<a[^\>]*\>Vimeo\<\/a\>/";
	$entry->post_content = preg_replace($object_pattern, $vimeo_thumbnail, $entry->post_content);
	return $entry;
}

add_filter('sideblog_entry', 'sideblog_youtube_thumbnail');
add_filter('sideblog_entry', 'sideblog_metacafe_thumbnail');
add_filter('sideblog_entry', 'sideblog_vimeo_thumbnail');

function sideblog_option_page(){
	global $wpdb, $sb_defaultformat, $sb_defaultposts;
	if(isset($_POST['op'])){
		update_option('sideblog_options',$_POST['sideblog_options']);
		echo "<div id=\"message\" class=\"updated fade\"><p>Sideblog Options Updated</p></div>\n";

	}
	$sideblog_options = get_option('sideblog_options');
	
	$rows = $wpdb->get_results(
		"SELECT Term.term_id as id, Term.name, Term.slug " .
		"FROM $wpdb->terms Term, $wpdb->term_taxonomy Taxonomy " .
		"WHERE Term.term_id = Taxonomy.term_id " .
			"AND Taxonomy.taxonomy IN ('category') " .
		"ORDER BY name");
	
	$catlist = "";
	if($rows) {
		$alt = true;
		foreach($rows as $row) {
			if($alt) {
				$class="class='alternate'";
				$alt = false;
			}else{
				$class="class=''";
				$alt = true;
			}

			$excludefromfeeds = "";
			if(isset($sideblog_options['excludefromfeeds'][$row->id])){
				$excludefromfeeds = "checked='checked'";
			}

			$setaside = "";
			if(isset($sideblog_options['setaside'][$row->id])){
				$setaside = "checked='checked'";
			}
	
			$numentries = "";
			$postno = isset($sideblog_options['numentries'][$row->id]) ? $sideblog_options['numentries'][$row->id] : '';
			if(trim($postno)==''){
				for($i=1;$i<=$sb_defaultposts;$i++){
					if($postno == $i){
						$numentries .= "<option value=\"" . $i . "\" selected='true' >" . $i . "</option>\n";
					} else {
						$numentries .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
					}
				}
			} else {
				for($i=1;$i<=$sb_defaultposts;$i++){
					if($postno == $i){
						$numentries .= "<option value=\"" . $i . "\" selected='true' >" . $i . "</option>\n";
					} else {
						$numentries .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
					}
				}
			}

			$displayformat = isset($sideblog_options['displayformat'][$row->id]) ? $sideblog_options['displayformat'][$row->id]: '' ;
			if(trim($displayformat)==''){
				$displayformat = $sb_defaultformat;
			}

			$displayformat = htmlspecialchars(stripslashes($displayformat));
			
			$order_options = '';
			if($sideblog_options['order'][$row->id] == 'ASC'){
				$order_options .= "<option value='ASC' selected='true'>Ascending</option>" .
					"<option value='DESC'>Descending</option>";
			} else {
				$order_options .= "<option value='ASC'>Ascending</option>" .
					"<option value='DESC' selected='true'>Descending</option>";
			}

			$catlist .= "<tr " . $class . ">\n<td align='center'><input type=\"checkbox\" name=\"sideblog_options[setaside][$row->id]\" value=\"$row->id\" " . $setaside . "/></td>\n";
			$catlist .= "<td>" . $row->name . "</td>\n";
			$catlist .= "<td>" . $row->slug . "</td>\n";
			$catlist .= "<td align='center'><input type=\"text\" name=\"sideblog_options[displayformat][" . $row->id . "]\" value=\"" . $displayformat . "\" style=\"width:90%;\"/></td>\n";
			$catlist .= "<td align='center'><select name=\"sideblog_options[numentries][" . $row->id . "]\">" . $numentries . "</select></td>";
			$catlist .= "<td align='center'><select name='sideblog_options[order][" . $row->id . "]'>" . $order_options . "</select></td>";
			$catlist .= "<td align='center'><input type=\"checkbox\" name=\"sideblog_options[excludefromfeeds][" . $row->id . "]\" value=\"" . $row->id . "\" " . $excludefromfeeds . "/></td>\n</tr>\n";
		}
	}
	
	echo '
		<div class="wrap">
			<h2>' . __('Sideblog','sideblog') . '</h2>
			<form name="sideblog_options" method="POST">
				<input type="hidden" name="sideblog_options_update" value="update" />
				<fieldset class="options">
					<table width="100%" cellpadding="10px">
						<tr>
							<th width="8%">Select Categories
							</th>
							<th width="10%">Category Name
							</th>
							<th width="10%">Category Slug
							</th>
							<th>Display Format
							</th>
							<th width="8%">Number of Entries
							</th>
							<th width="15%">Order
							</th>
							<th width="8%">Exclude from Feeds
							</th>
						</tr>
						' . $catlist . '
					</table>
				</fieldset>
				<p class="submit"><input type="submit" value="Update Sideblog Options"/></p>
				<input type="hidden" name="op" value="update"/>
				<legend>Display Format Tags</legend>
					<ul>
					<li>%title%</li>
					<li>%title_url%</li>
					<li>%content%</li>
					<li>%permalink%</li>
					<li>%postdate%</li>
					<li>%postdate_url%</li>
					<li>%excerpt%</li>
					<li>%excerpt_&lt;length&gt;% - e.g. %excerpt_200% (will cut after 200 words)</li>
					<li><a href="http://www.php.net/date">PHP Date Format</a> - e.g. %m%/%d%/%Y% - 08-11-2006</li>
					</ul>
			</form>
		</div>';
}


function sideblog_add_option_page() {
	add_options_page('Sideblog','Sideblog',9,basename(__FILE__),'sideblog_option_page');
}

function sideblog_install(){
	$sideblog_options = get_option('sideblog_options');
	if(!$sideblog_options){
		add_option('sideblog_options');
		$sideblog_options['version'] = 6;
		update_option('sideblog_options', $sideblog_options);
	} else {
		if(!isset($sideblog_options['version'])){
			$sideblog_options['version'] = 6;
			update_option('sideblog_options', $sideblog_options);
		}
	}
}

function sideblog_uninstall(){
	//delete_option('sideblog_options');
	//delete_option('widget_sideblog');
}

//A modified the_content_rss function
function sideblog_excerpt($content,$cut = 0, $encode_html = 0) {

	if ($cut && !$encode_html) {
		$encode_html = 2;
	}
	if ($encode_html == 1) {
		$content = wp_specialchars($content);
		$cut = 0;
	} elseif ($encode_html == 0) {
		$content = make_url_footnote($content);
	} elseif ($encode_html == 2) {
		$content = strip_tags($content);
	}
	if ($cut) {
		$blah = explode(' ', $content);
		if (count($blah) > $cut) {
			$k = $cut;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}
		for ($i=0; $i<$k; $i++) {
			$excerpt .= $blah[$i].' ';
		}
		$excerpt .= ($use_dotdotdot) ? '...' : '';
		$content = $excerpt;
	}
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}

class SideblogWidget extends WP_Widget {
	
	function SideblogWidget(){
		$this->WP_Widget('sideblog-' . $i, __('Sideblog'), array('classname' => 'widget_sideblog'));
	}
	
	function widget($args, $instance){

		extract($args);

		$title = $instance['title'];
		$category = $instance['category'];

		$title = trim($title);

		echo $before_widget;
		if(!empty($title)){
			echo $before_title . $title . $after_title;
		}
		echo "<ul>";
		sideblog($category);
		echo "</ul>" . $after_widget;		
	}
	
	function update($new_instance, $old_instance){
		return $new_instance;
	}
	
	function form($instance){
		global $wpdb;
		$sideblog_options = get_option('sideblog_options');
		
		$title = attribute_escape($instance['title']);

		$rows = $wpdb->get_results("SELECT Term.term_id AS id, Term.name, Term.slug FROM " . $wpdb->terms . " Term ORDER BY Term.name");

		$catlist = "";
		if($rows){
			foreach($rows as $row){
				if(isset($sideblog_options['setaside'][$row->id])){
					if($instance['category']==$row->slug){ 
						$catlist .= "<option value=\"" . $row->slug . "\" selected=\"selected\">" . $row->name . "</option>";
					} else {
						$catlist .= "<option value=\"" . $row->slug . "\">" . $row->name . "</option>";
					}
				}
			}
		}

	?>
		<input style="width: 250px;" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		<select name="<?php echo $this->get_field_name('category'); ?>"><?php echo $catlist; ?></select>	
	<?php
	}
}

function sideblog_init_widgets(){
	register_widget('SideblogWidget');
}

function sb_recent_entries_init_widget(){
	register_sidebar_widget('SB Recent Posts', 'sideblog_recent_entries');
}

add_filter('pre_get_posts', 'sideblog_post_filter');
add_action('admin_menu', 'sideblog_add_option_page');
register_activation_hook(__FILE__, 'sideblog_install');
register_deactivation_hook(__FILE__, 'sideblog_uninstall');
//add_action('plugins_loaded', 'sideblog_widget_init');
add_action('widgets_init', 'sideblog_init_widgets');
add_action('plugins_loaded', 'sb_recent_entries_init_widget');