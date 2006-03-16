/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  License: MIT License.
*/

/*
 * Basics:
 * - js search for javascript gradient
 * - auto-process
 *
 * Features:
 * - gradient-direction
 * - set the parent to position: relative
 * - API to reset the gradient
 * - choose the number of spans adaptively to the delta and radius
 * - vertical gradients
 * - use 100% if there's no radius
 *
 * Corners:
 * - is the z-index stuff placing it too far back?
 * - test in ie, opera
 *
 * Future:
 * - anti-aliasing
 * - css color names
 * - radial
 * - bloom
 * - diagonal
 */

var OSGradient = {};

OSGradient.addGradient = function(e, style) {
    e.style.position = 'relative';
	function getProperty(name, defaultValue) {
		var value = properties[name];
		return value == undefined ? defaultValue : value;
	}
    var c0 = style['gradient-start-color'];
    var c1 = style['gradient-end-color'];
	var a0 = style['gradient-start-opacity'];
	var a1 = style['gradient-stop-opacity'];
    var r = style['border-radius'];
    var width = e.offsetWidth, height = e.offsetHeight;
    var spans = [];
    var bars = Math.max(256, height);
    for (var i = 0; i < bars; i++) {
        var color = OSUtils.color.interpolate(c0, c1, i/bars);
		var w = width;
        var y = Math.floor(i*height/bars);
        var h = Math.floor((i+1)*height/bars) - y;
		if (!h) continue;
        var dx = 0;
        var dy = Math.max(r-y, y-(height-r));
        if (0 <= dy) dx = r - Math.sqrt(r*r-dy*dy);
        var properties = {position: 'relative',
                          left: dx,
						  top: 0,
                          width: w-2*dx,
                          height: h,
						  'font-size': 1,
						  'line-height': 0,
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
    g.style.position = 'absolute';
    g.style.left='0px';
    g.style.top='0px';
    g.style.width="100%";
    g.style.height='100%';
    g.style.zIndex = -1;
    if (e.childNodes.length)
        e.insertBefore(g, e.childNodes[0]);
    else
        e.appendChild(g);
    g.innerHTML = spans.join('');
};

//OSGradient

try {OSUtils} catch(e) {OSUtils = {}}
//try {OSUtils.color} catch(e) {OSUtils.color = {}}

//var OSUtils = {};
OSUtils.color = {};


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
