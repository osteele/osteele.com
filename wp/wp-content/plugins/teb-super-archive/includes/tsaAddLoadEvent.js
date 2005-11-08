/*
Care Of: 
		Simon Willison
		http://simon.incutio.com/archive/2004/05/26/addLoadEvent
		Thnx Dude!

2005-05-07:
	Renamed to avoid collisions there are other addLoadEvent functions.

******** USEAGE ********************************
addLoadEvent(nameOfSomeFunctionToRunOnPageLoad);
addLoadEvent(function() {
  // more code to run on page load 
});
*/
function tsaAddLoadEvent(func) {
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