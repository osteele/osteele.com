<?php 
	require('./../../../../wp-blog-header.php'); 
	$plugin_path = get_settings('siteurl') . '/wp-content/plugins/af-extended-live-archive/includes/af-ela.php';
	// get settings and construct default;
	$settings = get_option('af_ela_options');
	if (!$settings) {
		echo "document.write('<div id=\"af-ela\"><p class=\"alert\">Plugin is not initialized. Admin or blog owner, visit the ELA option panel in your admin section.</p></div>')";
		return;
	} else {
?>/*
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
// +----------------------------------------------------------------------+
// | Heavily Modified by Jeff Minard (07/09/04), Jonas Rabbe (2005-05-08) |
// | and Arnaud Froment (2005-07-10)                                      |
// +----------------------------------------------------------------------+
// | Same stuff as above, yo!                                             |
// +----------------------------------------------------------------------+
// | Author: Jeff Minard <jeff-js@creatimation.net>                       |
// |         http://www.creatimation.net                                  |
// | Author: Jonas Rabbe <jonas@rabbe.com>                                |
// |         http://www.jonas.rabbe.com                                   |
// | Author: Arnaud Froment                                               |
// |         http://www.sonsofskadi.net                                   |
// +----------------------------------------------------------------------+
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

var af_elaLiveReq = false;
var af_elaLiveReqLast = "-";
var af_elaYear = 0;
var af_elaMonth = 0;
var af_elaCategory = 0;
var af_elaTag = 0;
var af_elaMenu = 0;
var af_elaIsIE = false;
var af_elaProcessURI = '<?php echo $plugin_path; ?>';
var af_elaResultID = '<?php echo $settings['id']; ?>';
var af_elaLoadingContent = '<?php echo $settings['loading_content']; ?>';
var af_elaIdleContent = '<?php echo $settings['idle_content']; ?>';

// on !IE we only have to initialize it once
if (window.XMLHttpRequest) {
	af_elaLiveReq = new XMLHttpRequest();
}

function af_elaLiveReqDoReq(query) {
	if (af_elaLiveReqLast != query) {
		if (af_elaLiveReq && af_elaLiveReq.readyState < 4) {
			af_elaLiveReq.abort();
		}
		
		if (window.XMLHttpRequest) {
		// branch for IE/Windows ActiveX version
		} else if (window.ActiveXObject) {
			af_elaLiveReq = new ActiveXObject("Microsoft.XMLHTTP");
		}
		af_elaLiveReq.onreadystatechange = af_elaLiveReqProcessReqChange;
		af_elaLiveReq.open("GET", af_elaProcessURI + "?" + query);
		af_elaLiveReqLast = query;
		af_elaLiveReq.send(null);
	}
}

function af_elaGenerateMenu() {
	var menuElement = document.getElementById(af_elaResultID+'-menu')
	if( menuElement == null ) {
		return false;
	} else {
		var menu_list = menuElement.childNodes;
		for( var i = 0; i < menu_list.length; i++ ) {
			if( menu_list[i].nodeName == 'LI' ) {
				menu_list[i].style.cursor = 'pointer';
				var tf = function(e) {
 					var af_elaID = af_elaEventElement(e).id;
 					af_elaMenu = af_elaID.substring(af_elaID.lastIndexOf('-') + 1, af_elaID.length);
 					af_elaSelectMenu();
				}
				
				if( af_elaIsIE ) {
					menu_list[i].attachEvent('onclick',tf);
				} else {
					menu_list[i].addEventListener('click', tf, false);
				}
			}
		}
		return true;
	}
}

function af_elaGenerateYear() {
	var yearElement = document.getElementById(af_elaResultID+'-year')
	if( yearElement == null ) {
		return false;
	} else {
		var year_list = yearElement.childNodes;
		for( var i = 0; i < year_list.length; i++ ) {
			if( year_list[i].nodeName == 'LI' ) {
				year_list[i].style.cursor = 'pointer';
				var tf = function(e) {
					var af_elaID = af_elaEventElement(e).id;
					af_elaYear = af_elaID.substring(af_elaID.lastIndexOf('-') + 1, af_elaID.length);
					af_elaSelectYear();
				}
				
				if( af_elaIsIE ) {
					year_list[i].attachEvent('onclick',tf);
				} else {
					year_list[i].addEventListener('click', tf, false);
				}
			}
		}
		return true;
	}
}

function af_elaGenerateMonth() {
	var monthElement = document.getElementById(af_elaResultID+'-month');
	if( monthElement == null ) {
		return false;
	} else {
		var month_list = monthElement.childNodes;
		for( var i = 0; i < month_list.length; i++ ) {
			if( month_list[i].nodeName == 'LI' ) {
				month_list[i].style.cursor = 'pointer';
				var tf = function(e) {
 					var af_elaID = af_elaEventElement(e).id;
 					af_elaMonth = af_elaID.substring(af_elaID.lastIndexOf('-') + 1, af_elaID.length);
 					af_elaSelectMonth();
				}
				
				if( af_elaIsIE ) {
					month_list[i].attachEvent('onclick',tf);
				} else {
					month_list[i].addEventListener('click', tf, false);
				}
			}
		}
		return true;
	}
}

function af_elaGenerateTag() {
	var tagElement = document.getElementById(af_elaResultID+'-tag')
	if( tagElement == null ) {
		return false;
	} else {
		var tag_list = tagElement.childNodes;
		if( !af_elaIsIE ) {
			for( var i = 0; i < tag_list.length; i++ ) {
				if( tag_list[i].nodeName == 'LI' ) {
					tag_list[i].style.cursor = 'pointer';
					var tf = function(e) {
		 				var af_elaID = af_elaEventElement(e).id;
		 				af_elaTag = af_elaID.substring(af_elaID.lastIndexOf('-') + 1, af_elaID.length);
		 				af_elaSelectTag();
					}
					tag_list[i].addEventListener('click', tf, false);
				}
			}
		} else {
			for( var i = 0; i < tag_list.length; i++ ) {
				if( tag_list[i].nodeName == 'LI' ) {
					tag_listIE = tag_list[i].childNodes;
					for( var j = 0; j < tag_listIE.length; j++ ) {
						if( tag_listIE[j].nodeName == 'FONT' ) {
							tag_listIE[j].style.cursor = 'pointer';
							var tf = function(e) {
		 						var af_elaID = af_elaEventElement(e).parentNode.id;
		 						af_elaTag = af_elaID.substring(af_elaID.lastIndexOf('-') + 1, af_elaID.length);
		 						af_elaSelectTag();
		 					}
		 					tag_listIE[j].attachEvent('onclick',tf);
						}
					}
				}
			}
		}
		return true;
	}
}

function af_elaGenerateCategory() {
	var categoryElement = document.getElementById(af_elaResultID+'-category')
	if( categoryElement == null ) {
		return false;
	} else {
		var category_list = categoryElement.childNodes;
		for( var i = 0; i < category_list.length; i++ ) {
			if( category_list[i].nodeName == 'LI' ) {
				if ( category_list[i].className == 'empty') {
					category_list[i].style.cursor = 'default';
				} else { 
					category_list[i].style.cursor = 'pointer';
					var tf = function(e) {
		 				var af_elaID = af_elaEventElement(e).id;
	 					af_elaCategory = af_elaID.substring(af_elaID.lastIndexOf('-') + 1, af_elaID.length);
	 					af_elaSelectCategory();
					}	
				
					if( af_elaIsIE ) {
						category_list[i].attachEvent('onclick',tf);
					} else {
						category_list[i].addEventListener('click', tf, false);
					}
				}
			}
		}
		return true;
	}
}

function af_elaLiveReqProcessReqChange() {
	if (af_elaLiveReq.readyState != 4) {
		var loadingElement = document.getElementById(af_elaResultID+"-loading");
		if ( loadingElement != null) loadingElement.innerHTML = af_elaLoadingContent;
		
	} else if (af_elaLiveReq.readyState == 4) {
        var af_elaText = af_elaLiveReq.responseText;
		af_elaResultID = af_aleRemoveSpaces(af_elaText.substring(0, af_elaText.indexOf('|')));
		af_elaText = af_elaText.substring(af_elaText.indexOf('|') + 1, af_elaText.length);
		
		var resultElement = document.getElementById(af_elaResultID);
		if( resultElement == null ) return;	
		
		resultElement.innerHTML = af_elaText; 
		
		af_elaGenerateMenu();
		
		af_elaGenerateYear();
		
		af_elaGenerateMonth();
		
		af_elaGenerateCategory();
		
		af_elaGenerateTag();
				
		// Fade Anything.
		if( typeof Fat != 'undefined' && /class="fade"/.test(af_elaText)) {
			Fat.fade_all();
		}
	}
}

function af_elaLiveReqInit() {
	if (navigator.userAgent.indexOf("Safari") > 0) {
		// branch to get to internet explorer
	} else if (navigator.product == "Gecko") {
		// branch to get to internet explorer
	} else {
		af_elaIsIE = true;
	}
	af_elaLiveReqDoReq('');
}

function af_elaSelectYear() {
	af_elaLiveReqDoReq('menu=' + af_elaMenu + '&year=' + af_elaYear);
}

function af_elaSelectMonth() {
        af_elaLiveReqDoReq('menu=' + af_elaMenu + '&year=' + af_elaYear + '&month=' + af_elaMonth);
}

function af_elaSelectTag() {
        af_elaLiveReqDoReq('menu=' + af_elaMenu + '&tag=' + af_elaTag);
}

function af_elaSelectCategory() {
        af_elaLiveReqDoReq('menu=' + af_elaMenu + '&category=' + af_elaCategory);
}

function af_elaSelectMenu() {
	af_elaLiveReqDoReq('menu=' + af_elaMenu);
}
/*
	Courtesy of Chris Boulton [http://www.surfionline.com]
*/
function af_elaEventElement(e) {
	if( af_elaIsIE ) {
		return e.srcElement;
	} else {
		return e.currentTarget;
	}
}

/* Removing leading or trailing space just in case... */
function af_aleRemoveSpaces(TextToTrim)
{
  var buffer = "";
  var TextToTrimLen = TextToTrim.length;
  var TextToTrimLenMinusOne = TextToTrim.length - 1;
  for (index = 0; index < TextToTrimLen; index++)
  {
    if (TextToTrim.charAt(index) != ' ')
    {
      buffer += TextToTrim.charAt(index);
    }
    else
    {
      if (buffer.length > 0)
      {
        if (TextToTrim.charAt(index+1) != ' ' && index != TextToTrimLenMinusOne)
        {
          buffer += TextToTrim.charAt(index);
        }
      }
    }
  }
  return buffer;
}


/*
addEvent function found at http://www.scottandrew.com/weblog/articles/cbs-events
*/
function af_elaAddEvent(obj, evType, fn) {
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

//af_elaAddLoadEvent(af_elaLiveReqInit);
af_elaAddEvent(window, 'load', af_elaLiveReqInit);
<?php } ?>