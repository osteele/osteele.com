#!/usr/bin/python

import cgi
import sys
import traceback

print "Content-type: text/plain"
print

def createContent(pattern, graphKey=None):
    import FSA
    import reCompiler
    from rematch import parseDot, fsa2dot, fsa2obj
    from encoder import JSONEncoder
    
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
    if graphKey:
        obj = obj[graphKey]['graph']
    
    return JSONEncoder().encode(obj)


try:
    form = cgi.FieldStorage()
    
    pattern = 'a*b'
    graphKey = None
    if form.has_key('pattern'):
        pattern = form.getvalue('pattern')
    if form.has_key('graph'):
        graphKey = form.getvalue('graph')
    
    import urllib, os
    fname = os.path.join('cache', urllib.quote_plus(pattern, '') + '.json')
    if graphKey:
        fname = os.path.join('../rework/cache', urllib.quote_plus(pattern, '') + '.json')
    
    if os.path.exists(fname):
        print open(fname).read()
    else:
        import reCompiler
        from encoder import JSONEncoder
        try:
            text = createContent(pattern, graphKey)
            open(fname, 'w').write(text)
            print text
        except reCompiler.ParseError, e:
            print JSONEncoder().encode(str(e))
except Exception, e:
    print "Unexpected error:", e
    #traceback.print_tb(sys.exc_traceback)
    #traceback.print_exc()
    #print traceback.sys.last_traceback(
