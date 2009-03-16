

oc.TagSource = CFBase.extend({
	init: function() {
	},
	
	getTagTypeName: function() {
		return 'Other';
	},

	getTagText: function() {
		return '';
	},
	
	getTagTypeIconURL: function() {
		return '';
	},
	
	getTagTypeClassName: function() {
		return cf.slugify(this.getTagTypeName());
	},
	
	serialize: function() {
		return '{}';
	},
	
	shouldGenerateTag: function() {
		return true;
	},
	
	shouldUseForImageSearch: function() {
		return true;
	}
});
