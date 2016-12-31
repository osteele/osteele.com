<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
<HEAD>
<TITLE>reTools.py</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#1F00FF" ALINK="#FF0000" VLINK="#9900DD">
<A NAME="top">
<A NAME="file1">
<H1>PyFSA/reTools.py</H1>

<PRE>
__author__  = <FONT COLOR="#BC8F8F"><B>&quot;Oliver Steele &lt;steele@cs.brandeis.edu&gt;&quot;</FONT></B>

<I><FONT COLOR="#B22222">#
</FONT></I><I><FONT COLOR="#B22222"># Simplification
</FONT></I><I><FONT COLOR="#B22222">#
</FONT></I>
<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">simplify</FONT></B>(str):
    <I><FONT COLOR="#B22222"># replace() is workaround for bug in simplify
</FONT></I>    <B><FONT COLOR="#A020F0">return</FONT></B> decompileFSA(compileRE(str).minimized()).replace(<FONT COLOR="#BC8F8F"><B>'?*'</FONT></B>,<FONT COLOR="#BC8F8F"><B>'*'</FONT></B>)

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
             (<FONT COLOR="#BC8F8F"><B>'(ab)*(cd)'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'(ab)*cd)'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'(ab)*(cd)*'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'(ab)?cd'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'ab(cd)?'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'(ab)*c'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'abc|acb|bac|bca|cab|cba'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'c(ab|ba)|a(cb|bc)|b(ac|ca)'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'[ab]c|[bc]a|[ca]b'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'(a*b)*'</FONT></B>),
             (<FONT COLOR="#BC8F8F"><B>'(a|b)*'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'[ab]*'</FONT></B>),
             ]
    failures = 0
    <B><FONT COLOR="#A020F0">for</FONT></B> case <B><FONT COLOR="#A020F0">in</FONT></B> cases:
        <B><FONT COLOR="#A020F0">if</FONT></B> type(case) == TupleType:
            <B><FONT COLOR="#A020F0">if</FONT></B> len(case) == 1:
                case += case
        <B><FONT COLOR="#A020F0">else</FONT></B>:
            case = (case, case)
        input, expect = case
        receive = simplify(input)
        <B><FONT COLOR="#A020F0">if</FONT></B> expect != receive:
            <B><FONT COLOR="#A020F0">print</FONT></B> <FONT COLOR="#BC8F8F"><B>'expected %s -&gt; %s; received %s'</FONT></B> % (input, expect, receive)
            failures += 1
    <B><FONT COLOR="#A020F0">print</FONT></B> <FONT COLOR="#BC8F8F"><B>&quot;%s failure%s&quot;</FONT></B> % (failures <B><FONT COLOR="#A020F0">or</FONT></B> <FONT COLOR="#BC8F8F"><B>'no'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'s'</FONT></B>[failures==1:])


<I><FONT COLOR="#B22222">#
</FONT></I><I><FONT COLOR="#B22222"># Comparison
</FONT></I><I><FONT COLOR="#B22222">#
</FONT></I>
<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">compareREs</FONT></B>(*exprs):
    <B><FONT COLOR="#A020F0">import</FONT></B> string
    fsas = map(compileRE, exprs)
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">setName</FONT></B>(set):
        set = map(<B><FONT COLOR="#A020F0">lambda</FONT></B> x:x.label, set)
        <B><FONT COLOR="#A020F0">if</FONT></B> len(set) == 1:
            <B><FONT COLOR="#A020F0">return</FONT></B> set[0]
        <B><FONT COLOR="#A020F0">else</FONT></B>:
            <B><FONT COLOR="#A020F0">import</FONT></B> string
            <B><FONT COLOR="#A020F0">return</FONT></B> string.join(set[:-1], <FONT COLOR="#BC8F8F"><B>', '</FONT></B>) + <FONT COLOR="#BC8F8F"><B>' and '</FONT></B> + set[-1]
    <B><FONT COLOR="#A020F0">print</FONT></B> <FONT COLOR="#BC8F8F"><B>'Comparing'</FONT></B>, setName(fsas)
    processed = []
    sets = []
    <B><FONT COLOR="#A020F0">for</FONT></B> fsa <B><FONT COLOR="#A020F0">in</FONT></B> fsas:
        <B><FONT COLOR="#A020F0">if</FONT></B> fsa <B><FONT COLOR="#A020F0">in</FONT></B> processed:
            <B><FONT COLOR="#A020F0">continue</FONT></B>
        set = [fsa]
        <B><FONT COLOR="#A020F0">for</FONT></B> other <B><FONT COLOR="#A020F0">in</FONT></B> fsas:
            <B><FONT COLOR="#A020F0">if</FONT></B> other != fsa <B><FONT COLOR="#A020F0">and</FONT></B> other <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> processed <B><FONT COLOR="#A020F0">and</FONT></B> FSA.equivalent(fsa, other):
                set.append(other)
        sets.append(set)
        processed.extend(set)
    <B><FONT COLOR="#A020F0">for</FONT></B> set <B><FONT COLOR="#A020F0">in</FONT></B> sets:
        <B><FONT COLOR="#A020F0">if</FONT></B> len(set) &gt; 1:
            <B><FONT COLOR="#A020F0">print</FONT></B> setName(set), <FONT COLOR="#BC8F8F"><B>'are equivalent'</FONT></B>
    <B><FONT COLOR="#A020F0">if</FONT></B> len(sets) &gt; 1:
        <B><FONT COLOR="#A020F0">for</FONT></B> set <B><FONT COLOR="#A020F0">in</FONT></B> sets:
            others = filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> a,b=set:a <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> b, fsas)
            only = FSA.difference(set[0], reduce(FSA.union, others))
            <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> only.isEmpty():
                es = (len(set) == 1 <B><FONT COLOR="#A020F0">and</FONT></B> <FONT COLOR="#BC8F8F"><B>'es'</FONT></B>) <B><FONT COLOR="#A020F0">or</FONT></B> <FONT COLOR="#BC8F8F"><B>''</FONT></B>
                <B><FONT COLOR="#A020F0">print</FONT></B> <FONT COLOR="#BC8F8F"><B>'Only'</FONT></B>, setName(set), <FONT COLOR="#BC8F8F"><B>'match'</FONT></B>+es, decompileFSA(only)

<FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;
compareREs('a*b', 'b*a', 'b|a*b')
compareREs('a*b', 'ab*')
compareREs('a*b', 'b|a*b')
compareREs('ab*', 'b|a*b')

# todo: lift FSA operators to string operators:
print complement('ab*')
print difference('a*b', 'ab*')
&quot;&quot;&quot;</FONT></B>

<I><FONT COLOR="#B22222">#
</FONT></I><I><FONT COLOR="#B22222"># Tracing
</FONT></I><I><FONT COLOR="#B22222">#
</FONT></I>
<FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;
To do:
Bug fixes:
    - in tracing, ab*\&amp;a*b at final state only prints the period at the end
    - trace doesn't work with nondeterminized automata

Functions for web page:
1) Show how far we got (the last state)
2) Give a list of n steps

Later:
3) Simplify, add algebra
&quot;&quot;&quot;</FONT></B>

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">collectSourcePositions</FONT></B>(fsa, states):
    positions = []
    <B><FONT COLOR="#A020F0">for</FONT></B> state <B><FONT COLOR="#A020F0">in</FONT></B> states:
        <B><FONT COLOR="#A020F0">for</FONT></B> transition <B><FONT COLOR="#A020F0">in</FONT></B> fsa.transitionsFrom(state):
            <B><FONT COLOR="#A020F0">for</FONT></B> position <B><FONT COLOR="#A020F0">in</FONT></B> fsa.getArcMetadataFor(transition, []):
                <B><FONT COLOR="#A020F0">if</FONT></B> position <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> positions:
                    positions.append(position)
    <B><FONT COLOR="#A020F0">return</FONT></B> positions

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">fsaLabelWithCursor</FONT></B>(fsa, states):
    <FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;Return the FSA's label, with a cursor ('.') inserted at
    each position in states.&quot;&quot;&quot;</FONT></B>
    s = <FONT COLOR="#BC8F8F"><B>''</FONT></B>
    positions = collectSourcePositions(fsa, states)
    <B><FONT COLOR="#A020F0">for</FONT></B> index <B><FONT COLOR="#A020F0">in</FONT></B> range(len(fsa.label)):
        <B><FONT COLOR="#A020F0">if</FONT></B> index + 1 <B><FONT COLOR="#A020F0">in</FONT></B> positions:
            s = s + <FONT COLOR="#BC8F8F"><B>'.'</FONT></B>
        s = s + fsa.label[index]
    <B><FONT COLOR="#A020F0">if</FONT></B> filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> s, finals=fsa.finalStates: s <B><FONT COLOR="#A020F0">in</FONT></B> finals, states):
        s = s +  <FONT COLOR="#BC8F8F"><B>'.'</FONT></B>
    <B><FONT COLOR="#A020F0">return</FONT></B> s

<B><FONT COLOR="#A020F0">from</FONT></B> compileRE <B><FONT COLOR="#A020F0">import</FONT></B> compileRE

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">traceREStates</FONT></B>(re, str, trace=1):
    fsa = compileRE(re, recordSourcePositions=1)
    states = fsa.epsilonClosure(fsa.initialState)
    <B><FONT COLOR="#A020F0">for</FONT></B> i <B><FONT COLOR="#A020F0">in</FONT></B> range(len(str)):
        newStates = fsa.nextStateSet(states, str[i])
        <B><FONT COLOR="#A020F0">if</FONT></B> newStates:
            <B><FONT COLOR="#A020F0">if</FONT></B> trace:
                <B><FONT COLOR="#A020F0">print</FONT></B> fsaLabelWithCursor(fsa, newStates), <FONT COLOR="#BC8F8F"><B>'matches'</FONT></B>, str[:i+1] + <FONT COLOR="#BC8F8F"><B>'.'</FONT></B> + str[i+1:]
            states = newStates
        <B><FONT COLOR="#A020F0">else</FONT></B>:
            c = CharacterSet([])
            <B><FONT COLOR="#A020F0">for</FONT></B> s0 <B><FONT COLOR="#A020F0">in</FONT></B> states:
                <B><FONT COLOR="#A020F0">for</FONT></B> _, _, label <B><FONT COLOR="#A020F0">in</FONT></B> fsa.transitionsFrom(s0):
                    <B><FONT COLOR="#A020F0">if</FONT></B> label:
                        c = c.union(label)
            <B><FONT COLOR="#A020F0">print</FONT></B> fsaLabelWithCursor(fsa, states), <FONT COLOR="#BC8F8F"><B>'stops matching at'</FONT></B>, str[:i] + <FONT COLOR="#BC8F8F"><B>'.'</FONT></B> + str[i:], <FONT COLOR="#BC8F8F"><B>'; expected'</FONT></B>, c
            <B><FONT COLOR="#A020F0">break</FONT></B>

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">updatePositions</FONT></B>(fsa, states, input, positions):
    successors = []
    <I><FONT COLOR="#B22222">#print 'counting from', states, 'over', label, 'in', fsa.label
</FONT></I>    <B><FONT COLOR="#A020F0">for</FONT></B> state <B><FONT COLOR="#A020F0">in</FONT></B> states:
        <B><FONT COLOR="#A020F0">for</FONT></B> transition <B><FONT COLOR="#A020F0">in</FONT></B> fsa.transitionsFrom(state):
            _, sink, label = transition
            <B><FONT COLOR="#A020F0">if</FONT></B> fsa.labelMatches(label, input) <B><FONT COLOR="#A020F0">and</FONT></B> sink <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> successors:
                successors.extend(fsa.epsilonClosure(sink))
                <I><FONT COLOR="#B22222">#todo: share with collectSourcePositions
</FONT></I>                data = fsa.getArcMetadataFor(transition, [])
                <B><FONT COLOR="#A020F0">for</FONT></B> position <B><FONT COLOR="#A020F0">in</FONT></B> data:
                    <B><FONT COLOR="#A020F0">if</FONT></B> position <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> positions:
                        positions.append(position)
    <B><FONT COLOR="#A020F0">return</FONT></B> successors

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">reMatchStatePairs</FONT></B>(re, str):
    <FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;Return a list of (re, str), where re is a the regular
    expression with &lt;SPAN&gt;s inserted over all the matched characters,
    and str is the string with &lt;SPAN&gt;s likewise inserted.&quot;&quot;&quot;</FONT></B>
    <I><FONT COLOR="#B22222">#print re, '~=', str
</FONT></I>    pairs = []
    fsa = compileRE(re, recordSourcePositions=1)
    states = fsa.epsilonClosure(fsa.initialState)
    positions = [] <I><FONT COLOR="#B22222">#todo: everything that starts here?
</FONT></I>    <B><FONT COLOR="#A020F0">for</FONT></B> i <B><FONT COLOR="#A020F0">in</FONT></B> range(len(str)):
        <B><FONT COLOR="#A020F0">if</FONT></B> i &lt; len(str):
            <I><FONT COLOR="#B22222">#print states, '-&gt;', newStates, '(', str[i], ')'
</FONT></I>            <I><FONT COLOR="#B22222">#todo: factor the following block with fsa.nextStateSet
</FONT></I>            newPositions = []
            newStates = updatePositions(fsa, states, str[i], newPositions)
            <I><FONT COLOR="#B22222">#assert newStates == fsa.nextStateSet(states, str[i])
</FONT></I>        <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> newStates:
            <I><FONT COLOR="#B22222"># we ran out of matches
</FONT></I>            <I><FONT COLOR="#B22222"># todo: show in red where the match stopped, as in the textual version
</FONT></I>            expected = None
            <B><FONT COLOR="#A020F0">for</FONT></B> state <B><FONT COLOR="#A020F0">in</FONT></B> states:
                <B><FONT COLOR="#A020F0">for</FONT></B> t <B><FONT COLOR="#A020F0">in</FONT></B> fsa.transitionsFrom(state):
                    label = t[2]
                    <B><FONT COLOR="#A020F0">if</FONT></B> expected:
                        expected = expected + label
                    <B><FONT COLOR="#A020F0">else</FONT></B>:
                        expected = label
            <B><FONT COLOR="#A020F0">return</FONT></B> pairs, <FONT COLOR="#BC8F8F"><B>'expected %s'</FONT></B> % expected
        srcLabel = fsa.label
        <I><FONT COLOR="#B22222"># todo: could color newly matched states in a different color
</FONT></I>        <I><FONT COLOR="#B22222">#todo: quote the html stuff
</FONT></I>        rem = <FONT COLOR="#BC8F8F"><B>''</FONT></B>
        <I><FONT COLOR="#B22222">#print srcLabel, allStates, positions
</FONT></I>        <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">htmlQuote</FONT></B>(str):
            <B><FONT COLOR="#A020F0">return</FONT></B> <FONT COLOR="#BC8F8F"><B>''</FONT></B>.join([{<FONT COLOR="#BC8F8F"><B>'&lt;'</FONT></B>: <FONT COLOR="#BC8F8F"><B>'&amp;lt;'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'&gt;'</FONT></B>: <FONT COLOR="#BC8F8F"><B>'&amp;gt;'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'&amp;'</FONT></B>: <FONT COLOR="#BC8F8F"><B>'&amp;amp;'</FONT></B>}.get(c, c) <B><FONT COLOR="#A020F0">for</FONT></B> c <B><FONT COLOR="#A020F0">in</FONT></B> str])
        <B><FONT COLOR="#A020F0">for</FONT></B> j <B><FONT COLOR="#A020F0">in</FONT></B> range(len(srcLabel)):
            c = htmlQuote(srcLabel[j])
            <B><FONT COLOR="#A020F0">if</FONT></B> j+1 <B><FONT COLOR="#A020F0">in</FONT></B> newPositions:
                rem += <FONT COLOR="#BC8F8F"><B>'&lt;SPAN CLASS=&quot;rematchnew&quot;&gt;%s&lt;/SPAN&gt;'</FONT></B> % c
                <I><FONT COLOR="#B22222">#positions.append(j)
</FONT></I>            <B><FONT COLOR="#A020F0">elif</FONT></B> j+1 <B><FONT COLOR="#A020F0">in</FONT></B> positions:
                rem += <FONT COLOR="#BC8F8F"><B>'&lt;SPAN CLASS=&quot;rematch&quot;&gt;%s&lt;/SPAN&gt;'</FONT></B> % c
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                rem += c
        s0, s1, s2 = htmlQuote(str[:i+1]), <FONT COLOR="#BC8F8F"><B>''</FONT></B>, htmlQuote(str[i+1:])
        strm = <FONT COLOR="#BC8F8F"><B>'&lt;SPAN CLASS=&quot;strmatch&quot;&gt;%s&lt;/SPAN&gt;&lt;SPAN CLASS=&quot;strmatchnew&quot;&gt;%s&lt;/SPAN&gt;%s'</FONT></B> % (s0, s1, s2)
        comment = <FONT COLOR="#BC8F8F"><B>&quot;states: %s -&gt; %s; positions: %s -&gt; %s; index = %d&quot;</FONT></B> % (states,newStates,positions,newPositions,i)
        pairs.append((rem, strm, comment))
        states = newStates
        positions += newPositions
    <B><FONT COLOR="#A020F0">return</FONT></B> pairs, [s <B><FONT COLOR="#A020F0">for</FONT></B> s <B><FONT COLOR="#A020F0">in</FONT></B> states <B><FONT COLOR="#A020F0">if</FONT></B> s <B><FONT COLOR="#A020F0">in</FONT></B> fsa.finalStates]

_CASES = [(<FONT COLOR="#BC8F8F"><B>'abc'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'abc'</FONT></B>),
          (<FONT COLOR="#BC8F8F"><B>'abc'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'ab'</FONT></B>),
          (<FONT COLOR="#BC8F8F"><B>'ab'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'abc'</FONT></B>),
          (<FONT COLOR="#BC8F8F"><B>'ab|ac'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'ab'</FONT></B>),
          (<FONT COLOR="#BC8F8F"><B>'(a*b)*'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'aaabaabab'</FONT></B>),
          (<FONT COLOR="#BC8F8F"><B>'abc'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'ac'</FONT></B>)]

<I><FONT COLOR="#B22222"># attributes at http://www.echoecho.com/csslinks.htm
</FONT></I>STYLE = <FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;&lt;STYLE TYPE=&quot;text/css&quot; MEDIA=&quot;screen&quot; TITLE=&quot;Special paragraph colour&quot;&gt;
&lt;!--
SPAN.rematch {text-decoration: underline;}
SPAN.rematchnew {color: green; style: bold; text-face: bold; font-style: bold; background: red}
SPAN.strmatch {text-decoration: underline; background: yellow}
SPAN.strmatchnew {text-decoration: underline;}
--&gt;
&lt;/STYLE&gt;&quot;&quot;&quot;</FONT></B>

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">makeHtml</FONT></B>():
    <B><FONT COLOR="#A020F0">import</FONT></B> os
    f = open(<FONT COLOR="#BC8F8F"><B>'match.html'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'w'</FONT></B>)
    <I><FONT COLOR="#B22222">#f = open(os.path.join(os.path.split(__file__)[0], 'foo.html'), 'w')
</FONT></I>    <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;HTML&gt;&lt;HEAD&gt;'</FONT></B>
    <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, STYLE
    <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;/HEAD&gt;&lt;BODY&gt;'</FONT></B>
    <B><FONT COLOR="#A020F0">for</FONT></B> re, str <B><FONT COLOR="#A020F0">in</FONT></B> _CASES:
        pairs, success = reMatchStatePairs(re, str)
        op = success <B><FONT COLOR="#A020F0">and</FONT></B> <FONT COLOR="#BC8F8F"><B>'=~'</FONT></B> <B><FONT COLOR="#A020F0">or</FONT></B> <FONT COLOR="#BC8F8F"><B>'!~'</FONT></B>
        <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;H1&gt;%r %s /%s/&lt;/H1&gt;'</FONT></B> % (str, op, re)
        <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;TABLE&gt;'</FONT></B>
        <B><FONT COLOR="#A020F0">for</FONT></B> r, s, note <B><FONT COLOR="#A020F0">in</FONT></B> pairs:
            <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;TR&gt;&lt;TD&gt;%s'</FONT></B> % r
            <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;TD&gt;=~'</FONT></B>
            <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;TD&gt;%s'</FONT></B> % s
            <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;TD&gt;%s'</FONT></B> % note
        <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> success:
            <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;TR&gt;&lt;TD COLSPAN=2&gt;Failure'</FONT></B>
        <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;/TABLE&gt;'</FONT></B>
    <B><FONT COLOR="#A020F0">print</FONT></B> &gt;&gt; f, <FONT COLOR="#BC8F8F"><B>'&lt;/BODY&gt;&lt;/HTML&gt;'</FONT></B>

<I><FONT COLOR="#B22222">#print reMatchStatePairs('abc', 'abc')
</FONT></I>
makeHtml()

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">_test</FONT></B>(reset=0):
    <B><FONT COLOR="#A020F0">import</FONT></B> doctest, reTools
    <B><FONT COLOR="#A020F0">if</FONT></B> reset:
        doctest.master = None <I><FONT COLOR="#B22222"># This keeps doctest from complaining after a reload.
</FONT></I>    <B><FONT COLOR="#A020F0">return</FONT></B> doctest.testmod(reTools)
</PRE>
<HR>
<ADDRESS>Generated by <A HREF="http://www.iki.fi/~mtr/genscript/">GNU enscript 1.6.1</A>.</ADDRESS>
</BODY>
</HTML>
