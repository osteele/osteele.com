<?php
/*
Plugin Name: SezWho
Plugin URI: http://www.SezWho.com/
Description: A plugin that allows users to rate comments for a given post and to leverage ratings of commenter on other blogs. You need a key to use this service. Get your key at <a href="http://www.SezWho.com/register.php">SezWho</a>.
Author: SezWho
Version: WP1.2
Author URI: http://www.SezWho.com
*/

/*
1. Unzip cpratecomments.zip
2. Upload the folder to your wp-content/plugins directory
3. activate via wp-admin
4. add this code to your comments.php file inside the loop:
<?php yk_get_rating(get_comment_ID()); ?>
*/
ini_set("display_errors" , false);
ini_set('allow_url_fopen', 'On');
include_once(ABSPATH.'wp-content/plugins/sezwho/cpconstants.php');
include_once(ABSPATH.'wp-content/plugins/sezwho/WPWrapper.php');
$wpwrapper = WPWrapper::getInstance();
global $comment, $cpserverurl, $cppluginurl, $wpwrapper, $comment_num, $comment_iteration_num ,$blog_id , $blog_key, $site_key , $wpdb, $comment_id, $comment_author_email_enc, $sz_user_score, $sz_comment_score, $sz_auto_option_bar, $sz_auto_comment;
$sz_auto_option_bar = 0;
$sz_auto_comment = 0;
$comment_id = 0;
$comment_iteration_num = -1 ;
$cppluginurl = get_settings('siteurl')."/wp-content/plugins/sezwho";
// this will create the yk plugin schema when the plugin is activated
add_action('activate_sezwho/cpratecomments.php', 'yk_plugin_create_schema');

function yk_plugin_create_schema () {
	global $wpwrapper;
	global $cpserverurl;
	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	global $pluginversion ;
	global $jstagname;
	$existingpluginversion = get_option('CPPLUGINVERSION');

	if(!$existingpluginversion || $pluginversion > $existingpluginversion)
	{
		$sql_site = " CREATE TABLE IF NOT EXISTS sz_site (
			    site_key varchar(32) NOT NULL DEFAULT '' ,
			    plugin_version varchar(6) NOT NULL ,
			    site_url VARCHAR(255) ,
			    rating_verification VARCHAR(16) ,
			    CONSTRAINT site_pk PRIMARY KEY (site_key)
			    ) type=InnoDB ; " ;

		$wpwrapper->yk_maybe_create_table("sz_site", $sql_site);

		$sql_blog = "CREATE TABLE IF NOT EXISTS sz_blog (
			    blog_id int(8) NOT NULL,
			    blog_key varchar(32),
			    blog_url VARCHAR(255) ,
			    blog_title VARCHAR(255) ,
			    blog_subject VARCHAR(255) ,
			    display_template VARCHAR(16) ,
			    language VARCHAR(16) ,
			    site_key varchar(32) NOT NULL,
			    CONSTRAINT blog_pk PRIMARY KEY (blog_id),
				INDEX site_key_index (site_key),
				FOREIGN KEY (site_key) REFERENCES sz_site(site_key),
			    INDEX blog_idx2(blog_key)) type=InnoDB;";

		$wpwrapper->yk_maybe_create_table("sz_blog", $sql_blog);

		$sql_email = "CREATE TABLE IF NOT EXISTS sz_email (
			    email_address VARCHAR(255) NOT NULL,
			    yk_score float ,
			    global_name VARCHAR(255) ,
			    encoded_email VARCHAR(255),
			    CONSTRAINT email_pk PRIMARY KEY (email_address)) type=InnoDB;" ;

		$wpwrapper->yk_maybe_create_table("sz_email", $sql_email);


		$sql_comment = "CREATE TABLE IF NOT EXISTS sz_comment (
			    blog_id int(8) NOT NULL,
			    posting_id int(8) NOT NULL,
			    comment_id int(8) NOT NULL,
			    creation_date DATE,
			    comment_rating float ,
			    comment_score float ,
			    raw_score float ,
			    rating_count int(8) ,
			    email_address VARCHAR(255) NOT NULL,
			    exclude_flag VARCHAR(1),
			    CONSTRAINT comment_pk PRIMARY KEY (blog_id, posting_id, comment_id)
			    ) type=InnoDB;";

		$wpwrapper->yk_maybe_create_table("sz_comment", $sql_comment);

		$sql_blog_user = "CREATE TABLE IF NOT EXISTS sz_blog_user (
			    blog_id int(8) NOT NULL,
			    screen_name VARCHAR(255) NOT NULL,
			    email_address VARCHAR(255) NOT NULL,
			    yk_score float ,
			    CONSTRAINT email_pk PRIMARY KEY (blog_id, email_address),
			    CONSTRAINT email_uk UNIQUE (screen_name),
				INDEX blog_id_index (blog_id),
			    FOREIGN KEY (blog_id) REFERENCES sz_blog(blog_id),
				INDEX email_address_index (email_address),
			    FOREIGN KEY (email_address) REFERENCES sz_email(email_address)) type=InnoDB;" ;

		$wpwrapper->yk_maybe_create_table("sz_blog_user", $sql_blog_user);
	}
	update_option('CPPLUGINVERSION',$pluginversion);
	//Call Install Plugin Web Service
	$siteurl = get_settings('siteurl');
	//Get the site key from plugin database
	$sitequery = "select site_key from sz_site ";
	$row = $wpwrapper->yk_get_row($sitequery) ;

	$sitekey = $row->site_key;
	$response = cp_http_post("",  $cpserverurl, "/webservices/ykinstallplugin.php?site_key=$sitekey&pluginversion=$pluginversion&remoteurl=".urlencode($siteurl), 80);
	//$response = file_get_contents($cpserverurl."/webservices/ykinstallplugin.php?site_key=$sitekey&pluginversion=$pluginversion&remoteurl=".urlencode($siteurl));
	$resultArr = array();
	$returned_values = explode(',', $response); // split at the commas
	foreach($returned_values as $item) {
		list($key, $value) = explode('=', $item, 2); // split at the =
		$resultArr[$key] = $value;
	}
	$sitequery;
	if($resultArr['SUCCESS'] == "Y")
	{
		//update the plugin database
		if($resultArr['SITEKEY'])
		{
			//insert site detail
			$sitequery ="insert into  sz_site (site_key,plugin_version,site_url) values('".$resultArr['SITEKEY']."','$pluginversion','$siteurl')";
		}
		else
		{
			//update site details when plugin is reinstalled
			$sitequery = "update sz_site set plugin_version ='$pluginversion'  where site_key ='$sitekey'";
		}
		$wpwrapper->yk_query($sitequery);
	}
	else
	{
		if($resultArr['ERRORMSGCODE'] == "SYSERR")
		{
			update_option('YKPLUGIN_INSTALL_ERR', "System error . Plugin activation failed");
		}
		else if($resultArr['ERRORMSGCODE'] == 'SITEKEYERR')
		{
			update_option('YKPLUGIN_INSTALL_ERR', "Wrong sitekey  . SezWho  plugin activation failed");
		}
	}
}

/****************************************************************************************************/
// Adding action to add warning/err message to plugin
add_action('admin_footer', 'youkarma_warning');
add_action('admin_menu', 'yk_pluginconfig');
/****************************************************************************************************/

function youkarma_warning() {
	$ykpluginerr = get_option('YKPLUGIN_INSTALL_ERR');
	if($ykpluginerr)
	{
		echo "
		<div id='youkarma_warning' class='updated fade-ff0000'><p><strong>".__($ykpluginerr)."</strong></p></div>
		<style type='text/css'>
		#adminmenu { margin-bottom: 5em; }
		#youkarma_warning { position: absolute; top: 7em; }
		</style>
		";
	}
	else if(get_option('CPBLOGKEY') =="" || get_option('CPBLOGKEY') == NULL)
	{
		echo "
		<div id='youkarma_warning' class='updated fade-ff0000'><p><strong>".__('SezWho plugin is not yet ready for use. Please enter an access key for your blog by clicking ')."<a href='".get_settings('siteurl')."/wp-admin/plugins.php?page=youkarma-key-config'> here<a>"."</strong></p></div>
		<style type='text/css'>
		#adminmenu { margin-bottom: 5em; }
		#youkarma_warning { position: absolute; top: 7em; }
		</style>
		";
	}
	update_option('YKPLUGIN_INSTALL_ERR', '');
}

function yk_pluginconfig()
{
	global $wpdb;
	if ( function_exists('add_submenu_page') ) {
		add_submenu_page('plugins.php', __('SezWho Configuration'), __('SezWho Configuration'), 'manage_options', 'youkarma-key-config', 'youkarma_conf');
	}
}

function youkarma_conf()
{
	global $wpwrapper;
	global $cpserverurl;
	$siteurl=get_option('siteurl');
	$home=get_option('home');
	//Validate Key
	if ( isset($_POST['key']) && isset($_POST['submit'])) {
		$blogkey = trim($_POST['key']);
		$sitequery = "select site_key from sz_site ";
		$row = $wpwrapper->yk_get_row($sitequery) ;
		$sitekey = $row->site_key;
		$blogtitle = get_option('blogname');
		$blogsubject = get_option('blogdescription');
		$blogid = 0;// Revisit later
		$blogtitle   = addslashes($blogtitle) ;
		$blogsubject = addslashes($blogsubject) ;
		$response = cp_http_post("",  $cpserverurl, "/webservices/ykactivateblogkey.php?sitekey=$sitekey&blogtitle=".urlencode($blogtitle)."&blogid=$blogid&blogkey=$blogkey&blogurl=$siteurl&homeurl=$home", 80);
		//$response =file_get_contents($cpserverurl."/webservices/ykactivateblogkey.php?sitekey=$sitekey&blogtitle=".urlencode($blogtitle)."&blogid=$blogid&blogkey=$blogkey&blogurl=$siteurl");
		$returned_values = explode(',', $response); // split at the commas
		$resultArr = array();
		foreach($returned_values as $item) {
			list($key, $value) = explode('=', $item, 2); // split at the =
			$resultArr[$key] = $value;
		}
		if($resultArr['SUCCESS']== 'N')
		{
			if($resultArr['ERRORMSGCODE'] == 'SYSERR')
			{
				echo("<div class='updated fade-ff0000'>SezWho Server Error</div>");
			}
			else if($resultArr['ERRORMSGCODE'] == 'BLOGKEYERR')
			{
				echo("<div class='updated fade-ff0000'>Invalid Blog Key</div>");
			}
			else if($resultArr['ERRORMSGCODE'] == 'SITEKEYERR')
			{
				echo("<div class='updated fade-ff0000'>Invalid Site Key</div>");
			}
			else if($resultArr['ERRORMSGCODE'] == 'ALREADYACTIVATED')
			{
				echo("<div class='updated fade-ff0000'>Blog Key Already Activated</div>");
			}
		}
		else if($resultArr['SUCCESS']== 'Y')
		{

			echo("<div class='updated fade-ff0000'>Blog Key Successfully  Activated</div>");
			$blog_query="select count(*) as bcount from sz_blog  where blog_key='$blogkey'";
			$row = $wpwrapper->yk_get_row($blog_query) ;
			$blog_num = $row->bcount;
			if($blog_num == 0)
			{
				$insert_blog_query ="insert into sz_blog(blog_id,blog_key,blog_url,
				blog_title,blog_subject,site_key) values ('$blogid','$blogkey','$siteurl','$blogtitle','$blogsubject','$sitekey')";
				$wpwrapper->yk_query($insert_blog_query);
				update_option('CPBLOGKEY', $blogkey);
			}

			if($_POST['syncblog'])
			{
				echo("<div class='updated fade-ff0000'>Syncronizing your existing comments, Please wait. This step can take a few minutes depending on the number of comments you have</div>");
				$blogsyncurl = get_settings('siteurl')."/wp-content/plugins/sezwho/syncblog.php";
				include($blogsyncurl);
			}
		}
	}
	$blogkey=get_option('CPBLOGKEY');
	if($blogkey) {
		echo("<div class='updated fade-ff0000'>Blog activated . Your key is ".$blogkey."</div>");
	}
	echo("<div class='wrap'>");
	echo("<h2>SezWho Configuration</h2> &nbsp; If you don't have a blog key, <a target ='_blank' href='".$cpserverurl."/register.php'>get it here</a>");
	echo("<form action='' method='post' >");
	echo("<table class='optiontable'>");
	echo("<tr valign='top'>");
	echo("<th scope='row'>Blog Key:</th>");
	echo("<td><input id='key' name='key' type='text' size='15' /></td></tr>");
	echo("<tr valign='top'>");
	echo("<th scope='row'>Synchronize:</th>");
	echo("<td><input type='checkbox' name='syncblog' checked />");
	echo("<br />Sync allows you to get the latest user reputation data and to enable ratings for existing comments.");
	echo("</td></tr>");
	echo("</table>");
	echo("<p class='submit'><input type='submit' onclick=\"this.style.display='none';\" name='submit' value='Activate Service' /></p></form>");
	echo("</div>");
}

/****************************************************************************************************/
// plugin the post comment interface API here
add_action('comment_post', 'yk_postComment');

/****************************************************************************************************/

function yk_postComment($comment_id){
	// Explore how to get the blog_id from here!
	//print_r('blog id = '.$wpdb->blog_id);
	global  $wpwrapper;
	global $cpserverurl;
	global $wpdb;

	$query= "select blog_id,blog_key,site_key from sz_blog";
	$row = $wpwrapper->yk_get_row($query) ;
	$blog_id=$row->blog_id;
	$blog_key=$row->blog_key;
	$site_key = $row->site_key;

	$commentData = get_commentdata((int)$comment_id , 1, true);
	$comment_approved = $commentData["comment_approved"] ;
	$comment_author_email = $commentData["comment_author_email"] ;

	// if comment is not approved OR commenter email is not provided, return without processing
	if ($comment_approved === 'spam' || $comment_author_email === "") {
		return ;
	}

	$comment_author_url = $commentData["comment_author_url"];
	$postData = get_postdata($commentData[comment_post_ID]);
	$comment_id =   $commentData["comment_ID"] ;
	$post_id =   $commentData["comment_post_ID"] ;
	$post_url = get_settings('home').'/?p='.$post_id ;
	$comment_url = $post_url.'#comment-'.$comment_id ;
	$post_content = $postData["Title"] ;
	$post_intro = "" ;
	$comment_intro = "" ;
	// process post contents and comment contents and get 30 characters as the post_intro and comment_intro respectively
	if ($post_content) {
		$post_intro = substr($post_content, 0, 30);
	}

	$comment_content = $commentData["comment_content"] ;
	if ($comment_content) {
		$comment_intro = substr($comment_content, 0, 45);
	}

	//$cat_query = "SELECT cat_name from ".$wpdb->prefix."categories where cat_ID IN (select category_id from ".$wpdb->prefix."post2cat WHERE post_id = $post_id); ";
	$cat_query = "SELECT cat_name from ".$wpdb->prefix."categories, ".$wpdb->prefix."post2cat where cat_ID = category_id and post_id = $post_id;";
	$cat_result = mysql_query($cat_query);
	$categories = "" ;
	$co = 0 ;
	while ($cat = mysql_fetch_assoc($cat_result)) {
		$categories.= $cat["cat_name"]."," ;
	}
	$categories = substr($categories, 0, strlen($categories) - 1);

	// get plugin version
	$plugin_version_query = "SELECT plugin_version from sz_site; ";
	$plugin_version = $wpwrapper->yk_get_var($plugin_version_query);
	// assume plugin version is of the type MT1.0 or WP2.1
	$platform = substr($plugin_version, 0, 2) ;
	$version= substr($plugin_version, 2) ;
	/* call the webservice here - enocode the introductions as they may contain special characters */
	$content = cp_http_post("", $cpserverurl, "/webservices/ykwebservice_front.php?method=postComment&site_key=$site_key&blog_id=$blog_id&blog_key=$blog_key&posting_id=$post_id&posting_url=".urlencode($post_url)."&posting_intro=".urlencode($post_intro)."&comment_id=$comment_id&comment_url=".urlencode($comment_url)."&comment_intro=".urlencode($comment_intro)."&email_address=".$comment_author_email."&screen_name=nothing&comment_author_url=".urlencode($comment_author_url)."&plugin_version=$version&posting_tags=".urlencode($categories), 80);
	$postcomment_ws_result =trim(substr($content,strpos($content,"CPRESPONSE")+10,strlen($content)));
	$returned_values = explode(',', $postcomment_ws_result); // split at the commas
	$resultArr = array();
	foreach($returned_values as $item) {
		list($key, $value) = explode('=', $item, 2); // split at the =
		$resultArr[$key] = $value;
	}

	if ($resultArr["Success"] == 'Y') { // The webservice call has been a success, hence insert/update the plugin schema
		// Query row from email using email_address
		$email_query = "select email_address from sz_email where email_address = '".$comment_author_email."';" ;
		$email_res = $wpwrapper->yk_get_var($email_query) ;
		$email_res_count = $wpwrapper->yk_num_rows($email_query) ;
		if ($email_res_count == 1) { // this row already exists, hence update it
			$update_email_query = "update sz_email set yk_score = '".$resultArr["YKScore"]."' and global_name = '".$resultArr["Global_Name"]."' where email_address = '".$comment_author_email."';" ;
			$wpwrapper->yk_query($update_email_query);
		} else { // this row does not exist, hence insert it
			$insert_email_query = "insert into sz_email (email_address, yk_score, global_name, encoded_email) values ( '".$comment_author_email."' , '".$resultArr["YKScore"]."' , '".$resultArr["Global_Name"]."', '".$resultArr["encoded_email"]."');" ;
			$wpwrapper->yk_query($insert_email_query);
		}
		// now insert comment
		$yk_score =  $resultArr["YKScore"] ; // Get the YK Score returned by the webservice
		$raw_score=($yk_score-5)*10;
		$comment_score;
		if($raw_score >=1)
		{
			$comment_score =log($raw_score,10)+5;
		}
		else if($raw_score <= -1)
		{
			$comment_score = (-1*log(-1*$raw_score, 10))+5;
		}
		else {
			$comment_score=5;
		}
		$insert_comment_query ;
		if ($yk_score != null) {
			$insert_comment_query = "insert into sz_comment (blog_id, posting_id, comment_id, email_address, rating_count, comment_score,raw_score) values ('".$blog_id."', '".$post_id."', '".$comment_id."', '".$comment_author_email."' , '0' , ".$comment_score.",".$raw_score." )";
		} else {
			$insert_comment_query = "insert into sz_comment (blog_id, posting_id, comment_id, email_address) values ('".$blog_id."', '".$post_id."', '".$comment_id."', '".$comment_author_email."' )";
		}

		$wpwrapper->yk_query($insert_comment_query);
		$email_query = "select email_address from sz_email where email_address = '".$comment_author_email."';" ;
		$email_res = $wpwrapper->yk_get_var($email_query) ;
		$email_res_count = $wpwrapper->yk_num_rows($email_query) ;
		if ($email_res_count == 1) { // this row already exists, hence update it
			$update_email_query = "update sz_email set yk_score = '".$yk_score."' , global_name = '".$resultArr["Global_Name"]."' where email_address = '".$comment_author_email."';" ;
			$wpwrapper->yk_query($update_email_query);
		} else { // this row does not exist, hence insert it
			$insert_email_query = "insert into sz_email values ( '".$comment_author_email."' , '".$yk_score."' , '".$resultArr["Global_Name"]."');" ;
			$wpwrapper->yk_query($insert_email_query);
		}
	} else { // not to be done right now !

	}
}

/****************************************************************************************************/
// this is to insert a hidden image in each comment to identify where to insert nodes in the JS DOM w.r.t. this
add_filter('comment_text', 'yk_comment_text');
/****************************************************************************************************/

function yk_comment_text($content) {
	global $comment, $cpserverurl, $cppluginurl, $wpwrapper, $comment_num, $comment_iteration_num ,$blog_id , $blog_key, $site_key , $wpdb, $comment_id, $comment_author_email_enc, $sz_user_score, $sz_comment_score, $sz_auto_option_bar, $sz_auto_comment;

	yk_comment_data_gen();

	// call to include generated Javascript
	if ($comment_iteration_num == 0) insert_header_js_code();

	$content = $content.'<script type="text/javascript" id="szCommentHiddenTag:'.$comment_id.'">';

	if ($comment_iteration_num == 0)  $content = $content.' var sz_comment_data_array = [];';

	$content = $content.'sz_comment_data_array['.$comment_iteration_num.']= {comment_id:"'.$comment_id.'", comment_author:"'.$comment->comment_author.'", comment_author_url:"'.$comment->comment_author_url.'", comment_author_email:"'.$comment_author_email_enc.'",sz_score:"'.$sz_user_score.'",comment_score:"'.$sz_comment_score.'"};';

	if ($comment_iteration_num == $comment_num - 1)
	$content = $content.'var sz_js_config_params = {cppluginurl:"'.$cppluginurl.'",cpserverurl:"'.$cpserverurl.'", sitekey:"'.$site_key.'",blogkey:"'.$blog_key.'", blogid:"'.$blog_id.'",post_id:"'.$comment->comment_post_ID.'", comment_rating_submit_path:"/cpratingsubmit.php",proxy_path:"/profile.php?",sortOrder:"'.$sortOrder.'",sz_auto_comment:'.$sz_auto_comment.',sz_auto_option_bar:'.$sz_auto_option_bar.',comment_number:'.$comment_num.', sz_comment_data:sz_comment_data_array};';

	$content = $content.'</script>';
	return $content ;
}

function yk_comment_data_gen() {
	global $comment, $cpserverurl, $cppluginurl, $wpwrapper, $comment_num, $comment_iteration_num ,$blog_id , $blog_key, $site_key , $wpdb, $comment_id, $comment_author_email_enc, $sz_user_score, $sz_comment_score;

	if ($comment_id == $comment->comment_ID) return;

	$post_id = $comment->comment_post_ID ;
	$comment_id = $comment->comment_ID ;
	// call to populate the data that was originally getting passed in the footer
	if ($comment_iteration_num == -1) {
		$query= "select blog_id, blog_key, site_key from sz_blog";
		$row = $wpwrapper->yk_get_row($query) ;
		$blog_id = $row->blog_id;
		$blog_key = $row->blog_key;
		$site_key = $row->site_key;
		//$comments_query = "select comment_id, comment_author_email from ".$wpdb->prefix."comments where comment_post_id = '$post_id' order by comment_date asc;" ;
		//$comments = $wpwrapper->yk_get_results($comments_query);
		//$comment_num = $wpwrapper->yk_num_rows($comments_query);
		$comment_num = get_comments_number($post_id);
		$sortOrder = $_GET["sortOrder"] ;
	}
	if ($comment->comment_author_email != ''){
		$yk_score_query = "select yk_score,encoded_email from sz_email where email_address = '$comment->comment_author_email'; " ;
		$row = $wpwrapper->yk_get_row($yk_score_query) ;
		$sz_user_score = number_format($row->yk_score, 1);
		$comment_author_email_enc = $row->encoded_email;
		$comment_score_query = "select comment_score from sz_comment where comment_id = '$comment_id' and posting_id = '$post_id' and blog_id = '0'; " ;
		$sz_comment_score = number_format($wpwrapper->yk_get_var($comment_score_query) , 1);
	}
	else{
		$sz_user_score = 0;
		$comment_author_email_enc = '';
		$sz_comment_score = 0;
	}
	$comment_iteration_num = $comment_iteration_num + 1 ;
	return;
}

function insert_header_js_code() {
	global $wpwrapper ;
	global $cpserverurl ;
	global $cppluginurl ;
	global $jstagname ;

	$theme_name = get_option("template");
	$blog_query = "SELECT site_key, blog_key from sz_blog; ";
	$blog_obj = $wpwrapper->yk_get_row($blog_query);
	$blog_key = $blog_obj->blog_key ;
	$site_key = $blog_obj->site_key ;
	$plugin_version_query = "SELECT plugin_version from sz_site; ";
	$plugin_version = $wpwrapper->yk_get_var($plugin_version_query);
	// assume plugin version is of the type MT1.0 or WP2.1
	$platform = substr($plugin_version, 0, 2) ;
	$version= substr($plugin_version, 2) ;
	$script_src = "$cpserverurl/widgets/profile/js_output/$platform/$theme_name/$version/$jstagname/$site_key/$blog_key";
	//$script_src = "$cpserverurl/widgets/profile/js_generator.php?site_key=$site_key&theme=$theme_name&blog_key=$blog_key&plugin_version=$version&platform=$platform" ;
	echo "<script type='text/javascript' src='".$script_src."'></script>";
}

/****************************************************************************************************/
function yk_getCommentScore($comment_id, $post_id){
	global $wpwrapper;
	$query = "SELECT comment_score from sz_comment where posting_id = '$post_id' and comment_id = '$comment_id'";
	return $wpwrapper->yk_get_var($query);
}

function cp_comment_filter_options_bar() {
	global $sz_auto_option_bar;
	echo '<span class="szOptionBarPlaceHolder" id="szOptionBarPlaceHolder"></span>';
	$sz_auto_option_bar = 1;
}

function cp_comment_filter_options() {
	echo '<select onchange=\'javascript:cpshowFilterOptions(this)\' name=\'cpoptionselect\' style=\'width:155px;\'>'."\n";
	echo '<option value=\'0\' selected="selected">All Comments</option>'."\n";
	echo '<option value=\'5.5\'>Good Comment (3+)</option>'."\n";
	echo '<option value=\'7.5\'>Great Comments (4+)&nbsp;&nbsp;&nbsp;</option>'."\n";
	echo '</select>'."\n";
}

function cp_comment_filter_help() {
	global $cpserverurl ;
	echo '<a id=\'cpfilterhelp\' href="javascript:void window.open(\''.$cpserverurl.'/popup_help.php\', \'name\',\'height=820,width=600,resizable=no,scrollbars=no,toolbar=no\')">What is this?</a>'."\n";
}

function cp_branding() {
	global $cpserverurl ;
	$browser = $_SERVER['HTTP_USER_AGENT'];
	if(preg_match('/msie (5\.5|6)/i' , $browser)) {
		echo '<a id="cpbrandinglink" href="'.$cpserverurl.'" style="cursor:hand;display:block;width:100px;height:54px;FILTER: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.$cpserverurl.'/img/branding.png\', sizingMethod=\'scale\');text-decoration:none">&nbsp;</a>'."\n";
	} else {
		echo '<a id="cpbrandinglink" href="'.$cpserverurl.'" style="display:block;width:100px;height:54px;background:url('.$cpserverurl.'/img/branding.png);text-decoration:none">&nbsp;</a>'."\n";
	}
}

function cp_comment_header() {
	global $comment_iteration_num ,$sz_auto_comment;
	yk_comment_data_gen();
	echo '<span class="szCommentHeaderPlaceHolder" id="szCommentHeaderPlaceHolder:'.$comment_iteration_num.'"></span>';
	$sz_auto_comment = 1;
}

function cp_comment_footer() {
	global $comment_iteration_num ,$sz_comment_score;
	echo '<span class="szCommentFooterPlaceHolder" id="szCommentFooterPlaceHolder:'.$comment_iteration_num.'"></span>';
}

function cp_comment_show_hide_button() {
	global $comment ;
	global $cpserverurl ;
	$comment_id = $comment->comment_ID ;
	echo'<img id=\'yk_comment-'.$comment_id.'-showhide\' class=\'cpcommentshowhideclass\' onclick=\"javascript:toggleComment(this)\" src="'.$cpserverurl.'"/>'."\n";;
}

function cp_comment_profile_link() {
	global $cpserverurl ;
	global $cppluginurl ;
	global $wpwrapper ;
	global $comment ;
	// get blog_id, blog_key and site_key from DB
	$query= "select blog_id,blog_key,site_key from sz_blog";
	$row = $wpwrapper->yk_get_row($query) ;
	$blog_id=$row->blog_id;
	$blog_key=$row->blog_key;
	$site_key = $row->site_key;
	$commentData = get_commentdata((int)$comment->comment_ID , 1, true);
	$comment_author_email = $commentData["comment_author_email"] ;
	//$enc_comment_author_email = getHexEncryptedData($comment_author_email) ;
	$email_query = "select encoded_email from sz_email where email_address = '".$comment_author_email."';" ;
	$comment_author_email = $wpwrapper->yk_get_var($email_query) ;
	$enc_comment_author_email = urlencode($comment_author_email);

	$anchor = "a".uniqid(rand()) ;
	echo '(<a class=\'cpprofilelinkclass\' id="'.$anchor.'" name="cpprofilelink:'.$anchor.':'.$enc_comment_author_email.'">Profile</a>)' ;
	//echo '(<a class=\'cpprofilelinkclass\' id="'.$anchor.'" name="cpprofilelink:'.$anchor.':'.$enc_comment_author_email.'" href="javascript:loadMyProfileDiv(\''.$cppluginurl.'\', \''.$cpserverurl.'/webservices/ykgetprofile_if.php?site_key='.$site_key.'&blog_key='.$blog_key.'&isplugin=true&commenter_email='.$enc_comment_author_email .'\',\'profilepopup\',\''.$anchor.'\', 0);">Profile</a>)' ;
}

function cp_get_comment_score() {
	global $comment ;
	global $cppluginurl ;
	global $wpwrapper ;
	$comment_id = $comment->comment_ID ;
	$post_id = $comment->comment_post_ID ;
	$comment_author_email = $comment->comment_author_email ;
	//$yk_score_query = "select yk_score from sz_email where email_address = '$comment_author_email'; " ;
	//$yk_score = number_format($wpwrapper->yk_get_var($yk_score_query) , 1);
	$comment_score_query = "select comment_score from sz_comment where comment_id = '$comment_id' and posting_id = '$post_id' and blog_id = '0'; " ;
	$comment_score = number_format($wpwrapper->yk_get_var($comment_score_query) , 1);
	$cpplug_imagesurl = $cppluginurl.'/img' ;
	echo' <div class=\'cpcommentscoreclass\' id="yk_comment-'.$comment_id.'-commentscore" style=\'visibility:visible;display:block\'>'."\n";;
	echo '<script type=\'text/javascript\'>';
	echo 'populate_comment_star_rating("'.$comment_score.'","'.$cpplug_imagesurl.'","'.$comment_id.'");'."\n";
	echo '</script>';
	echo'</div>';
}
/****************************************************************************************************/
// this is to render the comment rating buttons etc.
add_action('wp_head', 'sz_addto_head');

/****************************************************************************************************/

function sz_addto_head() {
	global $cpserverurl ;
	echo "<link rel='stylesheet' id='szProfAndEmbedStyleSheet' href='".$cpserverurl."/css/prof.css' type='text/css' media='screen' />\n";
}
// Returns array with headers in $response[0] and body in $response[1]
function cp_http_post($request, $host, $path, $port = 80) {
	// host is of the type http://xxx.xxx.xxxx/xxxx
	global $wp_version;
	$cpserver= substr($host, 7);
	$cpserverparams = split( "/" , $cpserver);
	if (strpos($cpserver, stristr($cpserver , ':')) > 0) {
		$cpserver_port_arr = split (':' , $cpserverparams[0]) ;
		$cpserver = $cpserver_port_arr[0] ;
		//$cpport = $cpserver_port_arr[1] ;
	} else {
		$cpserver = $cpserverparams[0] ;
		//$cpport = 80 ;
	}
	$cpserverpath = $cpserverparams[1].$path;
	$http_request  = "POST /$cpserverpath HTTP/1.0\r\n";
	$http_request .= "Host: $cpserver\r\n";
	$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . get_option('blog_charset') . "\r\n";
	$http_request .= "Content-Length: " . strlen($request) . "\r\n";
	$http_request .= "User-Agent: WordPress/$wp_version | SezWho/2.0\r\n";
	$http_request .= "\r\n";
	$http_request .= $request;

	$response = '';
	if( false != ( $fs = @fsockopen($cpserver, $port, $errno, $errstr, 10) ) ) {
		fwrite($fs, $http_request);
		while ( !feof($fs) ) {
			$response .= fgets($fs, 1160); // One TCP-IP packet
		}
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);
		return $response[1];
	}
	return $response;
}
?>