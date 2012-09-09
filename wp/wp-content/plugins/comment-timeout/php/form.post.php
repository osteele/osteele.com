<?php
	global $post_ID;
	$setting = get_post_meta($post_ID, '_comment_timeout', true);
	$radio = array();
	$ctPostAge = $this->core->wp_timeout;
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
	<p>
		<label class="selectit" for="ct_Default">
			<input id="ct_Default" type="radio" name="CommentTimeout" value="default"
				<?php echo @$radio['default']; ?> />
			Use default settings
		</label>
		<br />
		<label class="selectit" for="ct_Ignore">
			<input id="ct_Ignore" type="radio" name="CommentTimeout" value="ignore"
				<?php echo @$radio['ignore']; ?> />
			Don't close comments
		</label>
		<br />
		<label class="selectit" for="ct_Close">
			<input id="ct_Close" type="radio" name="CommentTimeout" value="custom"
				<?php echo @$radio['custom']; ?>  />
			Close comments:
		</label>
		<br />
		&nbsp; &nbsp;
		<label class="selectit" for="ctPostAge">
			<input id="ctPostAge" type="text" size="3" name="ctPostAge"
				value="<?php echo @$ctPostAge; ?>" />
			days after post or
		</label>
		<br />
		&nbsp; &nbsp;
		<label class="selectit" for="ctCommentAge">
			<input id="ctCommentAge" type="text" size="3" name="ctCommentAge"
				value="<?php echo @$ctCommentAge; ?>" />
			days after last comment
		</label>
	</p>
<script type="text/javascript">

	if (document.getElementById) {
		document.getElementById('ctPostAge').onchange =
		document.getElementById('ctCommentAge').onchange =
		function() {
			document.getElementById('ct_Close').checked = true;
		};
	}

</script>
