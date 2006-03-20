/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  Docs: http://osteele.com/sources/javascript/docs/path
  Download: http://osteele.com/sources/javascript/path.js
  Example: http://osteele.com/sources/javascript/bezier-demo.php
  License: MIT License.
  
  Usage:
    var path = new Path();
    path.addBezier([{x:0,y:0}, {x:50,y:50}, {x:100,y:25}]);
    path.addLine([{x:100,y:25}, {x:150,y:50}]);
    path.draw(context);
    var midpoint = path.atT(0.5); // parametric, not length
    var length = 0.5 * path.measureLength();
  
  == Related
  Also see {<tt>path.js</tt>}[http://osteele.com/sources/javascript/docs/path].
*/

function Path(segments) {
    this.segments = segments || [];
}

Path.prototype.measureLength = function () {
    var length = 0;
    for (var i = 0; i < this.segments.length; i++)
        length += this.segments[i].measureLength();
    this.measureLength = function() {return length;}
    return length;
};

Path.prototype.atT = function (t) {
    var s = t * this.measureLength();
    // s is in the range [0, sum i {segment_i.length}]
    var i = 0;
    var segment = this.segments[i++];
    while (s > segment.measureLength() && i < this.segments.length) {
        s -= segment.measureLength();
        segment = this.segments[i++];
    }
    // s is in the range [0, segment.measureLength()]
    return segment.atT(s / segment.measureLength());
};

Path.prototype.draw = function (ctx) {
	for (var i = 0; i < this.segments.length; i++)
		this.segments[i].draw(ctx);
};

Path.prototype.addBezier = function (pointsOrBezier) {
    this.segments.push(new Path.Bezier(pointsOrBezier));
};

Path.Bezier = function(pointsOrBezier) {
    this.type = 'bezier';
	var bezier = pointsOrBezier;
	if (bezier instanceof Array)
		bezier = new Bezier(pointsOrBezier);
    this.bezier = bezier;
};

Path.Bezier.prototype.atT = function (t) {
    return this.bezier.atT(t);
};

Path.Bezier.prototype.measureLength = function () {
    return this.bezier.measureLength();
};

Path.Bezier.prototype.draw = function (ctx) {
	this.bezier.draw(ctx);
};

// A line could be represented as an order-2 bezier, but I wrote this
// before the Bezier library could support arbitrary orders, and I
// don't have the heart to take it out...
Path.prototype.addLine = function (p0, p1) {
    this.segments.push(new Path.Line([p0, p1]));
};

Path.Line = function (points) {
    this.type = 'line';
    this.points = points;
};

Path.Line.prototype.measureLength = function () {
    return distance.apply(null, this.points);
};

Path.Line.prototype.atT = function (t) {
    var p0 = this.points[0], p1 = this.points[1];
    return {x: p0.x + (p1.x-p0.x)*t,
            y: p0.y + (p1.y-p0.y)*t};
};

Path.Line.prototype.draw = function (ctx) {
	var points = this.points;
	ctx.moveTo(points[0].x, points[0].y);
	ctx.lineTo(points[1].x, points[1].y);
};
