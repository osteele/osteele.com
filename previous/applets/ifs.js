/* Copyright 2007.  Available under the MIT License. */

setInterval(drawFrame, 50);

var matrices = [
    {weight: .01, x:[0, 0, 0],        y:[0, 0.16, 0]},
    {weight: .07, x:[0.2, -0.26, 0],  y:[0.23, 0.22, 1.6]},
    {weight: .07, x:[-0.15, 0.28, 0], y:[0.26, 0.24, 0.44]},
    {weight: .85, x:[0.85, 0.04, 0],  y:[-0.04, 0.85, 1.6]}
];

var hue = 0;

function drawFrame() {
    var canvas = document.getElementById('myname');
    var context = canvas.getContext("2d");

    fadeOldFrame();
    nextState();
    drawTree();

    function fadeOldFrame() {
        context.fillStyle = 'rgba(255, 255, 255, 0.03)';
        context.fillRect(0, 0, 1000, 1000);
    }

    function nextState() {
        var sy = Math.random() / 10 - .05;
        sy = Math.sin(new Date().getTime() / 4000) / 10;
        matrices[3].x[1] = sy;
        matrices[3].y[0] = -sy;

        hue += 5;
        hue %= 360;
        var hsv = [hue, 1, 1],
            rgb = hsv2rgb(hsv),
            colorString = rgb2css(rgb);
        context.fillStyle = colorString;
    }

    function drawTree() {
        var pt = {x: 0, y: 0};
        for (i = 0; i < 500; i++) {
            var matrix = choose(matrices);
            pt = applyMatrix(matrix, pt);
            var x = .5 + pt.x/5,
                y = 1-pt.y/12,
                w = 1,
                s = 400;
            context.fillRect(s*x + 190, s*y, w, w);
        }
    }
}

function choose(list) {
    var r = Math.random(),
        ix = 0;
    while ((r -= list[ix].weight) > 0)
        ix += 1;
    return list[ix];
}

function applyMatrix(matrix, pt) {
    return {x:applyRow(matrix.x, pt), y:applyRow(matrix.y, pt)};
    function applyRow(row, pt) {
        return row[0]*pt.x + row[1]*pt.y + row[2];
    }
}

function hsv2rgb(hsv) {
    var h = ((hsv[0] % 360) + 360) % 360,
        s = Math.min(1, Math.max(0, hsv[1])),
        v = Math.min(1, Math.max(0, hsv[2]));
    if (s == 0) return [v, v, v];
    h = h / 60.0; // sector 0 to 5
    var i = Math.floor(h);
    var f = h - i;
    var p = v * (1 - s);
    var q = v * (1 - s * f);
    var t = v * (1 - s * (1 - f));
    return rgb = [[v,t,p],[q,v,p],[p,v,t],[p,q,v],[t,p,v],[v,p,q]][i % 6];
}

function rgb2css(rgb) {
    return ['rgb(', Math.floor(rgb[0]*255),
            ',', Math.floor(rgb[1]*255),
            ',', Math.floor(rgb[2]*255), ')'].join('');
}
