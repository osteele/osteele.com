<?php include("squible_options.php"); ?>
<div id="midpanel">

<table style="" width="100%" cellpadding="0" cellspacing="0"><tr><td valign="top" style='width:50%'>
<div style="padding: 10px; padding-bottom: 0px;">
<div class="tooltitle">Author</div>
<?php echo $aboutme; ?>

<br /><br />

<?php if (function_exists('c2c_get_recently_commented')) { ?>
<div class="tooltitle">Recent Comments</div>
	<?php c2c_get_recently_commented($show_recent_comments); ?>
</div>
<?php } ?>

</td><td valign="top">
<div style="padding: 10px; padding-bottom: 0px;">
<?php if (function_exists('get_flickrRSS')) { ?>
<div class="tooltitle">Flickr <a style="text-decoration: none; font-size: 8pt;" href="<?php echo $flickr_url; ?>">(more...)</a></div>
<?php get_flickrRSS($flickr_userid, "userid", $numpics, small, "", "", ""); ?><br /><br />
<?php } ?>

<div class="tooltitle">Tags</div>
<div class="poptags">
<ul><?php if (true) {
all_keywords('<li class="cosmos keyword%count%"><a href="/tag/%keylink%">%keyword%</a>&nbsp;(%count%)</li>','<li class="cosmos keyword%count%"><a href="%keylink%">%keyword%</a>&nbsp;(%count%)</li>', 1, false, 2);
    } else if (function_exists('UTW_ShowWeightedTagSetAlphabetical')) {
	UTW_ShowWeightedTagSetAlphabetical("coloredsizedtagcloud", '', 25);
	} else {
	popular_tags($minfont, $maxfont, $fontunit, $category_ids_to_exclude, $numberoftags); 
	}
	?></ul>
</div>

<br />
<div class="tooltitle">Search <?php bloginfo('name'); ?></div>
<?php include (TEMPLATEPATH . '/searchform.php'); ?>

</div>
</td></tr></table>
<br />

<div id="lowpanel">

<table width="100%" cellpadding="0" cellspacing="0"><tr><td valign="top" style='width:50%'>
<div style="padding: 10px;">
<div class="tooltitle">Previous Posts</div>

<?php $first=0; ?>
<?php rewind_posts(); ?>
<?php 
/* 
100 is just to guarantee that we will have enough posts to show
between asides and regular posts, don't worry, the loop will break after it finds
10 regular posts. There's probably a better way to do this and I just don't know it.
*/
$my_query = new WP_Query('showposts=100'); 
?>

<?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
<?php $comments = $wpdb->get_var("SELECT COUNT(*) FROM $tablecomments WHERE comment_post_ID = '$post->ID'"); ?>
<?php if (in_category($asides_cat)) {continue;} ?>
<?php if ($first == 0) { $first++; continue;} ?>
<?php if ($first > 10) {break; } ?>
	<div class="prevposts">
	<div style="display: inline;float: right;"><?php the_time('d M y'); ?></div>
        <a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>
<?php
	$words = get_the_content('', 0, '');
	if ($words) {
        	$post = strip_tags($words);
               	$post = explode(' ', $post);
               	$totalcount = count($post);
	} else {
		$totalcount=0;
	}
?>
	<div class="showwords"><?php echo " $totalcount"; ?> words</div>
	</div>
<?php $first++; ?>
<?php endwhile; ?>


<br /><br /><br />
<div class="tooltitle">Meta</div>
<div style="padding-top: 2px;">
<a title="RDF" href="<?php bloginfo('rdf_url'); ?>">RDF</a> | <a title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>">RSS</a> | <a title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>">Atom 0.3</a> | <a href="http://validator.w3.org/check?uri=referer">XHTML</a> | <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a> | <?php wp_register('',''); ?>
</div>

</div>

</td><td valign="top">
<div style="padding: 10px;">
<div class="tooltitle">Asides <a style="text-decoration: none; font-size: 8pt;" href="<?php echo bloginfo('url'); ?>/index.php?cat=<?php echo $asides_cat ?>">(more...)</a></div>
<div style="line-height:18px;">

<?php delicious('osteele/laszlo/?count=5&extended=body&tags=no&rssbutton=no') ?>
<?php 
$my_query = new WP_Query('showposts=100'); 
$found=0;
?>
<?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
<?php if (!in_category($asides_cat)) {continue;} ?>
<?php $found++; ?>
	<div class="asides">
		<?php $mycontent=wptexturize($post->post_content); echo $mycontent; echo ' '; comments_popup_link('(0)', '(1)', '(%)' ) ?> <?php edit_post_link("(e)", "", ""); ?>
	</div>
<?php if ($found == $asidesnum) {break;} ?>
<?php endwhile; ?>

</div>
</div>
<br />

</td></tr></table>
</div>

<?php get_footer(); ?>
