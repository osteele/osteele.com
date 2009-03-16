<?php

// PHP Proxy based on example for Yahoo! Web services.
//
// Author: Jason Levitt
// December 7th, 2005
//
//
// Adapted for Zemanta Wordpress plugin
// by Jure Cuhalev - <jure@zemanta.com>
// October 11th, 2007
//
// de-curled by Jure Koren - <jure.koren@zemanta.com>, borrowing from
// http://netevil.org/blog/2006/nov/http-post-from-php-without-curl
// October 16th, 2008

$path = 'http://api.zemanta.com/services/rest/0.0/';
$error = '';

function do_post_request($url, $data, $optional_headers = null)
{
	global $error;
	$params = array('http' => array(
				'method' => 'POST',
				'content' => $data
					));
	if ($optional_headers !== null) {
		$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if (!$fp) {
		$error = "Problem connecting to $url : $php_errormsg\n";
		return false;
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		$error = "Problem reading data from $url : $php_errormsg\n";
	}
	return $response;
}

// If it's a POST, put the POST data in the body
$postvars = '';
while ( ($element = current( $_POST ))!==FALSE ) {
	$new_element = str_replace( '&', '%26', $element );
	$new_element = str_replace( ';', '%3B', $new_element );
	$postvars .= key( $_POST ).'='.$new_element.'&';
	next( $_POST );
}
if (extension_loaded("curl")) { // curl extension is loaded
	$session = curl_init( $path );
	curl_setopt ( $session, CURLOPT_POST, true );
	curl_setopt ( $session, CURLOPT_POSTFIELDS, $postvars );
	curl_setopt( $session, CURLOPT_HEADER, false );
	curl_setopt( $session, CURLOPT_RETURNTRANSFER, true );
	$json = curl_exec( $session );
	curl_close( $session );
	if (!$json) {
		$json = false;
		$error = "CURL failed to connect to Zemanta.\n";
	}
} else if (ini_get("allow_url_fopen")) { // allow_url_fopen = on
	$json = do_post_request( $path, $postvars );
} else { // we can't POST like this
	$json = false;
	$error = "Sorry, your PHP does not support fopen wrappers and has no curl extension loaded.\n";
}

if ($json === false) {
	header($_SERVER['SERVER_PROTOCOL'] . " 500");
	die($error);
}
elseif ($json == "<h1>403 Developer Inactive</h1>") {
	header($_SERVER['SERVER_PROTOCOL'] . " 403");
	die("Mashery said access denied. Invalid api key? Too many requests?\n");
}
else {
	// The web service returns JSON. Set the Content-Type appropriately
	header("Content-Type: text/plain");
	echo $json;
}

?>
