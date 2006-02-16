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
        
    fsa = reCompiler.compileRE(pattern)
    #dfa = fsa.determinized()
    struc = parseDot(fsa2dot(fsa))
    struc['pattern'] = pattern
    struc['version'] = '1'
    
    from encoder import JSONEncoder
    print JSONEncoder().encode(struc)
    #print toxml(struc)
except Exception, e:
    print "Unexpected error:", e
    #traceback.print_tb(sys.exc_traceback)
    #traceback.print_exc()
    #print traceback.sys.last_traceback(
