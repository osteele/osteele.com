<?php
global $wpdb;
global $httppostdata;
ini_set('display_errors', false);
error_reporting(1);

if ( !defined('ABSPATH') )
{
	// include wp-config as it has the ABSPATH initialization
	require_once('../../../wp-config.php');
}
include_once(ABSPATH.'wp-content/plugins/sezwho/WPWrapper.php');
include_once(ABSPATH.'/wp-content/plugins/sezwho/cpconstants.php');

$blogurl = get_settings("home") ;
$sz_sync_block_size = 100;
$cpserver=substr($cpserverurl, 7 ,strlen($cpserverurl));
$wp_prefix = $wpdb->prefix ;
$wpwrapper = WPWrapper::getInstance();

// Get Site Key
$sitequery = "select site_key from sz_site ";
$row = $wpwrapper->yk_get_row($sitequery) ;
$sitekey = $row->site_key;
if(!$sitekey){
	echo("<div class='updated fade-ff0000'>Error , site key not found</div>");
	exit();
}

// Get Blog Key
$blogquery ="select blog_key from sz_blog ";
$row = $wpwrapper->yk_get_row($blogquery) ;
$blogkey = $row->blog_key;
if(!$blogkey) {
	echo("<div class='updated fade-ff0000'>Error , blog key not found</div>");
	exit();
}

//Get the number of comments to sync
$total_comments_to_sync_query = "select a.comment_ID, a.comment_post_ID, a.comment_author_url, a.comment_author_email, a.comment_content, a.comment_date  from ".$wp_prefix."comments a LEFT JOIN sz_comment b ON a.comment_ID = b.comment_id WHERE b.comment_id IS NULL AND a.comment_approved = '1' AND a.comment_author_email != ''" ;
$total_comments_to_sync_result = mysql_query($total_comments_to_sync_query);
$total_comments_to_sync_num = mysql_num_rows($total_comments_to_sync_result);
if (!$total_comments_to_sync_num) {
	echo("Nothing to sync" );
	echo("<br>");
	exit();
}

//Get the existing Post Ids in the comment table in an array
$ep_id_q = "select distinct posting_id from sz_comment;";
$ep_id_q_r = mysql_query($ep_id_q);
$p_db_data = array();
while ($obj = mysql_fetch_object($ep_id_q_r)) {
	$p_db_data[$obj->posting_id] = 1;
}

//Get the existing emails in the comment table in an array
$ee_id_q = "select distinct email_address from sz_comment;";
$ee_id_q_r = mysql_query($ee_id_q);
$e_db_data = array();
while ($obj = mysql_fetch_object($ee_id_q_r)) {
	$e_db_data[$obj->email_address] = 1;
}

$tosync = $total_comments_to_sync_num;

echo("To Process: ".$total_comments_to_sync_num." comments" );
echo("<br>");

$from = 1 ;
while ($tosync > 0) {
	$s_chunk = ($tosync > $sz_sync_block_size)? $sz_sync_block_size : $tosync;
	$posting_num = 0;
	$email_num = 0;
	$httppostdata = array();

	if($tosync > $sz_sync_block_size) {
		echo("Processing comments from ".$from." to ".($from + $sz_sync_block_size - 1));
		$from = $from + $sz_sync_block_size;
	} else {
		echo("Processing ".$tosync. " comment" );
	}
	echo("<br>");
	for ($i=0 ; $i < $s_chunk ; $i++)
	{
		$obj = mysql_fetch_object($total_comments_to_sync_result);
		// Insert the comment
		$comment_query= "INSERT INTO sz_comment (blog_id, posting_id, comment_id, creation_date, comment_rating, comment_score, raw_score, rating_count, email_address, exclude_flag) VALUES (0,".$obj->comment_post_ID.",".$obj->comment_ID.", '".$obj->comment_date."' , NULL, NULL, NULL, NULL , '".$obj->comment_author_email."','S');";
		mysql_query($comment_query);
		// add the comments to the http array
		sz_dump_comment($i, $obj, $blogurl);

		// See if the post needs to be inserted
		if (!array_key_exists ($obj->comment_post_ID, $p_db_data)) {
			sz_dump_post($posting_num, $obj->comment_post_ID, $blogurl, $wp_prefix);
			$p_db_data[$obj->comment_post_ID] = 1;
			$posting_num++;
		}

		// See if the email needs to be inserted
		if ($obj->comment_author_email != '' && !array_key_exists ($obj->comment_author_email, $e_db_data)) {
			$httppostdata["EMAIL-".$email_num] = $obj->comment_author_email;
			$e_db_data[$obj->comment_author_email] = 1;
			$email_num++;
		}
	}
	$httppostdata["POSTCOUNT"] = $posting_num ;
	$httppostdata["COMMENTCOUNT"] = $s_chunk ;
	$httppostdata["EMAILCOUNT"] = $email_num;
	$httppostdata["sitekey"]= $sitekey;
	$httppostdata["blogkey"]= $blogkey;
	$httppostdata["blogid"]= 0;

	$response = post_httppostdata($cpserver, $debug_file);
	sz_process_response($response);
	$tosync = $tosync - $sz_sync_block_size;
}
echo("Processed ".$total_comments_to_sync_num." comments" );

function sz_dump_comment($index, $comment_obj, $blogurl)
{
	global $httppostdata;
	$httppostdata["COMMENTPOSTID-".$index] = $comment_obj->comment_post_ID ;
	$httppostdata["COMMENT-".$index] = $comment_obj->comment_ID ;
	$httppostdata["COMMENTDATE-".$index] = $comment_obj->comment_date ;
	$httppostdata["COMMENTAUTHORURL-".$index] = $comment_obj->comment_author_url ;
	$httppostdata["COMMENTAUTHOREMAIL-".$index] = $comment_obj->comment_author_email ;
	$httppostdata["COMMENTURL-".$index] = $blogurl.'/?p='.$comment_obj->comment_post_ID."#".$comment_obj->comment_ID ;
	$httppostdata["COMMNETINTRO-".$index] = substr($comment_obj->comment_content, 0, 45);
}

function sz_dump_post($index, $pid, $blogurl, $wp_prefix)
{
	global $httppostdata;
	$p_r = mysql_query("SELECT post_title from ".$wp_prefix."posts where ID = '".$pid."';");
	$pobj = mysql_fetch_object($p_r);

	$httppostdata["POSTID-".$index] = $pid ;
	$httppostdata["POSTURL-".$index] = $blogurl.'/?p='.$pid ;
	$httppostdata["POSTTITLE-".$index] = $pobj->post_title ;
	// Handle the categories
	$category_query = "SELECT cat_name from ".$wp_prefix."categories, ".$wp_prefix."post2cat where cat_ID = category_id and post_id ='".$pid."';";
	$cat_result = mysql_query($category_query);
	$cat_rows = mysql_num_rows($cat_result);
	$categories = "";
	$j = 0 ;
	while ( $cat_obj = mysql_fetch_object($cat_result)) {
		$categories .= $cat_obj->cat_name;
		if ($j != $cat_rows -1) {
			$categories .= ",";
		}
		$j++ ;
	}
	$httppostdata["POSTTAGS-".$index] = $categories ;
}

function post_httppostdata($cpserver, $debug_file)
{
	global $httppostdata ;
	$keys = array_keys($httppostdata) ;
	$key ;
	$data ;
	for($i =0 ; $i < count($keys) ; $i++)
	{
		$key = $keys[$i];
		$data = $data.$key."=".$httppostdata[$key]."&";
	}
	$eol = "\r\n";
	$errno = 0;
	$errstr = '';
	$fid = fsockopen($cpserver, 80, $errno, $errstr, 90);
	if ($fid)
	{
		$http_request  = "POST /webservices/yksyncblogservice.php HTTP/1.0\r\n";
		$http_request .= "Host: ".$cpserver."\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded; \r\n";
		$http_request .= "Content-Length: ".strlen($data)."\r\n";
		$http_request .= "\r\n";
		$http_request .= $data;
		//echo ($http_request);
		fwrite($fid, $http_request);
		$content = "";
		while (!feof($fid)) {
			$content .= fgets($fid, 1160);
		}
		fclose($fid);
		$response =trim(substr($content,strpos($content,"CPRESPONSE")+10,strlen($content)));
		return $response;
	}
	return null;
}

function sz_process_response($response)
{
	$returned_values = explode('|', $response) ; // split at the commas
	if(trim($returned_values[0]) != "SUCCESS=N") {
		foreach($returned_values as $item)
		{
			$row = explode(',',$item);
			$cols = array();
			foreach($row as $item2) {
				list($key, $value) =  explode('=', $item2);
				$cols[$key] = $value;
			}
			$email =$cols['EMAIL'];
			$ykscore =$cols['YKSCORE'];
			$globalname =$cols['GLOBALNAME'];
			$encoded_email = $cols['ECRYPTED_EMAIL'];
			if($email != '') {
				$insquery ="Insert into sz_email (email_address,yk_score,global_name, encoded_email) values ('".$email."','".$ykscore."','".$globalname."', '".$encoded_email."')";
				mysql_query($insquery);
			}
		}
		//Compute and update comment score
		$raw_score_query ="update sz_comment ,sz_email set sz_comment.raw_score=(sz_email.yk_score-5)*10 where sz_comment.exclude_flag='S' and sz_comment.email_address=sz_email.email_address;";
		mysql_query($raw_score_query);
		$comment_score_query = "update sz_comment set comment_score=log(10,raw_score)+5  where exclude_flag='S' and sz_comment.raw_score > 1 ";
		mysql_query($comment_score_query);
		$comment_score_query = "update sz_comment set comment_score=-1*log(10,raw_score*-1)+5  where exclude_flag='S' and sz_comment.raw_score < 1 ";
		mysql_query($comment_score_query);
		$comment_score_query = "update sz_comment set comment_score=5  where exclude_flag='S' and sz_comment.raw_score >= -1 and sz_comment.raw_score <= 1";
		mysql_query($comment_score_query);
		// Fix the exclude flag
		$exculde_flag_update_query ="update sz_comment set exclude_flag = NULL where  exclude_flag ='S' ";
		mysql_query($exculde_flag_update_query);
	} else {
		$msgarr = explode('=',$returned_values[1]);
		$errmsg ;
		if(trim($msgarr[1]) =='SITEKEYERR')
		{
			$msgerr = 'Wrong site key !';
		}
		if(trim($msgarr[1]) =='BLOGKEYERR')
		{
			$msgerr = 'Wrong blog key !';
		}
		if(trim($msgarr[1]) =='NOCOMMENT')
		{
			$msgerr = 'No comment to sync !';
		}
		if(trim($msgarr[1]) == 'SYSERR')
		{
			$msgerr = ' System error ';
		}
		echo("<div class='updated fade-ff0000'>".$msgerr."</div>");
		$delete_comment_query ="delete from sz_comment where  exclude_flag ='S'";
		mysql_query($delete_comment_query);
	}
}
?>