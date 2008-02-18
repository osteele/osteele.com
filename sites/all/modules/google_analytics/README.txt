Module: Google Analytics
Author: Mike Carter <www.ixis.co.uk/contact>


Description
===========
Adds the Google Analytics tracking system to your website.

Requirements
============

* Google Analytics user account


Installation
============
* Copy the 'googleanalytics' module directory in to your Drupal
modules directory as usual.


Usage
=====
In the settings page enter your Google Analytics User ID.

You will also need to define what user roles should be tracked.
Simply tick the roles you would like to monitor.

You can also track the username and/or user ID who visits each page.
This data will be visible in Google Analytics as segmentation data.
If you enable the profile.module you can also add more detailed
information about each user to the segmentation tracking.

All pages will now have the required JavaScript added to the
HTML footer can confirm this by viewing the page source from
your browser.

'admin/' pages are automatically ignored by Google Analytics.


Advanced Settings
=================
You can include additional JavaScript snippets in the advanced
textarea. These can be found on various blog posts, or on the
official Google Analytics pages. Support is not provided for
any customisations you include.

To speed up page loading you may also cache the Analytics urchin.js
file locally. You need to make sure the site file system is in public
download mode.