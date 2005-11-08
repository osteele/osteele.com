<?php
	/*
	Welcome, and thanks for using the squible wordpress theme. This is the
	configuration file for the theme. You can control options like whether
	or not to use the builtin plugins I've included with the theme, your
	about text and other various options.
	*/
	$squible_version="Alpha 2.2";

	// This is the text for the about section on the home page,
	// edit to your liking.
	$aboutme="My name is Theron Parlin and I&#8217;m a web developer from Windsor, Connecticut. I&#8217;m also a new media strategist; a state certified firefighter; a part-time student working on degrees in journalism and political science; blah blah blah, you can change this text by editing <b>squible_options.php</b>.<br />";

	// This determines whether or not you would like to use the plugins 
	// that are included with the squible theme. Use 1 for yes and 0
	// for no. I would recommend using this unless you know for sure
	// that you have all the supported plugins already installed.
	$builtin_plugins=1;

	// This controls the amount of characters that gets displayed on
	// the top post on the home page.
	$limitchars=450;

	// This is your flickr user id, if you don't know what your flickr
	// user id is, you can find it by going here:
	// http://eightface.com/code/idgettr/
	$flickr_userid="87971625@N00";
	// This is the URL for your pictures on flickr
	$flickr_url="http://www.flickr.com/photos/photanical/";
	// The number of flickr thumbs to show;
	$numpics=4;

	// When users add a new tag to one of your posts, you will be emailed.
	// $tagemail is the email address where you would like those emails to
	// go. 
	$tagemail = "you@youremail.com";

	// This is the category used for asides.
	$asides_cat=29;
	// Number of asides to show
	$asidesnum=5;

	// The number of recently commented posts to show
	$show_recent_comments=10;

	// Popular tags options
	$minfont = 7;
        $maxfont = 14;
        $fontunit = "pt";
        $category_ids_to_exclude = "";
	$numberoftags=50;

	//Show author 1 for yes, 0 for no
	$show_author=0;

	//Use ajax comments !!!!!NOT COMPATIBLE WITH SPAM KARMA!!!!!
	$use_ajax=1;
?>
