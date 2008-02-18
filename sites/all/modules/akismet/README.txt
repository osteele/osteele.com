$Id: README.txt,v 1.6 2006/06/20 15:16:44 markus_petrux Exp $
********************************************************************
                    D R U P A L    M O D U L E                     
********************************************************************
Name	: akismet
Version	: 1.1.2
Author	: markus_petrux  [ http://www.phpmix.org ]
Drupal	: 4.7

********************************************************************
DESCRIPTION:

This module allows you to use Akismet services as an attempt to
protect your site from being spammed.

To use the akismet service, you need a WordPress.com API key. To get
one, you have to sign up for a free account at wordpress.com

Please, see the following pages for further information:
http://akismet.com
http://akismet.com/faq/
http://wordpress.com/api-keys/


********************************************************************
FEATURES:

* Ability to check for spam in comments and/or nodes, sending a
  query to Akismet, in real time.
* The actual content types that should be checked can be selected
  from the settings panel.
* Real time connections can be disabled.
* Detected spam is still recorded into database. However, a cron
  task can be customized to automatically remove spam older than a
  specified age.
* Moderators can submit missed spam or false possitives (ham) back
  to Akismet.
* Users may inherit moderator status depending on permissions
  assigned to them such as 'administer nodes', 'administer comments'
  or a set of 'moderate spam in <content-type>' permissions created
  by this module.
* Ability to perform publish/unpublish and submit spam/ham operations
  from links at the bottom of content.
* Enhanced moderator queue for nodes and comments aimed to help
  moderators to review spam, unpublished or even published content.
* An experimental set of anti-spambot measures that can be configured
  from the settings panel.
* All operations are logged to watchdog, trying not to generate a lot
  of records though.
* Spam counter that also reminds the 'counting since date'. This
  information can be changed at will from the settings panel.
* The spam counter is displayed on the settings panel and the
  moderation queue. It can also be displayed to user by means of a
  fully configurable and themable block.
* There is also an option in the settings panel that allows
  administrators set how many blocks they wish to use (or none at
  all). This is aimed to keep the block administration panel as clean
  as possible. Each block can be customized independently.
* Opt-in option for content administrators and moderators to receive
  e-mail notifications about new (or updated) content, only content
  needing approval or nothing at all. The site administrator can
  disable this option, though. If enabled, content administrators and
  moderators will have a new block of settings in their user profiles.
* Comprehensive settings panel where every option has been self
  documented with descriptions. Almost every feature can be customized.
* A couple of buttons are included in the contrib subdirectory for
  those who use the control panel module. I'm not really good with
  graphics, I did it the best that I could...
* Finally, this module has a simple, but hopefuly useful, version
  checker. It can check for updates at intervals that are customized
  in the settings panel. This option can be disabled.


********************************************************************
INSTALLATION - UPGRADE - UNINSTALLATION:

Please, see the file INSTALL.txt in this directory.

********************************************************************
