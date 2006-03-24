/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  Download: http://osteele.com/sources/javascript/memoize.js
  Docs: http://osteele.com/sources/javascript/docs/memoize
  Example: http://osteele.com/sources/javascript/demos/memoization.html
  License: MIT License.
  Created: 2006-03-23
  
  == Features
  * Automatic per-instance caching.  +a.m()+ and +a.m()+ will
  use separate memo tables, to cover the case where the function
  result depends on the .
  * Caches results that are false-like (e.g. +0+, +null+, and <tt>''</tt>).
  * Fast path for nonary method, e.g. +bezier.getLength()+.
  * Correctly distinguishes among arguments that stringify the
  same; e.g. +f(null)+, +f('')+, and +f([])+; +f([])+ and +f([''])+;
  etc.
  * Allows overriding the cache key generator with a custom key
  generator
  
  == Usage
  === Memoizing a global function
  === Memozing a method
  === Resetting the memoization cache
  === Using a custom key generator
*/

/*
  Agenda:
  - benchmark nonnary
  - fix reset for instances
  - remove +object+ function from memoize
  - special case for nonnary with this
  - test 'memoize' function
*/

Function.prototype.memoize = function(object, keyfn) {
    keyfn = keyfn || arguments.callee.simpleSerializer;
    var self = this, nonaryfn, value, globalValues;
    var mfn = function() {
        if (!arguments.length) return nonaryfn();
        var key = new Array(arguments.length);
        for (var i = 0; i < arguments.length; i++)
            key[i] = keyfn(arguments[i]);
        key = key.join(',');
		var values = globalValues;
		if (this) values = this.$memoization || (this.$memoization = {});
		// testing both 'key in values' and 'values[key]' doesn't
		// cost extra in Firefox 1.5 and Safari 2.0.2.
        return key in values ? values[key] : values[key] = self.apply(this, arguments);
    }
    mfn.reset = function() {
        nonaryfn = function() {
            value = self.apply(object);
            return (nonaryfn = function(){return value})();
        };
        globalValues = {};
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

Object.prototype.memoize = function(fn, keyfn) {
    this[name] = this[name].memoize(this, keyfn);
};
