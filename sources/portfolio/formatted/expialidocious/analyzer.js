<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
<HEAD>
<TITLE>analyzer.js</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#1F00FF" ALINK="#FF0000" VLINK="#9900DD">
<A NAME="top">
<A NAME="file1">
<H1>expialidocious/analyzer.js</H1>

<PRE>
<I><FONT COLOR="#B22222">/*
Copyright 2005-2006 Oliver Steele.  Some rights reserved.
$LastChangedDate: 2006-01-07 15:24:44 -0500 (Sat, 07 Jan 2006) $

This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License:
http://creativecommons.org/licenses/by-nc-sa/2.5/.
*/</FONT></I>

<I><FONT COLOR="#B22222">/*
Implementation notes:
- This code uses the optimization that array[i]=n is faster than array.push(n)
in the AVM. &lt;http://www.openlaszlo.org/docs/guide/performance-tuning.html&gt;
 */</FONT></I>

<B><FONT COLOR="#A020F0">var</FONT></B> DataFrame = <B><FONT COLOR="#A020F0">function</FONT></B> () {
  <B><FONT COLOR="#A020F0">this</FONT></B>.rowNames = [];
  <B><FONT COLOR="#A020F0">this</FONT></B>.columnNames = [];
  <B><FONT COLOR="#A020F0">this</FONT></B>.columns = [];
  <B><FONT COLOR="#A020F0">this</FONT></B>.columnSumCache = {}; <I><FONT COLOR="#B22222">// {[start,end+1] -&gt; [sum]}
</FONT></I>  <B><FONT COLOR="#A020F0">this</FONT></B>.columnNameIndices = {}; <I><FONT COLOR="#B22222">// {Date -&gt; row_index}
</FONT></I>  <B><FONT COLOR="#A020F0">this</FONT></B>.rowNameIndices = {}; <I><FONT COLOR="#B22222">// {tagname -&gt; col_index}
</FONT></I>};

DataFrame.prototype = {
  getColumnIndex: <B><FONT COLOR="#A020F0">function</FONT></B> (name) {
    <B><FONT COLOR="#A020F0">var</FONT></B> i = <B><FONT COLOR="#A020F0">this</FONT></B>.columnNameIndices[name];
    <I><FONT COLOR="#B22222">// create new columns on demand
</FONT></I>    <B><FONT COLOR="#A020F0">if</FONT></B> (!(i &gt;= 0)) {
      i = <B><FONT COLOR="#A020F0">this</FONT></B>.columnNameIndices[name] = <B><FONT COLOR="#A020F0">this</FONT></B>.columnNames.length;
      <B><FONT COLOR="#A020F0">var</FONT></B> column = <B><FONT COLOR="#A020F0">new</FONT></B> Array(<B><FONT COLOR="#A020F0">this</FONT></B>.rowNames.length);
      <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> j = 0; j &lt; column.length; j++)
        column[j] = 0;
      <B><FONT COLOR="#A020F0">this</FONT></B>.columnNames.push(name);
      <B><FONT COLOR="#A020F0">this</FONT></B>.columns.push(column);
    }
    <B><FONT COLOR="#A020F0">return</FONT></B> i;
  },
  
  getRowIndex: <B><FONT COLOR="#A020F0">function</FONT></B> (name) {
    <B><FONT COLOR="#A020F0">var</FONT></B> j = <B><FONT COLOR="#A020F0">this</FONT></B>.rowNameIndices[name];
    <I><FONT COLOR="#B22222">// create new rows on demand
</FONT></I>    <B><FONT COLOR="#A020F0">if</FONT></B> (!(j &gt;= 0)) {
      j = <B><FONT COLOR="#A020F0">this</FONT></B>.rowNameIndices[name] = <B><FONT COLOR="#A020F0">this</FONT></B>.rowNames.length;
      <B><FONT COLOR="#A020F0">this</FONT></B>.rowNames.push(name);
      <I><FONT COLOR="#B22222">// fill up new rows, to keep the matrix rectangular
</FONT></I>      <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> i = 0; i &lt; <B><FONT COLOR="#A020F0">this</FONT></B>.columns.length; i++)
        <B><FONT COLOR="#A020F0">this</FONT></B>.columns[i][j] = 0;
    }
    <B><FONT COLOR="#A020F0">return</FONT></B> j;
  },
  
  <I><FONT COLOR="#B22222">// helper method for columnRangeSum.  This method iterates over all
</FONT></I>  <I><FONT COLOR="#B22222">// the rows.
</FONT></I>  addColumns_iterate: <B><FONT COLOR="#A020F0">function</FONT></B> (a, b) {
    <B><FONT COLOR="#A020F0">var</FONT></B> sum = <B><FONT COLOR="#A020F0">new</FONT></B> Array(<B><FONT COLOR="#A020F0">this</FONT></B>.columnNames.length);
    <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> j = 0; j &lt; sum.length; j++) sum[j] = 0;
    <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> i = a; i &lt; b; i++) {
      <B><FONT COLOR="#A020F0">var</FONT></B> column = <B><FONT COLOR="#A020F0">this</FONT></B>.columns[i];
      <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> t = 0; t &lt; column.length; t++)
        sum[t] += column[t];
    }
    <B><FONT COLOR="#A020F0">return</FONT></B> sum;
  },
  
  <I><FONT COLOR="#B22222">// helper method for columnRangeSum.  This method iterates or
</FONT></I>  <I><FONT COLOR="#B22222">// subdivides, depending on the range.  It recurses through _memoize
</FONT></I>  <I><FONT COLOR="#B22222">// to use the cache.  It only subdivides at binary subdivisions of
</FONT></I>  <I><FONT COLOR="#B22222">// the domain range ([left, right]), rather than the selection range
</FONT></I>  <I><FONT COLOR="#B22222">// ([a,b]).  This allows the cache to be reused when the selection
</FONT></I>  <I><FONT COLOR="#B22222">// changes.
</FONT></I>  addColumns_choose: <B><FONT COLOR="#A020F0">function</FONT></B> (a, b, left, right) {
    <I><FONT COLOR="#B22222">// subdivision cutoff; empirically determined
</FONT></I>    <B><FONT COLOR="#A020F0">if</FONT></B> (b - a &lt; 4) <B><FONT COLOR="#A020F0">return</FONT></B> <B><FONT COLOR="#A020F0">this</FONT></B>.addColumns_iterate(a, b);
    <B><FONT COLOR="#A020F0">var</FONT></B> middle = <FONT COLOR="#DA70D6"><B>Math</FONT></B>.floor((left + right)/2);
    <B><FONT COLOR="#A020F0">var</FONT></B> s0 = a &lt; middle &amp;&amp; <B><FONT COLOR="#A020F0">this</FONT></B>.addColumns_memoize(a, <FONT COLOR="#DA70D6"><B>Math</FONT></B>.min(b, middle-1), left, middle);
    <B><FONT COLOR="#A020F0">var</FONT></B> s1 = middle &lt;= b &amp;&amp; <B><FONT COLOR="#A020F0">this</FONT></B>.addColumns_memoize(<FONT COLOR="#DA70D6"><B>Math</FONT></B>.max(a, middle), b, middle, right);
    <B><FONT COLOR="#A020F0">if</FONT></B> (!s0 || !s1) <B><FONT COLOR="#A020F0">return</FONT></B> s0 || s1;
    <B><FONT COLOR="#A020F0">var</FONT></B> sum = <B><FONT COLOR="#A020F0">new</FONT></B> Array(s0.length);
    <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> i = 0; i &lt; s0.length; i++)
      sum[i] = s0[i] + s1[i];
    <B><FONT COLOR="#A020F0">return</FONT></B> sum;
  },
  
  <I><FONT COLOR="#B22222">// helper method for columnRangeSum.  This method memoizes
</FONT></I>  <I><FONT COLOR="#B22222">// addcolumns_choose.  It only caches ranges in the binary
</FONT></I>  <I><FONT COLOR="#B22222">// subdivision tree of the domain.  This allows the cache
</FONT></I>  <I><FONT COLOR="#B22222">// to be reused when the range window changes.
</FONT></I>  addColumns_memoize: <B><FONT COLOR="#A020F0">function</FONT></B> (a, b, left, right) {
    <B><FONT COLOR="#A020F0">if</FONT></B> (arguments.length &lt;= 2) {
      left = 0;
      right = <B><FONT COLOR="#A020F0">this</FONT></B>.columns.length;
    }
    <B><FONT COLOR="#A020F0">var</FONT></B> cache = <B><FONT COLOR="#A020F0">this</FONT></B>.columnSumCache;
	<I><FONT COLOR="#B22222">// only cache even subdivisions
</FONT></I>    <B><FONT COLOR="#A020F0">var</FONT></B> key = a == left &amp;&amp; b == right-1 &amp;&amp; [a,b];
    <B><FONT COLOR="#A020F0">if</FONT></B> (key &amp;&amp; cache[key]) <B><FONT COLOR="#A020F0">return</FONT></B> cache[key];
    <B><FONT COLOR="#A020F0">if</FONT></B> (a==b) <B><FONT COLOR="#A020F0">return</FONT></B> <B><FONT COLOR="#A020F0">null</FONT></B>;
    <B><FONT COLOR="#A020F0">var</FONT></B> sum = <B><FONT COLOR="#A020F0">this</FONT></B>.addColumns_choose(a, b, left, right);
    <B><FONT COLOR="#A020F0">if</FONT></B> (key) cache[key] = sum;
    <B><FONT COLOR="#A020F0">return</FONT></B> sum;
  },
  
  columnRangeSum: <B><FONT COLOR="#A020F0">function</FONT></B>(a, b) {
    <I><FONT COLOR="#B22222">//var t0 = (new Date).getTime();
</FONT></I>    sum = <B><FONT COLOR="#A020F0">this</FONT></B>.addColumns_memoize(a, b);
    <I><FONT COLOR="#B22222">//Debug.write((new Date).getTime()-t0);
</FONT></I>    <B><FONT COLOR="#A020F0">return</FONT></B> sum;
  },
  
  getColumnSums: <B><FONT COLOR="#A020F0">function</FONT></B> () {
    <B><FONT COLOR="#A020F0">var</FONT></B> sums = <B><FONT COLOR="#A020F0">new</FONT></B> Array(<B><FONT COLOR="#A020F0">this</FONT></B>.columns.length);
    <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> i = 0; i &lt; <B><FONT COLOR="#A020F0">this</FONT></B>.columns.length; i++) {
      <B><FONT COLOR="#A020F0">var</FONT></B> column = <B><FONT COLOR="#A020F0">this</FONT></B>.columns[i];
      <B><FONT COLOR="#A020F0">var</FONT></B> sum = 0;
      <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> j = 0; j &lt; column.length; j++)
        sum += column[j];
      sums[i] = sum;
    }
    <B><FONT COLOR="#A020F0">return</FONT></B> sums;
  }
}

<I><FONT COLOR="#B22222">// Return an array +inversion+ s.t. Ai: source[inversions[i]]=target[i].
</FONT></I><I><FONT COLOR="#B22222">// Or maybe I've got that backwards.  It doesn't matter, since you
</FONT></I><I><FONT COLOR="#B22222">// won't be able to use it correctly without trying it post ways
</FONT></I><I><FONT COLOR="#B22222">// anyway.
</FONT></I><B><FONT COLOR="#A020F0">function</FONT></B> <B><FONT COLOR="#0000FF">computeArrayinversion</FONT></B>(source, target) {
  inversion = [];
  <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> i = 0; i &lt; source.length; i++) {
    <B><FONT COLOR="#A020F0">var</FONT></B> tagname = source[i];
    <B><FONT COLOR="#A020F0">var</FONT></B> j = 0;
    <B><FONT COLOR="#A020F0">while</FONT></B> (target[j] != tagname) j++;
    inversion.push(j);
  }
  <B><FONT COLOR="#A020F0">return</FONT></B> inversion;
}

<I><FONT COLOR="#B22222">// posts: [Element name='post']
</FONT></I><I><FONT COLOR="#B22222">// returns: DataFrame
</FONT></I><B><FONT COLOR="#A020F0">function</FONT></B> <B><FONT COLOR="#0000FF">fillTagFrame</FONT></B>(dataframe, posts) {
  <I><FONT COLOR="#B22222">// This relies on the fact that:
</FONT></I>  <I><FONT COLOR="#B22222">// 1. Flash iterates backwards, and
</FONT></I>  <I><FONT COLOR="#B22222">// 2. Delicious returns posts in backwards order
</FONT></I>  <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> i <B><FONT COLOR="#A020F0">in</FONT></B> posts) {
    <B><FONT COLOR="#A020F0">var</FONT></B> post = posts[i];
    <B><FONT COLOR="#A020F0">var</FONT></B> date = post.attributes[<FONT COLOR="#BC8F8F"><B>'time'</FONT></B>].split(<FONT COLOR="#BC8F8F"><B>'T'</FONT></B>)[0];
    <B><FONT COLOR="#A020F0">var</FONT></B> tags = post.attributes[<FONT COLOR="#BC8F8F"><B>'tag'</FONT></B>].split(<FONT COLOR="#BC8F8F"><B>' '</FONT></B>);
    <B><FONT COLOR="#A020F0">var</FONT></B> di = dataframe.getColumnIndex(date);
    <B><FONT COLOR="#A020F0">var</FONT></B> counts = dataframe.columns[di];
    <B><FONT COLOR="#A020F0">for</FONT></B> (<B><FONT COLOR="#A020F0">var</FONT></B> j <B><FONT COLOR="#A020F0">in</FONT></B> tags) {
      <B><FONT COLOR="#A020F0">var</FONT></B> tag = tags[j];
      <B><FONT COLOR="#A020F0">var</FONT></B> ti = dataframe.getRowIndex(tag);
      counts[ti] += 1;
    }
  }
}
</PRE>
<HR>
<ADDRESS>Generated by <A HREF="http://www.iki.fi/~mtr/genscript/">GNU enscript 1.6.1</A>.</ADDRESS>
</BODY>
</HTML>
