// section 1: Special variables
/// `this` and `arguments` are special.  Going into a function
/// sets them, and they're different inside of each function.
function f() {
    logArguments(this, arguments);
}
f();
f('a');
f('a', 'b');

// section 2: `call` and `apply`
/// `call` and `apply` are verbose ways to all a function.  `f.call`
/// is useful as a replacement for `f()` because it lets you set the
/// `this` that `f` sees.  `f.apply` is useful because it takes an
/// array of argument values, so that you don't need to write them
/// all out if you don't know how many there are.
function f() { logArguments(this, arguments); }
// hr
// these are equivalent:
f(1, 2, 3);
f.call(window, 1, 2, 3);
f.apply(window, [1, 2, 3]);

// section 3: Method call and apply
/// For a normal method call, a function's `this` is the value to the
/// left of the dot.  For a call to `call` or `apply`, it's the first
/// argument to `call` or `apply`.
var obj = {f: function() { logArguments(this, arguments); }};
// hr
// there are equivalent too:
obj.f(1, 2, 3);
obj.f.call(obj, 1, 2, 3);
obj.f.apply(obj, [1, 2, 3]);

// section 4: Stealing a method the wrong way
/// Here's an evil way to apply a method from one object, to another
/// object.  Note that this code doesn't even clean up after itself
/// if `o2` already had a `show` method; it should check for that case.
var o1 = {name: 'o1', show: function() { log(this.name); }};
var o2 = {name: 'o2'};
o1.show();
o2.show = o1.show;
o2.show();
delete o2.show;

// section 5: Using `apply` to steal a method
/// This applies `o1.show` to `o2` without modifying either object.
var o1 = {name: 'o1', show: function() { log(this.name); }};
var o2 = {name: 'o2'};
o1.show();
o1.show.call(o2);
o1.show.apply(o2, []);

// section 6: Slice
// arguments isn't an Array, so this doesn't work:
function capture() {
    var args = arguments.slice(0);
    // ...
}

// instead, steal the 'slice' method from an instance of Array,
// and apply it:
function capture() {
    var args = [].slice.call(arguments, 0);
    // ...
}

// or just take it from Array's prototype
function capture() {
    var args = Array.prototype.slice.call(arguments, 0);
    // ...
}

// section 7: Passing arguments to a function
/// Use `apply` to wrap variadic functions.  `id1(f)` returns a
/// function that works just like `f` (if `f` doesn't use `this`),
/// but only when it's called with one argument.  `id2(f)` works
/// just like `f`, but only when it's called with two arguments.
/// `idn` works with any number of arguments.
function id1(fn) {
    return function(x) {
        return fn(x);
    }
}

function id2(fn) {
    return function(x, y) {
        return fn(x, y);
    }
}

function idn(fn) {
    return function() {
        return fn.apply(this, arguments);
    }
}

// section 8: Copying arguments to pass to a function
/// This code has the same effect as though the calls to `capture`
/// were calls to `fn`.
var queue = [];
function capture() {
    queue.push(Array.prototype.slice.call(arguments, 0));
}
function replay() {
    while (queue.length)
        fn.apply(null, queue.shift());
}
function fn(a, b) {
    log(a + ' + ' + b + ' = ' + (a+b));
}
// hr
capture(1,2);
capture(1,3);
capture(10,20);
replay();

// section 9: Extending `Function`'s prototype
/// This is a popular way to add methods to `Function`.
/// I don't use it in these code samples.
Function.prototype.twice = function() {
    var fn = this;
    return function() {
        return fn.call(this, fn.apply(this, arguments));
    };
}
// hr
function plus1(x) { return x+1; }
var plus2 = plus1.twice();
log(plus2(10));
