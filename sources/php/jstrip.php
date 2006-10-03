<?php
  // Author: Oliver Steele
  // Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  // License: MIT License (Open Source)
  // Download: http://osteele.com/sources/php/jstrip.php
  // Created: 2006-03-22
  // Modified: 2006-04-03

  // Agenda:
  // - regular expression tokenizing
  // - header (earlier date; different docs)
  // - increment line count when /**/ contains \n\r

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
// Main
//
if ($_GET['preprocess'] == 'false') {
	header('Content-Type: text/plain');
	readfile($pathname);
	exit();
 }

if (passthru_cached($file, $pathname)) exit();

$source = file_get_contents($pathname);


//
// Tokenizer
//
$offset = 0;
$lineno = 1;

$table = array('|/\*.*?\*/|sS' => 'comment',
			   '|//.*|S' => 'comment',
			   '{/(?:[^/\\\\]|\\\\.)*?/\w*}S' => 're',
			   '/"(?:[^\\\\]|\\\\.)*?"/S' => 'string',
			   '/\'(?:[^\\\\]|\\\\.)*?\'/S' => 'string',
			   '/[\)\}\]]/S' => 'ldelim',
			   '/[\(\{\[]/S' => 'rdelim',
			   '/./S' => 'char'
			   );

// re mode unless div is possible
// div is possible 

function next_token_raw() {
	global $source, $offset, $lineno, $table, $goal;
	
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

	foreach ($table as $pattern => $type) {
		if ($type == 're' && $goal != 're') continue;
		
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
	$raw = next_token_raw();
	$len = $raw[1];
	$offset += $len;
	return array('type' => $raw[0],
				 'value' => substr($source, $start_offset, $len),
				 'lineno' => $start_lineno);
}

//
// Output
//
$segments = array();

while ($token = next_token()) {
	$type = $token['type'];
	$value = $token['value'];
	if ($type == 'ws') continue;
	if ($type == 'comment') {
		if (!count($segments)
			&& preg_match('/(Copyright.*\d{2,4}.*)\s*/iS', $value, $matches))
			$segments[] = "/* {$matches[1]} */";
		continue;
	}
	if ($type == 'compound') {
		if ($last_token && $segments[0]
			&& $last_token['lineno'] != $token['lineno']
			&& !(preg_match('/[-+\*\/^&|%=]$|\bin\b$/S', $last_token['value'])
				 || preg_match('/^[\*\/^&|%=]|^\+(?!\+)|^-(?!-)|^\bin/S', $value))
			)
			$segments[] = "\n";
		else if ($last_token && $last_type != 're' && $last_type != 'string'
				 && preg_match('/[\w\d_\$]\n[\w_\$]|\+\n\+|-\n-/S',
							   $last_token['value']."\n".$value))
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
	if ($type != 'comment') {
		//$segments[] = '[[<'.$type.'>';
		$segments[] = $value;
		//$segments[] = "<{$type}>]]\n";
		$last_token = $token;
		$last_type = $type;
		$last_value = $value;
	}
 }

$output = join("", $segments);
header('Content-Type: text/plain');
echo $output;

writecache($file, $output);
?>