<?php
/*
Plugin Name: Page Navigation
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Adds a more advanced page navigation to Wordpress.
Version: 1.5
Author: GaMerZ
Author URI: http://www.lesterchan.net
*/


### Page Navigation
function wp_pagenavi($before=' ', $after=' ', $prelabel='&laquo; ', $nxtlabel=' &raquo;') {
	global $request, $posts_per_page, $wpdb, $paged;
	if (!is_single()) {
		if (get_query_var('what_to_show') == 'posts') {
			preg_match('#FROM (.*) GROUP BY#', $request, $matches);
			$fromwhere = $matches[1];
			$numposts = $wpdb->get_var("SELECT COUNT(DISTINCT ID) FROM $fromwhere");
			$max_page = ceil($numposts /$posts_per_page);
		} else {
			$max_page = 999999;
		}
		if(empty($paged)) {
			$paged = 1;
		}
		echo "$before Pages ($max_page) : <b>";
		if ($paged >= 4) {
			echo '<a href="'.get_pagenum_link().'">&laquo; First</a> ... ';
		}
		previous_posts_link($prelabel);
		for($i = $paged - 2 ; $i  <= $paged +2; $i++) {
			if ($i >= 1 && $i <= $max_page) {
				if($i == $paged) {
					echo "[$i]";
				} else {
					echo '<a href="'.get_pagenum_link($i).'">'.$i.'</a> ';
				}
			}
		}
		next_posts_link($nxtlabel, $max_page);
		if (($paged+2) < ($max_page)) {
			echo ' ... <a href="'.get_pagenum_link($max_page).'">Last &raquo;</a>';
		}
		echo "$after</b>";
	}
}
?>