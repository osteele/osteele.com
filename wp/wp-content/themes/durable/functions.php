<?

/* 
Function Name: Time Since 
Function URI: http://binarybonsai.com/wordpress/timesince
Description: Tells the time between the entry being posted and the comment being made.
Author: Michael Heilemann & Dunstan Orchard
Author URI: http://binarybonsai.com
*/
function time_since($older_date, $newer_date = false)
{
	// array of time period chunks
	$chunks = array(
	array(60 * 60 * 24 * 365 , 'year'),
	array(60 * 60 * 24 * 30 , 'month'),
	array(60 * 60 * 24 * 7, 'week'),
	array(60 * 60 * 24 , 'day'),
	array(60 * 60 , 'hour'),
	array(60 , 'minute'),
	);
	
	// $newer_date will equal false if we want to know the time elapsed between a date and the current time
	// $newer_date will have a value if we want to work out time elapsed between two known dates
	$newer_date = ($newer_date == false) ? (time()+(60*60*get_settings("gmt_offset"))) : $newer_date;
	
	// difference in seconds
	$since = $newer_date - $older_date;
	
	// we only want to output two chunks of time here, eg:
	// x years, xx months
	// x days, xx hours
	// so there's only two bits of calculation below:

	// step one: the first chunk
	for ($i = 0, $j = count($chunks); $i < $j; $i++)
		{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];

		// finding the biggest chunk (if the chunk fits, break)
		if (($count = floor($since / $seconds)) != 0)
			{
			break;
			}
		}

	// set output var
	$output = ($count == 1) ? '1 '.$name : "$count {$name}s";

	// step two: the second chunk
	if ($i + 1 < $j)
		{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		
		if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0)
			{
			// add to output var
			$output .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}
	
	return $output;
}

// Altered from original by Dougal, to enable seperation of categories.
function get_grouped_links_list($order = 'name', $hide_if_empty = 'obsolete') {
	global $wpdb;

	$order = strtolower($order);

	// Handle link category sorting
	if (substr($order,0,1) == '_') {
		$direction = ' DESC';
		$order = substr($order,1);
	}

	// if 'name' wasn't specified, assume 'id':
	$cat_order = ('name' == $order) ? 'cat_name' : 'cat_id';

	if (!isset($direction)) $direction = '';
	// Fetch the link category data as an array of hashesa
	$cats = $wpdb->get_results("
		SELECT DISTINCT link_category, cat_name, show_images, 
			show_description, show_rating, show_updated, sort_order, 
			sort_desc, list_limit
		FROM `$wpdb->links` 
		LEFT JOIN `$wpdb->linkcategories` ON (link_category = cat_id)
		WHERE link_visible =  'Y'
			AND list_limit <> 0
		ORDER BY $cat_order $direction ", ARRAY_A);
	
	// Actually I'm gonna hack it here, and get categories ordered by number of links.
	// It's just easier for the layout that way. My bad.
	
	if( count($cats) < 1) { return false; }
	
	foreach ($cats as $cat) 
	{ 
		$num_links[$cat['link_category']] = $wpdb->get_results("SELECT count(*) FROM $wpdb->links WHERE link_category = " . $cat['link_category'], ARRAY_A);
	}
	
	arsort($num_links);
	
	foreach ($num_links as $key => $value)
	{
		for($i=0;$i<count($cats);$i++)
		{
			if($cats[$i]["link_category"] == $key)
			{
				$new_cats[] = $cats[$i];
			}
		}
	}
	
	// End the dirty, dirty hack.
	
	// Display each category
	if ($cats) {
		foreach ($new_cats as $cat) {
			// Handle each category.
			// First, fix the sort_order info
			$orderby = $cat['sort_order'];
			$orderby = (bool_from_yn($cat['sort_desc'])?'_':'') . $orderby;

			// Display the category name
			echo '	<div class="column" id="linkcat-' . $cat['link_category'] . '"><h3>' . $cat['cat_name'] . "</h3>\n\t<ul>\n";
			// Call get_links() with all the appropriate params
			get_links($cat['link_category'],
				'<li>',"</li>","\n",
				bool_from_yn($cat['show_images']),
				$orderby,
				bool_from_yn($cat['show_description']),
				bool_from_yn($cat['show_rating']),
				$cat['list_limit'],
				bool_from_yn($cat['show_updated']));

			// Close the last category
			echo "\n\t</ul>\n</div>\n";
		}
	}
}

// Checks to see if there any pages.
function check_num_pages() {

	// Query pages.
	$pages = & get_pages($args);
	if ( !$pages ) {
		return false;
	}
	
	return true;
}



include(dirname(__FILE__).'/themetoolkit.php');

themetoolkit(
	'durable',
	array(
	'pagestyle' => 'Page Style {radio|ripped|Page Rip Effect|solid|Solid Colors}',
	'posts_pp' => 'Number of Posts on Front Page {radio|3|3 Posts|5|5 Posts|7|7 Posts|9|9 Posts}',
	'alignment' => 'Posts Should Be: {radio|left|Left Aligned|justify|Justified}',
	'colorchange' => 'Let Users Change Colors? {radio|true|Yes|false|No}'
	),
	__FILE__	 
);

/*
 *	Set up the default theme colors on installation.
 */

if (!$durable->is_installed()) 
{
	saveColors('default');
}

function saveColors($mode)
{
	global $durable;
	global $user_level;
	// Check admin status..
	
	if($user_level > 8 || !$durable->is_installed())
	{
		// Header Colors
		$set_defaults['header_bgclr']=($mode=="default")?"#ff5c00":explodeCookie($_COOKIE['#header'], "#ff5c00", "backgroundColor");
		$set_defaults['header_txtclr']=($mode=="default")?"#fff":explodeCookie($_COOKIE['h1_a'], "#fff");
	
		// Menu Links
		$set_defaults['menulinks_lnktxtclr']=($mode=="default")?"#666":explodeCookie($_COOKIE['#topmenu_a'], "#666");
		$set_defaults['menulinks_lnkhvrclr']=($mode=="default")?"#fff":explodeCookie($_COOKIE['#topmenu_a:hover'], "#fff");
		$set_defaults['menulinks_lnkhvrbgclr']=($mode=="default")?"#3fbcec":explodeCookie($_COOKIE['#topmenu_a:hover'], "#3fbcec", "backgroundColor");	

		// Menu Sections
		$set_defaults['menusections_bgclr']=($mode=="default")?"#3fbcec":explodeCookie($_COOKIE['_menusection'], "#3fbcec", "backgroundColor");
		$set_defaults['menusections_txtclr']=($mode=="default")?"#fff":explodeCookie($_COOKIE['_menusection'], "#fff");
		$set_defaults['menusections_hdgclr']=($mode=="default")?"#fff":explodeCookie($_COOKIE['_menusection_h2'], "#fff");
		$set_defaults['menusections_lnktxtclr']=($mode=="default")?"#ffea00":explodeCookie($_COOKIE['_menusection_a'], "#ffea00");
		$set_defaults['menusections_lnkhvrclr']=($mode=="default")?"#3fbcec":explodeCookie($_COOKIE['_menusection_a:hover'], "#3fbcec");
		$set_defaults['menusections_lnkhvrbgclr']=($mode=="default")?"#ffea00":explodeCookie($_COOKIE['_menusection_a:hover'], "#ffea00", "backgroundColor");	

		// Main Content
		$set_defaults['maincontent_bgclr']=($mode=="default")?"#F5F5F5":explodeCookie($_COOKIE['#maincontent'], "#F5F5F5", "backgroundColor");
		$set_defaults['maincontent_txtclr']=($mode=="default")?"#666":explodeCookie($_COOKIE['#maincontent'], "#666");
		$set_defaults['maincontent_hdgclr']=($mode=="default")?"#ff5c00":explodeCookie($_COOKIE['h2'], "#ff5c00");
		$set_defaults['maincontent_lnktxtclr']=($mode=="default")?"#ff5c00":explodeCookie($_COOKIE['#maincontent_a'], "#ff5c00");
		$set_defaults['maincontent_lnkhvrclr']=($mode=="default")?"#F5F5F5":explodeCookie($_COOKIE['#maincontent_a:hover'], "#F5F5F5");
		$set_defaults['maincontent_lnkhvrbgclr']=($mode=="default")?"#ff5c00":explodeCookie($_COOKIE['#maincontent_a:hover'], "#ff5c00", "backgroundColor");	

		// Dates & Tags
		$set_defaults['datestags_bgclr']=($mode=="default")?"#3fbcec":explodeCookie($_COOKIE['_categories_a'], "#3fbcec", "backgroundColor");
		$set_defaults['datestags_txtclr']=($mode=="default")?"#fff":explodeCookie($_COOKIE['_categories_a'], "#fff");
		$set_defaults['datestags_lnkhvrtxtclr']=($mode=="default")?"#fff":explodeCookie($_COOKIE['_categories_a:hover'], "#fff");
		$set_defaults['datestags_lnkhvrbgclr']=($mode=="default")?"#ff5c00":explodeCookie($_COOKIE['_categories_a:hover'], "#ff5c00", "backgroundColor");

		// Comments
		$set_defaults['comments_rplyfrmbgclr']=($mode=="default")?"#ddd":explodeCookie($_COOKIE['#commentform'], "#ddd", "backgroundColor");
		$set_defaults['comments_rplyfrmhdgtxtclr']=($mode=="default")?"#666":explodeCookie($_COOKIE['#commentform_h2'], "#666");
		$set_defaults['comments_rplyfrmtxtclr']=($mode=="default")?"#666":explodeCookie($_COOKIE['#commentform'], "#666");
		$set_defaults['comments_rplybgclr']=($mode=="default")?"#fff":explodeCookie($_COOKIE['_alt'], "#fff", "backgroundColor");
		$set_defaults['comments_rplytxtclr']=($mode=="default")?"#666":explodeCookie($_COOKIE['_alt'], "#666");
		$set_defaults['comments_rplylnktxtclr']=($mode=="default")?"#ff5c00":explodeCookie($_COOKIE['_alt_a'], "#ff5c00");	
		$set_defaults['comments_rplylnkhvrtxtclr']=($mode=="default")?"#fff":explodeCookie($_COOKIE['_alt_a:hover'], "#fff");	
		$set_defaults['comments_rplylnkhvrbgclr']=($mode=="default")?"#ff5c00":explodeCookie($_COOKIE['_alt_a:hover'], "#ff5c00", "backgroundColor");	
				
		// Footer Overview
	  	$set_defaults['footer_bgclr']=($mode=="default")?"#3fbcec":explodeCookie($_COOKIE['#overview'], "#3fbcec", "backgroundColor");	
	  	$set_defaults['footer_txtclr']=($mode=="default")?"#fff":explodeCookie($_COOKIE['#overview'], "#fff");
	  	$set_defaults['footer_hdgclr']=($mode=="default")?"#fff":explodeCookie($_COOKIE['#overview_h2'], "#fff");
	  	$set_defaults['footer_lnktxtclr']=($mode=="default")?"#ffea00":explodeCookie($_COOKIE['#overview_a'], "#ffea00");
	  	$set_defaults['footer_lnkhvrtxtclr']=($mode=="default")?"#3fbcec":explodeCookie($_COOKIE['#overview_a:hover'], "#3fbcec");
	  	$set_defaults['footer_lnkhvrbgclr']=($mode=="default")?"#ffea00":explodeCookie($_COOKIE['#overview_a:hover'], "#ffea00", "backgroundColor");

		// Pass other theme variables.
		$set_defaults['pagestyle']=($mode=="default")?"ripped":$durable->option['pagestyle'];
		$set_defaults['posts_pp']=($mode=="default")?"5":$durable->option['posts_pp'];
		$set_defaults['alignment']=($mode=="default")?"left":$durable->option['alignment'];
		$set_defaults['colorchange']=($mode=="default")?"true":$durable->option['colorchange'];

	  $result = $durable->store_options($set_defaults);
	}
}

function explodeCookie($cookieValue, $default, $attribute = "color")
{
	if($cookieValue == "")
	{
		return $default;
	}
	
	$cookieValue = str_replace(",", "", $cookieValue);
	$cookieValue = explode("|", $cookieValue);
	
	foreach($cookieValue as $key => $value)
	{
		if($value == $attribute)
		{
			return $cookieValue[$key+1];
		}
	}
}

/*
Plugin Name: Category Cloud + Monthly Cloud by Andy Peatling
Plugin URI: http://zak.greant.com/
Description: Template tag to display a categories in a tag cloud
Version: 0.1.1
Author: Zak Greant
Modified for Durable by: Andy Peatling
Author URI: http://zak.greant.com
*/

/*
New BSD License (http://www.opensource.org/licenses/bsd-license.php)
Category Cloud, Copyright (c) 2006, Foo Associates Inc.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice,
   this list of conditions and the following disclaimer in the documentation
   and/or other materials provided with the distribution.
 * Neither the name of Foo Associates Inc. nor the names of its contributors
   may be used to endorse or promote products derived from this software
   without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/*
Thanks to Matt Kingston (http://www.hitormiss.org/).
Matt wrote a plugin called Weighted Categories that I stole ideas from.
*/

function mk_step($min, $max, $counts){
  return ($max - $min) / (max($counts) - min($counts) + 1);
}

function mk_stepped_base($base, $step, $count){
  return $base + $step * $count;
}

function monthly_cloud(
	$font_min          = 8, /* Minimum font size */
	$font_max          = 24, /* Maximum font size */
	$font_unit         = 'pt' /* Font unit */
) {
	
	global $wpdb;
	global $month;
	
	$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts WHERE post_date < NOW() AND post_date != '0000-00-00 00:00:00' AND post_status = 'publish' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC");

	if ( $arcresults ) {
		$afterafter = $after;
		foreach ( $arcresults as $arcresult ) {
			
			$months[] = array("url" => get_month_link($arcresult->year,	$arcresult->month),
							  "name" => $month[zeroise($arcresult->month,2)] . ' ' . $arcresult->year,
							  "posts" => $arcresult->posts);

			$counts[$arcresult->posts] = 1;
		}
	}
	
	if(count($counts) == 1) { $count[0] = 1; }
	
  	sort($counts = array_keys($counts));
  	$step = mk_step($font_min, $font_max, $counts);
	
	for($i=0;$i<count($months);$i++) {
		echo '<span style="display: block; float: left; margin: 8px; font-size: ' . mk_stepped_base($font_min, $step, $months[$i]['posts']) . $font_unit . ';">' . get_archives_link($months[$i]['url'], $months[$i]['name'], $format, $before, $after) . '</span>';
	}

}

function category_cloud(
  $font_min          = 8, /* Minimum font size */
  $font_max          = 24, /* Maximum font size */
  $font_unit         = 'pt', /* Font unit */
  $limit			 = 0 /* 0 for unlimited */
) { 

	if($limit == 0) {
		$all_cats = trim(list_cats(1, 'all', 'name', 'asc', '', 0, 0, 1, 1, 1, 1, 1, 0, 1, '', '', $cats_to_exclude, 0));
	}
	else {
		$all_cats = trim(list_cats(1, 'all', 'ID', 'DESC LIMIT ' . $limit, '', 0, 0, 1, 1, 1, 1, 1, 0, 1, '', '', $cats_to_exclude, 0));
	}
	foreach (explode("\n", $all_cats) as $cat)
	{
		preg_match('{a href="([^"]+)" title="([^"]+)".*?>(.+) ?\((\d+)\)}', $cat, $matches);
		list(,$url, $title, $name, $count) = $matches;
		printf("\t".'<a href="%s" title="%s (%s %s)" class="size-%d">%s</a>'."\n",
		$url, $title, $count, abs($count) != 1 ? 'entries' : 'entry', $count, $name);
		$counts[$count] = 1;
	}

	sort($counts = array_keys($counts));
	$step = mk_step($font_min, $font_max, $counts);

	echo '<style type="text/css">';
	foreach($counts as $count){
		echo "\t.size-$count { font-size: " . mk_stepped_base($font_min, $step, $count) . $font_unit . "; display: block; float: left; margin: 5px; padding: 3px; }\n";
	}
	echo ' </style>'."\n";
}

?>
