<?php $title='Inline Console'; ?>
<?php include('../../../includes/header.php'); ?>
<h1><?php echo $title; ?></h1>
<table>
<tr><td valign="top">Author:</td><td>Oliver Steele

</td></tr>
<tr><td valign="top">Copyright:</td><td>Copyright 2006 Oliver Steele. All rights reserved.

</td></tr>
<tr><td valign="top">License:</td><td>MIT License.

</td></tr>
<tr><td valign="top">Homepage:</td><td><a
href="http://osteele.com/sources/javascript">http://osteele.com/sources/javascript</a>

</td></tr>
<tr><td valign="top">Download:</td><td><a
href="http://osteele.com/sources/javascript/inline-console.js">http://osteele.com/sources/javascript/inline-console.js</a>

</td></tr>
<tr><td valign="top">Docs:</td><td><a
href="http://osteele.com/sources/javascript/docs/inline-console">http://osteele.com/sources/javascript/docs/inline-console</a>

</td></tr>
<tr><td valign="top">Example:</td><td><a
href="http://osteele.com/sources/javascript/demos/inline-console.html">http://osteele.com/sources/javascript/demos/inline-console.html</a>

</td></tr>
</table>
<h2>Usage</h2>
<p>
Include this file:
</p>
<pre>
  &lt;script type=&quot;text/javascript&quot; src=&quot;inline-console.js&quot;&gt;&lt;/script&gt;
</pre>
<p>
This will give you a console area at the bottom of your web page. The text
input field at the top of the console can be used to evaluate JavaScript
expressions and statements. The results are appended to the console.
</p>
<p>
This file also defines unary functions <tt>info</tt>, <tt>warning</tt>,
<tt>debug</tt>, and <tt>error</tt>, that log their output to the console.
</p>
<p>
To customize the location of the console, define
</p>
<pre>
  &lt;div id=&quot;inline-console&quot;&gt;&lt;/div&gt;
</pre>
<p>
in the including HTML file.
</p>
<h2>Related Packages</h2>
<p>
fvlogger provides finer-grained control over the display of log messages.
This file may be used in conjunction with fvlogger simply by including both
files. In this case, the fvlogger logging functions are used instead of the
functions defined here, and if &lt;div id=&quot;inline-console&quot;&gt; is
not defined, it is appended to the end of the the #fvlogger div, rather
than to the end of the HTML body.
</p>
<p>
readable.js provides a representations of JavaScript values (e.g. &quot;{a:
1}&quot; rather than &quot;[object Object]&quot;) and variadic logging
functions (e.g. <tt>log(key, &#8217;-&gt;&#8217;, value)</tt> instead of
<tt>log(key + &#8217;-&gt;&#8217; + value)</tt>). This file may be used in
conjunction with readable.js by including readable.js <b>after</b> this
file.
</p>
<?php include('../../../includes/footer.php'); ?>
