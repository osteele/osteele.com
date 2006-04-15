
</div>

<div id="overview">
	<div class="topTear">&nbsp;</div>
		
	<div class="column" id="postsColumn">
		<h2>Recent Posts</h2>
		<ul class="dates">
	        <?php query_posts('showposts=7'); ?>
	        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	        <li><a href="<?php the_permalink() ?>"><?php the_title() ?></a><br /><span class="date"><?php the_time('jS M') ?> | <? comments_popup_link('0 Comments', '1 Comment', '% Comments'); ?></span></li>
	        <?php endwhile; endif; ?>
		</ul>
	</div>

	<div class="column">
		<h2>Popular Categories</h2>
		<div class="cats">
			<?php category_cloud(8, 24, 'pt', 10); ?>
		<hr />
		</div >

	</div>
	
	<div class="column">
		<h2 id="titleAbout">About</h2>
		<?php query_posts('pagename=about'); ?>
		<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<?php the_content(); ?>
		<?php endwhile; ?>
		<?php else : ?>
		<p>You have no about page, you should add one through the admin interface, or edit 'footer.php' and put some super cool information here!</p>
		<? endif; ?>
	</div>

	<hr />
	
</div>

<div id="footer">

	<p>
		<?php bloginfo('name'); ?> is powered by 
		<a href="http://wordpress.org/">WordPress</a>
		<? // No need to keep this line below, but if you feel like sharing the love ;) ?>
		 and themed by <a href="http://www.cssdev.com/durable/">Durable v0.2</a>.
		<? // End Plug ?>
		
		<br /><a href="feed:<?php bloginfo('rss2_url'); ?>">Entries (RSS)</a>
		and <a href="feed:<?php bloginfo('comments_rss2_url'); ?>">Comments (RSS)</a>.
		<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
	</p>
</div>

<?php wp_footer(); ?>

<? 
global $durable;
if ($durable->option['colorchange'] != "false") {
?>
<div id="colourControl" style="display: none; position: absolute;">
	<? include_once("colourmod/colourControl.php"); ?>
</div>
<? } ?>

</body>
</html>
