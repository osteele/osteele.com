def languages
  lines = <<-EOF
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
  lines.split("\n").map do |s|
    name, years = s.split(/:\s*/)
    [name.strip, years.split(/,\s*/).map{|r| a,b = r.split(/-/); (a.to_i)..(b||a).to_i}]
  end
end

def categories
  c = ['Utility => BASIC C Common_Lisp Python XSLT Javascript Ruby', 
    'Systems => 68000 C C++',
    'Desktop Application => BASIC Z80 6502 FORTRAN Pascal C Smalltalk Common_Lisp',
    'Server => Python Java PHP Ruby',
    'Client => Java Javascript LZX',
    'Stretch => BASIC Z80 Smalltalk Common_Lisp Java Haskell LZX'
  ]
  c.map do |line|
    k, v = line.split(/\s*=>\s*/)
    [k, v.split.map{|n|n.sub(/_/, ' ')}]
  end
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
    'Desktop Application/Common Lisp' => 1991..1995,
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

def makeDiagram categorize=false
  entries = languages
  entries = categorized_languages if categorize
  years = entries.map{|_,r|r}.flatten.map{|r|[r.first,r.last]}.flatten
  min = years.min
  max = years.max
  
  lw = 60 # label width
  bh = 20 # cell height
  bw = 25 # cell width
  category_colors = {'Utility' => 'green',
    'Systems' => 'silver',
    'Desktop Application' => 'purple',
    'Server' => 'red',
    'Client' => 'blue',
    'Stretch' => 'yellow'}
  # purple, red, blue, orange, green, yellow
  type_colors = [
    'Assembly: Z80 6502 68000 => white',
    'Systems: C C++ => black',
    'Dynamic: Common_Lisp Smalltalk => rgb(255,0,255)',
    'Scripting: Javascript PHP Ruby Python => rgb(255,0,0)',
    'Education: BASIC Pascal => rgb(0,255,255)',
    'Research: Haskell => rgb(0,255,0)',
    'General Purpose: C C++ FORTRAN FORTH Java => rgb(0,0,255)',
    'Special-Purpose: LZX XSLT => rgb(255,255,0)'
  ]
  type_colors_h = Hash[*type_colors.map{|s|s.split(/\s*=>\s*/)}.flatten]
  language_colors = Hash[*type_colors_h.map{|k,c|k.split(/:/)[1].split.map{|n|
        [n.sub(/_/, ' '), c]}}.flatten]
  language_categories = type_colors.map{|s|s.match(/(.*):.*=>\s*(.*)/).to_a[1..-1]}
  
  diagram = Picture.new

  diagrm.define :shadebar do |x0, x1|
    canvas.rect(w+2,h-1,x,y+2).styles(:stroke=>'black', :stroke_width=>3,
                                      :stroke_opacity=>0.25, :fill=>'none')
    canvas.rect(w,h,x,y).styles(:fill=>color)
    canvas.rect(w,h,x,y).styles(:fill=>'white', :opacity=>0.75,
                                :stroke=>'black')
  end
  
  years = min..max
  columns = diagram.columns(years).indent(20)
  columns.lift.width(bw)
  columns.lift.label.rotate(-60)
  for name, spans in entries
    if categorize and spans.empty?
      label = name
      label = '"Stretch"' if label == 'Stretch'
      label += ' Languages'
      diagram.row(label, :font_size => 20)
      color = category_colors[name]
    else
      color = language_colors[name] unless categorize
      indent = categorize ? 10 : 0
      diagram.row(name, :font_size => 14).indent(indent)
      for span in spans
        diagram.row << shadebar(diagram.columns[span.first].left,
                                diagram.columns[span.last].right)
      end
    end
    
    # Legend
    unless categorize
      diagram.grid do |g|
        for catname, color in language_categories
          g.hbox do |h|
            h << shadebar bw, bh-6, x0, y0, color
            h.text catname
          end
        end
      end
    end
  end
  fname = categorize ? 'languages-by-use.png' : 'languages.png'
  diagram.save(fname)
  `open #{fname}`
end

makeImage false
#makeImage true
