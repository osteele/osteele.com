/**
 * Options:
 *   all: include undocumented elements
 *   headingLevel: hn for topmost headings; default 3
 *   staged: render incrementally if true
 *   target: an HTML Element that is set to the docs on completion
 *   onSuccess: called when load completes
 */
Protodoc.APIViewer = function(options) {
    this.options = OSUtils.merge({headingLevel: 3,
                                  staged: true}, options||{});
};

/// Load +url+ and parse its contents.
Protodoc.APIViewer.prototype.load = function(urls, _options) {
    var self = this,
        options = Hash.merge(this.options, _options || {});
    if (typeof urls == 'string')
        urls = [urls];
    var count = urls.length,
        results = new Array(count),
        target = options.target;
    target && (target.innerHTML = Protodoc.loadingHeader);
    urls.forEach(function(url, ix) {
        if (options.bustCache)
            url += (/\?/(url) ? '&' : '?') + 'ts=' + new Date().getTime();
        $.get(url,
              receive.reporting().bind(this, ix));
    });
    return this;
    function receive(ix, response) {
        results[ix] = response;
        --count || self.parse(results.join(''), options);
    }
}

/// Parse +text+.  If +options.target+ is specified, update it.
Protodoc.APIViewer.prototype.parse = function(text, options) {
    this.text = Protodoc.stripHeader(text);
    this.updateTarget(this.options.staged && 0, options);
    return this;
}

Protodoc.APIViewer.prototype.updateTarget = function(stage, options) {
    var target = options.target;
    if (!target) return options.onSuccess && options.onSuccess();

    var text = this.text,
        formatOptions = {headingLevel:options.headingLevel};
    switch (stage) {
    case 0:
        target.innerHTML = Protodoc.previewText(text);
        break;
    case 1:
        formatOptions.quicker = true;
    case 2:
        formatOptions.quick = true;
    default:
        var model = this.model = this.model || new Protodoc.Parser(options).parse(text),
            formatter = new HTMLFormatter(formatOptions),
            html = formatter.render(model);
        target.innerHTML = html;
        if (stage <= 2) break;
        options.onSuccess && options.onSuccess();
        return this;
        break;
    }
    this.updateTarget.bind(this).saturate(stage+1, options).delayed(10);
    return this;
}

Protodoc.APIViewer.prototype.getTestText = function() {
    return this.model.getTestText();
}

Protodoc.APIViewer.prototype.runTests = function() {
    return this.testResults = this.model.runTests();
}
