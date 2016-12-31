// section 1: Unthrottled GET
/// GET ten times in a row.  They all happen pretty quickly.
for (var i = 0; i < 10; i++)
    $.get('services/time', log);

// section 2: Manual throttling
/// Only send one GET per second.  This code is awful -- it's hard
/// to see that this is just a `for` loop (the same as the last slide);
/// and it wouldn't work if the calls to GET were coming from different
/// parts of the program.
var gCounter = 0;
function runNext() {
    if (gCounter < 10) {
        setTimeout(function() {
            $.get('services/time', log);
            runNext();
        }, 1000);
        gCounter++;
    }
}
runNext();

// section 3: A throttling wrapper for GET
/// The throttling has been factored into `$.throttled`.  This restores
/// the call site to simplicity.  `$.throttled` itself is pretty complex.
var gQueue = [];
var gNextTime = 0;
$.throttled = function(url, k) {
    gQueue.push([url, k]);
    if (gQueue.length == 1)
        schedule();
    function schedule() {
        setTimeout(function() {
            gNextTime = new Date().getTime() + 1000;
            var entry = gQueue.shift();
            $.get(entry[0], entry[1]);
            if (gQueue.length)
                schedule();
        }, Math.max(0, gNextTime - new Date().getTime()));
    }
};
// hr
for (var i = 0; i < 10; i++)
    $.throttled('services/time', log);

// section 4: Throttle-wrapper constructor
/// This factors the throttling logic out of `$.throttled`, into
/// a constructor that can throttle *any* function.  Now the program
/// is in three layers: `makeThrottled` (which is even *more* complex,
/// but can be used to throttle any function); `$.throttled`; and the
/// application-level code that uses `$.throttled`.
function makeThrottled(fn, interval) {
    var queue = [];
    var nextTime = 0;
    return function() {
        queue.push(Array.prototype.slice.call(arguments, 0));
        if (queue.length == 1) schedule();
    }
    function schedule() {
        setTimeout(function() {
            nextTime = new Date().getTime() + interval;
            fn.apply(null, queue.shift());
            if (queue.length) schedule();
        }, Math.max(0, nextTime - new Date().getTime()));
    }
}
// hr
$.throttled = makeThrottled($.get, 1000);
// hr
for (var i = 0; i < 10; i++)
    $.throttled('services/time', log);

// section 5: Outstanding-count throttle
/// Here's a different kind of throttle.  This one only allows
/// a certain number (two) of outstanding requests; it queues
/// subsequent requests until one of outstanding requests
/// has returned. \br A production version would need to check
/// for error conditions too.
var gQueue = [];
var gOutstanding = 0;
$.throttled = function(url, k) {
    function k2() {
        gOutstanding--;
        k.apply(this, arguments);
        if (gOutstanding < 2 && gQueue.length) {
            var entry = gQueue.shift();
            $.get(entry[0], entry[1]);
        }
    }
    if (gOutstanding < 2) {
        gOutstanding++;
        $.get(url, k2);
    } else
        gQueue.push([url, k2]);
};
// hr
for (var i = 0; i < 10; i++)
    $.throttled('services/sleep/2', log);

// section 6: Outstanding-count throttle constructor
/// This code factors the throttling code into a throttled
/// function constructor.
function makeLimited(fn, count) {
    var queue = [];
    var outstanding = 0;
    return function() {
        var args = Array.prototype.slice.call(arguments, 0);
        // replace the last arg by one that runs the
        // next queued fn
        args.push(adviseAfter(args.pop(), next));
        if (outstanding < count) {
            outstanding++;
            fn.apply(this, args);
        } else
            queue.push(args);
    }
    function next() {
        if (queue.length)
            fn.apply(null, queue.shift());
    }
}
// hr
$.throttled = makeLimited($.get, 2);    
// hr
for (var i = 0; i < 10; i++)
    $.throttled('services/sleep/2', log);

// hr
function adviseAfter(fn, afterfn) {
    return function() {
        var result = fn.apply(this, arguments);
        afterfn.apply(this, arguments);
        return result;
    }
}
