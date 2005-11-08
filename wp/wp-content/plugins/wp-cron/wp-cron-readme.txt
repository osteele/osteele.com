== WP-Cron ==
tags: cron, perioidic, schedule
Contributors: MCincubus (http://www.txfx.net/), Lionfire (http://the.lostrealm.com/)

WP-Cron provides a rudimentary support for scheduled exection of actions; a sort of "delayed action" processing for WordPress.  It is nowhere near as robust as the actual UNIX cron facility, but should be good enough to "do stuff" on a fairly regular basis.

What with human beings, search engine spiders, and content aggregators, we can be fairly sure that even the most uninteresting (public) blog will be visited with some regularity.  WP-Cron relies on this regularity to schedule the execution of three new plugin hooks. These hooks execute (roughly) once every fifteen minutes, every hour, or every day.

Again: this plugin is nowhere near as robust as the UNIX cron facility.  It is not guaranteed to be reliable in any way.  Do not use this plugin to schedule nuclear attacks against your enemies.  Do not rely on this pluign to remind you when to take your medication.

== Installation ==
Drop the wp-cron.php file into /wp-content/plugins/, and activate the plugin.
The plugin will automatically create in your database the three options it requires.

== Usage ==
Installing and activating this plugin won't do a whole lot, by itself.  Included with it are two additional plugins, to serve as examples of how to use WP-Cron.

* WP-Cron Dashboard: uses WP-Cron to update the data in your WordPress dashboard (roughly) every hour.

* WP-Cron Future Pings: suppresses outgoing pingbacks and trackbacks when publishing a post with a date in the future; then uses WP-Cron to periodically check if any such future-dated posts are visible, sending pingbacks and trackbacks as necessary.
  [ May not yet be fully functional ]

* WP-Cron Gravcache: refresh your cached Gravatar image files on a daily basis.
  [ Experimental, and may not yet be fully functional! ]

* WP-Cron Mail: periodic check of your secret email account for blog-by-email.  This is a near exact copy of the original wp-mail.php, which may or may not be sufficient for your needs.  See also http://codex.wordpress.org/Blog_by_Email

* WP-Cron Moderation: sends an hourly email summary of _new_ pending moderation requests.  That is, if you get two new comments at noon, you'll receive one email notififying you.  If you take no action, and receive no new comments, you will not get pestered again.  You will only receive one email per new comments per hour.

* WP-Cron Reminder: sends out a generic reminder email to the blog admin (roughly) every 15 and 60 minutes.  You can use this to prove to yourself that WP-Cron is working.

== Advanced ==
WP-Cron creates three new hooks against which plugins may register:
* wp_cron_15
* wp_cron_hourly
* wp_cron_daily

WP-Cron compares the current time (at which WordPress is presently executing) against three timestamps stored in the database.  If the timestamps are stale, WP-Cron updates them and then schedules the appropriate hook for execution at the end of the current session.  You probably don't want to schedule too many tasks against any one hook; nor do you want to schedule any particularly time-intensive tasks.

== Frequently Asked Questions ==
Q. What would I use this for?
A. Making sure that something happens on a semi-regular basis.  For example, the bundled plugin wp-cron-dashboard.php makes sure that your WordPress Dashboard is updated roughly every hour.  This not only ensures that you have the latest nwws in your Dashboard, it makes sure you don't need to spend your time waiting for the Dashboard to fetch said latest news!

Q. What else can I do?
A. Quite I bit, I imagine.  If your web host does not allow you to schedule _real_ cron jobs, you might be able to use this to run the scheduled task necessary to support "blog-by-email".

Q. How do you use this plugin?
A. I use WP-Cron Moderation to receive batched notifications of new comments.  I also have a custom plugin to refresh my list of FeedOnFeeds sources every 15 minutes.

== Credits ==
Copyright (c) 2005 Scott Merrill (skippy@skippy.net)
Released under the terms of the GNU GPL

