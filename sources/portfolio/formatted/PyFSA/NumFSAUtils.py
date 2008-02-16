<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
<HEAD>
<TITLE>NumFSAUtils.py</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#1F00FF" ALINK="#FF0000" VLINK="#9900DD">
<A NAME="top">
<A NAME="file1">
<H1>PyFSA/NumFSAUtils.py</H1>

<PRE>
<FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot; Module NumFSAUtils -- optional utility functions for the FSA

The FSA module uses these methods if the Numeric module is present&quot;&quot;&quot;</FONT></B>

__author__  = <FONT COLOR="#BC8F8F"><B>&quot;Oliver Steele&quot;</FONT></B>, <FONT COLOR="#BC8F8F"><B>'steele@osteele.com'</FONT></B>

<B><FONT COLOR="#A020F0">import</FONT></B> Numeric

<B><FONT COLOR="#A020F0">class</FONT></B> TransitionSet:
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">__init__</FONT></B>(self):
        self.capacity = capacity = 100
        self.sources = Numeric.zeros(capacity)
        self.sinks = Numeric.zeros(capacity)
        self.labels = Numeric.zeros(capacity)
        self.size = 0
        self.stateMap = {}
        self.reverseStateMap = []
        self.nextStateIndex = 0
        self.labelMap = {}
        self.reverseLabelMap = []
        self.nextLabelIndex = 0
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">stateIndex</FONT></B>(self, state):
        index = self.stateMap.get(state)
        <B><FONT COLOR="#A020F0">if</FONT></B> index <B><FONT COLOR="#A020F0">is</FONT></B> None:
            index = self.nextStateIndex
            self.nextStateIndex = index + 1
            self.stateMap[state] = index
            self.reverseStateMap.append(state)
        <B><FONT COLOR="#A020F0">return</FONT></B> index
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">labelIndex</FONT></B>(self, label):
        index = self.labelMap.get(label)
        <B><FONT COLOR="#A020F0">if</FONT></B> index <B><FONT COLOR="#A020F0">is</FONT></B> None:
            index = self.nextLabelIndex
            self.nextLabelIndex = index + 1
            self.labelMap[label] = index
            self.reverseLabelMap.append(label)
        <B><FONT COLOR="#A020F0">return</FONT></B> index
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">append</FONT></B>(self, transition):
        source, sink, label = transition
        source, sink, label = self.stateIndex(source), self.stateIndex(sink), self.labelIndex(label)
        index = self.size
        <B><FONT COLOR="#A020F0">if</FONT></B> index &gt;= self.capacity:
            capacity = self.capacity + 100
            self.sources = Numeric.resize(self.sources, [capacity])
            self.sinks = Numeric.resize(self.sinks, [capacity])
            self.labels = Numeric.resize(self.labels, [capacity])
            self.capacity = capacity
        self.sources[index] = source
        self.sinks[index] = sink
        self.labels[index] = label
        self.size = self.size + 1
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">__len__</FONT></B>(self):
        <B><FONT COLOR="#A020F0">return</FONT></B> self.size
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">toList</FONT></B>(self, useStateIndices=0):
        transitions = []
        <B><FONT COLOR="#A020F0">for</FONT></B> i <B><FONT COLOR="#A020F0">in</FONT></B> range(self.size):
            sourceIndex = self.sources[i]
            sinkIndex = self.sinks[i]
            <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> useStateIndices:
                sourceIndex = self.reverseStateMap[sourceIndex]
                sinkIndex = self.reverseStateMap[sinkIndex]
            labelIndex = self.reverseLabelMap[self.labels[i]]
            transitions.append((sourceIndex, sinkIndex, labelIndex))
        <B><FONT COLOR="#A020F0">return</FONT></B> transitions


<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">stateCodeFromSet</FONT></B>(set):
    code = 0
    <B><FONT COLOR="#A020F0">for</FONT></B> state <B><FONT COLOR="#A020F0">in</FONT></B> set:
        code = code + (1L &lt;&lt; state)
    <B><FONT COLOR="#A020F0">return</FONT></B> code

<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">stateCodeToSet</FONT></B>(code):
    set = []
    index, bitmask = 0, 1L
    <B><FONT COLOR="#A020F0">while</FONT></B> code:
        <B><FONT COLOR="#A020F0">if</FONT></B> code &amp; bitmask:
            set.append(index)
            code = code &amp; ~bitmask
        index, bitmask = index + 1, bitmask &lt;&lt; 1
    <B><FONT COLOR="#A020F0">return</FONT></B> set


DETERMINITION_PROGRESS_TRIGGER = None   <I><FONT COLOR="#B22222"># Higher than this prints progress to stdout
</FONT></I>DETERMINATION_CUTOFF = None <I><FONT COLOR="#B22222"># Higher than this returns (incorrectly, but useful for profiling)
</FONT></I>
<B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">determinize</FONT></B>(states0, alphabet, transitions0, initial0, finals0, epsilonClosure):
    <B><FONT COLOR="#A020F0">from</FONT></B> FSA <B><FONT COLOR="#A020F0">import</FONT></B> constructLabelMap
    progress = 0
    transitions = []
    stateCodes, index = [stateCodeFromSet(epsilonClosure(initial0))], 0
    transitions = TransitionSet()
    <B><FONT COLOR="#A020F0">while</FONT></B> index &lt; len(stateCodes):
        <B><FONT COLOR="#A020F0">if</FONT></B> DETERMINATION_CUTOFF <B><FONT COLOR="#A020F0">and</FONT></B> index &gt; DETERMINATION_CUTOFF:
            <B><FONT COLOR="#A020F0">break</FONT></B>
        stateCode, index = stateCodes[index], index + 1
        stateSet = stateCodeToSet(stateCode)
        <B><FONT COLOR="#A020F0">if</FONT></B> DETERMINITION_PROGRESS_TRIGGER <B><FONT COLOR="#A020F0">and</FONT></B> len(stateCodes) &gt; DETERMINITION_PROGRESS_TRIGGER:
            progress = 1
            <B><FONT COLOR="#A020F0">print</FONT></B> <FONT COLOR="#BC8F8F"><B>'NumFSAUtils:'</FONT></B>, index, <FONT COLOR="#BC8F8F"><B>'of'</FONT></B>, len(stateCodes)
        localTransitions = filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> (s0,s1,l), set=stateSet:l <B><FONT COLOR="#A020F0">and</FONT></B> s0 <B><FONT COLOR="#A020F0">in</FONT></B> set, transitions0)
        <B><FONT COLOR="#A020F0">if</FONT></B> localTransitions:
            localLabels = map(<B><FONT COLOR="#A020F0">lambda</FONT></B>(_,__,label):label, localTransitions)
            labelMap = constructLabelMap(localLabels, alphabet)
            labelTargets = {}
            <B><FONT COLOR="#A020F0">for</FONT></B> _, s1, l1 <B><FONT COLOR="#A020F0">in</FONT></B> localTransitions:
                <B><FONT COLOR="#A020F0">for</FONT></B> label, positives <B><FONT COLOR="#A020F0">in</FONT></B> labelMap:
                    <B><FONT COLOR="#A020F0">if</FONT></B> l1 <B><FONT COLOR="#A020F0">in</FONT></B> positives:
                        successorStates = labelTargets[label] = labelTargets.get(label) <B><FONT COLOR="#A020F0">or</FONT></B> []
                        <B><FONT COLOR="#A020F0">for</FONT></B> s2 <B><FONT COLOR="#A020F0">in</FONT></B> epsilonClosure(s1):
                            <B><FONT COLOR="#A020F0">if</FONT></B> s2 <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> successorStates:
                                successorStates.append(s2)
            <B><FONT COLOR="#A020F0">for</FONT></B> label, successorStates <B><FONT COLOR="#A020F0">in</FONT></B> labelTargets.items():
                successorCode = stateCodeFromSet(successorStates)
                transitions.append((stateCode, successorCode, label))
                <B><FONT COLOR="#A020F0">if</FONT></B> successorCode <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> stateCodes:
                    stateCodes.append(successorCode)
    finalStates = []
    <B><FONT COLOR="#A020F0">for</FONT></B> stateCode <B><FONT COLOR="#A020F0">in</FONT></B> stateCodes:
        <B><FONT COLOR="#A020F0">if</FONT></B> filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> s,finalStates=finals0:s <B><FONT COLOR="#A020F0">in</FONT></B> finalStates, stateCodeToSet(stateCode)):
            finalStates.append(stateCode)
    f = transitions.stateIndex
    tuple = map(f, stateCodes), alphabet, transitions.toList(useStateIndices=1), f(stateCodes[0]), map(f, finalStates)
    <B><FONT COLOR="#A020F0">if</FONT></B> progress:
        <B><FONT COLOR="#A020F0">print</FONT></B> <FONT COLOR="#BC8F8F"><B>'exiting'</FONT></B>
    <B><FONT COLOR="#A020F0">return</FONT></B> tuple
</PRE>
<HR>
<ADDRESS>Generated by <A HREF="http://www.iki.fi/~mtr/genscript/">GNU enscript 1.6.1</A>.</ADDRESS>
</BODY>
</HTML>
