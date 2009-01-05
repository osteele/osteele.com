<?php if (!defined ('ABSPATH')) die (); ?><div class="options">
	<a href="#delete" onclick="return delete_link(<?php echo $start ?>)"><img src="<?php echo $this->url () ?>/images/delete.png" width="16" height="16" alt="Delete"/></a>
</div>

<a href="#edit" onclick="return edit_link(<?php echo $start ?>)">
	<?php _e ('Post', 'advanced-permalinks'); ?> <?php echo $start; ?><?php if ($link['end'] != $start) echo '-'.$link['end'] ?>

&raquo; <code><?php echo $link['link']?></code></a>