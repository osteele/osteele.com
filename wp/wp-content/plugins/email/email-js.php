<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-EMail 2.20										|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://lesterchan.net															|
|																							|
|	File Information:																	|
|	- E-Mail Javascript File															|
|	- wp-content/plugins/email/email-js.php									|
|																							|
+----------------------------------------------------------------+
*/


### Session Start
@session_start();

### Include wp-config.php
@require('../../../wp-config.php');
cache_javascript_headers();

### Determine email.php Path
$email_ajax_url = dirname($_SERVER['PHP_SELF']);
if(substr($email_ajax_url, -1) == '/') {
	$email_ajax_url  = substr($email_ajax_url, 0, -1);
}

### Get Email Options
$email_max = intval(get_option('email_multiple'));
?>

// Variables
var email_ajax_url = "<?php echo $email_ajax_url; ?>/email.php";
var email = new sack(email_ajax_url);
var email_max_allowed = '<?php echo $email_max; ?>';
var email_verify = '<?php echo $_SESSION['email_verify']; ?>';
var email_p = 0;
var email_pageid = 0;
var email_yourname = '';
var email_youremail = '';
var email_yourremarks = '';
var email_friendname = '';
var email_friendemail = '';
var email_friendnames = '';
var email_friendemails = '';
var email_imageverify = '';

// Email Form Validation
function validate_email_form() {
	// Variables
	var errFlag = false;
	var errMsg = "<?php _e('The Following Error Occurs:', 'wp-email'); ?>\n";	
	errMsg = errMsg + "__________________________________\n\n";

	// Your Name Validation
	if(document.getElementById('yourname')) {
		if(isEmpty(email_yourname)) {
			errMsg = errMsg + "<?php _e('- Your Name is empty', 'wp-email'); ?>\n";
			errFlag = true;
		}
		if(!is_valid_name(email_yourname)) {
			errMsg = errMsg + "<?php _e('- Your Name is invalid', 'wp-email'); ?>\n";
			errFlag = true;
		}
	}
	// Your Email Validation
	if(document.getElementById('youremail')) {
		if(isEmpty(email_youremail)) {
			errMsg = errMsg + "<?php _e('- Your Email is empty', 'wp-email'); ?>\n";
			errFlag = true;
		}
		if(!is_valid_email(email_youremail)) {
			errMsg = errMsg + "<?php _e('- Your Email is invalid', 'wp-email'); ?>\n";
			errFlag = true;
		}
	}
	// Your Remarks Validation
	if(document.getElementById('yourremarks')) {
		if(!isEmpty(email_yourremarks)) {
			if(!is_valid_remarks(email_yourremarks)) {
				errMsg = errMsg + "<?php _e('- Your Remarks is invalid', 'wp-email'); ?>\n";
				errFlag = true;
			}
		}
	}
	// Friend Name(s) Validation
	if(document.getElementById('friendname')) {
		if(isEmpty(email_friendname)) {
			errMsg = errMsg + "<?php _e('- Friend Name(s) is empty', 'wp-email'); ?>\n";
			errFlag = true;
		} else {
			for(i = 0; i < email_friendnames.length; i++) {
				if(isEmpty(email_friendnames[i])) {
					errMsg = errMsg + "<?php _e('- Friend Name is empty: ', 'wp-email'); ?>" + email_friendnames[i] + "\n";
					errFlag = true;
				}
				if(!is_valid_name(email_friendnames[i])) {
					errMsg = errMsg + "<?php _e('- Friend Name is invalid: ', 'wp-email'); ?>" + email_friendnames[i] + "\n";
					errFlag = true;
				}
			}
		}
		if(email_friendnames.length > email_max_allowed) {
			errMsg = errMsg + "<?php printf(__('- Maximum %s Friend Name(s) allowed', 'wp-email'), $email_max); ?>\n";
			errFlag = true;
		}
	}
	// Friend Email(s) Validation
	if(isEmpty(email_friendemail)) {
		errMsg = errMsg + "<?php _e('- Friend Email(s) is empty', 'wp-email'); ?>\n";
		errFlag = true;
	} else {
		for(i = 0; i < email_friendemails.length; i++) {
			if(isEmpty(email_friendemails[i])) {
				errMsg = errMsg + "<?php _e('- Friend Email is empty: ', 'wp-email'); ?>" + email_friendemails[i] + "\n";
				errFlag = true;
			}
			if(!is_valid_email(email_friendemails[i])) {
				errMsg = errMsg + "<?php _e('- Friend Email is invalid: ', 'wp-email'); ?>" + email_friendemails[i] + "\n";
				errFlag = true;
			}
		}
	}
	if(email_friendemails.length > email_max_allowed) {
		errMsg = errMsg + "<?php printf(__('- Maximum %s Friend Email(s) allowed', 'wp-email'), $email_max); ?>\n";
		errFlag = true;
	}
	// Friend Name(s) And Email(s) Validation
	if(document.getElementById('friendname')) {
		if(email_friendnames.length != email_friendemails.length) {
			errMsg = errMsg + "<?php _e('- Friend Name(s) count does not tally with Friend Email(s) count', 'wp-email'); ?>\n";
			errFlag = true;
		}
	}
	if(document.getElementById('imageverify')) {
		if(isEmpty(email_imageverify)) {
			errMsg = errMsg + "<?php _e('- Image Verification is empty', 'wp-email'); ?>\n";
			errFlag = true;
		}
	}
	// If There Is Error Alert It
	if (errFlag == true){
		alert(errMsg);
		return false;
	} else {
		return true;
	}
}

// Check Form Field Is Empty
function isEmpty(value){
	if (trim(value) == "") {
		return true;
	}
	return false;
}

// Trim White Spaces
function trim(strText) { 
	// this will get rid of leading spaces 
	while (strText.substring(0,1) == ' ') 
		strText = strText.substring(1, strText.length);
	// this will get rid of trailing spaces 
	while (strText.substring(strText.length-1,strText.length) == ' ')
		strText = strText.substring(0, strText.length-1);
   return strText;
}

// Check Name
function is_valid_name(name) {
	var name = trim(name);
	var filter  = /[(\*\(\)\[\]\+\,\/\?\:\;\'\"\`\~\\#\$\%\^\&\<\>)+]/;
	return !filter.test(name);
}

// Check Email
function is_valid_email(email) {
	var email = trim(email);
	var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return filter.test(email);
}

// Check Remarks
function is_valid_remarks(remarks) {
	var remarks = trim(remarks);
	var injection_strings = new Array('apparently-to', 'cc', 'bcc', 'boundary', 'charset', 'content-disposition', 'content-type', 'content-transfer-encoding', 'errors-to', 'in-reply-to', 'message-id', 'mime-version', 'multipart/mixed', 'multipart/alternative', 'multipart/related', 'reply-to', 'x-mailer', 'x-sender', 'x-uidl');
	for(i = 0; i < injection_strings.length; i++) {
		if(remarks.indexOf(injection_strings[i]) != -1) {
			return false;
		}
	}
	return true;
}

// WP-Email Popup
function email_popup(email_url) {
	window.open(email_url, "_blank", "width=500,height=500,toolbar=0,menubar=0,location=0,resizable=0,scrollbars=1,status=0");
}

// Email Form AJAX
function email_form() {
	if(document.getElementById('yourname')) {
		email_yourname = document.getElementById('yourname').value;
	}
	if(document.getElementById('youremail')) {
		email_youremail = document.getElementById('youremail').value;
	}
	if(document.getElementById('yourremarks')) {
		email_yourremarks = document.getElementById('yourremarks').value;
	}
	if(document.getElementById('friendname')) {
		email_friendname = document.getElementById('friendname').value;
		email_friendnames = email_friendname.split(",");
	}
	email_friendemail = document.getElementById('friendemail').value;
	email_friendemails = email_friendemail.split(",");
	if(document.getElementById('imageverify')) {
		email_imageverify = document.getElementById('imageverify').value;
	}
	if(document.getElementById('p')) {
		email_p = document.getElementById('p').value;
	}
	if(document.getElementById('page_id')) {
		email_pageid = document.getElementById('page_id').value;
	}
	if(validate_email_form()) {
		document.getElementById('wp-email-submit').disabled = true;
		document.getElementById('wp-email-loading').style.display = 'block';
		email.reset();
		if(document.getElementById('yourname')) {			
			email.setVar('yourname', email_yourname);
			document.getElementById('yourname').disabled = true;
		}
		if(document.getElementById('youremail')) {
			email.setVar('youremail', email_youremail);
			document.getElementById('youremail').disabled = true;
		}
		if(document.getElementById('yourremarks')) {
			email.setVar('yourremarks', email_yourremarks);
			document.getElementById('yourremarks').disabled = true;
		}
		if(document.getElementById('friendname')) {
			email.setVar('friendname', email_friendname);
			document.getElementById('friendname').disabled = true;
		}
		email.setVar('friendemail', email_friendemail);
		document.getElementById('friendemail').disabled = true;
		if(document.getElementById('imageverify')) {
			email.setVar('imageverify', email_imageverify);
			document.getElementById('imageverify').disabled = true;
		}
		if(document.getElementById('p')) {
			email.setVar('p', email_p);
		}
		if(document.getElementById('page_id')) {
			email.setVar('page_id', email_pageid);
		}
		email.setVar('wp-email', '1');
		email.setVar('popup', document.getElementById('popup').value);
		email.method = 'POST';
		email.element = 'wp-email';
		email.runAJAX();
	}
}