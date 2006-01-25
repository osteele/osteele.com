<?php
if (preg_match('|&(.*)|', $_SERVER['QUERY_STRING'], $matches))
	$location = urldecode($matches[1]);
else
	die('missing url');

if (!preg_match('/^(http|svn):/', $location))
	die('Unsupported schema in '.$location.'.  Only http: and svn: are supported.');

$cache = 'cache/'.urlencode($location);
if (file_exists($cache)) {
	exec('svn log --xml -r HEAD '.escapeshellarg($location), $output);
	$log = join("\n", $output);
	if (!preg_match('|</log>|', $content))
		die('svn log for '.$location.' failed.  Check that it\'s a valid svn repository location.');
	$cacheData = read($cache);
	preg_match('|<logentry version="(.+?)"|', $log, $logRevision);
	preg_match('|Revision: (\S+)|', $cacheData, $cacheRevision);
	die('log='.$logRevision.',cache='.$cacheRevision);
	if ($logRevision && $cacheRevision)
		$merge = true;
	if ($merge && $logRevision == $cacheRevision)
		die($cacheRevision);
 }

if ($merge)
	$q='&revision='.urlencode('HEAD:'.($cacheRevision+1));
$content = open('./svn2ics.nocache?location='.urlencode($location).$q);
if ($merge)
	$content = preg_replace('|END:VCALENDAR\n.*?(BEGIN:VEVENT)|s', '\\1', $cacheData.$content);
write_to_file($content, $cache);
echo($content);

?>
