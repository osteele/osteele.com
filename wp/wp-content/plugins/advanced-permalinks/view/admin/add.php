<?php if (!defined ('ABSPATH')) die (); ?>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" accept-charset="utf-8"<?php if ($edit) : ?>onsubmit="return save_link (this)"<?php endif; ?>>
	<table>
		<tr>
			<th><?php _e ('Start ID', 'advanced-permalinks'); ?>:</th>
			<td><input size="5" type="text" name="start" value="<?php echo $start ? $start : 0 ?>"/> <strong><?php _e ('End ID', 'advanced-permalinks'); ?>:</strong> <input size="5" type="text" name="end" value="<?php echo $end ? $end : -1 ?>"/></td>
		</tr>
		<tr>
			<th><?php _e ('Permalink', 'advanced-permalinks'); ?>:</th>
			<td><input size="40" type="text" name="permalink" value="<?php echo $link ? $link : '' ?>"/></td>
		</tr>
		<tr>
			<td></td>
			<td>
			<?php if ($edit) : ?>
				<input class="button-primary" type="submit" name="save" value="<?php _e ('Save', 'advanced-permalinks'); ?>"/>
				<input class="button-secondary" type="submit" name="cancel" value="<?php _e ('Cancel', 'advanced-permalinks'); ?>" onclick="return cancel_link (<?php echo $start ?>)"/>
				<input type="hidden" name="cmd" value="save"/>
				<input type="hidden" name="id" value="<?php echo $start ?>"/>
			<?php else : ?>
				<input class="button-primary" type="submit" name="add" value="<?php _e ('Add', 'advanced-permalinks'); ?>" id="add"/>
			<?php endif; ?>
			</td>
		</tr>
	</table>
</form>