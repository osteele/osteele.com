# This file patches PyConsole.py to add history substitution to the IDE.
# (See the module comment for more specific information.)
# Requires http://www.strout.net/python/mac/PatchUtils.py
#
# Version 2 released to the public domain 23 October 1999
# by Oliver Steele, steele@cs.brandeis.edu

""" CommandHistory patch, version 2

FEATURES

Ctl-uparrow in the console window grabs the previous command from the
buffer, and replaces the current command with it.  Repeated grabs move
backwards through the history until they reach the beginning of the window,
and then wrap around.

Ctl-downarrow grabs the next command, and wraps from the bottom to the top.

Return anywhere on the current input line executes all the text to the end
on the line.  (The old behavior was to execute the text up to the insertion
point, ignoring any following text.)

Return on a previous line appends that line (minus the prompt) to the current
input line.

Any other non-navigation key on a previous appends that line to the input line,
moves the selection to the corresponding position within the appended text,
and then types the key.

INSTALLATION INSTRUCTIONS

Retrieve http://www.strout.net/python/mac/PatchUtils.py, put it in the
Python search path or in the same directory as this file, and drag this
file onto PythonInterpreter.  """

# Change history:
# 10/23/99 2
#  -changed option-p to ctl-uparrow
# - added ctl-downarrow
# - typing in a previous line copies the line and moves the selection
# 10/13/99 0.2
# - changed '\r' to '\\r', '\t' to '\\t' in the patch script
# 10/9/99 0.1
# - initial release
#
# To do:
# - uparrow/downarrow should move past the prompt
# -Clear with no selection should erase the current input line
# - should Next and Previous skip repeated lines?

import os
import sys
import string
from PatchUtils import *

basePath = sys.exec_prefix + 'Mac:Tools:IDE:'

class ExtendedPatch(Patch):
	"""An extension to PatchUtils.Patch, that adds ReplaceLines for multiple-line search
	replacement."""
	
	def _FindLines(self, searchLines, callerName):
		lineno = None
		for i in range(len(self.lines)):
			if self.lines[i:i+len(searchLines)] == searchLines:
				if lineno is not None:
					raise callerName+"_NotUnique", searchLines[0][:-1] + '...'
				lineno = i
		if lineno is None:
			raise callerName+"_NotFound", searchLines[0][:-1] + '...'
		return lineno
	
	def ReplaceLines(self, searchString, replaceString):
		searchLines = map(lambda line:line + '\n', string.split(searchString, '\n'))
		replaceLines = map(lambda line:line + '\n', string.split(replaceString, '\n'))
		startline = self._FindLines(searchLines, "ReplaceLines")
		endline = startline + len(searchLines)
		# we've found it; now, replace this line with the given ones
		print "Substituting %d line(s) for lines %d:%d" % (len(replaceLines), startline, endline)
		self.lines[startline:endline] = replaceLines
		self.qtyLines = len(self.lines)

######################################################################
PyEdit = ExtendedPatch(basePath, "PyConsole.py", "CommandHistory")

PyEdit.ReplaceLines(
r"""			if char not in Wkeys.navigationkeys:
				self.checkselection()
			if char == Wkeys.enterkey:
				char = Wkeys.returnkey
			selstart, selend = self.getselection()""",
r"""			modifierKeys = modifiers & (Events.cmdKey | Events.shiftKey | Events.optionKey | Events.controlKey)
			selstart, selend = self.getselection()
			if char == Wkeys.enterkey:
				char = Wkeys.returnkey
			if char not in Wkeys.navigationkeys:
				if selend < self._inputstart:
					# Copy the whole line, with the prompt stripped
					lineno = self.ted.WEOffsetToLine(selstart)
					copystart, copyend = self.ted.WEGetLineRange(lineno)
					text = self.get()[copystart:copyend][:-1]
					for prompt in (sys.ps1, sys.ps2):
						if text[:len(prompt)] == prompt:
							text = text[len(prompt):]
							copystart = copystart + len(prompt)
							break
					insertionpos = len(self.get())
					self.ted.WESetSelection(insertionpos, insertionpos)
					self.ted.WEInsert(text, None, None)
					if char == Wkeys.returnkey:
						return
					# Move the selection to the corresponding position in the new line
					selstart = selstart + insertionpos - copystart
					selend = selend + insertionpos - copystart
					self.ted.WESetSelection(selstart, selend)
				self.checkselection()
				selstart, selend = self.getselection()
			if char == chr(30) and modifierKeys == Events.controlKey:	# cmd-uparrow
				self.previousCommand()
				return
			elif char == chr(31) and modifierKeys == Events.controlKey:	# cmd-downarrow
				self.previousCommand(delta=1)
				return""")

PyEdit.ReplaceLine("			self.ted.WEKey(ord(char), modifiers)",
r"""			if char != Wkeys.returnkey:
				self.ted.WEKey(ord(char), modifiers)""")

PyEdit.ReplaceLines(
r"""			self.updatescrollbars()
			if char == Wkeys.returnkey:""",
r"""			self.updatescrollbars()
			if char == Wkeys.returnkey:
				text = self.get()[selend:] + '\r'
				if '\r' in text:
					selstart = selend = selend + string.find(text, '\r')
					self.ted.WESetSelection(selstart, selend)
				self.ted.WEKey(ord(char), modifiers)""")

PyEdit.InsertAfter("				self._inputstart = selstart",
r'''				self.commandHistoryCursor = None
	
	def previousCommand(self, delta=-1):
		def commandLineText(line):
			"""Return the line stripped of the prompt, if it begins with one, else None."""
			if line[:len(sys.ps1)] == sys.ps1:
				return line[len(sys.ps1):]
			elif line[:len(sys.ps2)] == sys.ps2:
				command = line[len(sys.ps2):]
		# Retrieve the lines, ignoring the last one, which is the current input
		lines = string.split(self.get(), '\r')[:-1]
		# Retrieve the input strings, and remove lines that either aren't command
		# lines (None), or have empty inputs ('')
		lines = filter(None, map(lambda line,f=commandLineText:f(line), lines))
		if not lines:
			return
		# Either pick up where the last history command left off, or start at the
		# current line (which isn't a valid index position, but the % fixes this.
		index = getattr(self, 'commandHistoryCursor', None) or len(lines)
		# Then take one step, and wrap around
		index = (index + delta) % len(lines)
		command = lines[index]
		self.commandHistoryCursor = index
		selstart, selend = self.getselection()
		self.ted.WESetSelection(self._inputstart, len(self.get()))
		self.ted.WEInsert(command, None, None)''')

######################################################################

# All files have been patched in memory, so write to disk.
print

PyEdit.Write()

print "\nQuit the IDE and restart it to use the new functionality."
