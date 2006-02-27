/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: Artistic License.
*/

function Path(segments) {
    this.segments = segments || [];
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
    // s is in the range [0, sum i {segment_i.length}]
    var i = 0;
    var segment = this.segments[i++];
    while (s > segment.getLength() && i < this.segments.length) {
        s -= segment.getLength();
        segment = this.segments[i++];
    }
    // s is in the range [0, segment.getLength()]
    return segment.atT(s / segment.getLength());
};

Path.prototype.addLine = function (p0, p1) {
    this.segments.push(new Path.Line([p0, p1]));
};

Path.prototype.addBezier = function (pointsOrBezier) {
    this.segments.push(new Path.Bezier(pointsOrBezier));
};

Path.prototype.draw = function (ctx) {
	for (var i = 0; i < this.segments.length; i++)
		this.segments[i].draw(ctx);
};

// could use an order 2 bezier, but it can be convenient for the
// caller to know that it's a line
Path.Line = function (points) {
    this.type = 'line';
    this.points = points;
};

Path.Bezier = function(pointsOrBezier) {
    this.type = 'bezier';
	var bezier = pointsOrBezier;
	if (bezier instanceof Array)
		bezier = new Bezier(pointsOrBezier);
    this.bezier = bezier;
};

Path.Line.prototype.getLength = function () {
    return distance.apply(null, this.points);
};

Path.Bezier.prototype.getLength = function () {
    return this.bezier.getLength();
};

Path.Line.prototype.atT = function (t) {
    var p0 = this.points[0], p1 = this.points[1];
    return {x: p0.x + (p1.x-p0.x)*t,
            y: p0.y + (p1.y-p0.y)*t};
};

Path.Bezier.prototype.atT = function (t) {
    return this.bezier.atT(t);
};

Path.Line.prototype.draw = function (ctx) {
	var points = this.points;
	ctx.moveTo(points[0].x, points[0].y);
	ctx.lineTo(points[1].x, points[1].y);
};

Path.Bezier.prototype.draw = function (ctx) {
	this.bezier.draw(ctx);
};