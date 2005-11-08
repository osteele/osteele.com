<?php
/*
	Support script for the Super Archive WordPress Plugin.
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

	// get the year that is requested, if no year is requested set the year to zer0
	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : 0;
	$month = isset($_REQUEST['month']) ? $_REQUEST['month'] : 0;
	
	// the paths for the cache files and settings
	$path = '../../../teb-super-archive-cache/';
	$cache_path = $path . 'cache-files/';
	
	// get settings and construct default;
	$settings_file = @file_get_contents($path.'settings.dat');
	if( $settings_file === false ) $settings_file = '';
	
	$settings = unserialize($settings_file);
	if( $settings === false ) {
		$settings = array();
	}
	
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
	
	// allow truncating of titles
	if( !isset($settings['truncate_title_length']) ) $settings['truncate_title_length'] = 0;
	if( !isset($settings['truncate_title_at_space']) ) $settings['truncate_title_at_space'] = 1;
	if( !isset($settings['truncate_title_text']) ) $settings['truncate_title_text'] = '&#8230;';
	
	// support for custom character sets - set default to UTF-8
	if( !isset($settings['charset']) ) $settings['charset'] = 'UTF-8';
	
	// remove slashes set by serialize
	$settings['selected_text'] = stripslashes($settings['selected_text']);
	$settings['closed_comment_text'] = stripslashes($settings['closed_comment_text']);
	$settings['comment_text'] = stripslashes($settings['comment_text']);
	$settings['number_text'] = stripslashes($settings['number_text']);
	$settings['truncate_title_text'] = stripslashes($settings['truncate_title_text']);
	
	// if fade is set, check the requested year and month
	if( $settings['fade'] == 1 ) {
		if( $year == 0 && $month == 0 ) {
			$fade_year = ' class="fade"';
			$fade_month = ' class="fade"';
			$fade_post = ' class="fade"';
		} elseif( $month == 0 ) {
			$fade_month = ' class="fade"';
			$fade_post = ' class="fade"';
		} else {
			$fade_post = ' class="fade"';
		}
	}
	
	// Output charset header
	header("Content-Type: text/html; charset=${settings['charset']}");

	// Read years	
	$year_contents = @file_get_contents($cache_path . 'years.dat');
	if( $year_contents === false ) $year_contents = '';
	
	$years = unserialize($year_contents);
	if( $years === false ) {
		echo "${settings['id']}|<p class='${settings['error_class']}'>Could not open cache file for years</p>";
		die();
	}
	
	if( $settings['newest_first'] == 0 ) {
		$years = array_reverse($years, true);
	}
	
	if( !array_key_exists($year, $years) ) {
		$temp = array_keys($years);
		$year = $temp[0];
	}
	
	// Read months
	$month_contents = @file_get_contents($cache_path . $year . '.dat');
	if( $month_contents === false ) $month_contents = '';
	
	$months = unserialize($month_contents);
	if( $posts === false ) {
		echo "${settings['id']}|<p class='${settings['error_class']}'>Could not open cache file '$year.dat'</p>";
		die();
	}
	
	if( $settings['newest_first'] == 0 ) {
		$months = array_reverse($months, true);
	}
	
	if( !array_key_exists($month, $months) ) {
		$temp = array_keys($months);
		$month = $temp[0];
	}

	// Read posts
	$post_contents = @file_get_contents($cache_path . $year . '-' . $month . '.dat');
	if( $post_contents === false ) $post_contents = '';
	
	$posts = unserialize($post_contents);
	if( $posts === false ) {
		echo "${settings['id']}|<p class='${settings['error_class']}'>Could not open cache file '$year-$month.dat'</p>";
		die();
	}
	
	if( $settings['newest_first'] == 0 ) {
		$posts = array_reverse($posts, true);
	}
		
	// Generate list of years
	$year_list = '';
	foreach( $years as $y => $p ) {
		$current = '';
		$current_text = '';
		if( $y == $year ) {
			$current = ' class="'.$settings['selected_class'].'"';
			$current_text = $settings['selected_text'] == '' ? '' : ' ' . $settings['selected_text'];
		}
		
		if( $settings['num_entries'] == 1 ) {
			$num = ' ' . str_replace('%', $p[0], $settings['number_text']);
		}
		
		$year_list .= <<<END_TEXT
<li id="${settings['id']}-year-$y"$current>$y$num$current_text</li>

END_TEXT;
	}
	$year_list = <<<END_LIST
<ul id="${settings['id']}-year"$fade_year>
$year_list</ul>
END_LIST;
	
	// Generate list of months
	$month_names = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	
	$month_list = '';
	foreach( $months as $m => $p ) {
		$current = '';
		$current_text = '';
		if( $m == $month ) {
			$current = ' class="' . $settings['selected_class'] . '"';
			$current_text = $settings['selected_text'] == '' ? '' : ' ' . $settings['selected_text'];
		}
		
		if( $settings['num_entries'] == 1 ) {
			$num = ' ' . str_replace('%', $p[0], $settings['number_text']);
		}
		
		$n = $month_names[$m];
		$month_list .= <<<END_TEXT
<li id="${settings['id']}-month-$m"$current>$n$num$current_text</li>

END_TEXT;
	}
	$month_list = <<<END_LIST
<ul id="${settings['id']}-month"$fade_month>
$month_list</ul>
END_LIST;
	
	// Generate list of posts
	$post_list = '';
	foreach( $posts as $d => $p ) {
		if( $settings['num_comments'] == 1 ) {
			if( $p[4] == 'closed' ) {
				$cmt_text = ' ' . str_replace('%', $p[3], $settings['closed_comment_text']);
			} else {
				$cmt_text = ' ' . str_replace('%', $p[3], $settings['comment_text']);
			}
		}
		
		if( $settings['day_format'] == '' ) {
			$day = '';
		} else {
			$day = date($settings['day_format'], strtotime("$year-$month-${p[0]}")) . ' ';
		}

		// truncate titles
		$title = $p[1];
		if( $settings['truncate_title_length'] > 0 ) {
			if( strlen($title) > $settings['truncate_title_length'] ) {
				$title = substr($title, 0, $settings['truncate_title_length']);
				if( $settings['truncate_title_at_space'] == 1 ) {
					$pos = strrpos($title, ' ');
					if( $pos !== false ) {
							$title = substr($title, 0, $pos);
					}
				}
				$title .= $settings['truncate_title_text'];
			}
		}
		
		$post_list .= <<<END_TEXT
<li id='${settings['id']}-post-${p[0]}'>$day<a href='${p[2]}'>$title</a>$cmt_text</li>

END_TEXT;
	}
	$post_list = <<<END_LIST
<ul id="${settings['id']}-post"$fade_post>
$post_list</ul>
END_LIST;
	
	$text = $year_list . $month_list . $post_list;

	echo $settings['id'] . '|';
	echo $text;
?>