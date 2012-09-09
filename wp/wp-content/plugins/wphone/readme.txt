=== WPhone ===
Contributors: stephdau, zamoose, Viper007Bond
Requires at least: 2.1
Stable tag: trunk

A no longer maintained plugin.

== Description ==

This plugin is no longer maintained and likely no longer even works with recent versions of WordPress. Please don't install it.

We strongly recommend you use one of the [wonderful mobile WordPress applications](http://wordpress.org/extend/mobile/) instead of this plugin which was written before the iPhone could do apps and before Android existed. Times have changed.

== ChangeLog ==

**Version 1.5.3**

* This plugin continues to be dead. Readme updated to reflect that.
* Some more fixed security issues. Man we were bad at writing code back then.

**Version 1.5.2**

* This plugin is still dead, but just fixing a little security issue.

**Version 1.5.1**

* Nonce the log out link so it works with WordPress 2.7.

**Version 1.5.0**

* iPhone/iPod Touch interface tweaks, based on user feedback and native testing.
* Improved and extended the WPhone hooks to be more flexible for other plugin developers to integrate (see wphone_exampleplugin.php)
* Fixed a visual issue and submitted an upstream patch for iUI: http://code.google.com/p/iui/issues/detail?id=49

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