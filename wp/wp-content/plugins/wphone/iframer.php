<?php

# Visit this file in your browser to simulate a mobile device's screensize via an <iframe>

$devices = array(
	'iphone_p' => array(
		'type'   => 'iPhone: portrait (320x480)',
		'width'  => 320,
		'height' => 480
	),
	'iphone_l' => array(
		'type'   => 'iPhone: landscape (480x320)',
		'width'  => 480,
		'height' => 320
	),
	'moto'	   => array(
		'type'   => 'Motorola phone/browser (RAZR, v551, etc)',
		'width'  => 176,
		'height' => 220
	),
	'n80'	   => array(
		'type'   => 'Nokia N80 (N60WebKit)',
		'width'  => 352,
		'height' => 416
	)
);

if ( (int) $_REQUEST['w'] && (int) $_REQUEST['h'] ) {
	$choice = array(
		'type'   => "Custom size ({$_REQUEST['w']}x{$_REQUEST['h']})",
		'width'  => $_REQUEST['w'],
		'height' => $_REQUEST['h']
	);
}

elseif ( $devices[$_REQUEST['d']] )
	$choice = $devices[$_REQUEST['d']];

else $choice = $devices['iphone_p'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>WPhone iFramer test tool: <?php echo $choice['type']; ?></title>
</head>
<body>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<label for="h">CHOOSE</label>
		<select name="d" id="d">
			<option></option>
<?php
			foreach ( $devices as $this_d_key => $this_d ) {
				$selected = ( $_REQUEST['d'] == $this_d_key ) ? 'selected' : '';
				echo '<option value="' . $this_d_key . '" ' . $selected . '>' . $this_d['type'] . '</option>' . "\n\t\t\t";
			}
			echo "\n";
?>		
		</select>
		<br />OR INPUT
		<label for="w">Width</label>
		<input type="text" name="w" id="w" value="" size="5" />
		x
		<label for="h">Height</label>
		<input type="text" name="h" id="h" value="" size="5" />
		<br />
		<input type="submit" name="submit" value="view" />
	</form>
	<h2><?php echo $choice['type']; ?></h2>
	<iframe src="../../../wp-login.php" width="<?php echo $choice['width']; ?>" height="<?php echo $choice['height']; ?>">your browser does not support iframes.</iframe>
</body>
</html>