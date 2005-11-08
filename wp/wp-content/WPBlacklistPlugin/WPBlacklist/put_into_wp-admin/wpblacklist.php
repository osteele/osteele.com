<?php
/* <Edit> */
// Todo - 
require_once('../wp-includes/wp-l10n.php');
require_once('../wp-includes/wpblfunctions.php');

function add_magic_quotes($array) {
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            $array[$k] = add_magic_quotes($v);
        } else {
            $array[$k] = addslashes($v);
        }
    }
    return $array;
}

if (!get_magic_quotes_gpc()) {
    $_GET    = add_magic_quotes($_GET);
    $_POST   = add_magic_quotes($_POST);
    $_COOKIE = add_magic_quotes($_COOKIE);
}

$blfilename = 'http://www.jayallen.org/comment_spam/blacklist.txt';
$wpvarstoreset = array('action', 'blfilename', 'regextype', 'domain', 'search', 'delete_regexs', 'options');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (empty($_POST["$wpvar"])) {
		if (empty($_GET["$wpvar"])) {
			if (!isset($$wpvar)) {
				$$wpvar = '';
			}
		} else {
			$$wpvar = $_GET["$wpvar"];
		}
	} else {
		$$wpvar = $_POST["$wpvar"];
	}
}
// if this is the first time, the options array will not be created
if (!is_array($options)) {
	$options = array();
}
$standalone = 0;
switch($action) {
	case 'options':
		// delete all options from DB first
		$sql = "DELETE FROM blacklist WHERE regex_type = 'option'";
		$wpdb->query($sql);
		// save options to DB
        foreach ($options as $option) {
			$sql = "INSERT INTO blacklist (regex, regex_type) VALUES ('$option','option')";
			$wpdb->query($sql);
		}
		break;

	case 'export':
		require_once('../wp-config.php');
		$postquery ="SELECT * FROM blacklist WHERE regex_type='url'";
		$exportfile = '';
		$results = $wpdb->get_results($postquery);
		foreach ($results as $result) {
			$exportfile .=$result->regex."\n";
			}
			//Send the headers to control the download
		header('Content-Type: text/comma-separated-values');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: inline; filename="blacklist.txt"');
		echo $exportfile;
		exit();

	case 'import':
		$title = 'Manage WPBlacklist - Import';
		break;

	case 'add':
		$title = 'Manage WPBlacklist - Add';
		break;

	case 'delete':
	case 'search':
		$title = 'Manage WPBlacklist - Delete';
		break;

	default:
		$title = 'Manage WPBlacklist';
		// load options from DB
		$sql = "SELECT * FROM blacklist WHERE regex_type = 'option'";
		$results = $wpdb->get_results($sql);
		if ($results) {
			foreach ($results as $result) {
				$options[] = $result->regex;
			}
		}
		break;
}
require_once ('./admin-header.php');
if ($user_level <= 0) {
?>
	<div class="wrap">
		<p>
			Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 1, in order to be authorized to modify the Blacklist.<br />
			You can also <a href="mailto:<?php echo $admin_email ?>?subject=Plugin permission">e-mail the admin</a> to ask for a promotion.<br />
			When you&#8217;re promoted, just reload this page to play with the Blacklist. :)
		</p>
	</div>
<?php
	exit();
} // $user_level <= 0
?>
	<ul id="adminmenu2">
	  <li><a href="wpblacklist.php" class="current"><?php _e('Manage') ?></a></li>
	  <li><a href="wpblsearch.php"><?php _e('Search') ?></a></li>
	  <li class="last"><a href="wpblmoderate.php"><?php _e('Moderate') ?></a></li>
	</ul>
	<script type="text/javascript">
	<!--
	function checkAll(form)
	{
		for (i = 0, n = form.elements.length; i < n; i++) {
			if(form.elements[i].type == "checkbox") {
				if(form.elements[i].checked == true)
					form.elements[i].checked = false;
				else
					form.elements[i].checked = true;
			}
		}
	}
	//-->
	</script>
	<div class="wrap">
		<form name="options" action="wpblacklist.php?action=options" method="post" id="post">
			<div id="poststuff">
			<fieldset id="postdiv">
				<legend><strong><?php _e('Blacklist Options') ?></strong></legend>
				<?php _e('You can specify if you want comments deleted instead of being held for moderation and if you want e-mail notification on deletion.') ?>
				<br />
				<label>
					<input type="checkbox" name="options[]" value="sendmail" <?php echo (in_array('sendmail', $options) ? 'checked' : ''); ?> />
					Send e-mail on comment deletion <br />
				</label>
				<label>
					<input type="checkbox" name="options[]" value="harvestinfo" <?php echo (in_array('harvestinfo', $options) ? 'checked' : ''); ?> />
					Harvest information from deleted comments to add to blacklist <br />
				</label>
				<label>
					<input type="checkbox" name="options[]" value="deletecore" <?php echo (in_array('deletecore', $options) ? 'checked' : ''); ?> />
					Delete comments which are already held for moderation (by the WP core moderation system, for instance) <br />
				</label>
				<label>
					<input type="checkbox" name="options[]" value="deleteip" <?php echo (in_array('deleteip', $options) ? 'checked' : ''); ?> />
					Delete comments for blacklisted author IPs <br />
				</label>
				<label>
					<input type="checkbox" name="options[]" value="deleterbl" <?php echo (in_array('deleterbl', $options) ? 'checked' : ''); ?> />
					Delete comments where the author IP appears in a real-time blacklist (RBL) <br />
				</label>
				<label>
					<input type="checkbox" name="options[]" value="deletemail" <?php echo (in_array('deletemail', $options) ? 'checked' : ''); ?> />
					Delete comments where the author e-mail is blacklisted <br />
				</label>
				<label>
					<input type="checkbox" name="options[]" value="deleteurl" <?php echo (in_array('deleteurl', $options) ? 'checked' : ''); ?> />
					Delete comments where the author URL is blacklisted <br />
				</label>
				<label>
					<input type="checkbox" name="options[]" value="delcommurl" <?php echo (in_array('delcommurl', $options) ? 'checked' : ''); ?> />
					Delete comments where the comment contains URLs which are blacklisted <br />
				</label>
				<input name="saveoptions" type="submit" id="saveoptions" tabindex="9" value="Save Settings" />
			</fieldset>
		</form>
		<br/>
		<form name="export" action="wpblacklist.php?action=export" method="post" id="post">
			<div id="poststuff">
			<fieldset id="postdiv">
				<legend><strong><?php _e('Export Blacklist') ?></strong></legend>
				<input name="exportblacklist" type="submit" id="exportblacklist" tabindex="9" value="Export Blacklist" />
			</fieldset>
		</form>
		<br/>
		<form name="import" action="wpblacklist.php?action=import" method="post" id="post">
			<fieldset id="postdiv">
				<legend><strong><?php _e('Import Blacklist') ?></strong></legend>
				<?php _e('Type in or paste the name of the Blacklist file you want to import and click "Import Blacklist"') ?>
				<div>
					<input type="text" name="blfilename" size="60" tabindex="1" value="<?php echo $blfilename ?>" id="blfilename" />
				</div>
				<input name="importblacklist" type="submit" id="importblacklist" tabindex="9" value="Import Blacklist" />
<?php
if ($action == 'import') {
?>
				<p>
				<b><?php _e('Import Blacklist Results') ?></b>
				<br/><br/>
<?php
	$blfile = @file($blfilename);
	if (!$blfile) {
		_e('File not found. Please check the path or copy the file to the wp-admin directory.');
	} else {
		for ($i=0; $i<count($blfile); $i++) {
			$data = $blfile[$i];
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
					$request1 = $wpdb->query("INSERT INTO blacklist (regex,regex_type) VALUES ('$buf','url')");
					if ($request1)
						echo "<font color='green'>Added : $temp</font><BR />";
					else
						echo "<font color='red'>Error adding: $temp</font><BR />";
				}
			}
		}
		echo 'Done! <br/>';
	}
	echo '</p>';
} // $action == 'import'
?>
			</fieldset>
		</form>
		<br />
		<form name="add" action="wpblacklist.php?action=add" method="post" id="post">
			<fieldset id="postdiv">
				<legend><strong><?php _e('Add values to Blacklist') ?></strong></legend>
				<?php _e('Select whether you are adding a domain or an IP or a realtime blacklist server (RBL) to the blacklist, type in or paste the expression or the IP address or the RBL server that you want to add to the Blacklist and click "Add"') ?>
				<table>
					<tr>
						<td><?php _e('Type of Expression') ?></td>
						<td>
							<select name="regextype">
								<option <?php echo ($regextype=='url'? 'selected' : '') ?> value="url">URL</option>
								<option <?php echo ($regextype=='ip'? 'selected' : '') ?> value="ip">IP</option>
								<option <?php echo ($regextype=='rbl'? 'selected' : '') ?> value="rbl">RBL</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php _e('Domain URL/IP') ?></td>
						<td>
							<input type="text" name="domain" size="60" tabindex="1" value="<?php echo $domain ?>" id="domain" />
						</td>
					</tr>
					<tr align="left">
						<td>&nbsp;</td>
						<td>
							<input name="add" type="submit" id="add" tabindex="9" value="Add" />
						</td>
					</tr>
				</table>
<?php
if ($action == 'add') {
?>
				<p>
				<b><?php _e('Add to Blacklist Result') ?></b>
				<br/><br/>
<?php
	$domain = trim($domain);
	if (!empty($domain)) {
		if ($regextype == 'url') {
			$answer = "Expression : $domain";
			$domain = sanctify($domain);
		} else if ($regextype == 'rbl') {
			$answer = "RBL : $domain";
		} else {
			$answer = "IP : $domain";
		}
		$request = $wpdb->get_row("SELECT id FROM blacklist WHERE regex='$domain'");
		if (!$request) {
			$request = $wpdb->query("INSERT INTO blacklist (regex,regex_type) VALUES ('$domain','$regextype')");
			if (!$request) {
				$answer = $answer . " could not be added!";
			} else {
				$answer = $answer . " successfully added!";
			}
		} else {
			$answer = $answer . " already exists in blacklist!";
		}
	} else {
		$answer = "Invalid/blank expression!";
	}
	echo $answer."<br/></p>";
} // $action == 'add'
?>
			</fieldset>
		</form><br />
		<fieldset id="postdiv">
			<legend><strong><?php _e('Delete from Blacklist') ?></strong></legend>
			<form name="search" id="search" action="wpblacklist.php?action=search" method="post">
				<?php _e('Search for blacklist items to delete. Results will be of any type - expression, IP or RBL server.') ?>
				<br /><br />
				<input type="text" name="search" value="<?php echo $search; ?>" size="17" />
				<input type="submit" name="submit" value="<?php _e('Search') ?>"  />
			</form>
<?php
if ($action == 'delete') {
	if (!empty($delete_regexs)) {
		$sql = 'DELETE FROM blacklist WHERE ID IN (';
		$i = 0;
		foreach ($delete_regexs as $id) {
			$id = strval($id);
			if ($i <> 0) {
				$sql = $sql . ',';
			}
			$sql = $sql . $id;
			++$i;
		}
		$sql = $sql . ')';
		$i = $wpdb->query($sql);
		echo "<p><b>" . sprintf(__('%s blacklist item(s) deleted.'), $i) . "</b></p>";
	} else {
		echo "<p><b>" . _e('no blacklist items selected') . "</b></p>";
	}
}
?>
<?php
if (($action == 'search') || ($action == 'delete')) {
	$search = trim($search);
	if (empty($search)) {
		$sql = 'SELECT * FROM blacklist ORDER BY id ASC';
	} else {
		$search = sanctify($search);
		$search = $wpdb->escape($search);
		$sql = "SELECT * FROM blacklist  WHERE regex LIKE '%$search%' ORDER BY id DESC";
	}
	$regexs = $wpdb->get_results($sql);
	if ($regexs) {
?>
			<form name="deleteregex" id="deleteregex" action="wpblacklist.php?action=delete" method="post">
				<input name="search" type="hidden" value="<?php echo $search; ?>">
				<table width="100%" cellpadding="3" cellspacing="3">
					<tr>
					  <th scope="col">*</th>
					  <th scope="col"><?php _e('Blacklist Item') ?></th>
					  <th scope="col"><?php _e('Type') ?></th>
					</tr>
<?php
		foreach ($regexs as $regex) {
			$bgcolor = ('#eee' == $bgcolor) ? 'none' : '#eee';
?>
					<tr style='background-color: <?php echo $bgcolor; ?>'>
						<td>
							<input type="checkbox" name="delete_regexs[]" value="<?php echo $regex->id ?>" />
						</td>
						<td><?php echo $regex->regex ?></td>
						<td><?php echo $regex->regex_type ?></td>
					</tr>
<?php
		} // foreach
?>
				</table>
				<p>
					<a href="javascript:;" onclick="checkAll(document.getElementById('deleteregex')); return false; "><?php _e('Invert Checkbox Selection') ?></a>
				</p>
				<p style="text-align: right;">
					<input type="submit" name="Submit" value="<?php _e('Delete Checked Items') ?>" onclick="return confirm('<?php _e("You are about to delete these blacklist items permanently \\n  \'Cancel\' to stop, \'OK\' to delete.") ?>')" />
				</p>
			</form>
<?php
	} else {
?>
			<p>
				<strong><?php _e('No results found.') ?></strong>
			</p>
<?php
	} // if ($regexs)
}
?>
		</fieldset>
	</div>
<?php
/* </Edit> */
include('admin-footer.php');
?>
