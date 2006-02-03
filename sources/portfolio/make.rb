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
      c = "' rowspan=3'" if t == 'comments'
      "<td valign='top' class='#{c}'>#{cells[t.intern] || y.send(t, '')}</td>"
    }.join() + '</tr>'
  end
  s << '</table>'
  File.open('table.php', 'w') do |f| f << s end
  #`open index.html`
end

#make_index

LANGUAGES = <<EOF unless Object.const_defined?(:LANGUAGES)
Basic: 1978-1984
Z80: 1981-1984
6502: 1983-1984
FORTH: 1984
68000: 1985-1989
C: 1985-1992
Fortran: 1987
Pascal: 1988
Smalltalk: 1987-1988, 1998
Common Lisp: 1991-1998
Java: 1994-1998,2001-2005
Python: 1999-2005
C++: 1999-2001
Haskell: 2000-2001
Javascript: 2002-2006
LZX: 2002-2006
XSLT: 2003-2005
PHP: 2004-2006
Ruby: 2005-2006
EOF

CATEGORIES = {'Assembly' => 'Z80 6502 6800', 
  'Dynamic' => 'Smalltalk Common_Lisp',
  'Enterprise' => 'C C++ Java',
  'Scripting' => 'PHP Ruby Javascript Python'}.map if false

def makeChart
order = %w{Basic Common_Lisp Python Haskell XSLT Ruby Z80 6502 Fortran Pascal C Smalltalk Java 68000 C C++ Python PHP Ruby Javascript LZX FORTH}.map{|w|w.gsub(/_/, ' ')}
  p LANGUAGES.map{|l|l.split(/:/)[0]}-order
  p LANGUAGES.map{|w|order.index w}
  map = LANGUAGES.sort_by{|w|order.index w.split(/:/)[0]}.map do |s|
    name, years = s.split(/:\s*/)
    [name, years.split(/,\s*/).map{|r| a,b = r.split(/-/); (a.to_i)..(b||a).to_i}]
  end
  min = map.map{|n,r|r}.flatten.map{|r|r.first}.min
  max = map.map{|n,r|r}.flatten.map{|r|r.last}.max
  cols = []
  cols << ['']+map.map{|n,_|n}
  for y in min..max
    cols << [y.to_s[2..-1]]
    l = map.select{|_,rs|rs.any?{|r|r.include? y}}.map{|n,_|n}
    for n, rs in map
      cols.last << (l.include?(n) ? '*' : '')
    end
  end
  cols
end

def transpose m
  m.first.zip(*m[1..-1])
end

def writeChart
  m = transpose makeChart
  s = '<table>'
  for r in m
    s << '<tr>'
    s << r.map{|c|c=='*'?'<td style="background: red">&nbsp;</td>':"<td>#{c}</td>"}.join('')
    s << '</tr>'
  end
  s << '</table>'
  File.open('test.html', 'w') do |f| f << s end
  `open test.html`
end

#writeChart
