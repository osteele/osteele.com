/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/javascript/sources
  License: MIT License.
  
  Usage:
  Include this file 
*/

function printEval(s) {
    var value;
    try {value = eval(s)}
    catch (e) {error(e); return}
    //if (value != undefined) 
    info(value);
}

function evalField(id) {
    printEval(document.getElementById(id).value);
}

function addConsole() {
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
    e.innerHTML = CONSOLE_HTML;
    if (!fv) {
        log_element = log_element | document.createElement('div');
        alert(log_element);
        e.appendChild(log_element);
    }
}

var CONSOLE_HTML = '<form id="debugger" action="#" method="get" onsubmit="evalField(\'eval\'); return false"><div><input type="button" onclick="evalField(\'eval\'); return false;" value="Eval"/><input type="text" size="80" id="eval" value="document" onkeyup="/*event.preventDefault(); */return false;"/></div></form>';

try {
    info;
} catch (e) {
    var log_element = document.createElement('div');
    info = function(msg) {
        var span = document.createElement('div');
        span.innerText = msg;
        log_element.appendChild(span);
    };
    warn = info;
    error = info;
    message = info;
}

if (window.addEventListener) {
    window.addEventListener('load', addConsole, false);
} else if (window.attachEvent) {
    window.attachEvent('onload', addConsole);
} else {
    window.onload = (function() {
        var nextfn = window.onload || function(){};
        return function() {
            addConsole();
            nextfn();
        }
    })();
}
