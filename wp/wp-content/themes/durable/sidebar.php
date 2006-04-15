<div id="sidebar">

<div id="archives" class="menuSection" style="display:none;">
	
<div class="column">	
	<h2>Calendar</h2>
	<?php get_calendar(); ?>
</div>
	
<div class="column">
	<h2>Recent Months</h2>
	<ul>
		<?php wp_get_archives('type=monthly&limit=8'); ?>
	</ul>
</div>
	
<div class="column">
	<h2>Popular Categories</h2>
	<div class="cats">
		<?php category_cloud(8, 24, 'pt', 8); ?>
	</div>
</div>

<hr />
<h3 style="text-align: right;"><a href="<? echo get_template_directory_uri(); ?>/archivecloud.php" title="View the Complete Archives">View the Complete Archives</a> &raquo;</h3>

<div class="bottomTear"></div>
</div>

<div id="links" class="menuSection" style="display: none;">
	<h2>Pages &amp; Interesting Links</h2>
	
	<? if (check_num_pages()) { ?>
	<div class="column" id="pagesColumn">
		<h3>Pages</h3>
		<ul>
			<?php wp_list_pages('title_li=' ); ?>
		</ul>
	</div>
	<? } ?>
	
	<?php get_grouped_links_list(); ?>
	
	<hr />
	<div class="bottomTear"></div>
</div>

<div id="search" class="menuSection" style="display: none;">
	<h2>Live Search</h2>
	<?php include (TEMPLATEPATH . '/searchform.php'); ?>
	<div class="bottomTear"></div>
</div>

</div>

<hr />