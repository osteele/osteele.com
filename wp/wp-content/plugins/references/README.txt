------------------------------------------------------------------------
File: Wordpress References Plugin 0.3.1 (Beta)
License: GNU/GPL (http://www.gnu.org/licenses/gpl.txt)
Date: July 24 2005
Author: Adam Hennessy
Email: adam@movabletripe.com
Website: http://www.movabletripe.com
------------------------------------------------------------------------

Description:

  Generates a list of references relating to any given post.
  Lists are generated from the post-meta (a.k.a 'Custom Fields').


Installation:

  (1) Upload 'references.php' to your /wp-content/plugins folder.
  (2) Activate the plugin. (Failure to activate the plugin before 
      adding the function in step 3 will result in a php error).
  (3) Add the following to your theme's index.php while in the
      post loop. (A good place is in the postmetadata):
   
	<?php post_references_link("References"); ?>

      You can also add $before and $after tags to the function
      to generate custom html around the output. Such as:

	<?php post_references_link("References", '<span class="postReferencesLink">', '</span>'); ?>

      This will output:

	<span class="postReferencesLink">
	  <a href="http://yourdomain.com/pathto/some-story/#references" rel="bookmark" title="References">References</a>
	</span>

  (4) Add the following to your theme's index.php (or single page template
      - such as single.php in kubrick) while in the post loop:

	<?php post_references("References"); ?>

      You can also add $before and $after tags to the function
      to generate custom html around the output. Such as:

	<?php post_references("References", '<div class="postReferences">', '</div>'); ?>

      This will output:

	<div id="postReferences">
	  <h2><a href="http://yourdomain.com/pathto/some-story/#references"
	        rel="bookmark" title="References">References</a></h2>
	  <ul>
	    <li><a href="http://somereference.com" title="A reference">Reference 1</a></li>
	    <li><a href="http://somereference2.com" title="Another reference">Reference 2</a></li>
	  </ul>
	</div>

  (5) Add a custom field to your post called 'reference'.

  (6) Add the link and/or data in the 'value' field.

  (7) Save the new/updated post.


Optional:

  (1) Add custom classes to your stylesheet (The class will be determined by
      your chosen $before variable shown in steps 3 and 4)

  (2) Add to and/or improve on the code.
  
  (3) Let me know of the improvements/additions so I can assess it and perhaps
      add it to my version.


Important Notes:

  (1) Tested on recent wordpress CVS builds (not tested on 1.2.2 - yet)

------------------------------------------------------------------------

Changelog:

July 24 2005 - 0.3.1 beta
 - Fixed typo in README.txt (Thanks John B. Abela http://www.johnabela.com/)

Feb 4 2005 - 0.3 beta
 - Added support for $before and $after variables
 - Stripped (some) unnecessary html from php functions
 - Added post_references_link() function for displaying on index/home page
 - Fixed (some) html semantics
 - Fixed XHTML 1.1 validation

Jan 25 2005 - 0.2 beta
 - References plugin is born (at least it is publically available anyhow)
