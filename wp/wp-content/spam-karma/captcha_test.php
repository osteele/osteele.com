<?php
// bring in WordPress
require_once('../../wp-blog-header.php');
get_currentuserinfo();
if ($user_level < 8)
	die ("Sorry, you must be logged in and at least a level 8 user to access test Spam Karma's captcha.");

$debug_info = "<p>Function 'ImageTTFText': ";
$bad = false;
if (function_exists("ImageTTFText"))
	$debug_info .= "<font style='color:green;'>exists (OK)</font>";
else
{
	$debug_info .= "<font style='color:red;'>does NOT exist (Bad)</font>";
	$bad =true;
}
$debug_info .= "</p><p>Function 'ImageJPEG': ";
if (function_exists("ImageJPEG"))
	$debug_info .= "<font style='color:green;'>exists (OK)</font>";
else
{
	$debug_info .= "<font style='color:red;'>does NOT exist (Bad)</font>";
	$bad =true;
}
$debug_info .= "</p>";

if ($bad)
	$debug_info .= "<p>ALERT: Your Captcha likely won't be displayed correctly. This is probably due to your version of PHP not supporting some essential graphic features required to display Captchas. You should ask your Server admin to upgrade PHP to ensure it supports <strong>GD 2.0</strong> with <strong>FreeType font</strong> support.</p><p>In the meantime, make sure you *disable* the 'Captcha' option in Spam Karma's preferences.</p>";

// Fill in all required variables			
require_once("hn_captcha.class.php");

// using free ttf fonts from: http://fonts.tom7.com/fonts98.html
// ConfigArray
$CAPTCHA_INIT = array(
		'tempfolder'     => "captcha_temp/",      // string: absolute path (with trailing slash!) to a writeable tempfolder which is also accessible via HTTP!
		'TTF_folder'     => dirname($_SERVER['SCRIPT_FILENAME']) . "/captcha_fonts/", // string: absolute path (with trailing slash!) to folder which contains your TrueType-Fontfiles.
							// mixed (array or string): basename(s) of TrueType-Fontfiles
		//'TTF_RANGE'      => array(''),
		'TTF_RANGE'      => array('thisprty.ttf'),
// 'thisprty.ttf' 'dnahand.ttf'
		'chars'          => 4,       // integer: number of chars to use for ID
		'minsize'        => 18,      // integer: minimal size of chars
		'maxsize'        => 20,      // integer: maximal size of chars
		'maxrotation'    => 3,      // integer: define the maximal angle for char-rotation, good results are between 0 and 30

		'noise'          => TRUE,    // boolean: TRUE = noisy chars | FALSE = grid
		'websafecolors'  => FALSE,   // boolean
		'refreshlink'    => FALSE,    // boolean
		'lang'           => 'en',    // string:  ['en'|'de']
		'maxtry'         => 5,       // integer: [1-9]

		'badguys_url'    => '/',     // string: URL
		'secretstring'   => 'Lollypop Smothering lipstick sucking adverbial Moses with SWISS cheese.',
		'secretposition' => 5,      // integer: [1-32]

		'debug'          => true
);

if (! is_file($CAPTCHA_INIT['TTF_folder'] . $CAPTCHA_INIT['TTF_RANGE'][0]))
	$debug_info .= "<p>The font file could <font style='color:red;'>NOT</font> be found. Make sure you copied the file named '" . $CAPTCHA_INIT['TTF_folder'] . "' inside the folder '" . $CAPTCHA_INIT['TTF_RANGE'][0] . "', itself inside 'wp-content/spam-karma'.</p>";
elseif (! is_readable($CAPTCHA_INIT['TTF_folder'] . $CAPTCHA_INIT['TTF_RANGE'][0]))
	$debug_info .= "<p>The font file could <font style='color:red;'>NOT</font> be read. Make sure you the file '" . $CAPTCHA_INIT['TTF_folder'] . "' located inside 'wp-content/spam-karma/ " . $CAPTCHA_INIT['TTF_RANGE'][0] . "' is readable by the web server (chmod 755).";

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
<body>'
. $debug_info
. '<p><em><strong>The text below is displayed to users whose comment failed to clear Spam Karma\'s first array of filters. Make sure you can see the colored text in the box below (email auto-moderation is not displayed on this test page, though):</strong></em></p> 
<h3>Comment Verification</h3>

<p>Your comment could not be posted immediately as it triggered some of the anti-spam filters that run on this blog. Please complete the form below in order to get it posted directly.</p>

<p>If you are not able to see the image due to a browser issue or a handicap, please use the e-mail confirmation link at the bottom of this page.</p>

<p><i>If you do not confirm this posting within a certain period (by either solving the test below or using the mail auto-moderation), your comment will likely be destroyed.</i></p>';


	$captcha =& new hn_captcha($CAPTCHA_INIT);

	switch($captcha->validate_submit())
	{
	
		// was submitted and has valid keys
		case 1:
			echo "<p style='color:green'>CAPTCHA SUCCESSFULLY SUBMITTED.</p>";
			break;
	
	
		// was submitted with no matching keys, but has not reached the maximum try's
		case 2:
		        echo $echo;
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


?>
</body>
</html>
