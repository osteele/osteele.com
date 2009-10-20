<?php if (!defined ('ABSPATH')) die (); ?><?php foreach ($links AS $start => $permalink) : ?>
	<li id="item_<?php echo $start ?>"><?php $this->render_admin ('permalinks_item', array ('link' => $permalink, 'start' => $start))?></li>
<?php endforeach; ?>