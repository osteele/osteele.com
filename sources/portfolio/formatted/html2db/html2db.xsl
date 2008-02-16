      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        <title>syntax.carldr.com : produce HTML syntax highlighted code</title>
  <link href="/sources/portfolio/stylesheets/ruby.css" rel="stylesheet" type="text/css" />
 </head>
 <body>
<pre><span class="punct">&lt;</span><span class="char">?x</span><span class="ident">ml</span> <span class="ident">version</span><span class="punct">=&quot;</span><span class="string">1.0</span><span class="punct">&quot;</span> <span class="ident">encoding</span><span class="punct">=&quot;</span><span class="string">utf-8</span><span class="punct">&quot;</span><span class="char">?&gt;</span>
<span class="punct">&lt;!</span><span class="constant">DOCTYPE</span> <span class="ident">xsl</span><span class="symbol">:stylesheet</span> <span class="punct">[</span>
<span class="punct">&lt;!</span><span class="constant">ENTITY</span> <span class="ident">cr</span> <span class="punct">&quot;</span><span class="string">&lt;xsl:text&gt;&amp;#10;&lt;/xsl:text&gt;</span><span class="punct">&quot;&gt;</span>
<span class="punct">]&gt;</span>
<span class="punct">&lt;!--</span> <span class="constant">Copyright</span> <span class="number">2004</span> <span class="ident">by</span> <span class="constant">Laszlo</span> <span class="constant">Systems</span><span class="punct">,</span> <span class="constant">Inc</span><span class="punct">.</span>
     <span class="ident">Released</span> <span class="ident">under</span> <span class="ident">the</span> <span class="constant">Artistic</span> <span class="constant">License</span><span class="punct">.</span>
     <span class="ident">Written</span> <span class="ident">by</span> <span class="constant">Oliver</span> <span class="constant">Steele</span><span class="punct">.</span>
     <span class="ident">Version</span> <span class="number">1.0</span><span class="punct">.</span><span class="number">1</span>
     <span class="ident">http</span><span class="punct">:/</span><span class="regex"></span><span class="punct">/</span><span class="ident">osteele</span><span class="punct">.</span><span class="ident">com</span><span class="punct">/</span><span class="ident">sources</span><span class="punct">/</span><span class="ident">xslt</span><span class="punct">/</span><span class="ident">htm2db</span><span class="punct">/</span>
  <span class="punct">--&gt;</span>
<span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:stylesheet</span> <span class="ident">xmlns</span><span class="symbol">:xsl=</span><span class="punct">&quot;</span><span class="string">http://www.w3.org/1999/XSL/Transform</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:exslt=</span><span class="punct">&quot;</span><span class="string">http://exslt.org/common</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:java=</span><span class="punct">&quot;</span><span class="string">http://xml.apache.org/xalan/java</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:math=</span><span class="punct">&quot;</span><span class="string">http://exslt.org/math</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:db=</span><span class="punct">&quot;</span><span class="string">urn:docbook</span><span class="punct">&quot;</span>
                <span class="ident">xmlns</span><span class="symbol">:h=</span><span class="punct">&quot;</span><span class="string">http://www.w3.org/1999/xhtml</span><span class="punct">&quot;</span>
                <span class="ident">exclude</span><span class="punct">-</span><span class="ident">result</span><span class="punct">-</span><span class="ident">prefixes</span><span class="punct">=&quot;</span><span class="string">exslt java math db h</span><span class="punct">&quot;</span>
                <span class="ident">version</span><span class="punct">=&quot;</span><span class="string">1.0</span><span class="punct">&quot;&gt;</span>
  
  <span class="punct">&lt;!--</span> <span class="constant">Prefixed</span> <span class="ident">to</span> <span class="ident">every</span> <span class="ident">id</span> <span class="ident">generated</span> <span class="ident">from</span> <span class="punct">&lt;</span><span class="ident">a</span> <span class="ident">name</span><span class="punct">=&gt;</span> <span class="keyword">and</span> <span class="punct">&lt;</span><span class="ident">a</span> <span class="ident">href</span><span class="punct">=&quot;</span><span class="string">#</span><span class="punct">&quot;&gt;</span> <span class="punct">--&gt;</span>
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">anchor-id-prefix</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">''</span><span class="punct">&quot;/&gt;</span>
  
  <span class="punct">&lt;!--</span> <span class="constant">Default</span> <span class="ident">document</span> <span class="ident">root</span><span class="punct">;</span> <span class="ident">can</span> <span class="ident">be</span> <span class="ident">overridden</span> <span class="ident">by</span> <span class="punct">&lt;</span><span class="char">?h</span><span class="ident">tml2db</span> <span class="keyword">class</span><span class="punct">=&gt;</span> <span class="punct">--&gt;</span>
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">document-root</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">'article'</span><span class="punct">&quot;/&gt;</span>
  
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:include</span> <span class="ident">href</span><span class="punct">=&quot;</span><span class="string">html2db-utils.xsl</span><span class="punct">&quot;/&gt;</span>
  
  <span class="punct">&lt;!--</span>
    <span class="constant">Default</span> <span class="ident">templates</span>
  <span class="punct">--&gt;</span>
  
  <span class="punct">&lt;!--</span> <span class="ident">pass</span> <span class="ident">docbook</span> <span class="ident">elements</span> <span class="ident">through</span> <span class="ident">unchanged</span><span class="punct">;</span> <span class="ident">just</span> <span class="ident">strip</span> <span class="ident">the</span> <span class="ident">prefix</span>
       <span class="punct">--&gt;</span>
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:template</span> <span class="ident">match</span><span class="punct">=&quot;</span><span class="string">db:*</span><span class="punct">&quot;&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:element</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">{local-name()}</span><span class="punct">&quot;&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:for</span><span class="punct">-</span><span class="ident">each</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">@*</span><span class="punct">&quot;&gt;</span>
        <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:attribute</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">{name()}</span><span class="punct">&quot;&gt;</span>
          <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:value</span><span class="punct">-</span><span class="ident">of</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">.</span><span class="punct">&quot;/&gt;</span>
        <span class="punct">&lt;/</span><span class="regex">xsl:attribute&gt;
      &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:for</span><span class="punct">-</span><span class="ident">each</span><span class="punct">&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xsl:element&gt;
  &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:template</span><span class="punct">&gt;</span>
  
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:template</span> <span class="ident">match</span><span class="punct">=&quot;</span><span class="string">@id</span><span class="punct">&quot;&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:copy</span><span class="punct">/&gt;</span>
  <span class="punct">&lt;/</span><span class="regex">xsl:template&gt;
  
  &lt;!-- copy processing instructions, too --&gt;
  &lt;xsl:template match=&quot;processing-instruction()&quot;&gt;
    &lt;xsl:copy</span><span class="punct">/&gt;</span>
  <span class="punct">&lt;/</span><span class="regex">xsl:template&gt;
  
  &lt;!-- except for html2db instructions --&gt;
  &lt;xsl:template match=&quot;processing-instruction('html2db')&quot;</span><span class="punct">/&gt;</span>
  
  <span class="punct">&lt;!--</span> <span class="constant">Warn</span> <span class="ident">about</span> <span class="ident">any</span> <span class="ident">html</span> <span class="ident">elements</span> <span class="ident">that</span> <span class="ident">don</span><span class="punct">'</span><span class="string">t match a more
       specific template.  Copy them too, since it</span><span class="punct">'</span><span class="ident">s</span> <span class="ident">often</span>
       <span class="ident">easier</span> <span class="ident">to</span> <span class="ident">find</span> <span class="ident">them</span> <span class="keyword">in</span> <span class="ident">the</span> <span class="ident">output</span><span class="punct">.</span> <span class="punct">--&gt;</span>
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:template</span> <span class="ident">match</span><span class="punct">=&quot;</span><span class="string">h:*</span><span class="punct">&quot;&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:message</span> <span class="ident">terminate</span><span class="punct">=&quot;</span><span class="string">no</span><span class="punct">&quot;&gt;</span>
      <span class="constant">Unknown</span> <span class="ident">element</span> <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:value</span><span class="punct">-</span><span class="ident">of</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">name()</span><span class="punct">&quot;/&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xsl:message&gt;
    &lt;xsl:copy&gt;
      &lt;xsl:apply-templates</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xsl:copy&gt;
  &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:template</span><span class="punct">&gt;</span>
  
  <span class="punct">&lt;!--</span>
    <span class="constant">Root</span> <span class="ident">element</span> <span class="keyword">and</span> <span class="ident">body</span>
  <span class="punct">--&gt;</span>
  
  <span class="punct">&lt;!--</span> <span class="ident">ignore</span> <span class="ident">everything</span> <span class="ident">except</span> <span class="ident">the</span> <span class="ident">body</span> <span class="punct">--&gt;</span>
  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:template</span> <span class="ident">match</span><span class="punct">=&quot;</span><span class="string">/</span><span class="punct">&quot;&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">//h:body</span><span class="punct">&quot;/&gt;</span>
  <span class="punct">&lt;/</span><span class="regex">xsl:template&gt;

  &lt;xsl:template match=&quot;h:body&quot;&gt;
    &lt;xsl:variable name=&quot;class-pi&quot;
                  select=&quot;processing-instruction('html2db')[starts-with(string(), 'class=&amp;quot;')][1]&quot;</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:variable</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">class</span><span class="punct">&quot;&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:choose</span><span class="punct">&gt;</span>
        <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:when</span> <span class="ident">test</span><span class="punct">=&quot;</span><span class="string">count($class-pi)!=0</span><span class="punct">&quot;&gt;</span>
          <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:value</span><span class="punct">-</span><span class="ident">of</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">substring-before(substring-after(string($class-pi[0]), 'class=&amp;quot;'), '&amp;quot;')</span><span class="punct">&quot;/&gt;</span>
        <span class="punct">&lt;/</span><span class="regex">xsl:when&gt;
        &lt;xsl:otherwise&gt;
          &lt;xsl:value-of select=&quot;$document-root&quot;</span><span class="punct">/&gt;</span>
        <span class="punct">&lt;/</span><span class="regex">xsl:otherwise&gt;
      &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:choose</span><span class="punct">&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xsl:variable&gt;
    
    &lt;!-- Warn if there are any text nodes outside a para, etc.  See
         the note at the naked text template for why this is a
         warning. --&gt;
    &lt;xsl:if test=&quot;text()[normalize-space() != '']&quot;&gt;
      &lt;xsl:message terminate=&quot;no&quot;&gt;
        Text must be inside a &amp;lt;p&amp;gt; tag.
      &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:message</span><span class="punct">&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xsl:if&gt;
    
    &lt;xsl:element name=&quot;{$class}&quot;&gt;
      &lt;xsl:apply-templates select=&quot;@id&quot;</span><span class="punct">/&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:call</span><span class="punct">-</span><span class="ident">template</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">section-content</span><span class="punct">&quot;&gt;</span>
        <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:with</span><span class="punct">-</span><span class="ident">param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">level</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">1</span><span class="punct">&quot;/&gt;</span>
        <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:with</span><span class="punct">-</span><span class="ident">param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">nodes</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">//h:body/node()|//h:body/text()</span><span class="punct">&quot;/&gt;</span>
      <span class="punct">&lt;/</span><span class="regex">xsl:call-template&gt;
    &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:element</span><span class="punct">&gt;</span>
  <span class="punct">&lt;/</span><span class="regex">xsl:template&gt;
  
  &lt;!--
    Section and section title processing
  --&gt;

  &lt;!--
    Nest elements that *follow* an h1, h2, etc. into &lt;section&gt; elements
    such that the &lt;h1&gt; content is the section's &lt;title&gt;.
  --&gt;
  &lt;xsl:template name=&quot;section-content&quot;&gt;
    &lt;xsl:param name=&quot;level&quot;</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">nodes</span><span class="punct">&quot;/&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">h1</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">concat('h', $level)</span><span class="punct">&quot;/&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">h2</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">concat('h', $level+1)</span><span class="punct">&quot;/&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:param</span> <span class="ident">name</span><span class="punct">=&quot;</span><span class="string">h2-position</span><span class="punct">&quot;</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">count(exslt:node-set($nodes)[1]/following-sibling::*[local-name()=$h2])</span><span class="punct">&quot;/&gt;</span>
    
    <span class="punct">&lt;!--</span> <span class="ident">copy</span> <span class="ident">up</span> <span class="ident">to</span> <span class="ident">first</span> <span class="ident">h2</span> <span class="punct">--&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">exslt:node-set($nodes)[
                         count(following-sibling::*[local-name()=$h2])=$h2-position
                         ]</span><span class="punct">&quot;/&gt;</span>
    
    <span class="punct">&lt;!--</span> <span class="keyword">if</span> <span class="ident">section</span> <span class="ident">is</span> <span class="ident">empty</span><span class="punct">,</span> <span class="ident">add</span> <span class="ident">an</span> <span class="ident">empty</span> <span class="ident">para</span> <span class="ident">so</span> <span class="ident">it</span> <span class="ident">will</span> <span class="ident">validate</span> <span class="punct">--&gt;</span>
    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:if</span> <span class="ident">test</span><span class="punct">=&quot;</span><span class="string">not(exslt:node-set($nodes)/h:para[
            count(following-sibling::*[local-name()=$h2])=$h2-position
            ])</span><span class="punct">&quot;&gt;</span>
      <span class="punct">&lt;</span><span class="ident">para</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xsl:if&gt;
    
    &lt;!-- subsections --&gt;
    &lt;xsl:for-each select=&quot;exslt:node-set($nodes)[local-name()=$h2]&quot;&gt;
      &lt;section&gt;
        &lt;xsl:variable name=&quot;mynodes&quot; select=&quot;exslt:node-set($nodes)[
                      count(following-sibling::*[local-name()=$h2])=
                      count(current()</span><span class="punct">/</span><span class="ident">following</span><span class="punct">-</span><span class="ident">sibling</span><span class="punct">::*[</span><span class="ident">local</span><span class="punct">-</span><span class="ident">name</span><span class="punct">()=</span><span class="global">$h2</span><span class="punct">])]&quot;</span><span class="string">/&gt;
        &lt;xsl:for-each select=</span><span class="punct">&quot;</span><span class="ident">exslt</span><span class="symbol">:node</span><span class="punct">-</span><span class="ident">set</span><span class="punct">(</span><span class="global">$mynodes</span><span class="punct">)[</span><span class="ident">local</span><span class="punct">-</span><span class="ident">name</span><span class="punct">()=</span><span class="global">$h2</span><span class="punct">]&quot;</span><span class="string">&gt;
          &lt;xsl:choose&gt;
            &lt;xsl:when test=</span><span class="punct">&quot;</span><span class="attribute">@id</span><span class="punct">&quot;</span><span class="string">&gt;
              &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="attribute">@id</span><span class="punct">&quot;</span><span class="string">/&gt;
            &lt;/xsl:when&gt;
            &lt;xsl:when test=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:a</span><span class="punct">/</span><span class="attribute">@name</span><span class="punct">&quot;</span><span class="string">&gt;
              &lt;xsl:attribute name=</span><span class="punct">&quot;</span><span class="ident">id</span><span class="punct">&quot;</span><span class="string">&gt;
                &lt;xsl:value-of select=</span><span class="punct">&quot;</span><span class="ident">concat</span><span class="punct">(</span><span class="global">$anchor</span><span class="punct">-</span><span class="ident">id</span><span class="punct">-</span><span class="ident">prefix</span><span class="punct">,</span> <span class="ident">h</span><span class="symbol">:a</span><span class="punct">/</span><span class="attribute">@name</span><span class="punct">)&quot;</span><span class="string">/&gt;
              &lt;/xsl:attribute&gt;
            &lt;/xsl:when&gt;
          &lt;/xsl:choose&gt;
        &lt;/xsl:for-each&gt;
        &lt;xsl:call-template name=</span><span class="punct">&quot;</span><span class="ident">section</span><span class="punct">-</span><span class="ident">content</span><span class="punct">&quot;</span><span class="string">&gt;
          &lt;xsl:with-param name=</span><span class="punct">&quot;</span><span class="ident">level</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;</span><span class="global">$level</span><span class="punct">+</span><span class="number">1</span><span class="punct">&quot;</span><span class="string">/&gt;
          &lt;xsl:with-param name=</span><span class="punct">&quot;</span><span class="ident">nodes</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;</span><span class="ident">exslt</span><span class="symbol">:node</span><span class="punct">-</span><span class="ident">set</span><span class="punct">(</span><span class="global">$nodes</span><span class="punct">)[</span>
                          <span class="ident">count</span><span class="punct">(</span><span class="ident">following</span><span class="punct">-</span><span class="ident">sibling</span><span class="punct">::*[</span><span class="ident">local</span><span class="punct">-</span><span class="ident">name</span><span class="punct">()=</span><span class="global">$h2</span><span class="punct">])=</span>
                          <span class="ident">count</span><span class="punct">(</span><span class="ident">current</span><span class="punct">()/</span><span class="ident">following</span><span class="punct">-</span><span class="ident">sibling</span><span class="punct">::*[</span><span class="ident">local</span><span class="punct">-</span><span class="ident">name</span><span class="punct">()=</span><span class="global">$h2</span><span class="punct">])]&quot;</span><span class="string">/&gt;
        &lt;/xsl:call-template&gt;
      &lt;/section&gt;
    &lt;/xsl:for-each&gt;
  &lt;/xsl:template&gt;
  
  &lt;!--
    Remove anchors from hn titles.  section-content attaches these as ids
    to the section (after mutilating them as described in the docs).
  --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:h1</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:h2</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:h3</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:h4</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:h5</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:h6</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;title&gt;
      &lt;xsl:apply-templates mode=</span><span class="punct">&quot;</span><span class="ident">skip</span><span class="punct">-</span><span class="ident">anchors</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;</span><span class="ident">node</span><span class="punct">()&quot;</span><span class="string">/&gt;
    &lt;/title&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template mode=</span><span class="punct">&quot;</span><span class="ident">skip</span><span class="punct">-</span><span class="ident">anchors</span><span class="punct">&quot;</span><span class="string"> match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:a</span><span class="punct">[</span><span class="attribute">@name</span><span class="punct">]&quot;</span><span class="string">&gt;
    &lt;xsl:apply-templates/&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template mode=</span><span class="punct">&quot;</span><span class="ident">skip</span><span class="punct">-</span><span class="ident">anchors</span><span class="punct">&quot;</span><span class="string"> match=</span><span class="punct">&quot;</span><span class="ident">node</span><span class="punct">()&quot;</span><span class="string">&gt;
    &lt;xsl:apply-templates select=</span><span class="punct">&quot;.&quot;</span><span class="string">/&gt;
  &lt;/xsl:template&gt;
  
  &lt;!--
    Inline elements
  --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:b</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:i</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:em</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:strong</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;emphasis role=</span><span class="punct">&quot;{</span><span class="ident">local</span><span class="punct">-</span><span class="ident">name</span><span class="punct">()}&quot;</span><span class="string">&gt;
      &lt;xsl:apply-templates/&gt;
    &lt;/emphasis&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:dfn</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;indexterm significance=</span><span class="punct">&quot;</span><span class="ident">preferred</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;primary&gt;&lt;xsl:apply-templates/&gt;&lt;/primary&gt;
    &lt;/indexterm&gt;
    &lt;glossterm&gt;&lt;xsl:apply-templates/&gt;&lt;/glossterm&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:var</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;replaceable&gt;&lt;xsl:apply-templates/&gt;&lt;/replaceable&gt;
  &lt;/xsl:template&gt;
  
  &lt;!--
    Inline elements in code
  --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:code</span><span class="punct">/</span><span class="ident">h</span><span class="symbol">:i</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:tt</span><span class="punct">/</span><span class="ident">h</span><span class="symbol">:i</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:pre</span><span class="punct">/</span><span class="ident">h</span><span class="symbol">:i</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;replaceable&gt;
      &lt;xsl:apply-templates/&gt;
    &lt;/replaceable&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:code</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:tt</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;literal&gt;
      &lt;xsl:if test=</span><span class="punct">&quot;</span><span class="attribute">@class</span><span class="punct">&quot;</span><span class="string">&gt;
        &lt;xsl:attribute name=</span><span class="punct">&quot;</span><span class="ident">role</span><span class="punct">&quot;</span><span class="string">&gt;&lt;xsl:value-of select=</span><span class="punct">&quot;</span><span class="attribute">@class</span><span class="punct">&quot;</span><span class="string">/&gt;&lt;/xsl:attribute&gt;
      &lt;/xsl:if&gt;
      &lt;xsl:apply-templates/&gt;
    &lt;/literal&gt;
  &lt;/xsl:template&gt;
  
  &lt;!-- For now, everything that doesn't have a specific match in inline
       processing mode is matched against the default processing mode. --&gt;
  &lt;xsl:template mode=</span><span class="punct">&quot;</span><span class="ident">inline</span><span class="punct">&quot;</span><span class="string"> match=</span><span class="punct">&quot;*&quot;</span><span class="string">&gt;
    &lt;xsl:apply-templates select=</span><span class="punct">&quot;.&quot;</span><span class="string">/&gt;
  &lt;/xsl:template&gt;
  
  &lt;!--
    Block elements
  --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:p</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;para&gt;
      &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="attribute">@id</span><span class="punct">&quot;</span><span class="string">/&gt;
      &lt;xsl:apply-templates mode=</span><span class="punct">&quot;</span><span class="ident">inline</span><span class="punct">&quot;</span><span class="string">/&gt;
    &lt;/para&gt;
  &lt;/xsl:template&gt;
  
  &lt;!-- Wrap naked text nodes in a &lt;para&gt; so that they'll process more
       correctly.  The h:body also warns about these, because even
       this preprocessing step isn't guaranteed to fix them.  This is
       because </span><span class="punct">&quot;</span><span class="constant">Some</span> <span class="punct">&lt;</span><span class="ident">i</span><span class="punct">&gt;</span><span class="ident">italic</span><span class="punct">&lt;/</span><span class="regex">i&gt; text&quot; will be preprocessed into
       &quot;&lt;para&gt;Some &lt;</span><span class="punct">/</span><span class="ident">para</span><span class="punct">&gt;</span> <span class="punct">&lt;</span><span class="ident">emphasis</span><span class="punct">&gt;</span><span class="ident">italic</span><span class="punct">&lt;/</span><span class="regex">emphasis&gt;&lt;para&gt;
       text&lt;</span><span class="punct">/</span><span class="ident">para</span><span class="punct">&gt;&quot;</span><span class="string"> instead of </span><span class="punct">&quot;&lt;</span><span class="ident">para</span><span class="punct">&gt;</span><span class="constant">Some</span> <span class="punct">&lt;</span><span class="ident">emphasis</span><span class="punct">&gt;</span><span class="ident">italic</span><span class="punct">&lt;/</span><span class="regex">emphasis&gt;
       text&lt;</span><span class="punct">/</span><span class="ident">para</span><span class="punct">&gt;&quot;</span><span class="string">.  Getting this right would require more work than
       just maintaining the source documents. --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:body</span><span class="punct">/</span><span class="ident">text</span><span class="punct">()[</span><span class="ident">normalize</span><span class="punct">-</span><span class="ident">space</span><span class="punct">()!=</span> <span class="punct">'</span><span class="string"></span><span class="punct">']&quot;</span><span class="string">&gt;
    &lt;!-- add an invalid tag to make it easy to find this in
         the generated file --&gt;
    &lt;naked-text&gt;
      &lt;para&gt;
        &lt;xsl:apply-templates/&gt;
      &lt;/para&gt;
    &lt;/naked-text&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:body</span><span class="punct">/</span><span class="ident">h</span><span class="symbol">:code</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:pre</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;programlisting&gt;
      &lt;xsl:apply-templates/&gt;
    &lt;/programlisting&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:blockquote</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;blockquote&gt;
      &lt;xsl:apply-templates mode=</span><span class="punct">&quot;</span><span class="ident">item</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;.&quot;</span><span class="string">/&gt;
    &lt;/blockquote&gt;
  &lt;/xsl:template&gt;
  
  &lt;!--
    Images
  --&gt;
  &lt;xsl:template name=</span><span class="punct">&quot;</span><span class="ident">imageobject</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;imageobject&gt;
      &lt;imagedata fileref=</span><span class="punct">&quot;{</span><span class="attribute">@src</span><span class="punct">}&quot;</span><span class="string">&gt;
        &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="attribute">@width</span><span class="punct">|</span><span class="attribute">@height</span><span class="punct">&quot;</span><span class="string">/&gt;
      &lt;/imagedata&gt;
    &lt;/imageobject&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:img</span><span class="punct">/</span><span class="attribute">@width</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;xsl:copy/&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:img</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;xsl:param name=</span><span class="punct">&quot;</span><span class="ident">informal</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:if test=</span><span class="punct">&quot;</span><span class="keyword">not</span><span class="punct">(</span><span class="attribute">@title</span><span class="punct">)</span> <span class="keyword">and</span> <span class="keyword">not</span><span class="punct">(</span><span class="ident">db</span><span class="symbol">:title</span><span class="punct">)&quot;</span><span class="string">&gt;informal&lt;/xsl:if&gt;
    &lt;/xsl:param&gt;
    &lt;xsl:element name=</span><span class="punct">&quot;{</span><span class="global">$informal</span><span class="punct">}</span><span class="ident">figure</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="attribute">@id</span><span class="punct">&quot;</span><span class="string">/&gt;
      &lt;xsl:choose&gt;
        &lt;xsl:when test=</span><span class="punct">&quot;</span><span class="attribute">@title</span><span class="punct">&quot;</span><span class="string">&gt;
          &lt;title&gt;&lt;xsl:value-of select=</span><span class="punct">&quot;</span><span class="attribute">@title</span><span class="punct">&quot;</span><span class="string">/&gt;&lt;/title&gt;
        &lt;/xsl:when&gt;
        &lt;xsl:otherwise&gt;
          &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="ident">db</span><span class="symbol">:title</span><span class="punct">&quot;</span><span class="string">/&gt;
        &lt;/xsl:otherwise&gt;
      &lt;/xsl:choose&gt;
      &lt;mediaobject&gt;
        &lt;xsl:call-template name=</span><span class="punct">&quot;</span><span class="ident">imageobject</span><span class="punct">&quot;</span><span class="string">/&gt;
        &lt;xsl:if test=</span><span class="punct">&quot;</span><span class="attribute">@alt</span> <span class="keyword">and</span> <span class="ident">normalize</span><span class="punct">-</span><span class="ident">space</span><span class="punct">(</span><span class="attribute">@alt</span><span class="punct">)!='</span><span class="string"></span><span class="punct">'&quot;</span><span class="string">&gt;
          &lt;caption&gt;
            &lt;para&gt;
              &lt;xsl:value-of select=</span><span class="punct">&quot;</span><span class="attribute">@alt</span><span class="punct">&quot;</span><span class="string">/&gt;
            &lt;/para&gt;
          &lt;/caption&gt;
        &lt;/xsl:if&gt;
      &lt;/mediaobject&gt;
    &lt;/xsl:element&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template mode=</span><span class="punct">&quot;</span><span class="ident">inline</span><span class="punct">&quot;</span><span class="string"> match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:img</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;inlinemediaobject&gt;
      &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="attribute">@id</span><span class="punct">&quot;</span><span class="string">/&gt;
      &lt;xsl:call-template name=</span><span class="punct">&quot;</span><span class="ident">imageobject</span><span class="punct">&quot;</span><span class="string">/&gt;
    &lt;/inlinemediaobject&gt;
  &lt;/xsl:template&gt;
  
  &lt;!--
    links
  --&gt;
  
  &lt;!-- anchors --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:a</span><span class="punct">[</span><span class="attribute">@name</span><span class="punct">]&quot;</span><span class="string">&gt;
    &lt;anchor id=</span><span class="punct">&quot;{</span><span class="global">$anchor</span><span class="punct">-</span><span class="ident">id</span><span class="punct">-</span><span class="ident">prefix</span><span class="punct">}{</span><span class="attribute">@name</span><span class="punct">}&quot;</span><span class="string">/&gt;
    &lt;xsl:apply-templates/&gt;
  &lt;/xsl:template&gt;
  
  &lt;!-- internal link --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:a</span><span class="punct">[</span><span class="ident">starts</span><span class="punct">-</span><span class="ident">with</span><span class="punct">(</span><span class="attribute">@href</span><span class="punct">,</span> <span class="punct">'</span><span class="string">#</span><span class="punct">')]&quot;</span><span class="string">&gt;
    &lt;link linkend=</span><span class="punct">&quot;{</span><span class="global">$anchor</span><span class="punct">-</span><span class="ident">id</span><span class="punct">-</span><span class="ident">prefix</span><span class="punct">}{</span><span class="ident">substring</span><span class="punct">-</span><span class="ident">after</span><span class="punct">(</span><span class="attribute">@href</span><span class="punct">,</span> <span class="punct">'</span><span class="string">#</span><span class="punct">')}&quot;</span><span class="string">&gt;
      &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="attribute">@</span><span class="punct">*&quot;</span><span class="string">/&gt;
      &lt;xsl:apply-templates/&gt;
    &lt;/link&gt;
  &lt;/xsl:template&gt;
  
  &lt;!-- external link --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:a</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;ulink url=</span><span class="punct">&quot;{</span><span class="attribute">@href</span><span class="punct">}&quot;</span><span class="string">&gt;
      &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="attribute">@</span><span class="punct">*&quot;</span><span class="string">/&gt;
      &lt;xsl:apply-templates/&gt;
    &lt;/ulink&gt;
  &lt;/xsl:template&gt;
  
  &lt;!-- email --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:a</span><span class="punct">[</span><span class="ident">starts</span><span class="punct">-</span><span class="ident">with</span><span class="punct">(</span><span class="attribute">@href</span><span class="punct">,</span> <span class="punct">'</span><span class="string">mailto:</span><span class="punct">')]&quot;</span><span class="string">&gt;
    &lt;email&gt;
      &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="attribute">@</span><span class="punct">*&quot;</span><span class="string">/&gt;
      &lt;xsl:value-of select=</span><span class="punct">&quot;</span><span class="ident">substring</span><span class="punct">-</span><span class="ident">after</span><span class="punct">(</span><span class="attribute">@href</span><span class="punct">,</span> <span class="punct">'</span><span class="string">mailto:</span><span class="punct">')&quot;</span><span class="string">/&gt;
    &lt;/email&gt;
  &lt;/xsl:template&gt;
  
  &lt;!-- link attributes --&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:a</span><span class="punct">/</span><span class="attribute">@</span><span class="punct">*&quot;</span><span class="string">/&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:a</span><span class="punct">/</span><span class="attribute">@id</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="attribute">@id</span><span class="punct">&quot;</span><span class="string">/&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:a</span><span class="punct">/</span><span class="attribute">@target</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:a</span><span class="punct">/</span><span class="attribute">@link</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;xsl:processing-instruction name=</span><span class="punct">&quot;</span><span class="ident">db2html</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:text&gt;attribute name=</span><span class="punct">&quot;&lt;/</span><span class="regex">xsl:text&gt;
      &lt;xsl:value-of select=&quot;name()&quot;</span><span class="punct">/&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:text</span><span class="punct">&gt;&quot;</span><span class="string"> value=&lt;/xsl:text&gt;
      &lt;xsl:call-template name=</span><span class="punct">&quot;</span><span class="ident">quote</span><span class="punct">&quot;</span><span class="string">/&gt;
    &lt;/xsl:processing-instruction&gt;
  &lt;/xsl:template&gt;
  
  &lt;!--
    lists
  --&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:dl</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;variablelist&gt;
      &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="ident">db</span><span class="punct">:*&quot;</span><span class="string">/&gt;
      &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:dt</span><span class="punct">&quot;</span><span class="string">/&gt;
    &lt;/variablelist&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:dt</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;xsl:variable name=</span><span class="punct">&quot;</span><span class="ident">item</span><span class="punct">-</span><span class="ident">number</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;</span><span class="ident">count</span><span class="punct">(</span><span class="ident">preceding</span><span class="punct">-</span><span class="ident">sibling</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:dt</span><span class="punct">)+</span><span class="number">1</span><span class="punct">&quot;</span><span class="string">/&gt;
    &lt;varlistentry&gt;
      &lt;term&gt;
        &lt;xsl:apply-templates/&gt;
      &lt;/term&gt;
      &lt;listitem&gt;
        &lt;!-- Select the dd that follows this dt without an intervening dd --&gt;
        &lt;xsl:apply-templates mode=</span><span class="punct">&quot;</span><span class="ident">item</span><span class="punct">&quot;</span><span class="string">
                             select=</span><span class="punct">&quot;</span><span class="ident">following</span><span class="punct">-</span><span class="ident">sibling</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:dd</span><span class="punct">[</span>
                             <span class="ident">count</span><span class="punct">(</span><span class="ident">preceding</span><span class="punct">-</span><span class="ident">sibling</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:dt</span><span class="punct">)=</span><span class="global">$item</span><span class="punct">-</span><span class="ident">number</span>
                             <span class="punct">]&quot;</span><span class="string">/&gt;
        &lt;!-- If there is no such dd, then insert an empty para --&gt;
        &lt;xsl:if test=</span><span class="punct">&quot;</span><span class="ident">count</span><span class="punct">(</span><span class="ident">following</span><span class="punct">-</span><span class="ident">sibling</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:dd</span><span class="punct">[</span>
                <span class="ident">count</span><span class="punct">(</span><span class="ident">preceding</span><span class="punct">-</span><span class="ident">sibling</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:dt</span><span class="punct">)=</span><span class="global">$item</span><span class="punct">-</span><span class="ident">number</span>
                <span class="punct">])=</span><span class="number">0</span><span class="punct">&quot;</span><span class="string">&gt;
          &lt;para/&gt;
        &lt;/xsl:if&gt;
      &lt;/listitem&gt;
    &lt;/varlistentry&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template mode=</span><span class="punct">&quot;</span><span class="ident">item</span><span class="punct">&quot;</span><span class="string"> match=</span><span class="punct">&quot;*[</span><span class="ident">count</span><span class="punct">(</span><span class="ident">h</span><span class="symbol">:p</span><span class="punct">)</span> <span class="punct">=</span> <span class="number">0</span><span class="punct">]&quot;</span><span class="string">&gt;
    &lt;para&gt;
      &lt;xsl:apply-templates/&gt;
    &lt;/para&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template mode=</span><span class="punct">&quot;</span><span class="ident">nonblank</span><span class="punct">-</span><span class="ident">nodes</span><span class="punct">&quot;</span><span class="string"> match=</span><span class="punct">&quot;</span><span class="ident">node</span><span class="punct">()&quot;</span><span class="string">&gt;
    &lt;xsl:element name=</span><span class="punct">&quot;{</span><span class="ident">local</span><span class="punct">-</span><span class="ident">name</span><span class="punct">()}&quot;</span><span class="string">/&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template mode=</span><span class="punct">&quot;</span><span class="ident">nonblank</span><span class="punct">-</span><span class="ident">nodes</span><span class="punct">&quot;</span><span class="string"> match=</span><span class="punct">&quot;</span><span class="ident">text</span><span class="punct">()[</span><span class="ident">normalize</span><span class="punct">-</span><span class="ident">space</span><span class="punct">()='</span><span class="string"></span><span class="punct">']&quot;</span><span class="string">/&gt;
  
  &lt;xsl:template mode=</span><span class="punct">&quot;</span><span class="ident">nonblank</span><span class="punct">-</span><span class="ident">nodes</span><span class="punct">&quot;</span><span class="string"> match=</span><span class="punct">&quot;</span><span class="ident">text</span><span class="punct">()&quot;</span><span class="string">&gt;
    &lt;text/&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template mode=</span><span class="punct">&quot;</span><span class="ident">item</span><span class="punct">&quot;</span><span class="string"> match=</span><span class="punct">&quot;*&quot;</span><span class="string">&gt;
    &lt;!-- Test whether the first non-blank node is not a p --&gt;
    &lt;xsl:param name=</span><span class="punct">&quot;</span><span class="ident">nonblank</span><span class="punct">-</span><span class="ident">nodes</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:apply-templates mode=</span><span class="punct">&quot;</span><span class="ident">nonblank</span><span class="punct">-</span><span class="ident">nodes</span><span class="punct">&quot;</span><span class="string">/&gt;
    &lt;/xsl:param&gt;
    
    &lt;xsl:param name=</span><span class="punct">&quot;</span><span class="ident">tested</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;</span>
               <span class="ident">count</span><span class="punct">(</span><span class="ident">exslt</span><span class="symbol">:node</span><span class="punct">-</span><span class="ident">set</span><span class="punct">(</span><span class="global">$nonblank</span><span class="punct">-</span><span class="ident">nodes</span><span class="punct">)/*)</span> <span class="punct">!=</span> <span class="number">0</span> <span class="keyword">and</span>
               <span class="ident">local</span><span class="punct">-</span><span class="ident">name</span><span class="punct">(</span><span class="ident">exslt</span><span class="symbol">:node</span><span class="punct">-</span><span class="ident">set</span><span class="punct">(</span><span class="global">$nonblank</span><span class="punct">-</span><span class="ident">nodes</span><span class="punct">)/*[</span><span class="number">1</span><span class="punct">])</span> <span class="punct">!=</span> <span class="punct">'</span><span class="string">p</span><span class="punct">'&quot;</span><span class="string">/&gt;
    
    &lt;xsl:param name=</span><span class="punct">&quot;</span><span class="ident">n1</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;</span><span class="ident">count</span><span class="punct">(*[</span><span class="number">1</span><span class="punct">]/</span><span class="ident">following</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:p</span><span class="punct">)&quot;</span><span class="string">/&gt;
    &lt;xsl:param name=</span><span class="punct">&quot;</span><span class="ident">n2</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;</span><span class="ident">count</span><span class="punct">(</span><span class="ident">text</span><span class="punct">()[</span><span class="number">1</span><span class="punct">]/</span><span class="ident">following</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:p</span><span class="punct">)&quot;</span><span class="string">/&gt;
    
    &lt;xsl:param name=</span><span class="punct">&quot;</span><span class="ident">n</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:if test=</span><span class="punct">&quot;</span><span class="global">$tested</span><span class="punct">&quot;</span><span class="string">&gt;
        &lt;xsl:value-of select=</span><span class="punct">&quot;</span><span class="ident">java</span><span class="symbol">:java</span><span class="punct">.</span><span class="ident">lang</span><span class="punct">.</span><span class="ident">Math</span><span class="punct">.</span><span class="ident">max</span><span class="punct">(</span><span class="global">$n1</span><span class="punct">,</span> <span class="global">$n2</span><span class="punct">)&quot;</span><span class="string">/&gt;
      &lt;/xsl:if&gt;
    &lt;/xsl:param&gt;
    
    &lt;xsl:if test=</span><span class="punct">&quot;</span><span class="constant">false</span><span class="punct">()&quot;</span><span class="string">&gt;
      &lt;nodeset tested=</span><span class="punct">&quot;{</span><span class="global">$tested</span><span class="punct">}&quot;</span><span class="string"> count=</span><span class="punct">&quot;{</span><span class="ident">count</span><span class="punct">(</span><span class="ident">exslt</span><span class="symbol">:node</span><span class="punct">-</span><span class="ident">set</span><span class="punct">(</span><span class="global">$nonblank</span><span class="punct">-</span><span class="ident">nodes</span><span class="punct">)/*)}&quot;</span><span class="string">&gt;
        &lt;xsl:for-each select=</span><span class="punct">&quot;</span><span class="ident">exslt</span><span class="symbol">:node</span><span class="punct">-</span><span class="ident">set</span><span class="punct">(</span><span class="global">$nonblank</span><span class="punct">-</span><span class="ident">nodes</span><span class="punct">)/*&quot;</span><span class="string">&gt;
          &lt;element name=</span><span class="punct">&quot;{</span><span class="ident">local</span><span class="punct">-</span><span class="ident">name</span><span class="punct">()}&quot;</span><span class="string">/&gt;
        &lt;/xsl:for-each&gt;
      &lt;/nodeset&gt;
    &lt;/xsl:if&gt;
    
    &lt;!-- Wrap everything before the first p into a para --&gt;
    &lt;xsl:if test=</span><span class="punct">&quot;</span><span class="global">$tested</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;para&gt;
        &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span>
                             <span class="ident">node</span><span class="punct">()[</span><span class="ident">count</span><span class="punct">(</span><span class="ident">following</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:p</span><span class="punct">)=</span><span class="global">$n</span><span class="punct">]</span> <span class="punct">|</span>
                             <span class="ident">text</span><span class="punct">()[</span><span class="ident">count</span><span class="punct">(</span><span class="ident">following</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:p</span><span class="punct">)=</span><span class="global">$n</span><span class="punct">]&quot;</span><span class="string">/&gt;
      &lt;/para&gt;
    &lt;/xsl:if&gt;
    &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span>
                         <span class="ident">node</span><span class="punct">()[</span><span class="ident">count</span><span class="punct">(</span><span class="ident">following</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:p</span><span class="punct">)!=</span><span class="global">$n</span><span class="punct">]</span> <span class="punct">|</span>
                         <span class="ident">text</span><span class="punct">()[</span><span class="ident">count</span><span class="punct">(</span><span class="ident">following</span><span class="punct">::</span><span class="ident">h</span><span class="symbol">:p</span><span class="punct">)!=</span><span class="global">$n</span><span class="punct">]&quot;</span><span class="string">/&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:ol</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;orderedlist spacing=</span><span class="punct">&quot;</span><span class="ident">compact</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:for-each select=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:li</span><span class="punct">&quot;</span><span class="string">&gt;
        &lt;listitem&gt;
          &lt;xsl:apply-templates mode=</span><span class="punct">&quot;</span><span class="ident">item</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;.&quot;</span><span class="string">/&gt;
        &lt;/listitem&gt;
      &lt;/xsl:for-each&gt;
    &lt;/orderedlist&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:ul</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;itemizedlist spacing=</span><span class="punct">&quot;</span><span class="ident">compact</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:for-each select=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:li</span><span class="punct">&quot;</span><span class="string">&gt;
        &lt;listitem&gt;
          &lt;xsl:apply-templates mode=</span><span class="punct">&quot;</span><span class="ident">item</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;.&quot;</span><span class="string">/&gt;
        &lt;/listitem&gt;
      &lt;/xsl:for-each&gt;
    &lt;/itemizedlist&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:ul</span><span class="punct">[</span><span class="ident">processing</span><span class="punct">-</span><span class="ident">instruction</span><span class="punct">('</span><span class="string">html2db</span><span class="punct">')]&quot;</span><span class="string">&gt;
    &lt;simplelist&gt;
      &lt;xsl:for-each select=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:li</span><span class="punct">&quot;</span><span class="string">&gt;
        &lt;member type=</span><span class="punct">&quot;</span><span class="ident">vert</span><span class="punct">&quot;</span><span class="string">&gt;
          &lt;xsl:apply-templates mode=</span><span class="punct">&quot;</span><span class="ident">item</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;.&quot;</span><span class="string">/&gt;
        &lt;/member&gt;
      &lt;/xsl:for-each&gt;
    &lt;/simplelist&gt;
  &lt;/xsl:template&gt;
  
  &lt;!--
    ignored markup
  --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:br</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;xsl:processing-instruction name=</span><span class="punct">&quot;</span><span class="ident">db2html</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:text&gt;element=</span><span class="punct">&quot;&lt;/</span><span class="regex">xsl:text&gt;
      &lt;xsl:value-of select=&quot;local-name()&quot;</span><span class="punct">/&gt;</span>
      <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:text</span><span class="punct">&gt;&quot;</span><span class="string">&lt;/xsl:text&gt;
    &lt;/xsl:processing-instruction&gt;
  &lt;/xsl:template&gt;
  
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:span</span><span class="punct">|</span><span class="ident">h</span><span class="symbol">:div</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;xsl:apply-templates select=</span><span class="punct">&quot;*|</span><span class="ident">node</span><span class="punct">()|</span><span class="ident">text</span><span class="punct">()&quot;</span><span class="string">/&gt;
  &lt;/xsl:template&gt;
  
  &lt;!--
    Utility functions and templates for tables
  --&gt;
  &lt;xsl:template mode=</span><span class="punct">&quot;</span><span class="ident">count</span><span class="punct">-</span><span class="ident">columns</span><span class="punct">&quot;</span><span class="string"> match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:tr</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;n&gt;
      &lt;xsl:value-of select=</span><span class="punct">&quot;</span><span class="ident">count</span><span class="punct">(</span><span class="ident">h</span><span class="symbol">:td</span><span class="punct">)&quot;</span><span class="string">/&gt;
    &lt;/n&gt;
  &lt;/xsl:template&gt;
  
  &lt;!-- tables --&gt;
  &lt;xsl:template match=</span><span class="punct">&quot;</span><span class="ident">h</span><span class="symbol">:table</span><span class="punct">&quot;</span><span class="string">&gt;
    &lt;xsl:param name=</span><span class="punct">&quot;</span><span class="ident">informal</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:if test=</span><span class="punct">&quot;</span><span class="keyword">not</span><span class="punct">(</span><span class="attribute">@summary</span><span class="punct">)&quot;</span><span class="string">&gt;informal&lt;/xsl:if&gt;
    &lt;/xsl:param&gt;
    &lt;xsl:param name=</span><span class="punct">&quot;</span><span class="ident">colcounts</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:apply-templates mode=</span><span class="punct">&quot;</span><span class="ident">count</span><span class="punct">-</span><span class="ident">columns</span><span class="punct">&quot;</span><span class="string"> select=</span><span class="punct">&quot;.//</span><span class="regex">h:tr&quot;</span><span class="punct">/&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xsl:param&gt;
    &lt;xsl:param name=&quot;cols&quot; select=&quot;math:max(exslt:node-set($colcounts)</span><span class="punct">/</span><span class="ident">n</span><span class="punct">)&quot;</span><span class="string">/&gt;
    &lt;xsl:param name=</span><span class="punct">&quot;</span><span class="ident">sorted</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:for-each select=</span><span class="punct">&quot;</span><span class="ident">exslt</span><span class="symbol">:node</span><span class="punct">-</span><span class="ident">set</span><span class="punct">(</span><span class="global">$colcounts</span><span class="punct">)/</span><span class="ident">n</span><span class="punct">&quot;</span><span class="string">&gt;
        &lt;xsl:sort order=</span><span class="punct">&quot;</span><span class="ident">descending</span><span class="punct">&quot;</span><span class="string"> data-type=</span><span class="punct">&quot;</span><span class="ident">number</span><span class="punct">&quot;</span><span class="string">/&gt;
        &lt;n&gt;&lt;xsl:value-of select=</span><span class="punct">&quot;.&quot;</span><span class="string">/&gt;&lt;/n&gt;
      &lt;/xsl:for-each&gt;
    &lt;/xsl:param&gt;
    &lt;xsl:element name=</span><span class="punct">&quot;{</span><span class="global">$informal</span><span class="punct">}</span><span class="ident">table</span><span class="punct">&quot;</span><span class="string">&gt;
      &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="attribute">@id</span><span class="punct">&quot;</span><span class="string">/&gt;
      &lt;xsl:if test=</span><span class="punct">&quot;</span><span class="ident">processing</span><span class="punct">-</span><span class="ident">instruction</span><span class="punct">('</span><span class="string">html2db</span><span class="punct">')[</span><span class="ident">starts</span><span class="punct">-</span><span class="ident">with</span><span class="punct">(.,</span> <span class="punct">'</span><span class="string">rowsep</span><span class="punct">')]&quot;</span><span class="string">&gt;
        &lt;xsl:attribute name=</span><span class="punct">&quot;</span><span class="ident">rowsep</span><span class="punct">&quot;</span><span class="string">&gt;1&lt;/xsl:attribute&gt;
      &lt;/xsl:if&gt;
      &lt;xsl:apply-templates select=</span><span class="punct">&quot;</span><span class="ident">processing</span><span class="punct">-</span><span class="ident">instruction</span><span class="punct">()&quot;</span><span class="string">/&gt;
      &lt;xsl:if test=</span><span class="punct">&quot;</span><span class="attribute">@summary</span><span class="punct">&quot;</span><span class="string">&gt;
        &lt;title&gt;&lt;xsl:value-of select=</span><span class="punct">&quot;</span><span class="attribute">@summary</span><span class="punct">&quot;</span><span class="string">/&gt;&lt;/title&gt;
      &lt;/xsl:if&gt;
      &lt;tgroup cols=</span><span class="punct">&quot;{</span><span class="global">$cols</span><span class="punct">}&quot;</span><span class="string">&gt;
        &lt;xsl:if test=</span><span class="punct">&quot;.//</span><span class="regex">h:tr</span><span class="punct">/</span><span class="ident">h</span><span class="symbol">:th</span><span class="punct">&quot;</span><span class="string">&gt;
          &lt;thead&gt;
            &lt;xsl:for-each select=</span><span class="punct">&quot;.//</span><span class="regex">h:tr[count(h:th)!=0]&quot;&gt;
              &lt;row&gt;
                &lt;xsl:apply-templates select=&quot;@id&quot;</span><span class="punct">/&gt;</span>
                <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:for</span><span class="punct">-</span><span class="ident">each</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">h:td|h:th</span><span class="punct">&quot;&gt;</span>
                  <span class="punct">&lt;</span><span class="ident">entry</span><span class="punct">&gt;</span>
                    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">@id</span><span class="punct">&quot;/&gt;</span>
                    <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span><span class="punct">/&gt;</span>
                  <span class="punct">&lt;/</span><span class="regex">entry&gt;
                &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:for</span><span class="punct">-</span><span class="ident">each</span><span class="punct">&gt;</span>
              <span class="punct">&lt;/</span><span class="regex">row&gt;
              &amp;cr;
            &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:for</span><span class="punct">-</span><span class="ident">each</span><span class="punct">&gt;</span>
          <span class="punct">&lt;/</span><span class="regex">thead&gt;
        &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:if</span><span class="punct">&gt;</span>
        <span class="punct">&lt;</span><span class="ident">tbody</span><span class="punct">&gt;</span>
          <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:for</span><span class="punct">-</span><span class="keyword">each</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">.//h:tr[count(h:th)=0]</span><span class="punct">&quot;&gt;</span>
            <span class="punct">&lt;</span><span class="ident">row</span><span class="punct">&gt;</span>
              <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">@id</span><span class="punct">&quot;/&gt;</span>
              <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:for</span><span class="punct">-</span><span class="ident">each</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">h:td|h:th</span><span class="punct">&quot;&gt;</span>
                <span class="punct">&lt;</span><span class="ident">entry</span><span class="punct">&gt;</span>
                  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span> <span class="ident">select</span><span class="punct">=&quot;</span><span class="string">@id</span><span class="punct">&quot;/&gt;</span>
                  <span class="punct">&lt;</span><span class="ident">xsl</span><span class="symbol">:apply</span><span class="punct">-</span><span class="ident">templates</span><span class="punct">/&gt;</span>
                <span class="punct">&lt;/</span><span class="regex">entry&gt;
              &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:for</span><span class="punct">-</span><span class="ident">each</span><span class="punct">&gt;</span>
            <span class="punct">&lt;/</span><span class="regex">row&gt;
            &amp;cr;
          &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:for</span><span class="punct">-</span><span class="ident">each</span><span class="punct">&gt;</span>
        <span class="punct">&lt;/</span><span class="regex">tbody&gt;
      &lt;</span><span class="punct">/</span><span class="ident">tgroup</span><span class="punct">&gt;</span>
    <span class="punct">&lt;/</span><span class="regex">xsl:element&gt;
  &lt;</span><span class="punct">/</span><span class="ident">xsl</span><span class="symbol">:template</span><span class="punct">&gt;</span>
<span class="punct">&lt;/</span><span class="regex">xsl:stylesheet&gt;<span class="normal">
</span></span></pre>