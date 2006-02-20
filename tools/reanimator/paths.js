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
    t *= this.getLength();
    // t in range [0, sum {segment_i.length}
    var i = 0;
    var segment = this.segments[i++];
    while (t > segment.getLength() && i < this.segments.length) {
        t -= segment.getLength();
        segment = this.segments[i++];
    }
    // t in range [0, segment.getLength()]
    return segment.atT(t / segment.getLength());
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
    var points = this.points;
    return Point.distance(points[0], points[1]);
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