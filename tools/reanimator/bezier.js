/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: MIT License.
*/

function Point(x, y) {
    this.x = x;
    this.y = y;
};

Point.distance = function (a, b) {
    var dx = a.x - b.x;
    var dy = a.y - b.y;
    return Math.sqrt(dx*dx + dy*dy);
};

// aliases the argument
function Bezier(points) {
    this.points = points;
};

Bezier.prototype._triangle = function () {
    var m = [this.points,[],[],[]];
    // fill the triangle
    for (var i = 1; i <= 3; i++) {
        for (var j = 0; j <= 3 - i; j++) {
            var c0 = m[i-1][j];
            var c1 = m[i-1][j+1];
            m[i][j] = {x: (c0.x + c1.x)/2,
                       y: (c0.y + c1.y)/2};
        }
    }
    this._triangle = function () {return m};
    return m;
}
    
Bezier.prototype.split = function () {
    var m = this._triangle();
    var left = new Array(4), right = new Array(4);
    for (var i = 0; i <= 3; i++) {
        left[i]  = m[i][0];
        right[i] = m[3-i][i];
    }
    return [new Bezier(left), new Bezier(right)];
};

Bezier.prototype.midpoint = function () {
    return this._triangle()[3][0];
};

Bezier.prototype.atT = function(t) {
    var p = this.points;
    var t2 = t*t;
    var t3 = t2*t;
    var u = 1-t;
    var u2 = u*u;
    var u3 = u2*u;
    return {x: p[0].x*u3 + 3*p[1].x*t*u2 + 3*p[2].x*t2*u + p[3].x*t3,
            y: p[0].y*u3 + 3*p[1].y*t*u2 + 3*p[2].y*t2*u + p[3].y*t3};
};

Bezier.prototype.getLength = function (error) {
    var sum = 0;
    var queue = [this];
    if (arguments.length < 1) error = 1;
    do {
        var b = queue.pop();
        var points = b.points;
        var chordlen = Point.distance(points[0], points[3]);
        var polylen = 0;
        for (var i = 0; i <= 2; i++)
            polylen += Point.distance(points[i], points[i+1]);
        if (polylen - chordlen <= error)
            sum += polylen;
        else
            queue = queue.concat(b.split());
    } while (queue.length);
    this.getLength = function () {return sum};
    return sum;
};
