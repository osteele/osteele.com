/* vim: set noai ts=4 sw=4: */
/*
 * flexo.js by Heath Harrelson, Copyright (C) 2007
 *
 * Version: 1.1.3
 * 
 * Expands and collapses menus.  Used by the Flexo Archives WordPress widget
 * (http://www.pointedstick.net/heath/flexo-archives-widget).
 *
 * This code is based on things found at the following pages:
 *   http://www.456bereastreet.com/archive/200705/accessible_expanding_and_collapsing_menu/
 *   http://icant.co.uk/sandbox/eventdelegation/
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

var flexoToggle = {
	widgetRoot : null,   // The widget's root element
	linksArray : null,   // The flexo-links
	listsArray : null,   // The flexo-lists

	init : function () {
		var yearLinks;   // List of all the expandable links
		var yearLists;   // List of month lists
		var numLinks;    // Number of elements in yearLinks
		var numLists;    // Number of elements in yearLists
		var widget;      // Outermost container of the widget

		// Dom support or bust!
		if (!document.getElementById)
			return;

		// If the widget's root can't be found, we can't
		// do anything.
		this.findWidgetElements();
		if (!this.widgetRoot) {
			return;
		} else {
			widget = this.widgetRoot;
		}

		// Get a list of all the expandable links
		yearLinks = this.linksArray;
		numLinks = yearLinks.length;

		// Get the list of all month lists
		yearLists = this.listsArray;
		numLists = yearLists.length;

		// Hide each list of months
		for (var i = 0; i < numLists; i++) {
			this.setDisplay(yearLists[i]);
		}

		// Add a hint to each expandable link
		for (i = 0; i < numLinks; i++) {
			yearLinks[i].title += ' (Click to Expand)';
		}

		// Hook event delegate to the root of the widget
		this.addEvent(widget, 'click', this.clickListener);
	},

	// Show or hide a list when the user clicks
	clickListener : function (e) {
		var targ;               // Element clicked

		// If event is undefined, this is IE
		if (!e)
			var e = window.event;

		// Get the element clicked
		if (e.target) {
			// W3C DOM events
			targ = e.target;
		} else if (e.srcElement) {
			// IE
			targ = e.srcElement;
		}

		// Back up to the link if this is a text node.
		// Hack for bug in Safari
		if (targ.nodeType == 3)
			targ = targ.parentNode;

		// Try to toggle this link
		if (flexoToggle.setStateForListWithLink(targ)) {
			// Don't follow link clicked
			flexoToggle.preventDefault(e);
			return false;
		}

		// Toggle failed, allow default action
		return true;
	},

	// Show or hide the list associated with element aLink,
	// hiding all other lists.
	setStateForListWithLink : function (aLink) {
		var startOfMenu;    // Element where archive list starts
		var thisList;       // Month list for this link
		var allLists;       // Array of month lists
		var expanded;       // Is the list for this month expanded
		var len;            // The number of flexLists

		// Grandparent should be start of archive list
		startOfMenu = aLink.parentNode.parentNode;

		// Bail if aLink node is not a Flexo Link or
		// startOfMenu node is not an archive list
		if (!flexoToggle.isFlexoLink(aLink) || 
		    !flexoToggle.isListElement(startOfMenu))
			return false;

		// Get month list associated with link clicked
		thisList = flexoToggle.getElementByClassName(startOfMenu,
								'ul', 'flexo-list');

		// See whether we're contracting an open list
		if (thisList[0] && thisList[0].style.display == 'block')
			expanded = true;

		// Hide any open lists, so only one is open after return
		allLists = flexoToggle.listsArray;
        len = allLists.length;
		for (var i = 0; i < len; i++) {
			if (allLists[i].style.display == 'block' ||
			    allLists[i].style.display == '')
				flexoToggle.setDisplay(allLists[i]);
		}

		// Show the month list for link clicked if it was collapsed
		if (!expanded) {
			len = thisList.length;
			for (var i = 0; i < len; i++)
				flexoToggle.setDisplay(thisList[i]);
		}

		return true;
	},

	// Find the root element of the widget.
	findWidgetElements : function () {
		// Nice themes will supply the widget with an ID.
		var rootElement = document.getElementById('flexo-archives');

		// Find all the flexo-links
		var flexLinks = flexoToggle.getElementByClassName(document, 'a',
									'flexo-link');

		// Find all the flexo-lists
		var flexLists = flexoToggle.getElementByClassName(document, 'ul',
									'flexo-list');

		// This theme is not so nice. :(
		if (!rootElement) {
			// The widget's root should be the great-grandparent
			// of the first flexo-link
			rootElement = flexLinks[0].parentNode.parentNode.parentNode;
		}

		// Set class variables
		if (rootElement)
			flexoToggle.widgetRoot = rootElement;

		if (flexLinks)
			flexoToggle.linksArray = flexLinks;

		if (flexLists)
			flexoToggle.listsArray = flexLists;
	},

	// Determine if DOM element el is a list (ul, ol)
	isListElement : function (el) {
		if (el.nodeName == 'UL' || el.nodeName == 'OL' ||
			el.NodeName == 'DL')
			return true;

		return false;
	},

	// Determine if DOM element el is a Flexo Link
	isFlexoLink : function (el) {
		if (el.nodeName == 'A' && el.className == 'flexo-link')
			return true;

		return false;
	},

	// Show or hide DOM element el (and its children)
	setDisplay : function (el) {
		if (el.style.display == 'block' || el.style.display == '') {
			el.style.display = 'none';
		} else {
			el.style.display = 'block';
		}
	},

	// Prevent the default action of DOM event e
	preventDefault : function (e) {
		if (e.preventDefault) {
			e.preventDefault(); // W3C DOM style
		} else if (!e.preventDefault) {
			window.event.returnValue = false; // IE style
		}
	},

	// Attach an event listener to an object
	addEvent : function (obj, type, callback) {
		if (obj.addEventListener) {
			obj.addEventListener(type, callback, false);
		} else if (obj.attachEvent) {
			obj['e' + type + callback] = callback;
			obj[type + callback] = function () {obj['e'+type+callback](window.event);}
			obj.attachEvent('on' + type, obj[type+callback]);
		}
	},

	// Get a list of tags bearing a certain CSS class
	getElementByClassName: function (obj, tagName, className) {
		var allSuchElements = obj.getElementsByTagName(tagName);
		var classElements = new Array();
		var len = allSuchElements.length;

		// Find elements with the specified class
		for (var i = 0; i < len; i++) {
			if (allSuchElements[i].className == className)
				classElements.push(allSuchElements[i]);
		}

		return classElements;
	}
};
/* Attach our click listener when the page has loaded. */
flexoToggle.addEvent(window, 'load', function () { flexoToggle.init(); });
