<?php
/*
Plugin Name: Jerome's Keywords
Plugin URI: http://vapourtrails.ca/wp-keywords
Version: 1.9
Description: Allows keywords to be associated with each post.  These keywords can be used for page meta tags, included in posts for site searching or linked like Technorati tags.
Author: Jerome Lavigne
Author URI: http://vapourtrails.ca
*/

/*	Copyright 2005  Jerome Lavigne  (email : darkcanuck@vapourtrails.ca)
	
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

/* Credits:
	Special thanks also to Dave Metzener, Mark Eckenrode, Dan Taarin, N. Godbout, "theanomaly", "oso", Wayne @ AcmeTech, Will Luke,
	Gyu-In Lee, Denis de Bernardy and the many others who have provided feedback, spotted bugs, and suggested improvements.
    WP2.0 compatibility fixes suggested by Horst Gutmann.
*/

/* ChangeLog:

29-Dec-2005:  Version 1.9
        - Added support for WordPress 2.0 (which unnecessarily broke things)

23-Jun-2005:  Version 1.8
		- Fixed bug in get_the_post_keytag() that did not return a valid category link.

20-Jun-2005:  Version 1.7
		- Fixed uksort bug that appeared on the edit page when there are no keywords in the database.
		- Fixed divide-by-zero error in all_keywords() scaling routine.
		- Local keyword search now includes pages
		- Added ability to create flickr- and del.icio.us-safe keyword links for use in cosmos/top-X keywords lists.

9-May-2005:  Version 1.6
		- Tag cosmos now uses a natural case-insensitive sort.
		- Added a very simple keyword suggestion feature when editing posts (suggestions refresh after every save)

16-Apr-2005:  Version 1.5
		- Added functions all_keywords() and get_all_keywords() for creating a "tag cosmos".
		- Added functions top_keywords() and get_top_keywords() to return a "Top X Tags" list.
		- Fixed slashes bug in get_the_search_keytag().
		- Fixed get_the_keywords() routine:
			- no longer lists duplicate keywords
			- only relevant categories are output, except on the home page where all are shown (can be overridden with parameters)
		- Added filter for adding keywords to pages.
		- Added fix for conflicting JOINs on wp_postmeta (or how to make friends with other plugins) and removed mini-posts "fix"

13-Mar-2005:  Version 1.4
		- Added ability to automatically generate .htaccess rewrite rules for keyword searches.
			- Can be turned off with new KEYWORDS_REWRITERULES flag.
			- Necessary for sites that use /index.php/*%blah% style permalinks
			- Thanks to Dave Metzener for finding the original bug and beta testing the fix.
		- Added formatting parameters to the_keywords() and get_the_keywords() to allow more control over the output.
		- Fixed XHTML validation bug:  added a space prior to title attribute in keyword links.
		- Fixed keyword link encoding for links that include '/' (now left as-is rather than encoded)
		- Temporary fix to prevent conflicts with mini-posts plugin:  removes mini-post's filters when a tag search is performed.

1-Mar-2005:  Version 1.3
		- Added ability to do site keyword searches.  This now the default keyword link behaviour.
		- Keyword search can also use its own template file.
		- If including categories, local links will return that category (not the keyword search).
		- Added filter for Atom feed content if not sending rss summaries only.

27-Feb-2005:  Version 1.2
		- Fixed search URL for sites not using permalinks (this is automatically detected)
		- If not using permalinks then the Atom feed will contain Technorati links instead of local search link (local search can't be parsed by Technorati)

26-Feb-2005:  Version 1.1
		- added ability to suppress link title if value passed is an empty string (used for Atom feed)
		- updated keywords_appendtags() to suppress link title.

25-Feb-2005:  Version 1.0 publicly released

*/

/* *****INSTRUCTIONS*****

Entering Keywords - simply type all of your keywords into the keywords field when creating/editing posts.  Keywords
				should be separated by commas and can include spaces (key phrases).

Template Tags - you can use the following php template tags to insert the keywords into your template

	the_keywords() - can be used outside the loop
			Outputs a comma-separated list of all categories & keywords on the current page.  You can use this
			to add a keyword meta tag to your page's title block:
				<meta name="keywords" content="<?php the_keywords(); ?>" />

			This function can take three optional parameters:
				before (default = blank) - text/html to insert before each keyword
				after (default = blank) - text/html to insert after each keyword
				separator (default = ",") - text/html to insert between keywords
			get_the_keywords() is a non-echoing version

	the_post_keywords() - must be used inside the loop
			Outputs a comma-separated list of the keywords for the current post.  This function can take one optional parameters:
				include_cats (default=false) - if true, post categories are included in the list.
			get_the_post_keywords() is a non-echoing version.

	the_post_keytags() - must be used inside the loop
			Outputs the keywords for the current post as a series of links.  By default these link a query for other posts with matching
			keywords (can also link to the WordPress search function or to Technorati's page for that tag)
			This function can take three optional parameters:
				include_cats (default=false) - if true, post categories are included in the list.
				local_search (default="tag") - if false or "technorati", the links will be to Technorati's tag page for the keyword instead,
										if "search", the links will be to the local Wordpress search function.
				link_title (default="") - alternate link title text to use, e.g. "My link title for" (tag name will be added at the end)
				
			An example from my site:
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				 [...]
				<div class="post">
					 [...]
					<div class="subtags">
			>>>			Tags: <?php the_post_keytags(); ?>
					</div>
					 [...]
				<?php endwhile; else: ?>
			
			get_the_post_keytags() is a non-echoing version.

	is_keyword() - can be used outside the loop
			Returns true if the current view is a keyword/tag search

	the_search_keytag() - can be used outside the loop
			Outputs the keyword/tag used for the search
			get_the_search_keytag() is a non-echoing version


Rewrite Rules - The plugin can generate new tag search rewrite rules automatically.  You need to 
			re-save your permalinks settings (Options -> Permalinks) for this to occur.
			If your .htaccess file cannot be written to by WordPress, add the following to your 
			.htaccess file to use the tag search feature, preferably below the "# END WordPress" line:

RewriteRule ^tag/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$ /index.php?tag=$1&feed=$2 [QSA,L]
RewriteRule ^tag/(.+)/(feed|rdf|rss|rss2|atom)/?$ /index.php?tag=$1&feed=$2 [QSA,L]
RewriteRule ^tag/(.+)/page/?([0-9]{1,})/?$ /index.php?tag=$1&paged=$2 [QSA,L]
RewriteRule ^tag/(.+)/?$ /index.php?tag=$1 [QSA,L]

*/


/* You can change these constants if you wish for further customization*/
define('KEYWORDS_META', 'keywords');							// post meta key used in the wp database
define('KEYWORDS_TECHNORATI', 'http://technorati.com/tag');		// Technorati link to use if local search is false
define('KEYWORDS_ATOMTAGSON', '1');								// flag to add tags to Atom feed (required for Technorati)
define('KEYWORDS_QUERYVAR', 'tag');								// get/post variable name for querying tag/keyword from WP
define('KEYWORDS_TAGURL', 'tag');								// URL to use when querying tags
define('KEYWORDS_TEMPLATE', 'keywords.php');					// template file to use for displaying tag queries
define('KEYWORDS_SEARCHURL', 'search');							// local search URL (from mod_rewrite rules)
define('KEYWORDS_REWRITERULES', '1');							// flag to determine if plugin can change WP rewrite rules
define('KEYWORDS_SUGGESTED', '8');								// maximum number of keywords suggested

/* WP 2.0 doesn't initialize the rewrite object before plugins are loaded anymore
    so these constants are set later
*/
function keywords_init() {
    global $wp_rewrite;
    
    /* Shouldn't need to change this - can set to 0 if you want to force permalinks off */
    if (isset($wp_rewrite) && $wp_rewrite->using_permalinks()) {
        define('KEYWORDS_REWRITEON', '1');							// nice permalinks, yes please!
        define('KEYWORDS_LINKBASE', $wp_rewrite->root);				// set to "index.php/" if using that style
    } else {
        define('KEYWORDS_REWRITEON', '0');							// old school links
        define('KEYWORDS_LINKBASE', '');							// don't need this
    }
    
    /* generate rewrite rules for above queries */
    if (KEYWORDS_REWRITEON && KEYWORDS_REWRITERULES)
        add_filter('search_rewrite_rules', 'keywords_createRewriteRules');
}
add_action('init','keywords_init');

/* use in the loop*/
function get_the_post_keywords($include_cats=true) {
	$keywords = '';

	if ($include_cats) {
		$categories = get_the_category();
		foreach($categories as $category) {
			if (!empty($keywords))
				$keywords .= ",";
			$keywords .= $category->cat_name;
		}
	}	

	$post_keywords = get_post_custom_values(KEYWORDS_META);
	if (is_array($post_keywords)) {
		foreach($post_keywords as $post_keys) {
			if (!empty($post_keys))
				$keywords .= ",";
			$keywords .= $post_keys;
		}
	}
	return( $keywords );
}

/* use in the loop*/
function the_post_keywords($include_cats=true) {
	echo get_the_post_keywords($include_cats);
}

/* use in the loop*/
function get_the_post_keytags($include_cats=false, $localsearch="tag", $linktitle=false) {
	// determine link mode
	$linkmode = strtolower(trim($localsearch));
	switch ($linkmode) {
		case '':
		case 'technorati':
			$linkmode = 'technorati';
			break;
		case 'search':
			$linkmode = 'search';
			break;
		//case 'tag':
		//case 'keyword':
		default:
			$linkmode = 'tag';
			break;
	}

	$output = "";
	if ($linktitle === false)
		$linktitle = ($linkmode == 'technorati') ? "Technorati tag page for" : "Search site for";
	
	// do categories separately to get category links instead of tag links
	if ($include_cats) {
		$categories = get_the_category();
		foreach($categories as $category) {
			$keyword = $category->cat_name;
			if ($linkmode == 'technorati')
				$taglink = KEYWORDS_TECHNORATI . "/" . jkeywords_localLink($keyword);
			else
				$taglink = get_category_link($category->cat_ID);
			$tagtitle = empty($linktitle) ? "" : " title=\"$linktitle $keyword\"";
			
			if (!empty($output))
				$output .= ", ";
			$output .= "<a href=\"$taglink\" rel=\"tag\"$tagtitle>$keyword</a>";
		}
	}	
	
	$post_keywords = get_post_custom_values(KEYWORDS_META);
	if (is_array($post_keywords)) {
		$keywordlist = array();
		foreach($post_keywords as $post_keys)
			$keywordlist = array_merge($keywordlist, explode(",", $post_keys));
		
		foreach($keywordlist as $keyword) {
			$keyword = trim($keyword);
			if (!empty($keyword)) {
				switch ($linkmode) {
					case 'tag':
						if (KEYWORDS_REWRITEON)
							$taglink = get_settings('home') . '/' . KEYWORDS_LINKBASE . KEYWORDS_TAGURL . 
										'/' . jkeywords_localLink($keyword);
						else
							$taglink = get_settings('home') . "/?" . KEYWORDS_TAGURL .  "=" . urlencode($keyword);
						break;
					case 'technorati':
						$taglink = KEYWORDS_TECHNORATI . "/" . jkeywords_localLink($keyword);
						break;
					case 'search':
						if (KEYWORDS_REWRITEON)
							$taglink = get_settings('home') . '/' . KEYWORDS_LINKBASE . KEYWORDS_SEARCHURL . 
										'/' . jkeywords_localLink($keyword);
						else
							$taglink = get_settings('home') . '/?s=' . urlencode($keyword) . '&submit=Search';
						break;
				}
				$tagtitle = empty($linktitle) ? "" : " title=\"$linktitle $keyword\"";
				
				if (!empty($output))
					$output .= ", ";
				$output .= "<a href=\"$taglink\" rel=\"tag\"$tagtitle>$keyword</a>";
			}
		}
	}
	return($output);
}

/* use in the loop*/
function the_post_keytags($include_cats=false, $localsearch=true, $linktitle=false) {
	$taglist = get_the_post_keytags($include_cats, $localsearch, $linktitle);
	
	if (empty($taglist))
		echo "none";
	else
		echo $taglist;
}

/* works outside the loop*/
function get_the_keywords($before='', $after='', $separator=',', $include_cats='default') {
	global $cache_categories, $category_cache, $post_meta_cache;
	
	$keywords = "";
	
	if ($include_cats) {
		if ( isset($cache_categories) && ( ($include_cats == 'all') ||
		                                  (($include_cats == 'default') && is_home()) ) ) {
			foreach($cache_categories as $category)
				$keywordarray[$category->cat_name] += 1;
		} elseif (isset($category_cache)) {
			foreach($category_cache as $post_category) {
				foreach($post_category as $category)
					$keywordarray[$category->cat_name] += 1;
			}
		}
	}
	
	if (isset($post_meta_cache)) {
		foreach($post_meta_cache as $post_meta) {
			if (is_array($post_meta[KEYWORDS_META])) {
				foreach($post_meta[KEYWORDS_META] as $post_keys) {
					$keywordlist = explode(",", $post_keys);
					foreach($keywordlist as $keyvalue)
						if (!empty($keyvalue))
							$keywordarray[$keyvalue] += 1;
				}
			}
		}
	}

	if (is_array($keywordarray)) {
		foreach($keywordarray as $key => $count) {
			if (!empty($keywords))
				$keywords .= $separator;
			$keywords .= $before . $key . $after;
		}
	}
	
	return ($keywords);
}

/* works outside the loop */
function the_keywords($before='', $after='', $separator=',') {
	echo get_the_keywords($before, $after, $separator);
}

function is_keyword() {
    global $wp_version;
    $keyword = ( isset($wp_version) && ($wp_version >= 2.0) ) ? 
                get_query_var(KEYWORDS_QUERYVAR) : 
                $GLOBALS[KEYWORDS_QUERYVAR];
	if (!is_null($keyword) && ($keyword != ''))
		return true;
	else
		return false;
}

function get_the_search_keytag() {
    $keyword = ( isset($wp_version) && ($wp_version >= 2.0) ) ? 
                get_query_var(KEYWORDS_QUERYVAR) : 
                $GLOBALS[KEYWORDS_QUERYVAR];
	$searchtag = stripslashes($keyword);
	return(get_magic_quotes_gpc() ? stripslashes($searchtag) : $searchtag);
}

function the_search_keytag() {
	echo get_the_search_keytag();
}


/***** Tag cosmos functions *****/
function get_all_keywords($include_cats = false) {
	global $wpdb, $cache_categories;
	
	if ($include_cats && isset($cache_categories)) {
		$catkeys = $wpdb->get_results("SELECT p2c.category_id AS cat_id, COUNT(p2c.rel_id) AS cat_count
										FROM  $wpdb->post2cat p2c, $wpdb->posts posts
										WHERE posts.ID = p2c.post_id
										  AND posts.post_status IN('publish', 'static')
										GROUP BY p2c.category_id");
		if (is_array($catkeys)) {
			foreach($catkeys as $category)
				$keywordarray[ $cache_categories[$category->cat_id]->cat_name . 
								'::Category::' . $category->cat_id ] += $category->cat_count;
		}
	}

	$metakeys = $wpdb->get_results("SELECT meta.meta_id, meta.meta_value
									FROM  $wpdb->posts posts, $wpdb->postmeta meta
									WHERE posts.ID = meta.post_id
									  AND posts.post_status IN('publish', 'static')
									  AND meta.meta_key = '" . KEYWORDS_META . "'");
	if (is_array($metakeys)) {
		foreach($metakeys as $post_meta) {
			if (!empty($post_meta->meta_value)) {
				$post_keys = explode(',', $post_meta->meta_value);
				
				foreach($post_keys as $keyword) {
					$keyword = trim($keyword);
					if (!empty($keyword))
						$keywordarray[ $keyword ] += 1;
				}
			}
		}
	}
    
    if(is_array($keywordarray))
        uksort($keywordarray, 'strnatcasecmp');
	
	return($keywordarray);
}

function jkeywords_localLink($keyword) {
    return str_replace('%2F', '/', urlencode($keyword));
}

function jkeywords_flickrLink($keyword) {
    return urlencode(preg_replace('/[^a-zA-Z0-9]/', '', strtolower($keyword)));
}

function jkeywords_deliciousLink($keyword) {
    $del = preg_replace('/\s/', '', $keyword);
    if (strstr($del, '+'))
        $del = '"' . $del . '"';
    return str_replace('%2F', '/', rawurlencode($del));
}

function all_keywords($element = '<li class="cosmos keyword%count%"><a href="/tag/%keylink%">%keyword%</a></li>',
                      $element_cat = '', $min_scale = 1, $max_scale = false, $min_include = 0) {
	
	$include_cats = !empty($element_cat);
	
	$allkeys = get_all_keywords($include_cats);
	
	$keywords = '';
	if (is_array($allkeys)) {
		
		// scaling
		if ($max_scale !== false) {
			$pre_scale = min($allkeys);
			$pre_scale = ($pre_scale < $min_include) ? $min_include : $pre_scale;
			$spread = (max($allkeys) - $pre_scale);
            $spread = ($spread > 0 ? $spread : 1);
            $scale_factor = ($max_scale - $min_scale) / $spread;
		}
	
		foreach($allkeys as $key => $count) {
			if ($count >= $min_include) {
				if ($max_scale !== false)
					$keycount = (int) (($count - $pre_scale) * $scale_factor + $min_scale);
				else
					$keycount = $count + $min_scale - 1;
				
				// need to do category stuff first so that we can decide between $element and $element_cat at the outset
				if ($include_cats && (strstr($key, '::Category::'))) {
					$keycat = explode('::Category::', $key);
					$key = $keycat[0];
					$keytemp = str_replace('%keylink%', get_category_link((int)$keycat[1]), $element_cat);
					$keytemp = str_replace('%flickr%', get_category_link((int)$keycat[1]), $keytemp);
					$keytemp = str_replace('%delicious%', get_category_link((int)$keycat[1]), $keytemp);
				} else {
					$keytemp = str_replace('%keylink%', jkeywords_localLink($key), $element);
					$keytemp = str_replace('%flickr%', jkeywords_flickrLink($key), $keytemp);
					$keytemp = str_replace('%delicious%', jkeywords_deliciousLink($key), $keytemp);
                }
				$keytemp = str_replace('%count%', $keycount, $keytemp);
				if (strstr($keytemp, '%em%')) {
					$keytemp = str_replace('%em%', str_repeat('<em>', $keycount), $keytemp);
					$keytemp = str_replace('%/em%', str_repeat('</em>', $keycount), $keytemp);
				}
				$keytemp = str_replace('%keyword%', str_replace(' ', '&nbsp;', $key), $keytemp);
				$keywords .= $keytemp . ' ';
			}
		}
	}
	echo $keywords;
}


/***** Top keywords/tags functions *****/
function get_top_keywords($number = false, $include_cats = false, $min_include = 0) {

	$allkeys = get_all_keywords($include_cats);

	$topkeys = array();
	if (is_array($allkeys)) {
	
		arsort($allkeys);
		if (($number <= 0) && ($min_include <= 1))
			return($allkeys);
		
		$topcount = 0;
		foreach ($allkeys as $key => $count) {
			if ($count >= $min_include) {
				$topkeys[$key] = $count;
				$topcount++;
			}
			if (($number > 0) && ($topcount >= $number))
				break;
		}
	}
	return($topkeys);
}

function top_keywords($number = false, $element='<li><a href="/tag/%keylink%">%keyword%</a></li>',
                      $element_cat = '', $min_include = 0) {
	
	$include_cats = !empty($element_cat);
	
	$topkeys = get_top_keywords($number, $include_cats, $min_include);
	
	$keywords = '';
	if (is_array($topkeys)) {
		foreach($topkeys as $key => $count) {
			// need to do category stuff first so that we can decide between $element and $element_cat at the outset
			if ($include_cats && (strstr($key, '::Category::'))) {
				$keycat = explode('::Category::', $key);
				$key = $keycat[0];
				$keytemp = str_replace('%keylink%', get_category_link((int)$keycat[1]), $element_cat);
				$keytemp = str_replace('%flickr%', get_category_link((int)$keycat[1]), $keytemp);
				$keytemp = str_replace('%delicious%', get_category_link((int)$keycat[1]), $keytemp);
			} else {
				$keytemp = str_replace('%keylink%', jkeywords_localLink($key), $element);
				$keytemp = str_replace('%flickr%', jkeywords_flickrLink($key), $keytemp);
				$keytemp = str_replace('%delicious%', jkeywords_deliciousLink($key), $keytemp);
			}
            
			$keytemp = str_replace('%count%', $count, $keytemp);
			if (strstr($keytemp, '%em%')) {
				$keytemp = str_replace('%em%', str_repeat('<em>', $keycount), $keytemp);
				$keytemp = str_replace('%/em%', str_repeat('</em>', $keycount), $keytemp);
			}
			$keytemp = str_replace('%keyword%', str_replace(' ', '&nbsp;', $key), $keytemp);
			$keywords .= $keytemp . ' ';
		}
	}
	echo $keywords;
}


/***** Add actions *****/

/* editing */
add_filter('simple_edit_form', 'keywords_edit_form');
add_filter('edit_form_advanced', 'keywords_edit_form');
add_filter('edit_page_form', 'keywords_edit_form');
add_action('edit_post', 'keywords_update');
add_action('publish_post', 'keywords_update');
add_action('save_post', 'keywords_update');

/* for keyword/tag queries */
add_filter('query_vars', 'keywords_addQueryVar');
add_action('parse_query', 'keywords_parseQuery');

/* Atom feed */
if (KEYWORDS_ATOMTAGSON) {
	add_filter('the_excerpt_rss', 'keywords_appendTags');
	if (!get_settings('rss_use_excerpt'))
		add_filter('the_content', 'keywords_appendTags');
}

/***** Callback functions *****/
function keywords_edit_form() {
	global $post, $postdata, $content;

	$id = isset($post) ? $post->ID : $postdata->ID;
    $post_keywords = get_post_meta($id, KEYWORDS_META, true);

	echo "
		<fieldset id=\"postkeywords\">
			<legend>Keywords</legend>
			<div>
                <textarea rows=\"1\" cols=\"40\" name=\"keywords_list\" tabindex=\"4\" id=\"keywords_list\" style=\"margin-left: 1%; width: 97%; height: 1.8em;\">$post_keywords</textarea>
        ";
    
    if (KEYWORDS_SUGGESTED > 0) {
        $top_keywords = get_top_keywords();
        
        $suggested = array();
        
        foreach($top_keywords as $keyword=>$keycount) {
            if (stristr($content, $keyword)) {
                $suggested[] = $keyword;
                if (count($suggested) >= KEYWORDS_SUGGESTED)
                    break;
            }
        }
        if (count($suggested) < KEYWORDS_SUGGESTED) {
            foreach($top_keywords as $keyword=>$keycount) {
                if (!in_array($keyword, $suggested)) {
                    $suggested[] = $keyword;
                    if (count($suggested) >= KEYWORDS_SUGGESTED)
                        break;
                }
            }
        }
        $suggested_keys = implode(', ', $suggested);
        echo "		<br /> <div style=\"font-size: 80%; margin-left: 1%;\">Suggested Keywords: <em>$suggested_keys</em> </div>";
    }
    
	echo "
			</div>
		</fieldset>
		";
}

function keywords_update($id) {

	// remove old value
	delete_post_meta($id, KEYWORDS_META);

	// clean up keywords list & save
	$keyword_list = "";
	$post_keywords = explode(",", $_REQUEST['keywords_list']);
	foreach($post_keywords as $keyword) {
		if ( !empty($keyword ) ) {
			if ( !empty($keyword_list) )
				$keyword_list .= ",";
			$keyword_list .= trim($keyword);
		}
	}

	if (!empty($keyword_list) )
		add_post_meta($id, KEYWORDS_META, $keyword_list);
}

function keywords_appendTags(&$text) {
	global $doing_rss, $feed;
	
	if ( (!$doing_rss) || ($feed != 'atom') )
		return($text);
	
	$local = KEYWORDS_REWRITEON ? "tag" : "technorati";
	
	$taglist = get_the_post_keytags(true, $local, "");
	if (empty($taglist))
		return($text);
	else
		return($text . " \n Tags: " . $taglist);
}

function keywords_addQueryVar($wpvar_array) {
	$wpvar_array[] = KEYWORDS_QUERYVAR;
	return($wpvar_array);
}

function keywords_parseQuery() {
	// if this is a keyword query, then reset other is_x flags and add query filters
	if (is_keyword()) {
		global $wp_query;
		$wp_query->is_single = false;
		$wp_query->is_page = false;
		$wp_query->is_archive = false;
		$wp_query->is_search = false;
		$wp_query->is_home = false;
		
		add_filter('posts_where', 'keywords_postsWhere');
		add_filter('posts_join', 'keywords_postsJoin');
		add_action('template_redirect', 'keywords_includeTemplate');
	}
}

function keywords_postsWhere($where) {
    global $wp_version;
    $keyword = ( isset($wp_version) && ($wp_version >= 2.0) ) ? 
                get_query_var(KEYWORDS_QUERYVAR) : 
                $GLOBALS[KEYWORDS_QUERYVAR];

    $where .= " AND jkeywords_meta.meta_key = '" . KEYWORDS_META . "' ";
	$where .= " AND jkeywords_meta.meta_value LIKE '%" . $keyword . "%' ";

    // include pages in search (from jeromes-search.php)
    $where = str_replace(' AND (post_status = "publish"', ' AND ((post_status = \'static\' OR post_status = \'publish\')', $where);
    
	return ($where);
}

function keywords_postsJoin($join) {
	global $wpdb;
	$join .= " LEFT JOIN $wpdb->postmeta AS jkeywords_meta ON ($wpdb->posts.ID = jkeywords_meta.post_id) ";
	return ($join);
}

function keywords_includeTemplate() {

	if (is_keyword()) {
		$template = '';
		
		if ( file_exists(TEMPLATEPATH . "/" . KEYWORDS_TEMPLATE) )
			$template = TEMPLATEPATH . "/" . KEYWORDS_TEMPLATE;
		else if ( file_exists(TEMPLATEPATH . "/tags.php") )
			$template = TEMPLATEPATH . "/tags.php";
		else
			$template = get_category_template();
		
		if ($template) {
			load_template($template);
			exit;
		}
	}
	return;
}

function keywords_createRewriteRules($rewrite) {
	global $wp_rewrite;
	
	// add rewrite tokens
	$keytag_token = '%' . KEYWORDS_QUERYVAR . '%';
	$wp_rewrite->add_rewrite_tag($keytag_token, '(.+)', KEYWORDS_QUERYVAR . '=');
    
	$keywords_structure = $wp_rewrite->root . KEYWORDS_QUERYVAR . "/$keytag_token";
	$keywords_rewrite = $wp_rewrite->generate_rewrite_rules($keywords_structure);
	
	return ( $rewrite + $keywords_rewrite );
}

?>