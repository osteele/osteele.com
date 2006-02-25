/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
*/

function Node(op, children) {
    this.op = op;
    this.children = children;
}

function Literal(s) {
    this.op = 'literal';
    this.content = s;
    this.children = [];
}

function CharSet(s) {
    this.op = 'charset';
    this.content = s;
    this.children = [];
}

function REParser() {
}

REParser.prototype.concat = function(a, b) {
    if (a && b) {
        if (a.op == 'concat') {
            a.children.push(b);
            return a;
        }
        return new Node('concat', [a, b]);
    } else
        return a || b;
};

REParser.prototype.parse = function(string) {
    this.string = string;
    this.index = 0;
    var node = this.readTerm();
    if (!node) throw "empty"; // fixme
    if (this.index != string.length)
        throw "extra ')': " + string.slice(this.index);
    return node;
};

REParser.prototype.readTerm = function() {
    var node = this.readDisjunct();
    if (!node && this.string.charAt(this.index) != '|') return node;
    var nodes = [node || new Literal('')];
    while (this.string.charAt(this.index) == '|') {
        this.index++;
        node = this.readDisjunct() || new Literal('');
        nodes.push(node);
    }
    if (nodes.length > 1)
        node = new Node('alternation', nodes);
    return node;
};

REParser.prototype.readDisjunct = function() {
    var s = this.string;
    var literal = '';

    var node = null;
    while (this.index < s.length) {
        var c = s.charAt(this.index++);
        if ("|)".indexOf(c) >= 0) {
            --this.index;
            break;
        }
        var fn = tabled[c];
        if (fn) {
            if (literal) {
                node = this.concat(node, new Literal(literal));
                literal = '';
            }
            var node2 = this.qualify(fn.apply(this));
            node = this.concat(node, node2);
            continue;
        }
        /*if (c == '\\')
          s += string.charAt(i++); // TODO*/
        var ahead = s.charAt(this.index); // TODO
        if (ahead && '?+*{'.indexOf(ahead) >= 0) {
            if (literal) {
                node = this.concat(node, new Literal(literal));
                 literal = '';
            }
            node = this.concat(node, this.qualify(new Literal(c)));
        } else
            literal += c;
    }
    if (literal)
        node = this.concat(node, new Literal(literal));
    return node;
};

REParser.prototype.qualify = function(node) {
    var s = this.string;
    var c = s[this.index];
    if (c && "?+*".indexOf(c) >= 0) {
        if (s[++this.index] == '?')
            c += s[this.index++];
        node = new Node(s, [node]);
    }
    while (true) {
        s = this.string.slice(this.index);
        var match = s.match(/^\{(.*?)\}/);
        if (!match) break;
        node = new Node('count', [node]);
        node.range = match[1].split(',')
        this.index += match[0].length;
    }
    return node;
};

tabled = {};

tabled['['] = function() {
    var s = this.string;
    var i = this.index;
    var i0 = i;
    if (s.charAt(i) == '^')
        ++i;
    while (++i < s.length) {
        var c = s.charAt(i); // fixme
        this.i = i;
        if (c == ']') {
            this.index = i+1;
            return new CharSet(s.slice(i0, i));
        }
        if (c == '\\')
            ++i;
        if (s.charAt(i) == '-') {
            ++i;
            c = s.charAt(i++);
            if (c == '\\')
                ++i;
        }
    }
    throw "unterminated ']'";
};

tabled['{'] = function() {throw "unexpected {"};

tabled['('] = function() {
    var s = this.string;
    var i = this.index;
    var c = s.charAt(i); // fixme
    if (c == '?') {
        c = s.charAt(++i);
        if (c == ':')
            i++;
    }
    var i0 = i;
    this.i = i;
    var node = null;
    while (s.charAt(this.index) != ')')
        node = this.concat(node, this.readTerm());
    if (!node)
        node = new Literal('');
    if (s.charAt(this.index++) != ')')
        throw "unterminated '('"
    return new Node('group', [node]);
};

function rep(s) {
    p(new REParser().parse(s.toString()));
}
rep('abc|def')
