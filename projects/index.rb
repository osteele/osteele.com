require 'rubygems'
require 'extensions/all'
require 'rexml/document'
require 'erb'

include REXML

module REXML
  class Element
    def first_element xpath
      get_elements(xpath).first
    end
  end
end

class XMLProxy
  attr_reader :base # for debugging
  
  def initialize(base, ns=nil)
    @base = base
    @ns = ns
  end
  
  def method_missing(sym, options={})
    name = sym.to_s
    ns = options[:ns] || @ns
    q = ns ? "#{ns}:" : ""
    qname = "#{q}#{name}"
    es = @base.get_elements(qname)
    if es.length == 1
      e = es.first
      return convert(e, ns, options[:type])
    end
    if es.empty? and @base.attributes[name]
      return @base.attributes[name]
    end
    return es.map{|e|convert(e, ns, options[:type])}
  end
  
  private
  def convert e, ns, type
    text = e.text
    text = Date.parse(text) if type == Date
    return XMLProxy.new(e, ns) if e.has_elements? or text.nil?
    return text
  end
end

class Project
  attr_accessor :name, :homepage, :created, :description, :tags
  
  #def created= date
  #    @created = Date.parse(date)
  #end
end

def to_project file
  d = Document.new open(file).read
  pel = XMLProxy.new(d.first_element("//doap:Project"), 'doap')
  pr = Project.new
  pr.name = pel.name
  pr.homepage = pel.homepage.resource
  pr.created = pel.created
  pr.description = pel.description
  pr.tags = pel.send("programming-language")
  pr
end

def yaml_to_project y
  project = Project.new
  for key in %w{name created description homepage tags} do
    if y[key]
      value = y[key].value
      value = value.split if key == 'tags'
      value = value-%w{personal} if key == 'tags'
      project.send("#{key}=", value)
    end
  end
  raise "No date for #{project.name}" unless project.created
  project
end

def relativize(url)
  url.gsub(%r{^http://(www.)?osteele.com/}, '/')
end

def format_project project, f
  s = 1.0 - f/5
  color = (format "%02x", (s*255).to_i)*3
  c2 = (format "%02x", (f/3*255).to_i)*3
  template = ERB.new <<-EOF
<div class="project" style="background: ##{color}">
<div class="name"><a href="<%= relativize project.homepage %>"><%= project.name %></a></div>
<div class="date"><%= project.created %></div>
<% if false %>
  <div class="img"><a href="url"><img src="img" /></a></div>
<% end %>
<div class="desc" style="color: ##{c2}"><%= project.description %></div>
<% if project.tags %>
<div class="tags">Tags:
  <% project.tags.each_with_index do |tag, i| %><%= ' ' if i %><span class="tag"><a href="http://www.technorati.com/tag/<%= tag %>"><%= tag %><img src="http://osteele.dev/images/icons/tbubble.gif" border="0" hspace="1"/></a></span><% end %>    
</div>
<% end %>
</div>
EOF
  template.result(binding)#.gsub!(/^\s+$/, '')#.gsub!(/\n+/, "\n")
end

module Enumerable
  def sort_by! reverse=false, &block
    if reverse
      sort! {|a, b| block.call(b) <=> block.call(a)}
    else
      sort! {|a, b| block.call(a) <=> block.call(b)}
    end
  end
end

projects = []#`ls *.rdf`.map(&:strip).map{|f|to_project f} 
projects += YAML.parse_file('index.yaml').children.map{|y|yaml_to_project y}

s = '<link rel="stylesheet" type="text/css" href="style.css" />'+"\n\n"
projects.sort_by!(true){|p|p.created}.each_with_index do |project, index|
  s << format_project(project, index.to_f / projects.length)
  s << "\n"
end
open('index.html', 'w') do |f| f.write(s) end