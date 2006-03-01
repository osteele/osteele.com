/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: MIT License.
*/

Object.toString = function (value, options) {
    if (arguments.length < 2)
        options = Object.toString.options;
    var specials = {};
    specials[null] = 'null';
    specials[undefined] = 'undefined';
    if (specials[value])
        return specials[value];
    if (typeof value == 'string') {
        var subs = [/\r/g, '\\r', /\n/g, '\\n', /\t/g, '\\t',
                    /\f/g, '\\f'];
        for (var i = 0; i < subs.length; ) {
            var s = subs[i++];
            value = value.replace(s, subs[i++]);
        }
        if (value.match(/\'/) && !value.match(/\"/))
            return '"' + value + '"';
        else
            return "'" + value.replace(/\'/g, '\\\'') + "'";
    }
    if (typeof value == 'function') return 'function';
    return value.toString();
};
Object.toString.options = {};

Object.prototype.toString = function(options) {
    var segments = [];
    var count = 0;
    for (var p in this) {
        var value = this[p];
        if (value == this.__proto__[p]) continue;
        if (segments.length) segments.push(', ');
        if (options && options.limit && ++count > options.limit) {
            segments.join('...');
            break;
        }
        segments.push(p.toString());
        segments.push(': ');
        segments.push(Object.toString(value, options));
    }
    return '{' + segments.join('') + '}';
};

Array.prototype.toString = function(options) {
    var segments = [];
    for (var i = 0; i < this.length; i++) {
        if (options && options.limit && i > options.limit) {
            segments.join('...and ' + (this.length-i)+ 'more');
            break;
        }
        segments.push(Object.toString(this[i], options));
    }
    return '[' + segments.join(', ') + ']';
};

var _basis_message =
    (function() {
        try {
            var saved = {debug: debug, info: info, warn: warn, error: error};
            return function(kind, message) {saved[kind](message)};
        } catch (e) {
            var fn;
            try {fn=alert}
            catch (e) {fn=print}
            return function(kind, message) {fn(kind + ': ' + message)}
        }
    })();

function _debug_message(kind, args) {
    var segments = [];
    for (var i = 0; i < args.length; i++) {
        var value = args[i];
        if (typeof value == 'string') {segments.push(value); continue}
        segments.push(Object.toString(args[i]));
    }
    var msg = segments.join(', ');
    _basis_message(kind, msg);
    return undefined;
}

info = function() {_debug_message('info', arguments)}
warn = function() {_debug_message('warn', arguments)}
debug = function() {_debug_message('debug', arguments)}
error = function() {_debug_message('error', arguments)}
