/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: MIT License.
*/

function Path() {
    this.segments = [];
}

Path.prototype.getLength = function () {
    var length = 0;
    for (var i = 0; i < this.segments.length; i++)
        length += this.segments[i].getLength();
    this.getLength = function() {return length;}
    return length;
};

Path.prototype.atT = function (t) {
    var s = t * this.getLength();
    // s is in range [0, sum i {segment_i.length}]
    var i = 0;
    var segment = this.segments[i++];
    while (s > segment.getLength() && i < this.segments.length) {
        s -= segment.getLength();
        segment = this.segments[i++];
    }
    // s in range [0, segment.getLength()]
    return segment.atT(s / segment.getLength());
};

Path.prototype.addLine = function (p0, p1) {
    this.segments.push(new Path.Line([p0, p1]));
};

Path.prototype.addCubic = function (points) {
    this.segments.push(new Path.Cubic(points));
};

Path.Line = function (points) {
    this.type = 'line';
    this.points = points;
};

Path.Cubic = function(points) {
    this.type = 'cubic';
    this.points = points;
    this.bezier = new Bezier(points);
};

Path.Line.prototype.getLength = function () {
    return distance.apply(null, this.points);
};

Path.Cubic.prototype.getLength = function () {
    return this.bezier.getLength();
};

Path.Line.prototype.atT = function (t) {
    var p0 = this.points[0], p1 = this.points[1];
    return {x: p0.x + (p1.x-p0.x)*t,
            y: p0.y + (p1.y-p0.y)*t};
};

Path.Cubic.prototype.atT = function (t) {
    return this.bezier.atT(t);
};