<?php
  // Author: Oliver Steele
  // Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  // License: MIT License (Open Source)
  // Download: http://osteele.com/sources/php/jsplus.php
  // Docs: http://osteele.com/sources/php/jsplus
  // Created: 2006-03-24
  // Modified: 2006-03-27

'start';

//
// Configuration
//

// Cache file directory.  Must be writable by the httpd process,
// otherwise it is ignored.  To verify that it is being used, list its
// contents after requesting a js file.
define('CACHE_DIR', 'cache');

//
// Option processing
//
$cache_dir = CACHE_DIR;

$file = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
$pathname = preg_replace('/\/$/', '.', $_SERVER['DOCUMENT_ROOT']).'/'.$file;
$debug_state = $_GET['debug-parser'] && $_GET['debug-parser'] != 'false';

if (defined('STDIN')) {
	if ($argc != 2) die("usage: php {$argv[0]} filename\n");
	$cache_dir = null;
	$pathname = $argv[1];
 }

header('Content-Type: text/plain');
ini_set('html_errors', 'false');

//
// Caching
//
function ignoreErrorHandler($erro, $errmsg, $filename, $linenum, $vars) {}

function cachefile_name($file) {
	global $cache_dir;
	if (!$cache_dir) return null;
	return preg_replace('|/$|', '', $cache_dir).'/'.
		preg_replace_callback('([^\w\d-_])',
							  create_function('$matches',
											  'return sprintf("%%%02X", ord($matches[0]));'),
							  $file);
}

function passthru_cached($file, $pathname) {
	$cachefile = cachefile_name($file);
	if ($cachefile) {
		set_error_handler("ignoreErrorHandler");
		$fp = fopen($cachefile, 'r');
		$valid = $fp &&
			filemtime($cachefile) > filemtime($pathname) &&
			filemtime($cachefile) > filemtime($_SERVER['SCRIPT_FILENAME']);
		restore_error_handler();
		if ($valid) {
			flock($fp, LOCK_SH);
			readfile($cachefile);
			return true;
		}
	}
	return false;
}

function writecache($file, $content) {
	$cachefile = cachefile_name($file);
	if ($cachefile) {
		set_error_handler("ignoreErrorHandler");
		$fp = fopen($cachefile, 'w');
		restore_error_handler();
		if ($fp) {
			flock($fp, LOCK_EX);
			fwrite($fp, $content);
			fclose($fp);
		}
	}
}

//
// Top level
//

function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
	header("HTTP/1.0 404 Not Found");
	$msg = htmlspecialchars($errmsg);
	exit("alert(\"{$msg}\");");
}

if ($_GET['preprocess'] == 'false') {
	set_error_handler("userErrorHandler");
	readfile($pathname);
	exit();
 }

if (!$debug_state && passthru_cached($file, $pathname)) exit();

set_error_handler("userErrorHandler");
$source = file_get_contents($pathname);
restore_error_handler();

//
// Tokenizer
//
$offset = 0;
$lineno = 1;
$last_token = null; // previous non-ws token
$delim_stack = array();

$tokens = array('/\bfunction\b/S' => 'function',
				'/[a-zA-Z\$_][\w\$]*/S' => 'ident',
				'/0x[\da-f]/iS' => 'number',
				'/[0-9]+(?:\.0-9*)?(?:e[+-]?\d+)?/iS' => 'number',
				'/\.[0-9]+(?:e[+-]\d+)?/iS' => 'number',
				'|/\*.*?\*/|sS' => 'comment',
				'|//.*|S' => 'comment',
				'{/(?:[^/\\\\]|\\\\.)*?/\w*}S' => 're',
				'/"(?:[^\\\\]|\\\\.)*?"/S' => 'string',
				'/\'(?:[^\\\\]|\\\\.)*?\'/S' => 'string',
				'/./S' => 'char'
				);

$keywords = explode(' ', 'abstract boolean break byte case catch char class const continue debugger default delete do double else enum export extends final finally float for function goto if implements import in instanceof int interface long native new package private protected public return short static super switch synchronized this throw throws transient try typeof var void volatile while with');

function next_type() {
	global $source, $offset, $lineno, $tokens, $keywords, $last_token, $delim_stack;
	
	$n = strspn($source, " \t", $offset);
	if ($n) return array('ws', $n);
	
	$n = strspn($source, "\n\r", $offset);
	if ($n) {
		$lineno += $n;
		return array('ws', $n);
	}
	
	if(count($delim_stack))info($delim_stack[count($delim_stack)-1]);
	foreach ($tokens as $pattern => $type) {
		if ($type == 're' && $last_token &&
			($lineno == $last_token['lineno'] ||
			 (count($delim_stack) && strpos("([", $delim_stack[count($delim_stack)-1]) !== false)) &&
			array_search($last_token['type'], array('ident', 'number', ')', ']', '}')) !== false)
			continue;
		
		if ($prefix && $source[$offset] != $prefix[0]) continue;
		if ($prefix && strlen($prefix)> 1
			&& $source[$offset+1] != $prefix[1]) continue;
		if (preg_match($pattern, $source, $matches,
					   PREG_OFFSET_CAPTURE, $offset)
			&& $matches[0][1] == $offset) {
			$value = $matches[0][0];
			if ($type == 'ident' && array_search($value, $keywords) !== false)
				$type = $value;
			if ($type == 'char') {
				$type = $value;
				if (strpos("({[", $value) !== false)
					$delim_stack[] = $value;
				else if (strpos(")}]", $value) !== false)
					array_pop($delim_stack);
			}
			return array($type, strlen($value));
		}
	}
	die("failed at offset={$offset}: '".substr($source, $offset). "'");
};

function next_token() {
	global $source, $offset, $lineno, $last_token;
	if ($offset >= strlen($source)) return null;
	$start_offset = $offset;
	$start_lineno = $lineno;
	$token_data = next_type();
	$type = $token_data[0];
	$len = $token_data[1];
	$offset += $len;
	$token = array('type' => $type,
				   'value' => substr($source, $start_offset, $len),
				   'lineno' => $start_lineno);
	if ($type != 'ws' && $type != 'comment')
		$last_token = $token;
	return $token;
}

//
// Output buffering
//

$segments = array();
$diversions = array();

function info($msg, $color='red') {
	global $debug_state, $segments;
	if ($debug_state)
		$segments[] = array($color, $msg);
}

function line_text() {
	global $segments, $debug_state;
	if ($debug_state)
		for ($i = 0; $i < count($segments); $i++) {
			$segment = $segments[$i];
			if (is_array($segment)) {
				$msg = htmlspecialchars($segment[1]);
				$segment = "<font color='{$segment[0]}'>{$msg}</font>";
			} else {
				$segment = htmlspecialchars($segment);
				$segment = str_replace("\n", "<br/>", $segment);
				$segment = "<b>{$segment}</b>";
			}
			$segments[$i] = $segment;
		}
	return join('', $segments);
}

function divert() {
	global $diversions, $segments;
	$diversions[] = $segments;
	$segments = array();
}

function merge_diversion() {
	global $diversions, $segments;
	if (count($diversions)) {
		$diversion = $segments;
		$segments = array_pop($diversions);
		foreach ($diversion as $segment)
			$segments[] = $segment;
	}
}

function pop_diversion() {
	global $segments;
	$s = line_text();
	$segments = array();
	merge_diversion();
	return $s;
}

//
// Parser state machine
//

$transitions = array('' =>
					 array('function' => 'function',
						   '{' => 'start_group()',
						   '}' => 'end_group()',
						   'ident' => 'term',
						   ')' => 'term',
						   ']' => 'term'
						   ),
					 'function' => array('(' => 'function(',
										 'ident' => 'function',
										 'any' => '*'),
					 'function(' => array(')' => ''),
					 'term' => array('(' => 'set_nullary()=>call(',
									 'any' => '*'),
					 'call(' => array(')' => 'wait_for_block()=>call(..)',
									  '{' => 'set_arity()=>start_group()=>save_state()=>',
									  '}' => 'end_group()',
									  'any' => 'set_arity()'),
					 'call(..)' => array('{' => array('at_end_paren_line()' => 'call(..){',
													  'else' => 'merge_diversion()=>*'),
										 '}' => 'merge_diversion()=>end_group()=>',
										 'any' => 'merge_diversion()=>*'),
					 'call(..){' => array('|' => 'start_block_parameters()=>call(..){|',
										  '}' => 'block_to_function()=>end_group()=>',
										  'any' => 'block_to_function()=>*'),
					 'call(..){|' => array('|' => 'end_block_parameters()=>',
										   '}' => 'unmatched_bar()',
										   'any' => 'call(..){|')
					 );

//
// Parser actions
//
// $value is the string representation of the current token, passed
// by reference.

$groups = array();
$states = array();

function start_group() {
	global $groups, $states;
	$groups[] = '}';
	$states[] = NULL;
}
function end_group($value) {
	global $groups, $states, $state;
	$value = array_pop($groups);
}
function set_nullary() {
	global $arity;
	$arity = false;
}
function set_arity() {
	global $arity;
	$arity = true;
}
function block_to_function() {
	global $groups, $segments, $arity;
	// hold="call(" or "call(a,...,c"; lines="){"
	$segments[0] = 'function()';
	// lines = "function(){"
	if ($arity)
		$segments[0] = ', '.$segments[0];
	merge_diversion();
	$groups[] = '})';
}
function wait_for_block() {
	global $end_paren_line, $lineno;
	$end_paren_line = $lineno;
	divert();
}
function at_end_paren_line() {
	global $end_paren_line, $lineno;
	return $end_paren_line == $lineno;
}
function start_block_parameters(&$value) {
	$value = '';
	divert();
}
function end_block_parameters(&$value) {
	global $groups, $segments, $arity;
	$value = '';
	// hold="call([a,...,c]", "){"; lines="p1 p2"
	$params = pop_diversion();
	// hold="call..."; lines="){";
	$segments[0] = "function ({$params})";
	if ($arity)
		$segments[0] = ', '.$segments[0];
	merge_diversion();
	$groups[] = '})';
}
function save_state() {
	global $states, $state, $arity;
	$states[count($states)-1] = array($state, $arity);
}
function restore_state($s) {
	global $states, $state, $arity;
	info("restore {$s[0]}");
	$state = $s[0];
	$arity = $s[1];
}

//
// Parser
//

$state = '';

function next_state($state, $type, &$value) {
	global $debug_state, $transitions;
	$table = $transitions[$state];
	$action = $table[$type];
	if ($action === NULL) $action = $table[$value];
	if ($action === NULL) $action = $table['any'];
	if ($action === NULL) $action = $state;
	if (is_array($action)) {
		foreach ($action as $test => $action)
			if ($test == 'else' || eval("return ({$test});"))
				break;
	}
	while (preg_match('/^(\w[\w\d_]*)\(\)(=>(.*))?$/S', $action, $matches)) {
		if ($debug_state) info("$matches[1]();", 'green');
		call_user_func($matches[1], &$value);
		if ($matches[2]) $action = $matches[3];
		else $action = $state;
	}
	$state = $action;
	if ($state == '*')
		return next_state('', $type, $value);
	return $state;
}

while ($token = next_token()) {
	$type = $token['type'];
	$value = $token['value'];
	$do_restore = $value == '}';
	if ($type != 'ws' && $type != 'comment')
		$state = next_state($state, $type, $value);
	if ($do_restore) {
		global $states;
		$s = array_pop($states);
		if ($s !== NULL)
			restore_state($s);
		info('->'.$state);
	}
	
	$segments[] = $value;
	if ($debug_state) info("<{$type}>", 'gray');
	if ($debug_state && $state && $type != 'ws' && $type != 'comment') info("<{$state}>", 'blue');
 }

//
// Output
//

$output = line_text();
if ($debug_state)
	header('Content-Type: text/html');
echo $output;

if (!$debug_state) writecache($file, $output);
?>