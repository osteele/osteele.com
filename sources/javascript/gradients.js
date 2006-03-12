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
 *
 * Release:
 * - test in ie, opera
 * - copyright
 *
 * Future:
 * - is the z-index stuff placing it too far back?
 * - allow css color names
 * - radial (is it fast enough?)
 * - diagonal?
 */

var OSGradient = {};

OSGradient.addGradient = function(e, properties) {
    info(e.style.position);
    e.style.position = 'relative';
    var c0 = properties['gradient-start-color'] || 0xff0000;
    var c1 = properties['gradient-end-color'] || 0x000000;
    var r = properties['border-radius'] || 0;
    var width = e.offsetWidth, height = e.offsetHeight;
    var spans = [];
    var bars = 256;
    for (var i = 0; i < bars; i++) {
        var color = OSUtils.color.interpolate(c0, c1, i/bars);
        var h = Math.floor((i+1)*height/bars) - Math.floor(i*height/bars);
        var y = Math.floor(i*height/bars);
        var w = width, dx = 0;
        var dy = r-y;
        dy = Math.max(r-y, y-(height-r));
        if (0 <= dy) dx = r - Math.sqrt(r*r-dy*dy);
        var properties = {position: 'relative',
                          width: w-2*dx,
                          height: h,
                          left: dx,
                          background: OSUtils.color.long2css(color)};
        var style = [];
        for (var p in properties) {
            style.push(p + ':' + properties[p]);
        }
        spans.push('<div style="'+style.join(';')+'"></div>');
        //info(dy, dx, properties);
        //break;
    }
    var g = document.createElement('div');
    g.style.position = 'absolute';
    g.style.left='0px';
    g.style.top='0px';
    g.style.width="100%";
    g.style.height='100%';
    //g.style['z-index'] = '-1';
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

OSUtils.dom = {};
OSUtils.dom.doElements = function(fn) {
    function visit(e) {
        fn(e);
        for (var i = 0; i < e.childNodes.length; i++)
            visit(e.childNodes[i]);
    };
    visit(document);
};

OSGradient.process = function(e) {
    for (var i = 0; i < e.childNodes.length; i++) {
        var child = e.childNodes[i];
        if (child.className && child.className.match(/\bgradient\b/)) {
            var properties = OSGradient.parseProperties(child.innerHTML);
            OSGradient.addGradient(e, properties);
            return;
        }
    }
};

OSGradient.addGradients = function(e) {
    var s = document.body.style;
    s.position = 'relative';
    s.left = 0;
    s.top = 0;
    s.zIndex = 0;
    OSUtils.dom.doElements(OSGradient.process);
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
