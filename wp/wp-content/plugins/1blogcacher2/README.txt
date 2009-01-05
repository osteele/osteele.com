=== 1 Blog Cacher ===
Contributors: javiergarciaesteban
Donate link: http://1blogcacher.com/
Tags: caching, cache, cacher
Requires at least: 1.5
Tested up to: 2.3.1
Stable tag: 2.0.1

1 Blog Cacher is a WordPress plugin that caches your pages in order to increase the response speed and minimize the server load.

== Description ==

1 Blog Cacher is a WordPress plugin that caches your pages in order to increase the response speed and minimize the server load.

* Quick and easy installation/configuration.
* Portable: edit the file for your convenience and use it anywhere.
* Cached files are stored in HTML files, and organized in directories emulating the urls (if "safe_mode" isn't enabled), so it's easy displaying the content of the files and organize them (for instance deleting the cache for a given entry, for all categories, for all searches, for all posts from a given date, etc.)
* If "safe_mode" is enabled, the plugin will still work, creating all the files in the cache directory.
* Option to remove all cache files (or just the expired ones) from the WordPress panel.
* Expiration time for cached files.
* Rejected and accepted strings in order to control exactly the urls to cache.
* Rejected User Agents in order to avoid over-caching from search engines.
* Cached files (including front page cache) are updated when posts and comments are published/edited/deleted.
* Option to include a "Expires" header in order to enable browser cache (even fastest response speed and less page requests. Inconvenience: Users won't be seeing their comments after submitting them).
* Only GET requests are cached.
* Browser super-reload (Ctrl+F5) avoids cached urls.
* Compatible with Gzip compression.

== Installation ==

1. (Optional) Edit the values in the advanced-cache.php file (define...) for your convenience (further information in that file).
2. Create the cache directory /wp-cache/ in your WordPress directory (<your wordpress directory>/wp-cache/) and make it writeable (chmod 777).
3. Upload 1blogcacher2.0.php file to /wp-content/plugins/ WordPress directory (<your wordpress directory>/wp-content/plugins/1blogcacher2.0.php).
4. Upload advanced-cache.php file to /wp-content/ (<your wordpress directory>/wp-content/advanced-cache.php).
5. Add this line to the wp-config.php file ("<yourwordpressdirectory>/wp-config.php"): define('WP_CACHE', true);
6. Activate the plugin and take a look to "Options > 1 Blog Cacher" in the WordPress panel.

That's all!

== New in version 2.0 ==

* Use of WordPress advanced-cache. The plugin runs before WordPress is fully loaded (less execution time and specially less memory use).
* Management of HTTP headers, that are saved in .txt files after being conveniently modified, for full cached responses.
* Support for dynamic code (mfunc and mclude comments) as in Staticize Reloaded (and later in WP-Cache).
* If Gzip compression is enabled, compressed content is saved in .gz files so it's only compressed once (less execution time and less CPU use). If dynamic code is used, that code is run and only if the final content is different from the already saved it's compressed again for the response.
	* If you have Gzip compression enabled and you are not using dynamic code, you can set the constant OBC_LOOK_FOR_DYNAMIC_CODE to false in order ro avoid this check.
* HTTP header 304 "not modified" returned when it's convenient (less loading time):
	* If the plugin is going to return the same cache (from the same date) to a user, it returns a 304 header instead.
	* Even with a different cache, if the content to return is the same (checked through a Etag header with a hash), a 304 header is also returned.
* When a post is created, modified or removed, the cache for that post and the index are removed for *all* users.
* (Logged) users and commenters management. Choose the plugin's behaviour for each group::
	* Use no cache.
	* Use a single global cache.
	* Use an individual cache for each user.
* More configuration options:
	* Option to cache or not error pages (status 404).
	* Option to cache or not redirections (status 301 or 302).
	* Option to omit url trailing slash ("/") in order to avoid caching the same content twice (don't use this in WordPress 2.3+ or if you are using a plugin that redirects urls with trailing slash).
	* Option to save all files in the same directory.
* The plugin creates automatically a .htaccess file in the cache directory that forbids web access.
* Only "inconvenience" in this version: now the cache directory must be <your wordpress directory>/wp-cache/ (though it would be easy changing this in the code).

== Based on ==

* WP-Cache by Ricardo Galli (http://mnm.uib.es/gallir/wp-cache-2/)
* HTML Cache Creator (http://www.storyday.com/html/y2007/908_html-cache-creator-12.html)

Thanks to both!

== More Info ==

For more info, please visit [http:/1blogcacher.com/ 1 Blog Cacher home page].