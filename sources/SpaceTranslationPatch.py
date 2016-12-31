# SpaceTranslationPatch patches the MacOS Python IDE to convert spaces to tabs
# when a source file is opened, and back to spaces when it's saved.
#
# Drop this file on PythonIDE to install it. It requires
# http://www.strout.net/python/mac/PatchUtils.py in order to run.
#
# Version 2 released to the public domain 3 November 1999
# by Oliver Steele (steele@cs.brandeis.edu).


""" SpaceTranslationPatch patch

Patches the MacOS Python IDE to convert spaces to tabs when a source
file is opened, and back to spaces when it's saved.

A file is converted if it contains any line (other than the first) that starts with
at least four spaces.  If the file was saved by the MacOS Python IDE with a
tabsize of n spaces set in the font settings dialog, n spaces are converted
to one tab.  Otherwise four is used for n, except that eight is used if no
line starts with four spaces and at least one line starts with eight.  Only
initial spaces are converted to tabs when a file is opened, and only initial
tabs are converted back to spaces when it's written.

If you find any files that this heuristic fails for, you're welcome to send them
to me, and if I have any free time, I'll improve the heuristic.

INSTALLATION INSTRUCTIONS

Retrieve http://www.strout.net/python/mac/PatchUtils.py, put it in the
Python search path or in the same directory as this file, and drag this file
onto PythonInterpreter.

BUT FIRST:  open both this file and PatchUtils.py, to verify that your download
program has successfully converted their line endings into MacOS form.  (Otherwise
this file will be one large comment line, and do nothing.)
"""

# To do:
# - auto-detect 2-space indents
# - read emacs file variables
# - let you save a new buffer as space-indented
# - let you see whether the file you're editing is space-indented
#
# Change history:
# 11/3/99 v. 2
# - handle mixed tab/space indented files
# - don't use the saved tabstop to convert (made the behavior too confusing)
# 10/13/99 v. 0.2
#  - detect eight-character indented files
#  - renamed to SpacesTabTranslationPatch
#  - changed to a patch file, using PatchUtils
# -  added some (not enough) comments
# 8/10/99
#  - initial release (released as PyIDESpaceTabTranslationPatch)


import os
import sys
import string
from PatchUtils import *

basePath = sys.exec_prefix + 'Mac:Tools:IDE:'

######################################################################
PyEdit = Patch(basePath, "PyEdit.py", "SpaceTranslationPatch")

PyEdit.InsertAfter('_wordchars = string.letters + string.digits + "_"',
"""TabifyFilenamePatterns = ['*.py']	# tabify files that match these patterns""")

PyEdit.InsertAfter("			self.run_as_main = 0",
"""		from fnmatch import fnmatch
		if filter(lambda pattern,path=path,fnmatch=fnmatch:fnmatch(path, pattern), TabifyFilenamePatterns):
			self.tabify()
""")

PyEdit.InsertBefore("class _saveoptions:",
'''def findFirstNot(s, c):
	"""Return the index of the first character in s that isn't c.
	If they're all c, return the length of the string."""
	for i in range(len(s)):
		if s[i] != c:
			return i
	return len(s) 

def computeIndentation(line, tabsize=8):
	"""Return the indentation of the line, and the offset of the first non-ws character (or the length of the line)"""
	column, index = 0, 0
	while len(line) > index and line[index] in '\\t ':
		if line[index] == ' ':
			column = column + 1
		else:
			column = (column + tabsize) / tabsize * tabsize
		index = index + 1
	return column, index

def contractInitialSpaces(s, indentation=4, tabsize=8):
	result = []
	for line in string.split(s, '\\r'):
		column, index = computeIndentation(line, tabsize=tabsize)
		tabs = column / indentation
		line = tabs * '\t' + (column - tabs * indentation) * ' ' + line[index:]
		result.append(line)
	return string.join(result, '\\r')

# string.expandtabs expands all the tabs, whereas we just want to
# expand the tabs at the beginning of each line (I think)
def expandInitialTabs(s, indentation=4, tabsize=None):
	result = []
	for line in string.split(s, '\\r'):
		col = findFirstNot(line, '\t')
		spaces = col * indentation
		tabs = 0
		if spaces:
			if tabsize:
				tabs = spaces / tabsize
				spaces = spaces - tabs * tabsize
			line = tabs * '\\t' + spaces * ' ' + line[col:]
		result.append(line)
	return string.join(result, '\\r')

def guessIndentation(text):
	"""Guess the indentation and tabsize for the text, based on how its lines are indented."""
	
	if string.find(text, '\\r\\t    ') >= 0:
		return 4, 8
	# Collect some simple statistics, to decide how many spaces make an indentation level.
	# The statistics are more general than what we now use, since it's simplest to collect them
	# that way, and since I'm expecting that we may want to tune the heuristics to attend to more
	# of them.
	indentations = [0] * 9
	tabsize = 8
	for line in string.split(text, '\\r'):
		indentation = computeIndentation(line, tabsize=tabsize)[0]
		if indentation < len(indentations):
			indentations[indentation] = indentations[indentation] + 1
	# Assume four spaces per tab, unless there's an indication that it's otherwise."
	indentation = 4
	# If there aren't any lines at 4 and there are at 8, then assume it's 8-stopped."
	if indentations[4] == 0 and indentations[8] > 0:
		indentation = 8
	if string.find(text, '\\t') < 0:
		tabsize = None
	return indentation, tabsize

''')

PyEdit.ReplaceLine("		data = self.editgroup.editor.get()",
"""		data = self.untabifiedtext()""")

PyEdit.InsertBefore("	def readwindowsettings(self):",
'''	def tabify(self):
		"""A file is ripe for tabifying if any line starts with at least four spaces.
		(The following code ignores the first line, but if that's the only indented line we
		probably shouldn't be considering it anyway.)"""
		if string.find(self.editgroup.editor.get(), '\\r  ') >= 0:
			self.tabified = 1	# we use this when the file is saved, to change it back
			indentation, tabsize = guessIndentation(self.editgroup.editor.get())
			self.tabsettings = (indentation, 1)
			self.editgroup.editor.settabsettings(self.tabsettings)
			self.tabifiedIndentation = indentation
			self.tabifiedTabSize = tabsize	# so we can expand back out when the file is saved
			self.editgroup.editor.set(contractInitialSpaces(self.editgroup.editor.get(), indentation=indentation, tabsize=tabsize or 8))
	
	def untabifiedtext(self):
		"""Retrieve the editor's text, with initial tabs replaced by spaces if it was tabified
		when it was read from a file."""
		data = self.editgroup.editor.get()
		if hasattr(self, 'tabified'):
			tabsize = self.tabifiedTabSize
			indentation = self.tabifiedIndentation
			# if the indentation has been changed, and it's one that we recognize, use that instead.
			if self.tabsettings[1] and self.tabsettings[0] in (4,8):
				indentation = self.tabsettings[0]
			data = expandInitialTabs(data, indentation=indentation, tabsize=tabsize)
		return data
	
''')


######################################################################

# All files have been patched in memory, so write to disk.
print

PyEdit.Write()

print "\nQuit the IDE and restart it to use the new functionality."
