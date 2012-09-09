<?php

class jmct_CommentProcessor
{
	private $core;
	private $comment;
	private $settings;


	public function __construct($core, $comment)
	{
		$this->core = $core;
		$this->comment = $comment;
		$this->settings = $core->get_settings();
	}


	public function process_comment()
	{
		$post = get_post($this->comment['comment_post_ID']);
		$post = $this->core->process_posts($post);

		$now = time();
		$isPing = ($this->comment['comment_type'] == 'trackback'
			|| $this->comment['comment_type'] == 'pingback');
		$isClosed = $isPing 
			? ($post->ping_status == 'closed')
			: ($post->comment_status == 'closed');
		if ($isPing) {
			$timedOut = isset($post->cutoff_pings) && ($now > $post->cutoff_pings);
		}
		else {
			$timedOut = isset($post->cutoff_comments) && ($now > $post->cutoff_comments);
		}

		switch ($this->settings['Mode']) {
			case 'moderate':
				if ($timedOut) {
					// This filter needs to run before the one inserted by Akismet
					add_filter('pre_comment_approved', create_function('$a', 'return 0;'), 0);
				}
				break;
			case 'close':
			default:
				if ($isClosed || $timedOut) {
					do_action('comment_closed', $this->comment->comment_post_ID);
					wp_die('Sorry, comments are closed for this item.');
				}
				break;
		}
		return $this->comment;
	}
}