<?php
$file = $_GET['source'];
function sanitize($str) {
  $str = preg_replace('/&/', '&amp;', $str);
  $str = preg_replace('/</', '&lt;', $str);
  $str = preg_replace('/>/', '&gt;', $str);
  return $str;
}
function class_for_file($name) {
  return 'xml';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title><?php echo $file ?></title>
  <link type="text/css" rel="stylesheet" href="SyntaxHighlighter/SyntaxHighlighter.css"></link>
  <style type="text/css">
	/* @import url(SyntaxHighlighter.css); */

	body {
		font-family: Arial;
		font-size: 12px;
	}
  </style>
</head>

<body bgcolor="#cecece">

<textarea name="code" cols="80" rows="24" class="xml">
</textarea>

<textarea name="code" cols="80" rows="24" class="<?php echo class_for_file($file)?>">
<?php echo(sanitize(file_get_contents($file))); ?>
<?php echo(file_get_contents($file)); ?>
</textarea>
  
<script language="javascript" src="SyntaxHighlighter/shCore.js"></script>
<script language="javascript" src="SyntaxHighlighter/shBrushPhp.js"></script>
<script language="javascript" src="SyntaxHighlighter/shBrushJScript.js"></script>
<script language="javascript" src="SyntaxHighlighter/shBrushXml.js"></script>
<script language="javascript" src="SyntaxHighlighter/shBrushPython.js"></script>
<script language="javascript">
dp.SyntaxHighlighter.HighlightAll('code');
</script>

</body>
</html>
