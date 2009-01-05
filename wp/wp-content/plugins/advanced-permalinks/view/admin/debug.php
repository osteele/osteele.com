<?php if (!defined ('ABSPATH')) die (); ?><div class="wrap">
	<h2><?php _e ('Advanced Permalinks Debug', 'advanced-permalinks'); ?></h2>
	
	<p><?php _e ('This screen contains debug information which you can use when reporting a bug with the <a href="http://urbangiraffe.com/plugins/advanced-permalinks/">plugin</a>.', 'advanced-permalinks'); ?></p>
	
	<textarea readonly="readonly" style="width: 98%" rows="20"><?php echo htmlspecialchars (print_r ($rewrite, true)); ?></textarea>
	
	<p><?php _e ('<strong>Please do not post this on the Advanced Permalinks page</strong>.  Instead, send it along with an <a href="http://urbangiraffe.com/contact/">email</a>.', 'advanced-permalinks'); ?></p>
</div>