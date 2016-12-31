#!/usr/bin/ruby
require "fcgi"
fcgi_count = 0

FCGI.each_cgi do |cgi|
  fcgi_count += 1
  begin
    require 'svn_activity'
    load 'svn_activity.rb'
    location = 'http://svn.openlaszlo.org/openlaszlo'
    location = cgi.params['location'].first if cgi.params['location'].any?
    format = 'png'
    format = cgi.params['format'].first if cgi.params['format'].any?
    fname = make_activity_graph location, :format => format
    puts cgi.header("image/#{fname[/[^.]*$/]}")
    File.open(fname) do |f| puts f.read end
  rescue
    puts cgi.header('text/plain')
    puts "Error: #{$@.join("\n")}: #{$!}"
  end
end
