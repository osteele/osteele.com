<?php
  // Agenda:
  //
  // Function: args() {|
  // - get it working
  // - multiline
  //
  // Features:
  // - args() {|x| ...}
  //
  // Future:
  // - (args()\n{...}) // nl, but inside parens
  // - *also* strip ws
  // - keywords
  // - type declarations
  //
  // Final:
  // - more from jstring.php
  // - syntax errors
  //
  // Usage:
  //   <script src="test.js++"></script>

$file = $_SERVER['REQUEST_URI'];
if ($_GET['preprocess'] == 'false') {
	header('Content-Type: text/plain');
	$fp = fopen("..{$file}", 'r');
	fpassthru($fp);
	die();
 }

$source = file_get_contents("..{$file}");
$offset = 0;
$lineno = 1;
$stack = array();

$lines = array();
//$lines[] = "// Compressed by http://osteele.com/tools/jstrip";

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
					 'term' => array('(' => 'term(', 'any' => ''),
					 'term(' => array(')' => 'term..)'),
					 'term..)' => array('{' => 'term..{', 'any' => ''),
					 'term..{' => array('any' => 'consolidate()=>')
					 );

function consolidate() {
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
		if ($action == NULL) $action = $table[$value];
		if ($action == NULL) $action = $table['any'];
		//$lines[] = "[{$type},'{$value}',{$table[$type]},{$table[$value]},{$table['any']},{$action}]";
		//print_r($action);
		if (preg_match('/^(\w[\w\d_]*)\(\)(=>(.*))?$/', $action, $matches)) {
			$lines[]="[{$matches[1]}]";//call_user_func($matches[1]);
			$action = $matches[2];
			if ($matches[2]) $state = $matches[3];
		} else
			$state = $action;
	}
	
	$lines[] = $value;
	//$lines[] = "<{$type}>";
	//if ($state) $lines[] = "{{$state}}";
	continue;

	if ($state == '') {
		if ($value == ')') {
			$lines[] = substr($source, $startpos, $offset-1-$startpos);
			$startpos = $offset-1;
			$last_line = $lineno;
			$state = '{';
			$lines[] = '.';
			continue;
		}
	} else if ($state == '{') {
		if ($value == '{' && $last_line == $lineno) {
			$lines[] = ', function() {';
			$startpos = $offset;
			$lines[] = '|';
			$state = '';
			continue;
		}
	}
 }
//$lines[] = substr($source, $startpos);
$output = join("", $lines);
header('Content-Type: text/plain');
die($output);
?>