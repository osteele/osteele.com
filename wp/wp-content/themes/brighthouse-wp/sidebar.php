	<div id="sidebar">
		<div class="sidebar-node">
			<div id="about-box">
            <h3>About</h3>
            <img src="<?php bloginfo('template_url'); ?>/images/author.gif" alt="Author's Picture" />
            <p>Write a paragraph or two about yourself here. Write in the third person to sound important :-).</p></div>
            </div>
		<div class="sidebar-node">  
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
		</div>
		<div class="sidebar-node">
			<h3>Subscribe</h3>
			<ul class="feeds">
			<li>
<a href="<?php bloginfo('rss_url'); ?>" title="Grab the feed">Click here to subscribe to news feed</a>
			</li>
			</ul>
		</div>

		<div class="sidebar-node">
			<h3>Latest blog posts</h3>
<ul>  
			<?php wp_get_archives('type=postbypost&limit=10&format=html'); ?>
</ul>
		</div>

		<div class="sidebar-node">
		<h3>Pages</h3>
		<ul><?php wp_list_pages('title_li='); ?>
		</ul>
		</div>

<div class="sidebar-node">
		<h3>Archives</h3>
<ul><?php wp_get_archives('type=monthly&limit=12'); ?></ul>
</div>

		<div class="sidebar-node">
		<h3>Meta</h3>
		<ul>
                <li><?php wp_loginout(); ?></li>
<li><?php wp_register(); ?></li>
		</ul>
		</div>

              	<div class="sidebar-node">
			<h3>Blogroll</h3> 
			<ul>
<?php get_links('-1', '<li>', '</li>', '', FALSE, 'rand', FALSE, FALSE, 5, FALSE); ?>
</ul>
		</div>

</div>
