


oc.ArtifactType = CFBase.extend({
	init: function(typeURL) {
		this.url = typeURL;
		this.name = typeURL.substring(typeURL.lastIndexOf('/') + 1);
		oc.entityManager.registerArtifactType(this);
	},

	// NB: this is a temporary solution
	serialize: function() {
		return '{\
			url:\'' + this.url + '\',\
			iconURL:\'' + this.iconURL + '\',\
			name:\'' + this.name + '\'\
		}';
	},
	url: '',
	iconURL: '',
	name: ''
});
