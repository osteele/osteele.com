<?php
/*
Plugin Name: Khanh's Instant Notepad
Version: 0.2.0
Plugin URI: http://ink.bur.st/wordpress-plugins#kin
Description: A handy notepad for jotting down notes and stuff.
Author: John Ha
Author URI: http://ink.bur.st
*/


/*
*******************************************************************************
		Khanh's Instant Notepad (KIN) - A handy notepad when you need it!
						Copyright (c) 2005 John Ha
*******************************************************************************
This work is licensed under the Creative Commons Attribution License. To view a
copy of this license, visit http://creativecommons.org/licenses/by-nc-nd/2.5/
*******************************************************************************
*/

class kin {

function kin () {
	add_action('admin_menu', array(&$this, 'add_kin'));
	add_action('admin_head', array(&$this, 'kin_head'));
}

function add_kin() {
	add_menu_page('KIN', 'Notepad', 8, __FILE__);
}

function kin_head() {
?>
<script type="text/javascript">
/*<![CDATA[*/
function kin_window() {
	x = (screen.availWidth - 500) / 2;
	y = (screen.availHeight - 500) / 2;
	w = window.open('<?php bloginfo('url') ?>/wp-content/plugins/kin/kin-popup.php', 'Notepad', 'width=500, height=500, scrollbars=no, resizable=yes, top='+y+',screenY='+y+',left='+x+',screenX='+x+', toolbar=no, status=no');
	w.moveTo(x, y);
	return false;
}
function kin_do_main() {
	var mNodes = document.getElementById('adminmenu').getElementsByTagName('A');
	for (i = 0; i < mNodes.length; i++) {
		if (mNodes[i].innerHTML == 'Notepad') {
			rNode = mNodes[i].parentNode;
			rNode.innerHTML = '<a href="#" title="Open Notepad" onClick="return kin_window()">Notepad</a>';
			break;
		}
	}
};
<?php
global $is_IE;
if ($is_IE) { ?>
if (typeof addEvent != 'function') {
	function addEvent(obj, evType, fn, useCapture) {
		if (obj.addEventListener) {
			obj.addEventListener(evType, fn, useCapture);
			return true;
		} else if (obj.attachEvent) {
			var r = obj.attachEvent('on'+evType, fn);
			return r;
		} else {
			alert('Handler could not be attached');
		}
	}
}
addEvent(window, 'load', function () { kin_do_main(); }, false);
<?php } else { ?>
function schedule(objectID, functionCall, timeleft) {
	timeleft > 0 ? document.getElementById(objectID) ? eval(functionCall) : setTimeout("schedule('" + objectID + "', '" + functionCall + "', " + (timeleft - 20) + ")") : 0;
}
schedule("adminmenu", "kin_do_main()", 1000);
<?php } ?>
//]]>
</script>
<?php
}

}

$kin = new kin();
?>