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
    fsa = reCompiler.compileRE(pattern, minimize=0)
    #obj['fsa'] = parseDot(fsa2dot(fsa))
    dfa = fsa.minimized()
    obj['dfa'] = {'graph': parseDot(fsa2dot(dfa)),
                  'model': fsa2obj(dfa)}
    
    from encoder import JSONEncoder
    print JSONEncoder().encode(obj)
    #print toxml(struc)
except Exception, e:
    print "Unexpected error:", e
    #traceback.print_tb(sys.exc_traceback)
    #traceback.print_exc()
    #print traceback.sys.last_traceback(
