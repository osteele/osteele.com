$(function() {
    // code that predates jQuery (and other frameworks).  Ouch.
    document.getElementsByClassName = function(name) {
	    var result = [];
	    var re = new RegExp('\\b' + name + '\\b');
	    var elements = document.getElementsByTagName('*');
	    for (var i = 0, e; e = elements[i++]; )
		    if (e.className.match(re))
			    result.push(e);
	    return result;
    };

    var sections = document.getElementsByClassName('section');
    var table = document.createElement('table');
    var row;
    for (var i = 0, section; section = sections[i]; i++) {
	    if (!document.getElementById('content')) break;
	    if (!(i % 2))
		    row = document.createElement('tr');
	    var td = document.createElement('td');
	    td.appendChild(section);
	    row.appendChild(td);
	    table.appendChild(row);
    }
    if (row) document.getElementById('content').appendChild(table);

    var divs = document.getElementsByClassName('more');
    for (var i = 0, div; div = divs[i++]; ) {
	    var h2 = div.parentNode.getElementsByTagName('h2')[0];
	    var href = div.getElementsByTagName('a')[0].href;
	    h2.innerHTML = '<a href="' + href + '">' + h2.innerHTML + '</a>';
    }

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
	    return 0x000000;
    };

    var colors = [
        'projects #ccf',
        'sources #fcf',
        'howto #fcc',
        'illustrations #ffc',
        'presentations #cfc',
        'essays #cff'];
    for (var i = 0; i < colors.length; i++) {
        var rec = colors[i].split(' '),
        className = rec[0],
        cssColor = rec[1],
        color = parseColor(rec[1]);
        $('.areas .' + rec[0]).each(function() {
            console.info(this, color);
            OSGradient.applyGradient({'border-radius': 25,
                                      'gradient-start-color': color,
                                      'gradient-end-color': 0xffffff},
                                     this);
        });
    }
});
