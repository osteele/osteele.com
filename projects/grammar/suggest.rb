require 'rubygems'
require 'extensions/enumerable'
require 'ostruct'
require 'lexicon'
require 'pp'

def make_suggestions before, after
  tokens0 = tokenize(before)
  tokens1 = tokenize(after)
  words0 = tokens0.map do |token|
    morph(token).each { |word| word.position = token.position }
  end
  changes = diff(tokens0, tokens1)
  pp changes
  return
  words1 = apply_changes(changes, words0)
  pp words1
  return
  matches = []
  for pos in words0.length
    matches += rules.select { |rule| testRule rule, words0, words1, pos }
  end
  suggestions = applyRule
end

def apply_changes changes, words
  word = words.mapf(:clone)
  for change in changes
  end
  return words
end

def tokenize string
  tokens = []
  pos = 0
  index = 0
  while string && string != ''
    case string
      when /^\s+/
      pos += $&.length
      string = $'
      next
      when /^\w+/
      when /^./
    end
    tokens << OpenStruct.new({:position => pos, :index => index, :form => $&})
    pos += $&.length
    index += 1
    string = $'
  end
  tokens
end

def morph token
  words = Lex.morph(token.form)
  words.map do |word|
    word.pos = token.pos
    word
  end
end

def diff before, after
  require 'rubygems'
  require 'diff/lcs'
  diffs = Diff::LCS.diff(before, after)
  diffs.flatten.select { |change| change.element != '' }
  #raise "diffs.length == #{diffs.length} != 1" unless diffs.length == 1
  #return diffs.first
end

unless Object.const_defined? :Lex
  require 'lexicon'
  Lex = Lexicon.new
end

Exceptions = {
  ['us', 'PRP'] => 'we'
} unless Object.const_defined? :Exceptions

def inflect string, pos
  if pos == 'VBZ' or pos == 'NNS'
    if string =~ /(s|sh|ch)$/
      string += 'e'
    end
    string += 's'
  end
  string
end

def parse_pattern_spec str
  ts = tokenize(str).map do |w|
    attr = nil
    if w =~ /\[(.*)\]$/
      w = $`
      attr = $1
    end
    [w, attr]
  end
  lx = ts.map do |s, r|
    if r
      cs = Lex.morph(s)
      cs = cs.first { |c| c[:base] == r }
    else
      {:form => s}
    end
  end
  lx
end

class PatternClass
  def initialize
    self.read
  end
  
  def read
    require 'yaml'
    @patterns = YAML::load_file('patterns.yaml').map do |s|
      str = s[:before][:words]
      parse_pattern_spec str
      #tokens.map{|tk| Lex.morph(tk)}
    end
    #p @patterns.first
  end
end

# Patterns = PatternClass.new unless Object.const_defined? :Patterns
# Patterns.read

def main s0, s1
  t0 = tokenize(s0)
  t1 = tokenize(s1)
  require 'rubygems'
  require 'diff/lcs'
  diff = Diff::LCS.diff t0, t1
  p diff
end

if $0 == __FILE__
  begin
    t0, t1 = ARGV
    main(t0, t1)
  rescue
    puts "Error: #{$@.join("\n")}: #{$!}"
  end
end

#make_suggestions "is leaving", "will leaving"
# make_suggestions "I see", "I am see"
 make_suggestions "I am see", "I see"
# make_suggestions "I see", "He see"

#p (tokenize "He is leaving.  She is not.")
