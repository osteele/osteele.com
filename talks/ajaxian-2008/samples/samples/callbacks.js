// section 1: Basic callback
/// You may be familiar with this pattern, where a function is
/// passed as an argument to another function. \br `$.get` here is
/// from the jQuery library; other AJAX libraries have
/// similar functions.
function callback(x) {
    log('received "' + x + '"');
}

function request() {
    $.get('request', callback);
}

request();

// section 2: The long syntax
/// `function f(...) {...}` is (mostly) just a shortcut for
/// `var f = function(...) {...}`.  They each define a variable
/// named `f`, with a function value.
var callback = function(x) {
    log('received "' + x + '"');
}

var request = function() {
    $.get('request', callback);
}

request();

// section 3: Reprise
/// We'll return to the short syntax.  (Yes, this is the same as the first
/// slide.)
function callback(x) {
    log('received "' + x + '"');
}

function request() {
    $.get('request', callback);
}

request();

// section 4: Local (nested) function
/// Just like any other variable, we can move `callback` inside
/// a function that contains all the references to its name.
function request() {
    function callback(x) {
        log('received "' + x + '"');
    }
    $.get('request', callback);
}

request();

// section 5: Pyramid order
/// The variable that a `function` statement defines is initialized
/// before any statements in that function are run.  (That's the
/// one difference between `function f(...) {...}` and `var f = function...`.
/// This allows for "pyramid style", where the function's implementation
/// summarizes its behavior before giving the details (the nested functions).
function request() {
    $.get('request', callback);
    function callback(x) {
        log('received "' + x + '"');
    }
}

request();

// section 6: Using the function value directly
/// If a function is only used once, we can place it where it's used,
/// instead of making a separate definition for it.  This does *not*
/// define a variable named `callback`; the name is visible only to the
/// the human reader, not the compiler.
function request() {
    $.get('request', function callback(x) {
        log('received "' + x + '"');
    });
}

request();

// section 7: Anonymous functions
/// In this case we can leave out the name.
function request() {
    $.get('request', function (x) {
        log('received "' + x + '"');
    });
}

request();
