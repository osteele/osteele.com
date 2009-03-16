

oc.Entity = oc.TagSource.extend({
	init: function(rdfDescription) {
		this.url = rdfDescription['rdf:about'];
		this.type = oc.entityManager.createArtifactTypeIfNew(rdfDescription['type'][0]['rdf:resource']);
		this.name = rdfDescription.name[0].Text;
	},
	
	getTagText: function() {
		return this.name;
	},
	
	getTagTypeName: function() {
		return this.type.name;
	},
	
	shouldGenerateTag: function() {
		// URLs have names, but let's not make them tags.
		return (this.type.name != 'URL');
	},
		
	addEventFact: function(eventFact) {
		this.eventFacts[eventFact.url] = eventFact;
	},

	// NB: this is a temporary solution
	serialize: function() {
		return '{\
			url:\'' + this.url + '\',\
			type:' + this.type.serialize() + ',\
			name:\'' + this.name.replace(/'/, "\\'") + '\',\
			nInstances:' + this.nInstances + '\
		}';
	},
	
	url: '',
	type: null,
	name: '',
	eventsFacts: {},
	nInstances: 1
});
