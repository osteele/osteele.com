<?php

/**
 * @note Still need to replicate/adapt error message display from /wp-admin/plugins.php
 */

$this->test_interface('header');

// PLUGINS LIST

$target_script = 'plugins.php';

if ( !$this->iscompat )
	echo '<h2 class="accessible">' . __('Plugins') . "</h2>\n";

echo '<form id="pluginslistmenu" class="panel" title="' . __('Plugins') . '" action="' . $target_script . '" method="get"';
if ( $this->iscompat ) echo ' selected="true"';
echo ">\n";

if ( current_user_can('activate_plugins') ) {

	/**
	 * @note Options cleanup and sanity check is already done by the standard
	 * WP admin before the process and data are handed to us.  The data is also
	 * available to us in the same way, no need for double querying.
	 */
	
	global $current_plugins, $plugins; // from /wp-admin/plugins.php, no need for double querying

	$plugins_count = count($plugins);

	if ( isset($_GET['error']) ) {
		$error_plugin = ( isset($_GET['plugin']) ) ? ' (' . strip_tags($_GET['plugin']) . ')' : '';
		echo '<fieldset class="pluginerror"><p class="pluginmeta">' . __('Plugin could not be activated because it triggered a <strong>fatal error</strong>.') . $error_plugin . "</p></fieldset>\n";
	} elseif ( isset($_GET['activate']) ) {
		echo '<fieldset class="pluginmessage"><p class="pluginmeta">' . __('Plugin <strong>activated</strong>.') . "</p></fieldset>\n";
	} elseif ( isset($_GET['deactivate']) ) {
		echo '<fieldset class="pluginmessage"><p class="pluginmeta">' . __('Plugin <strong>deactivated</strong>.') . "</p></fieldset>\n";
	}
	// @note Not sure if we want "disable all plugins", since it also disables wphone (see other note after foreach)
	/* elseif (isset($_GET['deactivate-all'])) {
		echo '<fieldset><p class="pluginmeta pluginmessage">' . __('All plugins <strong>deactivated</strong>.') . "</p></fieldset>\n";
	} */


	if ( !empty($plugins) ) {

		$link_pattern = '<a';
		$link_replace = '<a ' . $this->htmltarget('_blank', TRUE) . ' ';

		foreach($plugins as $plugin_file => $plugin_data) {
			if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
				$toggle_url = wp_nonce_url($target_script . "?action=deactivate&amp;plugin=$plugin_file", 'deactivate-plugin_' . $plugin_file);
				$toggle_text = __('Deactivate');
				$toggled = 'true';
				$plugin_data['Title'] = "<strong>{$plugin_data['Title']}</strong>";
			} else {
				$toggle_url = wp_nonce_url($target_script . "?action=activate&amp;plugin=$plugin_file", 'activate-plugin_' . $plugin_file);
				$toggle_text = __('Activate');
				$toggled = 'false';
			}
	
			// @note No need for as many tags allowed in mobile version
			$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()), 'strong' => array());
	
			// Sanitize all displayed data
			$plugin_data['Title']       = wp_kses($plugin_data['Title'], $plugins_allowedtags);
			$plugin_data['Version']     = wp_kses($plugin_data['Version'], $plugins_allowedtags);
			$plugin_data['Description'] = wp_kses($plugin_data['Description'], $plugins_allowedtags);
			$plugin_data['Author']      = wp_kses($plugin_data['Author'], $plugins_allowedtags);
			
			if ( $this->iscompat ) {
				$plugin_data['Title']       = str_replace($link_pattern, $link_replace, $plugin_data['Title']);
				$plugin_data['Version']     = str_replace($link_pattern, $link_replace, $plugin_data['Version']);
				$plugin_data['Description'] = str_replace($link_pattern, $link_replace, $plugin_data['Description']);
				$plugin_data['Author']      = str_replace($link_pattern, $link_replace, $plugin_data['Author']);
			}

			$identifier = 'plugin-'.str_replace('.', '_', $plugin_file);
			if ($this->iscompat) {
				$onclick = "WPhone.toggleElement('$identifier-container');";
				$active_button = ('true' == $toggled) ? 'grayButton' : 'whiteButton';
				$this->panel_button('button', $identifier.'-toggle', strip_tags($plugin_data['Title']), $onclick, $active_button);
			}
			
			$active_css = ('true' == $toggled) ? 'activeplugin' : '';
			echo '<fieldset id="' . $identifier . '-container" class="accessible '. $active_css .'">';
			
			echo '<div class="row">';
			echo '<label>' . $plugin_data['Title'] . '</label>';
			if ( FALSE == preg_match('/wphone.php$/',$plugin_file) ) {
				if ($this->iscompat) {
					echo '<div class="toggle" onclick="WPhone.togglePlugin(\''. $toggle_url .'\');" toggled="' . $toggled . '"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>' . "\n";
				} else {
					echo ' [<a href="' . $toggle_url . '"><strong>' . $toggle_text . '</strong></a>]';
				}
			}
			echo '</div>';

			echo '<div class="row"><p class="pluginmeta">V.' . $plugin_data['Version'] . ' - ' . $plugin_data['Author'] . ' - ' . $plugin_data['Description'] . '</p></div>';
			
			echo "</fieldset>\n";
		}
		
		// @note Not sure if we want "disable all plugins", since it also disables wphone
		// echo '<a href="' . wp_nonce_url('plugins.php?action=deactivate-all', 'deactivate-all') . '" class="grayButton" ' . $this->htmltarget('_self', TRUE) . '>' . __('Deactivate All Plugins') . "</a>\n";
	} else {
		echo '<fieldset class="pluginmessage"><p class="pluginmeta">' . __('Couldn&#8217;t open plugins directory or there are no plugins available.') . "</p></fieldset>\n";
	}
} else {
	echo '<fieldset class="pluginerror"><p class="pluginmeta">' . __('Access denied.', 'wphone') . "</p></fieldset>\n";
}

echo "</form>\n";

$this->test_interface('footer');

?>