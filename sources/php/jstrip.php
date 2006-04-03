<?php
  // Agenda:
  // - regular expression tokenizing
  // - cache
  // - header
  // - security


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

if (defined('STDIN')) {
	if ($argc != 2) die("usage: php {$argv[0]} filename\n");
	$cache_dir = null;
	$pathname = $argv[1];
 }


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
// Logic
//

if ($_GET['preprocess'] == 'false') {
	header('Content-Type: text/plain');
	readfile($pathname);
	exit();
 }


$source = file_get_contents($pathname);
$offset = 0;
$lineno = 1;

$segments = array();

$table = array('|/\*.*?\*/|sS' => 'comment',
			   '|//.*|S' => 'comment',
			   '{/(?:[^/\\\\]|\\\\.)*?/\w*}S' => 're',
			   '/"(?:[^\\\\]|\\\\.)*?"/S' => 'string',
			   '/\'(?:[^\\\\]|\\\\.)*?\'/S' => 'string',
			   '/[\)\}\]]/S' => 'ldelim',
			   '/[\(\{\[]/S' => 'rdelim',
			   '/./S' => 'char'
			   );

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
	$n = strcspn($source, " \t\n'\"/S", $offset);
	if ($n) return array('compound', $n);

	$context = 're';
	
	foreach ($table as $pattern => $type) {
		if ($type == 're' && $context != 're') continue;
		
		if (preg_match($pattern, $source, $matches,
					   PREG_OFFSET_CAPTURE, $offset)
			&& $matches[0][1] == $offset)
			return array($type, strlen($matches[0][0]));
	}
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
	if ($type == 'compound') {
		if ($last_token && $segments[0]
			&& $last_token['lineno'] != $token['lineno']
			&& !preg_match('/[{;,=\|\']$/S', $last_token['value']))
			$segments[] = "\n";
		if ($last_token && preg_match('/[\w\d_\$]$/S', $last_token['value'])
			&& $type = preg_match('/^[\w\$_]/S', $value))
			$segments[] = ' ';
		$segments[] = $value;
		$last_token = $token;
		continue;
	}
	$context = 'div';
	//	if ($last_type == 'word')// || preg_match('/[\w]/', $last_token) )
	//$context = 'div';
	if ($last_token && preg_match('/[=\(\[\{]$/S', $last_token['value']))
		$context = 're';
	if ($type == 'comment') {
		if (preg_match('/(Copyright.*\d{2,4}.*)\s*/Si', $value, $matches))
			$segments[] = "/* {$matches[1]} */S";
	}
	if ($type != 'comment') {
		//$segments[] = '[[<'.$type.'>';
		$segments[] = $value;
		//$segments[] = "<{$type}>]]\n";
		$last_token = $token;
	}
 }

$output = join("", $segments);
header('Content-Type: text/plain');
exit($output);
?>