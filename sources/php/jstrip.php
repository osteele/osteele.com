<?php
  // Agenda:
  // - regular expression tokenizing
  // - cache
  // - header
  // - security
  // - is 'prefix' worth it?
  // - configuration: copyright, header

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

$source = file_get_contents("..{$file}");
$offset = 0;
$lineno = 1;

$lines = array();
//$lines[] = "// Compressed by http://osteele.com/tools/jstrip";

$table = array('|/\*.*?\*/|s' => array('type' => 'comment', 'prefix' => '/*'),
			   '|//.*|' => array('type' => 'comment', 'prefix' => '//'),
			   '{/(?:[^/\\\\]|\\\\.)*?/\w*}' => array('type' => 're', 'prefix' => '/', 'context' => 're'),
			   '|/|' => array('type' => 'token', 'prefix' => '/', 'context' => 'div'),
			   '/"(?:[^\\\\]|\\\\.)*?"/' => array('type' => 'string', 'prefix' => '"'),
			   '/\'(?:[^\\\\]|\\\\.)*?\'/' => array('type' => 'string', 'prefix' => "'"));

function next_token0() {
	global $source, $offset, $lineno, $table;
	
	$n = strspn($source, " \t", $offset);
	if ($n) return array('ws', $n);
	
	$n = strspn($source, "\n\r", $offset);
	if ($n) {
		$lineno += $n;
		return array('ws', $n);
	}
	
	// read until the next ws, string quote, or potential comment
	$n = strcspn($source, " \t\n'\"/", $offset);
	if ($n) return array('composite', $n);
	
	foreach ($table as $pattern => $meta) {
		//if ($meta['context'] && $context != $meta['context']) continue;
		$type = $meta['type'];
		$prefix = $meta['prefix'];
		//if ($type == 're') die($pattern);
		
		if ($prefix && $source[$offset] != $prefix[0]) continue;
		if ($prefix && strlen($prefix)> 1
			&& $source[$offset+1] != $prefix[1]) continue;
		if (preg_match($pattern, $source, $matches,
					   PREG_OFFSET_CAPTURE, $offset)
			&& $matches[0][1] == $offset)
			return array($type, strlen($matches[0][0]));
	}
	die("failed at {$offset}, {$context}, type={$last_type}, token='{$last_token}': ".substr($source, $offset));
};

function next_token() {
	global $source, $offset, $lineno;
	if ($offset >= strlen($source)) return null;
	$start_offset = $offset;
	$start_lineno = $lineno;
	$raw = next_token0();
	$len = $raw[1];
	$offset += $len;
	return array('type' => $raw[0],
				 'pos' => $start_offset,
				 'len' => $len,
				 'value' => substr($source, $start_offset, $len),
				 'lineno' => $start_lineno);
}

while ($token = next_token()) {
	$type = $token['type'];
	$value = $token['value'];
	if ($type == 'ws') continue;
	//if ($type == 'comment') continue;
	/*$lines[] = $type;
	$lines[] = ':';
	$lines[] = $value;
	$lines[] = ';';
	continue;*/
	if ($type == 'composite') {
		if ($last_token && $lines[0]
			&& $last_token['lineno'] != $token['lineno']
			&& !preg_match('/[{;,=]$/', $last_token['value']))
			$lines[] = "\n";
		if ($last_token && preg_match('/[\w\d_\$]$/', $last_token['value'])
			&& $type = preg_match('/^[\w\$_]/', $value))
			$lines[] = ' ';
		$lines[] = $value;
		$last_token = $token;
		continue;
	}
	$context = 'div';
	//	if ($last_type == 'word')// || preg_match('/[\w]/', $last_token) )
	//$context = 'div';
	if ($last_token && preg_match('/[=\(\[\{]$/', $last_token['value']))
		$context = 're';
	if ($type == 'comment') {
		if (preg_match('/(Copyright.*\d{2,4}.*)\s*/i', $value, $matches))
			$lines[] = "/* {$matches[1]} */";
	}
	if ($type != 'comment') {
		//$lines[] = '[[<'.$type.'>';
		$lines[] = $value;
		//$lines[] = "<{$type}>]]\n";
		$last_token = $token;
	}
 }

$output = join("", $lines);
header('Content-Type: text/plain');
die($output);
?>