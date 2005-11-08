<?php
/*
Spam Karma 2.0
Copyright 2005 - drDave

SPAM KARMA 2 ("THE SOFTWARE") IS PROVIDED "AS IS",
WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

define ("sk2_kPluginFolder", dirname(__FILE__) . "/sk2_plugins/");
define ("sk2_kCoreVersion", 2);
define ("sk2_kVersion", 0);
define ("sk2_kRelease", "final");
if (isset($table_prefix))
	define ("sk2_kSpamTable", $table_prefix . "sk2_spams");
else
	define ("sk2_kSpamTable", sk2_table_prefix . "sk2_spams");

require_once(dirname(__FILE__) . "/sk2_util_class.php");
require_once(dirname(__FILE__) . "/sk2_plugin_class.php");
require_once(dirname(__FILE__) . "/sk2_comment_class.php");
require_once(dirname(__FILE__) . "/sk2_blacklist_class.php");
require_once(dirname(__FILE__) . "/sk2_functions.php");

global $sk2_plugin_array;
$sk2_plugin_array = 0;

class sk2_core
{
	var $cur_comment = 0;
	var $post_proc = false;
	var $plugins = array();
	var $version = sk2_kCoreVersion;
	
	function sk2_core($comment_ID = 0, $post_proc = false, $load_plugins = true)
	{

		$this->set_post_proc($post_proc);
		if ($comment_ID)
			$this->load_comment($comment_ID);
		$this->plugins = array();		
		if ($load_plugins)
			$this->load_plugin_files();
	}
	
	function load_plugin_files()
	{
		global $sk2_plugin_array;
		if ($sk2_plugin_array)
		{
			$this->plugins = $sk2_plugin_array;
			return;
		}
		$plugin_files = sk2_get_file_list(sk2_kPluginFolder);
		foreach($plugin_files as $plugin)
		{
			include_once(sk2_kPluginFolder . $plugin);	
		}	
		$sk2_plugin_array = $this->plugins;
	}
	
	function load_comment($comment_ID)
	{
		if (! $comment_ID)
			return false;
		$this->cur_comment = new sk2_comment($comment_ID, $this->post_proc, $comment_sk_info);
		if ($this->post_proc)
			$comment_sk_info = $this->get_comment_sk_info();
		
		return ($this->cur_comment->ID > 0);
	}
	
	function set_post_proc($post_proc)
	{
		$this->post_proc = $post_proc;
	}
	function is_post_proc()
	{
		return $this->post_proc;
	}
	
	function process_comment($comment_ID = 0)
	{
		if ($comment_ID && ($comment_ID != @$this->cur_comment->ID))
			$this->cur_comment = new sk2_comment($comment_ID, $this->post_proc); // ? does PHP garbage collect? sure hope so...

		global $sk2_settings;
		$this->filter_comment();
		
		if ($bias = $sk2_settings->get_core_settings("general_bias"))
			$this->cur_comment->modify_karma($bias, "core", "Severity settings adjustment.");
		
		$this->treat_comment();
	
		$this->set_comment_sk_info();
	}
	
	function filter_comment ($comment_ID = 0)
	{
		if ($comment_ID && ($comment_ID != @$this->cur_comment->ID))
			$this->cur_comment = new sk2_comment($comment_ID, $this->post_proc); // ? does PHP garbage collect? sure hope so...

		if (! $this->cur_comment)
		{
			$this->log_msg("Cannot run <code>filter_comment</code>: no valid comment provided.", 9);
			return;
		}
		
		$this->log_msg("Running filters on comment ID " . $this->cur_comment->ID, 0);

		foreach ($this->plugins as $plugin)
		{
			if ($plugin[1]->is_filter()
				&& ($plugin[1]->is_enabled())
				&& ($this->post_proc
					||	(((int) $this->cur_comment->karma > $plugin[1]->skip_under)
						&& ((int) $this->cur_comment->karma < $plugin[1]->skip_above))))
					$plugin[1]->filter_this($this->cur_comment);
		}
	}
	
	function treat_comment ($comment_ID = 0)
	{
		if ($comment_ID && ($comment_ID != @$this->cur_comment->ID))
			$this->cur_comment = new sk2_comment($comment_ID, $this->post_proc);

		if (! $this->cur_comment)
		{
			$this->log_msg("Cannot run <code>treat_comment</code>: no valid comment provided.", 9);
			return;
		}

		$this->log_msg("Running treatments on comment ID " . $this->cur_comment->ID, 0);
				
		foreach ($this->plugins as $plugin)
			if ($plugin[1]->is_treatment()
				&& ($plugin[1]->is_enabled()))
				$plugin[1]->treat_this($this->cur_comment);
	}
	
	function form_insert ($post_ID = 0)
	{
		foreach ($this->plugins as $plugin)
			if ($plugin[1]->is_enabled())
				$plugin[1]->form_insert($post_ID);
	}
	
	
	function second_chance()
	{
		if (! $this->cur_comment)
			return false;

		$rem_attempts = (int) $this->cur_comment->remaining_attempts;
			
		if($rem_attempts  <= 0)
		{
			echo ("<div class=\"sk2_box\">Too many unlock attempts.</div>");
			return false;
		}
		
		if (! $this->cur_comment->can_unlock())
		{
			echo ("<div class=\"sk2_box\">This comment wasn't given a second chance.</div>");
			return false;
		}
		
		if (!empty ($_REQUEST['sk2_second_chance']))
		{
			$class = $_REQUEST['sk2_second_chance'];
			$this_key = 0;
			foreach ($this->cur_comment->unlock_keys as $id => $unlock_key)
				$this_key = $unlock_key;

			if ($this_key
				&& ($this_key['expire'] > time())
				&& ($my_plugin = $this->get_plugin($this_key['class']))
				&& $my_plugin->is_enabled())
			{
				
				echo "<div class=\"sk2_box\">";
				if ($my_plugin->treat_second_chance($this->cur_comment, $this_key['key']))
				{
					$this->cur_comment->post_proc = true; // just making sure we don't run into loops here
				//	if ($anubis = $this->get_plugin('sk2_anubis_plugin'))
				//		$anubis->treat_this($this->cur_comment);
					$this->treat_comment();
					$this->cur_comment->remaining_attempts = 0; // no more attempts
					
					if ($this->cur_comment->approved == '1')
					{
						echo "Thank you. Your comment has been approved.";
						if ( get_settings('comments_notify') )
							wp_notify_postauthor($this->cur_comment->ID, $this->cur_comment->type);
						//TODO redirect to comment page...
					}
					else
					{
						echo "Thank you. Your comment will be displayed as soon as it is approved by a moderator.";
						wp_notify_moderator($this->cur_comment->ID);
					}
				}
				else
				{
					$this->cur_comment->remaining_attempts = --$rem_attempts;
					if ($rem_attempts <= 0)
					{
						//if ($anubis = $this->get_plugin('sk2_anubis_plugin'))
						//	$anubis->treat_this($this->cur_comment);
						$this->treat_comment($this->cur_comment);
						echo "<span class=\"sk2_fail\">Too many missed attempts. Your comment's moderation has been confirmed. A log of your comment will be kept and presented to the blog admin upon his next log-on. Please contact him directly via e-mail regarding this problem.</span>";
					}
					else
					{
						echo "<span class=\"sk2_fail\">Sorry, bad luck on this one, cow-boy, try again. You have " . $rem_attempts . " attempt" . (($rem_attempts > 1) ? "s" : "") . " left.</span>";
					}
				}
				$this->set_comment_sk_info();
				echo "</div>";
			}
			else
			{
				echo ("<div class=\"sk2_box\">Can't use this unlock method.</div>");
				$this->cur_comment->remaining_attempts = 0;
				$this->set_comment_sk_info();
				return false;
			}
							
		}
		else
		{
			$i = 0;
			foreach($this->cur_comment->unlock_keys as $id => $unlock_key)
			{
				$which_plugin_obj = $this->get_plugin($unlock_key['class']);

				echo "<div class=\"sk2_box\">";

				if (! $which_plugin_obj)
				{
					echo "<strong>" . $unlock_key['class'] . " backup check unavailable: file cannot be found.</strong>";
					$this->log_msg("Cannot find 2nd chance plugin file: ". $unlock_key['class'] . ".", 9);
				}
				elseif (! $which_plugin_obj->is_enabled())
				{
					echo "<strong>" . $which_plugin_obj->name . ": plugin disabled.</strong>";
					$this->log_msg("2nd chance: " . $which_plugin_obj->name . " plugin disabled." , 6);
				}
				elseif ($unlock_key['expire'] < time())
				{
					echo "<strong>" . $which_plugin_obj->name . ": unlock key expired.</strong>";
					$this->log_msg("2nd chance: " . $which_plugin_obj->name . " unlock key expired." , 6);
				}
				else
				{
					$i++;
					echo "<form name=\"sk2_form_". $unlock_key['class'] . "\" id=\"sk2_form_". $unlock_key['class'] . "\ method=\"post\">";
					echo "<input type=\"hidden\" name=\"sk2_second_chance\" id=\"sk2_second_chance\" value=\"". $unlock_key['class'] . "\">";
					echo "<input type=\"hidden\" name=\"c_id\" id=\"c_id\" value=\"" . $this->cur_comment->ID . "\">";
					echo "<input type=\"hidden\" name=\"c_author\" id=\"c_author\" value=\"" .  $this->cur_comment->author_email . "\">";

					$which_plugin_obj->display_second_chance($this->cur_comment, $unlock_key['key']);
					echo "</form>";
				}
				
				echo "</div>";
			
			}
		
			if ($i <= 0)
				$this->cur_comment->remaining_attempts = 0;
		}
		
		$this->set_comment_sk_info();
	}
	
	
	function register_plugin($plugin_class, $priority = 5)
	{
		$i = 0;
		foreach ($this->plugins as $elem)
			if ($elem[0] <= $priority)
				$i++;

		$this->log_msg("Registering plugin: <i>" . $plugin_class . "</i>, priority: " . $priority);
		array_splice($this->plugins, $i, 0, array(array($priority, new $plugin_class(), $plugin_class)));
	}
	
	function output_UI()
	{
?>
	    <div class="wrap hide" id="sk2_settings_pane"><h2>Spam Karma 2 Settings</h2>
	    <form name="sk2_settings_form" id="sk2_settings_form" method="post">
		<fieldset class="options">
			<legend>General Settings</legend>
		<ul>
<?php
		global $sk2_settings;
		foreach($sk2_settings->core_defaults as $name => $format)
		{
			if (@$format['auto_draw'] != true)
				continue; // no UI
				
			echo "<li";
			if (@$format['advanced'])
				 echo " class=\"advanced\"";
			echo ">\n";
			if ($format['type'] == "check" || $format['type'] == "checkbox")
			{
				sk2_plugin::output_UI_input($name, "checkbox", $sk2_settings->get_core_settings($name));
				if (!empty($format['caption']))
					echo " " . $format['caption'];
			}
			else
			{
				if (! empty($format['caption']))
					echo $format['caption'] . " ";
				if ($format['type'] == "menu" || $format['type'] == "select")
					sk2_plugin::output_UI_menu($name, $format['options'], $sk2_settings->get_core_settings($name));
				else 
					sk2_plugin::output_UI_input($name, "text", $sk2_settings->get_core_settings($name), @$format['size']);
				
				if (! empty($format['after']))
					echo " " . $format['after'];
			}
			
			echo "</li>\n";
		}
			
?>	
		</ul></fieldset>
		    <p class="submit"><input type="submit" id="sk2_settings_save" name="sk2_settings_save" value="Save new settings"></p>
		<fieldset class="options">
			<legend>Filter Plugins Settings</legend>
<?php
		foreach ($this->plugins as $plugin)
			if ($plugin[1]->is_filter())
				$plugin[1]->output_plugin_UI();
?>
		</fieldset>
		<fieldset class="options">
			<legend>Treatment Plugins Settings</legend>
<?php
		foreach ($this->plugins as $plugin)
			if ($plugin[1]->is_treatment() && ! $plugin[1]->is_filter())
				$plugin[1]->output_plugin_UI();
?>
		</fieldset>
						
		    <p class="submit"><input type="submit" id="sk2_settings_save" name="sk2_settings_save" value="Save new settings"></p>
		</form></div>
<?php		
	
	}
	
	function update_SQL_schema()
	{
		global $sk2_settings;
		
		$mysql_updates =  $sk2_settings->get_core_settings("mysql_updates");
		
		if (@$mysql_updates['core'] < $this->version)
		{
			if ($this->update_core_SQL_schema (@$mysql_updates['core']))
			{
				$this->log_msg("Updated SQL schema for the core (to version: ".  $this->version . ")",  5);
				$mysql_updates['core'] = $this->version;
			}
			else
				$this->log_msg("Failed to update SQL schema for the core (to version: ".  $this->version . ")",  8);
		}	

		foreach ($this->plugins as $plugin)
		{
			$class_name = $plugin[2];
			if (@$mysql_updates[$class_name] < $plugin[1]->version)
			{
				if ($plugin[1]->update_SQL_schema (@$mysql_updates[$class_name]))
				{
					$this->log_msg("Updated SQL schema for plugin: <em>" .  $plugin[1]->name . "</em> (to version: ".  $plugin[1]->version . ")",  5);
					$mysql_updates[$class_name] = $plugin[1]->version;
				}
				else
					$this->log_msg("Failed to update SQL schema for plugin: <em>" .  $plugin[1]->name . "</em> (to version: ".  $plugin[1]->version . ")",  8);
			}
		}
		
		$sk2_settings->set_core_settings($mysql_updates, "mysql_updates");
	}
	
	function update_components()
	{
		global $sk2_settings;
		
		$version_updates =  $sk2_settings->get_core_settings("version_updates");
		
		if (@$version_updates['core'] < $this->version)
		{
			if ($this->update_core (@$version_updates['core']))
			{
				$this->log_msg("Initialized the core (to version: ".  $this->version . ")",  5);
				$version_updates['core'] = $this->version;
			}
			else
				$this->log_msg("Failed to initialize the core (to version: ".  $this->version . ")",  8);
		}	

		foreach ($this->plugins as $plugin)
		{
			if (@$version_updates[$plugin[2]] < $plugin[1]->version)
			{
				if ($plugin[1]->version_update (@$version_updates[$plugin[2]]))
				{
					$this->log_msg("Updated plugin: <em>" .  $plugin[1]->name . "</em> (to version: ".  $plugin[1]->version . ")",  5);
					$version_updates[$plugin[2]] = $plugin[1]->version;
				}
				else
					$this->log_msg("Failed to update plugin: <em>" .  $plugin[1]->name . "</em> (to version: ".  $plugin[1]->version . ")",  8);
			}
		}
		
		$sk2_settings->set_core_settings($version_updates, "version_updates");
	}
	
	function update_core ($cur_version)
	{
		// doing nothing right now
		return true;
	}
	
	function update_core_SQL_schema ($cur_version)
	{
		global $wpdb;
		$success = true;
		
		if ($cur_version == 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `" . sk2_kSpamTable . "` (
	  `id` int(11) NOT NULL auto_increment,
	  `comment_ID` int(11) NOT NULL default '0',
	  `karma` float(2) NOT NULL default '0',
	  `karma_cmts` text NOT NULL,
	  `last_mod` datetime NOT NULL default '0000-00-00 00:00:00',
	  `unlock_keys` tinytext NOT NULL,
	  `remaining_attempts` INT NOT  NULL,
		PRIMARY KEY  (`id`),
		KEY `comment_ID` (`comment_ID`)
	) TYPE=MyISAM;";
	
			$wpdb->query($query);
			if (mysql_error())
			{
				$this->log_msg(__("Could not create table: ") . sk2_kSpamTable . ".", 10, true);
				$success = false;
			}	
	
			$query = "CREATE TABLE IF NOT EXISTS `" . sk2_kLogTable . "` (
	`id` INT NOT  NULL  AUTO_INCREMENT ,
	`msg` TEXT NOT  NULL ,
	`component` TINYTEXT NOT  NULL ,
	`level` TINYINT NOT  NULL ,
	`ts` DATETIME NOT  NULL ,
	PRIMARY  KEY (  `id`  ) 
	) TYPE=MyISAM;";
		
			 $wpdb->query($query);
			if (mysql_error())
			{
				$this->log_msg(__("Could not create table: ") . sk2_kLogTable . ".", 10, true);
				$success = false;
			}	
			
			$query = "CREATE TABLE IF NOT EXISTS `". sk2_kBlacklistTable ."` (
	  `id` int(11) NOT NULL auto_increment,
	  `type` tinytext NOT NULL,
	  `value` text NOT NULL,
	  `score` int(11) NOT NULL default '0',
	  `trust` int(11) NOT NULL default '0',
	  `comments` text NOT NULL,
	  `added` datetime NOT NULL default '0000-00-00 00:00:00',
	  `added_by` tinytext NOT NULL,
	  `last_used` datetime NOT NULL default '0000-00-00 00:00:00',
	  `used_count` int(11) NOT NULL default '0',
	  `user_reviewed` enum('yes','no') NOT NULL default 'no',
	  PRIMARY KEY  (`id`)
	) TYPE=MyISAM ;";
		
			$wpdb->query($query);
			if (mysql_error())
			{
				$this->log_msg(__("Could not create table: ") . sk2_kBlacklistTable . ".", 10, true);
				$success = false;
			}	
		}
		elseif ($cur_version == 1)
		{
			$query = "ALTER TABLE `" . sk2_kSpamTable . "` ADD INDEX (  `comment_ID` )";

			$wpdb->query($query);
			if (mysql_error())
			{
				$this->log_msg(__("Could not alter table: ") . sk2_kBlacklistTable . ".", 10, true);
				$success = false;
			}
			else
			{
				$this->log_msg(__("Successfully created index for table: ") . sk2_kBlacklistTable . ".", 5, false);
			}
		}
	
		return $success;
	}
	
	function save_UI_settings($form_values)
	{
		global $sk2_settings;
		//print_r($form_values);
		
		if (isset($form_values["sk2_settings_save"]))
		{
			if (isset($form_values['sk2_filter_options']))
			{
				$this->log_msg("Saving new settings for " . count($form_values['sk2_filter_options']) . " plugins.", 3);

				if (isset($form_values['sk2_filter_checkboxes']))
					foreach($form_values['sk2_filter_checkboxes'] as $check_plugin => $check_name)
						if (! isset($form_values['sk2_filter_options'][$check_plugin][$check_name]))
							$form_values['sk2_filter_options'][$check_plugin][$check_name] = false;
			
				if (isset($form_values['sk2_filter_options'][get_class($this)]))
				{
					foreach($form_values['sk2_filter_options'][get_class($this)] as $name => $value)
						$sk2_settings->set_core_settings($value, $name);
					unset($form_values['sk2_filter_options'][get_class($this)]);
				}

				$sk2_settings->set_plugins_settings($form_values['sk2_filter_options']);
			}
		}
	}

	function get_plugin($this_one)
	{
		$which_plugin_obj = 0;
		foreach ($this->plugins as $plugin)
			if ($plugin[2] == $this_one)
				$which_plugin_obj = $plugin[1];
		
		return $which_plugin_obj;
	}
	
	function set_comment_sk_info($comment_ID = 0, $comment_sk_info = 0, $append = false)
	{ // if $comment_ID != 0: must provide $comment_sk_info
		global $sk2_settings, $wpdb;
		
		if (! $comment_ID)
		{
			if (! $this->cur_comment->ID)
			{
				$this->log_msg("Cannot update sk2_kSpamTable info (no comment ID provided).", 8);
				return false;
			}

			$comment_sk_info = array();
			$comment_ID = $comment_sk_info['comment_ID'] = $this->cur_comment->ID;
			$comment_sk_info['karma'] =  $this->cur_comment->karma;
			$comment_sk_info['karma_cmts'] =  $this->cur_comment->karma_cmts;
			$comment_sk_info['unlock_keys'] =  $this->cur_comment->unlock_keys;
			$comment_sk_info['remaining_attempts'] =  $this->cur_comment->remaining_attempts;
		}
	
		$comment_sk_info_orig = $this->get_comment_sk_info($comment_ID);

		if ($comment_sk_info_orig)
		{
			if ($append)
			{
				if(! is_array($comment_sk_info_orig->karma_cmts))
					$comment_sk_info_orig->karma_cmts = array();
				if(! is_array($comment_sk_info_orig->unlock_keys))
					$comment_sk_info_orig->unlock_keys = array();
				if(! is_array($comment_sk_info['karma_cmts']))
					$comment_sk_info['karma_cmts'] = array();
				if(! is_array($comment_sk_info['unlock_keys']))
					$comment_sk_info['unlock_keys'] = array();
				$comment_sk_info['karma_cmts'] =  $comment_sk_info_orig->karma_cmts + $comment_sk_info['karma_cmts'];
				$comment_sk_info['unlock_keys'] =  $comment_sk_info_orig->unlock_keys + $comment_sk_info['unlock_keys'];
				if (! isset($comment_sk_info['karma']))
					$comment_sk_info['karma'] = $comment_sk_info_orig->karma;
				if (! isset($comment_sk_info['remaining_attempts']))
					$comment_sk_info['remaining_attempts'] = $comment_sk_info_orig->remaining_attempts;
			
		//	print_r($comment_sk_info);
			}
			
			$query = "UPDATE `". sk2_kSpamTable ."` SET ";
			$query_end = "`last_mod` = NOW() WHERE `id` = " . $comment_sk_info_orig->id;
		}
		else
		{
			$query = "INSERT INTO `". sk2_kSpamTable ."` SET ";
			$query_end = "`last_mod` = NOW(), `comment_ID` = $comment_ID";
		}

		foreach ($comment_sk_info as $key => $val)
		{
			if ($key == 'comment_ID')
				continue;
			if (is_array($val))
				$val = serialize($val);
			if (is_int($val) || is_float($val))
				$query .= "`$key` = " . $val . ",";
			else
				$query .= "`$key` = '" . sk2_escape_string($val) . "', ";
		}
		$query .= $query_end;
		//echo $query;
		$wpdb->query($query);
		if (! mysql_error())
			$this->log_msg(__("Inserted/Updated sk2_kSpamTable record for comment ID:") . $comment_ID . " (". ($append ? __("mode: append") : __("mode: overwrite")) . ").", 0);
		else
			$this->log_msg(__("Failed inserting/updating sk2_kSpamTable record for comment ID:") . $comment_ID . " (". ($append ? __("mode: append") : __("mode: overwrite")) . "). <br/>Query: <code>$query</code>", 8, true);

	}
	
	function get_comment_sk_info($comment_ID = 0)
	{
		global $wpdb;

		if (! $comment_ID)
			$comment_ID = $this->cur_comment->ID;

		if (! $comment_ID)
		{
		echo 
			$this->log_msg("get_comment_sk_info: Cannot get sk2_kSpamTable info (no comment ID provided).", 8);
			return false;
		}

		if ($comment_sk_info = $wpdb->get_row("SELECT * FROM `". sk2_kSpamTable ."` WHERE `comment_ID` = $comment_ID"))
		{

			if (! empty($comment_sk_info->karma_cmts))
				$comment_sk_info->karma_cmts = unserialize($comment_sk_info->karma_cmts);
			
			if (! is_array($comment_sk_info->karma_cmts))
				$comment_sk_info->karma_cmts = array();

			if (! empty($comment_sk_info->unlock_keys))
				$comment_sk_info->unlock_keys = unserialize($comment_sk_info->unlock_keys);

			if (! is_array($comment_sk_info->unlock_keys))
				$comment_sk_info->unlock_keys = array();
				
			return $comment_sk_info;
		}
		else
			return false;
	}
	
	function advanced_tools($run_tools)
	{
		if (! is_array($run_tools))
			$run_tools = array($run_tools => true);
		
		global $sk2_settings, $wpdb;
		
		if (isset($run_tools['force_sql_update']))
		{
			$sk2_settings->set_core_settings("", "mysql_updates");
			$this->log_msg("Forcing MySQL updates on core and plugins.", 3);
		}
		
		if (isset($run_tools['reinit_plugins']))
		{
			$sk2_settings->set_core_settings("", "version_updates");
			$this->log_msg("Reinitialize all plugins.", 3);
		}
		
		if (isset($run_tools['reset_all_tables']))
		{
			$this->log_msg("Dropping all SK2 Tables!", 8);
			$wpdb->query("DROP TABLE `". sk2_kSpamTable . "`;");
			$wpdb->query("DROP TABLE `". sk2_kLogTable . "`;");
			$wpdb->query("DROP TABLE `". sk2_kBlacklistTable . "`;");
			$this->log_msg("Dropped all SK2 Tables!", 7);
			$sk2_settings->set_core_settings("", "mysql_updates");
			$this->log_msg("Forcing MySQL updates on core and plugins.", 6, 0, "web_UI");
		}
	
		if(isset($run_tools['check_comment_form']))
		{
			$found = 0;
			foreach(array("comments.php", "comments-popup.php") as $file_name)
			{
				$content = "";
				if ($file = @fopen(TEMPLATEPATH . "/$file_name", "r"))
				{
					while(! feof($file))
						$content .= fread($file, 4096);
					if (preg_match("#do_action.*comment_form#", $content))
					{
						$found++;
						echo "<div class=\"msg_good\">File <code>$file_name</code> is OK.</div>";
					}
					else
					{
						echo "<div class=\"msg_bad\">File <code>$file_name</code>: Could not find <code>do_action</code> hook. Some plugins will fail. Please refer to <a href=\"http://wp-plugins.net/wiki/index.php?title=SK2_Theme_Compatibility\">SK2's documentation</a>.</div>";
					}
				}
				else
				{
					echo "<div class=\"msg_good\">File <code>$file_name</code> looks OK (not present in the active theme, should be using WP default).</div>";
					$found++;
				}
				
			}
			
			if ($found < 2)
			{
				if ($payload_plugin = $this->get_plugin("sk2_payload_plugin"))
				{
					$payload_plugin->set_option_value("weight", "0.0");
					echo "<div class=\"msg_bad\">Temporarily disabling form payload check.</div>";				
				}
			}
			else
			{
				echo "<div class=\"msg_good\">Your theme appears to be compatible (but you might want to run the <strong>Advanced Theme Compatibility Check</strong> at the bottom of the General Settings screen, for extra security).</div>";
			}
		}
		
		if(isset($run_tools['check_comment_form_2']))
		{
			if (empty($run_tools['check_comment_form_2_url']))
			{
				echo "<div class=\"msg_bad\">You need to provide the URL address to a page on your blog that contains a comment form (e.g. a single entry permalink).</div>";
			}
			else
			{
				global $sk2_settings;
				if ($payload_plugin = $this->get_plugin("sk2_payload_plugin"))
				{
					$save_weight = $payload_plugin->get_option_value("weight");
					$payload_plugin->set_option_value("weight", "1.0");
					$sk2_settings->save_settings();
				}
				else
				{
					echo "<div class=\"msg_bad\">Cannot load Payload plugin. You need to have this plugin present in your Plugins directory to use the advanced Theme check.</div>";
					return;
				}
				
				$content = sk2_get_url_content($run_tools['check_comment_form_2_url']);
				$payload_plugin->set_option_value("weight", $save_weight);				
				$sk2_settings->save_settings();
				
				if (empty ($content))
				{
					echo "<div class=\"msg_bad\">Couldn't open the page: You need to provide a valid URL address to a page on your blog that contains a comment form (e.g. a single entry permalink).</div>";
				}
				else
				{
					if (strpos($content, "id=\"sk2_payload\"") !== FALSE)
					{
						echo "<div class=\"msg_good\">URL <i>" . ($run_tools['check_comment_form_2_url']) . "</i> contains the Payload. Your theme appears to be OK.</div>";
						echo "<div class=\"msg_good\">Re-enabling form payload check.</div>";
						$payload_plugin->set_option_value("weight", 1.0);	
					}
					else
					{
						echo "<div class=\"msg_bad\">URL <i>" . ($run_tools['check_comment_form_2_url']) . "</i> : Could not find Payload in comment form. Your theme is likely not compatible. Make sure you used a URL that contains your blog's comment form, try again and, if still getting this result, please refer to <a href=\"http://wp-plugins.net/wiki/index.php?title=SK2_Theme_Compatibility\">SK2's documentation</a>.</div>";
						echo "<div class=\"msg_bad\">Temporarily disabling form payload check.</div>";				
						$payload_plugin->set_option_value("weight", 0.0);				
					}
			
				}
			}				
		}
	}
	
	function log_msg($msg, $level = 0, $mysql = false)
	{
		global $sk2_log;
		if ($mysql)
			$sk2_log->log_msg_mysql($msg, $level, 0, "core");
		else
			$sk2_log->log_msg($msg, $level, 0, "core");
	}
}

?>