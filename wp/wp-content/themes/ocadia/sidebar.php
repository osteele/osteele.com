</div>

<div id="sidebar">

<ul>
	<?php wp_list_pages('title_li=<h2>' . __('Pages') . '</h2>' ); ?>

	<li id="archives">
		<h2><?php _e('Archives'); ?></h2>
		<ul>
		<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</li>
	
	<li id="categories">
		<h2><?php _e('Categories'); ?></h2>
		<ul>
		<?php wp_list_cats(); ?> 
		</ul>
	</li>
	
	<li id="search">
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<h2><label for="s"><?php _e('Search'); ?></label></h2>
		<p>
		<input type="text" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s"  size="20" />
		<input type="submit" id="searchsubmit" value="<?php _e('Go'); ?>" />
		</p>
		</form>
	</li>
	
	<?php if (function_exists('wp_theme_switcher')) { ?>
	<li>
		<h2><?php _e('Themes'); ?></h2>
		<?php wp_theme_switcher(); ?>
	</li>
	<?php } ?>
		
	<?php if ( is_home() || is_page() ) { ?>				
	<?php get_links_list(); ?>
	<?php } ?>
				
	<li id="meta">
		<h2><?php _e('Meta'); ?></h2>
		<ul>
		<?php wp_register(); ?>
		<li><?php wp_loginout(); ?></li>
		<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS'); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
		<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
		<li><a href="http://wordpress.org" title="<?php _e('Powered by Wordpress, state-of-the-art semantic personal publishing platform.'); ?>">Wordpress</a></li>
		<?php wp_meta(); ?>
		</ul>
	</li>
	
</ul>

</div>
