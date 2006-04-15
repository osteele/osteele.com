<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
<label for="s">Search Terms</label>
<input type="text" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s" />
<input type="submit" id="livesearchButton" value="Search &raquo;" onclick="return liveSearch();" />
<span id="busy" style="display: none;"> Searching...</span>
</form>
<div id="searchResults"></div>
