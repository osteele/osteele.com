/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: MIT License.
*/

function GraphView(graph) {
    this.graph = graph;
};

GraphView.prototype.render = function(ctx) {
    this.context = ctx;
    var radius = 17;
    var doubleradius = 21;
    
    var graph = this.graph;
    
    // draw the edges
    ctx.beginPath();
    
    for (var i = 0; i < graph.edges.length; i++) {
        var e = graph.edges[i];
        ctx.moveTo(e.pos[0].x, e.pos[0].y);
        for (var j = 1; j < e.pos.length; ) {
            var c1 = e.pos[j++];
            var c2 = e.pos[j++];
            var c3 = e.pos[j++];
            ctx.bezierCurveTo(c1.x, c1.y, c2.x, c2.y, c3.x, c3.y);
        }
    }
    ctx.lineWidth = 3;
	ctx.strokeStyle = '#c0c0c0';
    ctx.stroke();
    
    // draw the arrow heads
    var as = 12; // length of arrow head
    var da = 25; // half-angle of arrow head
    for (var i = 0; i < graph.edges.length; i++) {
        var e = graph.edges[i];
        if (e.endArrow)
            this.drawArrow(ctx, e.pos[e.pos.length-1], e.endArrow, as, as, da);
    }
    
    // draw the nodes
    ctx.beginPath();
    for (var i in graph.nodes) {
        var node = graph.nodes[i];
        this.circle(node.x, node.y, radius);
        if (node.shape=='doublecircle')
            this.circle(node.x, node.y, doubleradius);
    }
	ctx.strokeStyle = 'black';
    ctx.lineWidth = 1;
    ctx.stroke();
    
    // draw the labels
    for (var i = 0; i < graph.edges.length; i++) {
        var e = graph.edges[i];
        if (e.label)
            ctx.drawString(e.lp.x, e.lp.y, e.label);
    }
};

GraphView.prototype.circle = function(x, y, r) {
    var ctx = this.context;
    ctx.moveTo(x+r,y);
    ctx.arc(x,y,r, 0, 2*Math.PI, true);
};

GraphView.prototype.drawArrow = function(ctx, p0, p1, rx, ry, angle, inset) {
    angle *= Math.PI/180;
    ctx.beginPath();
    var theta = Math.atan2(p1.y-p0.y, p1.x-p0.x);
    ctx.moveTo(p1.x, p1.y);
    ctx.lineTo(p1.x-rx*Math.cos(theta-angle), p1.y-ry*Math.sin(theta-angle));
    if (inset) ctx.lineTo(p1.x-rx/2, p1.y);
    ctx.lineTo(p1.x-rx*Math.cos(theta+angle), p1.y-ry*Math.sin(theta+angle));
    ctx.lineTo(p1.x, p1.y);
	ctx.fillStyle = "#c0c0c0";
    ctx.fill();
}
