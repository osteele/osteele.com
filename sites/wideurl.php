<?php
$chars = array('NULL', 'SOH', 'STX', 'ETX', 'EOT', 'ENQ', 'ACK', 'BEL',
			   'BS', 'TAB', 'LF', 'VT', 'FF', 'CR', 'SO', 'SI',
			   'DLE', 'DC1', 'DC2', 'DC3', 'DC4', 'NAK', 'SYN', 'ETB',
			   'CAN', 'EM', 'SUB', 'ESC', 'FS', 'GS', 'RS', 'US',
			   'space', 'bang', 'quote', 'hash', 'cash', 'mod', 'and',
			   'tick', 'open', 'close', 'splat', 'plus', 'comma', 'minus',
			   'dot', 'slash',
			   'zero', 'one', 'two', 'three', 'four', 'five', 'six',
			   'seven', 'eight', 'nine', 'colon', 'semi',
			   'less', 'equals', 'more', 'query', 'at',
			   'aye', 'bee', 'see', 'dee', 'ee', 'eff', 'gee',
			   'aitch', 'eye', 'jay', 'kay', 'ell', 'em', 'en',
			   'oh', 'pea', 'cue', 'are', 'ess', 'tee', 'you',
			   'vee', 'doubleyou', 'ex', 'why', 'zee',
			   'bra', 'slant', 'ket', 'hat', 'score', 'backtick');
$final_chars = array(
					 'brace', 'pipe', 'unbrace', 'twiddle', 'DEL');
for ($i = 1; $i <= 26; $i++) {
	$chars[96+$i] = $chars[64+$i];
	$chars[64+$i] = strtoupper($chars[64+$i]);
 }
for ($i = 123; $i <= 127; $i++)
	$chars[$i] = $final_chars[$i-123];

$names = array();
foreach ($chars as $index => $name) {
	$names[$name] = chr($index);
}

$digraphs = array(//'url' => 'http://',
				  'wubbleyou' => 'www.');

function encode($string) {
	global $chars, $digraphs;
	$s = array();
	$i = 0;
	while ($i < strlen($string)) {
		foreach ($digraphs as $name => $pattern)
			if (substr($string, $i, strlen($pattern)) == $pattern) {
				$s[] = $name;
				$i += strlen($pattern);
				continue 2;
			}
		$c = $string[$i++];
		$s[] = $chars[ord($c)];
	}
	return join('-', $s);
}

function decode($string) {
	global $names, $digraphs;
	$string = preg_replace('/\s+/', '', $string);
	$words = split('-', $string);
	$s = array();
	foreach ($words as $word) {
		$out = $names[$word];
		if (!$out) $out = $names[$word];
		if (!$out) $out = $digraphs[$word];
		if (!$out) $out = $names[strtolower($word)];
		if (!$out) $out = $digraphs[strtolower($word)];
		if (!$out) $out = $names[strtoupper($word)];
		if (!$out) $out = '<'.$word.'>';
		$s[] = $out;
	}
	return join('', $s);
}

function makewideurl($url) {
	return "http://{$_SERVER['HTTP_HOST']}/".encode($url);
}

$location = preg_replace('|^.*/|', '', $_SERVER['REQUEST_URI']);
$location = preg_replace('|\?.*$|', '', $location);
if ($location) {
	header("Location: ".decode($location));
	exit;
 }
?>
<html>
<head>
<title>W-i-d-e-U-R-L.com</title>
<style type="text/css"><!--
body {max-width: 600px; margin-left: auto; margin-right: auto}
em {font-weight: bold; font-style: normal}
pre {padding-left: 20px; padding-right: 20px}
form {background: #E7E7F7; padding: 10px;}
form div {margin-left: auto; margin-right: auto; width: 400px}
} 
--></style>
</head>
</body>
<h2>Welcome to W-i-d-e-U-R-L!&trade;</h2>
<p class="intro">Do the tiny URLs that you send to your friends and colleagues lack the visual significance that you'd like to associate with your messages? Then you've come to the right place. By entering a URL into the text field below, you can create a wide URL that <em>creates visual impact</em> and <em>is hard to miss</em>.</p>

<form action="." method="get">
<div>
<b>Enter a short URL to make it wide:</b><br /><input type="text" name="url" size="30"><input type="submit" name="submit" value="Make W-i-d-e-U-R-L!">
</div>
</form>

<?php
if ($_GET['url']) {
	echo $_GET['url'];
	echo " => ";
	echo makewideurl($_GET['url']);
 }
?>

<h2><a name="example"></a>An example</h2>
<p>Turn this URL:</p>
<pre>http://osteele.com/sources/javascript</pre> into this W-i-d-e-U-R-L: <pre>http://www.wideurl.com/aitch-tee-tee-pea-colon-slash-slash-
oh-ess-tee-ee-ee-ell-ee-dot-see-oh-em-slash-ess-oh-you-are-
see-ee-ess-slash-jay-aye-vee-aye-ess-see-are-eye-pea-tee</pre>
<p>Which one would you rather present to your boss? That's the power of W-i-d-e-U-R-L!</p>

<h2><a name="toolbar"></a>W-i-d-e-U-R-L bookmarklet</h2>
<p>Click and drag the following link to your <i>links</i> toolbar.
	<blockquote><a href="javascript:void(location.href='http://www.wideurl.com/?url='+location.href.escape())" onclick="alert('Drag this to your browser toolbar.'); return false">W-i-d-e-U-R-L!!!</a></blockquote>
Once this is on your toolbar, you'll be able to make a W-i-d-e-U-R-L at the click of a button. By clicking on the toolbar button, a W-i-d-e-U-R-L will be created for the current page.
</p>

<p>In Internet Explorer, click the link with your right mouse button and select "Add to Favorites..." from the menu. Click OK if a security warning alert pops-up (this shows up since the link contains javscript). If a list of folders is not shown, click the "Create&nbsp;in&nbsp;>>>" button (see image at right). Now select the folder called "Links" and then click OK. You should now see the W-i-d-e-U-R-L on your links toolbar, if not, see the last paragraph above.</p>

<div id="footer">Copyright 2006 by Oliver Steele.</div>
</body>
</html>