// section 1: GET with retry
/// `getWithRetry` requests the URL again if an error occurs,
/// up to a maximum of ten times.
$.getWithRetry = function(url, k) {
    var countdown = 10;
    $.ajax({url:url, success:k, error:retry});
    function retry() {
        if (--countdown >= 0) {
            log('retry');
            $.ajax({url:url, success:k, error:retry});
        }
    }
};
// hr
$.getWithRetry('services/error', log);

// section 2: Exponential backoff
/// This version of `getWithRetry` also requests the URL ten times,
/// but with an exponential backoff between requests.  This avoids
/// a DDoS attack on your own server!
var gPageLoadTime = new Date;
$.getWithRetry = function(url, k) {
    var countdown = 10;
    var delay = 1000;
    var nextTime = new Date().getTime() + delay;
    $.ajax({url:url, success:k, error:retry});
    function retry() {
        if (--countdown >= 0) {
            setTimeout(function() {
                delay *= 1.5;
                log('retry@t+' + (new Date - gPageLoadTime)/1000 + 's');
                nextTime = new Date().getTime() + delay;
                $.ajax({url:url, success:k, error:retry});
            }, Math.max(0, nextTime - new Date().getTime()));
        }
    }
};
// hr
$.getWithRetry('services/error', log);

// section 3: Failover
/// `getWithFailover` takes a list of URLs, and walks through them
/// until one succeeds.
$.getWithFailover = function(urls, k) {
    $.ajax({url:urls.shift(), success:k, error:retry});
    function retry() {
        if (urls.length)
            $.ajax({url:urls.shift(), success:k, error:retry});
    }
};
// hr
$.getWithFailover(
    ['services/error', 'services/error', 'services/time'],
    log);
