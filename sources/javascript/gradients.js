/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  License: MIT License.
*/

/*
 * Agenda:
 * - anti-alias
 *
 * Basics:
 * - docs
 * - ie
 *
 * Corners:
 * - is the z-index stuff placing it too far back?
 *
 * Future:
 * - gradient-direction
 * - API to reset the gradient
 * - use 100% if there's no radius
 * - radial
 * - bloom
 * - diagonal
 */

/*
 * Gradient package
 */
var OSGradient = {};

OSGradient.createGradient = function(e, style) {
    var c0 = style['gradient-start-color'];
    var c1 = style['gradient-end-color'];
	var a0 = style['gradient-start-opacity'];
	var a1 = style['gradient-stop-opacity'];
    var r = style['border-radius'];
	
    var width = e.offsetWidth, height = e.offsetHeight;
	
	var barCount = 0;
	var f = OSUtils.color.long2css;
	for (var shift = 24; (shift -= 8) >= 0; )
		barCount = Math.max(barCount, 1+(Math.abs(c0 - c1) >> shift & 255));
	
	var transitions = [];
	for (var i = 0; i <= barCount; i++)
		transitions.push(Math.floor(i * height / barCount));
	
	var sides = [];
	if (r) {
		var tops = [];
		var bottoms = [];
		for (var y = 0; y <= r; y++) {
			tops.push(y);
			bottoms.unshift(height-y);
		}
		transitions = OSUtils.Array.removeDuplicates(OSUtils.Array.merge(transitions, tops));
		transitions = OSUtils.Array.removeDuplicates(OSUtils.Array.merge(transitions, bottoms));
	}
	
    var spans = [];
    for (var i = 0; i < transitions.length-1; i++) {
        var y = transitions[i];
        var h = transitions[i+1] - y;
		if (!h) continue;
		var w = width;
        var dx = 0;
        var dy = Math.max(r-y, y-(height-r));
        if (0 <= dy) dx = r - Math.sqrt(r*r-dy*dy);
        var color = OSUtils.color.interpolate(c0, c1, y/height);
        var properties = {position: 'relative',
                          left: dx,
						  top: 0,
                          width: w-2*dx,
                          height: h,
						  'font-size': 1,
						  'line-height': 0,
						  opacity: .5,
                          background: OSUtils.color.long2css(color)};
        var style = [];
        for (var p in properties)
            style.push(p + ':' + String(properties[p]));
		if (false && dx) {
			spans.push('<div style="'+style.join(';')+'">');
			spans.push('<span style="opacity:.5;width:10;height:1px">&nbsp;</span>');
			spans.push('&nbsp;');
			spans.push('</div>');
			continue;
		}
        spans.push('<div style="'+style.join(';')+'">&nbsp;</div>');
    }
    var g = document.createElement('div');
    g.innerHTML = spans.join('');
    g.style.position = 'absolute';
    g.style.left='0px';
    g.style.top='0px';
    g.style.width="100%";
    g.style.height='100%';
    g.style.zIndex = -1;
	return g;
};

OSGradient.attachGradient = function(e, gradient) {
    if (!e.style.position.match(/absolute|relative/))
		e.style.position = 'relative';	
    if (e.childNodes.length)
        e.insertBefore(gradient, e.childNodes[0]);
    else
        e.appendChild(gradient);
};

OSGradient.addGradient = function(e, style) {
	var gradient = OSGradient.createGradient(e, style);
	OSGradient.attachGradient(e, gradient);
};

OSGradient.setupBody = function() {
    var s = document.body.style;
    s.position = 'relative';
    s.left = 0;
    s.top = 0;
    s.zIndex = 0;
};

OSGradient.applyGradients = function() {
    var elements = document.getElementsByTagName('*');
    for (var i = 0, e; e = elements[i++]; ) {
        var style = e.divStyle;
        if (style && style.gradientStartColor)
            OSGradient.addGradient(e, style);
    }
};

OSGradient.addGradients = function(e) {
    OSGradient.setupBody();
    OSGradient.applyGradients();
};

/*
 * Utilities
 */
try {OSUtils} catch(e) {OSUtils = {}}
if (!OSUtils.color) {OSUtils.color = {}}
if (!OSUtils.Array) {OSUtils.Array = {}}

OSUtils.color.long2css = function(n) {
  var a = "0123456789ABCDEF";
  var s = '#';
  for (var i = 24; (i -= 4) >= 0; )
    s += a.charAt((n>>i) & 0xf);
  return s;
};

OSUtils.color.interpolate = function(a, b, s) {
  var n = 0;
  for (var i = 24; (i -= 8) >= 0; ) {
    var ca = (a >> i) & 0xff;
    var cb = (b >> i) & 0xff;
    var cc = Math.floor(ca*(1-s) + cb*s);
    n |= cc << i;
  }
  return n;
};

OSUtils.Array.merge = function(a, b) {
	var c = new Array(Math.max(a.length, b.length));
	var ia = 0, ib = 0, ic = 0;
	while (ia < a.length && ib < b.length)
		c[ic++] = a[ia] <= b[ib] ? a[ia++] : b[ib++];
	while (ia < a.length)
		c[ic++] = a[ia++];
	while (ib < b.length)
		c[ic++] = b[ib++];
	c.length = ic;
	return c;
};

OSUtils.Array.removeDuplicates = function(ar) {
	var i = 0, j = 0;
	while (j < ar.length) {
		var v = ar[i] = ar[j++];
		if (!i || ar[i-1] != v) i++;
	}
	ar.length = i;
	return ar;
};

/*
 * Initialization
 */

DivStyle.defineProperty('gradient-start-color', 'color');
DivStyle.defineProperty('gradient-end-color', 'color', 0xffffff);
DivStyle.defineProperty('gradient-start-opacity', 'number', 1);
DivStyle.defineProperty('gradient-stop-opacity', 'number', 1);
DivStyle.defineProperty('border-radius', 'number', 0);

if (window.addEventListener) {
    window.addEventListener('load', OSGradient.addGradients, false);
} else if (window.attachEvent) {
    window.attachEvent('onload', OSGradient.addGradients);
} else {
    window.onload = (function() {
        var nextfn = window.onload || function(){};
        return function() {
            OSGradient.addGradients();
            nextfn();
        }
    })();
}
