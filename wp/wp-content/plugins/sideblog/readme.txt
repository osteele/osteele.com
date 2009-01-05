=== Sideblog Wordpress Plugin ===
Contributors: kates
Donate link: http://www.katesgasis.com/
Tags: asides, sideblog
Requires at least: 2.5
Tested up to: 2.5
Stable tag: trunk

A simple aside plugin.

== Description ==

Sideblog is a plugin for Wordpress Blog Platform. It is one way of implementing "Asides" - a series of "short" posts, 1-2 sentences in length.

For Wordpress version 2.2 and below, please visit http://katesgasis.com/download-page.

== Installation ==

While doing the installation procedure, it is recommended to go through all the steps first before viewing the output. If you don't, you'll get nasty error messages.

= For those with Sidebar Widget compatible themes =

1. Upload `sideblog.php` to the `/wp-content/plugins` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create your asides category (usually, 'asides') if you haven't created on yet
1. Go to 'Options' menu then to 'Sideblog' submenu
1. From the list of categories, select the one you just created above and choose the number of entries to display
1. Click on 'Update Sideblog Options' button
1. Go to 'Presentation' menu, then to 'Sidebar widget' or just 'Widgets' for WP 2.2 and greater.
1. Drag and drop the Sideblog widget ('Sideblog 1) to your sidebar container.
1. Click on the icon and give it a title and select a category.
1. Save your changes.
1. Create a post and put it in your asides category

= For those without Sidebar Widget =

1. Upload `sideblog.php` to the `/wp-content/plugins` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create your asides category (usually, 'asides') if you haven't created on yet
1. Open your themes' `sidebar.php` file if you have one and add `<?php sideblog('asides'); ?>`
1. Create a post and put it in your asides category

