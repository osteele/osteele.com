""" 
The script markov.py reads text from standard input and writes
a pagragraph of text to standard output.  Blank lines in the 
input are treated as paragraph separtors and are represented
as '\n' in the code.
"""
import random

nlnl = '\n', '\n'

def new_key(key, word):
   if word == '\n': return nlnl
   else: return (key[1], word)

def markov_data_from_words(words):
   data = {}
   key = nlnl
   for word in words:
      if word == '\n': continue
      data.setdefault(key, []).append(word)
      key = new_key(key, word)
   return data

def words_from_markov_data(data):
   key = nlnl
   while 1:
       word = random.choice(data.get(key, nlnl))
       key = new_key(key, word)
       yield word

def splice_punctuation(data):
   previous = None
   for word in data:
      if previous and word in '?!.,':
         yield previous + word
         previous = None
      else:
         if previous: yield previous
         previous = word
   if previous:
      yield previous

def words_from_file(f):
   for line in f:
      words = line.split()
      if len(words):
         for word in words:
            if word[-1] in ',.!?':
               yield word[:-1]
               word = word[-1]
            yield word
      else:
         yield '\n'
   yield '\n'

def paragraph_from_words(words):
   result = []
   for word in splice_punctuation(words):
       if word == '\n': break
       result.append(word)
   return ' '.join(result)

def gen(file):
   return paragraph_from_words(
      words_from_markov_data(
      markov_data_from_words(
      words_from_file(file))))

if __name__ == '__main__':
   import sys
   print paragraph_from_words(
           words_from_markov_data(
               markov_data_from_words(
                   words_from_file(
                       sys.stdin))))
