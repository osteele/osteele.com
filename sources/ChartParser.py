# ChartParser.py, version 1.0
#
# Released to the public domain 10 August 1999 by Oliver Steele.

"""Module ChartParser -- a simple chart parser

EXAMPLES
	>>> parse("John loves Mary")
	[S[NP[PN(John)], VP[VBAR[V(loves), NP[PN(Mary)]]]]]
	
	>>> parse("He saw the man with the binoculars")[0]
	S[NP[PRON(He)], VP[VBAR[VBAR[V(saw), NP[DET(the), NBAR[N(man)]]], PP[PREP(with), NP[DET(the), NBAR[N(binoculars)]]]]]]
	
	>>> parse("He saw the man with the binoculars")[1]
	S[NP[PRON(He)], VP[VBAR[V(saw), NP[DET(the), NBAR[NBAR[N(man)], PP[PREP(with), NP[DET(the), NBAR[N(binoculars)]]]]]]]]
"""

__author__  = 'Oliver Steele', 'steele@cs.brandeis.edu'
__version__ = '1.0'

import string

# Simple tokenizer: split at whitespace, and separate out leading quotes and
# trailing quotes and punctuation.
def tokenize(str):
	tokens = string.split(str, ' ')
	i = 0
	while i < len(tokens):
		token = tokens[i]
		if len(token) > 1 and token[0] in '\'"':
			tokens[i:i+1] = [token[0], token[1:]]
		elif len(token) > 1 and token[-1] in '.?,!;:\'"':
			tokens[i:i+1] = [token[:-1], token[-1]]
		else:
			i = i + 1
	return tokens

# Define some basic syntactic categories, as variables bound to their own names.
for name in string.split('V VP VBAR N NBAR NP PRON PN S DET PREP PP'):
	globals()[name] = name
del name

# Just enough of a tag dictionary to parse the test sentence.
TAGS = {'John': PN, 'loves': V, 'Mary': PN, 'he': PRON, 'saw': V, 'the': DET, 'man': N,
			'with': PREP, 'binoculars': N}

# Simple tagger.
def tags(token):
	return [TAGS.get(token) or TAGS.get(string.lower(token))]

class Rule:
	"""A rule represents a phrase-structure production rule of the form:
		A => B C
	where A expands to B followed by C (and therefore, B followed by
	C can be composed into an A).  In this example, the left-hand-side
	(lhs) is A, and the right-hand-side (rhs) is [B, C]."""
	
	def __init__(self, spec):
		"""spec is of the form [A, B, C], where A is the lhs and the remaining
		items are the rhs.  In other words, [A, B, C] represents A => B C"""
		self.lhs = spec[0]
		self.rhs = spec[1:]
	
	def __repr__(self):
		return '%s => %s' % (string.join(self.lhs, ' '), self.rhs)
	
	def matches(self, category):
		return self.rhs[0] == category

# A toy grammar:
RULES = map(Rule, [
		[VP, VBAR],
		[VBAR, V, NP],
		[VBAR, VBAR, PP],
		[NBAR, N],
		[NBAR, NBAR, PP],
		[NP, DET, NBAR],
		[NP, PN],
		[NP, PRON],
		[PP, PREP, NP],
		[S, NP, VP]])

class Constituent:
	def __init__(self, type, children, left, right):
		self.type = type
		self.children = children
		self.left = left
		self.right = right
	
	def __repr__(self):
		return '%s%s' % (self.type, `self.children`)
	
	def tree(self):
		return [self.type] + self.children
	
	def terminals(self):
		return(reduce(lambda a,b:a+b, map(lambda c:c.terminals(), self.children)))

class PreTerminal(Constituent):
	def __init__(self, tag, token, left):
		Constituent.__init__(self, tag, None, left, left+1)
		self.token = token
	
	def __repr__(self):
		return '%s(%s)' % (self.type, self.token)
	
	def tree(self):
		return self
	
	def terminals(self):
		return [self]

class Edge:
	def __init__(self, rule, left, right=None, index=0, children=None):
		self.rule = rule
		self.left = left
		self.right = right or left
		self.index = index
		self.children = children or []
	
	def __repr__(self):
		str = []
		for i in range(len(self.rule.rhs)):
			if i == self.index:
				str.append('^')
			str.append(self.rule.rhs[i] + ' ')
		return '<%s => %s at %s:%s>' % (self.rule.lhs, string.join(str, '')[:-1], self.left, self.right)
	
	def advanceOver(self, chart, constituent):
		rule = self.rule
		if self.right == constituent.left and rule.rhs[self.index] == constituent.type:
			if chart.TRACE:
				print 'advancing', self, 'over', constituent
			chart.addEdge(Edge(rule, self.left, constituent.right, self.index + 1, self.children + [constituent]))
	
	def active(self):
		return self.index < len(self.rule.rhs)

class Chart:
	TRACE = 0
	
	def __init__(self, rules=RULES):
		self.rules = rules
	
	def init(self, n):
		self.edges = map(lambda n:[], range(n))
		self.constituents = map(lambda n:[], range(n))
	
	def parse(self, string):
		tokens = tokenize(string)
		self.init(len(tokens))
		for i in range(len(tokens)):
				self.addToken(tokens[i], i)
		return self.spans()
	
	def spans(self):
		return filter(lambda c, len=len(self.edges):c.right == len, self.constituents[0])
	
	def addToken(self, token, position):
		for tag in tags(token):
			self.addConstituent(PreTerminal(tag, token, position))
	
	def addConstituent(self, constituent):
		if self.TRACE:
			print 'adding', constituent,
			print 'at', constituent.left, '[]', constituent.left
		self.constituents[constituent.left].append(constituent)
		for edge in self.edges[constituent.left]:
			edge.advanceOver(self, constituent)
		for rule in self.rules:
			if rule.matches(constituent.type):
				Edge(rule, constituent.left).advanceOver(self, constituent)
	
	def addEdge(self, edge):
		if self.TRACE:
			print 'adding', edge
		if edge.active():
			if edge.right < len(self.edges):
				self.edges[edge.right].append(edge)
				for constituent in self.constituents[edge.right]:
					edge.advanceOver(self, constituent)
		else:
			self.addConstituent(Constituent(edge.rule.lhs, edge.children, edge.left, edge.right))

def parse(string):
	return Chart().parse(string)

def _test():
	import doctest, ChartParser
	return doctest.testmod(ChartParser)

"""
_test()
"""