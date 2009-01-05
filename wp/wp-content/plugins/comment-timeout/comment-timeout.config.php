<div class="wrap">

	<h2>Closing comments</h2>

	<form action="" method="POST" id="comment-timeout-conf">
		
		<fieldset class="options">
			<legend>
				Comment timeout policy:
			</legend>
			
			<table class="optiontable"> 
				<tr valign="top"> 
					<th scope="row">
						<label for="ctPostAge">Allow comments on posts less than:</label>
					</th> 
					<td>
						<input id="ctPostAge" name="PostAge" size="6" value="<?php echo $this->settings['PostAge']; ?>" />
						days old
					</td> 
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="ctCommentAge">Also allow comments until:</label>
					</th>
					<td>
						<input id="ctCommentAge" name="CommentAge" size="6" value="<?php echo $this->settings['CommentAge']; ?>" />
						days after last approved comment
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ctCommentAgePopular">Or on popular posts until:</label>
					</th>
					<td>
						<input id="ctCommentAgePopular" name="CommentAgePopular" size="6" value="<?php echo $this->settings['CommentAgePopular']; ?>" />
						days after last approved comment
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ctPopularityThreshold">Where "popular" means at least:</label>
					</th>
					<td>
						<input id="ctPopularityThreshold" name="PopularityThreshold" size="6" value="<?php echo $this->settings['PopularityThreshold']; ?>" />
						approved comments
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">On older posts:</th>
					<td>
						<input type="radio" id="ctModeClose" name="Mode" value="close" <?php checked($this->settings['Mode'], 'close'); ?> />
						<label for="ctModeClose">Close comments</label>
						<br />
						<input type="radio" id="ctModeModerate" name="Mode" value="moderate" <?php checked($this->settings['Mode'], 'moderate'); ?> />
						<label for="ctModeModerate">Send to moderation queue</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Trackbacks and pingbacks:</th>
					<td>
						<input type="radio" id="ctDoPingsTogether" name="DoPings" value="together" <?php checked($this->settings['DoPings'], 'together'); ?> />
						<label for="ctDoPingsTogether">Treat as comments</label>
						<br />
						<input type="radio" id="ctDoPingsIndependent" name="DoPings" value="independent" <?php checked($this->settings['DoPings'], 'independent'); ?> />
						<label for="ctDoPingsIndependent">Handle independently</label>
						<br />
						<input type="radio" id="ctDoPingsIgnore" name="DoPings" value="ignore" <?php checked($this->settings['DoPings'], 'ignore'); ?> />
						<label for="ctDoPingsIgnore">Do not time out</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Post-specific settings:</th>
					<td>
						<input type="checkbox" name="DoPages" id="ctDoPages" value="true" <?php checked($this->settings['DoPages'], 'true'); ?> />
						<label for="ctDoPages">Apply these rules to pages, images and file uploads</label>						
						<br />
						<input type="checkbox" name="AllowOverride" id="ctAllowOverride" value="true" <?php checked($this->settings['AllowOverride'], 'true'); ?> />
						<label for="ctAllowOverride">Allow individual posts to override these settings</label>
						<br />
					</td>
				</tr>
			</table>
		</fieldset>

		<p class="submit">
			<input type="submit" name="Submit" value="Update Options &raquo;" />
		</p>

		<p style="text-align:center">
			Comment Timeout version <?php echo COMMENT_TIMEOUT_VERSION; ?> - 
			Copyright 2007 <a href="http://www.jamesmckay.net/">James McKay</a>
			-
			<a href="http://www.jamesmckay.net/code/comment-timeout/2.0/">Help and FAQ</a>
		</p>
	</form>
</div>