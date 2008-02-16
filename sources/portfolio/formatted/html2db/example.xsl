      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        <title>syntax.carldr.com : produce HTML syntax highlighted code</title>
  <link href="/sources/portfolio/stylesheets/ruby.css" rel="stylesheet" type="text/css" />
 </head>
 <body>
<pre><span class="punct">&lt;</span><span class="char">?x</span><span class="ident">ml</span> <span class="ident">version</span><span class="punct">=&quot;</span><span class="string">1.0</span><span class="punct">&quot;</span> <span class="ident">encoding</span><span class="punct">=&quot;</span><span class="string">utf-8</span><span class="punct">&quot;</span><span class="char">?&gt;</span>
<span class="punct">&lt;!--</span> <span class="constant">Copyright</span> <span class="number">2004</span> <span class="ident">by</span> <span class="constant">Laszlo</span> <span class="constant">Systems</span><span class="punct">,</span> <span class="constant">Inc</span><span class="punct">.</span>
     <span class="ident">Released</span> <span class="ident">under</span> <span class="ident">the</span> <span class="constant">Artistic</span> <span class="constant">License</span><span class="punct">.</span>
     <span class="ident">Written</span> <span class="ident">by</span> <span class="constant">Oliver</span> <span class="constant">Steele</span><span class="punct">.</span>
     <span class="ident">http</span><span class="punct">:/</span><span class="regex"></span><span class="punct">/</span><span class="ident">osteele</span><span class="punct">.</span><span class="ident">com</span><span class="punct">/</span><span class="ident">sources</span><span class="punct">/</span><span class="ident">xslt</span><span class="punct">/</span><span class="ident">htm2db</span><span class="punct">/</span>
     
     <span class="constant">This</span> <span class="ident">is</span> <span class="ident">an</span> <span class="ident">minimal</span> <span class="ident">embedding</span> <span class="ident">stylesheet</span><span class="punct">.</span>  <span class="ident">Make</span> <span class="ident">a</span> <span class="ident">copy</span> <span class="ident">of</span>
     <span class="ident">this</span> <span class="ident">file</span> <span class="keyword">and</span> <span class="ident">customize</span> <span class="ident">it</span> <span class="ident">with</span> <span class="ident">parameter</span> <span class="ident">definitions</span> <span class="keyword">and</span>
     <span class="ident">template</span> <span class="ident">overrides</span> <span class="ident">to</span> <span class="ident">customize</span> <span class="ident">the</span> <span class="ident">transformation</span><span class="punct">.</span>
     <span class="ident">See</span> <span class="ident">example</span><span class="punct">.</span><span class="ident">xsl</span> <span class="keyword">for</span> <span class="ident">an</span> <span class="ident">example</span><span class="punct">.</span>
  <span class="punct">--&gt;</span>
<span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:stylesheet</span> <span class="ident">xmlns</span><span class="symbol">:xsl=</span><span class="punct">&quot;</span><span class="string">http://www.w3.org/1999/XSL/Transform</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:h=</span><span class="punct">&quot;</span><span class="string">http://www.w3.org/1999/xhtml</span><span class="punct">&quot;</span>
                <span class="ident">exclude</span><span class="punct">-</span><span class="ident">result</span><span class="punct">-</span><span class="ident">prefixes</span><span class="punct">=&quot;</span><span class="string">h</span><span class="punct">&quot;</span>
                <span class="ident">version</span><span class="punct">=&quot;</span><span class="string">1.0</span><span class="punct">&quot;&gt;</span>
  
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:import</span> <span class="ident">href</span><span class="punct">=&quot;</span><span class="string">html2db.xsl</span><span class="punct">&quot;/&gt;</span>
  
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:template</span> <span class="ident">match</span><span class="punct">=&quot;</span><span class="string">h:div[@class='abstract']</span><span class="punct">&quot;&gt;</span>
    <span class="punct">&lt;</span><span class="ident">abstract</span><span class="punct">&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">abstract&gt;
  &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:template</span><span class="punct">&gt;</span>
  
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:template</span> <span class="ident">match</span><span class="punct">=&quot;</span><span class="string">h:p[@class='note']</span><span class="punct">&quot;&gt;</span>
    <span class="punct">&lt;</span><span class="ident">note</span><span class="punct">&gt;</span>
      <span class="punct">&lt;</span><span class="ident">para</span><span class="punct">&gt;</span>
        <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span><span class="punct">/&gt;</span>
      <span class="punct">&lt;/</span><span class="regex">para&gt;
    &lt;</span><span class="punct">/</span><span class="ident">note</span><span class="punct">&gt;</span>
  <span class="punct">&lt;/</span><span class="regex">xsl:template&gt;
  
  &lt;xsl:template match=&quot;h:pre[@class='example']&quot;&gt;
    &lt;informalexample&gt;
      &lt;xsl:apply-imports</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">informalexample&gt;
  &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:template</span><span class="punct">&gt;</span>

<span class="punct">&lt;/</span><span class="regex">xsl:stylesheet&gt;<span class="normal">
</span></span></pre>