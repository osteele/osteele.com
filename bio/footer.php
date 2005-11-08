<!-- END #content -->
</div>


<div class="footer">
<span class="timings">
<?php $renderTime = microtime() - $startTime; ?>
Page rendered in <?php echo(round($renderTime, 2)) ?> seconds
</span>

<p>
<cite id="credit">
Copyright 1998-2004 by Oliver Steele.  All rights reserved.<br/>
<?php
if (isset($isblog)):
  echo sprintf(__("Powered by <a href='http://wordpress.org' title='%s'><strong>WordPress</strong></a><br/>"), __("Powered by WordPress, state-of-the-art semantic personal publishing platform"));
endif;
?>
Powered by Linux, Apache, PHP, and DreamHost.
</cite>
</p>
</div>

<!-- BEGIN #container -->
</div>

</body>
</html>
