<?php if (!defined ('ABSPATH')) die (); ?><div class="options">
	<a href="#delete" onclick="return delete_migration(<?php echo $pos ?>)"><img src="<?php echo $this->url () ?>/images/delete.png" width="16" height="16" alt="Delete"/></a>
</div>

<a href="#edit" onclick="return edit_migration(<?php echo $pos ?>)">
	<?php echo $link ?>
</a>