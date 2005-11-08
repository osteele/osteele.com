/*
Care Of: 
		Simon Willison
		http://simon.incutio.com/archive/2004/05/26/addLoadEvent

*/
function af_elaAddLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    }
  }
}