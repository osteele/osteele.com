#!/usr/bin/python
from markov import gen

print "Content-type: text/html"
print
s = gen(open('flash.txt'))
title = "Flash Troll Generator"
print "<html><head><title>" + title + "</title>"
print '<link href="style.css" rel="stylesheet" type="text/css">'
print '''<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-202010-1";
urchinTracker();
</script>'''
print "</head><body><p>" + s
print "</p><br/><a href='.'>Reload</a></body></html>"

