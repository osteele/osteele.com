<?php get_header(); ?>

		<div id="main-wrapper">
			<div id="main-content">
<!-- google_ad_section_start -->
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<div class="post"><h2><?php the_title(); ?></h2>
			<p class="auth">Posted by <?php the_author(); ?> <span class="typo_date"><?php the_time('F d, Y') ?></span></p>

			<?php the_content("Continue reading '" . the_title('', '', false) . "'"); ?>
					</div>
     
			<?php endwhile; ?>

<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>
		<?php endif; ?>
<!-- google_ad_section_end -->
		</div>
 <?php get_sidebar(); ?>
 <?php get_footer(); ?>
