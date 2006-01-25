<?php
$title="svn2ics: Subversion Log to iCalendar";
$content_for_header = '<style type="text/css">img {border: 0}</style>';
include('../includes/header.php');

function webcal_for($location) {
	return 'webcal://'.$_SERVER['SERVER_NAME'].preg_replace('|\?.*|', '', $_SERVER['REQUEST_URI']).'/?'.urlEncode($location);
}
function webcal_to($location) {
	echo '<a href="'.webcal_for($location).'">';
}
?>

<img src="images/ical-icon.png" style="float: left; margin: 5px"/>
<!--img src="images/subversion-logo.png" style="float: right; margin: 5px"/-->
<h1><?php echo($title); ?></h1>
<div style="clear: both"/>

<?php if ($_GET['location']) {
	$url = webcal_for($_GET['location']);
?>
<p>The iCalendar for your subversion repository is at <a href="<?php echo $url;?>"><?php echo $url;?></a>.  Copy this link into your iCalendar client program.  If you're using Safari on the Macintosh, clicking on the link above will offer to subscribe iCal to this calendar.</p>

<a href="">Start over</a>
																	  <?php } else { ?>

																	  <p><tt>svn2ics</tt> allows you to use an <a href="http://en.wikipedia.org/wiki/Icalendar">iCalendar</a>-compliant calendar program such as <a href="http://www.apple.com/macosx/features/ical/">Apple iCal</a> or <a href="http://www.mozilla.org/projects/calendar/">Mozilla Sunbird</a> to view the activity of a <a href="http://subversion.tigris.org/">subversion</a> repository.  Paste the address of a subversion repository below, and click "Subscribe" to create a URL that you can subscribe to.</p>


<form action="svn2ics" method="GET">
<label for="location"><b>Repository location:</b></label><br/>
<input type="text" size="80" id="location" name="location" value="http://svn.openlaszlo.org/openlaszlo"><br/>
<input type="submit" value="Create URL"/>
</input>
</form>

<strong>Examples:</strong>
<div>
<?php webcal_to('http://svn.openlaszlo.org/openlaszlo')?>OpenLaszlo</a>
	| <?php webcal_to('http://dev.rubyonrails.org/svn/rails')?>Rails</a>
				  | <?php webcal_to('http://leetsoft.com/typo')?>Typo</a>
</div>

<img src="images/svn2ics-medium.png" style="margin-top: 10px"/>

																	  <?php } ?>

<div style="position: absolute; bottom: 0; width: 70%">
<hr/>
Copyright 2006 by <a href="/">Oliver Steele</a>.
</div>

<?php
  include('../includes/footer.php');
?>