<?php
  // Agenda:
  // - args() {|x| ...}
  //   - after {, look for |, }, or any
  //   - if it's a |, eat the token, start another hold, and wait for the next |
  //   - on the final |, release the hold with '[, ]function('+^+') {'
  // - nesting
  //   - arity needs to be on a stack
  //   - does hold need to be a stack?
  //   - test cases
  // - turn off for "f()\n{"
  //   - add nl to state machine
  // - cache
  // - regular expression context
  //
  // Final:
  // - header
  // - choose a name
  // - rationalize debug swtiches
  // - more from jstring.php
  // - syntax errors
  // - test query parameters
  // 
  // This file
  // The following are equivalent:
  //   fn(1,2) {return 3}
  //   fn(1,2, function(){return 3});
  //
  // Also the following:
  //   map(ar) {|n| s += n}
  //   map(ar, function(n) {s += n}
  //
  // keeps line numbers the same
  // actual parser, not regex.  You use ws and comments freely; won't
  // be confused by strings
  // 
  // == Installation
  // Place this file in a directory where the server can execute php;
  // for example, htdocs/cgi/jblock.php.
  //
  // Place the following lines in your .htaccess file:
  //   RewriteEngine On
  //   RewriteRule ^(.*\.js)$ /cgi/jblock.php [L]
  //
  // (Optional:) To enable caching, create a 'cache' directory
  // in the same directory as this file.  It needs to have write
  // permission.
  //   > mkdir cache
  //   > chmod g+rwx cache
  //   
  // == Usage
  // In JavaScript source file:
  //   fn(1,2) {return 3}
  //
  // In HTML:
  //   <script src="test.js"></script>
  // 
  // Options:
  // - ?debug-parser
  // - ?preprocess
  //
  // == Configuration

$file = $_SERVER['REQUEST_URI'];
$debug_state = $_GET['debug-parser'];

if ($_GET['preprocess'] == 'false') {
	header('Content-Type: text/plain');
	$fp = fopen("..{$file}", 'r');
	fpassthru($fp);
	die();
 }

$source = file_get_contents("..{$file}");

//
// Tokenizer
//
$offset = 0;
$lineno = 1;

$tokens = array('/function/' => 'function',
				'/[\w\$\_][\w\d\$\_]*/' => 'ident',
			   '|/\*.*?\*/|s' => 'comment',
				'|//.*|' => 'comment',
				'{/(?:[^/\\\\]|\\\\.)*?/\w*}' => 're',
				'|/|' => 'div',
				'/"(?:[^\\\\]|\\\\.)*?"/' => 'string',
				'/\'(?:[^\\\\]|\\\\.)*?\'/' => 'string',
				'/./' => 'char'
				);

function next_type() {
	global $source, $offset, $lineno, $tokens;
	
	$n = strspn($source, " \t", $offset);
	if ($n) return array('ws', $n);
	
	$n = strspn($source, "\n\r", $offset);
	if ($n) {
		$lineno += $n;
		return array('ws', $n);
	}
	
	foreach ($tokens as $pattern => $type) {
		//if ($meta['context'] && $context != $meta['context']) continue;
		//if ($type == 're') die($pattern);
		
		if ($prefix && $source[$offset] != $prefix[0]) continue;
		if ($prefix && strlen($prefix)> 1
			&& $source[$offset+1] != $prefix[1]) continue;
		if (preg_match($pattern, $source, $matches,
					   PREG_OFFSET_CAPTURE, $offset)
			&& $matches[0][1] == $offset)
			return array($type, strlen($matches[0][0]));
	}
	die("failed at offset={$offset}: '".substr($source, $offset). "'");
};

function next_token() {
	global $source, $offset, $lineno;
	if ($offset >= strlen($source)) return null;
	$start_offset = $offset;
	$start_lineno = $lineno;
	$raw = next_type();
	$len = $raw[1];
	$offset += $len;
	return array('type' => $raw[0],
				 'pos' => $start_offset,
				 'len' => $len,
				 'value' => substr($source, $start_offset, $len),
				 'lineno' => $start_lineno);
}

//
// Parser
//
$lines = array(); // output

$transitions = array('' =>
					 array('function' => 'function',
						   '{' => 'start_group()',
						   '}' => 'end_group()', //fixme: after_term
						   'ident' => 'term',
						   ')' => 'term',
						   ']' => 'term'
						   ),
					 'function' => array('(' => 'function(',
										 '{' => 'start_group()=>',
										 'ident' => 'function',
										 'any' => ''),
					 'function(' => array(')' => ''),
					 'term' => array('(' => 'set_nullary()=>call(',
									 '}' => 'end_group=>',
									 'any' => ''),
					 'call(' => array(')' => 'start_hold()=>call(..)',
									  'any' => 'set_ary()'),
					 'call(..)' => array('{' => 'call(..){',
										 '}' => 'end_hold()=>stop_group()=>',
										 'any' => 'stop_hold()=>'),
					 'call(..){' => array('}' => 'consolidate()=>end_group()=>',
										  'any' => 'consolidate()=>')
					 );

function set_nullary() {
	global $arity;
	$arity = 0;
}
function set_ary() {
	global $arity;
	$arity++;
}

function start_hold() {
	global $hold, $lines;
	$hold = $lines;
	$lines = array();
}
function stop_hold() {
	global $hold, $lines;
	if ($hold !== null) {
		foreach ($lines as $line)
			$hold[] = $line;
		$lines = $hold;
		$hold = null;
	}
}
function start_group() {
	global $groups;
	$groups[] = '}';
}
function end_group($value) {
	global $groups, $lines;
	stop_hold();
	$lines[] = array_pop($groups);
	$value = '';
}
function consolidate() {
	global $groups, $lines, $hold, $arity;
	//$hold[] = 'function';
	$lines[0] = 'function()';
	if ($arity)
		$lines[0] = ', function()';
	//array_unshift($lines, '+++');
	$groups[] = '})';
	stop_hold();
}

$limit = 0;
$startpos = 0;
$state = '';
while ($token = next_token()) {
	$type = $token['type'];
	$value = $token['value'];
	if ($type != 'ws' && $type != 'comment') {
		$table = $transitions[$state];
		$action = $table[$type];
		if ($action === NULL) $action = $table[$value];
		if ($action === NULL) $action = $table['any'];
		if ($action === NULL) $action = $state;
		//$v  = $table[$value]===NULL;
		//$lines[] = "[{$type},'{$value}',{$table[$type]},{$table[$value]}({$v}),{$table['any']},{$action}]";
		//print_r($action);
		while (preg_match('/^(\w[\w\d_]*)\(\)(=>(.*))?$/', $action, $matches)) {
			if ($debug_state) $lines[]="[{$matches[1]}]";
			call_user_func($matches[1], &$value);
			if ($matches[2]) $action = $matches[3];
			else $action = $state;
		}
		$state = $action;
	}
	
	$lines[] = $value;
	//$lines[] = "<{$type}>";
	if ($debug_state && $state && $type != 'ws' && $type != 'comment') $lines[] = "{{$state}}";
 }

$output = join("", $lines);
header('Content-Type: text/plain');
echo $output;
?>