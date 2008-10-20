class Shape
  attr_accessor :parent
  
  def initialize
    @transforms = []
  end
  
  def label text=nil, options={}
    @label = Text.new(label, options) if text
    @label
  end
  
  def rotate r
    @transforms << [:rotate, r]
  end
end

class Group < Shape
  include Lift
  
  def initialize
    @shapes = []
    @definitions = {}
  end
  
  def << shapes
    case Shape
    when Enumerable
      @shapes += shapes
      shapes.each {|s| s.parent = self}
    when Shape
      @shapes << shapes
      shapes.parent = self
    else
      raise 'not a shape'
    end
  end
  
  def define name, &block
    @definitions[name] = block
  end
  
  def missing_method 
  
  def map &block
    @shapes.map &block
  end
end

class Labeled < Group
  def initialize text=nil, options={}
    label text, options
  end
end

class Text < Shape
  def initialize text, options={}
    @text = text
    @options = options
  end
end

class Picture < Group
  def initialize
    @rows = Shape.new
    @columns = Shape.new
  end
  
  def columns labels=nil
    @columns << labels.map{|l| Labeled.new(l)} if labels
    @columns
  end
  
  def row name, options={}
    @rows << Labeled.new(name, options)
  end
  
  def save fname
    1/0
  end
end
