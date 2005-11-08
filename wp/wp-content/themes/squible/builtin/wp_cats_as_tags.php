<?php
/*
Plugin Name: Cats as Tags
Plugin URI: http://www.squible.com/
Description: A complete tagging system built from Wordpress categories
Version: 1.0
Author: Theron Parlin
Author URI: http://www.squible.org/
*/

function popular_tags($smallest=10, $largest=48, $unit="pt", $exclude='', $displaynum=25)
{
	$cats = list_cats(1, 'all', 'name', 'asc', '', 0, 0, 1, 1, 0, 1, 1, 0, 1, '', '', $exclude, 0);
	//only display the most 25 popular tags
	$myurl = get_bloginfo('url');
	$category_base = get_settings('category_base');
	$cats = explode("\n", $cats);
	foreach ($cats as $cat)
	{
		eregi("a href=\"(.+)\" ", $cat, $regs);
		$catlink = $regs[1];
		$cat = trim(strip_tags($cat));
		eregi("(.*) \(([0-9]+)\)$", $cat, $regs);
		$catname = $regs[1]; $count = $regs[2];
		$counts{$catname} = $count;
		$catlinks{$catname} = $catlink;
	}
	natsort($counts);
	$num=sizeof($counts);
	foreach($counts as $catname => $count) { 
		// Let's shrink the array to match $displaynum
		if ($num > $displaynum) {
			array_shift($counts);
		} else {
			break;
		}
		$num--;
	}

	ksort($counts);
	$spread = max($counts) - min($counts); 
	if ($spread <= 0) { $spread = 1; };
	$fontspread = $largest - $smallest;
	$fontstep = $spread / $fontspread;
	if ($fontspread <= 0) { $fontspread = 1; }

	foreach ($counts as $catname => $count)
	{
		$catlink = $catlinks{$catname};
		if (strstr($catlink, "http:") == FALSE) {
			print "<a href=\"$myurl$category_base/$catlink\" rel=\"tag\" title=\"$count entries\" style=\"font-size: ".  ($smallest + ($count/$fontstep))."$unit;\">$catname</a> \n";
		} else {
			print "<a href=\"$catlink\" rel=\"tag\" title=\"$count entries\" style=\"font-size: ". ($smallest + ($count/$fontstep))."$unit;\">$catname</a> \n";
		}
	}
}

function related_tags($id, $rlimit=0, $before='', $after='') {
    global $wpdb;
    $category_base = get_settings('category_base');
    $cat = get_query_var('cat');
    $categories = get_related_categories($id,$cat, $min_weight, $limit);
    $myurl = get_bloginfo('url');
    if (!$categories) {return;}

    $new = array();
    foreach((get_the_category()) as $cat) {

	$dups .= "$cat->cat_name ";
    }

    foreach((get_the_category()) as $cat) {

	foreach($categories as $c) {
		//echo "$cat->cat_name == $c->cat_name<br />";
		if ($cat->cat_name == $c->cat_name) {
			if (! strstr($dups, $c->related_cat)) {
        			array_push($new, $c->related_cat);
			}
		}
	}
    }

    $newcategories = array_unique($new);

    if ($newcategories) {
	$count=0;
        foreach ($newcategories as $category) {
		$count++;
                $gettheid = $wpdb->get_row("SELECT * FROM $wpdb->categories WHERE cat_name = '$category'");
                echo $before;
                echo "<a href=\"$myurl?cat=" . $gettheid->cat_ID . '" rel="tag">' . $gettheid->cat_name . '</a> ';
                echo $after;
		if ($count == $rlimit) {break;}
        }
    }
}

function get_related_categories ($id,$category_id, $threshold = 3, $limit=false) {
    global $wpdb;

    if ($limit) {
        $limit_sql = " LIMIT 0,$limit";
    }

    $results = $wpdb->get_results("
	SELECT c1.cat_name, c1.cat_ID, c2.cat_ID AS related_id, c2.cat_name AS related_cat, COUNT( w2.post_id ) AS weight FROM $wpdb->post2cat w1, $wpdb->post2cat w2, $wpdb->categories AS c1, $wpdb->categories AS c2 WHERE w1.post_id = w2.post_id AND w1.category_id <> w2.category_id AND c1.cat_ID = w1.category_id AND c2.cat_ID = w2.category_id GROUP BY related_id, c1.cat_ID HAVING weight >= 1 ORDER BY c1.cat_name, weight
       $limit_sql");

    return $results;
}

function show_tags ($limit=0) {
	$displaycount=0;
	global $user_level;
	$myurl = get_bloginfo('url');
	$category_base = get_settings('category_base');
	$technoimage = get_bloginfo('stylesheet_directory') . "/images/technobubble1.gif";

	foreach((get_the_category()) as $cat) {
		$displaycount++;
		echo "<a href=\"$myurl?cat=" . $cat->cat_ID . "\" rel=\"tag\">" . $cat->cat_name . "</a> ";
		echo "{<a href=\"$myurl?feed=rss&cat=" . $cat->cat_ID . '" rel="tag">rss</a>} ';
		$technocatname = preg_replace('/\s+/', '+', $cat->cat_name);
		echo '<a href="http://www.technorati.com/tag/' . $technocatname . '" rel="tag"><img src="' . $technoimage . '" alt="Technorati" style="border: 0px;" /></a> ';
		if ( $user_level > 3 && !is_home()) { echo "[<a href=\"?action=delete&cat_ID=" . $cat->cat_ID . "#extras\">X</a>]<br />"; } else {echo "<br />";}
		if ( $displaycount == $limit) {break;}
	}
}
?>
