/*
Copyright 2006 Oliver Steele.  Some rights reserved.

This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License:
http://creativecommons.org/licenses/by-nc-sa/2.5/.
*/

var JSON = {};

JSON.stringify = function (object) {
    return (new JSON.generator()).generate(object);
}

/*
  This accepts these strings that are not JSON-compliant:
  - '1.e1'
  - '01'
  - '"\a"'
 */
JSON.parse = function (str) {
    return (new JSON.parser()).parse(str);
};

JSON._hexDigits = "0123456789abcdef";

JSON.generator = function () {};

JSON.generator.prototype.escapes = {
    '\\': '\\\\',
    '\"': '\\\"',
    '\b': '\\b',
    '\f': '\\f',
    '\n': '\\n',
    '\r': '\\r',
    '\t': '\\t'
};

JSON.generator.prototype.generate = function (object) {
    this.segments = [];
    this.append(object);
    return this.segments.join('');
};

JSON.generator.prototype.appendString = function (string) {
    var segments = this.segments;
    var start = 0;
    var i = 0;
    segments.push('"');
    while (i < string.length) {
        var c = string.charAt(i++);
        if (this.escapes[c]) {
            if (i-1 > start)
                segments.push(string.slice(start, i-1));
            segments.push(this.escapes[c]);
            start = i;
        } else if (c < ' ' || c > '~') {
			var n = c.charCodeAt(0);
			segments.push('\\u');
			for (var shift = 16; (shift -= 4) >= 0; )
				segments.push(JSON._hexDigits.charAt((n >> shift) & 15));
			start = i;
		} // else collect into current segment
    }
    if (i > start)
        segments.push(string.slice(start, i));
    segments.push('"');
};

JSON.generator.prototype.objectEncoders = [
    [String, JSON.generator.prototype.appendString],
    [Boolean, function (v) {this.segments.push(String(v))}],
    [Number, function (v) {this.segments.push(String(v))}],
    [Array, function (ar) {
        var segments = this.segments;
        segments.push("[");
        for (var i = 0; i < ar.length; i++) {
            if (i > 0) segments.push(",");
            this.append(ar[i]);
        }
        segments.push("]");
    }],
    // This must come last, since it's a superclass
    [Object, function (object) {
        var segments = this.segments;
        segments.push("{");
        var count = 0;
        for (var key in object) {
            if (count++) segments.push(",");
            this.append(key);
            segments.push(":");
            this.append(object[key]);
        }
        segments.push("}");
    }]];

JSON.generator.prototype.findObjectEncoder = function (object) {
    if (object == null)
        return function (object) {this.segments.push("null")};
    for (var i = 0; i < this.objectEncoders.length; i++) {
        var entry = this.objectEncoders[i];
        if (object instanceof entry[0])
            return entry[1];
    }
    // program error
};

JSON.generator.prototype.append = function (object) {
    switch (typeof object) {
    case 'string':
        this.appendString(object);
        break;
    case 'object':
        this.findObjectEncoder(object).apply(this, [object]);
        break;
    default:
        this.segments.push(String(object));
    }
};

JSON.parser = function () {};

JSON.parser.prototype.escapes = {
    'b': '\b',
    'f': '\f',
    'n': '\n',
    'r': '\r',
    't': '\t'
};

JSON.parser.prototype.table = {
    '{': function () {
        var o = {};
        var c;
		var count = 0;
        while ((c = this.next()) != '}') {
            if (count) {
				if (c != ',')
					this.error("missing ','");
			} else if (c == ',') {
				return this.error("extra ','");
			} else
				--this.index;
            var k = this.read();
            if (typeof k == "undefined") return undefined;
            if (this.next() != ':') return this.error("missing ':'");
            var v = this.read();
            if (typeof v == "undefined") return undefined;
            o[k] = v;
			count++;
        }
        return o;
    },
    '[': function () {
        var ar = [];
        var c;
        while ((c = this.next()) != ']') {
            if (!c) return this.error("unmatched '['");
            if (ar.length) {
				if (c != ',')
					this.error("missing ','");
			} else if (c == ',') {
				return this.error("extra ','");
			} else
				--this.index;
            var n = this.read();
            if (typeof n == "undefined") return undefined;
            ar.push(n);
        }
        return ar;
    },
    '"': function () {
        var s = this.string;
        var i = this.index;
        var start = i;
        var segments = [];
        var c;
        while ((c = s.charAt(i++)) != '"') {
            //if (i == s.length) return this.error("unmatched '\"'");
			if (!c) return this.error("umatched '\"'");
            if (c == '\\') {
                if (start < i-1)
                    segments.push(s.slice(start, i-1));
                c = s.charAt(i++);
				if (c == 'u') {
					var code = 0;
					start = i;
					while (i < start+4) {
						c = s.charAt(i++);
						var n = JSON._hexDigits.indexOf(c.toLowerCase());
						if (n < 0) return this.error("invalid unicode hex digit");
						code = code * 16 + n;
					}
					segments.push(String.fromCharCode(code));
				} else
					segments.push(this.escapes[c] || c);
                start = i;
            }
        }
        if (start < i-1)
            segments.push(s.slice(start, i-1));
        this.index = i;
        return segments.length == 1 ? segments[0] : segments.join('');
    },
	// Also any digit.  The statement that follows this table
	// definition fills in the digits.
    '-': function () {
        var s = this.string;
        var i = this.index;
        var start = i-1;
        var state = 'int';
        var permittedSigns = '-';
        var transitions = {
            'int+.': 'frac',
            'int+e': 'exp',
            'frac+e': 'exp'
        };
        do {
            var c = s.charAt(i++);
			if (!c) break;
            if ('0' <= c && c <= '9') continue;
            if (permittedSigns.indexOf(c) >= 0) {
                permittedSigns = '';
                continue;
            }
            state = transitions[state+'+'+c.toLowerCase()];
            if (state == 'exp') permittedSigns = '+-';
        } while (state);
        this.index = --i;
		s = s.slice(start, i)
		if (s == '-') return this.error("invalid number");
        return Number(s);
    }
};
(function (table) {
    for (var i = 0; i <= 9; i++)
        table[String(i)] = table['-'];
})(JSON.parser.prototype.table);

JSON.parser.prototype.parse = function (str) {
    this.string = str;
    this.index = 0;
	this.message = null;
    var value = this.read();
	if (typeof value == undefined) return;
	if (this.next())
		return this.error("extra characters at the end of the string");
    return value;
};

JSON.parser.prototype.error = function (message) {
	this.message = message;
	return undefined;
}
    
JSON.parser.prototype.read = function () {
    var c = this.next();
    var fn = c && this.table[c];
    if (fn)
        return fn.apply(this);
    var keywords = {'true': true, 'false': false, 'null': null};
    var s = this.string;
    var i = this.index-1;
    for (var w in keywords) {
        if (s.slice(i, i+w.length) == w) {
            this.index = i+w.length;
            return keywords[w];
        }
    }
    return undefined;
}

JSON.parser.prototype.next = function () {
    var s = this.string;
    var i = this.index;
    do {
        if (i == s.length) return undefined;
        var c = s.charAt(i++);
    } while (" \t\n\r\f".indexOf(c) >= 0);
    this.index = i;
    return c;
};

//print(JSON.parse('[null,null]')[2]);
//print(JSON.stringify([null,null]));
