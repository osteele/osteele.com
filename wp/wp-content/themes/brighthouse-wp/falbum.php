<?php /*
	Template Name: FAlbum
*/ ?>
<?php get_header(); ?>

		<div id="main-wrapper">
			<div id="main-content">
<!-- google_ad_section_start -->

<script type="text/javascript" src="<?php echo get_settings('siteurl'); ?>/wp-content/plugins/falbum/res/falbum.js"></script>
<script type="text/javascript" src="<?php echo get_settings('siteurl'); ?>/wp-content/plugins/falbum/res/overlib.js"></script>

<script type="text/javascript" src="<?php echo get_settings('siteurl'); ?>/wp-content/plugins/falbum/res/prototype.js"></script>


       <?php
      
       $falbum->show_photos();
      
       ?>


<!-- google_ad_section_end -->
		</div>
 <?php get_sidebar(); ?>
 <?php get_footer(); ?>
