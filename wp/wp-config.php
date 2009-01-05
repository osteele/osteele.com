<?php
// ** MySQL settings ** //
// define('DB_NAME', 'oswordpress');    // The name of the database
// define('DB_USER', 'osteelewp');     // Your MySQL username
// define('DB_PASSWORD', 'osteelewp'); // ...and password
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

if ($_SERVER['HTTP_HOST'] == 'osteele.dev') {
	define('DB_HOST', 'osteele.dev');
	define('DB_NAME', 'oswordpress');    // The name of the database
	define('DB_USER', 'osteelewp');     // Your MySQL username
	define('DB_PASSWORD', 'osteelewp'); // ...and password
 } else {
	// define('DB_HOST', 'wordpressdb.osteele.com');
	define('DB_HOST', 'internal-db.s54779.gridserver.com');
	define('DB_NAME', 'db54779_osteelewp');    // The name of the database
	define('DB_USER', 'db54779');     // Your MySQL username
	define('DB_PASSWORD', 'wS6jpurf'); // ...and password
 }

define('WP_CACHE', true);

// Change each KEY to a different unique phrase.  You won't have to remember the phrases later,
// so make them long and complicated.  You can visit http://api.wordpress.org/secret-key/1.1/
// to get keys generated for you, or just make something up.  Each key should have a different phrase.
define('AUTH_KEY', 'under the wide and *rry sky'); // Change this to a unique phrase.
define('SECURE_AUTH_KEY', 'dig the ` and let me lie'); // Change this to a unique phrase.
define('LOGGED_IN_KEY', 'glad did I live && glad did I die'); // Change this to a unique phrase.

// You can have multiple installations in one database if you give each a unique prefix
$table_prefix  = 'wp_';   // Only numbers, letters, and underscores please!

// Change this to localize WordPress.  A corresponding MO file for the
// chosen language must be installed to wp-content/languages.
// For example, install de.mo to wp-content/languages and set WPLANG to 'de'
// to enable German language support.
define ('WPLANG', '');

/* That's all, stop editing! Happy blogging. */

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');
?>
