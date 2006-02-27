/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: MIT License.
*/

// This block creates a path for each bezier, and a single
// path that concatenates them all.  They're stored in gPaths.
// Magic numbers:
//   100 = width of a path, in both source and canvas coordinates
//     2 = scalars per points
// lift js into a functional language...
Array.map = function (ar, f) {
	var results = [];
	for (var i = 0; i < ar.length; i++)
        results.push(f(ar[i], i));
	return results;
};

Array.each = Array.map;

Array.partition = function (ar, n) {
	var partitions = [];
	Array.map(ar, function (item, i) {
			if (i % n == 0) partitions.push([]);
			partitions[partitions.length-1].push(item);
		});
	return partitions;
};

// easy to type, cumbersome to use...
var gPathData = [
	[0,0, 100,15],
	[0,15, 50,50, 100,0],
	[0,0, 20,25, 80,75, 100,10]];

// so turn them into {x: y:} lists, and adjust the ranges
var rowHeight = 60;
var gPoints = Array.map(
	gPathData,
	function (pts, i) {
		return Array.map(Array.partition(pts, 2),
						 function (item) {
							 return {x: item[0], y: item[1]}})});

// and make some Beziers:
var gBeziers = Array.map(gPoints, function(pts) {return new Bezier(pts)});
Array.map(gBeziers, function(bezier, i) {
		bezier.points = Array.map(bezier.points, function (pt) {
				return {x: 3*pt.x, y: pt.y + i * rowHeight}})});

// and then paths:
var gPaths = Array.map(gBeziers, function(bezier) {
		return new Path([bezier])});

// and finally a path that strings them all together
var catPath = new Path();
Array.map(gPoints, function (points, i) {
		var pts = Array.map(points, function (pt) {
				return {x: 3*(100*i+pt.x)/gBeziers.length, y: pt.y+rowHeight*gBeziers.length}});
		catPath.addBezier(new Bezier(pts));
    });
gPaths.push(catPath);

function drawBeziers(ctx) {
	ctx.beginPath();
	for (var i = 0; i <= 300; i += 20 ) {
		ctx.moveTo(i, 0);
		ctx.lineTo(i, 240);
	}
	for (var i = 0; i <= 240; i += 20 ) {
		ctx.moveTo(0, i);
		ctx.lineTo(300, i);
	}
	ctx.strokeStyle = 'blue';
	ctx.globalAlpha = 0.25;
	ctx.stroke();
	
	ctx.beginPath();
	Array.each(gPaths, function (path) {path.draw(ctx)});
	ctx.lineWidth = 2;
	ctx.globalAlpha = 1;
	ctx.strokeStyle = 'black';
	ctx.stroke();
	
	// draw the tangents
	ctx.beginPath();
	Array.each(gBeziers, function (bezier) {
			if (bezier.order > 2) {
				var pts = bezier.points;
				ctx.moveTo(pts[0].x, pts[0].y);
				ctx.lineTo(pts[1].x, pts[1].y);
				ctx.moveTo(pts[pts.length-2].x, pts[pts.length-2].y);
				ctx.lineTo(pts[pts.length-1].x, pts[pts.length-1].y);
			}
        });
	ctx.lineWidth = 1;
	ctx.strokeStyle = 'blue';
	ctx.stroke();
	
	// draw the control points
	Array.each(gBeziers, function (bezier) {
			Array.each(bezier.points, function (pt) {
					ctx.beginPath();
					ctx.arc(pt.x, pt.y, 3, 0, 2*Math.PI, true);
					ctx.fillStyle = 'green';
					ctx.fill();
				})});
}
	
function drawBezierSamples(ctx, t) {
	Array.each(gPaths, function (path) {
			var pt = path.atT(t);
			ctx.beginPath();
			ctx.arc(pt.x, pt.y, 3, 0, 2*Math.PI, true);
			ctx.fillStyle = 'red';
			ctx.fill();
		});
}
