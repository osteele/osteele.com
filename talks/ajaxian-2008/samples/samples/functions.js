// section 1: Function-valued returns
/// A function can return a function object.
function makeConst1() {
    return function() { return 1; }
}

function const1a() { return 1; }
var const1b = function() { return 1; }
var const1c = makeConst1();

log(const1a());
log(const1b());
log(const1c());

log(makeConst1()());

// section 2: Variable capture
/// If the function object refers to a variable, it's
/// the variable that was visible when the function object
/// was created.  `makeConstN` takes an argument; the
/// function that it returns doesn't.
function makeConstN(n) {
    return function() { return n; }
}

var const10 = makeConstN(10);
log(const10());
log(const10(100));

var const20 = makeConstN(20);
log(const20());
log(const20(100));

// section 3: Two different argument lists
/// `makePlus1` doesn't return an argument, but returns
/// a function that does.  `makePlusN` takes one argument,
/// and returns a function that itself takes an argument.
function makePlus1() {
    return function(x) { return x + 1; }
}
log(makePlus1()(10));
// hr
function makePlusN(n) {
    return function(x) { return x + n; }
}
var plus10 = makePlusN(10);
log(plus10(100));


// section 4: Function-valued arguments
/// A function can take a function object as an argument.
/// `twice` takes a function object; it returns a new function
/// that calls that function twice.
function plus1(x) {
    return x+1;
}
// hr
function twice(fn) {
    return function(x) {
        return fn(fn(x));
    }
}

var plus2 = twice(plus1);
log(plus2(10));

// section 5: Storing functions
/// Functions are just values.  You can store them in arrays or
/// objects.
var FnTable = {
    '+1': function(n) { return n+1; },
    '+2': function(n) { return n+2; }
};

log(FnTable['+1'](10));
log(FnTable['+2'](10));

// section 6: Function registries
/// This snippet stores functions in an object in order to create
/// a registry.
var FnTable = {};
function register(name, fn) { FnTable[name] = fn; }
function tableMethod(name) { return FnTable[name]; }
// hr
function makeAdder(n) {
    return function(x) { return x + n; }
}

register('+1', makeAdder(1));
register('+2', makeAdder(2));
// hr
log(tableMethod('+1')(10));
log(tableMethod('+2')(10));

// section 7: Unfiltered
/// `log` prints a value to the results area of the screen.
/// Here, we call it a bunch of times.
for (var i = -5; i < 5; i++)
    log(i);

// section 8: Filtering
/// Here, we make a new version of `log` that only does
/// something if its argument is positive.  We do this by
/// wrapping the original `log` function.  And we do *that*
/// by using `callIfPositive` to construct a *new* function
/// that calls the *original* function (its argument) when
/// the new function receives a positive value.
function callIfPositive(fn) {
    return function(x) {
        return x > 0 ? fn(x) : undefined;
    }
}
// hr
var logIfPositive = callIfPositive(log);
// hr
for (var i = -5; i < 5; i++)
    logIfPositive(i);

// section 9: Filtering with guards
/// We can factor the "filtering" part out of `callIfPositive`.
function guard(fn, g) {
    return function(x) {
        return g(x) ? fn(x) : undefined;
    }
}
// hr
function callIfPositive(fn) {
    return guard(fn, function(x) { return x > 0; });
}
// hr
var logIfPositive = callIfPositive(log);
// hr
for (var i = -5; i < 5; i++)
    logIfPositive(i);
