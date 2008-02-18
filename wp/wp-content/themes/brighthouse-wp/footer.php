      </div>
<div id="footer-wrapper">
      <div id="footer">
        <p><?php bloginfo('name'); ?> runs on <a href="http://www.wordpress.org">WordPress</a> and <a href="http://max.limpag.com/">WP-Brighthouse</a> theme.</p>
      </div>
  </div>
</div>
<?php wp_footer(); ?>
<?php if ($_SERVER['HTTP_HOST'] != 'osteele.dev') { ?>
    <script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
    </script>
    <script type="text/javascript">
      _uacct = "UA-202010-1";
      urchinTracker();
    </script>
<?php } ?>
</body>
</html>
