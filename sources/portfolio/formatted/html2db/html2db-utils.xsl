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
     
    <span class="constant">Utility</span> <span class="ident">functions</span>
  <span class="punct">--&gt;</span>
<span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:stylesheet</span> <span class="ident">xmlns</span><span class="symbol">:xsl=</span><span class="punct">&quot;</span><span class="string">http://www.w3.org/1999/XSL/Transform</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:exslt=</span><span class="punct">&quot;</span><span class="string">http://exslt.org/common</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:math=</span><span class="punct">&quot;</span><span class="string">http://exslt.org/math</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:xalan=</span><span class="punct">&quot;</span><span class="string">http://xml.apache.org/xalan</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:html2db=</span><span class="punct">&quot;</span><span class="string">urn:html2db</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:db=</span><span class="punct">&quot;</span><span class="string">urn:docbook</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:h=</span><span class="punct">&quot;</span><span class="string">http://www.w3.org/1999/xhtml</span><span class="punct">&quot;</span>
                <span class="ident">exclude</span><span class="punct">-</span><span class="ident">result</span><span class="punct">-</span><span class="ident">prefixes</span><span class="punct">=&quot;</span><span class="string">db exslt h html2db math xalan</span><span class="punct">&quot;</span>
                <span class="ident">extension</span><span class="punct">-</span><span class="ident">element</span><span class="punct">-</span><span class="ident">prefixes</span><span class="punct">=&quot;</span><span class="string">html2db</span><span class="punct">&quot;</span>
                <span class="ident">version</span><span class="punct">=&quot;</span><span class="string">1.0</span><span class="punct">&quot;&gt;</span>
  
  <span class="punct">&lt;!--</span> <span class="constant">Wrap</span> <span class="ident">with</span> <span class="punct">&quot;</span><span class="string">, and backslash </span><span class="punct">&quot;</span> <span class="keyword">and</span> \ <span class="punct">--&gt;</span>
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:template</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">quote</span><span class="punct">&quot;&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">str</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">string(.)</span><span class="punct">&quot;/&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">lquo</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">'&amp;quot;'</span><span class="punct">&quot;/&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">rquo</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">'&amp;quot;'</span><span class="punct">&quot;/&gt;</span>
    <span class="punct">&lt;!--</span> <span class="ident">first</span> <span class="punct">&quot;</span><span class="string"> --&gt;
    &lt;xsl:variable name=</span><span class="punct">&quot;</span><span class="ident">qpos</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;</span><span class="ident">string</span><span class="punct">-</span><span class="ident">length</span><span class="punct">(</span><span class="ident">substring</span><span class="punct">-</span><span class="ident">before</span><span class="punct">(</span><span class="global">$str</span><span class="punct">,</span> <span class="punct">'</span><span class="string">&amp;quot;</span><span class="punct">'))&quot;</span><span class="string">/&gt;
    &lt;!-- first <span class="escape">\ </span>--&gt;
    &lt;xsl:variable name=</span><span class="punct">&quot;</span><span class="ident">bspos</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;</span><span class="ident">string</span><span class="punct">-</span><span class="ident">length</span><span class="punct">(</span><span class="ident">substring</span><span class="punct">-</span><span class="ident">before</span><span class="punct">(</span><span class="global">$str</span><span class="punct">,</span> <span class="punct">'</span><span class="string"><span class="escape">\\</span></span><span class="punct">'))&quot;</span><span class="string">/&gt;
    &lt;!-- first </span><span class="punct">&quot;</span> <span class="keyword">or</span> \ <span class="punct">--&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:variable</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">pos</span><span class="punct">&quot;&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:choose</span><span class="punct">&gt;</span>
        <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:when</span> <span class="ident">test</span><span class="punct">=&quot;</span><span class="string">$qpos=0</span><span class="punct">&quot;&gt;&lt;</span><span class="ident">xsl</span><span class="symbol">:value</span><span class="punct">-</span><span class="ident">of</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">$bspos</span><span class="punct">&quot;/&gt;&lt;/</span><span class="regex">xsl:when&gt;
        &lt;xsl:when test=&quot;$bspos=0&quot;&gt;&lt;xsl:value-of select=&quot;$qpos&quot;</span><span class="punct">/&gt;&lt;/</span><span class="regex">xsl:when&gt;
        &lt;xsl:when test=&quot;$qpos&amp;lt;$bspos&quot;&gt;
          &lt;xsl:value-of select=&quot;$qpos&quot;</span><span class="punct">/&gt;</span>
        <span class="punct">&lt;/</span><span class="regex">xsl:when&gt;
        &lt;xsl:when test=&quot;$bspos&quot;&gt;
          &lt;xsl:value-of select=&quot;$bspos&quot;</span><span class="punct">/&gt;</span>
        <span class="punct">&lt;/</span><span class="regex">xsl:when&gt;
      &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:choose</span><span class="punct">&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xsl:variable&gt;
    &lt;xsl:value-of select=&quot;$lquo&quot;</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:choose</span><span class="punct">&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:when</span> <span class="ident">test</span><span class="punct">=&quot;</span><span class="string">$pos!=0</span><span class="punct">&quot;&gt;</span>
        <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:value</span><span class="punct">-</span><span class="ident">of</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">substring($str, 1, $pos)</span><span class="punct">&quot;/&gt;</span>
        <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:text</span><span class="punct">&gt;\&lt;/</span><span class="regex">xsl:text&gt;
        &lt;xsl:value-of select=&quot;substring($str, $pos + 1, 1)&quot;</span><span class="punct">/&gt;</span>
        <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:call</span><span class="punct">-</span><span class="ident">template</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">quote</span><span class="punct">&quot;&gt;</span>
          <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:with</span><span class="punct">-</span><span class="ident">param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">str</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">substring($str, $pos + 2)</span><span class="punct">&quot;/&gt;</span>
          <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:with</span><span class="punct">-</span><span class="ident">param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">lquo</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">''</span><span class="punct">&quot;/&gt;</span>
          <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:with</span><span class="punct">-</span><span class="ident">param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">rquo</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">''</span><span class="punct">&quot;/&gt;</span>
        <span class="punct">&lt;/</span><span class="regex">xsl:call-template&gt;
      &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:when</span><span class="punct">&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:otherwise</span><span class="punct">&gt;</span>
        <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:value</span><span class="punct">-</span><span class="ident">of</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">$str</span><span class="punct">&quot;/&gt;</span>
      <span class="punct">&lt;/</span><span class="regex">xsl:otherwise&gt;
    &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:choose</span><span class="punct">&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:value</span><span class="punct">-</span><span class="ident">of</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">$rquo</span><span class="punct">&quot;/&gt;</span>
  <span class="punct">&lt;/</span><span class="regex">xsl:template&gt;
  
&lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:stylesheet</span><span class="punct">&gt;</span>
</pre>