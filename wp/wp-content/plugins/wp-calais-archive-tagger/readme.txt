=== WP Calais Archive Tagger ===
Contributors: dangrossman
Tags: tags, tagging, tagger, semantic web, semweb, semantic, suggest, suggestion, post
Requires at least: 2.3
Tested up to: 2.6
Stable tag: trunk

Goes through your archives and adds tags to your posts based on semantic analysis.

== Description ==

The Calais Archive Tagger plugin automatically goes through your archives and tags every post you've written. The plugin uses the Open Calais API to perform semantic analysis of your post text and suggest tags. If a post already contains a suggested tag, that tag isn't added, but other new tags found are. It takes about 5 minutes to tag 200 posts.

This plugin requires <b>PHP 5</b> and the cURL library (both of which are available on most web hosts).

Also see <a href="http://wordpress.org/extend/plugins/calais-auto-tagger/">WP Calais Auto Tagger</a> for suggesting tags as you write new posts. 

== Installation ==

1. Upload `calais_archive_tagger.php` and `opencalais.php` to the `/wp-contents/plugins` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add your Open Calais API key through the 'Calais Archive Tagger' sub-page of the 'Plugins' menu
4. Click the 'Start Tagging' link on the 'Calais Archive Tagger' sub-page of the 'Plugins' menu

To obtan a Calais API key:

1. Go to the Open Calais website at http://opencalais.com/
2. Click "Register" at the top of the page and create an account
3. Request an API key at http://developer.opencalais.com/apps/register

You should receive the key immediately.

== Frequently Asked Questions ==

= What's new in version 1.1 =

The script now sleeps for half a second between posts to ensure you stay within the current Calais API rate limits (2 requests per second and 40,000 requests per day).

I've also wrapped the API call in a try/catch block so that any exceptions don't cause a loop condition.

= What's new in version 1.2 =

The plugin now ensures no manually added tags are lost when adding new tags. E-mail addresses are no longer added as tags.

= What's new in version 1.3 =

Updated opencalais.php to support new entity types.

== Screenshots ==

1. Calais Archve Tagger interface