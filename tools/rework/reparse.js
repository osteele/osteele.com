/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
*/

function REParser() {
}

REParser.Node = function(nodeType, children, start, end) {
	if (arguments.length < 3) {
		start = children[0].start;
		end = children[children.length-1].end;
	}
    this.nodeType = nodeType;
	this.start = start;
	this.end = end;
    this.children = children;
	// postorder visitor
	this.each = function (visitor) {
		for (var i = 0; i < children.length; i++)
			children[i].each(visitor);
		visitor(this);
	}
};

// sequence of atoms
REParser.Literal = function(start, end) {
    this.nodeType = 'literal';
    this.start = start;
	this.end = end;
    this.children = [];
	this.each = function(visitor) {visitor(this)};
};

REParser.Empty = function(index) {
	this.nodeType = 'empty';
	this.start = this.end = index;
	this.children = [];
	this.each = function(visitor) {visitor(this)};
};

REParser.CharSet = function(start, end) {
    this.nodeType = 'charset';
	this.start = start;
	this.end = end;
    this.children = [];
	this.each = function(visitor) {visitor(this)};
}

REParser.prototype.parse = function(string) {
    this.string = string;
    this.index = 0;
    var node = this.readDisjunction();
    if (this.index != string.length)
        throw "extra ')': " + string.slice(this.index);
	var self = this;
	node.each(function(n) {n.string = self.string.slice(n.start, n.end)});
    return node;
};

REParser.prototype.readList = function(nodeType, separator, endPattern, scanner) {
	var nodes = [];
	var c;
	while (!this.string.charAt(this.index).match(endPattern)) {
		nodes.push(scanner.apply(this));
		if (this.string.charAt(this.index) == separator)
			this.index++;
	}
	return this.makeList(nodeType, nodes);
};

REParser.prototype.makeList = function(nodeType, nodes) {
	switch (nodes.length) {
	case 0:
	return new REParser.Empty(this.index);
	case 1:
	return nodes[0];
	default:
	return new REParser.Node(nodeType, nodes);
	}
}

REParser.prototype.readDisjunction = function() {
	return this.readList('|', '|', /^\)?$/,
						 this.readDisjunct);
};

REParser.prototype.readDisjunct = function() {
	return this.readList('concat', null, /^[\)|]?$/,
						 this.readTerm);
};

REParser.quantifierChars = '?+*{';

REParser.prototype.readTerm = function() {
	var s = this.string;
	var i = this.index;
	var c = s.charAt(i);
	var fn = REParser.table[c];
	if (fn)
		return fn.apply(this);
	var match = s.slice(i).match(/^((?:[^\\]|\\.)*?)(?:([?+*\{])|[\[\(\)|]|$)/);
	var literal = match[1];
	var quantifier = match[2];
	if (quantifier && literal.length > 1) {
		quantifier = null;
		literal = literal.slice(0, literal.length - 1);
	}
	var start = this.index;
	this.index += literal.length;
	var node = literal ? new REParser.Literal(start, this.index) : new REParser.Empty(this.index);
	return this.quantify(node);
};

REParser.prototype.quantify = function(node) {
    var s = this.string;
	var start = this.index;
    var c = s[this.index];
    if (c && "?+*".indexOf(c) >= 0) {
        if (s[++this.index] == '?')
            c += s[this.index++];
        node = new REParser.Node(s.slice(start, this.index), [node]);
		node.end = this.index;
    }
    while (true) {
        s = s.slice(this.index);
        var match = s.match(/^\{(.*?)\}/);
        if (!match) break;
        node = new Node('count', [node]);
		node.end = this.index;
        node.range = match[1].split(',')
        this.index += match[0].length;
    }
    return node;
};

REParser.table = {};

REParser.table['['] = function() {
    var s = this.string;
	var i = this.index;
    var match = s.slice(i).match(/\[\^?([^\\\]]|\\.|([^\\\]]|\\.)-([^\\\]]|\\.))*\]/);
	if (!match)
		throw "unterminated ']'";
	this.index += match[0].length;
	return new REParser.CharSet(i, this.index);
};

REParser.table['('] = function() {
    var s = this.string;
    var i = this.index;
	var start = i;
    var c = s.charAt(++i); // fixme
    if (c == '?') {
        c = s.charAt(++i);
        if (c.match(/[?:!]/))
            i++;
    }
    this.index = i;
	var node = this.readDisjunction();
    if (s.charAt(this.index++) != ')')
        throw "unterminated '('";
	return new REParser.Node('group', [node], start, this.index);
};

REParser.debug = function (s) {
    node = new REParser().parse(s.toString());
	var self = this;
	node.each(function(n) {if (!n.children.length) delete n.children});
	node.each(function(n) {delete n.start; delete n.end; delete n.each});
	return node;
};
p(REParser.debug('a[bc]d'));
