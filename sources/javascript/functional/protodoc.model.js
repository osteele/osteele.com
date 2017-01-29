/* Copyright 2007 by Oliver Steele.  Available under the MIT License. */

/*
 * Domain Model
 */

var Model = Base.extend({
    constructor: function(name) {
        this.name = name;
        this.path = name ? [name] : [];
        this.container = null;
        this.definitions = [];
        this.docs = [];
    },

    addBlock: function(block) {
        this.container && this.container.addBlock(block);
    },

    addDefinition: function(defn) {
        // check for duplicates
        var name = defn.name,
            value = this.definitions.detect(function(defn) {
                return defn.name == name;
            });
        if (defn instanceof Model && value) {
            window.console && console.info &&
                console.info("duplicate definition in " + this + ": " + defn.name);
        };
        this.addBlock(defn);
        defn.container = this;
        defn.path = this.path.concat([defn.name]);
        this.definitions.push(defn);
    },

    findOrMake: function(name) {
        var parts = /(.+?)\.(.+)/.exec(name);
        if (parts)
            return this.findOrMake(parts[1]).findOrMake(parts[2]);
        var value = this.definitions.detect(function(defn) {
            return defn.name == name;
        });
        if (!value) {
            value = new Model(name);
            this.addDefinition(value);
        }
        return value;
    },

    // visitors
    eachDefinition: function(fn) {
        fn(this);
        this.definitions.each(function(defn) {
            if (defn instanceof Model)
                defn.eachDefinition(fn);
        });
    }
});

var GlobalContext = Model.extend({
    constructor: function() {
        gm = this;
        this.base(null);
        this.blocks = [];
    },

    addDefinition: function(defn) {
        this.addBlock(defn);
        this.base(defn);
    },

    addBlock: function(block) {
        this.blocks.push(block);
    },

    eachBlock: function(fn) {
        this.blocks.forEach(fn);
    }
});

var VariableDefinition = Model.extend({
    constructor: function(name, options) {
        options = options || {};
        this.base(name);
        this.docs = options.docs || [];
        this.path = null;
    },

    toString: function() {
        return ['var ', this.name].join('');
    },

    getQualifier: function() {
        return this.container && this.container.path;
    }
});

var FunctionDefinition = VariableDefinition.extend({
    constructor: function(name, params, options) {
        this.base(name, options);
        this.parameters = (params||'').split(/,/).select(pluck('length'));
    },

    toString: function() {
        return ['function ', this.name, '()'].join('');
    },

    getQualifier: function() {
        return this.container && this.container.path;
    }
});

// A comment block that isn't associated with any particular
// language element.
function SectionBlock(docs) {
    this.docs = docs;
}


/*
 * Comments
 */

var CommentBlockTypes = makeEnum('equivalence formatted output paragraph signature heading');
