// todo:
// - unicode
// - missing, extra ',' in array, object

var JSON = {};

JSON.stringify = function (object) {
    return (new JSON.generator()).generate(object);
}

JSON.parse = function (str) {
    return (new JSON.parser()).parse(str);
};

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
        }
    }
    if (i > start)
        segments.push(string.slice(start, i));
    segments.push('"');
};

JSON.generator.prototype.objectEncoders = [
    [String, JSON.generator.prototype.appendString],
    [Boolean, function (v) {this.segments.push(v)}],
    [Number, function (v) {this.segments.push(v)}],
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

JSON.generator.prototype.findEncoder = function (object) {
    if (object == null)
        return function (object) {this.segments.push("null")};
    for (var i in this.objectEncoders) {
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
        this.findEncoder(object).apply(this, [object]);
        break;
    default:
        this.segments.push(object);
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
        while ((c = this.next()) != '}') {
            if (c == ',') continue;
            --this.index;
            var k = this.read();
            if (typeof k == "undefined") return undefined;
            if (this.next() != ':') return undefined;
            var v = this.read();
            if (typeof v == "undefined") return undefined;
            o[k] = v;
        }
        return o;
    },
    '[': function () {
        var ar = [];
        var c;
        while ((c = this.next()) != ']') {
            if (this.index == this.string.length) return undefined;
            if (c == ',') continue;
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
            if (i == s.length) return undefined;
            if (c == '\\') {
                if (start < i-1)
                    segments.push(s.slice(start, i-1));
                c = s.charAt(i++);
                segments.push(this.escapes[c] || c);
                start = i;
            }
        }
        if (start < i-1)
            segments.push(s.slice(start, i-1));
        this.index = i;
        return segments.length == 1 ? segments[0] : segments.join('');
    },
    '-': function () {
        var s = this.string;
        var i = this.index;
        var start = i-1;
        var state = 'int';
        var signs = '-';
        var transitions = {
            'int+.': 'frac',
            'int+e': 'exp',
            'frac+e': 'exp'
        };
        do {
            var c = s.charAt(i++);
            if ('0' <= c && c <= '9') continue;
            if (signs.indexOf(c) >= 0) {
                signs = '';
                continue;
            }
            state = transitions[state+'+'+c.toLowerCase()];
            if (state == 'exp') signs = '+-';
        } while (state);
        this.index = i-1;
        return Number(s.slice(start, i-1));
    }
};
(function (table) {
    for (var i = 0; i <= 9; i++)
        table[String(i)] = table['-'];
})(JSON.parser.prototype.table);

JSON.parser.prototype.parse = function (str) {
    this.string = str;
    this.index = 0;
    var r = this.read();
    //Debug.write('detritus:', this.string.slice(this.index));
    return r;
};
    
JSON.parser.prototype.read = function () {
    var c = this.next();
    var fn = this.table[c];
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
