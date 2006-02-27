/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  License: MIT License.
*/

function TextCanvasController(container) {
    this.container = container;
	if (!container.style.position)
		container.style.position = 'relative';
    var canvas = document.createElement('canvas');
    this.canvas = canvas;
	canvas.style.position = 'absolute';
    container.appendChild(canvas);
    this.labels = [];
}

TextCanvasController.prototype.getContext = function(xxx) {
   	var ctx = this.canvas.getContext('2d');
	if (xxx == '2d')
		this.attachMethods(ctx, this);
	return ctx;
};

TextCanvasController.prototype.setDimensions = function(width, height) {
	var container = this.container;
	var canvas = this.canvas;
	this.container.style.width = canvas.width = width;
	this.container.style.height = canvas.height = height;
}

TextCanvasController.prototype.clear = function() {
    var canvas = this.canvas;
	var ctx = canvas.getContext("2d");
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	for (var i = 0; i < this.labels.length; i++)
		this.container.removeChild(this.labels[i]);
	this.labels = [];
};

TextCanvasController.prototype.attachMethods = function(ctx, controller) {
	ctx.circle = function(x, y, r) {
		this.moveTo(x+r,y);
		this.arc(x,y,r, 0, 2*Math.PI, true);
	};
	
	ctx.drawString = function(x, y, string) {
		controller.addLabel(x, y, string);
	};
	
	ctx.clear = function () {
		controller.clear();
	}
};

TextCanvasController.prototype.addLabel = function(x, y, string) {
	var label = document.createElement('div');
	var text = document.createTextNode(string);
	label.appendChild(text);
	label.style.position = 'absolute';
	label.style.left = x;
	label.style.top = y;
	this.container.appendChild(label);
	this.labels.push(label);
}