<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title>reWork: a regular expression workbench</title>
<?php if (isset($_GET['debug'])) { ?>
    <script type="text/javascript" src="/js/fvlogger/logger.js"></script>
    <link rel="stylesheet" type="text/css" href="/js/fvlogger/logger.css"/>
    <script type="text/javascript" src="/js/readable.js"></script>
    <script type="text/javascript" src="/js/inline-console.js"></script>
<?php } ?>
    <script type="text/javascript" src="/js/prototype.js"></script>
    <script type="text/javascript" src="json.js"></script>
    <script type="text/javascript" src="graphview.js"></script>
    <script type="text/javascript" src="textcanvas.js"></script>
    <script type="text/javascript" src="html-graphview.js"></script>
    <script type="text/javascript" src="reparse.js"></script>
    <script type="text/javascript" src="usage-generator.js"></script>
    <link rel="stylesheet" type="text/css" href="rework.css"/>
  </head>
  <body>

   <p class="info"><strong>Instructions</strong>: Type a regular expression into the "pattern" field, and a string to match it against into "input".  The results area updates as you type.</p>
     
   <form action="." method="get">
     <ul id="menu">
       <li class="selected"><a id="searchTab" href="#" onclick="TabController.select(this)"/>Search</a></li>
       <li><a href="#" onclick="TabController.select(this)">Replace</a></li>
       <li><a id="multipleTab" href="#" onclick="TabController.select(this)">Multiple</a></li>
       <li><a href="#" onclick="TabController.select(this)">Split</a></li>
       <li><a href="#" onclick="TabController.select(this)">Scan</a></li>
       <li id="parseTab"><a href="#" onclick="TabController.select(this)">Parse</a></li>
       <li id="graphTab"><a href="#" onclick="TabController.select(this)">Graph</a></li>
       <li><a href="#" onclick="TabController.select(this)">Help</a></li>
     </ul>
     
     <div id="tabcontents" style="display: none">
     <label for="pattern"><b>Pattern:</b></label>
     <span class="info">A regular expression, without surrounding characters.  For example, <tt>a*b</tt> or <tt>ab|cd</tt>, <em>not</em> <tt>/a*b/</tt>.</span><br/>
     <input type="text" size="80" id="pattern" value="(to|the|t.xt)"><br/>
     <span id="error"></span>

     <div id="extended-area">
     <div id="nongraph">
       <span width="50px"/>
       <input type="checkbox" id="ignoreCaseCheckbox">ignore case</input>
       <input type="checkbox" id="multilineCheckbox"><tt>^</tt> and <tt>$</tt>        match the beginning and end of each line</input>
       <input type="checkbox" id="globalCheckbox" checked="checked">all        matches</input>
       <br/>
       
     <div id="replacement-area">
       <label for="replacement"><b>Replacement text:</b></label><br/>
       <input type="text" size="80" id="replacement" value="replacement"><br/>
     </div>

     <div id="input-area">
     <label for="input"><b>Input:</b></label>
     <span class="info">A string that is matched against the regular      expression.  For example, <tt>ab</tt> and <tt>aaab</tt> both match      <tt>a*b</tt>.</span><br/>
     <table><tr>
       <td rowspan="2"><textarea rows="2" cols="80" id="input">sample text to          match against the pattern</textarea></td>
<td><span id="shrinkInput"">-</span><br/><span   id="expandInput">+</span></td></tr></table>
</div> <!-- #input-area -->
</div>
</div> <!-- #extended-area -->
     
       <div id="search">
	 <b>Results:</b><br/>
	 <div id="search-results">
	   <span id="search-summary"></span>
	   <div id="search-details"></div>
	 </div>
	 <div id="search-usage" class="usage"></div>
       </div>
       
       <div id="multiple">
	 <table id="multiple-table">
           <tr>
             <th>Input</th>
             <td>
               <input id="update-multiple" type="button"                value="Update&gt;&gt;" onclick="multipleController.updateResults(); return false;"/>
             </td>
             <th>Results</th>
           </tr>
         </table>
       </div>
       
       <div id="replace">
	 <div><strong>Output:</strong></div>
	 <div id="replace-results"></div>
	 <div id="replace-usage" class="usage"></div>
       </div>
       
       <div id="scan">
	 <div id="scan-results"></div>
	 <div id="scan-usage" class="usage"></div>
       </div>
       
       <div id="split">
	 <div id="split-results"></div>
	 <div id="split-usage" class="usage"></div>
       </div>
       
       <div id="parse">
	 <p class="info"><strong>Instructions</strong>: Create a parse tree of your regular expression.  This feature is highly experimental, and has known bugs.</p>
	 
         <input id="updateParseButton" type="button" value="Update Parse Tree"/>
	 <div id="parseTreeContainer"></div>
       </div>
       
       <div id="graph">
       <div class="info"><br/>This is a graph of the FSA that corresponds to this regular expression.  This won't help you <em>use</em> regular expressions; it's just to test my graph presentation library, and for fun.  If you're interested in this sort of thing, see my <a href="/tools/reanimator">reAnimator</a> tool.<br/></div>
	   <div><strong>Graph:</strong></div>
	   <input id="graphButton" type="button" value="Update"/>
	   <span id="noGraph" class="info"></span>
	   <div id="graphContainer" style="padding-bottom: 5px"></div>
	 </div> <!-- #graph -->

       <div id="help">
         <div class="info"><br/>For more information about how to use regular expressions, including examples, additional documentation, and additional tools, see:<ul>
<li><a href="http://www.regular-expressions.info">Regular-Expressions.info</a> (online)</li><li><a href="http://regexlib.com/Resources.aspx">RegExLib</a> (online)</li><li>Jeffrey Freidl's <a href="http://www.amazon.com/gp/product/oliversteele-20/0596002890">Mastering Regular Expressions</a> (Amazon)</li></ul></div>
         <span id="key" style="font-size: small"></span>
       </div>
       
       </div> <!-- tabs -->
       
       </form>
   
	<?php if (isset($_GET['debug'])) { ?>
       <div id="inline-console"></div>
       <a href="#fvlogger" onclick="eraseLog(); return false">clear</a>
       <div id="fvlogger"></div>
	<?php } ?>
   
     <script type="text/javascript" src="rework.js"></script>

<div id="footer">
<hr/>
Copyright 2006 by <a href="/">Oliver Steele</a>.  All rights reserved.<br/>
You might also be interested in <a href="/tools/reanimator">reAnimator</a>.
</div>

<?php include('../../includes/footer.php'); ?>
