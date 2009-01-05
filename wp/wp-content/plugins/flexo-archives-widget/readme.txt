=== Flexo Archives Widget ===
Contributors: heathharrelson
Donate link: 
Tags: sidebar, archive, archives, collapsible archive, collapsible, collapse, widget
Requires at least: 2.0
Tested up to: 2.7
Stable tag: 1.0.13

Displays your archives as a compact list of years that expands when clicked.

== Description ==

The Flexo Archives Widget displays your archives as a list of years that expands when clicked (thanks to some JavaScript magic) to show the months with posts. You also have the option of displaying the number of posts in each month.

This widget is designed to be a more compact alternative to the default archives widget supplied with WordPress.

== Installation ==

This plugin is a sidebar widget, so you will need to have WordPress 2.2 or greater. In principle, the widget should work in earlier versions of WordPress with the [Automattic Widgets Plugin](http://automattic.com/code/widgets/ "Widgets Plugin at automattic.com"), but this has not been tested.

1. Expand `flexo-archives-widget.VERSION.zip`
1. Upload the whole `flexo-archives-widget` directory to the `/wp-content/plugins/` directory.
1. Activate the Flexo Archives Widget plugin through the 'Plugins' menu in WordPress.
1. To add the widget to your sidebar, go to the widgets panel in your admin interface.
1. Configure the widget's title and whether post counts are displayed.

== Frequently Asked Questions ==

= The colors of the archive lists are funny. =

While this isn't a question, it is something I hear a lot about in connection
with the Flexo Archives Widget. 

This isn't the widget's fault. The colors of the lists are set (or not) by your theme.  All my JavaScript does is hide or display the lists.  It doesn't care about colors.  It's likely that your theme doesn't have rules in its stylesheet to match the nested lists generated.

To test whether the problem is your theme, temporarily configure your blog to use the default WordPress theme. Expand and contract a few year links in the sidebar. If things don't look odd, the problem is probably with your theme.

== Screenshots ==

1. Before and after expansion with the default theme

== Version History ==

= 1.0.13 =

- Documentation changes only.  Tested for WordPress 2.7.

= 1.0.12 =

- Documentation changes only. Tested for WordPress 2.6.

= 1.0.11 =

- Use a better method of getting the JavaScript into the page header.

= 1.0.10 =

- Display a description of the widget in the widget management panel (another improvement for WoredPress 2.5).

= 1.0.9 =

- Documentation changes only; tested for WordPress 2.5.

= 1.0.8 =

- Change JavaScript to only display one year's archives at a time.

= 1.0.7 =

- Bug fix for themes that don't set the ID of the widget's root element. We previously just didn't work on such themes; now we locate the root element using the flexo-links. Busted themes should work now, as long as they use a list for the sidebar.

= 1.0.6 =

- Not released.

= 1.0.5 =

- Added an ID to each flexo-list. Added a check for themes that don't put the
  widget's ID in the element that contains it. Now we don't hide the archive
lists on such broken themes, so they stay accessible.

= 1.0.4 =

- Fix generation of JavaScript URL for sites where the blog URL and WordPress URL are different.

= 1.0.3 =

- Build up JavaScript URL programmatically so installing the plugin in a directory other than flexo-archives-widget won't break the plugin.

= 1.0.2 =

- Documentation changes.

= 1.0.1 =

- Initial release through WordPress plugins site.
