Offset - Vertical alignment
---------------------------

Different browsers show images offset from the baseline quite differently. Currently LatexRender
uses align=absmiddle to vertically align images, but Mike Boyle has come up with a more 
sophisticated way of aligning images using CSS methods, such as style="vertical-align:-2pt". See
http://boyle.black-holes.org/dokuwiki_latex for the details.

The files in the offset beta directory uses Mike Boyle's code to give the result in an example 
page so that users may see if the method is useful to them. This is an experimental method so 
is labelled beta.

The changes in the code in latex.php and class.latexrender.php have been marked with // offset.

The process works by
1. adding to the tex file a formula box so latex can calculate the vertical offset in points.
The result is written to a depth file (class.latexrender.php)
2. The depth file is read and the offset (such as 10.11122pt) is added to the name of the image 
file saved in /pictures. This allows the information to be cached.
The depth file is then deleted (class.latexrender.php)
3. The offset information is read so that eg style="vertical-align:-10.11122pt" can be used for 
the placement of the image (latex.php)

Clearly extra processing is involved even for the cached images. Whether or not this proves to 
be a  problem will depend on the individual circumstances.
There may be a problem if /pictures contains a large number of files as the appropriate file has 
to be found even though its exact name may not be known. This is done in the FindFile function in 
class.latexrender.php so perhaps this function could be speeded up. It was felt better than 
saving the information in yet another file, which would double the number of files in /pictures 
or using a database which could be faster but just adds further complications.

Summary of extra/changed code:
class.latexrender.php
Lines 139 & 160: use of FindFile function
Lines 171 - 182: FindFile function
Lines 306 - 312: read depth info and add to file name
Line 337:        delete depth file

latex.php
Lines 70 - 77:   read depth info from filename
Line 85:         use eg style="vertical-align:-10.11122pt" instead of align=absmiddle

latexrender-plugin.php
No changes
                 
Steve Mayer steve@sixthform.info