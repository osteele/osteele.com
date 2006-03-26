<?php
  // Agenda:
  // - recursion
  //   - state machine needs to push context on nested {
  //   - move all state to object: arity, state
  //   - in args, { => 'savestate()=>'
  //   - end_group() pops state
  // - turn off for "f()\n{"
  //   - add nl to state machine
  // - cache
  //
  // Corners:
  // - missing file
  // - syntax errors
  // - unreadable file
  // - file is a directory
  //
  // Final:
  // - header
  // - new name
  // - debug swiches
  // - configuration
  // - rdoc

$file = $_SERVER['REQUEST_URI'];
//$debug_state = $_GET['debug-parser'];
$debug_state = false;

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
// Output
//

$lines = array();
$diversions = array();

function divert() {
	global $diversions, $lines;
	$diversions[] = $lines;
	$lines = array();
}

function merge_diversion() {
	global $diversions, $lines;
	if (count($diversions)) {
		$newlines = $lines;
		$lines = array_pop($diversions);
		foreach ($newlines as $line)
			$lines[] = $line;
	}
}

function pop_diversion() {
	global $lines;
	$s = join('', $lines);
	$lines = array();
	merge_diversion();
	return $s;
}

//
// Parser
//

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
										 '}' => 'end_group()=>',
										 'ident' => 'function',
										 'any' => ''),
					 'function(' => array(')' => ''),
					 'term' => array('(' => 'set_nullary()=>call(',
									 '}' => 'end_group()=>',
									 'any' => ''),
					 'call(' => array(')' => 'divert()=>call(..)',
									  '{' => 'start_group()=>set_arity()',
									  '}' => 'end_group()',
									  'any' => 'set_arity()'),
					 'call(..)' => array('{' => 'call(..){',
										 '}' => 'merge_diversion()=>end_group()=>',
										 'any' => 'merge_diversion()=>'),
					 'call(..){' => array('|' => 'start_block_parameters()=>call(..){|',
										  '}' => 'block_to_function()=>end_group()=>',
										  'any' => 'block_to_function()=>'),
					 'call(..){|' => array('|' => 'end_block_parameters()=>',
										   '}' => 'unmatched_bar()',
										   'any' => 'call(..){|')
					 );


function start_group() {
	global $groups;
	$groups[] = '}';
}
function end_group($value) {
	global $groups;
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
	global $groups, $lines, $arity;
	// hold="call(" or "call(a,...,c"; lines="){"
	$lines[0] = 'function()';
	// lines = "function(){"
	if ($arity)
		$lines[0] = ', '.$lines[0];
	merge_diversion();
	$groups[] = '})';
}
function start_block_parameters($value) {
	$value = '';
	divert();
}
function end_block_parameters($value) {
	global $groups, $lines, $arity;
	$value = '';
	// hold="call([a,...,c]", "){"; lines="p1 p2"
	$params = pop_diversion();
	// hold="call..."; lines="){";
	$lines[0] = "function ({$params})";
	if ($arity)
		$lines[0] = ', '.$lines[0];
	merge_diversion();
	$groups[] = '})';
}

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
			if ($debug_state) $lines[]="#{{$matches[1]}()}";
			call_user_func($matches[1], &$value);
			if ($matches[2]) $action = $matches[3];
			else $action = $state;
		}
		$state = $action;
	}
	
	$lines[] = $value;
	//$lines[] = "<{$type}>";
	if ($debug_state && $state && $type != 'ws' && $type != 'comment') $lines[] = "#<s={$state}>";
 }

$output = join("", $lines);
header('Content-Type: text/plain');
echo $output;
?>