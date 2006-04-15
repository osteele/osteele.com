<?
	require('../../../wp-blog-header.php');
	
	$page_type = $durable->option['pagestyle'];
	$align = $durable->option['alignment'];
	switch($page_type){case "ripped":$header_graphic = "header_tear.png";$body_graphic = "body_tear.png";$graphic_height = "22px";break;case "solid":$header_graphic = "spacer.gif";$body_graphic = "spacer.gif";$graphic_height = "35px";break;}

	header("content-type: text/css");
?>

/*  
Theme Name: Durable
Theme URI: http://cssdev.com/durable
Description: Durable is a wordpress theme by <a href="http://www.cssdev.com">Andy Peatling</a> with a difference, let your readers be in control of colors and settings.<br /><a href="http://www.cssdev.com/durable/check.php?ver=0.2">Check for Updates</a> &raquo;
Version: v0.2
Author: Andy Peatling
Author URI: http://cssdev.com/

 NOTE: Due to the way the color selector works, 
 grouping of user modifiable declarations is not possible.
 This means some declaration redundancy is inevitable.

*/

/**
 * Structural Components
 */

* {
	outline: none;
}

body {
	margin: 0;
	padding: 0 0 10px 0;
	width: 90%;
	min-width: 760px;
	line-height: 135%;
	color: #666;
	background: #fff;
	margin: 0 auto 0 auto;
	font-family: verdana, tahoma, arial, sans-serif;
	font-size: 13px;
}

#header {
	height: 87px;
	padding: 20px 0 0 0;
	background-color: <? echo $durable->option['header_bgclr']; ?>;
	color: <? echo $durable->option['header_txtclr']; ?>;
	text-align: center;
	margin: 0 0 10px 0;
}

body > #header {
	height: 95px;
	background-image: url(images/<? echo $header_graphic; ?>);
}

#topMenu {
	height: 20px;
	padding: 5px;
	text-align: center;
	font-family: georgia, times, serif;
	color: <? echo $durable->option['menulinks_lnktxtclr']; ?>;
	font-size: 16px;
	margin: 10px 0 0 0;
}

#overview {
	background-color: <? echo $durable->option['footer_bgclr']; ?>;
	color: <? echo $durable->option['footer_txtclr']; ?>;
	padding: 0 15px 30px 15px;
	margin: 10px 0 0 0;
}

#page > #overview {
	padding: 15px 15px 30px 15px;	
}

#mainContent {
	background: <? echo $durable->option['maincontent_bgclr']; ?>;
	padding: 0 20px 0 20px;
	margin: 10px 0 10px 0;
	color: <? echo $durable->option['maincontent_txtclr']; ?>;
}

#page > #mainContent {
		padding: 0 20px 20px 20px;
		margin: 20px 0 15px 0;
}

.menuSection {
	padding: 20px 20px 0 20px;
	margin: 0 0 20px 0;
	height: auto;
	background: <? echo $durable->option['menusections_bgclr']; ?>;
	color: <? echo $durable->option['menusections_txtclr']; ?>;
}

#sidebar > .menuSection {
	padding: 20px 20px 0 20px;
}

#footer {
	text-align: center;
}

.column {
	float: left;
	width: 55%;
	margin: 0 2% 0 2%;
}

.menuSection .column {
	width: 28%;
}

.column#postsColumn {
	width: 35%
}

#links .column {
	width: auto;
	margin-right: 15px;
}

#sidebar div.holder {
	float: left;
	margin: 0 30px 0 0;
	list-style-type: none;
}

.navigation {
	text-align: center;
	margin: 0 0 15px 0;	
}

.postMeta { 
	float: right;
	width: 180px;
	margin: 45px 10px 15px 30px;
}

.theDate {
	width: 45px;
	height: 43px;
	margin: 0 10px 10px 0;
	padding: 3px 0 0 0;
	float: left;
	text-align: center;
	font-family: georgia, times, serif;
	background: <? echo $durable->option['datestags_bgclr']; ?>;
	color: <? echo $durable->option['datestags_txtclr']; ?>;
}

.theDate .theMonth, .theDate .theDay {
	display: block;
	font-size: 14px;
}

.theDate .theDay {
	font-size: 25px;
	margin: -2px 0 0 0;
}

#overview .date, #search .date, .postMeta {
	font-size: 11px;
}

.entry {
	clear: left;
	margin: 10px 0 0 0;
}

.post {
	margin-right: 220px;
	text-align: <? echo $align; ?>;
}

.mini-post {
	text-align: <? echo $align; ?>;
	float: left;
	width: 45%;
	margin: 30px 4% 0 0;
}

#commentForm {
	padding: 0 20px 0 20px;
	background: <? echo $durable->option['comments_rplyfrmbgclr']; ?>;
	margin: 30px 0 0 0;
}

#colourControl {
	position: absolute;
	width: 650px;
	height: 360px;
	background: url(images/controlmainback.gif) repeat-y top left #fff;
	border: 3px solid #ddd;
	overflow: hidden;
}

#colourControl #headerOptions {
	background: url(images/controlheader.gif) repeat-x #1676f9;
	font-weight: bold;
	cursor: move;
	height: 26px;
	color: #fff;
	padding: 8px 0 0 15px;
}

#headerOptions span {
	position: absolute;
	right: 10px;
	top: 8px;
}

.topTear, .bottomTear {
	height: <? echo $graphic_height; ?>;
}

.topTear {
	margin: -20px -20px 15px -20px;
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<? echo get_template_directory_uri(); ?>/images/<? echo $body_graphic; ?>', sizingMethod=crop );
}

.topTear[class] {
	filter: none;
	background: url(images/<? echo $body_graphic; ?>) repeat-x left top;	
}

.bottomTear {
	margin: 15px -20px -20px -20px;
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<? echo get_template_directory_uri(); ?>/images/<? echo $header_graphic; ?>', sizingMethod=crop );
}

.bottomTear[class] {
	filter: none;
	background: url(images/<? echo $header_graphic; ?>) repeat-x;		
}

.align-right {
	text-align: right;
}

/**
 * Headings
 */

h1, h2, h3, h4, h5, h6 {
	line-height: 110%;
}

h1 {
	margin: 0;
	font-size: 32px;
	font-family: "Trebuchet MS", arial, verdana, tahoma, sans-serif;
	font-weight: normal;
	cursor: pointer;
}

h2 {
	margin: 2px 0 0 0;
	font-family: "Trebuchet MS", arial, verdana, tahoma, sans-serif;
	font-size: 20px;
}

.menuSection h2 {
	color: <? echo $durable->option['menusections_hdgclr']; ?>;
	margin: 0 0 15px 0;
}

.menuSection h3 {
	font-size: 14px;
	margin: 10px 0 0 0;
}

#links h2 {
	margin: 0;
}

h2.pagetitle {
	font-family: georgia, times, serif;
	font-size: 20px;
	color: <? echo $durable->option['maincontent_txtclr']; ?>;
	text-align: center;
	margin: 0 0 8px 0;
}

#mainContent h3, h3#comments {
	font-family: "Trebuchet MS", arial, verdana, tahoma, sans-serif;
	color: #3fbcec; /* NEEDS TO BE SELECTABLE */
	margin: 15px 0 10px 0;
	font-size: 18px;
}

#overview h2 {
	color: <? echo $durable->option['footer_hdgclr']; ?>;
}

#commentForm h2 {
	color: <? echo $durable->option['comments_rplyfrmhdgtxtclr']; ?>;
	margin: 0 0 15px 0;
}

#colourControl h4 {
	float: right;
	font-family: georgia, times, serif;
	font-size: 18px;
	width: 50%;
	text-align: center;
	margin: 15px 0 0 0;
	padding: 0 10px 0 0;
}


/**
 * Anchors
 */

a {
	color: <? echo $durable->option['maincontent_lnktxtclr']; ?>;
}

a:visited {
	color: <? echo $durable->option['maincontent_lnktxtclr']; ?>;	
}

a:hover {
	background: <? echo $durable->option['maincontent_lnkhvrbgclr']; ?>;
	color: <? echo $durable->option['maincontent_lnkhvrclr']; ?>;
}

h1 a {
	color: <? echo $durable->option['header_txtclr']; ?>;
	text-decoration: none;
}

h1 a:visited {
	color: <? echo $durable->option['header_txtclr']; ?>;
	text-decoration: none;
}

h1 a:hover {
	color: <? echo $durable->option['header_txtclr']; ?>;
	text-decoration: none;
	background: none;
}

#topMenu a {
	color: <? echo $durable->option['menulinks_lnktxtclr']; ?>;
	padding: 3px 7px 7px 7px;
	text-decoration: none;
	font-size: 16px;
	margin: 0 5px 0 5px;
}

#topMenu a:hover {
	background-color: <? echo $durable->option['menulinks_lnkhvrbgclr']; ?>;
	color: <? echo $durable->option['menulinks_lnkhvrclr']; ?>;
}

#topMenu a.menuSelected { /* Need to duplicate for colormod */
	background-color: <? echo $durable->option['menulinks_lnkhvrbgclr']; ?>;
	color: <? echo $durable->option['menulinks_lnkhvrclr']; ?>;
}

.menuSection a {
	color: <? echo $durable->option['menusections_lnktxtclr']; ?>;
}

.menuSection a:visited {
	color: <? echo $durable->option['menusections_lnktxtclr']; ?>;
}

.menuSection a:hover {
	background: <? echo $durable->option['menusections_lnkhvrbgclr']; ?>;
	color: <? echo $durable->option['menusections_lnkhvrclr']; ?>;
}

#mainContent a.postHeading {
	text-decoration: none;
	color: <? echo $durable->option['maincontent_hdgclr']; ?>;
}

#mainContent a.postHeading:hover {
	background: none;
	color: <? echo $durable->option['maincontent_hdgclr']; ?>;
}

.categories a {
	font-size: 11px;
	background: <? echo $durable->option['datestags_bgclr']; ?>;
	color: <? echo $durable->option['datestags_txtclr']; ?>;
	padding: 2px 5px 2px 5px;
	text-decoration: none;
}

.categories a:hover {
	color: <? echo $durable->option['datestags_lnkhvrtxtclr']; ?>;
	background: <? echo $durable->option['datestags_lnkhvrbgclr']; ?>;
}

.categories a:visited {
	color: <? echo $durable->option['datestags_txtclr']; ?>;
}

.categories#mCats a {
	font-size: 14px;
	font-family: georgia, times, serif;
	margin: 0 5px 5px 0;
	padding: 2px 6px 2px 6px;
	display: block;
	float: left;
}

.numComments a {
	color: #666; /* NEED TO MAKE THIS SELECTABLE */
	font-size: 16px;
	font-family: georgia, times, serif;
	margin: 6px 0 0 0;
}

.numComments a:visited {
	color: #666;
}

.numComments a:hover {
	background: #666; /* NEED TO MAKE THIS SELECTABLE */
	color: white;
}

.alt a {
	color: <? echo $durable->option['comments_rplylnktxtclr']; ?>;
}

.alt a:hover {
	background: <? $durable->option['comments_rplylnkhvrbgclr']; ?>;
	color: <? echo $durable->option['comments_rplylnkhvrtxtclr']; ?>;
}

#colourControl dd a:hover, #saveSettings a:hover {
	color: #fff;
	background: #555;
}

#overview a {
	color: <? echo $durable->option['footer_lnktxtclr']; ?>;
}

#overview a:hover {
	background: <? echo $durable->option['footer_lnkhvrbgclr']; ?>;
	color: <? echo $durable->option['footer_lnkhvrtxtclr']; ?>;
}

#overview .date, #overview .date a {
	color: inherit;
}

#overview .date a, #overview .date a:hover {
	text-decoration: none;
	background: none;
}

#colourControl dt a, #colourControl dt a:visited {
	color: #f36d21;
}

#colourControl dt a:hover {
	color: #fff;
	background: #f36d21;
}

#colourControl dd a, #colourControl dd a:visited, #saveSettings a {
	color: #555;
}

#colourControl #saveSettings {
	position: absolute;
	bottom: 0;
	left: 0;
	width: 44%;
	text-align: right;
	padding: 3px;
	font-size: 11px;
}

/**
 * Lists
 */

#topMenu ul {
	list-style-type: none;
	width: 100%;
	margin: 0;
	padding: 0;
}

#topMenu ul li {
	display: inline;
	font-size: 11px;
}

div.holder ul {
	margin: 0 0 0 30px;
	padding: 0;
}

.cats {
	margin: 0 0 20px 0;
}

#overview li, #search li {
	margin: 0 0 5px 0;
}

ol.commentlist {
	list-style-type: none;
	margin: 0;
	padding: 0;
}

ol.commentlist h4 {
	font-size: 14px;
	margin: 0 0 5px 0;
}

ol.commentlist li {
	padding: 15px;
}

.alt { 
	background: <? echo $durable->option['comments_rplybgclr']; ?>;
	margin: 0 0 10px 0;
}

#colourControl dl {
	position: absolute; 
	top: 40px;
	left: 6px;
	width: 50%;
	padding: 0 0 15px 15px;
}

#colourControl dt {
	color: #f36d21;
	font-size: 14px;
	margin: 3px 0 3px 0;
}

#colourControl dd {
	font-size: 10px;
	margin: 3px 0 3px 15px;
}

/**
 * Paragraphs
 */

#header p {
	margin: 5px 0 15px 0;
}

#header > p {
	margin: 5px 0 5px 0;
}

.postMeta p {
	clear: both;
	margin: 5px 0 5px 0;
	padding: 0 0 7px 0;
	border-bottom: 1px solid #ddd;
}

/**
 * Tables
 */

.menuSection td {
	border: 1px dotted <? echo $durable->option['menusections_txtclr']; ?>;
	text-align: center;
	padding: 2px;
}

.menuSection td.pad, .menuSection tfoot td {
	border: none;
}

.menuSection table caption {
	font-weight: bold;
	font-family: georgia;
	font-size: 14px;
	color: <? echo $durable->option['menusections_lnktxtclr']; ?>;
}

/**
 * Images
 */

img {
	vertical-align: middle;
	border: none;
}

.entry img {
	float: left;
	margin: 5px 20px 0 0;
}

.entry img.wp-smiley {
	float: none;
	margin: 0;
	vertical-align: absmiddle;
}

img.float-right {
	float: right;
	margin: 0 0 0 20px;
}

img.no-float {
	float: none;
	margin: 15px;
	display: block;
}

/**
 * Forms
 */

label {
	display: block;
	font-size: 11px;
}

input, select, textarea {
	width: 98%;
	margin: 0 0 10px 0;
}

input#submit, input#livesearchButton {
	width: auto;
}

#colourControl input {
	margin: 0;
}

/**
 * Misc Elements
 */

hr {
	clear: both;
	visibility: hidden;
	margin: 0;
}

pre code {
	display: block;
	padding: 0 15px 0 15px;
	border-left: 5px solid <? echo $durable->option['maincontent_txtclr']; ?>;
}

blockquote {
	padding: 0 15px 0 15px;
	border-left: 5px solid <? echo $durable->option['maincontent_txtclr']; ?>;
	text-align: justify;
}

