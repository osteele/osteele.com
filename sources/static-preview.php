<?php
/*
File: static-preview.php
Author: Oliver Steele http://osteele.com
Version: 1.0.1
Date: 2004-08-14
URL: http://osteele.com/software/wp

Copyright (c) 2004 by Oliver Steele
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

Description:
I like to edit my postings with a text editor, not a browser form.
I also often edit them while I'm offline.  But I like to preview
how they'll look when they're run through the WordPress filters
such as Textile.

static-preview.php allows you to preview files that you edit on your
server's filesystem, without storing them in the WordPress database.
It is intended for the case where you have access to the file system of
your web server or a staging server capable of running php.  It allows
you to use a text editor such as emacs to edit a file in one window and
preview the results in another window.  This gives you more
full-featured editing than the web browser, and a faster edit-preview
workflow cycle than copying between a text editor and and the WordPress
Writing web form.

Installation:
- Place this file in your WordPress directory (next to index.php)
- Add this line to your .htaccess, after RewriteEngine On:
    RewriteRule ^(preview/.*)$ wp/static-preview.php?file=$1 [QSA]
  (where 'wp/' is the URL to your WordPress directory)
- Create a directory $WP/preview, where $WP is the file system
  directory that contains your WordPress installation.

Usage:
- Create a file in $WP/preview/posting.txt, and enter some text into it.
- From your browser, request http://server/wp/preview/posting.txt, where
server is the address of your web site, or localhost for a staging
server.  The text will be formatted according to the posting plugins
that are currently active.
- Edit your content, and press Reload in the browser.
- When you are done, press the Post button at the bottom of the page.
This creates a WordPress posting, and you can continue editing it within
the WordPress interface.  From this point on, the posting is stored in
the WordPress database.  To avoid confusion,  you're advised to remove
the copy from the preview file, once you're confident that it's really
there.

Offline usage:
The 'offline' query parameter tells static-preview not to connect to the
WordPress database.  This is useful for airplane editing if you run
a staging server that includes Apache and PHP, but not a copy of your
WP database, on your laptop.

To preview a posting while you are offline, request
  http://localhost/wp/preview/posting.txt?offline.
If you *always* do this, you can change the htaccess condition to
include the query parameter:
  RewriteRule ^(preview/.*)$ wp/static-preview.php?file=$1&offline [QSA]

In offline mode, static-preview can't query the WP database for the list
of active plugins.  By default, it will use only the markdown.php plugin.
To customize this list, create a file named offline-preview-plugins.txt
in the same directory as static-preview.php.  Each line of this file is
name of a file in $WP/wp-content/plugins that is loaded to display
an offline preview.

Plugins that require access to the WP database won't work for offline
preview.
*/

define('PREVIEW_ABSPATH', dirname(__FILE__).'/');
$offline = isset($_GET['offline']);
$preview_siteurl = '/wp'; // overwritten if !offline
$title = preg_replace('/(.+)(\.[^.]*)$/', '$1', basename($_GET['file']));

$offline_plugins_file = PREVIEW_ABSPATH.'offline-preview-plugins.txt';
$offline_plugins_file_used = false;
$plugins_used_msg = '';
$default_offline_plugins = 'textile2.php';

function load_preview_plugins($offline) {
	global $offline_plugins_file, $offline_plugins_file_used;
	global $default_offline_plugins, $plugins_used_msg;
	if ($offline) {
		$plugins = $default_offline_plugins;
		if (file_exists($offline_plugins_file)) {
			$plugins = file_get_contents($offline_plugins_file);
			$offline_plugins_file_used = true;
		}
		
		require('wp-includes/functions.php');
		$plugins = explode("\n", $plugins);
		foreach ($plugins as $plugin) {
			$plugin = trim(rtrim($plugin));
			if ($plugin=='') continue;
			if ($plugin[0]=='#') continue;
			if (file_exists(PREVIEW_ABSPATH . 'wp-content/plugins/' . $plugin)) {
				include(PREVIEW_ABSPATH . 'wp-content/plugins/' . $plugin);
				if ($plugins_used_msg)
					$plugins_used_msg .= ' ';
				$plugins_used_msg .= $plugin;
			}
		}
	} else {
		include(PREVIEW_ABSPATH.'wp-config.php');
	}
}

if (!$offline) {
	require_once(dirname(__FILE__).'/wp-config.php');
	$preview_siteurl = get_settings('siteurl');
}

if ($offline) {
	load_preview_plugins($offline);
	$myname = basename(__FILE__);
	$create_or_edit = 'create a file';
	if ($offline_plugins_file_used)
		$create_or_edit = 'edit the file';
	$offline_note =
		'<p class="no-plugins-file"><code>'.$myname.'</code> is using '.
		'the following plugins: <code>'.$plugins_used_msg.'</code>.	 '.
		'To customize this list, '.$create_or_edit.' named '.
		'<code>'.$offline_plugins_file.'</code>. '.
		'Refer to the documentation in <code>'.$myname.'</code> '.
		'for more information.</p>';
}
?>
<html>
<head>
<title><?php echo($title) ?></title>
<style type="text/css" media="screen">
  @import url(<?php echo($preview_siteurl) ?>/wp-layout.css);
  body {
	border: none;
	padding: 5pt;
  }
.no-plugins-file {font-style: italic; line-height: inherit; border-top: 2px solid #999}
</style>
</head>
<body>

<?php
$source_content = file_get_contents($_GET['file']);
$preview_content = apply_filters('the_content', $source_content);
echo($preview_content);
?>

<?php if (!$offline) {get_currentuserinfo();}
if (!$offline && $user_level > 0):
?>
<form name="post" action="/wp/wp-admin/post.php" method="post" id="post">

<input type="hidden" name="action" value="post" />
<input type="hidden" name="user_ID" value="<?php echo($user_ID); ?>" />
<input type="hidden" name="post_title" value="<?php echo($title) ?>" />
<input type="hidden" name="post_category[]" value="1"/ >
<input name="save" type="submit" id="save" tabindex="6" value="Save to WP" /> 

<div style="display: none">
<textarea name="content" id="content" rows="30" cols="40" tabindex="4" >
<?php echo(htmlspecialchars($source_content)); ?>
</textarea>
</div>

<?php endif; ?>

<?php if ($offline): ?>
<?php echo($offline_note); ?>
<?php endif; ?>

</form>

</body>
</html>
