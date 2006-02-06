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

def transpose m
  m.first.zip(*m[1..-1])
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

# unused
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
  bartop = 30
  bartop += 10 unless categorize
  height = bartop+bh*entries.length
  height += categories.length*(5+8) if categorize
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
        y += bh + 8
        color = category_colors[name]
        next
      end
      color = language_colors[name] unless categorize
      indent = categorize ? 10 : 0
      canvas.text(indent,y+bh-8, name).styles(:text_anchor=>'start', :font_size=>14)
      def sr canvas,w,h,x,y,color
        canvas.rect(w+2,h-1,x,y+2).styles(:stroke=>'black', :stroke_width=>3,
                                          :stroke_opacity=>0.25, :fill=>'none')
        canvas.rect(w,h,x,y).styles(:fill=>color)
        canvas.rect(w,h,x,y).styles(:fill=>'white', :opacity=>0.75,
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
      x0 = lw+bh*3
      y0 = y+bh/2
      lc = language_categories.to_a
      for catname, color in lc
        sr canvas, bw, bh-6, x0, y0, color
        canvas.text x0+bw+8, y0+bh-8, catname
        y0 += bh
        if (1+lc.map{|n,_|n}.index(catname)) % 2 == 0
          x0 += 105
          y0 = y+bh/2
        end
      end
    end
    
    # projects per year
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
makeImage true
