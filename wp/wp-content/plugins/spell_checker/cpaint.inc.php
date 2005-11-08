<?
// CPAINT (Cross-Platform Asynchronous INterface Toolkit) - Version 1.01
// Copyright (c) 2005 Boolean Systems, Inc. - http://cpaint.sourceforge.net

error_reporting (E_ALL ^ E_NOTICE); 
global $cpaint_xml_result;
header ("Expires: Fri, 14 Mar 1980 20:53:00 GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache"); 
if ($_GET['cpaint_returnxml'] == "true") header("Content-Type:  text/xml");
if ($_POST['cpaint_returnxml'] == "true") header("Content-Type:  text/xml");
if ($_GET['cpaint_function'] != "") {
	print(call_user_func_array($_GET['cpaint_function'], $_GET['cpaint_argument']));
	exit();
} elseif ($_POST['cpaint_function']) {
	print(call_user_func_array($_POST['cpaint_function'], $_POST['cpaint_argument']));
	exit();
}
function cpaint_xml_return_data() {
	global $cpaint_xml_result;
	return "<?xml version=\"1.0\" standalone=\"yes\"?><AJAX-RESPONSE>" . $cpaint_xml_result . "</AJAX-RESPONSE>";
}
function cpaint_xml_add_data($dataname, $uniqueid, $datavalue) {
	global $cpaint_xml_result;
	$cpaint_xml_result = $cpaint_xml_result . "<" . strtoupper($dataname) . " ID=\"" . $uniqueid . "\">" . $datavalue . "</" . strtoupper($dataname) . ">";
}
function cpaint_xml_open_result($uniqueid) {
	global $cpaint_xml_result;
	$cpaint_xml_result = $cpaint_xml_result . "<AJAX-RESULT ID=\"" . $uniqueid . "\">";
}
function cpaint_xml_close_result() {
	global $cpaint_xml_result;
	$cpaint_xml_result = $cpaint_xml_result . "</AJAX-RESULT>";
}
?>