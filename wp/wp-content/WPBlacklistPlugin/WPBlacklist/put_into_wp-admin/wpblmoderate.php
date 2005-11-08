<?php
require_once('../wp-includes/wp-l10n.php');
require_once('../wp-includes/wpblfunctions.php');
$title = __('WPBlacklist - Moderate');
$parent_file = 'wpblacklist.php';
$standalone = 0;
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

$wpvarstoreset = array('btndeladd','btndel','btnapprove','delete_comments');
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
			When you&#8217;re promoted, just reload this page to work on the comment moderation in WPBlacklist. :)
		</p>
	</div>
<?php
	exit();
} // $user_level < 3
?>
<ul id="adminmenu2">
  <li><a href="wpblacklist.php"><?php _e('Manage') ?></a></li>
  <li><a href="wpblsearch.php"><?php _e('Search') ?></a></li>
  <li class="last"><a href="wpblmoderate.php" class="current"><?php _e('Moderate') ?></a></li>
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

<?php
// figure out what the action is
if ($btndeladd <> '') {
	$action = 'deladd';
} else if ($btndel <> '') {
	$action = 'delete';
} else if ($btnapprove <> '') {
	$action = 'approve';
} else {
	$action = '';
}

$cnt = 0;
$add = 0;
switch($action) {
	case 'deladd':
	case 'delete':
		foreach ($delete_comments as $comment) {
			// first get the details and add it to blacklist - if necessary
			if ($action == 'deladd') {
				// Add author e-mail to blacklist
				$details = $wpdb->get_row("SELECT * FROM $tablecomments WHERE comment_ID = $comment");
				$url = sanctify($details->comment_author_email);
				$request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$url'");
				if (!$request) {
					$wpdb->query("INSERT INTO blacklist (regex, regex_type) VALUES ('$url','url')");
					++$add;
				}
				// Add author IP to blacklist
				$url = sanctify($details->comment_author_IP);
				$request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$url'");
				if (!$request) {
					$wpdb->query("INSERT INTO blacklist (regex, regex_type) VALUES ('$url','ip')");
					++$add;
				}
				// get the author's url without the prefix stuff
				$regex   = "/([a-z]*)(:\/\/)([a-z]*\.)?(.*)/i";
				preg_match($regex, $details->comment_author_url, $matches);
				if (strcasecmp('www.', $matches[3]) == 0) {
					$url = $matches[4];
				} else {
					$url = $matches[3] . $matches[4];
				}
				$url = remove_trailer($url);
				$url = sanctify($url);
				$request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$url'");
				if (!$request) {
					$wpdb->query("INSERT INTO blacklist (regex, regex_type) VALUES ('$url','url')");
					++$add;
				}
				// harvest links found in comment
				$regex = "/([a-z]*)(:\/\/)([a-z]*\.)?([^\">\s]*)/im";
				preg_match_all($regex, $details->comment_content, $matches);
				for ($i=0; $i < count($matches[4]); $i++ ) {
					if (strcasecmp('www.', $matches[3][$i]) == 0) {
						$url = $matches[4][$i];
					} else {
						$url = $matches[3][$i] . $matches[4][$i];
					}
					$ps = strrpos($url, '/');
					if ($ps) {
						$url = substr($url, 0, $ps);
					}
					$url = remove_trailer($url);
					$url = sanctify($url);
					$request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$url'");
					if (!$request) {
						$wpdb->query("INSERT INTO blacklist (regex, regex_type) VALUES ('$url','url')");
						++$add;
					}
				} // for
			} // $action == 'deladd'
			wp_set_comment_status($comment, 'delete');
			++$cnt;
		}
		break;

	case 'approve':
		foreach ($delete_comments as $comment) {
			wp_set_comment_status($comment, 'approve');
			++$cnt;
		}
		break;
}
if ($cnt <> 0) {
	echo "<div class='updated'>\n<p>";
	if ('1' == $cnt) {
		$resp = '1 comment ';
	} else {
		$resp = sprintf("%s comments ", $cnt);
	}
	switch ($action) {
		case 'deladd':
			$resp = $resp . 'deleted <br />' . "\n";
			if ($add <> 0) {
				$resp = $resp . sprintf("%s comment details added to blacklist <br />", $add) . "\n";
			}
			break;

		case 'delete':
			$resp = $resp . 'deleted <br />' . "\n";
			break;

		case 'approve':
			$resp = $resp . 'approved <br />' . "\n";
			break;
	}
	echo "$resp</p></div>\n";
}
$comments = $wpdb->get_results("SELECT * FROM $tablecomments WHERE comment_approved = '0'");
if ($comments) {
    // list all comments that are waiting for approval
?>
    <?php _e('<p>This screen allows you to work with comments in the moderation queue. You can approve, delete or delete while adding the author IP, e-mail, URL and any URLs found in the comment body to the blacklist. These are the comments in the moderation queue:</p>') ?>
    <form name="approval" action="wpblmoderate.php" method="post">
		<ol id="comments">
<?php
    foreach($comments as $comment) {
		$comment_date = mysql2date(get_settings("date_format") . " @ " . get_settings("time_format"), $comment->comment_date);
		$post_title = $wpdb->get_var("SELECT post_title FROM $tableposts WHERE ID='$comment->comment_post_ID'");
		echo "\n\t<li id='comment-$comment->comment_ID'>";
?>
			<p>
 		    <?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { ?>
			<input type="checkbox" name="delete_comments[]" value="<?php echo $comment->comment_ID; ?>" /><?php } ?>
			<strong><?php _e('Name:') ?></strong> <?php comment_author() ?>
<?php
		if ($comment->comment_author_email) {
?>
			| <strong><?php _e('Email:') ?></strong> <?php comment_author_email_link() ?>
<?php
		}
		if ($comment->comment_author_url) {
?>
			| <strong><?php _e('URI:') ?></strong> <?php comment_author_url_link() ?>
<?php
		}
?>
			| <strong><?php _e('IP:') ?></strong>
			<a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>">
				<?php comment_author_IP() ?>
			</a></p>
			<?php comment_text() ?>
		</li>
<?php
    } // foreach
?>
    </ol>
    <p class="submit">
		<input type="submit" name="btndeladd" value="<?php _e('Delete & Add') ?>" />
		<input type="submit" name="btndel" value="<?php _e('Delete') ?>" />
		<input type="submit" name="btnapprove" value="<?php _e('Approve') ?>" />
	</p>
    </form>
<?php
} else {
    // nothing to approve
    echo __("<p>Currently there are no comments to be approved.</p>") . "\n";
}
?>

</div>

<?php
/* </Template> */
include("admin-footer.php")
?>
