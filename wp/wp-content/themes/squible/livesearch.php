<?php 
	require("../../../wp-blog-header.php"); 
	/* The results count is disabled be default as it proved very taxing for my sql server. Try at your own risk!
	$posts = query_posts('posts_per_page=-1&s='.$s.'&what_to_show=posts'); 
	$countthem = 0;

	if ($posts) { foreach ($posts as $post) { start_wp(); {
		$countthem++;
	} } }
	
	
	PS: For some reason it works best if this page is outputted in a single line, with no carriage returns.
	*/
?>
<?php $posts = query_posts('posts_per_page=10&s='.$s.'&what_to_show=posts'); ?><div class="LSRes"><?php if ($posts) { foreach ($posts as $post) { start_wp(); ?><div class="LSRow" onclick="location.href='<?php echo get_permalink() ?>';" style="cursor: pointer;"><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link to '<?php the_title(); ?>'"><?php the_title(); ?></a> &nbsp;<span class="metalink"><a href="<?php comments_link(); ?>" title="Go the the comments for this entry"><?php comments_number('0', '1', '%'); ?></a></span><br /><small><?php /* If 'Dunstan's Time Since' plugin is installed use it; else use default. */ if (function_exists('time_since')) { echo time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()); gt; ?> ago<? } else { the_time('F jS, Y') ?><?php } ?> <?php edit_post_link('e','',''); ?></span></small></div><?php if("comment" == $oddcomment) {$oddcomment="";} else { $oddcomment="comment"; } } ?><?php } else { ?><div class="LSRow" style="text-align: center;">Sorry, no results.</div><?php } ?>