import reCompiler
import FSA
from tempfile import mkstemp

def fsa2dot(fsa):    
    inf = mkstemp('.dot')[1]
    f = open(inf, 'w')
    f.write(fsa.toDotString())
    f.close()
    
    outf = mkstemp('.dot')[1]
    bindir = "/Applications/Graphviz.app/Contents/MacOS"
    #bindir = "/home/osteele/bin"
    import os
    os.system("%s %s -o %s" % (bindir+"/dot", inf, outf))
    f = open(outf)
    s = f.read()
    return s

def parseDot(s):
    import re
    nodes = []
    edges = []
    # look greedy inside []
    for label, attrs in re.findall("([^[\n\t]+?)\s+\[(.*)\]", s):
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
            # edge
            h['start'], h['stop'] = match.groups()
            h['pos'] = [{'x': float(ps[0]), 'y': float(ps[1])}
                        for ps in [xx.split(',') for xx in h['pos'].split(' ')]]
            edges += [h]
        else:
            # node
            h['name'] = label
            h['x'], h['y'] = [float(n) for n in h['pos'].split(',')]
            nodes += [h]
    return {'nodes': nodes, 'edges': edges}

#print (fsa2dot(reCompiler.compileRE('a|b')))
#print parseDot(fsa2dot(reCompiler.compileRE('a|b')))
