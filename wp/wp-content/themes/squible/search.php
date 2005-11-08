<?php get_header(); ?>
	<div id="content">
	<div id="topimage">	
		<div style="margin-top: 12px; float: right; margin-right: 7px;">
		<div style="text-align: right;"><a style="color: #fff;" href="/">Home</a> &nbsp;&nbsp;<a style="color: #fff;" href="http://www.photanical.com/about/">About Me</a></div>
		<div style="padding-top: 21px;"><?php include (TEMPLATEPATH . '/searchform.php'); ?></div>
		</div>
	</div>

	<div id="posts">

	<?php if (have_posts()) : ?>

		<h2 class="pagetitle" style="margin-left: 35px;">Search Results</h2>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post" style="margin-right: 35px;">
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<small><div style="margin-top: -15px;"><?php the_time('l, F jS, Y') ?></div></small>
				
				<div class="entry">
					<?php the_excerpt() ?>
				</div>
		
				<p class="postmetadata">Posted in <?php the_category(', ') ?> <strong>|</strong> <?php edit_post_link('Edit','','<strong>|</strong>'); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p> 
				
				<!--
				<?php trackback_rdf(); ?>
				-->
			</div>
	
		<?php endwhile; ?>

		<div class="navigation">
			<div class="left"><?php posts_nav_link('','','&laquo; Previous Entries') ?></div>
			<div class="right"><?php posts_nav_link('','Next Entries &raquo;','') ?></div>
		</div>
		<br /><br />
	
	<?php else : ?>

	<div class="post" style="margin-right: 35px;">
		<h2 class="center">Not Found</h2>
		<br /><br /><br />
	</div>
	<?php endif; ?>
		
	</div>
</div>

<?php get_footer(); ?>
