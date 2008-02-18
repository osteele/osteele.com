Overview:
--------
The trackback_blackhole module should only be enabled on Drupal sites that do
not use the trackback module.  It is intended to minimize the impact on
resources of a trackback spammer attempting to leave trackback spam on your
website.

With the trackback_blackhole module enabled, any attempts to post or view
trackbacks on your site will simply be greeted by a blank page.  This 
essentially short circuits the normal Drupal path, which would instead 
generate a 404 error.  The problem that this module attempts to solve is that
trackback spammers tend to post phenomenal amounts of trackback spam, and
generating a 404 error for each attempt is a waste of resources that can
ultimately lead to a Denial of Service.  Additionally, this prevents the
trackback attacks from filling your watchdog logs with repeated 404 errors.


Features:
--------
 - no configuration, simply enable the module
 - minimizes the impact of trackback spam attacks on sites that do not use the
   trackback module


Requires:
--------
 - Drupal 4.5.x or Drupal 4.6.x or Drupal 4.7.x


Credits:
-------
 - Written by Jeremy Andrews
