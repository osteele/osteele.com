<?php
/*
Plugin Name: Blacklist
Plugin URI: http://www.farook.org
Description: Checks each entered comment against a standard blacklist and either approves or holds the comment for later approval or automatically deletes it. Also allows you to work with comments in the moderation queue so that you can harvest information to add to the blacklist while mass-deleting held comments.<BR /><a href="../blacklist-install.php">Blacklist Installer</a><BR /><a href="wpblacklist.php">Blacklist Configuration</a>
This version has been repackaged to benefit from <a href="http://unknowngenius.com/wp-plugins">WP Plugin Manager</a> One-Click Install feature. As well as a few misc. improvements for WP 1.3... (<a href="http://unknowngenius.com/blog/">DdV</a> - 11/11/04)
Version: 2.7.0
Author: Fahim Farook
Author URI: http://www.farook.org
*/

require_once(ABSPATH.'/wp-config.php');
require_once(ABSPATH.'/wp-includes/wpblfunctions.php');

/*
   notifies the moderator of the blog (usually the admin) about deleted comments
   always returns true
 */
function wpbl_notify($comment_id, $reason, $harvest) {
    global $wpdb, $url, $email, $comment, $user_ip, $comment_post_ID, $author, $tableposts;

    $sql = "SELECT * FROM $tableposts WHERE ID='$comment_post_ID' LIMIT 1";
    $post = $wpdb->get_row($sql);
    if (!empty($user_ip)) {
        $comment_author_domain = gethostbyaddr($user_ip);
    } else {
        $comment_author_domain = '';
    }
    // create the e-mail body
    $notify_message  = "A new comment on post #$comment_post_ID \"".stripslashes($post->post_title)."\" has been automatically deleted by the WPBlacklist plugin.\r\n\r\n";
    $notify_message .= "Author : $author (IP: $user_ip , $comment_author_domain)\r\n";
    $notify_message .= "E-mail : $email\r\n";
    $notify_message .= "URL    : $url\r\n";
    $notify_message .= "Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=$user_ip\r\n";
    $notify_message .= "Comment:\r\n".stripslashes($comment)."\r\n\r\n";
    $notify_message .= "Triggered by : $reason\r\n\r\n";
    // add harvested info - if there is any
    if (!empty($harvest)) {
        $notify_message .= "Harvested the following information:\r\n". stripslashes($harvest);
    }
    // e-mail header
    $subject = '[' . stripslashes(get_settings('blogname')) . '] Automatically deleted: "' .stripslashes($post->post_title).'"';
    $admin_email = get_settings("admin_email");
    $from  = "From: $admin_email";
    $message_headers = "MIME-Version: 1.0\r\n"
    	. "$from\r\n"
    	. "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\r\n";
    // send e-mail
    @mail($admin_email, $subject, $notify_message, $message_headers);
    return true;
}

/*
  this function harvests blacklist info, e-mails moderator (if necessary) and deletes the comment
  return true on successful deletion, false otherwise
 */
function mail_and_del($commentID, $reason) {
    global $wpdb, $options, $url, $email, $comment, $user_ip;

    $info = '';
    // harvest information - if necessary
    if (in_array('harvestinfo', $options)) {
        // Add author e-mail to blacklist
        $buf = sanctify($email);
        $request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$buf'");
        if (!$request) {
            $wpdb->query("INSERT INTO blacklist (regex, regex_type) VALUES ('$buf','url')");
            $info .= "Author e-mail: $email\r\n";
        }
        // Add author IP to blacklist
        $buf = sanctify($user_ip);
        $request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$buf'");
        if (!$request) {
            $wpdb->query("INSERT INTO blacklist (regex, regex_type) VALUES ('$buf','ip')");
            $info .= "Author IP: $user_ip\r\n";
        }
        // get the author's url without the prefix stuff
        $regex   = "/([a-z]*)(:\/\/)([a-z]*\.)?(.*)/i";
        preg_match($regex, $url, $matches);
        if (strcasecmp('www.', $matches[3]) == 0) {
            $buf = $matches[4];
        } else {
            $buf = $matches[3] . $matches[4];
        }
        $buf = remove_trailer($buf);
        $buf = sanctify($buf);
        $request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$buf'");
        if (!$request) {
            $wpdb->query("INSERT INTO blacklist (regex, regex_type) VALUES ('$buf','url')");
            $info .= "Author URL: $buf\r\n";
        }
        // harvest links found in comment
        $regex = "/([a-z]*)(:\/\/)([a-z]*\.)?([^\">\s]*)/im";
        preg_match_all($regex, $comment, $matches);
        for ($i=0; $i < count($matches[4]); $i++ ) {
            if (strcasecmp('www.', $matches[3][$i]) == 0) {
                $buf = $matches[4][$i];
            } else {
                $buf = $matches[3][$i] . $matches[4][$i];
            }
            $ps = strrpos($buf, '/');
            if ($ps) {
                $buf = substr($buf, 0, $ps);
            }
            $buf = remove_trailer($buf);
            $buf = sanctify($buf);
            $request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$buf'");
            if (!$request) {
                $wpdb->query("INSERT INTO blacklist (regex, regex_type) VALUES ('$buf','url')");
                $info .= "Comment URL: $buf\r\n";
            }
        } // for
    }
    // send e-mail first since details won't be there after delete :p
    if (in_array('sendmail', $options)) {
        wpbl_notify($commentID, $reason, $info);
    }
    if (wp_set_comment_status($commentID, 'delete')) {
        return true;
    } else {
        return false;
    }
}

/*
  the main function which approves/holds/deletes comments based on the internal blacklist
 */
function blacklist($commentID) {
    global $wpdb, $url, $email, $comment, $user_ip, $options, $tablecomments;

//    $row = $wpdb->get_row("SELECT * FROM $tablecomments WHERE comment_ID='$commentID'");
//    echo "Author: $row->comment_author<br />";
    // first check the comment status based on WP core moderation
    $stat = wp_get_comment_status($commentID);
    if ($stat == 'deleted') {
        // no need to proceed since there is no comment
        return;
    } else if ($stat == 'unapproved') {
        $approved = False;
    } else {
        $approved = True;
    }
    // are we supposed to delete comments held by the core?
    if (!$approved && in_array('deletecore', $options)) {
        mail_and_del($commentID, "Mail held for moderation outside WPBlacklist");
        return;
    }
    // IP check
    $sites = $wpdb->get_results("SELECT regex FROM blacklist WHERE regex_type='ip'");
    if ($sites) {
        foreach ($sites as $site)  {
            $regex = "/^$site->regex/";
            if (preg_match($regex, $user_ip)) {
                $approved = False;
                if (in_array('deleteip', $options)) {
                    mail_and_del($commentID, "Author IP: $user_ip matched $regex");
                    return;
                }
                break;
            }
        }
    }
    // RBL check
    if ($approved || in_array('deleterbl', $options)) {
        $sites = $wpdb->get_results("SELECT regex FROM blacklist WHERE regex_type='rbl'");
        if ($sites) {
            foreach ($sites as $site)  {
                $regex = $site->regex;
                if (preg_match("/([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)/", $user_ip, $matches)) {
                    $rblhost = $matches[4] . "." . $matches[3] . "." . $matches[2] . "." . $matches[1] . "." . $regex;
                    $resolved = gethostbyname($rblhost);
                    if ($resolved != $rblhost) {
                        $approved = False;
                        if (in_array('deleterbl', $options)) {
                            mail_and_del($commentID, "Author IP: $user_ip blacklisted by RBL $regex");
                            return;
                        }
                        break;
                    }
                }
            }
        }
    }
    // expression check
    if ($approved || in_array('deletemail', $options) || in_array('deleteurl', $options) || in_array('delcommurl', $options)) {
        $sites = $wpdb->get_results("SELECT regex FROM blacklist WHERE regex_type='url'");
        if ($sites) {
            foreach ($sites as $site)  {
                $regex = "/$site->regex/i";
//                echo "Regex: $regex <br />";
                if (preg_match($regex, $url)) {
                    $approved = False;
                    if (in_array('deleteurl', $options)) {
                        mail_and_del($commentID, "Author URL: $url matched $regex");
                        return;
                    }
                    break;
                }
                if (preg_match($regex, $email)) {
                    $approved = False;
                    if (in_array('deletemail', $options)) {
                        mail_and_del($commentID, "Author e-mail: $email matched $regex");
                        return;
                    }
                    break;
                }
                if (preg_match($regex, $comment)) {
                    $approved = False;
                    if (in_array('delcommurl', $options)) {
                        mail_and_del($commentID, "Comment text contained $regex");
                        return;
                    }
                    break;
                }
            }
        }
    }
    if ($approved) {
        wp_set_comment_status($commentID, 'approve');
    } else {
        wp_set_comment_status($commentID, 'hold');
    }
}

// set up the options array
$options = array();
// load options from DB
$sql = "SELECT * FROM blacklist WHERE regex_type = 'option'";
$results = $wpdb->get_results($sql);
if ($results) {
    foreach ($results as $result) {
        $options[] = $result->regex;
    }
}

function add_wpblacklist_menu()
{
	if (function_exists('add_options_page')) // just being sage here...
		add_options_page("WPBlacklist", "WPBlacklist", 5, "wpblacklist.php");
}

// the hook
add_action("admin_menu", "add_wpblacklist_menu");
add_action('comment_post', 'blacklist');
?>
