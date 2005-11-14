<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post">
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<small><?php relativeDate(get_the_time('YmdHis')) ?></small>
				
				<div class="thumbnail"><?php echo(c2c_get_custom('thumbnail', '<a href="'.get_permalink().'"><img src="', '" alt="" /></a>')); ?></div>
				<div class="entry">
					<?php the_excerpt('Read the rest of this entry &raquo;'); ?>
<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">More &raquo;</a>
				</div>
		
				<p class="postmetadata"><!--Posted in <?php the_category(', ') ?>-->
      <span>Tags:
        <span class="utwtags" id="tags-<?php the_ID(); ?>">
	      <?php UTW_ShowTagsForCurrentPost("commalisticons"); ?>
        </span>
        <?php global $user_ID; if ($user_ID) { UTW_AddTagToCurrentPost("simplelist"); } ?>
      </span>

  <strong>|</strong> <?php edit_post_link('Edit','','<strong>|</strong>'); ?>  <?php comments_popup_link('No&nbsp;Comments&nbsp;&#187;', '1&nbsp;Comment&nbsp;&#187;', '%&nbsp;Comments&nbsp;&#187;'); ?></p> 
				
				<!--
				<?php trackback_rdf(); ?>
				-->
			</div>
	
		<?php endwhile; ?>

		<div class="navigation">
			<!--div class="alignleft"><?php posts_nav_link('','','&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php posts_nav_link('','Next Entries &raquo;','') ?></div-->
			<div class="alignleft"><?php wp_pagenavi()?></div>
		</div>
		
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center"><?php _e("Sorry, but you are looking for something that isn't here."); ?></p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
