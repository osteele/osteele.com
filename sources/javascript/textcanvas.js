/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/sources/javascript
  License: MIT License.
  
  Description:
    TextCanvasController provides an API similar to that of the
    canvas element, but with the addition of a method
      controller.getContext('2d').drawString(x, y, string);
    that can be used to position strings on the canvas.
    
    The strings are implemented as HTML spans, which are
    positioned absolutely in front of the canvas.  They
    therefore don't behave exactly as though they were on
    the canvas:
    - they don't respect the current transform
    - they don't respect 
    - nontext elements that are drawn subsequent to
      a string will be positioned over it, not under it
    (This last point could be fixed by changing the implementation
    to create a context that proxies the basis canvas, but creates
    a new canvas overlay at the start of each drawing command
    that follows a call to drawString and switches to proxying that
    instead.)
  
  API:
    === TextCanvasController
    textCanvasController = new TextCanvasController(container)
      Container is an HTML div.  The canvas and string spans
      will be placed inside this div.
    textCanvasController.setDimension(width, height)
      Set the width and height of the canvas.
    context = textCanvasController.getContext('2d')
      Returns a 2D context, modified to accept the following methods:
    
    === textCanvasController.getContext('2d')
    context.drawString(x, y, string)
      Draw string at (x, y), with the font and text style properties
      specified in context.style (below).
    context.erase()
      Erase the content of the canvas.  This is equivalent to
      context.clearRect(0, 0, canvas.width, canvas.height),
      except that it also removes any strings created by
      context.drawString().
    context.style
      An instance of ElementCSSInlineStyle.  Calls to drawString
      use the font and text properties in this style object.  (This
      API is analogous to the stateful mechanism that the 2d context
      provides for setting stroke and fill properties.)
      
      This implementation uses the container's style object
      for this.  This won't have any effect if you only set the
      font and style properties, but will have surprising results
      if you set other properties.
      
  Usage:
    // <div id="canvasContainer"></div>
    var container = document.getElementById('canvasContainer');
    var textCanvasController = new TextCanvasController(container);
    var ctx = textCanvasController.getContext('2d');
    ctx.moveTo(0, 0);
    ctx.lineTo(100, 100);
    ctx.stringStyle.color = 'red';
    ctx.drawString(0, 0, "red");
    ctx.stringStyle.color = 'blue';
    ctx.drawString(100, 100, "blue");
  
  There is a standalone example at
  http://osteele.com/sources/javascript/textcanvas-demo.html.
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
	};
    
    ctx.stringStyle = controller.container.style;
};

TextCanvasController.CSSStringProperties = 'color direction fontFamily fontSize fontSizeAdjust font-stretch font-style fontVariant fontWeight fontStyle font-variant font-weight letter-spacing  line-height text-align text-decoration text-indent text-shadow text-transform unicode-bidi white-space word-spacing'.split(' ');

TextCanvasController.prototype.addLabel = function(x, y, string) {
	var label = document.createElement('div');
	var text = document.createTextNode(string);
	label.appendChild(text);
    var style = this.container.style;
    var cssNames = TextCanvasController.CSSStringProperties;
    for (var i = 0; i < cssNames.length; i++) {
        var name = cssNames[i];
        label.style[name] = style[name];
    }
	label.style.position = 'absolute';
	label.style.left = x;
	label.style.top = y;
	this.container.appendChild(label);
	this.labels.push(label);
}