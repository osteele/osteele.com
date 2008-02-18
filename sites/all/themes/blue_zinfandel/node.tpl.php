<?php // $Id: node.tpl.php,v 1.5 2007/05/12 02:15:46 webchick Exp $ ?>
  <div class="contentdate">
    <h3><?php print $month; ?></h3>
    <h4><?php print $day; ?></h4>
  </div>
  <div class="node<?php if ($sticky) { print " sticky"; } ?><?php if (!$status) { print " node-unpublished"; } ?>">
    <?php if ($picture) {
      print $picture;
    }?>
    <div class="contenttitle">
    <h1><a href="<?php print $node_url?>"><?php print $title?></a></h1>
    <p><?php print format_date($node->created); ?>
    <?php if (!$page && isset($comment_link)) { // We're in teaser view ?>
       | <?php print $comment_link; ?>
    <?php }; ?>
    </p>
    </div>
    <div class="content"><?php print $content?></div>
    <?php if ($terms) { ?><div class="taxonomy"><?php print t('Tags: ') . $terms; ?></div><?php }; ?>
    <?php if ($links) { ?><div class="links">&raquo; <?php print $links?></div><?php }; ?>
  </div>

  <div style="clear: both;"></div>
  <div class="postspace">

  </div>
