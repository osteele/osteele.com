<?php
function backquote_subst($s) {
	return '<code>'.$s[1].'</code>';
}

function titleize($s) {
	return preg_replace_callback('|`(.*?)`|', 'backquote_subst', $s);
}

function show_section_list($names) {
	foreach ($names as $name) {
		$url = $name.'-1.js.html';
		echo '<li><a href="'.$url.'" target="fjs_sample">'.ucwords($name)."</a>";
		$_GET['file'] = $name.'.js';
		include('section-index.php');
		echo "</li>\n";
	}
}
?>