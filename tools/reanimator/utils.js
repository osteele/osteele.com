/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: MIT License.
*/

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

LzKeys.keyCodes['<'] = 188;
LzKeys.keyCodes['>'] = 190;

LzKeys.charFromCode = function(n) {
	var toUpper = "-_=+/?[{]}\\|\"',<.>;:";
	var ix;
	for (var c in LzKeys.keyCodes)
		if (LzKeys.keyCodes[c] == n) {
            if (c.length != 1) return null;
			if (LzKeys.downKeysHash[16]) {
				if (48 <= n && n <= 57)
					return ")!@#$%^&*(".charAt(n-48);
				c = c.toUpperCase();
				if ((ix = toUpper.indexOf(c)) >= 0 && (ix % 2) == 0)
					c = toUpper.charAt(ix+1);
			} else if ((ix = toUpper.indexOf(c)) >= 0)
				c = toUpper.charAt(ix-1);
			else if (48 <= n && n <= 57)
				c = "0123456789".charAt(n-48);
			return c;
		}
	return String.fromCharCode(n);
};