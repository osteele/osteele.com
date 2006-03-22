<?php
ini_set("include_path", ini_get('include_path') . PATH_SEPARATOR . ".");
require_once('ultimate-tag-warrior-core.php');
$utw = new UltimateTagWarriorCore();

$install_directory = "/UltimateTagWarrior";

class UltimateTagWarriorActions {

	/* ultimate_admin_menus
	Adds a tag management page to the menu.
	*/
	function ultimate_admin_menus() {
		// Add a new menu under Manage:
		add_management_page('Tag Management', 'Tags', 8, basename(__FILE__), array('UltimateTagWarriorActions', 'ultimate_better_admin'));

		// And one under options
		add_options_page('Tag Options', 'Tags', 8, basename(__FILE__), array('UltimateTagWarriorActions', 'utw_options'));
	}

/* ultimate_rewrite_rules

*/
function &ultimate_rewrite_rules(&$rules) {
	if(get_option("utw_use_pretty_urls") == "yes") {
		$baseurl = get_option("utw_base_url");

		$rules[substr($baseurl, 1) . "?(.*)/feed/(feed|rdf|rss|rss2|atom)/?$"] = "index.php?tag=$1&feed=$2";

		$rules[substr($baseurl, 1) . "?(.*)/page/?(.*)/$"] = "index.php?tag=$1&paged=$2";
		$rules[substr($baseurl, 1) . "?(.*)/$"] = "index.php?tag=$1";

		$rules[substr($baseurl, 1) . "?(.*)/page/?(.*)$"] = "index.php?tag=$1&paged=$2";
		$rules[substr($baseurl, 1) . "?(.*)$"] = "index.php?tag=$1";
	}
	return $rules;
}

function utw_options() {
	global $lzndomain, $utw, $wpdb, $tableposts, $tabletags, $tablepost2tag, $install_directory;

	$siteurl = get_option('siteurl');

	echo '<div class="wrap">';

	$configValues = array();

	$configValues[] = array("setting"=>"", "label"=>__("URL settings", $lzndomain),  "type"=>"label");
	$configValues[] = array("setting"=>"utw_use_pretty_urls", "label"=>__("Use url rewriting for local tag urls (/tag/tag instead of index.php?tag=tag)", $lzndomain),  "type"=>"boolean");
	$configValues[] = array("setting"=>"utw_base_url", "label"=>__("Base url", $lzndomain),  "type"=>"string");
	$configValues[] = array("setting"=>"utw_trailing_slash", 'label'=>__("Include trailing slash on tag urls", $lzndomain), 'type'=>'boolean');

	$configValues[] = array("setting"=>"", "label"=>__("Debugging", $lzndomain),  "type"=>"label");
	$configValues[] = array("setting"=>"utw_debug", 'label'=>__("Include debugging information", $lzndomain), 'type'=>'boolean');

	$configValues[] = array("setting"=>"", "label"=>__("Automatic Tag Link Inclusion", $lzndomain),  "type"=>"label");
	$configValues[] = array("setting"=>"utw_append_tag_links_to_feed", 'label'=>__("Include local tag links in feeds", $lzndomain), 'type'=>'boolean');

	$configValues[] = array("setting"=>"utw_include_local_links", "label"=>__("Automatically include primary tag links", $lzndomain),  "type"=>"boolean");
	$configValues[] = array("setting"=>'utw_primary_automagically_included_link_format', 'label'=>__('Format for primary tag links'), 'type'=>'dropdown', 'options'=>$utw->GetPredefinedFormatNames());

	$configValues[] = array("setting"=>"utw_include_technorati_links", "label"=>__("Automatically include secondary tag links", $lzndomain),  "type"=>"boolean");
	$configValues[] = array("setting"=>'utw_secondary_automagically_included_link_format', 'label'=>__('Format for secondary tag links'), 'type'=>'dropdown', 'options'=>$utw->GetPredefinedFormatNames());


	$configValues[] = array("setting"=>"", "label"=>__("Global Formatting Settings", $lzndomain),  "type"=>"label");

	$configValues[] = array("setting"=>"utw_tag_cloud_max_color", "label"=>__("Most popular color", $lzndomain),  "type"=>"color");
	$configValues[] = array("setting"=>"utw_tag_cloud_max_font", "label"=>__("Most popular size", $lzndomain),  "type"=>"color");
	$configValues[] = array("setting"=>"utw_tag_cloud_min_color", "label"=>__("Least popular color", $lzndomain),  "type"=>"color");
	$configValues[] = array("setting"=>"utw_tag_cloud_min_font", "label"=>__("Least popular size", $lzndomain),  "type"=>"color");
	$configValues[] = array("setting"=>'utw_tag_cloud_font_units', 'label'=>__('Font size units', $lzndomain), "type"=>"dropdown", "options"=>array('%','pt','px','em'));

	$configValues[] = array("setting"=>'utw_icons', 'label'=>__('Icons to display in icon formats', $lzndomain), "type"=>"multiselect", "options"=>array('Technorati','Flickr','delicious','Wikipedia','gadabe', 'Zniff', 'RSS'));

	$configValues[] = array("setting"=>"", "label"=>__("Editing Options", $lzndomain),  "type"=>"label");

	$configValues[] = array("setting"=>"utw_always_show_links_on_edit_screen", "label"=>__("Show existing tags on post editing page", $lzndomain),  "type"=>"dropdown", "options"=>array('none', 'dropdown', 'tag list'));
	$configValues[] = array("setting"=>"utw_include_categories_as_tags", "label"=>__("Automatically add categories as tags", $lzndomain),  "type"=>"boolean");



	if ($_POST["action"] == "saveconfiguration") {
		foreach($configValues as $setting) {
			if ($setting['type'] == 'multiselect') {
				$options = '|';

				foreach($setting['options'] as $option) {
					$options .= $_POST[$setting['setting'] . ":" . $option] . '|';
				}
				update_option($setting['setting'], $options);
			} else if ($setting['type'] != 'label') {
				update_option($setting['setting'], $_POST[$setting['setting']]);
			}
		}
		echo "<div class=\"updated\"><p>Updated settings</p></div>";
	}

	echo "<fieldset class=\"options\"><legend>" . __("Help!", $lzndomain) . "</legend><a href=\"$siteurl/wp-content/plugins$install_directory/ultimate-tag-warrior-help.html\" target=\"_new\">" . __("Local help", $lzndomain) . "</a> | <a href=\"http://www.neato.co.nz/ultimate-tag-warrior\" target=\"_new\">" . __("Author help", $lzndomain) . "</a> | <a href=\"./edit.php?page=ultimate-tag-warrior-actions.php\">Manage Tags</a></fieldset>";
	echo '<fieldset class="options"><legend>' . __('Configuration', $lzndomain) . '</legend>';
	echo "<form method=\"POST\">";
	echo "<table width=\"100%\">";

	foreach($configValues as $setting) {
		if ($setting['type'] == 'boolean') {
			UltimateTagWarriorActions::show_toggle($setting['setting'], $setting['label'], get_option($setting['setting']));
		}

		if ($setting['type'] == 'string') {
			UltimateTagWarriorActions::show_string($setting['setting'], $setting['label'], get_option($setting['setting']));
		}

		if ($setting['type'] == 'color') {
			UltimateTagWarriorActions::show_color($setting['setting'], $setting['label'], get_option($setting['setting']));
		}

		if ($setting['type'] == 'label') {
			UltimateTagWarriorActions::show_label($setting['setting'], $setting['label'], get_option($setting['setting']));
		}
		if ($setting['type'] == 'dropdown') {
			UltimateTagWarriorActions::show_dropdown($setting['setting'], $setting['label'], get_option($setting['setting']), $setting['options']);
		}

		if ($setting['type'] == 'multiselect') {
			UltimateTagWarriorActions::show_multiselect($setting['setting'], $setting['label'], get_option($setting['setting']), $setting['options']);
		}
	}
echo <<<CONFIGFOOTER
	</table>
			<input type="hidden" name="action" value="saveconfiguration">
			<input type="hidden" name="page" value="ultimate-tag-warrior-actions.php">
			<input type="submit" value="Save">
		</form>
	</fieldset>
CONFIGFOOTER;
}

function ultimate_better_admin() {
	global $lzndomain, $utw, $wpdb, $tableposts, $tabletags, $tablepost2tag, $install_directory;

	$siteurl = get_option('siteurl');

	echo '<div class="wrap">';

	if ($_GET["action"] == "savetagupdate") {
		$tagid = $_GET["edittag"];

		if ($_GET["updateaction"] == "Rename") {
			$tag = $_GET["renametagvalue"];

			$tagset = explode(",", $tag);

			$q = "SELECT post_id FROM $tablepost2tag WHERE tag_id = $tagid";
			$postids = $wpdb->get_results($q);

			$tagids = array();

			foreach ($tagset as $tag) {
				$tag = trim($tag);
				$q = "SELECT tag_id FROM $tabletags WHERE tag = '$tag'";
				$thistagid = $wpdb->get_var($q);

				if (is_null($thistagid)) {
					$q = "INSERT INTO $tabletags (tag) VALUES ('$tag')";
					$wpdb->query($q);
					$thistagid = $wpdb->insert_id;
				}
				$tagids[] = $thistagid;
			}

			$keepold = false;
			foreach($tagids as $newtagid) {
				if ($postids ) {
					foreach ($postids as $postid) {
						if ($wpdb->get_var("SELECT COUNT(*) FROM $tablepost2tag WHERE tag_id = $newtagid AND post_id = $postid->post_id") == 0) {
							$wpdb->query("INSERT INTO $tablepost2tag (tag_id, post_id) VALUES ($newtagid, $postid->post_id)");
						}
					}
				} else {
					// I guess we were renaming something which wasn't being used...
				}

				if ($newtagid == $tagid) {
					$keepold = true;
				}
			}

			if (!$keepold) {
				$q = "delete from $tablepost2tag where tag_id = $tagid";
				$wpdb->query($q);

				$q = "delete from $tabletags where tag_id = $tagid";
				$wpdb->query($q);
			}
			echo "<div class=\"updated\"><p>Tags have been updated.</p></div>";
		}

		if ($_GET["updateaction"] == __("Save Synonyms", $lzndomain)) {
			$synonyms = $_GET["synonyms"];
			$synonyms = explode(',', $synonyms);
			$utw->ClearSynonymsForTag($_GET["synonymtag"]);
			$message = "";
			foreach($synonyms as $synonym) {
				$message .= $utw->AddSynonymForTag("", $_GET["synonymtag"], $synonym);
				$message .= $synonym . " ";
			}

			echo "<div class=\"updated\"><p>Added $message</p></div>";
		}

		if ($_GET["updateaction"] ==__("Delete Tag", $lzndomain)) {
			$q = "delete from $tablepost2tag where tag_id = $tagid";
			$wpdb->query($q);

			$q = "delete from $tabletags where tag_id = $tagid";
			$wpdb->query($q);

			echo "<div class=\"updated\"><p>Tag has been deleted.</p></div>";
		}
		if ($_GET["updateaction"] == __("Force Reinstall", $lzndomain)) {
			$message = $utw->ForceInstall();
			if ($message) {
				echo "<div class=\"updated\"><p>$message</p></div>";
			} else {
				echo "<div class=\"updated\"><p>Reinstall has Completed</p></div>";
			}
		}
		if ($_GET["updateaction"] == __("Tidy Tags", $lzndomain)) {
			$utw->TidyTags();
			echo "<div class=\"updated\"><p>Tags have been tidied</p></div>";
		}
		if ($_GET["updateaction"] == __("Convert Categories to Tags", $lzndomain)) {
			$postids = $wpdb->get_results("SELECT id FROM $wpdb->posts");
			foreach ($postids as $postid) {
				$utw->SaveCategoriesAsTags($postid->id);
			}

			echo "<div class=\"updated\"><p>Categories have been converted to tags</p></div>";
		}
		if ($_GET["updateaction"] == __("Import from Custom Field", $lzndomain)) {
			update_option('utw_custom_field_conversion_field_name', $_GET["fieldName"]);
			update_option('utw_custom_field_conversion_delimiter', $_GET["delimiter"]);

			if ($_GET['fieldName'] && $_GET['delimiter']) {
				$postids = $wpdb->get_results("SELECT id FROM $wpdb->posts");
				foreach ($postids as $postid) {
					$utw->SaveCustomFieldAsTags($postid->id, $_GET["fieldName"], $_GET["delimiter"]);
				}
				echo "<div class=\"updated\"><p>Tags have been imported from a custom field</p></div>";
			} else {
				echo "<div class=\"updated\"><p>Could not import tags from custom field</p></div>";
			}
		}
		if ($_GET["updateaction"] == __("Export to Custom Field", $lzndomain)) {
			update_option('utw_custom_field_conversion_field_name', $_GET["fieldName"]);
			update_option('utw_custom_field_conversion_delimiter', $_GET["delimiter"]);

			if ($_GET['fieldName'] && $_GET['delimiter']) {
				$postids = $wpdb->get_results("SELECT id FROM $wpdb->posts");
				foreach ($postids as $postid) {
					$utw->SaveTagsAsCustomField($postid->id, $_GET["fieldName"], $_GET["delimiter"]);
				}
				echo "<div class=\"updated\"><p>Tags have been exported to a custom field</p></div>";
			} else {
				echo "<div class=\"updated\"><p>Could not export tags to custom field</p></div>";
			}
		}
	}

	echo "<fieldset class=\"options\"><legend>" . __("Help!", $lzndomain) . "</legend><a href=\"$siteurl/wp-content/plugins$install_directory/ultimate-tag-warrior-help.html\" target=\"_new\">" . __("Local help", $lzndomain) . "</a> | <a href=\"http://www.neato.co.nz/ultimate-tag-warrior\" target=\"_new\">" . __("Author help", $lzndomain) . "</a> | <a href=\"./options-general.php?page=ultimate-tag-warrior-actions.php\">Configuration</a></fieldset>";

	echo '<fieldset class="options"><legend>' . __("Edit Tags", $lzndomain) .'</legend>';
	echo '<p>' . __("Enter a comma separated list of tags", $lzndomain) . '</p>';
OPTIONS;
	$tags = $utw->GetPopularTags(-1, 'asc', 'tag');
	if ($tags) {
		echo "<form action=\"$siteurl/wp-admin/edit.php\">";
		echo "<select name=\"edittag\">";
		foreach($tags as $tag) {
			echo "<option value=\"$tag->tag_id\">$tag->tag</option>";
		}

		echo '</select> <input type="text" name="renametagvalue"> <input type="submit" name="updateaction" value="' . __("Rename", $lzndomain) . '"> <input type="submit" name="updateaction" value="' . __("Delete Tag", $lzndomain) . '" OnClick="javascript:return(confirm(\'' . __("Are you sure you want to delete this tag?", $lzndomain) . '\'))">';
		echo '<input type="hidden" name="action" value="savetagupdate">';
		echo '<input type="hidden" name="page" value="ultimate-tag-warrior-actions.php">';
		echo '</form>';
	} else {
		echo '<p>' . __('No tags are in use at the moment.', $lzndomain) . '</p>';
	}
	echo "</fieldset>";

	echo '<fieldset class="options"><legend>' . __("Assign Synonyms", $lzndomain) .'</legend>';
	echo '<p>' . __("Enter a comma separated list of synonyms. ", $lzndomain) . __("A synonym behaves in a similar manor to a tag - viewing the tag page for a synonym of a tag displays the tag page for the underlying tag.", $lzndomain) . '</p>';
	$tags = $utw->GetPopularTags(-1, 'asc', 'tag');
	if ($tags) {
		echo "<form action=\"$siteurl/wp-admin/edit.php\">";
		echo "<select name=\"synonymtag\" onChange=\"sndReqGenResp('editsynonyms', this.value, '', '')\">";
		foreach($tags as $tag) {
			echo "<option value=\"$tag->tag_id\">$tag->tag</option>";
		}

		echo '</select> <span id="ajaxResponse"></span> <input type="submit" name="updateaction" value="' . __("Save Synonyms", $lzndomain) . '">';
		echo '<input type="hidden" name="action" value="savetagupdate">';
		echo '<input type="hidden" name="page" value="ultimate-tag-warrior-actions.php">';
		echo '</form>';
	} else {
		echo '<p>' . __('No tags are in use at the moment.', $lzndomain) . '</p>';
	}
	echo "</fieldset>";


	echo "<form action=\"$siteurl/wp-admin/edit.php\">";

	echo '<fieldset class="options"><legend>' . __('Force Reinstall', $lzndomain) . '</legend>';
	_e('<p>Force Reinstall will run the installer.  This <em>will not</em> delete the tag tables.</p>');
	echo '<input type="submit" name="updateaction" value="' . __('Force Reinstall', $lzndomain) . '"></fieldset>';

	echo '<fieldset class="options"><legend>' . __('Tidy Tags', $lzndomain) . '</legend>';
	_e('<p>Tidy Tags is a scary, scary thing.  <em>Make sure you back up your database before clicking the button.</em></p><p>Tidy Tags will delete any tag&lt;-&gt;post associations which have either a deleted tag or deleted post;  delete any tags not associated with a post;  and merge tags with the same name into single tags.</p>');
	echo '<input type="submit" name="updateaction" value="' . __('Tidy Tags', $lzndomain) . '" OnClick="javascript:return(confirm(\'' . __("Are you sure you want to purge tags?", $lzndomain) . '\'))"></fieldset>';

	echo '<fieldset class="options"><legend>' . __('Convert Categories to Tags', $lzndomain) . '</legend>';
	_e('<p>Again.. very scary.. back up your database first!</p>');
	echo '<input type="submit" name="updateaction" onClick="javascript:return(confirm(\'' . __('Are you sure you want to convert categories to tags?', $lzndomain) . '\'))" value="' . __('Convert Categories to Tags', $lzndomain) . '"></fieldset>';

	echo '<fieldset class="options"><legend>' . __('Custom Fields', $lzndomain) . '</legend>';
	_e('<p>This pair of actions allow the moving of tag information from custom fields into the tag structure,  and moving the tag structure into a custom field.</p><p>When moving information from the custom field to the tag structure,  the existing tags are retained.  However, copying the tags to the custom field <strong>will overwrite the existing values</strong>.  To retain the existing values,  do an import before the export.</p><p><strong>This stuff seems to work,  but backup your database before trying,  just in case.</strong></p>', $lzndomain);
	echo '<table><tr><td>' . __("Custom field name", $lzndomain) . '</td><td><input type="text" name="fieldName" value="' . $fieldName . '" /></td></tr>';
	echo '<tr><td>' . __("Tag delimiter", $lzndomain) . '</td><td><input type="text" name="delimiter" value="' . $delimiter . '" /></td></tr></table>';
	echo '<input type="submit" name="updateaction" value="' . __("Import from Custom Field", $lzndomain) . '" />';
	echo '<input type="submit" name="updateaction" value="' . __("Export to Custom Field", $lzndomain) . '" OnClick="javascript:return(confirm(\'' . __('Beware:  This will overwrite any data in the custom field.  Continue?', $lzndomain) . '\'))"/></fieldset>';

	echo '<input type="hidden" name="action" value="savetagupdate">';
	echo '<input type="hidden" name="page" value="ultimate-tag-warrior-actions.php">';
	echo '</form>';
}

function show_dropdown($settingName, $label, $value, $options) {
	echo "<tr><td>$label</td><td><select name=\"$settingName\">";

	foreach($options as $option) {
		echo "<option value=\"$option\"";
		if ($value == $option) {
			echo " selected";
		}
		echo ">$option</option>";
	}

	echo "</select></td></tr>";
}

function show_multiselect($settingName, $label, $value, $options) {
	echo "<tr><td valign=\"top\">$label</td><td>";

	foreach($options as $option) {
		echo "<input type='checkbox' value=\"$option\" name=\"$settingName:$option\"";
		if (strpos($value,$option) > 0) {
			echo " checked";
		}
		echo "> $option<br />";
	}

	echo "</td></tr>";
}

function show_label($settingName, $label, $value) {
	echo <<<FORMWIDGET
<tr><td colspan="2" bgcolor="#DDD"><strong>$label</strong></td></tr>
FORMWIDGET;
}

function show_color($settingName, $label, $value) {
	echo <<<FORMWIDGET
<tr><td>$label</td><td><input type="text" name="$settingName" value="$value" maxlength="7" size="9"></td></tr>
FORMWIDGET;
}

function show_string($settingName, $label, $value) {
	echo <<<FORMWIDGET
<tr><td>$label</td><td><input type="text" name="$settingName" value="$value"></td></tr>
FORMWIDGET;
}

function show_toggle($settingName, $label, $value) {
	if ($value == 'yes') {
		$yeschecked = " checked";
	}
	echo <<<FORMWIDGET
<tr><td>$label</td><td><input type="checkbox" name="$settingName" id="$settingName" value="yes" $yeschecked></td></tr>
FORMWIDGET;
}

/*
ultimate_tag_templates
Handles the inclusion of templates, when appropriate.

index.php?archive=tag (or equivalent) will try and use the template tag_all.php
index.php?tag={tag name} (or equivalent) will try and use the template tag.php
*/
function ultimate_tag_templates() {
	if ($_GET["archive"] == "tag") {
		include(TEMPLATEPATH . '/tag_all.php');
		exit;
	} else 	if (get_query_var("tag") != "") {
		ultimate_get_posts();
		if (file_exists(TEMPLATEPATH . "/tag.php")) {
			if ( isset($_GET['feed']) || $_GET["feed"] == '') {
				include(TEMPLATEPATH . '/tag.php');
				exit;
			}
		} else {
	//		include(TEMPLATEPATH . '/index.php');
		}
	}
}

/*
ultimate_save_tags
Saves the tags for the current post to the database.

$postID the ID of the current post
$_POST['tagset'] the list of tags.
*/
function ultimate_save_tags($postID)
{
	global $wpdb, $tableposts, $table_prefix, $utw;

	$tags = $wpdb->escape($_POST['tagset']);
	$tags = explode(',',$tags);

	$utw->SaveTags($postID, $tags);

	if (get_option('utw_include_categories_as_tags') == "yes") {
		$utw->SaveCategoriesAsTags($postID);
	}


    return $postID;
}

function ultimate_delete_post($postID) {
	global $utw;

	$utw->DeletePostTags($postID);

	return $postID;
}

/*
ultimate_display_tag_widget
Displays the tag box on the content editing page.
*/
function ultimate_display_tag_widget() {
  global $post, $wpdb, $table_prefix, $utw;

  $tabletags = $table_prefix . "tags";
  $tablepost2tag = $table_prefix . "post2tag";

  $taglist = "";


  if ( (is_object($post) && $post->ID) || (!is_object($post) && $post)) {
	if (is_object($post)) {
		$postid = $post->ID;
	} else {
		$postid = $post;
	}


    $q = "select t.tag from $tabletags t inner join $tablepost2tag p2t on t.tag_id = p2t.tag_id and p2t.post_id=$postid";
    $tags = $wpdb->get_results($q);

    if ($tags) {
	  foreach($tags as $tag) {
		  $taglist .= $tag->tag . " ";
      }
	  $taglist = substr($taglist, 0, -1); // trim the trailing space.
    }
  }

	echo '<fieldset id="tagsdiv">';
	echo '<legend>Tags (Comma separated list; and -\'s and _\'s display as spaces)</legend>';
	echo "<input name=\"tagset\" type=\"text\" value=\"";
	if ($post) {
	$utw->ShowTagsForPost($post, array("first"=>'%tag%', 'default'=>', %tag%'));
	}
	echo "\" size=\"100\"><br />";

	$widgetToUse = get_option('utw_always_show_links_on_edit_screen');

		echo <<<JAVASCRIPT
       <script language="javascript">
       function addTag(tagname) {
         if (document.forms[0].tagset.value == "") {
           document.forms[0].tagset.value = tagname;
	   } else {
                 document.forms[0].tagset.value += ", " + tagname;
               }
       }
       </script>
JAVASCRIPT;


	if ($widgetToUse != 'none') {
		echo "Add existing tag: ";
		if ($widgetToUse=='tag list') {

			$format = "<a href=\"javascript:addTag('%tag%')\">%tagdisplay%</a> ";
			echo $utw->ShowPopularTags(-1, $format, 'tag', 'asc');

		} else {
			$format = array(
			'pre' => '<select onchange="if (document.getElementById(\'tag-menu\').value != \'\') { addTag(document.getElementById(\'tag-menu\').value) }" id="tag-menu"><option selected="selected" value="">Choose a tag</option>',
			'default' => '<option value="%tag%">%tagdisplay% (%tagcount%)</option>',
			'post' => '</select>');

			echo $utw->ShowPopularTags(-1, $format, 'tag', 'asc');
		}
	}
  echo '</fieldset>';

  echo '<fieldset id="tagsuggestdiv">';
  echo '<legend>Tag Suggestions (Courtesy of <a href="http://tagyu.com">tagyu.com</a>)</legend>';
  echo '<input type="button" onClick="askYahooForKeywords()" value="Get Keyword Suggestions"/>';
  echo '<div id="suggestedTags"></div>';
  echo '</fieldset>';

}

function ultimate_the_content_filter($thecontent='') {
	global $post, $utw, $lzndomain;

	$tags = $utw->GetTagsForPost($post->ID);

	if (count($tags) == 0 && $post->post_status == 'static') {
		return $thecontent;
	}

	if (get_option('utw_include_local_links') == 'yes') {

		if (get_option('utw_primary_automagically_included_link_format') != '') {
			$thecontent = $thecontent . $utw->FormatTags($tags, $utw->GetFormatForType(get_option('utw_primary_automagically_included_link_format')));
		} else {
			$thecontent = $thecontent . $utw->FormatTags($tags, array("first"=>"<span class=\"localtags\">%taglink% ","default"=>"%taglink% ", "last"=>"%taglink%</span>"));
		}
	}

	if (get_option('utw_include_technorati_links') == 'yes') {
		if (get_option('utw_secondary_automagically_included_link_format') != '') {
			$thecontent = $thecontent . $utw->FormatTags($tags, $utw->GetFormatForType(get_option('utw_secondary_automagically_included_link_format')));
		} else {
			$thecontent = $thecontent . $utw->FormatTags($tags, array("pre"=>__("<span class=\"technoratitags\">Technorati Tags", $lzndomain) . ": ","default"=>"%technoratitag% ", "last"=>"%technoratitag%","none"=>"","post"=>"</span>"));
		}
	}

	if (is_feed() && get_option('utw_append_tag_links_to_feed')) {
		$thecontent = $thecontent . $utw->FormatTags($tags, $utw->GetFormatForType('commalist'));
	}

	return $thecontent;
}

function ultimate_add_tags_to_rss($the_list, $type="") {
	global $post, $utw;

    $categories = get_the_category();
    $the_list = '';
    foreach ($categories as $category) {
        $category->cat_name = convert_chars($category->cat_name);
        $the_list .= "\n\t<dc:subject>$category->cat_name</dc:subject>";
    }

	$format="<dc:subject>%tagdisplay%</dc:subject>";
	echo $the_list;
	echo $utw->FormatTags($utw->GetTagsForPost($post->ID), $format);
}

function ultimate_add_ajax_javascript() {
	global $install_directory;
	$rpcurl = get_option('siteurl') . "/wp-content/plugins$install_directory/ultimate-tag-warrior-ajax.php";
	$jsurl = get_option('siteurl') . "/wp-content/plugins$install_directory/ultimate-tag-warrior-ajax-js.php";
	echo "<script src=\"$jsurl?ajaxurl=$rpcurl\" type=\"text/javascript\"></script>";

}

function ultimate_posts_join($join) {
	if (get_query_var("tag") != "") {
		global $table_prefix, $wpdb;

		$tabletags = $table_prefix . "tags";
		$tablepost2tag = $table_prefix . "post2tag";

		$join .= " INNER JOIN $tablepost2tag p2t on $wpdb->posts.ID = p2t.post_id INNER JOIN $tabletags t on p2t.tag_id = t.tag_id ";
	}
	return $join;
}

function ultimate_posts_where($where) {
	global $utw;
	if (get_query_var("tag") != "") {
		global $table_prefix, $wpdb;

		$tabletags = $table_prefix . "tags";
		$tablepost2tag = $table_prefix . "post2tag";

		$tags = get_query_var("tag");

		$tagset = explode(" ", $tags);

		if (count($tagset) == 1) {
			$tagset = explode("|", $tags);
		}

		$tags = array();
		foreach($tagset as $tag) {
			$tags[] = "'" . $utw->GetCanonicalTag($tag) . "'";
		}
		$tags = array_unique($tags);

		$taglist = implode (',',$tags);
		$where .= " AND t.tag IN ($taglist) ";
	}
	return $where;
}

function ultimate_query_vars($vars) {
	$vars[] = 'tag';

	return $vars;
}

/* Maaaaaybe some day...

function ultimate_posts_having () {
	if (get_query_var("tag") != "") {
		$tags = get_query_var("tag");
		$tagset = explode(" ", $tags);
		$taglist = "'" . $tagset[0] . "'";
		$tagcount = count($tagset);

		return " HAVING count(wp_posts.id) = $tagcount ";
	}
}
*/

}

// Admin menu items
add_action('admin_menu', array('UltimateTagWarriorActions', 'ultimate_admin_menus'));

// Add or edit tags
add_action('simple_edit_form', array('UltimateTagWarriorActions','ultimate_display_tag_widget'));
add_action('edit_form_advanced', array('UltimateTagWarriorActions','ultimate_display_tag_widget'));

// Save changes to tags
add_action('publish_post', array('UltimateTagWarriorActions','ultimate_save_tags'));
add_action('edit_post', array('UltimateTagWarriorActions','ultimate_save_tags'));
add_action('save_post', array('UltimateTagWarriorActions','ultimate_save_tags'));
add_action('wp_insert_post', array('UltimateTagWarriorActions','ultimate_save_tags'));

add_action('delete_post', array('UltimateTagWarriorActions', 'ultimate_delete_post'));

// Display tag pages
add_action('template_redirect', array('UltimateTagWarriorActions','ultimate_tag_templates'));

add_filter('posts_join', array('UltimateTagWarriorActions','ultimate_posts_join'));
add_filter('posts_where', array('UltimateTagWarriorActions','ultimate_posts_where'));
// add_filter('posts_having',array('UltimateTagWarriorActions','ultimate_posts_having'));

// URL rewriting
add_filter('rewrite_rules_array', array('UltimateTagWarriorActions','ultimate_rewrite_rules'));
add_filter('query_vars', array('UltimateTagWarriorActions','ultimate_query_vars'));

add_filter('the_content', array('UltimateTagWarriorActions', 'ultimate_the_content_filter'));
add_filter('the_category_rss', array('UltimateTagWarriorActions', 'ultimate_add_tags_to_rss'));

add_filter('wp_head', array('UltimateTagWarriorActions', 'ultimate_add_ajax_javascript'));
add_filter('admin_head', array('UltimateTagWarriorActions', 'ultimate_add_ajax_javascript'));
?>