<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

	<?php if (have_posts()) : ?>

		<h2 class="pagetitle">Search Results</h2>
		
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>


		<? $post_counter = 1;  ?>
		<?php while (have_posts()) : the_post(); ?>
		<div class="mini-post">
				<div class="theDate" title="<? the_time('F jS, Y') ?>"><span class="theMonth"><?php the_time('M'); ?></span><span class="theDay"><? the_time('j'); ?></span></div>
				<h2><a class="postHeading" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<div class="numComments"><?php comments_popup_link('0 Comments', '1 Comment', '% Comments'); ?></div>
				
				<div class="entry">
					<? the_excerpt(); ?>
				</div>
				
				<p><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">Continue Reading &raquo;</a></p>
				<p class="categories">Filed in: <?php the_category(' ') ?> <?php edit_post_link('<strong>Edit</strong>', '', ' '); ?></p> 
		</div>
		
		<? if( $post_counter % 2 == 0) {?><hr /><? } ?>
		
		<? $post_counter++; ?>
		
		<?php endwhile; ?>
		
		<hr />

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>
	
	<?php else : ?>

		<h2 class="center">No posts found. Try a different search?</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>
	
	<div class="bottomTear"></div>		
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>