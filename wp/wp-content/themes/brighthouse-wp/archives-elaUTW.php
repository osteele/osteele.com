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

<?php if (function_exists('af_ela_super_archive')) { ?>
<?php af_ela_super_archive('num_posts_by_cat=50&truncate_title_length=40&hide_pingbacks_and_trackbacks=1&num_entries=1&num_comments=1&number_text=<span>%</span>&comment_text=<span>%</span>&selected_text='.urlencode('')); ?><br /><br />

<h2>Article tags</h2>
<p>The following is a list of the tags used at <?php bloginfo('name'); ?>, colored and 'weighed' in relation to their relative usage.</p>

<?php UTW_ShowWeightedTagSetAlphabetical("coloredsizedtagcloud"); } ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
