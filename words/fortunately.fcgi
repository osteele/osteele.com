#!/usr/bin/ruby
require "fcgi"
fcgi_count = 0

FCGI.each_cgi {|cgi|
  name = cgi['name'][0]
  fcgi_count += 1
  puts cgi.header
  begin
    term = 'Oliver'
    term = cgi.params['q'].first if cgi.params['q']
    require 'fortunately'
    #load 'fortunately.rb' if cgi.params['reload']
    story = Fortunately::story_for(term, true) if term
    require 'erb'
    @erb = nil if cgi.params['reload']
    @erb ||= ERB.new(open('fortunately.rhtml') do |f| f.read end)
    @erb.run(binding)
  rescue
    puts "Error: #{$@.join("\n")}: #{$!}"
  end
}
