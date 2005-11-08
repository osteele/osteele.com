Spelling Checker Plugin for WordPress
-------------------------------------

This package contains the files needed to enable spell checking of posts 
and comments from within WordPress 1.2 and 1.5. There are a few ways to 
install this plugin.

1) By far the easiest installation option is utilizing the WordPress 
Plugin Manager and selecting a One-Click install of the plugin from the 
Plugin Manager interface. 

2) If this is a new installation and you're not performing a one-click
installation, perform the following steps:

	* Copy the "spell-plugin.php" file into your "wp-content/plugins" 
	directory.
	
	* Create a new directory in your "wp-content" directory called 
	"spell-plugin". For the default installation option, be certain 
	that this directory is writeable by the web server process. This 
	usually involves performing a chmod 755 on this directory but some 
	servers are different. Note that you have the option to choose a 
	different directory for things that must be written (a directory 
	for temporary data and a place for the personal dictionary) from 
	within the plugin options setup interface.
	
	* Copy the remaining files into this directory.
	
3) If you are upgrading from any previous version of this plugin, follow 
the steps in the "new installation" instructions above. Note that this
version moves the auxilliary directory from "wp-content/plugins/spell" to 
"wp-content/spell-plugin" in order to be usable by the WordPress Plugin 
Manager. If you have configuration options in the spellConfig.php file in 
the old directory, these options will be imported upon your first visit to 
the new plugin options page. Similarly, if you have an existing personal 
dictionary in the old heirarchy, that dictionary will be migrated to the 
new location. Therefore, once the plugin has been configured the first 
time in the new location (with the plugin interface), you are welcome to 
delete the old directory.

4) The plugin must be activated from the WordPress plugin page before it 
will function. 

5) IMPORTANT: Once activated, you then _must_ visit the plugin options 
page at least once to enable the functionality. Until you view this page, 
the plugin will not function and no "Check Spelling" button will appear. 
Simply visit: 
	
http://{WordPress root}/wp-content/plugins/spell-plugin.php?speller_setup
