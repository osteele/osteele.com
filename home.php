<?php
  $title = 'Oliver Steele';
  $nostyle = true;
  $nodtd = true;
  $content_for_header = <<<END
    <meta name="description" content="Oliver Steele's projects, essays, software libraries, and visualizations."/>
    <script type="text/javascript" src="/javascripts/behaviour.js"></script>
    <script type="text/javascript" src="/javascripts/divstyle.js"></script>
    <script type="text/javascript" src="/javascripts/gradients.js"></script>
    <link rel="stylesheet" type="text/css" href="/stylesheets/home.css" />
END;

include('includes/header.php');
?>

    <h1><a href="/about">Oliver Steele</a></h1>
    
    <div class="style">
      .section {border-radius: 25}
      .blue {gradient-start-color: #00f}
      .red {gradient-start-color: #ff0000}
      .green {gradient-start-color: #00ff00; border-radius: 25}
      .gray {gradient-start-color: #444; border-radius: 25}
    </div>
    
    <table><tr><td>

    <div class="section blue">
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
    
</td><td>

    <div class="section red">
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

</td></tr><tr><td>
    
    <div class="section green">
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
    
</td><td>

    <div class="section gray">
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
    
</td></tr></table>
    
    <div style="width: 70%">
      <hr/>
      Copyright 2006 by <a href="/about">Oliver Steele</a>.  All rights reserved.
    </div>
    
    <script type="text/javascript" src="/javascripts/home.js"></script>
    
<?php
  include('includes/footer.php');
?>