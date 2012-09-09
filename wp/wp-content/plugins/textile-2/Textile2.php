<?php
/*
Plugin Name: Textile 2 (Improved)
Version: 2.1
Plugin URI: http://idly.org/category/textile
Description: This is a wrapper for Jim Riggs' <a href="http://jimandlissa.com/project/textilephp">PHP implementation</a> of <a href="http://bradchoate.com/mt-plugins/textile">Brad Choate's Textile 2</a>.  It is feature compatible with the MovableType plugin. <strong>Does not play well with the Markdown, Textile, or Textile 2 plugins that ship with WordPress.</strong>  Packaged by <a href="http://idly.org/">Adam Gessaman</a>.
Author: Adam Gessaman
Author URI: http://idly.org/
*/

/* 
Textile Markup Wrapper for use with Wordpress
Copyright (C) 2003-2007 Adam Gessaman

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

require('class/Textile.php');

class Textile2_New {
   function Textile2_New () {
     add_action('admin_menu', array(&$this, 'textile_add_pages'));

     /** Let's add some filters! **/
     remove_filter('the_content', 'wpautop');
     remove_filter('the_excerpt', 'wpautop');
     remove_filter('comment_text', 'wpautop');
     
     remove_filter('the_content', 'wptexturize');
     remove_filter('the_excerpt', 'wptexturize');
     remove_filter('comment_text', 'wptexturize');
     
     add_filter('the_content', array(&$this, 'do_textile'), 6);
     add_filter('the_excerpt', array(&$this, 'do_textile'), 6);
     add_filter('comment_text', array(&$this, 'do_textile'), 6);
     
     add_filter('the_content_rss', array(&$this, 'do_textile'), 6);
     //add_filter('the_excerpt_rss', 'do_textile', 6);
     
   }
   
   function textile_add_pages() {
     add_options_page('Textile2 Configuration', 'Textile2', 2,  'textileoptions', array(&$this, 'textile_options_page'));
   }
      
   function textile_options_page() {
     if ('POST' == $_SERVER['REQUEST_METHOD']) {
       $this->save_settings();
     }
     $settings = $this->get_settings();
       ?>
       
       <div class=wrap>
	  <form method="post">
	  <h2>Textile2 Options</h2>
	  <fieldset class="options">
	  <legend>Textile Flavor</legend>
	  <ul style="list-style-type: none;"><li><label><input type="radio" name="Version" value="MTTextile" <?php echo ("MTTextile" === $this->settings['Version']) ? 'checked' : '';?>/> MTTextile - includes Brad Choates' extensions.</label></li>
		 <li><label><input type="radio" name="Version" value="Textile" <?php echo ("Textile" === $this->settings['Version']) ? 'checked' : '';?>/> - Textile for the Textile purist.</label></li></ul>
	       <legend>Text Filters</legend>
	       <ul style="list-style-type: none;">
		 <li><label><?php $this->render_checkbox('SmartyPants', 'SmartyPants'); ?> Apply SmartyPants (provides em and en dashes, and other typographic niceities)</label></li>	
		 <li><label><?php $this->render_checkbox('EducateQuotes', 'EducateQuotes'); ?> Apply Texturize (applies curly quotes)</label></li>	
	       </ul>
		 </fieldset>

	       <fieldset class="options">
	       <legend>Header Offset</legend>
		 <ul style="list-style-type: none;"><li><?php $this->render_header_selectbox('HeaderOffset', 'HeaderOffset'); ?></li></ul>
		 </fieldset>	

	       <fieldset class="options">
	       <legend>Parsing Options</legend>
	       <ul style="list-style-type: none;">
		 <li><label><?php $this->render_checkbox('ClearLines', 'ClearLines'); ?> Strip extra spaces from the end of each line.</label></li>	
		 <li><label><?php $this->render_checkbox('PreserveSpaces', 'PreserveSpaces'); ?> Change double-spaces to the HTML entity for an em-space (&amp;8195;).</label></li>	
		 </ul>
		 </fieldset>

	       <fieldset class="options">
	       <legend>Character Encoding</legend>
	       <ul style="list-style-type: none;">
		 <li><label>Input Character Encoding:  <?php $this->render_textbox('InputEncoding', 'InputEncoding'); ?></label></li>	
		 <li><label>Output Character Encoding:  <?php $this->render_textbox('Encoding', 'Encoding'); ?></label></li>
	       </ul>
		 </fieldset>

	       <div class="submit">
		  <input type="submit" name="info_update" value="Update options &raquo;" />
	       </div>
	    </form>
	  </div>

	 <?php
	 }

	 /* ====== get_settings ====== */

         function get_settings()
	 {
		 if (!isset($this->settings)) {
			 $defaultSettings = array(
				 'UniqueID' => md5(uniqid(rand(), true)),
				 'Version' => "MTTextile",
				 'HeaderOffset' => 0,
				 'EducateQuotes' => 1,
				 'Encoding' => 'utf-8',
				 'InputEncoding' => 'utf-8',
				 'TrimSpaces' => 0,
				 'SmartyPants' => 1,
				 'PreserveSpaces' => 0
			 );

			 $this->settings = get_option('textile2_new_settings');
			 if (FALSE === $this->settings) {
				 $this->settings =& $defaultSettings;
				 add_option('textile2_new_settings', $this->settings);
			 }
			 else if (!isset($this->settings['UniqueID'])) {
				 $this->settings = array_merge($defaultSettings, $this->settings);
				 update_option('textile2_new_settings', $this->settings);
			 }
			 else {
				 $this->settings = array_merge($defaultSettings, $this->settings);
			 }
		 }
		 return $this->settings;
	 }


	 /* ====== save_settings ====== */

	 function save_settings()
	 {
		 $this->settings = array();
		 $this->settings['Version'] = $_POST['Version'];
		 $this->settings['HeaderOffset'] = (isset($_POST['HeaderOffset']) ? (int)$_POST['HeaderOffset'] : 0);
		 $this->settings['EducateQuotes'] = (($_POST['EducateQuotes'] == 'on') ? 1 : 0);
		 $this->settings['Encoding'] = $_POST['Encoding'];
		 $this->settings['InputEncoding'] = $_POST['InputEncoding'];
		 $this->settings['TrimSpaces'] = (isset($_POST['TrimSpaces']) ? (int)$_POST['TrimSpaces'] : 0);
		 $this->settings['SmartyPants'] = isset($_POST['SmartyPants']);
		 $this->settings['PreserveSpaces'] = isset($_POST['PreserveSpaces']);
		 update_option('textile2_new_settings', $this->settings);
		 ?>
			 <div id='textile2-saved' class='updated fade-ffff00'><p><strong> <?php _e('Options saved.') ?></strong></p></div>
		 <?php

	 }


	 function render_checkbox($checkbox_name, $setting_name = "")
	 {
		 if ($setting_name == '') $setting_name = $checkbox_name;
		 echo '<input type="checkbox" ';
		 echo $this->settings[$setting_name] ? 'checked="checked"' : '';
		 echo " name=\"$checkbox_name\" />";
	 }

	 function render_textbox($textbox_name, $setting_name = "", $size=4)
	 {
		 if ($setting_name == '') $setting_name = $textbox_name;
		 echo '<input type="textbox" value="';
		 if ($this->settings[$setting_name] != '') {
			 echo $this->settings[$setting_name];
		 }
		 echo "\" name=\"$textbox_name\" size=\"$size\" />";
	 }

	 function render_header_selectbox($selectbox_name, $setting_name = "")
	 {
		 if ($setting_name == '')  $setting_name = $selectbox_name;
		 echo "<select name=\"$selectbox_name\">";
		 for ($i = 0; $i < 6; $i++) {
			 echo '  <option ';
			 echo ($i === $this->settings[$setting_name]) ? 'selected ' : '';
			 echo 'value="';
			 echo $i . "\">" . $i . " ";
			 echo '(.h1 = .h';
			 echo $i + 1;
			 echo ')';
			 echo '</option>';
		 }
		 echo '</select>';
	 }



	 /* Textile Filter */

	 function do_textile($text) {
	 $settings = $this->get_settings();

	 // setting $dont_make_text_pretty to 1 will ensure that neither wptexturize() or SmartyPants() functions are run on your text.
	 // There is no command line option for this... if wanted it bad enough to code dive for it, here it is. -ag
	   $dont_make_text_pretty = 0;

	 if ($this->settings['Version'] == 'Textile') {
		 $textile = new Textile;
	 } else {
		 $textile = new MTLikeTextile;
	 }

	   // head_offset sets the amount to increase header level (i.e. h1. becomes h3. if head_offset == 2)
	   $textile->options['head_offset'] = $this->settings['HeaderOffset'];
	   $textile->options['char_encoding'] = $this->settings['Encoding'];
	   $textile->options['input_encoding'] = $this->settings['InputEncoding'];

	   // do_quotes enables/disables 'smart quotes'
	   $textile->options['do_quotes'] = $this->settings['EducateQuotes'];

	   // trim_spaces, if set, will tell textile to clear all lines containing only spaces
	   $textile->options['trim_spaces'] = $this->settings['TrimSpaces'];

	   // enable/disable SmartyPants filtering
	   $textile->options['smarty_mode'] = $this->settings['SmartyPants'];

	   // if preserve_spaces is 1, whitespace will be encoded to html entities
	   $textile->options['preserve_spaces'] = $this->settings['PreserveSpaces'];

	   /** Don't edit below this line **/
			     if ($dont_make_text_pretty == 1) {
			       $textile->options['smarty_mode'] = 0;
			       return $textile->process($text);
			     } else if (!function_exists('SmartyPants') && $this->settings['EducateQuotes'] == 1) {
			       $text = $textile->process($text);
			       return wptexturize($text);
			     } else {
			       return $textile->process($text);   
			     }
   }
}

$myTextile2 = new Textile2_New();

?>
