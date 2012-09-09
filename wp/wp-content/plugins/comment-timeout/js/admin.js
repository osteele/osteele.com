function confirm_timeout_reset() {
	return window.confirm(
		"Are you sure you want to reset all posts and pages to their default "+
		"settings? This action can not be undone."
	);
}

jQuery(function() {
	var ctActive = document.getElementById('ctActive');
	var tbSettings = document.getElementById('tbSettings');

	function enableDisableControls() {
		if (ctActive.checked) {
			jQuery(tbSettings).slideDown();
		}
		else {
			jQuery(tbSettings).slideUp();
		}
	}

	jQuery(ctActive).change(enableDisableControls);
	if (!ctActive.checked) 
		jQuery(tbSettings).hide();
});