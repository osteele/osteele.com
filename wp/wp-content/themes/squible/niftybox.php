        <div id="nifty">
        <div style="padding-left: 20px; padding-right: 20px;">
        <div class="tooltitle">Tags</div>
         <p style="margin-top: -2px; margin-bottom: 4px;" class="post-footer"><em>
		<?php
		if (function_exists('UTW_ShowRelatedTagsForCurrentPost')) {
			UTW_ShowTagsForCurrentPost("commalist",'',3);
        	} else {
			show_tags(3); 
		} 
		?>
        </em></p>
        <div class="tooltitle">Conversation</div>
        <p style="margin-top: -2px; margin-bottom: 4px;" class="post-footer"><em>
        <a href="http://www.technorati.com/search/<?php the_permalink() ?>">Technorati Cosmos</a>
        </em></p>
        <div class="tooltitle">Full Post</div>
        <p style="margin-top: -2px; margin-bottom: 4px;" class="post-footer"><em>
        <a href="<?php the_permalink() ?>" rel="bookmark">Read this post</a>
        </em></p>
        <div class="tooltitle">Comments</div>
        <p style="margin-top: -2px; margin-bottom: 4px;" class="post-footer"><em>
	<a href="<?php the_permalink() ?>#comments" rel="bookmark">view comments</a><br />
        <a href="<?php the_permalink() ?>#postcomment" rel="bookmark">Post a comment</a>
        <?php edit_post_link(__('<br />Edit This Post')); ?>
        </em></p>
        </div>
        </div>

