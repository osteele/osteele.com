<?php
// Basic plugins
// A bunch of simple plugin classes, all lumped into one single file

class sk2_user_level_plugin extends sk2_plugin
{
	var $name = "User Level";
	var $description = "";
	var $author = "";
	var $plugin_help_url = "http://wp-plugins.net/wiki/index.php/SK2_BasicChecks_Plugin";
	var $filter = true;
	var $settings_format = array ("min_level" => array("type" => "text", "value"=> 1, "caption" => "Automatically approve logged-in users above or equal to level:", "size" => 3));
	var $skip_under = -50;
	var $skip_above = 20;
	
	
	function filter_this(&$cmt_object)
	{
		if (! $cmt_object->is_comment())
			return;
		
		$min_level = $this->get_option_value('min_level');
		
		if ($cmt_object->user_id > 0)
		{
			if ($cmt_object->user_level < $min_level)
				$bonus = $cmt_object->user_level + 1; // should give a little bonus no matter what
			else
				$bonus = 25;
			$log = "Commenter logged in. ID: $cmt_object->user_id, Level: $cmt_object->user_level";
			$this->log_msg($log , 2);
			$this->raise_karma($cmt_object, $bonus, $log);
		}
	}
}

class sk2_entities_plugin extends sk2_plugin
{
	var $name = "Entities Detector";
	var $description = "Detect improper use of HTML entities (used by spammers to foil keyword detection).";
	var $author = "";
	var $plugin_help_url = "http://wp-plugins.net/wiki/index.php/SK2_BasicChecks_Plugin";
	var $filter = true;	
	
	function filter_this(&$cmt_object)
	{
		$this->look_for_entities($cmt_object, "author");
		$this->look_for_entities($cmt_object, "content");
	}
	
	function look_for_entities(&$cmt_object, $part)
	{
		$hit = $letter_entities = 0;
		if ($total = preg_match_all('|&#([0-9]{1,5});|', $cmt_object->$part, $matches))
			foreach($matches[1] as $match)
				if ( (($match >= 65) && ($match <= 90))
					|| (($match >= 97) && ($match <= 122)))
						$letter_entities++;

		if ($double_entities = preg_match_all('|&amp;#[0-9]{1,2};|', $cmt_object->$part, $matches))
		{
			$log = "Comment $part contains $double_entities <em>double</em> entit". ($double_entities > 1 ? "ies" : "y") .  " and $letter_entities regular entit". ($letter_entities > 1 ? "ies" : "y") .  " coding for a letter ($total total).";
			$hit = $double_entities * 5 + $letter_entities *2;
		}
		elseif($letter_entities)
		{
			$log = "Comment $part contains $letter_entities entit". ($letter_entities > 1 ? "ies" : "y") .  " coding for a letter ($total total).";
			$hit = 1+ $letter_entities * 2;
		}

		if ($hit)
		{
			$this->log_msg($log , 2);
			$this->hit_karma($cmt_object, $hit, $log);
		}
	
	}
}


class sk2_link_count_plugin extends sk2_plugin
{
	var $name = "Link Counter";
	var $description = "";
	var $author = "";
	var $plugin_help_url = "http://wp-plugins.net/wiki/index.php/SK2_BasicChecks_Plugin";
	var $filter = true;
	var $settings_format = array ("too_many_links" => array("type" => "text", "value"=>2, "caption" => "Penalize if there are more than ", "size" => 3, "after" => "links in the comment content."));
	var $skip_under = -30;
	var $skip_above = 10;
	
	
	function filter_this(&$cmt_object)
	{
		$url_count = count($cmt_object->content_links) +  (0.75 * count($cmt_object->content_url_no_links));
		if (! $url_count)
		{
			if (empty($cmt_object->author_url))
			{
				$log = "Comment contains no URL at all.";
				$this->raise_karma($cmt_object, 2, $log); // only possible abuse might be to try and get many comments approved in abuse to use snowball effect
				$this->log_msg($log , 1);
			}
			else
			{
				$log = "Comment has no URL in content (but one author URL)";
				$this->raise_karma($cmt_object, 0.5, $log); // verrrry light bonus
				$this->log_msg($log , 1);
			}		
			
			return;
		}
		
		$threshold = max($this->get_option_value('too_many_links'), 1);
		$log = "Comment contains: " . count($cmt_object->content_links) ." linked URLs and " . count($cmt_object->content_url_no_links) . " unlinked URLs: total link coef: " . $url_count;

		if ($url_count < $threshold)
		{
			$log .= " < threshold ($threshold).";
			$this->log_msg($log , 1);
		}
		else
		{
			$len =  strlen($cmt_object->content_filtered);
			$chars_per_url = 150;
			$hit = pow($url_count / $threshold, 2) * max(0.20, ($url_count * $chars_per_url / ($len + $chars_per_url)));
			$log .= " >= threshold ($threshold). Non-URL text size: $len chars.";
			$this->hit_karma($cmt_object, 
							$hit, 
							$log);
			$this->log_msg($log . " Hitting for: $hit karma points." , 2);
		}
	}

}

class sk2_old_post_plugin extends sk2_plugin
{
	var $name = "Post Age and Activity";
	var $description = "Stricter on old posts showing no recent activity.";
	var $author = "";
	var $plugin_help_url = "http://wp-plugins.net/wiki/index.php/SK2_BasicChecks_Plugin";
	var $filter = true;
	var $settings_format = array ("old_when" => array("type" => "text", "value"=>15, "caption" => "Consider a post old after ", "size" => 3, "after" => "days."), "still_active" => array("type" => "text", "value"=>2, "caption" => "Still active if more than ", "size" => 3, "after" => "comments recently."));
	var $skip_under = -30;
	var $skip_above = 2;
	
	
	function filter_this(&$cmt_object)
	{
		$post_ts = strtotime($cmt_object->post_date . " GMT");
		$post_timesince = sk2_time_since($post_ts);
		$old_when = max($this->get_option_value('old_when'), 1);
		$still_active = max($this->get_option_value('still_active'), 1);

		global $wpdb;
		
		$count_cmts = $wpdb->get_var("SELECT COUNT(*) AS `cmt_count` FROM `$wpdb->comments` AS `comments` WHERE `comments`.`comment_ID` != $cmt_object->ID AND `comment_post_ID` = $cmt_object->post_ID AND `comment_approved` = '1' AND `comment_date_gmt` > DATE_SUB(NOW() , INTERVAL ". $this->get_option_value("old_when") . " DAY) ");

		$log = "Entry posted " . $post_timesince . " ago. $count_cmts comments in the past $old_when days. Current Karma: " . $cmt_object->karma . ".";
		
		if ($post_ts + ($old_when * 86400) < time())
		{
			if (($cmt_object->karma <= 0) && ($count_cmts < $still_active))
			{
				$tot_cmts = 1 + $wpdb->get_var("SELECT COUNT(*) AS `cmt_count` FROM `$wpdb->comments` AS `comments` WHERE `comments`.`comment_ID` != $cmt_object->ID AND `comment_post_ID` = $cmt_object->post_ID AND `comment_approved` = '1'");
				echo mysql_error();
				$hit = ($still_active / $tot_cmts) * min((time() - $post_ts) / ($old_when * 86400), 10) *  min ((1 - $cmt_object->karma) / 5, 2);
				$this->hit_karma($cmt_object, $hit, $log);
				$this->log_msg($log . " Hitting for: ". round($hit, 2) ." points." , 2);
			}
		}
		elseif (($cmt_object->karma > 0)
				&& ($count_cmts > 2 * $still_active))
		{
			$bonus = min (3, ($cmt_object->karma * $count_cmts / (10 * $still_active)));
			$this->raise_karma($cmt_object, $bonus, $log);
			$this->log_msg($log . " Rewarding with: ". round($bonus, 2) ." points." , 2);
		}
	}

}

$this->register_plugin("sk2_user_level_plugin", 1); // so basic we should go there first
$this->register_plugin("sk2_link_count_plugin", 2); // idem
$this->register_plugin("sk2_entities_plugin", 3); 
$this->register_plugin("sk2_old_post_plugin", 7); 


?>