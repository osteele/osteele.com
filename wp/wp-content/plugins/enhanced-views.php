<?php
/*
Plugin Name: Enhanced Post List
Plugin URI: http://www.coldforged.org/
Description: Replaces the standard post listing with a much more functional listing, including browsing by category and author as well as sorting by various columns.
Version: 0.4
Author: Brian "ColdForged" Dupuis
Author URI: http://www.coldforged.org
*/

/*
Enhanced Post List plugin for WordPress
Copyright (C) 2005  Brian "ColdForged" Dupuis

Parts of this code adapted from WordPress 1.5 Strayhorn.

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
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

if( !function_exists( 'EV_construct_post_url' ) ) {

	function EV_construct_post_url( $s, $m, $category, $offset, $perpage, $author, $orderby ) {
		$base_uri = explode('?', $_SERVER['REQUEST_URI']);
		$base_uri = $base_uri[0];
		$order_array = explode( " ", $orderby );
		return "$base_uri?".( !empty($offset) ? "paged=$offset":'').( !empty($category) ? "&cat=$category":'').( !empty($m) ? "&m=$m":'').( !empty($s) ? "&s=$s":'').( !empty($perpage) ? "&showpostsoverride=$perpage":'').( !empty($author) ? "&author=$author":'').( !empty($order_array[0]) ? "&orderby=".$order_array[0]:'').( !empty($order_array[1]) ? "&order=".$order_array[1]:'');
	}
}

// Many people ask me why I don't put comments describing each of these functions.
// I couldn't tell you exactly why, but it's probably got something to do with
// the fact that I write my plugins for me first, and I don't care whether 
// anyone else understands what I'm doing. In Real Life I'm a prolific 
// commenter ;-).
if( !function_exists( 'EV_dropdown_authors' ) ) {
	function EV_dropdown_authors( $currentauthor ){
		global $wpdb;

		$users = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE user_level > 0 ORDER BY user_nickname");
		$return_string = '';
		foreach( $users as $user ) {
			$return_string .= '<option value="'.$user->ID.'"'.( $currentauthor == $user->ID ? ' selected="selected"':'').'>'.$user->user_nickname.'</option>';
		}
		return $return_string;
	}
}

if( !function_exists( 'EV_order_string' ) ) {
	function EV_order_string( $currentorder, $orderby ) {
		$order_array = explode( " ", $currentorder );
		if( $order_array[0] == $orderby ) {
			return $orderby . " " . ( "ASC" == $order_array[1] ? "DESC" : "ASC" );
		} else {
			return $orderby . " DESC";
		}
	}
}

if( !function_exists( 'EV_order_image' ) ) {
	function EV_order_image( $currentorder, $orderby ) {
		$order_array = explode( " ", $currentorder );
		if( $order_array[0] == $orderby ) {
			return ( "ASC" == $order_array[1] ? 
				"<img alt=\"ASC\" src=\"".get_settings('siteurl')."/wp-content/enhanced-views/asc.png\" />" : 
				"<img alt=\"DSC\" src=\"".get_settings('siteurl')."/wp-content/enhanced-views/dsc.png\" />" );
		} else {
			return "";
		}
	}
}

if( !function_exists( 'EV_dropdown_cats' ) ) {
	function EV_dropdown_cats($currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0) {
		global $wpdb;
		$currentstr = '';
		if (!$categories) {
			$categories = $wpdb->get_results("SELECT * FROM $wpdb->categories ORDER BY cat_name");
		}
		if ($categories) {
			foreach ($categories as $category) { 
				if ($parent == $category->category_parent) {
					$pad = str_repeat('&#8211; ', $level);
					$category->cat_name = addslashes($category->cat_name);
					$currentstr .= '\n\t<option value="'.$category->cat_ID.'"';
					if ($currentcat == $category->cat_ID)
						$currentstr .= ' selected="selected"';
					$currentstr .= ">$pad$category->cat_name</option>";
					$currentstr .= EV_dropdown_cats($currentcat, $currentparent, $category->cat_ID, $level + 1, $categories, $currentstr);
				} 
			} 
			return $currentstr;
		} else {
			return $currentstr;
		}
	}
}

if( !function_exists( 'EV_posts_determine_page' ) ) {
	function EV_posts_determine_page() {
		if( ( preg_match('#\/edit.php#i', $_SERVER['REQUEST_URI']) ) && !isset($_GET['page']) ) {
			EV_posts_display();
		}
	}	
}	

if( !function_exists( 'EV_posts_modify_query' ) ) {
	function EV_posts_modify_query($query) {
		// ONLY do this if we're in the admin page. Otherwise things tend
		// to get squirrely.
		if( ( preg_match('/edit.php/i', $_SERVER['REQUEST_URI']) ) && !isset($_GET['page']) ) {
			if( isset( $query->query_vars['showpostsoverride'] ) ) {
				// We have a bit of work to do to undo some of the things
				// that the edit.php page does. First, get rid of the hard-coded
				// 15 posts.
				$query->query = EV_url_remove_parameter( $query->query, 'showposts' );
				if( isset( $query->query_vars['showposts'] ) ) {
					unset( $query->query_vars['showposts'] );
				}
				
				// Our override variable is a bit tricky. We can't set showposts because
				// edit.php does and overwrites the value. We can't set posts_per_page
				// because it will be overwritten by the admin.php file. So, create a new
				// variable and set it, then substitute our value for the showposts value.
				$query->query_vars['showposts'] = $query->query_vars['showpostsoverride'];
				$query->query_vars['nopaging'] = false;
				$query->is_archive = $query->is_search = false;
			}

			// Just in case, get rid of any trailing ampersands to make the 
			// query string pretty.
			$query->query = rtrim( $query->query, '&' );

		} else {
			return $text;
		}
	}
}

if( !function_exists( 'EV_posts_modify_query_vars' ) ) {
	// We need to add a variable to the allowable list of query variables.
	// Luckily WordPress allows us to do this. See the previous function for
	// why we need an override.
	function EV_posts_modify_query_vars($query_vars) {
		if( ( preg_match('/edit.php/i', $_SERVER['REQUEST_URI']) ) && !isset($_GET['page']) ) {
			return array_merge( $query_vars, array('showpostsoverride') );
		} else {
			return $query_vars;
		}
	}
}

if( !function_exists( 'EV_url_remove_parameter' ) ) {
	function EV_url_remove_parameter($pURL, $pParameter){
		return preg_replace("/(&|\?)?" . $pParameter . "=(.+?)(&|$)/", "\\1", $pURL);
	}
}

if( !function_exists( 'EV_posts_display' ) ) {
	function EV_posts_display() {
		require_once( dirname(dirname(dirname(__FILE__))).'/wp-config.php');
		global $wpdb, $user_ID, $month, $post, $wp_query;

		// Load up needed variables early.
		$cur_page = $wp_query->get('paged');
		if( empty( $cur_page ) ) {
			$cur_page = 1;
		}
		$m = $wp_query->get('m');
		$posts_per_page = $wp_query->get('posts_per_page');
		$cat = $wp_query->get('cat');
		$author = $wp_query->get('author');
		$s = $wp_query->get('s');
		$orderby = $wp_query->get('orderby');

		// Fetch the used query string. 
		$query_string = $wp_query->query;

		// Modify the query string to return all rows, regardless of paging 
		// and turn off ordering to speed things up.
		$query_string = EV_url_remove_parameter($query_string, 'paged');
		$query_string = EV_url_remove_parameter($query_string, 'posts_per_page');
		$query_string = EV_url_remove_parameter($query_string, 'showpostsoverride');
		$query_string = EV_url_remove_parameter($query_string, 'showposts');
		$query_string = EV_url_remove_parameter($query_string, 'what_to_show');
		$query_string = EV_url_remove_parameter($query_string, 'orderby');
		$query_string = EV_url_remove_parameter($query_string, 'order');
		$query_string = ( empty( $query_string ) ? "posts_per_page=-1" : $query_string . "&posts_per_page=-1");
		$my_query = new WP_Query();
		$posts = $my_query->query( $query_string );
		$num_posts = count( $posts );

		// Set up the header string.
		$no_output_posted = true;
		$header_string = '';
		if ( !empty( $s ) ) {
			$header_string .= sprintf(__('Search for &#8220;%s&#8221;'), $s);
			$no_output_posted = false;
		} 
		if ( !empty($m) ) {
			if( !$no_output_posted )
				$header_string .= " from ";
			else
				$header_string .= "Posts from ";
			$header_string .= $month[substr( $m, 4, 2 )] . ' ' . substr( $m, 0, 4 );
			$no_output_posted = false;
		} 
		if ( !empty($cat) ) {
			if( !$no_output_posted )
				$header_string .= " within ";
			else
				$header_string .= "Posts within ";
			$header_string .= sprintf(__('the &#8220;%s&#8221; category'), get_the_category_by_ID($cat) );			
			$no_output_posted = false;
		} 
		if( !empty($author) ) {
			if( !$no_output_posted )
				$header_string .= " by ";
			else
				$header_string .= "Posts by ";
			$user_data = get_userdata($author);
			$header_string .= $user_data->user_nickname;
			$no_output_posted = false;
		}
		if( $no_output_posted ) {
			$header_string .= __("All Posts");
		}
?>
		<script language="JavaScript" type="text/javascript">
		// Change the header to what we want.
		var headerElement = document.getElementsByTagName('h2')[0];
		headerElement.innerHTML = "<?php echo $header_string;?>";
<?php	unset( $header_string ); ?>


			// Create the hidden elements that symantically change what we're doing
			// with the "search" and "month" elements.
<?php
		if( !empty( $m ) ) {   
?>		
			var hiddenMonth = document.createElement('input');
			hiddenMonth.type = "hidden";
			hiddenMonth.name = "m";
			hiddenMonth.value = "<?php echo $m?>";
			var searchElement = document.getElementsByName("searchform")[0];
			searchElement.appendChild(hiddenMonth);
<?php 	
		}
?>
<?php
		if( !empty( $s ) ) {   
?>		
			var hiddenSearch = document.createElement('input');
			hiddenSearch.type = "hidden";
			hiddenSearch.name = "s";
			hiddenSearch.value = "<?php echo $s?>";
			var monthElement = document.getElementsByName("viewarc")[0];
			monthElement.appendChild(hiddenSearch);

<?php 	
		}
?>

<?php
		if( !empty( $cat ) ) {   
?>		
			var hiddenCat = document.createElement('input');
			hiddenCat.type = "hidden";
			hiddenCat.name = "cat";
			hiddenCat.value = "<?php echo $cat?>";
			var monthElement = document.getElementsByName("viewarc")[0];
			monthElement.appendChild(hiddenCat);
			var catClone = hiddenCat.cloneNode(false);
			var searchElement = document.getElementsByName("searchform")[0];
			searchElement.appendChild(catClone);
<?php 	
		}
?>

<?php
		if( !empty( $author ) ) {   
?>		
			var hiddenAuthor = document.createElement('input');
			hiddenAuthor.type = "hidden";
			hiddenAuthor.name = "author";
			hiddenAuthor.value = "<?php echo $author?>";
			var monthElement = document.getElementsByName("viewarc")[0];
			monthElement.appendChild(hiddenAuthor);
			var authorClone = hiddenAuthor.cloneNode(false);
			var searchElement = document.getElementsByName("searchform")[0];
			searchElement.appendChild(authorClone);
<?php 	
		}
?>

			// Now add the non-optional hidden elements to those forms.
			var hiddenPaged = document.createElement('input');
			hiddenPaged.type = "hidden";
			hiddenPaged.name = "showpostsoverride";
			hiddenPaged.value = "<?php echo $posts_per_page?>";
			var monthElement = document.getElementsByName("viewarc")[0];
			var searchElement = document.getElementsByName("searchform")[0];
			searchElement.appendChild(hiddenPaged);

			// We'll apply the right style to the search element while we have it here.
			searchElement.setAttribute('style','float: left; width: 16em; margin-right: 1em; margin-bottom: 1em;');
			var hiddenClone = hiddenPaged.cloneNode(false);
			monthElement.appendChild(hiddenClone);
			monthElement.setAttribute('style','float: left; width: 20em;');

			// Add an "All Months" option to the "Archives" menu, otherwise the
			// symantics are a little goofy.
			var allMonths = document.createElement('option');
			allMonths.value = "0";
<?php if( empty($m) ) { ?>
	        allMonths.selected = "selected";
<?php } ?>
			allMonths.appendChild(document.createTextNode('All Months'));
			var monthSelect = document.getElementsByTagName('select');
			var i;
			for( i=0; i < monthSelect.length; i++ )
			{
				if( monthSelect[i].name == "m" ) {
					monthSelect[i].insertBefore( allMonths, monthSelect[i].firstChild );
				}
			}
<?php
			// Let's work on the category dropdown.
			$cat_dropdown = 
				'	<input type="hidden" name="paged" value="'.$cur_page.'" />'.
				'	<input type="hidden" name="showpostsoverride" value="'.$posts_per_page.'" />'.
				'	<input type="hidden" name="author" value="'.$author.'" />'.
				( !empty($s) ? '	<input type="hidden" name="s" value="'.$s.'" />':'' ) .
				( !empty($m) ? '	<input type="hidden" name="m" value="'.$m.'" />':'' ) .
				( !empty($author) ? '	<input type="hidden" name="author" value="'.$author.'" />':'' ) .
				( !empty($orderby) ? '	<input type="hidden" name="orderby" value="'.$orderby.'" />':'').
				'	<fieldset>'.
				'		<legend>'.__('Browse By Category&hellip;').'</legend>'.
				'		<select name="cat">'.
				'			<option value="0"'.( $cat == 0 ? ' selected="selected"':'').'>'.__('All Categories').'</option>'.
				EV_dropdown_cats( $cat ) .
				'		</select>'.
				'		<input type="submit" name="submit" value="'.__('Show Category').'"  />'.
				'	</fieldset>';
?>
			// Get the category list in.
			var theTable = document.getElementsByTagName('table')[0];
			var catList = document.createElement('form');
			catList.name= 'viewcat';
			catList.action= '';
			catList.method= 'get';
			catList.setAttribute('style','clear: both; float: left; width: 22em; margin-bottom: 1em; margin-right: 1em;');
			catList.innerHTML = '<?php echo $cat_dropdown;?>';
			monthElement.parentNode.insertBefore( catList, monthElement );

<?php		unset( $cat_dropdown );

			// Create the author dropdown.
			$author_dropdown = 
				'	<input type="hidden" name="paged" value="'.$cur_page.'" />'.
				'	<input type="hidden" name="showpostsoverride" value="'.$posts_per_page.'" />'.
				( !empty($cat) ? '	<input type="hidden" name="cat" value="'.$cat.'" />':'' ) .
				( !empty($s) ? '	<input type="hidden" name="s" value="'.$s.'" />':'' ) .
				( !empty($m) ? '	<input type="hidden" name="m" value="'.$m.'" />':'' ) .
				( !empty($orderby) ? '	<input type="hidden" name="orderby" value="'.$orderby.'" />':'').
				'	<fieldset>'.
				'		<legend>'.__('Browse By Author&hellip;').'</legend>'.
				'		<select name="author">'.
				'			<option value="0"'.( !empty($author) ? ' selected="selected"':'').'>'.__('All Authors').'</option>'.
				EV_dropdown_authors( $author ) .
				'       </select>'.
				'		<input type="submit" name="submit" value="'.__('Select Author').'" />'.
				'	</fieldset>';
?>
			// Turn on the author list.
			var authorList = document.createElement('form');
			authorList.name= 'viewauthor';
			authorList.action= '';
			authorList.method= 'get';
			authorList.setAttribute('style','float: left; width: 20em; margin-bottom: 1em; margin-right: 1em;');
			authorList.innerHTML = '<?php echo $author_dropdown;?>';
			monthElement.parentNode.insertBefore( authorList, monthElement );

<?php		unset( $author_dropdown );
			if ($my_query->have_posts()) {
				global $id;

				// paging code inspired by the lovely and talented Scripty Goddess
				$pages = ceil( $num_posts / $posts_per_page );
				$page_string = '';
				if( ($cur_page) > $pages ) {
					$cur_page = $pages;
					$page_string = "<p>Current page exceeds number of pages for this query. <a href=\"".EV_construct_post_url($s,$m,$cat,$cur_page-1,$posts_per_page,$author,$orderby)."\">Refresh</a> to view posts.</p>";
				}
				$page_string .= '<p><form name="posts_per_page" action="" method="get">';
				if($pages > 1)
				{
					$lowest_displayed = $cur_page - 3;
					$highest_displayed = $cur_page + 3;

					$page_string .= __('Page: ');

					if( $cur_page > 1 ) {
						$page_string .= ' (<a href="'.EV_construct_post_url($s,$m,$cat,$cur_page-1,$posts_per_page,$author,$orderby).'">&laquo; '.__('Previous').'</a> | ';
					} else {
						$page_string .= ' (&laquo; '.__('Previous').' | ';
					}
					if( $cur_page != $pages ) {
						$page_string .= '<a href="'.EV_construct_post_url($s,$m,$cat,$cur_page+1,$posts_per_page,$author,$orderby).'">'.__('Next').' &raquo;</a>)';
					} else {
						$page_string .= __('Next &raquo;');
					}
					$page_string .= '&nbsp;&nbsp;';

					for($i=1; $i <= $pages; $i++)
					{
						if( ($i == 1) || ( $i == $pages ) || ( $i > $lowest_displayed && $i < $highest_displayed ) )
						{
							if( $i != 1 )
								$page_string .= '|';
							if( $i == $cur_page )
								$page_string .= "<strong>$i</strong>"; 
							else
								$page_string .= '<a href="'.EV_construct_post_url($s,$m,$cat,$i,$posts_per_page,$author,$orderby).'">'.$i.'</a>';
							$ellipsis_inserted = false;
						}
						else
						{
							if( !$ellipsis_inserted )
							{
								$page_string .= '|...';
								$ellipsis_inserted = true;
							}
						}
					}
					$page_string .= '&nbsp;&nbsp;&nbsp;';
				}

				$page_string .= 
					'<input type="submit" name="view" value="'.__('Show:').'"  />'.
					'<input type="text" name="showpostsoverride" value="'.$posts_per_page.'" size="3" />'.
					'<input type="hidden" name="paged" value="'.$cur_page.'" />'.
					( !empty($orderby) ? '<input type="hidden" name="orderby" value="'.$orderby.'" />':'').
					( !empty($s) ? '<input type="hidden" name="s" value="'.$s.'" />':'').
					( !empty($m) ? '<input type="hidden" name="m" value="'.$m.'" />':'').
					( !empty($author) ? '	<input type="hidden" name="author" value="'.$author.'" />':'' ) .
					( !empty($cat) ? '	<input type="hidden" name="cat" value="'.$cat.'" />':'' ) .
					__('posts per page.').'</p>'.
					'</form>';
				$page_string .= '</p>';
			}
?>
			// Let's insert the paging elements.
			var pagethemanDiv = document.createElement('div');
			var pagethemanDiv2 = document.createElement('div');
			pagethemanDiv2.setAttribute('style','clear: both;');
			pagethemanDiv.innerHTML ='<?php echo $page_string; ?>';
			pagethemanDiv2.innerHTML = pagethemanDiv.innerHTML;
			theTable.style.clear='both';
			theTable.parentNode.appendChild(pagethemanDiv);
			theTable.parentNode.insertBefore(pagethemanDiv2,theTable);


<?php		unset( $page_string );

			// Set up the new column headers.
			$column_headers = 
				'<th scope="col">ID</th>' .	   
				'<th scope="col"><a href="'.EV_construct_post_url($s,$m,$cat,$cur_page,$posts_per_page,$author,EV_order_string($orderby,"date")).'">When</a>'.EV_order_image($orderby,"date").'</th>' .
				'<th scope="col"><a href="'.EV_construct_post_url($s,$m,$cat,$cur_page,$posts_per_page,$author,EV_order_string($orderby,"title")).'">Title</a>'.EV_order_image($orderby,"title").'</th>' .
				'<th scope="col">Categories</th>' .
				'<th scope="col">Comments</th>' .
				'<th scope="col"><a href="'.EV_construct_post_url($s,$m,$cat,$cur_page,$posts_per_page,$author,EV_order_string($orderby,"author")).'">Author</a>'.EV_order_image($orderby,"author").'</th>' .
				'<th scope="col"></th>' .
				'<th scope="col"></th>' .
				'<th scope="col"></th>';
?>
			// Now change the column headers to be links changing the "order by" feature.
			// Start with ID.
			var theRow = document.getElementsByTagName('tr')[0];
			theRow.innerHTML = '<?php echo $column_headers ?>';

		</script>
<?php
	}
}		


	add_action('admin_footer', 'EV_posts_determine_page'); 
	add_action('parse_query', 'EV_posts_modify_query');
	add_action('query_vars', 'EV_posts_modify_query_vars');
?>
