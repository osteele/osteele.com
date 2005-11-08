# Author: Oliver Steele, steele@cs.brandeis.edu
# Source: http://osteele.com/sources/textlines.py
#
# Copyright 1998-1999 by Oliver Steele.
# You have my permission to use this freely, as long as you keep the attribution
# and label your changes. -- Oliver Steele

"""Module textlines -- a read-on-demand substitute for file.readlines()

OVERVIEW

Use textlines(file) instead of file.readlines() when it's not possible or
desirable to read the entire file into memory at once.  For example:
  for line in textlines(file):
    ...
is computationally equivalent to
  for line in file.readlines():
    ...
but only reads one line at a time into memory.

The argument to textlines can also be a pathname -- that is, textlines(pathname)
is equivalent to textlines(open(pathname)).


DETAILS

textlines() returns an object of type TextFileLineIterator, which supports the
len() and [index] operations and can therefore be used in for loops and as a
sequence argument to map, and filter.  Unlike readlines(), textlines() doesn't
read the entire file into memory at once -- it reads each line as it's requested
(reading it multiple times if it's requested multiple times).

If lines is an object returned by a call to textlines, lines[n] for an arbitrary
value is generally very inefficient (the file is scanned from the beginning, and
previous computation isn't cached).  However, the special case where the previous
operation on lines was an evaluation of lines[n-1] is cached.  This makes the
idioms
  for line in textlines(file):
    ...
and
  map(fn, textlines(file))
  filter(fn, textlines(file))
roughly as efficient in time as the corresponding code that uses
open(file).readlines() instead of textlines(file).

The result of a call to len(lines) is also cached.  The implementation class,
TextFileLineIterator, is exposed so that it can be subclassed to implement
additional caching schemes (for example, lineno -> string mappings could
be stored in a table).
"""

__author__  = "Oliver Steele", 'steele@cs.brandeis.edu'
__version__ = '1.0d1'

# Change history:
# 1.0	2/22/99
#	Initial version.
# 1.1d1	2/22/99
#	Return object now emulates a file more fully:
#	- f.closed, f.mode, f.name, and f.softspace are defined (and call the basis object)
#	- f.close() is defined (ditto)

import string

def textlines(path_or_file):
	"""Return an object that supports a subset of the sequence protocol (lines.len
	and lines[index]), and that can be used as a lazy (less memory-hungry)
	replacement for file.readlines() or open(path).readlines()."""
	return TextFileLineIterator(path_or_file)

class TextFileLineIterator:
	def __init__(self, path_or_file):
		import types
		file = path_or_file
		if isinstance(file, types.StringType):
			file = open(file)
		self.file = file
		self.rewind()
	
	def __getattr__(self, name):
		if name in ('closed', 'mode', 'name', 'softspace'):
			return getattr(self.file, name)
			#return {'closed': lambda f:f.closed,
			#		'mode': lambda f:f.mode,
			#		'name': lambda f:f.name,
			#		'softspace': lambda f:f.softspace}[name]
		else:
			raise AttributeError, name
	
	def close(self):
		self.file.close()
	
	def rewind(self):
		self.file.seek(0)
		self.nextindex = 0
	
	def __len__(self):
		if not hasattr(self, 'length'):
			self.rewind()
			length = 0
			for line in self:
				length = length + 1
			self.length = length
		return self.length
	
	def __getitem__(self, index):
		if index < self.nextindex:
			self.rewind()
		while index >= self.nextindex:
			line = self.file.readline()
			if not line:
				raise IndexError, "index out of range"
			self.nextindex = self.nextindex + 1
		return line
