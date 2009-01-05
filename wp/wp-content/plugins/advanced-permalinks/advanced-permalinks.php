<?php
/*
Plugin Name: Advanced Permalinks
Plugin URI: http://urbangiraffe.com/plugins/advanced-permalinks/
Description: Allows multiple permalink structures, migration of permalinks without redirections, permalinks for posts in specific categories, and categories without a base
Author: John Godley
Version: 0.1.13
Author URI: http://urbangiraffe.com/
============================================================================================================
0.1.2  - First release
0.1.3  - Fix javascript errors
0.1.4  - Fix problem with file upload
0.1.5  - Add option to allow periods in URLs
0.1.6  - Preliminary support for .html in category base in WP 2.3
0.1.7  - Add 301 redirects
0.1.8  - Stop redirect loop
0.1.9  - Fix WP 2.3.1 problem, fix problem with 404s on category specific URLs, add Bulgarian translation
0.1.10 - Add debug page
0.1.11 - Fix missing 404 on category specific URLs on some hosts
0.1.12 - Update base plugin to fix path problem
0.1.13 - WP 2.5 fixes
============================================================================================================
This software is provided "as is" and any express or implied warranties, including, but not limited to, the
implied warranties of merchantibility and fitness for a particular purpose are disclaimed. In no event shall
the copyright owner or contributors be liable for any direct, indirect, incidental, special, exemplary, or
consequential damages (including, but not limited to, procurement of substitute goods or services; loss of
use, data, or profits; or business interruption) however caused and on any theory of liability, whether in
contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.

For full license details see license.txt
============================================================================================================ */

include (dirname (__FILE__).'/plugin.php');

class Advanced_Permalinks extends Advanced_Permalinks_Plugin
{
	var $category;
	var $category_cache;
	var $cache_hit = false;
	
	/**
	 * Constructor.  Setup all the juicy filter and action hooks
	 *
	 * @return void
	 **/
	
	function Advanced_Permalinks ()
	{
		$this->register_plugin ('advanced-permalinks', __FILE__);
		
		if (is_admin ())
	  {
			$this->add_action ('edit_category_form', 'edit_category_form', 1);
			$this->add_action ('edit_category');
			$this->add_action ('admin_head');
			
			// Update the rules when we install/remove the plugin
			$this->add_action ('activate_advanced-permalinks/advanced-permalinks.php', 'flush_rules');
			$this->add_action ('deactivate_advanced-permalinks/advanced-permalinks.php', 'flush_rules');
	  }
		else
			$this->add_filter ('query_vars');                   // Add our 'redirect' parameter

		// Insert our code into the rewrite rules
		$this->add_filter ('post_rewrite_rules');
		$this->add_filter ('author_rewrite_rules');
		$this->add_filter ('category_rewrite_rules');
		$this->add_filter ('rewrite_rules_array');
			
		$this->add_filter ('post_link', 'post_link', 1, 2);   // Override post links to insert our own code
		$this->add_filter ('the_posts');                      // Redirect any old URLs
		$this->add_action ('parse_request');                  // Redirect old permalinks
		$this->add_action ('init');                           // Update rewrite object with new structures
		$this->add_action ('shutdown');
	}
	
	function is_25 ()
	{
		global $wp_version;
		if (version_compare ('2.5', $wp_version) <= 0)
			return true;
		return false;
	}
	
	function shutdown ()
	{
		if ($this->cache_hit)
			update_option ('advanced_permalinks_cache', $this->category_cache);
	}
	
	function version ()
	{
		return '1';
	}
	
	/**
	 * If we're on the permalink options page then hook into the menu display
	 *
	 * @return void
	 **/
	
	function admin_head ()
	{
		if (strpos ($_SERVER['REQUEST_URI'], 'options-permalink.php'))
		{
			$this->render_admin ('head');
			$this->add_action ('admin_notices');
		}
	}


	/**
	 * Catch the init action and modify the rewrite object so all the permalink structures are as required
	 *
	 * @return void
	 **/
	
	function init ()
	{
		global $wp_rewrite;

		$settings = $this->get_options ();
		if (isset ($settings['permalinks']) && count ($settings['permalinks']) > 0)
		{
			foreach ($settings['permalinks'] AS $field => $value)
			{
				if ($field)
					$wp_rewrite->$field = str_replace ('//', '/', $wp_rewrite->front.$value);
			}
		}
		
		if ($settings['periods'])
		{
			remove_filter ('sanitize_title', 'sanitize_title_with_dashes');
			
			$this->add_filter ('sanitize_title');
		}
		
		$this->category_cache = get_option ('advanced_permalinks_cache');
	}
	
	
	
	/**
	 * We've hooked into the menu display on the permalink page so now show the submenu and take over the rest of the page if
	 * it's one of ours
	 *
	 * @return void
	 **/
	
	function admin_notices ()
	{
		$this->render_admin ('submenu', array ('url' => 'options-permalink.php', 'sub' => isset ($_GET['sub']) ? $_GET['sub'] : ''));
		
		// If we're on a sub-page then show ours instead
		if (isset ($_GET['sub']))
		{
			if ($_GET['sub'] == 'advanced')
				$this->admin_advanced ();
			else if ($_GET['sub'] == 'post')
				$this->admin_post ();
			else if ($_GET['sub'] == 'migrate')
				$this->admin_migrate ();
			else if ($_GET['sub'] == 'debug')
				$this->admin_debug ();

			// Show footer and stop running
			require (ABSPATH.'wp-admin/admin-footer.php');
			die ();
		}
	}
	
	function admin_debug ()
	{
		$this->render_admin ('debug', array ('rewrite' => get_option ('rewrite_rules')));
	}

		
	/**
	 * Show the 'advanced' screen
	 *
	 * @return void
	 **/
	
	function admin_advanced ()
	{
		if (isset ($_POST['save']))
		{
			$options = $this->get_options ();
			$options['permalinks'] = array_map ('trim', $_POST['link']);
			$options['periods']    = isset ($_POST['periods']) ? true : false;
			$options['extra']      = $_POST['extra'];
			
			update_option ('advanced_permalinks_settings', $options);
			
			$this->flush_rules ();
			$this->render_message (__ ('Your advanced settings have been saved', 'advanced-permalinks'));
		}
		
		$this->render_admin ('advanced', array ('options' => $this->get_options ()));
	}
	

	/**
	 * Show the 'posts' screen
	 *
	 * @return void
	 **/
	
	function admin_post ()
	{
		if (isset ($_POST['add']))
		{
			$this->create_permalink (intval ($_POST['start']), intval ($_POST['end']), trim ($_POST['permalink']));
			$this->flush_rules ();
			$this->render_message ('Your permalink has been added');
		}
		
		$this->render_admin ('posts', array ('permalinks' => get_option ('advanced_permalinks_posts')));
	}
	
	
	
	function admin_migrate ()
	{
		if (isset ($_POST['add']))
		{
			$existing = get_option ('advanced_permalinks_migration');
			$existing[] = trim ($_POST['permalink']);
			update_option ('advanced_permalinks_migration', $existing);
			
			$this->flush_rules ();
			$this->render_message ('Your permalink has been added');
		}
		
		$this->render_admin ('migrate', array ('permalinks' => get_option ('advanced_permalinks_migration')));
	}
	
	
	/**
	 * Helper function to clean a permalink (taken from options-permalink.php)
	 *
	 * @param string $link Permalink to clean
	 * @return string Cleaned link
	 **/
	
	function clean_permalink ($link)
	{
		$link = trim ($link);
		if ($link)
			return preg_replace('#/+#', '/', '/' . $link);
		return $link;
	}
	
	
	/**
	 * Flush all rewrite rules
	 *
	 * @return void
	 **/
	
	function flush_rules ()
	{
		// Force a rewrite rule update
		global $wp_rewrite;
		$wp_rewrite->flush_rules ();
	}
	
	
	/**
	 * Returns the ID of the last post
	 *
	 * @return integer Last post ID
	 **/
	
	function last_post_id ()
	{
		global $wpdb;
		return $wpdb->get_var ("SELECT ID FROM {$wpdb->posts} ORDER BY ID DESC LIMIT 0,1");
	}
	
	
	/**
	 * Removes a permalink from the plugin
	 *
	 * @param integer $start The 'start' ID of the permalink to remove
	 * @return boolean
	 **/
	
	function remove_permalink ($start)
	{
		$existing = $this->get_post_permalinks ();
		if (isset ($existing[$start]))
		{
			unset ($existing[$start]);
			update_option ('advanced_permalinks_posts', $existing);
			return true;
		}
		return false;
	}
	
	
	/**
	 * Creates a permalink
	 *
	 * @param integer $start The 'start' ID of the permalink
	 * @param integer $end The 'end' ID of the permalink
	 * @param string $permalink The permalink
	 * @return boolean
	 **/
	
	function create_permalink ($start, $end, $permalink)
	{
		$existing = $this->get_post_permalinks ();

		// All posts
		if ($end == -1)
			$end = $this->last_post_id ();
		
		// Only this post
		if ($end == 0)	
			$end = $start;
		
		$existing[$start] = array ('end' => $end, 'link' => $this->clean_permalink ($permalink));
		ksort ($existing);
		
		update_option ('advanced_permalinks_posts', $existing);
	}
	
	
	/**
	 * Shows the special form on category edit pages
	 *
	 * @return void
	 **/
	
	function edit_category_form ($cat)
	{
		$category_links = $this->get_category_permalinks ();
		$links = '';
		if (isset ($category_links[$cat->cat_ID]))
			$links = $category_links[$cat->cat_ID];
				
		$this->render_admin ('edit', array ('permalink' => $links));
	}
	
	
	/**
	 * Saves category-specific permalinks
	 *
	 * @return void
	 **/
	
	function edit_category ($catid)
	{
		if (isset ($_POST['cat_ID']))
		{
			$cats = $this->get_category_permalinks ();
		
			$permalink = trim ($_POST['permalink']);
			if ($permalink)
				$cats[$catid] = $permalink;
			else if (isset ($cats[$catid]))
				unset ($cats[$catid]);
		
			// Save
			update_option ('advanced_permalinks_categories', $cats);
		
			$this->flush_rules ();
		}
	}
	
	
	/**
	 * Create category rewrite rules by deferring it until rewrite_rules_array
	 *
	 * @param array $rules Rules
	 * @return array Rules
	 **/

	function category_rewrite_rules ($rules)
	{
		// Do we have a special category permalink?
		$settings = $this->get_options ();
		if (isset ($settings['permalinks']) && isset ($settings['permalinks']['category_structure']))
		{
			global $wp_rewrite;

			// We need to add a redirect for old author links
			$old = new WP_Rewrite ();
			$oldrules = $wp_rewrite->generate_rewrite_rules ($old->get_category_permastruct ());

			foreach ($oldrules AS $key => $item)
				$rule[$key] = $item.'&redirect=true';
				
			$category = array_merge ($rule, $rules);
		}
		else
			$category = $rules;
			
		// If we have a category with no base then we defer the category permalinks, otherwise we return them now
		global $wp_rewrite;
		if ($wp_rewrite->category_base != '')
			return $category;

		$this->category = $category;
		return array ();
	}
	
	
	/**
	 * Create author rewrite rules
	 *
	 * @param array $rules Rules
	 * @return array Rules
	 **/
	
	function author_rewrite_rules ($rules)
	{
		// Do we have any author rewrite rule?
		$settings = $this->get_options ();
		if (isset ($settings['permalinks']) && isset ($settings['permalinks']['author_structure']))
		{
			global $wp_rewrite;
			
			// We need to add a redirect for old author links
			$old = new WP_Rewrite ();
			$oldrules = $wp_rewrite->generate_rewrite_rules ($old->get_author_permastruct ());
			foreach ($oldrules AS $key => $item)
				$rule[$key] = $item.'&redirect=true';

			$rules = array_merge ($rules, $rule);
		}
		
		return $rules;
	}
	
	
	/**
	 * Add our 'redirect' variable to the query variables
	 *
	 * @param array $vars Variables
	 * @return array Variables
	 **/
	
	function query_vars ($vars)
	{
		$vars[] = 'redirect';
		return $vars;
	}
	
	
	/**
	 * Hook into the 'parse_request' action and check if our rewrite rules require a redirection
	 *
	 * @param array $vars Variables
	 * @return array Variables
	 **/
	
	function parse_request ($vars)
	{
		// Have we triggered a redirect?
		if (isset ($vars->query_vars['redirect']))
		{
			if (isset ($vars->query_vars['author_name']))
				wp_redirect (get_author_posts_url (0, $vars->query_vars['author_name']));
			else if (isset ($vars->query_vars['category_name']))
				wp_redirect (get_category_link (get_category_by_path ($vars->query_vars['category_name'])));
			
			// Stop anything else
			die ();
		}

		// Workaround for WP 2.3
		global $wp_db_version;
		if ($wp_db_version > 6000 && isset ($vars->query_vars['category_name']))
		{
			$vars->query_vars['category_name'] = str_replace ('.html', '', $vars->query_vars['category_name']);
			$vars->matched_query               = str_replace ('.html', '', $vars->matched_query);
		}

		return $vars;
	}
	
	
	/**
	 * Add our deferrer category rules onto the end of the list, allowing a category with no base
	 *
	 * @param array $rules Rules
	 * @return array Rules
	 **/
	
	function rewrite_rules_array ($rules)
	{
		if (count ($this->category))
			$rules = array_merge ($rules, $this->category);
		return $rules;
	}
	
	
	/**
	 * Add all our post-specific rewrite rules into the main list
	 *
	 * @param array $rules Rules
	 * @return array Rules
	 **/
	
	function post_rewrite_rules ($rules)
	{
		global $wp_rewrite;

		// Add post-specific rules
		$specific = $this->get_post_permalinks ();
		if (count ($specific) > 0)
		{
			foreach ($specific as $start => $details)
				$rules = array_merge ($rules, $wp_rewrite->generate_rewrite_rules ($details['link']));
		}
		
		// Add category-specific rules
		$cats = $this->get_category_permalinks ();
		if (count ($cats) > 0)
		{
			foreach ($cats AS $id => $permalink)
				$rules = array_merge ($wp_rewrite->generate_rewrite_rules ($permalink), $rules);
		}
		
		// Add migration
		$migrate = get_option ('advanced_permalinks_migration');
		if ($migrate)
		{
			$newrules = array ();
			foreach ($migrate AS $permalink)
				$newrules = array_merge ($newrules, $wp_rewrite->generate_rewrite_rules ($permalink, false, false, false, true));
				
			update_option ('advanced_permalinks_migration_rule', $newrules);
			$rules = array_merge ($rules, $newrules);
		}

		// Add any extra rules
		$options = $this->get_options ();
		if (isset ($options['extra']) && $options['extra'])
		{
			$lines = explode ("\r\n", $options['extra']);
			if (count ($lines) > 0)
			{
				foreach ($lines AS $line)
				{
					$parts = explode (' = ', $line);
					if (count ($parts) == 2)
						$wp_rewrite->extra_rules_top[$parts[0]] = $parts[1];
				}
			}
		}

		return $rules;
	}
	
	
	/**
	 * Return the specific permalink structure for a post
	 *
	 * @param integer $postid Post ID
	 * @return mixed Permalink or false
	 **/
	
	function get_custom_permalink ($postid)
	{
		// Post-specific permalinks take precedence
		$specific = $this->get_post_permalinks ();
		if (count ($specific) > 0)
		{
			foreach ($specific as $start => $details)
			{
				if ($postid >= $start && $postid <= $details['end'])
					return $details['link'];
			}
		}
		
		// Finally try a category-specific permalink
		$category_links = $this->get_category_permalinks ();
		if (count ($category_links) > 0)
		{
			$category_id = $this->get_category ($postid);   // We assume the post's first category is the 'main' one

			if (isset ($category_links[$category_id]))
				return $category_links[$category_id];
		}
		
		return false;
	}
	
	
	/**
	 * Similar to get_custom_permalink, but returns the custom or normal permalink
	 *
	 * @param integer $postid Post ID
	 * @return mixed Permalink or false
	 **/

	function get_full_permalink ($postid)
	{
		$custom = $this->get_custom_permalink ($postid);
		if ($custom)
			return $custom;
		return get_option ('permalink_structure');
	}
	
	
	/**
	 * Returns the array of custom post permalinks
	 *
	 * @return array
	 **/
	
	function get_post_permalinks ()
	{
		$specific = get_option ('advanced_permalinks_posts');
		if ($specific === false)
			return array ();
		return $specific;
	}
	
	
	/**
	 * Returns the array of custom category permalinks
	 *
	 * @return array
	 **/
	
	function get_category_permalinks ()
	{
		$cats = get_option ('advanced_permalinks_categories');
		if ($cats === false)
			$cats = array ();
		return $cats;
	}
	
	
	/**
	 * Returns a single category ID for a post (assume first category)
	 *
	 * @return array
	 **/
	
	function get_category ($id)
	{
		if (isset ($this->category_cache[$id]))
			return $this->category_cache[$id];

		$cats = get_the_category ($id);
		$this->category_cache[$id] = $cats[0]->cat_ID;
		$this->cache_hit = true;

		if (function_exists ('_usort_terms_by_ID'))
			usort ($cats, '_usort_terms_by_ID');
		else if (function_exists ('_get_the_category_usort_by_ID'))
			usort($cats, '_get_the_category_usort_by_ID'); // order by ID
		return $cats[0]->cat_ID;
	}
	
	
	/**
	 * Hook that is called when a post URL is to be displayed.  We check if a custom permalink is needed and change the URL
	 *
	 * @param string $link
	 * @param object $post
	 * @return string URL
	 **/
	
	function post_link ($link, $post)
	{
		// Do we have a custom permalink structure for this category
		$permalink = $this->get_custom_permalink ($post->ID);
		if ($permalink)
			return $this->get_permalink ($post, $permalink);
		return $link;
	}
	
	
	/**
	 * A copy of the WordPress get_permalink function, but one tailored for custom permalinks
	 *
	 * @return string URL
	 **/
	
	function get_permalink ($post, $permalink)
	{
		$rewritecode = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			'%postname%',
			'%post_id%',
			'%category%',
			'%author%',
			'%pagename%'
		);

		if ( '' != $permalink && 'draft' != $post->post_status ) {
			$unixtime = strtotime($post->post_date);

			$category = '';
			if (strpos($permalink, '%category%') !== false) {
				$cats = get_the_category($post->ID);
				if ( $cats )
				{
					if (function_exists ('_usort_terms_by_ID'))
						usort ($cats, '_usort_terms_by_ID');
					else if (function_exists ('_get_the_category_usort_by_ID'))
						usort($cats, '_get_the_category_usort_by_ID'); // order by ID
				}
				$category = $cats[0]->category_nicename;
				if ( $parent=$cats[0]->category_parent )
					$category = get_category_parents($parent, FALSE, '/', TRUE) . $category;
			}

			$authordata = get_userdata($post->post_author);
			$author = $authordata->user_nicename;
			$date = explode(" ",date('Y m d H i s', $unixtime));
			$rewritereplace =
			array(
				$date[0],
				$date[1],
				$date[2],
				$date[3],
				$date[4],
				$date[5],
				$post->post_name,
				$post->ID,
				$category,
				$author,
				$post->post_name,
			);
			$permalink = get_option('home') . str_replace($rewritecode, $rewritereplace, $permalink);
			$permalink = user_trailingslashit($permalink, 'single');
			return $permalink;
		}
	}


	/**
	 * Hook that is called when a post is ready to be displayed.  We check if the permalink that generated this post is the
	 * correct one.  This prevents people accessing posts on other permalink structures.  A 301 is issued back to the original post
	 *
	 * @return void
	 **/
	
	function the_posts ($posts)
	{
		// Only validate the permalink on single-page posts
		if (is_single () && count ($posts) > 0)
		{
			global $wp, $wp_rewrite;

			$id = $posts[0]->ID;  // Single page => only one post

			// Is this a migrated rule?
			$migrate = get_option ('advanced_permalinks_migration_rule');
			if ($migrate)
			{
				if (isset ($migrate[$wp->matched_rule]) && substr (get_permalink ($id), strlen (get_bloginfo ('home'))) != $_SERVER['REQUEST_URI'])
				{
					wp_redirect (get_permalink ($id));
					die ();
				}
			}
			else
			{
				// Get the permalink for the post
				$permalink = $this->get_full_permalink ($id);

				// Generate rewrite rules for this permalink
				$rules = $wp_rewrite->generate_rewrite_rules ($permalink);
			
				// If the post's permalink structure is not in the rewrite rules then we redirect to the correct URL
				if ($wp->matched_rule && !isset ($rules[$wp->matched_rule]))
				{
					wp_redirect (get_permalink ($id));
					die ();
				}
			}
		}
		
		return $posts;
	}

	function get_options ()
	{
		$options = get_option ('advanced_permalinks_settings');
		if ($options === false)
			$options = array ();
			
		if (!isset ($options['periods']))
			$options['periods'] = true;
		return $options;
	}

	function sanitize_title ($title)
	{
		$title = strip_tags($title);
		// Preserve escaped octets.
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		// Remove percent signs that are not part of an octet.
		$title = str_replace('%', '', $title);
		// Restore octets.
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

		$title = remove_accents($title);
		if (seems_utf8($title)) {
			if (function_exists('mb_strtolower')) {
				$title = mb_strtolower($title, 'UTF-8');
			}
			$title = utf8_uri_encode($title);
		}

		$title = strtolower($title);
		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = preg_replace('/[^%a-z0-9\. _-]/', '', $title);
		$title = preg_replace('/\s+/', '-', $title);
		$title = preg_replace('|-+|', '-', $title);
		$title = trim($title, '-');

		return $title;
	}
	
	/**
	 * Singleton method
	 *
	 * @return void
	 **/
	
	function &get ()
	{
    static $instance;

    if (!isset ($instance))
		{
			$c = __CLASS__;
			$instance = new $c;
    }

    return $instance;
	}
}

// Start our singleton plugin
Advanced_Permalinks::get ();

?>
