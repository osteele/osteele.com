<?php
/*
Plugin Name: Ajax Spell Checker
Version: 0.6
Plugin URI: http://www.lab4games.net/zz85/blog/live-spell-checker/
Description: The MOD of spellchecker plugin for wordpress using this <a href="http://www.broken-notebook.com/spell_checker/index.php">Ajax spellchecker</a>
Author: zz85 & m0n5t3r & emil & broken-notebook
Author URI: http://www.lab4games.net/zz85/blog

Instructions:
Just copy into your plugin folder, activate and run it!
Edit config.php if needed

Modifications:
0.4
- Update to spellcheck 1.7
- Spell check for comments.
- Fix some bugs and make some improvments

0.5 
- More fixes to pspell
- Integration of Emil’s DHTML SpellChecker (http://me.eae.net/archive/2005/05/27/dhtml-spell-checker/)
- Check as u type style

0.6
- Integration of Chris Meller's Google Spellcheck webservice (http://dacnomm.com/googlespell/)
- Upgrade to the latest Garrison Locke spellcheck 2.2 (http://www.broken-notebook.com)
- Fix the resized textarea
- Addition of config.php

Known issues: 
- Wont work with autosave or wysiwygs enabled
- Layout is messed up in Tiger admin

Improvements: 
- Get selected text for spell checking
- Fix styles

Warning:
zz85 provides low quality support, try at your own risk and fun ;)

*/


global $spellchecker_path;
$spellchecker_path = get_settings("siteurl")."/wp-content/plugins/spell_checker/";

require_once "spell_checker/spell_checker.php";


function spellcheck_head()
{
	global $spellchecker_path;
	
	?>
	<link rel="stylesheet" type="text/css" href="<?=$spellchecker_path ?>spell_checker.css" />
	<script src="<?=$spellchecker_path ?>spell_checker.js" type="text/javascript"></script>
	<script src="<?=$spellchecker_path ?>cpaint.inc.js" type="text/javascript"></script>
	
	<link rel="stylesheet" type="text/css" href="<?=$spellchecker_path ?>spellchecker.css" />
	<script type="text/javascript" src="<?=$spellchecker_path ?>spellchecker.js"></script>
	
	<?php  /* spell_checker_compressed.js */
}

function spellcheck_ui($spellingbox)
{
?>
<div class="action" id="action">
	<div class="check_spelling" onClick="setObjToCheck('<?=$spellingbox?>'); spellCheck();">Check Spelling</div>
</div>
<div class="action" id="schecker"> 
	<div class="check_spelling" onClick="startSpellFx(this);">Activate Spell Check while Typing</div>
</div>

<span class="status" id="status"></span>
<?php
}

function spellcheck_footer($spellingbox)
{
	
?>

<script type="text/javascript">
var el, o, spt;

function startSpellFx(ele) {
	webFXSpellCheckHandler.serverURI = "<?php 
	global $spellchecker_path;
	echo $spellchecker_path."spellchecker.php";?>";
	el = document.getElementById('<?=$spellingbox?>');
	o = new WebFXSpellChecker(el, false);
	ele.innerHTML = ""; //Happy Typing
	//ele.onClick = "startCommentsUpdates();";
	
	setTimeout("startCommentsUpdates()", 500);
	/*
	el2 = document.getElementById('<?=$spellingbox?>');
	el2.onFocus= "alert('boo');spt = false;";
	el2.onBlur= "spt = true;";
	el2.onChange="spt = false;";
	el2.onKeyPress= "spt = false;";
	*/
	
}

function startCommentsUpdates(){
	if (o) o.toForm(); //Place existing text into SpellBox
	spt = true;
	updateCommentForm();
}

function updateCommentForm(){
	if (o && spt) {
		o.fromForm();
	}
	setTimeout("updateCommentForm()", 100);
}
//---

c=document.getElementById('<?=$spellingbox?>');
d=c.parentNode;

s=document.getElementById('status');
if(s)s.parentNode.removeChild(s);

ac=document.getElementById('action');
if(ac)ac.parentNode.removeChild(ac);

sc = document.getElementById('schecker');
if(sc)sc.parentNode.removeChild(sc);

if(ac && s){
    d.insertBefore(ac,c);
    d.insertBefore(sc,c);
    d.appendChild(s);

    

    /*
    c.style.width="97%";
    c.style.height="auto";
    c.onfocus=function(){ setObjToCheck('<?=$spellingbox?>'); resetAction(); };
    setObjToCheck('<?=$spellingbox?>');
    resetAction();
    */	
    
}

setSpellUrl('<?php 
global $spellchecker_path;
echo $spellchecker_path."spell_checker.php";?>');
</script>
<?php

}



function spellcheck_adminhead(){
	if(stristr($_SERVER["PHP_SELF"],"post.php")||stristr($_SERVER["PHP_SELF"],"page-new.php")) {
		spellcheck_head();
	}
}

function spellcheck_adminfooter(){
	if(stristr($_SERVER["PHP_SELF"],"post.php")||stristr($_SERVER["PHP_SELF"],"page-new.php")){
		spellcheck_footer('content');
	}
}

function spellcheck_commenthead(){
	//if (is_single())
		spellcheck_head();
}

function spellcheck_commentfooter(){
	//if (is_single())
		spellcheck_footer('comment');
}

function spellcheck_ui_admin(){
	spellcheck_ui('content');
}

function spellcheck_ui_comment(){
	spellcheck_ui('comment');
	spellcheck_commentfooter();
}

add_action("admin_head","spellcheck_adminhead");
add_action("admin_footer","spellcheck_adminfooter");
add_action("simple_edit_form","spellcheck_ui_admin");
add_action("edit_form_advanced","spellcheck_ui_admin");
add_action("edit_page_form","spellcheck_ui_admin");

add_action("wp_head","spellcheck_commenthead");
add_action("comment_form","spellcheck_ui_comment");
//add_action("wp_footer","");




?>
