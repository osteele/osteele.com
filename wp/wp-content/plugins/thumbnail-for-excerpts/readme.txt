=== Plugin Name ===
Contributors: radukn
Donate link: http://www.cnet.ro/wordpress/thumbnailforexcerpts/
Tags: excerpts, thumbnails
Requires at least: 2.6
Tested up to: 2.7
Stable tag: 1.3

Thumbnail For Excerpts allow easily, without any further work, to add thumbnails wherever you show excerpts (archive page, feed...).

== Description ==

There are some solutions for you if you want to show thumbnails near excerpts of your posts. Most of them needs you to work with custom fields. But why, if you already have images in your posts? Why not use those pictures?

Thumbnail For Excerpts search the post for the first image. If exists, than it will search for the thumbnail created by default by WordPress for the image, if it was uploaded from WP administration area. If not, it will show the image itself, but of course, scaled (IMPORTANT: since version 1.2, there is an option to let the plugin to automatically generate the thumbnail where it do no exists.)

Important: Showing thumbnails in excerpts will not *always* look nice. It depends a lot on the theme you choose. Anyway, with some CSS knowledge, it can work nicely. And if you upload photos in posts only from web, it will go 99% perfectly. Make the tests and keep the plugin if you like the results.

== Installation ==

The plugin is simple to install:

1. Download the zip file
1. Unpack the zip. You should have a directory called `thumbnailforexcerpts`, containing several files and folders
1. Upload the `thumbnailforexcerpts` directory to the `wp-content/plugins` directory on your WordPress installation. 
1. Activate plugin
1. Edit thumbnailforexcerpts.php if you need to tweak the settings

It will work immediately!

== Frequently Asked Questions ==

= Is not working for me. Why? =

First, the question is too generic. But the main cause may be that you... don't use excerpts! If your theme do not use the_excerpt() is obviously that this plugin will not work. This is Thumnails for *Excerpts*. I repeat: *Excerpts*!

= The thumbnails are present in feed? =

Yes, if you don't provide full feed, than this plugin will put thumbnails to excerpts from your posts.

= Can I choose the size of the thumbnail? =

Yes, open the PHP file and see in the top of it.

= Can I choose the alignment of the thumbnail? =

Yes, open the PHP file and see in the top of it.

= Can I further customize the look of the the thumbnail? =

Yes, with CSS. The thumbnails are usign imgtfe as default class, but you can change it.

== Screenshots ==

1. By default it shows on left side, with 100 set as width
2. Now in the right side, with 50 as width
3. This screenshot is done in Firefox, showing the feed: yes, the thumbnails are there too!

== Documentation ==

Full documentation can be found on the [Thumbnail for Excerpts](http://www.cnet.ro/wordpress/thumbnailforexcerpts/) page.

== Settings ==

Open the PHP file and edit as you wish. Here are the constants and theri explanation

define("TFE_ALIGN","left");
can be left or right

define("TFE_SIZE","100");
the size of the thumbnail; modify it for better integration with your design; if you set it as 0 it will be than the default size of your WP thumbnails, from admin area

define("TFE_MAXSIZE","no"); 
if yes, than the above indicated size will be used as maximum size for widht and height; if no, than the above indicated size is used only to limit the width

define("TFE_SPACE","5"); 
for the HSPACE parameter of the IMG tag

define("TFE_LINK","yes"); 
can be yes or no; if yes, the image will link to the post

define("TFE_CLASS","imgtfe"); 
the class for the thumbnail images; you can change it or use this class in you CSS file

define("TFE_CREATETH","no"); 
if yes, the images without thumbnails will have one created now (based on default values for thumbnail from admin area, or on TFE_SIZE if in admin area thumbanil size is set to zero)

define("TFE_TITLE","no"); 
if yes, it will use titles for pictures (when you move mouse over the picture you will see the alt text)

== Changelog ==

1.3 [November 22, 2008]
- working now with GIF and PNG also
- working now also with WordPress installations which do not use wp_ for tables
- tested for WP 2.7

1.2 [September 9, 2008]
- can create thumbnails if do not exists

1.1 [September 6, 2008]
- some suggestion implemented (link, title, max dim)

1.0 [August 13, 2008]
- first release