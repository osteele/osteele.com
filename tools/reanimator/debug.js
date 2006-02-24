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
