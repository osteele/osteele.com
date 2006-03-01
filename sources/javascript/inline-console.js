/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/javascript/sources
  License: MIT License.

Todo:
- insert at bottom if no fvlogger
- define own logger if no fvlogger
*/

function printEval(s) {
    var value;
    try {value = eval(s)}
    catch (e) {error(e); return}
    if (value != undefined) info(value);
}

function evalField(id) {
    printEval(document.getElementById(id).value);
}

function addConsole() {
    var e = document.getElementById('inline-console');
    if (!e) {
        var fv = document.getElementById('fvlogger');
        e = document.createElement('div');
        fv.appendChild(e);
    }
    e.innerHTML = CONSOLE_HTML;
}

var CONSOLE_HTML = '<form id="debugger" action="#" method="get" onsubmit="evalField(\'eval\'); return false"><div><input type="button" onclick="evalField(\'eval\'); return false;" value="Eval"/><input type="text" size="80" id="eval" value="document" onkeyup="/*event.preventDefault(); */return false;"/></div></form>';

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

