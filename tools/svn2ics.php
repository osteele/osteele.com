<?php
$default_repository = 'http://svn.openlaszlo.org/openlaszlo';
$repository = $default_repository;
$args = '';

if (!$_GET['location'] && preg_match('|&(.*)|', $_SERVER['QUERY_STRING'], $matches))
	$repository = urldecode($matches[1]);
if ($_GET['location'])
	$repository = urldecode($_GET['location']);
if ($_GET['revision'])
	$args = '-r '.escapeshellarg(urldecode($_GET['revision']));
//$args = ' -r HEAD';

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

function xmldecode($string) {
	$string = preg_replace('/&lt;/', '<', $string);
	$string = preg_replace('/&gt;/', '>', $string);
	$string = preg_replace('/&quot;/', '"', $string);
	$string = preg_replace('/&amp;/', '&', $string);
	return $string;
}

function logentry2event($content) {
	$author = preg_replace('|.*<author>(.*)</author>.*|s', '\\1', $content);
	$summary = preg_replace('|.*<msg>\s*([^\n<]*).*|s', '\\1', $content);
	$revision = preg_replace('|.*<logentry[^>]*revision="(.*?)">.*|s', '\\1', $content);
	$date = preg_replace('|.*<date>(.*)</date>.*|s', '\\1', $content);
	$date = preg_replace('/\.\d*/', '', $date);
	$date = preg_replace('|[:-]|', '', $date);
	$msg = preg_replace('|.*<msg>\s*(.*)\s*</msg>.*|s', '\\1', $content);
	if ($repository == $openlaszlo_repository) {
		if (preg_match('|.*<msg>.*User:\s*(\S*).*</msg>.*|s', $content, $matches))
			$author = $matches[1];
		if (preg_match('|.*<msg>.*Description:\s*([^\n]*).*</msg>.*|s', $content, $matches))
			$summary = $matches[1];
		if (preg_match('|.*<msg>.*Summary:\s*([^\n]*).*</msg>.*|s', $content, $matches))
			$summary = $matches[1];
	}
	
	$description = 'Revision: '.$revison.'\nAuthor: '.$author.'\n\n'.$msg;
	
	$items = array();
	$items[] = 'BEGIN:VEVENT';
	$items[] = 'DTSTART:'.$date;
	$items[] = 'CREATED:'.$date;
	//$items[] = 'ORGANIZER;CN:'.$author;
	//TODO: url
	$items[] = 'SUMMARY:'.xmldecode($summary);
	$items[] = 'DESCRIPTION:'.preg_replace('|\n|s', '\\n', xmldecode($description));
	$items[] = 'END:VEVENT';
	//$items[] = $content;
	return join("\n", $items);
}

function replace_logentry($match) {
	return logentry2event($match[0]);
}

$preamble = array();
$preamble[] = 'BEGIN:VCALENDAR';
$preamble[] = 'VERSION:2.0';
$preamble[] = 'PRODID:-//osteele.com//svn2ics//EN';
$preamble[] = 'CALSCALE:GREGORIAN';
$preamble[] = 'X-WR-CALNAME:'.preg_replace('|.*?:/*|', '', $repository).' Log';
$preamble[] = '';


$content = preg_replace_callback('|<logentry.*?</logentry>|s',
								 'replace_logentry', $content);
$content = preg_replace('|.*<log>\s*|s', join("\n", $preamble), $content);
$content = preg_replace('|</log>|', 'END:VCALENDAR', $content);

echo $content;

?>