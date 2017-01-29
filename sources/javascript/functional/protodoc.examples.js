/* Copyright 2007 by Oliver Steele.  Available under the MIT License. */

// Options:
//   headingLevel: hn for topmost headings; default 3
//   target: an HTML Element that is set to the docs on completion
//   onSuccess: called when load completes
Protodoc.ExampleViewer = function(options) {
    this.options = OSUtils.merge({headingLevel: 3,
                                  staged: true}, options||{});
};

// Load +url+ and parse its contents.
Protodoc.ExampleViewer.prototype.load = function(url, options) {
    options = OSUtils.merge(this.options, options||{});
    var target = options.target;
    target && (target.innerHTML = Protodoc.loadingHeader);
    new $.get(
        url,
        function(response) {
            this.parse(response, options);
        }.bind(this).reporting());
    return this;
}

// Parse +text+.  If +options.target+ is specified, update it.
Protodoc.ExampleViewer.prototype.parse = function(text, options) {
    this.text = Protodoc.stripHeader(text);
    this.updateTarget(this.options.staged && 0, options);
    return this;
}

Protodoc.ExampleViewer.prototype.updateTarget = function(stage, options) {
    var target = options.target;
    if (!target) return options.onSuccess && options.onSuccess();

    var text = this.text;
    switch (stage) {
    case 0:
        target.innerHTML = Protodoc.previewText(text);
        break;
    case 1:
        target.innerHTML = Protodoc.processingHeader + this.toHTML(true);
        break;
    case 2:
        this.runExamples();
        target.innerHTML = this.toHTML();
        options.onSuccess && options.onSuccess();
        return this;
    }
    this.updateTarget.bind(this).saturate(stage+1, options).delayed(10);
    return this;
}

Protodoc.ExampleViewer.prototype.toHTML = function(fast) {
    var self = this;
    var chunks = (OSUtils.unindent(this.text)
                  .escapeHTML()
                  .split('trace('));
    var outputs = this.trace || [];
    var lines = ['<pre>', chunks.shift()];
    chunks.each(function(segment, ix) {
        var output = ix < outputs.length
            ? outputs[ix].escapeHTML()
            : 'execution did not get this far';
        var m = segment.indexOf(');');
        fast || lines.push('<kbd>');
        lines.push(segment.slice(0, m));
        fast || lines.push('</kbd>');
        lines.push(';\n <samp>&rarr; ');
        lines.push(output.strip());
        lines.push('</samp>');
        fast && lines.push(segment.slice(m+2));
        fast || segment.slice(m+2).split('\n').each(function(line, ix) {
            ix && lines.push('\n');
            var hasContent = line.match(/\S/) && !line.match(/\/\//);
            hasContent && lines.push('<kbd>');
            lines.push(line);
            hasContent && lines.push('</kbd>');
        });
    });
    lines.push('</pre>');
    var html = lines.join('').replace(/((?:\/\/+.*\n)+)/g, function(text) {
        if (!fast)
            text = Protodoc.inlineFormat(text);
        text = text.replace(/\/\/  (.*)/g, '<pre>$1</pre>');
        text = text.replace(/\/\//g, ' ');
        text = text.replace(/(\^+)\s*(.*)/, function(_, level, title) {
            var tagName = 'h' + (level.length - 1 + self.options.headingLevel);
            return ['</div><', tagName, '>', title, '</', tagName, '><div class="comment">'].join('');
        });
        return '<div class="comment">'+text+'</div>';
    }.bind(this)).replace(/<div class="comment">\s*<\/div>/g, '');
    return html;
}

Protodoc.ExampleViewer.prototype.runExamples = function() {
    var results = this.trace = [];
    try {
        trace = function() {
            var args = map(OSUtils.toString, arguments);
            results.push(args.join(' '));
        }
        var fn = new Function('trace', this.text);
        fn(trace);
    } catch (e) {
        this.error = e;
        results.push('Error: ' + e.toString());
    }
}

OSUtils.unindent = function(text) {
    var lines = text.split('\n'),
        min = lines.grep(/\S/).map('_.match(/^\\s*/)[0].length'.lambda()).min();
    return lines.map(function(line) {
        return line.slice(min);
    }).join('\n');
}
