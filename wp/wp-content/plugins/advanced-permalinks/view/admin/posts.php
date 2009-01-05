<?php if (!defined ('ABSPATH')) die (); ?><div class="wrap">
	<h2><?php _e ('Advanced Post Permalinks', 'advanced-permalinks'); ?></h2>

	<p><?php _e ('Here you can assign permalinks to a specific post or a range of posts.  Specify a start and end post ID and a <a href="http://codex.wordpress.org/Using_Permalinks">permalink structure</a>.', 'advanced-permalinks'); ?></p>
	<p><?php _e ('Use <strong>ID 0</strong> to indicate the <strong>first post</strong>, and <strong>ID -1</strong> to indicate the <strong>last</strong>.  It is your responsibility to ensure that IDs do not overlap.', 'advanced-permalinks'); ?></p>
	
	<?php if (is_array ($permalinks) && count ($permalinks) > 0) : ?>
	<ul class="links" id="links">
		<?php $this->render_admin ('permalinks', array ('links' => $permalinks)); ?>
	</ul>
	<?php endif; ?>
</div>

<div class="wrap">
	<h2><?php _e ('Add new permalink', 'advanced-permalinks'); ?></h2>
	<?php $this->render_admin ('add'); ?>
	
	<p><?php _e ('Remember that posts not in the specified range will use the default permalink structure.  If you want to change all future posts to a new permalink structure but retain existing URLs then use 0 as the start and -1 as the end.', 'advanced-permalinks'); ?></p>
</div>