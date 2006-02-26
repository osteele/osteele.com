/* Copyright 2006 Oliver Steele.  All rights reserved. */


// On a development machine, display the debugger.
// Do this first, so that we can see errors that occur
// during the remainder of the load sequence.
var host = document.location.toString().match(/.+?:\/\/(.+?)\//)[1];
if (host.match(/\.xdev/) || document.location.toString().match('[\?&]debug'))
	Element.show($('debugger'));


/*
 * Utilities
 */

String.prototype.escapeJavascript = function () {
	var s = this;
	s = s.replace('\\', '\\\\', 'g');
	s = s.replace('\n', '\\n', 'g');
	s = s.replace('"', '\\"', 'g');
	return '"' + s + '"';
};

String.prototype.scan = function(re) {
	if (re.global)
		re = new RegExp(re.source, re.toString().replace(/.*\//,'').replace('g',''));
	var matches = [];
	var i = 0;
	while (i < this.length) {
		var m = this.slice(i).match(re);
		if (!m) break;
		matches.push({index: i+m.index, string: m[0], length: m[0].length});
		i += m.index + m[0].length;
		if (m.index + m[0].length == 0) i++;
	}
	return matches;
};

function contentTag(content, tag, options) {
	if (arguments.length < 3) options = {};
	var s = '<' + tag;
    for (var k in options)
        s += k + '="' + options[k] + '"';
	s += '>' + content;
	s += '</' + tag + '>';
	return s;
}

function escapeTag(content, tag, options) {
	return contentTag(content.escapeHTML(), tag, options);
}

function replaceCallback(input, re, fn, fn2) {
	var s = '';
	var i = 0;
	fn2 = fn2 || function(s){return s};
	var matches = input.scan(re);
	if (!re.global && matches.length)
		matches = matches.slice(0, 1)
	$A(matches).each(
		function (m) {
			s += fn2(input.slice(i, m.index));
			s += fn(input.slice(m.index, m.index+m.length));
			i = m.index + m.length;
		});
	s += fn2(input.slice(i));
	return s;
}

/*
 * Tab Controller (class doubles as container)
 */

function TabController(name) {
	TabController.controllers[name] = this;
	this.name = name;
	this.view = $(name);
	this.results = $(name + '-results');
	this.usage = $(name + '-usage');
}

// Class methods (in its use as a container)

TabController.controllers = {};
TabController.selected = null;

Element.setVisible = function (node, visible) {
    if (visible)
        Element.show(node);
    else
        Element.hide(node);
};

TabController.select = function(name) {
	if (typeof name != 'string') {
		var tab = name;
		name = tab.innerHTML.toLowerCase();
		Element.addClassName(tab.parentNode, 'selected');
		if (this.lastTab)
			Element.removeClassName(this.lastTab.parentNode, 'selected');
		this.lastTab = tab;
	}
    Element.setVisible('nongraph', name != 'graph' && name != 'parse');
    Element.setVisible('input-area', name != 'multiple');
    Element.setVisible('extended-area', name != 'help');
    Element.setVisible('replacement-area', name == 'replace');
	//Element.hide.apply(null, $H(TabController.controllers).keys());
    for (var k in TabController.controllers)
        Element.hide(TabController.controllers[k].view);
	Element.show(name);
    var controller = this.controllers[name];
	TabController.selected = controller;
    if (this._updateArguments)
        controller.updateContents.apply(controller, this._updateArguments);
};

TabController.updateContents = function(patternChanged, re, input) {
	controller = TabController.selected;
    if (controller)
        controller.updateContents(patternChanged, re, input);
    this._updateArguments = [true, re, input];
}

// Instance methods (in its use as a base class)
TabController.prototype.updateContents = function(patternChanged, re, input) {
    if (patternChanged)
        this.updatePattern(re, input);
    else
        this.updateInput(re, input);
    this.updateProgramUsage(re, input);
};

TabController.prototype.updatePattern = function (pattern, input) {
	this.updateInput(pattern, input);
};

TabController.prototype.updateInput = function (pattern, input) {};

TabController.prototype.makeResultsList = function(ar) {
	if (!ar.length)
		return '<i>No match.</i>';
	return '<strong>Results:</strong><br/>' +
    $A(ar).map(function(s, i){
						  var value = "<i>empty string</i>";
						  if (s)
                              value = contentTag(s.escapeJavascript(), 'tt');
                          return 'results['+i+'] = '+ value;
					  }).join('<br/>');
};

TabController.prototype.updateProgramUsage = function(re, input) {
    var generator = UsageGenerator.getGenerator(this.name);
	if (!generator) return;
    var text = generator.getUsageText(re, input, $F('replacement'));
    this.usage.innerHTML = text;
}

/*
 * Search tab
 */
var searchController = new TabController('search');

searchController.updateInput = function (re, input) {
	this.showResults(re, input);
};

searchController.showResults = function(re, input) {
    var presentation = searchResultsPresentation(re, input);
    $('search-summary').innerHTML = presentation[0];
    $('search-details').innerHTML = presentation[1];
};

function searchResultsPresentation(re, input, limit) {
	var match = input.match(re);
    if (!match)
        return ['<span class="nomatch">No match.</span>', ''];
    
    var s = '';
	var label = 'Groups';
    var makeLabel = function(i) {return '$'+i};
    var prefix = '';
    var suffix = '';
	if (re.global) {
		label = 'Matches';
        makeLabel = function(i) {return 'match['+i+']'};
		s = replaceCallback(input, re,
			   function (seg) {return escapeTag(seg, 'em')},
			   function (seg) {return escapeTag(seg, 'span', {'class': 'prefix'})});
	} else {
		prefix = input.slice(0, match.index);
		suffix = input.slice(input.match(re).index + match[0].length);
		s  = escapeTag(prefix, 'span', {'class': 'prefix'});
		s += escapeTag(match[0], 'em');
		s += escapeTag(suffix, 'span', {'class': 'suffix'});
	}
	
	s = escapeTag(re.toString(), 'kbd')+' matches '+contentTag(s, 'tt');
	var summary = s;
	
	var s = '';
	if (prefix) s += 'Prefix = ' + escapeTag(prefix, 'tt')+'<br/>';
	//s += 'Match = ' + (match[0] ? escapeTag(match[0], 'tt') : "'' (empty string)")+'<br/>';
	if (suffix) s += 'Suffix = ' + escapeTag(suffix, 'tt')+'<br/>';
	if (match.length) {
        s += '<br/>';
		s += contentTag(label+':', 'span', {style: 'font-style: italic'}) + '<br/>';
		match.each(function(m, i) {
                       if (m == undefined) return;
                       if (i == limit) {
                           s += '&hellip;';
                           return;
                       }
                       if (i > limit) return;
                       var value = m.escapeJavascript();
                       s += makeLabel(i)+' = '+escapeTag(value, 'tt') + '<br/>';
				   });
	}
	return [summary, s];
};


/*
 * Replace tab
 */
var replaceController = new TabController('replace');

replaceController.updateInput = function (re, input) {
	var sub = $F('replacement');
	this.results.innerHTML = replaceCallback(input, re, function () {return '<em>' + sub + '</em>'});
};

/*
 * Multiple tab
 */

var multipleController = new TabController('multiple');

multipleController.updatePattern = function(re) {
    this.re = re;
	this.updateResults();
};

multipleController.updateResults = function () {
    var inputs = document.getElementsByClassName('multiple-inputs');
    var outputs = document.getElementsByClassName('multiple-outputs');
    var re = this.re;
    $A(inputs).each(
        function (input, i) {
            var output = outputs[i];
            var match = input.value.match(re);
            var presentation = searchResultsPresentation(re, input.value, 5);
            output.innerHTML = presentation[0] + '<br/>' + presentation[1];
        });
};

(function() {
    var e = $('multiple-table');
    var s = e.innerHTML;
    for (var i = 0; i < 5; i++) {
        s += '<tr><td colspan="2"><textarea class="multiple-inputs" rows="4" cols="40"></textarea></td>' +
            '<td><span class="multiple-outputs"></span></td></tr>';
    }
    try {
        e.innerHTML = s;
    } catch (er) {
        error(er);
		Element.hide('multipleTab');
    }
})();

/*
 * Scan tab
 */
var scanController = new TabController('scan');

scanController.updateInput = function (re, input) {
	var strings = $A(input.scan(re)).map(function(m){return m.string});
	this.results.innerHTML = this.makeResultsList(strings);
};

/*
 * Split tab
 */
var splitController = new TabController('split');

splitController.updateInput = function (re, input) {
	this.results.innerHTML = this.makeResultsList(input.split(re));
};

/*
 * Parse tab
 */
var parseController = new TabController('parse');

parseController.updatePattern = function (re, input) {
    this.re = re;
};

parseController.updateTree = function () {
	var canvas = this.canvas;
    if (!canvas) {
        canvas = document.createElement('canvas');
        this.canvas = canvas;
        this.container = $('parseTreeContainer');
        this.container.appendChild(canvas);
        this.labels = [];
        setupCanvas(canvas, this);
    }
    try {
        var parse = new REParser().parse($F('pattern'));
    } catch (e) {
        error(e);
        return;
    }
    var root = parse;
    var ctx = canvas.getContext('2d');
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	this.labels.each(function(e){e.parentNode.removeChild(e)});
	this.labels = [];
    gNode = root;
	new TreeLayout().layout(root).render(canvas, ctx);
};

// there's some magic number in this seciton.  get them out.
function TreeLayout() {}

TreeLayout.prototype.layout = function(root) {
	this.root = root;
	root.each(function(node){
			node.width = node.string.length * 8;
			node.height = 12;
		});
    this.layoutSubtree(root);
	this.translateSubtree(root, 0, 20);
	return this;
};

TreeLayout.prototype.render = function(canvas, ctx) {
	this.ctx = ctx;
	var bounds = this.getBounds(this.root);
    canvas.width = bounds.right;
    canvas.height = bounds.bottom;
    this.drawNode(this.root);
};

TreeLayout.prototype.drawNode = function (node) {
	var self = this;
	var ctx = this.ctx;
	ctx.drawString(node.x, node.y+10, node.string);
	$A(node.children).each(
		function (child) {
			ctx.beginPath();
			ctx.moveTo(node.x+node.width/2, node.y+node.height);
			ctx.lineTo(child.x+child.width/2, child.y);
			ctx.stroke();
			self.drawNode(child);
		});
};

TreeLayout.prototype.layoutSubtree = function(node) {
	var xpad = 10;
	var ypad = 40;
	node.x = node.y = 0;
	if (node.children.length) {
		var x = 0;
		var y = 20;
		var self = this;
		node.children.each(
			function (child) {
				self.layoutSubtree(child);
				self.translateSubtree(child, x, ypad);
				var b = self.getBounds(child);
				x = self.getBounds(child).right + xpad;
			});
		node.x = (x-xpad)/2 - node.width/2;
	}
};

TreeLayout.prototype.translateSubtree = function(node, dx, dy) {
	node.x += dx;
	node.y += dy;
	var self = this;
	$A(node.children).each(
		function (child) {self.translateSubtree(child, dx, dy)});
};

TreeLayout.prototype.getBounds = function(node, bounds) {
	bounds = bounds || {left: Infinity, right: -Infinity, top: Infinity, bottom: -Infinity};
	bounds.left = Math.min(bounds.left, node.x);
	bounds.right = Math.max(bounds.right, node.x+node.width);
	bounds.top = Math.min(bounds.top, node.y);
	bounds.bottom = Math.max(bounds.top, node.y+node.height);
	var self = this;
	node.children.each(function(child){self.getBounds(child, bounds)});
	return bounds;
};

Event.observe('updateParseButton', 'click',
              function(){parseController.updateTree()});


/*
 * Graph tab
 */
var graphController = new TabController('graph');

graphController.updatePattern = function (re, input) {
	var pattern = $F('pattern');
    this.updateButton(); // call this before checkPattern
	var msg = this.checkPattern(pattern);
	if (msg) {
		$('graphButton').disabled = true;
		if (msg != ' ')
            msg = '(The "Update" button is disabled because the graphing engine doesn\'t handle ' + msg + '.)';
		$('noGraph').innerHTML = msg;
		Element.show('noGraph');
	} else {
		Element.hide('noGraph');
	}
};

graphController.checkPattern = function(s) {
	try {
		RegExp(s);
	} catch (e) {
		return ' ';
	}
	s = s.replace(/\\[^bB\d''`&]/g, '');
	s = s.replace(/$$/g, '');
	var e = {
		'quantifiers': /\{.*?\}/,
		'anchors': /\\[bB]|[\^\$]/,
		'assertions': /\(\?[=!]/,
		'back-references': /\\[\d''`&]/ 
	}
	for (var p in e) {
		var m = s.match(RegExp(e[p]));
		if (m) {
			return p.escapeHTML() + ', such as "<kbd>' + m[0].escapeHTML() + '</kbd>"';
		}
	}
};

graphController.requestGraph = function(s) {
    var pattern = $F('pattern');
    this.graphView.requestPattern(pattern,
                                  this.updateButton.bind(this));
};

graphController.updateButton = function() {
    var e = $('graphButton');
    if (this.graphView && $F('pattern') != this.graphView.patternSource) {
        e.value = 'Update';
        e.disabled = false;
e
    } else {
        e.value = 'Up to date';
        e.disabled = true;
    }
};

/*
 * Help tab
 */
var helpController = new TabController('help');

var LegendKey = [
    '.', 'any character except newline.  If DOTALL, matches newline.',
    '^', 'the start of the string.  In multiline, matches start of each line.',
    '$', 'the end of the string or just before the last newline.  In multiline, matches before each newline.',
    '\\d,\\w,\\s', 'digit, word, or whitespace, respectively',
    '\\D,\\W,\\S', 'anything except digit, word, or whitespace',
    '\\.', 'a period (and so on for <tt>\\*</tt>, <tt>\\(</tt>, etc.)',
    '[ab]', 'characters <tt>a</tt> or <tt>b</tt>',
    '[a-c]', '<tt>a</tt> through <tt>c</tt>',
    '[^ab]', 'any character except <tt>a</tt> or <tt>b</tt>',
    'expr*', 'zero or more repetitions of expr',
    'expr+', 'one or more repetitions of expr',
    'expr?', 'zero or one repetition of expr',
    '*?,+?,??', '...same as above, but as little text as possible',
    'expr{m}', 'm copies of expr',
    'expr{m,n}', 'between m and n copies of the preceding expression',
    'expr{m,n}?', '...but as few as possible',
    '<var>a</var>|<var>b</var>', 'either <var>a</var> or <var>b</var>',
    '(expr)', 'same as expr, but captures the match for use in \\1, etc.',
    '(?:expr)', 'doesn\'t capture the match',
    '(?=expr)', 'followed by expr',
    '(?!expr)', 'not followed by expr'];

function createLegend() {
    var s = '<strong>Quick Reference:</strong>';
    for (var i = 0; i < LegendKey.length;) {
        var a = LegendKey[i++];
        var b = LegendKey[i++];
        s += contentTag(a, 'dt');
        s += contentTag(b, 'dd');
    }
    s = contentTag(s, 'dl');
    s = s.replace(/expr/g, '<var>expr</var>');
    $('key').innerHTML = s;
}

/*
 * Graph view
 */
function FSAView(container) {
    this.container = container;
    var canvas = document.createElement('canvas');
    container.appendChild(canvas);
    setupCanvas(canvas, this);
    this.canvas = canvas;
    this.patternSource = null;
    this.labels = [];
}

function setupCanvas(canvas, controller) {
	var ctx = canvas.getContext("2d");
	ctx.circle = function(x, y, r) {
		this.moveTo(x+r,y);
		this.arc(x,y,r, 0, 2*Math.PI, true);
	};
	
	ctx.drawString = function(x, y, string) {
		var label = document.createElement('div');
		var text = document.createTextNode(string);
		label.style.left = x;//+'px';
		label.style.top = y-10;//+'px';
		label.style.position = 'absolute';
		label.appendChild(text);
		controller.container.appendChild(label);
		controller.labels.push(label);
	};
	return ctx;
}

FSAView.prototype.requestPattern = function (pattern, success) {
	var upattern = pattern.replace(/^\.(?!\.\*)/, '.*');
	var url="server.py?pattern="+encodeURIComponent(upattern);
	var req = new XMLHttpRequest();
    var self= this;
	req.onreadystatechange = function(){
		self.processReqChange(req, pattern, success)
	};
	req.open("GET", url, true);
	req.send(null);
};

FSAView.prototype.processReqChange = function(request, pattern, success) {
	if (request.readyState != 4)
        return;
    this.patternSource = pattern;
    if (0 < request.status && request.status < 200 ||
        300 < request.status) {
        error('error: ' + request.status);
        return;
    }
    var result = JSON.parse(request.responseText);
    if (typeof result == 'string') {
        warn(result);
        return;
    }
    this.showGraph(result.dfa.graph);
    if (success) success();
}

FSAView.prototype.showGraph = function(graph) {
    var canvas = this.canvas;
	var ctx = canvas.getContext("2d");
    canvas.width = graph.bb[2];
    canvas.height = graph.bb[3];
    setupCanvas(canvas, this);
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	this.labels.each(function(e){e.parentNode.removeChild(e)});
	this.labels = [];
	new GraphView(graph).render(ctx);
}


/*
 * Observers
 */

function updateTabContents(patternChanged) {
	var input = $F('input');
	flags = '';
	if ($F('globalCheckbox')) flags += 'g';
	if ($F('ignoreCaseCheckbox')) flags += 'i';
	if ($F('multilineCheckbox')) flags += 'm';
	try {
		var re = RegExp($F('pattern'), flags);
	} catch (e) {
		Element.show('error');
		$('error').innerHTML = '' + e.message + '<br/><br/>';
		return;
	}
	Element.hide('error');
	TabController.updateContents(patternChanged, re, input);
}

function patternChanged() {
	updateTabContents(true);
}

function resizeTextArea(d) {
    var e = $('input');
    var n = e.rows;
    if (d == -1 && n > 1) n = Math.floor(n/2);
    if (d == 1) n *= 2;
    e.rows = n;
    Element.setVisible('shrinkInput', n > 1);
}

Event.observe('pattern', 'keyup', patternChanged);
Event.observe('globalCheckbox', 'click', patternChanged);
Event.observe('ignoreCaseCheckbox', 'click', patternChanged);
Event.observe('multilineCheckbox', 'click', patternChanged);
Event.observe('input', 'keyup', updateTabContents);
Event.observe('replacement', 'keyup', updateTabContents);
Event.observe('graphButton', 'click', function(){graphController.requestGraph($F('pattern'))});
Event.observe('shrinkInput', 'click', function(){resizeTextArea(-1)});
Event.observe('expandInput', 'click', function(){resizeTextArea(1)});

/*
 * Initialization
 */

function implementsCanvas() {
    // Safari's canvas doesn't work here.  Debug this later.
    if (navigator.appVersion.match(/safari/i))
        return false;
    try {
        HTMLCanvasElement;
        return true;
    } catch (e) {
        return false;
    }
}

if (implementsCanvas()) {
    graphController.graphView = new FSAView($('graphContainer'));
	if (!graphController.checkPattern($F('pattern')))
		graphController.requestGraph($F('pattern'));
} else {
	Element.hide($('graphTab'));
}
//Element.hide($('treeTab'));

createLegend();

TabController.select($('searchTab'));
//TabController.select($('parseTab'));

updateTabContents(true);

