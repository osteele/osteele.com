<?php header("Content-type: text/html; charset=utf-8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Oliver Steele's Source Portfolio</title>
<style type="text/css">
	 h1 {font-size: large}
	 h2 {font-size: medium}
</style>
</head>
<body>

<h1>Oliver Steele's Source Portfolio</h1>

<h2>Source code sampler</h2>
<?php include('table.html'); ?>

<p>I was the sole author of the files above except <tt>DependencyTracker.java</tt>, where I wrote the dependency tracking part but not the web server integration.  Some sources that I wrote as part of a team are <a href="http://svn.openlaszlo.org/openlaszlo/WEB-INF/lps/server/src/org/openlaszlo">here</a>.  My other team projects have not been open sourced.</p>

<p>Other languages that I've used professionally but don't have public sources from are C (Quickdraw GX, MacOS hacks), C++ (AlphaMask Graphics Library), Common Lisp (Apple Dylan), Smalltalk (Lexeme), Pascal (Riverrun), and assembly (Z80, 6502, and 68K; for graphics, games and systems programming).</p>

<p>A complete list of publicly available code that I've written, with project descriptions, is available from my <a href="/projects">project page</a> (click the "Sources" button).</p>

<h2>Specs</h2>

<p>These are some of the specs that I wrote at <a href="http://laszlosystems.com">Laszlo Systems</a>, after we open-sourced the platform and made the design process public.</p>

<ul>
<li><a href="http://article.gmane.org/gmane.comp.java.openlaszlo.devel/428/match=rfc">serverless deployment</a></li>
<li><a href="http://article.gmane.org/gmane.comp.java.openlaszlo.devel/412/match=rfc">query parameter for serverless deployment</a></li>
<li><a href="http://article.gmane.org/gmane.comp.java.openlaszlo.devel/1695/match=rfc">target-specific code</a></li>
<li><a href="http://article.gmane.org/gmane.comp.java.openlaszlo.user/398/match=rfc">overridable classes</a> (and <a href="http://article.gmane.org/gmane.comp.java.openlaszlo.user/405/match=rfc">response</a>, and <a href="http://www.mail-archive.com/laszlo-dev@openlaszlo.org/msg00315.html">follow-up</a>)</li>
<li><a href="http://article.gmane.org/gmane.comp.java.openlaszlo.user/469/match=rfc">standalone mode for libraries</a></li>
</ul>

<h2>About Programming</h2>

<dl>
<dt><a
href="http://osteele.com/archives/2004/11/ides">The IDE Divide</a></dt>
<dd>My most-cited entry.</dd>

<dt><a
href="http://osteele.com/archives/2004/12/serving-clients">Serving Client-Side
Applications</a></dt>
<dd>A nice description of AJAX, which was starting to get some traction before James Jesse Garret came up with a better description and a catchy name and blew mine out of the water.</dd>

<dt><a
href="http://osteele.com/archives/2004/08/web-mvc">Web MVC</a></dt>
<dd>A precursor to "Serving Client-Side Applications".</dd>

<dt><a href="http://www.openlaszlo.org/pipermail/laszlo-user/2005-October/001802.html">Why Javascript</a></dt>
<dd>An explanation of why OpenLaszlo uses Javascript, that some people found enlightening.</dd>
</dl>

</body>
</html>
