# textopen, version 1.2.1
#
# Released to the public domain 16 August 1999 by Oliver Steele,
# steele@cs.brandeis.edu.

"""Module textopen -- read from text files that use foreign line separators

USAGE

  from textopen import textopen
  file1 = textopen(pathname)
is equivalent to
  file2 = open(pathname)
except that file1.readline() will return the first line that's terminated by any
of '\n' (UNIX), '\r' (MacOS), or '\r\n' (PC), whereas file2.readline() will
look for a different line separator depending on the execution platform (and
won't generally work for files that use a different convention).

The line that's returned, in any case, will end in '\n' (or nothing, if it's an
unterminated line that ends the file).


RATIONALE

Using open followed by readline can attempt to read the whole file if it's a
non-native text file (for instance, a UNIX file on a Mac). It's therefore not
generally possible to read the whole file and then look for line breaks (the file
may not fit in memory), and it's inconvenient to read it in blocks.  This module
reads in blocks, but it isolates the inconvenience from the client programs.


DETAILS

textopen returns an object that supports a subset of the file protocol (readline,
readlines, seek, and tell).  textopen can also be applied to a file object (or
any object that implements the read, seek and tell functions) -- that is,
textopen(pathname) is equivalent to textopen(open(pathname)).
"""

__author__  = "Oliver Steele", 'steele@cs.brandeis.edu'
__version__ = '1.2.1'

# Change history:
# 1.0	2/22/99
#	Initial version.
# 1.1	3/22/99
#	Return object now fully emulates a file:
#	- f.closed, f.mode, f.name, and f.softspace are defined (and call the basis object)
#	- f.close() is defined (ditto)
# 1.2	6/11/99
#	Added an optional 'mode' argument, for compatibility with open().
#	Added test code.
# 1.2.1	8/16/99
#	Released to the public domain.
#	Fixed a bug where a final line that didn't end in a line separator could be
#	ignored.

import string

def textopen(pathOrFile, mode='r'):
	"""If mode is 'r' (the default), return a file-like object based on pathOrFile
	(a pathname string or a file-like object) whose readline() method understands
	UNIX (lf), Mac (cr), and PC (crlf) separators, and returns lines that end in
	'\n' in all cases.
	
	If mode is 'rb' or a writing mode, textopen() is the same as open()."""
	
	if mode == 'r':
		return _TextFileLinefeedAdaptor(pathOrFile)
	elif mode == 'r+':
		try:
			exception = NotImplementedError	# new in 1.5.2
		except:
			exception = Exception
		raise exception, "'r+' mode is not implemented"
	else:
		return open(pathOrFile)

class _TextFileLinefeedAdaptor:
	BLOCK_SIZE = 512
	
	def __init__(self, pathOrFile):
		import types
		file = pathOrFile
		if isinstance(file, types.StringType):
			file = open(file, "rb")
		self.file = file
		self.buffer = None
	
	def __getattr__(self, name):
		if name in ('closed', 'mode', 'name', 'softspace'):
			return getattr(self.file, name)
		else:
			raise AttributeError, name
	
	def close(self):
		self.file.close()
	
	def readline(self):
		# prime the buffer
		buffer = self.buffer
		if not buffer:
			buffer = self.file.read(self.BLOCK_SIZE)
			if not buffer:
				return ''
		# find the earliest '\r' or '\n'
		crpos = string.find(buffer, '\r')
		lfpos = string.find(buffer, '\n')
		# If there's none, return the whole line plus the next block.
		# In the worst case (no '\r' or '\n' in the file), this recurses
		# filesize/BLOCK_SIZE deep.  I consider this unlikely (the file
		# is supposed to be a text file), but if it's a problem the
		# recursion could be changed into a loop at a slight expense in
		# readability.
		if max(crpos, lfpos) < 0:
			self.buffer = None
			return buffer + self.readline()
		if lfpos < 0 or 0 <= crpos < lfpos:	# '\r\n?'
			line = buffer[:crpos] + '\n'
			otherchar = '\n'
			nextpos = crpos + 1
		else:								# '\n\r?'
			line = buffer[:lfpos + 1]		# include the '\n'
			otherchar = '\r'
			nextpos = lfpos + 1
		if nextpos == len(buffer):
			buffer = self.file.read(self.BLOCK_SIZE)
			nextpos = 0
		# Skip over an '\r' after an '\n' or vice versa.  This interprets
		# '\n\r' as a newline as well as PC '\r\n', but on the Mac line
		# separators in a PC file opened in text mode (default or "r", as opposed
		# to "rb") read '\n\r', so the added generality makes the class work
		# as a wrapper for files that are opened in text mode outside our control.
		if buffer and buffer[nextpos] == otherchar:
			nextpos = nextpos + 1
		self.buffer = buffer[nextpos:]
		return line
	
	def readlines(self):
		lines = []
		while 1:
			line = self.readline()
			if line == '':
				break
			lines.append(line)
		return lines
	
	def read(self, length=None):
		self.seek(self.tell())
		if length:
			return self.file.read(length)
		else:
			self.file.read()
	
	def rewind(self):
		self.seek(0)
	
	def seek(self, position):
		self.file.seek(position)
		self.buffer = None
	
	def tell(self):
		return self.file.tell() - len(self.buffer or "")

def _test():
	_testlength(1)
	for index in range(-2, 3):
		_testlength(_TextFileLinefeedAdaptor.BLOCK_SIZE + index)

def _testlength(length):
	import tempfile
	import string
	lines = []
	for line in '1', '2', '3':
		lines.append(line * length)
	separators = {'UNIX': '\n', 'MacOS': '\r', 'DOS': '\n\r'}
	for separator in separators.keys():
		filename = tempfile.mktemp()
		try:
			file = open(filename, 'wb')
			# leave the separator off the last line, to test that case too
			file.write(string.join(lines, separators[separator]))
			file.close()
			file = textopen(filename, 'r')
			for index in range(len(lines)):
				expected = lines[index]
				if index != len(lines) - 1:
					expected = expected + '\n' 
				actual = file.readline()
				if expected != actual:
					print 'Platform %s:\n  expected: %s\n  read: %s' % (separator, `expected`, `actual`)
		finally:
			try:
				file.close()
			except:
				pass
			import os
			os.remove(filename)
