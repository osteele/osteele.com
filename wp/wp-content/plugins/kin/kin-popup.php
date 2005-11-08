<?php
/*
*******************************************************************************
		Khanh's Instant Notepad (KIN) - A handy notepad when you need it!
						Copyright (c) 2005 John Ha
*******************************************************************************
This work is licensed under the Creative Commons Attribution License. To view a
copy of this license, visit http://creativecommons.org/licenses/by-nc-nd/2.5/
*******************************************************************************
*/

class kin_popup {
var $kin_desc = array (
	'Title'		=> 'string',
	'Author'	=> 'string',
	'Created'	=> 'string',
	'Updated'	=> 'string',
	'Note'		=> 'string',
);
var $kin_options;
var $kin_keys;
var $kin_tot_notes;
var $kin_num_opts;
var $kin_tot_opts;
var $kin_msg = '';
var $kin_days = 365;

function kin_popup() {
	$this -> kin_options = get_option('kin_options');
	$this -> kin_keys = array_keys($this -> kin_desc);
	empty($this -> kin_options) ? $this -> kin_tot_notes = 0 : $this -> kin_tot_notes = count($this -> kin_options);
	$this -> kin_num_opts = count($this -> kin_keys);
	$this -> kin_tot_opts = $this -> kin_tot_notes * $this -> kin_num_opts;
	$this -> kin_process();
	$this -> kin_notes();
}


///////////////////////////////////////////////////////////////////////////
///////////////////////// Process Data ////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
function kin_process() {
if(isset($_POST['update'])) {
	$c = ($_POST['note'] - 1) * $this -> kin_num_opts + 1;
	for ($i = $c; $i < $c + $this -> kin_num_opts; $i++) {
		if ($this -> kin_keys[$i - $c] == 'Updated') {
			$this -> kin_options[$_POST['note'] - 1][$this -> kin_keys[$i - $c]] = date("D m-d-y @ G:i");
		} else {
			$this -> kin_options[$_POST['note'] - 1][$this -> kin_keys[$i - $c]] = stripslashes($_POST['kin_' . $i]);
		}
	}
	// highlight_string(print_r($this -> kin_options[$_POST['note'] - 1],true),false);
	update_option('kin_options', $this -> kin_options);
	$this -> kin_msg = '<p><strong>[Note #' . $_POST['note'] . ']</strong> updated.</p>';
} elseif (isset($_POST['update-all'])) {
	$c = 1;
	for ($i = 0; $i < $this -> kin_tot_notes; $i++) {
		for ($j = 0; $j < $this -> kin_num_opts; $j++) {
			if ($_POST['kin_' . $c] == 'true') {
				$this -> kin_options[$i][$this -> kin_keys[$j]] = true;
			} elseif ($_POST['kin_' . $c] == 'false') {
				$this -> kin_options[$i][$this -> kin_keys[$j]] = false;
			} else {
				$this -> kin_options[$i][$this -> kin_keys[$j]] = stripslashes($_POST['kin_' . $c]);
			}
			$c++;
		}
	}
	update_option('kin_options', $this -> kin_options);
	$this -> kin_msg = '<p><strong>All</strong> notes have been updated.</p>';
} elseif (isset($_POST['save'])) {
	for ($i = 0; $i < $this -> kin_num_opts; $i++) {
		if ($this -> kin_keys[$i] == 'Created' && !$_POST['kin_' . ($this -> kin_tot_opts + $i + 1)]) {
			$this -> kin_options[$this -> kin_tot_notes][$this -> kin_keys[$i]] = date("D m-d-y @ G:i");
		} else {
			$this -> kin_options[$this -> kin_tot_notes][$this -> kin_keys[$i]] = stripslashes($_POST['kin_' . ($this -> kin_tot_opts + $i + 1)]);
		}
	}
	update_option('kin_options', $this -> kin_options);
	$this -> kin_msg = '<p><strong>[Note #' . ($this -> kin_tot_notes + 1) . ']</strong> added.</p>';
	$this -> kin_tot_notes++;
	$this -> kin_tot_opts = $this -> kin_tot_notes * $this -> kin_num_opts;
} elseif (isset($_POST['delete'])) {
	if ($_POST['note'] - 1 != $this -> kin_tot_notes) {
		for ($i = $_POST['note'] - 1; $i < $this -> kin_tot_notes; $i++) {
			$this -> kin_options[$i] = $this -> kin_options[$i + 1];
		}
	}
	unset($this -> kin_options[$this -> kin_tot_notes - 1]);
	update_option('kin_options', $this -> kin_options);
	$this -> kin_msg = '<p><strong>[Note #' . $_POST['note'] . ']</strong> deleted.</p>';
	$this -> kin_tot_notes--;
	$this -> kin_tot_opts = $this -> kin_tot_notes * $this -> kin_num_opts;
} elseif (isset($_POST['purge-all'])) {
	for ($i = 0; $i < $this -> kin_tot_notes; $i++) {
		unset($this -> kin_options[$i]);
	}
	update_option('kin_options', $this -> kin_options);
	$this -> kin_msg = '<p><strong>All</strong> notes have been purged.</p>';
	$this -> kin_tot_notes = 0;
	$this -> kin_tot_opts = 0;
}
empty($this -> kin_options) ?
$this -> kin_msg .='<p>You currently have no notes.</p>' :
$this -> kin_msg ? '' :	$this -> kin_msg = '<p>No Messages.</p>';
}


///////////////////////////////////////////////////////////////////////////
//////////////////////////////// Notepad //////////////////////////////////
///////////////////////////////////////////////////////////////////////////
function kin_notes() {
global $is_IE;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Khanh's Instant Notepad</title>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>/wp-admin/wp-admin.css" />
<?php
ob_start();
require_once('kin.css');
$buffer = '<style type="text/css">' . $this -> kin_comp_css(ob_get_contents()) . '</style>';
ob_clean();

global $is_IE;
require_once('kin-js.php');
$buffer .= '<script type="text/javascript">/*<![CDATA[*/' . $this -> kin_comp_js(ob_get_contents()) . "//]]></script>\n";
ob_end_clean();
echo $buffer;
?>
<link rel="stylesheet" type="text/css" href="dom-tooltips.css" />
<script type="text/javascript" src="dom-tooltips.js"></script>
</head>
<body>


<ul id="kin-nav">
<li class="kin_nav_button"><a href="#" title="Toggle Font Size" onclick="return kin_toggle_font()">A</a></li>
<li class="kin_nav_button"><a href="#kin_msg_panel" title="Toggle Messages" onclick="return kin_toggle_msg()">&Xi;</a></li>
<li class="kin_nav_button"><a href="#kin_note_<?= $this -> kin_tot_notes ? $this -> kin_tot_notes : '1' ?>" title="Last" onclick="return kin_go_to_last()">&raquo;</a></li>
<li class="kin_nav_button"><a href="#" title="Next" onclick="return kin_go_to_next()">&rsaquo;</a></li>
<li class="kin_nav_button"><a href="#" title="Previous" onclick="return kin_go_to_prev()">&lsaquo;</a></li>
<li class="kin_nav_button"><a href="#kin_note_1" title="First" onclick="return kin_go_to_first()">&laquo;</a></li>
<?php
for ($j = 1; $j <= $this -> kin_tot_notes; $j++) {
?>
<li><a id="nav_tab_<?= $j ?>" href="#kin_note_<?= $j ?>" title="<?= $this -> kin_options[$j - 1]['Title'] ? $this -> kin_options[$j - 1]['Title'] : 'No Title' ?>" onclick="return kin_go_to(<?= $j ?>)"><?= $j ?></a></li>
<?php
}
// highlight_string(print_r($this -> kin_options,true),false);
?>
<li><a id="nav_tab_<?= $this -> kin_tot_notes + 1 ?>" href="#kin_note_<?= $this -> kin_tot_notes + 1 ?>" title="Add [Note #<?= $this -> kin_tot_notes + 1 ?>]" onclick="return kin_go_to(<?= $this -> kin_tot_notes + 1 ?>)">+</a></li>
<li><a id="nav_tab_<?= $this -> kin_tot_notes + 2 ?>" href="#kin_note_<?= $this -> kin_tot_notes + 2 ?>" title="Global" onclick="return kin_go_to(<?= $this -> kin_tot_notes + 2 ?>)">&Theta;</a></li>
<li class="info" id="kin-show-closed" href="#" title="Closed Panels"></li>
<li class="info" id="kin-show-info" href="#"></li>
</ul>

<div id="kin_msg_panel" class="wrap">
<h2>
<a href="#" class="close" title="Close Messages Panel" onclick="return kin_toggle_msg()">&times;</a>
<a href="#" id="kin_info_1_" class="rollup" title="Rollup Messages Panel" onclick="return kin_rollup_info(1)">&ndash;</a>
Messages
</h2>
<table id="kin_info_1" cellspacing="2" cellpadding="5" class="editform">
<tr><td>
<span id="kin_msg_text"><p>Loading...</p></span>
</td></tr>
</table>
</div>

<?php
$c = 0;
for ($note = 1; $note <= $this -> kin_tot_notes + 1; $note++) {
?>
<div id="kin_note_<?= $note ?>" class="wrap kin-hide">
<form name="kin_form_<?= $note ?>" id="kin_form_<?= $note ?>" method="post">
<table cellspacing="2" cellpadding="5" class="editform">


<tr valign="top">
<td width="50%">
<?php $c++; ?>
<label for="kin_<?= $c ?>"><p><strong>Title</strong><br />
<input class="kin-field" name="kin_<?= $c ?>" type="text" id="kin_<?= $c ?>" value="<?= wp_specialchars($this -> kin_options[$note - 1]['Title']) ?>" /></p>
</label>
</td>

<td width="50%">
<?php $c++; ?>
<label for="kin_<?= $c ?>"><p><strong>Author</strong><br />
<?php
	global $user_level, $users, $wpdb, $user_ID;
	if ($user_level > 7 && $users = $wpdb->get_results("SELECT ID, user_login, user_firstname, user_lastname FROM $wpdb->users WHERE user_level <= $user_level AND user_level > 0") ) : ?>
		<select class="kin-field" name="kin_<?= $c ?>" id="kin_<?= $c ?>">
		<?php
		$i = 0;
		foreach ($users as $o) :
			if ($this -> kin_options[$note - 1]['Author'] == $o->ID) $selected = 'selected="selected"';
			else $selected = '';
			echo "<option value='$o->ID' $selected>$o->user_login ($o->user_firstname $o->user_lastname)</option>";
			$i++;
		endforeach;
		?>
		</select>
<?php endif; ?>
</label>
</td>
</tr>

<tr valign="top">
<td width="50%">
<?php $c++; ?>
<label for="kin_<?= $c ?>"><p><strong>Created</strong><br />
<input class="kin-field" name="kin_<?= $c ?>" type="text" id="kin_<?= $c ?>" readonly value="<?= wp_specialchars($this -> kin_options[$note - 1]['Created']) ?>" /></p>
</label>
</td>

<td width="50%">
<?php $c++; ?>
<label for="kin_<?= $c ?>"><p><strong>Updated</strong><br />
<input class="kin-field" name="kin_<?= $c ?>" type="text" id="kin_<?= $c ?>" readonly value="<?= wp_specialchars($this -> kin_options[$note - 1]['Updated']) ?>" /></p>
</label>
</td>
</tr>

<tr valign="top">
<td colspan="3">
<?php $c++; ?>
<label for="kin_<?= $c ?>"><p><strong>Note</strong><br />
<textarea rows="10" name="kin_<?= $c ?>" id="kin_<?= $c ?>"><?= wp_specialchars($this -> kin_options[$note - 1]['Note']); ?></textarea></p>
</label>
</td>
</tr>


<tr valign="top">
<td colspan="3">
<p class="submit">
<input type="button" name="clear" id="kin_clear_<?= $note ?>" value="<?php _e('Clear') ?> &raquo;" onclick="document.getElementById('kin_<?= $note * $this -> kin_num_opts ?>').value = ''" title="Clear Note" />
<input type="button" name="reset_" value="<?php _e('Reset') ?> &raquo;" onclick="this.form.reset()" title="Reset form" />
<?php if ($note - 1 == $this -> kin_tot_notes) { ?>
<input type="submit" name="save" value="<?php _e('Save') ?> &raquo;" onclick="return kin_save(<?= $note ?>);" title="Save note" />
<?php } else { ?>
<input type="submit" name="update" id="kin_update_<?= $note ?>" value="<?php _e('Update') ?> &raquo;" onclick="return confirm('You are about to update [Note  #<?= $note ?>].\n  \'OK\' to update, \'Cancel\' to stop.');" title="Update Note" />
<input type="submit" name="delete" id="kin_delete_<?= $note ?>" value="<?php _e('Delete') ?> &raquo;" onclick="return kin_delete(<?= $note ?>);" title="Delete from database" />
<input type="hidden" name="note" value="<?= $note ?>" />
<?php
}
?>
</p>
</td>
</tr>
</table>
</form></div>
<?php } ?>


<div id="kin_note_<?= $note ?>" class="wrap kin-hide">
<h2>
Global
</h2>
<form name="kin_global" id="kin_form_<?= $note ?>" method="post">
<table cellspacing="2" cellpadding="5" class="editform">
<tr valign="top">
<td>
<p><strong>These options operate on all notes simultaneously, so use with caution.</strong></p>
<p class="submit">
<input type="submit" name="purge-all" value="<?php _e('Purge') ?> &raquo;" onclick="return kin_purge_all();" title="Purge kin database of all data" />
<input type="submit" name="update-all" value="<?php _e('Update') ?> &raquo;" onclick="return kin_update_all();" title="Global update to database" />
</p>
</td></tr>
</table>
</form></div>



</body>
</html>


<?php
}


function kin_comp_css($buffer) {
	$buffer = preg_replace('/\/\*(?!-)[\x00-\xff]*?\*\//', '', $buffer);
	$buffer = preg_replace('/\s*(^|[{},:;\(\)\-]|$)\s*/', '$1', $buffer);
	return $buffer;
}

function kin_comp_js($buffer) {
	$buffer = preg_replace('/\/\*[^@].*?[^@]\*\//s', '', $buffer);
	$buffer = preg_replace('/(^|[^:])(\/\/).*/', '', $buffer);

	preg_match_all("/(.*?)(([\/'\"]).*?\\3)(?!s )/s", $buffer, $matches, PREG_SET_ORDER);
	$re = '/\s*(^|[\\\[\]\?\(\)\!\+\-\*\/<>{}\|%&,:;=~]|$)\s*/';
	$tmpstr = '';
	foreach ($matches as $val) {
		$val1 = preg_replace($re, '$1', $val[1]);
		if (strpos('(=:/', substr($val1, -1, 1)) === false && $val[2]{0} == '/') {
			$val2 = preg_replace($re, '$1', $val[2]);
		} else $val2 = $val[2];
		$tmpstr .= $val1 . $val2;
	}

	$endstr = preg_replace('/^.+([\/\'"])(.+$)/s', '$2', $buffer);
	$endstr = preg_replace($re, '$1', $endstr);

	return $tmpstr . $endstr;
}


}

chdir('../../../wp-admin');
require_once('admin.php');

$kin_popup = new kin_popup();
?>