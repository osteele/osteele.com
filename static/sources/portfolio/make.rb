class Y
  def initialize basis
    @basis = basis
  end
  
  def method_missing name, default=:error
    name = name.to_s
    return @basis[$`] != nil if name =~ /\?$/
    scalar = @basis[name]
    return scalar.value if scalar
    return default unless default==:error
    raise "Missing property: #{@basis['project'].value}.#{name}"
  end
end

def make_index
  require 'yaml'
  require 'fileutils'
  titles = %w{project year language file directory docs (comments)}
  s = ''
  s << '<table>'
  s << '<tr><th>'+titles.map{|w|w[0]== '('[0] ?'':w}.
    map{|w|w.capitalize}.
    map{|w|%w{File Directory}.include?(w) ? 'Source '+w : w}.
    join('</th><th>')+'</th></tr>'
  projects = []
  YAML.parse_file('index.yaml').children.each do |y|
    y = Y.new(y)
    unless y.file?
      puts "Skipping #{y.project? ? y.project : y}"
      next
    end
    cells = {}
    if y.project? and !projects.include?(y.project)
      cells[:project] = "<a href=\"#{y.site}\">#{y.project.gsub(/ /, '&nbsp;')}</a>"
      projects << y.project
    end
    cells[:file] = "<a href=\"#{y.file}\"><tt>#{File.basename y.file}</tt></a>"
    cells[:directory] = "<a href=\"#{y.dir}\"><tt>#{File.basename y.dir}/</tt></a>" if y.dir?
    cells[:docs] = "<a href=\"#{y.docs}\">docs</a>" if y.docs?
    s << '<tr>' + titles.map{|t|t.sub(/\((.*)\)/, '\1')}.map{|t|
      c = t
      c = "comments' rowspan=3'" if t == 'comments'
      "<td valign='top' class='#{c}'>#{cells[t.intern] || y.send(t, '')}</td>"
    }.join() + '</tr>'
  end
  s << '</table>'
  File.open('table.html', 'w') do |f| f << s end
  #`open index.html`
end

#make_index
