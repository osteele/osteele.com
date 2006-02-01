<?php
$default_repository = 'http://svn.openlaszlo.org/openlaszlo';
$location = $default_repository;
$args = '';

if (!$_GET['location'] && preg_match('|&(.*)|', $_SERVER['QUERY_STRING'], $matches))
	$location = urldecode($matches[1]);
if ($_GET['location'])
	$location = urldecode($_GET['location']);
if ($_GET['revision'])
	$args = '-r '.escapeshellarg(urldecode($_GET['revision']));
//$args = ' -r HEAD';

if (!preg_match('/^(http|svn):/', $location))
	die('Unsupported schema in '.$location.'.  Only http: and svn: are supported.');

exec('svn log --xml '.$args.' '.escapeshellarg($location), $output);
$content = join("\n", $output);

if (!preg_match('|</log>|', $content))
  die('svn log for '.$location.' failed.  Check that it\'s a valid svn repository location.');

function xmldecode($string) {
	$string = preg_replace('/&lt;/', '<', $string);
	$string = preg_replace('/&gt;/', '>', $string);
	$string = preg_replace('/&quot;/', '"', $string);
	$string = preg_replace('/&amp;/', '&', $string);
	return $string;
}

function logentry2event($content) {
	preg_match('|<author>(.*)</author>|s', $content, $author);
	preg_match('|<msg>\s*(.*?)\s*</msg>|s', $content, $summary);
	preg_match('|<logentry[^>]*revision="(.*?)"|s', $content, $revision);$revision=$revision[1];
	preg_match('|<date>(.*)</date>|s', $content, $date);$date=$date[1];
	preg_match('|.*<msg>\s*(.*)\s*</msg>.*|s', $content, $msg);$msg=$msg[1];
	$date = preg_replace('/\.\d*/', '', $date);
	$date = preg_replace('/[:-]/', '', $date);
	if ($location == $openlaszlo_repository) {
		if (preg_match('|User:\s*(\S*)|s', $msg, $matches))
			$author = $matches[1];
		if (preg_match('|Description:\s*([^\n]*)|s', $msg, $matches))
			$summary = $matches[1];
		if (preg_match('|Summary:\s*([^\n]*)|s', $msg, $matches))
			$summary = $matches[1];
	}
	
	$description = 'Revision: '.$revision.'\n';
	if ($author) $description .= 'Author: '.$author.'\n';
	if ($msg) $description .= '\n'.$msg;
	
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
$preamble[] = 'X-WR-CALNAME:'.preg_replace('|.*?:/*|', '', $location).' Log';
$preamble[] = '';


$content = preg_replace_callback('|<logentry.*?</logentry>|s',
								 'replace_logentry', $content);
$content = preg_replace('|.*<log>\s*|s', join("\n", $preamble), $content);
$content = preg_replace('|</log>|', 'END:VCALENDAR', $content);

echo $content;

?>