/* Copyright 2006 Oliver Steele.  All rights reserved. */

/*
  Graph:
  - resize the graph
  - add .* unless it starts with ^
  - add magic chars for \A, \Z, \<, \>, $
  - errors for the cases it doesn't handle
  - remove the old graph on submit
  
  Polish:
  - link to reanimator
  - server error
  - scrim while loading graph
  - document the language
  
  Deploy:
  - conditionalize canvas
  - link to blog entry
  
  Features:
  - global, ignoreCase, multiline
  - generate python, perl, php, ruby, javascript, java
  - intersection, union, complement
*/

var host = document.location.toString().match(/.+?:\/\/(.+?)\//)[1];
if (host.match(/\.dev/)) {
	Element.show($('debugger'));
}

var canvas = $("canvas");
var ctx = canvas.getContext("2d");

Element.show($('graphArea'));

function setPattern(pattern) {
	var url="server.py?pattern="+encodeURIComponent(pattern);
	var req = new XMLHttpRequest();
	gReq = req;
	req.onreadystatechange = function(){processReqChange(req)};
	req.open("GET", url, true);
	req.send(null);
}

function processReqChange(request) {
	if (request.readyState == 4) {
        if (request.status == 200) {
			var result = JSON.parse(request.responseText);
			if (typeof result == 'string') {
				warn(result);
			}
			showGraph(result.dfa.graph);
        }
	}
}

function showGraph(graph) {
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	ctx.labels.each(function(e){e.parentNode.removeChild(e)});
	new GraphView(graph).render(ctx);
}

ctx.labels = [];

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
	document.getElementById('cp').appendChild(label);
	ctx.labels.push(label);
}

//setPattern('a*b');

function showMatch() {
	Element.hide('match', 'nomatch', 'error');
	try {
		var re = RegExp($F('pattern'));
	} catch (e) {
		Element.show('error');
		$('error').innerHTML = '' + e;
		return;
	}
	var match = $F('input').match(re);
	var input = $F('input');
	if(!match) {
		Element.show('nomatch');
		//$('nomatch').innerHTML = re + ' does not match ' + input;
		return;
	}
	//$A($('match').childNodes).each(function(m){Element.remove(m)});
	Element.show('match');
	var n = match[0].length;
	var prefix = input.slice(0,match.index);
	var suffix = input.slice(match.index+n);
	var e = $('overview');
	function esc(str, cssClass) {
		str = '<tt>'+str.escapeHTML()+'</tt>';
		if (cssClass)
			str = '<span class="'+cssClass+'">'+str+'</span>';
		return str;
	};
	e.innerHTML = '<kbd>'+re.toString().escapeHTML()+'</kbd>' + ' matches ' + esc(prefix, 'prefix') + '<em>' + esc(match[0]) +'</em>'+ esc(suffix, 'suffix') + '<br/>';
	var e = $('details');
	e.innerHTML = '';
	if (prefix) e.innerHTML += 'Prefix = ' + esc(prefix)+'<br/>';
	e.innerHTML += 'Match = ' + esc(match[0]) + '<br/>';
	if (suffix) e.innerHTML += 'Suffix = ' + esc(suffix)+'<br/>';
	if (match.length>1) {
		e.innerHTML += '<br/><i>Groups:</i><br/>';
		match.each(function(m, i) {
					   if (i)
						   e.innerHTML += '$'+i+' = '+esc(m);
				   });
	}
}

showMatch();

function updateGraphButton() {
	var pattern = $F('pattern');
	var e = checkPattern(pattern);
	info(e);
	if (e) {
		Element.show('noGraph');
		Element.hide('graphButton');
		$('noGraph').innerHTML = 'The graphing engine doesn\'t handle ' + e + '.';
	} else {
		Element.show('graphButton');
		Element.hide('noGraph');
	}
}

updateGraphButton();

function checkPattern(s) {
	try {
		RegExp(s);
	} catch (e) {
		return ' ';
	}
	s = s.replace(/\\[^bB\d]/, '');
	var e = {
		'quantifiers': '\\{',
		'anchors': '\\[bB',
		'assertions': '\(\?(=)',
		'back-references': '\\[\d]'
	}
	for (var p in e) {
		var m = s.match(RegExp(e[p]));
		if (m)
			return p.escapeHTML() + ' such as <kbd>' + m[0].escapeHTML() + '</kbd>';
	}
}

function preparePattern(s) {
	s = s.replace(/^\./, '.*');
	s = s.replace(/\$$/, '');
	return s;
}

Event.observe('pattern', 'keyup', function(){showMatch(); updateGraphButton();});
Event.observe('input', 'keyup', function(){showMatch()});

function handle(request) {
	info(request.responseText);
	var result = JSON.parse(request.responseText);
	if (typeof result == 'string') {
		warn(result);
	}
	info(result);
}