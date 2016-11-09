<?php
if (!$_GET['location'] && $_SERVER['QUERY_STRING']) {
	$_GET['location'] = $_SERVER['QUERY_STRING'];
	include('cache.php');
	die();
 }

$title="svn2ics: Subversion Log to iCalendar";
$content_for_header = '<style type="text/css">img {border: 0}</style>';
include('../../includes/header.php');

function webcal_for($location) {
	return 'webcal://'.$_SERVER['SERVER_NAME'].preg_replace('|\?.*|', '', $_SERVER['REQUEST_URI']).'?'.urlEncode($location);
}
function webcal_to($location) {
	echo '<a href="'.webcal_for($location).'">';
}
function svn_viewer_for($location) {
	return 'http://'.$_SERVER['SERVER_NAME'].'/tools/svn-viewer/?location='.urlEncode($location);
}
?>

<img src="images/ical-icon.png" style="float: left; margin: 5px"/>
<!--img src="images/subversion-logo.png" style="float: right; margin: 5px"/-->
<h1><?php echo($title); ?></h1>
<div style="clear: both"/>

<?php if ($_GET['location']) {
	$location = $_GET['location'];
	$url = webcal_for($location);
	if (!preg_match('/^(http|svn):/', $location)) {
		echo 'Unsupported schema in <tt>'.$location.'</tt>.  Only <tt>http:</tt> and <tt>svn:</tt> are supported.<br/><br/>';
		$message = true;
		$location = false;
	} else {
		exec('svn log --xml -r HEAD '.escapeshellarg($location), $output);
		$content = join("\n", $output);
		if (!preg_match('|<logentry\s+revision="(.*?)"|m', $content, $revision)) {
			echo '<tt>svn log</tt> for <tt>'.$location.'</tt> failed.  Please verify that this is a valid svn repository location.<br/><br/>';
			$message = true;
			$location = false;
		}
	}
}
	
if ($location) {
?>
<p>The iCalendar for your subversion repository is at <a href="<?php echo $url;?>"><?php echo $url;?></a>.  Copy this link into your iCalendar client program.  If you're using Safari on the Macintosh, clicking on the link above will offer to subscribe iCal to this calendar.</p>

<p>You might also be interested in the <a href="<?php echo svn_viewer_for($location)?>">SVN Log Viewer</a> for this URL.</p>


30 day activity: <img src="/tools/svn-activity/sparkline.png?location=<?php echo urlEncode($location)?>"/><br/><br/>

<a href=".">Start over</a>
																	  <?php } else { ?>

<?php if (!$message) { ?>
																	  <p><tt>svn2ics</tt> lets you use Apple iCal or Mozilla Sunbird to browse the change log for a <a href="http://subversion.tigris.org/">subversion</a> repository.</p>

<p>Paste the address of a subversion repository below, and click "Subscribe" to create a URL.  You can paste this URL into any <a href="http://en.wikipedia.org/wiki/Icalendar">iCalendar</a>-compliant calendar program, such as <a href="http://www.apple.com/macosx/features/ical/">Apple iCal</a> or <a href="http://www.mozilla.org/projects/calendar/">Mozilla Sunbird</a>, to subscribe to a calendar of changes for that repository.</p>

<?php } ?>

<form action="." method="GET">
<label for="location"><b>Repository location:</b></label><br/>
<input type="text" size="80" id="location" name="location" value="<?php echo $_GET['location'] ? $_GET['location'] : 'http://svn.openlaszlo.org/' ?>"><br/>
<input type="submit" value="Create URL"/>
</input>
</form>

<br/><strong>Examples:</strong>
<div>
	  <!--?php webcal_to('http://svn.apache.org/repos/asf/')?>ASF</a>
	| <?php webcal_to('svn://anonsvn.kde.org/home/kde/')?>KDE</a>
	| <?php webcal_to('svn://mono.myrealbox.com/source/')?>Mono</a-->
     <?php webcal_to('http://svn.openlaszlo.org')?>OpenLaszlo</a>
	| <?php webcal_to('http://dev.rubyonrails.org/svn/rails')?>Rails</a>
	| <?php webcal_to('http://svn.edgewall.com/repos/trac/')?>Trac</a>
	| <?php webcal_to('svn://typosphere.org/typo')?>Typo</a>
</div>

<img src="images/svn2ics-medium.png" style="margin-top: 10px"/>

																	  <?php } ?>

<div style="position: absolute; bottom: 0; width: 70%">
<hr/>
Copyright 2006 by <a href="/">Oliver Steele</a>.  All rights reserved (for now).
</div>

<?php
  include('../../includes/footer.php');
?>