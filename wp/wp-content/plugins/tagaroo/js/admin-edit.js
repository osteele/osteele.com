
oc.rte = null;
oc.scanOnIdle = true;
oc.cachedPostContent = '<br>';
oc.lastResponse = null;
oc.docLangWorkaround = true;

if (typeof(console) == 'undefined') {
	console = {log:function() {}, dir: function(){}};
}

oc.getVersion = function() {
	return '1.0';
}

oc.showTagSearchingIndicator = function() {
	jQuery('#oc_api_notifications').html('').hide();
	jQuery('#oc_suggest_tags_link').hide();
	jQuery('#oc_tag_searching_indicator').show();
}
oc.hideTagSearchingIndicator = function() {
	jQuery('#oc_suggest_tags_link').show();
	jQuery('#oc_tag_searching_indicator').hide();
}

oc.pingCalais = function() {
	var text = oc.getPostText();

	if (oc.docLangWorkaround && text.length <= 64 ) {
		oc.tagManager.deleteUnusedSuggestedTags();
		jQuery('#oc_api_notifications').html('tagaroo needs at least 64 characters to start searching for tags.').show();
		return;
	}
	
	oc.showTagSearchingIndicator();
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		dataType: 'html',
		data: { 
			oc_action: 'api_proxy_oc', 
			text: text
		},
		success: function(responseString) {
			try {
				oc.handleCalaisResponse(responseString);
			}
			catch (error) {
				if (error.type && error.type == '__oc_request_failed__') {
					oc.handleCalaisError(error);
				}
				else {
					throw error;
				}
			}
			finally {
				oc.hideTagSearchingIndicator();
			}
		},
		error: function() { 
			oc.hideTagSearchingIndicator();
			oc.handleAjaxFailure();
		}
	 });
};

oc.handleCalaisError = function(error) {
	jQuery('#oc_api_notifications').html('<span style="color:red;"><strong>OpenCalais error</strong>: ' + error.string).show();
};

// we couldn't even reach our own server
oc.handleAjaxFailure = function(requestObj, errorString, exception) {
	jQuery('#oc_api_notifications').html('<span style="color:red;"><strong>Error talking to WordPress server.</strong>: ' + errorString);
};

oc.handleCalaisResponse = function(responseString) {
	if (responseString.indexOf('__oc_request_failed__') >= 0) {
		eval('var errorObject = ' + responseString.substring('__oc_request_failed__'.length));
		if (!oc.docLangWorkaround || (errorObject.error.indexOf('Unsupported document language') == -1)) {
			throw { type: '__oc_request_failed__', string: errorObject.error };
		}
		else {
			oc.tagManager.deleteUnusedSuggestedTags();
			jQuery('#oc_api_notifications').html('<span>No new tags extracted.<br/><a href="javascript:oc.pingCalais();">Suggest Tags</a></span>');
		}
	}

	oc.lastResponse = jQuery.xmlToJSON(jQuery.textToXML(responseString));
	if (oc.isValidResponse(oc.lastResponse) && oc.lastResponse.RDF[0].Description.length > 0) {
		
		jQuery('#oc_suggest_tags_link').show();
		
		oc.tagManager.deleteUnusedSuggestedTags();

		var artifacts = oc.entityManager.generateArtifacts(oc.lastResponse.RDF[0].Description);
		var tagsWereMade = false;
		jQuery.each(artifacts, function(i, artifact) {
			if (artifact.shouldGenerateTag && artifact.shouldGenerateTag()) {
				var newTag = oc.tagManager.createTagIfNew(artifact.getTagText(), artifact);
				if (newTag) {
					tagsWereMade = true;
					oc.tagManager.putTagInSuggested(newTag);
				}
			}
		});

		if (oc.tagManager.suggestedTags.length == 0) {
			jQuery('#oc_api_notifications').html('<span>No new tags extracted.<br/><a href="javascript:oc.pingCalais();">Suggest Tags</a></span>');
		}
		else {
			if (oc.imageManager.mode == oc.imageManager.mode_none) {
				oc.imageManager.pingFlickr(oc.imageManager.mode_currentSuggested);
			}
			else if (oc.imageManager.mode != oc.imageManager.mode_specificTags) {
				oc.imageManager.pingFlickr();
			}
		}
	}
};

oc.isValidResponse = function(responseObject) {
	return ((typeof(oc.lastResponse) != 'undefined') && typeof(oc.lastResponse.RDF) != 'undefined');
};

oc.tickleIdleTimer = function() {
	if(oc.idleTimer) {
		clearTimeout(oc.idleTimer);
	}
	oc.idleTimer = setTimeout(oc.idleTimeout, 1000);	
};

oc.firstScan = true;
oc.idleTimeout = function() {
	if (oc.scanOnIdle && oc.postIsDirty(true)) {
		oc.pingCalais();
	}
	if (oc.firstScan) {
		// if we have any current tags, go ahead and fire off an image request
		if (oc.tagManager.currentTags.length) {
			oc.imageManager.pingFlickr(oc.imageManager.mode_currentSuggested);
		}
		oc.firstScan = false;
	}
};

oc.postIsDirty = function(updateCache) {
	var content = oc.getPostText();
	var changed = (content != oc.cachedPostContent) || oc.firstScan;
	if (typeof(updateCache) !== 'undefined') {
		oc.cachedPostContent = content;
	}
	return changed;
};

oc.getRTE = function() {
	return oc.rte;
};

oc.getPostText = function() {
	var rte = oc.getRTE();
	if (rte !== null) {
		return rte.getBody().innerHTML;
	}
	else if (jQuery('#content').size() > 0) {
		return jQuery('#content')[0].value;
	}
	return '';
};

oc.windowResized = function() {
	if (oc.wp_gte_23 && !oc.wp_gte_25) {
		oc.imageManager.pageForFilmstripSize();
		oc.imageManager.resizePreviewInfoPane();
	}
};

oc.addTagFieldHandler = function() {
	var tagsString = jQuery('#oc_add_tag_field').val();
	if (tagsString) {
		var tagTexts = tagsString.split(/\s*,\s*/);
		jQuery.each(tagTexts, function(i, tagText) {
			if (tagText.length > 0) {
				var newTag = oc.tagManager.createTagIfNew(tagText);
				if (newTag) {
					oc.tagManager.putTagInCurrent(newTag);
					jQuery('#oc_add_tag_field').val('');
				}
				else {
					jQuery('#oc_current_tag_notifications').html('Tag already exists.');
				}
			}
		});
	}
	return false;
}

// So we need to give the autocomplete a chance to do the completion for us if it wants to.
// We look for enter key events at both the tag entry field and at the oc_tag_controls
// container. At the field, we set a flag saying "let's wait to see if autocomplete fires."
// At the container, we check to see if it's fired. If it hasn't we can go ahead and add the
// tag. If it did fire, it will invoke this method, tagAutocompleteHandler.
oc.waitingForTagAutocomplete = false;
oc.tagAutocompleteHandler = function() {
	jQuery('#oc_add_tag_button').click();
	oc.waitingForTagAutocomplete = false;
}

oc.updateArchiveField = function() {
	var v = '{\
		version:' + oc.getVersion() + ',\
		tags: ' + oc.tagManager.getSerializedTags() + '\
	}';
	jQuery('#tags-input').val(oc.tagManager.tagsAsCSV('current'));
	jQuery('#oc_metadata').val(v);
}

// temporary solution
oc.unarchiveSavedTags = function(wpTags) {
	var j = jQuery('#oc_metadata');
	if (j.size() && j.val() != '') {
		var archive = eval('(' + j.val() + ')');
		jQuery.each(archive.tags, function(slug, tag) {
			if (tag.source != null) {
				oc.entityManager.unarchiveArtifact(tag.source);
			}
		});
		
		var artifacts = oc.entityManager.getArtifacts();
		jQuery.each(artifacts, function(i, artifact) {
			if (artifact.shouldGenerateTag && artifact.shouldGenerateTag()) {
				var newTag = oc.tagManager.createTagIfNew(artifact.getTagText(), artifact);
				
				if (newTag) {
					jQuery.each(archive.tags, function(slug, tag) {
						if (newTag.text == tag.text) {
							switch(tag.bucketName) {
								case 'current':
									// be sure it still exists for WP
									if (wpTags.indexOf(newTag.text) != -1) {
										oc.tagManager.putTagInCurrent(newTag);
									}
								break;
								case 'suggested':
									oc.tagManager.putTagInSuggested(newTag);
								break;
								case 'blacklisted':
									oc.tagManager.putTagInBlacklist(newTag);							
								break;
							}
						}
					});
				}
			}
		});
	}
}

oc.initPostEditPage = function() {
	// remove wp's tag control
	var wpTagList = jQuery('#tags-input').val();
	if (oc.wp_gte_23 && !oc.wp_gte_25) {
		jQuery('#tagdiv').remove();
		jQuery('#grabit').prepend(jQuery('#oc_dbx'));
		var ocDBXGroup = new dbxGroup(
			'oc-dbx',
			'vertical',
			'7',
			'yes',
			'10',
			'yes',
			'open',
			'open',
			'close',
			'Click and Drag to move',
			'Click to %toggle%',
			'Use the arrow keys to move',
			', or press the enter key to %toggle% it',
			'%mytitle%  [%dbxtitle%]'
		);
		jQuery('#oc_tag_controls').append('<input id="tags-input" type="hidden" value="" name="tags_input"/>');
	}
	else if (oc.wp_gte_25) {
		jQuery('#tagsdiv').remove();
		jQuery('#oc_tag_controls div.inside').append('<input id="tags-input" type="hidden" value="" name="tags_input"/>');
	}
	
	// set up buckets
	oc.tagManager.suggestedBox = new oc.SuggestedTagBox();
	oc.tagManager.currentBox = new oc.CurrentTagBox();
	oc.tagManager.blacklistedBox = new oc.BlacklistedTagBox();

	oc.tagManager.suggestedBox.insertIntoDOM('append', jQuery('#oc_suggested_tags_wrapper'));
	oc.tagManager.currentBox.insertIntoDOM('append', jQuery('#oc_current_tags_wrapper'));
	oc.tagManager.blacklistedBox.insertIntoDOM('append', jQuery('#oc_suggested_tags_wrapper'));

	var wpTags = wpTagList.split(/\s*,\s*/);
	oc.unarchiveSavedTags(wpTags);

	// create new tags for any wp tags we don't know about.
	if (wpTagList.length > 0) {
		jQuery.each(wpTags, function(i, tagName) {
			if (tagName.length > 0) {
				oc.tagManager.putTagInCurrent(oc.tagManager.createTagIfNew(tagName));
			}
		});		
	}

	jQuery('#oc_tag_controls').keypress(function(e) {
		// if autocomplete did not fire we go ahead and add the tag
		// and kill the event.
		if (e.which == 13 && oc.waitingForTagAutocomplete) {
			jQuery('#oc_add_tag_button').click();
			oc.waitingForTagAutocomplete = false;
			return false;
		}
	});
		
	jQuery('#oc_add_tag_button').click(oc.addTagFieldHandler);
	jQuery('#oc_add_tag_field').keypress(function(e) {
		if (e.which == 13) {
			// let's see if autocomplete will fire
			oc.waitingForTagAutocomplete = true;
		}
		else {
			jQuery('#oc_current_tag_notifications').html('&nbsp;');
		}
		
	});
	
	jQuery('#oc_add_tag_field').suggest(
		'admin-ajax.php?action=ajax-tag-search', 
		{ 
			delay: 500, 
			minchars: 2, 
			onSelect: oc.tagAutocompleteHandler
		} 
	);
		
	// images
	oc.imageManager.filmstripBox = new oc.ImageParadeBox();
	oc.imageManager.filmstripBox.insertIntoDOM('append', jQuery('#oc_filmstrip_wrapper'));
	jQuery('#oc_images_page_fwd').click(function() {
		oc.imageManager.pageForward();
	});
	jQuery('#oc_images_page_back').click(function() {
		oc.imageManager.pageBack();
	});
	jQuery('#oc_images_sort_select').change(function() {
		oc.imageManager.setSortMode(jQuery('option:selected', jQuery(this)).val());
	});
	jQuery('input[@name=oc_sort_direction]').change(function() {
		oc.imageManager.setSortDirection(jQuery('input:checked[@name=oc_sort_direction]').val());
	});
	jQuery('#oc_images_sort_toggle').click(function() {
		jQuery('#oc_images_sort').slideToggle();
		return false;
	});
	jQuery('#oc_suggest_tags_link').click(function() { oc.pingCalais(); return false; });
	jQuery('#content').keypress(function(e) {
		oc.tickleIdleTimer();
	});
	jQuery(window).resize(function() {
		oc.windowResized();
	})	
	oc.tickleIdleTimer();
}

jQuery(document).ready(oc.initPostEditPage);
