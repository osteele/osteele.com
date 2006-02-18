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

LzDrawView.prototype.cubicBezierTo = function(x1, y1, x2, y2, x3, y3) {
	// recover the last point
	// TODO: if there isn't one
	var instr = this.__path[this.__path.length - 1];
	var x0 = instr[instr.length - 2];
	var y0 = instr[instr.length - 1];
    var points = [{x: x0, y: y0}, c1 = {x: x1, y: y1}, c2 = {x: x2, y: y2}, c3 = {x: x3, y: y3}];
    var queue = [points];
    function distance(a,b) {
        var dx = a.x-b.x;
        var dy = a.y-b.y;
        return Math.sqrt(dx*dx+dy*dy);
    }
    var limit = 0;
    while (queue.length) {
        points = queue.pop();
        var chordLength = distance(points[0], points[3]);
        var polyLength = 0;
        for (var i = 0; i < 3; i++)
            polyLength += distance(points[i], points[i+1]);
        if (polyLength - chordLength < 5 && chordLength < 10) {
            this.lineTo(points[3].x, points[3].y);
            continue;
        }
        // subdivide the bezier
        var m = [points, [], [], []];
        for (var i = 1; i <= 3; i++) {
            for (var j = 0; j <= 3 - i; j++) {
                var c0 = m[i-1][j];
                var c1 = m[i-1][j+1];
                m[i][j] = {x: (c0.x + c1.x)/2,
                           y: (c0.y + c1.y)/2};
            }
        }
        var left = new Array(4), right = new Array(4);
        for (i = 0; i <= 3; i++) {
            left[i]  = m[i][0];
            right[i] = m[3-i][i];
        }
        queue.push(right);
        queue.push(left);
        }
}

Array.includes = function(ar, n) {
	for (var i = 0; i < ar.length; i++)
        if (ar[i] == n) return true;
	return false;
}

Array.compact = function (ar) {
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

LzKeys.keyCodes['<'] = 188;
LzKeys.keyCodes['>'] = 190;

LzKeys.charFromCode = function(n) {
	var toUpper = "-_=+/?[{]}\\|\"',<.>";
	var ix;
	for (var c in LzKeys.keyCodes)
		if (LzKeys.keyCodes[c] == n) {
			if (LzKeys.downKeysHash[16]) {
				if (48 <= n && n <= 57)
					return ")!@#$%^&*(".charAt(n-48);
				c = c.toUpperCase();
				if ((ix = toUpper.indexOf(c)) >= 0 && (ix % 2) == 0)
					c = toUpper.charAt(ix+1);
			} else if ((ix = toUpper.indexOf(c)) >= 0)
				c = toUpper.charAt(ix-1);
			else if (48 <= n && n <= 57)
				c = "0123456789".charAt(n-48);
			return c;
		}
	return String.fromCharCode(n);
};