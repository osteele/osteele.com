<?php
  // Agenda:
  // - directory
  // - cache
  // - header

  // Limitations:
  // - unicode, e.g. \u000A as comment terminator
  // - more ws: 09 \t, 0b \vt, 0c \f, \a0 nbsp, other unicode space
$file = $_SERVER['REQUEST_URI'];
if ($_GET['file'])
	$file = $_GET['file'];
if ($_GET['strip'] == 'false') {
	header('Content-Type: text/plain');
	$fp = fopen("..{$file}", 'r');
	fpassthru($fp);
	die();
 }

header('Content-Type: text/plain');
$source = file_get_contents("..{$file}");
$offset = 0;
$limit = 0;
$last_type = '';

$context = 'div';

$lines = array();
$lines[] = "// Compressed by http://osteele.com/tools/jstrip";

$table = array('|/\*.*?\*/|s' => array('type' => 'comment', 'prefix' => '/*'),
			   '|//.*|' => array('type' => 'comment', 'prefix' => '//'),
			   '{/[^/\*](?:[^/]|\\\\/)*?/\w*}' => array('type' => 're', 'prefix' => '/', 'context' => 're'),
			   '|/|' => array('type' => 'token', 'prefix' => '/', 'context' => 'div'),
			   '/"(?:[^\\\\]|\\\\.)*?"/' => array('type' => 'string', 'prefix' => '"'),
			   '/\'(?:[^\\\\]|\\\\.)*?\'/' => array('type' => 'string', 'prefix' => "'"));

while ($offset < strlen($source)) {
	$limit += 1;
	//if ($limit > 100) break;
	//$lines[] = array('x', $offset, substr($source, $offset, 20));
	// skip ws
	$n = strspn($source, " \t", $offset);
	if ($n) {$offset += $n; continue;}
	$n = strspn($source, "\n\r", $offset);
	if ($n) {$offset += $n; $last_type = 'cr'; continue;}
	// read until the next ws, string quote, or potential comment
	$n = strcspn($source, " \t\n'\"/", $offset);
	if ($n) {
		if ($last_type == 'cr' && $lines[0] && !preg_match('/[{;,=]$/', $last_token))
			$lines[] = "\n";
		$token = substr($source, $offset, $n);
		$offset += $n;
		$type = preg_match('/^[\w\$_]/', $token) ? 'word' : '';
		if ($type == 'word' && $last_type == 'word') $lines[] = ' ';
		$lines[] = ''.$token.'';
		$last_type = preg_match('/[\w\d_\$]$/', $token) ? 'word' : '';
		$last_token = $token;
		continue;
	}
	$context = 're';
	if ($last_type == 'word')// || preg_match('/[\w]/', $last_token) )
		$context = 'div';
	//$lines[] = '#['.$last_type.','.$context.']';
	foreach ($table as $pattern => $meta) {
		if ($meta['context'] && $context != $meta['context']) continue;
		$type = $meta['type'];
		$prefix = $meta['prefix'];
		
		if ($prefix && $source[$offset] != $prefix[0]) continue;
		if ($prefix && strlen($prefix)> 1
			&& $source[$offset+1] != $prefix[1]) continue;
		if (preg_match($pattern, $source, $matches,
					   PREG_OFFSET_CAPTURE, $offset) &&
			$matches[0][1] == $offset) {
			$token = $matches[0][0];
			//if ($type == 're') $token = '#re['.$token.']';
			$offset += strlen($token);
			if ($type == 'comment') {
				if (preg_match('/(Copyright.*\d{2,4}.*)\s*/i', $token, $matches))
					$lines[] = "/* {$matches[1]} */";
			}
			if ($type != 'comment') {
				$lines[] = $token;
				$last_type = $type;
				$last_token = $token;
			}
			continue 2;
		}
	}
	die('failed at"'. $offset.': "'.substr($source, $offset));
 }
//print_r($lines);
//die("i={$offset}; limit={$limit}");
$output = join("", $lines);
die($output);
?>