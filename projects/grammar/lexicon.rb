require 'ostruct'

# class Word
#   def initialize
#   end
# end

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
  
  def tag string
    tag = @@lexicon[string]
    unless tag
      # unknown words are nouns
      tag = string =~ /s$/ ? 'NNS' : 'NN'
    end
    tag
  end
  
  def morph string
    tags = tag string
    tagMap = {}
    words = tags.map do |tag|
      base = string
      base = $` if tag == 'VBZ' and string =~ /(es|s)$/
      base = $` if tag == 'NNS' and string =~ /(es|s)$/
      #base = Exceptions[[string.downcase, tag]] || base
      word = OpenStruct.new({:base => base, :tag => tag})
      word.number = 'sing'   if tag == 'VB' or tag == 'NN'
      word.number = 'plural' if tag == 'VBZ' or tag == 'NNS'
      word.tag = tagMap[tag] || tag
      word
    end
    override = @@words[string.downcase]
    words = [OpenStruct.new(override)] if override
    return words
  end
end
