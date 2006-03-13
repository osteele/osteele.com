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

OSGradient.addGradient = function(e, properties) {
    e.style.position = 'relative';
	function getProperty(name, defaultValue) {
		var value = properties[name];
		return value == undefined ? defaultValue : value;
	}
    var c0 = getProperty('gradient-start-color', 0x000000);
    var c1 = getProperty('gradient-end-color', 0xffffff);
	var a0 = getProperty('gradient-start-opacity', 1);
	var a1 = getProperty('gradient-stop-opacity', 1);
    var r = getProperty('border-radius', 0);
    var width = e.offsetWidth, height = e.offsetHeight;
    var spans = [];
    var bars = 256;
    for (var i = 0; i < bars; i++) {
        var color = OSUtils.color.interpolate(c0, c1, i/bars);
		var w = width;
        var y = Math.floor(i*height/bars);
        var h = Math.floor((i+1)*height/bars) - y;
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

//try {OSUtils} catch(e) {OSUtils = {}}
//try {OSUtils.color} catch(e) {OSUtils.color = {}}

var OSUtils = {};
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

// Convert a css color string to an integer.  This recognizes only
// '#rgb', '#rrggbb', and the color names that have been defined in
// the global namespace ('red', 'green', 'blue', etc.)
OSUtils.color.cssColorToLong = function(value) {
	if (typeof value != 'string') return value;
    if (value.charAt(0) == '#') {
        var n = parseInt(value.slice(1), 16);
        switch (!isNaN(n) && value.length-1) {
        case 3:
            return ((n & 0xf00) << 8 | (n & 0xf0) << 4 | (n & 0xf)) * 17;
        case 6:
            return n;
        default:
            Debug.warn('invalid color: ' + value);
        }
    }
    //if (typeof eval(value) == 'number')
    //    return eval(value);
	//Debug.warn('unknown color format: ' + value);
    return 0;
};

OSGradient.findGradientDirectives = function() {
    var results = [];
    var divs = document.getElementsByTagName('div');
    for (var i = 0, div; div=divs[i++]; )
        if (div.className.match(/\bgradient\b/))
            results.push(div);
    return results;
};

OSGradient.processGradients = function() {
    var directives = OSGradient.findGradientDirectives();
    for (var i = 0, dir; dir = directives[i++]; ) {
        var e = dir.parentNode;
        var properties = OSGradient.parseProperties(dir.innerHTML);
        OSGradient.addGradient(e, properties);
    }
};

OSGradient.addGradients = function(e) {
    var s = document.body.style;
    s.position = 'relative';
    s.left = 0;
    s.top = 0;
    s.zIndex = 0;
    OSGradient.processGradients();
};

OSGradient.parseProperties = function(text) {
    var properties = {};
    var m = text.match(/[\w-]+\s*:\s*[^;]+/g);
    for (var i = 0; i < m.length; i++) {
        var im = m[i].match(/([\w-]+)\s*:\s*(.*)/);
        var name = im[1], value = im[2];
        if (name.match(/color/))
            value = OSUtils.color.cssColorToLong(value);
        if (name.match(/radius/))
            value = Number(value);
        properties[name] = value;
    }
    return properties;
}

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
