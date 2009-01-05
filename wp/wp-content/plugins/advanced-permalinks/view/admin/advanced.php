<?php if (!defined ('ABSPATH')) die (); ?><div class="wrap">
	<h2><?php _e ('Advanced Permalinks', 'advanced-permalinks'); ?></h2>

	<p><?php _e ('Here you can override the default permalink structures.  Note that you can still use the same <a href="http://codex.wordpress.org/Using_Permalinks">permalink tags</a>.  Leave an entry blank to keep the default.', 'advanced-permalinks'); ?></p>
	<?php $myrewrite = new WP_Rewrite () ?>

	<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8">
		<table class="permalinks">
			<tr>
				<th><?php _e ('Author pages', 'advanced-permalinks'); ?>:</th>
				<td><input size="40" type="text" name="link[author_structure]" value="<?php echo htmlspecialchars ($options['permalinks']['author_structure']); ?>"/> <span class="sub">default: <code><?php echo $myrewrite->get_author_permastruct () ?></code></span></td>
			</tr>

			<tr>
				<th><?php _e ('Category pages', 'advanced-permalinks'); ?>:</th>
				<td><input size="40" type="text" name="link[category_structure]" value="<?php echo htmlspecialchars ($options['permalinks']['category_structure']); ?>"/> <span class="sub">default: <code><?php echo $myrewrite->get_category_permastruct () ?></code></span></td>
			</tr>
		
			<tr>
				<th><label for="period"><?php _e ('Allow period in URL'); ?>:</label></th>
				<td><input type="checkbox" name="periods" id="period"<?php if ($options['periods']) echo ' checked="checked"' ?>/>
					<span class="sub">Checking this allows URLs such as <code>thing.html</code> and is ideal for migrating old websites</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="save" value="<?php _e ('Save permalinks', 'advanced-permalinks'); ?>"/></td>
			</tr>
		</table>
		
		<?php if ($this->is_25 ()) : ?>
		<p>Extra Rules:</p>
		<textarea name="extra" style="width: 95%" rows="5"><?php echo htmlspecialchars ($options['extra']); ?></textarea>
		<p>Syntax: MATCH = QUERY (a space must be present either side of the = sign)</p>
		<p>e.g. <code>articles/inside-wordpress = index.php?&pagename=articles/inside-wordpress&page=</code></p>
		<p>This is for advanced users only - enter rules at your own risk!</p>
		<?php endif; ?>
	</form>
</div>