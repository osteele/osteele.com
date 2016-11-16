<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
<HEAD>
<TITLE>reCompiler.py</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#1F00FF" ALINK="#FF0000" VLINK="#9900DD">
<A NAME="top">
<A NAME="file1">
<H1>PyFSA/reCompiler.py</H1>

<PRE>
<FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot; Module re_compile -- compile a regular expression into an FSA

To Do
-----
New features:
    - add \-, \~
    - add remaining metachars
    - char set with ^ as first char will print wrong
    - figure out when to print spaces between operators
&quot;&quot;&quot;</FONT></B>

__author__  = <FONT COLOR="#BC8F8F"><B>&quot;Oliver Steele &lt;steele@osteele.com&gt;&quot;</FONT></B>

<B><FONT COLOR="#A020F0">import</FONT></B> FSA
<B><FONT COLOR="#A020F0">from</FONT></B> types <B><FONT COLOR="#A020F0">import</FONT></B> TupleType

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">compileSymbolRE</FONT></B>(str):
    <B><FONT COLOR="#A020F0">return</FONT></B> SymbolRECompiler(str).toFSA()
    
<B><FONT COLOR="#A020F0">class</FONT></B> SymbolRECompiler:
    EOF = -1
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">__init__</FONT></B>(self, str, recordSourcePositions=0):
        self.str = str
        self.recordSourcePositions = recordSourcePositions
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">toFSA</FONT></B>(self, minimize=1):
        self.index = 0
        self.nextToken = None
        fsa = self.compileExpr()
        <B><FONT COLOR="#A020F0">if</FONT></B> self.index &lt; len(self.str):
            <B><FONT COLOR="#A020F0">raise</FONT></B> <FONT COLOR="#BC8F8F"><B>'extra '</FONT></B> + `<FONT COLOR="#BC8F8F"><B>')'</FONT></B>`
        del self.index
        fsa.label = self.str
        <B><FONT COLOR="#A020F0">if</FONT></B> minimize:
            fsa = fsa.minimized()
        <B><FONT COLOR="#A020F0">return</FONT></B> fsa
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">readChar</FONT></B>(self):
        <B><FONT COLOR="#A020F0">if</FONT></B> self.index &lt; len(self.str):
            c, self.index = self.str[self.index], self.index + 1
            <B><FONT COLOR="#A020F0">return</FONT></B> c
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">peekChar</FONT></B>(self):
        <B><FONT COLOR="#A020F0">if</FONT></B> self.index &lt; len(self.str):
            <B><FONT COLOR="#A020F0">return</FONT></B> self.str[self.index]
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">readToken</FONT></B>(self):
        token = self.nextToken <B><FONT COLOR="#A020F0">or</FONT></B> self._readNextToken()
        self.nextToken = None
        <B><FONT COLOR="#A020F0">return</FONT></B> token != self.EOF <B><FONT COLOR="#A020F0">and</FONT></B> token
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">peekToken</FONT></B>(self):
        token = self.nextToken = self.nextToken <B><FONT COLOR="#A020F0">or</FONT></B> self._readNextToken()
        <I><FONT COLOR="#B22222">#print 'peekToken', token
</FONT></I>        <B><FONT COLOR="#A020F0">return</FONT></B> token != self.EOF <B><FONT COLOR="#A020F0">and</FONT></B> token
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">_readNextToken</FONT></B>(self):
        c = self.readChar()
        <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> c:
            <B><FONT COLOR="#A020F0">return</FONT></B> self.EOF
        <B><FONT COLOR="#A020F0">elif</FONT></B> c <B><FONT COLOR="#A020F0">in</FONT></B> <FONT COLOR="#BC8F8F"><B>'()|&amp;'</FONT></B>:
            <B><FONT COLOR="#A020F0">return</FONT></B> c
        <B><FONT COLOR="#A020F0">elif</FONT></B> c == <FONT COLOR="#BC8F8F"><B>'.'</FONT></B>:
            <B><FONT COLOR="#A020F0">return</FONT></B> ANY
        <B><FONT COLOR="#A020F0">return</FONT></B> c
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">skipTokens</FONT></B>(self, bag):
        <B><FONT COLOR="#A020F0">while</FONT></B> self.peekToken() <B><FONT COLOR="#A020F0">and</FONT></B> self.peekToken() <B><FONT COLOR="#A020F0">in</FONT></B> bag:
            self.readToken()
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">compileExpr</FONT></B>(self):
        fsa = FSA.NULL_FSA
        <B><FONT COLOR="#A020F0">while</FONT></B> self.peekToken() <B><FONT COLOR="#A020F0">and</FONT></B> self.peekToken() != <FONT COLOR="#BC8F8F"><B>')'</FONT></B>:
            fsa = FSA.union(fsa, self.compileConjunction())
            self.skipTokens([<FONT COLOR="#BC8F8F"><B>'|'</FONT></B>])
        <B><FONT COLOR="#A020F0">return</FONT></B> fsa
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">compileConjunction</FONT></B>(self):
        fsa = None
        <B><FONT COLOR="#A020F0">while</FONT></B> self.peekToken() <B><FONT COLOR="#A020F0">and</FONT></B> self.peekToken() <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> (<FONT COLOR="#BC8F8F"><B>')'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'|'</FONT></B>):
            sequence = self.compileSequence()
            fsa = fsa <B><FONT COLOR="#A020F0">and</FONT></B> FSA.intersection(fsa, sequence) <B><FONT COLOR="#A020F0">or</FONT></B> sequence
            self.skipTokens([<FONT COLOR="#BC8F8F"><B>'&amp;'</FONT></B>])
        <B><FONT COLOR="#A020F0">return</FONT></B> fsa
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">compileSequence</FONT></B>(self):
        fsa = FSA.EMPTY_STRING_FSA
        <B><FONT COLOR="#A020F0">while</FONT></B> self.peekToken() <B><FONT COLOR="#A020F0">and</FONT></B> self.peekToken() <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> (<FONT COLOR="#BC8F8F"><B>')'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'|'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'&amp;'</FONT></B>):
            fsa = FSA.concatenation(fsa, self.compileItem())
        <B><FONT COLOR="#A020F0">return</FONT></B> fsa
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">compileItem</FONT></B>(self):
        startPosition = self.index
        c = self.readToken()
        <B><FONT COLOR="#A020F0">if</FONT></B> c == <FONT COLOR="#BC8F8F"><B>'('</FONT></B>:
            fsa = self.compileExpr()
            <B><FONT COLOR="#A020F0">if</FONT></B> self.readToken() != <FONT COLOR="#BC8F8F"><B>')'</FONT></B>:
                <B><FONT COLOR="#A020F0">raise</FONT></B> <FONT COLOR="#BC8F8F"><B>&quot;missing ')'&quot;</FONT></B>
        <B><FONT COLOR="#A020F0">elif</FONT></B> c == <FONT COLOR="#BC8F8F"><B>'~'</FONT></B>:
            fsa = FSA.complement(self.compileItem())
        <B><FONT COLOR="#A020F0">else</FONT></B>:
            fsa = FSA.singleton(c, arcMetadata=self.recordSourcePositions <B><FONT COLOR="#A020F0">and</FONT></B> [startPosition])
        <B><FONT COLOR="#A020F0">while</FONT></B> self.peekChar() <B><FONT COLOR="#A020F0">and</FONT></B> self.peekChar() <B><FONT COLOR="#A020F0">in</FONT></B> <FONT COLOR="#BC8F8F"><B>'?*+'</FONT></B>:
            c = self.readChar()
            <B><FONT COLOR="#A020F0">if</FONT></B> c == <FONT COLOR="#BC8F8F"><B>'*'</FONT></B>:
                fsa = FSA.closure(fsa)
            <B><FONT COLOR="#A020F0">elif</FONT></B> c == <FONT COLOR="#BC8F8F"><B>'?'</FONT></B>:
                fsa = FSA.union(fsa, FSA.EMPTY_STRING_FSA)
            <B><FONT COLOR="#A020F0">elif</FONT></B> c == <FONT COLOR="#BC8F8F"><B>'+'</FONT></B>:
                fsa = FSA.iteration(fsa)
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                <B><FONT COLOR="#A020F0">raise</FONT></B> <FONT COLOR="#BC8F8F"><B>'program error'</FONT></B>
        <B><FONT COLOR="#A020F0">return</FONT></B> fsa


<I><FONT COLOR="#B22222">#
</FONT></I><I><FONT COLOR="#B22222"># Character REs
</FONT></I><I><FONT COLOR="#B22222">#
</FONT></I>
<B><FONT COLOR="#A020F0">class</FONT></B> CharacterSet:
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">__init__</FONT></B>(self, ranges):
        <B><FONT COLOR="#A020F0">from</FONT></B> types <B><FONT COLOR="#A020F0">import</FONT></B> StringType
        <B><FONT COLOR="#A020F0">if</FONT></B> type(ranges) == StringType:
            ranges = self.convertString(ranges)
        accum = []
        <I><FONT COLOR="#B22222"># copy, so sort doesn't destroy the arg
</FONT></I>        <B><FONT COLOR="#A020F0">for</FONT></B> item <B><FONT COLOR="#A020F0">in</FONT></B> ranges:
            <B><FONT COLOR="#A020F0">if</FONT></B> type(item) == TupleType:
                <B><FONT COLOR="#A020F0">if</FONT></B> len(item) == 1:
                    accum.append((item, item))
                <B><FONT COLOR="#A020F0">elif</FONT></B> len(item) == 2:
                    accum.append(item)
                <B><FONT COLOR="#A020F0">else</FONT></B>:
                    <B><FONT COLOR="#A020F0">raise</FONT></B> <FONT COLOR="#BC8F8F"><B>&quot;invalid argument to CharacterSet&quot;</FONT></B>
            <B><FONT COLOR="#A020F0">elif</FONT></B> type(item) == String:
                <B><FONT COLOR="#A020F0">for</FONT></B> c <B><FONT COLOR="#A020F0">in</FONT></B> item:
                    accum.append((c, c))
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                <B><FONT COLOR="#A020F0">raise</FONT></B> <FONT COLOR="#BC8F8F"><B>&quot;invalid argument to CharacterSet&quot;</FONT></B>
        ranges = accum
        ranges.sort()
        index = 0
        <B><FONT COLOR="#A020F0">while</FONT></B> index &lt; len(ranges) - 1:
            [(c0, c1), (c2, c3)] = ranges[index:index + 2]
            <B><FONT COLOR="#A020F0">if</FONT></B> c1 &gt;= c2:
                ranges[index:index + 2] = [(c0, max(c1, c3))]
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                index = index + 1
        self.ranges = ranges
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">__cmp__</FONT></B>(self, other):
        <B><FONT COLOR="#A020F0">return</FONT></B> cmp(type(self), type(other)) <B><FONT COLOR="#A020F0">or</FONT></B> cmp(self.__class__, other.__class__) <B><FONT COLOR="#A020F0">or</FONT></B> cmp(self.ranges, other.ranges)

    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">__hash__</FONT></B>(self):
        <B><FONT COLOR="#A020F0">return</FONT></B> reduce(<B><FONT COLOR="#A020F0">lambda</FONT></B> a, b:a ^ b, map(hash, self.ranges))
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">convertString</FONT></B>(self, str):
        ranges = []
        index = 0
        <B><FONT COLOR="#A020F0">while</FONT></B> index &lt; len(str):
            c0 = c1 = str[index]
            index = index + 1
            <B><FONT COLOR="#A020F0">if</FONT></B> index + 1 &lt; len(str) <B><FONT COLOR="#A020F0">and</FONT></B> str[index ] == <FONT COLOR="#BC8F8F"><B>'-'</FONT></B>:
                c1 = str[index + 1]
                index = index + 2
            ranges.append((c0, c1))
        <B><FONT COLOR="#A020F0">return</FONT></B> ranges
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">matches</FONT></B>(self, c):
        <B><FONT COLOR="#A020F0">for</FONT></B> c0, c1 <B><FONT COLOR="#A020F0">in</FONT></B> self.ranges:
            <B><FONT COLOR="#A020F0">if</FONT></B> c0 &lt;= c <B><FONT COLOR="#A020F0">and</FONT></B> c &lt;= c1:
                <B><FONT COLOR="#A020F0">return</FONT></B> 1
        <B><FONT COLOR="#A020F0">return</FONT></B> 0
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">complement</FONT></B>(self):
        results = []
        <B><FONT COLOR="#A020F0">for</FONT></B> (_, c0), (c1, _) <B><FONT COLOR="#A020F0">in</FONT></B> map(None, [(None, None)] + self.ranges, self.ranges + [(None, None)]):
            i0 = c0 <B><FONT COLOR="#A020F0">and</FONT></B> ord(c0) + 1 <B><FONT COLOR="#A020F0">or</FONT></B> 0
            i1 = c1 <B><FONT COLOR="#A020F0">and</FONT></B> ord(c1) - 1 <B><FONT COLOR="#A020F0">or</FONT></B> 255
            <B><FONT COLOR="#A020F0">if</FONT></B> i0 &lt;= i1:
                results.append((chr(i0), chr(i1)))
        <B><FONT COLOR="#A020F0">if</FONT></B> results:
            <B><FONT COLOR="#A020F0">return</FONT></B> CharacterSet(results)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">union</FONT></B>(self, other):
        a = self.complement()
        b = other.complement()
        <B><FONT COLOR="#A020F0">if</FONT></B> a <B><FONT COLOR="#A020F0">and</FONT></B> b:
            c = a.intersection(b)
            <B><FONT COLOR="#A020F0">if</FONT></B> c:
                <B><FONT COLOR="#A020F0">return</FONT></B> c.complement()
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                <B><FONT COLOR="#A020F0">return</FONT></B> self.ANY
        <B><FONT COLOR="#A020F0">else</FONT></B>:
            <B><FONT COLOR="#A020F0">return</FONT></B> a <B><FONT COLOR="#A020F0">or</FONT></B> b

    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">__add__</FONT></B>(self, other):
        <B><FONT COLOR="#A020F0">return</FONT></B> self.union(other)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">intersection</FONT></B>(self, other):
        <B><FONT COLOR="#A020F0">if</FONT></B> self.ranges == other.ranges:
            <B><FONT COLOR="#A020F0">return</FONT></B> self
        results = []
        <B><FONT COLOR="#A020F0">for</FONT></B> (a0, a1) <B><FONT COLOR="#A020F0">in</FONT></B> self.ranges:
            <B><FONT COLOR="#A020F0">for</FONT></B> (b0, b1) <B><FONT COLOR="#A020F0">in</FONT></B> other.ranges:
                c0 = max(a0, b0)
                c1 = min(a1, b1)
                <B><FONT COLOR="#A020F0">if</FONT></B> c0 &lt;= c1:
                    results.append((c0, c1))
        results.sort()
        <B><FONT COLOR="#A020F0">if</FONT></B> results:
            <B><FONT COLOR="#A020F0">return</FONT></B> CharacterSet(results)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">__str__</FONT></B>(self):
        <FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;
        &gt;&gt;&gt; print CharacterSet([('a', 'a')])
        a
        &gt;&gt;&gt; print CharacterSet([('a', 'b')])
        [ab]
        &quot;&quot;&quot;</FONT></B>
        <B><FONT COLOR="#A020F0">if</FONT></B> self == self.ANY:
            <B><FONT COLOR="#A020F0">return</FONT></B> <FONT COLOR="#BC8F8F"><B>'.'</FONT></B>
        <B><FONT COLOR="#A020F0">elif</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> self.ranges:
            <B><FONT COLOR="#A020F0">return</FONT></B> <FONT COLOR="#BC8F8F"><B>'[^.]'</FONT></B>
        <B><FONT COLOR="#A020F0">for</FONT></B> key, value <B><FONT COLOR="#A020F0">in</FONT></B> METACHARS.items():
            <B><FONT COLOR="#A020F0">if</FONT></B> self == value:
                <B><FONT COLOR="#A020F0">return</FONT></B> <FONT COLOR="#BC8F8F"><B>'\\'</FONT></B> + key
        ranges = self.ranges
        <B><FONT COLOR="#A020F0">if</FONT></B> len(ranges) == 1 <B><FONT COLOR="#A020F0">and</FONT></B> ranges[0][0] == ranges[0][1]:
            <B><FONT COLOR="#A020F0">return</FONT></B> ranges[0][0]
        <B><FONT COLOR="#A020F0">if</FONT></B> ranges[0][0] == chr(0) <B><FONT COLOR="#A020F0">and</FONT></B> ranges[-1][1] == chr(255):
            s = str(self.complement())
            <B><FONT COLOR="#A020F0">if</FONT></B> s[0] == <FONT COLOR="#BC8F8F"><B>'['</FONT></B> <B><FONT COLOR="#A020F0">and</FONT></B> s[-1] == <FONT COLOR="#BC8F8F"><B>']'</FONT></B>:
                s = s[1:-1]
            <B><FONT COLOR="#A020F0">return</FONT></B> <FONT COLOR="#BC8F8F"><B>'[^'</FONT></B> + s + <FONT COLOR="#BC8F8F"><B>']'</FONT></B>
        s = <FONT COLOR="#BC8F8F"><B>''</FONT></B>
        <B><FONT COLOR="#A020F0">for</FONT></B> c0, c1 <B><FONT COLOR="#A020F0">in</FONT></B> ranges:
            <B><FONT COLOR="#A020F0">if</FONT></B> c0 == c1 <B><FONT COLOR="#A020F0">and</FONT></B> c0 != <FONT COLOR="#BC8F8F"><B>'-'</FONT></B>:
                s = s + self.crep(c0)
            <B><FONT COLOR="#A020F0">elif</FONT></B> ord(c0) + 1 == ord(c1) <B><FONT COLOR="#A020F0">and</FONT></B> c0 != <FONT COLOR="#BC8F8F"><B>'-'</FONT></B> <B><FONT COLOR="#A020F0">and</FONT></B> c1 != <FONT COLOR="#BC8F8F"><B>'-'</FONT></B>:
                s = s + <FONT COLOR="#BC8F8F"><B>&quot;%s%s&quot;</FONT></B> % (self.crep(c0), self.crep(c1))
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                s = s + <FONT COLOR="#BC8F8F"><B>&quot;%s-%s&quot;</FONT></B> % (self.crep(c0), self.crep(c1))
        <B><FONT COLOR="#A020F0">return</FONT></B> <FONT COLOR="#BC8F8F"><B>'['</FONT></B> + s + <FONT COLOR="#BC8F8F"><B>']'</FONT></B>
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">crep</FONT></B>(self, c):
        <B><FONT COLOR="#A020F0">return</FONT></B> {<FONT COLOR="#BC8F8F"><B>'\t'</FONT></B>: <FONT COLOR="#BC8F8F"><B>'\\t'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'\n'</FONT></B>: <FONT COLOR="#BC8F8F"><B>'\\n'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'\r'</FONT></B>: <FONT COLOR="#BC8F8F"><B>'\\r'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'\f'</FONT></B>: <FONT COLOR="#BC8F8F"><B>'\\f'</FONT></B>, <FONT COLOR="#BC8F8F"><B>'\v'</FONT></B>: <FONT COLOR="#BC8F8F"><B>'\\v'</FONT></B>}.get(c, c)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">__repr__</FONT></B>(self):
        <B><FONT COLOR="#A020F0">return</FONT></B> <FONT COLOR="#BC8F8F"><B>'&lt;'</FONT></B> + self.__class__.__name__ + <FONT COLOR="#BC8F8F"><B>' '</FONT></B> + str(self) + <FONT COLOR="#BC8F8F"><B>'&gt;'</FONT></B>

METACHARS = {
        <FONT COLOR="#BC8F8F"><B>'d'</FONT></B>: CharacterSet(<FONT COLOR="#BC8F8F"><B>'0-9'</FONT></B>),
        <FONT COLOR="#BC8F8F"><B>'s'</FONT></B>: CharacterSet(<FONT COLOR="#BC8F8F"><B>' \t\n\r\f\v'</FONT></B>),
        <FONT COLOR="#BC8F8F"><B>'w'</FONT></B>: CharacterSet(<FONT COLOR="#BC8F8F"><B>'a-zA-Z0-9'</FONT></B>)}
METACHARS[<FONT COLOR="#BC8F8F"><B>'D'</FONT></B>] = METACHARS[<FONT COLOR="#BC8F8F"><B>'d'</FONT></B>].complement()
METACHARS[<FONT COLOR="#BC8F8F"><B>'S'</FONT></B>] = METACHARS[<FONT COLOR="#BC8F8F"><B>'s'</FONT></B>].complement()
METACHARS[<FONT COLOR="#BC8F8F"><B>'W'</FONT></B>] = METACHARS[<FONT COLOR="#BC8F8F"><B>'w'</FONT></B>].complement()

CharacterSet.ANY = CharacterSet([(chr(0), chr(255))])


<B><FONT COLOR="#A020F0">class</FONT></B> RECompiler(SymbolRECompiler):
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">_readNextToken</FONT></B>(self):
        c = self.readChar()
        <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> c:
            <B><FONT COLOR="#A020F0">return</FONT></B> self.EOF
        <B><FONT COLOR="#A020F0">elif</FONT></B> c <B><FONT COLOR="#A020F0">in</FONT></B> <FONT COLOR="#BC8F8F"><B>'()|'</FONT></B>:
            <B><FONT COLOR="#A020F0">return</FONT></B> c
        <B><FONT COLOR="#A020F0">elif</FONT></B> c == <FONT COLOR="#BC8F8F"><B>'.'</FONT></B>:
            <B><FONT COLOR="#A020F0">return</FONT></B> CharacterSet.ANY
        <B><FONT COLOR="#A020F0">elif</FONT></B> c == <FONT COLOR="#BC8F8F"><B>'['</FONT></B>:
            <B><FONT COLOR="#A020F0">if</FONT></B> self.peekChar() == <FONT COLOR="#BC8F8F"><B>'~'</FONT></B>:
                self.readChar()
                <B><FONT COLOR="#A020F0">return</FONT></B> self.readCSetInnards().complement()
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                <B><FONT COLOR="#A020F0">return</FONT></B> self.readCSetInnards()
        <B><FONT COLOR="#A020F0">elif</FONT></B> c == <FONT COLOR="#BC8F8F"><B>'\\'</FONT></B>:
            c = self.readChar()
            <B><FONT COLOR="#A020F0">if</FONT></B> METACHARS.get(c):
                <B><FONT COLOR="#A020F0">return</FONT></B> METACHARS.get(c)
            <B><FONT COLOR="#A020F0">elif</FONT></B> c == <FONT COLOR="#BC8F8F"><B>'&amp;'</FONT></B>:
                <B><FONT COLOR="#A020F0">return</FONT></B> c
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                <B><FONT COLOR="#A020F0">return</FONT></B> CharacterSet([(c,c)])
        <B><FONT COLOR="#A020F0">else</FONT></B>:
            <B><FONT COLOR="#A020F0">return</FONT></B> CharacterSet([(c,c)])
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">readCSetInnards</FONT></B>(self):
        cset = CharacterSet([])
        <B><FONT COLOR="#A020F0">while</FONT></B> 1:
            c = self.readChar()
            <B><FONT COLOR="#A020F0">if</FONT></B> c == <FONT COLOR="#BC8F8F"><B>']'</FONT></B>:
                <B><FONT COLOR="#A020F0">return</FONT></B> cset
            <B><FONT COLOR="#A020F0">if</FONT></B> self.peekChar() == <FONT COLOR="#BC8F8F"><B>'-'</FONT></B>:
                self.readChar()
                cset = cset.union(CharacterSet([(c, self.readChar())]))
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                cset = cset.union(CharacterSet([(c, c)]))

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">compileRE</FONT></B>(str, minimize=1, recordSourcePositions=0):
    <B><FONT COLOR="#A020F0">return</FONT></B> RECompiler(str, recordSourcePositions=recordSourcePositions).toFSA(minimize=minimize)

<I><FONT COLOR="#B22222">#
</FONT></I><I><FONT COLOR="#B22222"># testing
</FONT></I><I><FONT COLOR="#B22222">#
</FONT></I><B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">_printCompiledREs</FONT></B>():
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'ab'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a|b'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'abc'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'ab*c'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'ab?c'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'ab+c'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'ab|c'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a(b|c)'</FONT></B>)
    <I><FONT COLOR="#B22222">#print compileRE('a\&amp;a')
</FONT></I>    <I><FONT COLOR="#B22222">#print compileRE('ab+\&amp;a+b')
</FONT></I>    <I><FONT COLOR="#B22222">#print compileRE('ab*\&amp;a*b')
</FONT></I>    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'ab|c?'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'ab|bc?'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a?'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'abc|acb|bac|bca|cab|cba'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'abc|acb|bac|bca|cab|cba'</FONT></B>, 0).determinized()
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'abc|acb|bac|bca|cab|cba'</FONT></B>, 0).determinized()
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'abc|acb|bac|bca|cab|cba'</FONT></B>, 0).minimized()
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'abc|acb|bac|bca|cab'</FONT></B>, 0).determinized()

    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a'</FONT></B>, 0)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a'</FONT></B>, 0).determinized()
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'ab'</FONT></B>, 0).determinized()
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a'</FONT></B>, 0).minimized()
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'ab'</FONT></B>, 0).minimized()
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a'</FONT></B>)
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a|b'</FONT></B>, 0).determinized()
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a|b'</FONT></B>, 0).minimized().getArcMetadata()
    <B><FONT COLOR="#A020F0">print</FONT></B> compileRE(<FONT COLOR="#BC8F8F"><B>'a|b'</FONT></B>, 0).minimized()

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">_test</FONT></B>(reset=0):
    <B><FONT COLOR="#A020F0">import</FONT></B> doctest, compileRE
    <B><FONT COLOR="#A020F0">if</FONT></B> reset:
        doctest.master = None <I><FONT COLOR="#B22222"># This keeps doctest from complaining after a reload.
</FONT></I>    <B><FONT COLOR="#A020F0">return</FONT></B> doctest.testmod(compileRE)
</PRE>
<HR>
<ADDRESS>Generated by <A HREF="http://www.iki.fi/~mtr/genscript/">GNU enscript 1.6.1</A>.</ADDRESS>
</BODY>
</HTML>
