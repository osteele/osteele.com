<?php 
/* 
 Plugin Name: Extended Live Archives 
 Plugin URI: http://www.sonsofskadi.net/index.php/extended-live-archive/
 Description: Implements a dynamic archive, inspired by <a href="http://binarybonsai.com/archives/2004/11/21/freya-dissection/#livearchives">Binary Bonsai</a> and the original <a href="http://www.jonas.rabbe.com/archives/2005/05/08/super-archives-plugin-for-wordpress/">Super Archives by Jonas Rabbe</a>. Visit <a href="options-general.php?page=af-extended-live-archive/af-extended-live-archive-options.php">the ELA option panel</a> to initialize the plugin.
 Version: 0.10beta-r5
 Author: Arnaud Froment
 Author URI: http://www.sonsofskadi.net/ 
 */

/*  Extended Live Archives is extensively based on code from Jonas Rabbe and his 
    Super Archives Plugin. This is merely an extension to this already existing 
	wonderful plugin (see 
	http://www.jonas.rabbe.com/archives/2005/05/08/super-archives-plugin-for-wordpress/)
	for more info.

    Copyright 2005  Arnaud Froment 
	Copyright 2005  Jonas Rabbe  (email : jonas@rabbe.com)
	
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
$af_ela_cache_root = ABSPATH . 'wp-content/af-extended-live-archive/';
$utw_is_present = false;
if (file_exists(ABSPATH . 'wp-content/plugins/UltimateTagWarrior/ultimate-tag-warrior-core.php') && in_array('UltimateTagWarrior/ultimate-tag-warrior.php', get_option('active_plugins'))) {
	@include_once(ABSPATH . 'wp-content/plugins/UltimateTagWarrior/ultimate-tag-warrior-core.php');
	$utw_is_present=true;
}
	/* ***********************************
	 * main template function.
	 * ***********************************/	 
function af_ela_super_archive($arguments = '') {
	global $wpdb, $af_ela_cache_root;
	
	$settings = get_option('af_ela_options');
	$is_initialized = get_option('af_ela_is_initialized');
	if (!$settings || !$is_initialized || strstr($settings['installed_version'], $is_initialized) === false ) {
		echo '<div id="af-ela"><p class="alert">Plugin is not initialized. Admin or blog owner, <a href="' . get_settings('home') . '/wp-admin/options-general.php?page=af-extended-live-archive/af-extended-live-archive-options.php">visit the ELA option panel</a> in your admin section.</p></div>';
		return false;
	}
	
	
	/* ****************************************************
	 * Those three lines are here on a temporary basis so *
	 * that the beta testers get some kind a backward     *
	 * compatibility with 0.9+                            *
	 * ****************************************************/
	$settingsOverride = array();
	parse_str($arguments, $settingsOverride);
	$settings = array_merge($settings, $settingsOverride);
	/* ****************************************************/
	
	
	$settings['loading_content'] = urldecode($settings['loading_content']);
	$settings['idle_content'] = urldecode($settings['idle_content']);
	$settings['selected_text'] = urldecode($settings['selected_text']);
	$settings['truncate_title_text'] = urldecode($settings['truncate_title_text']);
	
	$options = get_option('af_ela_super_archive');
	
	if( $options === false ) {
		// create and store default options
		$options = array();
		$options['num_posts'] = 0;
		$options['last_post_id'] = 0;
	}
	
	$num_posts = $wpdb->get_var("
		SELECT COUNT(ID) 
		FROM $wpdb->posts 
		WHERE post_status = 'publish'");
		
	$last_post_id = $wpdb->get_var("
		SELECT ID 
		FROM $wpdb->posts 
		WHERE post_status = 'publish' 
		ORDER BY post_date DESC LIMIT 1");
	
	if( !is_dir($af_ela_cache_root) || !is_file($af_ela_cache_root.'years.dat') || $num_posts != $options['num_posts'] || $last_post_id != $options['last_post_id'] ) {
		$options['num_posts'] = $num_posts;
		$options['last_post_id'] = $last_post_id;
		update_option('af_ela_super_archive', $options);

		$res = true;
		if( !is_dir($af_ela_cache_root) ) {
			if( !mkdir($af_ela_cache_root, 0777) ) {
				$res = false;
			}
		}
		
		if( $res === true ) {
			$res = af_ela_create_cache($settings);
		}
			
		if( $res === false ) {
			// we could not create the cache, bail with error message
			echo '<div id="'.$settings['id'].'"><p class="'.$settings['error_class'].'">Could not create cache. Make sure the wp-content folder is writable by the web server.</p></div>';
			return false;
		}
	}
	
	$year = date('Y');
	$text = <<<TEXT

<div id="${settings['id']}"></div>

TEXT;

	echo $text;
}

	/* ***********************************
	 * loading stuff in the header.
	 * ***********************************/	
function af_ela_header() {
	// loading stuff
	$settings = get_option('af_ela_options');

	$plugin_path = get_settings('siteurl') . '/wp-content/plugins/af-extended-live-archive';
	if ($settings['use_default_style']) { 
		$text .= <<<TEXT
		
		<link rel="stylesheet" href="$plugin_path/includes/af-ela-style.css" type="text/css" media="screen" />

TEXT;
	} else { 
		$text ='';
	}
	$text .= <<<TEXT

	<script type="text/javascript" src="$plugin_path/includes/af-extended-live-archive.js.php"></script>
	
TEXT;
	echo $text;
}

	/* ***********************************
	 * actions when a comment changes
	 * ***********************************/	
function af_ela_comment_change($id) {
	global $wpdb;
	$generator = new af_ela_classGenerator;
	
	$settings = get_option('af_ela_options');
	
	if ($id) $generator->buildPostToGenerateTable($settings['excluded_categories'], $id, true);
	
	$generator->buildPostsInMonthsTable($settings['excluded_categories'], $settings['hide_pingbacks_and_trackbacks'], $generator->postToGenerate['post_id']);
		
	$generator->buildPostsInCatsTable($settings['excluded_categories'],$settings['hide_pingbacks_and_trackbacks'], $generator->postToGenerate['post_id'] );

	//logthis("Queries:".$wpdb->num_queries);
	return $id;
}

	/* ***********************************
	 * actions when a post changes
	 * ***********************************/	
function af_ela_post_change($id) {
	global $wpdb,$utw_is_present;
	$generator = new af_ela_classGenerator;
	
	$settings = get_option('af_ela_options');
	
	if ($id) {
		$generator->buildPostToGenerateTable($settings['excluded_categories'], $id);
	}	
	
	$generator->buildYearsTable($settings['excluded_categories'], $id);

	$generator->buildMonthsTable($settings['excluded_categories'], $id);
	
	$generator->buildPostsInMonthsTable($settings['excluded_categories'], $settings['hide_pingbacks_and_trackbacks'], $id);
		
	$generator->buildCatsTable($settings['excluded_categories'], $id);
	
	$generator->buildPostsInCatsTable($settings['excluded_categories'], $settings['hide_pingbacks_and_trackbacks']);
	
	if($utw_is_present) $generator->buildTagsTable($settings['excluded_categories'], $id);
	
	if($utw_is_present) $generator->buildPostsInTagsTable($settings['excluded_categories'], $settings['hide_pingbacks_and_trackbacks']);
	
	//logthis("Queries:".$wpdb->num_queries);
	return $id;
}

	/* ***********************************
	 * creation of the cache
	 * ***********************************/	
function af_ela_create_cache($settings) {
	global $wpdb, $af_ela_cache_root, $utw_is_present;

	if( !is_dir($af_ela_cache_root) ) {
		if( !mkdir($af_ela_cache_root) ) return false;
	}
	$generator = new af_ela_classGenerator;
	
	$generator->buildYearsTable($settings['excluded_categories']);

	$generator->buildMonthsTable($settings['excluded_categories']);
	
	$generator->buildPostsInMonthsTable($settings['excluded_categories'], $settings['hide_pingbacks_and_trackbacks']);

	$generator->buildCatsTable($settings['excluded_categories']);

	$generator->buildPostsInCatsTable($settings['excluded_categories'], $settings['hide_pingbacks_and_trackbacks']);
	
	if($utw_is_present) $generator->buildTagsTable($settings['excluded_categories']);
	
	if($utw_is_present) $generator->buildPostsInTagsTable($settings['excluded_categories'], $settings['hide_pingbacks_and_trackbacks']);
	
	//logthis("Queries:".$wpdb->num_queries);
	return true;
}
	/* ***********************************
	 * dirty little debug function
	 * ***********************************/	
function logthis($message) {
	global $af_ela_cache_root;
	$handle = @fopen($af_ela_cache_root . "log.log", 'a');
	if( $handle === false ) {
		return false;
	}
	fwrite($handle, serialize($message));
	fwrite($handle, "\r\n");
	fclose($handle);
	
}

	/* ***********************************
	 * Cache generator class
	 * ***********************************/	
class af_ela_classGenerator {
	
	var $cache;
	var $utwCore;
	var $yearTable = array();
	var $monthTable = array();
	var $catsTable = array();
	var $postsInCatsTable = array();
	var $postToGenerate = array();
	var $tagsTable = array();
	var $postsInTagsTable = array();
	
	
	/* ***********************************
	 * Helper Function : class creator
	 * ***********************************/	
	function af_ela_classGenerator() {
		global $utw_is_present;
		$this->cache = new af_ela_classCacheFile('');
		if($utw_is_present) $this->utwCore = new UltimateTagWarriorCore;
		return true;
	}
	
	/* ***********************************
	 * Helper Function : Find info about 
	 * 		updated post.
	 * ***********************************/	
	function buildPostToGenerateTable($exclude, $id, $commentId = false) {
		global $wpdb, $tabletags, $tablepost2tag;
		
		if (!empty($exclude)) {
			$excats = preg_split('/[\s,]+/',$exclude);
			if (count($excats)) {
				foreach ($excats as $excat) {
					$exclusions .= ' AND category_id <> ' . intval($excat) . ' ';
				}
			}
		}

		if(!$commentId) {
			if($id) { 
				$dojustid = ' AND ID = ' . intval($id) . ' ' ;
			}

			$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, category_id 
				FROM $wpdb->posts 
				INNER JOIN $wpdb->post2cat ON ($wpdb->posts.ID = $wpdb->post2cat.post_id)
				WHERE post_date > 0
				$exclusions $dojustid
				ORDER By post_date DESC";
			$results = $wpdb->get_results($query);
			if ($results) {
				foreach($results as $result) {
					$this->postToGenerate['category_id'][] = $result->category_id;
				}
			}
			$this->postToGenerate['new_year']= $results[0]->year;
			$this->postToGenerate['new_month']= $results[0]->month;
			
			// For UTW
			$query = "SELECT tag_id
				FROM $wpdb->posts 
				INNER JOIN $wpdb->post2cat ON ($wpdb->posts.ID = $wpdb->post2cat.post_id)
				INNER JOIN $tablepost2tag ON ($wpdb->posts.ID = $tablepost2tag.post_id) 
				WHERE post_date > 0
				$exclusions $dojustid
				ORDER By post_date DESC";
			$results = $wpdb->get_results($query);
			if ($results) {
				foreach($results as $result) {
					$this->postToGenerate['tag_id'][] = $result->tag_id;
				}
			}
			// End of stuff for UTW
			
			return true;
		} else {
			$query = "SELECT comment_post_ID  
				FROM $wpdb->comments
				WHERE comment_ID = $id AND comment_approved = '1'";
			$result = $wpdb->get_var($query);
			if ($result) {
				$id = $result;
				if($id) {
					$dojustid = ' AND ID = ' . intval($id) . ' ' ;
				}

				$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, category_id
					FROM $wpdb->posts 
					INNER JOIN $wpdb->post2cat ON ($wpdb->posts.ID = $wpdb->post2cat.post_id)
					WHERE post_date > 0
					$exclusions $dojustid
					ORDER By post_date DESC";
				$results = $wpdb->get_results($query);
				if($results) {
					foreach($results as $result) {
						$this->postToGenerate['category_id'][]=$result->category_id;
					}
					$this->postToGenerate['post_id'] = $id;
					$this->postToGenerate['new_year']= $results[0]->year;
					$this->postToGenerate['new_month'] = $results[0]->month;
					$this->yearTable = array($this->postToGenerate['new_year'] => 0);
					$this->monthTable[$this->postToGenerate['new_year']] = array($this->postToGenerate['new_month'] => 0);
					$this->catsTable = $this->postToGenerate['category_id'];
					return true;
				}
			}
			return false;
		}
	}
	
	
	/* ***********************************
	 * Helper Function : build Years.
	 * ***********************************/	
	function buildYearsTable($exclude, $id = false) {
		global $wpdb;
		
		if (!empty($exclude)) {
			$excats = preg_split('/[\s,]+/',$exclude);
			if (count($excats)) {
				foreach ($excats as $excat) {
					$exclusions .= ' AND p2c.category_id <> ' . intval($excat) . ' ';
				}
			}
		}
		$now = current_time('mysql', 1);
		
		$query = "SELECT DISTINCT YEAR(p.post_date) AS `year`
			FROM $wpdb->posts p 
			INNER JOIN $wpdb->post2cat p2c ON (p.ID = p2c.post_id)
			WHERE p.post_date > 0
			$exclusions 
			ORDER By p.post_date DESC";
		$year_results = $wpdb->get_results($query);
		if( $year_results ) {
			foreach( $year_results as $year_result ) {
				$query = "SELECT p.ID
					FROM $wpdb->posts p  
					INNER JOIN $wpdb->post2cat p2c ON (p.ID = p2c.post_id) 
					WHERE YEAR(p.post_date) = $year_result->year 
					$exclusions 
					AND p.post_status = 'publish' 
					AND p.post_date_gmt < '$now'
					GROUP BY p.ID";
				$num_entries_for_year = $wpdb->get_results($query);
				if(count($num_entries_for_year)) $this->yearTable[$year_result->year] = count($num_entries_for_year);
			}
		}
		if ($this->yearTable) {
			$this->cache->contentIs($this->yearTable);
			$this->cache->writeFile('years.dat');
		}
		if($id) {
			$this->cache->readFile('years.dat');
			$diffyear = array_diff_assoc($this->cache->readFileContent, $this->yearTable);
			if (!empty($diffyear)) {
				$this->yearTable = $diffyear;
			} else {
				$this->yearTable = array($this->postToGenerate['new_year'] => 0);
			}
		}
	}
	
	/* ***********************************
	 * Helper Function : build Months.
	 * ***********************************/
	function buildMonthsTable($exclude, $id = false) {
		global $wpdb;
		
		if (!empty($exclude)) {
			$excats = preg_split('/[\s,]+/',$exclude);
			if (count($excats)) {
				foreach ($excats as $excat) {
					$exclusions .= ' AND p2c.category_id <> ' . intval($excat) . ' ';
				}
			}
		}
		
		$now = current_time('mysql', 1);
		foreach( $this->yearTable as $year => $y ) {
			$query = "SELECT DISTINCT MONTH(p.post_date) AS `month` 
				FROM $wpdb->posts p
				INNER JOIN $wpdb->post2cat p2c ON (p.ID = p2c.post_id )
				WHERE YEAR(p.post_date) = $year 
				$exclusions  
				AND p.post_date_gmt < '$now' 
				ORDER By p.post_date DESC";
			$month_results = $wpdb->get_results($query);
			if( $month_results ) {
				foreach( $month_results as $month_result ) {
					$query = "SELECT p.ID 
						FROM $wpdb->posts p
						INNER JOIN $wpdb->post2cat p2c ON (p.ID = p2c.post_id) 
						WHERE YEAR(p.post_date) = $year 
						$exclusions
						AND MONTH(p.post_date) = $month_result->month 
						AND p.post_status = 'publish' 
						AND p.post_date_gmt < '$now' 
						GROUP BY p.ID";
					$num_entries_for_month = $wpdb->get_results($query);
					if (count($num_entries_for_month)) $this->monthTable[$year][$month_result->month] = count($num_entries_for_month);
				}
				if ($this->monthTable[$year]) {
					$this->cache->contentIs($this->monthTable[$year]);
					$this->cache->writeFile($year . '.dat');
				}
				if($id) {
					$this->cache->readFile($year . '.dat');
					$diffmonth = array_diff_assoc($this->cache->readFileContent, $this->monthTable[$year]);
					if (!empty($diffmonth)) {
						$this->monthTable[$year] = $diffmonth;
					} else {
						$this->monthTable[$year] = array($this->postToGenerate['new_month'] => 0);
					}
				}
			}
		}
	}
	
	/* ***********************************
	 * Helper Function : build Posts in 
	 * 			Month.
	 * ***********************************/
	function buildPostsInMonthsTable($exclude, $hide_ping_and_track, $id = false) {
		global $wpdb;
		if( 1 == $hide_ping_and_track ) {
			$ping = "AND comment_type NOT LIKE '%pingback%' AND comment_type NOT LIKE '%trackback%'";
		} else {
			$ping = '';
		}
		
		if (!empty($exclude)) {
			$excats = preg_split('/[\s,]+/',$exclude);
			if (count($excats)) {
				foreach ($excats as $excat) {
					$exclusions .= ' AND category_id <> ' . intval($excat) . ' ';
				}
			}
		}
		
		$posts = array();
		$now = current_time('mysql', 1);
		foreach( $this->yearTable as $year => $y ) {
			$posts[$year] = array();
			foreach( $this->monthTable[$year] as $month =>$m ) {
				$posts[$year][$month] = array();
				$query = "SELECT ID, post_title, DAYOFMONTH(post_date) as `day`, comment_status 
					FROM $wpdb->posts 
					WHERE YEAR(post_date) = $year 
					AND MONTH(post_date) = $month 
					AND post_status = 'publish' 
					AND post_date_gmt < '$now' 
					ORDER By post_date DESC";
				$post_results = $wpdb->get_results($query);
				if( $post_results ) {
					foreach( $post_results as $post_result ) {
						$query = "SELECT category_id
							FROM $wpdb->post2cat 
							WHERE post_id = $post_result->ID
							$exclusions";
						$posts_in_cat_results = $wpdb->get_results($query);
						if (!empty($posts_in_cat_results)) {
							$query = "SELECT COUNT(comment_ID) FROM $wpdb->comments 
								WHERE comment_post_ID = $post_result->ID 
								AND comment_approved = '1' 
								$ping";
							$num_comments = $wpdb->get_var($query);
							$posts[$year][$month][$post_result->ID] = array($post_result->day, $post_result->post_title, get_permalink($post_result->ID), $num_comments, $post_result->comment_status);
						}
					}
				}
				if ($posts[$year][$month]) {
					$this->cache->contentIs($posts[$year][$month]);
					$this->cache->writeFile($year . '-' . $month . '.dat');
				}
			}
		}
	}
	
	/* ***********************************
	 * Helper Function : build Categories.
	 * ***********************************/	
	function buildCatsTable($exclude='', $id = false) {
		
		$this->buildCatsList('ID', 'asc', FALSE, TRUE, '0', 0, $exclude, TRUE);
		foreach( $this->catsTable as $category ) {
			$parentcount = 0;
			if(($parentkey = $category[4])) {
				$parentcount++;
				while($parentkey) {
					$parentcount++;
					$this->catsTable[$parentkey][6] = TRUE;
					$parentkey=$this->catsTable[$parentkey][4];
				}
			}
			$this->catsTable[$category[0]][5] = $parentcount;
		}
		
		foreach( $this->catsTable as $category ) {
			if ($category[6] == TRUE || intval($category[3]) > 0) {
				$this->catsTable[$category[0]][6] = TRUE;
			} else {
				$this->catsTable[$category[0]][6] = FALSE;
			}
		}

		if($id) {
			$this->cache->readFile('categories.dat');
			foreach($this->cache->readFileContent as $key => $value) {
				$diffcats[$value[0]] = array_diff_assoc($value, $this->catsTable[$value[0]]);
			}
		}

		$this->cache->contentIs($this->catsTable);
		$this->cache->writeFile('categories.dat');
		
		if($id) {			
			if (!empty($diffcats)) {
				$this->catsTable = $diffcats;
			} else {
				$this->catsTable = $this->postToGenerate['categories_id'];
			}
		}
	}
	
	/* ***********************************
	 * Helper Function : build list of cats
	 * ***********************************/	
	function buildCatsList($sort_column = 'ID', $sort_order = 'asc', $hide_empty = FALSE, $children=TRUE, $child_of=0, $categories=0, $exclude = '', $hierarchical=TRUE, $id = false) {
		global $wpdb, $category_posts;
		
		if (!empty($exclude)) {
			$excats = preg_split('/[\s,]+/',$exclude);
			if (count($excats)) {
				foreach ($excats as $excat) {
					$exclusions .= ' AND c.cat_ID <> ' . intval($excat) . ' ';
				}
			}
		}
	
		if (intval($categories)==0){
			$sort_column = 'c.cat_'.$sort_column;
	
			$query  = "SELECT cat_ID, cat_name, category_nicename, category_parent
				FROM $wpdb->categories c
				WHERE c.cat_ID > 0 $exclusions $dojustid
				ORDER BY $sort_column $sort_order";
	
			$categories = $wpdb->get_results($query);
		}
	
		if (!count($category_posts)) {
			$now = current_time('mysql', 1);
			$query = "SELECT c.cat_ID,
				COUNT(distinct p2c.post_id) AS cat_count
				FROM $wpdb->categories c
				INNER JOIN $wpdb->post2cat p2c ON (c.cat_ID = p2c.category_id)
				INNER JOIN $wpdb->posts p ON (p.ID = p2c.post_id)
				WHERE p.post_status = 'publish'
				AND p.post_date_gmt < '$now' 
				$exclusions 
				GROUP BY p2c.category_id";
				
			$cat_counts = $wpdb->get_results($query);
			
	        if (! empty($cat_counts)) {
	            foreach ($cat_counts as $cat_count) {
	                if (1 != intval($hide_empty) || $cat_count > 0) {
	                    $category_posts[$cat_count->cat_ID] = $cat_count->cat_count;
	                }
	            }
	        }
		}
		foreach ($categories as $category) {
			if ((intval($hide_empty) == 0 || isset($category_posts[$category->cat_ID])) && (!$hierarchical || $category->category_parent == $child_of) ) {
				$this->catsTable[$category->cat_ID] = array(	$category->cat_ID, 
	 															$category->cat_name,
	 															$category->category_nicename, 
																$category_posts["$category->cat_ID"], 
	 															$category->category_parent);
				if ($hierarchical && $children) {
					$this->buildCatsList(	$sort_column,
										$sort_order, 
										$hide_empty, 
										$children, 
										$category->cat_ID, 
										$categories, 
										$exclude, 
										$hierarchical);
				}
			}
		}
	}
	
	/* ***********************************
	 * Helper Function : build Posts In 
	 * 			Categories
	 * ***********************************/	
	function buildPostsInCatsTable($exclude='',$hide_ping_and_track) {
		global $wpdb, $category_posts;
		
		if( 1 == $hide_ping_and_track ) {
			$ping = "AND comment_type NOT LIKE '%pingback%' AND comment_type NOT LIKE '%trackback%'";
		} else {
			$ping = '';
		}
		if (!empty($exclude)) {
			$excats = preg_split('/[\s,]+/',$exclude);
			if (count($excats)) {
				foreach ($excats as $excat) {
					$exclusions .= ' AND category_id <> ' . intval($excat) . ' ';
				}
			}
		}
		
		$now = current_time('mysql', 1);
		foreach( $this->catsTable as $category ) {
			$posts_in_cat[$category[0]] = array();
			$query = "SELECT post_id
				FROM $wpdb->post2cat 
				WHERE category_id = $category[0] 
				$exclusions";
			$posts_in_cat_results = $wpdb->get_results($query);
	
			if( $posts_in_cat_results ) {
				$posts_in_cat_results = array_reverse($posts_in_cat_results);
				foreach( $posts_in_cat_results as $post_in_cat_result ) {
					
					$query = "SELECT ID, post_title, post_date as `day`, comment_status 
						FROM $wpdb->posts 
						WHERE ID = $post_in_cat_result->post_id 
						AND post_status = 'publish' 
						AND post_date_gmt <= '$now'
						ORDER By post_date";
					$post_results = $wpdb->get_results($query);
					if( $post_results ) {
						foreach( $post_results as $post_result ) {
							$query = "SELECT COUNT(comment_ID) 
								FROM $wpdb->comments 
								WHERE comment_post_ID = $post_result->ID 
								AND comment_approved = '1' 
								$ping";
							$num_comments = $wpdb->get_var($query);
							$this->postsInCatsTable[$category[0]][$post_result->ID] = array($post_result->day, $post_result->post_title, get_permalink($post_result->ID), $num_comments, $post_result->comment_status);
						}
					}
				}
				if ($this->postsInCatsTable[$category[0]]) {
					$this->cache->contentIs($this->postsInCatsTable[$category[0]]);
					$this->cache->writeFile('cat-' . $category[0] . '.dat');
				}
			}
		}
	}
	
	/* ***********************************
	 * Helper Function : build Tags.
	 * ***********************************/	
	function buildTagsTable($exclude='', $id = false) {
		global $utw_is_present;
		if($utw_is_present) {
			global $wpdb, $tabletags, $tablepost2tag;
			
			if (!empty($exclude)) {
				$excats = preg_split('/[\s,]+/',$exclude);
				if (count($excats)) {
					foreach ($excats as $excat) {
						$exclusions .= ' AND p2c.category_id <> ' . intval($excat) . ' ';
					}
				}
			}
			$now = current_time('mysql', 1);
	
			$query = "SELECT t.tag_id, t.tag, count(distinct p2t.post_id) as tag_count
				FROM $tabletags t 
				INNER JOIN $tablepost2tag p2t ON t.tag_id = p2t.tag_id
				INNER JOIN $wpdb->posts p ON p2t.post_id = p.ID
				INNER JOIN $wpdb->post2cat p2c ON p2t.post_id = p2c.post_ID
				WHERE p.post_date_gmt < '$now'
				AND p.post_status = 'publish'
				$exclusions
				GROUP BY t.tag";
			$tagsSet = $wpdb->get_results($query);
			$tagged_posts = 0;
			$posted_tags = 0;
			foreach($tagsSet as $tag) {
				if ($tag->tag_count) {
					$this->tagsTable[$tag->tag_id] = array($tag->tag_id, $tag->tag, $tag->tag_count );
					$tagged_posts++;
					if (intval($posted_tags) < intval($tag->tag_count)) $posted_tags = $tag->tag_count;
				}
			}
			$this->tagsTable[0] = array($tagged_posts, $posted_tags);
			
			$this->cache->contentIs($this->tagsTable);
			$this->cache->writeFile('tags.dat');
			
			if($id) {
				$this->cache->readFile('tags.dat');
				$difftags = array_diff_assoc($this->cache->readFileContent, $this->tagsTable);
				if (!empty($difftags)) {
					$this->tagsTable = $difftags;
				} else {
					$this->tagsTable = $this->postToGenerate['tag_id'];
				}
			}
		}		
	}
	
	
	/* ***********************************
	 * Helper Function : build Posts In 
	 * 			Tags
	 * ***********************************/	
	function buildPostsInTagsTable($exclude='',$hide_ping_and_track) {
		global $utw_is_present;
		if($utw_is_present) { 	
			global $wpdb, $tabletags, $tablepost2tag;
			
			if( 1 == $hide_ping_and_track ) {
				$ping = "AND comment_type NOT LIKE '%pingback%' AND comment_type NOT LIKE '%trackback%'";
			} else {
				$ping = '';
			}
			
			if (!empty($exclude)) {
				$excats = preg_split('/[\s,]+/',$exclude);
				if (count($excats)) {
					foreach ($excats as $excat) {
						$exclusions .= ' AND p2c.category_id <> ' . intval($excat) . ' ';
					}
				}
			}
			
			$now = current_time('mysql', 1);
			foreach( $this->tagsTable as $tag) {
				$posts_in_tags[$tags[0]] = array();
				$query = "SELECT p2t.post_id
					FROM $tablepost2tag p2t 
					INNER JOIN $wpdb->post2cat p2c ON p2t.post_id = p2c.post_ID
					WHERE p2t.tag_id = $tag[0] 
					$exclusions";
				$posts_in_tag_results = $wpdb->get_results($query);
		
				if( $posts_in_tag_results ) {
					$posts_in_tag_results = array_reverse($posts_in_tag_results);
					foreach( $posts_in_tag_results as $posts_in_tag_result ) {
						
						$query = "SELECT ID, post_title, post_date as `day`, comment_status 
							FROM $wpdb->posts 
							WHERE ID = $posts_in_tag_result->post_id 
							AND post_status = 'publish' 
							AND post_date_gmt <= '$now'
							ORDER By post_date";
						$post_results = $wpdb->get_results($query);
						if( $post_results ) {
							foreach( $post_results as $post_result ) {
								$query = "SELECT COUNT(comment_ID) 
									FROM $wpdb->comments 
									WHERE comment_post_ID = $post_result->ID 
									AND comment_approved = '1' 
									$ping";
								$num_comments = $wpdb->get_var($query);
								$this->postsInTagsTable[$tag[0]][$post_result->ID] = array($post_result->day, $post_result->post_title, get_permalink($post_result->ID), $num_comments, $post_result->comment_status);
							}
						}
					}
					if ($this->postsInTagsTable[$tag[0]]) {
						$this->cache->contentIs($this->postsInTagsTable[$tag[0]]);
						$this->cache->writeFile('tag-' . $tag[0] . '.dat');
					}
				}
			}
		}
	}
}




	/* ***********************************
	 * Cache File Handling class
	 * ***********************************/	
class af_ela_classCacheFile {
	var $fileContent = array();
	var $readFileContent = array();
	var $fileName;
	var $dbResults = array();
	
	/* ***********************************
	 * Helper Function : class creator
	 * ***********************************/	
	function af_ela_classCacheFile($filename) {
		$this->fileName = $filename;
		return true;
	}
	
	/* ***********************************
	 * Helper Function : set fileContent 
	 * 			property
	 * ***********************************/	
	function contentIs($content) {
		$this->fileContent = $content;
		return true;
	}
	
	/* ***********************************
	 * Helper Function : read an existing 
	 * 			file and set 
	 * 			readFileContent property
	 * ***********************************/	
	function readFile($filename = false) {
		global $af_ela_cache_root;
		
		if($filename) $this->fileName = $filename;
		
		$handle = fopen ($af_ela_cache_root.$this->fileName, "r");
		if( $handle === false ) {
			return false;
		}
		
		$buf = fread($handle, filesize($af_ela_cache_root.$this->fileName));
		$this->readFileContent = unserialize($buf);
		
		fclose ($handle);
	}
	
	/* ***********************************
	 * Helper Function : actual flushing 
	 * 			of fileContent to the file 
	 * 			system
	 * ***********************************/	
	function writeFile($filename = false) {
		global $af_ela_cache_root;
		
		if($filename) $this->fileName = $filename;
		
		$handle = @fopen($af_ela_cache_root . $this->fileName, 'w');
		if( $handle === false ) {
			return false;
		}
		fwrite($handle, serialize($this->fileContent));
		fclose($handle);
		return true;
	}
	
	/* ***********************************
	 * Helper Function : deletes cache 
	 * 			files
	 * ***********************************/	
	function deleteFile($id = false, $exclude = '') {
		global $wpdb, $af_ela_cache_root;
		
		$del_cache_path = $af_ela_cache_root . "*.dat";
		// 	delete the cache files
		if ( ($filelist=glob($del_cache_path)) === false ) return false;
		foreach ($filelist as $filename) {
			if (!@unlink($filename)) return false;	// delete it
		}
		return true;
	}
}
			
function af_ela_admin_pages() {
	if (function_exists('add_options_page')) add_options_page('Ext. Live Archive Options', 'Ext. Live Archive', 9, get_settings('siteurl') . '/wp-content/plugins/af-extended-live-archive/af-extended-live-archive-options.php');
}

if( function_exists('add_action') ) {
	// insert javascript in headers
	add_action('wp_head', 'af_ela_header');
	
	// make sure the cache is rebuilt when post changes
	add_action('publish_post', 'af_ela_post_change');
	add_action('delete_post', 'af_ela_post_change');
	// make sure the cache is rebuilt when comments change
	add_action('comment_post', 'af_ela_comment_change');
	add_action('trackback_post', 'af_ela_comment_change');
	add_action('pingback_post', 'af_ela_comment_change');
	add_action('delete_comment', 'af_ela_comment_change');
}

add_action('admin_menu', 'af_ela_admin_pages');
?>