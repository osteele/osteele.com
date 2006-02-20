<?php $title='OpenLaszlo: JSON'; ?>
<?php include('../../../includes/header.php'); ?>
<h1>OpenLaszlo: JSON</h1>
<table>
<tr><td valign="top">Author:</td><td>Oliver Steele

</td></tr>
<tr><td valign="top">Copyright:</td><td>Copyright 2006 Oliver Steele. All rights reserved.

</td></tr>
<tr><td valign="top">Homepage:</td><td><a
href="http://osteele.com/sources/openlaszlo/json">http://osteele.com/sources/openlaszlo/json</a>

</td></tr>
<tr><td valign="top">License:</td><td>MIT License.

</td></tr>
<tr><td valign="top">Version:</td><td>1.0

</td></tr>
</table>
<p>
OpenLaszlo:JSON is an implementation of JSON for the OpenLaszlo platform.
It is an Open Source package available under the MIT License.
</p>
<h2>Requirements</h2>
<p>
Any ECMAScript interpreter or compiler. (There are <a
href="http://www.json.org/">other implementations</a> available for <a
href="http://www.json.org/js.html">Javascript</a> and ActionScript &#8212;
the sole benefit of this implementation is that it&#8217;s compatible with
<a href="openlaszlo.org">OpenLaszlo</a>.)
</p>
<p>
The *.lzx files are OpenLaszlo source files, and require the <a
href="http://openlaszlo.org">OpenLaszlo</a> SDK.
</p>
<h2>Installation</h2>
<p>
Download the zip file from <a
href="http://osteele.com/sources/openlaszlo/json/openlaszlo-json-1.0.zip">http://osteele.com/sources/openlaszlo/json/openlaszlo-json-1.0.zip</a>.
</p>
<h2>Usage</h2>
<pre>
  JSON.stringify(123) // =&gt; '123'
  JSON.parse('123') // =&gt; 123
</pre>
<p>
See the header of json.js for additional usage information.
</p>
<h2>Examples</h2>
<p>
Here is a complete OpenLaszlo program that prints a JSON string, and a
parsed object, to the debugger:
</p>
<pre>
  &lt;canvas debug=&quot;true&quot;&gt;
    &lt;script src=&quot;json.js&quot;&gt;
    &lt;script&gt;
      Debug.write(JSON.stringify([1729, &quot;argos&quot;, {a: 1}]));
      Debug.write(JSON.parse('[1729, &quot;argos&quot;, {&quot;a&quot;: 1}]'));
    &lt;/script&gt;
  &lt;/canvas&gt;
</pre>
<p>
For an example of using JSON with AJAX to parse response text, see
json-example.lzx. This example is also running on the <a
href="http://osteele.com/archives/2006/02/json-for-openlaszlo">blog page
for this project</a>
</p>
<h2>Contents</h2>
<table>
<tr><td valign="top">README:</td><td>This file

</td></tr>
<tr><td valign="top">MIT-LICENSE:</td><td>The MIT license

</td></tr>
<tr><td valign="top">json.js:</td><td>The implementation

</td></tr>
<tr><td valign="top">json-example.lzx:</td><td>An example of using json.js with AJAX

</td></tr>
<tr><td valign="top">data:</td><td>A directory of static json files, for use with the example

</td></tr>
<tr><td valign="top">json-tests.lzx:</td><td>Unit tests (using LzUnit)

</td></tr>
<tr><td valign="top">json-test-data.js:</td><td>Data for the unit tests

</td></tr>
</table>
<?php include('../../../includes/footer.php'); ?>
