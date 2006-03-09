/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/openlaszlo
  License: MIT License.
  
  Contents:
  * A fix to <tt>arc()</tt>
  * An implementation of <tt>bezierCurveTo()</tt>
  * Patches to <tt>stroke()</tt> and <tt>fill()</tt> to accept (some)
    CSS color strings.
*/

// Convert a css color string to an integer.  This recognizes only
// '#rgb', '#rrggbb', and the color names that have been defined in
// the global namespace ('red', 'green', 'blue', etc.)
function cssColorToLong(value) {
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
    if (typeof eval(value) == 'number')
        return eval(value);
	Debug.warn('unknown color format: ' + value);
    return 0;
}

// Fix OpenLaszlo arc to comply with the WHATWG specification.  This
// patch is waiting in JIRA (LPP-1588).
LzDrawView.prototype.arc = function(x, y, r, startAngle, endAngle, clockwise) {
	x += r*Math.cos(startAngle);
	y += r*Math.sin(startAngle);
	startAngle *= 180/Math.PI;
	endAngle *= 180/Math.PI;
    var arc = clockwise == true ? startAngle - endAngle : endAngle - startAngle;
	this.moveTo(x, y);
	this._drawArc(x, y, r, arc, startAngle);
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

// Approximate a cubic bezier with quadratic segments, to within a
// midpoint error of LzDrawView.prototype.bezierCurveTo.error.
LzDrawView.prototype.bezierCurveTo = function(x1, y1, x2, y2, x3, y3) {
    var error = arguments.callee.error;
    
    // These would be useful generally, but put them inside the
    // function so they don't pollute the general namespace.
    function distance(p0, p1) {
        var dx = p1.x - p0.x;
        var dy = p1.y - p0.y;
        return Math.sqrt(dx*dx+dy*dy);
    }
    // returns null if they're collinear
    function intersection(p0, p1, p2, p3) {
        var u = (p3.x-p2.x)*(p0.y-p2.y) - (p3.y-p2.y)*(p0.x-p2.x);
        var d = (p3.y-p2.y)*(p1.x-p0.x) - (p3.x-p2.x)*(p1.y-p0.y);
        if (!d) return null;
        u /= d;
        return {x: p0.x + (p1.x-p0.x) * u,
                y: p0.y + (p1.y-p0.y) * u};
    }
    function midpoint(p0, p1) {
        return {x: (p0.x+p1.x)/2, y: (p0.y+p1.y)/2};
    }
    
    // Start from the cursor position, or (0, 0)
    var x0 = 0, y0 = 0;
    if (this.__path.length) {
        var instr = this.__path[this.__path.length - 1];
        x0 = instr[instr.length - 2];
        y0 = instr[instr.length - 1];
    }
    // The algorithm used is to recursively subdivide the cubic until
    // it's close enough to a quadratic, and then draw that.
    // The code below has the effect of
    //   function draw_cubic(cubic) {
    //     if (|midpoint(cubic)-midpoint(quadratic)| < error)
    //       draw_quadratic(qudratic);
    //     else
    //       map(draw_cubic, subdivide(cubic));
    //   }
    // where the recursion has been replaced by an explicit
    // work item queue.
    
    // To avoid recursion and undue temporary structure, the following
    // loop has a funny control flow.  Each iteration either pops
    // the next work item from queue, or creates two new work items
    // and pushes one to the queue while setting +points+ to the other one.
    // The loop effectively exits from the *middle*, when the next
    // work item is null.  (This continues to the loop test,
    // which then exits.)
    
    // each item is a list of control points, with a sentinel of null
    var work_items = [null]; 
    // the current work item
    var points = [{x: x0, y: y0}, {x: x1, y: y1}, {x: x2, y: y2}, {x: x3, y: y3}];
    while (points) {
        // Compute the triangle, since the fringe is the subdivision
        // if we need that and the peak is the midpoint which we need
        // in any case
        var m = [points, [], [], []];
        for (var i = 1; i < 4; i++) {
            for (var j = 0; j < 4 - i; j++) {
                var c0 = m[i-1][j];
                var c1 = m[i-1][j+1];
                m[i][j] = {x: (c0.x + c1.x)/2,
                           y: (c0.y + c1.y)/2};
            }
        }
        // Posit a quadratic.  For C1 continuity, control point has to
        // be at the intersection of the tangents.
        var q1 = intersection.apply(null, points);
        var q0 = points[0];
        var q2 = points[3];
        if (!q1) {
            // It's really a line.
            this.lineTo(q2.x, q2.y);
            points = work_items.pop();
            continue;
        }
        var qa = midpoint(q0, q1);
        var qb = midpoint(q1, q2);
        var qm = midpoint(qa, qb);
        // Is the midpoint of the quadratic close to the midpoint of
        // the cubic?  If so, use it as the approximation.
        if (distance(qm, m[3][0]) < error) {
            this.quadraticCurveTo(q1.x, q1.y, q2.x, q2.y);
            points = work_items.pop();
            continue;
        }
        // Otherwise subdivide the cubic.  The first division is the
        // next work item, and the second goes on the work queue.
        var left = new Array(4), right = new Array(4);
        for (i = 0; i < 4; i++) {
            left[i]  = m[i][0];
            right[i] = m[3-i][i];
        }
        points = left;
        work_items.push(right);
    }
};
LzDrawView.prototype.bezierCurveTo.error = 10;
