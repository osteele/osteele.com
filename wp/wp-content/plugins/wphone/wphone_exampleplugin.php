<?php /*

In order to not clutter the plugins listing page, a space has been added to
the "Plugin Name" label. Remove it if you wish for this plugin to show up.

**************************************************************************

P lugin Name: WPhone Example Plugin
Plugin URI:   http://wphoneplugin.org/
Version:      1.5.0
Description:  An example plugin on how to add to the WPhone interface.
Author:       <a href="http://tekartist.org/">Stephane Daury</a>, <a href="http://literalbarrage.org/blog/">Doug Stewart</a>, and <a href="http://www.viper007bond.com/">Viper007Bond</a>

**************************************************************************/

class WPhone_Example {
	/**
	 * Registers filters and actions.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function WPhone_Example() {
		// DEPRECATED add_filter( 'wphone_linkslist', array(&$this, 'AddListItem'), 10, 2 );
		add_filter( 'wphone_menulist', array(&$this, 'UpdateMenuItems') );
		add_filter( 'wphone_submenulist', array(&$this, 'UpdateSubmenuItems') );
		add_filter( 'wphone_commentsmenu_countlist', array(&$this, 'ReplaceSpamSums') );
		// COULD add_filter( 'wphone_includefile', array(&$this, 'FunctionToIncludeCustomFile') );

		add_action( 'wphone_dashboard', array(&$this, 'OptionsList') );
		add_action( 'wphone_dashboard_init', array(&$this, 'MaybeOptionsPage') );
	}


	/**
	 * If the current user can manage options, modify the primary navigation (initial screen + primary nav).
	 *
	 * Designed for the "wphone_menulist" filter.
	 *
	 * @since 1.5.0
	 * @param array $links The primary nav links, as defined in WPhone->set_nav_menu().
	 */
	function UpdateMenuItems( $links ) {
		if ( current_user_can('manage_options') ) {
			// adding our own primary nav item
			$links[55] = array( __('Options [EG]', 'wphone_example'), 'manage_options', '', 'options' );

			// overriding a default primary nav item
			$links[60] = array( __('What&#8217;s New [EG]', 'wphone_example'), 'manage_options', '', 'activity' );
		}
		return $links;
	}


	/**
	 * If the current user can moderate comments, modify the comments submenu.
	 *
	 * Designed for the "wphone_submenulist" filter.
	 *
	 * @since 1.5.0
	 * @param array $links The submenu links, as defined in WPhone->set_nav_menu().
	 */
	function UpdateSubmenuItems( $links ) {
		if ( current_user_can('moderate_comments') ) {
			// overriding a default item in the Comments submenu
			$links['comments'][30] = array( __('Evil Spam (%d) [EG]', 'wphone_example'), 'moderate_comments', 'edit-comments.php?wphone=ajax&amp;parent=edit-comments&amp;type=spam' );

			// adding our own item to the Comments submenu
			$links['comments'][40] = array( __('Standard Spam List (%d) [EG]', 'wphone_example'), 'moderate_comments', 'edit-comments.php?wphone=ajax&amp;parent=edit-comments&amp;type=spam' );
		}
		return $links;
	}


	/**
	 * If the current user can moderate comments, modify the sums displayed in the comments submenu.
	 *
	 * Designed for the "wphone_commentsmenu_countlist" filter.
	 *
	 * @since 1.5.0
	 * @param array $sums The comment count sums as defined in wphone/includes/comment/php -> apply_filters('wphone_commentsmenu_countlist', ...).
	 */
	function ReplaceSpamSums( $sums ) {
		if ( current_user_can('moderate_comments') ) {
			// displaying standard spam sum in our new comments menu item.
			$sums[40] = $sums[30];

			// displaying our own custom spam count where we replaced the standard comments menu spam entry.
			$sums[30] = 157; // we caught 157 more spams!
		}
		return $sums;
	}


	/**
	 * Outputs the sub-menu for the options menu.
	 *
	 * Designed for the "wphone_dashboard" filter.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function OptionsList() {
		global $WPhone;

		if ( !current_user_can('manage_options') ) return;

		// Tells WPhone not to load the header/footer when in AJAX mode
		$query_string = ($WPhone->iscompat) ? '?wphone=ajax' : '';
		
		echo '<h2 class="accessible">' . __('Options') . "</h2>\n";
		echo '<ul id="optionsmenu" title="' . __('Options') . '" ' . $iui_selected . ">\n";
		echo '<li><a href="options-general.php' . $query_string . '">' . __('General') . "</a></li>\n";
		echo '<li><a href="options-writing.php' . $query_string . '">' . __('Writing') . "</a></li>\n";
		echo '<li><a href="options-reading.php' . $query_string . '">' . __('Reading') . "</a></li>\n";
		echo '<li><a href="options-discussion.php' . $query_string . '">' . __('Discussion') . "</a></li>\n";
		echo '<li><a href="options-privacy.php' . $query_string . '">' . __('Privacy') . "</a></li>\n";
		echo '<li><a href="options-permalink.php' . $query_string . '">' . __('Permalinks') . "</a></li>\n";
		echo '<li><a href="options-misc.php' . $query_string . '">' . __('Miscellaneous') . "</a></li>\n";
		echo "</ul>\n";
	}


	/**
	 * Checks to see if it's a URL we care about and if so, outputs the header, content, and footer.
	 *
	 * Designed for the "wphone_dashboard_init" filter.
	 *
	 * @since 1.0.0
	 * @return NULL
	 */
	function MaybeOptionsPage() {
		global $WPhone;

		if ( !current_user_can('manage_options') || 'options-' != substr( $WPhone->current_basename, 0, 8 ) ) return;

		// Tells iUI to show this container by default
		$iui_selected = ( $WPhone->iscompat ) ? ' selected="true"' : '';
		
		// Which options page are we on?
		switch( $WPhone->current_basename ) {
			case 'options-general.php' :
				$text = '<p' . $iui_selected . '>This would be the general options page.</p>';
				break;
			case 'options-writing.php' :
				$text = '<p' . $iui_selected . '>This would be the writing options page.</p>';
				break;
			case 'options-reading.php' :
				$text = '<p' . $iui_selected . '>This would be the reading options page.</p>';
				break;
			case 'options-discussion.php' :
				$text = '<p' . $iui_selected . '>This would be the discussion options page.</p>';
				break;
			case 'options-privacy.php' :
				$text = '<p' . $iui_selected . '>This would be the privacy options page.</p>';
				break;
			case 'options-permalink.php' :
				$text = '<p' . $iui_selected . '>This would be the permalink options page.</p>';
				break;
			case 'options-misc.php' :
				$text = '<p' . $iui_selected . '>This would be the miscellaneous options page.</p>';
				break;
			default :
				return; // Unknown options page, so don't do anything
		}

		// Load header if not in AJAX mode
		if ( 'ajax' != $_GET['wphone'] ) $WPhone->load_interface('header');

		// Page contents
		echo $text;

		// Load footer if not in AJAX mode
		if ( 'ajax' != $_GET['wphone'] ) $WPhone->load_interface('footer');

		// Needed to abort showing the regular WPhone dashboard (a bit dirty, I know)
		exit();
	}
}

$WPhone_Example = new WPhone_Example();

?>