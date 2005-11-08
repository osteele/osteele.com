<?php
/*
Template Name: Links List for Squible
*/
?>

<?php get_header(); ?>

<div id="toppanel">
	<div id="posts">
	<div class="storycontent">
	
			<div class="post">
				<?php if (have_posts()) : while (have_posts()) : the_post();?>
				<h2 class="storytitle"><?php the_title(); ?></h2>
				<?php endwhile; endif; ?>
				<?php $link_cats = $wpdb->get_results("SELECT cat_id, cat_name FROM $wpdb->linkcategories");
				foreach ($link_cats as $link_cat) {
				?>			
				<h3><?php echo $link_cat->cat_name; ?></h3>
				<ul>
				<?php wp_get_links($link_cat->cat_id); ?>
				</ul>
				<?php } ?>
			</div>

	</div>
	</div>

</div>

<?php get_footer(); ?>
