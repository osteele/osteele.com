var $a = new Array();
var $h = location.hash.substr(2) || '0';

function go ($hash) {
 $h = $hash;
 location.hash = 'f' + $hash;
}

function getLoad() {

 var $nav = document.getElementById('navigation').firstChild.childNodes;

 for (var $i = 0; $i < $nav.length; $i++)
  $a.push(getFoils($nav[$i]));
 debug("<pre>" + var_dump($a, "") + "</pre>");
}

function debug ($msg) {
 document.getElementById('debug').innerHTML += $msg;
}

function var_dump($v, $p) {
 var $o = '';
 for (var $i in $v)
  if (typeof($v[$i]) == 'object')
   $o += $p + $i + ": \n" + var_dump($v[$i], $p + "   ") + "\n";
  else
   $o += $p + $i + ": <i>" + $v[$i] + "</i>\n";
 return $o;
}

function Foil () {
}

function Foilgroup () {
}

function getFoils($node) {
 if ($node.className.indexOf("foilgroup") > -1) {
  var $r = new Foilgroup();
  var $t = new Array();
  $r.id = $node.getAttribute('id');
  $r.title = $node.childNodes[1].innerHTML;
  var $c = $node.lastChild.childNodes;
  for (var $i = 0; $i < $c.length; $i++)
   $t.push(getFoils($c[$i]));
  $r.foils = $t;
  return $r;
 } else if ($node.className.indexOf("foil") > -1) {
  var $f = new Foil();
  $f.id = $node.getAttribute("id");
  $f.title = $node.lastChild.innerHTML;
  return $f;
 }
}

function up() {
 $s = $h.split('_');
 $s.pop();
 $h = $s.join('_');
 if ($h.length == 0)
  $h = '0';
 go($h);
}

function next() {
 $s = $h.split('_');

 for ($i = 1; $i < $s.length; $i++)
  $s[$i] = $s[$i] - 1;

 $l = $s.pop();
 $f = $a;
 for ($i in $s)
  $f = $f[$s[$i]].foils;

 $g = $f[$l];
 if($g.foils)
  $g = $g.foils[0];
 else
  $g = $f[++$l];

 while ($s.length > 0 && $g == null) {
  $l = $s.pop();
  $f = $a;
  for ($i in $s)
   $f = $f[$s[$i]].foils;
  $g = $f[++$l];
 }
 if ($g != null)
  go($g.id.substr(1));
}

function prev() {
 $s = $h.split('_');

 for ($i = 1; $i < $s.length; $i++)
  $s[$i] = $s[$i] - 1;

 $l = $s.pop();
 $f = $a;
 for ($i in $s)
  $f = $f[$s[$i]].foils;

 if ($l > 0) {
  $g = $f[--$l];
  while ($g.foils)
   $g = $g.foils[$g.foils.length-1];
 } else if ($s.length > 0) {
  $l = $s.pop();
  $f = $a;
  for ($i in $s)
   $f = $f[$s[$i]].foils;
  $g = $f[$l];
 }

 if ($g != null)
  go($g.id.substr(1));
}
