#!/usr/bin/python
from markov import gen

print "content-type: text/html"
print
print gen(open('flash.txt'))
