jQuery('a').each(function() {
	var a = jQuery(this);
	var href = a.attr('href');
	var hrefArray = href.split('.');
	var extension = hrefArray[href.length - 1];
 
 	// If the link is external
 	if ( ( href.match(/^http/) ) && ( !href.match(document.domain) ) ) {
    	// Add the tracking code
		a.click(function() {
			pageTracker._trackPageview('/outgoing/' + href);
		});
		
	}
	
 	// If the link is a download
	if (jQuery.inArray(extension,fileTypes) != -1) {
		// Add the tracking code
		a.click(function() {
			pageTracker._trackPageview('/documents/' + href);
		});
	}
});