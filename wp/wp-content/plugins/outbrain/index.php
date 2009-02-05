<?php
/*
Plugin Name: outbrain
Plugin URI: http://wordpress.org/extend/plugins/outbrain/
Description: A WordPress plugin to deal with the <a href="http://www.outbrain.com">Outbrain</a> blog posting rating system.
Author: outbrain
Version: 3.7.0.0
Author URI: http://www.outbrain.com
*/
include 'versionControl.php';

//Control of parts related Partners 
if ($userType == "Partners"){
	$itemRecommendationsPerPage = true;
	$itemSelfRecommendations	= true;
	$itemExport					= true;
} else {
	$itemRecommendationsPerPage = false;
	$itemSelfRecommendations	= false;
	$itemExport					= false;
}


$outbrain_plugin_version = "3.7.0.0_". $userType; 


// consts
$outbrain_start_comment = "//OBSTART:do_NOT_remove_this_comment";
$outbrain_end_comment = "//OBEND:do_NOT_remove_this_comment";


// add admin options page
function outbrain_add_options_page(){
	add_options_page('Outbrain options', 'Outbrain Options', 8, dirname(__FILE__).'/ob_options.php');
}

function outbrain_getVersion(){
	 global $outbrain_plugin_version;
	 return $outbrain_plugin_version;
}

// display the plugin
function outbrain_display ($content)
{
	global $post_ID, $outbrain_start_comment, $outbrain_end_comment, $outbrain_plugin_version, $itemRecommendationsPerPage, $itemSelfRecommendations ;
	
	$where = array();
	$fromDB = get_option("outbrain_pages_list");
	if ((isset($fromDB)) && (is_array($fromDB))){
			$where = $fromDB;
	}
	//now get recommendations array
	$where_recs = array();
	$fromDB_recs = get_option("outbrain_pages_recs");
	if ((isset($fromDB_recs)) && (is_array($fromDB_recs))){
			$where_recs = $fromDB_recs;
	}
	
	if
	(
		(!(is_feed())) &&
		(
			((is_home()) && (in_array(0,$where))) 	|| 
			((is_single()) && (!is_attachment()) && (in_array(1,$where)) )	|| 
			((is_page()) && (in_array(2,$where))) 	|| 
			((is_archive()) && (in_array(3,$where)))|| 
			((is_attachment()) && (in_array(4,$where))) 
		)
	)
	{
		$recommendations_string				=	'';
		$self_recommendations_string		=	'';
		
	if ($itemRecommendationsPerPage){
		if (
			((is_home()) && (in_array(0,$where_recs))) 	|| 
			((is_single()) && (!is_attachment()) && (in_array(1,$where_recs)) )	|| 
			((is_page()) && (in_array(2,$where_recs))) 	|| 
			((is_archive()) && (in_array(3,$where_recs)))|| 
			((is_attachment()) && (in_array(4,$where_recs))) 
		)
		{
			$recommendations_string 		= "var OB_showRec			=	true;";
		}else{
			$recommendations_string 		= "var OB_showRec			=	false;";
		} 

	}

	if ($itemSelfRecommendations){
		if (get_option('outbrain_rater_self_recommendations')	==	true){
			$self_recommendations_string 	= "var OB_self_posts		=	true;";
		} else{
			$self_recommendations_string 	= "var OB_self_posts		=	false;";
		}
	}
		
		$installation_time_string			=	get_option('installation_time');
		
		
		if (! isset($installation_time_string) || (isset($installation_time_string) &&  empty($installation_time_string))){
			$installation_time_string =   time(); 
			update_option("installation_time",$installation_time_string);
		}
		
		
		$content .= '<script type=\'text/javascript\'>
		<!--
		' . $outbrain_start_comment . '
		var OutbrainPermaLink="' . get_permalink( $post_ID ) . '";
		if(typeof(OB_Script)!=\'undefined\'){
			OutbrainStart();
		} else {
			var OB_demoMode			=	false;
			' . $recommendations_string . '
			' . $self_recommendations_string . '
			var OB_PlugInVer		=	"' . $outbrain_plugin_version . '";
			var OBITm				=	"' . $installation_time_string . '";
			var OB_Script			=	true;
			var OB_langJS			=	"' . get_option("outbrain_lang") . '";
			document.write ("<script src=\'http://widgets.outbrain.com/OutbrainRater.js\' type=\'text/javascript\'><\/script>"); 
		}
		' . $outbrain_end_comment . '
		//-->
		</script>
		';
	}
	return $content;
}

// change the plugin on the_excerpt call
function outbrain_display_excerpt($content){
	global $outbrain_start_comment,$outbrain_end_comment;
	$pos = strpos($content,$outbrain_start_comment);
	$posEnd = strpos($content,$outbrain_end_comment);
	if ($pos){
		if ($posEnd == false){
			$content = str_replace(substr($content,$pos,strlen($content)),'',$content);
		} else {
			$content = str_replace(substr($content,$pos,$posEnd-$pos+strlen($outbrain_end_comment)),'',$content);
		}
	}
	$content = $content . outbrain_display('');
	return $content;
}

// print the css / js functions of the options page

function outbrain_get_plugin_admin_path(){
	$site_url = get_option("siteurl");
	// make sure the url ends with /
	$last = substr($site_url, strlen( $site_url ) - 1 );
	if ($last != "/") $site_url .= "/";
	// calculate base url based on current directory.
	$base_len = strlen(ABSPATH);
	$suffix = substr(dirname(__FILE__),$base_len)."/";
	// fix windows path sperator to url path seperator.
	$suffix = str_replace("\\","/",$suffix);
	$base_url = $site_url . $suffix;
	
	return $base_url;
}

function outbrain_get_plugin_place(){
	$ref = dirname(__FILE__);
	return $ref;
}


function outbrain_admin_script(){
	if ((strpos($_SERVER['QUERY_STRING'],'outbrain') == false) || (strpos($_SERVER['QUERY_STRING'],'options') == false)){
                // no outbrain's options page.
		return;
	}
	
	$src = 'http://widgets.outbrain.com/language_list.js';
	
	$base_url	=	outbrain_get_plugin_admin_path();
?>
	<link rel="stylesheet" href="<?php echo $base_url?>style.css" type="text/css" />
	<script type="text/javascript" src="<?php echo $src; ?>"></script>
	<script type="text/javascript" src="<?php echo $base_url?>jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="<?php echo $base_url?>script.js"></script>
	<script type="text/javascript">
		onload = function(){
			var current;
			<?php if (isset($_POST['lang_path'])){ ?>
				current = "<?php echo $_POST['lang_path']?>";
			<?php } else { ?>
				current = "<?php echo get_option('outbrain_lang')?>";
			<?php } ?>
			outbrain_admin_onload(current);
		}
	</script>
<?php
}

//--------------------------------------------------------------------------------------------------------
//	most popular widget
//--------------------------------------------------------------------------------------------------------
$outbrain_widget_dbdatafieldname = 'outbrain_mostPopular_data';

function outbrain_mostPopular_widget_control(){

	if (!function_exists('register_sidebar_widget')){ // no widgets in this wordpress installation!
		return;
	}
	
	global $outbrain_widget_dbdatafieldname;
	$curr_options = $new_options = get_option($outbrain_widget_dbdatafieldname);
	
	if ( $_POST["outbrain_widget_sent"] ) {
		$new_options['title'] = trim(strip_tags(stripslashes($_POST["outbrain_widget_title"])));
		$new_options['postsCount'] = $_POST["outbrain_widget_postsCount"];
		$new_options['dontShowVotersCount'] = $_POST["outbrain_widget_VotersCount"];
		
		if (!is_numeric($new_options['postsCount'])){
			$new_options['postsCount'] = $curr_options['postsCount'];
		}
		
		if (!is_numeric($new_options['dontShowVotersCount'])){
			$new_options['dontShowVotersCount'] = $curr_options['dontShowVotersCount'];
		}
	}
	
	if ( $curr_options != $new_options ) {
		$curr_options = $new_options;
		update_option($outbrain_widget_dbdatafieldname, $curr_options);
	}
	?>
		<input type="hidden" name="outbrain_widget_sent" value="1" />
		<div style="width:100%;margin-bottom:15px;">
			<label name="outbrain_widget_title">title</label>
			<div style="margin-left:15px;">
				<input type="text" name="outbrain_widget_title"	value="<?php	echo $curr_options['title'];	?>" />
			</div>
		</div>
		<div style="width:100%;margin-bottom:15px;">
			<label name="outbrain_widget_postsCount">how many posts to display</label>
			<div style="margin-left:15px;">
				<select name="outbrain_widget_postsCount">
					<?php
						for ($i=1;$i<=10;$i++){
							if ($curr_options['postsCount'] == $i){
								echo "<option value='$i' selected='selected'>$i</option>";
							} else {
								echo "<option value='$i'>$i</option>";
							}
						}
					?>
				</select>
				
				<!-- input type="text" name="outbrain_widget_postsCount" value="<?php	echo $curr_options['postsCount'];	?>" / -->
			</div>
		</div>
		<div style="width:100%;margin-bottom:15px;">
			<div style="margin-left:15px;">
				<input type="radio" name="outbrain_widget_VotersCount" 	value="0" <?php if($curr_options['dontShowVotersCount'] != 1) echo "checked='checked'" ?>>&nbsp; <label>Show number of raters</label><br/>
				<input type="radio" name="outbrain_widget_VotersCount" 	value="1" <?php if($curr_options['dontShowVotersCount'] == 1) echo "checked='checked'" ?>>&nbsp; <label>Don't show number of raters</label>
			</div>
		</div>
		
	<?php
}

function outbrain_mostPopular_widget($args) {

	global $outbrain_widget_dbdatafieldname;
	
	$options 		= get_option($outbrain_widget_dbdatafieldname);
	$title 			= $options['title'];
	$count 			= $options['postsCount'];
	$dontShowCountRec 	= $options['dontShowVotersCount'];
	
    extract($args);
	$cssUpdate  = '';
	
	$text .= '';
	
	$text	.=	$before_widget
			.	$before_title
			.	$title
			.	$after_title
			.	'<script type="text/javascript">'
			.	"\r\n"
			.	'var OB_MP_hideTitle = true; // hide the widget\'s title from js. we have it from wordpress'
			.	"\r\n";

	if (is_numeric($count)){
	$text	.=	''
			.	'var OB_MP_itemsCount =' . $count . ';'
			.	"\r\n";
	}

	if (is_numeric($dontShowCountRec) && $dontShowCountRec == 1 ){//show
	$cssUpdate	.=	''
				.'.outbrain_MP_widget .item_rating {display:none;}';
	}
	
	$text	.=	''
			.	'var OB_langJS ="' . get_option("outbrain_lang") . '";'
			.	"\r\n"
			.	'</script>'
			.	'<script type="text/javascript" src="http://widgets.outbrain.com/outbrainMP.js"></script>'
			.	'<style type="text/css">'. $cssUpdate .'</style>'
			.	$after_widget;

	echo $text;
	
}

function outbrain_mostPopular_widget_init(){

	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') ){
		return;
	}

	global $outbrain_widget_dbdatafieldname;
	$defaults = array();
	$defaults['title'] = 'Most Popular Posts';
	$defaults['postsCount'] = 3;
	
	add_option($outbrain_widget_dbdatafieldname, $defaults); 

	register_sidebar_widget('Most Popular','outbrain_mostPopular_widget');
	register_widget_control('Most Popular', 'outbrain_mostPopular_widget_control', 250, 170);
}

function outbrain_addClaimCode(){
	$key	=	get_option('outbrain_claim_key');
	if ($key == ''){
		return;
	}
	echo "<meta name='OBKey' content='$key' />\r\n";
}

function outbrain_returnClaimCode(){
	$key	=	get_option('outbrain_claim_key');
	if ($key == ''){
		return;
	}
	echo "$key";
}

function outbrain_returnEncodeClaimCode(){
	$key	=	get_option('outbrain_claim_key');
	if ($key == ''){
		return;
	}
	$encodeKey = urlencode($key);
	echo "$encodeKey";
}


add_action('plugins_loaded', 'outbrain_mostPopular_widget_init');

// add filters 
add_filter('the_content'	, 'outbrain_display');
add_filter('the_excerpt'	, 'outbrain_display_excerpt');
add_filter('wp_head'		, 'outbrain_addClaimCode', 1);
add_action('admin_menu'		, 'outbrain_add_options_page');
add_filter('admin_head'		, 'outbrain_admin_script');

add_option('outbrain_pages_list',array(0,1,2,3,4));
add_option('outbrain_pages_recs',array(0,1,2,3,4));
add_option('outbrain_claim_key','');
add_option('outbrain_claim_status_num','');
add_option('outbrain_claim_status_string','');
	
add_option('outbrain_rater_show_recommendations',false);
add_option('outbrain_rater_self_recommendations',false);
?>