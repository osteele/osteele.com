 D r u T e X

1. Installation
2. Configuration
3. Usage
4. Additional modules
5. Developer notes
6. Author


1. Installation
---------------

1) Download the DruTeX module from the Drupal website and unpack it to the modules directory. You now have a directory modules/drutex.

2) Go to the module admin section and enable "drutex".

3) Go to the input-filters admin section and create a new input format. You can choose an arbitrary name, but "DruTeX" would make sense. When you create a new format, you can select an input filter from a list: Just choose DruTeX.

4) Go again to the input-filters admin section, and select "configure" for the newly added input format. Click on the "configure" tab, and you are able to adjust many settings of DruTeX.

5) Go and test DruTeX: Create a new node, select your new input format, and try to render something, e.g. just try $x^2$.


2. Configuration
----------------

All configuration is done on the input filter settings page.

*Submodules*

You can activate the following facilities of DruTeX:
* LaTeX to HTML: Converts many LaTeX commands and environments to plain HTML.
* LaTeX Renderer: Provides different environments to create rendered images (especially maths). For most of the users, this is the most important feature.
* Line break converter: Converts line breaks into HTML (i.e. <br> and <p> tags).
* PDF Generator: Allows to generate pdf-versions of a node (with decent html2latex features).

Further submodules are available and can be made ready for activation by copying the submodule files from modules/drutex/contrib to modules/drutex. For more, see chapter 4.

*Base Settings*

In the base settings you can set the paths for temporary dir, the image directory and the image url.

*LaTeX Renderer*

Here you can adjust the LaTeX template, DPI (Resolution) and conversion command used for the LaTeX-to-Image conversion. If you know LaTeX well, you may want to create your own template. The templates reside in modules/drutex/templates/render and are not difficult to understand (if you know LaTeX well).

*Line break converter*

LaTeX treates a single new line not as a new line, but only as space - while Drupal would interpret it as a real newline. Two or more new lines, that means a least one empty line, are treated as new paragraph by both, Drupal and LaTeX. So the only critical point are single new lines.

You can choose between Drupal- and LaTeX-interpretation of new lines. You could also completely deactivate this submodule.

*PDF Generator*

Here you can also adjust the LaTeX template and the conversion command used for the generation of the pdf files. The templates reside in modules/drutex/templates/pdf - you may want to edit this if you know LaTeX well.

*Security restrictions*

If you activate this, you can define a list of LaTeX commands and environments that are allowed to get executed. Then, all other commands and environments would cause DruTeX to stop processing the potentially dangerous code.


3. Usage
--------

*Rendering LaTeX / Math*

Writing maths works as in LaTeX. Use dollar signs to enclose inline math, e.g. $x^2$. Examples for paragraph math are \[ x^2 \] and $$x^2$$ (both variants are equivalent). There is also a display-style inline math environment, compare $\sum_{k=1}^\infty \frac{1}{k}$ and $!\sum_{k=1}^\infty \frac{1}{k}$.

To make a dollar sign, you have to type \$ as in LaTeX!

Arbitrary LaTeX code can be rendered as in this example: <tex>Let $x^2$ be a natural number.</tex>

There are also more elaborate environments to write math - they support auto-numbering, referencing and more. These environments are <equation> and <equations>. They both support the same attributes.

E.g. rendering an equation with a different resolution: <equation dpi="200">e^{i \cdot \pi} = -1.</equation>

To give the equation automatically a number, you have to give it an id: <equation id="euler">e^{i \cdot \pi} = -1.</equation>

You now can produce a link to that equation by typing \ref{euler}, or even better (\ref{euler}).

You can also give it a name instead of a number: <equation id="euler" name="Euler's Identity">e^{i \cdot \pi} = -1.</equation>

An example for the <equations> environment is given by:
<equations>
  \int \ln(x) \;dx
    &= \int 1 \cdot \ln(x) \;dx \\
    &= x \cdot \ln(x) - \int x \cdot \frac{1}{x} \;dx \\
    &= x \cdot \ln(x) - x
</equations>

The spaces on the left are optional.


*PDF Generator*

The pdf version of a node can be retrieved by accessing drutex/pdf/nid where nid is the node id. But only users with the "access pdf files" flag can access this link! You can configure this on the access controal page admin/access. There is also the possibility to get the LaTeX source by accessing drutex/tex/nid.

*Verbatim*

To prohibit some text from being processed by DruTeX, you can use <code> and <notex>, e.g. <notex>$x^2$</notex>.


4. Additional modules
---------------------

Copy the submodule from modules/drutex/contrib to modules/drutex. Submodules availabe:
* drutex_blahtex.inc:     MathML support through blahtex (in development). See also http://blahtex.org
* drutex_eukleides.inc:   Eukleides support, for geometric figures (in development). See also http://eukleides.org
* drutex_example.inc:     Example module for developers.


5. Developer notes
------------------

DruTeX has an own submodule system, alldefined in drutex.module. modules/drutex/contrib/drutex_example.inc will explain how to write a submodule. There is also a Drupal-wide hook drutex2html, which returns entities as in subhook_node2html, see drutex_example.inc.

To test if a submodule is marked active in a given input format, use drutex_submodule_is_active(), e.g. drutex_submodule_is_active('security', $format).


6. Authors
---------

* Daniel Gutekunst (dfg@d-f-g.net)
* Steven Jones (darthsteven@gmail.com)
