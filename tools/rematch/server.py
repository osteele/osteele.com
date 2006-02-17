#!/usr/bin/python

import cgi
import sys
import traceback

print "Content-type: text/plain"
print

try:
    import FSA
    import reCompiler
    
    form = cgi.FieldStorage()
    
    from rematch import *
    
    pattern='a*b'
    if form.has_key('pattern'):
        pattern = form.getvalue('pattern')
    
    obj = {}
    obj['pattern'] = pattern
    fsa = reCompiler.compileRE(pattern, minimize=0, recordSourcePositions=1)
    # withoutEpsilons doesn't preserve metadata, so capture it in the
    # dfa first
    dfa = fsa.minimized()
    fsa = fsa.withoutEpsilons()
    obj['nfa'] = {'graph': parseDot(fsa2dot(fsa)),
                  'model': fsa2obj(fsa)}
    obj['dfa'] = {'graph': parseDot(fsa2dot(dfa)),
                  'model': fsa2obj(dfa)}
    
    from encoder import JSONEncoder
    print JSONEncoder().encode(obj)
except Exception, e:
    print "Unexpected error:", e
    #traceback.print_tb(sys.exc_traceback)
    #traceback.print_exc()
    #print traceback.sys.last_traceback(
