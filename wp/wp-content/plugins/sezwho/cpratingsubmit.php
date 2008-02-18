<?php
// class to handle DB stuff on the YK server side
include_once("WPWrapper.php");
include_once(ABSPATH.'/wp-content/plugins/sezwho/cpconstants.php');
$wpwrapper = WPWrapper::getInstance();
$params = file_get_contents("php://input") ;
parse_str($params, $ajax_post_params);

$post_id = $ajax_post_params["postID"] ;
$comment_id = $ajax_post_params["commentID"]  ;
$rater_email = $ajax_post_params["emailID"]  ;
$rating = $ajax_post_params["ratingIncrement"]  ;

// pass these parameters into the method that will call the postRating webservice
$ajax_rating_response = processRequestDataAndInvokePostRatingWebservice($post_id, $comment_id, $rater_email, $rating) ;
echo ($ajax_rating_response) ;

function processRequestDataAndInvokePostRatingWebservice ($post_id, $comment_id, $rater_email, $rating) {
	global  $wpwrapper;
	global  $cpserverurl;
	$query= "select blog_id,blog_key,site_key from sz_blog";
	$row = $wpwrapper->yk_get_row($query) ;
	$blog_id=$row->blog_id;
	$blog_key=$row->blog_key;
	$site_key = $row->site_key;
	$postrating_ws_result ;
	$site_verification_query = "select rating_verification from sz_site where site_key = '".$site_key."';";
	$site_verification_query_res = $wpwrapper->yk_get_var($site_verification_query) ;
	if ( $wpwrapper->yk_num_rows($site_verification_query) == 0) {
		// log and return "no site data found error"
		$postrating_ws_result = 'Status=Failure,ErrorMsgCode=NoSiteKey';
		return $postrating_ws_result ;
	}
	$blog_query = "select blog_key from sz_blog where blog_id = '".$blog_id."';";
	$blog_query_res = $wpwrapper->yk_get_results($blog_query) ;
	$blog_query_count = 0 ;
	if ($wpwrapper->yk_num_rows($blog_query) == 0) {
		// log and return "no blog key found error"
		$postrating_ws_result = 'Status=Failure,ErrorMsgCode=NoBlogKey';
		return $postrating_ws_result ;
	}
	// get plugin version
	$plugin_version_query = "SELECT plugin_version from sz_site; ";
	$plugin_version = $wpwrapper->yk_get_var($plugin_version_query);
	// assume plugin version is of the type MT1.0 or WP2.1
	$platform = substr($plugin_version, 0, 2) ;
	$version= substr($plugin_version, 2) ;
	$email_query = "select email_address from sz_comment where comment_id = '".$comment_id."';" ;
	$email_res = $wpwrapper->yk_get_var($email_query) ;
	if($email_res == $rater_email){
		// log and return "self rating error"
		$postrating_ws_result = 'Status=Failure,ErrorMsgCode=SelfRating';
		return $postrating_ws_result ;
	} else {
		//$postrating_ws_result = file_get_contents($cpserverurl."/webservices/ykwebservice_front.php?method=postRating&site_key=$site_key&blog_id=$blog_id&blog_key=$blog_key&posting_id=$post_id&comment_id=$comment_id&rating=$rating&email_address=$rater_enc_email");
		$postrating_ws_result = cp_http_post("",  $cpserverurl, "/webservices/ykwebservice_front.php?method=postRating&site_key=$site_key&blog_id=$blog_id&blog_key=$blog_key&posting_id=$post_id&comment_id=$comment_id&rating=$rating&email_address=$rater_email&plugin_version=$version", 80);
	}
	// Strip CPRESPONSE from the webservice returned response
	$postrating_ws_result = substr(trim($postrating_ws_result) , 10);
	$returned_values = explode(',', $postrating_ws_result); // split at the commas
	$resultArr = array();
	foreach($returned_values as $item) {
		list($key, $value) = explode('=', $item, 2); // split at the =
		$resultArr[$key] = $value;
	}
	// update rater's yk score
	$rater_ykscore = $resultArr["YKScore"] ;
	if ($rater_ykscore != null) {
		$update_email_query = "update sz_email set yk_score = '".$rater_ykscore."' where email_address = '".$rater_email."';" ;
		$wpwrapper->yk_query($update_email_query);
	}
	// get the status for any further processing
	$status = $resultArr["Status"] ;
	if ($status == "Success") {
		if (($site_verification_query_res == 'None' || $site_verification_query_res == 'Site-Verified')
		& $rater_ykscore == ''){
			$rater_ykscore = 5 ; // in this case setting rater yk-score to default score
		}
		//update latest commenter score
		$commneterYkScore= $resultArr["CommenterYKscore"];
		$commneterEmail= $resultArr["CommenterEmail"];
		if($commneterYkScore != null)
		{
			$updateYkScore ="update sz_email set yk_score =$commneterYkScore where email_address='$commneterEmail'";
			$wpwrapper->yk_query($updateYkScore);
		}
		if($rater_ykscore != null){
			$comment = $wpwrapper->yk_get_row("SELECT * FROM sz_comment WHERE comment_id='$comment_id' and posting_id='$post_id' and blog_id = '$blog_id' LIMIT 1;");
			$raw_score = $comment->raw_score;
			$new_raw_score= $rater_ykscore*($rating-5) + $raw_score;
			$new_rating_count = $comment->rating_count + 1 ;
			$commentscore ;
			if($new_raw_score >=1)
			{
				$commentscore=5+ log($new_raw_score,10);
			}
			else if($new_raw_score <= -1)
			{
				$commentscore=-1*log((-1*$new_raw_score),10) +5;
			}
			else {
				$commentscore = 5;
			}
			$update_comment_query = "update sz_comment set comment_score='$commentscore' , raw_score='$new_raw_score' , rating_count = '$new_rating_count' WHERE comment_id='$comment_id' and posting_id='$post_id' and blog_id = '$blog_id';";
			$wpwrapper->yk_query($update_comment_query);
			$commentscore_formatted = number_format($commentscore, "1");
			$postrating_ws_result = "Status=Success,Rating=$commentscore_formatted" ;
		} else {
			$postrating_ws_result = "Status=Failure" ;
		}
	} else if ($status == 'Blocked' | $status == 'Denied') {
		$postrating_ws_result = "Status=$status" ;
	}
	return $postrating_ws_result ;
}

function validate_email($email){
	echo(' email = '.$email);
	$mailparts=explode("@",$email);
	$hostname = $mailparts[1];

	// validate email address syntax
	$exp = "^[a-z\'0-9]+([._-][a-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$";
	$b_valid_syntax=eregi($exp, $email);

	// support windows platforms
	if (!function_exists ('getmxrr') ) {
		function getmxrr($hostname, &$mxhosts) {
			$mxhosts = array();
			//echo('%SYSTEMDIRECTORY%\\nslookup.exe -q=mx '.escapeshellarg($hostname));
			exec('nslookup.exe -q=mx '.escapeshellarg($hostname), $result_arr);
			foreach($result_arr as $line)
			{
				//echo('line = '.$line);
				if (preg_match("/.*mail exchanger = (.*)/", $line, $matches))
				$mxhosts[] = $matches[1];
			}
			return( count($mxhosts) > 0 );
		}//--End of workaround
	}

	// get mx addresses by getmxrr
	$b_mx_avail=getmxrr( $hostname, $mx_records, $mx_weight );
	if ($b_mx_avail == true) {
		echo('b_mx_avail = true');
	} else {
		echo('b_mx_avail = false');
	}

	$b_server_found=0;

	if($b_valid_syntax && $b_mx_avail){
		// copy mx records and weight into array $mxs
		$mxs=array();

		for($i=0;$i<count($mx_records);$i++){
			$mxs[$mx_weight[$i]]=$mx_records[$i];
		}

		// sort array mxs to get servers with highest prio
		ksort ($mxs, SORT_NUMERIC );
		reset ($mxs);

		while (list ($mx_weight, $mx_host) = each ($mxs) ) {
			if($b_server_found == 0){

				//try connection on port 25
				echo('mx_host = '.$mx_host);
				$fp = @fsockopen($mx_host, 25, $errno, $errstr, 2);
				echo('fp = '.$fp.' ');
				if($fp){
					$ms_resp="";
					// say HELO to mailserver
					$ms_resp.=send_command($fp, "HELO microsoft.com");

					// initialize sending mail
					$ms_resp.=send_command($fp, "MAIL FROM:<support@microsoft.com>");

					// try receipent address, will return 250 when ok..
					$rcpt_text=send_command($fp, "RCPT TO:<".$email.">");

					echo('rcpt_text = '.$rcpt_text);

					$ms_resp.=$rcpt_text;

					if(substr( $rcpt_text, 0, 3) == "250")  {
						$b_server_found=1;
					}
					// quit mail server connection
					$ms_resp.=send_command($fp, "QUIT");
					fclose($fp);
				}
			}
		}
	}
	return $b_server_found;
}

function send_command($fp, $out){
	fwrite($fp, $out . "\r\n");
	return get_data($fp);
}

function get_data($fp){
	$s="";
	stream_set_timeout($fp, 2);
	for($i=0;$i<2;$i++)
		$s.=fgets($fp, 1024);
	return $s;
}
?>