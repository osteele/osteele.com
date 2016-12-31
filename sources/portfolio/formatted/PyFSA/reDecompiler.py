<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
<HEAD>
<TITLE>reDecompiler.py</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#1F00FF" ALINK="#FF0000" VLINK="#9900DD">
<A NAME="top">
<A NAME="file1">
<H1>PyFSA/reDecompiler.py</H1>

<PRE>
<FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;
To do:
New features:
    - iteration reduction
Bug fixes:
    - A* ending up A?*
    - (a*b)*
Cleanup:
    - fix the case where there's no expression
    - alphabetize multiple branches (or sort by source order?)
Optimizations:
    - only walk the cycle when there's a suffix

&quot;&quot;&quot;</FONT></B>

__author__  = <FONT COLOR="#BC8F8F"><B>&quot;Oliver Steele &lt;steele@cs.brandeis.edu&gt;&quot;</FONT></B>

<B><FONT COLOR="#A020F0">from</FONT></B> compileRE <B><FONT COLOR="#A020F0">import</FONT></B> compileRE

<I><FONT COLOR="#B22222">#
</FONT></I><I><FONT COLOR="#B22222"># Converting FSAs to RE trees
</FONT></I><I><FONT COLOR="#B22222">#
</FONT></I>
<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">decompileFSA</FONT></B>(fsa, dottedStates=[], wrap=None, sep=None, returnTree=0):
    queries = {}
    <B><FONT COLOR="#A020F0">if</FONT></B> dottedStates:
        <B><FONT COLOR="#A020F0">for</FONT></B> state <B><FONT COLOR="#A020F0">in</FONT></B> dottedStates:
            queries[state] = []
    tree = computeSubgraphTree(fsa.transitions, fsa.initialState, fsa.finalStates, dottedStates)
    <B><FONT COLOR="#A020F0">if</FONT></B> returnTree <B><FONT COLOR="#A020F0">or</FONT></B> tree <B><FONT COLOR="#A020F0">is</FONT></B> None:
        <B><FONT COLOR="#A020F0">return</FONT></B> tree
    <B><FONT COLOR="#A020F0">if</FONT></B> sep <B><FONT COLOR="#A020F0">is</FONT></B> None:
        sep = <FONT COLOR="#BC8F8F"><B>''</FONT></B>
        labels = fsa.alphabet <B><FONT COLOR="#A020F0">or</FONT></B> fsa.labels()
        <B><FONT COLOR="#A020F0">if</FONT></B> filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> label:type(label) == type(<FONT COLOR="#BC8F8F"><B>'a'</FONT></B>), labels) == labels <B><FONT COLOR="#A020F0">and</FONT></B> filter( <B><FONT COLOR="#A020F0">lambda</FONT></B> label:len(label) &gt; 1, labels):
            sep = <FONT COLOR="#BC8F8F"><B>' '</FONT></B>
    <I><FONT COLOR="#B22222">#print tree
</FONT></I>    <I><FONT COLOR="#B22222">#tree = combineDisjunctionEnds(tree)
</FONT></I>    <I><FONT COLOR="#B22222">#print tree
</FONT></I>    <B><FONT COLOR="#A020F0">return</FONT></B> treeToString(tree, sep=sep, wrap=wrap)

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">addListMappingItem</FONT></B>(mapping, key, value):
    <B><FONT COLOR="#A020F0">for</FONT></B> tk, tv <B><FONT COLOR="#A020F0">in</FONT></B> mapping:
        <B><FONT COLOR="#A020F0">if</FONT></B> key == tk:
            tv.append(value)
            <B><FONT COLOR="#A020F0">return</FONT></B>
    mapping.append((key, [value]))

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">combineDisjunctionEnds</FONT></B>(tree):
    <B><FONT COLOR="#A020F0">if</FONT></B> tree <B><FONT COLOR="#A020F0">in</FONT></B> (EMPTY_TREE, DOT_TREE) <B><FONT COLOR="#A020F0">or</FONT></B> tree[0] == <FONT COLOR="#BC8F8F"><B>'LEAF'</FONT></B>:
        <B><FONT COLOR="#A020F0">return</FONT></B> tree
    <B><FONT COLOR="#A020F0">if</FONT></B> tree[0] == DISJUNCTION:
        mapping = []
        disjuncts = map(combineDisjunctionEnds, tree[1])
        <I><FONT COLOR="#B22222">#print 'incoming:', disjuncts
</FONT></I>        <B><FONT COLOR="#A020F0">for</FONT></B> disjunct <B><FONT COLOR="#A020F0">in</FONT></B> disjuncts:
            <B><FONT COLOR="#A020F0">if</FONT></B> type(disjunct) == TupleType <B><FONT COLOR="#A020F0">and</FONT></B> disjunct[0] == CONCATENATION:
                disjunct = disjunct[1]
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                disjunct = [disjunct]
            addListMappingItem(mapping, disjunct[-1], makeConcatenationTree(disjunct[:-1]))
        <B><FONT COLOR="#A020F0">if</FONT></B> len(mapping) &lt; len(disjuncts):
            disjuncts = []
            <B><FONT COLOR="#A020F0">for</FONT></B> key, paths <B><FONT COLOR="#A020F0">in</FONT></B> mapping:
                disjuncts.append(concatenateTrees(makeDisjunctionTree(paths), key))
            <I><FONT COLOR="#B22222">#print '-&gt;', disjuncts
</FONT></I>        disjuncts.sort()
        <B><FONT COLOR="#A020F0">return</FONT></B> makeDisjunctionTree(disjuncts)
    <B><FONT COLOR="#A020F0">elif</FONT></B> tree[0] == CONCATENATION:
        <B><FONT COLOR="#A020F0">return</FONT></B> (tree[0], map(combineDisjunctionEnds, tree[1]))
    <B><FONT COLOR="#A020F0">elif</FONT></B> tree[0] <B><FONT COLOR="#A020F0">in</FONT></B> QUANTIFIERS:
        <B><FONT COLOR="#A020F0">return</FONT></B> (tree[0], combineDisjunctionEnds(tree[1]))
    <B><FONT COLOR="#A020F0">else</FONT></B>:
        <B><FONT COLOR="#A020F0">raise</FONT></B> <FONT COLOR="#BC8F8F"><B>'unknown operator'</FONT></B>, tree[0]

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">computeSubgraphTree</FONT></B>(graph, start, finals, dottedStates, brokenNodes=[], isFirst=0, forbidden=[]):
    <FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;Returns a list of disjuncts.  If there's only one possibility, the list is a singleton.&quot;&quot;&quot;</FONT></B>
    <B><FONT COLOR="#A020F0">if</FONT></B> start <B><FONT COLOR="#A020F0">in</FONT></B> brokenNodes <B><FONT COLOR="#A020F0">and</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> isFirst:
        <I><FONT COLOR="#B22222">#print graph, start, finals, isFirst, brokenNodes, forbidden, ':', (start in finals and EMPTY_TREE) or None
</FONT></I>        <B><FONT COLOR="#A020F0">if</FONT></B> start <B><FONT COLOR="#A020F0">in</FONT></B> finals:
            <B><FONT COLOR="#A020F0">return</FONT></B> EMPTY_TREE
        <B><FONT COLOR="#A020F0">else</FONT></B>:
            <B><FONT COLOR="#A020F0">return</FONT></B> None
    
    wasforbidden = forbidden
    wasBroken = brokenNodes
    
    <B><FONT COLOR="#A020F0">if</FONT></B> start <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> brokenNodes:
        brokenNodes = brokenNodes + [start]
    
    preamble = None
    <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> isFirst:
        preamble = computeSubgraphTree(graph, start, [start], dottedStates, brokenNodes, isFirst=1, forbidden=forbidden)
        <B><FONT COLOR="#A020F0">if</FONT></B> preamble:
            preamble = quantifyTree(preamble)
    
    disjuncts = []
    <B><FONT COLOR="#A020F0">if</FONT></B> start <B><FONT COLOR="#A020F0">in</FONT></B> finals <B><FONT COLOR="#A020F0">and</FONT></B> start <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> forbidden:
        disjuncts.append(EMPTY_TREE)
    <B><FONT COLOR="#A020F0">if</FONT></B> preamble:
        forbidden = forbidden + [start]
    <B><FONT COLOR="#A020F0">for</FONT></B> s0, s1, label <B><FONT COLOR="#A020F0">in</FONT></B> graph:
        <B><FONT COLOR="#A020F0">if</FONT></B> s0 == start <B><FONT COLOR="#A020F0">and</FONT></B> s1 <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> forbidden:
            branch = label <B><FONT COLOR="#A020F0">and</FONT></B> makeLeaf(label) <B><FONT COLOR="#A020F0">or</FONT></B> EMPTY_TREE
            tail = computeSubgraphTree(graph, s1, finals, dottedStates, brokenNodes, isFirst=0, forbidden=forbidden)
            <B><FONT COLOR="#A020F0">if</FONT></B> tail:
                disjuncts.append(concatenateTrees(branch, tail))
    body = disjuncts <B><FONT COLOR="#A020F0">and</FONT></B> makeDisjunctionTree(disjuncts) <B><FONT COLOR="#A020F0">or</FONT></B> None
    
    <B><FONT COLOR="#A020F0">if</FONT></B> body <B><FONT COLOR="#A020F0">and</FONT></B> start <B><FONT COLOR="#A020F0">in</FONT></B> dottedStates:
        body = concatenateTrees(DOT_TREE, body)
    
    <I><FONT COLOR="#B22222">#print graph, start, finals, isFirst, wasBroken, wasforbidden, ':', preamble, '+', body
</FONT></I>    <B><FONT COLOR="#A020F0">if</FONT></B> preamble <B><FONT COLOR="#A020F0">and</FONT></B> body:
        body = concatenateTrees(preamble,body)
    <I><FONT COLOR="#B22222">#elif preamble and start in finals and not isFirst:
</FONT></I>    <I><FONT COLOR="#B22222">#   body = preamble
</FONT></I>    <B><FONT COLOR="#A020F0">return</FONT></B> body


<I><FONT COLOR="#B22222">#
</FONT></I><I><FONT COLOR="#B22222"># Tree construction
</FONT></I><I><FONT COLOR="#B22222">#
</FONT></I>
DISJUNCTION = <FONT COLOR="#BC8F8F"><B>'|'</FONT></B>
LEAF = <FONT COLOR="#BC8F8F"><B>'LEAF'</FONT></B>
CONCATENATION = <FONT COLOR="#BC8F8F"><B>':'</FONT></B>
QUANTIFIERS = (<FONT COLOR="#BC8F8F"><B>'*'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'?'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'+'</FONT></B>)
EMPTY_TREE = <FONT COLOR="#BC8F8F"><B>'e'</FONT></B>
DOT_TREE = <FONT COLOR="#BC8F8F"><B>'.'</FONT></B>

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">makeNode</FONT></B>(op, children):
    <B><FONT COLOR="#A020F0">return</FONT></B> (op, children)

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">makeLeaf</FONT></B>(value):
    <B><FONT COLOR="#A020F0">return</FONT></B> makeNode(LEAF, value)

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">makeDisjunctionTree</FONT></B>(disjuncts):
    <B><FONT COLOR="#A020F0">assert</FONT></B> disjuncts, <FONT COLOR="#BC8F8F"><B>'no disjuncts'</FONT></B>
    <B><FONT COLOR="#A020F0">if</FONT></B> len(disjuncts) == 1:
        <B><FONT COLOR="#A020F0">return</FONT></B> disjuncts[0]
    <B><FONT COLOR="#A020F0">elif</FONT></B> EMPTY_TREE <B><FONT COLOR="#A020F0">in</FONT></B> disjuncts:
        <B><FONT COLOR="#A020F0">return</FONT></B> quantifyTree(makeDisjunctionTree(filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> expr:expr != EMPTY_TREE, disjuncts)), <FONT COLOR="#BC8F8F"><B>'?'</FONT></B>)
    <B><FONT COLOR="#A020F0">else</FONT></B>:
        <B><FONT COLOR="#A020F0">return</FONT></B> makeNode(DISJUNCTION, disjuncts)

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">makeConcatenationTree</FONT></B>(items):
    <B><FONT COLOR="#A020F0">if</FONT></B> len(items) == 0:
        <B><FONT COLOR="#A020F0">return</FONT></B> EMPTY_TREE
    <B><FONT COLOR="#A020F0">elif</FONT></B> len(items) == 1:
        <B><FONT COLOR="#A020F0">return</FONT></B> items[0]
    <B><FONT COLOR="#A020F0">else</FONT></B>:
        <B><FONT COLOR="#A020F0">return</FONT></B> makeNode(CONCATENATION, items)

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">concatenateTrees</FONT></B>(a, b):
    <B><FONT COLOR="#A020F0">if</FONT></B> a == EMPTY_TREE:
        left = []
    <B><FONT COLOR="#A020F0">elif</FONT></B> a[0] == CONCATENATION:
        left = a[1]
    <B><FONT COLOR="#A020F0">else</FONT></B>:
        left = [a]
    <B><FONT COLOR="#A020F0">if</FONT></B> b == EMPTY_TREE:
        right = []
    <B><FONT COLOR="#A020F0">elif</FONT></B> b[0] == CONCATENATION:
        right = b[1]
    <B><FONT COLOR="#A020F0">else</FONT></B>:
        right = [b]
    children = left + right
    <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> children:
        <B><FONT COLOR="#A020F0">return</FONT></B> EMPTY_TREE
    <B><FONT COLOR="#A020F0">elif</FONT></B> len(children) == 1:
        <B><FONT COLOR="#A020F0">return</FONT></B> children[0]
    <B><FONT COLOR="#A020F0">else</FONT></B>:
        <B><FONT COLOR="#A020F0">return</FONT></B> makeNode(CONCATENATION, children)

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">quantifyTree</FONT></B>(node, quantifier=<FONT COLOR="#BC8F8F"><B>'*'</FONT></B>):
    <B><FONT COLOR="#A020F0">if</FONT></B> node == EMPTY_TREE:
        <B><FONT COLOR="#A020F0">return</FONT></B> node
    <B><FONT COLOR="#A020F0">else</FONT></B>:
        <B><FONT COLOR="#A020F0">return</FONT></B> makeNode(quantifier, node)


<I><FONT COLOR="#B22222">#
</FONT></I><I><FONT COLOR="#B22222"># Rendering trees to strings
</FONT></I><I><FONT COLOR="#B22222">#
</FONT></I>
<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">treeToString</FONT></B>(node, caller=None, wrap=None, sep=None):
    <B><FONT COLOR="#A020F0">import</FONT></B> string
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">toString</FONT></B>(node, caller=node[0], wrap=wrap, sep=sep):
        <B><FONT COLOR="#A020F0">return</FONT></B> treeToString(node, caller=caller, wrap=wrap, sep=sep)

    <B><FONT COLOR="#A020F0">if</FONT></B> node == EMPTY_TREE:
        <B><FONT COLOR="#A020F0">return</FONT></B> <FONT COLOR="#BC8F8F"><B>''</FONT></B>
    <B><FONT COLOR="#A020F0">elif</FONT></B> node == DOT_TREE:
        <B><FONT COLOR="#A020F0">return</FONT></B> <FONT COLOR="#BC8F8F"><B>'.'</FONT></B>
    
    op, args = node[0], node[1]
    <B><FONT COLOR="#A020F0">if</FONT></B> op == LEAF:
        <B><FONT COLOR="#A020F0">if</FONT></B> args == FSA.ANY:
            <B><FONT COLOR="#A020F0">return</FONT></B> <FONT COLOR="#BC8F8F"><B>'.'</FONT></B>
        <B><FONT COLOR="#A020F0">else</FONT></B>:
            s = str(args)
            <B><FONT COLOR="#A020F0">if</FONT></B> (<FONT COLOR="#BC8F8F"><B>'|'</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> s <B><FONT COLOR="#A020F0">or</FONT></B> <FONT COLOR="#BC8F8F"><B>'&amp;'</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> s) <B><FONT COLOR="#A020F0">and</FONT></B> (s[0] != <FONT COLOR="#BC8F8F"><B>'('</FONT></B> <B><FONT COLOR="#A020F0">or</FONT></B> s[-1] != <FONT COLOR="#BC8F8F"><B>')'</FONT></B>):
                s = <FONT COLOR="#BC8F8F"><B>'('</FONT></B> + s + <FONT COLOR="#BC8F8F"><B>')'</FONT></B>
            <B><FONT COLOR="#A020F0">return</FONT></B> s
    <B><FONT COLOR="#A020F0">elif</FONT></B> op == CONCATENATION:
        s = string.join(map(toString, args), sep)
        <B><FONT COLOR="#A020F0">if</FONT></B> caller <B><FONT COLOR="#A020F0">in</FONT></B> QUANTIFIERS:
            s = <FONT COLOR="#BC8F8F"><B>'('</FONT></B> + s + <FONT COLOR="#BC8F8F"><B>')'</FONT></B>
        <B><FONT COLOR="#A020F0">return</FONT></B> s
    <B><FONT COLOR="#A020F0">elif</FONT></B> op == DISJUNCTION:
        bar = <FONT COLOR="#BC8F8F"><B>'|'</FONT></B>
        <I><FONT COLOR="#B22222">#if wrap and len(s) &gt; wrap:
</FONT></I>        <I><FONT COLOR="#B22222">#   bar = '|\n  '
</FONT></I>        s = string.join(map(<B><FONT COLOR="#A020F0">lambda</FONT></B> arg, f=toString:f(arg, wrap=None), args), bar)
        <B><FONT COLOR="#A020F0">if</FONT></B> caller == CONCATENATION <B><FONT COLOR="#A020F0">or</FONT></B> caller <B><FONT COLOR="#A020F0">in</FONT></B> QUANTIFIERS:
            s = <FONT COLOR="#BC8F8F"><B>'('</FONT></B> + s + <FONT COLOR="#BC8F8F"><B>')'</FONT></B>
        <B><FONT COLOR="#A020F0">return</FONT></B> s
    <B><FONT COLOR="#A020F0">elif</FONT></B> op <B><FONT COLOR="#A020F0">in</FONT></B> QUANTIFIERS:
        <B><FONT COLOR="#A020F0">return</FONT></B> toString(args) + op
    <B><FONT COLOR="#A020F0">else</FONT></B>:
        <B><FONT COLOR="#A020F0">raise</FONT></B> <FONT COLOR="#BC8F8F"><B>'unknown operator:'</FONT></B>, op


<I><FONT COLOR="#B22222">#
</FONT></I><I><FONT COLOR="#B22222"># RE comparison
</FONT></I><I><FONT COLOR="#B22222">#
</FONT></I>
<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">simplify</FONT></B>(str):
    <B><FONT COLOR="#A020F0">return</FONT></B> decompileFSA(compileRE(str).minimized())

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">_testSimplify</FONT></B>():
    cases = [<FONT COLOR="#BC8F8F"><B>'a'</FONT></B>,
             <FONT COLOR="#BC8F8F"><B>'a*'</FONT></B>,
             <FONT COLOR="#BC8F8F"><B>'a?'</FONT></B>,
             <FONT COLOR="#BC8F8F"><B>'ab'</FONT></B>,
             <FONT COLOR="#BC8F8F"><B>'a?b'</FONT></B>,
             <FONT COLOR="#BC8F8F"><B>'ab?'</FONT></B>,
             <FONT COLOR="#BC8F8F"><B>'a*b'</FONT></B>,
             <FONT COLOR="#BC8F8F"><B>'ab*'</FONT></B>,
             (<FONT COLOR="#BC8F8F"><B>'abc'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'ab|c'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'ab|ac'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'a[bc]'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'ab|cb'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'[ac]b'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'ab|c?'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'(ab)*'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'(ab)*c'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'(ab)*(cd)'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'(ab)*cd),
             ('</FONT></B>(ab)*(cd)*<FONT COLOR="#BC8F8F"><B>'),
             ('</FONT></B>(ab)?cd<FONT COLOR="#BC8F8F"><B>'),
             ('</FONT></B>ab(cd)?<FONT COLOR="#BC8F8F"><B>'),
             ('</FONT></B>(ab)*c<FONT COLOR="#BC8F8F"><B>'),
             ('</FONT></B>abc|acb|bac|bca|cab|cba<FONT COLOR="#BC8F8F"><B>', '</FONT></B>c(ab|ba)|a(cb|bc)|b(ac|ca)<FONT COLOR="#BC8F8F"><B>'),
             ('</FONT></B>[ab]c|[bc]a|[ca]b<FONT COLOR="#BC8F8F"><B>'),
             ('</FONT></B>(a*b)*<FONT COLOR="#BC8F8F"><B>'),
             ('</FONT></B>(a|b)*<FONT COLOR="#BC8F8F"><B>', '</FONT></B>[ab]*<FONT COLOR="#BC8F8F"><B>'),
             ]
    failures = 0
    for case in cases:
        if type(case) == TupleType:
            if len(case) == 1:
                case += case
        else:
            case = (case, case)
        input, expect = case
        receive = simplify(input)
        if expect != receive:
            print '</FONT></B>expected %s -&gt; %s; received %s<FONT COLOR="#BC8F8F"><B>' % (input, expect, receive)
            failures += 1
    print &quot;%s failure%s&quot; % (failures or '</FONT></B>no<FONT COLOR="#BC8F8F"><B>', '</FONT></B>s<FONT COLOR="#BC8F8F"><B>'[failures==1:])

def compareREs(*exprs):
    import string
    fsas = map(compileRE, exprs)
    def setName(set):
        set = map(lambda x:x.label, set)
        if len(set) == 1:
            return set[0]
        else:
            import string
            return string.join(set[:-1], '</FONT></B>, <FONT COLOR="#BC8F8F"><B>') + '</FONT></B> <B><FONT COLOR="#A020F0">and</FONT></B> <FONT COLOR="#BC8F8F"><B>' + set[-1]
    print '</FONT></B>Comparing<FONT COLOR="#BC8F8F"><B>', setName(fsas)
    processed = []
    sets = []
    for fsa in fsas:
        if fsa in processed:
            continue
        set = [fsa]
        for other in fsas:
            if other != fsa and other not in processed and FSA.equivalent(fsa, other):
                set.append(other)
        sets.append(set)
        processed.extend(set)
    for set in sets:
        if len(set) &gt; 1:
            print setName(set), '</FONT></B>are equivalent<FONT COLOR="#BC8F8F"><B>'
    if len(sets) &gt; 1:
        for set in sets:
            others = filter(lambda a,b=set:a not in b, fsas)
            only = FSA.difference(set[0], reduce(FSA.union, others))
            if not only.isEmpty():
                es = (len(set) == 1 and '</FONT></B>es<FONT COLOR="#BC8F8F"><B>') or '</FONT></B><FONT COLOR="#BC8F8F"><B>'
                print '</FONT></B>Only<FONT COLOR="#BC8F8F"><B>', setName(set), '</FONT></B>match<FONT COLOR="#BC8F8F"><B>'+es, decompileFSA(only)

&quot;&quot;&quot;
compareREs('</FONT></B>a*b<FONT COLOR="#BC8F8F"><B>', '</FONT></B>b*a<FONT COLOR="#BC8F8F"><B>', '</FONT></B>b|a*b<FONT COLOR="#BC8F8F"><B>')
compareREs('</FONT></B>a*b<FONT COLOR="#BC8F8F"><B>', '</FONT></B>ab*<FONT COLOR="#BC8F8F"><B>')
compareREs('</FONT></B>a*b<FONT COLOR="#BC8F8F"><B>', '</FONT></B>b|a*b<FONT COLOR="#BC8F8F"><B>')
compareREs('</FONT></B>ab*<FONT COLOR="#BC8F8F"><B>', '</FONT></B>b|a*b<FONT COLOR="#BC8F8F"><B>')

print decompileFSA(compileRE('</FONT></B>ab|ac<FONT COLOR="#BC8F8F"><B>'), [0])
print decompileFSA(compileRE('</FONT></B>ab|ac<FONT COLOR="#BC8F8F"><B>', 0), [0])
print decompileFSA(compileRE('</FONT></B>abc<FONT COLOR="#BC8F8F"><B>'), [1])
print decompileFSA(compileRE('</FONT></B>abc<FONT COLOR="#BC8F8F"><B>'), [1, 2])
print decompileFSA(compileRE('</FONT></B>abc<FONT COLOR="#BC8F8F"><B>'), [0, 1, 2])
print decompileFSA(compileRE('</FONT></B>abc<FONT COLOR="#BC8F8F"><B>'), [0, 1, 2, 3])
print decompileFSA(FSA.compileRE('</FONT></B>a bb* c<FONT COLOR="#BC8F8F"><B>', multichar=1))
print decompileFSA(FSA.compileRE('</FONT></B>(a bb)* c<FONT COLOR="#BC8F8F"><B>', multichar=1))
print decompileFSA(FSA.complement(FSA.containment(FSA.singleton('</FONT></B>a<FONT COLOR="#BC8F8F"><B>'), 3)))

# lift FSA operators to string operators:
print complement('</FONT></B>ab*<FONT COLOR="#BC8F8F"><B>')
print difference('</FONT></B>a*b<FONT COLOR="#BC8F8F"><B>', '</FONT></B>ab*<FONT COLOR="#BC8F8F"><B>')
&quot;&quot;&quot;

def _test(reset=0):
    import doctest, decompileFSA
    if reset:
        doctest.master = None # This keeps doctest from complaining after a reload.
    return doctest.testmod(decompileFSA)
</FONT></B></PRE>
<HR>
<ADDRESS>Generated by <A HREF="http://www.iki.fi/~mtr/genscript/">GNU enscript 1.6.1</A>.</ADDRESS>
</BODY>
</HTML>
