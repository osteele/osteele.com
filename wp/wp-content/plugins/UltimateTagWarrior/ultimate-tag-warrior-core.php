<?php
$tabletags = $table_prefix . "tags";
$tablepost2tag = $table_prefix . "post2tag";
$tabletag_synonyms = $table_prefix . "tag_synonyms";

$lzndomain = "ultimate-tag-warrior";
$current_build = 6;

class UltimateTagWarriorCore {

	/* Comparing x.y.z versions is more effort than I'm prepared
	   to go to.  '*/
	function CheckForInstall() {
		global $current_build, $wpdb, $tabletags, $tablepost2tag, $tabletag_synonyms;

		$installed_build = get_option('utw_installed_build');
		if ($installed_build == '') $installed_build = 0;

		if ($installed_build < 1) {
			$q = <<<SQL
			CREATE TABLE IF NOT EXISTS $tabletags (
			  ID int(11) NOT NULL auto_increment,
			  tag varchar(255) NOT NULL default '',
			  PRIMARY KEY  (ID)
			) TYPE=MyISAM;
SQL;

			$wpdb->query($q);

			$q = <<<SQL
			CREATE TABLE IF NOT EXISTS $tablepost2tag (
			  rel_id int(11) NOT NULL auto_increment,
			  tag_id int(11) NOT NULL default '0',
			  post_id int(11) NOT NULL default '0',
			  PRIMARY KEY  (rel_id)
			) TYPE=MyISAM;
SQL;

			$wpdb->query($q);

			add_option('utw_include_technorati_links', 'yes', 'Indicates whether technorati links should be automatically appended to the content.', 'yes');
			add_option('utw_include_local_links', 'no', 'Indicates whether local tag links should be automatically appended to the content.', 'yes');
			add_option('utw_base_url', '/tag/', 'The base url for tag links i.e. {base url}{sometag}', 'yes');
			add_option('utw_include_categories_as_tags', 'no', 'Will include any selected categories as tags', 'yes');

			add_option('utw_use_pretty_urls', 'no', 'Use /tag/tag urls instead of index.php?tag=tag urls', 'yes');

			add_option('utw_tag_cloud_max_color', '#000000', 'The color of popular tags in tag clouds', 'yes');
			add_option('utw_tag_cloud_min_color', '#FFFFFF', 'The color of unpopular tags in tag clouds', 'yes');

			add_option('utw_tag_cloud_max_font', '250', 'The maximum font size (as a percentage) for popular tags in tag clouds', 'yes');
			add_option('utw_tag_cloud_min_font', '70', 'The minimum font size (as a percentage) unpopular tags in tag clouds', 'yes');

			add_option ('utw_tag_cloud_font_units', '%', 'The units to display the font sizes with, on tag clouds.');

			add_option('utw_tag_line_max_color', '#000000', 'The color of popular tags in a tag line', 'yes');
			add_option('utw_tag_line_min_color', '#FFFFFF', 'The color of unpopular tags in a tag line', 'yes');

			add_option('utw_long_tail_max_color', '#000000', 'The color of popular tags in a long tail chart', 'yes');
			add_option('utw_long_tail_min_color', '#FFFFFF', 'The color of unpopular tags in a long tail chart', 'yes');

			add_option('utw_always_show_links_on_edit_screen', 'no', 'Always display existing tags as links; regardles of how many there are', 'yes');
		}

		if ($installed_build < 2) {
			$alreadyChanged = $wpdb->get_var("SHOW COLUMNS FROM $tabletags LIKE 'tag_id'");
			if ($alreadyChanged == 'tag_id') {
				// do nothing! the column has already been changed; and trying to change it again makes an error.
			} else {
				$q = "ALTER TABLE $tabletags CHANGE id tag_id int(11) AUTO_INCREMENT";
				$wpdb->query($q);
			}
		}

		if ($installed_build < 3) {

			$q = <<<SQL
		CREATE TABLE IF NOT EXISTS $tabletag_synonyms (
		  tagsynonymid int(11) NOT NULL auto_increment,
		  tag_id int(11) NOT NULL default '0',
		  synonym varchar(150) NOT NULL default '',
		  PRIMARY KEY  (`tagsynonymid`)
) TYPE=MyISAM;
SQL;

			$wpdb->query($q);

			$worked = $wpdb->get_var("SHOW TABLES LIKE '$tabletag_synonyms'");

			if ($worked != $tabletag_synonyms) {
				return "Wasn't able to create $tabletag_synonyms";
			}
		}

		if ($installed_build < 6) {
			$alreadyChanged = $wpdb->get_var("SHOW COLUMNS FROM $tablepost2tag LIKE 'ip_address'");
			if ($alreadyChanged == 'ip_address') {
				// do nothing! the column has already been changed; and trying to change it again makes an error.
			} else {
				$q = "ALTER TABLE $tablepost2tag ADD ip_address varchar(15)";
				$wpdb->query($q);

				$changed = $wpdb->get_var("SHOW COLUMNS FROM $tablepost2tag LIKE 'ip_address'");

				if ($changed != 'ip_address') {
					return "Couldn't add ip_address column to $tablepost2tag";
				}
			}
		}

		update_option('utw_installed_build', $current_build);
	}

	function ForceInstall() {
		update_option('utw_installed_build', 0);
		$this->CheckForInstall();
	}

	/* Fundamental functions for dealing with tags */
	/* The post corresponding to the postID are updated to be the tags in the list.  Previously assigned
		tags not in the list are deleted. */
	function SaveTags($postID, $tags) {
		global $tabletags, $tablepost2tag, $wpdb, $current_build, $REMOTE_ADDR;

		$tags = array_flip(array_flip($tags));

		foreach($tags as $tag) {
			if ($tag <> "") {
				$tag = trim($tag);
				$tag = str_replace(' ', '_', $tag);

				$q = "SELECT tag_id FROM $tabletags WHERE tag='$tag' limit 1";
				$tagid = $wpdb->get_var($q);

				if (is_null($tagid)) {
					$q = "INSERT INTO $tabletags (tag) VALUES ('$tag')";
					$wpdb->query($q);
					$tagid = $wpdb->insert_id;
				}

				$q = "SELECT rel_id FROM $tablepost2tag WHERE post_id = '$postID' AND tag_id = '$tagid'";

				if ( is_null($wpdb->get_var($q))) {
					$q = "INSERT INTO $tablepost2tag (post_id, tag_id, ip_address) VALUES ('$postID','$tagid', '" . $REMOTE_ADDR . "')";

					$wpdb->query($q);
				}

				$taglist .= $tagid . ", ";
			}
		}

		// Remove any tags that are no longer associated with the post.

		if ($taglist == "") {
			// since "not in ()" doesn't play nice.
			$q = "delete from $tablepost2tag where post_id = $postID";
		} else {
			// lop off the trailing space+comma
			$taglist = substr($taglist, 0 ,-2);

			$q = "delete from $tablepost2tag where post_id = $postID and tag_id not in ($taglist)";
		}
		$wpdb->query($q);
	}

	/* Adds the specified tag to the post corresponding with the post ID */
	function AddTag($postID, $tag) {
		global $tabletags, $tablepost2tag, $wpdb;

		if ($tag <> "") {
			$tag = trim($tag);
			$tag = str_replace(' ', '_', $tag);

			$tag = $this->GetCanonicalTag($tag);

			$q = "SELECT tag_id FROM $tabletags WHERE tag='$tag' limit 1";
			$tagid = $wpdb->get_var($q);

			if (is_null($tagid)) {
				$q = "INSERT INTO $tabletags (tag) VALUES ('$tag')";
				$wpdb->query($q);
				$tagid = $wpdb->insert_id;
			}

			$q = "SELECT rel_id FROM $tablepost2tag WHERE post_id = '$postID' AND tag_id = '$tagid'";

			if ( is_null($wpdb->get_var($q))) {
				$q = "INSERT INTO $tablepost2tag (post_id, tag_id) VALUES ('$postID','$tagid')";
				$wpdb->query($q);
			}
		}
	}

	/* Adds the specified tag to the post corresponding with the post ID */
	function RemoveTag($postID, $tag) {
		global $tabletags, $tablepost2tag, $wpdb;

		if ($tag <> "") {

			$q = "SELECT tag_id FROM $tabletags WHERE tag='$tag' limit 1";
			$tagid = $wpdb->get_var($q);

			if (!is_null($tagid)) {
				$q = "DELETE FROM $tablepost2tag WHERE post_id = '$postID' AND tag_id = '$tagid'";

				$wpdb->query($q);
			}

			$q = "SELECT count(*) FROM $tablepost2tag WHERE tag_id = '$tagid'";

			if ( 0 == $wpdb->get_var($q)) {
				$q = "DELETE FROM $tabletags WHERE tag_id = $tagid";
				$wpdb->query($q);
			}
		}
	}

	/*
	 * Add any categories assigned to the post as tags.  This retains any exising tags.
	 */
	function SaveCategoriesAsTags($postID) {
		global $wpdb, $tablepost2tag, $wpdb;

		$default = get_option('default_category');

		$categories = $wpdb->get_results("SELECT c.cat_name FROM $wpdb->post2cat p2c INNER JOIN $wpdb->categories c ON p2c.category_id = c.cat_id WHERE p2c.post_id = $postID AND c.cat_ID != $default");
		$tags = $this->GetTagsForPost($postID);

		$alltags = array();
		if ($tags) {
			foreach($tags as $tag) {
				$alltags[] = $tag->tag;
			}
		}

		if ($categories) {
			foreach($categories as $cat) {
				$alltags[] = str_replace(" ", "_", $cat->cat_name);
			}
		}

		if (count($alltags) > 0) {
			$this->SaveTags($postID, $alltags);
		}
	}

	/*
	 * Add any tags, from the specified custom field as tags.  This retains any existing tags.
	 */
	function SaveCustomFieldAsTags($postID, $fieldName, $separator) {
		if (!$fieldName || !$separator) return;

		$allExisting = get_post_meta($postID, $fieldName, false);

		$tags = $this->GetTagsForPost($postID);

		$alltags = array();

		if ($tags) {
			foreach($tags as $tag) {
				$alltags[] = $tag->tag;
			}
		}

		foreach ($allExisting as $existing) {
			$items = explode($separator, $existing);
			foreach ($items as $tag) {
				$alltags[] = str_replace(" ", "_", trim($tag));
			}
		}

		if (count($alltags) > 0) {
			$this->SaveTags($postID, $alltags);
		}
	}

	/*
	 * Write the set of tags to the custom field specified.
	 * If the separator is anything but a space; -'s and _' will be converted back to spaces.
	 * NB.  It's generally a good idea to call SaveCustomFieldAsTags first.
	 */
	function SaveTagsAsCustomField($postID, $fieldName, $separator) {
		$tags = $this->GetTagsForPost($postID);

		if ($tags) {
			foreach ($tags as $tag) {
				if ($separator == " ") {
					$tagstr .= $tag->tag . $separator;
				} else {
					$tagstr .= str_replace("-", " ", str_replace("_"," ",$tag->tag)) . $separator;
				}
			}

			$tagstr = substr($tagstr, 0, strlen($separator)*-1);
		}
		delete_post_meta($postID, $fieldName);
		add_post_meta($postID, $fieldName, $tagstr);
	}

	function DeleteTags($postID) {
		global $tabletags, $tablepost2tag, $wpdb;

		$query = "DELETE FROM $tablepost2tag WHERE post_id = $postID";
		$wpdb->query($query);
	}

	function DeletePostTags($postID) {
		$this->DeleteTags($postID);
	}

	function GetTagsForTagString($tags) {
		global $wpdb, $tabletags;

		if ($tags) {
			$q = "SELECT * FROM $tabletags WHERE tag IN ($tags)";

			return $wpdb->get_results($q);
		}
	}

	function GetCurrentTagSet() {
		$tags = $_GET["tag"];
		$tagset = explode(" ", $tags);

		if (count($tagset) == 1) {
			$tagset = explode("|", $tags);
		}

		$tagcount = count($tagset);
		$taglist = array();

		if ($tagcount > 0) {
			for ($i = 0; $i < $tagcount; $i++) {
				if (trim($tagset[$i]) <> "") {
					$taglist[] = "'" . trim($tagset[$i]) . "'";
				}
			}
		}

		return ($this->GetTagsForTagString( implode(',',$taglist)));
	}

	function TidyTags() {
		global $wpdb, $tablepost2tag, $tabletags;

		/* Phase 1:  delete the post-tag relationships from posts which have been deleted */
		$q = "SELECT post_id FROM $tablepost2tag left join $wpdb->posts on ID = post_id where ID is null group by post_id";
		$orphanpostids = $wpdb->get_results($q);

		if ($orphanpostids) {
			foreach ($orphanpostids as $orphanpostid) {
				$q = "DELETE FROM $tablepost2tag WHERE post_id = $orphanpostid->post_id";
				$wpdb->query($q);
			}
		}

		/* Phase 2:  delete any tags which are no longer in use */
		$q = "SELECT t.tag_id FROM $tabletags t LEFT JOIN $tablepost2tag p2t ON p2t.tag_id = t.tag_id WHERE p2t.tag_id IS NULL";
		$orphantagids = $wpdb->get_results($q);

		if ($orphantagids) {
			foreach ($orphantagids as $orphantagid) {
				$q = "DELETE FROM $tabletags where tag_id = $orphantagid->id";
				$wpdb->query($q);
			}
		}

		/* Phase 3:  consolidate any duplicate tags */
		$q = "SELECT tag, MIN(tag_id) as lowid, COUNT(*) cnt FROM $tabletags GROUP BY tag HAVING cnt > 1";
		$duplicatetags = $wpdb->get_results($q);

		if ($duplicatetags) {
			foreach($duplicatetags as $duplicatetag) {
				$trueid = $duplicatetag->lowid;

				$duplicatetagids = $wpdb->get_results("SELECT tag_id FROM $tabletags WHERE tag = '$duplicatetag->tag' AND tag_id != $trueid");
				$tagidstr = "";
				if ($duplicatetagids) {
					foreach($duplicatetagids as $tagid) {
						$tagidstr .= $tagid->id . ', ';
					}

					$tagidstr = substr($tagidstr, 0, -2);
				}

				$effectedposts = $wpdb->get_results("SELECT post_id FROM $tablepost2tag WHERE tag_id IN ($tagidstr) OR tag_id = $trueid");

				foreach($effectedposts as $post) {
					if(is_null($wpdb->get_var("SELECT rel_id FROM $tablepost2tag WHERE post_id = $post->post_id AND tag_id = $trueid"))) {
						$wpdb->query("INSERT INTO $tablepost2tag (post_id, tag_id) VALUES ($post->post_id, $trueid)");
					}
				}

				if ($tagidstr) {
					$wpdb->query("DELETE FROM $tablepost2tag WHERE tag_id IN ($tagidstr)");

					$wpdb->query("DELETE FROM $tabletags WHERE tag_id IN ($tagidstr)");
				}
			}
		}
	}








	/* Functions for the tags associated with a post */
	function ShowTagsForPost($postID, $format, $limit=0) {
		echo $this->FormatTags($this->GetTagsForPost($postID, $limit), $format);
	}

	function GetTagsForPost($postID, $limit = 0) {
		global $tabletags, $tablepost2tag, $wpdb;

if (!$postID) return;
		if ($limit != 0) {
			$limitclause = "LIMIT $limit";
		}

		$q = "SELECT DISTINCT t.tag FROM $tabletags t INNER JOIN $tablepost2tag p2t ON p2t.tag_id = t.tag_id INNER JOIN $wpdb->posts p ON p2t.post_id = p.ID AND p.ID=$postID ORDER BY t.tag ASC $limitclause";
		return($wpdb->get_results($q));
	}

	function GetPostsForTag($tag) {
		global $tabletags, $tablepost2tag, $wpdb;

		if (is_object($tag)) {
			$tag = $tag->tag;
		}

		$now = current_time('mysql', 1);

		   $q = <<<SQL
		SELECT * from
			$tabletags t, $tablepost2tag p2t, $wpdb->posts p
		WHERE t.tag_id = p2t.tag_id
		  AND p.ID = p2t.post_id
		  AND t.tag = '$tag'
		  AND post_date_gmt < '$now'
		  AND post_status = 'publish'
		ORDER BY post_date desc
SQL;

		   return ($wpdb->get_results($q));
	}

	function GetPostHasTags($postID) {
		global $tabletags, $tablepost2tag, $wpdb;

		$q = "SELECT count(*) FROM $tabletags t INNER JOIN $tablepost2tag p2t ON p2t.tag_id = t.tag_id INNER JOIN $wpdb->posts p ON p2t.post_id = p.ID AND p.ID=$postID";
		return($wpdb->get_var($q) > 0);
	}

	function ClearSynonymsForTag($tagid="") {
		global $tabletags, $tabletag_synonyms, $wpdb;

		if ($tag) {
			if (is_object($tag)) {
				$tag = $tag->tag;
			}
			// XXX: Fix me when you need me.
		} else {
			return $wpdb->query("DELETE FROM $tabletag_synonyms WHERE tag_id = $tagid");
		}
	}

	function GetSynonymsForTag($tag="", $tagid="") {
		global $tabletags, $tabletag_synonyms, $wpdb;

		if ($tag) {
			if (is_object($tag)) {
				$tag = $tag->tag;
			}
			return $wpdb->get_results("SELECT ts.synonym as tag, ts.tagsynonymid as tag_id FROM $tabletags t INNER JOIN $tabletag_synonyms ts ON t.tag_id = ts.tag_id WHERE t.tag = '$tag'");
		} else {
			return $wpdb->get_results("SELECT ts.synonym as tag, ts.tagsynonymid as tag_id FROM $tabletag_synonyms ts WHERE ts.tag_id = $tagid");
		}
	}

	function ShowSynonymsForTag($tag, $format, $limit=0) {
		echo $this->FormatTags($this->GetSynonymsForTag($tag), $format);
	}

	function AddSynonymForTag($tag='', $tagid='', $synonym) {
		global $tabletags, $tabletag_synonyms, $wpdb;

		$synonym = trim($synonym);

		$q = "SELECT count(*) FROM $tabletags WHERE tag = '$synonym'";

		if ($wpdb->get_var($q) == 0) {
			if (!$tagid) {
				$tagid = $wpdb->get_var("SELECT tag_id FROM $tabletags WHERE tag = '$tag'");
			}

			if ($tagid) {
				$wpdb->query("INSERT INTO $tabletag_synonyms (tag_id, synonym) VALUES ($tagid, '$synonym')");
			} else {
				return "Tag $tagid doesn't exist!";
			}
		} else {
			return "$synonym already exists as a tag.";
		}
	}



	function GetCanonicalTag($tag) {
		global $tabletags, $tabletag_synonyms, $wpdb;

		$truetag = $wpdb->get_var("select tag from $tabletags where tag = '$tag'");

		if ($truetag) {
			return $truetag;
		} else {
			$synonym = $wpdb->get_var("select t.tag from $tabletags t INNER JOIN $tabletag_synonyms ts ON t.tag_id = ts.tag_id WHERE synonym = '$tag'");

			return $synonym;
		}
		return $tag;
	}













	/* Functions for the related tags */
	function ShowRelatedTags($tags, $format, $limit=0) {
		echo $this->FormatTags($this->GetRelatedTags($tags, $limit), $format);
	}

	function GetRelatedTags($tags, $limit = 0) {
		global $wpdb, $tabletags, $tablepost2tag;

		$now = current_time('mysql', 1);

		$taglist = "'" . $tags[0]->tag . "'";
		$tagcount = count($tags);
		if ($tagcount > 1) {
			for ($i = 1; $i <= $tagcount; $i++) {
				$taglist = $taglist . ", '" . urldecode($tags[$i]->tag) . "'";
			}
		}

		$q = <<<SQL
		SELECT p2t.post_id
			 FROM $tablepost2tag p2t, $tabletags t, $wpdb->posts p
			 WHERE p2t.tag_id = t.tag_id
			 AND p2t.post_id = p.ID
			 AND (t.tag IN ($taglist))
			 AND post_date_gmt < '$now'
			 AND post_status = 'publish'
			 GROUP BY p2t.post_id HAVING COUNT(p2t.post_id)=$tagcount
			 ORDER BY t.tag ASC
SQL;
		$postids = $wpdb->get_results($q);
		if ($postids) {

			$postidlist = $postids[0]->post_id;

			for ($i = 1; $i <= count($postids); $i++) {
				$postidlist = $postidlist . ", '" . $postids[$i]->post_id . "'";
			}

			if ($limit != 0) {
				$limitclause = "LIMIT $limit";
			}

			$q = <<<SQL
		SELECT t.tag, COUNT(p2t.post_id) AS count
		FROM $tablepost2tag p2t, $tabletags t, $wpdb->posts p
		WHERE p2t.post_id IN ($postidlist)
		AND p2t.post_id = p.ID
		AND t.tag NOT IN ($taglist)
		AND t.tag_id = p2t.tag_id
		AND post_date_gmt < '$now'
		AND post_status = 'publish'
		GROUP BY p2t.tag_id
		ORDER BY count DESC, t.tag ASC
		$limitclause
SQL;

			return $wpdb->get_results($q);
		}
	}

	function ShowRelatedPosts($tags, $format, $limit=0) {
		echo $this->FormatPosts($this->GetRelatedPosts($tags, $limit), $format);
	}

	function GetRelatedPosts($tags, $limit = 0) {
		global $wpdb, $tabletags, $tablepost2tag, $post;

		$now = current_time('mysql', 1);

		$taglist = "'" . $tags[0]->tag . "'";
		$tagcount = count($tags);
		if ($tagcount > 1) {
			for ($i = 1; $i <= $tagcount; $i++) {
				$taglist = $taglist . ", '" . urldecode($tags[$i]->tag) . "'";
			}
		}

		if ($post->ID) {
			$notclause = "AND p.ID != $post->ID";
		}

		if ($limit != 0) {
			$limitclause = "LIMIT $limit";
		}

		$q = <<<SQL
		SELECT DISTINCT p.*, count(p2t.post_id) as cnt
			 FROM $tablepost2tag p2t, $tabletags t, $wpdb->posts p
			 WHERE p2t.tag_id = t.tag_id
			 AND p2t.post_id = p.ID
			 AND (t.tag IN ($taglist))
			 AND post_date_gmt < '$now'
			 AND post_status = 'publish'
			 $notclause
			 GROUP BY p2t.post_id
			 ORDER BY cnt desc
			 $limitclause
SQL;

		return $wpdb->get_results($q);
	}











	/* Functions for popular tags */
	function ShowPopularTags($maximum, $format, $order='count', $direction='desc') {
		echo $this->FormatTags($this->GetPopularTags($maximum, $order, $direction), $format);
	}

	function GetPopularTags($maximum, $order, $direction) {
		global $wpdb, $tabletags, $tablepost2tag;

		if ($order <> "tag" && $order <> "count") { $order = "tag"; }
		if ($direction <> "asc" && $direction <> "desc") { $direction = "asc"; }

		$now = current_time('mysql', 1);

		$query = <<<SQL
			select tag, t.tag_id, count(p2t.post_id) as count
			from $tabletags t inner join $tablepost2tag p2t on t.tag_id = p2t.tag_id
							  inner join $wpdb->posts p on p2t.post_id = p.ID
			 WHERE post_date_gmt < '$now'
			 AND post_status = 'publish'
			group by t.tag
			having count > 0
			order by $order $direction
SQL;
		if ($maximum > 0) {
			$query .= " limit $maximum";
		}

		return $wpdb->get_results($query);
	}

	function GetWeightedTags($order, $direction, $limit = 150) {
		global $wpdb, $tabletags, $tablepost2tag;

		if ($order <> "tag" && $order <> "weight") { $order = "weight"; }
		if ($direction <> "asc" && $direction <> "desc") { $direction = "desc"; }


		if ($order == "tag" && $direction == "asc") {
			$sort = "SortWeightedTagsAlphaAsc";
			$orderclause = "order by weight desc";
		} else if ($order == "tag" && $direction == "desc") {
			$sort = "SortWeightedTagsAlphaDesc";
			$orderclause = "order by weight desc";
		} else if ($order == "weight" && $direction == "asc") {
			$sort = "SortWeightedTagsWeightAsc";
			$orderclause = "order by weight asc";
		} else if ($order == "weight" && $direction == "desc") {
			$sort = "SortWeightedTagsWeightDesc";
			$orderclause = "order by weight desc";
		}


		$totaltags = $this->GetDistinctTagCount();
		$maxtag = $this->GetMostPopularTagCount();

		if ($totaltags == 0 || $maxtag == 0) {
			return;
		}

		$now = current_time('mysql', 1);

		if ($limit != 0) {
			$limitclause = "LIMIT $limit";
		}

		$query = <<<SQL
			select tag, count(p2t.post_id) as count, ((count(p2t.post_id)/$totaltags)*100) as weight, ((count(p2t.post_id)/$maxtag)*100) as relativeweight
			from $tabletags t inner join $tablepost2tag p2t on t.tag_id = p2t.tag_id
							  inner join $wpdb->posts p on p2t.post_id = p.ID
			 WHERE post_date_gmt < '$now'
			 AND post_status = 'publish'

			group by t.tag
			$orderclause
			$limitclause
SQL;

		$results = $wpdb->get_results($query);

		usort($results, array("UltimateTagWarriorCore",$sort));

		if ($limit != 0) {
			$results = array_slice($results, 0, $limit);
		}

		$distinctweights = array();
		foreach($results as $result) {
			$weight = $result->relativeweight;
			if (!array_key_exists($weight, $distinctweights)) {
				$distinctweights[$weight] = $weight;
			}
		}

		sort($distinctweights, SORT_NUMERIC);

		$finalresults = array();
		foreach($results as $result) {
			$result->weightrank =  ((array_search($result->relativeweight, $distinctweights) + 1) / (count($distinctweights))) * 100;
			$finalresults[] = $result;
		}

		return $finalresults;
	}

	function SortWeightedTagsAlphaAsc($x, $y) {
		return strcmp(strtolower($x->tag), strtolower($y->tag));
	}

	function SortWeightedTagsAlphaDesc($x, $y) {
		return strcmp(strtolower($y->tag), strtolower($x->tag));
	}

	function SortWeightedTagsWeightAsc($x, $y) {
		if($x->weight > $y->weight) return 1;
		if($x->weight < $y->weight) return -1;
		return strcmp(strtolower($x->tag), strtolower($y->tag));
	}

	function SortWeightedTagsWeightDesc($x, $y) {
		if($y->weight > $x->weight) return 1;
		if($y->weight < $x->weight) return -1;
		return strcmp(strtolower($y->tag), strtolower($x->tag));
	}

	function GetDistinctTagCount() {
		global $wpdb, $tablepost2tag;

		return $wpdb->get_var("select count(*) from $tablepost2tag p2t inner join $wpdb->posts p on p2t.post_id = p.ID WHERE post_date_gmt < '" . current_time('mysql', 1) . "' AND post_status = 'publish'");
	}

	function GetMostPopularTagCount() {
		global $wpdb, $tabletags, $tablepost2tag;

		return $wpdb->get_var("select count(p2t.post_id) cnt from $tabletags t inner join $tablepost2tag p2t on t.tag_id = p2t.tag_id inner join $wpdb->posts p on p2t.post_id = p.ID WHERE post_date_gmt < '" . current_time('mysql', 1) . "' AND post_status = 'publish' group by t.tag order by cnt desc limit 1");
	}








	/* Functions for formatting things*/
	function FormatTags($tags, $format, $limit = 0) {
		if (is_array($format) && $format["pre"]) {
			$out .= $this->FormatTag(null, $format["pre"]);
		}

		if ($limit != 0 && is_array($tags)) {
			$tags = array_slice($tags, 0, $limit);
		}

		if ((!is_array($tags) || count($tags) == 1) && $tags[0] && (is_array($format) && $format["single"])) {
			$out .= $this->FormatTag($tags[0], $format["single"]);
		} else {

			if ($tags) {
				for ($i = 0; $i < count($tags); $i++) {
					if (is_array($format)) {
						if ($i == 0 && $format["first"]) {
							$out .= $this->FormatTag($tags[$i], $format["first"]);
						} else if ($i == (count($tags) -1) && $format["last"]) {
							$out .= $this->FormatTag($tags[$i], $format["last"]);
						} else {
							$out .= $this->FormatTag($tags[$i], $format["default"]);
						}
					} else {
						$out .= $this->FormatTag($tags[$i], $format);
					}
				}
			} else {
				if (is_array($format) && $format["none"]) {
					$out .= $format["none"];
				}
			}
		}

		if (is_array($format) && $format["post"]) {
			$out .= $this->FormatTag(null, $format["post"]);
		}

		return $out;
	}

	function FormatTag($tag, $format) {
		global $install_directory;

		$tag_display = str_replace('_',' ', $tag->tag);
		$tag_display = str_replace('-',' ',$tag_display);
		$tag_name = strtolower($tag->tag);

		$trati_tag_name = str_replace(' ', '+', $tag_display);
		$flickr_tag_name = str_replace(' ', '', $tag_display);
		$wiki_tag_name = str_replace(' ', '_', $tag_display);
		$gada_tag_name = str_replace(' ', '.',$tag_display);

		$baseurl = get_option('utw_base_url');
		$home = get_option('home');
		$siteurl = get_option('siteurl');

		$prettyurls = get_option('utw_use_pretty_urls');
		$tagset = array();
		$tags = $_GET["tag"];

		$type = "none";

		if ($tags <> "") {
			$tagset = explode(" ", $tags);
			if (count($tagset) == 1) {
				$tagset = explode("|", $tags);
				if (count($tagset) <> 1) {
					$type = "or";
				} else {
					if (strtolower($tagset[0]) == strtolower($tag->tag)) {
						$type = "none";
					} else {
						$type = "single";
					}
				}
			} else {
				$type = "and";
			}
		}

		$iconsettings = explode('|', get_option('utw_icons'));
		$iconformat = '';
		foreach($iconsettings as $iconsetting) {
			switch($iconsetting) {
				case 'Technorati':
					$iconformat .= '%technoratiicon%';
					break;
				case 'Flickr':
					$iconformat .= '%flickricon%';
					break;

				case 'delicious':
					$iconformat .= '%deliciousicon%';
					break;

				case 'Wikipedia':
					$iconformat .= '%wikipediaicon%';
					break;

				case 'gadabe':
					$iconformat .= '%gadabeicon%';
					break;

				case 'Zniff':
					$iconformat .= '%znifficon%';
					break;

				case 'RSS':
					$iconformat .= '%rssicon%';
					break;
			}
		}

		if (get_option('utw_trailing_slash') == 'yes') { $trailing = "/"; }

		global $post;

		// This feels so... dirty.
		if ($prettyurls == "yes") {
			$format = str_replace('%tagurl%', "$home$baseurl$tag_name$trailing", $format);
			$format = str_replace('%taglink%', "<a href=\"$home$baseurl$tag_name$trailing\" rel=\"tag\">$tag_display</a>", $format);
			$rssurl = "$home$baseurl$tag_name/feed/rss2";
			$tagseturl = "$home$baseurl" . implode('+', $tagset) . 	"+$tag_name$trailing";
			$unionurl = "$home$baseurl" . implode('|', $tagset) . 	"|$tag_name$trailing";
		} else {
			$format = str_replace('%tagurl%', "$home/index.php?tag=$tag_name", $format);
			$format = str_replace('%taglink%', "<a href=\"$home/index.php?tag=$tag_name\" rel=\"tag\">$tag_display</a>", $format);
			$rssurl = "$home/index.php?tag=$tag_name&feed=rss2";
			$tagseturl = "$home/index.php?tag=" . implode('+', $tagset) . "+$tag_name";
			$unionurl = "$home/index.php?tag=" . implode('|', $tagset) . "|$tag_name";
		}

		$format = str_replace('%tag%', $tag_name, $format);
		$format = str_replace('%tagdisplay%', $tag_display, $format);
		$format = str_replace('%tagcount%', $tag->count, $format);

		$format = str_replace('%tagweight%', $tag->weight, $format);
		$format = str_replace('%tagweightint%', ceil($tag->weight), $format);
		$format = str_replace("%tagweightcolor%", $this->GetColorForWeight($tag->weight), $format);
		$format = str_replace("%tagweightfontsize%", $this->GetFontSizeForWeight($tag->weight), $format);

		$format = str_replace('%tagrelweight%', $tag->relativeweight, $format);
		$format = str_replace('%tagrelweightint%', ceil($tag->relativeweight), $format);
		$format = str_replace("%tagrelweightcolor%", $this->GetColorForWeight($tag->relativeweight), $format);
		$format = str_replace("%tagrelweightfontsize%", $this->GetFontSizeForWeight($tag->relativeweight), $format);

		$format = str_replace('%tagrelweightrank%', $tag->weightrank, $format);
		$format = str_replace('%tagrelweightrankint%', ceil($tag->weightrank), $format);
		$format = str_replace("%tagrelweightrankcolor%", $this->GetColorForWeight($tag->weightrank), $format);
		$format = str_replace("%tagrelweightrankfontsize%", $this->GetFontSizeForWeight($tag->weightrank), $format);

		$format = str_replace('%technoratitag%', "<a href=\"http://www.technorati.com/tag/$trati_tag_name\" rel=\"tag\">$tag_display</a>", $format);
		$format = str_replace('%flickrtag%', "<a href=\"http://www.flickr.com/photos/tags/$flickr_tag_name\" rel=\"tag\">$tag_display</a>", $format);
		$format = str_replace('%delicioustag%', "<a href=\"http://del.icio.us/tag/$tag_name\" rel=\"tag\">$tag_display</a>", $format);
		$format = str_replace('%wikipediatag%', "<a href=\"http://en.wikipedia.org/wiki/$wiki_tag_name\" rel=\"tag\">$tag_display</a>", $format);
		$format = str_replace('%gadabetag%', "<a href=\"http://$gada_tag_name.gada.be\" rel=\"tag\">$tag_display</a>", $format);
		$format = str_replace('%znifftag%', "<a href=\"http://zniff.com/?s=%22$trati_tag_name%22&amp;sort=\"rel=\"tag\">$tag_display</a>", $format);
		$format = str_replace('%rsstag%', "<a href=\"$rssurl\" rel=\"tag\">RSS</a>", $format);

		$format = str_replace('%icons%', $iconformat, $format);

$format = str_replace('%technoratiicon%', "<a href=\"http://www.technorati.com/tag/$trati_tag_name\"><img src=\"/images/icons/tbubble.gif\" border=\"0\" hspace=\"1\" alt=\"\"/></a>", $format);

		$format = str_replace('%technoratiicon%', "<a href=\"http://www.technorati.com/tag/$trati_tag_name\"><img src=\"$siteurl/wp-content/plugins$install_directory/technoratiicon.jpg\" border=\"0\" hspace=\"1\" alt=\"\"/></a>", $format);
		$format = str_replace('%flickricon%', "<a href=\"http://www.flickr.com/photos/tags/$flickr_tag_name\"><img src=\"$siteurl/wp-content/plugins$install_directory/flickricon.jpg\" border=\"0\" hspace=\"1\"/></a>", $format);
		$format = str_replace('%deliciousicon%', "<a href=\"http://del.icio.us/tag/$tag_name\"><img src=\"$siteurl/wp-content/plugins$install_directory/deliciousicon.jpg\" border=\"0\" hspace=\"1\"/></a>", $format);
		$format = str_replace('%wikipediaicon%', "<a href=\"http://en.wikipedia.org/wiki/$wiki_tag_name\"><img src=\"$siteurl/wp-content/plugins$install_directory/wikiicon.jpg\" border=\"0\" hspace=\"1\"/></a>", $format);
		$format = str_replace('%gadabeicon%', "<a href=\"http://$gada_tag_name.gada.be\"><img src=\"$siteurl/wp-content/plugins$install_directory/gadaicon.jpg\" border=\"0\" hspace=\"1\"/></a>", $format);
		$format = str_replace('%znifficon%', "<a href=\"http://zniff.com/?s=%22$trati_tag_name%22&amp;sort=\"rel=\"tag\" target=\"_blank\"><img src=\"$siteurl/wp-content/plugins$install_directory/znifficon.jpg\" border=\"0\" hspace=\"1\"/></a>", $format);
		$format = str_replace('%rssicon%', "<a href=\"$rssurl\"><img src=\"$siteurl/wp-content/plugins$install_directory/rssicon.jpg\" border=\"0\" hspace=\"1\"/></a>", $format);

		$format = str_replace('%intersectionurl%', $tagseturl, $format);
		$format = str_replace('%unionurl%', $unionurl, $format);

		if ($type == "and" || $type == "single") {
			$format = str_replace('%intersectionicon%', "<a href=\"$tagseturl\"><img src=\"$siteurl/wp-content/plugins$install_directory/intersectionicon.jpg\" border=\"0\" hspace=\"1\"/></a>", $format);
			$format = str_replace('%intersectionlink%', "<a href=\"$tagseturl\">+</a>", $format);
		} else {
			$format = str_replace('%intersectionicon%','',$format);
			$format = str_replace('%intersectionlink%','',$format);
		}

		if ($type == "or" || $type == "single") {
			$format = str_replace('%unionicon%', "<a href=\"$unionurl\"><img src=\"$siteurl/wp-content/plugins$install_directory/unionicon.jpg\" border=\"0\" hspace=\"1\"/></a>", $format);
			$format = str_replace('%unionlink%', "<a href=\"$unionurl\">|</a>", $format);

		} else {
			$format = str_replace('%unionicon%','',$format);
			$format = str_replace('%unionlink%','',$format);
		}

		if ($type == "or") {
			$format = str_replace('%operatortext%', 'or',$format);
			$format = str_replace('%operatorsymbol%', '|',$format);
		} else if ($type == "and") {
			$format = str_replace('%operatortext%', 'and',$format);
			$format = str_replace('%operatorsymbol%', '+',$format);
		} else {
			$format = str_replace('%operatortext%', '',$format);
			$format = str_replace('%operatorsymbol%', '',$format);
		}



		if ($post->ID) {
			$format = str_replace('%postid%', $post->ID, $format);
		} else {
			$format = str_replace('%postid%', $_REQUEST["post"], $format);
		}
		return $format;
	}

	function FormatPosts($posts, $format) {

		if (is_array($format) && $format["pre"]) {
			$out .= $format["pre"];
		}

		if ($posts) {
			for ($i = 0; $i < count($posts); $i++) {
				if (is_array($format)) {
					if ($i == 0 && $format["first"]) {
						$out .= $this->FormatPost($posts[$i], $format["first"]);
					} else if ($i == (count($posts) -1) && $format["last"]) {
						$out .= $this->FormatPost($posts[$i], $format["last"]);
					} else {
						$out .= $this->FormatPost($posts[$i], $format["default"]);
					}
				} else {
					$out .= $this->FormatPost($posts[$i], $format);
				}
			}
		} else {
			if (is_array($format) && $format["none"]) {
				$out .= $format["none"];
			}
		}

		if (is_array($format) && $format["post"]) {
			$out .= $format["post"];
		}

		return $out;
	}

	function FormatPost($post, $format) {
		$url = get_permalink($post->ID);

		$format = str_replace('%title%', $post->post_title, $format);
		$format = str_replace('%postlink%', "<a href=\"$url\">$post->post_title</a>", $format);
		$format = str_replace('%excerpt%', $post->post_excerpt, $format);

		return $format;
	}

	var $predefinedFormats = array();

	function GetFormatForType($formattype) {
		global $user_level, $post, $lzndomain, $predefinedFormats;

		if ($post->ID) {
			$postid = $post->ID;
		} else {
			$postid = $_REQUEST["post"];
		}

		if (count($predefinedFormats) == 0) {
			$predefinedFormats["tagsetsimplelist"] = array('first'=>'%taglink%', 'default'=>' %operatortext% %taglink%');
			$predefinedFormats["tagsetcommalist"] = array('first'=>'%taglink%', 'default'=>', %taglink%', 'last'=>' %operatortext% %taglink%');
			$predefinedFormats["simplelist"] = array ("default"=>"%taglink% ", "none"=>__("No Tags", $lzndomain) );
			$predefinedFormats["iconlist"] = array ("default"=>"%taglink% %icons% ", "none"=>__("No Tags", $lzndomain) );
			$predefinedFormats["htmllist"] = array ("default"=>"<li>%taglink%</li>", "none"=>"<li>" . __("No Tags", $lzndomain) . "</li>");
			$predefinedFormats["htmllisticons"] = array ("default"=>"<li>%icons%%taglink%</li>", "none"=>"<li>" . __("No Tags", $lzndomain) . "</li>");
			$predefinedFormats["htmllistandor"] = array ("default"=>"<li>%taglink% %intersectionlink% %unionlink%</li>","none"=>"<li>" . __("No Tags", $lzndomain) . "</li>");
			$predefinedFormats["commalist"] = array ("default"=>", %taglink%", "first"=>"%taglink%", "none"=>__("No Tags", $lzndomain) );
			$predefinedFormats["commalisticons"] = array ("default"=>", %taglink% %icons%", "first"=>"%taglink% %icons%", "none"=>__("No Tags", $lzndomain) );
			$predefinedFormats["technoraticommalist"] = array ("default"=>", %technoratitag%", "first"=>"%technoratitag%", "none"=>__("No Tags", $lzndomain) );
			$predefinedFormats["gadabecommalist"] = array ("default"=>", %gadabetag%", "first"=>"%gadabetag%", "none"=>__("No Tags", $lzndomain) );
			$predefinedFormats["andcommalist"] = array ("default"=>", %taglink% %intersectionlink% %unionlink%", "first"=>"%taglink% %intersectionlink%%unionlink%", "none"=>__("No Tags", $lzndomain) );

			$relStr = "";
			if ($formattype == "superajaxrelated" || $formattype == "superajaxrelateditem") {
				$relStr = "rel";
			}

			$default = "<span id=\"tags-%postid%-%tag%\">%taglink%";
			if ($user_level > 3 && $postid != "") {
				if ($formattype == 'superajaxrelated' || $formattype == 'superajaxrelateditem') {
					$default .= "[<a href=\"javascript:sndReq('add', '%tag%', '%postid%', '$formattype')\">+</a>]";
				} else {
					$default .= "[<a href=\"javascript:sndReq('del', '%tag%', '%postid%', '$formattype')\">-</a>]";
				}
				$aft = " <input type=\"text\" size=\"9\" id=\"addTag-%postid%\" /> <input type=\"button\" value=\"+\" onClick=\"sndReq('add', document.getElementById('addTag-%postid%').value, '%postid%', '$formattype')\" />";
			}

			$default .= "<a href=\"javascript:sndReq('expand$relStr', '%tag%', '%postid%', '$formattype')\">&raquo;</a> </span>";
			$aft .= "</span>";

			$predefinedFormats["superajax"] = array("pre"=>"<span id=\"tags-%postid%\">","default"=>$default, "post"=>"$aft");;
			$predefinedFormats["superajaxitem"] = $default;
			$predefinedFormats["superajaxrelated"] = $default;
			$predefinedFormats["superajaxrelateditem"] = $default;

			$predefinedFormats["linkset"] = "%taglink% %icons%<a href=\"javascript:sndReq('shrink', '%tag%', '%postid%', 'superajaxitem')\">&laquo;</a>&#160;";
			$predefinedFormats["linksetrel"] = "%taglink% %icons%<a href=\"javascript:sndReq('shrink', '%tag%', '%postid%', 'superajaxrelated')\">&laquo;</a>&#160;";

			$predefinedFormats["weightedlinearbar"] = array("default"=>"<td width=\"%tagweightint%%\" style=\"background-color:%tagrelweightcolor%; border-right:1px solid black;\"><a href=\"%tagurl%\" title=\"%tagdisplay%\" style=\"color:%tagrelweightcolor%;\"><div width=\"100%\">&#160;</div></a></td>", "pre"=>"<table cellpadding=\"0\" cellspacing=\"0\" style=\"border:1px solid black; border-right:0px\" width=\"100%\"><tr>", "post"=>"</tr></table>");

				// Thanks http://www.cssirc.com/codes/?code=23!
				$css = <<<CSS
				<style type="text/css">
				.longtail, .longtail li { list-style: none; margin: 0; padding: 0; }
				.longtail li a {text-decoration:none;}
				.longtail {position: relative; height: 100px;}
				.longtail:after { display: block; visibility: hidden; content: "."; height: 0; overflow: hidden; clear: both;}
				.longtail li {float: left; position: relative; height: 100%;width: 5px;margin:0px;background-color:#fff;}
				.longtail li div {position: absolute;bottom: 0; left: 0;width: 100%;background-color:#000;}
				</style>
CSS;

			$predefinedFormats["weightedlongtail"] = array("pre"=>"$css<ol class=\"longtail\">", "default"=>"<li><a href=\"%tagurl%\" title=\"%tagdisplay%\"><div style=\"height:%tagrelweightint%%\">&#160;</div></a></li>", "post"=>"</ol>");;
			$predefinedFormats["weightedlongtailvertical"] = array("pre"=>"<div class=\"longtailvert\">", "default"=>'<div style="background-color:%tagrelweightrankcolor%; width:%tagrelweightint%%; \"><a href="%tagurl%" title="%tagdisplay% (%tagcount%)" style="display:block; ">%tagdisplay%</a></div>', "post"=>"</div>");
			$predefinedFormats["coloredtagcloud"] = array("default"=>"<a href=\"%tagurl%\" title=\"%tagdisplay% (%tagcount%)\" style=\"color:%tagrelweightrankcolor%\">%tagdisplay%</a> ");
			$predefinedFormats["sizedtagcloud"] = array("default"=>"<a href=\"%tagurl%\" title=\"%tagdisplay% (%tagcount%)\" style=\"font-size:%tagrelweightfontsize%\">%tagdisplay%</a> ");
			$predefinedFormats["coloredsizedtagcloud"] = array("default"=>"<a href=\"%tagurl%\" title=\"%tagdisplay% (%tagcount%)\" style=\"font-size:%tagrelweightrankfontsize%; color:%tagrelweightrankcolor%\">%tagdisplay%</a> ");
			$predefinedFormats["sizedcoloredtagcloud"] = array("default"=>"<a href=\"%tagurl%\" title=\"%tagdisplay% (%tagcount%)\" style=\"font-size:%tagrelweightrankfontsize%; color:%tagrelweightrankcolor%\">%tagdisplay%</a> ");

			// Thanks drac! http://lair.fierydragon.org/
			$predefinedFormats["coloredsizedtagcloudwithcount"] = array("default"=>"<a href=\"%tagurl%\" style=\"font-size:%tagrelweightfontsize%; color:%tagrelweightrankcolor%\">%tagdisplay%<sub style=\"font-size:60%; color:#ccc;\">%tagcount%</sub></a> ");
			$predefinedFormats["postsimplelist"] = array ("default"=>"%postlink%");
			$predefinedFormats["postcommalist"] = array ("default"=>", %postlink%", "first"=>"%postlink%", "none"=> __("No Related Posts", $lzndomain));
			$predefinedFormats["posthtmllist"] = array ("default"=>"<li>%postlink%</li>", "none"=>"<li>" . __("No Related Posts", $lzndomain) . "</li>");
		}

		if (array_key_exists($formattype, $predefinedFormats)) {
			return $predefinedFormats[$formattype];
		} else {
			return "";
		}
	}

	function GetPredefinedFormatNames() {
		global $predefinedFormats;
		if (count($predefinedFormats) == 0) {
			$this->GetFormatForType("");
		}
		return array_keys($predefinedFormats);
	}

	/* This is pretty filthy.  Doing math in hex is much too weird.  It's more likely to work,  this way! */
	function GetColorForWeight($weight) {
		if ($weight) {
			$weight = $weight/100;

			$max = get_option ('utw_tag_cloud_max_color');
			$min = get_option ('utw_tag_cloud_min_color');

			$minr = hexdec(substr($min, 1, 2));
			$ming = hexdec(substr($min, 3, 2));
			$minb = hexdec(substr($min, 5, 2));

			$maxr = hexdec(substr($max, 1, 2));
			$maxg = hexdec(substr($max, 3, 2));
			$maxb = hexdec(substr($max, 5, 2));

			$r = dechex(intval((($maxr - $minr) * $weight) + $minr));
			$g = dechex(intval((($maxg - $ming) * $weight) + $ming));
			$b = dechex(intval((($maxb - $minb) * $weight) + $minb));

			if (strlen($r) == 1) $r = "0" . $r;
			if (strlen($g) == 1) $g = "0" . $g;
			if (strlen($b) == 1) $b = "0" . $b;

			return "#$r$g$b";
		}
	}


	function GetFontSizeForWeight($weight) {
		$max = get_option ('utw_tag_cloud_max_font');
		$min = get_option ('utw_tag_cloud_min_font');

		$units = get_option ('utw_tag_cloud_font_units');
		if ($units == "") $units = '%';

		if ($max > $min) {
			$fontsize = (($weight/100) * ($max - $min)) + $min;

		} else {
			$fontsize = (((100-$weight)/100) * ($min - $max)) + $max;
		}

		return intval($fontsize) . $units;
	}
}


/* ultimate_get_posts()
Retrieves the posts for the tags specified in $_GET["tag"].  Gets the intersection when there are multiple tags.
*/
function ultimate_get_posts() {
	global $wpdb, $table_prefix, $posts, $table_prefix, $tableposts, $id, $wp_query, $request, $utw;
	$tabletags = $table_prefix . 'tags';
	$tablepost2tag = $table_prefix . "post2tag";

	$or_query = false;

	$tags = $_GET["tag"];

	$tagset = explode(" ", $tags);

	if (count($tagset) == 1) {
		$tagset = explode("|", $tags);
		$or_query = true;
	}

	$tags = array();
	foreach($tagset as $tag) {
		$tags[] = "'" . $utw->GetCanonicalTag($tag) . "'";
	}

	$tags = array_unique($tags);
	$tagcount = count($tags);

	if (strpos($request, "HAVING COUNT(ID)") == false && !$or_query) {
		$request = preg_replace("/GROUP BY $tableposts.ID /", "GROUP BY $tableposts.ID HAVING COUNT(ID) = $tagcount ", $request);
	}

	$posts = $wpdb->get_results($request);
	// As requested by Splee and copperleaf
	$wp_query->is_home=false;
	// Thanks Mark! http://txfx.net/
	$posts = apply_filters('the_posts', $posts);
	$wp_query->posts = $posts;
	$wp_query->post_count = count($posts);
	update_post_caches($posts);
	if ($wp_query->post_count > 0)
		$wp_query->post = $wp_query->posts[0];
}

?>