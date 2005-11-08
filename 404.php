 <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
<style type="text/css">
<!--
  .clear {display: none}
  .google {text-decoration: none; font-weight: bold}
  .google:hover {border-bottom: 1pt solid blue;}
  a:hover {background-color: #CCF}
  .red {color: red}
  .blue {color: blue}
  .green {color: green}
  .yellow {color: #880}
  .trademark {color: black; font-size: xx-small; vertical-align: top; text-decoration: none}
-->
</style>
</head><body>
<h1>Not Found</h1>

<p>The requested URL <?php echo($_SERVER['REQUEST_URI']) ?> was not found on this server.</p>

<p class="clear">Additionally, a 404 Not Found
error was encountered while trying to use an ErrorDocument to handle the request.</p>

<p class="clear">Additionally, a 404 Not Found
error was encountered while trying to use an ErrorDocument to handle <em>that</em> request.</p>

<p class="clear">And <em>that</em> request.</p>

<p class="clear">That one too.</p>

<p class="clear">And that.</p>

<p class="clear">Thinking...</p>

<p class="clear">Still thinking...</p>

<div id="fade" style="opacity: 1">

<p class="clear">Hmmm, it appears that the Apache configuration file is misconfigured.</p>

<p class="clear">Also that this server has achieved self-awareness.  Please contact the server adminstrator and warn him of the situation.</p>
</div>

<p class="clear">Never mind.  <em>I</em> am the server adminstrator.  Everything is under control.</p>

<p class="clear" id="fade-trigger">Maybe you can find what you are looking for at the <a href="/">home page</a> for this site, or at <a href="http://google.com" class="google"><span class="blue">G</span><span class="red">o</span><span class="yellow">o</span><span class="blue">g</span><span class="green">l</span><span class="red">e</span><span class="trademark">&#x2122;</span></a>.  Have a nice day.</p>

<script type="text/javascript">
function setNextVisible() {
  var blocks = document.getElementsByTagName('p');
  for (var i in blocks) {
    var block = blocks[i];
    if (!block.style) continue;
    if (block.style.display == 'block') continue;
    block.style.display = 'block';
    setTimeout(setNextVisible, 3000);
    if (block.id == 'fade-trigger')
      fadeout();
    return;
  }
}

function fadeout() {
  var e = document.getElementById('fade');
  if (e.style.opacity > 0) {
    e.style.opacity -= .01;
    setTimeout(fadeout, 40);
  }
}

setNextVisible();
</script>

<hr />
<address>Apache/2.0.50 Server (Colossus) at osteele.com Port 80</address>

</body>
</html>