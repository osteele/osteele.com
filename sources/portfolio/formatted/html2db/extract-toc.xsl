      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        <title>syntax.carldr.com : produce HTML syntax highlighted code</title>
  <link href="/sources/portfolio/stylesheets/ruby.css" rel="stylesheet" type="text/css" />
 </head>
 <body>
<pre><span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:stylesheet</span> <span class="ident">xmlns</span><span class="symbol">:xsl=</span><span class="punct">&quot;</span><span class="string">http://www.w3.org/1999/XSL/Transform</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:xalanredirect=</span><span class="punct">&quot;</span><span class="string">org.apache.xalan.xslt.extensions.Redirect</span><span class="punct">&quot;</span>
                <span class="ident">exclude</span><span class="punct">-</span><span class="ident">result</span><span class="punct">-</span><span class="ident">prefixes</span><span class="punct">=&quot;</span><span class="string"></span><span class="punct">&quot;</span>
                <span class="ident">extension</span><span class="punct">-</span><span class="ident">element</span><span class="punct">-</span><span class="ident">prefixes</span><span class="punct">=&quot;</span><span class="string">xalanredirect</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:h=</span><span class="punct">&quot;</span><span class="string">http://www.w3.org/1999/xhtml</span><span class="punct">&quot;</span>
                <span class="ident">version</span><span class="punct">=&quot;</span><span class="string">1.0</span><span class="punct">&quot;&gt;</span>
  
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:output</span> <span class="ident">method</span><span class="punct">=&quot;</span><span class="string">html</span><span class="punct">&quot;/&gt;</span>
  
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:template</span> <span class="ident">match</span><span class="punct">=&quot;</span><span class="string">/</span><span class="punct">&quot;&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">h:html/h:body/*</span><span class="punct">&quot;/&gt;</span>
  <span class="punct">&lt;/</span><span class="regex">xsl:template&gt;
  
  &lt;xsl:template match=&quot;h:div[@class='toc']&quot;&gt;
    &lt;xalanredirect:write file=&quot;categories.html&quot;&gt;
      &lt;xsl:apply-templates</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xalanredirect:write&gt;
  &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:template</span><span class="punct">&gt;</span>
  
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:template</span> <span class="ident">match</span><span class="punct">=&quot;</span><span class="string">h:div[@class='toc']//text()[string(.)='Table of Contents']</span><span class="punct">&quot;&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:value</span><span class="punct">-</span><span class="ident">of</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">/h:html/h:head/h:title/text()</span><span class="punct">&quot;/&gt;</span>
  <span class="punct">&lt;/</span><span class="regex">xsl:template&gt;
  
  &lt;xsl:template match=&quot;@*|node()&quot;&gt;
    &lt;xsl:copy&gt;
      &lt;xsl:apply-templates select=&quot;@*|node()&quot;</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xsl:copy&gt;
  &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:template</span><span class="punct">&gt;</span>
  
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:template</span> <span class="ident">match</span><span class="punct">=&quot;</span><span class="string">h:a[string()='']</span><span class="punct">&quot;&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:copy</span><span class="punct">&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">@*|node()</span><span class="punct">&quot;/&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:text</span><span class="punct">&gt;</span> <span class="punct">&lt;/</span><span class="regex">xsl:text&gt;
    &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:copy</span><span class="punct">&gt;</span>
  <span class="punct">&lt;/</span><span class="regex">xsl:template&gt;
&lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:stylesheet</span><span class="punct">&gt;</span>
</pre>