/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  License: MIT License.
  
  Agenda:
  - get it working in the base case
  - add siblings
  - several definitions
  - nested definitions
  - recursive definitions
  
  Features:
  - separate definition file
  - add table layout

  Corners:
  - test on IE
  
  Finally:
  - docs
*/

var OSStructure = {};

OSStructure.getClassDefinitions = function () {
	if (this._definitions) return this._definitions;
	var parent = document.getElementById('definitions');
	var definitions = {};
	if (!parent) return this._definitions = definitions;
	for (var node = parent.firstChild; node; node = node.nextSibling )
		if (node.className && node.className.match(/^\w+$/))
			definitions[node.className] = node;
	return this._definitions = definitions;
};

OSStructure.findClassDefinition = function (elt) {
	//info(elt);
	if (!elt.className) return;
	var classNames = elt.className.split(/\s+/)
	//info('class', classNames);
	var definitions = this.getClassDefinitions();
	for (var i = 0, className; className = classNames[i++]; )
		if (definitions[className]) return definitions[className];
};

OSStructure.inDefinitionSection = function (elt) {
	for (; elt; elt = elt.parentNode)
		if (elt.id == 'definition')
			return true;
	return false;
};

OSStructure.getInstances = function () {
	var instances = [];
	var elements = document.getElementsByTagName('*');
	for (var i = 0, e; e = elements[i++]; )
		if (!this.inDefinitionSection(e) && this.findClassDefinition(e))
			instances.push(e);
	return instances;
};

function trail(node) {
	if (node == document) return 'document';
	var s = node.tagName;
	if (node.id) {
        try {if (typeof $ == 'function') return "$('" + node.id + "')"}
        catch(e) {}
        s += '#' + node.id;
    }
	else if (node.className) s += '.' + node.className.replace(/\s.*/, '');
	if (node.parentNode) {
		if (s == node.tagName && !s.match(/^(html|head|body)$/i)) {
			var i = 1;
			for (var sibling = node.parentNode.firstChild; sibling != node; sibling = sibling.nextSibling)
				if (node.tagName == sibling.tagName)
					i++;
			s += '[' + i + ']';
		}
		s = (node.parentNode == document ? '' : trail(node.parentNode))+'/'+s;
	}
	return s;
}

OSStructure.removeIds = function(node) {
    if (!arguments.callee.seed) arguments.callee.seed = 0;
    if (node.id) node.id += '-' + (arguments.callee.seed += 1);
    for (var i = 0, child; child = node.childNodes[i++]; )
        if (child instanceof Element)
            OSStructure.removeIds(child);
};

OSStructure.applyClassDefinition = function (definition, instance) {
	var copy = definition.cloneNode(true);
    gCopy = copy;
    OSStructure.removeIds(copy);
	copy.innerHTML = "I'm a clone";
	info('copy',trail(copy),trail(definition),trail(instance));
	//instance.parentNode.insertBefore(copy, instance);
	info('instance=',trail(instance));
	info('parent=',trail(instance.parentNode));
	instance.parentNode.appendChild(copy);
	//instance.parentNode.removeChild(instance);
	//this.replace(copy, 'content', instance);
};

OSStructure.replace = function(node, className, instance) {
	error('check insertChild');
	if (node.className && node.className.match(new RegExp('\\b' + className + '\\b'))) {
		node.parentNode.insertChild(instance);
		node.deleteNode(node);
		return true;
	}
	for (var child = node.firstChild; child; child = child.nextSibling)
		if (this.replace(child, className, instance))
			return true;
	return false;
};

OSStructure.expandClassDefinitions = function () {
	if (this != OSStructure) return arguments.callee.apply(OSStructure, arguments);
	var instances = this.getInstances();
	for (var i = 0, instance; instance = instances[i++]; ) {
		var definition = this.findClassDefinition(instance);
		this.applyClassDefinition(definition, instance);
	}
};

if (window.addEventListener) {
    window.addEventListener('load', OSStructure.expandClassDefinitions, false);
} else if (window.attachEvent) {
    window.attachEvent('onload', OSStructure.expandClassDefinitions);
} else {
    window.onload = (function() {
        var nextfn = window.onload || function(){};
        return function() {
            OSStructure.expandClassDefinitions();
            nextfn();
        }
    })();
}
