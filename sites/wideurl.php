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
<title>WideURL.com</title>
<style type="text/css"><!--
body {max-width: 600px; margin-left: auto; margin-right: auto}
em {font-weight: bold; font-style: normal}
pre {padding-left: 20px; padding-right: 20px}
form {background: #E7E7F7; padding: 10px;}
form div {margin-left: auto; margin-right: auto; width: 400px}
#footer {border-top: 1px solid black; font-size: small; background: #ddf}
.logo {color: red}
h1 {font-size: xx-large; text-align: center; background: blue; color: white; padding: 20px}
h1 a {color: white; text-decoration: none}
} 
--></style>
</head>
<body>

<h1><a href="/">WideURL</a></h1>

<?php if ($_GET['url']) { 
		 $source = $_GET['url'];
		 $wide = makewideurl($_GET['url']);
?>
<h2><span class="logo">W-i-d-e-U-R-L</span> was created!!!</h2>

<p>The following URL:</p>
<pre><?=$source?></pre>
	 <span>has a length of <?=strlen($source)?> characters and resulted in the following <span class="logo">W-i-d-e-U-R-L</span> which has a length of <?=strlen($wide)?> characters:</span>
<pre><?=$wide?></pre>
<p><small>[<a href="<?=$wide?>" target="_blank">Open in new window</a>]</small></p>

<h2>Too Long?</h2>
<p><span class="logo">W-i-d-e-U-R-L</span> can be used together with <a href="http://tinyurl.com">TinyURL.com</a> to create a short representation of a wide URL.  Click <a href="http://tinyurl.com/create.php?url=<?=$wide?>">here</a> to create a TinyURL of the <span class="logo">WideURL</span> above.</p>

<h2>Make another <span class="logo">W-i-d-e-U-R-L</span>!!!</h2>
<form action="." method="get">
<b>Enter another tiny URL to make w-i-d-e:</b><br /><input type="text" name="url" size="30"><input type="submit" name="submit" value="Make W-i-d-e-U-R-L!!!">
</form>

			 <?php } else { ?>

<h2>Welcome to <span class="logo">W-i-d-e-U-R-L</span>!!!</h2>
<p class="intro">Do the tiny URLs that you send to your friends and colleagues lack the visual significance that you'd like to associate with your messages? Then you've come to the right place. By entering a URL into the text field below, you can create a wide URL that <em>creates visual impact</em> and is <em>hard to miss</em>.</p>

<form action="." method="get">
<div>
<b>Enter a short URL to make it wide:</b><br /><input type="text" name="url" size="30"><input type="submit" name="submit" value="Make W-i-d-e-U-R-L!!!">
</div>
</form>

<h2><a name="example"></a>An example</h2>
<p>Turn this URL:</p>
<pre>http://osteele.com/projects</pre> into this <span class="logo">W-i-d-e-U-R-L</span>: <pre>http://wideurl.dev/aitch-tee-tee-pea-colon-
slash-slash-oh-ess-tee-ee-ee-ell-ee-
dot-see-oh-em-slash-pea-are-oh-jay-ee-see-tee-ess</pre>
<p>Which one would you rather present to your boss? That's the power of <span class="logo">W-i-d-e-U-R-L</span>!</p>

<h2><a name="toolbar"></a><span class="logo">W-i-d-e-U-R-L</span> bookmarklet</h2>
<p>Click and drag the following link to your <i>links</i> toolbar.
	<blockquote><a href="javascript:void(location.href='http://wideurl.com/?url='+location.href)" onclick="alert('Drag this to your browser toolbar.'); return false">W-i-d-e-U-R-L!!!</a></blockquote>
Once this is on your toolbar, you'll be able to make a <span class="logo">W-i-d-e-U-R-L</span> at the click of a button. By clicking on the toolbar button, a <span class="logo">W-i-d-e-U-R-L</span> will be created for the current page.
</p>

<?php } ?>

<div id="footer">Copyright 2006 by Oliver Steele.</div>
</body>
</html>