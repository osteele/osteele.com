var gDocs;
function DocViewer(options) {
    this.options = options;
    this.initialize.apply(this, arguments);
}
DocViewer.prototype.initialize = function(options) {
    var examples = options.examples,
        api = options.api,
        noscript = $('#noscript')[0];
    noscript && (noscript.innerHTML = noscript.innerHTML.replace(/<span.*?<\/span>/, 'If this message remains on the screen,'));
    new Protodoc.ExampleViewer().load(examples, {
        onSuccess: this.noteCompletion.bind(this, 'examples'),
        target: $('#examples')[0]
    });
    gDocs = new Protodoc.APIViewer().load(api, {
        onSuccess: this.noteCompletion.bind(this, 'docs'),
        target: $('#docs')[0]
    });
    initializeHeaderToggle();
    initializeTestLinks();
}
function initializeHeaderToggle() {
    $('#header-toggle').click(updateHeaderState);
    updateHeaderState();
    function updateHeaderState(e) {
        var show = $('#header-toggle')[0].checked;
        $('#header').invoke(show ? 'show' : 'hide');
    }
}
jQuery.fn.invoke = function(fname) {
    var args = Array.prototype.slice.call(arguments, 1);
    return this[fname].apply(this, args);
}
function initializeTestLinks() {
    $('#run-tests').click(function(e) {
        var results = gDocs.runTests();
        alert(results.toHTML());
        return false;
    });
    $('#write-tests').click(function(e) {
        var text = gDocs.getTestText();
        document.write('<pre>' + text.escapeHTML() + '</pre>');
        return false;
    });
}
DocViewer.prototype.noteCompletion = function(flag) {
    var flags = arguments.callee,
        onload = this.options.onLoad;
    flags[flag] = true;
    if (!flags.docs || !flags.examples)
        return;
    onload && onload();
    $('#noscript').hide();
    var inputs = $('kbd');
    if (window.location.search.match(/[\?&]test\b/)) {
        var results = gDocs.runTests();
        alert(results.toHTML());
    }
    scheduleGradientReset();
    $(window).resize(scheduleGradientReset);
}
var scheduleGradientReset = (function() {
    var resizer;
    return function() {
        resizer = resizer || window.setTimeout(function() {
            resizer = null;
            resetGradients();
        }, 60);
    }
})();
function resetGradients() {
    resetGradient('#intro', 0xeeeeff);
}
function resetGradient(name, startColor, endColor) {
    if (!window.OSGradient)
        return;
    if (arguments.length < 3)
        endColor = 0xffffff;
    $(name + ' .grad').remove();
    var old = $(name + ' *');
    old.indexOf = Array.prototype.indexOf;
    OSGradient.applyGradient({
        'gradient-start-color': startColor,
        'gradient-end-color': endColor,
        'border-radius': 15
    }, $(name)[0]);
    $(name + ' *').each(function() {
        old.indexOf(this) >= 0 || $(this).addClass('grad');
    });
}
