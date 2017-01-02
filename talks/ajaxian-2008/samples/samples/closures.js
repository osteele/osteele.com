// section 1: Functions share variables
/// A function sees whatever variables were around when it was created.
/// Here, `get` and `set` were created within the same call to
/// `assignAccessors`, so they see the same `x`.
var get, set;
function assignAccessors() {
    var x = 1;
    get = function() { return x; }
    set = function(y) { x = y; }
}
// hr
assignAccessors();
log(get());
set(10);
log(get());

// section 2: Invocation create distinct variables
/// `gf1.set` and `gf1.get` were created within the same call to
/// `makeAccessors`, so they see the same `x`.  `gf1.set` and `gf2.set`
/// were created within different calls, so they see different `x`'s.
function makeAccessors() {
    var x;
    return {get: function() { return x; },
            set: function(y) { x = y; }}
}
// hr
var gf1 = makeAccessors();
var gf2 = makeAccessors();
gf1.set(10);
gf2.set(20);
log(gf1.get());
log(gf2.get());
