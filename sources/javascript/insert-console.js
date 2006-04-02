/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  License: MIT License (Open Source)
  Created: 2006-04-01
  Modified: 2006-04-02
  
  Usage:
  Add the following bookmarklet to your browser:
  <a href="javascript:(function(){var s=document.createElement('script');s.type='text/javascript';s.src='http://osteele.dev/sources/javascript/insert-console.js';document.documentElement.childNodes[0].appendChild(s)})();">Console</a>
  
  Credits:
  From a suggestion by Stephen Clay <http://mrclay.org/>
*/

(function() {
	function load(src) {
		var s = document.createElement('script');
		s.type='text/javascript';
		s.src=src;
		document.documentElement.childNodes[0].appendChild(s);
	};
	try {InlineConsole}
	catch (e) {
		load('http://osteele.com/sources/javascript/inline-console.js');
		InlineConsole.addConsole();
	}
	try {Readable}
	catch (e) {
		load('http://osteele.com/sources/javascript/readable.js');
	}
})();
