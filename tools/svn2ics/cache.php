<?php
if (!$_GET['location'])
	die('missing url'.$_SERVER['QUERY_STRING']);

$location = urldecode($_GET['location']);

if (!preg_match('/^(http|svn):/', $location))
	die('Unsupported schema in '.$location.'.  Only http: and svn: are supported.');

exec('svn log --xml -r HEAD '.escapeshellarg($location), $output);
$log = join("\n", $output);
if (!preg_match('|<logentry\s+revision="(.+?)"|m', $log, $match))
	die('svn log for '.$location.' failed.  Check that it\'s a valid svn repository location.');
$headRevision=$match[1];
$tailRevision=1;

$cachePath = 'cache/'.urlencode($location);
if (file_exists($cachePath)) {
	$cacheData = file_get_contents($cachePath);
	preg_match('|Revision: (\d+)|', $cacheData, $match);
	$cacheRevision=$match[1];
	$tailRevision = $cacheRevision+1;
	if ($headRevision && $cacheRevision)
		$merge = true;
	if ($merge && $headRevision == $cacheRevision)
		die($cacheData);
 }

$limit = 100;
if ($headRevision - $tailRevision > $limit) {
	$headRevision = $tailRevision + $limit - 1;
	$trimmed = true;
 }
if ($headRevision || $cacheRevision)
	$q='&revision='.urlencode($headRevision.':'.$tailRevision);

exec('curl '.escapeshellarg('http://osteele.com/tools/svn2ics/svn2ics.php?location='.urlencode($location).$q), $curl_output);
$content = join("\n", $curl_output);
if (!preg_match('|END:VCALENDAR|', $content))
	die($content);
if ($cacheData)
	$content = preg_replace('|END:VCALENDAR.*?(BEGIN:VEVENT)|s', '\\1', $content.$cacheData);

$cacheFile = fopen($cachePath, 'w');
$cacheFile || die("couldn't open cache file ".$cachePath);
flock($cacheFile, LOCK_EX);
fwrite($cacheFile, $content);
fclose($cacheFile);

if ($trimmed) {
	$lines = array();
	$lines[] = 'BEGIN:VEVENT';
	$lines[] = 'DTSTART:VEVENT';
	$lines[] = 'DTSTART:'.date('Ymd');
	$lines[] = 'CREATED:'.date('Ymd');
	$lines[] = 'SUMMARY:Refresh the calendar to see the rest of the log.';
	$lines[] = 'DESCRIPTION:This log contains so much data that your browser would have timed out if I had fetched it all at one go.  Now that I\'ve cached some of the beginning, I\'ll add '.$limit.' entries each time you refresh the calendar until I catch up.';
	$lines[] = 'END:VEVENT';
	$lines[] = 'BEGIN:VEVENT';
	$content = preg_replace('|BEGIN:VEVENT|m', join("\n", $lines), $content, 1);
 }
echo($content);

?>
