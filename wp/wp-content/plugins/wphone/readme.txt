=== WPhone ===
Contributors: stephdau, zamoose, Viper007Bond
Tags: iphone, phone, mobile, admin
Requires at least: 2.1
Stable tag: trunk

An upload-and-activate plugin that creates the option to use a custom admin interface designed for mobile phones including, but not limited to, the iPhone.

== Description ==

Looking to manage your WordPress install via your phone? Then the [award winning](http://groups.google.com/group/wp-hackers/browse_thread/thread/958c506e018681a6) WPhone plugin is exactly what you need. It creates an option while logging in to replace the default admin interface with one designed for your phone (see [screenshots](http://wordpress.org/extend/plugins/wphone/screenshots/)).

It contains two versions of the mobile admin interface:

* Rich: designed for the iPhone / iPod Touch and other phones supporting full Javascript and CSS featuring fancy AJAX and sliding menus
* Lite: a lightweight, simple version designed for all other phone types (no Javascript or anything else required)

This plugin is designed for use with the latest version of WordPress, but will work with versions 2.1 and newer.

== Installation ==

###Upgrading From A Previous Version###

To upgrade from a previous version of this plugin, delete the entire folder and files from the previous version of the plugin and then follow the installation instructions below.

###Installing The Plugin###

Extract all files from the ZIP file, making sure to keep the file structure intact, and then upload the plugin's folder to `/wp-content/plugins/`.

This should result in something very similiar to the following file structure:

`- wp-content
    - plugins
        - wphone
            | iframer.php
            | readme.txt
            | wphone.php
            | wphone_exampleplugin.php
            - includes
                | category.php
                | category-form.php
                | comment.php
                | dashboard.php
                [ ... ]
                - css
                    | wphone.css
                    | wphone-alt.css
                - images
                    [ ... ]
                - iui
                    [ ... ]
                - js
                    | wphone.js`

Then just visit your admin area and activate the plugin.

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

###Plugin Usage###

When logging into your account, check the checkbox marked "Use mobile admin interface" on the login form to use the mobile admin interface.

== Frequently Asked Questions ==

= How do I switch between the lite and rich versions? =

Currently, it's automatic. If your phone's browser supports Javascript and has `webkit` in it's user agent, you get the rich version, otherwise you get the lite version.

= Does this plugin support other languages? =

Yes, it does. The plugin is completely translatable. Included in the `localization` folder is the translation template you can use to translate the plugin. See the [WordPress Codex](http://codex.wordpress.org/Translating_WordPress) for details.

== Screenshots ==

1. Dashboard on an iPhone/iPod
2. Manage screen on iPhone/iPod
3. Write post form on an iPhone/iPod, with expanded keyboard
4. Add new user form on iPhone/iPod, with expanded select list
5. Dashboard in "lite" mode, with basic CSS support (in Firefox here)
6. Dashboard in "lite" mode, with no css support (on Motorola v551 here)

== ChangeLog ==


**Version 1.4.2**

* Added Italian translation (thanks to http://www.lucacicca.it/ for the contrib)
* Added German translation (thanks to http://www.deutsche-franzosische-schule-bildung.de/)
* Added Japanese version (thanks to http://blog.pear.co.jp/)
* Fixed the French translation by renaming the locale files appropriately.
* Dealt with a conflict with another plugin reported by a user. We chose to be the good guys on that one. ;-)


**Version 1.4.1**

* Added Russian and French translations (props to http://lecactus.ru/ for the RU contrib)
* Nokia devices compatibility fix.
* Fixed a "View Site" bug when the blog url is different from the wordpress install one (props to tieum for spotting it).
* Added extra checks to make sure we do not gzip compress the output when when php zlib.output_compression is already used by default on the server.

**Version 1.4.0**

* Plugins management: You can now activate or deactivate installed WP plugins from the new Plugins screen. 
* Addressing a rich interface false positive with Nokia/WebKit based devices until we can gain access to such devices to try and fully support them under the CSS+JS interface.
* Backward compatibility fix in the post/page form.
* Ajax improvements in global navigation and dashboard links.
* Miscellaneous bug fixes, tweaks and improvements.

**Version 1.3.1**

* Simply bundling much better screenshots.

**Version 1.3.0**

* Latest Activity: added a new section to the dashboard to replicate the WP's Latest Activity features, but without requiring JS as WP does (incoming links).
* Quicklinks: Added quick links to the main features directly from the hide/show "Go..." panel for easier global navigation.
* User search: Added a search form/feature to and tweaked the output of the search listing screen.
* Added display preferences select list in the appropriate user/profile edit forms.
* Styles and xhtml improvements for both the rich and lite interfaces.
* Further improved output compression scheme for faster download and processing performance.
* Bug fixes, thanks to loads of wonderful feedback we had from the WordPress users community.
* Improved on the already pretty good WP backward compatibility front.
* Code: Improved adherence to WP's coding standards, added function documentation (phpdocs), more hooks, etc.
* And more (see svn log if interested).

**Version 1.2.0**

* CSS tweaks to fix spacing issues in the original and updated iPhone/iPod Touch firmwares, as well as in desktop safari, instead of just the latest iPhone/iPod Touch.
* Convenience links on email and URL field where and when appropriate.
* Now launching some links in a new window (target="_blank") on devices supporting the feature.
* Added a View/Preview button to the post/page edit forms.
* Misc. navigational tweaks.
* Improved the user experience on webkit devices with no or disabled Javascript support.
* Better browser detection for rich v. lite versions of the interface.
* Custom changes to iUI, submitted upstream to the original project.
* Better, more consistent location bar hiding.

**Version 1.1.0**

* Changes made to address sizing, spacing and scrolling issues that were inherent in upstream iUI code.  Specifically addresses iPod Touch/iPhone browser issues encountered on forms.

**Version 1.0.1**

* Slight bugfixes to iUI code to (hopefully) reduce "dancing" and sizing issues on Webkit browsers.

**Version 1.0.0**

* Inital release.
