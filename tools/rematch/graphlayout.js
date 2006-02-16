function Graph() {
    this.nodeSet = {};
    this.nodes = [];
    this.arcs = [];
}

Graph.prototype.addNode = function(n) {
    if (this.nodeSet[n.id]) return;
    this.nodeSet[n.id] = n;
    this.nodes.push(n);
    n.radius = 10;
};

Graph.prototype.addArc = function(n1, n2, label) {
    this.addNode(n1);
    this.addNode(n2);
    this.arcs.push({source: n1, target: n2, label: label});
};

Graph.Layout = function (graph) {
    this.graph = graph;
    this.nodes = graph.nodes;
    this.springConstant = 2;
    this.springLength = 0.2;
    this.repulsion = .5;
};

Graph.Layout.prototype.layout = function () {
    this.initializePositions();
    for (var i = 0; i < 100; i++)
        this.refine();
};

Graph.Layout.prototype.initializePositions = function () {
    for (var i in this.nodes) {
        var node = this.nodes[i];
        var theta = 2*Math.PI*i/this.nodes.length;
        node.x = this.width/2*(1 + Math.cos(theta));
        node.y = this.height/2*(1 + Math.sin(theta));
    }
};

Graph.Layout.prototype.refine = function () {
    this.resetForces();
    this.addSpringForces();
    this.addRepulsiveForces();
    this.applyForces();
    this.respectBounds();
};

Graph.Layout.prototype.resetForces = function () {
    for (var i in this.nodes) {
        var node = this.nodes[i];
        node.fx = node.fy = 0;
    }
};

Graph.Layout.prototype.addSpringForces = function () {
    for (var i in this.graph.arcs) {
        var arc = this.graph.arcs[i];
        var n1 = arc.source;
        var n2 = arc.target;
        var dx = n2.x - n1.x;
        var dy = n2.y - n1.y;
        var d = Math.sqrt(dx*dx + dy*dy);
        var f = this.springConstant * (d-this.springLength);
        if (d < .01) {dx = .1; d = Math.sqrt(dx*dx + dy*dy)}
        if (n1 == fred && n2 == wilma) Debug.write('arc', n1, n2, d, f);
        if (Math.abs(f) > .01) {
            var fx = f * dx / d;
            var fy = f * dy / d;
            n1.fx += fx; n1.fy += fy;
            n2.fx -= fx; n2.fy -= fy;
        }
        //Debug.write(n1, n2, n1.fx, n1.fy);
    }
};

Graph.Layout.prototype.addRepulsiveForces = function () {
    for (var i = 0; i < this.nodes.length-1; i++) {
        var n1 = this.nodes[i];
        for (var j = i + 1; j < this.nodes.length; j++) {
            var n2 = this.nodes[j];
            var dx = n2.x - n1.x;
            var dy = n2.y - n1.y;
            var d2 = dx*dx + dy*dy;
            if (d2 < .01) {dx = .1; d2 = dx*dx + dy*dy}
            var d = Math.sqrt(d2);
            var f = this.repulsion*this.repulsion*this.springLength*this.springLength / d2;
            if (n1 == fred && n2 == wilma) Debug.write(n1,n2,d,d2,f,f*dx/d,f*dy/d);
            var fx = f * dx / d;
            var fy = f * dy / d;
            n1.fx += fx; n1.fy += fy;
            n2.fx -= fx; n2.fy -= fy;
        }
    }
};

Graph.Layout.prototype.applyForces = function () {
    var s = 20;
    for (var i in this.nodes) {
        var n = this.nodes[i];
        n.x += n.fx/s;
        n.y += n.fy/s;
        //Debug.write(n, n.fx, n.fy, n.x, n.y);
    }
};

Graph.Layout.prototype.respectBounds = function () {
    for (var i in this.nodes) {
        var n = this.nodes[i];
        n.x = Math.max(0, Math.min(this.width, n.x));
        n.y = Math.max(0, Math.min(this.height, n.y));
    }
};
