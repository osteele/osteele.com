Version 2.2

If you use this code, please give me credit.  I'd also like to see where you're using it.  So send me urls!


http://www.broken-notebook.com/spell_checker

http://sourceforge.net/projects/ajax-spell/


This code requires php and the pspell module to work correctly.
It has only been tested on Firefox and Internet Explorer so far, and I'm
told that it works fine in Safari too.

If you find any bugs or anything please email me and let me know.


Garrison Locke

gplocke@broken-notebook.com

http://www.broken-notebook.com


---------------------------------------------------------------------------------
Changelog
---------------------------------------------------------------------------------


Fixes in Version 2.2 - 24.June.2005
--------------------------------------------------------------
- Fixed a problem where it wouldn't strip the html tags properly.

- Non-standard unicode characters are no longer used.

- The compressed Javascript file wasn't being compressed properly, 
  so I used a different one and that fixed the problem.


Fixes in Version 2.1 - 23.June.2005
--------------------------------------------------------------
- Fixed a bug where it wouldn't resume if the box was empty.



Fixes in Version 2.0 - 23.June.2005
--------------------------------------------------------------
- Fixed a bug where words with apostrophes wouldn't be replaced properly if your server
  had magic quotes turned on.
  
- Fixed a bug where "No Misspellings" would be returned if you clicked Check Spelling twice.

- Fixed a bug where if you clicked "Resume Editing" twice it would delete the contents of the text box.

- Made it so you can still preview the HTML even if there are no misspellings.

- Fixed the &quot;flashing&quot; problem where the text was shown before the spelling updates were made.

- Added a feature that makes the spell_checker.php include optional. You can call setSpellUrl with the
  url to the spell_checker.php file if you don't want to include it in every page. It defaults to SELF if you don't set it,
  so you'll need to include the spell_checker.php page if you don't set the url to it yourself with the setSpellUrl call.

- Thanks to Jake Olefsky of www.jakeo.com for most of these updates except for the optional include update.
  Thanks to Ir8 Prim8 of www.prim8.net for that update.  Thanks to Justin Greer for testing the magic quotes crap.



Fixes in Version 1.9 - 16.June.2005
--------------------------------------------------------------
- The fix from 1.8 to strip out extra stuff in html tags had a bug. That's now fixed.

- The results and suggestions divs are now auto generated so you don't have to have 
  them hardcoded on the page. This allows for multiple spell checkers on the same page. 
  You currently still need to have a hardcoded action and status divs for each spell checker.

- Added a fix in spell_checker.php for the people who don't have magic_quotes_gpc enabled on 
  their server...I think this works ok, but it's not been verified as of yet. It basically 
  only affects words with apostrophe's and them not being replaced.

- Added a check so it will say "No Suggestions" if there aren't any suggestions for the misspelled word.

- Added check to the onClick handler in spell_checker.js so that it doesn't interfere with 
  any onClick handlers that may already exist on the page.

- Modified the findPosX and findPosY functions in spell_checker.js so that it will find the 
  correct position if the spellchecker is inside any other divs.

- Consolidated the switchText() spell_checker.js code a bit.

- Many thanks to Justin Greer for helping find these bugs.


Fixes in Version 1.8 - 16.June.2005
--------------------------------------------------------------
- Code will now strip out all arbitrary text the user might have added inside html
  tags. (i.e. <b onMouseover="document.location='something';"> will now be shortened to just <b>).
  Thanks to Jake Olefsky for noticing that potential security issue.

- Added support for the img tag.  The image will be shown while in the (preview) spell checking mode.

- Fixed a bug where strings would be stripped of slashes all the time.  I changed it so it only does
  stripslashes if magic quotes are on.

- Upgraded to CPAINT 1.01


Fixes in Version 1.7 - 08.June.2005
--------------------------------------------------------------
- Back to CPAINT.  Bugs have been worked out and it works very well and
  very quickly. It's also much more efficient than Sajax.

- Added a Beta pspell wrapper for aspell. I've never actually tested it 
  for myself so let me know if you use and if it works or doesn't work or whatever.
  Thanks to Thanks to Andreas Gohr <andi@splitbrain.org> for that addition.

- Also added a thing to increase or decrease the size of the text box...some dude 
  asked for it, so there it is if you want to use it. 
  

Fixes in Version 1.6 - 06.June.2005
--------------------------------------------------------------
- Reverted back to Sajax instead of CPAINT.  I had reports of it
  not working well with some browsers.  Will wait for later version.


Fixes in Version 1.5 - 03.June.2005
--------------------------------------------------------------
- Code no longer uses Sajax cause it was slow and very inefficient.
  Uses a new homebrew AJAX library called CPAINT by BooleanSystems.
  http://www.booleansystems.com.  Now the code is hella fast.

- Separated Javascript and CSS from the file.  Also added a compressed
  version of the Javascript so it's faster and smaller.


Fixes in Version 1.4 - 31.May.2005
--------------------------------------------------------------
- Fixed a little bug where it wouldn't convert html entites back 
  to their applicable characters.


Fixes in Version 1.3 - 26.May.2005
--------------------------------------------------------------
- Fixed the bug where it would add breaks for no good reason in Firefox
  if you had a lot of text.  Firefox tries to pretty up 
  the formatting of your html, which I didn't want it to do.


Fixes in Version 1.2 - 26.May.2005
--------------------------------------------------------------
- Made a change to the Sajax.php file to make it use POST so
  that large pieces of text go through properly.
  

Fixes in Version 1.1 - 25.May.2005
--------------------------------------------------------------
- Added BSD open source license.