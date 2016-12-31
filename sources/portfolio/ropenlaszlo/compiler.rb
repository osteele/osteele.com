# Author:: Oliver Steele
# Copyright:: Copyright (c) 2005-2006 Oliver Steele.  All rights reserved.
# License:: Ruby License.
 
# == module OpenLaszlo
# 
# This module contains utility methods for compiling
# OpenLaszlo[openlaszlo.org] programs.
#
# Example:
#   # Set up the environment to use the compile server.  The OpenLaszlo server
#   # must be running in order at this location in order for this to work.
#   ENV['OPENLASZLO_HOME'] = '/Applications/OpenLaszlo Server 3.1'
#   ENV['OPENLASZLO_URL'] = 'http://localhost:8080/lps-3.1'
#
#   require 'openlaszlo'
#   # Create a file 'hello.swf' in the current directory:
#   OpenLaszlo::compile 'hello.lzx'
#
# See OpenLaszlo.compile for additional documentation.
# 

module OpenLaszlo
  class CompilationError < StandardError; end
  
  # This class implements a bridge to the compile server.
  #
  # If you don't need multiple compilers, you can use the methods in
  # the OpenLaszlo module instead.
  #
  # CompileServer is faster than CommandLineCompiler.
  class CompileServer
    # Options:
    # * <tt>:openlaszlo_home</tt> - filesystem location of the Open<tt></tt>Laszlo SDK.  Defaults to EVN['OPENLASZLO_HOME']
    # * <tt>:server_uri</tt> - the URI of the server.  Defaults to ENV['OPENLASZLO_URL'] if this is specified, otherwise to 'http://localhost:8080/lps-dev'.
    def initialize options={}
      @server_directory = options[:home] || ENV['OPENLASZLO_HOME']
      @server_url = options[:server_uri] || ENV['OPENLASZLO_URL'] || 'http://localhost:8080/lps-dev'
      @tmpdir = File.join @server_directory, 'tmp'
    end
    
    # Invokes the Open<tt></tt>Laszlo server-based compiler on
    # +source_file+.  Copies +source_file+ into the home directory
    # of the server if it is not already there.
    #
    # Options:
    # * <tt>:format</tt> - request type (default 'swf')
    # See OpenLaszlo.compile for a description of +options+.
    def compile source_file, options={}
      unless in_server_directory?(source_file)
        begin
          copy_sources source_file, tmp_directory_for(source_file)
          source_file = File.join(tmp_directory_for(source_file), File.basename(source_file))
        rescue NotImplementedError
          raise "The compiler server couldn't compile #{source_file} because it isn't in the server home directory (#{@server_directory}) and rsync isn't installed.  Either unset move the file, install rsync, or unset OPENLASZLO_URL."
        end
      end
      mtime = File.mtime source_file
      output = options[:output] || "#{File.expand_path(File.join(File.dirname(source_file), File.basename(source_file, '.lzx')))}.swf"
      compile_object source_file, output, options
      results = request_metadata_for source_file, options
      raise "Race condition: #{source_file} was modified during compilation" if mtime != File.mtime(source_file)
      results[:output] = output
      raise CompilationError.new(results[:error]) if results[:error]
      return results
    end
    
    private
    def compile_object source_file, object, options={}
      options = {}.update(options).update(:output => object)
      request source_file, options
    end
    
    def request_metadata_for source_file, options={}
      results = {}
      options = {}.update(options).update(:format => 'canvas-xml', :output => nil)
      text = request source_file, options
      if text =~ %r{<warnings>(.*?)</warnings>}m
        results[:warnings] = $1.scan(%r{<error>\s*(.*?)\s*</error>}m).map{|w|w.first}
      elsif text !~ %r{<canvas>} && text =~ %r{<pre>Error:\s*(.*?)\s*</pre>}m
        results[:error] = $1
      end
      return results
    end
    
    def request source_file, options={}
      output = options[:output]
      require 'net/http'
      require 'uri'
      # assert that pathname is relative to LPS home:
      raise "#{absolute_path} isn't inside #{@server_directory}" unless in_server_directory?(source_file)
      server_relative_path = File.expand_path(source_file)[@server_directory.length..-1]
      # FIXME: this doesn't handle quoting; use recursive File.split instead
      # FIXME: should encode the url, for filenames that include '/'
      server_relative_path.gsub(File::Separator, '/')
      options = {
        :lzr => options[:runtime],
        :debug => options[:debug],
        :lzproxied => options.fetch(:proxied, false),
        :lzt => options[:format] || 'swf'}
      query = options.map{|k,v|"#{k}=#{v}" unless v.nil?}.compact.join('&')
      url = "#{@server_url}#{server_relative_path}"
      url += "?#{query}" unless query.empty?
      Net::HTTP.get_response URI.parse(url) do |response|
        case response
        when Net::HTTPOK
          if output
            File.open(output, 'w') do |f|
              response.read_body do |segment|
                f << segment
              end
            end
          else
            return response.body
          end
        else
          response.value # for effect: raises error
        end
      end
    end
    
    def in_server_directory? file
      return File.contains?(@server_directory, file, :indirect => true)
    end
    
    def tmp_directory_for file
      File.join @tmpdir, File.basename(file)
    end
    
    def clean
      require 'fileutils'
      rm_rf @tmpdir
    end
    
    def copy_sources source, target
      sources = [source] + OpenLaszlo::collect_includes(source).select{|f|File.exists? f}
      require 'fileutils'
      FileUtils::mkdir_p target
      OpenLaszlo::rsync sources, target, :delete => true
    end
  end
  
  # This class implements a bridge to the command-line compiler.
  #
  # If you don't need multiple compilers, you can use the methods in
  # the OpenLaszlo module instead.
  #
  # CommandLineCompiler is slower than CompileServer, but,
  # unlike the server, it can compile files in any location.
  class CommandLineCompiler
    # Creates a new compiler.
    #
    # Options are:
    # * <tt>:compiler_script</tt> - the path to the shell script that
    # invokes the compiler.  This defaults to a standard location inside
    # the value specified by :home.
    # * <tt>:openlaszlo_home</tt> - the home directory of the Open<tt></tt>Laszlo SDK.
    # This defaults to ENV['OPENLASZLO_HOME'].
    def initialize options={}
      @lzc = options[:compiler_script]
      unless @lzc
        home = options[:openlaszlo_home] || ENV['OPENLASZLO_HOME']
        raise ":compiler_script or :openlaszlo_home must be specified" unless home
        search = bin_directories.map{|f| File.join(home, f, 'lzc')}
        found = search.select{|f| File.exists? f}
        raise "couldn't find bin/lzc in #{bin_directories.join(' or ')}" if found.empty?
        @lzc = found.first
        @lzc += '.bat' if windows?
      end
    end
    
    # Invokes the OpenLaszlo command-line compiler on +source_file+.
    #
    # See OpenLaszlo.compile for a description of +options+.
    def compile source_file, options={}
      default_output = File.join(File.dirname(source_file),
                                 File.basename(source_file, '.lzx') + '.swf')
      output = options[:output] || default_output
      raise "#{source_file} and #{output} do not have the same basename." unless File.basename(source_file, '.lzx') == File.basename(output, '.swf')
      args = []
      args << '--runtime=#{options[:runtime]}' if options[:runtime]
      args << '--debug' if options[:debug]
      args << '--profile' if options[:profile]
      args << "--dir '#{File.dirname output}'" unless File.dirname(source_file) == File.dirname(output)
      args << source_file
      command = "\"#{@lzc}\" #{args.join(' ')}"
      ENV['LPS_HOME'] ||= ENV['OPENLASZLO_HOME']
      begin
        #raise NotImplementedError --- for testing Windows
        require "open3"
        # The compiler writes errors to stdout, warnings to stderr
        stdin, stdout, stderr = Open3.popen3(command)
        errors = stdout.read
        warnings = stderr.readlines
      rescue NotImplementedError
        # Windows doesn't have popen
        errors = `#{command}`
        warnings = []
      end
      errors.gsub!(/^\d+\s+/, '') # work around a bug in OpenLaszlo 3.1
      if errors =~ /^Compilation errors occurred:\n/
        raise CompilationError.new($'.strip)
      end
      results = {:output => output, :warnings => warnings}
      return results
    end
    
    private
    
    # Locations in which to look for the lzc script, relative to OPENLASZLO_HOME
    def bin_directories
      [# binary distro location
        'bin',
        # source distro location
        'WEB-INF/lps/server/bin'
      ]
    end
  end
  
  # Returns the default compiler.  Use the server-based compiler if it's
  # available, since it's so much faster.
  def self.compiler
    return @compiler if @compiler
    return @compiler = CompileServer.new if ENV['OPENLASZLO_URL'] and ENV['OPENLASZLO_HOME']
    return @compiler = CommandLineCompiler.new if ENV['OPENLASZLO_HOME']
    raise <<EOF
Couldn\'t find an OpenLaszlo compiler.

To use the compile server (recommended), set ENV['OPENLASZLO_URL'] and ENV['OPENLASZLO_HOME'].

To use the command-line compiler, set ENV['OPENLASZLO_HOME'].
EOF
  end
  
  # Sets the default compiler for future invocations of OpenLaszlo.compile.
  def self.compiler= compiler
    @compiler = compiler
  end
  
  # Compile an OpenLaszlo source file.
  #
  # Examples:
  #   require 'openlaszlo'
  #   OpenLaszlo::compile 'hello.lzx'
  #   OpenLaszlo::compile 'hello.lzx', :debug => true
  #   OpenLaszlo::compile 'hello.lzx', :runtime => 'swf8'
  #   OpenLaszlo::compile 'hello.lzx', {:runtime => 'swf8', :debug => true}
  #   OpenLaszlo::compile 'hello.lzx', :output => 'hello-world.swf'
  #
  # Options are:
  # * <tt>:debug</tt> - debug mode (default false)
  # * <tt>:output</tt> - specify the name and location for the output file (default = <tt>input_file.sub(/\.lzx$/, '.swf')</tt>)
  # * <tt>:proxied</tt> - is application proxied (default true)
  # * <tt>:runtime</tt> - runtime (default swf7)
  #
  # See CompileServer.compile and CommandLineCompiler.compile for
  # additional options that are specific to the compilation methods in
  # those classes.
  def self.compile source_file, options={}
    compiler.compile source_file, options
  end
  
  def self.make_html source_file, options={} #:nodoc:
    raise 'not really supported, for now'
    options = {
      :format => 'html-object',
      :output => File.basename(source_file, '.lzx')+'.html'}.update(options)
    compiler.compile source_file, options
    source_file = options[:output]
    s = open(source_file).read
    open(source_file, 'w') {|f| f.write s.gsub!(/\.lzx\?lzt=swf&amp;/, '.lzx.swf?')}
  end
end
