// section 1: Cached GET
/// `cachedGet` only submits the request if it hasn't seen a response
/// to that same request.  Otherwise it re-uses the previous response.
/// Note that the first two requests for 'services/random' still get
/// different answers, though, because the second goes back before
/// the first response returns.  The final request might get one of
/// these same two answers, if the five-second delay is long enough
/// for one of the first two requests to return.
var gRequestCache = {};
$.cachedGet = function(url, k) {
    if (url in gRequestCache)
        k(gRequestCache[url], 'success');
    else
        $.get(url, adviseBefore(k, function(result, status) {
            if (status == 'success')
                gRequestCache[url] = result;
        }));
};
// hr
$.cachedGet('services/random', log);
$.cachedGet('services/random', log);
$.cachedGet('services/echo/1', log);
$.cachedGet('services/echo/2', log);
setTimeout(function() {$.cachedGet('services/random', log);}, 5000);

// section 2: Joinable cache
/// This version of `cachedGet` only allows out one request for each
/// URL.  If it's called again with a URL that it's already seen,
/// it adds that to the callback handler for the first request for
/// that URL.
var gPendingRequests = {};
var gRequestCache = {};
$.cachedGet = function(url, k) {
    if (url in gRequestCache)
        k(gRequestCache[url], 'success');
    else if (url in gPendingRequests)
        gPendingRequests[url].push(k);
    else {
        var queue = [k];
        gPendingRequests[url] = queue;
        $.get(url, function(result, status) {
            if (status == 'success')
                gRequestCache[url] = result;
            while (queue.length)
                queue.shift().call(this, result, status);
            delete gPendingRequests[url];
        });
    }
};
// hr
$.cachedGet('services/random', log);
$.cachedGet('services/random', log);
$.cachedGet('services/echo/1', log);
$.cachedGet('services/echo/2', log);
$.cachedGet('services/random', log);

// section 3: Factored cache construction
/// The previous definition for `cachedGet` is complicated.  This factors
/// the complexity out into a couple of function-making functions that
/// could be used for different purposes, or combined in different ways.
function memoizedContinuation(fn) {
    var cache = {};
    return function(key, k) {
        if (key in cache)
            k(cache[key]);
        else
            fn(key, k);
    }
}
function consolidateContinuations(fn) {
    var queues = {};
    return function(key, k) {
        if (key in queues)
            queues[key].push(k);
        else {
            var queue = queues[key] = [k];
            fn(key, function(value) {
                while (queue.length)
                    queue.shift().call(this, value);
                delete queues[key];
            });
        }
    }
}
// hr
$.cachedGet = consolidateContinuations(memoizedContinuation($.get));
// hr
$.cachedGet('services/random', log);
$.cachedGet('services/random', log);
$.cachedGet('services/echo/1', log);
$.cachedGet('services/echo/2', log);
$.cachedGet('services/random', log);
