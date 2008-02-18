<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

  if (!empty($post->post_password)) { // if there's a password
    if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
?>

      <p class="nocomments">This post is password protected. Enter the password to view comments.<p>

<?php
      return;
    }
  }
?>

<!-- You can start editing here. -->
<?php if ($comments) : ?>

<?php /* Count the totals */
$numPingBacks = 0;
$numComments  = 0;

foreach ($comments as $comment) {
if (get_comment_type() != "comment") {
$numPingBacks++;
} else {
$numComments++;
}
}
?>

<?php if ($numComments != 0) : ?>
<h4 class="blueblk"><?php comments_number('No Responses', 'One Response', '% Responses' );?> to &#8220;<?php the_title(); ?>&#8221;</h4>
<h4 class="blueblk">Comments</h4>
<ol class="commentlist">
<?php foreach ($comments as $comment) : ?>
<?php if (get_comment_type() == "comment"){ ?>
<li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
<cite>
<span class="author"><?php comment_author_link() ?></span>
<span class="date"><?php comment_date('M d Y') ?> / <?php comment_date('ga') ?></span>
</cite>
											<?php cp_comment_header() ?>

<div class="content">
<?php if ($comment->comment_approved == '0') : ?>
<em>Your comment is awaiting moderation.</em>
<?php endif; ?>
<?php comment_text() ?>
</div>
<div class="clear"></div>
											<?php cp_comment_footer() ?>
</li>
<?php /* Changes every other comment to a different class */
if ('alt' == $oddcomment) $oddcomment = '';
else $oddcomment = 'alt';
?>
<?php } ?>
<?php endforeach; /* end for each comment */ ?>
</ol>
<?php endif; ?>

<?php if ($numPingBacks != 0) : ?>
<h4 class="blueblk">Blog posts on this article</h4>
<ol class="commentlist">
<?php foreach ($comments as $comment) : ?>
<?php if (get_comment_type() != "comment"){ ?>
<li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
<cite>
<span class="author"><?php comment_author_link() ?></span>
<span class="date"><?php comment_date('M d Y') ?> / <?php comment_date('ga') ?></span>
</cite>

<div class="content">
<?php if ($comment->comment_approved == '0') : ?>
<em>Your comment is awaiting moderation.</em>
<?php endif; ?>
<?php comment_text() ?>
</div>
<div class="clear"></div>
</li>
<?php /* Changes every other comment to a different class */
if ('alt' == $oddcomment) $oddcomment = '';
else $oddcomment = 'alt';
?>
<?php } ?>

<script type="text/javascript"><!--
if(!mmcomments){var mmcomments=[];}mmcomments[mmcomments.length]="<?php comment_ID(); ?>";
//--></script>
<!-- mmc mmid:<?php comment_ID(); ?> mmdate:<?php comment_date('YmdHis') ?> mmauthor:<?php comment_author() ?> -->

<?php endforeach; /* end for each comment */ ?>
</ol>
<?php endif; ?>


<?php else : // this is displayed if there are no comments so far ?>

  <?php if ('open' == $post->comment_status) : ?> 
		<!-- If comments are open, but there are no comments. -->
		
	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments">Comments are closed.</p>
		
	<?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

<h4 class="blueblk">Leave a Reply</h4>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">logged in</a> to post a comment.</p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account">Logout &raquo;</a></p>

<?php else : ?>

<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
<label for="author"><small>Name <?php if ($req) echo "(required)"; ?></small></label></p>

<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
<label for="email"><small>Mail (will not be published) <?php if ($req) echo "(required)"; ?></small></label></p>

<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
<label for="url"><small>Website</small></label></p>

<?php endif; ?>

<!--<p><small><strong>XHTML:</strong> You can use these tags: <?php echo allowed_tags(); ?></small></p>-->

<p><textarea name="comment" id="comment" cols="60%" rows="10" tabindex="4"></textarea></p>

<p><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
</p>
<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>
