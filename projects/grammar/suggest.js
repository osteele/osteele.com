/*
 * Agenda:
 * - client:
 * -- display debug suggestions
 * -- display readable suggestions
 * Features:
 * - send text on continuous basis
 * - inline suggestions
 *
 * Server:
 * - lexer
 * - tagger
 * - text diff
 * - token diff
 * - rule database
 * - rule matcher
 * - suggester
 * Features:
 * - lexer
 * - tagger
 *
 * Deploy:
 * - lock off sources
 */

function encodeHTML(s) {
    s = s.replace(/&/g, '&amp;');
    s = s.replace(/</g, '&lt;');
    s = s.replace(/\"/g, '&quot;');
    return s;
}

var Application = function() {};

Application.prototype.inputModeIds = ['checkButton', 'inputArea'];
Application.prototype.reviewModeIds = ['doneButton', 'outputArea'];


Application.prototype.setInputMode = function() {
    Element.show.apply(null, this.inputModeIds);
    Element.hide.apply(null, this.reviewModeIds);
};

Application.prototype.setResultsMode = function() {
    Element.hide.apply(null, this.inputModeIds);
    Element.show.apply(null, this.reviewModeIds);
};

var app = new Application();

function initialize() {
    app.setInputMode();
    oldText = $F('inputArea');
}

function check() {
//     app.setResultsMode();
//     $('outputArea').innerHTML = encodeHTML($F('inputArea'));
    var text0 = oldText;
    var text1 = $F('inputArea');
    oldText = text1;
	text0 = $F('inputArea');
	text1 = $F('postArea');
	new Ajax.Request('suggest.php',
					 {method: 'get',
							 parameters: ($H({text0: text0, text1: text1}).
										  toQueryString()),
							 onComplete: function(r) {
							 $('results').innerHTML = encodeHTML(r.responseText);
						 }
					 });
}

function done() {
    app.setInputMode();
}

Event.observe(window, 'load', initialize);
