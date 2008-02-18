$Id: README.txt,v 1.8 2007/02/26 17:26:10 karthik Exp $

Overview
--------
The relatedlinks module enables nodes to display related URLs to the user via
blocks. Related links can be defined in 3 ways:

  * Parsed links: links that are retrieved from the body of a node.
  * Manual links: links that are added manually.
  * Discovered links: links that are discovered by the module using various 
    criteria, including the category terms of a node and suggestions provided by 
    the search module (when enabled).

The relatedlinks module allows for flexibility in creating blocks for each type
of relatedlinks or creating blocks for a combination of link types.

Installation
------------
1) Copy the entire relatedlinks directory into your drupal module directory.
2) Go to admin/build/modules to enable relatedlinks. This should automatically
install the relatedlinks database table.
3) The discovered links feature requires either the taxonomy or search modules
to be enabled.
4) Go to admin/settings/relatedlinks to configure which content types may have
related links, and other options. The discovered links tab provides
configuration options for the discovered links feature.
5) Go to admin/build/block to enable the appropriate blocks for your site.

Upgrade
-------
If you are upgrading from an older version of this module, ensure that you
visit update.php to update existing database tables.

Links
-----
* Related links configuration: admin/settings/relatedlinks
* Block configuration: admin/build/block
* Project URL: http://drupal.org/project/relatedlinks

Authors
-------
Nic Ivy (nji@njivy.org)
Scott Courtney (scott@4th.com)
Karthik Kumar/ Zen / |gatsby| : http://drupal.org/user/21209
