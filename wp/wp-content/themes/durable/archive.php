<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

		<?php if (have_posts()) : ?>

		 <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
<?php /* If this is a category archive */ if (is_category()) { ?>				
		<h2 class="pagetitle">Archive for the '<?php echo single_cat_title(); ?>' Category</h2>
		
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
		
	 <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>

		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
		
	  <?php /* If this is a search */ } elseif (is_search()) { ?>
		<h2 class="pagetitle">Search Results</h2>
		
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle">Author Archive</h2>

		<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle">Blog Archives</h2>

		<?php } ?>

		<div class="navigation">
			<a href="<?php echo get_settings('home'); ?>" title="Back to Front Page">&laquo; Front Page</a> | <a href="javascript: toggleMenu('archives', 'menuItems');" title="Show the Archives Section">View Archives Menu &raquo;</a>
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

		<h2 class="center">Not Found</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>
	<hr />
	
	<div class="bottomTear"></div>
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>