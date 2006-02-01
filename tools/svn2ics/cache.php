<?php
if (!$_GET['location'])
	die('missing url'.$_SERVER['QUERY_STRING']);

$location = urldecode($_GET['location']);

if (!preg_match('/^(http|svn):/', $location))
	die('Unsupported schema in '.$location.'.  Only http: and svn: are supported.');

$cachePath = 'cache/'.urlencode($location);
if (file_exists($cachePath)) {
	exec('svn log --xml -r HEAD '.escapeshellarg($location), $output);
	$log = join("\n", $output);
	if (!preg_match('|</log>|', $log))
		die('svn log for '.$location.' failed.  Check that it\'s a valid svn repository location.');
	$cacheData = file_get_contents($cachePath);
	preg_match('|<logentry\s+revision="(.+?)"|m', $log, $match);$logRevision=$match[1];
	preg_match('|Revision: (\d+)|', $cacheData, $match);$cacheRevision=$match[1];
	if ($logRevision && $cacheRevision)
		$merge = true;
	if ($merge && $logRevision == $cacheRevision)
		die($cacheRevision);
 }

if ($cacheRevision)
	$q='&revision='.urlencode('HEAD:'.($cacheRevision+1));
$content = file_get_contents('http://osteele.com/tools/svn2ics/svn2ics.php?location='.urlencode($location).$q, 'r');
if ($cacheData)
	$content = preg_replace('|END:VCALENDAR.*?(BEGIN:VEVENT)|s', '\\1', $content.$cacheData);

$cacheFile = fopen($cachePath, 'w');
$cacheFile || die("couldn't open cache file ".$cachePath);
flock($cacheFile, LOCK_EX);
fwrite($cacheFile, $content);
fclose($cacheFile);

echo($content);

?>
