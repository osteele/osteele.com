      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        <title>file_utils.rb</title>
  <link href="/sources/portfolio/stylesheets/ruby.css" rel="stylesheet" type="text/css" />
 </head>
 <body class="ruby"><h1>file_utils.rb</h1>
<pre><span class="comment"># Author:: Oliver Steele</span>
<span class="comment"># Copyright:: Copyright (c) 2005-2006 Oliver Steele.  All rights reserved.</span>
<span class="comment"># License:: Ruby License.</span>
 
<span class="keyword">module </span><span class="module">OpenLaszlo</span>
  <span class="keyword">def </span><span class="method">self.rsync</span> <span class="ident">sources</span><span class="punct">,</span> <span class="ident">target</span><span class="punct">,</span> <span class="ident">options</span><span class="punct">={}</span>
    <span class="comment"># TODO: could implement in this case with fileutils</span>
    <span class="keyword">raise</span> <span class="constant">NotImplementedError</span> <span class="keyword">unless</span> <span class="ident">cmd</span> <span class="punct">=</span> <span class="ident">which</span><span class="punct">('</span><span class="string">rsync</span><span class="punct">')</span>
    <span class="ident">args</span> <span class="punct">=</span> <span class="punct">[]</span>
    <span class="ident">args</span> <span class="punct">&lt;&lt;</span> <span class="punct">'</span><span class="string">--delete</span><span class="punct">'</span> <span class="keyword">if</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:delete</span><span class="punct">]</span>
    <span class="constant">ENV</span><span class="punct">['</span><span class="string">PATH</span><span class="punct">'].</span><span class="ident">split</span><span class="punct">('</span><span class="string">:</span><span class="punct">').</span><span class="ident">any?</span> <span class="punct">{|</span><span class="ident">p</span><span class="punct">|</span><span class="constant">File</span><span class="punct">.</span><span class="ident">exists?</span><span class="punct">(</span><span class="constant">File</span><span class="punct">.</span><span class="ident">join</span><span class="punct">(</span><span class="ident">p</span><span class="punct">,'</span><span class="string">rsync</span><span class="punct">'))}</span>
    `<span class="comment">#{cmd} -avz #{args.join(' ')} #{sources} #{target}`</span>
  <span class="keyword">end</span>
  
  <span class="comment"># Returns pathname, pathname.bat, pathname.exe, or nil</span>
  <span class="keyword">def </span><span class="method">self.which</span> <span class="ident">name</span>
    <span class="ident">extensions</span> <span class="punct">=</span> <span class="punct">['</span><span class="string"></span><span class="punct">']</span>
    <span class="ident">extensions</span> <span class="punct">+=</span> <span class="punct">['</span><span class="string">.exe</span><span class="punct">',</span> <span class="punct">'</span><span class="string">.bat</span><span class="punct">']</span> <span class="keyword">if</span> <span class="ident">windows?</span>
    <span class="ident">extensions</span><span class="punct">.</span><span class="ident">each</span> <span class="keyword">do</span> <span class="punct">|</span><span class="ident">ext</span><span class="punct">|</span>
      <span class="ident">target</span> <span class="punct">=</span> <span class="ident">name</span><span class="punct">+</span><span class="ident">ext</span>
      <span class="ident">dir</span> <span class="punct">=</span> <span class="constant">ENV</span><span class="punct">['</span><span class="string">PATH</span><span class="punct">'].</span><span class="ident">split</span><span class="punct">('</span><span class="string">:</span><span class="punct">').</span><span class="ident">find</span> <span class="punct">{|</span><span class="ident">p</span><span class="punct">|</span><span class="constant">File</span><span class="punct">.</span><span class="ident">exists?</span><span class="punct">(</span><span class="constant">File</span><span class="punct">.</span><span class="ident">join</span><span class="punct">(</span><span class="ident">p</span><span class="punct">,</span> <span class="ident">target</span><span class="punct">))}</span>
      <span class="keyword">return</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">join</span><span class="punct">(</span><span class="ident">dir</span><span class="punct">,</span> <span class="ident">target</span><span class="punct">)</span> <span class="keyword">if</span> <span class="ident">dir</span>
    <span class="keyword">end</span>
    <span class="constant">nil</span>
  <span class="keyword">end</span>
  
  <span class="keyword">def </span><span class="method">self.windows?</span>
    <span class="constant">RUBY_PLATFORM</span> <span class="punct">=~</span> <span class="punct">/</span><span class="regex">win</span><span class="punct">/</span> <span class="keyword">and</span> <span class="keyword">not</span> <span class="constant">RUBY_PLATFORM</span> <span class="punct">=~</span> <span class="punct">/</span><span class="regex">darwin</span><span class="punct">/</span>
  <span class="keyword">end</span>
  
  <span class="keyword">class </span><span class="class">::File</span>
    <span class="comment"># Returns true if +parent+ is the parent of +child+.</span>
    <span class="comment"># If the +indirect+ option is true, also returns true if</span>
    <span class="comment"># +parent+ is an ancestor of child.</span>
    <span class="comment">#</span>
    <span class="comment">#   File.contains?('/a', '/a/b') # -&gt; true</span>
    <span class="comment">#   File.contains?('/a', '/b/c') # -&gt; false</span>
    <span class="comment">#   File.contains?('/a', '/a/b/c') # -&gt; false</span>
    <span class="comment">#   File.contains?('/a', '/a/b', :indirect =&gt; true) # -&gt; true</span>
    <span class="comment">#   File.contains?('/a', '/a/b/c', :indirect =&gt; true) # -&gt; true</span>
    <span class="comment">#   File.contains?('/a', '/a') # -&gt; false</span>
    <span class="comment">#   File.contains?('/a', '/a', :indirect =&gt; true) # -&gt; false</span>
    <span class="keyword">def </span><span class="method">self.contains?</span> <span class="ident">parent</span><span class="punct">,</span> <span class="ident">child</span><span class="punct">,</span> <span class="ident">options</span><span class="punct">={}</span>
      <span class="ident">parent</span> <span class="punct">=</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">expand_path</span><span class="punct">(</span><span class="ident">parent</span><span class="punct">)</span>
      <span class="ident">child</span> <span class="punct">=</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">expand_path</span><span class="punct">(</span><span class="ident">child</span><span class="punct">)</span>
      <span class="constant">File</span><span class="punct">.</span><span class="ident">dirname</span><span class="punct">(</span><span class="ident">child</span><span class="punct">)</span> <span class="punct">==</span> <span class="ident">parent</span> <span class="keyword">or</span>
        <span class="ident">options</span><span class="punct">[</span><span class="symbol">:indirect</span><span class="punct">]</span> <span class="punct">==</span> <span class="constant">true</span> <span class="punct">&amp;&amp;</span> <span class="ident">child</span><span class="punct">.</span><span class="ident">index</span><span class="punct">(</span><span class="ident">parent</span> <span class="punct">+</span> <span class="punct">'</span><span class="string">/</span><span class="punct">')</span> <span class="punct">==</span> <span class="number">0</span>
    <span class="keyword">end</span>
  <span class="keyword">end</span>
<span class="keyword">end</span>
</pre></body></html>