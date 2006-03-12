/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  License: MIT License.
  Homepage: http://osteele.com/sources/javascript
  Docs: http://osteele.com/sources/javascript/docs/inline-console
  Download: http://osteele.com/sources/javascript/inline-console.js
  Example: http://osteele.com/sources/javascript/demos/inline-console.html
  Created: 2006-03-03
  Modified: 2006-03-09
  
  == Usage
  Include this line in the +head+ of an HTML document:
    <script type="text/javascript" src="inline-console.js"></script>
  This will give you a console area at the bottom of your web page.
  (See http://osteele.com/sources/javascript/demos/inline-console.html
  for an example.)
  
  The text input field at the top of the console can be used to
  evaluate JavaScript expressions and statements.  The results are
  appended to the console.
  
  This file also defines unary functions +info+, +warning+, +debug+,
  and +error+, that log their output to the console.
  
  == Input Area
  Text that is entered into the input area is evaluated, and the
  result is displayed in the console.
  
  <tt>properties(object)</tt> displays all the properties of +object+.
  (If Readable is loaded, only the first 10 properties will be
  displayed.  To display all the properties, evaluate
    properties //limit=null
  instead.  See the Customization section for more about overriding
  the Readable defaults.)
  
  == Customization
  To customize the location of the console, define
    <div id="inline-console"></div>
  in the HTML file.
  
  If Readable is loaded, it will limit the length and recursion
  level of the displayed string.  You can change these limits
  globally by assigning to ReadableLogger.defaults, e.g.:
    ReadableLogger.defaults.limit=20
    document
  You can also change these limits for a single evaluation by
  appending a comment string to the end of the value, e.g.:
    document//limit=20
    document//limit=20,level=2
  See the {Readable documentation}[http://osteele.com/sources/javascript/docs/readable] for the complete list of options.
  
  == Related
  fvlogger[http://www.alistapart.com/articles/jslogging] provides
  finer-grained control over the display of log messages.  This file
  may be used in conjunction with fvlogger simply by including both
  files.  In this case, the fvlogger logging functions are used
  instead of the functions defined here, and if <div
  id="inline-console"> is not defined, it is appended to the end of
  the the #fvlogger div, rather than to the end of the HTML body.
  
  {readable.js}[http://osteele.com/sources/javascript/] provides a
  representation of JavaScript values (e.g. "<tt>{a: 1}</tt>" rather than
  "<tt>[object Object]</tt>") and variadic logging functions (e.g. <tt>log(key,
  '->', value)</tt> instead of <tt>log(key + '->' + value)</tt>).
  This file may be used in conjunction with readable.js by including
  readable.js *after* this file.
  
  {Simple logging for OpenLaszlo}[http://osteele.com/sources/openlaszlo/]
  defines logging functions that are compatible with those defined by this
  file.  This allows libraries that use these functions to be used
  in both OpenLaszlo programs and in DHTML.
*/

var InlineConsole = {};

InlineConsole.bindings = {};
InlineConsole.bindings.properties = function (object) {
	var ar = [];
	for (var i in object)
		ar.push(i);
	ar.sort();
	return ar;
};

InlineConsole.utils = {};

InlineConsole.utils.shallowCopy = function(object) {
	var copy = new Object;
	InlineConsole.utils.update(copy, object);
	return copy;
};

InlineConsole.utils.update = function(target, source) {
	for (var p in source)
		target[p] = source[p];
};

InlineConsole.parseOptions = function(input) {
	var options = {};
	var m = input.match(/\/\/\s*(.*)\s*$/);
	if (!m) return options;
	var lines = m[1].split(/\s*,\s*/);
	for (var i = 0; i < lines.length; i++) {
		var pair = lines[i].split('=', 2);
		if (!pair) continue;
		var key = pair[0], value = pair[1];
		options[key] = Number(value);
	}
	return options;
};

InlineConsole.getDefaults = function() {
	try {
		return ReadableLogger.defaults;
	} catch (e) {
		return {};
	}
};

InlineConsole.setDefaults = function(options) {
	try {
		ReadableLogger.defaults = options;
	} catch (e) {}
};

InlineConsole.readEvalPrint = function(input) {
    var value;
	try {
		with (InlineConsole.bindings)
			value = eval(input);
	}
	catch (e) {error(e.toString()); return}
	var options = InlineConsole.parseOptions(input);
	InlineConsole.print(value, options);
};

InlineConsole.print = function(value, options) {
    // RedableLogger might not exist.
	try {defaults = ReadableLogger.defaults} catch (e) {}
	try {
		if (defaults) {
			var newOptions = InlineConsole.utils.shallowCopy(defaults);
			InlineConsole.utils.update(newOptions, options);
			ReadableLogger.defaults = newOptions;
		}
		info(value);
	} finally {
		if (defaults) ReadableLogger.defaults = defaults;
	}
};

InlineConsole.evalField = function(id) {
    InlineConsole.readEvalPrint(document.getElementById(id).value);
};

InlineConsole.addConsole = function() {
    var e = document.getElementById('inline-console');
    var fv = document.getElementById('fvlogger');
    if (!e) {
        e = document.createElement('div');
        if (fv)
            fv.appendChild(e);
        else {
            document.body.appendChild(e);
        }
    }
    e.innerHTML = InlineConsole.CONSOLE_HTML;
    if (!fv) {
        if (!log_element) {
            document.createElement('div');
            document.body.appendChild(log_element);
        }
        e.appendChild(log_element);
    }
};

InlineConsole.CONSOLE_HTML = '<form id="debugger" action="#" method="get" onsubmit="InlineConsole.evalField(\'eval\'); return false"><div><input type="button" onclick="InlineConsole.evalField(\'eval\'); return false;" value="Eval"/><input type="text" size="80" id="eval" value="" onkeyup="/*event.preventDefault(); */return false;"/></div></form>';

InlineConsole.initializeLoggingFunctions = function() {
    try {
        var logging_functions = [info, warn, error, message];
        for (var i in logging_functions)
            if (typeof logging_functions[i] != 'function')
                throw "break";
    } catch (e) {
        log_element = document.createElement('div');
        function logger (msg) {
            var span = document.createElement('div');
            span.innerText = msg;
            log_element.appendChild(span);
        };
        try {if (typeof debug != 'function') throw 0} catch (e) {debug = logger}
        try {if (typeof error != 'function') throw 0} catch (e) {error = logger}
        try {if (typeof info != 'function') throw 0} catch (e) {info = logger}
        try {if (typeof warn != 'function') throw 0} catch (e) {warn = logger}
    }};

InlineConsole.initializeLoggingFunctions();

if (window.addEventListener) {
    window.addEventListener('load', InlineConsole.addConsole, false);
} else if (window.attachEvent) {
    window.attachEvent('onload', InlineConsole.addConsole);
} else {
    window.onload = (function() {
        var nextfn = window.onload || function(){};
        return function() {
            addConsole();
            nextfn();
        }
    })();
}
