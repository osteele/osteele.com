<?php
/*
Plugin Name: mimeTeX
Plugin URI: http://sixthform.info/steve/wordpress/index.php
Description: Add mimeTeX support
Version: 1.0
Author: Steve Mayer
Author URI: http://www.mayer.dial.pipex.com/tex.htm
*/

function addlatex($latextext) {
	include_once('/home/path_to/wordpress/latexrender/mimetex.php');
	return mimetex($latextext);
}

// And now for the filters

add_filter('the_title','addlatex');
add_filter('the_content', 'addlatex');
add_filter('the_excerpt', 'addlatex');

/*
Add a TeX button (this won't be visible if a visiual rich editor is being used)
Adapted from:
Plugin Name: Edit Button Framework
Plugin URI: http://www.asymptomatic.net/wp-hacks
Description: A Plugin template for adding new buttons to the post editor.
Version: 1.0
Author: Owen Winkler
Author URI: http://www.asymptomatic.net
*/

add_filter('admin_footer', 'stevem_function_name');

function stevem_function_name()
{
	if(strpos($_SERVER['REQUEST_URI'], 'post.php'))
	{
?>
<script language="JavaScript" type="text/javascript"><!--
var toolbar = document.getElementById("ed_toolbar");
<?php
	edit_insert_button("tex", "tex_button_handler", "Add TeX tag");
?>

function tex_button_handler() {
	var j=edButtons.length - 1;
	for (i = 0; i < edButtons.length; i++) {
		if (edButtons[i].id == 'ed_tex') {
			j=i;
		}
	}
	edInsertTag(edCanvas, j);
}
//--></script>

<?php
	}
}

if(!function_exists('edit_insert_button'))
{
	//edit_insert_button: Inserts a button into the editor
	function edit_insert_button($caption, $js_onclick, $title = '')
	{
		?>
		if(toolbar)
		{
			edButtons[edButtons.length] =
			new edButton('ed_tex'
			,'tex'
			,'[tex]'
			,'[/tex]'
			,'x'
			);

			var theButton = document.createElement('input');
			theButton.type = 'button';
			theButton.value = '<?php echo $caption; ?>';
			theButton.onclick = <?php echo $js_onclick; ?>;
			theButton.className = 'ed_button';
			theButton.title = "<?php echo $title; ?>";
			theButton.id = "<?php echo "ed_{$caption}"; ?>";
			theButton.accessKey='x';
			toolbar.appendChild(theButton);
		}
	<?php

	}
}

?>