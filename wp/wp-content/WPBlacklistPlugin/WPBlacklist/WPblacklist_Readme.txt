WordPress Blacklist Comment Spam Plugin 2.6.1

Table of Contents
-----------------

1 - Introduction
2 - What's New
3 - Installation
4 - Usage
5 - Problems/Incompatibilities
6 - Bug Reports/Suggestions
7 - Version History
8 - Future Plans
9 - Credits/Thanks

1 - Introduction

	This was originally a WordPress hack written by LaughingLizard (http://dinki.mine.nu/word) and was written to emulate the functionality provided by Jay Allen's (http://jayallen.org) MT Blacklist plugin for Movable Type. With the introduction of a plugin architecture for WP 1.2, it was easy enough to convert the original hack into a WP plugin and that is what I have done. I've also added a few extra features that I liked to the original. Please note that this plugin probably will not work with WP releases before 1.2.
	
2 - What's New
	+ Added checks to ensure that blank entries don't get into the blacklist
	+ Added code to the update routine to remove blank entries from existing blacklist
	* Modified the harvesting routine to remove trailing backslashes from URLs 
	* Fixed include statement in install routine which throws an error under certain conditions
	* Modified blacklist search to return all entries when search expression is blank
	* Modified/updated the documentation
		
3 - Installation
	Upgrade
	~~~~~~~
	a) Delete the blacklistForm.php file (if it exists) from the wp-admin folder since that file is no longer needed.
	b) Optional:
		 If you want better functionality from the blacklist plugin so that you get notifications on a comment being held, then find the following section (towards the end of the file) in wp-comments-post.php in your WordPress root folder:
			// If we've made it this far, let's post.
			if( check_comment($author, $email, $url, $comment, $user_ip, $user_agent) ) {
				$approved = 1;
			} else {
				$approved = 0;
			}
			$wpdb->query("INSERT INTO $wpdb->comments 
			(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content, comment_approved, comment_agent) 
			VALUES 
			('$comment_post_ID', '$author', '$email', '$url', '$user_ip', '$now', '$now_gmt', '$comment', '$approved', '$user_agent')
			");
			$comment_ID = $wpdb->get_var('SELECT last_insert_id()');
			if (!$approved) {
				wp_notify_moderator($comment_ID);
			}
			if ((get_settings('comments_notify')) && ($approved)) {
				wp_notify_postauthor($comment_ID, 'comment');
			}
			do_action('comment_post', $comment_ID);
			
			Change the above to:
			// If we've made it this far, let's post.
			if(check_comment($author, $email, $url, $comment, $user_ip)) {
				$approved = 1;
			} else {
				$approved = 0;
			}
			$wpdb->query("INSERT INTO $tablecomments 
			(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content, comment_approved) 
			VALUES 
			('$comment_post_ID', '$author', '$email', '$url', '$user_ip', '$now', '$now_gmt', '$comment', '$approved')
			");
			$comment_ID = $wpdb->get_var('SELECT last_insert_id()');
			// call the post action
			do_action('comment_post', $comment_ID);
			// check comment status after actions
			$stat = wp_get_comment_status($comment_ID);
			if ($stat == "unapproved") {
				wp_notify_moderator($comment_ID);
			} else if ($stat == "approved") {
				if ((get_settings('comments_notify')) && ($approved)) {
					wp_notify_postauthor($comment_ID, 'comment');
				}
			}
			

4 - Usage
	Once the plugin is activated, any comment where the author URL or e-mail, or the comment itself matches one of the regular expressions or strings in your database or where the author IP matches an IP in the blacklist database, will be held for moderation. You can use the Blacklist configuration screen to update your blacklist data from time to time from Jay Allen's list by using the Import Blacklist option. You can also manually add IPs, URLs, regular expressions and the addresses of real-time blacklist servers (RBLs) to the blacklist from the Blacklist configuration screen. Here is an explanation of the individual types of items that you can add to the blacklist.
	1. URL - a URL, bit of text or a regular expression. Any items of this type are matched against the comment author's URL and e-mail as well as the comment itself.
	2. IP - an IP address or a partial address (just the beginning bit - of any length). Items of this type are matched against the comment author's IP.
	3. RBL - a realtime blacklist server address (such as bl.spamcop.net or sbl-xbl.spamhaus.org). These items are used to check the comment author's IP against a remote server's blacklisted IPs. If a match is found, the comment is blacklisted.
	
5 - Problems/Incompatibilities
	- A comment which is held for moderation by this plugin still would have generated a "a new comment was posted" e-mail since that's done automatically by WP
	- No e-mail is generated by the plugin to let the moderator know that a comment was held for moderation

6 - Bug Reports/Suggestions
	There are a couple of ways to get support or to offer suggestions:
	- You can e-mail me at fahim@farook.org
	- You can use the online RookSoft support forums at: http://forums.farook.org
	
7 - Version History
Nov 11, 2004	2.7
	+ DdV: Packaged plugin to be usable as a One-click install

Oct 12, 2004	2.6
	+ Added option to delete comments held for moderation outside the blacklist plugin (for instance, by WP core)
	+ Added option to harvest spammer information from automatically deleted comments
	* Fixed URLs added through the WPBlacklist add interface not being regular expression safe
	* Moved commonly used blacklist functions to a separate include file
	* Modified distribution format to include sub-directory structures in ZIP file
	
Oct 06, 2004	2.5
	+ Added WPBlacklist specific configuration options which specify automatic deletion of comments based on specific blacklist matches
	+ Added an option to notify the moderator of automatically deleted comments alongwith the full text of the comment
	* Revamped the WPBlacklist installer so that it is not destructive on existing data and so that it could do table upgrades based on new features
	* Fixed a bug due to the original table structures which would prevent any RBL items being added to the blacklist
	* Modified the "by given expression" search option so that it handles regular expressions
	* Fixed a few minor cosmetic bugs

Oct 05, 2004	2.01
	* Fixed URL's harvested from comments not being regular expression safe
	* Fixed bug in MT blacklist import which was preventing the blacklist from being updated

Oct 01, 2004	2.0
	+ Added the ability to blacklist on partial IP addresse (the front portion - not a wildcard match)
	+ Added the ability to check RBL servers for a given comment to see if it's from a known spammer IP
	+ Added the option to delete entries from the blacklist
	+ Added the option to search existing comments based on the blacklist or any other given criteria and then delete selected comments from the search results
	+ Added the option to delete comments held for moderation and to harvest information from those comments (such as IP, e-mail, URL etc.) at the same time
	* Various minor tweaks and usability enhancements

Sep 21, 2004	1.22
	* Fixed the plugin not checking the status of the comment to see if it is already on hold or not (and sometimes approving a comment on hold)

Jun 03, 2004	1.21
	* Fixed forward slashes in blacklist items causing PHP errors

May 23, 2004	1.2
	+ Added the option to be able to ban specific IPs for comment posting
	+ Added plugin setup and configuration links to the plugin screen
	* Changed the functionality to be encapsulated in a plugin
	* Fixed a few minor bugs with the original blacklist import code
	
8 - Future Plans
	- Add more functionality to the blacklist configuration screen so that you can search existing comments for a given expression and be able to add that expression to the blacklist
	- Add more functionality to the blacklist configuration screen so that you can search existing comments for a given IP and be able to add that IP to the blacklist
	- Better blacklist management, especially deletion of existing entries

9 - Credits/Thanks
	- Matt for WordPress (http://www.wordpress.org)
	- LaughingLizard for the original WP blacklist code (http://dinki.mine.nu/word/)
	
Fahim (fahim@farook.org)
last updated: 20/10/2004