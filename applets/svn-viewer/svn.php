<?php
  // Notes:
  // * regex-based XML parsing; very fragile

$default_repository = 'http://svn.openlaszlo.org/openlaszlo';
$repository = $default_repository;
$args = '';

if ($_GET['location'])
	$repository = urldecode($_GET['location']);
if ($_GET['revision'])
	$args = '-r '.escapeshellarg(urldecode($_GET['revision']));

if (!preg_match('/^(http|svn):/', $repository)) {
	header('Content-type: application/xml');
	die('<error message="Unsupported schema in '.$repository.'.  Only http: and svn: are supported."/>');
	}

exec('svn log --xml '.$args.' '.escapeshellarg($repository), $output);
$content = join("\n", $output);

if (!preg_match('|</log>|', $content)) {
  header('Content-type: application/xml');
  die('<error message="svn log command failed.  Try it from the command line: svn log '.$repository.'"/>');
 }

#die('<xml>'.join('\n', $output).'</xml>');
#exec('cat log.xml', $output);
#die($content);

// annotate the document root with the options that were passed in
#$content = preg_replace('|<log>|',
#						'<log options="'.$_GET['options'].'">',
#						$content);

function update_logentry($content) {
	// annotate the <date> element with @time and @date attributes
	$content = preg_replace("|<date>(([^<]*)T(\d\d:\d\d:\d\d*)[^<]*)</date>|",
							"<date date='\\2' time='\\3'>\\1</date>", $content);
	
	// If the author is 'perforce', replace it with the p4 user from the msg
	$content = preg_replace("|(<author>)perforce(</author>.*?<msg>[^<]*User:\s*(\S*))|s",
							"\\1\\3\\2", $content);
	
	// Add an author thumbnail, which may or may not exist
	if ($repository == $default_repository)
		$content = preg_replace('|(<author)(>\s*([^<]+)\s*</author>)|s',
								'\\1 image="images/\\3-57x57.jpg"\2', $content);
	
	// Splice in different titles in increasing preference
	$content = preg_replace('|(<logentry\s+revision="(.*)")|',
							'\\1 title="Revision: \\2"', $content);
	$content = preg_replace('|(<logentry[^>]*title=")[^"]*(.*?<msg>\s*([^\n<>"]*))|s',
							'\\1\\3\\2', $content);
	$content = preg_replace('{(<logentry[^>]*title=")[^"]*(.*?<msg>[^<]*Description:\s*([^\n<>"]*))}s',
							'\\1\\3\\2', $content);
	$content = preg_replace('{(<logentry[^>]*title=")[^"]*(.*?<msg>[^<]*Summary:\s*([^\n<>"]*))}s',
							'\\1\\3\\2', $content);
	
	// Prefix whatever title was chosen with the revision number
	$content = preg_replace('|<logentry\s+revision="([^"]*)"\s+title="|s',
							'\\0\\1: ', $content);
	return $content;
}

function replace_logentry($match) {
	return update_logentry($match[0]);
}

$content = preg_replace_callback('|<logentry.*?</logentry>|s',
								 'replace_logentry', $content);
$content = preg_replace('|<log>|', '<log location="'.$repository.'">', $content);

header('Content-type: application/xml');
echo $content;

?>