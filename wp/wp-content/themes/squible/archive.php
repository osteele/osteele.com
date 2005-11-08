<?php get_header(); ?>

        <div id="content">
        <div id="topimage">
                <div style="margin-top: 12px; float: right; margin-right: 7px;">
                <div style="text-align: right;"><a style="color: #fff;" href="/">Home</a> &nbsp;&nbsp;<a style="color: #fff;" href="http://www.photanical.com/about/">About
Me</a></div>
                <div style="padding-top: 21px;"><?php include (TEMPLATEPATH . '/searchform.php'); ?></div>
                </div>
        </div>


	<div id="posts">

		<div style="margin-left: 35px;">
		<?php if (have_posts()) : ?>

		 <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
<?php /* If this is a category archive */ if (is_category()) { ?>				
		<h2 class="pagetitle">Archive for the '<?php echo single_cat_title(); ?>' tag</h2>
		
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
		</div>


		<?php while (have_posts()) : the_post(); ?>
		<div class="postintro" style="margin-right: 25px;">
				<h3 id="post-<?php the_ID(); ?>"><a style="color: #000;" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				<small><div style="margin-top: -15px;"><?php the_time('l, F jS, Y') ?></div></small>
				
				<div class="entry">
					<?php the_excerpt() ?>
				</div>
		
				<p class="postmetadata">Tagged as <?php the_category(', ') ?> <strong>|</strong> <?php edit_post_link('Edit','','<strong>|</strong>'); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p> 
				
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

		<h2 class="center">Not Found</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>
		
	</div>
	</div>

<?php get_footer(); ?>
