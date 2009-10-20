=== dTabs ===
Contributors: 
Donate link: http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#donate
Tags: dTabs, tabs, dynamic, menu, highlighting, horizontal, vertical, list, drop, down, dropdown, post, posts, page, pages, category, categories, archive, archives, bookmarks, links, front page, posts page, navigation
Requires at least: 2.8
Tested up to: 2.8.3
Stable tag: trunk

Adds a new template tag to output user controlled dynamic tabs and drop down menus for posts, pages, categories, archives, and bookmarks.

== Description ==

Basically, it makes adding dynamic tabs and menus to your Wordpress blog/theme easy.

dTabs provides a new template tag `dtab_list_tabs` which outputs a user controled dynamically tabbed navigation system with optional drop down menus in Wordpress.  

* Tabs can be made for individual posts/categories/pages, the posts page, the front page, the archives, and the bookmarks.
* Tabs for categories and pages have optional dropdown menus of their sub-categories/sub-pages, while archives and bookmarks have non-optional drop down menus.
* Tabs are set up by the user in an easy to use admin panel, while their properties and appearance can either be set by the user in the admin panel or within the theme's css.
* dTabs can be easily added as an option in themes for public distribution.

See [Other Notes](http://wordpress.org/extend/plugins/dtabs/other_notes/) for documentation.

== Installation ==

1. Upload dtabs.php to your Wordpress Plugins directory.
1. Activate dTabs in the Plugins section of the admin control panel.

Please note that if your theme is not pre-enabled for dTabs you need to insert the dTabs template tag ([dtab_list_tabs](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#enabletheme "Adding dTabs to your theme")) of code into your theme before your tabs will appear on your blog, alternatively/in addition you could ask your theme's author to pre-enable your theme.

== Frequently Asked Questions ==

= Can I have multiple rows of tabs? =

Yep this can be achieved with css, have a play.

= What about multiple sets of tabs, like one at the top of the page and one at the bottom? =

Yes and No.  You can use `dtab_list_tabs` as many times as you want (setting "tabbar=class" as a parameter to ensure valid css) to procude several copies of the same set of tabs, but currently you can only set up and administer that single set of tabs.  Multiple tab sets is a feature I am [considering](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#donate,"Donate") adding in future versions.

== Screenshots ==

1. Tabs created using dTabs plugin with Kubrick Tabs theme
2. Drop down menu in Kubrick Tabs
3. Appearance of tabs using default CSS provided in dTabs
4. Appearance of tabs created using dTabs and a custom theme on dtcnet.co.uk

== Changelog ==
= Version 1.4 24/07/2009 =
* Moved admin panel from Tools to Options.
* Added option to disable links for tabs (Suggested by Elody).
* Added option to make tabbar a class instead of id for use with multiple tabbars (Suggested by Mor).
* Updated default css for use with tabbar class.
* Added <i>first</i> and <i>last</i> classes to respective tabs (Suggested by johnho).
* Implemented hierarchical dropdown boxes for categories and pages when editing a tab.
* Increased use of internal WP functions to reduce calls to the database and speed up dTabs.
* Updated implementation of metaboxes to work with WP 2.8.
* Fixed Uninstall dTabs button to work with WP 2.8.

= Version 1.3 05/07/2008 (11006 downloads) =
* Fixed problem with default css in Firefox 2 where tabs always appear on seperate lines (reported by G Dan Mitchell and fixed by Ian Brown).
* Added "between" argument for dtab_list_tabs for adding content between tabs.
* Added "fadetime" argument for dtab_list_tabs to enable control over the length of time it takes to fade menus and add the option of disabling fading all together.
* Fixed flickering of dropdown menus in certain circumstances.
* Added provisional (not suported by IE) support for javascript free css menus .
* Added "fadetype" argument for dtab_list_tabs for switching to javascript free menus.
* Updated default css to support  javascript free css menus.
* Added auto css for javascript free css layered menus.

= Version 1.2.2 29/04/2008 (1909 downloads) =
* Fixed a bug introduced in version 1.2.1 that prevented tabs with non-word characters in their name being selected (reported by Joy).

= Version 1.2.1 20/04/2008 (456 downloads) =
* Fixed a bug introduced in version 1.2 that prevented menus fading out for tabs with non-word characters in their name (Reported by Mike and Morgan).

= Version 1.2 02/04/2008 (664 downloads) =
* Improved improved support for static front pages
* Updated javascript to properly support Safari 3 (and dropped support for Safari 2)
* Updated javascript to fade drop down menus in and out
* Made javascript output optional (so a javascript file can be included with themes)
* Depreciated dtabs_echo_dtabs, replaced with dtabs_list_tabs
* Removed Tabs Options pannel
* Moved automatic CSS generation option to Manage Tabs Panel
* Dropped <i>before</i> and <i>after</i> admin options
* Rewrote default CSS, making it more complex but producing half decent looking tabs
* Used register_activation_hook to set up tabs upon activation
* Introduced activation message
* Introduced ability to uninstall
* Integrated into WP 2.5 UI
* Made other minor changes to streamline the interface

= Version 1.1 18/12/2007 (1045 downloads) =
* Added full support for static front pages
* Added suport for <a href="http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/apuc-automatic-plugin-update-checker-plugin-for-wordpress/">automatic update checking</a>
* Prepared for internationalisation
* Fixed several empty array bugs (reported by Joost Verweij and others)

= Version 1.0.4 16/10/2007 (390 downloads) =
* Fixed ambiguity bug (reported by Julian) introduced with 1.0.3
* Fixed a bug also introduced with 1.0.3 that prevented dTabs from recognising sub categories

= Version 1.0.3 15/10/2007 (6 downloads) =
* Added support for WP 2.3

= Verson 1.0.2 - 17/08/2007 (240 downloads) =
* Fixed the zero bug (reported by Gregg Mendez) where in certain circumstances a "0" was added within tabs' links tags - invalidating the html.
* Fixed the slashes bug (reported by AJ) so slashes are stripped from tab labels
* Rearranged the order of tags for tabs so that <i>before</i> and <i>after</i> parameters are applied within the list item tags, to enable Gina's suggestion to allow tabs to stretch and expand - i.e. dynamic css.
* Minor update to default css

= Verson 1.0.1 - 03/04/2007 (496 downloads) =
* Fixed behaviour for page tabs so that they cannot be confused with categories of the same id number.
* Put tabs in an unordered list - replacing the &lt;div&gt; tags with &lt;ul&gt;s and &lt;span&gt; tags with &lt;li&gt;s.

= Verson 1.0 - 09/02/2007 (479 downloads) =
* Added simple dynamic menu functionality - tabs that link to categories and pages can have a css/javascript drop down menu listing any subcategories, sub-pages, archives, or bookmarks in a hierarchal tree.
* Fixed the problem with the default tab present upon installation or after resetting the tabs so that it <i>can</i> be selected.
* Fixed the bug caused by database changes brought in with WP 2.1 (reported by James) where pages were no longer listed in the Modify > Tabs admin pannel.
* Added ability to make tabs for pages, subcategories, archives, and bookmarks (previously it was only super-parent categories and pages).
* Improved the method by which the current tab is selected - now all tabs linking to the current page/post/category and it's parents are taken into consideration, and the closest match is selected.

= Verson 0.911 - 14/08/2006 (645 downloads) =
* Fixed behaviour for blog/main page tab so that it can be selected and so the link points towards the blogs web address (instead of the website's root directory).

= Verson 0.91 - 06/06/2006 (327 downloads) =
* Added options > Tabs admin panel,  with the following features: =*Optional automatic (basic) css generation/example.
* Optional automatic css output into any theme.
* Option to create vertical tab interface (as opposed to horizontal by default).
* The ability to add html before and after each tab (just like you can in the templates).
* Also added the option to control the formatting of selected and non selected tabs using their id instead of class, allowing the custom formatting for each tab.

= Verson 0.9 - 10/03/2006 (28 downloads) =
* Update to manage > Tabs admin panel, including the addition of a drop down list of available tabs to make setting the plugin up as easy as possible.
* Fixed behaviour with categories: current tab now reflects the parent category instead of the current category.

= Verson 0.8 - 09/02/2006 (10 downloads) =
* Initial release

== Documentation ==
* [Recent Changes](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#recentchanges)
* [Requirements](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#requirements)
* [Installation](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#installation)
* [Updating](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#updating)
* [Uninstallation and deactivation](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#uninstallation_and_deactivation)
* [Themes pre-enabled for dTabs](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#pre-enabledthemes)
* [Adding dTabs to your theme](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#enabletheme)
* [Examples](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#eg)
* [Known Issues](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#ki)
* [Future](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#future)
* [History](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#history)
* [Support and Feedback](http://dynamictangentconceptions.dtcnet.co.uk/downloads/wp-plugins/dtabs-dynamic-tabs-wordpress-plugin/#support)