# Module PySpaceEditor, alpha 5/21/99
# by Oliver Steele (steele@cs.brandeis.edu).

""" Module PySpaceEditor

This is a work in progress.  It's alpha software: use it at your own risk.

Importing this module into PythonIDE (the MacOS Python IDE) changes the behavior of
text windows such that the Tab key inserts four spaces, instead of a tab character.
It also changes "Shift left" and "Shift right" to recognize and insert, respectively, spaces
instead of tabs.  These changes make PythonIDE compatible with the behavior of emacs'
python-mode, and with IDLE; they're useful if you're editing code that was developed on
or is shared with developers on other platforms.

For compatibility with hard-tabbed files, the system recognizes files that contain a
carriage return followed by a tab ('\r\t'), and uses hard tabs instead of spaces in windows
opened on those files.   The behavior of the patched system is intended to be identical
to the behavior of the unpatched system in this case, except that with the patch, the
editor recognizes a sequence of spaces -- even in a window marked as hard-tabbed --
as being equivalent to a level of indentation.

At some point it should become possible to set this behavior (hard tab versus soft tab)
on a window-by-window basis, and to change the default.  For now, you can type:
	>>> PySpaceEditor.PySpaceEditor.use_hard_tabs = 1
to cause new files to use hard tabs.

Usage:  After PythonIDE has launched, type 'import PySpaceEditor' at its command line.
"""

# To do:
# - add UI for setting default, file to use hard vs. soft tabs
# - add UI for setting soft tab indentation?  maybe this should just be hardwired to 4
# - Generalize references to '\t' to accept soft tabs too:
#   - PyEdit.Editor._runselection
#   - PyEdit.getminindent
#   - Wtext.indentPat; this is used to recognize comments
#   - Wtext.PyEditor.click
# Notes:
# - WASTE documentation is at http://www.cs.dartmouth.edu/~ngm/waste/reference.html

import Qd
import Res
import Events
import string
import Wkeys
import PyFontify
import Wtext
import W
from Wtext import GetPortFontSettings, SetPortFontSettings

# Implementation note: in order to make developing this easier, and to make it work as a patch,
# we define a subclass of (the original) Wtext.PyEditor, and rebind Wtext.PyEditor and
# W.PyEditor to that subclass.  It's intended that when this code is complete, the methods
# in the PySpaceEditor class could supplement and replace the corresponding methods in
# PyEditor, and this would cease to exist as a separate file and class.

PyEditor = globals().get("PyEditor", Wtext.PyEditor)

class PySpaceEditor(PyEditor):
	indent_offset = 4	# spaces per program indentation level
	use_hard_tabs = 0	# false to indent with indent_offset spaces; true to use '\t'
	
	def __init__(*args, **keys):
		self = args[0]
		apply(PyEditor.__init__, args, keys)
		if string.find(self.get(), '\r\t') >= 0:
			self.use_hard_tabs = 1
	
	def get_selection(self):
		selstart, selend = self.ted.WEGetSelection()
		selstart, selend = min(selstart, selend), max(selstart, selend)
		return selstart, selend
	
	def currentline(self):
		selstart, selend = self.get_selection()
		pos, dummy = self.ted.WEFindLine(selstart, 0)
		lineres = Res.Resource('')
		self.ted.WECopyRange(pos, selstart, lineres, None, None)
		return lineres.data
	
	def get_indentation(self, line, allcolumns=0):
		columncount = 0
		tabwidth = self.tab_width_in_spaces()
		for c in line:
			if c == ' ':
				columncount = columncount + 1
			elif c == '\t':
				columncount = (columncount + tabwidth) / tabwidth * tabwidth
			elif not allcolumns:
				break
		if self.use_hard_tabs:
			tab_width = self.tab_width_in_spaces()
		else:
			tab_width = self.indent_offset
		tabcount = columncount / tab_width
		return tabcount
	
	def tab_width_in_spaces(self):
		tabsize, tabmode = self.tabsettings
		if not tabmode:
			(font, style, size, color) = self.getfontsettings()
			port = self._parentwindow.wid.GetWindowPort()
			savesettings = GetPortFontSettings(port)
			SetPortFontSettings(port, (font, style, size))
			tabsize = Qd.StringWidth(' ' * tabsize)
			SetPortFontSettings(port, savesettings)
		return tabsize
	
	def tabbing(self, tabcount):
		if self.use_hard_tabs:
			return '\t' * tabcount
		else:
			return ' ' * tabcount * self.indent_offset
	
	def key(self, char, event):
		(what, message, when, where, modifiers) = event
		if modifiers & Events.cmdKey and not char in Wkeys.arrowkeys:
			return
		if char == '\r':
			selstart, selend = self.get_selection()
			lastchar = chr(self.ted.WEGetChar(selstart-1))
			if lastchar <> '\r' and selstart:
				line = self.currentline()
				tabcount = self.extratabs(line)
				self.insert('\r' + self.tabbing(tabcount))
			else:
				self.ted.WEKey(ord('\r'), 0)
		elif char == '\t':
			if self.use_hard_tabs:
				self.ted.WEKey(ord(char), 0)
			else:
				line = self.currentline()
				tabwidth = self.tab_width_in_spaces()
				column = self.get_indentation(line, 1)
				newcolumn = (column + tabwidth) / tabwidth * tabwidth
				self.insert(' ' * (newcolumn - column))
		elif char in ')]}':
			self.ted.WEKey(ord(char), modifiers)
			self.balanceparens(char)
		else:
			self.ted.WEKey(ord(char), modifiers)
		if char not in Wkeys.navigationkeys:
			self.changed = 1
		self.selchanged = 1
		self.updatescrollbars()
	
	def extratabs(self, line):
		tabcount = self.get_indentation(line)
		last = 0
		cleanline = ''
		tags = PyFontify.fontify(line)
		# strip comments and strings
		for tag, start, end, sublist in tags:
			if tag in ('string', 'comment'):
				cleanline = cleanline + line[last:start]
				last = end
		cleanline = cleanline + line[last:]
		cleanline = string.strip(cleanline)
		if cleanline and cleanline[-1] == ':':
			tabcount = tabcount + 1
		else:
			# extra indent after unbalanced (, [ or {
			for open, close in (('(', ')'), ('[', ']'), ('{', '}')):
				count = string.count(cleanline, open)
				if count and count > string.count(cleanline, close):
					tabcount = tabcount + 2
					break
		return tabcount
	
	def domenu_shiftleft(self):
		self.expandselection()
		selstart, selend = self.get_selection()
		snippet = self.getselectedtext()
		lines = string.split(snippet, '\r')
		for i in range(len(lines)):
			if lines[i][:1] == '\t':
				lines[i] = lines[i][1:]
			elif lines[i][:self.indent_offset] == ' ' * self.indent_offset:
				lines[i] = lines[i][self.indent_offset:]
		snippet = string.join(lines, '\r')
		self.insert(snippet)
		self.ted.WESetSelection(selstart, selstart + len(snippet))
	
	def domenu_shiftright(self):
		self.expandselection()
		selstart, selend = self.get_selection()
		snippet = self.getselectedtext()
		lines = string.split(snippet, '\r')
		for i in range(len(lines) - (not lines[-1])):
			lines[i] = self.tabbing(1) + lines[i]
		snippet = string.join(lines, '\r')
		self.insert(snippet)
		self.ted.WESetSelection(selstart, selstart + len(snippet))

# Swap in PySpaceEditor for PyEditor
Wtext.PyEditor = PySpaceEditor
W.PyEditor = PySpaceEditor
