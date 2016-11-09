<ol>
<?php
include_once 'support.php';
$file = $_GET['file'];
$content = file_get_contents('samples/'.$file);
$count = preg_match_all('|// section (.*?)(?::(.*))?\n|', $content, $matches);
for ($i = 0; $i < $count; $i++) {
	$key = $matches[1][$i];
	$basename = preg_replace('|\.js|', '', $_GET['file']);
	$url = $basename.'-'.$key.'.js.html';
	$title = $matches[2][$i];
	if (!$title) $title = $key;
	$title = titleize($title);
	echo '<li><a href="'.$url.'" title="<? echo $title; ?>">'.$title.'</a></li>';
}
?>
</ol>
