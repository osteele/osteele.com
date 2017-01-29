/*
 * Author: Oliver Steele
 * Copyright: Copyright 2007 by Oliver Steele.  All rights reserved.
 * License: MIT License
 * Source: /web/20121016224130/http://osteele.com/javascripts/protodoc
 */

var OSUtils = window.OSUtils || {};

OSUtils.toString = function(value) {
    if (value instanceof Array) {
        var spans = map(OSUtils.toString, value);
        return '[' + spans.join(', ') + ']';
    }
    switch (typeof(value)) {
    case 'function': return 'function()';
    case 'string': return '"' + value + '"';
    default: return value ? value.toString() : ''+value;
    }
}

Function.prototype.reporting = function() {
    if (!window.console) return this;
    var fn = this;
    return function() {
        try {
            return fn.apply(this, arguments);
        } catch (e) {
            window.console && console.error(e);
            throw e;
        }
    }
}

Function.prototype.delayed = Function.prototype.delayed || function(ms) {
    window.setTimeout(this.reporting(), ms);
}

// used in debugging
String.prototype.debugInspect = function() {
    var m = {'\b':'b', '\f':'f', '\n':'n', '\t':'t'};
    return '"' + this.replace(/([\"\\\n\t\b\f])/g, function(s) {
        return '\\' + (m[s]||s)}) + '"';
}

OSUtils.merge = function(target, source) {
    var dummy = {};
    for (var p in source)
        if (!(p in dummy))
            target[p] = source[p];
    return target;
}

/*
 * A RopeWriter accumulates strings with deferred concatenatation.
 */

function RopeWriter() {
    this.blocks = [];
}

RopeWriter.prototype = {
    // Takes any number of string-representable values, or Arrays that
    // recursively contain such objects.
    append: function() {
        var blocks = this.blocks,
            len = arguments.length;
        for (var i = 0; i < len; i++) {
            var block = arguments[i];
            if (block instanceof Array)
                this.append.apply(this, block);
            else
                blocks.push(block);
        }
    },

    toString: function() {
        if (this.blocks.length == 1)
            return this.blocks[0];
        var value = this.blocks.join('');
        this.blocks = [value];
        return value;
    }
}
