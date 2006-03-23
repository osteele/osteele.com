/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  Download: http://osteele.com/sources/javascript/memoize.js
  Docs: http://osteele.com/sources/javascript/docs/gradients
  Example: http://osteele.com/sources/javascript/demos/gradients.html
  License: MIT License.
  Created: 2006-03-23
  
  
*/
  
Function.prototype.memoize = function(object, keyfn) {
    if (arguments.length < 3) keyfn = arguments.callee.simpleSerializer;
    var self = this, nonaryfn, value, values;
    var mfn = function() {
        if (!arguments.length) return nonaryfn();
        var key = new Array(arguments.length);
        for (var i = 0; i < arguments.length; i++)
            key[i] = keyfn(arguments[i]);
        key = key.join(',');
        return key in values ? values[key] : values[key] = self.apply(object, arguments);
    }
    mfn.reset = function() {
        nonaryfn = function() {
            value = self.apply(object);
            return (nonaryfn = function(){return value})();
        };
        values = {};
    };
    mfn.reset();
    return mfn;
};

Function.prototype.memoize.simpleSerializer = function(value) {
    if (value instanceof Array) {
        var s = new Array(value.length);
        for (var i = 0; i < value.length; i++)
            s[i] = arguments.callee(value[i]);
        return '[' + s.join(',') + ']';
    }
    if (value instanceof Object) {
        var s = [];
        for (var p in value)
            s[s.length] = p + ':' + arguments.callee(value[i]);
        return '{' + s.join(',') + '}';
    }
    if (typeof value == 'string')
        return '"' + value.replace('\\', '\\\\').replace('"', '\\"') + '"';
    return String(value);
};

Object.prototype.memoize = function(name, keyfn) {
    this[name] = this[name].memoize(this, keyfn);
};
