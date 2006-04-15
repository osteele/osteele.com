<?php

/************************************************************************************
*							 DO NOT MODIFY THIS FILE !
************************************************************************************/

if (!function_exists('themetoolkit')) {
	function themetoolkit($theme='',$array='',$file='') {
		global ${$theme};
		if ($theme == '' or $array == '' or $file == '') {
			die ('No theme name, theme option, or parent defined in Theme Toolkit');
		}
		${$theme} = new ThemeToolkit($theme,$array,$file);
	}
}

if (!class_exists('ThemeToolkit')) {
	class ThemeToolkit{

		var $option, $infos;

		function ThemeToolkit($theme,$array,$file){
			
			global $wp_version;
			// is it WP 2.0+ and do we have plugins like "../themes/foo/functions.php" running ?
			if ( $wp_version >= 2 and count(@preg_grep('#^\.\./themes/[^/]+/functions.php$#', get_settings('active_plugins'))) > 0 ) {
				wp_cache_flush();
				$this->upgrade_toolkit();
			}
			
			$this->infos['path'] = '../themes/' . basename(dirname($file));

			/* Create some vars needed if an admin menu is to be printed */
			if ($array['debug']) {
				if ((basename($file)) == $_GET['page']) $this->infos['debug'] = 1;
				unset($array['debug']);
			}
			if ((basename($file)) == $_GET['page']){
				$this->infos['menu_options'] = $array;
				$this->infos['classname'] = $theme;
			}
			$this->option=array();

			/* Check this file is registered as a plugin, do it if needed */
			$this->pluginification();

			/* Get infos about the theme and particularly its 'shortname'
			 * which is used to name the entry in wp_options where data are stored */
			$this->do_init();

			/* Read data from options table */
			$this->read_options();

			/* Are we in the admin area ? Add a menu then ! */
			$this->file = $file;
			add_action('admin_menu', array(&$this, 'add_menu'));
		}


		/* Add an entry to the admin menu area */
		function add_menu() {
			global $wp_version;
			if ( $wp_version >= 2 ) {
				$level = 'edit_themes';
			} else {
				$level = 9;
			}
			//add_submenu_page('themes.php', 'Configure ' . $this->infos[theme_name], $this->infos[theme_name], 9, $this->infos['path'] . '/functions.php', array(&$this,'admin_menu'));
			add_theme_page('Configure ' . $this->infos['theme_name'], 'Configure ' . $this->infos['theme_name'], 'edit_themes', basename($this->file), array(&$this,'admin_menu'));
			/* Thank you MCincubus for opening my eyes on the last parameter :) */
		}

		/* Get infos about this theme */
		function do_init() {
			$themes = get_themes();
			$shouldbe= basename($this->infos['path']);
			foreach ($themes as $theme) {
				$current= basename($theme['Template Dir']);
				if ($current == $shouldbe) {
					if (get_settings('template') == $current) {
						$this->infos['active'] = TRUE;
					} else {
						$this->infos['active'] = FALSE;
					}
				$this->infos['theme_name'] = $theme['Name'];
				$this->infos['theme_shortname'] = $current;
				$this->infos['theme_site'] = $theme['Title'];
				$this->infos['theme_version'] = $theme['Version'];
				$this->infos['theme_author'] = preg_replace("#>\s*([^<]*)</a>#", ">\\1</a>", $theme['Author']);
				}
			}
		}

		/* Read theme options as defined by user and populate the array $this->option */
		function read_options() {
			$options = get_option('theme-'.$this->infos['theme_shortname'].'-options');
			$options['_________junk-entry________'] = 'ozh is my god';
			foreach ($options as $key=>$val) {
				$this->option["$key"] = stripslashes($val);
			}
			array_pop($this->option);
			return $this->option;
			/* Curious about this "junk-entry" ? :) A few explanations then.
			 * The problem is that get_option always return an array, even if
			 * no settings has been previously saved in table wp_options. This
			 * junk entry is here to populate the array with at least one value,
			 * removed afterwards, so that the foreach loop doesn't go moo. */
		}

		/* Write theme options as defined by user in database */
		function store_options($array) {
			update_option('theme-'.$this->infos['theme_shortname'].'-options','');
			if (update_option('theme-'.$this->infos['theme_shortname'].'-options',$array)) {
				return "Options successfully stored";
			} else {
				return "Could not save options !";
			}
		}

		/* Delete options from database */
		  function delete_options() {
			/* Remove entry from database */
			delete_option('theme-'.$this->infos['theme_shortname'].'-options');
			/* Unregister this file as a plugin (therefore remove the admin menu) */
			$this->depluginification();
			/* Revert theme back to Kubrick if this theme was activated */
			if ($this->infos['active']) {
				update_option('template', 'default');
				update_option('stylesheet', 'default');
				do_action('switch_theme', 'Default');
			}
			/* Go back to Theme admin */
			print '<meta http-equiv="refresh" content="0;URL=themes.php?activated=true">';
			echo "<script> self.location(\"themes.php?activated=true\");</script>";
			exit;
		}

		/* Check if the theme has been loaded at least once (so that this file has been registered as a plugin) */
		function is_installed() {
			global $wpdb;
			$where = 'theme-'.$this->infos['theme_shortname'].'-options';
			$check = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->options WHERE option_name = '$where'");
			if ($check == 0) {
				return FALSE;
			} else {
				return TRUE;
			}
		}

		/* Theme used for the first time (create blank entry in database) */
		function do_firstinit() {
			global $wpdb;
			$options = array();
			foreach(array_keys($this->option) as $key) {
				$options["$key"]='';
			}
			add_option('theme-'.$this->infos['theme_shortname'].'-options',$options, 'Options for theme '.$this->infos['theme_name']);
			return "Theme options added in database (1 entry in table '". $wpdb->options ."')";
		}

		/* The mother of them all : the Admin Menu printing func */
		function admin_menu () {
			global $cache_settings, $wpdb;

			/* Process things when things are to be processed */
			if (@$_POST['action'] == 'store_option') {
				unset($_POST['action']);
				$msg = $this->store_options($_POST);
			} elseif (@$_POST['action'] == 'delete_options') {
				$this->delete_options();
			} elseif (!$this->is_installed()) {
				$msg = $this->do_firstinit();
			}

			if (@$msg) print "<div class='updated'><p><b>" . $msg . "</b></p></div>\n";

			echo '<div class="wrap"><h2>Thank you !</h2>';
			echo '<p>Thank you for installing ' . $this->infos['theme_site'] . ', a theme for Wordpress. This theme was made by '.$this->infos['theme_author'].'. </p>';

			if (!$this->infos['active']) { /* theme is not active */
				echo '<p>(Please note that this theme is currently <strong>not activated</strong> on your site as the default theme.)</p>';
			}

			$cache_settings = '';
			$check = $this->read_options();
			
			echo "<h2>Configure ${$this->infos['theme_name']} </h2>";
			echo '<p>This theme allows you to configure some variables to suit your blog, which are :</p>
			<form action="" method="post">
			<input type="hidden" name="action" value="store_option">
			<table cellspacing="2" cellpadding="5" border="0" width=100% class="editform">';

			/* Print form, here comes the fun part :) */
			foreach ($this->infos['menu_options'] as $key=>$val) {
				$items='';
				preg_match('/\s*([^{#]*)\s*({([^}]*)})*\s*([#]*\s*(.*))/', $val, $matches);
				if ($matches[3]) {
					$items = split("\|", $matches[3]);
				}

				print "<tr valign='top'><th scope='row' width='33%'>\n";
				if (@$items) {
					$type = array_shift($items);
					switch ($type) {
					case 'separator':
						print '<h3>'.$matches[1]."</h3></th>\n<td>&nbsp;</td>";
						break;
					case 'radio':
						print $matches[1]."</th>\n<td>";
						while ($items) {
							$v=array_shift($items);
							$t=array_shift($items);
							$checked='';
							if ($v == $this->option[$key]) $checked='checked';
							print "<label for='${key}${v}'><input type='radio' id='${key}${v}' name='$key' value='$v' $checked /> $t</label>";
							if (@$items) print "<br />\n";
						}
						break;
					case 'textarea':
						$rows=array_shift($items);
						$cols=array_shift($items);
					print "<label for='$key'>".$matches[1]."</label></th>\n<td>";
						print "<textarea name='$key' id='$key' rows='$rows' cols='$cols'>" . $this->option[$key] . "</textarea>";
						break;
					case 'checkbox':
						print $matches[1]."</th>\n<td>";
						while ($items) {
							$k=array_shift($items);
							$v=array_shift($items);
							$t=array_shift($items);
							$checked='';
							if ($v == $this->option[$k]) $checked='checked';
							print "<label for='${k}${v}'><input type='checkbox' id='${k}${v}' name='$k' value='$v' $checked /> $t</label>";
							if (@$items) print "<br />\n";
						}
						break;
					}
				} else {
					print "<label for='$key'>".$matches[1]."</label></th>\n<td>";
					print "<input type='text' name='$key' id='$key' value='" . $this->option[$key] . "' />";
				}

				if ($matches[5]) print '<br/>'. $matches[5];
				print "</td></tr>\n";
			}
			echo '</table>';
			echo '
			<input type="hidden" id="header_bgclr" name="header_bgclr" value="' . $this->option['header_bgclr'] . '" />
			<input type="hidden" id="header_txtclr" name="header_txtclr" value="' . $this->option['header_txtclr'] . '" />
			<input type="hidden" id="menulinks_lnktxtclr" name="menulinks_lnktxtclr" value="' . $this->option['menulinks_lnktxtclr'] . '" />
			<input type="hidden" id="menulinks_lnkhvrclr" name="menulinks_lnkhvrclr" value="' . $this->option['menulinks_lnkhvrclr'] . '" />
			<input type="hidden" id="menulinks_lnkhvrbgclr" name="menulinks_lnkhvrbgclr" value="' . $this->option['menulinks_lnkhvrbgclr'] . '" />
			<input type="hidden" id="menusections_bgclr" name="menusections_bgclr" value="' . $this->option['menusections_bgclr'] . '" />
			<input type="hidden" id="menusections_txtclr" name="menusections_txtclr" value="' . $this->option['menusections_txtclr'] . '" />
			<input type="hidden" id="menusections_hdgclr" name="menusections_hdgclr" value="' . $this->option['menusections_hdgclr'] . '" />
			<input type="hidden" id="menusections_lnktxtclr" name="menusections_lnktxtclr" value="' . $this->option['menusections_lnktxtclr'] . '" />
			<input type="hidden" id="menusections_lnkhvrclr" name="menusections_lnkhvrclr" value="' . $this->option['menusections_lnkhvrclr'] . '" />
			<input type="hidden" id="menusections_lnkhvrbgclr" name="menusections_lnkhvrbgclr" value="' . $this->option['menusections_lnkhvrbgclr'] . '" />
			<input type="hidden" id="maincontent_bgclr" name="maincontent_bgclr" value="' . $this->option['maincontent_bgclr'] . '" />
			<input type="hidden" id="maincontent_txtclr" name="maincontent_txtclr" value="' . $this->option['maincontent_txtclr'] . '" />
			<input type="hidden" id="maincontent_hdgclr" name="maincontent_hdgclr" value="' . $this->option['maincontent_hdgclr'] . '" />
			<input type="hidden" id="maincontent_lnktxtclr" name="maincontent_lnktxtclr" value="' . $this->option['maincontent_lnktxtclr'] . '" />
			<input type="hidden" id="maincontent_lnkhvrclr" name="maincontent_lnkhvrclr" value="' . $this->option['maincontent_lnkhvrclr'] . '" />
			<input type="hidden" id="maincontent_lnkhvrbgclr" name="maincontent_lnkhvrbgclr" value="' . $this->option['maincontent_lnkhvrbgclr'] . '" />
			<input type="hidden" id="datestags_bgclr" name="datestags_bgclr" value="' . $this->option['datestags_bgclr'] . '" />
			<input type="hidden" id="datestags_txtclr" name="datestags_txtclr" value="' . $this->option['datestags_txtclr'] . '" />
			<input type="hidden" id="datestags_lnkhvrtxtclr" name="datestags_lnkhvrtxtclr" value="' . $this->option['datestags_lnkhvrtxtclr'] . '" />
			<input type="hidden" id="datestags_lnkhvrbgclr" name="datestags_lnkhvrbgclr" value="' . $this->option['datestags_lnkhvrbgclr'] . '" />
			<input type="hidden" id="comments_rplyfrmbgclr" name="comments_rplyfrmbgclr" value="' . $this->option['comments_rplyfrmbgclr'] . '" />
			<input type="hidden" id="comments_rplyfrmhdgtxtclr" name="comments_rplyfrmhdgtxtclr" value="' . $this->option['comments_rplyfrmhdgtxtclr'] . '" />
			<input type="hidden" id="comments_rplyfrmtxtclr" name="comments_rplyfrmtxtclr" value="' . $this->option['comments_rplyfrmtxtclr'] . '" />
			<input type="hidden" id="comments_rplybgclr" name="comments_rplybgclr" value="' . $this->option['comments_rplybgclr'] . '" />
			<input type="hidden" id="comments_rplytxtclr" name="comments_rplytxtclr" value="' . $this->option['comments_rplytxtclr'] . '" />
			<input type="hidden" id="comments_rplylnktxtclr" name="comments_rplylnktxtclr" value="' . $this->option['comments_rplylnktxtclr'] . '" />
			<input type="hidden" id="comments_rplylnkhvrtxtclr" name="comments_rplylnkhvrtxtclr" value="' . $this->option['comments_rplylnkhvrtxtclr'] . '" />
			<input type="hidden" id="comments_rplylnkhvrbgclr" name="comments_rplylnkhvrbgclr" value="' . $this->option['comments_rplylnkhvrbgclr'] . '" />
			<input type="hidden" id="footer_bgclr" name="footer_bgclr" value="' . $this->option['footer_bgclr'] . '" />
			<input type="hidden" id="footer_txtclr" name="footer_txtclr" value="' . $this->option['footer_txtclr'] . '" />
			<input type="hidden" id="footer_hdgclr" name="footer_hdgclr" value="' . $this->option['footer_hdgclr'] . '" />
			<input type="hidden" id="footer_lnktxtclr" name="footer_lnktxtclr" value="' . $this->option['footer_lnktxtclr'] . '" />
			<input type="hidden" id="footer_lnkhvrtxtclr" name="footer_lnkhvrtxtclr" value="' . $this->option['footer_lnkhvrtxtclr'] . '" />
			<input type="hidden" id="footer_lnkhvrbgclr" name="footer_lnkhvrbgclr" value="' . $this->option['footer_lnkhvrbgclr'] . '" />
			
			
			<p class="submit"><input type="submit" value="Save Options" /></p>
			</form>';

			if ($this->infos['debug'] and $this->option) {
				$g = '<span style="color:#006600">';
				$b = '<span style="color:#0000CC">';
				$o = '<span style="color:#FF9900">';
				$r = '<span style="color:#CC0000">';
				echo '<h2>Programmer\'s corner</h2>';
				echo '<p>The array <em>$'. $this->infos['classname'] . '->option</em> is actually populated with the following keys and values :</p>
				<p><pre class="updated">';
				$count = 0;
				foreach ($this->option as $key=>$val) {
					$val=str_replace('<','&lt;',$val);
					if ($val) {
						print '<span class="ttkline">'.$g.'$'.$this->infos['classname'].'</span>'.$b.'-></span>'.$g.'option</span>'.$b.'[</span>'.$g.'\'</span>'.$r.$key.'</span>'.$g.'\'</span>'.$b.']</span>'.$g.' = "</span>'. $o.$val.'</span>'.$g."\"</span></span>\n";
						$count++;
					}
				}
				if (!$count) print "\n\n";
				echo '</pre><p>To disable this report (for example before packaging your theme and making it available for download), remove the line "&nbsp;<em>\'debug\' => \'debug\'</em>&nbsp;" in the array you edited at the beginning of this file.</p>';
			}

			echo '<h2>Delete Theme options</h2>
			<p>To completely remove these theme options from your database (reminder: they are all stored in a single entry, in Wordpress options table <em>'. $wpdb->options. '</em>), click on
			the following button. You will be then redirected to the <a href="themes.php">Themes admin interface</a>';
			if ($this->infos['active']) {
				echo ' and the Default theme will have been activated';
			}
			echo '.</p>
			<p><strong>Special notice for people allowing their readers to change theme</strong> (i.e. using a Theme Switcher on their blog)<br/>
			Unless you really remove the theme files from your server, this theme will still be available to users, and therefore will self-install again as soon as someone selects it. Also, all custom variables as defined in the above menu will be blank, this could lead to unexpected behaviour.
			Press "Delete" only if you intend to remove the theme files right after this.</p>
			<form action="" method="post">
			<input type="hidden" name="action" value="delete_options">
			<p class="submit"><input type="submit" value="Delete Options" onclick="return confirm(\'Are you really sure you want to delete ?\');"/></p>
			</form>';
			
			ob_start(array(&$this,'footercut'));

			echo '<h2>Credits</h2>';
			echo '<p>'.$this->infos['theme_site'].' has been designed and programmed by '.$this->infos['theme_author'].'. ';
			echo 'This administration menu uses <a href="http://frenchfragfactory.net/ozh/my-projects/wordpress-theme-toolkit-admin-menu/" title="Wordpress Theme Toolkit : create a admin menu for your own theme as easily as editing 3 lines">Wordpress Theme Toolkit</a> by <a href="http://frenchfragfactory.net/ozh/" title="planetOzh">Ozh</a>. And everything was made possible thanks to <a href="http://wordpress.org/" title="Best. Blogware. Ever.">Wordpress</a>.</p>
			</div>';
		}

		/* Make this footer part of the real #footer DIV of Wordpress admin area */
		function footercut($string) {
			return preg_replace('#</div><!-- footercut -->.*<div id="footer">#m', '', $string);
		}


		/***************************************
		 * Self-Pluginification (TM)(R)(C) . Â© Â®
		 *
		 * The word "Self-Pluginification" and
		 * any future derivatives are licensed,
		 * patented and trademarked under the
		 * terms of the OHYEAHSURE agreement.
		 * Hmmmmmkey ? Fine.
		 **************************************/
		function pluginification () {
			global $wp_version;
			if ($wp_version<2) {		
				$us = $this->infos['path'].'/functions.php';
				$them = get_settings('active_plugins');
				/* Now, are we members of the PPC (Plugins Private Club) yet ? */
				if (!in_array($us,$them)) {
					/* No ? Jeez, claim member card ! */
					$them[]=$us;
					update_option('active_plugins',$them); 
					/* Wow. We're l33t now. */ 
					return TRUE; 
				} else { 
					return FALSE; 
				} 
			}
		} 
		 
		/*************************************** 
		 * De-Pluginification (TM)(R)(C) . Â© Â® 
		 * 
		 * Same legal notice. It's not that I 
		 * really like it, but my lawyer asked 
		 * for it. I swear. 
		 **************************************/ 
		function depluginification () {
			global $wp_version;
			if ($wp_version<2) {
				$us = $this->infos['path'].'/functions.php';
				$them = get_settings('active_plugins');
				if (in_array($us,$them)) {
					$here = array_search($us,$them);
					unset($them[$here]);
					update_option('active_plugins',$them);
					return TRUE;
				} else {
					return FALSE;
				}
			}
		}

		/***************************************
		 * Really, the whole plugin management
		 * system is really neat in WP, and very
		 * easy to use.
		 **************************************/

		/* Clean plugins lists in order to work with Wordpress 2.0 */
		function upgrade_toolkit () {
			$plugins=get_settings('active_plugins');
			$delete=@preg_grep('#^\.\./themes/[^/]+/functions.php$#', $plugins);
			$result=array_diff($plugins,$delete);
			$temp = array();
			foreach($result as $item) $temp[]=$item;
			$result = $temp;
			update_option('active_plugins',$result);
			wp_cache_flush;
		}

	}
}

?>
