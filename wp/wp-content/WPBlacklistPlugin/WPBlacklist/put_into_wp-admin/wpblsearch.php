<?php
require_once('../wp-includes/wp-l10n.php');
require_once('../wp-includes/wpblfunctions.php');
$title = __('WPBlacklist - Search');
$parent_file = 'wpblacklist.php';
require_once('admin-header.php');

function add_magic_quotes($array) {
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            $array[$k] = add_magic_quotes($v);
        } else {
            $array[$k] = addslashes($v);
        }
    }
    return $array;
}

if (!get_magic_quotes_gpc()) {
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

$rb_search = 0;
$wpvarstoreset = array('action','rb_search','search','delete_comments', 'deladd');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (empty($_POST["$wpvar"])) {
		if (empty($_GET["$wpvar"])) {
			if (!isset($$wpvar)) {
				$$wpvar = '';
			}
		} else {
			$$wpvar = $_GET["$wpvar"];
		}
	} else {
		$$wpvar = $_POST["$wpvar"];
	}
}

if ($user_level < 3) {
?>
	<div class="wrap">
		<p>
			You don&#8217;t have sufficient rights to work with comments, you&#8217;ll have to wait for an admin to raise your level to 3, in order to be authorized to work with comments.<br />
			You can also <a href="mailto:<?php echo $admin_email ?>?subject=Plugin permission">e-mail the admin</a> to ask for a promotion.<br />
			When you&#8217;re promoted, just reload this page to work on the comment searching in WPBlacklist. :)
		</p>
	</div>
<?php
	exit();
} // $user_level <= 0
?>
<ul id="adminmenu2">
  <li><a href="wpblacklist.php"><?php _e('Manage') ?></a></li>
  <li><a href="wpblsearch.php" class="current"><?php _e('Search') ?></a></li>
  <li class="last"><a href="wpblmoderate.php"><?php _e('Moderate') ?></a></li>
</ul>
<script type="text/javascript">
<!--
function checkAll(form)
{
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
	}
}
//-->
</script>
<div class="wrap">
<p>
<?php _e('You can search your existing comments using multiple options and delete any of the results of your searches. In addition, you can also use the "Delete & Add" button (where relevant) to add the search expression you specified to your blacklist while deleting the checked entries. Please note that searching all comments using the blacklist might take a longtime and so it is better to specify a number of comments to search.') ?>
</p>
<form name="searchform" action="wpblsearch.php?action=search" method="post">
	<fieldset>
		<legend><strong><?php _e('Search ...') ?></strong></legend>
		<label>
			<input name="rb_search" type="radio" value="0" <?php echo ($rb_search==0 ? 'checked' : '') ?> />
			<?php _e('Using blacklist (specify number of comments to search or blank for all)') ?>
		</label><br />
		<label>
			<input name="rb_search" type="radio" value="1" <?php echo ($rb_search==1 ? 'checked' : '') ?> />
			<?php _e('For given IP') ?>
		</label><br />
		<label>
			<input name="rb_search" type="radio" value="2" <?php echo ($rb_search==2 ? 'checked' : '') ?> />
			<?php _e('For given expression') ?>
		</label><br />
		<input type="text" name="search" value="<?php echo $search; ?>" size="17" />
		<input type="submit" name="submit" value="<?php _e('Search') ?>"  />
	</fieldset>
</form>
<?php
if (($action == 'delete') && !empty($delete_comments)) {
	// check permissions on each comment before deleting
	$del_comments = '';
	$safe_delete_commeents = '';
	$i = 0;
	foreach ($delete_comments as $comment) { // Check the permissions on each
		$comment = intval($comment);
		$post_id = $wpdb->get_var("SELECT comment_post_ID FROM $tablecomments WHERE comment_ID = $comment");
		$authordata = get_userdata($wpdb->get_var("SELECT post_author FROM $tableposts WHERE ID = $post_id"));
		if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
			$wpdb->query("DELETE FROM $tablecomments WHERE comment_ID = $comment");
			++$i;
		}
	}
	echo "<p><strong>" . sprintf(__('%s comments deleted.'), $i);
	// was this an add & delete operation - if so, add search item to blacklist
	if ($deladd <> '') {
		if ($rb_search == 1) {
			$answer = "IP : $search ";
			$search = sanctify($search);
			$sql = "INSERT INTO blacklist (regex,regex_type) VALUES ('$search','ip')";
		} else {
			$search = sanctify($search);
			$answer = "Expression : $search";
			$sql = "INSERT INTO blacklist (regex,regex_type) VALUES ('$search','url')";
		}
		$request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$search'");
		if (!$request) {
			$request = $wpdb->query($sql);
			if (!$request) {
				$answer = $answer . " could not be added!";
			} else {
				$answer = $answer . " successfully added!";
			}
		} else {
			$answer = $answer . " already exists in blacklist!";
		}
		echo "<br />$answer";
    }
	echo "</strong></p>";
}
if (($action == 'search') || ($action == 'delete')) {
	$search = $wpdb->escape($search);
	$sql = '';
	$valid = True;
	// do the search based on type
    switch ($rb_search) {
		case 0:
			// search blacklist
            $sql = "SELECT * FROM $tablecomments ORDER BY comment_date DESC";
			if (!empty($search)) {
				if (is_numeric($search)) {
					$sql = $sql . " LIMIT $search";
				} else {
					$valid = False;
					$sql = '';
				}
			}
			// if it's a valid query, then build the final query based on blacklist
            if (!empty($sql)) {
				$comments = $wpdb->get_results($sql);
				$sql = '';
				if ($comments) {
					foreach ($comments as $comment) {
						$next = False;
						// IP check
						$sites = $wpdb->get_results("SELECT regex FROM blacklist WHERE regex_type='ip'");
						if ($sites) {
							foreach ($sites as $site)  {
								$regex = "/^$site->regex/";
								if (preg_match($regex, $comment->comment_author_ip)) {
									$next = True;
									break;
								}
							}
						}
						// RBL check
						if (!$next) {
							$sites = $wpdb->get_results("SELECT regex FROM blacklist WHERE regex_type='rbl'");
							if ($sites) {
								foreach ($sites as $site)  {
									$regex = $site->regex;
									if (preg_match("/([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)/", $comment->comment_author_ip, $matches)) {
										$rblhost = $matches[4] . "." . $matches[3] . "." . $matches[2] . "." . $matches[1] . "." . $regex;
										$resolved = gethostbyname($rblhost);
										if ($resolved != $rblhost) {
											$next = True;
											break;
										}
									}
								}
							}
						}
						// expression check
						if (!$next) {
							$sites = $wpdb->get_results("SELECT regex FROM blacklist WHERE regex_type='url'");
							if ($sites) {
								foreach ($sites as $site)  {
									$regex = "/$site->regex/i";
									if (preg_match($regex, $comment->comment_author_url)) {
										$next = True;
										break;
									}
									if (preg_match($regex, $comment->comment_author_email)) {
										$next = True;
										break;
									}
									if (preg_match($regex, $comment->comment_content)) {
										$next = True;
										break;
									}
								}
							}
						}
						// was a match found - if so add ID to list
                        if ($next) {
							if (!empty($sql)) {
								$sql = $sql . ',';
							}
							$sql = $sql . strval($comment->comment_ID);
						}
					} // foreach
                    // are there any ID's in list - if so build query
                    if (!empty($sql)) {
						$sql = "SELECT * FROM $tablecomments WHERE comment_ID IN (" . $sql . ')';
					}
				}
			}
			break;

		case 1:
			// search by IP
            if (!empty($search)) {
				$sql = "SELECT * FROM $tablecomments WHERE comment_author_IP LIKE ('$search%') " .
					"ORDER BY comment_date DESC";
			} else {
				$valid = False;
			}
			break;

		case 2:
			// search by expression
			if (!empty($search)) {
				$sql = "SELECT * FROM $tablecomments ORDER BY comment_date DESC";
				$comments = $wpdb->get_results($sql);
				$sql = '';
				if ($comments) {
					foreach ($comments as $comment) {
						$next = False;
						// regular expression/URL check
						$sites = $wpdb->get_results("SELECT regex FROM blacklist WHERE regex_type='url'");
						if ($sites) {
							foreach ($sites as $site)  {
								$regex = "/$site->regex/i";
								if (preg_match($regex, $comment->comment_author_url)) {
									$next = True;
									break;
								}
								if (preg_match($regex, $comment->comment_author_email)) {
									$next = True;
									break;
								}
								if (preg_match($regex, $comment->comment_content)) {
									$next = True;
									break;
								}
							}
						}
						// was a match found - if so add ID to list
                        if ($next) {
							if (!empty($sql)) {
								$sql = $sql . ',';
							}
							$sql = $sql . strval($comment->comment_ID);
						}
					} // foreach
                    // are there any ID's in list - if so build query
                    if (!empty($sql)) {
						$sql = "SELECT * FROM $tablecomments WHERE comment_ID IN (" . $sql . ')';
					}
				}
			} else {
				$valid = False;
			}


            if (!empty($search)) {
				$sql = strtoupper($search);
				$sql = "SELECT * FROM $tablecomments WHERE UPPER(comment_author) LIKE '%$sql%' OR " .
					"UPPER(comment_author_email) LIKE '%$sql%' OR UPPER(comment_author_url) LIKE ('%$sql%') OR " .
					"UPPER(comment_content) LIKE ('%$sql%') ORDER BY comment_date DESC";
			} else {
				$valid = False;
			}
			break;
	}
    // catch errors on blank search condition
    if (!empty($sql)) {
		$comments = $wpdb->get_results($sql);
		if ($comments) {
			echo '<form name="deletecomments" id="deletecomments" action="wpblsearch.php?action=delete" method="post">
					<input name="search" type="hidden" value="' . $search . '">
					<input name="rb_search" type="hidden" value="' . $rb_search . '">
					<table width="100%" cellpadding="3" cellspacing="3">
						<tr>
						  <th scope="col">*</th>
						  <th scope="col">' .  __('Name') . '</th>
						  <th scope="col">' .  __('Email') . '</th>
						  <th scope="col">' . __('IP') . '</th>
						  <th scope="col">' . __('Comment Excerpt') . '</th>
						  <th scope="col" colspan="3">' .  __('Actions') . '</th>
						</tr>';
			foreach ($comments as $comment) {
				$authordata = get_userdata($wpdb->get_var("SELECT post_author FROM $tableposts WHERE ID = $comment->comment_post_ID"));
				$bgcolor = ('#eee' == $bgcolor) ? 'none' : '#eee';
?>
				<tr style='background-color: <?php echo $bgcolor; ?>'>
				  <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { ?><input type="checkbox" name="delete_comments[]" value="<?php echo $comment->comment_ID; ?>" /><?php } ?></td>
				  <td><?php comment_author_link() ?></td>
				  <td><?php comment_author_email_link() ?></td>
				  <td><a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></td>
				  <td><?php comment_excerpt(); ?></td>
				  <td><a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php comment_ID() ?>" class="edit"><?php _e('View') ?></a></td>
				  <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
				  echo "<a href='post.php?action=editcomment&amp;comment=$comment->comment_ID' class='edit'>" .  __('Edit') . "</a>"; } ?></td>
				  <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
						  echo "<a href=\"post.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('" . sprintf(__("You are about to delete this comment by \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), $comment->comment_author) . "')\"    class='delete'>" . __('Delete') . "</a>"; } ?></td>
				</tr>
<?php
			} // end foreach
?>
			</table>
			<p>
				<a href="javascript:;" onclick="checkAll(document.getElementById('deletecomments')); return false; "><?php _e('Invert Checkbox Selection') ?></a>
			</p>
			<p style="text-align: right;">
<?php
			if ($rb_search > 0) {
?>
				<input type="submit" name="deladd" value="<?php _e('Delete & Add') ?>" onclick="return confirm('<?php _e("You are about to delete these comments permanently \\n  \'Cancel\' to stop, \'OK\' to delete.") ?>')" />
<?php
			} // $rb_search > 0
?>
				<input type="submit" name="Submit" value="<?php _e('Delete Checked') ?>" onclick="return confirm('<?php _e("You are about to delete these comments permanently \\n  \'Cancel\' to stop, \'OK\' to delete.") ?>')" />
			</p>
		</form>
<?php
		} else {
?>
		<p>
			<strong><?php _e('No results found.') ?></strong>
		</p>
<?php
		} // end if ($comments)
	} else {
		if ($valid) {
			// the blacklist-based search will end up here when there are no results
			echo '<p><strong>';
			_e('No results found.');
			echo '</strong></p>';
		} else {
			echo '<p><strong>';
			_e('Please enter a valid search expression!');
			echo '</strong></p>';
		}
	} // !empty($sql)
} // if ($action == 'search') || ($action == 'delete')
?>

</div>
<?php
include('admin-footer.php');
?>
