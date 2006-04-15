<? 
global $user_level;
?>
	<div id="headerOptions">
		Color Options
		<span><a href="javascript:togglePanel('colourControl');"><img src="<? echo get_template_directory_uri(); ?>/images/controlclose.gif" alt="Close Window" /></a></span>
	</div>

	<h4 id="itemTitle"></h4>
	
	<div id="ColourMod">
	
	<!--

		ColourMod Plug-N-Play v2.2
		DHTML Dynamic Color Picker/Selector
		© 2005 ColourMod.com
		Design/Programming By Stephen Hallgren (www.teevio.net)
		Produced By The Noah Institute (www.noahinstitute.org)
		Manual: http://colourmod.com/blog/?page_id=37

		Modified for Wordpress by: Andy Peatling ~ http://www.cssdev.com/

	-->

	<div id="cmDefault">

		<div id="cmColorContainer" class="cmColorContainer"></div>
		<div id="cmSatValBg" class="cmSatValBg"></div>
		<div id="cmDefaultMiniOverlay" class="cmDefaultMiniOverlay"></div>
		<div id="cmSatValContainer">
			<div id="cmBlueDot" class="cmBlueDot"></div>
		</div>
		<div id="cmHueContainer">
			<div id="cmBlueArrow" class="cmBlueArrow"></div>
		</div>
		<div id="cmClose">
			<input type="text" name="cmHex" id="cmHex" value="FFFFFF" maxlength="6" size="9" style="width: auto; margin-top: 10px;" />
		</div>
		<div style="display:none">
			<input type="text" name="cmHue" id="cmHue" value="0" maxlength="3" />
		</div>
		<a href="http://www.colourmod.com" target="_blank" title="ColourMod - Dynamic Color Picker" class="cmLink">&copy; ColourMod.com</a>

		</div>

	</div>

	<!-- End ColourMod Code -->

	<dl>
		<dt><a href="javascript: toggleOptions('cHeader');" title="Toggle Header Options">Header &raquo;</a></dt>
		<dd style="display: none;" id="cHeader">
			<a href="javascript:changeColor('Header Background Color', '#header', 'backgroundColor', true, '', this);">Background Color</a> &rarr;<br />
			<a href="javascript:changeColor('Header Text Color', '#header;h1 a;h1 a:visited', 'color', true, '', this);">Text Color</a> &rarr;
		</dd>
		
		<dt><a href="javascript:toggleOptions('cMenuLinks');" title="Toggle Menu Link Options">Menu Links &raquo;</a></dt>
		<dd style="display: none;" id="cMenuLinks">
			<a href="javascript:changeColor('Menu Link Text Color', '#topMenu a;#topMenu', 'color', true, '', this);">Link Text Color</a> &rarr;<br /> 
			<a href="javascript:changeColor('Menu Link Hover Text Color', '#topMenu a:hover;#topMenu a.menuSelected', 'color', true, '', this);">Link Hover Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Menu Link Hover Background Color', '#topMenu a:hover;#topMenu a.menuSelected', 'backgroundColor', true, '', this);">Link Hover Background Color</a> &rarr;<br />
		</dd>
		
		<dt><a href="javascript:toggleOptions('cMenuSections');" title="Toggle Menu Section Options">Menu Sections &raquo;</a></dt>
		<dd style="display: none;" id="cMenuSections">
			<a href="javascript:changeColor('Menu Section Background Color', '.menuSection', 'backgroundColor', true, '', this);">Background Color</a> &rarr;<br /> 
			<a href="javascript:changeColor('Menu Section Text Color', '.menuSection', 'color', true, '', this);">Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Menu Section Text Color', '.menuSection h2', 'color', true, '', this);">Main Heading Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Menu Section Link Text Color', '.menuSection a;.menuSection a:visited', 'color', true, '', this);">Link Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Menu Section Link Hover Text Color', '.menuSection a:hover', 'color', true, '', this);">Link Hover Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Menu Section Link Hover Background Color', '.menuSection a:hover', 'backgroundColor', true, '', this);">Link Hover Background Color</a> &rarr;<br />
		</dd>
		
		<dt><a href="javascript:toggleOptions('cContent');" title="Toggle Main Content Options">Main Content &raquo;</a></dt>
		<dd style="display: none;" id="cContent">
			<a href="javascript:changeColor('Main Content Background Color', '#mainContent', 'backgroundColor', true, '', this);">Background Color</a> &rarr;<br />			
			<a href="javascript:changeColor('Main Content Text Color', '#mainContent', 'color', true, '', this);">Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Main Content Post Heading Text Color', '#mainContent a.postHeading;#mainContent a.postHeading:hover;h2;h2 a', 'color', true, '', this);">Post Heading Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Main Content Link Text Color', 'a;a:visited', 'color', true, '', this);">Link Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Main Content Link Hover Text Color', 'a:hover', 'color', true, '', this);">Link Hover Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Main Content Link Hover Background Color', 'a:hover', 'backgroundColor', true, '', this);">Link Hover Background Color</a> &rarr;<br />
		</dd>
		
		<dt><a href="javascript:toggleOptions('cDates');" title="Toggle Date &amp; Tag Options">Dates &amp; Tags &raquo;</a></dt>
		<dd style="display: none;" id="cDates">
			<a href="javascript:changeColor('Dates &amp; Tags Background Color', '.theDate;.categories a', 'backgroundColor', true, '', this);">Background Color</a> &rarr;<br /> 
			<a href="javascript:changeColor('Dates &amp; Tags Text Color', '.theDate;.categories a', 'color', true, '', this);">Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Dates &amp; Tags Hover Text Color', '.theDate:hover;.categories a:hover', 'color', true, '', this);">Hover Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Dates &amp; Tags Hover Background Color', '.theDate:hover;.categories a:hover', 'backgroundColor', true, '', this);">Hover Background Color</a> &rarr;<br />
		</dd>
		
		<dt><a href="javascript:toggleOptions('cComments');" title="Toggle Comment Options">Comments &raquo;</a></dt>
		<dd style="display: none;" id="cComments">
			<a href="javascript:changeColor('Reply Form Background Color', '#commentForm', 'backgroundColor', true, '', this);">Add Reply Form Background Color</a> &rarr;<br /> 
			<a href="javascript:changeColor('Reply Form Heading Color', '#commentForm h2', 'color', true, '', this);">Add Reply Form Heading Text Color</a> &rarr;<br />			
			<a href="javascript:changeColor('Reply Form Text Color', '#commentForm', 'color', true, '', this);">Add Reply Form Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Posted Reply Background Color', '.alt', 'backgroundColor', true, '', this);">Posted Reply Background Color</a> &rarr;<br />
			<a href="javascript:changeColor('Posted Reply Text Color', '.alt', 'color', true, '', this);">Posted Reply Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Posted Reply Link Text Color', '.alt a', 'color', true, '', this);">Posted Reply Link Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Posted Reply Link Hover Text Color', '.alt a:hover', 'color', true, '', this);">Posted Reply Link Hover Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Posted Reply Link Hover Background Color', '.alt a:hover', 'backgroundColor', true, '', this);">Posted Reply Link Hover Background Color</a> &rarr;<br />
		</dd>
		
		<dt><a href="javascript:toggleOptions('cFooter');" title="Toggle Footer Options">Footer Overview &raquo;</a></dt>
		<dd style="display: none;" id="cFooter">
			<a href="javascript:changeColor('Footer Overview Background Color', '#overview', 'backgroundColor', true, '', this);">Background Color</a> &rarr;<br /> 
			<a href="javascript:changeColor('Footer Overview Text Color', '#overview', 'color', true, '', this);">Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Footer Overview Heading Color', '#overview h2', 'color', true, '', this);">Main Headings Text Color</a> &rarr;<br />			
			<a href="javascript:changeColor('Footer Overview Link Text Color', '#overview a', 'color', true, '', this);">Link Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Footer Overview Link Hover Text Color', '#overview a:hover', 'color', true, '', this);">Link Hover Text Color</a> &rarr;<br />
			<a href="javascript:changeColor('Footer Overview Link Hover Background Color', '#overview a:hover', 'backgroundColor', true, '', this);">Link Hover Background Color</a> &rarr;<br />
		</dd>

	</dl>
	
	<div id="saveSettings">
		<? if($user_level > 8) { ?>
		<a href="<? echo get_template_directory_uri(); ?>/savecolors.php?mode=default" onclick="clearColors(false);" title="Reset colors to their original settings before any modifications (blue and orange).">Reset Original Colors</a> &raquo; | 
		<a href="<? echo get_template_directory_uri(); ?>/savecolors.php?mode=setNew" title="Save your current color settings as the default for all visitors who have not made their own modifications.">Save as Default</a> &raquo;
		<? } else { ?>
		<a href="javascript:clearColors(true);" title="Reset your personal colors to the site's default color settings.">Reset My Colors</a> &raquo;
		<? } ?>	
	</div>

</div>

	<script type="text/javascript">
		<? /* This script handles dragging of color control window. DON'T MODIFY!! */ ?>
		var theHandle = $("headerOptions");
		var theRoot = $("colourControl");
		Drag.init(theHandle, theRoot);
	</script>

