<form method="get" id="searchform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<p>
<label for="s"><?php _e('Search this site:'); ?></label><br />
<input type="text" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s" />
<input type="submit" id="searchsubmit" value="<?php _e('Search'); ?>" />
</p>
</form>