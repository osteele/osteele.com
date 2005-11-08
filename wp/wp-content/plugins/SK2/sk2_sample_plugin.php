<?php
// Sample filter

$this->register_plugin("blah_2");
$this->register_plugin("blah_1", 3); // second argument is priority

class blah_1 extends sk2_plugin
{
	var $name = "Blah 1.0";
	var $author = "dr Dave's evil twin";
	var $author_email = "test@unknowngenius.com";
	var $description = "This is a sample plugin.";
	var $filter = true; // note: you can define a plugin with both a filter and a treatment functions
	
	function filter_this(&$cmt_object)
	{
		// have our way with the comment...
		// EXAMPLE:
/*		$this->log_msg("yay! we are filtering comment ID: " . $cmt_object->ID . " with plugin: " . $this->name) ;
		if ($cmt_object->is_comment())
			echo "This is a comment";
		elseif ($cmt_object->is_pingback())
			echo "This is a pingback";
		elseif ($cmt_object->is_trackback())
			echo "This is a trackback";
			
	//	echo "<pre>";
//		print_r($cmt_object); // contains all the stuff we might want to use
	//	echo "</pre>";
		
		//END EXAMPLE
		
		$this->raise_karma($cmt_object, -25, "Because we can"); // raise the comment
	*/
	}
}


class blah_2 extends sk2_plugin
{
	var $name = "Blah 2.0";
	var $author = "dr Dave's evil twin";
	var $author_email = "test@unknowngenius.com";
	var $description = "This is a sample plugin.";
	var $author_url = "http://unknowngenius.com";
	var $settings_format = array ("option_1" => array("type" => "check", "value"=>true, "caption" => "This is option number 1."),
												"option_2_menu" => array("type" => "menu", "caption" => "select a value:", "options" => array("bla_1" => "item 1", "bla_2" => "item 2", "bla_3" => "item 3"), "value" => "bla_2"),
												"option_3_text" => array("type" => "text", "value" => "foobar", "caption" => "Enter some text"));

	var $filter = true; // note: you can define a plugin with both a filter and a treatment functions
	
	function filter_this(&$cmt_object)
	{
		// have our way with the comment...
		// EXAMPLE:
/*		$this->log_msg("yay! we are filtering comment ID: " . $cmt_object->ID . " with plugin: " . $this->name) ;
		if ($cmt_object->is_comment())
			echo "This is a comment";
		if ($cmt_object->is_pingback())
			echo "This is a pingback";
		elseif ($cmt_object->is_trackback())
			echo "This is a trackback";
			
		echo "<pre>";
		print_r($cmt_object); // contains all the stuff we might want to use
		echo "</pre>";
		
		//END EXAMPLE
		
		$this->hit_karma($cmt_object, -25); // spank the comment
	*/
	}
}



?>