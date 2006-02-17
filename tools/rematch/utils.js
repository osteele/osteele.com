LzDrawView.prototype.cubicBezierTo = function(x1, y1, x2, y2, x3, y3) {
	// recover the last point
	// TODO: if there isn't one
	var instr = this.__path[this.__path.length - 1];
	var x0 = instr[instr.length - 2];
	var y0 = instr[instr.length - 1];
	// compute the midpoint of the cubic
	var c01 = {x: (x0+x1)/2, y: (y0+y1)/2};
	var c12 = {x: (x1+x2)/2, y: (y1+y2)/2};
	var c23 = {x: (x2+x3)/2, y: (y2+y3)/2};
	var c012 = {x: (c01.x+c12.x)/2, y: (c01.y+c12.y)/2};
	var c123 = {x: (c12.x+c23.x)/2, y: (c12.y+c23.y)/2};
	var cm = {x: (c012.x+c123.x)/2, y: (c012.y+c123.y)/2};
	// compute the midpoint of the quadratic
	var q0 = {x: x0, y: y0};
	var q1 = c12;
	var q2 = {x: x3, y: y3};
	var q01 = {x: (q0.x+q1.x)/2, y: (q0.y+q1.y)/2};
	var q12 = {x: (q1.x+q2.x)/2, y: (q1.y+q2.y)/2};
	var qm = {x: (q01.x+q12.x)/2, y: (q01.y+q12.y)/2};
	// approximate with a quadratic if the midpoints are less than
	// sqrt(2) pixels away, else recurse
	var dx = qm.x-cm.x;
	var dy = qm.y-cm.y;
	if (dx*dx+dy*dy < 2)
        this.quadraticCurveTo(q1.x, q1.y, q2.x, q2.y);
	else {
        this.cubicBezierTo(c01.x, c01.y, c012.x, c012.y, qm.x, qm.y);
		// could iterate here to almost halve the number of fn calls
		// (although not the stack depth)
        this.cubicBezierTo(c123.x, c123.y, c23.x, c23.y, x3, y3);
	}
}

function arrayIncludes(ar, n) {
	for (var i = 0; i < ar.length; i++)
        if (ar[i] == n) return true;
	return false;
}

function arrayCompact(ar) {
	var dst = 0;
	for (var i = 0; i < ar.length; i++) {
        ar[dst] = ar[i];
        if (ar[i] != null) dst++;
	}
	ar.length = dst;
}

function long2css(n) {
	var a = "0123456789ABCDEF";
	var s = '#';
	for (var i = 24; (i -= 4) >= 0; )
        s += a.charAt((n>>i) & 0xf);
	return s;
}
