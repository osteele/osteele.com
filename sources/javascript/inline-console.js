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
     document.getElementById('inline-console').innerHTML = '<form id="debugger" action="#" method="get" onsubmit="evalField(\'eval\'); return false"><div><input type="button" onclick="evalField(\'eval\'); return false;" value="Eval"/><input type="text" size="80" id="eval" value="document" onkeyup="event.preventDefault(); return false;"/><br/></div></form>';
}
     window.addEventListener('load', addConsole, false);
