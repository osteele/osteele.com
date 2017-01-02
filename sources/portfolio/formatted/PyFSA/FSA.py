<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
<HEAD>
<TITLE>FSA.py</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#1F00FF" ALINK="#FF0000" VLINK="#9900DD">
<A NAME="top">
<A NAME="file1">
<H1>PyFSA/FSA.py</H1>

<PRE>
<I><FONT COLOR="#B22222"># Module FSA -- methods to manipulate finite-state automata
</FONT></I>
<FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;
This module defines an FSA class, for representing and operating on
finite-state automata (FSAs). FSAs can be used to represent regular
expressions and to test sequences for membership in the languages
described by regular expressions.

FSAs can be deterministic or nondeterministic, and they can contain
epsilon transitions. Methods to determinize an automaton (also
eliminating its epsilon transitions), and to minimize an automaton,
are provided.

The transition labels for an FSA can be symbols from an alphabet, as
in the standard formal definition of an FSA, but they can also be
instances which represent predicates. If these instances implement
instance.matches(), then the FSA nextState() function and accepts()
predicate can be used. If they implement instance.complement() and
instance.intersection(), the FSA can be be determinized and minimized,
to find a minimal deterministic FSA that accepts an equivalent
language.


Quick Start
----------
Instances of FSA can be created out of labels (for instance, strings)
by the singleton() function, and combined to create more complex FSAs
through the complement(), closure(), concatenation(), union(), and
other constructors. For example, concatenation(singleton('a'),
union(singleton('b'), closure(singleton('c')))) creates an FSA that
accepts the strings 'a', 'ab', 'ac', 'acc', 'accc', and so on.

Instances of FSA can also be created with the compileRE() function,
which compiles a simple regular expression (using only '*', '?', '+',
'|', '(', and ')' as metacharacters) into an FSA. For example,
compileRE('a(b|c*)') returns an FSA equivalent to the example in the
previous paragraph.

FSAs can be determinized, to create equivalent FSAs (FSAs accepting
the same language) with unique successor states for each input, and
minimized, to create an equivalent deterministic FSA with the smallest
number of states. FSAs can also be complemented, intersected, unioned,
and so forth as described under 'FSA Functions' below.


FSA Methods
-----------
The class FSA defines the following methods.

Acceptance
``````````
fsa.nextStates(state, input)
  returns a list of states
fsa.nextState(state, input)
  returns None or a single state if
  |nextStates| &lt;= 1, otherwise it raises an exception
fsa.nextStateSet(states, input)
  returns a list of states
fsa.accepts(sequence)
  returns true or false

Accessors and predicates
````````````````````````
isEmpty()
  returns true iff the language accepted by the FSA is the empty language
labels()
  returns a list of labels that are used in any transition
nextAvailableState()
  returns an integer n such that no states in the FSA
  are numeric values &gt;= n

Reductions
``````````
sorted(initial=0)
  returns an equivalent FSA whose states are numbered
  upwards from 0
determinized()
  returns an equivalent deterministic FSA
minimized()
  returns an equivalent minimal FSA
trimmed()
  returns an equivalent FSA that contains no unreachable or dead
  states

Presentation
````````````
toDotString()
  returns a string suitable as *.dot file for the 'dot'
  program from AT&amp;T GraphViz
view()
  views the FSA with a gs viewer, if gs and dot are installed


FSA Functions
------------
Construction from FSAs
``````````````````````
complement(a)
  returns an fsa that accepts exactly those sequences that its
  argument does not
closure(a)
  returns an fsa that accepts sequences composed of zero or more
  concatenations of sequences accepted by the argument
concatenation(a, b)
  returns an fsa that accepts sequences composed of a
  sequence accepted by a, followed by a sequence accepted by b
containment(a, occurrences=1)
  returns an fsa that accepts sequences that
  contain at least occurrences occurrences of a subsequence recognized by the
  argument.
difference(a, b)
  returns an fsa that accepts those sequences accepted by a
  but not b
intersection(a, b)
  returns an fsa that accepts sequences accepted by both a
  and b
iteration(a, min=1, max=None)
  returns an fsa that accepts sequences
  consisting of from min to max (or any number, if max is None) of sequences
  accepted by its first argument
option(a)
  equivalent to union(a, EMPTY_STRING_FSA)
reverse(a)
  returns an fsa that accepts strings whose reversal is accepted by
  the argument
union(a, b)
  returns an fsa that accepts sequences accepted by both a and b

Predicates
``````````
equivalent(a, b)
  returns true iff a and b accept the same language

Reductions (these equivalent to the similarly-named methods)
````````````````````````````````````````````````````````````
determinize(fsa)
  returns an equivalent deterministic FSA
minimize(fsa)
  returns an equivalent minimal FSA
sort(fsa, initial=0)
  returns an equivalent FSA whose states are numbered from
  initial
trim(fsa)
  returns an equivalent FSA that contains no dead or unreachable
  states

Construction from labels
````````````````````````
compileRE(string)
  returns an FSA that accepts the language described by
  string, where string is a list of symbols and '*', '+', '?', and '|' operators,
    with '(' and ')' to control precedence.
sequence(sequence)
  returns an fsa that accepts sequences that are matched by
  the elements of the argument. For example, sequence('abc') returns an fsa that
  accepts 'abc' and ['a', 'b', 'c'].
singleton(label)
  returns an fsa that accepts singletons whose elements are
  matched by label. For example, singleton('a') returns an fsa that accepts only
  the string 'a'.


FSA Constants
------------
EMPTY_STRING_FSA is an FSA that accepts the language consisting only
of the empty string.

NULL_FSA is an FSA that accepts the null language.

UNIVERSAL_FSA is an FSA that accepts S*, where S is any object.


FSA instance creation
---------------------
FSA is initialized with a list of states, an alphabet, a list of
transition, an initial state, and a list of final states. If fsa is an
FSA, fsa.tuple() returns these values in that order, i.e. (states,
alphabet, transitions, initialState, finalStates). They're also
available as fields of fsa with those names.

Each element of transition is a tuple of a start state, an end state,
and a label: (startState, endSTate, label).

If the list of states is None, it's computed from initialState,
finalStates, and the states in transitions.

If alphabet is None, an open alphabet is used: labels are assumed to
be objects that implements label.matches(input), label.complement(),
and label.intersection() as follows:

    - label.matches(input) returns true iff label matches input
    - label.complement() returnseither a label or a list of labels which,
        together with the receiver, partition the input alphabet
    - label.intersection(other) returns either None (if label and other don't
        both match any symbol), or a label that matches the set of symbols that
        both label and other match

As a special case, strings can be used as labels. If a strings 'a' and
'b' are used as a label and there's no alphabet, '~a' and '~b' are
their respective complements, and '~a&amp;~b' is the intersection of '~a'
and '~b'. (The intersections of 'a' and 'b', 'a' and '~b', and '~a'
and 'b' are, respectively, None, 'a', and 'b'.)


Goals
-----
Design Goals:

- easy to use
- easy to read (simple implementation, direct expression of algorithms)
- extensible

Non-Goals:

- efficiency
&quot;&quot;&quot;</FONT></B>

__author__  = <FONT COLOR="#BC8F8F"><B>&quot;Oliver Steele &lt;steele@osteele.com&gt;&quot;</FONT></B>

<B><FONT COLOR="#A020F0">from</FONT></B> types <B><FONT COLOR="#A020F0">import</FONT></B> InstanceType, ListType, IntType, LongType
IntegerTypes = (IntType, LongType)

try:
    <B><FONT COLOR="#A020F0">import</FONT></B> NumFSAUtils
<B><FONT COLOR="#A020F0">except</FONT></B> ImportError:
    NumFSAUtils = None

ANY = <FONT COLOR="#BC8F8F"><B>'ANY'</FONT></B>
EPSILON = None

TRACE_LABEL_MULTIPLICATIONS = 0
NUMPY_DETERMINIZATION_CUTOFF = 50

<B><FONT COLOR="#A020F0">class</FONT></B> FSA:
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">__init__</FONT></B>(self, states, alphabet, transitions, initialState, finalStates, arcMetadata=[]):
        <B><FONT COLOR="#A020F0">if</FONT></B> states == None:
            states = self.collectStates(transitions, initialState, finalStates)
        <B><FONT COLOR="#A020F0">else</FONT></B>:
            <B><FONT COLOR="#A020F0">assert</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> s, states=states:s <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> states, self.collectStates(transitions, initialState, finalStates))
        self.states = states
        self.alphabet = alphabet
        self.transitions = transitions
        self.initialState = initialState
        self.finalStates = finalStates
        self.setArcMetadata(arcMetadata)
    
    
    <I><FONT COLOR="#B22222">#
</FONT></I>    <I><FONT COLOR="#B22222"># Initialization
</FONT></I>    <I><FONT COLOR="#B22222">#
</FONT></I>    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">makeStateTable</FONT></B>(self, default=None):
        <B><FONT COLOR="#A020F0">for</FONT></B> state <B><FONT COLOR="#A020F0">in</FONT></B> self.states:
            <B><FONT COLOR="#A020F0">if</FONT></B> type(state) != IntType:
                <B><FONT COLOR="#A020F0">return</FONT></B> {}
        <B><FONT COLOR="#A020F0">if</FONT></B> reduce(min, self.states) &lt; 0: <B><FONT COLOR="#A020F0">return</FONT></B> {}
        <B><FONT COLOR="#A020F0">if</FONT></B> reduce(max, self.states) &gt; max(100, len(self.states) * 2): <B><FONT COLOR="#A020F0">return</FONT></B> {}
        <B><FONT COLOR="#A020F0">return</FONT></B> [default] * (reduce(max, self.states) + 1)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">initializeTransitionTables</FONT></B>(self):
        self._transitionsFrom = self.makeStateTable()
        <B><FONT COLOR="#A020F0">for</FONT></B> s <B><FONT COLOR="#A020F0">in</FONT></B> self.states:
            self._transitionsFrom[s] = []
        <B><FONT COLOR="#A020F0">for</FONT></B> transition <B><FONT COLOR="#A020F0">in</FONT></B> self.transitions:
            s, _, label = transition
            self._transitionsFrom[s].append(transition)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">collectStates</FONT></B>(self, transitions, initialState, finalStates):
        states = finalStates[:]
        <B><FONT COLOR="#A020F0">if</FONT></B> initialState <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> states:
            states.append(initialState)
        <B><FONT COLOR="#A020F0">for</FONT></B> s0, s1, _ <B><FONT COLOR="#A020F0">in</FONT></B> transitions:
            <B><FONT COLOR="#A020F0">if</FONT></B> s0 <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> states: states.append(s0)
            <B><FONT COLOR="#A020F0">if</FONT></B> s1 <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> states: states.append(s1)
        <B><FONT COLOR="#A020F0">return</FONT></B> states
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">computeEpsilonClosure</FONT></B>(self, state):
        states = [state]
        index = 0
        <B><FONT COLOR="#A020F0">while</FONT></B> index &lt; len(states):
            state, index = states[index], index + 1
            <B><FONT COLOR="#A020F0">for</FONT></B> _, s, label <B><FONT COLOR="#A020F0">in</FONT></B> self.transitionsFrom(state):
                <B><FONT COLOR="#A020F0">if</FONT></B> label == EPSILON <B><FONT COLOR="#A020F0">and</FONT></B> s <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> states:
                    states.append(s)
        states.sort()
        <B><FONT COLOR="#A020F0">return</FONT></B> states
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">computeEpsilonClosures</FONT></B>(self):
        self._epsilonClosures = self.makeStateTable()
        <B><FONT COLOR="#A020F0">for</FONT></B> s <B><FONT COLOR="#A020F0">in</FONT></B> self.states:
            self._epsilonClosures[s] = self.computeEpsilonClosure(s)
    
    
    <I><FONT COLOR="#B22222">#
</FONT></I>    <I><FONT COLOR="#B22222"># Copying
</FONT></I>    <I><FONT COLOR="#B22222">#
</FONT></I>    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">create</FONT></B>(self, *args):
        <B><FONT COLOR="#A020F0">return</FONT></B> apply(self.__class__, args)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">copy</FONT></B>(self, *args):
        copy = apply(self.__class__, args)
        <B><FONT COLOR="#A020F0">if</FONT></B> hasattr(self, <FONT COLOR="#BC8F8F"><B>'label'</FONT></B>):
            copy.label = self.label
        <B><FONT COLOR="#A020F0">if</FONT></B> hasattr(self, <FONT COLOR="#BC8F8F"><B>'source'</FONT></B>):
            copy.source = self.source
        <B><FONT COLOR="#A020F0">return</FONT></B> copy
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">creationArgs</FONT></B>(self):
        <B><FONT COLOR="#A020F0">return</FONT></B> self.tuple() + (self.getArcMetadata(),)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">coerce</FONT></B>(self, klass):
        copy = apply(klass, self.creationArgs())
        <B><FONT COLOR="#A020F0">if</FONT></B> hasattr(self, <FONT COLOR="#BC8F8F"><B>'source'</FONT></B>):
            copy.source = self.source
        <B><FONT COLOR="#A020F0">return</FONT></B> copy
    
    
    <I><FONT COLOR="#B22222">#
</FONT></I>    <I><FONT COLOR="#B22222"># Accessors
</FONT></I>    <I><FONT COLOR="#B22222">#
</FONT></I>    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">epsilonClosure</FONT></B>(self, state):
        try:
            <B><FONT COLOR="#A020F0">return</FONT></B> self._epsilonClosures[state]
        <B><FONT COLOR="#A020F0">except</FONT></B> AttributeError:
            self.computeEpsilonClosures()
        <B><FONT COLOR="#A020F0">return</FONT></B> self._epsilonClosures[state]
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">labels</FONT></B>(self):
        <FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;Returns a list of transition labels.&quot;&quot;&quot;</FONT></B>
        labels = []
        <B><FONT COLOR="#A020F0">for</FONT></B> (_, _, label) <B><FONT COLOR="#A020F0">in</FONT></B> self.transitions:
            <B><FONT COLOR="#A020F0">if</FONT></B> label <B><FONT COLOR="#A020F0">and</FONT></B> label <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> labels:
                labels.append(label)
        <B><FONT COLOR="#A020F0">return</FONT></B> labels
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">nextAvailableState</FONT></B>(self):
        <B><FONT COLOR="#A020F0">return</FONT></B> reduce(max, filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> s:type(s) <B><FONT COLOR="#A020F0">in</FONT></B> IntegerTypes, self.states), -1) + 1
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">transitionsFrom</FONT></B>(self, state):
        try:
            <B><FONT COLOR="#A020F0">return</FONT></B> self._transitionsFrom[state]
        <B><FONT COLOR="#A020F0">except</FONT></B> AttributeError:
            self.initializeTransitionTables()
        <B><FONT COLOR="#A020F0">return</FONT></B> self._transitionsFrom[state]
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">tuple</FONT></B>(self):
        <B><FONT COLOR="#A020F0">return</FONT></B> self.states, self.alphabet, self.transitions, self.initialState, self.finalStates
    
    
    <I><FONT COLOR="#B22222">#
</FONT></I>    <I><FONT COLOR="#B22222"># Arc Metadata Accessors
</FONT></I>    <I><FONT COLOR="#B22222">#
</FONT></I>    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">hasArcMetadata</FONT></B>(self):
        <B><FONT COLOR="#A020F0">return</FONT></B> hasattr(self, <FONT COLOR="#BC8F8F"><B>'_arcMetadata'</FONT></B>)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">getArcMetadata</FONT></B>(self):
        <B><FONT COLOR="#A020F0">return</FONT></B> getattr(self, <FONT COLOR="#BC8F8F"><B>'_arcMetadata'</FONT></B>, {}).items()
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">setArcMetadata</FONT></B>(self, list):
        arcMetadata = {}
        <B><FONT COLOR="#A020F0">for</FONT></B> (arc, data) <B><FONT COLOR="#A020F0">in</FONT></B> list:
            arcMetadata[arc] = data
        self._arcMetadata = arcMetadata
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">addArcMetadata</FONT></B>(self, list):
        <B><FONT COLOR="#A020F0">for</FONT></B> (arc, data) <B><FONT COLOR="#A020F0">in</FONT></B> list:
            self.addArcMetadataFor(arc, data)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">addArcMetadataFor</FONT></B>(self, transition, data):
        <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> hasattr(self, <FONT COLOR="#BC8F8F"><B>'_arcMetadata'</FONT></B>):
            self._arcMetadata = {}
        oldData = self._arcMetadata.get(transition)
        <B><FONT COLOR="#A020F0">if</FONT></B> oldData:
            <B><FONT COLOR="#A020F0">for</FONT></B> item <B><FONT COLOR="#A020F0">in</FONT></B> data:
                <B><FONT COLOR="#A020F0">if</FONT></B> item <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> oldData:
                    oldData.append(item)
        <B><FONT COLOR="#A020F0">else</FONT></B>:
            self._arcMetadata[transition] = data
        
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">setArcMetadataFor</FONT></B>(self, transition, data):
        <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> hasattr(self, <FONT COLOR="#BC8F8F"><B>'_arcMetadata'</FONT></B>):
            self._arcMetadata = {}
        self._arcMetadata[transition] = data
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">getArcMetadataFor</FONT></B>(self, transition, default=None):
        <B><FONT COLOR="#A020F0">return</FONT></B> getattr(self, <FONT COLOR="#BC8F8F"><B>'_arcMetadata'</FONT></B>, {}).get(transition, default)
    
    
    <I><FONT COLOR="#B22222">#
</FONT></I>    <I><FONT COLOR="#B22222"># Predicates
</FONT></I>    <I><FONT COLOR="#B22222">#
</FONT></I>    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">isEmpty</FONT></B>(self):
        <B><FONT COLOR="#A020F0">return</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> self.minimized().finalStates
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">isFSA</FONT></B>(self):
        <B><FONT COLOR="#A020F0">return</FONT></B> 1
    
    
    <I><FONT COLOR="#B22222">#
</FONT></I>    <I><FONT COLOR="#B22222"># Accepting
</FONT></I>    <I><FONT COLOR="#B22222">#
</FONT></I>    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">labelMatches</FONT></B>(self, label, input):
        <B><FONT COLOR="#A020F0">return</FONT></B> labelMatches(label, input)
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">nextStates</FONT></B>(self, state, input):
        states = []
        <B><FONT COLOR="#A020F0">for</FONT></B> _, sink, label <B><FONT COLOR="#A020F0">in</FONT></B> self.transitionsFrom(state):
            <B><FONT COLOR="#A020F0">if</FONT></B> self.labelMatches(label, input) <B><FONT COLOR="#A020F0">and</FONT></B> sink <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> states:
                states.extend(self.epsilonClosure(sink))
        <B><FONT COLOR="#A020F0">return</FONT></B> states
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">nextState</FONT></B>(self, state, input):
        states = self.nextStates(state, input)
        <B><FONT COLOR="#A020F0">assert</FONT></B> len(states) &lt;= 1
        <B><FONT COLOR="#A020F0">return</FONT></B> states <B><FONT COLOR="#A020F0">and</FONT></B> states[0]
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">nextStateSet</FONT></B>(self, states, input):
        successors = []
        <B><FONT COLOR="#A020F0">for</FONT></B> state <B><FONT COLOR="#A020F0">in</FONT></B> states:
            <B><FONT COLOR="#A020F0">for</FONT></B> _, sink, label <B><FONT COLOR="#A020F0">in</FONT></B> self.transitionsFrom(state):
                <B><FONT COLOR="#A020F0">if</FONT></B> self.labelMatches(label, input) <B><FONT COLOR="#A020F0">and</FONT></B> sink <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> successors:
                    successors.append(sink)
        <B><FONT COLOR="#A020F0">return</FONT></B> successors
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">accepts</FONT></B>(self, sequence):
        states = [self.initialState]
        <B><FONT COLOR="#A020F0">for</FONT></B> item <B><FONT COLOR="#A020F0">in</FONT></B> sequence:
            newStates = []
            <B><FONT COLOR="#A020F0">for</FONT></B> state <B><FONT COLOR="#A020F0">in</FONT></B> states:
                <B><FONT COLOR="#A020F0">for</FONT></B> s1 <B><FONT COLOR="#A020F0">in</FONT></B> self.nextStates(state, item):
                    <B><FONT COLOR="#A020F0">if</FONT></B> s1 <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> newStates:
                        newStates.append(s1)
            states = newStates
        <B><FONT COLOR="#A020F0">return</FONT></B> len(filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> s, finals=self.finalStates:s <B><FONT COLOR="#A020F0">in</FONT></B> finals, states)) &gt; 0
    
    
    <I><FONT COLOR="#B22222">#
</FONT></I>    <I><FONT COLOR="#B22222"># FSA operations
</FONT></I>    <I><FONT COLOR="#B22222">#
</FONT></I>    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">complement</FONT></B>(self):
        states, alpha, transitions, start, finals = completion(self.determinized()).tuple()
        <B><FONT COLOR="#A020F0">return</FONT></B> self.create(states, alpha, transitions, start, filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> s,f=finals:s <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> f, states))<I><FONT COLOR="#B22222">#.trimmed()
</FONT></I>    
    
    <I><FONT COLOR="#B22222">#
</FONT></I>    <I><FONT COLOR="#B22222"># Reductions
</FONT></I>    <I><FONT COLOR="#B22222">#
</FONT></I>    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">sorted</FONT></B>(self, initial=0):
        <B><FONT COLOR="#A020F0">if</FONT></B> hasattr(self, <FONT COLOR="#BC8F8F"><B>'_isSorted'</FONT></B>):
            <B><FONT COLOR="#A020F0">return</FONT></B> self
        stateMap = {}
        nextState = initial
        states, index = [self.initialState], 0
        <B><FONT COLOR="#A020F0">while</FONT></B> index &lt; len(states) <B><FONT COLOR="#A020F0">or</FONT></B> len(states) &lt; len(self.states):
            <B><FONT COLOR="#A020F0">if</FONT></B> index &gt;= len(states):
                <B><FONT COLOR="#A020F0">for</FONT></B> state <B><FONT COLOR="#A020F0">in</FONT></B> self.states:
                    <B><FONT COLOR="#A020F0">if</FONT></B> stateMap.get(state) == None:
                        <B><FONT COLOR="#A020F0">break</FONT></B>
                states.append(state)
            state, index = states[index], index + 1
            new, nextState = nextState, nextState + 1
            stateMap[state] = new
            <B><FONT COLOR="#A020F0">for</FONT></B> _, s, _ <B><FONT COLOR="#A020F0">in</FONT></B> self.transitionsFrom(state):
                <B><FONT COLOR="#A020F0">if</FONT></B> s <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> states:
                    states.append(s)
        states = stateMap.values()
        transitions = map(<B><FONT COLOR="#A020F0">lambda</FONT></B> (s0,s1,l),m=stateMap:(m[s0], m[s1], l), self.transitions)
        arcMetadata = map(<B><FONT COLOR="#A020F0">lambda</FONT></B> ((s0, s1, label), data), m=stateMap: ((m[s0], m[s1], label), data), self.getArcMetadata())
        copy = self.copy(states, self.alphabet, transitions, stateMap[self.initialState], map(stateMap.get, self.finalStates), arcMetadata)
        copy._isSorted = 1
        <B><FONT COLOR="#A020F0">return</FONT></B> copy
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">trimmed</FONT></B>(self):
        <FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;Returns an equivalent FSA that doesn't include unreachable states,
        or states that only lead to dead states.&quot;&quot;&quot;</FONT></B>
        <B><FONT COLOR="#A020F0">if</FONT></B> hasattr(self, <FONT COLOR="#BC8F8F"><B>'_isTrimmed'</FONT></B>):
            <B><FONT COLOR="#A020F0">return</FONT></B> self
        states, alpha, transitions, initial, finals = self.tuple()
        reachable, index = [initial], 0
        <B><FONT COLOR="#A020F0">while</FONT></B> index &lt; len(reachable):
            state, index = reachable[index], index + 1
            <B><FONT COLOR="#A020F0">for</FONT></B> (_, s, _) <B><FONT COLOR="#A020F0">in</FONT></B> self.transitionsFrom(state):
                <B><FONT COLOR="#A020F0">if</FONT></B> s <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> reachable:
                    reachable.append(s)
        endable, index = list(finals), 0
        <B><FONT COLOR="#A020F0">while</FONT></B> index &lt; len(endable):
            state, index = endable[index], index + 1
            <B><FONT COLOR="#A020F0">for</FONT></B> (s0, s1, _) <B><FONT COLOR="#A020F0">in</FONT></B> transitions:
                <B><FONT COLOR="#A020F0">if</FONT></B> s1 == state <B><FONT COLOR="#A020F0">and</FONT></B> s0 <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> endable:
                    endable.append(s0)
        states = []
        <B><FONT COLOR="#A020F0">for</FONT></B> s <B><FONT COLOR="#A020F0">in</FONT></B> reachable:
            <B><FONT COLOR="#A020F0">if</FONT></B> s <B><FONT COLOR="#A020F0">in</FONT></B> endable:
                states.append(s)
        <B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> states:
            <B><FONT COLOR="#A020F0">if</FONT></B> self.__class__  == FSA:
                <B><FONT COLOR="#A020F0">return</FONT></B> NULL_FSA
            <B><FONT COLOR="#A020F0">else</FONT></B>:
                <B><FONT COLOR="#A020F0">return</FONT></B> NULL_FSA.coerce(self.__class__)
        transitions = filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> (s0, s1, _), states=states:s0 <B><FONT COLOR="#A020F0">in</FONT></B> states <B><FONT COLOR="#A020F0">and</FONT></B> s1 <B><FONT COLOR="#A020F0">in</FONT></B> states, transitions)
        arcMetadata = filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> ((s0, s1, _), __), states=states: s0 <B><FONT COLOR="#A020F0">in</FONT></B> states <B><FONT COLOR="#A020F0">and</FONT></B> s1 <B><FONT COLOR="#A020F0">in</FONT></B> states, self.getArcMetadata())
        result = self.copy(states, alpha, transitions, initial, filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> s, states=states:s <B><FONT COLOR="#A020F0">in</FONT></B> states, finals), arcMetadata).sorted()
        result._isTrimmed = 1
        <B><FONT COLOR="#A020F0">return</FONT></B> result
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">withoutEpsilons</FONT></B>(self):
        <I><FONT COLOR="#B22222"># replace each state by its epsilon closure
</FONT></I>        states0, alphabet, transitions0, initial0, finals0 = self.tuple()
        initial = self.epsilonClosure(self.initialState)
        initial.sort()
        initial = tuple(initial)
        stateSets, index = [initial], 0
        transitions = []
        <B><FONT COLOR="#A020F0">while</FONT></B> index &lt; len(stateSets):
            stateSet, index = stateSets[index], index + 1
            <B><FONT COLOR="#A020F0">for</FONT></B> (s0, s1, label) <B><FONT COLOR="#A020F0">in</FONT></B> transitions0:
                <B><FONT COLOR="#A020F0">if</FONT></B> s0 <B><FONT COLOR="#A020F0">in</FONT></B> stateSet <B><FONT COLOR="#A020F0">and</FONT></B> label:
                    target = self.epsilonClosure(s1)
                    target.sort()
                    target = tuple(target)
                    transition = (stateSet, target, label)
                    <B><FONT COLOR="#A020F0">if</FONT></B> transition <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> transitions:
                        transitions.append(transition)
                    <B><FONT COLOR="#A020F0">if</FONT></B> target <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> stateSets:
                        stateSets.append(target)
        finalStates = []
        <B><FONT COLOR="#A020F0">for</FONT></B> stateSet <B><FONT COLOR="#A020F0">in</FONT></B> stateSets:
            <B><FONT COLOR="#A020F0">if</FONT></B> filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> s, finalStates=self.finalStates:s <B><FONT COLOR="#A020F0">in</FONT></B> finalStates, stateSet):
                finalStates.append(stateSet)
        copy = self.copy(stateSets, alphabet, transitions, stateSets[0], finalStates).sorted()
        copy._isTrimmed = 1
        <B><FONT COLOR="#A020F0">return</FONT></B> copy
    
    <B><FONT COLOR="#A020F0">def</FONT></B> <B><FONT COLOR="#0000FF">determinized</FONT></B>(self):
        <FONT COLOR="#BC8F8F"><B>&quot;&quot;&quot;Returns a deterministic FSA that accepts the same language.&quot;&quot;&quot;</FONT></B>
        <B><FONT COLOR="#A020F0">if</FONT></B> hasattr(self, <FONT COLOR="#BC8F8F"><B>'_isDeterminized'</FONT></B>):
            <B><FONT COLOR="#A020F0">return</FONT></B> self
        <B><FONT COLOR="#A020F0">if</FONT></B> len(self.states) &gt; NUMPY_DETERMINIZATION_CUTOFF <B><FONT COLOR="#A020F0">and</FONT></B> NumFSAUtils <B><FONT COLOR="#A020F0">and</FONT></B> <B><FONT COLOR="#A020F0">not</FONT></B> self.getArcMetadata():
            data = apply(NumFSAUtils.determinize, self.tuple() + (self.epsilonClosure,))
            result = apply(self.copy, data).sorted()
            result._isDeterminized = 1
            <B><FONT COLOR="#A020F0">return</FONT></B> result
        transitions = []
        stateSets, index = [tuple(self.epsilonClosure(self.initialState))], 0
        arcMetadata = []
        <B><FONT COLOR="#A020F0">while</FONT></B> index &lt; len(stateSets):
            stateSet, index = stateSets[index], index + 1
            localTransitions = filter(<B><FONT COLOR="#A020F0">lambda</FONT></B> (s0,s1,l), set=stateSet:l <B><FONT COLOR="#A020F0">and</FONT></B> s0 <B><FONT COLOR="#A020F0">in</FONT></B> set, self.transitions)
            <B><FONT COLOR="#A020F0">if</FONT></B> localTransitions:
                localLabels = map(<B><FONT COLOR="#A020F0">lambda</FONT></B>(_,__,label):label, localTransitions)
                labelMap = constructLabelMap(localLabels, self.alphabet)
                labelTargets = {}   <I><FONT COLOR="#B22222"># a map from labels to target states
</FONT></I>                <B><FONT COLOR="#A020F0">for</FONT></B> transition <B><FONT COLOR="#A020F0">in</FONT></B> localTransitions:
                    _, s1, l1 = transition
                    <B><FONT COLOR="#A020F0">for</FONT></B> label, positives <B><FONT COLOR="#A020F0">in</FONT></B> labelMap:
                        <B><FONT COLOR="#A020F0">if</FONT></B> l1 <B><FONT COLOR="#A020F0">in</FONT></B> positives:
                            successorStates = labelTargets[label] = labelTargets.get(label) <B><FONT COLOR="#A020F0">or</FONT></B> []
                            <B><FONT COLOR="#A020F0">for</FONT></B> s2 <B><FONT COLOR="#A020F0">in</FONT></B> self.epsilonClosure(s1):
                                <B><FONT COLOR="#A020F0">if</FONT></B> s2 <B><FONT COLOR="#A020F0">not</FONT></B> <B><FONT COLOR="#A020F0">in</FONT></B> successorStates:
                                    successorStates.append(s2)
                            <B><FONT COLOR="#A020F0">if</FONT></B> self.getArcMetadataFor(transition):
                                arcMetadata.append(((stateSet, successorStates, label), self.getArcMetadataFo