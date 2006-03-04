<?php header("Content-type: text/html; charset=utf-8"); ?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>reAnimator: Regular Expression FSA Visualizer</title>
  <meta name="description" content="reAnimator is an interactive visualization of how finite-state automata are used to match regular expressions."/>
<style type="text/css">
html, body { margin: 0; padding: 0; height: 100%; }
#flashcontent { height: 100%; }
.fallback { margin: 5px }
</style></head>
<script type="text/javascript" src="/javascript/flashobject.js"></script>
<body>

<div id="flashcontent">
<div class="fallback">
  <p>This application requires the Flash plugin.  If the plugin is already installed, click <a href="?detectflash=false">here</a>.</p>
</div>
</div>

<script type="text/javascript">
var fo = new FlashObject("rematch.swf", "rematch", "100%", "100%", "7", "#FFFFFF");
fo.addParam("scale", "noscale");
fo.addVariable("source", "server");
fo.addVariable("lzproxied", "false");
fo.write("flashcontent");
</script>

<?php include('../../includes/footer.php'); ?>
