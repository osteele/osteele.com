<?
// CPAINT (Cross-Platform Asynchronous INterface Toolkit) - Version 1.01
// Copyright (c) 2005 Boolean Systems, Inc. - http://cpaint.sourceforge.net

error_reporting (E_ALL ^ E_NOTICE); 
if ($_GET['cpaint_remote_url'] != "") {
	$cp_remote_url = urldecode($_GET['cpaint_remote_url']);
	$cp_remote_method = urldecode($_GET['cpaint_remote_method']);
	$cp_remote_query = urldecode($_GET['cpaint_remote_query']);
	$cp_return_type = strtoupper($_GET['cpaint_return_type']);
}
if ($_POST['cpaint_remote_url'] != "") {
	$cp_remote_url = urldecode($_POST['cpaint_remote_url']);
	$cp_remote_method = urldecode($_POST['cpaint_remote_method']);
	$cp_remote_query = urldecode($_POST['cpaint_remote_query']);
	$cp_return_type = strtoupper($_POST['cpaint_return_type']);
}
if ($cp_return_type == "XML") header("Content-type:  text/xml\n\n");
if ($cp_remote_method == "GET") $cp_remote_url = $cp_remote_url . "?" . $cp_remote_query;
if ($cp_remote_method == "GET") {
	print(file_get_contents($cp_remote_url));
	exit();
} else {

	$cp_host = str_replace("http://", "", $cp_remote_url);
	$cp_uri = substr($cp_host, strpos($cp_host, "/"));
	$cp_host = substr($cp_host, 0, strpos($cp_host, "/"));
	$cp_request_header = "POST $cp_uri HTTP/1.0\nHost: $cp_host\nContent-Type:  application/x-www-form-urlencoded\nContent-Length:  " . strlen($cp_remote_query) . "\n\n$cp_remote_query\n\n";
	$cp_socket = fsockopen($cp_host, 80, $error, $errstr, 10);
	fwrite($cp_socket, $cp_request_header);
	while (!feof($cp_socket)) {
		$http_data = $http_data . fgets($cp_socket);
	}
	list($http_headers, $http_body) = split("\r\n\r\n", $http_data, 2);
	print($http_body);
	fclose($cp_socket);
	exit();
}
?>