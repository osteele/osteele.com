/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Download: http://osteele.com/sources/openlaszlo/laszlo-utils.js
  License: MIT License.
*/

// add some missing entries
LzKeys.keyCodes['<'] = 188;
LzKeys.keyCodes['>'] = 190;

// these are off by one in LzKeys.as
LzKeys.keyCodes[')']  = 48;
LzKeys.keyCodes['!']  = 49;
LzKeys.keyCodes['@']  = 50;
LzKeys.keyCodes['#']  = 51;
LzKeys.keyCodes['$']  = 52;
LzKeys.keyCodes['%']  = 53;
LzKeys.keyCodes['^']  = 54;
LzKeys.keyCodes['&']  = 55;
LzKeys.keyCodes['*']  = 56;
LzKeys.keyCodes['(']  = 57;

LzKeys._SHIFTLESS = "0123456789-=[]\\;',./";
LzKeys._SHIFTED   = ")!@#$%^&*(_+{}|:\"<>?";

/* Given a key code (an integer), return the character.
 * If the second argument is supplied, this should be the
 * downKey hash as from LzKeys.downKeysHash.  If it it absent,
 * LzKey.downKeysHash is used.
 *
 * Returns null if the code doesn't correspond to any character
 * (e.g. shift, etc.).
 *
 * Usage:
 *   <method event="onkeydown" args="n">
 *     var c = LzKeys.fromEventCode(n);
 *     if (c == null) return;
 *     Debug.write('keycode='+n+'; charcode='+c.charCodeAt(0)+'; char=\''+c+'\'');
 *   </method>
 */

LzKeys.fromEventCode = function(n, downKeys) {
    if (downKeys == null) downKeys = LzKeys.downKeysHash;
    if (16 <= n && n <= 20) return null; // modifier key
    if (n < 0) return null;
	for (var c in LzKeys.keyCodes)
		if (LzKeys.keyCodes[c] == n) {
            if (c.length != 1) break; // control characters
            // translate unshifted to shifted and vice versa, since
            // LzKeys.keyCodes is a hash so it's undefined which value
            // we'll run into first
            var from = LzKeys._SHIFTED;
            var to = LzKeys._SHIFTLESS;
            if (downKeys[16]) { // shift
                c = c.toUpperCase();
                from = LzKeys._SHIFTLESS;
                to = LzKeys._SHIFTED;
            }
            var i = from.indexOf(c);
            if (i >= 0)
                c = to.charAt(i);
			return c;
		}
	return String.fromCharCode(n);
};