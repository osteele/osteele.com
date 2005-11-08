<?php
require_once('../../wp-config.php');
get_currentuserinfo();
global $user_level;

if ($user_level < 8)
	die ("Sorry, you must be logged in and at least a level 8 user to run the Blacklist Install script.");

require_once('../../wp-includes/wpblfunctions.php');
$step = $_GET['step'];
if (!$step)
	$step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WordPress &rsaquo; Blacklist Installer</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style media="screen" type="text/css">
    <!--
	body {
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 15%;
		margin-right: 15%;
	}
	#logo {
		margin: 0;
		padding: 0;
		background-image: url(http://wordpress.org/images/logo.png);
		background-repeat: no-repeat;
		height: 60px;
		border-bottom: 4px solid #333;
	}
	#logo a {
		display: block;
		height: 60px;
	}
	#logo a span {
		display: none;
	}
	p, li {
		line-height: 140%;
	}
	.success {color: green}
	.error {color: red}
    -->
	</style>
</head>
<body>
<h1 id="logo"><a href="http://wordpress.org"><span>WordPress</span></a></h1>
<?php
switch($step) {
	case 0:
?>
<p>Welcome to the WordPress Blacklist installer/updater utility. To get started, we just need one bit of information.</p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?step=1">
  <p>You can use the installer even if you are upgrading - your existing data will be left intact. </p>
  <table>
    <tr>
      <th scope="row">Blacklist File</th>
      <td><input type="text" name="blfile" size="60" tabindex="1" value="http://www.jayallen.org/comment_spam/blacklist.txt" id="blfile" /></td>
      <td>The location of the blacklist import file, either on your server or on a remote server.</td>
    </tr>
  </table>
  <input name="submit" type="submit" value="Submit" />
</form>
<?php
	break;

	case 1:
echo	$bl_file = $_POST['blfile'];
?>
<p>All right sparky, here we go with the installation/upgrade! Do you feel lucky today? :p</p>
<?php
	$sql = "CREATE TABLE IF NOT EXISTS `blacklist` (`id` int(11) NOT NULL auto_increment," .
		"`regex` varchar(200) NOT NULL default '',`regex_type` enum('ip','url','rbl','option') NOT NULL default 'url'," .
		"KEY `id` (`id`), FULLTEXT KEY `regex` (`regex`)) TYPE=MyISAM AUTO_INCREMENT=1046";
	$wpdb->query($sql);
	// update table structure for WPBlacklist 2.1 onwards
    $sql = "ALTER TABLE `blacklist` CHANGE COLUMN `regex_type` `regex_type` enum('ip','url','rbl','option') NOT NULL DEFAULT 'url'";
	$wpdb->query($sql);
	// clean up blacklist table to remove blank entries
    $wpdb->query("DELETE FROM `blacklist` WHERE TRIM(`regex`) = ''");
	/*if ($results) {
		foreach ($results as $result) {
			$temp = trim($result->regex);
			if (empty($temp)) {
				$wpdb->query("DELETE FROM blacklist WHERE id=$result->id");
			}
		}
	}*/
	echo "Database stuff done. Adding to blacklist ... <br />";
	$domain = file($bl_file);
	for ($i=0; $i < count($domain); $i++) {
		//echo "original : $domain[$i]-<br/>";
		$data = $domain[$i];
		$temp = "";
		for ($j=0; $j<strlen($data); $j++)  {
			 if ($data[$j]==" " || $data[$j] == "#")
				break;
			 else
				$temp.=$data[$j];
			 continue;
		}
		$temp = trim($temp);
		if (!empty($temp)) {
			$buf = sanctify($temp);
			// echo "Regex: $temp<br />";
			$request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$buf'");
			if (!$request) {
				$res = $wpdb->query("INSERT INTO blacklist (regex, regex_type) VALUES ('$buf','url')");
				if ($res) {
					echo "<span class='success'>Imported : $temp</span><br/>";
				} else {
					echo "<span class='error'>Error importing : $temp</span><br/>";
				}
			} else {
				echo "<span class='error'>Not imported : $temp already exists!</span><br/>";
			}
		}
	}
	echo 'All done!<br/>';
	break;
}
?>
