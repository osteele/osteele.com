<?php get_header(); ?>
		<div id="main-wrapper">
			<div id="main-content">
			<!-- google_ad_section_start -->
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<div class="post"><h2><a href="<?php the_permalink() ?>" rel="bookmark" title='Click to read: "<?php strip_tags(the_title()); ?>"'><?php the_title(); ?></a></h2>
			<p class="auth">Posted by <?php the_author(); ?> <span class="typo_date"><?php the_time('F d, Y') ?></span></p>
			<?php the_content("Continue reading '" . the_title('', '', false) . "'"); ?>
					</div>
     
			<?php endwhile; ?>

		<p id="pagination">
<?php next_posts_link('&laquo; Older blog posts') ?> <?php previous_posts_link('&bull; Newer blog posts &raquo;') ?></p>
		<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>
		<?php endif; ?>
<!-- google_ad_section_end -->
		</div>
 <?php get_sidebar(); ?>
 <?php get_footer(); ?>
