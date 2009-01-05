=== Google Analyticator ===
Contributors: cavemonkey50
Donate link: http://cavemonkey50.com/code/
Tags: stats, google, analytics, tracking
Requires at least: 2.3
Tested up to: 2.6
Stable tag: 2.14

Adds the necessary JavaScript code to enable Google Analytics.

== Description ==

Google Analyticator adds the necessary JavaScript code to enable Google Analytics logging on any WordPress blog. This eliminates the need to edit your template code to begin logging.

= Features =

Google Analyticator Has the Following Features:

- Full support for the latest version of Google Analytics tracking code (ga.js).
- Inserts tracking code on all pages WordPress manages.
- Automatically tracks outbound links.
- Provides support for download link tracking.
- Easy install: only need to know your tracking UID.
- Expandable: can insert additional tracking code if needed, while maintaining ease of use.
- Option to disable tracking of WordPress administrators.
- Can include tracking code in the footer, speeding up load times.
- Complete control over options; disable any feature if needed.

= Usage =

In your WordPress administration page go to Options > Google Analytics. From there enter your UID and enable logging. Information on how to obtain your UID can be found on the options page.

Once you save your settings the JavaScript code should now be appearing on all of your WordPress pages.

== Installation ==

1. Upload the `google-analyticator` folder to your `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Configure your tracking settings through the 'Settings' > 'Google Analytics' menu in WordPress.
1. Watch Google Analytics for excellent information on your user base.

== Frequently Asked Questions ==

= Where is the Google Analytics code displayed? =

The Google Analytics code is added to the <head> section of your theme by default. It should be somewhere near the bottom of that section.

= Why don't I see the Google Analytics code on my website? =

If you have switched off admin logging, you will not see the code. You can try enabling it temporarily or log out of your WordPress account to see if the code is displaying.

= Why is Google saying my tracking code is not installed? =

Google's servers are slow at crawling for the tracking code. While the code may be visible on your site, it takes Google a number of days to realize it. The good news is hits are being recorded during this time; they just will not be visible until Google acknowledges your tracking code.

== Changelog ==

**2.14** - Bug Fix
- Stops the external link tracking code from appearing in feeds, breaking feed validation.
- Adds compatibility for a very rare few users who cannot save options.

**2.13** - Bug Fix
- Stops the external link tracking code from appearing in feeds, breaking feed validation.

**2.12** - Bug Fix

- Applies the new administrator level selection to outbound tracking (I forgot to that in the last release).
- Fixes a potential plugin conflict.

**2.11** - Minor Update

- Adds an option to change what Google Analyticator considers a WordPress administrator.

**2.1** - Minor Update

- Fixes a bug preventing options from being saved under WordPress 2.5.
- Updates option page to comply with WordPress 2.5 user interface changes.
- Note: Users of WordPress 2.3 may wish to stay on 2.02 as the UI will look 'weird' under 2.3.

**2.02** - Bug Fix

- Corrects potential XHTML validation issues with external link tracking.

**2.01** - Bug Fix

- Corrects XHTML validation issues with ga.js.

**2.0** - Major Update

- Adds support for the latest version of Google Analytics' tracking code (ga.js).
- Reverts external link/download tracking method back to writing the tracking code in the HTML source, due to the previous Javascript library no longer being support. Users of previous Google Analyticator versions may safely delete ga_external-links.js.
- Slightly modified the way extra code is handled. There are now two sections (before tracker initialization and after tracker initialization) to handle ga.js' extra functions. Refer to Google Analytics' support documentation for use of these sections.

**1.54** - Bug Fix

- Corrects problem where certain installation of WordPress do not have the user level value.

**1.53** - Bug Fix

- Finally fixes the "Are you sure?" bug some users experience.

**1.52** - Bug Fix

- Addresses compatibility issue with other JavaScript plugins.

**1.5** - Major Update

- Now using JavaScript solution for keeping track of external links instead of the current URL rewrite method. JavaScript library is courtesy of Terenzani.it.
- **IMPORTANT:** Google Analyticator is now in a folder. If upgrading from a version less than 1.5, delete google-analyticator.php from your /wp-content/plugins/ folder before proceeding.

**1.42** - Bug Fix

- Fixes a bug where outbound link tracking would be disabled if the tracking code was in the footer.

**1.41** - Minor Update

- Added an option to insert the tracking code in the footer instead of the header.

**1.4** - Major Update

- Adds support for download tracking.

**1.31** - Bug Fix

- Fixes a small bug with backslashes in the additional tracking code box.

**1.3** - Bug Fix

- WordPress 2.0 beta is now supported.
- Missing options page bug is finally fixed.

**1.2** - Major Update

- Added support for outbound links.

**1.12** - Bug Fix

- Try number two at fixing missing option page bug.

**1.11** - Bug Fix

- Hopefully fixed a bug where options page would sometimes not display.

**1.1** - Major Update

- Added an option to disable administrator logging.
- Added an option to add any additional tracking code that Google has.

**1.0** - Initial Release