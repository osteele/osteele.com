<?php
  $title = 'Oliver Steele';
  $nostyle = true;
  $nodtd = true;
  $content_for_header = <<<END
    <script type="text/javascript" src="/sources/javascript/gradients.js"></script>
    <style type="text/css">
      h1, h2, h3, .more {font-family: impact, sans-serif; font-weight: normal}
      h1 {margin: 0; padding: 0}
      h2 {padding-top: 20px; margin-bottom: 10}
      h1 a, h2 a, .more a {color: black; text-decoration: none}
      h1 a:hover {color: red}
      h2 a:hover {color: white}
      .section {padding-left: 20px; margin-right: 20px}
      .more {font-size: large; margin-top: 10px; margin-bottom: 30px}
      .red .more a:hover {color: red}
      .green .more a:hover {color: green}
      .blue .more a:hover {color: blue}
      .gray .more a:hover {color: gray}
      h3 {margin-top: 10px; margin-bottom: 2px}
      td {width: 450px; vertical-align: top}
      ul {margin: 0}
    </style>
END;

include('includes/header.php');
?>

    <h1><a href="/about">Oliver Steele</a></h1>

    <div id="content">
    
    <div class="section blue">
      <div class="gradient" style="display:none">gradient-start-color: #00f; border-radius: 25</div>
      <h2>Web Tools</h2>
      <h3>Recent</h3>
      <ul>
        <li><a href="/tools/rework/">reWork</a>: regular expression workbench</li>
        <li><a href="/tools/svn2ics/">svn2ics</a>: Subversion log to iCalendar</li>
      </ul>
      <h3>Popular</h3>
      <ul>
        <li><a href="/words/fortunately">Fortunately</a></li>
        <li><a href="http://packagemapper.com/">PackageMapper</a></li>
	<li><a href="/slashbot/">Flash Troll Generator</a></li>
      </ul>
      <div class="more"><a href="/tools">more&hellip;</a></div>
    </div>
    
    <div class="section red">
      <div class="gradient" style="display:none">gradient-start-color: #ff0000; border-radius: 25</div>
      <h2>Visualizations</h2>
      <h3>Recent</h3>
      <ul>
        <li><a href="/tools/reanimator/">reAnimator</a>: regular expression visualizer</li>
	<li><a href="/tools/svn-viewer/">Subversion Log Viewer</a></li>
      </ul>
      <h3>Popular</h3>
      <ul>
        <li><a href="http://expialidocio.us/">Expialidocious</a></li>
        <li><a href="/words/aargh">The Aargh Page</a></li>
	<li><a href="/tag/mathematics">Math articles</a></li>
      </ul>
      <div class="more"><a href="/tag/visualization">more&hellip;</a></div>
    </div>
    
    <div class="section green">
      <div class="gradient" style="display:none">gradient-start-color: #00ff00; border-radius: 25</div>
      <h2>Software Libraries</h2>
      <h3>Recent</h3>
      <ul>
        <li><a href="/sources/">JavaScript inline console</a></li>
        <li><a href="/sources/">JavaScript readable values</a></li>
        <li><a href="/sources/">JavaScript bezier Library</a></li>
      </ul>
      <h3>Popular</h3>
      <ul>
        <li><a href="http://www.openlaszlo.org/">OpenLaszlo</a></li>
        <li><a href="http://laszlo-plugin.rubyforge.org/">OpenLaszlo Rails Plugin</a></li>
        <li><a href="/sources/">JSON for OpenLaszlo</a></li>
        <li><a href="http://laszlo-plugin.rubyforge.org/">PyWordnet</a></li>
      </ul>
      <div class="more"><a href="/sources">more&hellip;</a></div>
    </div>
    
    <div class="section gray">
      <div class="gradient" style="display:none">gradient-start-color: #444; border-radius: 25</div>
      <h2>Essays</h2>
      <h3>Recent</h3>
      <ul>
        <li><a href="/archives/2006/02/stretch-languages">"Stretch" Languages</a></li>
        <!--li><a href="/archives/2005/12/grief">Grief</a></li-->
        <li><a href="/archives/2005/12/second-grade-squares">Second-Grade Squares</a></li>
        <li><a href="/archives/2005/01/three-lefts">Three Lefts: The Type Declaration Compromise</a></li>
      </ul>
      <h3>Popular</h3>
      <ul>
        <li><a href="/archives/2004/11/ides">The IDE Divide</a></li>
        <li><a href="archives/2004/12/serving-clients">Serving Client-Side Applications</a></li>
        <li><a href="/archives/2004/08/web-mvc">Web MVC</a></li>
        <li><a href="http://www.openlaszlo.org/pipermail/laszlo-user/2005-October/001802.html">Why JavaScript</a></li>
      </ul>
      <div class="more"><a href="/tag/essays">more&hellip;</a></div>
    </div>
    
    </div>
    
    <div style="position: absolute; bottom: 0; width: 70%">
      <hr/>
      Copyright 2006 by <a href="/">Oliver Steele</a>.  All rights reserved.
    </div>
    
    <script type="text/javascript" src="/javascripts/home.js"></script>
    
<?php
  include('includes/footer.php');
?>