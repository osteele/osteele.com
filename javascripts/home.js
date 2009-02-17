// Copyright 2006 by Oliver Steele.

// abbreviated from divstyle.js
function parseColor(value) {
    if (value.charAt(0) == '#') {
        var n = parseInt(value.slice(1), 16);
        switch (!isNaN(n) && value.length-1) {
        case 3:
            return ((n & 0xf00) << 8 | (n & 0xf0) << 4 | (n & 0xf)) * 17;
        case 6:
            return n;
        }
    }
	console && console.error('invalid color: ' + value);
	return 0;
};

function copyMoreLinkToHeaders() {
    var h2 = $('h2', this)[0];
    var more = $('.more a', this)[0];
    if (h2 && more) {
	    h2.innerHTML =
            '<a href="' + more.href + '">' + h2.innerHTML + '</a>';
    }
}

function copyMoreLinksToHeaders() {
    $('.areas .section').each(copyMoreLinkToHeaders);
}

function installGradients() {
    var sectionColors = [
        '.services #eee',
        '.areas.projects #ccf',
        '.areas.sources #fcf',
        '.areas.howto #fcc',
        '.areas.illustrations #ffc',
        '.areas.presentations #cfc',
        '.areas.essays #cff'];
    for (var i = 0; i < sectionColors.length; i++) {
        var rec = sectionColors[i].split(' '),
        path = rec[0].replace(/\./g, ' .'),
        cssColor = rec[1],
        color = parseColor(rec[1]);
        console.info(path);
        $(path).each(function() {
            style = {'border-radius': 25,
                     'gradient-start-color': color,
                     'gradient-end-color': 0xffffff};
            //style['gradient-end-color'] = 0x808000;
            OSGradient.applyGradient(style, this);
        });
    }
}

$(function() {
    copyMoreLinksToHeaders();
    installGradients();
});
