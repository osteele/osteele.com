module Enumerable
  def method_missing id, *args
    if first.respond_to? id
      return map {|n| n.send id, *args}
    end
    super
  end
end

class Proc
  def map items
    items.map &self
  end
end
