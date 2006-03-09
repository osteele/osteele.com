/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: MIT License.
*/

String.prototype.replace = function (s, r, flags) {
    return this;
    /*var global = (flags||'').indexOf('g') >= 0;
    var prefix = '';
    var match;
    while (match = this.search(*/
};

Array.includes = function(ar, n) {
	for (var i = 0; i < ar.length; i++)
        if (ar[i] == n) return true;
	return false;
}

Array.compact = function (ar) {
	var dst = 0;
	for (var i = 0; i < ar.length; i++) {
        ar[dst] = ar[i];
        if (ar[i] != null) dst++;
	}
	ar.length = dst;
}

function long2css(n) {
	var a = "0123456789ABCDEF";
	var s = '#';
	for (var i = 24; (i -= 4) >= 0; )
        s += a.charAt((n>>i) & 0xf);
	return s;
}
