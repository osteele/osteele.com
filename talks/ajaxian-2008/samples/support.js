function log() {
    var now = new Date();
    $('#output-area').show();
    $('#results').append('<span class="timestamp">' + now.getMinutes() + ':' + padzeros(now.getSeconds(), 2) + 's &rarr; </span> ');
    $('#results').append(Array.prototype.join.call(arguments,' ') + '<br/>');
    function padzeros(n, w) {
        var s = String(n);
        while (s.length < w)
            s = '0' + s;
        return s;
    }
}

function logArguments(thisObject, args) {
    log('this = ' + thisObject);
    args = Array.prototype.slice.call(args, 0);
    var argSource = args.toSource
        ? args.toSource()
        : '[' + args.join(', ') + ']';
    log('arguments = ' + argSource);
}

$(function() {
    $('#code').dblclick(function() {
        var text = $('#code').text().replace(/ +$/g, '');
        $('body').append('<textarea id="inc" wrap="off">' + text + '</textarea>');
        $('#inc').width('100%');
        $('#inc').height('100%');
        $('#inc').css('font-size', parseInt($('#code').css('font-size'), 10)*1.5 + 'px');
        $('#code').hide(false);
        $('#output-area').addClass('clickable');
        $('#output-area h3').text('Rerun').click(function() {
            $('#results').html('');
            eval($('#inc')[0].value);
        });
    });
});

function adviseBefore(fn, advice) {
    return function() {
        advice.apply(this, arguments);
        return fn.apply(this, arguments);
    }
}

function adviseAfter(fn, afterfn) {
    return function() {
        var result = fn.apply(this, arguments);
        afterfn.apply(this, arguments);
        return result;
    }
}
