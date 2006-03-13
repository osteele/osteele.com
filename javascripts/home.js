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
	if (!(i % 2))
		row = document.createElement('tr');
	var td = document.createElement('td');
	td.appendChild(section);
	row.appendChild(td);
	table.appendChild(row);
}

document.getElementById('content').appendChild(table);
var divs = document.getElementsByClassName('more');
for (var i = 0, div; div = divs[i++]; ) {
	var h2 = div.parentNode.getElementsByTagName('h2')[0];
	var href = div.getElementsByTagName('a')[0].href;
	h2.innerHTML = '<a href="' + href + '">' + h2.innerHTML + '</a>';
}

