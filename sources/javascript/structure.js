/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  License: MIT License.
  
  Agenda:
  - replace by contents of div, not by div itself?
  - Safari
  
  Features:
  - recursive definitions

  Corners:
  - IE
  
  Finally:
  - docs
  
  Future:
  - separate definition file
  - table layout
  - nested definitions
*/

var OSStructure = {};

OSStructure.getClassDefinitions = function () {
	if (this._definitions) return this._definitions;
	var parent = document.getElementById('definitions');
	var definitions = {};
	if (!parent) return this._definitions = definitions;
	for (var node = parent.firstChild; node; node = node.nextSibling )
		if (node.id)
			definitions[node.id] = node;
	return this._definitions = definitions;
};

OSStructure.findClassDefinition = function (elt) {
	if (!elt.className) return;
	var classNames = elt.className.split(/\s+/)
	var definitions = this.getClassDefinitions();
	for (var i = 0, className; className = classNames[i++]; )
		if (definitions[className]) return definitions[className];
};

OSStructure.inDefinitionSection = function (elt) {
	for (; elt; elt = elt.parentNode)
		if (elt.id == 'definitions')
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

OSStructure.removeIds = function(node) {
    if (!arguments.callee.seed) arguments.callee.seed = 0;
    if (node.id) node.id += '-' + (arguments.callee.seed += 1);
    for (var i = 0, child; child = node.childNodes[i++]; )
        if (child instanceof Element)
            OSStructure.removeIds(child);
};

OSStructure.applyClassDefinition = function (definition, instance) {
	var copy = definition.cloneNode(true);
    OSStructure.removeIds(copy);
	instance.parentNode.insertBefore(copy, instance);
	instance.parentNode.removeChild(instance);
	this.replace(copy, 'content', instance);
};

OSStructure.replace = function(node, className, instance) {
	if (node.className && node.className.match(new RegExp('\\b' + className + '\\b'))) {
		node.parentNode.insertBefore(instance, node);
		node.parentNode.removeChild(node);
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
