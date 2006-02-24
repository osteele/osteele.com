/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: MIT License.
*/

Array.prototype.toString = function() {
    var segments = [];
    for (var i = 0; i < this.length; i++)
        segments.push(Object.toString(this[i]));
    return '[' + segments.join(', ') + ']';
};

Object.toString = function (o) {
    var specials = {};
    specials[null] = 'null';
    specials[undefined] = 'undefined';
    if (specials[o])
        return specials[o];
    if (typeof o == 'function') return 'function';
    return o.toString();
};

Object.prototype.toString = function() {
    var segments = [];
    for (var p in this) {
        var o = this[p];
        if (o == this.__proto__[p]) continue;
        if (segments.length) segments.push(', ');
        segments.push(p.toString());
        segments.push(': ');
        segments.push(Object.toString(o));
    }
    return '{' + segments.join('') + '}';
};

function p() {
    var segments = [];
    for (var i = 0; i < arguments.length; i++)
        segments.push(Object.toString(arguments[i]));
    print(segments.join(', '));
    return undefined;
}

/*
b = new Bezier([{x: 56, y: 118}, {x: 68, y: 125}, {x: 83, y: 135}, {x: 96, y: 143}]);
b = new Bezier([{x: 0, y: 0}, {x: 0, y: 100}, {x: 100, y: 100}, {x: 100, y: 0}]);
//p(b.midpoint());

p(b.atT(.5));

path = new Path();
path.addCubic(b.points);
p(path.atT(0));
p(path.atT(.5));
p(path.atT(1));
*/

bezierCurveTo = function(points) {
    var error = arguments.callee.error;
    
    // These would be useful generally, but put them inside the
    // function so they don't pollute the general namespace.
    function distance(p0, p1) {
        var dx = p1.x - p0.x;
        var dy = p1.y - p0.y;
        return Math.sqrt(dx*dx+dy*dy);
    }
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
    var limit = 0;
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
        if (!q1) {
            p(q1);
            return;
        }
        var q0 = points[0];
        var q2 = points[3];
        var qa = midpoint(q0, q1);
        var qb = midpoint(q1, q2);
        var qm = midpoint(qa, qb);
        if (++limit > 10) return;
        // Is the midpoint of the quadratic close to the midpoint of
        // the cubic?  If so, use it as the approximation.
        p(qm, m[3][0]);
        if (distance(qm, m[3][0]) < error) {
            p(q1.x, q1.y, q2.x, q2.y);
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
bezierCurveTo.error = 1;


bezierCurveTo([{"y":22.0, "x":36.0}, {"y":22.0, "x":45.0}, {"y":22.0, "x":57.0}, {"y":22.0, "x":68.0}]);