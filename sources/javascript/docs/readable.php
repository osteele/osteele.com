<?php $title='Readable'; ?>
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
href="http://osteele.com/sources/javascript">http://osteele.com/sources/javascript</a>/

</td></tr>
<tr><td valign="top">Docs:</td><td><a
href="http://osteele.com/sources/javascript/docs/readable">http://osteele.com/sources/javascript/docs/readable</a>

</td></tr>
</table>
<h1>Description</h1>
<p>
This file adds readable strings for JavaScript values, and a simple set of
logging commands that use them.
</p>
<p>
A readable string is intended for use by developers, to faciliate
command-line and logger-based debugging. Readable strings correspond to the
literal representation of a value, except that:
</p>
<ul>
<li>Collections (arrays and objects) may be optionally be limited in length and
recursion depth.

</li>
<li>Functions are abbreviated. This makes collections that contain them more
readable.

</li>
<li>Some inconsistencies noted in the Notes section below.

</li>
</ul>
<p>
As an example, <tt>[1, &#8217;&#8217;, null ,[3
,&#8217;4&#8217;]].toString()</tt> evaluates to <tt>1,,,3,4</tt>. This is
less than helpful for command-line debugging or logging. With the inclusion
of this file, the string representation of this object is the same as its
source representation, and similarly for <tt>{a: 1, b: 2}</tt> (which
otherwise displays as <tt>[object Object]</tt>).
</p>
<p>
Loading <tt>readable.js</tt> file has the following effects:
</p>
<ol>
<li>It defines a <tt>Readable</tt> object. <tt>Readable.toReadable(value)</tt>
is equivalent to <tt>v.toReadable()</tt>, except that it can be applied to
<tt>null</tt> and <tt>undefined</tt>.

</li>
<li>It adds <tt>toReadable</tt> methods to a several of the builtin classes.

</li>
<li>It optionally replaces <tt>Array.prototype.toString</tt> and
<tt>Object.prototype.toString</tt> by &#8230;<tt>.toReadable</tt>. This
makes command-line debugging using Rhino more palatable, at the expense of
polluting instances of <tt>Object</tt> and <tt>Array</tt> with an extra
property that <tt>for(&#8230;in&#8230;)</tt> will iterate over.

</li>
<li>It defines <tt>info</tt>, <tt>error</tt>, <tt>warn</tt>, and <tt>debug</tt>
functions that can be used to display information to the Rhino console, the
browser alert dialog, <a
href="http://www.alistapart.com/articles/jslogging">fvlogger</a>, or a
custom message function.

</li>
</ol>
<p>
Read more or leave a comment <a
href="http://osteele.com/archives/2006/03/readable">here</a>.
</p>
<h2>Readable API</h2>
<h3><tt>Readable.represent(value, [options])</tt></h3>
<p>
Returns a string representation of <tt>value</tt>.
</p>
<h3><tt>object.toReadable([options])</tt></h3>
<p>
Returns a string representation of <tt>object</tt>.
</p>
<h3>options</h3>
<p>
Options is a hash of:
</p>
<ul>
<li><tt>length</tt> &#8212; how many items of a collection will print

</li>
<li><tt>level</tt> &#8212; how many levels of a nested object will print

</li>
<li><tt>printFunctions</tt> &#8212; determines how functions are represented

</li>
</ul>
<p>
where <tt>printFunctions</tt> is one of:
</p>
<ul>
<li><tt>true</tt> &#8212; functions are printed by toString()

</li>
<li><tt>null</tt> (default) &#8212; function are printed as &#8216;function
name&#8217;

</li>
<li><tt>false</tt> &#8212; reserved for future use

</li>
</ul>
<h2>toString() replacement</h2>
<p>
By default, this file replaces <tt>object.toString()</tt>. and
<tt>array.toString()</tt> with calls to <tt>toReadable()</tt>. To disable
this replacement, define <tt>READABLE_TOSTRING</tt> to a non-false value
before loading this file.
</p>
<p>
In principle, these replacements could break code. For example, code that
depends on
<tt>[&#8216;one&#8217;,&#8217;two&#8217;,&#8217;three&#8217;].toString()</tt>
evaluating to <tt>&quot;one,two,three&quot;</tt> for serialization or
before presenting it to a user will no longer work. In practice, this was
what was most convenient for me &#8212; it means that I can use the Rhino
command line to print values readably, without having to wrap them in an
extra function call. So that&#8217;s the default
</p>
<h2>Logging</h2>
<p>
This file defines the logging functions <tt>info</tt>, <tt>warn</tt>,
<tt>debug</tt>, and <tt>error</tt>. These are designed to work in the
browser or in Rhino, and to call <tt>fvlogger</tt> if it has been loaded.
(For this to work, <tt>readable.js</tt> has to load <b>after</b>
<tt>fvlogger</tt>.)
</p>
<p>
The functions are defined in one of the following ways:
</p>
<ul>
<li>If <tt>info</tt>, <tt>warn</tt>, <tt>debug</tt>, and <tt>error</tt> are
defined when this file is loaded, the new implementations call the old
ones. This is the fvlogger compatability mode, and the new functions are
identical to the fvlogger functions except that (1) they are variadic (you
can call <tt>info(key, &#8217;=&gt;&#8217;, value)</tt> instead of having
to write <tt>info(key + &#8217;=&gt;&#8217; + value)</tt>), and (2) they
apply <tt>toReadable</tt> to the arguments (which is why the variadicity is
important).

</li>
<li>Otherwise, if &#8216;alert&#8217; exists, logging calls this. This can be
useful in the browser. (You can replace a <tt>ReadableLogger.log</tt> with
a function sets the status bar or appends text to a &lt;div&gt; instead.)

</li>
<li>Otherwise logging calls &#8216;print&#8217;. This would be the Wrong Thing
in the browser, but the browser will take the &#8216;alert&#8217; case.
This is for Rhino, which defines <tt>print</tt> this to print to the
console.

</li>
</ul>
<p>
The advantages of calling <tt>info</tt> (and the other logging functions)
instead of (browser) <tt>alert</tt> or (Rhino) <tt>print</tt> are:
</p>
<ul>
<li><tt>info</tt> is variadic

</li>
<li><tt>info</tt> produces readable representations

</li>
<li><tt>info</tt> is compatible between browses and Rhino. This means you can
use Rhino for development of logic and other non-UI code, and test the
code, with logging calls, in both Rhino and the browser.

</li>
</ul>
<h3>Customizing</h3>
<p>
Replace <tt>ReadableLogger.log(level, message)</tt> or
<tt>ReadableLog.display(message)</tt> to customize this behavior.
</p>
<p>
Logging uses ReadableLogger.defaults to limit the maximum length and
recursion level.
</p>
<h2>Notes and Bugs</h2>
<p>
There&#8217;s no check for recursive objects. Setting the <tt>level</tt>
option will at least keep the system from recursing infinitely. (It&#8217;s
set by default.)
</p>
<p>
Not all characters in strings are quoted, and JavaScript keywords that are
used as Object property names aren&#8217;t quoted either. This is simply
laziness.
</p>
<p>
The logging functions intentionally use <tt>toString</tt> instead of
<tt>toReadable</tt> for the arguments themselves. That is, <tt>a</tt> but
not <tt>b</tt> is quoted in <tt>info([a], b)</tt>. This is <b>usually</b>
what you want, for uses such as <tt>info(key, &#8217;=&gt;&#8217;,
value)</tt>. When it&#8217;s not, you can explicitly apply
<tt>toReadable</tt> to the value, e.g. <tt>info(value.toReadable())</tt>
or, when it might be undefined or null,
<tt>info(Readable.toReadable(value))</tt>.
</p>
<?php include('../../../includes/footer.php'); ?>
