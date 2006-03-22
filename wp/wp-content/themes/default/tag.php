<?php get_header(); ?>

	<div id="center">

	<h2><?php UTW_ShowCurrentTagSet('tagsetcommalist') ?></h2>

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="dateline"><span class="date"><?php the_time('DdMY') ?></span><span class="time"><?php the_time('hiA') ?> <!-- by <?php the_author() ?> --></span></div><h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<h3 class="tags"><?php UTW_ShowTagsForCurrentPost("commalist", array('last'=>' and %taglink%', 'first'=>'tagged %taglink%',)) ?></h3>

			<div class="entry">
				<?php the_content('Read the rest of this entry &raquo;'); ?>
			</div>

			<?php edit_post_link('Edit','','/'); ?><?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?><br />
		</div>


		<?php endwhile; ?>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
