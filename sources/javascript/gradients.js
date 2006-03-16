/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  License: MIT License.
  
  == Overview
  gradients.js adds roundrect gradients to a page without the use of
  images.
  
  == Usage
  === JavaScript API
    <html>
	  <head>
	    <script type="text/javascript" src="gradients.js"></script>
	  </head>
	  <body>
  	    <div id="e1">Some text</div>
	    <script type="text/javascript">
		  var e = document.getElementById('e1');
		  var style = {'gradient-start-color': 0x0000ff,
		               'border-radius': 25};
		  OSGradient.applyGradient(e, style);
	    </script>
	  </body>
	</html>
  
  === DivStyle API
    <html>
	  <head>
	    <script type="text/javascript" src="behaviour.js"></script>
	    <script type="text/javascript" src="divstyle.js"></script>
	    <script type="text/javascript" src="gradients.js"></script>
	  </head>
	  <body>
	    <div class="style" style="display:none">
		  #e1 {gradient-start-color: #0000ff; border-radius: 25}
		</div>
		
  	    <div id="e1">Some text</div>
	  </body>
	</html>
*/

/*
 * Agenda:
 * - anti-alias
 *
 * Basics:
 * - docs
 * - ie
 * - version
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
	
	function makeSpan(x, y, width, height, color, opacity) {
        var properties = {position: 'relative',
                          left: x,
						  top: 0,
                          width: width,
                          height: height,
						  'font-size': 1,
						  'line-height': 0,
                          background: OSUtils.color.long2css(color)};
		if (opacity != undefined) properties.opacity = opacity;
        var style = [];
        for (var p in properties)
            style.push(p + ':' + String(properties[p]));
        return '<div style="'+style.join(';')+'">&nbsp;</div>';
	}
	
	var sides = [];
	if (r) {
		var tops = [];
		var bottoms = [];
		var y0 = null;
		for (var x = 0; x <= r; x++) {
			var y = Math.floor(Math.sqrt(r*r - x*x));
			if (y0 = y) continue;
			y0 == y;
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
        var x = 0;
        var dy = Math.max(r-y, y-(height-r));
        if (dy >= 0) x = r - Math.floor(Math.sqrt(r*r-dy*dy));
        var color = OSUtils.color.interpolate(c0, c1, y/height);
		spans.push(makeSpan(x, 0, width-2*x, h, color));
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

OSGradient.applyGradient = function(e, style) {
	var gradient = OSGradient.createGradient(e, style);
    OSGradient.setupBody();
	OSGradient.attachGradient(e, gradient);
};

OSGradient.setupBody = function() {
	OSGradient.setupBody = function() {}
    var s = document.body.style;
    s.position = 'relative';
    s.left = 0;
    s.top = 0;
    s.zIndex = 0;
};

OSGradient.applyGradients = function() {
	try {DivStyle.initialize()} catch(e) {}
    var elements = document.getElementsByTagName('*');
    for (var i = 0, e; e = elements[i++]; ) {
        var style = e.divStyle;
        if (style && style.gradientStartColor)
            OSGradient.applyGradient(e, style);
    }
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


try {
	DivStyle.defineProperty('gradient-start-color', 'color');
	DivStyle.defineProperty('gradient-end-color', 'color', 0xffffff);
	DivStyle.defineProperty('gradient-start-opacity', 'number', 1);
	DivStyle.defineProperty('gradient-stop-opacity', 'number', 1);
	DivStyle.defineProperty('border-radius', 'number', 0);
} catch(e) {}

if (window.addEventListener) {
    window.addEventListener('load', OSGradient.applyGradients, false);
} else if (window.attachEvent) {
    window.attachEvent('onload', OSGradient.applyGradients);
} else {
    window.onload = (function() {
        var nextfn = window.onload || function(){};
        return function() {
            OSGradient.applyGradients();
            nextfn();
        }
    })();
}
