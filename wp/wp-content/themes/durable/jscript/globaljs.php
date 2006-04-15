<?
	require('../../../../wp-blog-header.php');
	header("content-type: text/javascript");
?>

// AJAX Functions

Ajax.Responders.register({
onCreate: function() {
 if($('busy') && Ajax.activeRequestCount>0)
 Effect.Appear('busy',{duration:0.5,queue:'end'});
},

onComplete: function() {
 if($('busy') && Ajax.activeRequestCount==0)
 Effect.Fade('busy',{duration:0.5,queue:'end'});
}
});

function liveSearch() {

	var url = '<? echo get_template_directory_uri(); ?>/livesearch.php';
	var pars = 's=' + $F('s');
	var target = 'searchResults';
	$(target).style.display = "none"; // incase there are old search results showing.
	var requestSuccess = function() { new Effect.BlindDown(target, {duration: 0.4}) }
	
	var myAjax = new Ajax.Updater(target, url, { 
										asynchronous:true,
										parameters: pars,
										method: 'get',
										onComplete: requestSuccess
									});
					
	var element = $(target);
	return false;
}

// Other less exciting functions.

function isOpera() {
	if(navigator.userAgent.indexOf("Opera")!=-1){
		return true;
	}
	return false;
}

function toggle(element) {
	var element = $(element);
	( element.style.display == "none" ) ? element.style.display = "" : element.style.display = "none";
}

function toggleMenu(element, menuList) {
	var element = $(element);
	var menuList = $(menuList).childNodes;
	var tabs = element.parentNode.childNodes;
	var listItem = $(element.id + "Link");


	for(i=0;i<tabs.length;i++) {
		if( tabs[i].nodeName == "DIV" && (tabs[i] != element) ) {
			//new Effect.Fade(tabs[i], {duration: 0.1}); // busts in IE
			tabs[i].style.display = "none";
		}
	}
	
	for(i=0;i<menuList.length;i++) {
		if( menuList[i].nodeName == "LI") {
			if(menuList[i].childNodes[0].nodeName == "A") {
				menuList[i].childNodes[0].className = "";
			}
		}
	}
	
	if(element.style.display == "none") {
		(isOpera())?element.style.display = "":new Effect.BlindDown(element, {duration: 0.4});
		listItem.childNodes[0].className = "menuSelected";
	}
	else {
		(isOpera())?element.style.display = "none":new Effect.BlindUp(element, {duration: 0.4});
		listItem.childNodes[0].className = "";
	}
}

function togglePanel(element) {
	element = $(element);
	( element.style.display == "none" ) ? new Effect.Appear(element, {duration: 0.4}) : new Effect.Fade(element, {duration: 0.4});	
}

function toggleOptions(element) {
	var element = $(element);
	var dlNodes = element.parentNode.childNodes;

	for(i=0;i<dlNodes.length;i++)
	{
		if( dlNodes[i].nodeName == "DD" && (dlNodes[i] != element) )
		{
			(isOpera())?dlNodes[i].style.display="none":new Effect.BlindUp(dlNodes[i], {duration: 0.3});
		}
	}
	
	if(isOpera()) {
		( element.style.display == "none" ) ? element.style.display = "" : element.style.display = "none";
	}else{
		( element.style.display == "none" ) ? new Effect.BlindDown(element, {duration: 0.3}) : new Effect.BlindUp(element, {duration: 0.3});
	}

}

function changeColor(title, cssclass, csselement, csscookie, cssform, thelink) {
	setItemTitle(title);
	pickcolor(cssclass, csselement, csscookie, cssform, thelink);
}

function setItemTitle(title) {
	$('itemTitle').innerHTML = title;
}

function clearColors(redirect) {
	deleteCookies();
	
	if(redirect) {
		location.href = "<? $_SERVER['HTTP_REFERER']; ?>";
	}
}



