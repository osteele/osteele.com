function OSGradient() {
    this.initialize.apply(this, arguments);
}
OSGradient.applyGradient = function(style, element) {
    var gradient = new OSGradient(style);
    gradient.applyGradient(element);
};
OSGradient.applyGradients = function() {
    try {
        DivStyle.initialize()
    } catch (e) {}
    var elements = document.getElementsByTagName('*');
    for (var i = 0, e; e = elements[i++];) {
        var style = e.divStyle;
        if (style && style.gradientStartColor)
            OSGradient.applyGradient(style, e);
    }
};
OSGradient.maxBands = [192, 192, 96];
OSGradient.setBodyStyle = function() {
    OSGradient.setBodyStyle = function() {}
    var style = document.body.style;
    style.position = 'relative';
    style.left = 0;
    style.top = 0;
    style.zIndex = 0;
};
OSGradient.prototype.initialize = function(style) {
    this.style = style;
};
OSGradient.prototype.applyGradient = function(e) {
    var width = e.offsetWidth,
        height = e.offsetHeight;
    var gradientElement = (this.createCanvasGradient(e, width, height) || this.createGradientElement(width, height));
    OSGradient.setBodyStyle();
    this.attachGradient(e, gradientElement);
};
OSGradient.prototype.createCanvasGradient = function(e, width, height) {
    var canvas = document.createElement('canvas');
    var ctx;
    try {
        ctx = canvas.getContext('2d')
    } catch (e) {
        return null
    }
    e.appendChild(canvas);
    if (navigator.appVersion.match(/Konqueror|Safari|KHTML/))
        canvas.style.position = 'fixed';
    canvas.setAttribute('width', width);
    canvas.setAttribute('height', height);
    var style = this.style;
    var c0 = style['gradient-start-color'];
    var c1 = style['gradient-end-color'];
    var r = style['border-radius'];
    ctx.beginPath();
    ctx.moveTo(0, r);
    ctx.arc(r, r, r, Math.PI, -Math.PI / 2, false);
    ctx.lineTo(width - r, 0);
    ctx.arc(width - r, r, r, -Math.PI / 2, 0, false);
    ctx.lineTo(width, height - r);
    ctx.arc(width - r, height - r, r, 0, Math.PI / 2, false);
    ctx.lineTo(r, height);
    ctx.arc(r, height - r, r, Math.PI / 2, Math.PI, false);
    ctx.clip();
    var g = ctx.fillStyle = ctx.createLinearGradient(0, 0, 0, height);
    g.addColorStop(0, OSUtils.color.long2css(c0));
    g.addColorStop(1, OSUtils.color.long2css(c1));
    ctx.rect(0, 0, width, height);
    ctx.fill();
    return canvas;
};
OSGradient.prototype.makeSpan = function(x, y, width, height, color, opacity) {
    var properties = {
        position: 'absolute',
        left: x + 'px',
        top: y + 'px',
        width: width + 'px',
        height: height + 'px',
        'font-size': 1,
        'line-height': 0,
        background: color
    };
    if (opacity != undefined)
        properties.opacity = opacity;
    var style = [];
    for (var p in properties)
        style.push(p + ':' + String(properties[p]));
    return '<div style="' + style.join(';') + '">&nbsp;</div>';
};
OSGradient.prototype.createGradientElement = function(width, height) {
    var style = this.style;
    var c0 = style['gradient-start-color'];
    var c1 = style['gradient-end-color'];
    var r = style['border-radius'];
    function xAt(y) {
        var dy = Math.max(r - y, y - (height - r));
        if (dy >= 0)
            return r - Math.sqrt(r * r - dy * dy);
        return 0;
    }
    ;
    var bands = 0;
    for (var shift = 24; (shift -= 8) >= 0;)
        bands = Math.max(bands, 1 + Math.min(Math.abs(c0 - c1) >> shift & 255, OSGradient.maxBands[2 - shift / 8]));
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
            if (x == lastx)
                continue;
            lastx = x;
            transitions.push(y);
            transitions.push(height - y);
        }
        transitions.sort(function(a, b) {
            return a - b
        });
    }
    OSUtils.Array.removeDuplicates(transitions);
    var spans = [];
    for (var i = 0; i < transitions.length - 1; i++) {
        var y = transitions[i];
        var h = transitions[i + 1] - y;
        var x = Math.ceil(xAt(y));
        var color = OSUtils.color.interpolate(c0, c1, y / height);
        spans.push(this.makeSpan(x, y, width - 2 * x, h, OSUtils.color.long2css(color)));
    }
    var g = document.createElement('div');
    g.innerHTML = spans.join('');
    if (true) {
        g.style.position = 'absolute';
        g.style.left = '0px';
        g.style.top = '0px';
        g.style.width = "100%";
        g.style.height = '100%';
        g.style.zIndex = -1;
    }
    return g;
};
OSGradient.prototype.attachGradient = function(parent, gradient) {
    gradient.style.position = 'absolute';
    gradient.style.left = '0px';
    gradient.style.top = '0px';
    if (gradient.width != parent.offsetWidth)
        gradient.width = parent.offsetWidth;
    if (gradient.height != parent.offsetHeight)
        gradient.height = parent.offsetHeight;
    gradient.style.zIndex = -1;
    if (!parent.style.position.match(/absolute|relative/i))
        parent.style.position = 'relative';
    if (gradient.parentNode != parent)
        parent.appendChild(gradient);
};
OSUtils = window.OSUtils || {};
if (!OSUtils.color) {
    OSUtils.color = {}
}
if (!OSUtils.Array) {
    OSUtils.Array = {}
}
OSUtils.color.long2css = function(n) {
    var a = "0123456789ABCDEF";
    var s = '#';
    for (var i = 24; (i -= 4) >= 0;)
        s += a.charAt((n >> i) & 0xf);
    return s;
};
OSUtils.color.interpolate = function(a, b, s) {
    var n = 0;
    for (var i = 24; (i -= 8) >= 0;) {
        var ca = (a >> i) & 0xff;
        var cb = (b >> i) & 0xff;
        var cc = Math.floor(ca * (1 - s) + cb * s);
        n |= cc << i;
    }
    return n;
};
OSUtils.Array.removeDuplicates = function(ar) {
    var i = 0,
        j = 0;
    while (j < ar.length) {
        var v = ar[i] = ar[j++];
        if (!i || ar[i - 1] != v)
            i++;
    }
    ar.length = i;
    return ar;
};
try {
    DivStyle.defineProperty('gradient-start-color', 'color');
    DivStyle.defineProperty('gradient-end-color', 'color', 0xffffff);
    DivStyle.defineProperty('border-radius', 'number', 0);
} catch (e) {}
if (window.addEventListener) {
    window.addEventListener('load', OSGradient.applyGradients, false);
} else if (window.attachEvent) {
    window.attachEvent('onload', OSGradient.applyGradients);
} else {
    window.onload = (function() {
        var nextfn = window.onload || function() {};
        return function() {
            OSGradient.applyGradients();
            nextfn();
        }
    })();
}
