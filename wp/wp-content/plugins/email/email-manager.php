<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-EMail 2.20										|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://lesterchan.net															|
|																							|
|	File Information:																	|
|	- Manages Your E-Mail Logs													|
|	- wp-content/plugins/email/email-manager.php							|
|																							|
+----------------------------------------------------------------+
*/


### Check Whether User Can Manage EMail
if(!current_user_can('manage_email')) {
	die('Access Denied');
}


### E-Mail Variables
$base_name = plugin_basename('email/email-manager.php');
$base_page = 'admin.php?page='.$base_name;
$email_page = intval($_GET['emailpage']);
$email_sortby = trim($_GET['by']);
$email_sortby_text = '';
$email_sortorder = trim($_GET['order']);
$email_sortorder_text = '';
$email_log_perpage = intval($_GET['perpage']);
$email_sort_url = '';


### Form Sorting URL
if(!empty($email_sortby)) {
	$email_sort_url .= '&amp;by='.$email_sortby;
}
if(!empty($email_sortorder)) {
	$email_sort_url .= '&amp;order='.$email_sortorder;
}
if(!empty($email_log_perpage)) {
	$email_sort_url .= '&amp;perpage='.$email_log_perpage;
}


### Get Order By
switch($email_sortby) {
	case 'id':
		$email_sortby = 'email_id';
		$email_sortby_text = __('ID', 'wp-email');
		break;
	case 'fromname':
		$email_sortby = 'email_yourname';
		$email_sortby_text = __('From Name', 'wp-email');
		break;
	case 'fromemail':
		$email_sortby = 'email_youremail';
		$email_sortby_text = __('From E-Mail', 'wp-email');
		break;
	case 'toname':
		$email_sortby = 'email_friendname';
		$email_sortby_text = __('To Name', 'wp-email');
		break;
	case 'toemail':
		$email_sortby = 'email_friendemail';
		$email_sortby_text = __('To E-Mail', 'wp-email');
		break;
	case 'postid':
		$email_sortby = 'email_postid';
		$email_sortby_text = __('Post ID', 'wp-email');
		break;
	case 'posttitle':
		$email_sortby = 'email_posttitle';
		$email_sortby_text = __('Post Title', 'wp-email');
		break;
	case 'ip':
		$email_sortby = 'email_ip';
		$email_sortby_text = __('IP', 'wp-email');
		break;
	case 'host':
		$email_sortby = 'email_host';
		$email_sortby_text = __('Host', 'wp-email');
		break;
	case 'status':
		$email_sortby = 'email_status';
		$email_sortby_text = __('Status', 'wp-email');
		break;
	case 'date':
	default:
		$email_sortby = 'email_timestamp';
		$email_sortby_text = __('Date', 'wp-email');
}


### Get Sort Order
switch($email_sortorder) {
	case 'asc':
		$email_sortorder = 'ASC';
		$email_sortorder_text = __('Ascending', 'wp-email');
		break;
	case 'desc':
	default:
		$email_sortorder = 'DESC';
		$email_sortorder_text = __('Descending', 'wp-email');
}


### Form Processing 
if(!empty($_POST['delete_logs'])) {
	if(trim($_POST['delete_logs_yes']) == 'yes') {
		$delete_logs = $wpdb->query("DELETE FROM $wpdb->email");
		if($delete_logs) {
			$text = '<font color="green">'.__('All E-Mail Logs Have Been Deleted.', 'wp-email').'</font>';
		} else {
			$text = '<font color="red">'.__('An Error Has Occured While Deleting All E-Mail Logs.', 'wp-email').'</font>';
		}
	}
}


### Get E-Mail Logs Data
$total_email_success = $wpdb->get_var("SELECT COUNT(email_id) FROM $wpdb->email WHERE email_status = '".__('Success', 'wp-email')."'");
$total_email_failed = $wpdb->get_var("SELECT COUNT(email_id) FROM $wpdb->email WHERE email_status = '".__('Failed', 'wp-email')."'");
$total_email = $total_email_success+$total_email_failed;


### Checking $email_page and $offset
if(empty($email_page) || $email_page == 0) { $email_page = 1; }
if(empty($offset)) { $offset = 0; }
if(empty($email_log_perpage) || $email_log_perpage == 0) { $email_log_perpage = 20; }


### Determin $offset
$offset = ($email_page-1) * $email_log_perpage;


### Determine Max Number Of Polls To Display On Page
if(($offset + $email_log_perpage) > $total_email) { 
	$max_on_page = $total_email; 
} else { 
	$max_on_page = ($offset + $email_log_perpage); 
}


### Determine Number Of Polls To Display On Page
if (($offset + 1) > ($total_email)) { 
	$display_on_page = $total_email; 
} else { 
	$display_on_page = ($offset + 1); 
}


### Determing Total Amount Of Pages
$total_pages = ceil($total_email / $email_log_perpage);


### Get The Logs
$email_logs = $wpdb->get_results("SELECT * FROM $wpdb->email ORDER BY $email_sortby $email_sortorder LIMIT $offset, $email_log_perpage");
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<!-- Manage E-Mail -->
<div class="wrap">
	<h2><?php _e('E-Mail Logs', 'wp-email'); ?></h2>
	<p><?php printf(__('Dispaying <strong>%s</strong> To <strong>%s</strong> Of <strong>%s</strong> E-Mail Logs', 'wp-email'), $display_on_page, $max_on_page, $total_email); ?></p>
	<p><?php printf(__('Sorted By <strong>%s</strong> In <strong>%s</strong> Order', 'wp-email'), $email_sortby_text, $email_sortorder_text); ?></p>
	<table width="100%"  border="0" cellspacing="3" cellpadding="3">
	<tr class="thead">
		<th width="5%"><?php _e('ID', 'wp-email'); ?></th>
		<th width="17%"><?php _e('From', 'wp-email'); ?></th>
		<th width="17%"><?php _e('To', 'wp-email'); ?></th>
		<th width="17%"><?php _e('Date / Time', 'wp-email'); ?></th>
		<th width="17%"><?php _e('IP / Host', 'wp-email'); ?></th>
		<th width="17%"><?php _e('Post Title', 'wp-email'); ?></th>
		<th width="10%"><?php _e('Status', 'wp-email'); ?></th>
	</tr>
	<?php
		if($email_logs) {
			$i = 0;
			foreach($email_logs as $email_log) {
				if($i%2 == 0) {
					$style = 'style=\'background: none\'';					
				}  else {
					$style = 'style=\'background-color: #eee\'';
				}
				$email_id = intval($email_log->email_id);
				$email_yourname = stripslashes($email_log->email_yourname);
				$email_youremail = stripslashes($email_log->email_youremail);
				$email_friendname = stripslashes($email_log->email_friendname);
				$email_friendemail = stripslashes($email_log->email_friendemail);
				$email_postid = intval($email_log->email_postid);
				$email_posttitle = htmlspecialchars(stripslashes($email_log->email_posttitle));
				$email_date = gmdate(sprintf(__('%s @ %s', 'wp-email'), get_option('date_format'), get_option('time_format')), $email_log->email_timestamp);
				$email_ip = $email_log->email_ip;
				$email_host = $email_log->email_host;
				$email_status = stripslashes($email_log->email_status);
				echo "<tr $style>\n";
				echo "<td>$email_id</td>\n";
				echo "<td>$email_yourname<br />$email_youremail</td>\n";
				echo "<td>$email_friendname<br />$email_friendemail</td>\n";
				echo "<td>$email_date</td>\n";
				echo "<td>$email_ip<br />$email_host</td>\n";
				echo "<td>$email_posttitle</td>\n";
				echo "<td>$email_status</td>\n";
				echo '</tr>';
				$i++;
			}
		} else {
			echo '<tr><td colspan="7" align="center"><strong>'.__('No E-Mail Logs Found', 'wp-email').'</strong></td></tr>';
		}
	?>
	</table>
		<!-- <Paging> -->
		<?php
			if($total_pages > 1) {
		?>
		<br />
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td align="left" width="50%">
					<?php
						if($email_page > 1 && ((($email_page*$email_log_perpage)-($email_log_perpage-1)) <= $total_email)) {
							echo '<strong>&laquo;</strong> <a href="'.$base_page.'&amp;emailpage='.($email_page-1).$email_sort_url.'" title="&laquo; '.__('Previous Page', 'wp-email').'">'.__('Previous Page', 'wp-email').'</a>';
						} else {
							echo '&nbsp;';
						}
					?>
				</td>
				<td align="right" width="50%">
					<?php
						if($email_page >= 1 && ((($email_page*$email_log_perpage)+1) <=  $total_email)) {
							echo '<a href="'.$base_page.'&amp;emailpage='.($email_page+1).$email_sort_url.'" title="'.__('Next Page', 'wp-email').' &raquo;">'.__('Next Page', 'wp-email').'</a> <strong>&raquo;</strong>';
						} else {
							echo '&nbsp;';
						}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<?php _e('Pages', 'wp-email'); ?> (<?php echo $total_pages; ?>):
					<?php
						if ($email_page >= 4) {
							echo '<strong><a href="'.$base_page.'&amp;emailpage=1'.$email_sort_url.'" title="'.__('Go to First Page', 'wp-email').'">&laquo; '.__('First', 'wp-email').'</a></strong> ... ';
						}
						if($email_page > 1) {
							echo ' <strong><a href="'.$base_page.'&amp;emailpage='.($email_page-1).$email_sort_url.'" title="&laquo; '.__('Go to Page', 'wp-email').' '.($email_page-1).'">&laquo;</a></strong> ';
						}
						for($i = $email_page - 2 ; $i  <= $email_page +2; $i++) {
							if ($i >= 1 && $i <= $total_pages) {
								if($i == $email_page) {
									echo "<strong>[$i]</strong> ";
								} else {
									echo '<a href="'.$base_page.'&amp;emailpage='.($i).$email_sort_url.'" title="'.__('Page', 'wp-email').' '.$i.'">'.$i.'</a> ';
								}
							}
						}
						if($email_page < $total_pages) {
							echo ' <strong><a href="'.$base_page.'&amp;emailpage='.($email_page+1).$email_sort_url.'" title="'.__('Go to Page', 'wp-email').' '.($email_page+1).' &raquo;">&raquo;</a></strong> ';
						}
						if (($email_page+2) < $total_pages) {
							echo ' ... <strong><a href="'.$base_page.'&amp;emailpage='.($total_pages).$email_sort_url.'" title="'.__('Go to Last Page', 'wp-email'), 'wp-email'.'">'.__('Last', 'wp-email').' &raquo;</a></strong>';
						}
					?>
				</td>
			</tr>
		</table>	
		<!-- </Paging> -->
		<?php
			}
		?>
	<br />
	<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get">
		<input type="hidden" name="page" value="<?php echo $base_name; ?>" />
		<?php _e('Sort Options:', 'wp-email'); ?>&nbsp;&nbsp;&nbsp;
		<select name="by" size="1">
			<option value="id"<?php if($email_sortby == 'email_id') { echo ' selected="selected"'; }?>><?php _e('ID', 'wp-email'); ?></option>
			<option value="fromname"<?php if($email_sortby == 'email_yourname') { echo ' selected="selected"'; }?>><?php _e('From Name', 'wp-email'); ?></option>
			<option value="fromemail"<?php if($email_sortby == 'email_youremail') { echo ' selected="selected"'; }?>><?php _e('From E-Mail', 'wp-email'); ?></option>
			<option value="toname"<?php if($email_sortby == 'email_friendname') { echo ' selected="selected"'; }?>><?php _e('To Name', 'wp-email'); ?></option>
			<option value="toemail"<?php if($email_sortby == 'email_friendemail') { echo ' selected="selected"'; }?>><?php _e('To E-Mail', 'wp-email'); ?></option>
			<option value="date"<?php if($email_sortby == 'email_timestamp') { echo ' selected="selected"'; }?>><?php _e('Date', 'wp-email'); ?></option>
			<option value="postid"<?php if($email_sortby == 'email_postid') { echo ' selected="selected"'; }?>><?php _e('Post ID', 'wp-email'); ?></option>
			<option value="posttitle"<?php if($email_sortby == 'email_posttitle') { echo ' selected="selected"'; }?>><?php _e('Post Title', 'wp-email'); ?></option>
			<option value="ip"<?php if($email_sortby == 'email_ip') { echo ' selected="selected"'; }?>><?php _e('IP', 'wp-email'); ?></option>
			<option value="host"<?php if($email_sortby == 'email_host') { echo ' selected="selected"'; }?>><?php _e('Host', 'wp-email'); ?></option>
			<option value="status"<?php if($email_sortby == 'email_status') { echo ' selected="selected"'; }?>><?php _e('Status', 'wp-email'); ?></option>	
		</select>
		&nbsp;&nbsp;&nbsp;
		<select name="order" size="1">
			<option value="asc"<?php if($email_sortorder == 'ASC') { echo ' selected="selected"'; }?>><?php _e('Ascending', 'wp-email'); ?></option>
			<option value="desc"<?php if($email_sortorder == 'DESC') { echo ' selected="selected"'; } ?>><?php _e('Descending', 'wp-email'); ?></option>
		</select>
		&nbsp;&nbsp;&nbsp;
		<select name="perpage" size="1">
		<?php
			for($i=10; $i <= 100; $i+=10) {
				if($email_log_perpage == $i) {
					echo "<option value=\"$i\" selected=\"selected\">".__('Per Page', 'wp-email').": $i</option>\n";
				} else {
					echo "<option value=\"$i\">".__('Per Page', 'wp-email').": $i</option>\n";
				}
			}
		?>
		</select>
		<input type="submit" value="<?php _e('Sort', 'wp-email'); ?>" class="button" />
	</form>
</div>

<!-- E-Mail Stats -->
<div class="wrap">
	<h2><?php _e('E-Mail Logs Stats', 'wp-email'); ?></h2>
	<table border="0" cellspacing="3" cellpadding="3">
	<tr>
		<th align="left"><?php _e('Total E-Mails:', 'wp-email'); ?></th>
		<td align="left"><?php echo number_format($total_email); ?></td>
	</tr>
	<tr>
		<th align="left"><?php _e('Total E-Mail Sent:', 'wp-email'); ?></th>
		<td align="left"><?php echo number_format($total_email_success); ?></td>
	</tr>
	<tr>
		<th align="left"><?php _e('Total E-Mail Failed:', 'wp-email'); ?></th>
		<td align="left"><?php echo number_format($total_email_failed); ?></td>
	</tr>
	</table>
</div>

<!-- Delete E-Mail Logs -->
<div class="wrap">
	<h2><?php _e('Delete E-Mail Logs', 'wp-email'); ?></h2>
	<div align="center">
		<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
			<strong><?php _e('Are You Sure You Want To Delete All E-Mail Logs?', 'wp-email'); ?></strong><br /><br />
			<input type="checkbox" name="delete_logs_yes" value="yes" />&nbsp;Yes<br /><br />
			<input type="submit" name="delete_logs" value="Delete" class="button" onclick="return confirm('<?php _e('You Are About To Delete All E-Mail Logs\nThis Action Is Not Reversible.\n\n Choose [Cancel] to stop, [OK] to delete.', 'wp-email'); ?>')" />
		</form>
	</div>
</div>