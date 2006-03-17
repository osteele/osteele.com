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
    
    <span style=""><?php echo $prefix?>(<a href="#" onclick="f(this)">&hellip;</a>)<?php echo $suffix?></span>
    
    <script type="text/javascript">
      function f(e) {
        var d = e.parentNode.cloneNode(true);//document.createElement('div');
        e.parentNode.insertBefore(d, e);
        e.parentNode.removeChild(e);
      }
    </script>
    
  </body>
</html>

