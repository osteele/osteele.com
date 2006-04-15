<?php get_header(); ?>

	<div id="content" class="widecolumn">
				
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<div class="navigation">
			<div class="alignleft"></div>
			<div class="alignright"></div>
		</div>
	
		<div class="postMeta">
			<p class="categories" id="mCats"> Filed In:<br /> <?php the_category(' '); ?><br style="clear: both;" /></p>
			<?php edit_post_link('<strong>Edit Post &raquo;</strong>', '<p>', '</p>'); ?>
			<p>Posted on:<br /><a href="<? echo get_day_link(date('Y', strtotime($post->post_date)), date('m', strtotime($post->post_date)), date('d', strtotime($post->post_date))); ?>" title="Posts For <? the_time('F jS, Y'); ?>"><? the_time('F jS, Y'); ?></a> at <? the_time('g:ia'); ?>.</p>
			<p>There's <a href="#comments" title="Jump to Comments"><?php echo $post->comment_count . " comment"; if($post->comment_count != 1) { echo "s"; } echo " so far"; ?></a>.</p>
			<? if ($post->comment_count > 0) { ?><p>Last comment was posted <?php if (function_exists('time_since')) { echo time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()); ?> ago. <? } else { the_time('F jS, Y'); echo "."; } ?></p> <? } ?>
			<?php previous_post_link('<p>Previous Post:<br />&laquo; %link</p>') ?>
			<?php next_post_link('<p>Next Post:<br />%link &raquo;</p>') ?>

<? if (true) { ?>
<script type="text/javascript"><!--
google_ad_client = "pub-7558884554835464";
google_ad_width = 160;
google_ad_height = 600;
google_ad_format = "160x600_as";
google_ad_type = "text_image";
google_ad_channel ="3072123568";
google_color_border = "F5F5F5";
google_color_bg = "F5F5F5";
google_color_link = "FF6000";
google_color_url = "FF6000";
google_color_text = "707070";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<?php } ?>

		</div>
			
		<div class="post" id="post-<?php the_ID(); ?>">

			<div class="theDate" title="<? the_time('F jS, Y') ?>"><span class="theMonth"><?php the_time('M'); ?></span><span class="theDay"><? the_time('j'); ?></span></div>
			<h2><a class="postHeading" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<div class="numComments"><a href="#comments" title="Jump to Comments"><?php echo $post->comment_count . " Comment"; if($post->comment_count != 1) { echo "s"; } ?></a></div>
			<!-- <p>by <?php the_author() ?></p> -->

			<div class="entry">

<?php if (false) { ?>
<div style="float:left;margin-right:5px">
<script type="text/javascript"><!--
google_ad_client = "pub-7558884554835464";
google_ad_width = 125;
google_ad_height = 125;
google_ad_format = "125x125_as";
google_ad_type = "text_image";
google_ad_channel ="3072123568";
google_color_border = "FFFFFF";
google_color_bg = "FFFFFF";
google_color_link = "707070";
google_color_url = "F5F5F5";
google_color_text = "707070";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
<?php } ?>

				<?php the_content('Read the rest of this entry &raquo;'); ?>
			</div>
			
			<hr />
			
<script type="text/javascript"><!--
	google_ad_client = "pub-7558884554835464";
google_ad_width = 728;
google_ad_height = 15;
google_ad_format = "728x15_0ads_al";
google_ad_channel ="3072123568";
google_color_border = "F5F5F5";
google_color_bg = "F5F5F5";
google_color_link = "FF6000";
google_color_url = "F5F5F5";
google_color_text = "707070";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>

			<h3><?php comments_number('No Responses', 'One Response', '% Responses' );?> to &#8220;<?php the_title(); ?>&#8221;</h3> 
			<p>
			<small>
			<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
				// Both Comments and Pings are open ?>
				You can <a href="#respond">leave a response</a>, or <a href="<?php trackback_url(true); ?>" rel="trackback">trackback</a> from your own site.
			
			<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
				// Only Pings are Open ?>
				Responses are currently closed, but you can <a href="<?php trackback_url(true); ?> " rel="trackback">trackback</a> from your own site.
			
			<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
				// Comments are open, Pings are not ?>
				You can skip to the end and leave a response. Pinging is currently not allowed.

			<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
				// Neither Comments, nor Pings are open ?>
				Both comments and pings are currently closed.			
			
			<?php } edit_post_link('Edit this entry.','',''); ?>
			
			</small>
			</p>
								
		</div>

	</div>
		
	<?php comments_template(); ?>
	
	<?php endwhile; else: ?>
	
		<p>Sorry, no posts matched your criteria.</p>
	
<?php endif; ?>
	<div class="bottomTear"></div>
	</div>

<?php get_footer(); ?>
