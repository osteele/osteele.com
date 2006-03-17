<html xmlns="http://www.w3.org/1999/xhtml">
<?php
$title = 'foldr.com';
$prefix = '1+';
$suffix = '';
if ($_SERVER['HTTP_HOST'] == 'foldl.com') {
  $title = 'foldl.com';
  $prefix = '';
  $suffix = '+1';
}
?>
  <head>
    <title><?php echo $title; ?></title>
  </head>
  <body>
    <span><?php echo $prefix?>(<a href="#" onclick="f(this); return false">&hellip;</a>)<?php echo $suffix?></span>
    
    <script type="text/javascript">
      function f(e) {
        var p = e.parentNode;
        var d = p.cloneNode(true);
        p.insertBefore(d, e);
        p.removeChild(e);
      }
    </script>
    
  </body>
</html>

