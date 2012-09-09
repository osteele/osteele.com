<?php
/*
Plugin Name: Flexo Archives
Description: Displays archives as a list of years that expand when clicked
Author: Heath Harrelson
Version: 2.1.5
Plugin URI: http://wordpress.org/extend/plugins/flexo-archives-widget/
*/

/*
 * Flexo Archives Widget by Heath Harrelson, Copyright (C) 2011
 *
 * This contains heavily modified code from the default WordPress archives
 * widget, with bits from wp_get_archives() and Ady Romantika's random posts
 * widget 
 * (http://www.romantika.name/v2/2007/05/02/wordpress-plugin-random-posts-widget/).
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 */

class FlexoArchives {
    // Options constants
    var $OLD_OPTIONS_NAME = 'widget_flexo';
    var $OPTIONS_NAME   = 'flexo_archives';
    var $OPT_STANDALONE = 'standalone'; // bool: standalone func enabled?
    var $OPT_ANIMATE    = 'animate';    // bool: list animation enabled
    var $OPT_NOFOLLOW   = 'nofollow';   // bool: add rel="nofollow" to links
    var $OPT_COUNT      = 'count';      // bool: monthly post counts in lists
    var $OPT_COUNT_STANDALONE = 'standalone-count'; // bool: monthly post counts
    var $OPT_YRTOTAL_STANDALONE = 'standalone-yeartotal'; // bool: yearly post total DEPRECATED
    var $OPT_YRTOTAL    = 'yeartotal'; // bool: yearly post total
    var $OPT_WTITLE     = 'title';      // string; widget title string
    var $OPT_CONVERTED  = '2';  // array: converted non-multi widget settings
    var $OPT_MONTH_DESC = 'month-descend'; // bool: order months descending

    // Filename constants
    var $FLEXO_JS = 'flexo.js';
    var $FLEXO_ANIM_JS = 'flexo-anim.js';

    // Subdirectory where the plugin is located
    var $flexo_dir;

    // Options array
    var $options;

    /**
     * PHP4 constructor
     */
    function FlexoArchives () {
        return $this->__construct();
    }

    /**
     * PHP5 constructor
     */
    function __construct () {
        $this->flexo_dir = basename(dirname(__FILE__));
        $this->initialize();
    }

    /**
     * Register plugin callbacks with WordPress
     */
    function initialize () {
        // get translations loaded
        add_action('init', array(&$this, 'load_translations'));

        // make sure options are initialized
        $this->set_default_options();

        // register callbacks
        add_action('init', array(&$this, 'enqueue_standalone_scripts'));
        add_action('admin_menu', array(&$this, 'options_menu_item'));

        add_action('widgets_init', array(&$this, 'widget_init'));

        register_uninstall_hook(__FILE__, array(&$this, 'uninstall'));

        // add stylesheet for settings page
        if (is_admin()) {
            $css_url = plugins_url('flexo-admin-style.css', __FILE__);
            wp_enqueue_style('flexo-admin-style', $css_url);
        }
    }

    /**
     * Sets the default values for unset options
     */
    function set_default_options () {
        $options = $this->get_opts();

        $global_defaults = array(
                            $this->OPT_ANIMATE => true,
                            $this->OPT_NOFOLLOW => false,
                            $this->OPT_MONTH_DESC => false,
                            $this->OPT_STANDALONE => false,
                            $this->OPT_COUNT_STANDALONE => false,
                            $this->OPT_YRCOUNT_STANDALONE => false,
                            $this->OPT_YRCOUNT => false
                          );

        // global defaults
        foreach ($global_defaults as $def_key => $def_value) {
            if (!isset($options[$def_key])) {
                $options[$def_key] = $def_value;
            }
        }

        // convert option to print yearly totals to a global option
        // rather than one for the standalone function only
        if ($options[$this->OPT_YRCOUNT_STANDALONE]) {
            $options[$this->OPT_YRCOUNT] = true;
        }

        // widget options
        foreach ($options as $opts_key => $opts_value) {
            if (is_numeric($opts_key)) {
                // default widget title is "Archives"
                if (!isset($opts_value[$this->OPT_WTITLE])) {
                    $opts_value[$this->OPT_WTITLE] = strip_tags(__('Archives', 'flexo-archives'));
                }

                // post counts disabled
                if (!isset($opts_value[$this->OPT_COUNT])) {
                    $opts_value[$this->OPT_COUNT] = false;
                }
            }
        }

        $this->set_opts($options);
    }

    /**
     * Gets the entire options array from the database. Converts
     * old-style options to new-style multi-widget options.
     * 
     * Returns: An array of options. Individual options
     * can be accessed by their keys, defined as class
     * constants (see above).
     */
    function get_opts () {
        // options not initialized yet
        if (is_null($this->options)) {
            $this->options = get_option($this->OPTIONS_NAME);

            if (!$this->options) {
                // convert old-style options to multi-widget options
                if (get_option($this->OLD_OPTIONS_NAME)) {
                    $this->convert_old_opts();
                } else {
                    // this will get populated by defaults
                    $this->options = array();
                }
            }
        }

        if (isset($this->options[0])) {
            unset($this->options[0]);
        }

        return $this->options;
    }

    /**
     * Save a modified options array to the database
     *
     * Arguments: An array containing the options. Array
     * keys are defined as class constants (see above).
     */
    function set_opts ($newoptions = null) {
        $options = $this->get_opts();
        if ($options != $newoptions) {
            $this->options = $newoptions;
            update_option($this->OPTIONS_NAME, $newoptions);
        }
    }

    /**
     * Convert old-style options to global or widget options for
     * the multi-widget version as appropriate.
     */
    function convert_old_opts () {
        $old = get_option($this->OLD_OPTIONS_NAME);

        // widget options
        $widget_opts = array(
                         $this->OPT_WTITLE => $old[$this->OPT_WTITLE],
                         $this->OPT_COUNT => $old[$this->OPT_COUNT]
                       );

        $this->replace_old_widget_id();
        $this->options = array($this->OPT_CONVERTED => $widget_opts);

        // global options
        if (isset($old[$this->OPT_STANDALONE])) {
            $this->options[$this->OPT_STANDALONE] = $old[$this->OPT_STANDALONE];
        }

        if (isset($old[$this->OPT_ANIMATE])) {
            $this->options[$this->OPT_ANIMATE] = $old[$this->OPT_ANIMATE];
        }

        // save converted options and clean up
        delete_option($this->OLD_OPTIONS_NAME);
        update_option($this->OPTIONS_NAME, $this->options);
    }

    /**
     * Converts the name of the old single widget in the sidebar 
     * settings. Unless this is done, the widget will disappear
     * from the sidebar and its settings are lost. Converting the 
     * name will keep the widget working for existing users when 
     * upgrading to multi-widget capability.
     */
    function replace_old_widget_id () {
        $replacement_id = 'flexo-archives-' . $this->OPT_CONVERTED;

        $sidebars = get_option('sidebars_widgets');

        $modified_sidebar_key = false;
        $modified_sidebar_arr = false;
        $modified = false;

        // bail if db fetch failed
        if (!is_array($sidebars))
            return;

        // iterate the sidebars and replace the widget id of the old version
        // $sidebars is a mixed array, where keys mostly point to arrays
        foreach($sidebars as $sidebar => $widgets) {
            // skip non-array elements
            if (!is_array($widgets))
                continue;

            // iterate arrays found; one for each sidebar
            foreach ($widgets as $widget_index => $widget_id) {
                // found the only old-style widget
                if ($widget_id == 'flexo-archives') {
                    $modified_sidebar_key = $sidebar;
                    $modified_sidebar_arr = $widgets;
                    $modified_sidebar_arr[$widget_index] = $replacement_id;
                    $modified = true;
                    break 2; // break out of both foreach loops
                }
            }
        }


        // save
        if ($modified) {
            $sidebars[$modified_sidebar_key] = $modified_sidebar_arr;
            update_option('sidebars_widgets', $sidebars);
        }
    }

    /**
     * Gets the widget title set in the database
     */
    function widget_title ($widget_num) {
        $options = $this->get_opts();
        $widget_opts = isset($options[$widget_num]) ? $options[$widget_num] : false;
        return $widget_opts ? attribute_escape($widget_opts[$this->OPT_WTITLE]) : __('Archives', 'flexo-archives');
    }

    /**
     * Reports whether the user enabled post counts
     */
    function widget_count_enabled ($widget_num) {
        $options = $this->get_opts();
        $widget_opts = isset($options[$widget_num]) ? $options[$widget_num] : false;
        return $widget_opts ? $widget_opts[$this->OPT_COUNT] : false;
    }

    /**
     * Reports whether the user enabled post counts for standalone
     */
    function standalone_count_enabled () {
        $options = $this->get_opts();
        return $options[$this->OPT_COUNT_STANDALONE];
     }

    /**
     * Reports whether the user enabled yearly post totals
     */
    function yearly_total_enabled () {
        $options = $this->get_opts();
        return $options[$this->OPT_YRTOTAL];
    }

    /**
     * Reports whether standalone archive function is enabled
     */
    function standalone_enabled () {
        $options = $this->get_opts();
        return $options[$this->OPT_STANDALONE];
    }

    /**
     * Reports whether list animation is enabled
     */
    function animation_enabled () {
        $options = $this->get_opts();
        return $options[$this->OPT_ANIMATE];
    }

    /**
     * Reports whether links should have rel="nofollow" added.
     */
    function nofollow_enabled () {
        $options = $this->get_opts();
        return $options[$this->OPT_NOFOLLOW];
    }

    /**
     * How should months in the lists be sorted
     */
    function month_order () {
        $options = $this->get_opts();
        return $options[$this->OPT_MONTH_DESC] ? 'DESC' : 'ASC';
    }

    /**
     * Loads translated strings from catalogs in ./lang
     */
    function load_translations () {
        $lang_dir = $this->flexo_dir . '/lang';
        load_plugin_textdomain('flexo-archives', null, $lang_dir);
    }

    /**
     * Function to register our sidebar widget with WordPress
     */
    function widget_init () {
        // Check for required functions
        if (!function_exists('wp_register_sidebar_widget'))
            return;

        // Call the registration function on init
        $this->register_widgets();
    }

    /**
     * standalone anymore
     * Register the configuration page for the standalone function
     */
    function options_menu_item () {
        $page_title = __('Flexo Archives Advanced Options', 'flexo-archives');
        $menu_title = __('Flexo Archives', 'flexo-archives');
        $menu_slug  = 'flexo-archvies-options';

        add_options_page($page_title, $menu_title, 'manage_options',
                 $menu_slug, array(&$this, 'options_page'));
    }

    /**
     * Output advanced plugin configuration page
     */
    function options_page () {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient priveleges to access this page.', 'flexo-archives'));
        }


        // form submitted
        $options = $newoptions = $this->get_opts();
        if ( !empty($_POST["flexo-submit"]) &&
             check_admin_referer('flexo-archives-options-page') )
        {
            $newoptions[$this->OPT_ANIMATE] = isset($_POST['flexo-animate']);
            $newoptions[$this->OPT_NOFOLLOW] = isset($_POST['flexo-nofollow']);
            $newoptions[$this->OPT_MONTH_DESC] = isset($_POST['flexo-monthdesc']);
            $newoptions[$this->OPT_STANDALONE] = isset($_POST['flexo-standalone']);
            $newoptions[$this->OPT_COUNT_STANDALONE] = isset($_POST['flexo-count']);
            $newoptions[$this->OPT_YRTOTAL] = isset($_POST['flexo-yrtotal']);
        }

        // save if options changed
        if ($options != $newoptions) {
            $options = $newoptions;
            $this->set_opts($options);
        }

        $standalone = $this->standalone_enabled() ? 'checked="checked"' : '';
        $animate = $this->animation_enabled() ? 'checked="checked"' : '';
        $count = $this->standalone_count_enabled() ? 'checked="checked"' : '';
        $total = $this->yearly_total_enabled() ? 'checked="checked"' : '';
        $nofollow = $this->nofollow_enabled() ? 'checked="checked"' : '';
        $monthdesc = $this->month_order() == 'DESC' ? 'checked="checked"' : '';

?>
<div class="wrap">
  <h2><?php _e('Advanced Flexo Archives Options', 'flexo-archives'); ?></h2>
  <!-- <div class="narrow"> -->
  <div id="flexo-admin-form">
  <form name="flexo-options-form" method="post" action="">
    <?php wp_nonce_field('flexo-archives-options-page'); ?>
    <fieldset>
      <legend><?php _e('Global Options', 'flexo-archives'); ?></legend>
      <p><label for="flexo-animate"><input type="checkbox" class="checkbox" id="flexo-animate" name="flexo-animate" <?php echo $animate; ?>/> <?php _e('animate collapsing and expanding lists', 'flexo-archives'); ?></label></p>
      <p><label for="flexo-nofollow"><input type="checkbox" class="checkbox" id="flexo-nofollow" name="flexo-nofollow" <?php echo $nofollow; ?>/> <?php _e('add rel="nofollow" to links', 'flexo-archives'); ?></label></p>
      <p><label for="flexo-monthdesc"><input type="checkbox" class="checkbox" id="flexo-monthdesc" name="flexo-monthdesc" <?php echo $monthdesc; ?>/> <?php _e('sort months in descending order', 'flexo-archives'); ?></label></p>
      <p><label for="flexo-yrtotal"><input type="checkbox" class="checkbox" id="flexo-yrtotal" name="flexo-yrtotal" <?php echo $total; ?>/> <?php _e('show yearly post totals in lists', 'flexo-archives'); ?></label></p>
    </fieldset>

  <p><?php _e('The following options are only relevant to users who cannot use or do not want to use the sidebar widget. If you are using the widget, then you should ignore the following settings.', 'flexo-archives'); ?></p>
  <p><?php _e('To use the standalone version of the archives, check the "enable standalone theme function" box below, and then add the following code your theme where you want the expandable archive lists to be:', 'flexo-archives'); ?></p>

  <code>&lt;?php if (function_exists('flexo_standalone_archives')){ flexo_standalone_archives(); } ?&gt;</code>

  <p><?php _e('The code will output the nested archive lists into the HTML at that point in the theme. JavaScript automatically attached to the pages generated by WordPress will make the lists expand and collapse.', 'flexo-archives'); ?></p>

    <fieldset>
      <legend><?php _e('Standalone Function Options', 'flexo-archives'); ?></legend>
      <p><label for="flexo-standalone"><input type="checkbox" class="checkbox" id="flexo-standalone" name="flexo-standalone" <?php echo $standalone; ?>/> <?php _e('enable standalone theme function', 'flexo-archives'); ?></label></p>
      <p><label for="flexo-count"><input type="checkbox" class="checkbox" id="flexo-count" name="flexo-count" <?php echo $count; ?>/> <?php _e('include monthly post counts in lists', 'flexo-archives'); ?></label></p>
      </legend>
    </fieldset>

    <input type="submit" name="flexo-submit" class="button-primary" value="<?php _e('Submit', 'flexo-archives'); ?>"/>
  </form>
  </div>
</div>
<?php
    }

    /**
     * Handle widget configuration
     */
    function widget_control ($args) {
        $options = $newoptions = $this->get_opts();

        if (is_array($_POST) && !empty($_POST['flexo-archives']) && 
             check_admin_referer('flexo-archives-widget-options') )
        {
            foreach ($_POST['flexo-archives'] as $wnum => $vals) {
                if (empty($vals) && isset($newoptions[$wnum]))
                    continue;

                if (!isset($newoptions[$wnum]) && $args['number'] == -1) {
                    $args['number'] = $wnum;
                    $newoptions['last_number'] = $wnum;
                }
                $newoptions[$wnum] = array($this->OPT_COUNT => isset($vals['flexo-count']),
                                           $this->OPT_WTITLE => strip_tags(stripslashes($vals['flexo-title']))
                                     );
            }

            if ($args['number'] == -1 && !empty($newoptions['last_number'])) {
                $args['number'] = $newoptions['last_number'];
                unset($newoptions['last_number']);
            }

            if ($options != $newoptions) {
                $options = $newoptions;
                $this->set_opts($options);
            }
        }

        $widget_num = ($args['number'] == -1) ? '%i%' : $args['number'];

        $count = $this->widget_count_enabled($widget_num) ? 'checked="checked"' : '';
        $title = $this->widget_title($widget_num);

        wp_nonce_field('flexo-archives-widget-options');
?>
  <p><label for="flexo-title"><?php _e('Title:', 'flexo-archives'); ?> <input style="width: 90%;" id="flexo-title" name="flexo-archives[<?php echo $widget_num; ?>][flexo-title]" type="text" value="<?php echo $title; ?>" /></label></p>
  <p style="text-align:right;margin-right:40px;"><label for="flexo-count"><?php _e('Show post counts', 'flexo-archives'); ?> <input class="checkbox" type="checkbox" <?php echo $count; ?> id="flexo-count" name="flexo-archives[<?php echo $widget_num; ?>][flexo-count]"/></label></p>
  <input type="hidden" id="flexo-submit" name="flexo-archives[<?php echo $widget_num; ?>][flexo-submit]" value="1" />
<?php
    }

    /**
     * Helper function that Adds rel="nofollow" to links in $text
     */
    function add_link_nofollow ($text) {
        return preg_replace_callback('|<a (.*?)>|i', array(&$this, 
                                      'add_link_nofollow_cb'), $text);
    }

    /**
     * Callback used to add rel="nofollow" to HTML A element.
     */
    function add_link_nofollow_cb ($matches) {
        $text = $matches[1];
        $text = str_replace(array(' rel="nofollow"', " rel='nofollow'"), '', $text);
        return "<a $text rel=\"nofollow\">";
    }

    /**
     * Helper function to get yearly post totals.
     *
     * Returns: An array. Array keys are years, and array values are the 
     * number of posts posted that year. The array is empty on failure.
     */
    function year_post_totals () {
        global $wpdb;

        // Support archive filters other plugins may have inserted
        $join = apply_filters('getarchives_join', '');
        $default_where = "WHERE post_type='post' AND post_status='publish'";
        $where = apply_filters('getarchives_where', $default_where);

        $totals_qstr  = "SELECT YEAR(post_date) AS `year`, ";
        $totals_qstr .= "COUNT(YEAR(post_date)) AS `total` ";
        $totals_qstr .= "FROM $wpdb->posts ";
        $totals_qstr .= $join . ' ';
        $totals_qstr .= $where;
        $totals_qstr .= " GROUP BY YEAR(post_date)";

        $totals_array = array();

        $totals_result = $wpdb->get_results($totals_qstr);
        if ($totals_result) {
            foreach ($totals_result as $a_result) {
                $totals_array[$a_result->year] = $a_result->total;
            }
        }

        return $totals_array;
    }

    /**
     * Helper function to print first bit of year list
     *
     * Args:
     *  $year: String. The year we're creating a start tag for.
     *  $totals: Array. The array produced by year_post_totals().
     *
     * Returns: An HTML fragment that starts the unordered list for
     * the given year.
     */
    function year_start_tags ($year = '', $totals = null) {
        $link_title = __('Year %s archives', 'flexo-archives');
        $nofollow   = $this->nofollow_enabled();

        // Ugly strings used in building the tags
        $year_start = '<ul><li><a href="%s" class="flexo-link" ';
        $year_start .= 'title="' . $link_title . '" >';
        $year_start .= (is_null($totals)) ? '%s' : "%s ($totals[$year])";
        $year_start .= '</a><ul class="flexo-list">';

        $link = sprintf($year_start, get_year_link($year), $year, $year);

        if ($nofollow) {
            $link = $this->add_link_nofollow($link);
        }

        return $link;
    }

    /**
     * Perform database query to get archives.  Archives are sorted in
     * *descending* order or year and *ascending* order of month
     *
     * Returns: result of query if successful, null otherwise
     */
    function query_archives () {
        global $wpdb;

        // Support archive filters other plugins may have inserted
        $join = apply_filters('getarchives_join', '');
        $default_where = "WHERE post_type='post' AND post_status='publish'";
        $where = apply_filters('getarchives_where', $default_where);

        // Query string
        $qstring = "SELECT DISTINCT YEAR(post_date) AS `year`,";
        $qstring .= " MONTH(post_date) AS `month`,";
        $qstring .= " count(ID) AS posts FROM  $wpdb->posts ";
        $qstring .= $join . ' ';
        $qstring .= $where;
        $qstring .= " GROUP BY YEAR(post_date), MONTH(post_date)";
        $qstring .= " ORDER BY YEAR(post_date) DESC, MONTH(post_date) ";
        $qstring .= $this->month_order();

        // Query database
        $flexo_results = $wpdb->get_results($qstring);

        // Check we actually got results
        if ($flexo_results) {
            return $flexo_results;
        } else {
            // No results or database error
            return null;
        }
    }

    /**
     * Constructs the nested unordered lists from data obtained from
     * the database.
     *
     * Args:
     *  $count: Boolean. Show per-month post counts.
     *  $total: Boolean. Show per-year post totals.
     *
     * Returns: An HTML fragment containing the archives lists
     */
    function build_archives_list ($count = false, $total = false) {
        global $wp_locale;
        $list_html = "";
        $totals_array = null;

        // Whether we should add rel="nofollow"
        $nofollow = $this->nofollow_enabled();

        // If yearly totals are enabled, get totals from database
        if ($total) {
            $totals_array = $this->year_post_totals();
        }

        // Get archives from database
        $results = $this->query_archives();

        // Log and retrun an error if query failed.
        if (is_null($results)) {
            $error_str = __('Database query unexpectedly failed.', 'flexo-archives');
            error_log(__('ERROR: ', 'flexo-archives') . __FILE__ . 
                  '(' . __LINE__ . ') ' .  $error_str);
            return "<p>$error_str</p>";
        }
        
        // Detect year change in loop.
        $a_year = '0';

        // Loop over results and print our archive lists
        foreach ($results as $a_result) {
            $before = '';
            $after = '';

            if ($a_result->year != $a_year) {
                // If not first iteration, close previous list
                if ($a_year != '0')
                    $list_html .= '</ul></li></ul>';

                $a_year = $a_result->year;
                $list_html .= $this->year_start_tags($a_result->year, $totals_array) . "\n";
            }

            $url = get_month_link($a_result->year, $a_result->month);
            $text = sprintf(__('%1$s'), $wp_locale->get_month($a_result->month));

            // Append number of posts in month, if they want it
            if ($count)
                $after = '&nbsp;(' . $a_result->posts . ')' . $after;

            $list_html .= get_archives_link($url, $text, 'html', $before, $after);
            if ($nofollow) {
                $list_html = $this->add_link_nofollow($list_html);
            }
        }

        // Close the last list
        $list_html .= '</ul></li></ul>';

        return $list_html;
    }

    /**
     * Output the archive list as a sidebar widget
     *
     * Arguments: $args array passed by WordPress's widgetized
     * sidebar code
     */
    function widget_archives ($args) {
        extract($args);

        // Fetch widget options
        $widget_num = (int) str_replace('flexo-archives-', '', $widget_id);
        $title = $this->widget_title($widget_num);
        $count = $this->widget_count_enabled($widget_num);
        $yearly_totals = $this->yearly_total_enabled();

        // Print out the title
        echo $before_widget; 
        echo $before_title . $title . $after_title;

        // Print out the archive list
        echo $this->build_archives_list($count, $yearly_totals);

        // Close out the widget
        echo $after_widget; 
    }

    /**
     * Attach JavaScript to normal pages if the standalone archives
     * function is enabled
     */
    function enqueue_standalone_scripts () {
        if (!is_admin() && $this->standalone_enabled()) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('flexo', $this->script_url(), array('jquery'),
                      '2.0');
        }
    }

    /**
     * Helper function that prints the url for our javascript
     */
    function script_url () {
        $url = WP_PLUGIN_URL . '/' . $this->flexo_dir . '/';

        if ($this->animation_enabled()) {
            $url .= $this->FLEXO_ANIM_JS;
        } else {
            $url .= $this->FLEXO_JS;
        }

        return $url;
    }

    /**
     * Register our widgets with the widget system
     */
    function register_widgets () {
        $name = __('Flexo Archives', 'flexo-archives');
        $desc = __('Your archives as an expandable list of years', 'flexo-archives');
        $widget_cb = array(&$this, 'widget_archives');
        $control_cb = array(&$this, 'widget_control');
        $css_class = 'flexo';
        $id_base = 'flexo-archives';

        // Tell the dynamic sidebar about our widget(s)
        if (function_exists('wp_register_sidebar_widget')) {
            $widget_ops = array('class' => $css_class, 'description' => $desc);
            $control_ops = array('width' => 250, 'height' => 100, 'id_base' => $id_base);
            $id = 'flexo-archives'; // Never never never translate an id

            $widgets_registered = 0;
            foreach (array_keys($this->options) as $widget_num) {
                if (!is_numeric($widget_num)) 
                    continue;

                $id_str = $id . '-' . $widget_num;
                wp_register_sidebar_widget($id_str, $name, $widget_cb, 
                                           $widget_ops, 
                                           array('number' => $widget_num));
                wp_register_widget_control($id_str, $name, $control_cb, 
                                           $control_ops, 
                                           array('number' => $widget_num));
                $widgets_registered++;
            }

            if ($widgets_registered == 0) {
                $id_str = $id . '-' . '1';
                wp_register_sidebar_widget($id_str, $name, $widget_cb, 
                                           $widget_ops,
                                           array('number' => '1'));
                wp_register_widget_control($id_str, $name, $control_cb,
                                           $control_ops,
                                           array('number' => '1'));
            }
        }

        // Add CSS and JavaScript to header if we're active
        if (is_active_widget(array(&$this, 'widget_archives'))) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('flexo', $this->script_url(), array('jquery'), '2.0');
        }
    }

    /**
     * Uninstall Function. Deletes plugin configuration from the
     * database.
     */
    function uninstall () {
        $options = $this->get_opts();

        if (is_array($options)) {
            delete_option($this->OPTIONS_NAME);
        }
    }
}

/**
 * Output the archive lists as a standalone function, for users
 * can't or don't want to use the widget.
 */
function flexo_standalone_archives () {
    $archives = new FlexoArchives();

    if ($archives->standalone_enabled()) {
        echo $archives->build_archives_list(
                            $archives->standalone_count_enabled(),
                            $archives->yearly_total_enabled()
                        );
    }
}

$flexo_archives = & new FlexoArchives();
?>
