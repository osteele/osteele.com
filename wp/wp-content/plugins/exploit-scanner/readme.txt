=== WordPress Exploit Scanner ===
Contributors: donncha, duck_, ryan, azaozz
Tags: hacking, spam, hack, crack, exploit, vulnerability
Tested up to: 2.8.5
Stable tag: 0.5
Requires at least: 2.7.1
Donate link: http://ocaoimh.ie/wordpress-plugins/gifts-and-donations/

Search the files and database of your WordPress install for malicious code or spammy links left by a hacker.

== Description ==
This plugin searches the files on your website, and the posts and comments tables of your database for anything suspicious. It also examines your list of active plugins for unusual filenames.

It does not remove anything. That is left to the user to do.

MD5 for version 0.1:      6a88a18a37c4add7dabd72fc97be13b6
MD5 for version 0.2:      48dd892fb9c41899af14e9cf94ec7ea8
MD5 for version 0.3:      44cc8a46861f18698789357fa2fc7e60
MD5 for version 0.4:      54f04bb11ab369063a9c8cc34fe9ee86
MD5 for version 0.5:      e434bad527c860ebf95777c05d551784
MD5 for hashes-2.8.5.php: a64eb922fa9d21bd43398467e8eb67cc

See the [WordPress Exploit Scanner homepage](http://ocaoimh.ie/exploit-scanner/) for further information.

== Installation ==
1. Download and unzip the plugin.
2. Copy the exploit-scanner directory into your plugins folder.
3. Visit your Plugins page and activate the plugin.
4. A new menu item called "Exploit Scanner" will be made off the Dashboard.

== Frequently Asked Questions ==

= How do I fix the out of memory error? =

Scanning your website can take quite a bit of memory. The plugin tries to allocate 128MB but sometimes that's not enough. You can allocate more by editing wp-config.php and adding the following line before the wp-settings.php require command. This code allocates 256MB of memory. Adjust the "256" figure to suit your needs.

`define( 'WP_MEMORY_LIMIT', '256M' );`


== Updates ==
Updates to the plugin will be posted here, to [Holy Shmoly!](http://ocaoimh.ie/) and the [WordPress Exploit Scanner](http://ocaoimh.ie/exploit-scanner/) will always link to the newest version.
