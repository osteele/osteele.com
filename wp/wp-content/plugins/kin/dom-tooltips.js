/*******************************************************************************
Files:			dom-tooltips.js, dom-tooltips.css
Version:		1.3
Author:			John Ha
Author URI:		http://ink.bur.st
Description:	Add tooltips to any web page without modifying any code, as long
				as there are already title atributes used in the page.
				An implementation of tooltips using DOM. The script will search
				the document for all title attributes and automaticaly assign
				the title text to a toolip for that element.
				The element to attach a tooltip to can also be defined when
				calling this class.
Installation:	Include script in any page.
				Configurables: offsetX, offsetY, dom-tooltips.css file.
				The script will most likely not work if anything else is changed.
*******************************************************************************
This work is licensed under the Creative Commons Attribution License. To view a
copy of this license, visit http://creativecommons.org/licenses/by-nc-nd/2.5/
********************************************************************************/
tooltip = {
	name: 'dom-tooltips',
	offsetX: 15,
	offsetY: 20,
	enabled: false,
	tip: null,
	ie: document.all,
	ns6: document.getElementById && !document.all,
	opr: window.opera,

	init: function (tags) {
		if (!document.getElementById) return;

		typeof tags == 'undefined' ? tags = new Array('a') : 0; // default is anchor element (* for all)

		with (this) {
			if (!document.getElementById(name)) {
				tip = document.createElement ('div');
				tip.id = name;
			}
			document.getElementsByTagName('body')[0].appendChild(tip);
		}

		for (j = 0; j < tags.length; j++) {
			var all = document.getElementsByTagName(tags[j]);
			for (i = 0; i < all.length; i++) {
				// TextArea resizer and tinyMCE uses title = 'true' for ID purposes, so no need to process their title
				if (all[i].nodeName != 'TEXTAREA' && all[i].title != 'true' && all[i].title && typeof(all[i].title) == 'string') {
					tooltip.addtip(all[i], all[i].title);
					all[i].alt = all[i].title;
					all[i].title = '';
					addEvent(all[i], 'mouseout', function () { tooltip.hide(); }, false);
				}
			}
		}

		typeof document.mousemoveHandler != 'undefined' ? removeEvent(document, 'mouseover', document.mousemoveHandler, false) : 0;
		addEvent(document, 'mousemove', document.mousemoveHandler = function (evt) { tooltip.move (evt); }, false);
	},

	addtip: function (obj, tiptext) {
		tiptext = tooltip.wordwrap(tiptext);
		eval("addEvent(obj, 'mouseover', obj.mouseoverHandler = function () { tooltip.enable('" + tiptext + "'); }, false);");
	},

	changetip: function (obj, tiptext) {
		typeof obj.mouseoverHandler != 'undefined' ? removeEvent(obj, 'mouseover', obj.mouseoverHandler, false) : 0;
		tooltip.addtip(obj, tiptext);
	},

	ietruebody: function () {
		return document.compatMode && document.compatMode != 'BackCompat' ? document.documentElement : document.body;
	},

	wordwrap: function (s) {
		s = s.replace(/(.{1,50})(?:[\s\/]|$)/g, '$1<br />'); // Word wrap long lines
		s = s.replace(/[\r\n]/g, '');
		s = s.replace(/\'/g, '\\\'');
		return s;
	},

	move: function (evt) {
		with (this) {
			if (enabled) {
				var curX = ns6 ? evt.pageX : event.x + tooltip.ietruebody().scrollLeft;
				var curY = ns6 ? evt.pageY : event.y + tooltip.ietruebody().scrollTop;

				//tip.innerHTML = 'x:'+curX+', y:'+curY+', w:'+tip.offsetWidth+', h:'+tip.offsetHeight; // Uncomment for debugging

				//Find out how close the mouse is to the corner of the window
				var rightedge = ie && ! opr ? tooltip.ietruebody().clientWidth - event.clientX - offsetX : window.innerWidth - evt.clientX - offsetX - 18;
				var bottomedge = ie && ! opr ? tooltip.ietruebody().clientHeight - event.clientY - offsetY : window.innerHeight - evt.clientY - offsetY;
				var leftedge = offsetX < 0 ? offsetX * (-1) : -1000;

				//if the horizontal distance isn't enough to accomodate the width of the tooltip
				if (rightedge < tip.offsetWidth) {
					//move the horizontal position of the tooltip to the left by it's width
					tip.style.left = ie ? tooltip.ietruebody().scrollLeft + event.clientX - tip.offsetWidth - 10 + 'px' : window.pageXOffset + evt.clientX - tip.offsetWidth - 10 + 'px';
				} else if (curX < leftedge) {
					tip.style.left = '5px';
				} else {
					//horizontal position of the tooltip
					tip.style.left = curX + offsetX + 'px';
				}

				//same concept with the vertical position
				if (bottomedge < tip.offsetHeight) {
					tip.style.top = ie ? tooltip.ietruebody().scrollTop + event.clientY - tip.offsetHeight + 'px' : window.pageYOffset + evt.clientY - tip.offsetHeight + 'px';
				} else {
					tip.style.top = curY + offsetY + 'px';
				}
				tip.style.visibility = 'visible';
			}
		}
	},

	enable: function (text) {
		with (this) {
			if (!tip) return;
			if (ns6 || ie) {
				tip.innerHTML = text;
				enabled = true;
			}
		}
	},

	hide: function () {
		with (this) {
			if (!tip) return;
			if (ns6 || ie) {
				tip.style.visibility = 'hidden';
				enabled = false;
			}
		}
	}
};
if (typeof addEvent != 'function') {
	function addEvent(obj, evType, fn, useCapture) {
		if (obj.addEventListener) {
			obj.addEventListener(evType, fn, useCapture);
			return true;
		} else if (obj.attachEvent) {
			var r = obj.attachEvent("on"+evType, fn);
			return r;
		} else {
			alert("Handler could not be attached");
		}
	}
}
if (typeof removeEvent != 'function') {
	function removeEvent(obj, evType, fn, useCapture){
		if (obj.removeEventListener){
			obj.removeEventListener(evType, fn, useCapture);
			return true;
		} else if (obj.detachEvent){
			var r = obj.detachEvent("on"+evType, fn);
			return r;
		} else {
			alert("Handler could not be removed");
		}
	} 
}
addEvent(window, 'load', function () { tooltip.init(new Array('a', 'div', 'li', 'input', 'blockquote', 'img')); }, false);
