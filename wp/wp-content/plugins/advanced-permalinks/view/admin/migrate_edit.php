<?php if (!defined ('ABSPATH')) die (); ?><form action="<?php echo $this->url () ?>/ajax.php" method="post" accept-charset="utf-8" id="form_<?php echo $pos ?>">
	<input type="text" size="40" name="permalink" value="<?php echo $migration ?>"/>	
	
	<input type="hidden" name="cmd" value="save_migrate"/>
	<input type="hidden" name="id" value="<?php echo $pos ?>"/>
	
	<input type="submit" name="save" value="Save"/>
</form>

<script type="text/javascript" charset="utf-8">
	$('#form_<?php echo $pos ?>').ajaxForm (function() { $('#item_<?php echo $pos ?>').load (wp_apl_base, { cmd: 'show_migrate', id: <?php echo $pos ?>});});
</script>