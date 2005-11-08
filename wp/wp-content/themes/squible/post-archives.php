<?php
/*
	Template name: Post Archives
*/
?>
<?php get_header(); ?>
                        <div class="post"><!-- Post Div-->


                        <h2 class="storytitle" id="post-<?php the_ID(); ?>">
			Live Archives
                        </h2>

<p>This is the frontpage of the <?php bloginfo('name'); ?> archives. Through here, you will be able to move down into the archives by way of time or category. If you are looking for something specific, perhaps you should try the searching from the home page.</p><br />
			<?php af_ela_super_archive('num_entries=1&num_comments=1&number_text=<span>%</span>&comment_text=<span>%</span>&closed_comment_text=<span>-</span>&selected_text='.urlencode('')); ?>


			<div style='clear: both;'></div>
			<br /><br />

                        </div><!-- End of post div-->

<?php get_footer(); ?>
