<?php
/*
 * Database Manager For WordPress
 *	- wp-admin/database-manager.php
 *
 * Copyright © 2004-2005 Lester "GaMerZ" Chan
*/


### Require Admin Header
require_once('./admin.php');


### Variables Variables Variables
$title = __('Manage Database');
$this_file = $parent_file = 'database-manager.php';
$mode = trim($_GET['mode']);
$backup = array();
$backup['date'] = time();
$backup['mysqldumppath'] = 'mysqldump';
$backup['mysqlpath'] = 'mysql';
$backup['path'] = ABSPATH.'wp-backup-db/';


### Cancel
if(isset($_POST['cancel'])) {
	Header('Location: database-manager.php');
	exit();
}


### Format Bytes Into KB/MB
function format_size($rawSize) {
	if ($rawSize / 1048576 > 1)
		return round($rawSize/1048576, 1) . ' MB';
	else if ($rawSize / 1024 > 1)
		return round($rawSize/1024, 1) . ' KB';
	else
		return round($rawSize, 1) . ' bytes';
}


### Check Folder Whether There Is Any File Inside
function is_emtpy_folder($folder){
   if(is_dir($folder) ){
       $handle = opendir($folder);
       while( (gettype( $name = readdir($handle)) != 'boolean')){
               $name_array[] = $name;
       }
       foreach($name_array as $temp)
           $folder_content .= $temp;

       if($folder_content == '...')
           return true;
       else
           return false;
       closedir($handle);
   }
   else
       return true; // folder doesnt exist
}


### Form Processing 
if($_POST['do']) {
	// Lets Prepare The Variables
	$database_file = trim($_POST['database_file']);
	$optimize = $_POST['optimize'];
	$delete = $_POST['delete'];
	$nice_file_date = date('l, jS F Y @ H:i', substr($database_file, 0, 10));

	// Decide What To Do
	switch($_POST['do']) {
		case 'Backup':
			$gzip = intval($_POST['gzip']);
			if($gzip == 1) {
				$backup['filename'] = $backup['date'].'_-_'.DB_NAME.'.sql.gz';
				$backup['filepath'] = $backup['path'].'/'.$backup['filename'];
				$backup['command'] = $backup['mysqldumppath'].' -h'.DB_HOST.' -u'.DB_USER.' -p'.DB_PASSWORD.' --add-drop-table '.DB_NAME.' | gzip > '.$backup['filepath'];
			} else {
				$backup['filename'] = $backup['date'].'_-_'.DB_NAME.'.sql';
				$backup['filepath'] = $backup['path'].'/'.$backup['filename'];
				$backup['command'] = $backup['mysqldumppath'].' -h'.DB_HOST.' -u'.DB_USER.' -p'.DB_PASSWORD.' --add-drop-table '.DB_NAME.' > '.$backup['filepath'];
			}
			passthru($backup['command'], $error);
			if(!is_writable($backup['path'])) {
				$text = "<font color=\"red\">Database Failed To Backup On '".date('l, jS F Y @ H:i')."'. Backup Folder Not Writable</font>";
			} elseif(filesize($backup['filepath']) == 0) {
				unlink($backup['filepath']);
				$text = "<font color=\"red\">Database Failed To Backup On '".date('l, jS F Y @ H:i')."'. Backup File Size Is 0KB</font>";
			} elseif(!is_file($backup['filepath'])) {
				$text = "<font color=\"red\">Database Failed To Backup On '".date('l, jS F Y @ H:i')."'. Invalid Backup File Path</font>";
			} elseif($error) {
				$text = "<font color=\"red\">Database Failed To Backup On '".date('l, jS F Y @ H:i')."'</font>";
			} else {
				$text = "<font color=\"green\">Database Backed Up Successfully On '".date('l, jS F Y @ H:i')."'</font>";
			}
			break;
		case 'Restore':
			if(!empty($database_file)) {
				if(stristr($database_file, '.gz')) {
					$backup['command'] = 'gunzip < '.$backup['path'].'/'.$database_file.' | '.$backup['mysqlpath'].' -h'.DB_HOST.' -u'.DB_USER.' -p'.DB_PASSWORD.' '.DB_NAME;
				} else {
					$backup['command'] = $backup['mysqlpath'].' -h'.DB_HOST.' -u'.DB_USER.' -p'.DB_PASSWORD.' '.DB_NAME.' < '.$backup['path'].'/'.$database_file;
				}
				passthru($backup['command'], $error);
				if($error) {
					$text = "<font color=\"red\">Database On '$nice_file_date' Failed To Restore</font>";
				} else {
					$text = "<font color=\"green\">Database On '$nice_file_date' Restored Successfully</font>";
				}
			} else {
				$text = '<font color="red">No Backup Database File Selected</font>';
			}
			break;
		case 'Delete':
			if(!empty($delete)) {
				foreach($delete as $dbbackup) {
					$nice_file_date = date('l, jS F Y @ H:i', substr($dbbackup, 0, 10));
					if(is_file($backup['path'].'/'.$dbbackup)) {
						if(!unlink($backup['path'].'/'.$dbbackup)) {
							$text .= "<font color=\"red\">Unable To Delete Database Backup File On '$nice_file_date'</font><br />";
						} else {
							$text .= "<font color=\"green\">Database Backup File On '$nice_file_date' Deleted Successfully</font><br />";
						}
					} else {
						$text = "<font color=\"red\">Invalid Database Backup File On '$nice_file_date'</font>";
					}
				}
			} else {
				$text = '<font color="red">No Backup Database File Selected</font>';
			}
			break;
		case 'Download':
			if(!empty($database_file)) {
				$file_path = $backup['path'].'/'.$database_file;
				$nice_file_name = date('jS_F_Y', substr($database_file, 0, 10)).'-'.substr($database_file, 13);
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");
				header("Content-Disposition: attachment; filename=".basename($nice_file_name).";");
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize($file_path));
				@readfile($file_path);
			} else {
				$text = '<font color="red">No Backup Database File Selected</font>';
			}
			break;
		case 'Optimize':
			foreach($optimize as $key => $value) {
				if($value == 'yes') {
					$tables_string .=  ', '.$key;
				}
			}
			$selected_tables = substr($tables_string, 2);
			if(!empty($selected_tables)) {
				$optimize2 = $wpdb->query("OPTIMIZE TABLE $selected_tables");
				if(!$optimize2) {
					$text = "<font color=\"red\">Table(s) '$selected_tables' Failed To Be Optimized</font>";
				} else {
					$text = "<font color=\"green\">Table(s) '$selected_tables' Optimized Successfully</font>";
				}
			} else {
				$text = '<font color="red">No Tables Selected</font>';
			}
			break;
		case 'Run':
			$sql_queries2 = trim($_POST['sql_query']);
			$totalquerycount = 0;
			$successquery = 0;
			if($sql_queries2) {
				$sql_queries = array();
				$sql_queries2 = explode("\n", $sql_queries2);
				foreach($sql_queries2 as $sql_query2) {
					$sql_query2 = trim(stripslashes($sql_query2));
					$sql_query2 = preg_replace("/[\r\n]+/", '', $sql_query2);
					if(!empty($sql_query2)) {
						$sql_queries[] = $sql_query2;
					}
				}
				if($sql_queries) {
					foreach($sql_queries as $sql_query) {			
						if (preg_match("/^\\s*(insert|update|replace|delete|create) /i",$sql_query)) {
							$run_query = $wpdb->query($sql_query);
							if(!$run_query) {
								$text .= "<font color=\"red\">$sql_query</font><br />";
							} else {
								$successquery++;
								$text .= "<font color=\"green\">$sql_query</font><br />";
							}
							$totalquerycount++;
						} elseif (preg_match("/^\\s*(select|drop|show|grant) /i",$sql_query)) {
							$text .= "<font color=\"red\">$sql_query</font><br />";
							$totalquerycount++;						
						}
					}
					$text .= "<font color=\"blue\">$successquery/$totalquerycount Query(s) Executed Successfully</font>";
				} else {
					$text = "<font color=\"red\">Empty Query</font>";
				}
			} else {
				$text = "<font color=\"red\">Empty Query</font>";
			}
			break;
	}
}


### Switch $mode
switch($mode) { 
	// Backup Database
	case 'backup':
		$title = 'Backup Database';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 9) {
			die('<p>Insufficient Level</p>');
		}
		$backup['filename'] = $backup['date'].'_-_'.DB_NAME.'.sql';
?>
		<ul id="adminmenu2"> 
			<li><a href="database-manager.php">Manage Database</a></li> 
			<li><a href="database-manager.php?mode=backup"  class="current">Backup DB</a></li>
			<li><a href="database-manager.php?mode=optimize">Optimize DB</a></li>
			<li><a href="database-manager.php?mode=restore">Restore/Download DB</a></li> 
			<li><a href="database-manager.php?mode=delete">Delete Backup DB</a></li>
			<li class="last"><a href="database-manager.php?mode=run">Run SQL Query</a></li>
		</ul>
		<!-- Backup Database -->
		<div class="wrap">
			<h2>Backup Database</h2>
			<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<table width="100%" cellspacing="3" cellpadding="3" border="0">
				<tr style='background-color: #eee'>
					<th align="left" scope="row">Database Name:</th>
					<td><?=DB_NAME?></td>
				</tr>
				<tr style='background-color: none'>
					<th align="left" scope="row">Database Backup To:</th>
					<td><?=$backup['path']?></td>
				</tr>
				<tr style='background-color: #eee'>
					<th align="left" scope="row">Database Backup Date:</th>
					<td><?=date('jS F Y', $backup['date'])?></td>
				</tr>
				<tr style='background-color: none'>
					<th align="left" scope="row">Database Backup File Name:</th>
					<td><?=$backup['filename']?> / <?=date('jS_F_Y', substr($backup['filename'], 0, 10)).'-'.substr($backup['filename'], 13);?></td>
				</tr>
				<tr style='background-color: #eee'>
					<th align="left" scope="row">Database Backup Type:</th>
					<td>Full (Structure and Data)</td>
				</tr>
				<tr style='background-color: none'>
					<th align="left" scope="row">MYSQL Dump Location</th>
					<td><?=$backup['mysqldumppath']?></td>
				</tr>
				<tr style='background-color: #eee'>
					<th align="left" scope="row">GZIP Database Backup File?</th>
					<td><input type="radio" name="gzip" value="1">Yes&nbsp;&nbsp;<input type="radio" name="gzip" value="0" CHECKED>No</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="submit" name="do" value="Backup" class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="Cancel" class="button"></td>
				</tr>
			</table>
			</form>
		</div>
<?php
		break;
	// Optimize Database
	case 'optimize':
		$title = 'Optimize Database';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 9) {
			die('<p>Insufficient Level</p>');
		}
		$tables = $wpdb->get_results("SHOW TABLES");
?>
		<ul id="adminmenu2"> 
			<li><a href="database-manager.php">Manage Database</a></li> 
			<li><a href="database-manager.php?mode=backup">Backup DB</a></li>
			<li><a href="database-manager.php?mode=optimize"  class="current">Optimize DB</a></li>
			<li><a href="database-manager.php?mode=restore">Restore/Download DB</a></li> 
			<li><a href="database-manager.php?mode=delete">Delete Backup DB</a></li>
			<li class="last"><a href="database-manager.php?mode=run">Run SQL Query</a></li>
		</ul>
		<!-- Optimize Database -->
		<div class="wrap">
			<h2>Optimize Database</h2>
			<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
				<table width="100%" cellspacing="3" cellpadding="3" border="0">
					<tr>
					<th align="left" scope="col">Tables</th>
					<th align="left" scope="col">Options</th>
				</tr>
						<?php
							foreach($tables as $dbtable) {
								if($no%2 == 0) {
									$style = 'style=\'background-color: #eee\'';
								} else {
									$style = 'style=\'background-color: none\'';
								}
								$no++;
								$table_name = '$dbtable->Tables_in_'.DB_NAME;
								eval("\$table_name = \"$table_name\";");
								echo "<tr $style><th align=\"left\" scope=\"row\">$table_name</th>\n";
								echo "<td><input type=\"radio\" name=\"optimize[$table_name]\" value=\"no\">No&nbsp;&nbsp;<input type=\"radio\" name=\"optimize[$table_name]\" value=\"yes\" CHECKED>Yes</td></tr>";
							}
						?>
					<tr>
						<td colspan="2" align="center">Database should be optimize once every month.</td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" name="do" value="Optimize" class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="Cancel" class="button"></td>
					</tr>
				</table>
			</form>
		</div>
<?php
		break;
	// Restore Database
	case 'restore':
		$title = 'Restore/Download Database';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 9) {
			die('<p>Insufficient Level</p>');
		}
?>
		<ul id="adminmenu2"> 
			<li><a href="database-manager.php">Manage Database</a></li> 
			<li><a href="database-manager.php?mode=backup">Backup DB</a></li>
			<li><a href="database-manager.php?mode=optimize">Optimize DB</a></li>
			<li><a href="database-manager.php?mode=restore"  class="current">Restore/Download DB</a></li> 
			<li><a href="database-manager.php?mode=delete">Delete Backup DB</a></li>
			<li class="last"><a href="database-manager.php?mode=run">Run SQL Query</a></li>
		</ul>
		<!-- Restore/Download Database -->
		<div class="wrap">
			<h2>Restore/Download Database</h2>
			<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
				<table width="100%" cellspacing="3" cellpadding="3" border="0">
					<tr>
						<th align="left" scope="row" colspan="5">Choose A Backup Date To Restore Or Download</th>
					</tr>
					<tr>
						<th align="left" scope="col">No.</th>
						<th align="left" scope="col">Database File</th>
						<th align="left" scope="col">Date/Time</th>
						<th align="left" scope="col">Size</th>
						<th align="left" scope="col">Select</th>
					</tr>
					<?php
						if(!is_emtpy_folder($backup['path'])) {
							if ($handle = opendir($backup['path'])) {
								$database_files = array();
								while (false !== ($file = readdir($handle))) { 
									if ($file != '.' && $file != '..') {
										$database_files[] = $file;
									} 
								}
								closedir($handle);
								for($i = (sizeof($database_files)-1); $i > -1; $i--) {
									if($no%2 == 0) {
										$style = 'style=\'background-color: #eee\'';
									} else {
										$style = 'style=\'background-color: none\'';
									}
									$no++;
									$database_text = substr($database_files[$i], 13);
									$date_text = date('l, jS F Y @ H:i', substr($database_files[$i], 0, 10));
									$size_text = filesize($backup['path'].'/'.$database_files[$i]);
									echo "<tr $style>\n<td>$no</td>";
									echo "<td>$database_text</td>";
									echo "<td>$date_text</td>";
									echo '<td>'.format_size($size_text).'</td>';
									echo "<td><input type=\"radio\" name=\"database_file\" value=\"$database_files[$i]\" /></td>\n</tr>\n";
									$totalsize += $size_text;
								}
							} else {
								echo '<tr><td align="center" colspan="5">There Are No Database Backup Files Available</td></tr>';
							}
						} else {
							echo '<tr><td align="center" colspan="5">There Are No Database Backup Files Available</td></tr>';
						}
					?>
					</tr>
					<tr>
						<th align="left" colspan="3"><?=$no?> Backup File(s)</th>
						<th align="left"><?=format_size($totalsize)?></th>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="5" align="center"><input type="submit" name="do" value="Download" class="button">&nbsp;&nbsp;<input type="submit" class="button" name="do" value="Restore" onclick="return confirm('You Are About To Restore A Database.\nThis Action Is Not Reversible.\nAny Data Inserted After The Backup Date Will Be Gone.\n\n Choose \'Cancel\' to stop, \'OK\' to restore.')" class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="Cancel" class="button"></td>
					</tr>
				</table>
			</form>
		</div>
<?php
		break;
	// Delete Database Backup Files
	case 'delete':
		$title = 'Delete Backup Database';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 9) {
			die('<p>Insufficient Level</p>');
		}
?>
		<ul id="adminmenu2"> 
			<li><a href="database-manager.php">Manage Database</a></li> 
			<li><a href="database-manager.php?mode=backup">Backup DB</a></li>
			<li><a href="database-manager.php?mode=optimize">Optimize DB</a></li>
			<li><a href="database-manager.php?mode=restore">Restore/Download DB</a></li> 
			<li><a href="database-manager.php?mode=delete" class="current">Delete Backup DB</a></li>
			<li class="last"><a href="database-manager.php?mode=run">Run SQL Query</a></li>
		</ul>
		<!-- Delete Database Backup Files -->
		<div class="wrap">
			<h2>Delete Database Backup Files</h2>
			<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
				<table width="100%" cellspacing="3" cellpadding="3" border="0">
					<tr>
						<th align="left" scope="row" colspan="5">Choose Database Backup Files To Delete</th>
					</tr>
					<tr>
						<th align="left" scope="col">No.</th>
						<th align="left" scope="col">Database File</th>
						<th align="left" scope="col">Date/Time</th>
						<th align="left" scope="col">Size</th>
						<th align="left" scope="col">Select</th>
					</tr>
					<?php
						if(!is_emtpy_folder($backup['path'])) {
							if ($handle = opendir($backup['path'])) {
								$database_files = array();
								while (false !== ($file = readdir($handle))) { 
									if ($file != '.' && $file != '..') { 
										$database_files[] = $file;
									} 
								}
								closedir($handle); 
								for($i = (sizeof($database_files)-1); $i > -1; $i--) {
									if($no%2 == 0) {
										$style = 'style=\'background-color: #eee\'';
									} else {
										$style = 'style=\'background-color: none\'';
									}
									$no++;
									$database_text = substr($database_files[$i], 13);
									$date_text = date('l, jS F Y @ H:i', substr($database_files[$i], 0, 10));
									$size_text = filesize($backup['path'].'/'.$database_files[$i]);
									echo "<tr $style>\n<td>$no</td>";
									echo "<td>$database_text</td>";
									echo "<td>$date_text</td>";
									echo '<td>'.format_size($size_text).'</td>';
									echo "<td><input type=\"checkbox\" name=\"delete[]\" value=\"$database_files[$i]\" /></td>\n</tr>\n";
									$totalsize += $size_text;
								}
							} else {
								echo '<tr><td align="center" colspan="5">There Are No Database Backup Files Available</td></tr>';
							}
						} else {
							echo '<tr><td align="center" colspan="5">There Are No Database Backup Files Available</td></tr>';
						}
					?>
					</tr>
					<tr>
						<th align="left" colspan="3"><?=$no?> Backup File(s)</th>
						<th align="left"><?=format_size($totalsize)?></th>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="5" align="center"><input type="submit" class="button" name="do" value="Delete" onclick="return confirm('You Are About To Delete The Selected Database Backup Files.\nThis Action Is Not Reversible.\n\n Choose \'Cancel\' to stop, \'OK\' to delete.')">&nbsp;&nbsp;<input type="submit" name="cancel" Value="Cancel" class="button"></td>
					</tr>
				</table>
			</form>
		</div>
<?php
		break;
	// Run SQL Query
	case 'run':
		$title = 'Run SQL Query';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 9) {
			die('<p>Insufficient Level</p>');
		}
?>
		<ul id="adminmenu2"> 
			<li><a href="database-manager.php">Manage Database</a></li> 
			<li><a href="database-manager.php?mode=backup">Backup DB</a></li>
			<li><a href="database-manager.php?mode=optimize">Optimize DB</a></li>
			<li><a href="database-manager.php?mode=restore">Restore/Download DB</a></li> 
			<li><a href="database-manager.php?mode=delete">Delete Backup DB</a></li>
			<li class="last"><a href="database-manager.php?mode=run" class="current">Run SQL Query</a></li>
		</ul>
		<!-- Run SQL Query -->
		<div class="wrap">
			<h2>Run SQL Query</h2>
			<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
				<p><b>Seperate Multiple Queries With A New Line</b><br /><font color="green">Use Only INSERT, UPDATE, REPLACE, DELETE and CREATE statements.</font></p>
				<p align="center"><textarea cols="150" rows="30" name="sql_query"></textarea></p>
				<p align="center"><input type="submit" name="do" Value="Run" class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="Cancel" class="button"></p>
				<p>1. CREATE statement will return an error, which is perfectly normal due to the database class. To confirm that your table has been created check the Manage Database page.<br />2. UPDATE statement may return an error sometimes due to the newly updated value being the same as the previous value.</font></p>
			</form>
		</div>
<?php
		break;
	// Database Information
	default:
		$title = 'Manage Database';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 9) {
			die('<p>Insufficient Level</p>');
		}
		// Get MYSQL Version
		$sqlversion = $wpdb->get_var("SELECT VERSION() AS version");
?>
		<ul id="adminmenu2"> 
			<li><a href="database-manager.php"  class="current">Manage Database</a></li> 
			<li><a href="database-manager.php?mode=backup">Backup DB</a></li>
			<li><a href="database-manager.php?mode=optimize">Optimize DB</a></li>
			<li><a href="database-manager.php?mode=restore">Restore/Download DB</a></li> 
			<li><a href="database-manager.php?mode=delete">Delete Backup DB</a></li>
			<li class="last"><a href="database-manager.php?mode=run">Run SQL Query</a></li>
		</ul>
		<?php
			if(!empty($text)) { echo '<!-- Last Action --><div class="wrap"><h2>Last Action</h2>'.$text.'	</div>'; }
		?>
		
		<!-- Database Information -->
		<div class="wrap">
			<h2>Database Information</h2>
			<table width="100%" cellspacing="3" cellpadding="3" border="0">
				<tr>
					<th align="left" scope="col">Setting</th>
					<th align="left" scope="col">Value</th>
				</tr>
				<tr>
					<td>Database Host</td>
					<td><?=DB_HOST?></td>
				</tr>
				<tr>
					<td>Database Name</td>
					<td><?=DB_NAME?></td>
				</tr>	
				<tr>
					<td>Database User</td>
					<td><?=DB_USER?></td>
				</tr>
				<tr>
					<td>Database Type</td>
					<td>MYSQL</td>
				</tr>	
				<tr>
					<td>Database Version</td>
					<td>v<?=$sqlversion?></td>
				</tr>	
			</table>
		</div>
		<div class="wrap">
			<h2>Tables Information</h2>
			<table width="100%" cellspacing="3" cellpadding="3" border="0">
				<tr>
					<th align="left" scope="col">No.</th>
					<th align="left" scope="col">Tables</th>
					<th align="left" scope="col">Records</th>
					<th align="left" scope="col">Data Usage</th>
					<th align="left" scope="col">Index Usage</th>
					<th align="left" scope="col">Overhead</th>
				</tr>
<?php
		// If MYSQL Version More Than 3.23, Get More Info
		if($sqlversion >= '3.23') {
			$tablesstatus = $wpdb->get_results("SHOW TABLE STATUS");
			foreach($tablesstatus as  $tablestatus) {
				if($no%2 == 0) {
					$style = 'style=\'background-color: #eee\'';
				} else {
					$style = 'style=\'background-color: none\'';
				}
				$no++;
				echo "<tr $style>\n<td>$no</td>\n";
				echo "<td>$tablestatus->Name</td>\n";
				echo "<td>".number_format($tablestatus->Rows)."</td>\n";
				echo "<td>".format_size($tablestatus->Data_length)."</td>\n";
				echo "<td>".format_size($tablestatus->Index_length)."</td>\n";
				echo "<td>".format_size($tablestatus->Data_free)."</td>\n";
				$row_usage += $tablestatus->Rows;
				$data_usage += $tablestatus->Data_length;
				$index_usage +=  $tablestatus->Index_length;
				$overhead_usage += $tablestatus->Data_free;
			}
			echo "<tr><th align=\"left\" scope=\"row\">Total:</th>\n";
			echo "<th align=\"left\" scope=\"row\">$no Tables</th>\n";
			echo "<th align=\"left\" scope=\"row\">".number_format($row_usage)."</th>\n";
			echo "<th align=\"left\" scope=\"row\">".format_size($data_usage)."</th>\n";
			echo "<th align=\"left\" scope=\"row\">".format_size($index_usage)."</th>";
			echo "<th align=\"left\" scope=\"row\">".format_size($overhead_usage)."</th></tr>";
		} else {
			echo '<tr><td colspan="6" align="center"><b>Could Not Show Table Status Due To Your MYSQL Version Is Lower Than 3.23.</b></td></tr>';
		}
?>
			</table>
		</div>
<?php
} // End switch($mode)

### Require Admin Footer
require_once 'admin-footer.php';
?>