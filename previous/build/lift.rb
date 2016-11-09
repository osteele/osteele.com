module Lift
  class Lifter
    def initialize basis
      @basis = basis
    end
    
    def method_missing id, *args
      if id =~ /!$/
        @basis.each {|o| o.send id, *args}
      else
        @basis.map {|o| o.send id, *args}
      end
    end
  end
  
  def lift
    Lifter.new(self)
  end
end

module Enumerable; include Lift; end

class Array; include Enumerable; end

p ['A'].lift
