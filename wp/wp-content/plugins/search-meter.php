<?php
/*
Plugin Name: Search Meter
Plugin URI: http://www.thunderguy.com/semicolon/wordpress/search-meter-wordpress-plugin/
Description: Keeps track of what your visitors are searching for. After you have activated this plugin, you can check the <a href="index.php?page=search-meter.php">Search Meter Statistics</a> page to see what your visitors are searching for on your blog.
Version: 1.1
Author: Bennett McElwee
Author URI: http://www.thunderguy.com/semicolon/

INSTRUCTIONS

1. Copy this file into the plugins directory in your WordPress installation (wp-content/plugins).
2. Log in to WordPress administration. Go to the Plugins page and Activate this plugin.

To see search statistics, log in to WordPress Admin, go to the Dashboard page and click Search Meter.
To control search statistics, log in to WordPress Admin, go to the Options page and click Search Meter.

TEMPLATE TAGS

sm_list_popular_searches()

	Show a list of the search terms that have produced hits at your site during the last 30 days.
	Readers can click the search term to repeat the search.

sm_list_popular_searches('<h2>Popular Searches</h2>')

	Show the list as above, with the heading "popular Searches". If there have been no searches,
	then this tag displays nothing.

sm_list_popular_searches('<li><h2>Popular Searches</h2>', '</li>')

	Show the headed list as above; this form of the tag should be used in the default WordPress theme.
	Put it in the sidebar.php file.


Thanks to Kaufman (http://www.terrik.com/wordpress/) for valuable coding suggestions.

Copyright (C) 2005 Bennett McElwee (bennett at thunderguy dotcom)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
The license is also available at http://www.gnu.org/copyleft/gpl.html
*/ 

// Template Tags

function sm_list_popular_searches($before = '', $after = '') {
	global $wpdb, $table_prefix;
	/*	This is a simpler query that the report query, and may produce
		slightly different results. This query returns searches if they
		have ever had any hits, even if the last search yielded no hits.
		This makes for a more efficient search -- important if this
		function will be used in a sidebar.
	*/
	$results = $wpdb->get_results(
		"SELECT `terms`,
			SUM( `count` ) AS countsum
		FROM `{$table_prefix}searchmeter`
		WHERE DATE_SUB( CURDATE( ) , INTERVAL 30 DAY ) <= `date`
		AND 0 < `last_hits`
		GROUP BY `terms`
		ORDER BY countsum DESC, `terms` ASC
		LIMIT 5");
	if (count($results)) {
		echo "$before\n<ul>\n";
		foreach ($results as $result) {
			echo '<li><a href="'. get_settings('home') . '/search/' . urlencode($result->terms) . '">'. htmlspecialchars($result->terms) .'</a></li>'."\n";
		}
		echo "</ul>\n$after\n";
	}
}

/*	Use a "the_posts" filter to check if it's a search, and to count
	the number of hits. The filter doesn't make any changes.
*/
function tguy_sm_save_search(&$posts) {
	global $wp_query, $table_prefix;
	if ($wp_query->is_search && ! $wp_query->is_admin) {
		$search_string = $wp_query->query_vars['s'];
		if (!get_magic_quotes_gpc()) {
			$search_string = addslashes($search_string);
		}
		$search_string = preg_replace('/, +/', ' ', $search_string);
		$search_string = str_replace(',', ' ', $search_string);
		$search_string = str_replace('"', ' ', $search_string);
		$search_string = trim($search_string);

		$hit_count = count($posts);

		// Save them into the DB. Usually this will be a new query, so try to insert first
		$query = "INSERT INTO `{$table_prefix}searchmeter` (`terms`,`date`,`count`,`last_hits`)
		VALUES ('$search_string',CURDATE(),1,$hit_count)";
		$success = mysql_query($query);
		if (!$success) {
			$query = "UPDATE `{$table_prefix}searchmeter` SET
				`count` = `count` + 1,
				`last_hits` = $hit_count
			WHERE `terms` = '$search_string' AND `date` = CURDATE()";
			$success = mysql_query($query);
		}
	}
	return $posts;
}

function tguy_sm_init() {
// Create the table if it's not already there
	global $table_prefix;
	if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
		$result = mysql_list_tables(DB_NAME);
		$tables = array();
		while ($row = mysql_fetch_row($result)) {
			$tables[] = $row[0];
		}
		if (!in_array($table_prefix.'searchmeter', $tables)) {
			tguy_sm_install();
		}
	}
}

function tguy_sm_install() {
	global $wpdb, $table_prefix;
	$result = mysql_query("
		CREATE TABLE `{$table_prefix}searchmeter`
		(
			`terms` VARCHAR(50) NOT NULL,
			`date` DATE NOT NULL,
			`count` INT(11) NOT NULL,
			`last_hits` INT(11) NOT NULL,
			PRIMARY KEY (`terms`,`date`)
		)
	") or die(mysql_error());
	
	if (!$result) {
		return false;
	}
	return true;		
}

function tguy_sm_stats_css() {
?>
<style type="text/css">
div.sm-stats-table {
	float: left;
	padding-right: 5em;
	padding-bottom: 3ex;
}
div.sm-stats-table h3 {
	margin-top: 0;
}
div.sm-stats-table .left {
	text-align: left;
}
div.sm-stats-table .right {
	text-align: right;
}
div.sm-stats-clear {
	clear: both;
}
</style>
<?php
}

function tguy_sm_add_admin_pages() {
	add_submenu_page('index.php', 'Search Meter Statistics', 'Search Meter', 1, __FILE__, 'tguy_sm_stats_page');
	add_options_page('Search Meter', 'Search Meter', 10, __FILE__, 'tguy_sm_options_page');
}

function tguy_sm_stats_table($results, $days, $do_include_successes = false) {
	global $wpdb, $table_prefix;
	/*	Explanation of the query:
	We group by terms, because we want all rows for a term to be combined.
	For the search count, we simply SUM the count of all searches for the term.
	For the hits, we only want the number of hits for the latest search. Each row
	contains the hits for the latest search on that row's date. So for each date,
	CONCAT the date with the number of hits, and take the MAX. This gives us the
	latest date combined with its hit count. Then strip off the date with SUBSTRING.
	This Rube Goldberg-esque procedure should work in older MySQL versions that
	don't allow subqueries. It's inefficient, but that doesn't matter since it's
	only used in admin pages and the tables involved won't be too big.
	*/
	$hits_selector = $do_include_successes ? '' : 'HAVING hits = 0';
	$results = $wpdb->get_results(
		"SELECT `terms`,
			SUM( `count` ) AS countsum,
			SUBSTRING( MAX( CONCAT( `date` , ' ', `last_hits` ) ) , 12 ) AS hits
		FROM `{$table_prefix}searchmeter`
		WHERE DATE_SUB( CURDATE( ) , INTERVAL $days DAY ) <= `date`
		GROUP BY `terms`
		$hits_selector
		ORDER BY countsum DESC, `terms` ASC
		LIMIT 20");
	if (count($results)) {
		?>
		<table cellpadding="3" cellspacing="2">
		<tbody>
		<tr class="alternate"><th class="left">Term</th><th>Searches</th>
		<?php
		if ($do_include_successes) {
			?><th>Hits</th><?php
		}
		?></tr><?php
		$class= '';
		foreach ($results as $result) {
			?>
			<tr class="<?php echo $class ?>">
			<td><?php echo htmlspecialchars($result->terms) ?></td>
			<td class="right"><?php echo $result->countsum ?></td>
			<?php
			if ($do_include_successes) {
				?>
				<td class="right"><?php echo $result->hits ?></td></tr>
				<?php
			}
			$class = ($class == '' ? 'alternate' : '');
		}
		?>
		</tbody>
		</table>
		<?php
	} else {
		?><p>No searches in this period.</p><?php
	}
}

function tguy_sm_stats_page() {
	global $wpdb, $table_prefix;
	// Delete old records
	$result = $wpdb->query(
	"DELETE FROM `{$table_prefix}searchmeter`
	WHERE `date` < DATE_SUB( CURDATE() , INTERVAL 30 DAY)");
	echo "<!-- Search Meter: deleted $result old rows -->\n";
	?>
	<div class="wrap">

		<h2>All searches</h2>

		<p>These tables show the most popular searches on your blog for the given time periods. <strong>Term</strong> is the text that was searched for; <strong>Searches</strong> is the number of times the term was searched for; and <strong>Hits</strong> is the number of posts that were returned from the <em>last</em> search for that term.</p>

		<div class="sm-stats-table">
		<h3>Yesterday and today</h3>
		<?php tguy_sm_stats_table($results, 1, true); 	?>
		</div>
		<div class="sm-stats-table">
		<h3>Last 7 days</h3>
		<?php tguy_sm_stats_table($results, 7, true); ?>
		</div>
		<div class="sm-stats-table">
		<h3>Last 30 days</h3>
		<?php tguy_sm_stats_table($results, 30, true); ?>
		</div>
		<div class="sm-stats-clear"></div>

		<h2>Unsuccessful searches</h2>

		<p>These tables show only the search terms for which the last search yielded no results. People are searching your blog for these terms; maybe you should give them what they want.</p>

		<div class="sm-stats-table">
		<h3>Yesterday and today</h3>
		<?php tguy_sm_stats_table($results, 1, false); ?>
		</div>
		<div class="sm-stats-table">
		<h3>Last 7 days</h3>
		<?php tguy_sm_stats_table($results, 7,false); 	?>
		</div>
		<div class="sm-stats-table">
		<h3>Last 30 days</h3>
		<?php tguy_sm_stats_table($results, 30, false); ?>
		</div>
		<div class="sm-stats-clear"></div>

		<h2>Notes</h2>

		<p>To reset your search statistics, go to your <a href="<?php bloginfo('wpurl'); ?>/wp-admin/options-general.php?page=search-meter.php">Search Meter Options page</a>.</p>

		<p>For information and updates, see the <a href="http://www.thunderguy.com/semicolon/wordpress/search-meter-wordpress-plugin/">Search Meter home page</a>. At that page, you can also offer suggestions, request new features or report problems.</p>

	</div>
	<?php
}

function tguy_sm_options_page() {
	global $wpdb, $table_prefix;
	?>
	<div class="wrap">

		<h2>Reset statistics</h2>

		<p>Click this button to reset all search statistics. This will delete all information about previuos searches.</p>

		<form name="tguy_sm_admin" action="<?php bloginfo('wpurl'); ?>/wp-content/plugins/search-meter.php" method="post">
			<p>
			<input type="submit" name="tguy_sm_reset" value="Reset Statistics" onclick="return confirm('You are about to reset your search statistics.\n  \'Cancel\' to stop, \'OK\' to delete.');" />
			</p>
		</form>

		<h2>Notes</h2>

		<p>To see your search statistics, go to your <a href="<?php bloginfo('wpurl'); ?>/wp-admin/index.php?page=search-meter.php">Search Meter Statistics page</a>.</p>

		<p>For information and updates, see the <a href="http://www.thunderguy.com/semicolon/wordpress/search-meter-wordpress-plugin/">Search Meter home page</a>. At that page, you can also offer suggestions, request new features or report problems.</p>

	</div>
	<?php
}

// Check to see if we need to carry out a command
if (!isset($wpdb)) {
	require('../../wp-blog-header.php');
}
if (!empty($_POST['tguy_sm_reset'])) {
	global $wpdb, $table_prefix;
	// Delete all records
	$result = $wpdb->query("DELETE FROM `{$table_prefix}searchmeter`");
	header('Location: '.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=search-meter.php&updated=true');
	exit();
}

if (function_exists('add_action') && function_exists('add_filter')) {
	add_action('init', 'tguy_sm_init');
	add_filter('the_posts', 'tguy_sm_save_search', 20); // run after other plugins
	add_action('admin_head', 'tguy_sm_stats_css');
	add_action('admin_menu', 'tguy_sm_add_admin_pages');
}