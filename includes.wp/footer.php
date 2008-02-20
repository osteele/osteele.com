<?php if ($_SERVER['HTTP_HOST'] != 'osteele.dev') { ?>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">_uacct = "UA-202010-1";urchinTracker();</script>
<script type="text/javascript" src="http://edge.quantserve.com/quant.js"></script>
<script type="text/javascript">_qacct="p-52aYZlc3ACyMc";quantserve();</script>
<?php } ?>
<?php if (isset($_GET['debug']) && false) { ?>
    <a href="#" onclick="eraseLog(); return false">clear</a>
    <div id="fvlogger"></div>
<?php } ?>
  </body>
</html>
