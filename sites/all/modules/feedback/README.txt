$Id: README.txt,v 1.7 2006/07/25 18:31:48 kbahey Exp $

Description:
------------

This is a simple feedback module, allowing visitors to your web site to
send you email from web forms.

Features:
---------

* Multiple feedback pages are configureable. Each page has its own settings,
  so e.g. you can create two forms which send to 2 different e-mail address.
* It's possible to define a list of categories which the user has to choose
  from - similar to drupals core-contact module since 4.7.
* Flooding Prevention. Each user is only able to send a configureable amount
  of mails per hour. So you are safe from flooding attacks.
* Configurable required fields on the web form (Sender Name, Postal Address,
  Phone Number, Message Subject, Message Body)
* If the visitor is a registered user of the site, then the sender's
  email address field and sender's name default to the values entered
  upon registration
* Configurable subject prefix for messages sent by visitors
* Configurable instructions/guidelines text above the form (for example,
  you can use this to link to your FAQ page, site map, or ask users to do
  a search before contacting you)
* Contact mail address can be different from the general email address
  of the site administrator
* Optional setting for logging of all attempts to contact you to the
  Drupal log
* Mails the IP address of the sender and the browser they are using to 
  the siteadmin (helps detect bots, and abuse)
* Works with clean URLs as well as regular URLs (post 4.5.0 CVS version) 

Database:
---------
With 4.7 and later this module uses own database tables, which are automatically
created for you. Previous versions did not require any new database tables, so
in any case you needn't care.

Installation:
-------------

Please see the INSTALL document for details.

Bugs/Features/Patches:
----------------------

If you want to report bugs, feature requests, or submit a patch, please do so
at the project page on the Drupal web site.
http://drupal.org/project/feedback

Author
------
Originally by: Barry O'Rourke (barry@alted.co.uk)
Rewritten by: Khalid Baheyeldin (http://baheyeldin.com/khalid and http://2bits.com)
Again rewritten by: Wolfgang Ziegler (nuppla@zites.net)