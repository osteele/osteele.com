/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/openlaszlo
  License: MIT License.
*/

// Convert a css color string to an integer.  This recognizes only
// recognizes '#rgb', '#rrggbb', and the color names that have been
// defined in the global namespace ('red', 'green', 'blue', etc.)
function cssColorToLong(value) {
	if (typeof value != 'string') return;
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
    if (typeof eval(value) == 'number')
        return eval(value);
	Debug.warn('unknown color format: ' + value);
    return 0;
}

// Fix OpenLaszlo arc to comply with the WHATWG specification.  This
// patch is waiting in JIRA (LPP-1588).
LzDrawView.prototype.arc = function(x, y, r, startAngle, endAngle, clockwise) {
	x -= r*Math.cos(startAngle);
	y -= r*Math.sin(startAngle);
	startAngle *= Math.PI/180;
	endAngle *= Math.PI/180;
    var arc = clockwise == true ? startAngle - endAngle : endAngle - startAngle;
	this.moveTo(x, y);
	this._drawArc(x, y, radius, arc, startAngle);
};

// Patch LzDrawView.fill() and LzDrawView.frame() to permit CSS color
// strings as the value of fillStyle and frameStyle.  Note that you'll
// also need to patch addGradientStop if you want to use CSS colors
// and gradients too.
LzDrawView.prototype._savedFill = LzDrawView.prototype.fill;
LzDrawView.prototype.fill = function() {
	var savedStyle = this.fillStyle;
	this.fillStyle = cssColorToLong(this.fillStyle);
	this._savedFill.apply(this,arguments);
	this.fillStyle = savedStyle;
};

LzDrawView.prototype._savedStroke = LzDrawView.prototype.stroke;
LzDrawView.prototype.stroke = function() {
	var savedStyle = this.strokeStyle;
	this.strokeStyle = cssColorToLong(this.strokeStyle);
	this._savedStroke.apply(this,arguments);
	this.strokeStyle = savedStyle;
};

// Approximate a cubic bezier with line segments.  Should be able to
// approximate it with (a smaller number of) quadratics.  Once I get
// that working, I'll contribute this to OpenLaszlo.
LzDrawView.prototype.bezierCurveTo = function(x1, y1, x2, y2, x3, y3) {
    var x0 = 0, y0 = 0;
    if (this.__path.length) {
        var instr = this.__path[this.__path.length - 1];
        x0 = instr[instr.length - 2];
        y0 = instr[instr.length - 1];
    }
    var points = [{x: x0, y: y0}, {x: x1, y: y1}, {x: x2, y: y2}, {x: x3, y: y3}];
    var queue = [points];
    function measureDistance(p0, p1) {
        var dx = p1.x - p0.x;
        var dy = p1.y - p0.y;
        return Math.sqrt(dx*dx+dy*dy);
    }
    while (queue.length) {
        points = queue.pop();
        var chordLength = measureDistance(points[0], points[3]);
        var polyLength = 0;
        for (var i = 0; i < 3; i++)
            polyLength += measureDistance(points[i], points[i+1]);
        if (polyLength - chordLength < 1 && chordLength < 10) {
            this.lineTo(points[1].x, points[1].y);
            this.lineTo(points[2].x, points[2].y);
            this.lineTo(points[3].x, points[3].y);
            continue;
        }
        // subdivide the bezier
        var m = [points, [], [], []];
        for (var i = 1; i <= 3; i++) {
            for (var j = 0; j <= 3 - i; j++) {
                var c0 = m[i-1][j];
                var c1 = m[i-1][j+1];
                m[i][j] = {x: (c0.x + c1.x)/2,
                           y: (c0.y + c1.y)/2};
            }
        }
        var left = new Array(4), right = new Array(4);
        for (i = 0; i <= 3; i++) {
            left[i]  = m[i][0];
            right[i] = m[3-i][i];
        }
        queue.push(right);
        queue.push(left);
    }
}
