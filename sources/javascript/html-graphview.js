/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  License: MIT License.
*/

function HTMLGraphView(container, serverUrl) {
    this.canvasController = new TextCanvasController(container);
	this.serverUrl = serverUrl || 'graphserver';
	this.onnewgraph = function(){};
	this.onrequesterror = function(){};
	this.oninvalidresponse = function(){};
}

HTMLGraphView.prototype.requestGraph = function (url) {
	url = url || this.serverUrl;
    var self = this;
	var request = new XMLHttpRequest();
	request.onreadystatechange = function(){
		self.processRequestChange(request)
	};
	request.open("GET", url, true);
	request.send(null);
};

HTMLGraphView.prototype.processRequestChange = function(request) {
	if (request.readyState != 4)
        return;
    if (0 < request.status && request.status < 200 || 300 < request.status) {
        this.onrequesterror(request);
        return;
    }
    var result = JSON.parse(request.responseText);
    if (typeof result == 'string') {
        this.oninvalidresponse(result);
        return;
    }
    this.setGraph(result.dfa.graph);
};

HTMLGraphView.prototype.setGraph = function(graph, display) {
	if (arguments.length < 2) display = true;
	this.graph = graph;
	if (display) this.display();
};


HTMLGraphView.prototype.display = function(clear) {
	if (arguments.length < 1) clear = true;
	var graph = this.graph;
	var controller = this.canvasController;
	controller.setDimensions(graph.bb[2], graph.bb[3]);
	var ctx = controller.getContext("2d");
	if (clear)
		ctx.clear();
	new GraphView(graph).render(ctx);
};
