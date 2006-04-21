<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

	<?php
		// This setting can be administered in the Theme Options section in your control panel.
		query_posts('showposts=' . $durable->option['posts_pp']);
	?>

	<?php if (have_posts()) : ?>
		
		<? $post_counter = 0; ?>
		
		<?php while (have_posts()) : the_post(); ?>
			
			<? if($post_counter == 0) { ?>
				
			<div class="postMeta">

				<p class="categories" id="mCats"> Filed In:<br /> <?php the_category(' '); ?><br style="clear: both;" /></p>
				<?php edit_post_link('<strong>Edit Post &raquo;</strong>', '<p>', '</p>'); ?>
				<p>Posted on:<br /><a href="<? echo get_day_link(date('Y', strtotime($post->post_date)), date('m', strtotime($post->post_date)), date('d', strtotime($post->post_date))); ?>" title="Posts For <? the_time('F jS, Y'); ?>"><? the_time('F jS, Y'); ?></a> at <? the_time('g:ia'); ?>.</p>
				<p>There's <?php comments_popup_link('0 comments yet.', '1 comment so far.', '% comments so far.'); ?></p>
				<? if ($post->comment_count > 0) { ?><p>Last comment was posted <?php if (function_exists('time_since')) { echo time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()); ?> ago <? } else { the_time('F jS, Y'); } ?></p> <? } ?>

<? if (false) { ?>
<script type="text/javascript"><!--
google_ad_client = "pub-7558884554835464";
google_ad_width = 120;
google_ad_height = 240;
google_ad_format = "120x240_as_rimg";
google_cpa_choice = "CAAQ0aCazgEaCAu3WxmTyqbLKOPC93M";
//--></script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<? } ?>
			</div>
				
			<div class="post" id="post-<?php the_ID(); ?>">

				<div class="theDate" title="<? the_time('F jS, Y') ?>"><span class="theMonth"><?php the_time('M'); ?></span><span class="theDay"><? the_time('j'); ?></span></div>
				<h2><a class="postHeading" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<div class="numComments"><?php comments_popup_link('0 Comments', '1 Comment', '% Comments'); ?></div>
				<!-- <p>by <?php the_author() ?></p> -->

				<div class="entry">				
					<?php the_excerpt('Read the rest of this entry &raquo;'); ?>
				</div>
<p><a href="<? the_permalink() ?>" rel="bookmark" title="<? the_title() ?>">Continue Reading &raquo;</a></p>
				
<? if (false) { ?>
				<p><?php comments_popup_link('Add Your Comments &raquo;', 'Add Your Comments &raquo;', 'Add Your Comments &raquo;'); ?></p>
<? } ?>
				
				<hr />
										
			</div>
						
			<? $post_counter++ ?>
			
			<? } else if($post_counter > 0) { ?>

			<div class="mini-post" id="post-<?php the_ID(); ?>">
				<div class="theDate" title="<? the_time('F jS, Y'); ?>"><span class="theMonth"><?php the_time('M'); ?></span><span class="theDay"><? the_time('j'); ?></span></div>
				<h2><a class="postHeading" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<div class="numComments"><?php comments_popup_link('0 Comments', '1 Comment', '% Comments'); ?></div>
				<!-- <p>by <?php the_author() ?></p> -->

				<div class="entry">
					<?php the_excerpt() ?>
				</div>
				
				<p><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">Continue Reading &raquo;</a></p>
				<p class="categories">Filed In: <?php the_category(' '); ?> <?php edit_post_link('<strong>Edit</strong>', '', ' '); ?></p>
			
			</div>
			
			<? if($post_counter % 2 == 0) { echo "<hr />"; } ?>
			<? $post_counter++ ?>
			
			<? } ?>
			
	
		<?php endwhile; ?>
		
		<hr />
		
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
	<div class="bottomTear"></div>
	</div>

<?php get_footer(); ?>
