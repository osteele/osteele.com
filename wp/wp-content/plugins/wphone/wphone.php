<?php /*

**************************************************************************

Plugin Name:  WPhone
Plugin URI:   http://wphoneplugin.org/
Version:      1.5.3
Description:  A lightweight admin interface for the iPhone and other mobile devices.
Author:       <a href="http://tekartist.org/">Stephane Daury</a>, <a href="http://literalbarrage.org/blog/">Doug Stewart</a>, and <a href="http://www.viper007bond.com/">Viper007Bond</a>

**************************************************************************/

class WPhone {
	var $version;
	var $folder;
	var $whitelist = array();
	var $browsers = array();
	var $showui = FALSE;
	var $ismobile = FALSE;
	var $iscompat = FALSE;
	var $context;
	var $current_basename;
	var $site_url;
	var $blog_url;
	var $interface_url;
	var $interface_path;
	var $admin_url;
	var $compatregex;
	var $falsepositiveregex;
	var $forcetakeover = FALSE;
	var $obstartstatus = FALSE;
	var $menu = array();
	var $submenu = array();

	/**
	 * Ensures plugin is installed correctly and registers actions and filters
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function WPhone() {
		$this->version = '1.5.3';

		if ( !defined('PLUGINDIR') ) {
			add_action( 'admin_notices', array(&$this, 'WordPressTooOld') );
			return;
		}

		$this->folder = PLUGINDIR . '/wphone';

		// Make sure people install this plugin correctly by doing a couple simple checks
		if ( 'wphone' !== basename( dirname( __FILE__ ) ) || !file_exists( ABSPATH . $this->folder . '/includes/header.php' ) ) {
			add_action( 'admin_notices', array(&$this, 'IncorrectlyInstalled') );
			return;
		}

		// Load up the localization file if we're using WordPress in a different language
		// Just drop it in this plugin's "localization" folder and name it "wphone-[value in wp-config].mo"
		load_plugin_textdomain( 'wphone', $this->folder . '/localization' );

		// Set the cookie names as constants
		define( 'WPHONE_USEUI_COOKIE', 'wordpress_wphone_useui_' . COOKIEHASH );
		define( 'WPHONE_CHKBOX_COOKIE', 'wordpress_wphone_chkbox_' . COOKIEHASH );

		// Set some variables after running their values through filters
		$this->current_basename   = apply_filters( 'wphone_currentbasename', basename( $_SERVER['PHP_SELF'] ) );
		$this->compatregex        = apply_filters( 'wphone_compatregex', '/iphone|ipod|safari/i' );
		$this->falsepositiveregex = apply_filters( 'wphone_falsepositiveregex', '/symbian|nokia/i' );
		$this->site_url           = get_bloginfo( 'wpurl' );
		$this->blog_url           = get_option('home');
		$this->interface_url      = apply_filters( 'wphone_interfaceurl', $this->site_url . '/' . $this->folder . '/includes' );
		$this->admin_url          = apply_filters( 'wphone_adminurl', $this->site_url . '/wp-admin' );


		# Create the user agents lists. These are filterable as well.
		# Most of these browser names are thanks to Alex King via his GPL'ed "WordPress Mobile Edition" plugin

		// Set and sort some browsers that are never mobile browsers
		$this->whitelist = apply_filters( 'wphone_whitelistbrowsers', array(
			'Stand Alone/QNws',
		) );
		natcasesort( $this->whitelist );

		// Set and sort some browsers that we should consider mobile browsers
		$this->browsers = apply_filters( 'wphone_mobilebrowsers', array(
			'2.0 MMP',
			'240x320',
			'AvantGo',
			'BlackBerry',
			'Blazer',
			'Cellphone',
			'Danger',
			'DoCoMo',
			'Elaine/3.0',
			'EudoraWeb',
			'hiptop',
			'iPhone',
			'iPod',
			'MMEF20',
			'MOT-V',
			'NetFront',
			'Newt',
			'Nokia',
			'Opera Mini',
			'Palm',
			'portalmmm',
			'Proxinet',
			'ProxiNet',
			'SHARP-TQ-GX10',
			'Small',
			'SonyEricsson',
			'Symbian OS',
			'SymbianOS',
			'TS21i-10',
			'UP.Browser',
			'UP.Link',
			'Windows CE',
			'WinWAP',
		) );
		natcasesort( $this->browsers );


		// Detect browsers. See function definition for details.
		$this->LookForMobileBrowser();


		# Register our hooks

		// Login form stuff
		add_action( 'login_head', array(&$this, 'LoginFormHead') );
		add_action( 'login_form', array(&$this, 'LoginFormCheckbox'), 7 );
		add_action( 'wp_login', array(&$this, 'LoginFormPOST') );
		add_action( 'wp_logout', array(&$this, 'ClearCookie') );

		# The rest are admin only, so don't bog down the main site
		if ( !is_admin() ) return;

		add_filter( 'wp_redirect', array(&$this, 'MaybeChangeRedirect'), 1 );

		add_action( 'init', array(&$this, 'MaybeStartOutputBuffering'), 1 );
		add_action( 'admin_notices', array(&$this, 'MaybeDisplayBufferError') );
		add_action( 'admin_footer', array(&$this, 'MaybeOverride'), 77 );

		// Welcome message
		add_action( 'activate_wphone/wphone.php', array(&$this, 'PluginActivated') );
		add_action( 'admin_notices', array(&$this, 'WelcomeMessage'), 7 );
		add_action( 'plugins_loaded', array(&$this, 'QuickSwitchInterface'), 17 );
	}


	/**
	 * Displays an error saying this version of WordPress is too old.
	 *
	 * Designed for the "admin_notices" action.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function WordPressTooOld() { ?>

	<div id="wphone-error" class="error"><p><?php printf( __('<strong>WordPress Version Too Old:</strong> WPhone is only compatible with WordPress 2.1+ and is designed for use with the latest version of WordPress. Please <a href="%s">upgrade to the latest version</a>.', 'wphone'), 'http://wordpress.org/download/' ); ?></p></div>
<?php
	}


	/**
	 * Displays an error saying this plugin is incorrectly installed.
	 *
	 * Designed for the "admin_notices" action.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function IncorrectlyInstalled() { ?>

	<div id="wphone-error" class="error"><p><?php printf( __("<strong>WPhone Incorrectly Installed:</strong> WPhone must be installed to <code>%s</code> and it's directory structure intact. You will not be able to use the plugin until this is fixed.", 'wphone'), '/' . PLUGINDIR . '/wphone/' ); ?></p></div>
<?php
	}


	/**
	 * After this plugin is activated, this function redirects to a special URL with
	 * the variable "wphone" set to "activated" for use in WPhone::WelcomeMessage().
	 *
	 * Designed for the "activate_[plugin/path/here]" action.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function PluginActivated() {
		wp_redirect( 'plugins.php?activate=true&wphone=activated' );
		exit();
	}


	/**
	 * Displays a message welcoming the user to the plugin and instructing them how to activate the interface.
	 * Only will display the message is $_GET['wphone'] is set to "activated".
	 *
	 * Designed for the "admin_notices" action.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function WelcomeMessage() {
		if ( 'activated' != $_GET['wphone'] ) return;
?>

	<div id="wphone-welcome" class="updated fade"><p><?php printf( __('Thanks for installing <strong>WPhone</strong>! We hope your enjoy it. You can start using the admin interface by checking the new checkbox on the login form when you login on your phone.', 'wphone'), './?wphone=on' ); ?></p></div>
<?php
		//<a href="%s">right now</a> or later 
	}


	/**
	 * Sets or deletes the USEUI cookie based on a URL variable.
	 *
	 * If $_GET['wphone'] is set to "on", sets the WPhone USEUI cookie.
	 * If $_GET['wphone'] is set to "off", deletes the WPhone USEUI cookie.
	 * Then redirects to the dashboard.
	 *
	 * Designed for the "plugins_loaded" action.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function QuickSwitchInterface() {
		if ( 'on' != $_GET['wphone'] && 'off' != $_GET['wphone'] ) return;

		if ( 'on' == $_GET['wphone'] ) $expire = time() + 31536000; // on
		else                           $expire = time() - 31536000; // off

		setcookie( WPHONE_USEUI_COOKIE, 1, $expire, COOKIEPATH, COOKIE_DOMAIN );

		wp_redirect( get_bloginfo('wpurl') . '/wp-admin/' );
		exit();
	}


	/**
	 * Sets the internal $showui, $ismobile, and $iscompat variables to either TRUE or FALSE based on a number of tests.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function LookForMobileBrowser() {
		// Reset the vars
		$this->ismobile = FALSE;
		$this->iscompat = FALSE;
		$this->showui = FALSE;


		// If the user has manually selected the mobile interface, assume they're on a mobile device
		if ( !empty($_COOKIE[WPHONE_USEUI_COOKIE]) ) {
			$this->showui = TRUE;
			$this->ismobile = TRUE;

			// If the browser supports Javascript AND has "webkit" in the user agent, assume it's an iPhone or similiar
			// This ensures it's not a partial JS-support browser and that it's not a no JS browser with a "webkit" user agent
			if ( 'rich' == $_COOKIE[WPHONE_USEUI_COOKIE] && preg_match( $this->compatregex, $_SERVER['HTTP_USER_AGENT'] ) && FALSE == preg_match( $this->falsepositiveregex, $_SERVER['HTTP_USER_AGENT'] ) )
				$this->iscompat = TRUE;
		}

		// Okay, so they don't want the mobile interface, but let's guess about their browser anyway for other uses
		else {
			// Check to see if the user's browser is on the browser whitelist
			foreach ( (array) $this->whitelist as $browser ) {
				if ( strstr( $_SERVER['HTTP_USER_AGENT'], $browser ) ) {
					return;
				}
			}

			// Check for known mobile browsers
			foreach ( (array) $this->browsers as $browser ) {
				if ( strstr( $_SERVER['HTTP_USER_AGENT'], $browser ) ) {
					$this->ismobile = TRUE;
					return;
				}
			}
		}
	}


	/**
	 * If the mobile UI is wanted, this function attempts to turn on output
	 * buffering and records the result to the internal variable, $obstartstatus.
	 *
	 * Designed for the "init" action.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function MaybeStartOutputBuffering() {
		if ( TRUE != $this->showui ) return;

		$this->obstartstatus = ob_start();
	}


	/**
	 * If the mobile UI is wanted but output buffering failed, then display an error message.
	 *
	 * Designed for the "admin_notices" action.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function MaybeDisplayBufferError() {
		if ( TRUE != $this->showui || FALSE != $this->obstartstatus ) return;

		echo '<div id="wphone-error" class="error"><p>' . __('WPhone was unable to display the mobile interface as <code>ob_start()</code> failed to initialize.', 'wphone') . "</p></div>\n\n";
	}


	/**
	 * If the mobile UI is wanted and output buffering didn't fail, check for
	 * certain cases where we want to modify the redirect URL to something else of our choosing.
	 *
	 * Designed for the "wp_redirect" filter.
	 *
	 * @since 1.0.0
	 * @param string $location A full or relative URL to redirect to.
	 * @return string $location The possibly modified full or relative URL to redirect to.
	 */
	function MaybeChangeRedirect( $location ) {
		if ( TRUE != $this->showui || FALSE == $this->obstartstatus ) return $location;

		global $user_ID;

		// After deleting a comment
		if ( 'comment.php' == $this->current_basename && 'deletecomment' == $_GET['action'] && get_option('siteurl') . '/wp-admin/edit-comments.php' == $location ) {
			$location = 'edit-comments.php?type=approved&parent=edit-comments';
		}

		// After managing a post and it's not published
		elseif ( 'post.php' == $this->current_basename ) {
			$post_ID = (int) $_POST['post_ID'];
			$post = get_post($post_ID);

			// If a draft, redirect to your or others' drafts
			if ( 'post' == $post->post_type && 'draft' == $post->post_status ) {
				$location = ( $user_ID == $post->post_author ) ? 'edit.php?post_status=draft&author=' . $user_ID : 'edit.php?post_status=draft&author=-' . $user_ID;
			}

			// if it's pending, redirect to the pending list
			elseif ( 'post' == $post->post_type && 'pending' == $post->post_status ) {
				$location = 'edit.php?post_status=pending';
			}
		}

		return $location;
	}


	/**
	 * Display the mobile admin interface if it's wanted.
	 *
	 * If the mobile UI is wanted and output buffering didn't fail, empty the
	 * output buffer, determine what page is to be displayed, enable GZip, require_once() the
	 * file containing the page we wish to display, and then exit() to avoid any further HTML.
	 *
	 * Designed for the "admin_footer" filter.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function MaybeOverride() {
		if ( TRUE != $this->showui || FALSE == $this->obstartstatus ) return;

		auth_redirect(); // Make sure the user is logged in with credentials

		ob_clean(); // Dump any HTML that's already been created

		$current_basename = $this->current_basename; // Incase the old var is referenced anywhere

		// Figure out which one of files contains the stuff we need as a base
		switch ( $this->current_basename ) {
			// Write or edit posts and pages
			case 'post.php' :
			case 'page.php' :
			case 'post-new.php' :
			case 'page-new.php' :
				$this->context =  ( strstr($this->current_basename, 'page') ) ? 'page' : 'post';
				$include_file  = 'write.php';
				break;

			// List posts or pages
			case 'edit.php' :
			case 'edit-pages.php' :
				$this->context =  ( strstr($this->current_basename, 'page') ) ? 'page' : 'post';
				$include_file  = 'list.php';
				break;

			// List or edit comments
			case 'edit-comments.php' :
			case 'comment.php' :
				$this->context =  ( strstr($this->current_basename, 's.php') ) ? 'list' : 'edit';
				$include_file  = 'comment.php';
				break;

			// List users or edit user (including self)
			case 'profile.php' :
			case 'users.php' :
			case 'user-edit.php' :
				$this->context = ( strstr($this->current_basename, 'users') ) ? 'list' : 'edit';
				$include_file = 'user.php';
				break;

			// List, add or edit categories
			case 'categories.php' :
				$this->context = ( $_GET['action'] ) ? 'edit' : 'list';
				$include_file = 'category.php';
				break;

			// List or edit categories
			case 'plugins.php' :
				$this->context = 'list';
				$include_file = 'plugins.php';
				break;
				
			// Index or unknown page
			default :
				$this->context = 'dashboard';
				$include_file = 'dashboard.php';
		}

		// Special case for successful submit of the "Your Profile" form
		if ( 'profile.php' == $this->current_basename && !empty($_GET['updated']) ) {
			$this->context = 'dashboard';
			$include_file = 'dashboard.php';
			$profileupdated = TRUE;
		}

		global $wpdb, $userdata, $wp_version;

		if ( empty($userdata->ID) ) get_currentuserinfo(); // Just incase

		// Instantiate the navigantional menus and submenus
		$this->set_nav_menus();

		// Attempt to gzip page to make faster load times
		ob_start( 'ob_gzhandler' );

		// Load up the admin functions and such
		require_once( ABSPATH . 'wp-admin/admin.php');

		// Filters!
		$this->context = apply_filters( 'wphone_context', $this->context );
		$include_file = apply_filters( 'wphone_includefile', ABSPATH . $this->folder . '/includes/' . $include_file );

		// Load the file to display
		if ( !file_exists( $include_file ) ) {
			wp_die( sprintf( __('The file WPhone needed to display this page could not be found. <a href="%s">Click here to disable the mobile interface</a>.', 'wphone'), './?wphone=off' ) );
		}

		require_once( $include_file );

		// Stop from displaying the rest of the stuff below the "admin_footer" hook
		exit();
	}


	/**
	 * Use current_user_can() to check if the user is allowed to do a capability
	 * and if not, display an error message and then stop the script.
	 *
	 * @since 1.0.0
	 * @param string $args[0] The capability name to check for
	 * @param string $args[1] An ID or other identifier to use in combination with the capability.
	 * @param string $args[2] Unknown use, if any use at all.
	 * @return NULL
	 */
	function check_user_permissions() {
		$args = func_get_args();

		if ( ! current_user_can( $args[0], $args[1], $args[2] ) ) {
			exit ( 
				'<script type="text/javascript">alert("' . __('Access Denied', 'wphone') . '");</script>'
				. '<div id="errormenu" title="' . __('Error', 'wphone') . '" class="panel">'
				. sprintf( __('You are not allowed to %s.', 'wphone'), $args[0] )
				. "</div>\n</body>\n</html>"
			);
		}
	}


	/**
	 * Returns TRUE or FALSE based on whether the current user is on a mobile device or not.
	 *
	 * @since 1.0.0
	 * @param string $browser A specific string to check for the user agent (optional).
	 * @return boolean TRUE if mobile device OR parameter string was in the user agent, otherwise FALSE.
	 */
	function is_mobile( $browser = FALSE ) {
		if ( FALSE === $this->ismobile ) return FALSE;

		// Default call to the function (we've already checked to see if we're using a mobile browser)
		if ( FALSE === $browser ) return TRUE;

		// Check for a specific, user-specified browser
		if ( strstr( $_SERVER['HTTP_USER_AGENT'], $browser ) ) return TRUE;

		// User-specified browser not found
		return FALSE;
	}


	/**
	 * Wrapper to load the header or footer file based on the entered parameter.
	 *
	 * @since 1.0.0
	 * @param string $part The file to load, can only be "header" or "footer". Defaults to "footer".
	 * @return NULL
	 */
	function load_interface( $part = 'header' ) {
		if ( $part != 'header' ) $part = 'footer'; 
		require_once( apply_filters( 'wphone_loadinterface', ABSPATH . $this->folder . '/includes/' . $part . '.php')  );
	}


	/**
	 * If the mobile device is not a "rich" device or the page is not being loaded via AJAX, call WPhone::load_interface();
	 *
	 * @since 1.0.0
	 * @param string $part The file to load, can only be "header" or "footer". Defaults to "footer".
	 * @return NULL
	 */
	function test_interface($part = 'header') {
		if ( $part != 'header' ) $part = 'footer'; 
		if ( !$this->iscompat || $_REQUEST['wphone'] != 'ajax' ) {
			$this->load_interface( $part );
		}
	}


	/**
	 * Sets the two main levels of navigational menus
	 *
	 * @since 1.5.0
	 */
	function set_nav_menus() {		
		global $userdata; // Always instantiated in $this->MaybeOverride()
		
		/*
		 * Level 1 navigation
		 */
		
		$this->menu[10] = array( __('Write'), 'edit_posts', '', 'write' );
		$this->menu[20] = array( __('Manage'), 'edit_posts', '', 'manage' );
		$this->menu[30] = array( __('Comments'), 'moderate_comments', 'edit-comments.php', 'comments' );
		$this->menu[40] = array( __('Plugins'), 'activate_plugins', 'plugins.php', 'plugins' );
		if ( current_user_can('edit_users') )
			$this->menu[50] = array( __('Users'), 'edit_users', '', 'users' );
		else
			$this->menu[50] = array( __('Your Profile'), 'read', 'profile.php', 'profile' );
		$this->menu[60] = array( __('Latest Activity'), 'manage_options', '', 'activity' );

		// Allows plugin developers to add or overwrite menu entries
		$this->menu = apply_filters( 'wphone_menulist', $this->menu );
		
		/*
		 * Level 2 navigation
		 */

		$this->submenu['write'][10] = array( __('Post'), 'edit_posts', 'post-new.php?wphone=ajax' );
		$this->submenu['write'][20] = array( __('Page'), 'edit_pages', 'page-new.php?wphone=ajax' );
		
		$this->submenu['manage'][10] = array( __('Published Posts'), 'edit_posts', 'edit.php?wphone=ajax' );
		$this->submenu['manage'][20] = array( __('Your Drafts (%d)', 'wphone'), 'edit_posts', 'edit.php?wphone=ajax&amp;post_status=draft&amp;author=' . $userdata->ID );
		$this->submenu['manage'][30] = array( __('Pending Review (%d)', 'wphone'), 'edit_posts', 'edit.php?wphone=ajax&amp;post_status=pending' );
		$this->submenu['manage'][40] = array( __('Others&#8217; Drafts (%d)', 'wphone'), 'edit_posts', 'edit.php?wphone=ajax&amp;post_status=draft&amp;author=-' . $userdata->ID );
		$this->submenu['manage'][50] = array( __('Pages'), 'edit_pages', 'edit-pages.php?wphone=ajax' );
		$this->submenu['manage'][60] = array( __('Categories'), 'manage_categories', 'categories.php?wphone=ajax' );
		$this->submenu['manage'][70] = array( __('Add Category'), 'manage_categories', 'categories.php?wphone=ajax&amp;add=true' );
		
		$this->submenu['comments'][10] = array( __('Edit Comments (%d)', 'wphone'), 'moderate_comments', 'edit-comments.php?wphone=ajax&amp;parent=edit-comments&amp;type=approved' );
		$this->submenu['comments'][20] = array( __('Awaiting Moderation (%d)', 'wphone'), 'moderate_comments', 'edit-comments.php?wphone=ajax&amp;parent=edit-comments&amp;type=moderation' );
		$this->submenu['comments'][30] = array( __('Spam Comments (%d)', 'wphone'), 'moderate_comments', 'edit-comments.php?wphone=ajax&amp;parent=edit-comments&amp;type=spam' );
		
		$this->submenu['users'][10] = array( __('User List & Search'), 'edit_users', 'users.php?wphone=ajax' );
		$this->submenu['users'][20] = array( __('Add New User'), 'edit_users', 'users.php?wphone=ajax&amp;add=true' );
		$this->submenu['users'][30] = array( __('Your Profile'), 'read', 'profile.php?wphone=ajax' );

		// Allows plugin developers to add or overwrite submmenu entries
		$this->submenu = apply_filters( 'wphone_submenulist', $this->submenu );
	}

	/**
	 * Generates an HTML link based on whether the current page is the mobile dashboard or not.
	 *
	 * @since 1.3.0
	 * @param string $id The HTML ID / anchor to go to.
	 * @param boolean $setgetvar Whether to add "&wphone=ajax" to the link URL
	 * @return string $goto HTML link
	 */
	function quick_link_url( $id, $setgetvar = FALSE ) {
		$id .= 'menu';
		$dashboard = ( 'dashboard' == $this->context ) ? TRUE : FALSE;
		$add_hash = ( TRUE != $this->iscompat ) ? TRUE : FALSE;
		if ( TRUE === $dashboard ) {
			$goto = '#' . $id;
		} else {
			$goto = $this->admin_url . '/?goto=' . $id;
			if ( FALSE === $setgetvar ) $goto .= '&wphone=ajax';
			if ( TRUE === $add_hash ) $goto .= '#' . $id;
		}
		return $goto;
	}


	/**
	 * Based on user capabilities, display certain list items.
	 * Intended for usage on the primary mobile dashboard and navigation menu.
	 *
	 * @since 1.3.0
	 * @param string $location Passed to the filter saying where this function is being used.
	 * @return NULL
	 */
	function quick_links( $location ) {
		$extra_html = '';
		$extra_qs   = '';
		$setgetvar  = FALSE;

		switch( $location ) {
			case 'navigation' :
				$extra_html = ' class="grayButton" ' . $this->htmltarget( '_self', TRUE );
				$setgetvar = TRUE;
				break;
			case 'dashboard' :
				$extra_qs   = '?wphone=ajax';
		}

		uksort( $this->menu, 'strnatcasecmp' );

		foreach ( $this->menu as $entry ) {
			if ( current_user_can($entry[1]) ) {
				if ( empty($entry[2]) )
					$link = $this->quick_link_url($entry[3], $setgetvar);
				else
					$link = $this->admin_url . '/' . $entry[2] . $extra_qs;
				echo '<li><a href="' . $link . '" ' . $extra_html . ' >' . $entry[0] . "</a></li>\n";
			}
		}
	}
	
	
	/**
	 * Echos the requested pre-filtered submenu entries
	 *
	 * @since 1.5.0
	 * @param string $menu_id Submenu ID as defined in $this->set_nav_menus()
	 * @param array $count_info Record count info to be added to the submenu caption. EG: show_submenu( 'manage', array(20 => $draft_count_int) )
	 * @param boolean $display_empty Should the menu entry be displayed if the associated record count is less than 1
	 */
	function show_submenu( $submenu_id, $count_info = array(), $display_empty = TRUE ) {
		$submenu = $this->submenu[$submenu_id];
		if ( !empty($submenu) && is_array($submenu) ) {
			foreach ( $submenu as $index => $options ) {
				if ( current_user_can($options[1]) ) {
					$has_count = ( isset($count_info[$index]) ) ? TRUE : FALSE;
					if ( TRUE == $has_count ) {
						$options[0] = sprintf($options[0], $count_info[$index]);
					}
					if ( ( TRUE == $display_empty ) || ( ( TRUE == $has_count ) && ( 0 < intval($count_info[$index]) ) ) )
						echo '<li><a href="' . $options[2] . '" >' . $options[0] . "</a></li>\n";
					elseif ( FALSE == $has_count ) 
						echo '<li><a href="' . $options[2] . '" >' . $options[0] . "</a></li>\n";
				}
			}
		}
	}


	/**
	 * Output the HTML for an input/button (submit or whatnot) based on whether the mobile browser is a "rich" one or a "lite" one.
	 *
	 * @since 1.0.0
	 * @param string $type "type" HTML parameter, such as "submit".
	 * @param string $name "name" HTML parameter.
	 * @param string $value "value" HTML parameter.
	 * @param string $onclick "onclick" HTML parameter.
	 * @param string $class_change The CSS class for the "rich" button, defaults to "whiteButton".
	 * @return NULL
	 */
	function panel_button( $type, $name, $value, $onclick = '', $class_change = 'whiteButton' )
	{
		if ( !$this->iscompat ) {
			echo '<input class="' . $class_change . '" type="' . $type . '" name="' . $name . '" id="' . $name . '" value="' . $value . '" style="text-align:center;" /><br />' . "\n";
		} else {
			if ( $onclick == '' && $type == 'submit' ) $onclick = "WPhone.submitForm(this)";
			echo '<button class="' . $class_change . '" type="' . $type . '" name="' . $name . '" id="' . $name . '" onclick="' . $onclick . '" >' . $value . "</button>\n";
		}
	}
	
	
	/**
	 * Output the HTML for a link or <button> that acts as a link based whether the mobile browser is a "rich" one or a "lite" one.
	 *
	 * @since 1.0.0
	 * @param string $value The name of the link or button.
	 * @param string $question If "rich" version, the user will be asked this via a confirm(). Should be like "are you sure you want to do this?".
	 * @param string $url The URL to go to.
	 * @return NULL
	 */
	function panel_delete_button( $value, $question, $url )
	{
		if ( !$this->iscompat ) {
			echo '<a href="' .$url. '">' .$value. "</a><br />\n";
		} else {
			$question = js_escape($question);
			$url = js_escape($url);
			$onclick = "if ( confirm('$question') ) { top.location.href = '$url';}";
			echo '<button class="whiteButton" type="button" name="deletebutton" onclick="' . $onclick . '" >' . $value . '</button>' . "\n";
		}
	}


	/**
	 * Returns the referer if it's a non "wp-admin" URL, otherwise returns the $url parameter.
	 *
	 * @since 1.0.0
	 * @param string $url The URL to use if it's a "wp-admin" URL
	 * @return string The referer, either the true one or the $url parameter.
	 */
	function referer( $url = '' ) {
		if ( $referer = wp_get_referer() ) {
			if ( !strstr( $referer, 'wp-admin' ) ) return $referer;
		}

		return $url;
	}


	/**
	 * Outputs an HTML "target" parameter if the mobile browser is a "rich" one.
	 *
	 * @since 1.0.0
	 * @param string $target The HTML "target" parameter value.
	 * @param string $return If TRUE, return the HTML, otherwise echo it.
	 * @return string The "target" HTML, but only returns if the $return parameter is set to TRUE.
	 */
	function htmltarget( $target, $return = FALSE ) {
		if ( !$this->iscompat ) return;
		$value = ' target="' . $target . '"';
		if ( !empty($return) )
			return $value;
		else
			echo $value;
	}


	/*
	 * Outputs Mobile Safari specific information to help with rendering.
	 *
	 * Designed for the "login_head" action.
	 *
	 * @since 1.5.0
	 * @return NULL
	 */
	function LoginFormHead() {
		echo '	<meta name="viewport" content="width=400;" />' . "\n";
	}



	/**
	 * Outputs a checkbox and some Javascript for the WordPress login form.
	 *
	 * Designed for the "login_form" action.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function LoginFormCheckbox() { ?>
	<p>
		<label><input type="checkbox" name="wphone_usemobile" id="wphone_usemobile" value="1" tabindex="80"<?php if ( !empty($_COOKIE[WPHONE_CHKBOX_COOKIE]) || $this->is_mobile() || !empty($_POST['wphone_usemobile']) ) echo ' checked="checked"'; ?> /> <?php _e('Use mobile admin interface', 'wphone'); ?></label>
		<script type="text/javascript"><!--
			document.write( '<input type="hidden" name="wphone_supportsjs" id="wphone_supportsjs" value="1" />' );
		--></script>
	</p>
<?php
	}


	/**
	 * If the user checked the login box on the WordPress login form, set the WPhone cookies, otherwise delete them.
	 *
	 * Designed for the "wp_login" action.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function LoginFormPOST() {
		// The "wp_login" hook can be triggered when a logged in user visits wp-login.php apparently, so check for $_POST
		if ( 0 < count($_POST) ) {
			// The user wants to use the mobile interface. Set a cookie for that and that they checked the checkbox.
			if ( 1 == $_POST['wphone_usemobile'] ) {
				$mode = ( 1 == $_POST['wphone_supportsjs'] ) ? 'rich' : 'lite';

				setcookie( WPHONE_USEUI_COOKIE, $mode, time() + 31536000, COOKIEPATH, COOKIE_DOMAIN );
				setcookie( WPHONE_CHKBOX_COOKIE, 1, time() + 31536000, COOKIEPATH, COOKIE_DOMAIN );
			}

			// Otherwise make sure they don't have our checkbox cookie
			else {
				setcookie( WPHONE_USEUI_COOKIE, 0, time() - 31536000, COOKIEPATH, COOKIE_DOMAIN );
				setcookie( WPHONE_CHKBOX_COOKIE, 0, time() - 31536000, COOKIEPATH, COOKIE_DOMAIN );
			}
		}
	}


	/**
	 * Deletes the WPhone USEUI cookie, but not their checkbox preference.
	 *
	 * Designed for the "wp_logout" action.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function ClearCookie() {
		setcookie( WPHONE_USEUI_COOKIE, 0, time() - 31536000, COOKIEPATH, COOKIE_DOMAIN );
	}
}


/**
 * External wrapper for WPhone::is_mobile(). See it's documentation for details.
 *
 * @since 1.0.0
 */
if ( !function_exists('is_mobile') ) {
	function is_mobile( $browser = FALSE ) {
		global $WPhone;
		return $WPhone->is_mobile( $browser );
	}
}


/**
 * External wrapper for WPhone::is_mobile(). See it's documentation for details.
 *
 * @since 1.4.2
 */
function wphone_ismobile( $browser = FALSE ) {
	global $WPhone;
	return $WPhone->is_mobile( $browser );
}


/**
 * Returns TRUE or FALSE based on whether the current user is on a "rich" mobile device or not.
 *
 * @since 1.0.0
 * @return boolean TRUE if "rich" mobile device, otherwise FALSE.
 */
function is_mobile_highcompat() {
	global $WPhone;
	return $WPhone->iscompat;
}


// Start this plugin once all other plugins are fully loaded
add_action( 'plugins_loaded', create_function( '', 'global $WPhone; $WPhone = new WPhone();' ) );

?>