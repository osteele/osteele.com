<?php
/*
Author: Outbrain
Author URI: http://www.outbrain.com
Description: Administrative options for Outbrain plugin.
*/
$location = '?page=outbrain/ob_options.php'; // Form Action URI
$maxPages = 5;
/*
option: outbrain_pages_list
pages list
0: is_home (home page)
1: is_single (single post)
2: is_page (page)
3: is_archive (some archive. Category, Author, Date based and Tag pages are all types of Archives)
*/
$PIpath 	= outbrain_get_plugin_place();
$PIurlPath 	= outbrain_get_plugin_admin_path();
if (isset($_POST['claim'])){
	$key	=	isset($_POST['key'])? $_POST['key']:'';
	if ($key != ''){
		update_option("outbrain_claim_key",$key);
	}
	die; // end of file
} else if (isset($_POST['saveClaimStatus'])){
	update_option("outbrain_claim_status_num",$_POST['status']);
	update_option("outbrain_claim_status_string",$_POST['statusString']);
	die; // end of file
} else if (isset($_POST['ob_export'] ) && ($_POST['ob_export']== "true") ){
} else if (isset($_POST['outbrain_send'])){
	// form sent
	$value = (isset($_POST['lang_path'])? $_POST['lang_path'] : (isset($_POST['your_translation_path'])? $_POST['your_translation_path'] : ''));
	if ($value != ''){
		update_option("outbrain_lang",$value);
	}
	
	$recommendations_value = 	(isset($_POST['outbrain_rater_show_recommendations']) && $_POST['outbrain_rater_show_recommendations'] == true);
	update_option("outbrain_rater_show_recommendations",$recommendations_value);
	
	$self_recommendations_value = 	(isset($_POST['outbrain_rater_self_recommendations']) && $_POST['outbrain_rater_self_recommendations'] == true);
	update_option("outbrain_rater_self_recommendations",$self_recommendations_value);
	
	$selected_pages = (isset($_POST['select_pages']))? $_POST['select_pages']: array();	
	update_option("outbrain_pages_list",$selected_pages);

	$selected_pages_recs = (isset($_POST['select_pages_recs']))? $_POST['select_pages_recs']: array();	
	update_option("outbrain_pages_recs",$selected_pages_recs);

	?>
	<div id="message" class="updated fade">
		<p>
			<strong><?php _e('Options saved.'); ?></strong>
		</p>
	</div>
<?php
} else {
	$selected_pages 		= (isset($_POST['select_pages']))? $_POST['select_pages']: get_option("outbrain_pages_list");
	$selected_pages_recs 	= (isset($_POST['select_pages_recs']))? $_POST['select_pages_recs']: get_option("outbrain_pages_recs");
}
?>

<div class="wrap" style="text-align:left;direction:ltr;">
	
	<table border="0" style="width:100%;">
		<tr>
			<td width="1%" nowrap="nowrap"><h2><?php _e('Outbrain options', 'outbrain') ?></h2></td>
			<td align="right"><a href="http://getsatisfaction.com/outbrain" target="_blank" style="font-size:13px;">Outbrain Support</a></td>
			<td style="width:20px">&nbsp;</td>
		</tr>
	</table>
	
	<form method="post" id="outbrain_form" name="outbrain_form" action="<?php echo $location; ?>" onsubmit="return outbrain_options_submit(document.outbrain_form.claim_code.value);">
	<input type="hidden" name="ob_export" 		id="export" 		value="false">
	<input type="hidden" name="obVersion" 		id="obVersion" 		value="<?php echo outbrain_getVersion(); ?>">
	<input type="hidden" name="obCurrentKey" 	id="obCurrentKey" 	value="<?php outbrain_returnClaimCode() ?>">
	
		<?php
		if (function_exists('wp_nonce_field')){
			wp_nonce_field('update-options');
		}
		
		//get the path to plug ins 
		$pathOfAdmin = outbrain_get_plugin_admin_path();
		?>
		<input type="hidden" name="outbrain_send" value="send" />
		<ul style="position: relative;">
			<div id="block_claim" class="option_board_right" style="display:none;">
				<a href="javascript:void(0)" onClick="toggleStateValidate(this)" class="blockTitle">Verify Blog ownership to Outbrain</a>
				<div id="block_claim_inner" class="block_inner" style="display:none;">
					<div>
						Outbrain key is used to verify your blog ownership.<br/>
						It will allow you to receive interesting statistics on your blog ratings and customize additional features. <br />
						<a href="http://www.outbrain.com/ln/AddBlogPage" target="_blank">For further information.</a><a href="#" onClick="javascript:failedMsg()" style="color:#ffffff">.</a>
					</div>
					<div id="outbrain_key_insertion">
						Outbrain Key
						<input type="text" size="35" name="claim_code" value="" onkeyup="claimChanged(document.outbrain_form.claim_code.value);" />
						<button type="button" id="claim_button" class="key_button_active" name="claim_code_send" onclick="return claimClicked('<?php echo $location; ?>',document.outbrain_form.claim_code.value);">Send</button>
						<span id="claimLoadingImage">&nbsp;</span>
					</div>
					<div id="after_claiming">
					</div>
				</div>
			</div>
			
			<div  id="block_language" class="option_board_right" style="display:none;">
				<a href="javascript:void(0)" onClick="toggleStateValidate(this)" class="blockTitle">Language file </a>
				<div id="block_language_inner" style="display:none" class="block_inner">

					Select a language:
					<span style="margin-left:10px;">&nbsp;</span>
					<select name="lang_path" id="langs_list" onchange="outbrain_changeLang(language_list[this.selectedIndex])" onkeyup="outbrain_changeLang(language_list[this.selectedIndex])">
						<?php //JS print here the options ?>
					</select>
					<span style="margin-left:40px;">&nbsp;</span>
					<div id='translator_div'></div>
					<div style="clear:both;">
						<a href='http://www.outbrain.com/addtranslation'>Can't find your language here?</a>
					</div>
				</div>
			</div>
		

			<div id="block_settings" class="option_board_right" style="display:none">
				<a href="javascript:void(0)" onClick="toggleStateValidate(this)" class="blockTitle"> Settings</a>
				<div id="block_settings_inner" style="display:none" class="block_inner"> 
					
					<div id="block_pages" class="option_board_down" style="">
						<a href="javascript:void(0)" onClick="toggleStateValidate(this)" class="blockTitle"> Pages</a>					
						<div id="block_pages_inner" style="" class="block_inner"> 
							<?php
								$select_page_texts = array('Home page','Single post','Page','Archive (category page, author page, date page and also tag page in WP 2.3+)','Attachment');
								//$select_page_recs =  array('Home page','Single post','Page','Archive (category page, author page, date page and also tag page in WP 2.3+)','Attachment');
								for ($i=0;$i<$maxPages;$i++){
									$checked = '';
									$checked_recs = '';
									if (in_array($i,$selected_pages)){
										$checked = " checked='checked' ";
									}
									if (in_array($i,$selected_pages_recs)){
										$checked_recs = " checked='checked' ";
									}
								?>
									<div class="block_inner"><label><input type="checkbox" name="select_pages[]" <?php echo $checked; ?> value="<?php echo $i; ?>"> <?php echo $select_page_texts[$i]; ?> </label></div>
									<?php if ($itemRecommendationsPerPage){?>
									<div class="block_inner" style="margin-left:40px;margin-bottom:10px"><label><input type="checkbox" name="select_pages_recs[]" <?php echo $checked_recs; ?> value="<?php echo $i; ?>"> Show recommendations </label></div>
									<?php } ?>
								<?php
								}
							?>
						</div>
					</div>
					
					
					<?php 
					if ($itemSelfRecommendations){
					$checked	=	(get_option('outbrain_rater_self_recommendations')	==	true)? 'checked="checked"':''; ?>
					<div id="block_recommendation" class="option_board_down" style="">
						<a href="javascript:void(0)" onClick="toggleStateValidate(this)" class="blockTitle"> Recommendations</a>
							<div id="block_recommendation_inner" style="" class="block_inner"> 
							<label>
								<input type="checkbox" name="outbrain_rater_self_recommendations" <?php echo $checked; ?> /> Only recommend my blog posts
							</label>
						</div>	
					</div>
					<?php }?>
				</div>
			</div>
				
			<div id="block_additonal_setting" class="option_board_down" style="display:none">
				<a href="javascript:void(0)" onClick="toggleStateValidate(this)" class="blockTitle">Additional Features</a>

				<div id="block_additonal_setting_inner" style="display:block" class="block_inner">
					<div id="block_additonal_instruction" style="display:none" >
					Blog ownership verification is required to enable additional customization features.
					</div>
					<ul>
						<div id="block_custom_settings" class="additional_settings" style="display:none;">
							<a href='http://www.outbrain.com/ln/BlogSettings?key=<?php outbrain_returnEncodeClaimCode()  ?>'> Configure outbrain settings </a>
						</div>
						<div id="block_MP" class="additional_settings" style="">
							<?php
								$mostPopularBlockContent	=	'';
								if (function_exists('register_sidebar_widget')){ //	only for installations with widgets support
									$mostPopularBlockContent	=	'<a href="widgets.php"> Add Most Popular widget</a>&nbsp;';
								} else {
									$mostPopularBlockContent	=	'<a href="http://getsatisfaction.com/outbrain/topics/install_most_popular_widget_with_no_wordpress_widgets_support" target="_blank">Your blog does not support widgets, See our tech support forum for installation instructions of the Most Popular widget.</a>';
								}
								echo $mostPopularBlockContent;
							?>
						</div>
						<?php if ($itemExport){ ?>
							<div id="block_export" class="additional_settings" style="">
								<a href="<?php echo $pathOfAdmin ?>ob_export.php" >Export Rates from WP PostRatings </a>
							<div style="font-size:0.9em;">	This action might take a while...<br>
									Please send the result file to <a href="mailto:support@outbrain.com">Outbrain Support</a> and we will import your blog rates shortly..
							</div>
						</div>
						<?php } ?>
					</ul>
				</div>
			</div>	
			
			<div id="block_logger" class="option_board_down" style="display:none">
				<a href="javascript:void(0)" onClick="toggleStateValidate(this)" class="blockTitle">Log </a>

				<div id="block_logger_inner" style="display:block" class="block_inner">
					<div id="block_logger_display" style="display:block" >
						Please reffer to <a href="mailto:support@outbrain.com">Outbrain Support</a> and attach the logger contant.<br/> We will assist you shortly....<br/>   
					</div>
					<p></p>
					<div id="block_logger_textArea_display" style="display:block" >
						<textarea rows="4" id="outbrainLogger" readonly="readonly" style="width:700px;"></textarea>
					</div>
				</div>
			</div>
			
			
			
			<!--
			<div id="getWidget" style="text-align:center;width:500px;margin:auto;border:1px solid red;padding:10px;display:none" >
				<?php
					$mostPopularBlockContent	=	'';
					if (function_exists('register_sidebar_widget')){ //	only for installations with widgets support
						$mostPopularBlockContent	=	'<a href="widgets.php">get outbrain Most Popular widget - click here and add the widget</a>';
					} else {
						$mostPopularBlockContent	=	'<a href="http://getsatisfaction.com/outbrain/topics/install_most_popular_widget_with_no_wordpress_widgets_support" target="_blank">Your blog does not support widgets, See our tech support forum for installation instructions of the Most Popular widget.</a>';
					}
					echo $mostPopularBlockContent;
				?>
			</div>
			-->
		</ul>
		<div id="block_loader" style="text-align:center;width:500px;margin:auto;padding:10px;display:block" class="">
			<img src="<?php echo $pathOfAdmin?>/spinner.gif"></img>
			<b>Loading...</b> 
		</div>
	
		<p id="block_submit" class="submit options" style="text-align:left;display:none">
			<input type="submit" name="Submit" value="<?php _e('Update Options »') ?>"/>
		</p>
		

	</form>
</div>
<script language="javascript">
	var pathOfPlug  ='<?php echo $pathOfAdmin ?>';
//check if claim already
	
	var key = "<?php outbrain_returnClaimCode()  ?>";
		
	if (key.length > 0){
		outbrain_isUserClaim(key);
	}else {
		outbrain_noClaimMode();//no key - show other options 
	}
</script>