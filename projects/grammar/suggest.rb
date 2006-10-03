require 'rubygems'
require 'yaml'
require 'extensions/enumerable'
require 'lexicon'
require 'pp'

class Grammar
  def initialize
    yaml = YAML::load_file 'fsa.yaml'
    @initial_state = :a
    @final_states = yaml.last[:terminal]
    @rules = yaml[0..-1].map { |x| Rule.new(x) }
  end
  
  def initial_state
    return []
  end
  
  def find_matches states, windows
    find_initial_matches windows
  end
  
  def find_initial_matches windows
    states = []
    for rule in @rules.select { |rule| rule.is_initial? }
      for window in windows
        match = rule.matches window
        if match
          states += OpenStruct.new({:rule => rule})
        end
      end
    end
    return states
  end
end

class Rule
  attr_reader :to, :from, :prior, :post
  
  def initialize(attrs)
    @to = attrs[:to]
    @from = attrs[:from]
    @prior = attrs[:prior]
    @post = attrs[:post]
  end
  
  def is_initial?; true; end
  def left_context; 0; end
  
  def matches window
    i0 = window.prior_index - left_context
    i1 = window.post_index - left_context
    return false if [i0, i1].min <= 0
    
  end
end

def calculate_windows w0, w1
  require 'diff/lcs'
  def strip tokens
    tokens.map do |token|
      token.form
    end
  end
  diffs = Diff::LCS.diff(strip(w0), strip(w1))
  i0 = 0
  i1 = 0
  diffs.map do |changes|
    i = changes.first.position
    pos = OpenStruct.new({:prior_index => i,
                           :post_index => i - i0 + i1})
    changes.each do |change|
      case change.action
      when '-'
        i1 -= 1
        break
      when '+'
        i1 += 1
        break
      end
    end
    pos
  end
end

def make_suggestions before, after
  tokens0 = tokenize(before)
  tokens1 = tokenize(after)
  words0 = morphize(tokens0)
  words1 = morphize(tokens1)
  windows = calculate_windows(tokens0, tokens1)
  windows.each do |window|
    window.prior = words0
    window.post = words1
  end
  grammar = Grammar.new
  state0 = grammar.initial_state
  matches = grammar.find_matches(state0, windows)
  pp matches
  return
  state1 = grammar.advance_state state0, matches
  partials = grammar.collect_recommendations state1, words1
end

def align_items changes, xs, ys
  i = 0
  j = 0
  align_to = Proc.new do |n|
    p [n, i, j, xs, ys]
    while i < n
      xs[i].post_index = j
      ys[j].prior_index = i
      i += 1
      j += 1
    end
  end
  for change in changes.flatten do
    align_to.call(change.position - 1)
    case change.action
    when '-'
      i += 1
    when '+'
      j += 1
    else
      raise "unknown action"
    end
  end
  align_to.call xs.length
end

def morphize tokens
  tokens.map do |token| morph token end
end

def tokenize string
  tokens = []
  pos = 0
  while string && string != ''
    case string
      when /^\s+/
      pos += $&.length
      string = $'
      next
      when /^\w+/
      when /^./
    end
    tokens << OpenStruct.new({:form => $&, :position => pos})
    pos += $&.length
    string = $'
  end
  #tokens.each_with_index do |token, index| token.index = index end
  tokens
end

class WordSet
  attr_accessor :index, :alternatives
  
  def initialize(index, alternatives)
    @index = index
    @alternatives = alternatives
  end
end

def morph token
  words = Lex.morph(token.form)
  set = WordSet.new(token.index, words)
  #set.prior_index = token.prior_index if token.prior_index
  set
end

def diff prior, post
  raise "if you keep me, remove source positions before comparing"
  require 'diff/lcs'
  diffs = Diff::LCS.diff(prior, post)
  pp diffs
  diffs.flatten
#   require 'diff'
#   changes = Diff.new(prior, post)
#   changes = changes.map do |change|
#     OpenStruct.new({:action => action, :index => 
#   pp changes
#   changes
  #diffs.flatten.select { |change| change.element != '' }
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
make_suggestions "I see", "I am see"
make_suggestions "I am see", "I be see"
# make_suggestions "I see", "He see"

#p (tokenize "He is leaving.  She is not.")
