=== After the Deadline ===
Contributors: rsmudge
Tags: writing, spell, spelling, spellchecker, grammar, style, plugin, edit, proofreading
Stable tag: trunk
Requires at least: 2.8.4
Tested up to: 2.8.4

After the Deadline checks spelling, style, and grammar in your WordPress posts.

== Description ==

After the Deadline helps you write better and spend less time editing.  Click the button in the visual editor mode to check spelling, style, and grammar.

After the Deadline communicates with a software service.  You'll need an [API key](http://www.afterthedeadline.com/profile.slp) to use this plugin.  

== Screenshots ==

1. After the Deadline in action.

== Installation ==

Upload the After the Deadline plugin to your blog, Activate it, and enter your [API key](http://www.afterthedeadline.com/profile.slp)

That's it!

* Note: make sure After the Deadline is in a folder named "after-the-deadline".  This is necessary for it to work.

== Changelog ==

= 9 Oct 09 =
- Fixed a bug in IE causing immediate space after an error to be eaten (in some cases)

= 6 Oct 09 =
- Fixed a bug preventing the second of two-like errors in the same span from getting highlighted. 

= 21 Sept 09 =
- Changed editor plugin to avoid namespace conflicts with Javascript when storing error precontext and strings.  

= 14 Sept 09 =
- AtD/WP.org now works with WordPress blogs configured to use SSL in the admin area.  Special thanks to Alex Rodriguez
  who patiently worked with me to track this bug down.  
- removed atd.css and made the TinyMCE plugin load the button.

= 10 Sept 09 =
- Fixed an issue with bold/italic stripped in Safari and object (YouTube embeds) tag stripped in other browsers

= 8 Sept 09 =
- small update to the TinyMCE editor plugin, fixes a rarely occuring bug where some suggestions weren't highlighted.

= 6 Sept 09 =
I'm still learning how to program, jumping from BASIC to JavaScript is tough--here are the things fixed this time:

- Fixed an issue preventing errors with hyphens not being highlighted
- Empty span tags created by cutting and pasting AtD marked text in Firefox are removed.  
  IE has the good sense to not transfer these tags.  Safari can't be helped as it creates 
  a span tag with inline styling.  
- Fixed an issue preventing some errors from being highlighted in certain situations.

= 4 Sept 09 =
- Fixed a bug that caused a fatal error in some cases

= 3 Sept 09 =
- Major updates.  Note that most errors are now optional and disabled by default.  Visit your user profile (/wp-admin/profile.php) to update your After the Deadline options.

= 17 Jun 09 =
- Added hack to make sure AtD tags are stripped.  My apologies for this bug.  

The good news--install this update and all your old posts will be free of AtD tags 
when displayed.

= 15 May 09 =
- Updated TinyMCE plugin to something more sane.
- Small cosmetic change to the AtD toolbar icon
- Added the ability to quickly ignore/unignore phrases.  

= 13 Mar 09 =
- Removed use of reference in foreach (PHP 5.0 only feature?)
  Fixes: Parse error: parse error, unexpected '&', expecting T_VARIABLE or '$'

= 2 Mar 09 =
- Removed curl dependence
