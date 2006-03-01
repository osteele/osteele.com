/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/javascript/sources
  License: MIT License.
  
  = Description
  This file adds readable strings for JavaScript values.  A readable
  string is intended for use by developers, to faciliate command-line
  and logger-based debugging.
  
  Loading this file has the following effects:

  1. It adds +toReadable+ methods to a number of the builtin classes.

  2. It defines a +Readable+ object.
     <code>Readable.toReadable(value)</code> is is equivalent to
     <code>v.toReadable()</code>, except that it can be applied to
     +null+ and +undefined+.

  3. It optionally replaces <tt>Array.toString</tt> and
     <tt>Object.toString</tt> by *.toReadable.  This makes command-line
     debugging using Rhino more palatable.

  4. It defines +info+, +error+, +warn+, and +debug+ functions that
     can be used to display information to the Rhino console, the
     browser alert dialog,
     fvlogger[http://www.alistapart.com/articles/jslogging], or a
     custom message function.
  
  == Readable API
  === <tt>Readable.represent(value, [options])</tt>
  Returns a string representation of +value+.
  
  === <tt>object.toReadable([options])</tt>
  Returns a string representation of +object+.
  
  Options is a hash of:
  * +limit+ -- max number of items of an Object or Array
  * +printFunctions+ -- determines how functions are represented:
  * +true+ -- functions are printed by toString()
  * +null+ (default) -- function are printed as 'function name'
  * +false+ -- reserved for future use
  
  == toString() replacement
  By default, this file replaces +object.toString()+ and
  +array.toString()+ with calls to +toReadable()+.  To disable this
  replacement, define +READABLE_TOSTRING+ to a non-false value before
  loading this file.
  
  In principle, these replacements could break code.  For example,
  code that depends on <code>['one','two','three'].toString()</code>
  evaluating to <tt>"one,two,three"</tt> for serialization or before
  presenting it to a user will no longer work.  In practice, this was
  what was most convenient for me -- it means that I can use the Rhino
  command line to print values readably, without having to wrap them
  in an extra function call.  So that's the default

  == Logging
  This file defines the logging functions +info+, +warn+, +debug+, and
  +error+.  These are designed to work in the browser or in Rhino, and
  to call fvlogger if it has been loaded.
  
  The functions are defined in one of the following ways:
  
  - If +info+, +warn+, +debug+, and +error+ are defined when this file
    is loaded, the new implementations call the old ones.  This is the
    fvlogger compatability mode, and the new functions are identical
    to the fvlogger functions except that (1) they are variadic (you
    can call <code>info(key, '=>', value)</code> instead of having to
    write <code>info(key + '=>' + value)</code>), and (2) they apply
    +toReadable+ to the arguments (which is why the variadicity is
    important).

  - Otherwise, if a function named 'log' exists, it is applied to a
    message constructed from the arguments to +info+, +warn+, etc.
    This allows you to customize the sink for logging calls.
    
   - Otherwise, if 'alert' exists, logging calls this.  This can be
     useful in the browser.  (You can supply a 'log' function that
     sets the status bar or appends text to a <div> instead.)  The
     advantages of +info()+ over +alert()+ are that it's variadic,
     readable, and compatabile with Rhino (see below).
     
   - Otherwise logging calls 'print'.  This would be the Wrong Thing
     in the browser, but the browser will take the 'alert' case.  This
     is for Rhino, which uses this to print to the console.  The
     advantages of +info()+ over +print()+ are that it's variadic,
     readable, and compatible with Browser JavaScript (see above).
*/

var Readable = {};

Readable.toReadable = function (value, options) {
    // null and undefined don't have properties
    // (this also catches false, NaN, 0, but they come out
    // the same)

    if (!value)
        return ''+value;
    try {value.toReadable}catch(e){return value.toString()}
    if (typeof value.toReadable == 'function') return value.toReadable(options);
    if (typeof value.toString == 'function') return value.toString();
    return '*';
};

Readable.charEncodingTable = (function() {
        var table = {};
        var map = ['\r', '\\r', '\n', '\\n', '\t', '\\t',
                   '\f', '\\f', '\b', '\\b'];
        for (var i = 0; i < map.length; i++) {
            var c = map[i++];
            table[c] = new RegExp(map[i++], 'g');
        }
        return table;
    })();

String.prototype.toReadable = function () {
    string = this.replace(/\\/g, '\\\\');
    for (var c in Readable.charEncodingTable)
        string = string.replace(c, Readable.charEncodingTable[c]);
    if (string.match(/\'/) && !string.match(/\"/))
        return '"' + string + '"';
    else
        return "'" + string.replace(/\'/g, '\\\'') + "'";
};

Object.prototype.toReadable = function(options) {
    options = options || {}
    if (options.depth == 0) return '{...}';
    if (options.depth) {
        var savedDepth = options.depth;
        options.depth--;
    }
    var segments = [];
    var delim = '{}';
    return this.toString();
    if (this.constructor) {
        if (this.constructor.toString().match(/internal function/))
            return this.toString();
        var match = this.constructor.toString().match(/function\s+(\w+)/);
        if (match && match[1] != 'Object') {
            segments.push(match[1]);
            delim = '()';
        }
    }
    segments.push(delim.charAt(0));
    var count = 0;
    for (var p in this) {
        var value = this[p];
        if (value == this.__proto__[p]) continue;
        if (count) segments.push(', ');
        if (options && options.limit && ++count > options.limit) {
            segments.push('...');
            break;
        }
        segments.push(p.toString());
        segments.push(': ');
        segments.push(Readable.toReadable(value, options));
    }
    if (savedDepth)
        options.depth = savedDepth;
    return segments.join('') + delim.charAt(1);
};

Array.prototype.toReadable = function(options) {
    options = options || []
    if (options.depth == 0) return '{...}';
    if (options.depth) {
        var savedDepth = options.depth;
        options.depth--;
    }
    var segments = [];
    for (var i = 0; i < this.length; i++) {
        if (options && options.limit && i >= options.limit) {
            segments.push('...');
            break;
        }
        segments.push(Readable.toReadable(this[i], options));
    }
    if (savedDepth)
        options.depth = savedDepth;
    return '[' + segments.join(', ') + ']';
};

Function.prototype.toReadable = function(options) {
    var string = this.toString();
    if (!(options||{}).printFunctions) {
        var match = string.match(/(function\s+\w+)/);
        if (match)
            string = match[1] + '() {...}';
    }
    return string;
};

Number.prototype.toReadable = function(){return Number.prototype.toString.apply(this)};
Boolean.prototype.toReadable = Boolean.prototype.toString;
RegExp.prototype.toReadable = RegExp.prototype.toString;

try {
    if (!READABLE_TOSTRING) throw "break";
    throw "beak";
} catch (e) {
    // call rather than replace, to pick up subclass
    // overrides
    //    Object.prototype.toString = function () {return this.toReadable()}
    // but don't worry about that here, yet...
    //    Array.prototype.toString = Array.prototype.toReadable;
    // Don't replace these.  Too much might rely on the spec'ed
    // implementation.
    //Function.prototype.toString = Function.prototoype.toReadable;
    //String.prototype.toString = String.prototoype.toReadable;
}

var ReadableLogger = {};

ReadableLogger.defaults = {limit: 10, depth: 2};

// function(level, message) --- log the message
ReadableLogger._log2 =
    (function() {
        try {
            // if all of these are functions, use them
            var loggers = {debug: debug, info: info, warn: warn, error: error};
            for (var i in loggers)
                if (typeof loggers[i] != 'function')
                    throw 'break';
            return function(level, message) {loggers[level](message)};
        } catch (e) {
            // else use whichever of alert (browser) and print (Rhino)
            // exists (but look for a function named 'log' at runtime)
            var fn;
            try {fn=alert}
            catch (e) {fn=print}            
            return function(level, message) {
                // check each time, so that the client can
                // redefine the log function at any time
                try {if (typeof logger == 'function') fn = log}
                catch (e) {}
                fn(level + ': ' + message);
            }
        }
        })();

// log with message level (info, warn, debug, or error)
ReadableLogger._logWithLevel = function(level, args) {
    var segments = [];
    for (var i = 0; i < args.length; i++) {
        var value = args[i];
        if (typeof value != 'string')
            value = Readable.toReadable(value, ReadableLogger.defaults);
        segments.push(value);
    }
    var msg = segments.join(', ');
    ReadableLogger._log2(level, msg);
};

// These are assignments rather than definitions, so that they
// are evaluated *after* the attempt to construct the 'loggers'
// hash, above
info = function() {ReadableLogger._logWithLevel('info', arguments)}
warn = function() {ReadableLogger._logWithLevel('warn', arguments)}
debug = function() {ReadableLogger._logWithLevel('debug', arguments)}
error = function() {ReadableLogger._logWithLevel('error', arguments)}
