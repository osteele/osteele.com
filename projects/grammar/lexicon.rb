require 'ostruct'

class Lexicon
  @@lexicon ||= nil

  def initialize
    reset unless @@lexicon
  end
  
  def reset
    @@lexicon = {}
    open('lexicon.txt') do |f|
      for line in f.read.split("\n") do
        key, *value = line.split
        #p line if key =~ /^Run$/i
        @@lexicon[key] = value
      end
    end
    
    require 'yaml'
    @@words = YAML::load_file('exceptions.yaml')
  end
  
  def pos string
    pos = @@lexicon[string]
    unless pos
      pos = string =~ /s$/ ? 'NNS' : 'NN'
    end
    pos
  end
  
  def morph string
    poss = pos string
    words = poss.map do |pos|
      base = string
      base = $` if pos == 'VBZ' and string =~ /(es|s)$/
      base = $` if pos == 'NNS' and string =~ /(es|s)$/
      #base = Exceptions[[string.downcase, pos]] || base
      word = OpenStruct.new({:base => base, :tag => pos})
      word.number = 'sing'   if pos == 'VB' or pos == 'NN'
      word.number = 'plural' if pos == 'VBZ' or pos == 'NNS'
      word
    end
    override = @@words[string.downcase]
    words = [OpenStruct.new(override)] if override
    return words
  end
end
