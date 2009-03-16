

oc.EventFact = oc.TagSource.extend({
	init: function(rdfDescription) {
		this.url = rdfDescription['rdf:about'];

		// try to find our target
		var entityTargetNames = oc.entityManager.getArtifactTypeNames();
		entityTargetNames.push('subject');
		jQuery.each(entityTargetNames, function(i, name) {
			if (rdfDescription[name.toLowerCase()]) {
				this.targetEntityURL = rdfDescription[name.toLowerCase()][0]['rdf:resource'];
			}
		});

		this.type = oc.entityManager.createArtifactTypeIfNew(rdfDescription['type'][0]['rdf:resource']);
		
		if (this.type && oc.entityManager.artifactDisplayInfo.eventFactDisplayText[this.type.name]) {
			this.name = oc.entityManager.artifactDisplayInfo.eventFactDisplayText[this.type.name];
			this.makeMeATag = true;
		}
	},
	
	getTargetEntityURL: function() {
		return this.targetEntityURL;
	},
	
	getTagText: function() {
		return this.name;
	},
	
	getTagTypeName: function() {
		return 'Event/Fact';
	},
	
	shouldGenerateTag: function() {
		return this.makeMeATag;
	},
	
	shouldUseForImageSearch: function() {
		return false;
	},

	// NB: this is a temporary solution
	serialize: function() {
		return '{\
			url:\'' + this.url + '\',\
			type:' + this.type.serialize() + ',\
			name:\'' + this.name + '\',\
			targetEntityURL:\'' + this.targetEntityURL + '\',\
			nInstances:' + this.nInstances + ',\
			makeMeATag:\'' + this.makeMeATag + '\'\
		}';
	},

	
	url: '',
	type: null,
	name: '',
	targetEntityURL: '',
	nInstances: 1,
	makeMeATag: false

});
