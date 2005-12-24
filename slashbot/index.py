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
print "</head><body><div class=\"column\"><p>" + s + "</p>"
print "<div style='text-align: justify'>"
print "<a href=\".\">Reload</a> | "
print "<a href=\"http://osteele.com\">Home</a>"
print "</div>"
print "</div>"
print """<script type="text/javascript"><!--
google_ad_client = "pub-7558884554835464";
google_ad_width = 728;
google_ad_height = 15;
google_ad_format = "728x15_0ads_al_s";
google_ad_channel ="9468724158";
//--></script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>"""
print "</body></html>"
