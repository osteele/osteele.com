<?php

class jmct_Admin
{
	private $core;
	private $settings;

	public function __construct($core)
	{
		$this->core = $core;
	}

	public function init()
	{
		$this->settings =& $this->core->get_settings();
		$page = add_submenu_page('options-general.php',
			__('Comment Timeout'), __('Comment Timeout'),
			'manage_options', 'comment-timeout',
			array(&$this, 'config_page'));
		if ($this->settings['AllowOverride']) {
			if (function_exists('add_meta_box')) {
				add_meta_box('comment-timeout', __('Comment Timeout'),
					array(&$this, 'post_custombox'), 'post', 'normal');
				add_meta_box('comment-timeout', __('Comment Timeout'),
					array(&$this, 'post_custombox'), 'page', 'normal');
			}
			else {
				add_action('dbx_post_sidebar', array(&$this, 'post_sidebar'));
				add_action('dbx_page_sidebar', array(&$this, 'post_sidebar'));
			}
			add_action('save_post', array(&$this, 'save_post'));
		}
		add_action('admin_print_styles-' . $page, array(&$this, 'admin_print_styles'));
	}

	/* ====== admin_print_styles ====== */
	
	/**
	 * Enqueues the scripts that we'll be using.
	 */

	function admin_print_styles() {
		wp_enqueue_script('comment-timeout-admin');
	}

	/* ====== config_page ====== */

	/**
	 * Loads in and renders the configuration page in the dashboard, executing any commands that
	 * have been posted back.
	 * @remarks
	 *  Commands are implemented as methods of this class. They must have a "@command" doc
	 *  comment to verify that they are indeed commands.
	 */

	public function config_page()
	{
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$cmd = $_POST['command'];
			if (method_exists(&$this, $cmd)) {
				check_admin_referer('comment-timeout-' . $cmd);
				$method = new ReflectionMethod('jmct_Admin', $cmd);
				$comment = $method->getDocComment();
				if (preg_match('/^\\s*\\*\\s*@command\\b/im', $comment)) {
					$method->invoke(&$this);
				}
			}
		}
		require_once(dirname(__FILE__) . '/form.config.php');
	}

	/* ====== update_settings command ====== */

	/**
	 * @command
	 */

	public function update_settings()
	{
		$this->settings = $this->core->save_settings_from_postback();
		echo '<div id="comment-locking-saved" class="updated fade-ffff00"">';
		echo '<p><strong>';
		_e('Options saved.');
		echo '</strong></p></div>';
	}


	/* ====== reset command ====== */

	/**
	 * @command
	 */

	public function reset()
	{
		global $wpdb;

		$reset_all = (bool)$_POST['rpDoPages'];
		$sql1 = "delete from $wpdb->postmeta pm where pm.meta_key='_comment_timeout'";
		$sql2 = $wpdb->prepare("update $wpdb->posts set comment_status=%s, ping_status=%s",
			get_option('default_comment_status'),
			get_option('default_ping_status')
		);

		if (!$reset_all) {
			$sql1 .= " and pm.post_id in " .
				"(select id from $wpdb->posts where post_type = 'post')";
			$sql2 .= " and post_type = 'post'";
		}

		$wpdb->query($sql1);
		$wpdb->query($sql2);

		echo '<div id="comment-locking-saved" class="updated fade-ffff00"">';
		echo '<p><strong>';
		_e('Comment timeout settings on all posts have been reset to their original state.');
		echo '</strong></p></div>';
	}
	/* ====== save_post ====== */

	/**
	 * Called when a post or page is saved. Updates CT's per-post settings
	 * from the bit in the sidebar.
	 */

	public function save_post($postID)
	{
		switch(@$_POST['CommentTimeout']) {
			case 'ignore':
				$setting = 'ignore';
				break;
			case 'custom':
				$setting = (int)$_POST['ctPostAge'] . ',' . (int)$_POST['ctCommentAge'];
				break;
			case 'default':
			default:
				$setting = false;
				break;
		}

		if ($setting !== false) {
			if (!update_post_meta($postID, '_comment_timeout', $setting)) {
				add_post_meta($postID, '_comment_timeout', $setting);
			}
		}
		else {
			delete_post_meta($postID, '_comment_timeout');
		}
	}


	/* ====== post_custombox ====== */

	/**
	 * Adds an entry to the "Edit Post" screen to allow us to set simple comment
	 * settings on a post-by-post basis.
	 * For WordPress versions 2.5 or later.
	 */

	public function post_custombox()
	{
		$label_class = '';
		require_once(dirname(__FILE__) . '/form.post.php');
	}



	/* ====== post_sidebar ====== */

	/**
	 * Adds an entry to the post's sidebar to allow us to set simple comment
	 * settings on a post-by-post basis.
	 * For WordPress versions < 2.5
	 */

	public function post_sidebar()
	{
		$label_class = 'selectit';
		echo <<< SIDEBAR1
<fieldset id="comment-timeout-div" class="dbx-box">
	<h3 class="dbx-handle">Comment Timeout:</h3>
	<div class="dbx-content">
SIDEBAR1;
		post_custombox();
		echo <<< SIDEBAR2
	</div>
</fieldset>
SIDEBAR2;
	}
}