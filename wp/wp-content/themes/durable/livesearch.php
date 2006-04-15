<?

require('../../../wp-blog-header.php');


	// live search by Andy Peatling
		$orig = $_GET['s'];
		
		if($orig == "")
		{
			echo "<p>Please enter a search criteria!</p>";
			die;
		}
		
		$_GET['s'] = addslashes_gpc($_GET['s']);
		$_GET['s'] = preg_replace('/, +/', ' ', $_GET['s']);
		$_GET['s'] = str_replace(',', ' ', $_GET['s']);
		$_GET['s'] = str_replace('"', ' ', $_GET['s']);
		$_GET['s'] = trim($_GET['s']);
	
		$n = '%';
		$search = ' AND ((post_title LIKE \''.$n.$_GET['s'].$n.'\') OR (post_content LIKE \''.$n.$_GET['s'].$n.'\') OR (post_excerpt LIKE \''.$n.$_GET['s'].$n.'\'))';

	$results = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_date < NOW() AND post_date != '0000-00-00 00:00:00' AND post_status = 'publish'" . $search . "LIMIT 10");
	
	if(empty($results))
	{
		echo "<h3>Search Results</h3>";
		echo "<p>Sorry there were no matches for '" . $orig . "'</p>";
	}
	else
	{
		echo "<h3>Search Results For '" . $orig . "'</h3>";
		
		$counter = 1;
		foreach($results as $result)
		{
			$comments = ($result->comment_count == 1) ? $result->comment_count . " Comment" : $result->comment_count . " Comments";
			
			if($counter == 1 || $counter == 6)
			{
				echo "<div class=\"column\">\n<ul>";
			}
			
			echo "<li><a href=\"" . get_permalink($result->ID) . "\" title=\"" . $result->post_title . "\">" . $result->post_title . "</a><br /><span class=\"date\">" . date("jS M", strtotime($result->post_date)) . " | " . $comments . "</span></li>";
			
			if($counter == 5)
			{
				echo "</ul>\n</div>";
			}
			
			$counter++;
		}
		
		if($counter != 5)
		{	
			echo "</ul>\n</div>\n";
		}
		
		echo "<div class=\"column\">\n";
		echo "<ul>\n";
		echo "<li>For more detailed results try a <a href=\"" . get_bloginfo('home') . "?s=" . $orig . "\" title=\"Detailed Search\">Regular Search &raquo;</a></li>\n";
		echo "</ul>\n";
		echo "</div>\n";
		echo "<hr />";
	}

?>