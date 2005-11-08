/*
// +----------------------------------------------------------------------+
// | Orginial Code Care Of:                                               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Bitflux GmbH                                      |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the "License");      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an "AS IS" BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: Bitflux GmbH <devel@bitflux.ch>                              |
// |         http://blog.bitflux.ch/p1735.html                            |
// +----------------------------------------------------------------------+
//
//
// +----------------------------------------------------------------------+
// | Heavily Modified by Jeff Minard (07/09/04)                           |
// +----------------------------------------------------------------------+
// | Same stuff as above, yo!                                             |
// +----------------------------------------------------------------------+
// | Author: Jeff Minard <jeff-js@creatimation.net>                       |
// |         http://www.creatimation.net                                  |
// +----------------------------------------------------------------------+
//
//
// +----------------------------------------------------------------------+
// | Heavily Modified by Jonas Rabbe (2005-05-08)                         |
// +----------------------------------------------------------------------+
// | Same stuff as above, yo!                                             |
// +----------------------------------------------------------------------+
// | Author: Jonas Rabbe <jonas@rabbe.com>                                |
// |         http://www.jonas.rabbe.com                                   |
// +----------------------------------------------------------------------+
//
//
// +----------------------------------------------------------------------+
// | What is this nonsense?? (2005-05-08)                                 |
// +----------------------------------------------------------------------+
// | This is a script that, by using XMLHttpRequest javascript objects    |
// | you can quickly add some very click live interactive feed back to    |
// | your pages that require server side interaction.                     |
// |                                                                      |
// | This javascript has been modified to make a 'digging archive' as     |
// | outlined by Michael Heilemann in his dissection of Freya             |
// | [http://binarybonsai.com/archives/2004/11/21/freya-dissection/]      |
// +----------------------------------------------------------------------+
*/

var tsaLiveReq = false;
var tsaLiveReqLast = "-";
var tsaYear = 0;
var tsaMonth = 0;
var tsaIsIE = false;

// on !IE we only have to initialize it once
if (window.XMLHttpRequest) {
	tsaLiveReq = new XMLHttpRequest();
}

function tsaLiveReqDoReq(query) {
	if (tsaLiveReqLast != query) {
		if (tsaLiveReq && tsaLiveReq.readyState < 4) {
			tsaLiveReq.abort();
		}
		
		if (window.XMLHttpRequest) {
		// branch for IE/Windows ActiveX version
		} else if (window.ActiveXObject) {
			tsaLiveReq = new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		tsaLiveReq.onreadystatechange = tsaLiveReqProcessReqChange;
		tsaLiveReq.open("GET", tsaProcessURI + "?" + query);
		tsaLiveReqLast = query;
		tsaLiveReq.send(null);
	}
}

function tsaLiveReqProcessReqChange() {
	if (tsaLiveReq.readyState == 4) {
		var tsaText = tsaLiveReq.responseText;
		var resultID = tsaText.substring(0, tsaText.indexOf('|'));
		tsaText = tsaText.substring(tsaText.indexOf('|') + 1, tsaText.length);
		
		var resultElement = document.getElementById(resultID)
		
		if( resultElement == null ) return;
		
		resultElement.innerHTML = tsaText; 
		
		var yearElement = document.getElementById(resultID+'-year')
		if( yearElement == null ) return;
		var year_list = yearElement.childNodes;
		for( var i = 0; i < year_list.length; i++ ) {
			if( year_list[i].nodeName == 'LI' ) {
				year_list[i].style.cursor = 'pointer';
				var tf = function(e) {
 					var tsaID = tsaEventElement(e).id;
 					tsaYear = tsaID.substring(tsaID.lastIndexOf('-') + 1, tsaID.length);
 					tsaSelectYear();
				}
				
				if( tsaIsIE ) {
					year_list[i].attachEvent('onclick',tf);
				} else {
					year_list[i].addEventListener('click', tf, false);
				}
			}
		}

		var monthElement = document.getElementById(resultID+'-month');
		if( monthElement == null ) return;
		var month_list = monthElement.childNodes;
		for( var i = 0; i < month_list.length; i++ ) {
			if( month_list[i].nodeName == 'LI' ) {
				month_list[i].style.cursor = 'pointer';
				var tf = function(e) {
 					var tsaID = tsaEventElement(e).id;
 					tsaMonth = tsaID.substring(tsaID.lastIndexOf('-') + 1, tsaID.length);
 					tsaSelectMonth();
				}
				
				if( tsaIsIE ) {
					month_list[i].attachEvent('onclick',tf);
				} else {
					month_list[i].addEventListener('click', tf, false);
				}
			}
		}
		
		// Fade Anything.
		if( typeof Fat != 'undefined' && /class="fade"/.test(tsaText)) {
			Fat.fade_all();
		}
	}
}

function tsaLiveReqInit() {
	if (navigator.userAgent.indexOf("Safari") > 0) {
		// branch to get to internet explorer
	} else if (navigator.product == "Gecko") {
		// branch to get to internet explorer
	} else {
		tsaIsIE = true;
	}

	tsaLiveReqDoReq('');
}

function tsaSelectYear() {
	tsaLiveReqDoReq('year=' + tsaYear);
}

function tsaSelectMonth() {
	tsaLiveReqDoReq('year=' + tsaYear + '&month=' + tsaMonth);
}

/*
	Courtesy of Chris Boulton [http://www.surfionline.com]
*/
function tsaEventElement(e) {
	if( tsaIsIE ) {
		return e.srcElement;
	} else {
		return e.currentTarget;
	}
}

/*
addEvent function found at http://www.scottandrew.com/weblog/articles/cbs-events
*/
function tsaAddEvent(obj, evType, fn) {
	if (obj.addEventListener) {
		obj.addEventListener(evType, fn, true);
		return true;
	} else if (obj.attachEvent) {
		var r = obj.attachEvent("on"+evType, fn); 
		return r;
	} else {
		return false;
	}
}

// tsaAddLoadEvent(tsaLiveReqInit);
tsaAddEvent(window, 'load', tsaLiveReqInit);