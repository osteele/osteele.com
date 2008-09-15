<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Is It Past 12:00pm Yet?</title>
    <!-- inspired by http://www.hasthelhcdestroyedtheearth.com/,
	 suggested by Margaret Minsky,
	 coded by Oliver Steele. -->
    <style type="text/css">
      #content {text-align:center; font-weight:bold; font-size:120pt; font-family:Arial,sans-serif}
    </style>
  </head>
  <body>
    <div id="content">This page requires JavaScript</div>
    
    <script type="text/javascript">
      document.getElementById('content').innerHTML = new Date().getHours() >= 12 ? 'Yes' : 'No';
    </script>
  </body>
</html>
