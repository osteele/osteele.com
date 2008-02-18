I have now managed to make LatexRender as a plugin which means that it 
won’t be necessary to edit WordPress files. Before you get too excited this assumes

a) You are using at least WordPress 1.2 (which uses plugins) 
b) You have the latest files which don’t eat backslashes (detailed on page 2) 
If not, you can still use the method in 5th August’s post (readme_noplugin.txt). 
Also you will need to put some information about where to find files in the latexrender files.

1. You need to have LaTeX and ImageMagick (the latter also requires Ghostscript) installed on 
your server. If this is not possible, then try mimeTeX (see 8. below)

2. latexrender-plugin.php
Ensure that 
include_once('/home/path_to/wordpress/latexrender/latex.php');
contains the correct path to where you have put latex.php

3. latex.php
Ensure that
$latexrender_path = "/home/domain_name/public_html/path_to/latexrender";
and
$latexrender_path_http = "/path_to/latexrender"; 
contain the correct path to where your latexrender files are to go

4. class.latexrender.php
Ensure that
var $_latex_path = "/usr/bin/latex";
var $_dvips_path = "/usr/bin/dvips";
var $_convert_path = "/usr/bin/convert";
var $_identify_path = "/usr/bin/identify";
point to the right places.

For Windows the paths in class.latexrender.php must use \\ or / not just a single \
For example
var $_latex_path = "C:\\texmf\\miktex\\bin\\latex.exe";
or
var $_latex_path = "C:/texmf/miktex/bin/latex.exe";

5. Create a new latexrender folder in your Wordpress folder, with sub folders tmp and pictures. 
These 2 folders must be chmod to 777. Upload latex.php and class.latexrender.php to the 
latexrender folder

6. If you are happy to let commenters use LaTeX (but beware spammers) then in latexrender-plugin.php
remove // from the line:
// add_filter('comment_text', 'addlatex');

7. Upload latexrender-plugin.php to WordPress’s plug-in folder 
(/wp-content/plugins) and activate it in the Plugins menu

LaTeX should now be enabled! Use [tex] and [/tex] to surround your maths.
There’s a tex button to do this for you if you aren't using a visual rich editor. 

8. No access to LaTeX? Then get mimeTeX
You need a latexrender folder inside WordPress and a subfolder called pictures which must be 
chmod to 777.
In mimetex.php you need to put in your paths for
$mimetex_path = "/home/domain_name/public_html/cgi-bin/mimetex.cgi";
$mimetex_path_http = "http://domain_name/mimetex";
$mimetex_cgi_path_http="http://domain_name/cgi-bin/mimetex.cgi";
$pictures_path = "/home/domain_name/public_html/mimetex/pictures";

Then upload mimetex.php to the latexrender folder. Upload mimetex_plugin.php 
to the plugins folder and activate it.

--------------------------------
Steve Mayer steve@sixthform.info