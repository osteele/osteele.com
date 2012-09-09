=== Comment Timeout ===
Contributors: jammycakes
Donate link: http://bitbucket.org/jammycakes/comment-timeout/
Tags: comments, spam
Requires at least: 2.8
Tested up to: 3.2
Stable tag: 2.4.0

Closes comments on blog entries after a user-configurable period of time, with an option to make allowances for active discussions.

== Description ==

This plugin extends the comment closing functionality in WordPress to allow
you to extend the discussion time when older posts have recent comments
accepted, or to override the comment closing time on a post by post basis.

**Note: PHP 4 is no longer supported.** As of version 2.1, Comment Timeout requires PHP 5.2 or later.
If you are still using PHP 4, you should use Comment Timeout 2.0.1.

== Installation ==

* Copy the directory `comment-timeout` and all the files in it into your
`/wp-content/plugins` directory.
* Activate the plugin through the "Plugins" menu in the WordPress dashboard.
* Configure the plugin by going to the "Comment Timeout" page on the "Options" menu.

== Configuration ==

You can change various settings globally on the "Comment Timeout" page under the
"Options" tab on the WordPress dashboard. Most of the options are fairly
self-explanatory.

You can also change options on a per-post basis by looking for the
"Comment Timeout" section in the sidebar of the post or page editor.

== Template tags ==

Comment Timeout 2.1 introduces two new functions that you can use in your theme:

`get_comment_timeout()`

Returns the UTC time, as a Unix timestamp, when comments will be closed for the current post.

`the_comment_timeout($relative, $dateformat, $before, $after, $moderated)`

Formats and displays the date and time after which comments will no longer be
accepted on this post. Parameters are:

* **$relative**: Set this to `true` if you want to display the date in terms of the time remaining
(for instance "in 3 weeks"). Set it to `false` to display it as an absolute date.
* **$dateformat**: The format in which the date should be displayed, as used by the PHP
`date()` function. If you set this to `false`, it will use the date format that you have configured
globally for your WordPress installation.
  * If `$relative` is set to `true`, this parameter is ignored.
* **$before**: The HTML to insert before the date that comments will be closed.
* **$after**: The HTML to insert after the date that comments will be closed.
* **$moderated**: The HTML to display when late comments are being sent to the moderation queue
rather than being rejected outright.

== Frequently Asked Questions ==

**My page layout breaks when comments are closed!**

This is the fault of your theme, not this plugin. Some theme authors do not test
their themes properly with posts for which comments have been closed.
See [this blog post](http://jamesmckay.net/2008/07/comment-timeout-and-faulty-wordpress-themes/ "Comment Timeout and faulty WordPress themes") for details.

You should contact your theme developer and ask them for a fix.

== Changelog ==

= 2.4.0 =

* You can now optionally specify a date on which to close comments across the board, and then
  to re-open them at a later date. This is useful if you are going on holiday, for example, or
  if you want to take an indefinite break from blogging.
* Timeout options are now hidden on the admin page when "Close comments" is deselected.

= 2.3.0 =

* Added an option to allow users to reset all per-post settings to their defaults.

= 2.2.0 =

* Comment Timeout now integrates with WordPress's built in comment closing feature. Enabling
  or disabling comments through the "Discussion" tab will be reflected in Comment Timeout.
* Old versions of WordPress (prior to 2.8) are no longer supported.


= 2.1.2 =

* Fixed: "allow comments" and "allow pings" options were being disabled when old posts were
  edited using Quick Edit. See [this issue](https://bitbucket.org/jammycakes/comment-timeout/issue/1/editing-a-post-where-comments-are-auto)
* The home page for Comment Timeout is now [the Bitbucket repository](https://bitbucket.org/jammycakes/comment-timeout/)

= 2.1.1 =

* Fixed layout of files to make automatic upgrades work properly.

= 2.1.0 =

* Added option to disable or modify the message indicating when comments will time out.
* Added template tags to allow further customisation in the theme.
* Made the per-post option display correctly on WordPress 2.5 and later.
* Discontinued support for PHP 4.

= 2.0.1 =

* Fixed a bug that was causing comments to be closed incorrectly when pings were disabled.

= 2.0 =

* Fixed a bug that was allowing comments through from spam bots on old posts.

= 2.0 alpha 1 =

* Initial release of Comment Timeout 2.0. This was a total rewrite with new features:
  * Link Limits, Three Strikes and You're Out moved to separate plugins.
  * Timeouts can now be set on a post-by-post basis.
  * Redesigned admin page.
  * Comments on old posts can be sent to the moderation queue instead of being blocked.

== Development and reporting bugs ==

When reporting bugs, please provide me with the following information:

1. Which version of Comment Timeout you are using;
2. Which version of WordPress you are using;
3. The URL of your blog;
4. Which platform (Windows/IIS/PHP or Linux/Apache/MySQL/PHP) your server
   is running, and which versions of Apache and PHP you are using, if you
   know them;
5. The steps that need to be taken to reproduce the bug.

If you wish to get hold of the latest development version, or to contribute
bug fixes or new features, you can clone the project's Mercurial repository:

`hg clone https://bitbucket.org/jammycakes/comment-timeout/`

== Redistribution ==

Copyright (c) 2007 James McKay
http://jamesmckay.net/

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

== For more information ==

For more information, please visit the plugin's home page:

http://bitbucket.org/jammycakes/comment-timeout/