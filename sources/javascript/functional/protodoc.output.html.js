/* Copyright 2007 by Oliver Steele.  Released under the MIT License. */

/*
 * HTML Formatter
 */

function HTMLFormatter(options) {
    this.options = options || {};
    this.commentFormatter = options.commentFormatter ||
        new HTMLCommentFormatter(options);
}

HTMLFormatter.prototype = {
    render: function(model) {
        var writer = this.writer = new RopeWriter;
        model.eachBlock(this.definition.bind(this));
        return writer.toString();
    },

    definition: function(defn) {
        if (!this.options.all && !defn.docs.length && !defn.definitions.length)
            return;
        if (defn instanceof FunctionDefinition)
            this.functionDefinition(defn);
        else if (defn instanceof VariableDefinition)
            this.variableDefinition(defn);
        else if (defn instanceof Model)
            this.members(defn);
        else if (defn instanceof SectionBlock)
            this.section(defn);
        else
            throw "unknown definition";
    },

    members: function(defn) {
        //defn.definitions.each(this.definition.bind(this));
    },

    functionDefinition: function(defn) {
        var writer = this.writer;
        writer.append('<div class="record"><div class="signature">');
        if (defn.container.name)
            writer.append(this.qualifiedName(defn), ' = function(');
        else
            writer.append('function ', this.qualifiedName(defn), '(');
        writer.append('<span class="params">',
                      defn.parameters.join(', ').replace(/\/\*(.*?)\*\//g, '<i>$1</i>'),
                      '</span>)\n',
                      '</div>');
        this.doc(defn);
        writer.append('</div>');
        this.members(defn);
    },

    variableDefinition: function(defn) {
        var writer = this.writer;
        writer.append('<div class="record"><div class="signature">',
                      'var ', this.qualifiedName(defn), ';',
                      '</div>');
        this.doc(defn);
        writer.append('</div>');
        this.members(defn);
    },

    section: function(defn) {
        this.commentFormatter.render(defn.docs, this.writer);
    },

    qualifiedName: function(defn) {
        var path = defn.path.slice(0, defn.path.length-1),
            name = ['<span class="name">', defn.name, '</span>'];
        return path.length
            ? ['<span name="target">', path.join('.'), '.</span>', name]
            : name;
    },

    doc: function(defn) {
        var writer = this.writer,
            formatter = this.commentFormatter,
             blocks = defn.docs;
        blocks = blocks.select(isSignature).concat(blocks.reject(isSignature));
        formatter.render(blocks, writer);
        function isSignature(block) {
            return block.type == CommentBlockTypes.signature;
        }
    }
}


/*
 * HTML Comment Formatter
 */

function HTMLCommentFormatter(options) {
    this.options = options || {};
}

Function.prototype.hoisted = function() {
    var fn = this;
    return function(lines) {
        var thisObj = this,
            args = [].slice.call(arguments, 1);
        lines.each(function(line) {
            fn.apply(thisObj, [line].concat(args));
        });
    }
}

HTMLCommentFormatter.byType = {
    equivalence: function(text, writer) {
        var html = Protodoc.toMathHTML(text).replace(/==/, '=<sub class="def">def</sub> ')
        writer.append('<pre class="equivalence">', html, '</pre>');
    }.hoisted(),

    formatted: function(line, writer) {
        writer.append('<pre>&nbsp;&nbsp;', line.escapeHTML(), '</pre>');
    }.hoisted(),

    heading: function(title, writer, block) {
        var tagName = ['h', ((this.options.headingLevel||1)-1 + block.level)];
        writer.append('<', tagName, '>', title, '</', tagName, '>');
    },

    output: function(text, writer) {
        var match = text.match(/\s*(.*)\s*->\s*(.*?)\s*$/),
            input = match ? match[1].replace(/\s+$/,'') : text,
            output = match && match[2];
        var line = (match
                    ? ['<kbd>', input.escapeHTML(), '</kbd>',
                       ' <samp>&rarr; ', output.escapeHTML(), '</samp>']
                    : '<kbd>' + text.escapeHTML() + '</kbd>');
        writer.append('<div class="io">', line,
                      '<div class="clear"></div></div>');
    }.hoisted(),

    paragraph: function(lines, writer) {
        writer.append('<p class="description">',
                      this.options.quick ? lines : Protodoc.inlineFormat(lines.join(' ')),
                      '</p>');
    },

    signature: function(lines, writer) {
        var text = (lines.join(' ').escapeHTML().
                    replace(/-&gt;/g, '&rarr;').replace(/\.\.\./g, '&hellip;').
                    replace(/(?:(\d+)|_{(.*?)})/g, function(_, sub, sub2) {
                        return '<sub>'+(sub||sub2)+'</sub>';
                    }));
        writer.append('<div class="type"><span class="label">Type:</span> ',
                      text, '</div>');
    }
}

HTMLCommentFormatter.prototype = {
    render: function(blocks, writer) {
        var self = this;
        blocks.each(function(block) {
            if (self.options.quicker)
                return writer.append(block.lines);
            isComment(block) && writer.append('<div class="description">');
            this.renderBlock(block, writer);
            isComment(block) && writer.append('</div>');
        }.bind(this));
        function isComment(block) {
            return block.type == CommentBlockTypes.signature;
        }
    },

    renderBlock: function(block, writer) {
        var fn = HTMLCommentFormatter.byType[block.type];
        if (!fn)
            throw "no formatter for " + block.type;
        fn.call(this, block.lines, writer, block);
    }
}
