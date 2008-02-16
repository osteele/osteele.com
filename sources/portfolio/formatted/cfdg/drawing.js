<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
<HEAD>
<TITLE>drawing.js</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#1F00FF" ALINK="#FF0000" VLINK="#9900DD">
<A NAME="top">
<A NAME="file1">
<H1>cfdg/drawing.js</H1>

<PRE>
<I><FONT COLOR="#B22222">/* Copyright 2006 by Oliver Steele.  All rights reserved. */</FONT></I>

<B><FONT COLOR="#A020F0">var</FONT></B> Context = <B><FONT COLOR="#A020F0">function</FONT></B> (model) {
	<B><FONT COLOR="#A020F0">this</FONT></B>.model = model;
	<B><FONT COLOR="#A020F0">this</FONT></B>.transform = <B><FONT COLOR="#A020F0">new</FONT></B> Transform;
    <B><FONT COLOR="#A020F0">this</FONT></B>.graphics = <B><FONT COLOR="#A020F0">new</FONT></B> Graphics;
    <B><FONT COLOR="#A020F0">this</FONT></B>.color = [0,0,0, 1];
	<B><FONT COLOR="#A020F0">this</FONT></B>.queue = [];
    <B><FONT COLOR="#A020F0">this</FONT></B>.stats = {rules: 0, cutoff: .0005};
};

Context.prototype = {
	clone: <B><FONT COLOR="#A020F0">function</FONT></B> () {
		<B><FONT COLOR="#A020F0">var</FONT></B> clone = <B><FONT COLOR="#A020F0">new</FONT></B> Context(<B><FONT COLOR="#A020F0">this</FONT></B>.model);
        clone.transform = <B><FONT COLOR="#A020F0">this</FONT></B>.transform.clone();
        clone.graphics = <B><FONT COLOR="#A020F0">this</FONT></B>.graphics;
        clone.color = [].concat(<B><FONT COLOR="#A020F0">this</FONT></B>.color);
		clone.queue = <B><FONT COLOR="#A020F0">this</FONT></B>.queue;
        clone.stats = <B><FONT COLOR="#A020F0">this</FONT></B>.stats;
		<B><FONT COLOR="#A020F0">return</FONT></B> clone;
	},
	invoke: <B><FONT COLOR="#A020F0">function</FONT></B> (name) {
        <B><FONT COLOR="#A020F0">if</FONT></B> (<FONT COLOR="#DA70D6"><B>Math</FONT></B>.abs(<B><FONT COLOR="#A020F0">this</FONT></B>.transform.determinant()) &lt; <B><FONT COLOR="#A020F0">this</FONT></B>.stats.cutoff) <B><FONT COLOR="#A020F0">return</FONT></B>;
		<I><FONT COLOR="#B22222">//this.model.draw(this, name);
</FONT></I>        <B><FONT COLOR="#A020F0">this</FONT></B>.queue[<B><FONT COLOR="#A020F0">this</FONT></B>.queue.length] = [<B><FONT COLOR="#A020F0">this</FONT></B>, name];
	},
    flush: <B><FONT COLOR="#A020F0">function</FONT></B> (time) {
        <B><FONT COLOR="#A020F0">var</FONT></B> stopTime = (<B><FONT COLOR="#A020F0">new</FONT></B> <FONT COLOR="#DA70D6"><B>Date</FONT></B>).getTime() + time;
        <B><FONT COLOR="#A020F0">while</FONT></B> (<B><FONT COLOR="#A020F0">this</FONT></B>.queue.length &amp;&amp; (<B><FONT COLOR="#A020F0">new</FONT></B> <FONT COLOR="#DA70D6"><B>Date</FONT></B>).getTime() &lt; stopTime) {
            <B><FONT COLOR="#A020F0">var</FONT></B> item = <B><FONT COLOR="#A020F0">this</FONT></B>.queue.shift();
            item[0].model.draw(item[0], item[1]);
            <B><FONT COLOR="#A020F0">this</FONT></B>.stats.rules += 1;
        }
    },
	setBackground: <B><FONT COLOR="#A020F0">function</FONT></B> (hsv) {
		<B><FONT COLOR="#A020F0">this</FONT></B>.graphics.setBackgroundHSV(hsv);
	},
    drawPolygon: <B><FONT COLOR="#A020F0">function</FONT></B> (points) {
        <B><FONT COLOR="#A020F0">var</FONT></B> points = <B><FONT COLOR="#A020F0">this</FONT></B>.transform.transformPoints(points);
        <B><FONT COLOR="#A020F0">this</FONT></B>.graphics.setHSV(<B><FONT COLOR="#A020F0">this</FONT></B>.color);
		<B><FONT COLOR="#A020F0">this</FONT></B>.graphics.drawPolygon(points);
    },
	drawCircle: <B><FONT COLOR="#A020F0">function</FONT></B> (center, radius) {
        <B><FONT COLOR="#A020F0">this</FONT></B>.graphics.setHSV(<B><FONT COLOR="#A020F0">this</FONT></B>.color);
		<B><FONT COLOR="#A020F0">this</FONT></B>.graphics.drawCircle(center, radius, <B><FONT COLOR="#A020F0">this</FONT></B>.transform);
	},
    transform: <B><FONT COLOR="#A020F0">function</FONT></B> (points) {<B><FONT COLOR="#A020F0">return</FONT></B> <B><FONT COLOR="#A020F0">this</FONT></B>.transform.transform(points)},
    set_x: <B><FONT COLOR="#A020F0">function</FONT></B> (dx) {<B><FONT COLOR="#A020F0">this</FONT></B>.transform.pretranslate(dx, 0)},
    set_y: <B><FONT COLOR="#A020F0">function</FONT></B> (dy) {<B><FONT COLOR="#A020F0">this</FONT></B>.transform.pretranslate(0, dy)},
	set_size: <B><FONT COLOR="#A020F0">function</FONT></B> (size) {<B><FONT COLOR="#A020F0">this</FONT></B>.transform.prescale(size[0], size[1])},
	set_rotate: <B><FONT COLOR="#A020F0">function</FONT></B> (r) {<B><FONT COLOR="#A020F0">this</FONT></B>.transform.prerotate(r*<FONT COLOR="#DA70D6"><B>Math</FONT></B>.PI/180);},
	set_skew: <B><FONT COLOR="#A020F0">function</FONT></B> (skew) {
		t = <B><FONT COLOR="#A020F0">new</FONT></B> Transform;
		t.m[0][1] = <FONT COLOR="#DA70D6"><B>Math</FONT></B>.tan(skew[0]*<FONT COLOR="#DA70D6"><B>Math</FONT></B>.PI/180);
		t.m[1][0] = <FONT COLOR="#DA70D6"><B>Math</FONT></B>.tan(skew[1]*<FONT COLOR="#DA70D6"><B>Math</FONT></B>.PI/180);
		<B><FONT COLOR="#A020F0">this</FONT></B>.transform.premultiply(t);
	},
    set_flip: <B><FONT COLOR="#A020F0">function</FONT></B> (r) {
        r *= <FONT COLOR="#DA70D6"><B>Math</FONT></B>.PI/180;
        <B><FONT COLOR="#A020F0">this</FONT></B>.transform.prerotate(-r);
        <B><FONT COLOR="#A020F0">this</FONT></B>.transform.prescale(1, -1);
        <B><FONT COLOR="#A020F0">this</FONT></B>.transform.prerotate(r);
    },
    set_hue: <B><FONT COLOR="#A020F0">function</FONT></B> (h) { <B><FONT COLOR="#A020F0">this</FONT></B>.color[0] += h; },
    set_sat: <B><FONT COLOR="#A020F0">function</FONT></B> (s) { <B><FONT COLOR="#A020F0">this</FONT></B>.color[1] = s; },
    set_brightness: <B><FONT COLOR="#A020F0">function</FONT></B> (b) { <B><FONT COLOR="#A020F0">this</FONT></B>.color[2] += <B><FONT COLOR="#A020F0">this</FONT></B>.color[2]*b; },
    set_alpha: <B><FONT COLOR="#A020F0">function</FONT></B> (a) { <B><FONT COLOR="#A020F0">this</FONT></B>.color[3] += <B><FONT COLOR="#A020F0">this</FONT></B>.color[3]*a; }
};

Model.prototype.draw = <B><FONT COLOR="#A020F0">function</FONT></B> (context, name) {
	context.setBackground(<B><FONT COLOR="#A020F0">this</FONT></B>.background);
	<B><FONT COLOR="#A020F0">if</FONT></B> (!name) name = <B><FONT COLOR="#A020F0">this</FONT></B>.startName;
    <B><FONT COLOR="#A020F0">var</FONT></B> rule = <B><FONT COLOR="#A020F0">this</FONT></B>.choose(name);
    rule &amp;&amp; rule.draw(context);
    context.flush();
};

Rule.prototype.draw = <B><FONT COLOR="#A020F0">function</FONT></B> (context) {
	<B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> i = 0; i &lt; <B><FONT COLOR="#A020F0">this</FONT></B>.calls.length; i++)
		<B><FONT COLOR="#A020F0">this</FONT></B>.calls[i].draw(context);
};

Call.prototype.draw = <B><FONT COLOR="#A020F0">function</FONT></B> (context) {
	<B><FONT COLOR="#A020F0">if</FONT></B> (<B><FONT COLOR="#A020F0">this</FONT></B>.attributes.length)
		context = context.clone();
    <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> i = 0; i &lt; <B><FONT COLOR="#A020F0">this</FONT></B>.attributes.length; i++)
		context[<FONT COLOR="#BC8F8F"><B>'set_'</FONT></B> + <B><FONT COLOR="#A020F0">this</FONT></B>.attributes[i][0]](<B><FONT COLOR="#A020F0">this</FONT></B>.attributes[i][1]);
	<B><FONT COLOR="#A020F0">if</FONT></B> (Shapes[<B><FONT COLOR="#A020F0">this</FONT></B>.name])
		<B><FONT COLOR="#A020F0">return</FONT></B> Shapes[<B><FONT COLOR="#A020F0">this</FONT></B>.name](context);
    context.invoke(<B><FONT COLOR="#A020F0">this</FONT></B>.name);
};

<B><FONT COLOR="#A020F0">var</FONT></B> Sqrt3 = <FONT COLOR="#DA70D6"><B>Math</FONT></B>.sqrt(3);

<B><FONT COLOR="#A020F0">var</FONT></B> Shapes = {
	CIRCLE: <B><FONT COLOR="#A020F0">function</FONT></B> (context) {
		<B><FONT COLOR="#A020F0">if</FONT></B> (<FONT COLOR="#DA70D6"><B>Math</FONT></B>.abs(context.transform.determinant()) &lt; context.stats.cutoff*2)
            <B><FONT COLOR="#A020F0">return</FONT></B> <B><FONT COLOR="#A020F0">this</FONT></B>.SQUARE(context);
		context.drawCircle([0,0],0.5);
	},
	SQUARE: <B><FONT COLOR="#A020F0">function</FONT></B> (context) {
		<B><FONT COLOR="#A020F0">var</FONT></B> pts = [[-.5,-.5], [-.5,.5], [.5,.5], [.5,-.5]];
		context.drawPolygon(pts);
	},
	TRIANGLE: <B><FONT COLOR="#A020F0">function</FONT></B> (context) {
        <B><FONT COLOR="#A020F0">var</FONT></B> y = -0.5/Sqrt3;
		<B><FONT COLOR="#A020F0">var</FONT></B> pts = [[-.5,y], [.5,y], [0, y+Sqrt3/2]];
		context.drawPolygon(pts);
	}
}</PRE>
<HR>
<ADDRESS>Generated by <A HREF="http://www.iki.fi/~mtr/genscript/">GNU enscript 1.6.1</A>.</ADDRESS>
</BODY>
</HTML>
