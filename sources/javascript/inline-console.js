/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  License: MIT License.
  Homepage: http://osteele.com/sources/javascript
  Download: http://osteele.com/sources/javascript/inline-console.js
  Docs: http://osteele.com/sources/javascript/docs/inline-console
  Example: http://osteele.com/sources/javascript/demos/inline-console.html
  Created: 2006-03-03
  Modified: 2006-03-08
  
  == Usage
  Include this line in your HTML +head+:
    <script type="text/javascript" src="inline-console.js"></script>
  
  This will give you a console area at the bottom of your web page.
  The text input field at the top of the console can be used to
  evaluate JavaScript expressions and statements.  The results are
  appended to the console.
  
  This file also defines unary functions +info+, +warning+, +debug+,
  and +error+, that log their output to the console.
  
  To customize the location of the console, define
    <div id="inline-console"></div>
  in the including HTML file.
  
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

InlineConsole.printEval = function(input) {
    var value;
    try {
		with (InlineConsole.bindings)
			value = eval(input);
	}
    catch (e) {error(e.toString()); return}
    info(value);
}

InlineConsole.evalField = function(id) {
    InlineConsole.printEval(document.getElementById(id).value);
}

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
