/* Copyright 2006 Oliver Steele.  All rights reserved. */

/*
  Details:
  - Safari: make a proxy object to attach new methods to
  
  Deploy:
  - link to blog entry
*/

// On a development machine, display the debugger.
// Do this first, so that we can see errors that occur
// during the remainder of the load sequence.
var host = document.location.toString().match(/.+?:\/\/(.+?)\//)[1];
if (host.match(/\.dev/))
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
	s += $H(options).map(function(item){
							 return ' '+item[0]+'="'+item[1]+'"'});
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
    Element.setVisible('nongraph', name != 'graph');
    Element.setVisible('input-area', name != 'help');
    Element.setVisible('replacement-area', name == 'replace');
	Element.hide.apply(null, $H(TabController.controllers).keys());
	Element.show(name);
	TabController.selected = this.controllers[name];
}

TabController.updateContents = function(patternChanged, re, input) {
	/*controller = TabController.selected;
			if (patternChanged)
				controller.updatePattern(re, input);
			else
				controller.updateInput(re, input);
				controller.updateProgramUsage(re, input);*/
	$H(TabController.controllers).values().each(
		function (controller) {
			if (patternChanged)
				controller.updatePattern(re, input);
			else
				controller.updateInput(re, input);
			controller.updateProgramUsage(re, input);
		});
}

// Instance methods (in its use as a base class)

TabController.prototype.updatePattern = function (pattern, input) {
	this.updateInput(pattern, input);
};

TabController.prototype.updateInput = function (pattern, input) {};

TabController.prototype.makeResultsList = function(ar) {
	if (!ar.length)
		return '<i>No results.</i>';
	return $A(ar).map(function(s){
						  if (!s)
							  return "<i>empty string</i>";
						  return '<tt>'+s.escapeHTML()+'</tt>';
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
	var match = input.match(re);
	if(!match) {
		$('search-summary').innerHTML = '<span class="nomatch">No match.</span>';
		$('search-details').innerHTML = '';
		return;
	}
	
	var s = '';
	var label = 'Groups';
    var prefix = '';
    var suffix = '';
	if (re.global) {
		label = 'Matches';
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
	$('search-summary').innerHTML = s;
	
	var s = '';
	if (prefix) s += 'Prefix = ' + escapeTag(prefix, 'tt')+'<br/>';
	s += 'Match = ' + (match[0] ? escapeTag(match[0], 'tt') : "'' (empty string)")+'<br/>';
	if (suffix) s += 'Suffix = ' + escapeTag(suffix, 'tt')+'<br/>';
	if (match.length) {
        s += '<br/>';
		s += contentTag(label, 'span', {style: 'font-style: italic'}) + '<br/>';
		match.each(function(m, i) {
                       if (m == undefined) return;
                       s += '$'+i+' = '+escapeTag(m, 'tt') + '<br/>';
				   });
	}
	$('search-details').innerHTML = s;
}

/*
 * Replace tab
 */
var replaceController = new TabController('replace');

replaceController.updateInput = function (re, input) {
	var sub = $F('replacement');
	this.results.innerHTML = replaceCallback(input, re, function () {return '<em>' + sub + '</em>'});
};

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
		'quantifiers': /\{/,
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
    if ($F('pattern') != this.graphView.patternSource) {
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



/*
 * Graph view
 */
function FSAView(container) {
    this.container = container;
    var canvas = document.createElement('canvas');
    container.appendChild(canvas);

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
		label.style.top = y-0;//+'px';
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
    var id = this;
	req.onreadystatechange = function(){id.processReqChange(req, pattern, success)};
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
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	this.labels.each(function(e){e.parentNode.removeChild(e)});
	this.labels = [];
    setupCanvas(canvas, this);
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

var exx = [
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


Event.observe('pattern', 'keyup', patternChanged);
Event.observe('globalCheckbox', 'click', patternChanged);
Event.observe('ignoreCaseCheckbox', 'click', patternChanged);
Event.observe('multilineCheckbox', 'click', patternChanged);
Event.observe('input', 'keyup', updateTabContents);
Event.observe('replacement', 'keyup', updateTabContents);
Event.observe('graphButton', 'click', function(){graphController.requestGraph($F('pattern'))});
Event.observe('shrinkInput', 'click', function(){resizeTextArea(-1)});
Event.observe('expandInput', 'click', function(){resizeTextArea(1)});

function resizeTextArea(d) {
    var e = $('input');
    var n = e.rows;
    if (d == -1 && n > 1) n = Math.floor(n/2);
    if (d == 1) n *= 2;
    e.rows = n;
    Element.setVisible('shrinkInput', n > 1);
}

/*
 * Initialization
 */

function implementsCanvas() {
    try {
        HTMLCanvasElement;
        return true;
    } catch (e) {
        return false;
    }
}

if (implementsCanvas) {
	Element.show($('graphArea'));
	var canvas = $("canvas");
    graphController.graphView = new FSAView($('graphContainer'));
	if (!graphController.checkPattern($F('pattern')))
		graphController.requestGraph($F('pattern'));
} else {
	Element.hide($('graphTabLabel'));
}

TabController.select($('searchTab'));
updateTabContents(true);

function createLegend() {
    var s = '';
    for (var i = 0; i < exx.length;) {
        var a = exx[i++];
        var b = exx[i++];
        s += contentTag(a, 'dt');
        s += contentTag(b, 'dd');
    }
    s = contentTag(s, 'dl');
    s = s.replace(/expr/g, '<var>expr</var>');
    $('key').innerHTML = s;
}

createLegend();
