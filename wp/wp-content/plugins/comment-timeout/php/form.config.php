<div class="wrap">

	<h2>Comment Timeout</h2>

	<form action="" method="POST" id="comment-timeout-conf">
		<?php if (function_exists('wp_nonce_field')) {
			wp_nonce_field('comment-timeout-update_settings');
		} ?>
		<input type="hidden" name="command" value="update_settings" />
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Comment closing:</th>
				<td>
					<input type="checkbox" name="Active" id="ctActive" value="true"
						<?php checked($this->core->wp_active, true); ?> />
					<label for="ctActive">Close comments on old posts</label>
				</td>
			</tr>
		</table>

		<div id="tbSettings">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="ctPostAge">Allow comments on posts less than:</label>
					</th>
					<td>
						<input id="ctPostAge" name="PostAge" size="6"
							value="<?php echo $this->core->wp_timeout; ?>" />
						days old
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="ctCommentAge">Also allow comments until:</label>
					</th>
					<td>
						<input id="ctCommentAge" name="CommentAge" size="6"
							value="<?php echo $this->settings['CommentAge']; ?>" />
						days after last approved comment
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ctCommentAgePopular">Or on popular posts until:</label>
					</th>
					<td>
						<input id="ctCommentAgePopular" name="CommentAgePopular" size="6"
							value="<?php echo $this->settings['CommentAgePopular']; ?>" />
						days after last approved comment
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ctPopularityThreshold">
							Where "popular" means at least:
						</label>
					</th>
					<td>
						<input id="ctPopularityThreshold" name="PopularityThreshold" size="6"
							value="<?php echo $this->settings['PopularityThreshold']; ?>" />
						approved comments
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">On older posts:</th>
					<td>
						<input type="radio" id="ctModeClose" name="Mode" value="close"
							<?php checked($this->settings['Mode'], 'close'); ?> />
						<label for="ctModeClose">Close comments</label>
						<br />
						<input type="radio" id="ctModeModerate" name="Mode" value="moderate"
							<?php checked($this->settings['Mode'], 'moderate'); ?> />
						<label for="ctModeModerate">Send to moderation queue</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Trackbacks and pingbacks:</th>
					<td>
						<input type="radio" id="ctDoPingsTogether" name="DoPings"
							value="together"
							<?php checked($this->settings['DoPings'], 'together'); ?> />
						<label for="ctDoPingsTogether">Treat as comments</label>
						<br />
						<input type="radio" id="ctDoPingsIndependent" name="DoPings"
							value="independent"
							<?php checked($this->settings['DoPings'], 'independent'); ?> />
						<label for="ctDoPingsIndependent">Handle independently</label>
						<br />
						<input type="radio" id="ctDoPingsIgnore" name="DoPings" value="ignore"
							<?php checked($this->settings['DoPings'], 'ignore'); ?> />
						<label for="ctDoPingsIgnore">Do not time out</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Post-specific settings:</th>
					<td>
						<input type="checkbox" name="DoPages" id="ctDoPages" value="true"
							<?php checked($this->settings['DoPages'], true); ?> />
						<label for="ctDoPages">
							Apply these rules to pages, images and file uploads
						</label>
						<br />
						<input type="checkbox" name="AllowOverride" id="ctAllowOverride"
							value="true"
							<?php checked($this->settings['AllowOverride'], true); ?> />
						<label for="ctAllowOverride">
							Allow individual posts to override these settings
						</label>
						<br />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Show when comments close:</th>
					<td>
						<select id="ctDisplayTimeout" name="DisplayTimeout">
							<option value="absolute"
								<?php selected($this->settings['DisplayTimeout'], 'absolute') ?>>
								as date ("on 24 March 2010")
							</option>
							<option value="relative"
								<?php selected($this->settings['DisplayTimeout'], 'relative') ?>>
								as time remaining ("in 3 days")
							</option>
							<option value="off"
								<?php selected($this->settings['DisplayTimeout'], 'off') ?>>
								do not display
							</option>
						</select>
					</td>
				</tr>
			</table>

			<h3>Global timeout options</h3>

			<table class="form-table">
				<tr valign="top">
					<th scope="row">Close all comments after:</th>
					<td>
						<input id="ctGlobalClose" name="GlobalClose" size="16"
							value="<?php
								if ($this->settings['GlobalClose'])
									echo date('d/m/Y H:i', $this->settings['GlobalClose']);
							?>" />
							DD/MM/YYYY hh:mm
							(leave blank if not required; 12:00 assumed if time not specified)
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Re-open comments after:</th>
					<td>
						<input id="ctGlobalReopen" name="GlobalReopen" size="16"
							value="<?php
								if ($this->settings['GlobalReopen'])
								echo date('d/m/Y H:i', $this->settings['GlobalReopen']);
							?>" />
					</td>
				</tr>
			</table>

		</div>

		<p class="submit">
			<input type="submit" name="Submit" value="Update Options &raquo;" />
		</p>
	</form>

	<form method="POST" action="" id="comment-timeout-reset">
		<?php if (function_exists('wp_nonce_field')) {
			wp_nonce_field('comment-timeout-reset');
		} ?>
		<input type="hidden" name="command" value="reset" />
		<h3>Reset per-post settings</h3>
		<p>
			This will reset all your per-post comment settings to the defaults for new
			posts.
		</p>

		<p>
			<input type="checkbox" name="Active" id="rpDoPages" value="true" />
			<label for="rpDoPages">
				Reset settings for pages, images and files as well as posts.
			</label>
		</p>

		<p class="submit">
			<input type="submit"
				value="Reset per-post settings &raquo;"
				onclick="return confirm_timeout_reset()" />
		</p>
	</form>

	<p style="text-align:center">
		Comment Timeout version <?php echo COMMENT_TIMEOUT_VERSION; ?> -
		Copyright 2007-2011 <a href="http://jamesmckay.net/">James McKay</a>
		-
		<a href="http://bitbucket.org/jammycakes/comment-timeout/">Help and FAQ</a>
	</p>
</div>