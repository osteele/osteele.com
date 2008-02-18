  <div class="comment<?php print ($comment->new) ? ' comment-new' : ''; print (isset($comment->status) && $comment->status  == COMMENT_NOT_PUBLISHED) ? ' comment-unpublished' : ''; print ' '. $zebra; ?>">
  
    <?php if ($picture) { print $picture; } ?>

    <?php if ($comment->new): ?>
      <span class="new"><?php print $new ?></span>
    <?php endif; ?>

    <div class="commentTitle">
      <?php print $title; ?>
    </div>

    <div class="submitted">
      <?php print $submitted ?>
    </div>

    <div class="content">
      <?php print $content ?>
      <?php if ($signature): ?>
        <div class="user-signature clear-block">
          <?php print $signature ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="links">
      <?php print $links; ?>
    </div>
  </div>
  
