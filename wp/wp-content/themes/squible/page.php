<?php get_header(); ?>

<div id="toppanel">
	<div id="posts">
	<div class="storycontent">

	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>

			<div id="submenu">
			<ul class="smenu">
        			<?php wp_list_pages('depth=1&sort_column=menu_order&title_li=&child_of='. $post->ID); ?>
			</ul>
			</div>
				
			<div class="post">
				<h2 class="storytitle" id="post-<?php the_ID(); ?>"><?php the_title(); ?></h2>

	
					   <?php the_content('Read the rest of this entry &raquo;'); ?> <?php edit_post_link(__('<br />Edit This Article')); ?>
		
				<!--
				<?php trackback_rdf(); ?>
				-->
			</div>
	
		<?php endwhile; ?>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center"><?php _e("Sorry, but you are looking for something that isn't here."); ?></p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>
	</div>

</div>

<?php get_footer(); ?>
