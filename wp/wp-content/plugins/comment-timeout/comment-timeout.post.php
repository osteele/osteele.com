<?php
	global $post_ID;
	$this->get_settings();
	$setting = get_post_meta($post_ID, '_comment_timeout', true);
	$radio = array();
	$ctPostAge = $this->settings['PostAge'];
	$ctCommentAge = $this->settings['CommentAge'];
	if ($setting == 'ignore') {
		$radio['ignore'] = ' checked="checked"';
	}
	elseif (preg_match('|^(\d+)\,(\d+)$|', $setting, $match) > 0) {
		$radio['custom'] = ' checked="checked"';
		$ctPostAge = $match[1];
		$ctCommentAge = $match[2];
	}
	else {
		$radio['default'] = ' checked="checked"';
	}
?>
<fieldset id="comment-timeout-div" class="dbx-box">
	<h3 class="dbx-handle">Comment Timeout:</h3>
	<div class="dbx-content">
		<label class="selectit" for="ct_Default">
			<input id="ct_Default" type="radio" name="CommentTimeout" value="default" <?php echo @$radio['default']; ?> />
			Use default settings
		</label>
		<label class="selectit" for="ct_Ignore">
			<input id="ct_Ignore" type="radio" name="CommentTimeout" value="ignore" <?php echo @$radio['ignore']; ?> />
			Don't close comments
		</label>
		<label class="selectit" for="ct_Close">
			<input id="ct_Close" type="radio" name="CommentTimeout" value="custom" <?php echo @$radio['custom']; ?>  />
			Close comments:
		</label>
		<br />
		<label class="selectit" for="ctPostAge">
			<input id="ctPostAge" type="text" size="3" name="ctPostAge" value="<?php echo @$ctPostAge; ?>" />
			days after post or
		</label>
		<label class="selectit" for="ctCommentAge">
			<input id="ctCommentAge" type="text" size="3" name="ctCommentAge" value="<?php echo @$ctCommentAge; ?>" />
			days after last comment
		</label>
	</div>
</fieldset>
<script type="text/javascript">

	if (document.getElementById) {
		document.getElementById('ctPostAge').onchange =
		document.getElementById('ctCommentAge').onchange =
		function() {
			document.getElementById('ct_Close').checked = true;
		};	
	}

</script>
