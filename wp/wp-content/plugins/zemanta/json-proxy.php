<?php

// PHP Proxy based on example for Yahoo! Web services.
//
// Author: Jason Levitt
// December 7th, 2005
//
//
// Adapted for Zemanta
// by Jure Cuhalev - <jure@zemanta.com>
// October 11th, 2007
//
// de-curled by Jure Koren - <jure.koren@zemanta.com>, borrowing from
// http://netevil.org/blog/2006/nov/http-post-from-php-without-curl
// October 16th, 2008
//
// Licensed under the GPL, http://www.gnu.org/copyleft/gpl.html

$path = 'http://api.zemanta.com/services/rest/0.0/';
$error = '';

function do_fopen_post_request($url, $data, $optional_headers = null)
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
		@$error = "Problem connecting to $url : $php_errormsg\n";
		return array(false, $error);
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		@$error = "Problem reading data from $url : $php_errormsg\n";
	}
	return array($response, $error);
}

function do_curl_post_request($url, $data)
{
	$session = curl_init( $url );
	curl_setopt( $session, CURLOPT_POST, true );
	curl_setopt( $session, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $session, CURLOPT_HEADER, false );
	curl_setopt( $session, CURLOPT_RETURNTRANSFER, true );
	$json = curl_exec( $session );
	$info = curl_getinfo( $session );
	if ($info['http_code'] != 200) {
		return array(false, "Problem reading data from $url : " . curl_error( $session ) . "\n");
	}
	curl_close( $session );
	if (!$json) {
		$json = false;
		$error = "CURL failed to connect to Zemanta.\n";
	} else {
		$error = "";
	}
	return array($json, $error);
}

function do_post_request($url, $data) {
	if (extension_loaded("curl")) { // curl extension is loaded
		list($json, $error) = do_curl_post_request( $url, $data );
	} else if (ini_get("allow_url_fopen")) { // allow_url_fopen = on
		list($json, $error) = do_fopen_post_request( $url, $data );
	} else { // we can't POST like this
		$json = false;
		$error = "Sorry, your PHP does not support fopen wrappers and has no curl extension loaded.\n";
	}
	return array($json, $error);
}

// If it's a POST, put the POST data in the body
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// HTTP POST data
	if (!isset($_POST['api_key'])) {
		header($_SERVER['SERVER_PROTOCOL'] . " 500");
		header("Content-Type: text/plain");
		die("No api key.\n");
	}

	$postvars = '';
	while ( ($element = current( $_POST ))!==FALSE ) {
		$new_element = str_replace( '&', '%26', $element );
		$new_element = str_replace( ';', '%3B', $new_element );
		$postvars .= key( $_POST ).'='.$new_element.'&';
		next( $_POST );
	}

	// Zemanta Pro signatures
	if (file_exists(dirname(__FILE__) . "/SECRET.php")) {
		require_once(dirname(__FILE__) . "/SECRET.php");
		if (defined("ZEMANTA_SECRET")) {
			$values = array_values($_POST);
			sort($values, SORT_STRING);
			$raw = ZEMANTA_SECRET . join("", $values);
			$postvars .= "&signature=" . md5($raw);
		}
	}

	list($json, $error) = do_post_request($path, $postvars);

	// The web service returns errors or JSON or XML. Set the Content-Type to something lightweight.
	if ($json === false) {
		header($_SERVER['SERVER_PROTOCOL'] . " 500");
		header("Content-Type: text/plain");
		die($error);
	}
	else {
		header("Content-Type: text/plain");
		echo $json;
	}
} else { // Display DEBUG page on GET

	$port = '';
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
		$_SERVER['FULL_URL'] = 'https://';
		if($_SERVER['SERVER_PORT']!='443') {
			$port = ':' . $_SERVER['SERVER_PORT'];
		}
	} else {
		$_SERVER['FULL_URL'] = 'http://';
		if($_SERVER['SERVER_PORT']!='80') {
			$port = ':' . $_SERVER['SERVER_PORT'];
		}
	}
	if(isset($_SERVER['REQUEST_URI'])) {
		$script_name = $_SERVER['REQUEST_URI'];
	} else {
		$script_name = $_SERVER['PHP_SELF'];
		if($_SERVER['QUERY_STRING']>'') {
			$script_name .= '?'.$_SERVER['QUERY_STRING'];
		}
	}
	if (isset($_SERVER['HTTP_HOST'])) {
		$_SERVER['FULL_URL'] .= $_SERVER['HTTP_HOST'].$port.$script_name;
	} else {
		$_SERVER['FULL_URL'] .= $_SERVER['SERVER_NAME'].$port.$script_name;
	}

	session_start();
	$t = time();
	if (isset($_SESSION['last_check']) and $t - $_SESSION['last_check'] > 9) {
		$ping_response = '';
		$suggest_response = '';
		$direct_ping_response = '';
		$direct_suggest_response = '';
		$test_api_key='9uqkzfkx8nvnycbynmfeg3uq';
		$_SESSION['last_check'] = $t;
		$ping_response = do_post_request($_SERVER['FULL_URL'], "method=zemanta.service.ping&api_key=$test_api_key&format=json");
		$direct_ping_response = do_post_request($path, "method=zemanta.service.ping&api_key=$test_api_key&format=json");
		$suggest_response = do_post_request($_SERVER['FULL_URL'], "method=zemanta.suggest&api_key=$test_api_key&format=json&text=Proxy test " . $t);
		$direct_suggest_response = do_post_request($path, "method=zemanta.suggest&api_key=$test_api_key&format=json&text=Proxy test " . $t);
		echo "<html><head><script type=\"text/javascript\">function toggle(o) {if (o.style.width == 'auto')";
		echo "{o.style.width='30em'; o.style.height='1em';} else {o.style.width=o.style.height='auto';} return false;}</script></head><body>\n";
		echo "<h1>Zemanta proxy</h1>\n";
		echo "<h2>Direct API responses</h2>\n";
		echo "<p>Ping response:</p>\n";
		echo "<p>" . htmlentities(print_r($direct_ping_response, true)) . "</p>\n";
		echo "<p>Suggest response (click to reveal):</p>\n";
		echo "<div onclick=\"return toggle(this)\" style=\"overflow: hidden; width: 30em; height: 1em; cursor: hand; cursor: pointer;\">" . htmlentities(print_r($direct_suggest_response, true)) . "</div>\n";
		echo "<h2>Proxied API responses</h2>\n";
		echo "<p>Ping response: " . htmlentities(print_r($ping_response, true)) . "</p>\n";
		echo "<p>Suggest response (click to reveal):</p>\n";
		echo "<div onclick=\"return toggle(this)\" style=\"overflow: hidden; width: 30em; height: 1em; cursor: hand; cursor: pointer;\">" . htmlentities(print_r($suggest_response, true)) . "</div>\n";
		echo "<h2>Ping test</h2>\n";
		echo "<p>API key: <form action=\"\" method=\"post\">";
		echo "<input type=\"text\" name=\"api_key\" />";
		echo "<input type=\"hidden\" name=\"method\" value=\"zemanta.service.ping\" />";
		echo "<input type=\"hidden\" name=\"format\" value=\"json\" />";
		echo "</form></p>\n";
		echo "</body></html>\n";
	} else {
		echo '<html><head><meta http-equiv="refresh" content="10" /></head><body><p>Please wait for about 10 seconds...</p></body></html>';
		$_SESSION['last_check'] = time();
	}
}

?>
