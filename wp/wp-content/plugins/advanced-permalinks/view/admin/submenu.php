<?php if (!defined ('ABSPATH')) die (); ?>
<ul class="subsubsub" style="margin-left: 15px">
  <li><a <?php if ($sub == '') echo 'class="current"'; ?>href="<?php echo $url ?>"><?php _e ('Defaults', 'advanced-permalinks') ?></a> |</li>
  <li><a <?php if ($sub == 'advanced') echo 'class="current"'; ?>href="<?php echo $url ?>?sub=advanced"><?php _e ('Advanced', 'advanced-permalinks') ?></a> |</li>
  <li><a <?php if ($sub == 'post') echo 'class="current"'; ?>href="<?php echo $url ?>?sub=post"><?php _e ('Posts', 'advanced-permalinks') ?></a> |</li>
  <li><a <?php if ($sub == 'migrate') echo 'class="current"'; ?>href="<?php echo $url ?>?sub=migrate"><?php _e ('Migration', 'advanced-permalinks') ?></a> |</li>
  <li><a <?php if ($sub == 'debug') echo 'class="current"'; ?>href="<?php echo $url ?>?sub=debug"><?php _e ('Debug', 'advanced-permalinks') ?></a></li>
</ul>

<?php if ($sub) : ?>
<script type="text/javascript" charset="utf-8">
	window.onload = '';
</script>
<?php endif; ?>