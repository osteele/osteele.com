/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/openlaszlo
  License: MIT License.
*/

LzDrawView.prototype.arc = function(x, y, r, startAngle, endAngle, clockwise) {
	x -= r*Math.cos(startAngle);
	y -= r*Math.sin(startAngle);
	startAngle *= Math.PI/180;
	endAngle *= Math.PI/180;
    var arc = clockwise == true ? startAngle - endAngle : endAngle - startAngle;
	this.moveTo(x, y);
	this._drawArc(x, y, radius, arc, startAngle);
};

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

function cssColorToLong(value) {
	if (value && typeof value == 'string') {
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
	}
	return value;
}

LzDrawView.prototype.bezierCurveTo = function(x1, y1, x2, y2, x3, y3) {
	// recover the last point
	// TODO: if there isn't one
	var instr = this.__path[this.__path.length - 1];
	var x0 = instr[instr.length - 2];
	var y0 = instr[instr.length - 1];
	// compute the midpoint of the cubic
	var c01 = {x: (x0+x1)/2, y: (y0+y1)/2};
	var c12 = {x: (x1+x2)/2, y: (y1+y2)/2};
	var c23 = {x: (x2+x3)/2, y: (y2+y3)/2};
	var c012 = {x: (c01.x+c12.x)/2, y: (c01.y+c12.y)/2};
	var c123 = {x: (c12.x+c23.x)/2, y: (c12.y+c23.y)/2};
	var cm = {x: (c012.x+c123.x)/2, y: (c012.y+c123.y)/2};
	// compute the midpoint of the quadratic
	var q0 = {x: x0, y: y0};
	var q1 = c12;
	var q2 = {x: x3, y: y3};
	var q01 = {x: (q0.x+q1.x)/2, y: (q0.y+q1.y)/2};
	var q12 = {x: (q1.x+q2.x)/2, y: (q1.y+q2.y)/2};
	var qm = {x: (q01.x+q12.x)/2, y: (q01.y+q12.y)/2};
	// approximate with a quadratic if the midpoints are less than
	// sqrt(2) pixels away, else recurse
	var dx = qm.x-cm.x;
	var dy = qm.y-cm.y;
	if (dx*dx+dy*dy < 2)
        this.quadraticCurveTo(q1.x, q1.y, q2.x, q2.y);
	else {
        this.cubicBezierTo(c01.x, c01.y, c012.x, c012.y, qm.x, qm.y);
		// could iterate here to almost halve the number of fn calls
		// (although not the stack depth)
        this.cubicBezierTo(c123.x, c123.y, c23.x, c23.y, x3, y3);
	}
}

LzDrawView.prototype.bezierCurveTo = function(x1, y1, x2, y2, x3, y3) {
	// recover the last point
	// TODO: if there isn't one
	var instr = this.__path[this.__path.length - 1];
	var x0 = instr[instr.length - 2];
	var y0 = instr[instr.length - 1];
    var points = [{x: x0, y: y0}, c1 = {x: x1, y: y1}, c2 = {x: x2, y: y2}, c3 = {x: x3, y: y3}];
    var queue = [points];
    function distance(a,b) {
        var dx = a.x-b.x;
        var dy = a.y-b.y;
        return Math.sqrt(dx*dx+dy*dy);
    }
    var limit = 0;
    while (queue.length) {
        //if (++limit>20) return;
        points = queue.pop();
        var chordLength = distance(points[0], points[3]);
        var polyLength = 0;
        for (var i = 0; i < 3; i++)
            polyLength += distance(points[i], points[i+1]);
        if (polyLength - chordLength < 5 && chordLength < 10) {
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
        /*if (distance(m[1][1], m[3][0]) < 5) {
            this.quadraticCurveTo(m[1][1].x, m[1][1].y, points[3].x, points[3].y);
            continue;
        }*/
        var left = new Array(4), right = new Array(4);
        for (i = 0; i <= 3; i++) {
            left[i]  = m[i][0];
            right[i] = m[3-i][i];
        }
        queue.push(right);
        queue.push(left);
    }
}
