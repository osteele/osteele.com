<?php if (!is_search()) {
		$search_text = "search blog archives";
	} else {
		$search_text = "$s";
	}
?>

		<div id="livesearchform">
		<form onsubmit="return liveSearchSubmit()" id="searchform" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<p style='display:inline;'><input type="text" id="livesearch" name="s" value="<?php echo wp_specialchars($search_text, 1); ?>" onkeypress="liveSearchStart()" onblur="setTimeout('closeResults()',2000); if (this.value == '') {this.value = 'search blog archives';}"  onfocus="if (this.value == 'search blog archives') {this.value = '';}" /></p>
			<p style='display: inline;'><input type="submit" id="searchsubmit" style="display: none;" value="Search" /></p>
			<!--[if IE]><div><![endif]--><div id="LSResult" style="display: none;"><div id="LSShadow"></div></div><!--[if IE]></div><br /><![endif]-->
		</form>
		</div>

