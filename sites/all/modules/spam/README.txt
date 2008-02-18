Overview:
--------
The spam module is a powerful collection of tools designed to help website
administrators to automatically deal with spam.  Spam is any content that
is posted to a website that is unrelated to the subject at hand, usually in
the form of advertising and links back to the spammer's own website.  This
module can automatically detect spam, instantly unpublish it, and send
notification to the site administrator.


Features:
--------
 - Written in PHP specifically for Drupal.
 - Highly configurable.
 - Automatically detects and unpublishes spam comments and other spam content.
 - Automatically learns to detect spam in any language using Bayesian logic.
 - Automatically learns and blocks spammer URLs.
 - Automatically blacklists IPs of learned spammers, preventing them from
   posting additional spam and wasting database resources.
 - Detects repeated postings of the same identical content.
 - Detects content containing too many links, or the same link over and over.
 - Supports the creation of custom filters using powerful regular expressions.
 - Can notify the user that his or her content was determined to be spam,
   preventing confusion over why their content doesn't show up.
 - Can notify the site administrator in an email when spam is detected.
 - Provides 'report as spam' links allowing users to easily help detect spam.
 - Provides simple administrative interfaces for reviewing spam content.
 - Provides comprehensive logging to offer an understanding as to how and why
   content is determined to be or not to be spam.


Requires:
--------
 - Drupal 5.x


Credits:
-------
 - Written by Jeremy Andrews <jeremy@kerneltrap.org>
