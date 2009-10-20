<?php if (!defined ('ABSPATH')) die (); ?><div class="wrap">
	<h2><?php _e ('Permalink Migration', 'advanced-permalinks'); ?></h2>

	<p>Here you can specify old permalink structures which will be redirected (301) to your existing permalink structure.</p>
	
	<?php if (is_array ($permalinks) && count ($permalinks) > 0) : ?>
	<ul class="links" id="links">
		<?php foreach ($permalinks AS $pos => $link) : ?>
		<li id="item_<?php echo $pos ?>"><?php $this->render_admin ('migrate_item', array ('link' => $link, 'pos' => $pos)); ?></li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>

<div class="wrap">
	<h2><?php _e ('Add new permalink', 'advanced-permalinks'); ?></h2>
	<?php $this->render_admin ('add_migrate'); ?>
</div>