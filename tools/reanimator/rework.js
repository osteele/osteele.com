/* Copyright 2006 Oliver Steele.  All rights reserved. */

/*
  Tabs:
  - Scan
  
  Graph:
  - resize the graph
  - link to reanimator
  
  Polish:
  - text area
  - server error
  - scrim while loading graph
  - document the language
  
  Deploy:
  - fix the Python syntax
  - conditionalize canvas
  - link to blog entry
  
  Features:
  - java example
  - global, ignoreCase, multiline
  - intersection, union, complement
*/

var host = document.location.toString().match(/.+?:\/\/(.+?)\//)[1];
if (host.match(/\.dev/)) {
	Element.show($('debugger'));
}

function requestPattern(pattern) {
	pattern = preparePattern(pattern);
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
	ctx.labels = [];
	new GraphView(graph).render(ctx);
}

function showMatch() {
	Element.hide('match', 'nomatch', 'error');
	try {
		var re = RegExp($F('pattern'));
	} catch (e) {
		Element.show('error');
		$('error').innerHTML = '' + e.message + '<br/><br/>';
		return;
	}
	var match = $F('input').match(re);
	var input = $F('input');
	if(!match) {
		Element.show('nomatch');
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
	e.innerHTML += 'Match = ' + (match[0] ? esc(match[0]) : "'' (empty string)")+'<br/>';
	if (suffix) e.innerHTML += 'Suffix = ' + esc(suffix)+'<br/>';
	if (match.length>1) {
		e.innerHTML += '<br/><i>Groups:</i><br/>';
		match.each(function(m, i) {
					   if (i)
						   e.innerHTML += '$'+i+' = '+esc(m);
				   });
	}
}

function updateProgramUsage() {
	var re = $F('pattern');
	var s = $F('input');
	var e = $('programUsage');
	try {
		RegExp(re);
	} catch (e) {
		Element.hide(e);
		return;
	}
	Element.show(e);
	re = re.replace(/\\/, '\\\\');
	re = re.replace('/', '\/');
	re = re.escapeHTML();
	s = s.escapeHTML();
	var syntaxes = [
		'PHP', 'preg_match(\'/' + re + '/\', ' + s + ', &match)',
		'Python', 'match = re.match(r\'' + re + '\', '+s+')',
		'Ruby', s + ' =~ /' + re + '/'
		];
	html = '<div><strong>Usage:</strong></div><table>';
	for (var i = 0; i < syntaxes.length; ) {
		var name = syntaxes[i++];
		var syntax = syntaxes[i++];
		html += '<tr><td>'+name+'</td><td><tt>'+syntax+'</tt></td></tr>';
	}
	e.innerHTML = html + '</table>';
}

function updateGraphButton() {
	var pattern = $F('pattern');
	var e = checkPattern(pattern);
	if (e) {
		$('graphButton').disabled = true;
		Element.show('noGraph');
		if (e != ' ') e = '(The "Graph" button is disabled because the graphing engine doesn\'t handle ' + e + '.)';
		$('noGraph').innerHTML = e;
	} else {
		$('graphButton').disabled = false;
		Element.hide('noGraph');
	}
}

function checkPattern(s) {
	try {
		RegExp(s);
	} catch (e) {
		return ' ';
	}
	s = s.replace(/\\[^bB\d]/, '');
	s = s.replace(/\\[^bB\d''`&]/, '');
	s = s.replace(/$$/, '');
	var e = {
		'quantifiers': /\{/,
		'anchors': /\\[bB]/,
		'assertions': /\(\?[=!]/,
		'back-references': /\\[\d''`&]/ 
	}
	for (var p in e) {
		var m = s.match(RegExp(e[p]));
		//info(e[p]+','+s+','+m);
		if (m) {
			return p.escapeHTML() + ', such as "<kbd>' + m[0].escapeHTML() + '</kbd>"';
		}
	}
}

function preparePattern(s) {
	s = s.replace(/^\.(?!\.\*)/, '.*');
	return s;
}

function handle(request) {
	info(request.responseText);
	var result = JSON.parse(request.responseText);
	if (typeof result == 'string') {
		warn(result);
	}
	info(result);
}

String.prototype.scan = function(re) {
	var results = [];
	var i = 0;
	while (i < this.length) {
		var m = this.slice(i).match(re);
		if (!m) return results;
		results.push(m[0]);
		i += m.index + m[0].length;
		if (m.index + m[0].length == 0) i++;
	}
};

function updateTabContents(patternChanged) {
	showMatch();
	updateProgramUsage();
	if (patternChanged) updateGraphButton();
	try {
		var re = RegExp($F('pattern'));
	} catch (e) {info(e.message);}
	var s = $F('input');
	function w(ar) {
		return $A(ar).map(function(s){
							  if (!s)
								  return "'' (empty string)";
							  return '<tt>'+s.escapeHTML()+'</tt>';
						  }).join('<br/>');
	}
	$('replaced').innerHTML = s.replace(re, $F('replacement')).escapeHTML();
	$('splitted').innerHTML = w(s.split(re));
	$('scanned').innerHTML = w(s.scan(re));
}

Event.observe('pattern', 'keyup', function(){updateTabContents(true);});
Event.observe('input', 'keyup', updateTabContents);
Event.observe('replacement', 'keyup', updateTabContents);

updateTabContents(true);

function setupCanvas(canvas) {
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
		document.getElementById('cp').appendChild(label);
		ctx.labels.push(label);
	};
	
	ctx.labels = [];
	return ctx;
}

if (true) {
	Element.show($('graphArea'));
	var canvas = $("canvas");
	var ctx = setupCanvas(canvas);
	if (!checkPattern($F('pattern')))
		requestPattern($F('pattern'));
}

function ff(name) {
	Element.hide('search', 'replace', 'scan', 'ggraph', 'split');
	Element.show(name);
}

ff('search');
