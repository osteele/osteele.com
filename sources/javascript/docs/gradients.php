<?php $title='JavaScript Gradients'; ?>
<?php include('../../../includes/header.php'); ?>
<h1><?php echo $title; ?></h1>
<table>
<tr><td valign="top">Author:</td><td><a href="http://osteele.com">Oliver Steele</a>

</td></tr>
<tr><td valign="top">Copyright:</td><td>Copyright 2006 Oliver Steele. All rights reserved.

</td></tr>
<tr><td valign="top">Homepage:</td><td><a
href="http://osteele.com/sources/javascript">http://osteele.com/sources/javascript</a>

</td></tr>
<tr><td valign="top">Download:</td><td><a
href="http://osteele.com/sources/javascript/gradients.js">http://osteele.com/sources/javascript/gradients.js</a>

</td></tr>
<tr><td valign="top">Docs:</td><td><a
href="http://osteele.com/sources/javascript/docs/gradients">http://osteele.com/sources/javascript/docs/gradients</a>

</td></tr>
<tr><td valign="top">Example:</td><td><a
href="http://osteele.com/sources/javascript/demos/gradients.html">http://osteele.com/sources/javascript/demos/gradients.html</a>

</td></tr>
<tr><td valign="top">License:</td><td>MIT License.

</td></tr>
<tr><td valign="top">Version:</td><td>2006-03-20

</td></tr>
</table>
<h2>Overview</h2>
<p>
<tt>gradients.js</tt> adds rounded rectangular gradients to a page, without
the use of images.
</p>
<p>
<tt>gradients.js</tt> uses the <tt>canvas</tt> tag if it is available.
Otherwise it creates the gradient through the use of <tt>div</tt> tags.
</p>
<p>
A gradient can be applied to an element procedurally, via JavaScript, or
(with the use of the <a
href="http://osteele.com/sources/javascript/">divstyle library</a>) through
the use of CSS embedded within an element&#8217;s <tt>div</tt> tags.
</p>
<h2>JavaScript API</h2>
<p>
<tt>OSGradient.applyGradient(properties, element)</tt> applies a gradient
to <tt>element</tt>.
</p>
<p>
<tt>properties</tt> is a hash of properties:
</p>
<ul>
<li><tt>gradient-start-color</tt>: gradient start color (top); required

</li>
<li><tt>gradient-end-color</tt>: gradient end color (bottom); default white

</li>
<li><tt>border-radius</tt>: rounded corner radius; default zero

</li>
</ul>
<p>
Colors must be specified as the hex number <tt>0xrrggbb</tt>, e.g.
<tt>0xff0000</tt> for red. (The Divstyle API, below, allows the use of
symbolic color names.) <tt>border-radius</tt> must be specified as a Number
of pixels, e.g. <tt>25</tt> (not <tt>25px</tt>).
</p>
<h3>Usage</h3>
<pre>
  &lt;html&gt;
    &lt;head&gt;
      &lt;script type=&quot;text/javascript&quot; src=&quot;gradients.js&quot;&gt;&lt;/script&gt;
    &lt;/head&gt;
    &lt;body&gt;
      &lt;div id=&quot;e1&quot;&gt;Some text&lt;/div&gt;
      &lt;script type=&quot;text/javascript&quot;&gt;
        var e = document.getElementById('e1');
        var style = {'gradient-start-color': 0x0000ff,
                     'border-radius': 25};
        OSGradient.applyGradient(e, style);
      &lt;/script&gt;
    &lt;/body&gt;
  &lt;/html&gt;
</pre>
<h3>DivStyle API</h3>
<p>
If the <tt>divstyle.js</tt> and <tt>behaviour.js</tt> files are included,
you can also specify a gradient using CSS syntax inside a <tt>div</tt> tag
with class <tt>style</tt>. CSS selectors within the div style can select
multiple tags, and multiple selectors can add properties to a single
element.
</p>
<p>
<tt>divstyle.js</tt> is available from <a
href="http://osteele.com/sources/javascript/">http://osteele.com/sources/javascript/</a>.
<tt>behaviour.js</tt> is available from <a
href="http://bennolan.com/behaviour/">http://bennolan.com/behaviour/</a>.
(That&#8217;s the British spelling of &quot;behaviour&quot;.)
</p>
<h3>Usage</h3>
<pre>
  &lt;html&gt;
    &lt;head&gt;
      &lt;script type=&quot;text/javascript&quot; src=&quot;behaviour.js&quot;&gt;&lt;/script&gt;
      &lt;script type=&quot;text/javascript&quot; src=&quot;divstyle.js&quot;&gt;&lt;/script&gt;
      &lt;script type=&quot;text/javascript&quot; src=&quot;gradients.js&quot;&gt;&lt;/script&gt;
    &lt;/head&gt;
    &lt;body&gt;
      &lt;div class=&quot;style&quot; style=&quot;display:none&quot;&gt;
        #e1 {gradient-start-color: #0000ff; border-radius: 25}
      &lt;/div&gt;

      &lt;div id=&quot;e1&quot;&gt;Some text&lt;/div&gt;
    &lt;/body&gt;
  &lt;/html&gt;
</pre>
<h3>Limitations</h3>
<p>
The <tt>style</tt> div can contain a subset of CSS syntax. This subset is
described in the <a
href="http://osteele.com/sources/javascript/docs/divstyle">documentation
for <div>divstyle.js</div></a>.
</p>
<?php include('../../../includes/footer.php'); ?>
