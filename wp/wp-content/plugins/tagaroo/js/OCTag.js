
oc.Tag = CFBase.extend({
	init: function(text, source) {
		this.text = text;
		this.slug = cf.slugify(text);
		this.textToken = new oc.TagToken(this);
		oc.tagManager.registerTag(this);
		this.source = source || null;
	},
	
	isUserGenerated: function() {
		return (this.source == null);
	},
	
	shouldUseForImageSearch: function() {
		if (this.source) {
			return this.source.shouldUseForImageSearch();
		}
		return true;
	},
	
	makeCurrent: function() {
		oc.tagManager.putTagInCurrent(this);
	},
	
	makeSuggested: function() {
		oc.tagManager.putTagInSuggested(this);
	},
	
	makeBlacklisted: function() {
		oc.tagManager.putTagInBlacklist(this);
	},
	
	_setBucketName: function(bucketName) {
		this.bucketName = bucketName;
	},
		
	// i can haz automatic destructors?
	destruct: function() {
		this.textToken.removeFromDOM();
		oc.tagManager.unregisterTag(this);		
		if (this.source) {
			oc.entityManager.deleteArtifact(this.source);
		}
	},

	// NB: this is a temporary solution
	serialize: function() {
		return '{\
			text:\'' + this.text.replace(/'/, "\\'") + '\',\
			slug:\'' + this.slug + '\',\
			source:' + (this.source ? this.source.serialize() : 'null') + ',\
			bucketName:\'' + this.bucketName + '\'\
		}';
	},
	
	text: '',
	slug: '',
	textToken: null,
	source: null,
	bucketName: 'none'
});

