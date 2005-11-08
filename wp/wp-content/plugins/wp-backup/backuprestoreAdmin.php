<?php
/* <Edit> */
require_once('../wp-config.php');
global $siteurl, $user_level;
$siteurl = get_settings('siteurl');
function make_sql($table)
{
	/*
		Reads the Database table in $table and creates
		SQL Statements for recreating structure and data
		Taken partially from phpMyAdmin and partially from
		Alain Wolf, Zurich - Switzerland
		Website: http://restkultur.ch/personal/wolf/scripts/db_backup/
	*/

		$sql_statements  = "";

		// Add SQL statement to drop existing table
		$sql_statements .= "\n";
		$sql_statements .= "\n";
		$sql_statements .= "#\n";
		$sql_statements .= "# Delete any existing table " . backquote($table) . "\n";
		$sql_statements .= "#\n";
		$sql_statements .= "\n";
		$sql_statements .= "DROP TABLE IF EXISTS " . backquote($table) . ";\n";

		// Table structure

		// Comment in SQL-file
		$sql_statements .= "\n";
		$sql_statements .= "\n";
		$sql_statements .= "#\n";
		$sql_statements .= "# Table structure of table " . backquote($table) . "\n";
		$sql_statements .= "#\n";
		$sql_statements .= "\n";

		// Get table structure
		$query = "SHOW CREATE TABLE " . backquote($table);
		$result = mysql_query($query, $GLOBALS["db_connect"]);
		if ($result == FALSE) {
			echo "Error getting table structure of $table!\n".mysql_errno() . ": " . mysql_error(). "\n";
		} else {
			if (mysql_num_rows($result) > 0) {
				$sql_create_arr = mysql_fetch_array($result);
				$sql_statements .= $sql_create_arr[1];
			}
			mysql_free_result($result);
			$sql_statements .= " ;";
		} // ($result == FALSE)

		// Table data contents

		// Get table contents
		$query = "SELECT * FROM " . backquote($table);
		$result = mysql_query($query, $GLOBALS["db_connect"]);
		if ($result == FALSE) {
			echo "Error getting records of $table!\n".mysql_errno() . ": " . mysql_error(). "\n";
		} else {
			$fields_cnt = mysql_num_fields($result);
			$rows_cnt   = mysql_num_rows($result);
		} // if ($result == FALSE)

		// Comment in SQL-file
		$sql_statements .= "\n";
		$sql_statements .= "\n";
		$sql_statements .= "#\n";
		$sql_statements .= "# Data contents of table " . $table . " (" . $rows_cnt . " records)\n";
		$sql_statements .= "#\n";

		// Checks whether the field is an integer or not
		for ($j = 0; $j < $fields_cnt; $j++) {
			$field_set[$j] = backquote(mysql_field_name($result, $j));
			$type          = mysql_field_type($result, $j);
			if ($type == 'tinyint' || $type == 'smallint' || $type == 'mediumint' || $type == 'int' ||
				$type == 'bigint'  ||$type == 'timestamp') {
				$field_num[$j] = TRUE;
			} else {
				$field_num[$j] = FALSE;
			}
		} // end for

		// Sets the scheme
		$entries = 'INSERT INTO ' . backquote($table) . ' VALUES (';
		$search			= array("\x00", "\x0a", "\x0d", "\x1a"); 	//\x08\\x09, not required
		$replace		= array('\0', '\n', '\r', '\Z');
		$current_row	= 0;
		while ($row = mysql_fetch_row($result)) {
			$current_row++;
			for ($j = 0; $j < $fields_cnt; $j++) {
				if (!isset($row[$j])) {
					$values[]     = 'NULL';
				} else if ($row[$j] == '0' || $row[$j] != '') {
					// a number
					if ($field_num[$j]) {
						$values[] = $row[$j];
					}
					else {
						$values[] = "'" . str_replace($search, $replace, sql_addslashes($row[$j])) . "'";
					} //if ($field_num[$j])
			} else {
					$values[]     = "''";
				} // if (!isset($row[$j]))
			} // for ($j = 0; $j < $fields_cnt; $j++)
			$sql_statements .= " \n" . $entries . implode(', ', $values) . ') ;';
			unset($values);
		} // while ($row = mysql_fetch_row($result))
		mysql_free_result($result);

		// Create footer/closing comment in SQL-file
		$sql_statements .= "\n";
		$sql_statements .= "#\n";
		$sql_statements .= "# End of data contents of table " . $table . "\n";
		$sql_statements .= "# --------------------------------------------------------\n";
		$sql_statements .= "\n";
		return $sql_statements;
} //function make_sql($table)

function sql_addslashes($a_string = '', $is_like = FALSE)
{
	/*
		Better addslashes for SQL queries.
		Taken from phpMyAdmin.
	*/
    if ($is_like) {
        $a_string = str_replace('\\', '\\\\\\\\', $a_string);
    } else {
        $a_string = str_replace('\\', '\\\\', $a_string);
    }
    $a_string = str_replace('\'', '\\\'', $a_string);

    return $a_string;
} // function sql_addslashes($a_string = '', $is_like = FALSE)


function backquote($a_name)
{
	/*
		Add backqouotes to tables and db-names in
		SQL queries. Taken from phpMyAdmin.
	*/
    if (!empty($a_name) && $a_name != '*') {
        if (is_array($a_name)) {
             $result = array();
             reset($a_name);
             while(list($key, $val) = each($a_name)) {
                 $result[$key] = '`' . $val . '`';
             }
             return $result;
        } else {
            return '`' . $a_name . '`';
        }
    } else {
        return $a_name;
    }
} // function backquote($a_name, $do_it = TRUE)

if (!function_exists('add_magic_quotes')) {
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
}

if (!get_magic_quotes_gpc()) {
    $HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
    $HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
    $HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$wpvarstoreset = array('action', 'safe_mode', 'withcomments', 'posts', 'poststart', 'postend', 'content', 'edited_post_title', 'comment_error', 'profile', 'trackback_url', 'excerpt', 'showcomments', 'commentstart', 'commentend', 'commentorder');

for ($i=0; $i<count($wpvarstoreset); $i += 1) {
    $wpvar = $wpvarstoreset[$i];
    if (!isset($$wpvar)) {
        if (empty($HTTP_POST_VARS["$wpvar"])) {
            if (empty($HTTP_GET_VARS["$wpvar"])) {
                $$wpvar = '';
            } else {
                $$wpvar = $HTTP_GET_VARS["$wpvar"];
            }
        } else {
            $$wpvar = $HTTP_POST_VARS["$wpvar"];
        }
    }
}

switch($action) {
	case 'backupdatabase':
			require_once ('admin-header.php');
			require_once('brvars.php');
			if (!$db_connect = @mysql_pconnect(DB_HOST, DB_USER, DB_PASSWORD)) {
				echo "Could not connect to MySQL server!\n           " . mysql_error() . "\n";
				}
			mysql_select_db(DB_NAME,$db_connect);
			$datum = date(dmY);
			$file_name = $datum."wordpress.sql";
			//Begin new backup of MySql
			$tables = mysql_list_tables(DB_NAME);
			$sql_file  = "# WordPress MySQL database backup\n";
			$sql_file .= "#\n";
			$sql_file .= "# Generated: " . date("l j. F Y H:i T") . "\n";
			$sql_file .= "# Hostname: " . DB_HOST . "\n";
			$sql_file .= "# Database: " . backquote(DB_NAME) . "\n";
			$sql_file .= "# --------------------------------------------------------\n";
			for ($i = 0; $i < mysql_num_rows($tables); $i++) {
				$curr_table = mysql_tablename($tables, $i);
					// Increase script execution time-limit to 15 min for every table.
					if ( !ini_get('safe_mode')) @set_time_limit(15*60);
					// Create the SQL statements
					$sql_file .= "# --------------------------------------------------------\n";
					$sql_file .= "# Table: " . backquote($curr_table) . "\n";
					$sql_file .= "# --------------------------------------------------------\n";
					$sql_file .= make_sql($curr_table);
			}
			$cachefp = fopen($path.$file_name, "w");
			fwrite($cachefp, $sql_file);
			echo "Backup Successful. <br/><a href=\"$siteurl/".$backup_folder.$file_name."\">Download the new Backup Sql File</a>";
			if ($user_level > 0) {
				include('backuprestoreForm.php');
				}
			 else {


			?>
			<div class="wrap">
						<p>Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 5, in order to be authorized to Backup or Restore.<br />
							You can also <a href="mailto:<?php echo $admin_email ?>?subject=Blog posting permission">e-mail the admin</a> to ask for a promotion.<br />
							When you&#8217;re promoted, just reload this page to Backup or restore WordPress. :)</p>
			</div>
			<?php

					}

			break;

	case 'backupfiles':
			require_once ('admin-header.php');
			require_once ('Tar.php');
			require_once('brvars.php');
			$title = 'Backup WordPress Files';
			$standalone = 0;
			$dir_path = ABSPATH;
			if ( !ini_get('safe_mode')) @set_time_limit(15*60);
			// generate filesuffix if it should be used
			$datum = date(dmY);
			$file_name = $path.$datum."wordpress.tar.gz";
			echo "<div class=\"wrap\">";
			//echo substr($dir_path,0,(strlen($dir_path)-1))."<br/>";
			$tar = new Archive_Tar($file_name, "true");
			$v_list[0] = substr($dir_path,0,(strlen($dir_path)-1));
			$exclude[0] = 'backup';
			if ($tar->create($v_list, $exclude)) {
				echo date("H:i:s") . ": Archiving succesfull.\n";
				echo "<a href=\"$siteurl/".$backup_folder.$datum."wordpress.tar.gz\">Download the new Backup File</a>";
				}
			else
				echo "There was an error in backing up files!";
			if ($user_level > 0) {
				include('backuprestoreForm.php');
				}
			 else {


			?>
			<div class="wrap">
						<p>Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 5, in order to be authorized to Backup or Restore.<br />
							You can also <a href="mailto:<?php echo $admin_email ?>?subject=Blog posting permission">e-mail the admin</a> to ask for a promotion.<br />
							When you&#8217;re promoted, just reload this page to Backup or restore WordPress. :)</p>
			</div>
			<?php

					}

			break;

	case 'restore':
			require_once ('admin-header.php');
			require_once('brvars.php');
			$title = 'Backup WordPress Files';
			$standalone = 0;
			echo "<div class=\"wrap\">";
			$filename = $_POST["restoredbfile"];
			echo "mysql -hDB_HOST -uDB_USER -pDB_PASSWORD -f DB_NAME < $path$filename";
			passthru("mysql -hDB_HOST -uDB_USER -pDB_PASSWORD -f DB_NAME < $path$filename");
			echo "Restore successful. <br/><a href=$siteurl>Click here to view your site</a>";
			if ($user_level > 0) {
				include('backuprestoreForm.php');
				}
			 else {


			?>
			<div class="wrap">
						<p>Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 5, in order to be authorized to Backup or Restore.<br />
							You can also <a href="mailto:<?php echo $admin_email ?>?subject=Blog posting permission">e-mail the admin</a> to ask for a promotion.<br />
							When you&#8217;re promoted, just reload this page to Backup or restore WordPress. :)</p>
			</div>
			<?php

					}

			break;

    default:
		$title = 'Backup and Restore MySql database and WordPress Files';
        $standalone = 0;
        require_once ('admin-header.php');
		require_once('brvars.php');

        if ($user_level > 0) {
 			include('backuprestoreForm.php');
            }
 else {


?>
<div class="wrap">
            <p>Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 5, in order to be authorized to Backup or Restore.<br />
				You can also <a href="mailto:<?php echo $admin_email ?>?subject=Blog posting permission">e-mail the admin</a> to ask for a promotion.<br />
				When you&#8217;re promoted, just reload this page to Backup or restore WordPress. :)</p>
</div>
<?php

        }

        break;
} // end switch
/* </Edit> */
include('admin-footer.php');
?>
