<?php foreach ($output as $group => $contents) { ?>
<div class="relatedcontent-nodes <?php echo $group; ?>">
  <?php if ($grouped) { ?><h3><?php echo relatedcontent_group_title($grouped, $group, $node_type) ?></h3><?php } ?>
  <?php foreach ($contents as $content) { echo $content; } ?>
</div>
<?php } ?>
