import reCompiler
import FSA
from tempfile import mkstemp

if True:
    fsa = reCompiler.compileRE('(a*b)|(cd*)')
    
    inf = mkstemp('.dot')[1]
    f = open(inf, 'w')
    f.write(fsa.toDotString())
    f.close()
    
    outf = mkstemp('.dot')[1]
    bindir = "/Applications/Graphviz.app/Contents/MacOS"
    import os
    os.system("%s %s -o %s" % (bindir+"/dot", inf, outf))
    s = open(outf).read()

import re
nodes = []
edges = []
# look greedy inside []
for label, attrs in re.findall("([^[\n\t]+?)\s+\[(.*?)\]", s):
    if label == 'node': continue
    h = {}
    for k, v1, v2 in re.findall('([^=,\s]+)=(?:"([^""]*?)"|([^,""]*))', attrs):
        v = v1 or v2
        if k == 'pos' and v.startswith('e'):
            arp, v = re.match("e,(\d+,\d+)\s+(.*)", v).groups()
            h['endArrow'] = arp
        h[k] = v
    match = re.match("(.*?)\s*->\s*(.*)", label)
    if match:
        h['start'], h['stop'] = match.groups()
        edges += [h]
    else:
        h['name'] = label
        nodes += [h]

def toxml(o, s=''):
    def addTag(n, o):
        s = "\n<%s" % n
        if type(o) == type({}):
            for k, v in o.items():
                if type(v) != type({}) and type(v) != type([]):
                    s += " %s=%s" % (k, `v`)
        s += ">"
        s += toxml(o)
        s += "</%s>" % n
        return s
    if type(o) == type({}):
        for k, v in o.items():
            if type(v) == type([]):
                for i in v:
                    s += addTag(k, i)
            elif type(v) == type({}):
                s += addTag(k, v)
    return s

print toxml({'graph': {'node': nodes, 'edge': edges}})
