      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        <title>compiler.rb</title>
  <link href="/sources/portfolio/stylesheets/ruby.css" rel="stylesheet" type="text/css" />
 </head>
 <body class="ruby"><h1>compiler.rb</h1>
<pre><span class="comment"># Author:: Oliver Steele</span>
<span class="comment"># Copyright:: Copyright (c) 2005-2006 Oliver Steele.  All rights reserved.</span>
<span class="comment"># License:: Ruby License.</span>
 
<span class="comment"># == module OpenLaszlo</span>
<span class="comment"># </span>
<span class="comment"># This module contains utility methods for compiling</span>
<span class="comment"># OpenLaszlo[openlaszlo.org] programs.</span>
<span class="comment">#</span>
<span class="comment"># Example:</span>
<span class="comment">#   # Set up the environment to use the compile server.  The OpenLaszlo server</span>
<span class="comment">#   # must be running in order at this location in order for this to work.</span>
<span class="comment">#   ENV['OPENLASZLO_HOME'] = '/Applications/OpenLaszlo Server 3.1'</span>
<span class="comment">#   ENV['OPENLASZLO_URL'] = 'http://localhost:8080/lps-3.1'</span>
<span class="comment">#</span>
<span class="comment">#   require 'openlaszlo'</span>
<span class="comment">#   # Create a file 'hello.swf' in the current directory:</span>
<span class="comment">#   OpenLaszlo::compile 'hello.lzx'</span>
<span class="comment">#</span>
<span class="comment"># See OpenLaszlo.compile for additional documentation.</span>
<span class="comment"># </span>

<span class="keyword">module </span><span class="module">OpenLaszlo</span>
  <span class="keyword">class </span><span class="class">CompilationError</span> <span class="punct">&lt;</span> <span class="constant">StandardError</span><span class="punct">;</span> <span class="keyword">end</span>
  
  <span class="comment"># This class implements a bridge to the compile server.</span>
  <span class="comment">#</span>
  <span class="comment"># If you don't need multiple compilers, you can use the methods in</span>
  <span class="comment"># the OpenLaszlo module instead.</span>
  <span class="comment">#</span>
  <span class="comment"># CompileServer is faster than CommandLineCompiler.</span>
  <span class="keyword">class </span><span class="class">CompileServer</span>
    <span class="comment"># Options:</span>
    <span class="comment"># * &lt;tt&gt;:openlaszlo_home&lt;/tt&gt; - filesystem location of the Open&lt;tt&gt;&lt;/tt&gt;Laszlo SDK.  Defaults to EVN['OPENLASZLO_HOME']</span>
    <span class="comment"># * &lt;tt&gt;:server_uri&lt;/tt&gt; - the URI of the server.  Defaults to ENV['OPENLASZLO_URL'] if this is specified, otherwise to 'http://localhost:8080/lps-dev'.</span>
    <span class="keyword">def </span><span class="method">initialize</span> <span class="ident">options</span><span class="punct">={}</span>
      <span class="attribute">@server_directory</span> <span class="punct">=</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:home</span><span class="punct">]</span> <span class="punct">||</span> <span class="constant">ENV</span><span class="punct">['</span><span class="string">OPENLASZLO_HOME</span><span class="punct">']</span>
      <span class="attribute">@server_url</span> <span class="punct">=</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:server_uri</span><span class="punct">]</span> <span class="punct">||</span> <span class="constant">ENV</span><span class="punct">['</span><span class="string">OPENLASZLO_URL</span><span class="punct">']</span> <span class="punct">||</span> <span class="punct">'</span><span class="string">http://localhost:8080/lps-dev</span><span class="punct">'</span>
      <span class="attribute">@tmpdir</span> <span class="punct">=</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">join</span> <span class="attribute">@server_directory</span><span class="punct">,</span> <span class="punct">'</span><span class="string">tmp</span><span class="punct">'</span>
    <span class="keyword">end</span>
    
    <span class="comment"># Invokes the Open&lt;tt&gt;&lt;/tt&gt;Laszlo server-based compiler on</span>
    <span class="comment"># +source_file+.  Copies +source_file+ into the home directory</span>
    <span class="comment"># of the server if it is not already there.</span>
    <span class="comment">#</span>
    <span class="comment"># Options:</span>
    <span class="comment"># * &lt;tt&gt;:format&lt;/tt&gt; - request type (default 'swf')</span>
    <span class="comment"># See OpenLaszlo.compile for a description of +options+.</span>
    <span class="keyword">def </span><span class="method">compile</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span><span class="punct">={}</span>
      <span class="keyword">unless</span> <span class="ident">in_server_directory?</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">)</span>
        <span class="keyword">begin</span>
          <span class="ident">copy_sources</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">tmp_directory_for</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">)</span>
          <span class="ident">source_file</span> <span class="punct">=</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">join</span><span class="punct">(</span><span class="ident">tmp_directory_for</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">),</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">basename</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">))</span>
        <span class="keyword">rescue</span> <span class="constant">NotImplementedError</span>
          <span class="keyword">raise</span> <span class="punct">&quot;</span><span class="string">The compiler server couldn't compile <span class="expr">#{source_file}</span> because it isn't in the server home directory (<span class="expr">#{@server_directory}</span>) and rsync isn't installed.  Either unset move the file, install rsync, or unset OPENLASZLO_URL.</span><span class="punct">&quot;</span>
        <span class="keyword">end</span>
      <span class="keyword">end</span>
      <span class="ident">mtime</span> <span class="punct">=</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">mtime</span> <span class="ident">source_file</span>
      <span class="ident">output</span> <span class="punct">=</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:output</span><span class="punct">]</span> <span class="punct">||</span> <span class="punct">&quot;</span><span class="string"><span class="expr">#{File.expand_path(File.join(File.dirname(source_file), File.basename(source_file, '.lzx')))}</span>.swf</span><span class="punct">&quot;</span>
      <span class="ident">compile_object</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">output</span><span class="punct">,</span> <span class="ident">options</span>
      <span class="ident">results</span> <span class="punct">=</span> <span class="ident">request_metadata_for</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span>
      <span class="keyword">raise</span> <span class="punct">&quot;</span><span class="string">Race condition: <span class="expr">#{source_file}</span> was modified during compilation</span><span class="punct">&quot;</span> <span class="keyword">if</span> <span class="ident">mtime</span> <span class="punct">!=</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">mtime</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">)</span>
      <span class="ident">results</span><span class="punct">[</span><span class="symbol">:output</span><span class="punct">]</span> <span class="punct">=</span> <span class="ident">output</span>
      <span class="keyword">raise</span> <span class="constant">CompilationError</span><span class="punct">.</span><span class="ident">new</span><span class="punct">(</span><span class="ident">results</span><span class="punct">[</span><span class="symbol">:error</span><span class="punct">])</span> <span class="keyword">if</span> <span class="ident">results</span><span class="punct">[</span><span class="symbol">:error</span><span class="punct">]</span>
      <span class="keyword">return</span> <span class="ident">results</span>
    <span class="keyword">end</span>
    
    <span class="ident">private</span>
    <span class="keyword">def </span><span class="method">compile_object</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">object</span><span class="punct">,</span> <span class="ident">options</span><span class="punct">={}</span>
      <span class="ident">options</span> <span class="punct">=</span> <span class="punct">{}.</span><span class="ident">update</span><span class="punct">(</span><span class="ident">options</span><span class="punct">).</span><span class="ident">update</span><span class="punct">(</span><span class="symbol">:output</span> <span class="punct">=&gt;</span> <span class="ident">object</span><span class="punct">)</span>
      <span class="ident">request</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span>
    <span class="keyword">end</span>
    
    <span class="keyword">def </span><span class="method">request_metadata_for</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span><span class="punct">={}</span>
      <span class="ident">results</span> <span class="punct">=</span> <span class="punct">{}</span>
      <span class="ident">options</span> <span class="punct">=</span> <span class="punct">{}.</span><span class="ident">update</span><span class="punct">(</span><span class="ident">options</span><span class="punct">).</span><span class="ident">update</span><span class="punct">(</span><span class="symbol">:format</span> <span class="punct">=&gt;</span> <span class="punct">'</span><span class="string">canvas-xml</span><span class="punct">',</span> <span class="symbol">:output</span> <span class="punct">=&gt;</span> <span class="constant">nil</span><span class="punct">)</span>
      <span class="ident">text</span> <span class="punct">=</span> <span class="ident">request</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span>
      <span class="keyword">if</span> <span class="ident">text</span> <span class="punct">=~</span> <span class="punct">%r{</span><span class="regex">&lt;warnings&gt;(.*?)&lt;/warnings&gt;</span><span class="punct">}</span><span class="ident">m</span>
        <span class="ident">results</span><span class="punct">[</span><span class="symbol">:warnings</span><span class="punct">]</span> <span class="punct">=</span> <span class="global">$1</span><span class="punct">.</span><span class="ident">scan</span><span class="punct">(%r{</span><span class="regex">&lt;error&gt;<span class="escape">\s</span>*(.*?)<span class="escape">\s</span>*&lt;/error&gt;</span><span class="punct">}</span><span class="ident">m</span><span class="punct">).</span><span class="ident">map</span><span class="punct">{|</span><span class="ident">w</span><span class="punct">|</span><span class="ident">w</span><span class="punct">.</span><span class="ident">first</span><span class="punct">}</span>
      <span class="keyword">elsif</span> <span class="ident">text</span> <span class="punct">!~</span> <span class="punct">%r{</span><span class="regex">&lt;canvas&gt;</span><span class="punct">}</span> <span class="punct">&amp;&amp;</span> <span class="ident">text</span> <span class="punct">=~</span> <span class="punct">%r{</span><span class="regex">&lt;pre&gt;Error:<span class="escape">\s</span>*(.*?)<span class="escape">\s</span>*&lt;/pre&gt;</span><span class="punct">}</span><span class="ident">m</span>
        <span class="ident">results</span><span class="punct">[</span><span class="symbol">:error</span><span class="punct">]</span> <span class="punct">=</span> <span class="global">$1</span>
      <span class="keyword">end</span>
      <span class="keyword">return</span> <span class="ident">results</span>
    <span class="keyword">end</span>
    
    <span class="keyword">def </span><span class="method">request</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span><span class="punct">={}</span>
      <span class="ident">output</span> <span class="punct">=</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:output</span><span class="punct">]</span>
      <span class="ident">require</span> <span class="punct">'</span><span class="string">net/http</span><span class="punct">'</span>
      <span class="ident">require</span> <span class="punct">'</span><span class="string">uri</span><span class="punct">'</span>
      <span class="comment"># assert that pathname is relative to LPS home:</span>
      <span class="keyword">raise</span> <span class="punct">&quot;</span><span class="string"><span class="expr">#{absolute_path}</span> isn't inside <span class="expr">#{@server_directory}</span></span><span class="punct">&quot;</span> <span class="keyword">unless</span> <span class="ident">in_server_directory?</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">)</span>
      <span class="ident">server_relative_path</span> <span class="punct">=</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">expand_path</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">)[</span><span class="attribute">@server_directory</span><span class="punct">.</span><span class="ident">length</span><span class="punct">..-</span><span class="number">1</span><span class="punct">]</span>
      <span class="comment"># FIXME: this doesn't handle quoting; use recursive File.split instead</span>
      <span class="comment"># FIXME: should encode the url, for filenames that include '/'</span>
      <span class="ident">server_relative_path</span><span class="punct">.</span><span class="ident">gsub</span><span class="punct">(</span><span class="constant">File</span><span class="punct">::</span><span class="constant">Separator</span><span class="punct">,</span> <span class="punct">'</span><span class="string">/</span><span class="punct">')</span>
      <span class="ident">options</span> <span class="punct">=</span> <span class="punct">{</span>
        <span class="symbol">:lzr</span> <span class="punct">=&gt;</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:runtime</span><span class="punct">],</span>
        <span class="symbol">:debug</span> <span class="punct">=&gt;</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:debug</span><span class="punct">],</span>
        <span class="symbol">:lzproxied</span> <span class="punct">=&gt;</span> <span class="ident">options</span><span class="punct">.</span><span class="ident">fetch</span><span class="punct">(</span><span class="symbol">:proxied</span><span class="punct">,</span> <span class="constant">false</span><span class="punct">),</span>
        <span class="symbol">:lzt</span> <span class="punct">=&gt;</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:format</span><span class="punct">]</span> <span class="punct">||</span> <span class="punct">'</span><span class="string">swf</span><span class="punct">'}</span>
      <span class="ident">query</span> <span class="punct">=</span> <span class="ident">options</span><span class="punct">.</span><span class="ident">map</span><span class="punct">{|</span><span class="ident">k</span><span class="punct">,</span><span class="ident">v</span><span class="punct">|&quot;</span><span class="string"><span class="expr">#{k}</span>=<span class="expr">#{v}</span></span><span class="punct">&quot;</span> <span class="keyword">unless</span> <span class="ident">v</span><span class="punct">.</span><span class="ident">nil?</span><span class="punct">}.</span><span class="ident">compact</span><span class="punct">.</span><span class="ident">join</span><span class="punct">('</span><span class="string">&amp;</span><span class="punct">')</span>
      <span class="ident">url</span> <span class="punct">=</span> <span class="punct">&quot;</span><span class="string"><span class="expr">#{@server_url}#{server_relative_path}</span></span><span class="punct">&quot;</span>
      <span class="ident">url</span> <span class="punct">+=</span> <span class="punct">&quot;</span><span class="string">?<span class="expr">#{query}</span></span><span class="punct">&quot;</span> <span class="keyword">unless</span> <span class="ident">query</span><span class="punct">.</span><span class="ident">empty?</span>
      <span class="constant">Net</span><span class="punct">::</span><span class="constant">HTTP</span><span class="punct">.</span><span class="ident">get_response</span> <span class="constant">URI</span><span class="punct">.</span><span class="ident">parse</span><span class="punct">(</span><span class="ident">url</span><span class="punct">)</span> <span class="keyword">do</span> <span class="punct">|</span><span class="ident">response</span><span class="punct">|</span>
        <span class="keyword">case</span> <span class="ident">response</span>
        <span class="keyword">when</span> <span class="constant">Net</span><span class="punct">::</span><span class="constant">HTTPOK</span>
          <span class="keyword">if</span> <span class="ident">output</span>
            <span class="constant">File</span><span class="punct">.</span><span class="ident">open</span><span class="punct">(</span><span class="ident">output</span><span class="punct">,</span> <span class="punct">'</span><span class="string">w</span><span class="punct">')</span> <span class="keyword">do</span> <span class="punct">|</span><span class="ident">f</span><span class="punct">|</span>
              <span class="ident">response</span><span class="punct">.</span><span class="ident">read_body</span> <span class="keyword">do</span> <span class="punct">|</span><span class="ident">segment</span><span class="punct">|</span>
                <span class="ident">f</span> <span class="punct">&lt;&lt;</span> <span class="ident">segment</span>
              <span class="keyword">end</span>
            <span class="keyword">end</span>
          <span class="keyword">else</span>
            <span class="keyword">return</span> <span class="ident">response</span><span class="punct">.</span><span class="ident">body</span>
          <span class="keyword">end</span>
        <span class="keyword">else</span>
          <span class="ident">response</span><span class="punct">.</span><span class="ident">value</span> <span class="comment"># for effect: raises error</span>
        <span class="keyword">end</span>
      <span class="keyword">end</span>
    <span class="keyword">end</span>
    
    <span class="keyword">def </span><span class="method">in_server_directory?</span> <span class="ident">file</span>
      <span class="keyword">return</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">contains?</span><span class="punct">(</span><span class="attribute">@server_directory</span><span class="punct">,</span> <span class="ident">file</span><span class="punct">,</span> <span class="symbol">:indirect</span> <span class="punct">=&gt;</span> <span class="constant">true</span><span class="punct">)</span>
    <span class="keyword">end</span>
    
    <span class="keyword">def </span><span class="method">tmp_directory_for</span> <span class="ident">file</span>
      <span class="constant">File</span><span class="punct">.</span><span class="ident">join</span> <span class="attribute">@tmpdir</span><span class="punct">,</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">basename</span><span class="punct">(</span><span class="ident">file</span><span class="punct">)</span>
    <span class="keyword">end</span>
    
    <span class="keyword">def </span><span class="method">clean</span>
      <span class="ident">require</span> <span class="punct">'</span><span class="string">fileutils</span><span class="punct">'</span>
      <span class="ident">rm_rf</span> <span class="attribute">@tmpdir</span>
    <span class="keyword">end</span>
    
    <span class="keyword">def </span><span class="method">copy_sources</span> <span class="ident">source</span><span class="punct">,</span> <span class="ident">target</span>
      <span class="ident">sources</span> <span class="punct">=</span> <span class="punct">[</span><span class="ident">source</span><span class="punct">]</span> <span class="punct">+</span> <span class="constant">OpenLaszlo</span><span class="punct">::</span><span class="ident">collect_includes</span><span class="punct">(</span><span class="ident">source</span><span class="punct">).</span><span class="ident">select</span><span class="punct">{|</span><span class="ident">f</span><span class="punct">|</span><span class="constant">File</span><span class="punct">.</span><span class="ident">exists?</span> <span class="ident">f</span><span class="punct">}</span>
      <span class="ident">require</span> <span class="punct">'</span><span class="string">fileutils</span><span class="punct">'</span>
      <span class="constant">FileUtils</span><span class="punct">::</span><span class="ident">mkdir_p</span> <span class="ident">target</span>
      <span class="constant">OpenLaszlo</span><span class="punct">::</span><span class="ident">rsync</span> <span class="ident">sources</span><span class="punct">,</span> <span class="ident">target</span><span class="punct">,</span> <span class="symbol">:delete</span> <span class="punct">=&gt;</span> <span class="constant">true</span>
    <span class="keyword">end</span>
  <span class="keyword">end</span>
  
  <span class="comment"># This class implements a bridge to the command-line compiler.</span>
  <span class="comment">#</span>
  <span class="comment"># If you don't need multiple compilers, you can use the methods in</span>
  <span class="comment"># the OpenLaszlo module instead.</span>
  <span class="comment">#</span>
  <span class="comment"># CommandLineCompiler is slower than CompileServer, but,</span>
  <span class="comment"># unlike the server, it can compile files in any location.</span>
  <span class="keyword">class </span><span class="class">CommandLineCompiler</span>
    <span class="comment"># Creates a new compiler.</span>
    <span class="comment">#</span>
    <span class="comment"># Options are:</span>
    <span class="comment"># * &lt;tt&gt;:compiler_script&lt;/tt&gt; - the path to the shell script that</span>
    <span class="comment"># invokes the compiler.  This defaults to a standard location inside</span>
    <span class="comment"># the value specified by :home.</span>
    <span class="comment"># * &lt;tt&gt;:openlaszlo_home&lt;/tt&gt; - the home directory of the Open&lt;tt&gt;&lt;/tt&gt;Laszlo SDK.</span>
    <span class="comment"># This defaults to ENV['OPENLASZLO_HOME'].</span>
    <span class="keyword">def </span><span class="method">initialize</span> <span class="ident">options</span><span class="punct">={}</span>
      <span class="attribute">@lzc</span> <span class="punct">=</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:compiler_script</span><span class="punct">]</span>
      <span class="keyword">unless</span> <span class="attribute">@lzc</span>
        <span class="ident">home</span> <span class="punct">=</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:openlaszlo_home</span><span class="punct">]</span> <span class="punct">||</span> <span class="constant">ENV</span><span class="punct">['</span><span class="string">OPENLASZLO_HOME</span><span class="punct">']</span>
        <span class="keyword">raise</span> <span class="punct">&quot;</span><span class="string">:compiler_script or :openlaszlo_home must be specified</span><span class="punct">&quot;</span> <span class="keyword">unless</span> <span class="ident">home</span>
        <span class="ident">search</span> <span class="punct">=</span> <span class="ident">bin_directories</span><span class="punct">.</span><span class="ident">map</span><span class="punct">{|</span><span class="ident">f</span><span class="punct">|</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">join</span><span class="punct">(</span><span class="ident">home</span><span class="punct">,</span> <span class="ident">f</span><span class="punct">,</span> <span class="punct">'</span><span class="string">lzc</span><span class="punct">')}</span>
        <span class="ident">found</span> <span class="punct">=</span> <span class="ident">search</span><span class="punct">.</span><span class="ident">select</span><span class="punct">{|</span><span class="ident">f</span><span class="punct">|</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">exists?</span> <span class="ident">f</span><span class="punct">}</span>
        <span class="keyword">raise</span> <span class="punct">&quot;</span><span class="string">couldn't find bin/lzc in <span class="expr">#{bin_directories.join(' or ')}</span></span><span class="punct">&quot;</span> <span class="keyword">if</span> <span class="ident">found</span><span class="punct">.</span><span class="ident">empty?</span>
        <span class="attribute">@lzc</span> <span class="punct">=</span> <span class="ident">found</span><span class="punct">.</span><span class="ident">first</span>
        <span class="attribute">@lzc</span> <span class="punct">+=</span> <span class="punct">'</span><span class="string">.bat</span><span class="punct">'</span> <span class="keyword">if</span> <span class="ident">windows?</span>
      <span class="keyword">end</span>
    <span class="keyword">end</span>
    
    <span class="comment"># Invokes the OpenLaszlo command-line compiler on +source_file+.</span>
    <span class="comment">#</span>
    <span class="comment"># See OpenLaszlo.compile for a description of +options+.</span>
    <span class="keyword">def </span><span class="method">compile</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span><span class="punct">={}</span>
      <span class="ident">default_output</span> <span class="punct">=</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">join</span><span class="punct">(</span><span class="constant">File</span><span class="punct">.</span><span class="ident">dirname</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">),</span>
                                 <span class="constant">File</span><span class="punct">.</span><span class="ident">basename</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">,</span> <span class="punct">'</span><span class="string">.lzx</span><span class="punct">')</span> <span class="punct">+</span> <span class="punct">'</span><span class="string">.swf</span><span class="punct">')</span>
      <span class="ident">output</span> <span class="punct">=</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:output</span><span class="punct">]</span> <span class="punct">||</span> <span class="ident">default_output</span>
      <span class="keyword">raise</span> <span class="punct">&quot;</span><span class="string"><span class="expr">#{source_file}</span> and <span class="expr">#{output}</span> do not have the same basename.</span><span class="punct">&quot;</span> <span class="keyword">unless</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">basename</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">,</span> <span class="punct">'</span><span class="string">.lzx</span><span class="punct">')</span> <span class="punct">==</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">basename</span><span class="punct">(</span><span class="ident">output</span><span class="punct">,</span> <span class="punct">'</span><span class="string">.swf</span><span class="punct">')</span>
      <span class="ident">args</span> <span class="punct">=</span> <span class="punct">[]</span>
      <span class="ident">args</span> <span class="punct">&lt;&lt;</span> <span class="punct">'</span><span class="string">--runtime=#{options[:runtime]}</span><span class="punct">'</span> <span class="keyword">if</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:runtime</span><span class="punct">]</span>
      <span class="ident">args</span> <span class="punct">&lt;&lt;</span> <span class="punct">'</span><span class="string">--debug</span><span class="punct">'</span> <span class="keyword">if</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:debug</span><span class="punct">]</span>
      <span class="ident">args</span> <span class="punct">&lt;&lt;</span> <span class="punct">'</span><span class="string">--profile</span><span class="punct">'</span> <span class="keyword">if</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:profile</span><span class="punct">]</span>
      <span class="ident">args</span> <span class="punct">&lt;&lt;</span> <span class="punct">&quot;</span><span class="string">--dir '<span class="expr">#{File.dirname output}</span>'</span><span class="punct">&quot;</span> <span class="keyword">unless</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">dirname</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">)</span> <span class="punct">==</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">dirname</span><span class="punct">(</span><span class="ident">output</span><span class="punct">)</span>
      <span class="ident">args</span> <span class="punct">&lt;&lt;</span> <span class="ident">source_file</span>
      <span class="ident">command</span> <span class="punct">=</span> <span class="punct">&quot;</span><span class="string"><span class="escape">\&quot;</span><span class="expr">#{@lzc}</span><span class="escape">\&quot;</span> <span class="expr">#{args.join(' ')}</span></span><span class="punct">&quot;</span>
      <span class="constant">ENV</span><span class="punct">['</span><span class="string">LPS_HOME</span><span class="punct">']</span> <span class="punct">||=</span> <span class="constant">ENV</span><span class="punct">['</span><span class="string">OPENLASZLO_HOME</span><span class="punct">']</span>
      <span class="keyword">begin</span>
        <span class="comment">#raise NotImplementedError --- for testing Windows</span>
        <span class="ident">require</span> <span class="punct">&quot;</span><span class="string">open3</span><span class="punct">&quot;</span>
        <span class="comment"># The compiler writes errors to stdout, warnings to stderr</span>
        <span class="ident">stdin</span><span class="punct">,</span> <span class="ident">stdout</span><span class="punct">,</span> <span class="ident">stderr</span> <span class="punct">=</span> <span class="constant">Open3</span><span class="punct">.</span><span class="ident">popen3</span><span class="punct">(</span><span class="ident">command</span><span class="punct">)</span>
        <span class="ident">errors</span> <span class="punct">=</span> <span class="ident">stdout</span><span class="punct">.</span><span class="ident">read</span>
        <span class="ident">warnings</span> <span class="punct">=</span> <span class="ident">stderr</span><span class="punct">.</span><span class="ident">readlines</span>
      <span class="keyword">rescue</span> <span class="constant">NotImplementedError</span>
        <span class="comment"># Windows doesn't have popen</span>
        <span class="ident">errors</span> <span class="punct">=</span> `<span class="comment">#{command}`</span>
        <span class="ident">warnings</span> <span class="punct">=</span> <span class="punct">[]</span>
      <span class="keyword">end</span>
      <span class="ident">errors</span><span class="punct">.</span><span class="ident">gsub!</span><span class="punct">(/</span><span class="regex">^<span class="escape">\d</span>+<span class="escape">\s</span>+</span><span class="punct">/,</span> <span class="punct">'</span><span class="string"></span><span class="punct">')</span> <span class="comment"># work around a bug in OpenLaszlo 3.1</span>
      <span class="keyword">if</span> <span class="ident">errors</span> <span class="punct">=~</span> <span class="punct">/</span><span class="regex">^Compilation errors occurred:<span class="escape">\n</span></span><span class="punct">/</span>
        <span class="keyword">raise</span> <span class="constant">CompilationError</span><span class="punct">.</span><span class="ident">new</span><span class="punct">(</span><span class="global">$'</span><span class="punct">.</span><span class="ident">strip</span><span class="punct">)</span>
      <span class="keyword">end</span>
      <span class="ident">results</span> <span class="punct">=</span> <span class="punct">{</span><span class="symbol">:output</span> <span class="punct">=&gt;</span> <span class="ident">output</span><span class="punct">,</span> <span class="symbol">:warnings</span> <span class="punct">=&gt;</span> <span class="ident">warnings</span><span class="punct">}</span>
      <span class="keyword">return</span> <span class="ident">results</span>
    <span class="keyword">end</span>
    
    <span class="ident">private</span>
    
    <span class="comment"># Locations in which to look for the lzc script, relative to OPENLASZLO_HOME</span>
    <span class="keyword">def </span><span class="method">bin_directories</span>
      <span class="punct">[</span><span class="comment"># binary distro location</span>
        <span class="punct">'</span><span class="string">bin</span><span class="punct">',</span>
        <span class="comment"># source distro location</span>
        <span class="punct">'</span><span class="string">WEB-INF/lps/server/bin</span><span class="punct">'</span>
      <span class="punct">]</span>
    <span class="keyword">end</span>
  <span class="keyword">end</span>
  
  <span class="comment"># Returns the default compiler.  Use the server-based compiler if it's</span>
  <span class="comment"># available, since it's so much faster.</span>
  <span class="keyword">def </span><span class="method">self.compiler</span>
    <span class="keyword">return</span> <span class="attribute">@compiler</span> <span class="keyword">if</span> <span class="attribute">@compiler</span>
    <span class="keyword">return</span> <span class="attribute">@compiler</span> <span class="punct">=</span> <span class="constant">CompileServer</span><span class="punct">.</span><span class="ident">new</span> <span class="keyword">if</span> <span class="constant">ENV</span><span class="punct">['</span><span class="string">OPENLASZLO_URL</span><span class="punct">']</span> <span class="keyword">and</span> <span class="constant">ENV</span><span class="punct">['</span><span class="string">OPENLASZLO_HOME</span><span class="punct">']</span>
    <span class="keyword">return</span> <span class="attribute">@compiler</span> <span class="punct">=</span> <span class="constant">CommandLineCompiler</span><span class="punct">.</span><span class="ident">new</span> <span class="keyword">if</span> <span class="constant">ENV</span><span class="punct">['</span><span class="string">OPENLASZLO_HOME</span><span class="punct">']</span>
    <span class="keyword">raise</span> <span class="punct">&lt;&lt;</span><span class="constant">EOF</span><span class="string">
Couldn<span class="escape">\'</span>t find an OpenLaszlo compiler.

To use the compile server (recommended), set ENV['OPENLASZLO_URL'] and ENV['OPENLASZLO_HOME'].

To use the command-line compiler, set ENV['OPENLASZLO_HOME'].
</span><span class="constant">EOF</span>
  <span class="keyword">end</span>
  
  <span class="comment"># Sets the default compiler for future invocations of OpenLaszlo.compile.</span>
  <span class="keyword">def </span><span class="method">self.compiler=</span> <span class="ident">compiler</span>
    <span class="attribute">@compiler</span> <span class="punct">=</span> <span class="ident">compiler</span>
  <span class="keyword">end</span>
  
  <span class="comment"># Compile an OpenLaszlo source file.</span>
  <span class="comment">#</span>
  <span class="comment"># Examples:</span>
  <span class="comment">#   require 'openlaszlo'</span>
  <span class="comment">#   OpenLaszlo::compile 'hello.lzx'</span>
  <span class="comment">#   OpenLaszlo::compile 'hello.lzx', :debug =&gt; true</span>
  <span class="comment">#   OpenLaszlo::compile 'hello.lzx', :runtime =&gt; 'swf8'</span>
  <span class="comment">#   OpenLaszlo::compile 'hello.lzx', {:runtime =&gt; 'swf8', :debug =&gt; true}</span>
  <span class="comment">#   OpenLaszlo::compile 'hello.lzx', :output =&gt; 'hello-world.swf'</span>
  <span class="comment">#</span>
  <span class="comment"># Options are:</span>
  <span class="comment"># * &lt;tt&gt;:debug&lt;/tt&gt; - debug mode (default false)</span>
  <span class="comment"># * &lt;tt&gt;:output&lt;/tt&gt; - specify the name and location for the output file (default = &lt;tt&gt;input_file.sub(/\.lzx$/, '.swf')&lt;/tt&gt;)</span>
  <span class="comment"># * &lt;tt&gt;:proxied&lt;/tt&gt; - is application proxied (default true)</span>
  <span class="comment"># * &lt;tt&gt;:runtime&lt;/tt&gt; - runtime (default swf7)</span>
  <span class="comment">#</span>
  <span class="comment"># See CompileServer.compile and CommandLineCompiler.compile for</span>
  <span class="comment"># additional options that are specific to the compilation methods in</span>
  <span class="comment"># those classes.</span>
  <span class="keyword">def </span><span class="method">self.compile</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span><span class="punct">={}</span>
    <span class="ident">compiler</span><span class="punct">.</span><span class="ident">compile</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span>
  <span class="keyword">end</span>
  
  <span class="keyword">def </span><span class="method">self.make_html</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span><span class="punct">={}</span> <span class="comment">#:nodoc:</span>
    <span class="keyword">raise</span> <span class="punct">'</span><span class="string">not really supported, for now</span><span class="punct">'</span>
    <span class="ident">options</span> <span class="punct">=</span> <span class="punct">{</span>
      <span class="symbol">:format</span> <span class="punct">=&gt;</span> <span class="punct">'</span><span class="string">html-object</span><span class="punct">',</span>
      <span class="symbol">:output</span> <span class="punct">=&gt;</span> <span class="constant">File</span><span class="punct">.</span><span class="ident">basename</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">,</span> <span class="punct">'</span><span class="string">.lzx</span><span class="punct">')+'</span><span class="string">.html</span><span class="punct">'}.</span><span class="ident">update</span><span class="punct">(</span><span class="ident">options</span><span class="punct">)</span>
    <span class="ident">compiler</span><span class="punct">.</span><span class="ident">compile</span> <span class="ident">source_file</span><span class="punct">,</span> <span class="ident">options</span>
    <span class="ident">source_file</span> <span class="punct">=</span> <span class="ident">options</span><span class="punct">[</span><span class="symbol">:output</span><span class="punct">]</span>
    <span class="ident">s</span> <span class="punct">=</span> <span class="ident">open</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">).</span><span class="ident">read</span>
    <span class="ident">open</span><span class="punct">(</span><span class="ident">source_file</span><span class="punct">,</span> <span class="punct">'</span><span class="string">w</span><span class="punct">')</span> <span class="punct">{|</span><span class="ident">f</span><span class="punct">|</span> <span class="ident">f</span><span class="punct">.</span><span class="ident">write</span> <span class="ident">s</span><span class="punct">.</span><span class="ident">gsub!</span><span class="punct">(/</span><span class="regex"><span class="escape">\.</span>lzx<span class="escape">\?</span>lzt=swf&amp;amp;</span><span class="punct">/,</span> <span class="punct">'</span><span class="string">.lzx.swf?</span><span class="punct">')}</span>
  <span class="keyword">end</span>
<span class="keyword">end</span>
</pre></body></html>