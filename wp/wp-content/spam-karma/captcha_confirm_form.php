<?php
// bring in WordPress
require_once('../../wp-blog-header.php');

include("spamk_include.php");

function my_escape_string( $string ) 
{
	if (get_magic_quotes_gpc() == 1) 
		return ( $string );
	else 
	    return ( mysql_escape_string ( $string ) );
}


$now_gmt = current_time('mysql', 1);
$max_attempts = 3;
global $open_meta_spamk, $close_meta_spamk; //

$open_meta_spamk = "<!-- spamk    : "; // must match values in spam-karma.php (too lazy to use includes)
$close_meta_spamk = " -->";

global $c_id, $c_author;

// get vars from post
if (isset($_REQUEST['c_id']) && isset($_REQUEST['c_author']))
{
	$c_id = $_REQUEST['c_id'];
	$c_author = $_REQUEST['c_author'];	
}
else
	die ('You may not access this file directly!');



// Check to see if $c_id and $mcauthor match a comment in moderation
$comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE `comment_ID` = '$c_id'  AND comment_author = '" . my_escape_string($c_author) . "' LIMIT 1;");

if (!$comment->comment_ID)
	die ("No such comment exists (id: $c_id, author: $c_author)");
	
	// set their cookie, since WordPress won't be able to
	setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + 30000000, COOKIEPATH);
	setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + 30000000, COOKIEPATH);
	setcookie('comment_author_url_' . COOKIEHASH, $comment->comment_author_url, time() + 30000000, COOKIEPATH);	
	
if ($comment->comment_approved == '1')
	die('Your comment has already been approved (maybe by the administrator).');

if (! preg_match("/^{$open_meta_spamk}\\s*CAPTCHA:sent-(.)-times:(.*){$close_meta_spamk}$/m", $comment->comment_content, $matches))
{
	//echo $comment->comment_content, "\n\n";
	die('This comment cannot be approved by a Captcha (it may have expired).');
}

$spamk_options = spamk_get_settings('spamk_options');

$send_mail = isset($_REQUEST['send_email']);
$check_mail = isset($_REQUEST['x']);

if (! in_array('email_moderation', $spamk_options) && ($send_mail || $check_mail))
	die("This comment cannot be auto-moderated via e-mail.");

if ($send_mail)
{
	send_link($comment);
	echo "Mail sent. Please check your mail and follow instructions.";
	exit(0);
}
elseif($check_mail)
{ // gives one more chance for email check (in case Captcha failed)
	$cur_attempt = $matches[1]-1;
}
else
{
	$cur_attempt = $matches[1];
}

if($cur_attempt > $max_attempts)
	die ('Too many attempts.');
$time_expire = $matches[2];
if ($expired = ($time_expire < current_time('timestamp', 1)))
	die ('Auto-moderation period has expired.');

if (! preg_match("/^{$open_meta_spamk}\\s*KARMA:\\s*([^\\s]*)\\s*{$close_meta_spamk}$/m", $comment->comment_content, $matches))
{
	echo "Cannot retrieve comment karma: using -7";
	$karma = -7;
}
else
	$karma = $matches[1];
	
if ($check_mail)
{
/* Code from Plugin Comment Authorization 0.1
Author: Scott Merrill
Author URI: http://www.skippy.net/ */
	
	$seed = DB_USER;// uses DB user name as a seed (secure enough but not dangerous if broken)
	$x = $_REQUEST['x'];
	
	$md5 = md5($comment->comment_content . $seed);

	if ($md5 == $x) 
	{
		//wp_set_comment_status($id, 'approve');
		$karma += 3;
		spamk_update_comment_status($karma, $c_id, $c_author, $comment);
		exit(0);
	}
}

$new_content = preg_replace("/^({$open_meta_spamk}\\s*CAPTCHA:sent-.-times:[0-9]*\\s*{$close_meta_spamk})$/m",  $open_meta_spamk . "CAPTCHA:sent-". ($cur_attempt+1) . "-times:$time_expire" . $close_meta_spamk, $comment->comment_content);
	
$wpdb->query("UPDATE $wpdb->comments SET `comment_content` = '". mysql_escape_string($new_content) . "' WHERE DATE_SUB('$now_gmt', INTERVAL 24 HOUR) <= comment_date_gmt AND comment_ID = '$c_id' AND comment_author = '". mysql_escape_string($c_author) . "' LIMIT 1;");

if ($check_mail) // if we are still here means the check failed.
	die("This URL is not valid... please make sure you copy the whole URL.");

// Fill in all required variables			
require_once("hn_captcha.class.php");

// using free ttf fonts from: http://fonts.tom7.com/fonts98.html
// ConfigArray
$CAPTCHA_INIT = array(
		'tempfolder'     => "captcha_temp/",      // string: absolute path (with trailing slash!) to a writeable tempfolder which is also accessible via HTTP!
		'TTF_folder'     => dirname($_SERVER['SCRIPT_FILENAME']) . "/captcha_fonts/", // string: absolute path (with trailing slash!) to folder which contains your TrueType-Fontfiles.
							// mixed (array or string): basename(s) of TrueType-Fontfiles
		'TTF_RANGE'      => array('thisprty.ttf'),
// 'thisprty.ttf' 'dnahand.ttf'
		'chars'          => 4,       // integer: number of chars to use for ID
		'minsize'        => 18,      // integer: minimal size of chars
		'maxsize'        => 20,      // integer: maximal size of chars
		'maxrotation'    => 3,      // integer: define the maximal angle for char-rotation, good results are between 0 and 30

		'noise'          => FALSE,    // boolean: TRUE = noisy chars | FALSE = grid
		'websafecolors'  => FALSE,   // boolean
		'refreshlink'    => FALSE,    // boolean
		'lang'           => 'en',    // string:  ['en'|'de']
		'maxtry'         => 5,       // integer: [1-9]

		'badguys_url'    => '/',     // string: URL
		'secretstring'   => 'Lollypop Smothering lipstick sucking adverbial Moses with SWISS cheese.',
		'secretposition' => 5,      // integer: [1-32]

		'debug'          => false
);


$echo = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<TITLE>CAPTCHA Auto-Moderation</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

<style type="text/css">
<!--
/*********************************
 *
 *	CAPTCHA-Styles
 *
 */
	p.captcha_1,
	p.captcha_2,
	p.captcha_notvalid
	{
		margin-left: 30px;
		margin-right: 20px;
		font-size: 12px;
		font-style: normal;
		font-weight: normal;
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		background: transparent;
		color: #000000;
	}
	p.captcha_2
	{
		font-size: 10px%;
		font-style: italic;
		font-weight: normal;
	}
	p.captcha_notvalid
	{
		font-weight: bold;
		color: #FFAAAA;
	}
	
	.captchapict
	{
		margin: 0;
		padding: 0;
		border: 2px solid #CCC;
	}
	
	#captcha
	{
		margin: 0 30px;
		border: 1px solid #666;
	}
-->
</style>
</head>
<body>
<h3>Comment Verification</h3>

<p>Your comment could not be posted immediately as it triggered some of the anti-spam filters that run on this blog. Please complete the form below in order to get it posted directly.</p>

<p>If you are not able to see the image due to a browser issue or a handicap, please use the e-mail confirmation link at the bottom of this page.</p>

<p><i>If you do not confirm this posting within a certain period (by either solving the test below or using the mail auto-moderation), your comment will likely be destroyed.</i></p>';


	$captcha =& new hn_captcha($CAPTCHA_INIT);

	switch($captcha->validate_submit())
	{
	
		// was submitted and has valid keys
		case 1:
			// PUT IN ALL YOUR STUFF HERE //
			$karma += 4;
			
			spamk_update_comment_status($karma, $c_id, $c_author, $comment);

			break;
	
	
		// was submitted with no matching keys, but has not reached the maximum try's
		case 2:
			echo $echo;
			if($cur_attempt >= $max_attempts)
				echo "<p><br>Reached the maximum try's of $max_attempts without success!";
			else
				echo $captcha->display_form();
			break;
	
	
		// was submitted, has bad keys and also reached the maximum try's
		case 3:
			//if(!headers_sent() && isset($captcha->badguys_url)) header('location: '.$captcha->badguys_url);
			echo $echo;
					echo "<p><br>Reached the maximum attempt of ".$captcha->maxtry." without success!";
			break;
	
	
		// was not submitted, first entry
		default:
		        echo $echo;
			echo $captcha->display_form();
			break;
	
	}

// ALTERNATIVE EMAIL CONFIRMATION

function valid_email($email) 
{

$host = substr($email,strpos($email,"@") + 1);
if (eregi("^([[:alnum:]_%+=.-]+)@([[:alnum:]_.-]+)\.([a-z]{2,3}|[0-9]{1,3})$",$email))  //  && (checkdnsrr($host,"MX") || checkdnsrr($host,"A"))
	return 1;
else
	return 0;
} // end function


function send_link($comment) 
{
/*
Code from Plugin Comment Authorization 0.1
Author: Scott Merrill
Author URI: http://www.skippy.net/
*/
	global $open_meta_spamk, $close_meta_spamk;

	
	$seed = DB_USER;// uses DB user name as a seed (secure enough but not dangerous if broken)
		
	// get the comment details
	$comment_ID = $comment->comment_ID;
	
	// make an MD5 sum of the comment text + seed
	$md5 = md5($comment->comment_content . $seed);
	
	 $comment_text = trim(preg_replace("/^({$open_meta_spamk}.*{$close_meta_spamk}\\n*)/m", '', $comment->comment_content ));	
	
	$message = "A comment on " . get_settings('blogname') . " was posted using this email address.  In order to approve this comment, please visit the following URL:\n\n   " . get_settings('siteurl') . "/wp-content/spam-karma/captcha_confirm_form.php?c_id=" . $comment_ID . "&c_author=". urlencode($comment->comment_author) . "&x=" . $md5 . "\n\n\nComment details:\n" . $comment_text ."\n\n";
	
	@mail($comment->comment_author_email, sprintf(__('[%s] Comment Authorization Request'), get_settings('blogname')), $message, "From: " . get_settings('admin_email') . "\r\nReply-To: " . get_settings('admin_email') . "\r\nX-Mailer: PHP/" . phpversion());

} // end function

function spamk_update_comment_status($karma, $c_id, $c_author, $comment)
{
	global $wpdb, $open_meta_spamk, $close_meta_spamk;


	if ($karma >= 0)
	{
		$new_content = preg_replace("/^({$open_meta_spamk}.*{$close_meta_spamk}\\n*)/m", "", $comment->comment_content);

		$wpdb->query("UPDATE `{$wpdb->comments}` SET `comment_approved` = '1', `comment_content` = '". mysql_escape_string($new_content) . "' WHERE `comment_ID` = '$c_id' AND `comment_author` = '". mysql_escape_string($c_author) . "' LIMIT 1;");

		//wp_set_comment_status($c_id, "approve");
		if (get_settings("comments_notify") == true) 
		{
			wp_notify_postauthor($c_id);
		}
	
		do_action('wp_set_comment_status', $c_id);
	
		$spamk_settings = spamk_get_settings('spamk_settings');
		$spamk_settings['moderate_spam'] --;
		$spamk_settings['approved_cmts'] ++;
		spamk_update_option('spamk_settings', $spamk_settings);
		
		
		$redirect = get_permalink($comment->comment_post_ID);
		header('Location: ' . $redirect);
	}
	else
	{
		$new_content = preg_replace("/^({$open_meta_spamk}CAPTCHA:sent-.-times:.*{$close_meta_spamk})$/m", $open_meta_spamk . "CAPTCHA: approved" . $close_meta_spamk . "\n", $comment->comment_content);
		$new_content = preg_replace("/^{$open_meta_spamk}KARMA:.*{$close_meta_spamk}$/m", $open_meta_spamk . "KARMA: $karma" . $close_meta_spamk . "\n", $new_content);

		$wpdb->query("UPDATE `{$wpdb->comments}` SET `comment_approved` = '0', `comment_content` = '". mysql_escape_string($new_content) . "' WHERE `comment_ID` = '$c_id' AND `comment_author` = '" . mysql_escape_string($c_author) . "' LIMIT 1;");

		//wp_set_comment_status($c_id, "hold"); // not using this as it seems not to work well...
		wp_notify_moderator($c_id);

		echo "<p><b>Your comment has been put in the moderation queue: it will be displayed as soon as an admin approves it</b> (no need to re-submit)</p>";
	}
}

if ( (! empty($comment->comment_author_email))
&& (valid_email($comment->comment_author_email)) 
&& in_array('email_moderation', $spamk_options))
{
		echo "<p>You can also have an e-mail sent to you to let you moderate this comment yourself. The email you have provided in your comment ('". $comment->comment_author_email . "') must be a valid one.<br/> <a href=\"". $_SERVER['PHP_SELF'] ."?send_email=true&c_author=". urlencode($c_author) . "&c_id=$c_id\">Click Here</a> to send send yourself an Auto-Moderation Email</a></p>";
}
?>

<p><b>Comment Spam Filtering by <a href="http://unknowngenius.com/blog/wordpress/spam-karma/"><i>Spam Karma</i></a></b></p>
</body>
</html>
