<?php
/*
*******************************************************************************
		Khanh's Instant Notepad (KIN) - A handy notepad when you need it!
						Copyright (c) 2005 John Ha
*******************************************************************************
This work is licensed under the Creative Commons Attribution License. To view a
copy of this license, visit http://creativecommons.org/licenses/by-nc-nd/2.5/
*******************************************************************************
*/
?>
/******************
 Form Manipulation
*******************/
function kin_save(note) {
	i = (note - 1) * <?= $this -> kin_num_opts ?> + 1;
	for(j = i; j < i + <?= $this -> kin_num_opts ?>; j++) {
		var dataRef = document.getElementById('kin_' + j);
		if (dataRef.type != 'checkbox' && dataRef.type != 'select-one') {
			if (dataRef.value != '') {
				return confirm('Save [Note #' + note + '] to database?\n  \'OK\' to save, \'Cancel\' to abort.');
			}
		}
	}
	alert('Nothing to save!');
	return false;
}
function kin_update_all() {
	if (confirm('You are about to do a global update to the database.\n  \'OK\' to continue, \'Cancel\' to stop.')) {
		for (i = 0; i < <?= $this -> kin_tot_notes ?>; i++) {
			for (j = 0; j < <?= $this -> kin_num_opts ?>; j++) {
				var input = document.createElement('input');
					input.name = 'kin_' + (i * <?= $this -> kin_num_opts ?> + j + 1);
					input.type = 'hidden';
				dataRef = document.getElementById('kin_' + (i * <?= $this -> kin_num_opts ?> + j + 1));
				if (dataRef.type == 'checkbox') {
					input.value = dataRef.checked;
				} else {
					input.value = dataRef.value;
				}
				document.getElementById('kin_form_' + <?= $this -> kin_tot_notes + 2 ?>).appendChild(input);
			}
		}
	} else {
		return false;
	}
}
function kin_delete(note) {
	return confirm('You are about to delete [Note  #' + note + ']!\n  \'OK\' to delete, \'Cancel\' to stop.');
}
function kin_purge_all() {
	return confirm('You are about to purge the kin database of all data.\n  \'OK\' to purge, \'Cancel\' to cancel.');
}
/***************
 Navigation Bar
****************/
function kin_go_to(loc) {
	var note = kin_loc_cookie();
	if (loc != note) {
		document.getElementById('kin_note_' + note).style.display = 'none';
		document.getElementById('kin_note_' + loc).style.display = kin_do_notes('kin_note', loc, 'block')[loc - 1];
	}
	kin_do_nav(loc);
	return false;
}
function kin_go_to_next() {
	if (<?= $this -> kin_tot_notes ?>) {
		var loc = kin_loc_cookie();
		if (loc < <?= $this -> kin_tot_notes ?>) {
			loc++;
			kin_go_to(loc);
		} else if (loc != 1) {
			loc = 1;
			kin_go_to(loc);
		}
	}
	return false;
}
function kin_go_to_prev() {
	if (<?= $this -> kin_tot_notes ?>) {
		var loc = kin_loc_cookie();
		if (loc > 1) {
			if (loc > <?= $this -> kin_tot_notes ?>) {
				loc = <?= $this -> kin_tot_notes ?>;
			} else {
				loc--;
			}
			kin_go_to(loc);
		} else if (loc != <?= $this -> kin_tot_notes ?>) {
			loc = <?= $this -> kin_tot_notes ?>;
			kin_go_to(loc);
		}
	}
	return false;
}
function kin_go_to_first() {
	if (<?= $this -> kin_tot_notes ?>) {
		kin_loc_cookie() != 1 ? kin_go_to(1) : 0;
	}
	return false;
}
function kin_go_to_last() {
	if (<?= $this -> kin_tot_notes ?>) {
		kin_loc_cookie() != <?= $this -> kin_tot_notes ?> ? kin_go_to(<?= $this -> kin_tot_notes ?>) : 0;
	}
	return false;
}
function kin_toggle_font(state) {
	state = state ? state : kin_do_info(3)[2];
	if (state != 'none') {
		document.getElementById('kin-nav').style.fontSize = '12px';
	} else {
		document.getElementById('kin-nav').style.fontSize = '16px';
	}
	return false;
}
function kin_toggle_msg(state) {
	state = state ? state : kin_do_info(2)[1];
	document.getElementById('kin_msg_panel').style.display = state;
	kin_do_nav();
	return false;
}
function kin_rollup_info(loc, state) {
	state = state ? state : kin_do_info(loc)[loc - 1];
	document.getElementById('kin_info_' + loc).style.display = state;
	kin_toggle_button('kin_info', loc, state);
	return false;
}
function kin_rollup_note(loc, state) {
	state = state ? state : kin_do_notes('kin_form', loc)[loc - 1];
	document.getElementById('kin_form_' + loc).style.display = state;
	kin_toggle_button('kin_form', loc, state);
	return false;
}
function kin_toggle_button(prefix, loc, state) {
	var button = document.getElementById(prefix + '_' + loc + '_');
	button.color = '#fff';
	if (state != 'none') {
		button.innerHTML = '&ndash;';
		if (typeof tooltip != 'undefined') {
			button.alt = button.alt ? button.alt.replace(/Rolldown/, 'Rollup') : 'Rollup';
			tooltip.changetip(button, button.alt);
		} else {
			button.title = button.title.replace(/Rolldown/, 'Rollup');
		}
	} else {
		button.innerHTML = '+';
		// If dom-tooltips is present, then update tooltips
		if (typeof tooltip != 'undefined') {
			button.alt = button.alt ? button.alt.replace(/Rollup/, 'Rolldown') : 'Rolldown';
			tooltip.changetip(button, button.alt);
		} else {
			button.title = button.title.replace(/Rollup/, 'Rolldown');
		}
	}
}
function kin_do_info(loc) {
	var info_cookie = ReadCookie('kin_info_state');
	var info = info_cookie.split('|');
	if (info.length != 3 || loc) {
		if (info.length != 3) {
			info = new Array();
			info[0] = 'block'; // msg rollup state
			info[1] = 'block'; // msg panel state
			info[2] = 'block'; // nav-bar font size
		} else if (loc) {
			if (info[loc - 1] != 'none') {
				info[loc - 1] = 'none';
			} else {
				info[loc - 1] = 'block';
			}
		}
		info_cookie = '';
		for (i = 0; i < info.length; i++) {
			info_cookie += info[i] + (i < info.length - 1 ? '|' : '');
		}
		SetCookie('kin_info_state', info_cookie, <?= $this -> kin_days ?>);
	}
	return info;
}
function kin_do_notes(prefix, loc, state) {
	var notes_cookie = ReadCookie(prefix + 's_state');
	var notes = notes_cookie.split('|');
	if (notes.length != <?= $this -> kin_tot_notes + 2 ?> || loc) {
		if (notes.length != <?= $this -> kin_tot_notes + 2 ?>) {
			notes = new Array();
			for (i = 0; i < <?= $this -> kin_tot_notes + 2 ?>; i++) {
				notes[i] = 'block';
			}
		} else if (loc) {
			if (state) {
				notes[loc - 1] = state;
			} else if (notes[loc - 1] != 'none') {
				notes[loc - 1] = 'none';
			} else {
				notes[loc - 1] = 'block';
			}
		}
		notes_cookie = '';
		for (i = 0; i < notes.length; i++) {
			notes_cookie += notes[i] + (i < notes.length - 1 ? '|' : '');
		}
		SetCookie(prefix + 's_state', notes_cookie, <?= $this -> kin_days ?>);
	}
	return notes;
}
function SetCookie(cookieName,cookieValue,nDays) {
	var today = new Date();
	var expire = new Date();
	if (nDays==null || nDays==0) nDays = 1;
	expire.setTime(today.getTime() + 3600000*24*nDays);
	document.cookie = cookieName + '=' + escape(cookieValue) + ';expires=' + expire.toGMTString();
}
function ReadCookie(cookieName) {
	var theCookie = '' + document.cookie;
	var ind = theCookie.indexOf(cookieName);
	if (ind == -1 || cookieName == '') return '';
	var ind1 = theCookie.indexOf(';', ind);
	if (ind1 == -1) ind1 = theCookie.length;
	return unescape(theCookie.substring(ind + cookieName.length + 1, ind1));
}
function kin_loc_cookie(crumb) {
	var loc = ReadCookie('kin_last_loc');
	if (loc > <?= $this -> kin_tot_notes + 2 ?> && <?= $this -> kin_tot_notes ?> > 0) {
		crumb = <?= $this -> kin_tot_notes ?>;
	} else if (loc > 2 && <?= $this -> kin_tot_notes ?> < 1) {
		crumb = 1;
	} else if (crumb < 1) {
		crumb = 1;
	}
	if (crumb) {
		if (isNaN(loc)) {
			SetCookie('kin_last_loc', crumb, <?= $this -> kin_days ?>);
		} else {
			if (parseInt(loc) != crumb) {
				SetCookie('kin_last_loc', crumb, <?= $this -> kin_days ?>);
			}
		}
	} else {
		if (isNaN(loc) || !loc || loc == 'undefined') {
			crumb = 1;
			SetCookie('kin_last_loc', crumb, <?= $this -> kin_days ?>);
		} else {
			crumb = loc;
		}
	}
	return crumb;
}
function kin_do_nav(loc) {
	var info = kin_do_info();
	var notes = kin_do_notes('kin_note');
	var min = '';
	for (i = 0; i < <?= $this -> kin_tot_notes + 2 ?>; i++) {
		min += i == <?= $this -> kin_tot_notes + 1 ?> && notes[i] == 'none' ? '&Theta;|' :
			i == <?= $this -> kin_tot_notes ?> && notes[i] == 'none' ? '+|' :
			notes[i] != 'block' ? (i + 1) + '|' : '';
	}
	min += info[1] == 'none' ? '&Xi;|' : '';
	min ? min = min.substring(0, (min.length - 1)) : min = '&ndash;';
	document.getElementById('kin-show-closed').innerHTML = '[' + min + ']';
	if (loc) {
		document.getElementById('nav_tab_' + kin_loc_cookie()).className = 'normal';
		document.getElementById('nav_tab_' + kin_loc_cookie(loc)).className = 'current';
	} else {
		document.getElementById('nav_tab_' + kin_loc_cookie()).className = 'current';
	}
	resizeNote();
}
<?php
if ($is_IE) {
?>
function CopyToClipboard() {
	CopiedTxt = document.selection.createRange();
	CopiedTxt.execCommand('Copy');
}
function PasteFromClipboard() {
	document.kin_scratch_pad.kin_scratch.focus();
	PastedText = document.kin_scratch_pad.kin_scratch.createTextRange();
	PastedText.execCommand('Paste');
}
<?php
}
?>
function print(s) {
	document.getElementById('kin-show-info').innerHTML = '[' + s + ']';
}
function write(s) {
	if (typeof(debug) == 'undefined') {
		debug = window.open('about:blank','debugWindow','width=800,height=580,scrollbars=yes');
	}
	debug.document.write('<pre>'+s+'<br /></pre>');
}
function getFile(filename) {
	oxmlhttp = null;
	try {
		oxmlhttp = new XMLHttpRequest();
		oxmlhttp.overrideMimeType('text/xml');
	}
	catch(e) {
		try {
			oxmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
		}
		catch(e) {
			return null;
		}
	}
	if(!oxmlhttp) return null;
	try {
		oxmlhttp.open('GET', filename, false);

		oxmlhttp.setRequestHeader('If-Modified-Since', 'Sat, 1 Jan 2000 00:00:00 GMT');
		
		oxmlhttp.send(null);
	}
	catch(e) {
		return null;
	}
	return oxmlhttp.responseText;
}
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
// schedule("objectID", "function(\"arg\")\;", timeout);
function schedule(objectID, functionCall, timeleft) {
	timeleft > 0 ? document.getElementById(objectID) ? eval(functionCall) : setTimeout("schedule('" + objectID + "', '" + functionCall + "', " + (timeleft - 20) + ")", 20) : 0;
}
function winHeight() {
	var myHeight = 0;
	if( typeof(window.innerHeight) == 'number' ) {
		//Non-IE
		myHeight = window.innerHeight;
	} else if(document.documentElement && document.documentElement.clientHeight) {
		//IE 6+ in 'standards compliant mode'
		myHeight = document.documentElement.clientHeight;
	} else if(document.body && document.body.clientHeight) {
		//IE 4 compatible
		myHeight = document.body.clientHeight;
	}
	return myHeight;
}
function findPosY(obj) {
	var curtop = 0;
	if (obj.offsetParent) {
		while (obj.offsetParent) {
			curtop += obj.offsetTop;
			obj = obj.offsetParent;
		}
	} else if (obj.y) curtop += obj.y;
	return curtop;
}
function resizeNote() {
	if (!resizing) {
		resizing = true;
		if (note = tNode = document.getElementById('kin_' + kin_loc_cookie() * <?= $this -> kin_num_opts ?>)) {
			while (tNode.nodeName != 'TR') tNode = tNode.parentNode;
			var h = 0;
			while (tNode = tNode.nextSibling) h += isNaN(tNode.offsetHeight) ? 0 : tNode.offsetHeight;
			h = winHeight() - findPosY(note) - h - 48;
			note.style.height = (h < 0 ? 0 : h) + 'px';
		}
		resizing = false;
	}
}
function kin_do_main() {
	var info = kin_do_info();
	// msg rollup & panel state
	kin_rollup_info(1, info[0]);
	document.getElementById('kin_msg_text').innerHTML = '<?= preg_replace("[\r|\n|\t]", '', $this -> kin_msg) ?>';
	kin_toggle_msg(info[1]);
	// note panel state
	document.getElementById('kin_note_' + kin_loc_cookie()).style.display = 'block';
	kin_toggle_font(info[2]);
	kin_do_nav();
	addEvent(window, 'resize', function () { resizeNote(); }, false);
}
var resizing = false;
addEvent(window, 'load', function () { kin_do_main(); }, false);
