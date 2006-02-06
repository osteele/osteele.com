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

LANGUAGES = <<EOF unless Object.const_defined?(:LANGUAGES)
BASIC: 1978-1984
Z80: 1981-1984
6502: 1983-1984
FORTH: 1984
68000: 1985-1989
C: 1985-1992
FORTRAN: 1987
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

def categories
  c = ['Utility => BASIC C Common_Lisp Python XSLT Javascript Ruby', 
    'Systems => 68000 C C++',
    'Application => BASIC Z80 6502 FORTRAN Pascal C Smalltalk Common_Lisp',
    'Server => Python Java PHP Ruby',
    'Client => Java Javascript LZX',
    'Stretch => BASIC Z80 Smalltalk Common_Lisp Java Haskell LZX'
  ]
  c.map do |line|
    k, v = line.split(/\s*=>\s*/)
    [k, v.split.map{|n|n.sub(/_/, ' ')}]
  end
end

#p categories

def languages
  map = LANGUAGES.map do |s|
    name, years = s.split(/:\s*/)
    [name, years.split(/,\s*/).map{|r| a,b = r.split(/-/); (a.to_i)..(b||a).to_i}]
  end
  map
end

def makeChart
  #order = %w{BASIC Common_Lisp Python Haskell XSLT Ruby Z80 6502 FORTRAN Pascal C Smalltalk Java 68000 C C++ Python PHP Ruby Javascript LZX FORTH}.map{|w|w.gsub(/_/, ' ')}
  #p LANGUAGES.map{|l|l.split(/:/)[0]}-order
  #p LANGUAGES.map{|w|order.index w}
  #map = LANGUAGES.sort_by{|w|order.index w.split(/:/)[0]}.map do |s|
  #  name, years = s.split(/:\s*/)
  #  [name, years.split(/,\s*/).map{|r| a,b = r.split(/-/); (a.to_i)..(b||a).to_i}]
  #end
  languages, min, max = languages
  cols = []
  cols << ['']+languages.map{|n,_|n}
  for y in min..max
    cols << [y.to_s[2..-1]]
    names = map.select{|_,rs|rs.any?{|r|r.include? y}}.map{|n,_|n}
    for n, rs in languages
      cols.last << (names.include?(n) ? '*' : '')
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

def ranges_for_category c, n, rs
  r = {'Stretch/BASIC' => 1978..1983,
    'Stretch/Z80' => 1984..1985,
    'Stretch/Smalltalk' => 1987..1988,
    'Stretch/Common Lisp' => 1991..1995,
    'Stretch/Java' => 1994..1995,
    'Utility/C' => 1985..1990,
    'Utility/Common Lisp' => 1991..1998,
    'Javascript' => 2005..2005,
    'Application/Common Lisp' => 1991..1995,
    'Client/Java' => 1994..1998}["#{c}/#{n}"]
  r ? [r] : rs
end

# sorted by categories
def categorized_languages
  lines = []
  for name, items in categories
    lines << [name, []]
    lines += languages.select {|n,_| items.include?(n)}.
      map{|n,rs|[n,ranges_for_category(name, n, rs)]}
  end
  lines
end

def projects
  ['Starswarm/Z80/1984',
    'Pogo Joe/6502/1984',
    'Quickdraw GX/C/1989',
    'Dylan/Common Lisp/1992',
    'Method Game Engine/Java/1994',
    'PyWordnet/Python/1999',
    'JWordNet/Java/1998',
    'AGL/C++&Haskell/1998',
    'OpenLaszlo/Python&Java/2002',
    'OpenLaszlo/XSLT/2003',
    'Storybase/Ruby/2005',
    'PackageMapper/Ruby/2005',
    'Expialidocious/LZX&PHP&Javascript/2005'].map{|s|s.split('/')}
end

# Agenda:
# - add lines
# - second graph with groupings
# - add projects below
# - add projects to the right
# - add hyperlinks
# - right-align langauges?
require 'rvg/rvg'
include Magick
def makeImage categorize=false
  entries = languages
  entries = categorized_languages if categorize
  years = entries.map{|_,r|r}.flatten.map{|r|[r.first,r.last]}.flatten
  min = years.min
  max = years.max
  
  lw = 60 # label width
  bh = 20 # cell height
  bw = 25 # cell width
  bartop = 20
  bartop += 20 unless categorize
  height = bartop+bh*entries.length
  height += categories.length*(5+5) if categorize
  category_colors = {'Utility' => 'green',
    'Systems' => 'silver',
    'Application' => 'purple',
    'Server' => 'red',
    'Client' => 'blue',
    'Stretch' => 'yellow'}
  type_colors = [
    'Assembly: Z80 6502 68000 => white',
    'Systems: C C++ => black',
    'Dynamic: Common_Lisp Smalltalk => yellow',
    'Scripting: Javascript PHP Ruby Python => red',
    'Education: BASIC Pascal => blue',
    'Research: Haskell => navy',
    'General Purpose: C C++ FORTRAN Java => green',
    'Special-Purpose: LZX FORTH XSLT => purple'
  ]
  type_colors_h = Hash[*type_colors.map{|s|s.split(/\s*=>\s*/)}.flatten]
  language_colors = Hash[*type_colors_h.map{|k,c|k.split(/:/)[1].split.map{|n|
        [n.sub(/_/, ' '), c]}}.flatten]
  language_categories = type_colors.map{|s|s.match(/(.*):.*=>\s*(.*)/).to_a[1..-1]}
  height += bh*2.5 unless categorize # for the legend
  
  rvg = RVG.new(lw+bw*(max+1-min), height).
    viewbox(5,0,lw+bw*(max+1-min)+bw/2, lw+entries.length*bh).
    preserve_aspect_ratio('xMidYMin', 'meet') do |canvas|
    canvas.background_fill = 'white'
    # column labels (years)
    for year in min..max do
      canvas.text(lw+(year-min)*bw-bw/2, 0, year.to_s.sub(/^x../, "'")).
        styles(:text_anchor=>'start').rotate(-60)
    end
    y = bartop
    for name, spans in entries
      if categorize and spans.empty?
        label = name
        label = '"Stretch"' if label == 'Stretch'
        label += ' Languages'
        y += 5
        canvas.text(0, y+bh, label).styles(:text_anchor=>'start', :font_weight=>'bold', :font_size=>20)
        y += bh + 5
        color = category_colors[name]
        next
      end
      color = language_colors[name] unless categorize
      indent = categorize ? 10 : 0
      canvas.text(indent,y+bh-8, name).styles(:text_anchor=>'start', :font_size=>14)
      def sr canvas,w,h,x,y,color
        canvas.rect(w+3,h,x-1,y+2).styles(:stroke=>'black', :stroke_width=>2,
                                        :stroke_opacity=>0.25, :fill=>'none')
        canvas.rect(w,h,x,y).styles(:fill=>color)
        canvas.rect(w,h,x,y).styles(:fill=>'white', :opacity=>0.25,
                                    :stroke=>'black')
      end
      for span in spans
        sr(canvas,(span.last+1-span.first)*bw, bh-5,
           lw+(span.first-min)*bw, y, color)
      end
      y += bh
    end
    
    # Legend
    unless categorize
      x0 = lw+bh
      y0 = y+bh/2
      lc = language_categories.to_a
      for catname, color in lc
        sr canvas, bw, bh-6, x0, y0, color
        canvas.text x0+bw+8, y0+bh-8, catname
        y0 += bh
        if (1+lc.map{|n,_|n}.index(catname)) % 2 == 0
          x0 += 110
          y0 = y+bh/2
        end
      end
    end
    
    if false
    yt = y
    colys = Hash.new {|h,k| h[k] = yt}
    for name, lang, year in projects
      col = year.to_i - min
      y = colys[year] += bh
      canvas.text(lw+col*bw, y, name)
      canvas.g do |g|
        g.translate(lw+col*bw, y)
        g.g do |gg|
          gg.rotate(60)
          gg.text(0,0,name)
        end
      end
    end
    end
  end
  fname = categorize ? 'languages-by-use.png' : 'languages.png'
  rvg.draw.write(fname)
  `open #{fname}`
end

makeImage false
