var OSLoader = {
    loadedModules: (function() {
        var loaded = [];
        var elements = $('script[@src]');
        for (var i = 0; i < elements.length; i++)
            loaded.push(elements[i].src);
        return loaded;
    })(),
    require: function(path) {
        var loaded = this.loadedModules;
        if (loaded.indexOf(path) >= 0)
            return;
        loaded.push(path);
        document.write('<script type="text/javascript" src="' + path + '"></script>');
    }
}
var Protodoc = {
    checkRequirements: function() {
        if (!window.Functional)
            throw "Protodoc requires the Functional JavaScript library";
    },
    load: function() {
        Protodoc.checkRequirements();
        Functional.install();
        var src = map('.src', $('script[@src]')).grep(/\bprotodoc\.js/)[0];
        if (!src)
            return;
        var modules = Functional.K([_, 'utils,examples,model,parser,doctest,apiviewer,output.html']).guard('!')(src.match(/\?.*load=([a-z,]*)/))[1].split(',');
        map('a -> b -> a+"protodoc."+b+".js"'.call(null, src.replace(/[^\/]*$/, '')), modules).each(OSLoader.require.bind(OSLoader));
    }
}
Protodoc.loadingHeader = '<p class="processing">Loading...</p>';
Protodoc.processingHeader = '<p class="processing">Formatting...</p>';
Protodoc.previewText = function(text) {
    return Protodoc.processingHeader + '<pre>' + text.escapeHTML() + '</pre>';
}
Protodoc.stripHeader = function(text) {
    return text.replace(/^\s*\/\*[^*](?:.|\n)*?\*\/[ \t]*/, function(s) {
        return s.match(/copyright/i) ? '' : s;
    });
}
Protodoc.inlineFormat = function(html, variables) {
    return ( html.replace(/\[(https?:.*?)\]/, '<a href="$1">$1</a>').replace(/\*(\w+?)\*/g, '<em>$1</em>').replace(/\$(.+?)\$/g, Protodoc.toMathHTML.compose('_ s -> s')).replace(/\`(.+?)\`/g, variables ? function(_, str) {
        if (variables[str])
            return '<var>' + str + '</var>';
        return '<code>' + str + '</code>';
    } : '<code>$1</code>')) ;
}
Protodoc.toMathHTML = function(text) {
    return '<span class="math">' + text.replace(/[a-z]+/gi, function(w) {
        return '<var>' + w + '</var>';
    }).replace(/<\/var>(?:(\d+)|_\{(.*?)\})/g, function(_, sub, sub2) {
        return '</var><sub>' + (sub || sub2) + '</sub>';
    }).replace(/\.\.\./g, '&hellip;') + '</span>';
}
function makeEnum(words) {
    var types = {};
    words = words.split(/\s+/);
    words.each(function(word) {
        types[word] = word;
    });
    return types;
}
window.Protodoc.loaded || Protodoc.load();
