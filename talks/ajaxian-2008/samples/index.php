<?php include_once('support.php'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Practical Functional JavaScript: Code Samples</title>
<style type="text/css">
h1 {text-align: center}
h2 {font-size:large}
#footer {font-size:small; border-top:1px solid gray; padding-top:2px}
</style>
</head>
<body>

<h1><em>Practical</em> Functional JavaScript: Code Samples</h1>
<p>These are the code samples from my Ajax Experience 2008 talk, <a href="http://www.slideshare.net/osteele/oliver-steele-functional-javascript-presentation"><em>Practical</em> Functional JavaScript</a>.  Click on a line to view (and run) that sample.</p>

<p>I've written out some of what I said about the samples in the first section.  Check back later, and I'll update the rest.&mdash; <i>Oliver Steele, 2 Oct 2008, Amherst, MA</i></p>

<h2>1. Function Objects</h2>
<ol>
<?php show_section_list(array('callbacks', 'functions')); ?>
</ol>

<h2>2. Function Construction Tools</h2>
<ol>
<?php show_section_list(array('closures', 'idioms')); ?>
</ol>

<h2>3. Case Studies: Callbacks</h2>
<ul>
<?php show_section_list(array('throttling', 'caching', 'retry')); ?>
</ul>

      <div id="footer">
Copyright 2008 by <a href="http://osteele.com">Oliver Steele</a>.  This work is licensed under a 
<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Attribution-Noncommercial-Share Alike 3.0 License</a>.
        </div>

<?php include('analytics.php'); ?>

</body>
</html>
