/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  License: MIT License.
  Homepage: http://osteele.com/sources/javascript/
  Docs: http://osteele.com/sources/javascript/docs/readable

  = Description
  This file adds readable strings for JavaScript values, and a simple
  set of logging commands that use them.
  
  A readable string is intended for use by developers, to faciliate
  command-line and logger-based debugging.  Readable strings
  correspond to the literal representation of a value, except that:
  
  * Collections (arrays and objects) may be optionally be limited in
    length and recursion depth.
  * Functions are abbreviated.  This makes collections that contain
    them more readable.
  * Some inconsistencies noted in the Notes section below.
  
  As an example, <code>[1, '', null ,[3 ,'4']].toString()</code> evaluates
  to <tt>1,,,3,4</tt>.  This is less than helpful for command-line
  debugging or logging.  With the inclusion of this file, the string
  representation of this object is the same as its source representation,
  and similarly for <code>{a: 1, b: 2}</code> (which otherwise
  displays as <tt>[object Object]</tt>).
  
  Loading <tt>readable.js</tt> file has the following effects:
  
  2. It defines a +Readable+ object.
     <code>Readable.toReadable(value)</code> is equivalent to
     <code>v.toReadable()</code>, except that it can be applied to
     +null+ and +undefined+.

  3. It adds +toReadable+ methods to a several of the builtin
     classes.
     
  3. It optionally replaces <tt>Array.prototype.toString</tt> and
     <tt>Object.prototype.toString</tt> by ...<tt>.toReadable</tt>.
     This makes command-line debugging using Rhino more palatable,
     at the expense of polluting instances of +Object+ and +Array+
     with an extra property that <code>for(...in...)</code> will
     iterate over.
  
  4. It defines +info+, +error+, +warn+, and +debug+ functions that
     can be used to display information to the Rhino console, the
     browser alert dialog,
     fvlogger[http://www.alistapart.com/articles/jslogging], or a
     custom message function.
  
  Read more or leave a comment
  here[http://osteele.com/archives/2006/03/readable-javascript-values].
  
  == Readable API
  === <tt>Readable.represent(value, [options])</tt>
  Returns a string representation of +value+.
  
  === <tt>object.toReadable([options])</tt>
  Returns a string representation of +object+.
  
  === options
  Options is a hash of:
  * +length+ -- how many items of a collection will print
  * +level+ -- how many levels of a nested object will print
  * +printFunctions+ -- determines how functions are represented
  where +printFunctions+ is one of: 
  * +true+ -- functions are printed by toString()
  * +null+ (default) -- function are printed as 'function name'
  * +false+ -- reserved for future use
  
  == toString() replacement
  By default, this file replaces <tt>object.toString()</tt>. and
  <tt>array.toString()</tt> with calls to <tt>toReadable()</tt>.  To
  disable this replacement, define +READABLE_TOSTRING+ to a non-false
  value before loading this file.
  
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
  to call +fvlogger+ if it has been loaded.  (For this to work,
  <tt>readable.js</tt> has to load *after* +fvlogger+.)
  
  The functions are defined in one of the following ways:
  
  - If +info+, +warn+, +debug+, and +error+ are defined when this file
    is loaded, the new implementations call the old ones.  This is the
    fvlogger compatability mode, and the new functions are identical
    to the fvlogger functions except that (1) they are variadic (you
    can call <code>info(key, '=>', value)</code> instead of having to
    write <code>info(key + '=>' + value)</code>), and (2) they apply
    +toReadable+ to the arguments (which is why the variadicity is
    important).

  - Otherwise, if 'alert' exists, logging calls this.  This can be
    useful in the browser.  (You can replace a
    <tt>ReadableLogger.log</tt> with a function sets the status bar or
    appends text to a <div> instead.)  
  
  - Otherwise logging calls 'print'.  This would be the Wrong Thing
    in the browser, but the browser will take the 'alert' case.  This
    is for Rhino, which defines +print+ this to print to the console.
  
  The advantages of calling +info+ (and the other logging functions)
  instead of (browser) +alert+ or (Rhino) +print+ are:
  * +info+ is variadic
  * +info+ produces readable representations
  * +info+ is compatible between browses and Rhino.  This means you
    can use Rhino for development of logic and other non-UI code, and
    test the code, with logging calls, in both Rhino and the browser.
  
  === Customizing
  Replace <tt>ReadableLogger.log(level, message)</tt> or
  <tt>ReadableLog.display(message)</tt> to customize this behavior.
  
  Logging uses ReadableLogger.defaults to limit the maximum length
  and recursion level.

  == Notes and Bugs
  There's no check for recursive objects.  Setting the +level+ option
  will at least keep the system from recursing infinitely.  (It's set
  by default.)

  Not all characters in strings are quoted, and JavaScript keywords
  that are used as Object property names aren't quoted either.  This
  is simply laziness.
  
  The logging functions intentionally use +toString+ instead of
  +toReadable+ for the arguments themselves.  That is, +a+ but not +b+
  is quoted in <code>info([a], b)</code>.  This is *usually* what you
  want, for uses such as <code>info(key, '=>', value)</code>.  When
  it's not, you can explicitly apply +toReadable+ to the value, e.g.
  <code>info(value.toReadable())</code> or, when it might be undefined
  or null, <code>info(Readable.toReadable(value))</code>.
*/

// Se we don't blow away Readable.objectToString if the file is loaded
// twice
try {if (!Readable) throw "undefined"} catch (e) {
    var Readable = {};
}

Readable.defaults = {limit: 50, level: 5, omitInstanceFunctions: true};

Readable.toReadable = function (value, options) {
    // it's an error to read a property of null or undefined
    if (value == null || value == undefined)
        return ''+value;
    
    if (value.constructor && typeof value.constructor.toReadable == 'function')
        return value.constructor.toReadable.apply(value, [options]);

    return Object.toReadable.apply(value, [options]);

    // Safari: some objects don't like to have their properties probed
    // (e.g. properties of document)
        try {typeof value.toReadable == 'function'} catch(e) {return 'y'}
    return 'x';

    if (typeof value.toReadable == 'function') return value.toReadable(options);
    if (typeof value.toString == 'function') return value.toString();
    // Safari: some values don't have properties (e.g. the alert function)
    return '<value>';
};

Readable.charEncodingTable = ['\r', '\\r', '\n', '\\n', '\t', '\\t',
                              '\f', '\\f', '\b', '\\b'];

String.toReadable = function (options) {
    if (options == undefined) options = Readable.defaults;
    var string = this;
    if (options.limit && string.length > options.limit)
        string = string.slice(0, options.limit) + '...';
    string = string.replace(/\\/g, '\\\\');
    for (var c in Readable.charEncodingTable)
        string = string.replace(c, Readable.charEncodingTable[c], 'g');
    if (string.match(/\'/) && !string.match(/\"/))
        return '"' + string + '"';
    else
        return "'" + string.replace(/\'/g, '\\\'') + "'";
};

// save this so we still have access to it after it's replaced, below
Readable.objectToString = Object.toString;

Object.toReadable = function(options) {
    if (this.constructor == Number || this.constructor == Boolean ||
        this.constructor == RegExp || this.constructor == Error ||
        this.constructor == String)
        return this.__proto__.toString.apply(this);
    if (options == undefined) options = Readable.defaults;
    var level = options.level;
    if (level == 0) return '{...}';
    if (level) options.level--;
    var omitFunctions = options.omitFunctions;
    var segments = [];
    var cname = null;
    var delim = '{}';
    if (this.constructor && this.constructor != Object) {
        var cstring = this.constructor.toString();
        var m = cstring.match(/function\s+(\w+)/);
        if (!m) m = cstring.match(/^\[object\s+(\w+)\]$/);
        if (!m) m = cstring.match(/^\[(\w+)\]$/);
        cname = m[1];
    }
    if (cname) {
        segments.push(cname);
        delim = '()';
        omitFunctions = options.omitInstanceFunctions;
    }
    segments.push(delim.charAt(0));
    var count = 0;
    for (var p in this) {
        var value;
        // accessing properties of document in Firefox produces an error
        try {value = this[p]} catch(e) {continue}
        try {if (value == this.__proto__[p]) continue} catch(e) {continue}
        if (typeof value == 'function' && omitFunctions) continue;
        if (count++) segments.push(', ');
        if (options && options.length && count > options.length) {
            segments.push('...');
            break;
        }
        segments.push(p.toString());
        segments.push(': ');
        segments.push(Readable.toReadable(value, options));
    }
    if (level) options.level = level;
    return segments.join('') + delim.charAt(1);
};

Array.toReadable = function(options) {
    if (options == undefined) options = Readable.defaults;
    if (options.level == 0) return '{...}';
    if (options.level) {
        var savedLevel = options.level;
        options.level--;
    }
    var segments = [];
    for (var i = 0; i < this.length; i++) {
        if (options && options.length && i >= options.length) {
            segments.push('...');
            break;
        }
        segments.push(Readable.toReadable(this[i], options));
    }
    if (savedLevel)
        options.level = savedLevel;
    return '[' + segments.join(', ') + ']';
};

Function.toReadable = function(options) {
    if (options == undefined) options = Readable.defaults;
    var string = this.toString();
    if (!options.printFunctions) {
        var match = string.match(/(function\s+\w*)/);
        if (match)
            string = match[1] + '() {...}';
    }
    return string;
};

try {
    if (!READABLE_TOSTRING) throw "break";
    //    throw "break";
} catch (e) {
    READABLE_TOSTRING = false; // in case the file is loaded twice
    // call rather than replace, to pick up subclass overrides
    Object.prototype.toString = function () {return Object.toReadable.apply(this)}
    // but don't worry about that here, yet...
    Array.prototype.toString = Array.toReadable;
    
    // Don't replace these.  Too much might rely on the spec'ed
    // implementation, especially for string.
    //String.prototype.toString = String.prototoype.toReadable;
    //Function.prototype.toString = Function.prototoype.toReadable;
}

var ReadableLogger = {};

ReadableLogger.defaults = {length: 10, level: 1, omitInstanceFunctions: true};

// function(message)
ReadableLogger.display = (function () {
        try {return alert} catch (e) {}
        try {return print} catch (e) {}
        return function (){};
    })();

// function(level, message) --- log the message
ReadableLogger.log =
    (function() {
        try {
            // if all of these are functions, use them
            var loggers = {debug: debug, info: info, warn: warn, error: error};
            for (var i in loggers)
                if (typeof loggers[i] != 'function')
                    throw 'break';
            return function(level, message) {loggers[level](message)};
        } catch (e) {
            return function(level, message) {
                ReadableLogger.display(level + ': ' + message);
            }
        }
    })();

// log with message level (info, warn, debug, or error), and an array of values
ReadableLogger.logValues = function(level, args) {
    var segments = [];
    for (var i = 0; i < args.length; i++) {
        var value = args[i];
        if (typeof value != 'string')
            value = Readable.toReadable(value, ReadableLogger.defaults);
        segments.push(value);
    }
    var msg = segments.join(', ');
    ReadableLogger.log(level, msg);
};

// These are assignments rather than definitions, so that they
// are evaluated *after* the attempt to construct the 'loggers'
// hash, above
info = function() {ReadableLogger.logValues('info', arguments)}
warn = function() {ReadableLogger.logValues('warn', arguments)}
debug = function() {ReadableLogger.logValues('debug', arguments)}
error = function() {ReadableLogger.logValues('error', arguments)}