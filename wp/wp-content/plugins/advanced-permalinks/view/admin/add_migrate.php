<?php if (!defined ('ABSPATH')) die (); ?><form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" accept-charset="utf-8"<?php if ($start) : ?>onsubmit="return save_link (this)"<?php endif; ?>>
	<table>
		<tr>
			<th><?php _e ('Old permalink', 'advanced-permalinks'); ?>:</th>
			<td>
				<input size="40" type="text" name="permalink" value="<?php echo $link ? $link : '' ?>"/>

				<?php if ($start) : ?>
					<input type="submit" name="save" value="<?php _e ('Save', 'advanced-permalinks'); ?>"/>
					<input type="submit" name="cancel" value="<?php _e ('Cancel', 'advanced-permalinks'); ?>" onclick="return cancel_link (<?php echo $start ?>)"/>
					<input type="hidden" name="cmd" value="save"/>
					<input type="hidden" name="id" value="<?php echo $start ?>"/>
				<?php else : ?>
					<input type="submit" name="add" value="<?php _e ('Add', 'advanced-permalinks'); ?>" id="add"/>
				<?php endif; ?>
			</td>
		</tr>
	</table>
</form>