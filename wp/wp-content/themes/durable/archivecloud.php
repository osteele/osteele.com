<?
require('../../../wp-blog-header.php');
get_header(); 
?>

	<div id="content" class="widecolumn">

		<h2>Blog Archives</h2>
		
		<h3>Archives by Month</h3>
		<div class="cats">
			<? monthly_cloud(); ?>
			
		<hr />
		</div>
		
		<br />
		
		<h3>Archives by Category</h3>
		<div class="cats">
			<? category_cloud(); ?>
			
		<hr />
		</div>

	<hr />
	</div>
	
<?
get_footer(); 
?>
