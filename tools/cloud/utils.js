function parseDate(str) {
  var u = $(str.match(/\d+/g)).map(function(){return parseInt(this, 10)});
  u[1] -= 1;
  var d = new Date(u[0], u[1], u[2], u[3], u[4], u[5]);
  return new Date(d.getTime() + d.getTimezoneOffset() * 60000);
}

function parseXML(text) {
//   console.info('r', text);
  try { //Internet Explorer
    var xmlDom = new ActiveXObject("Microsoft.XMLDOM");
    xmlDom.async = "false";
    xmlDom.loadXML(txt);
    return xmlDom;
  } catch(e) {
    var parser = new DOMParser();
    return parser.parseFromString(text, "text/xml");
  }
}

Array.prototype.max = function() {
  return Math.max.apply(null, this);
  var max = -Infinity, n;
  for (var i = this.length; i--; )
    if ((n = this[i]) > max)
      max = n;
  return max;
}
