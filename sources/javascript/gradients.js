/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  License: MIT License.
  
  == Overview
  +gradients.js+ adds roundrect gradients to a page without the use of
  images.  This can be slower than using images (especially on a
  slow client), but it lets you do everything with text files if
  that's what floats your boat.
  
  == JavaScript API
  <tt>OSGradient.applyGradient(properties, element)</tt> applies
  a gradient to +element+.  +properties+ is a hash of
  properties:
  +gradient-start-color+: gradient start color (top); required
  +gradient-end-color+: gradient end color (bottom); default white
  +border-radius+: rounded corner radius; default zero
  
  === Usage
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
  If the +divstyle.js+ and +behaviour.js+ files are included,
  you can also specify a gradient using CSS syntax inside
  a +div+ tag with class +style+.  CSS selectors within the
  div style can select multiple tags, and multiple selectors
  can add properties to a single element.
  
  +divstyles.js+ is available from http://osteele.com/sources/javascript.
  +behaviour.js+ is available from http://bennolan.com/behaviour/.
  That's the British spelling.
  
  === Usage
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
  
  == Limitations
  CSS-like selectors are limited as described in divstyle.js.
*/

/*
 * Agenda:
 * - anti-alias
 * - canvas tag
 * - re-test on IE
 * - add version
 */

/*
 * Gradient package
 */
function OSGradient(style) {
	this.style = style;
}

// Number of bands necessary for a smooth gradient, by component.
// The max of the color range and height are pinned to this.
OSGradient.maxBands = [192, 192, 96];

OSGradient.applyGradient = function(style, element) {
	var gradient = new OSGradient(style);
	gradient.applyGradient(element);
};

// Create a gradient for each element that has a divStyle.
OSGradient.applyGradients = function() {
	try {DivStyle.initialize()} catch(e) {}
    var elements = document.getElementsByTagName('*');
    for (var i = 0, e; e = elements[i++]; ) {
        var style = e.divStyle;
        if (style && style.gradientStartColor)
            OSGradient.applyGradient(style, e);
    }
};

// Enable div.style.zIndex in IE.  This is called the first time that
// a gradient is attached to an element.
OSGradient.setupBody = function() {
	OSGradient.setupBody = function() {}
    var s = document.body.style;
    s.position = 'relative';
    s.left = 0;
    s.top = 0;
    s.zIndex = 0;
};

//
// Instance methods
//

OSGradient.prototype.applyGradient = function(e) {
    var width = e.offsetWidth, height = e.offsetHeight;
	var gradientElement = (this.createCanvasGradient(e, width, height) ||
						   this.createGradientElement(width, height));
	//this.addEdges(gradientElement, width, height);
    OSGradient.setupBody();
	this.attachGradient(e, gradientElement);
};

OSGradient.prototype.createCanvasGradient = function(e, width, height) {
	var canvas = document.createElement('canvas');
	var ctx;
	// Return null if canvas isn't supported.  The caller will
	// fall back on divs.
	try {ctx = canvas.getContext('2d')} catch (e) {return null}
	
	// Safari requires the following prior to rendering
	e.appendChild(canvas);
	canvas.style.position = 'absolute';
    canvas.setAttribute('width', width);
    canvas.setAttribute('height', height);
	
	var style = this.style;
    var c0 = style['gradient-start-color'];
    var c1 = style['gradient-end-color'];
    var r = style['border-radius'];
	
	ctx.beginPath();
	ctx.moveTo(0,r);
	//arcTo() produces an unimpl error in Firefox 1.5; use arc() instead:
	//ctx.arcTo(0,0,r,0,r);
	ctx.arc(r,r,r,Math.PI,-Math.PI/2,false);
	ctx.lineTo(width-r,0);
	//ctx.arcTo(width,0,width,r,r);
	ctx.arc(width-r,r,r,-Math.PI/2,0,false);
	ctx.lineTo(width,height-r);
	//ctx.arcTo(width,height,width-r,height,r);
	ctx.arc(width-r,height-r,r,0,Math.PI/2,false);
	ctx.lineTo(r,height);
	//ctx.arcTo(0,height,0,height-r,r);
	ctx.arc(r,height-r,r,Math.PI/2,Math.PI,false);
	ctx.clip();
	var g = ctx.fillStyle = ctx.createLinearGradient(0,0,0,height);
	g.addColorStop(0, OSUtils.color.long2css(c0));
	g.addColorStop(1, OSUtils.color.long2css(c1));
	ctx.rect(0,0,width,height);
	ctx.fill();
	return canvas;
};

// incomplete and unused
OSGradient.prototype.addEdges = function(parent) {
	var style = this.style;
    var c0 = style['gradient-start-color'];
    var c1 = style['gradient-end-color'];
	var height = this.height;
	var r = this.r;
	
	var spans = [];
	var xs = [];
	for (var y = 0; y <= r; y += 0.25) {
		var x = this.computeX(y);
		xs.push(x);
		if (xs.length < 5) continue;
		info(y, xs);
		xs.sort(function(a,b){return a-b});
		// Each span x0:x1 accumulates:
		// floor(x0):floor(x0)+1: (floor(x0)+1-x0)/4
		// floor(x0)+1:... : 1/4
		//
		// structure: [x0,alpha]
		// each x is a transition point at which 1/4 is added
		// if the next x is greater, then render at that alpha
		// from the cursor until the next x
		var x0 = xs[0];
		var alpha = 0;
		/*for (var i = 1; i < xs.length; i++) {
			alpha += .25;
			var x1 = xs[i];
			if (Math.floor(*/
		var xmin = Math.floor(xs[0]);
		var xmax = Math.ceil(xs[xs.length-1]);
		var color = OSUtils.color.interpolate(c0, c1, y/height);
		var alpha = 0.125;
		color = OSUtils.color.interpolate(0xffffff, color, alpha);
		spans.push(this.makeSpan(xmin, y-1, xmax-xmin, 1, color));
		ReadableLogger.defaults.stringLength=null;
		xs = [x];
	}
	
	var corner = document.createElement('div');
	corner.innerHTML = spans.join('');
	corner.style.position='absolute';
	corner.style.left='0px';
	corner.style.top='0px';
	
	parent.insertBefore(corner, parent.childNodes[0]);
};

OSGradient.prototype.makeSpan = function(x, y, width, height, color, opacity) {
	var properties = {position: 'absolute',
					  left: x+'px',
					  top: y+'px',
					  width: width+'px',
					  height: height+'px',
					  // for IE:
					  'font-size': 1,
					  'line-height': 0,
					  background: OSUtils.color.long2css(color)};
	if (opacity != undefined) properties.opacity = opacity;
	var style = [];
	for (var p in properties)
		style.push(p + ':' + String(properties[p]));
	return '<div style="'+style.join(';')+'">&nbsp;</div>';
};

OSGradient.prototype.createGradientElement = function(width, height) {
	var style = this.style;
    var c0 = style['gradient-start-color'];
    var c1 = style['gradient-end-color'];
    var r = style['border-radius'];
	
	function xAt(y) {
		var dy = Math.max(r-y, y-(height-r));
		if (dy >= 0)
			return r - Math.sqrt(r*r-dy*dy);
		return 0;
	};
	
	var bands = 0;
	for (var shift = 24; (shift -= 8) >= 0; )
		bands = Math.max(bands,
						 1+Math.min(Math.abs(c0 - c1) >> shift & 255,
									OSGradient.maxBands[2-shift/8]));
	bands = Math.max(bands, height);
	
	var transitions = [];
	for (var i = 0; i <= bands; i++)
		transitions.push(Math.floor(i * height / bands));
	
	if (r) {
		var tops = [];
		var bottoms = [];
		var lastx = null;
		for (var y = 0; y <= r; y++) {
			var x = Math.ceil(xAt(y));
			if (x == lastx) continue;
			lastx = x;
			transitions.push(y);
			transitions.push(height-y);
		}
		transitions.sort(function(a,b){return a-b});
	}
	OSUtils.Array.removeDuplicates(transitions);
	
    var spans = [];
    for (var i = 0; i < transitions.length-1; i++) {
        var y = transitions[i];
        var h = transitions[i+1] - y;
        var x = Math.ceil(xAt(y));
        var color = OSUtils.color.interpolate(c0, c1, y/height);
		spans.push(this.makeSpan(x, y, width-2*x, h, color));
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

OSGradient.prototype.attachGradient = function(parent, gradient) {
	gradient.style.position = 'absolute';
    gradient.style.left = '0px';
    gradient.style.top = '0px';
	// Assigning to the canvas erases its contents in Firefox,
	// even though it's the SAME DIMENSIONS.
	if (gradient.width != parent.offsetWidth)
		gradient.width = parent.offsetWidth;
	if (gradient.height != parent.offsetHeight)
		gradient.height = parent.offsetHeight;
    gradient.style.zIndex = -1;
	
    if (!parent.style.position.match(/absolute|relative/))
		parent.style.position = 'relative';	
	// The canvas parent has already been set, for Safari.
	if (!gradient.parentNode)
		if (parent.childNodes.length)
			parent.insertBefore(gradient, e.childNodes[0]);
		else
			parent.appendChild(gradient);
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
