<?php /*
	Template Name: Archives
*/ ?>
<?php /* Counts the posts, comments and categories on your blog */
	$numposts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish'");
	if (0 < $numposts) $numposts = number_format($numposts); 
	
	$numcomms = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");
	if (0 < $numcomms) $numcomms = number_format($numcomms);
	
	$numcats = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->categories");
	if (0 < $numcats) $numcats = number_format($numcats);
?>

<?php get_header(); ?>

		<div id="main-wrapper">
			<div id="main-content">

<h2>Archives</h2>
This is the <?php bloginfo('name'); ?> archives. Currently the archives are spanning <?php echo $numposts; ?> posts and <?php echo $numcomms; ?> comments, contained within <?php echo $numcats; ?> categories. Through here, you will be able to move down into the archives by way of time or category.

<h2>Archives by Month:</h2>
  <ul>
    <?php wp_get_archives('type=monthly'); ?>
  </ul>

<h2>Archives by Subject:</h2>
  <ul>
     <?php wp_list_cats(); ?>
  </ul>

</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
