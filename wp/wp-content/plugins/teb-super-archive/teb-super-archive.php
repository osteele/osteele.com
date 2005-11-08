<?php 
/* 
 Plugin Name: Super Archive 
 Plugin URI: http://www.jonas.rabbe.com/archives/2005/05/08/super-archives-plugin-for-wordpress/ 
 Description: Implements a dynamic archive, inspired by <a href="http://binarybonsai.com/archives/2004/11/21/freya-dissection/#livearchives">Binary Bonsai</a>.
 Version: 1.6.2
 Author: Jonas Rabbe 
 Author URI: http://www.jonas.rabbe.com/ 
 */

/*  Copyright 2005  Jonas Rabbe  (email : jonas@rabbe.com)

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

$teb_sa_cache_root = ABSPATH . 'wp-content/teb-super-archive-cache/';
$teb_sa_cache_path = $teb_sa_cache_root . 'cache-files/';

// main template function
function teb_super_archive($arguments = '') {
	global $wpdb;
	global $teb_sa_cache_root;
	
	parse_str($arguments, $settings);
	if( !isset($settings['newest_first']) ) $settings['newest_first'] = 1;
	if( !isset($settings['id']) ) $settings['id'] = 'teb-super-archive';
	if( !isset($settings['selected_text']) ) $settings['selected_text'] = '';
	if( !isset($settings['selected_class']) ) $settings['selected_class'] = 'selected';
	if( !isset($settings['num_entries']) ) $settings['num_entries'] = 0;
	if( !isset($settings['num_comments']) ) $settings['num_comments'] = 0;
	if( !isset($settings['day_format']) ) $settings['day_format'] = '';
	if( !isset($settings['closed_comment_text']) ) $settings['closed_comment_text'] = '';
	if( !isset($settings['comment_text']) ) $settings['comment_text'] = '(%)';
	if( !isset($settings['number_text']) ) $settings['number_text'] = '(%)';
	if( !isset($settings['fade']) ) $settings['fade'] = 0;
	if( !isset($settings['error_class']) ) $settings['error_class'] = 'alert';
	if( !isset($settings['hide_pingbacks_and_trackbacks']) ) $settings['hide_pingbacks_and_trackbacks'] = 0;

	// allow truncating of titles
	if( !isset($settings['truncate_title_length']) ) $settings['truncate_title_length'] = 0;
	if( !isset($settings['truncate_title_at_space']) ) $settings['truncate_title_at_space'] = 1;
	if( !isset($settings['truncate_title_text']) ) $settings['truncate_title_text'] = '&#8230;';
	
	// we always set the character set from the blog settings
	$settings['charset'] = get_bloginfo('charset');
	
	$settings['selected_text'] = urldecode($settings['selected_text']);
	$settings['truncate_title_text'] = urldecode($settings['truncate_title_text']);
	
	$options = get_option('teb_super_archive');
	
	if( $options === false )
	{
		// create and store default options
		$options = array();
		$options['num_posts'] = 0;
		$options['last_post_id'] = 0;
	}
	
	$num_posts = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = 'publish'");
	$last_post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
	
	if( !is_dir($teb_sa_cache_root.'cache-files') || $num_posts != $options['num_posts'] || $last_post_id != $options['last_post_id'] ) {
		$options['num_posts'] = $num_posts;
		$options['last_post_id'] = $last_post_id;
		update_option('teb_super_archive', $options);

		$res = true;
		if( !is_dir($teb_sa_cache_root) ) {
			if( !mkdir($teb_sa_cache_root, 0775) ) {
				$res = false;
			}
		}
		
		if( $res === true ) {
			$res = teb_sa_create_cache();
		}

		if( is_file($teb_sa_cache_root . 'years.dat') ) {
			teb_sa_clean_old_cache();
		}		
		
		if( $res === false ) {
			// we could not create the cache, bail with error message
			echo '<div id="'.$settings['id'].'"><p class="'.$settings['error_class'].'">Could not create cache. Make sure the wp-content folder is writable by the web server.</p></div>';
			return;
		}
	}
	
	$local = fopen($teb_sa_cache_root.'settings.dat', 'w');
	fwrite($local, serialize($settings));
	fclose($local);
	
	$year = date('Y');
	$text = <<<TEXT
<div id="${settings['id']}"></div>
TEXT;

	echo $text;
}

// plugin support functions
function teb_sa_reset_cache() {
	global $teb_sa_cache_path;
	
	$sourcedir = @opendir($teb_sa_cache_path);
	
	if( $sourcedir === false ) return; // the cache doesn't exist.
	
	while( false !== ($filename = readdir($sourcedir)) ) {
		if( is_file($teb_sa_cache_path.$filename) ) {
			 // unlink file
			 unlink($teb_sa_cache_path.$filename);
		}
	}
	closedir($sourcedir);
	if( rmdir($teb_sa_cache_path) ) {
		return true;
	} else {
		return false;
	}
}

function teb_sa_clean_old_cache() {
	global $teb_sa_cache_root;
	
	$sourcedir = @opendir($teb_sa_cache_root);
	
	if( $sourcedir === false ) return; // the cache doesn't exist.
	
	while(false !== ($filename = readdir($sourcedir))) {
		if( is_file($teb_sa_cache_root.$filename) && strstr($filename, '.dat') && $filename != 'settings.dat' ) {
			 // unlink file
			 unlink($teb_sa_cache_root.$filename);
		}
	}
	closedir($sourcedir);
}

function teb_sa_header() {
	$plugin_path = get_settings('siteurl') . '/wp-content/plugins/teb-super-archive';
	$text = <<<TEXT
<!--<script type="text/javascript" src="$plugin_path/includes/tsaAddLoadEvent.js"></script>-->
	<script type="text/javascript" src="$plugin_path/includes/super-archive.js"></script>
	<script type="text/javascript">
		var tsaProcessURI = '$plugin_path/includes/tsa.php';
	</script>

TEXT;
	echo $text;
}

function teb_sa_post_change($id) {
	teb_sa_reset_cache();
	teb_sa_create_cache();
	return $id;
}

if( function_exists('add_action') ) {
	// insert javascript in headers
	add_action('wp_head', 'teb_sa_header');
	
	// make sure the cache is rebuilt 
	add_action('publish_post', 'teb_sa_post_change');
	add_action('edit_post', 'teb_sa_post_change');
	add_action('delete_post', 'teb_sa_post_change');
	add_action('comment_post', 'teb_sa_post_change');
	add_action('trackback_post', 'teb_sa_post_change');
	add_action('pingback_post', 'teb_sa_post_change');
	add_action('edit_comment', 'teb_sa_post_change');
	add_action('delete_comment', 'teb_sa_post_change');
//	add_action('template_save', 'teb_sa_post_change');
//	add_action('switch_theme', 'teb_sa_post_change');
}

function teb_sa_create_cache() {
	global $wpdb;
	global $teb_sa_cache_path;
	
	if( !is_dir($teb_sa_cache_path) ) {
		if( !mkdir($teb_sa_cache_path, 0775) ) {
			return false;
		}
	}
	
	// find all years
	$year_results = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year` FROM $wpdb->posts WHERE post_status = 'publish' ORDER By post_date DESC");
	
	$years = array();
	$year_data = array();
	
	$files = array();
	
	if( $year_results ) {
		foreach( $year_results as $year_result ) {
			$years[] = $year_result->year;
			
			$num_entries_for_year = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE YEAR(post_date) = $year_result->year AND post_status = 'publish'");
			$year_data[$year_result->year] = array($num_entries_for_year);
		}
	}
	
	$files['years.dat'] = $year_data;
	
	// find all months for each year
	$months = array();
	$month_data = array();

	foreach( $years as $year ) {
		$months[$year] = array();
		$month_results = $wpdb->get_results("SELECT DISTINCT MONTH(post_date) AS `month` FROM $wpdb->posts WHERE YEAR(post_date) = $year AND post_status = 'publish' ORDER By post_date DESC");
		if( $month_results ) {
			foreach( $month_results as $month_result ) {
				$months[$year][] = $month_result->month;
				$num_entries_for_month = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE YEAR(post_date) = $year AND MONTH(post_date) = $month_result->month AND post_status = 'publish'");
				$month_data[$year][$month_result->month] = array($num_entries_for_month);
			}
		}
		$files[$year . '.dat'] = $month_data[$year];
	}
	
	// find all posts for a given month
	$posts = array();
	
	if( $settings['hide_pingbacks_and_trackbacks'] == 1 ) {
		$ping = "AND comment_type NOT LIKE '%pingback%' AND comment_type NOT LIKE '%trackback%'";
	} else {
		$ping = '';
	}

	foreach( $years as $year ) {
		$posts[$year] = array();
		foreach( $months[$year] as $month ) {
			$posts[$year][$month] = array();				
			$post_results = $wpdb->get_results("SELECT ID, post_title, DAYOFMONTH(post_date) as `day`, comment_status FROM $wpdb->posts WHERE YEAR(post_date) = $year AND MONTH(post_date) = $month AND post_status = 'publish' ORDER By post_date DESC");
			if( $post_results ) {
				foreach( $post_results as $post_result ) {
					$num_comments = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = $post_result->ID AND comment_approved = '1' $ping");
					$posts[$year][$month][$post_result->ID] = array($post_result->day, $post_result->post_title, get_permalink($post_result->ID), $num_comments, $post_result->comment_status);
				}
			}
			$files[$year . '-' . $month . '.dat'] = $posts[$year][$month];
		}
	}
	
	// write cache files.
	foreach( $files as $name => $content )
	{
		$handle = @fopen($teb_sa_cache_path . $name, 'w');
		if( $handle === false ) {
			return false;
		}
		
		fwrite($handle, serialize($content));
		fclose($handle);
	}
	
	return true;
}

?>
