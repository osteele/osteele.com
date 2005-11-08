<?php
/*
	Template name: Gallery
*/
?>
<?php get_header(); ?>

<div id="toppanel">
	<div id="posts">
		<div class="post">
                        <h2 class="storytitle">Gallery</h2>
			<?php flickr_show_photos($_GET['album'], $_GET['photo'], $_GET['page']); ?>
                </div>
	<br /><br />
	</div>

</div>

<?php get_footer(); ?>
