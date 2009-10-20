(function() {
	
window.WPhone =
{
	findParent: function(node, localName)
	{
		while (node && (node.nodeType != 1 || node.localName.toLowerCase() != localName))
			node = node.parentNode;
		return node;
	},
	
	toggleElement: function(elID)
	{
		var container = document.getElementById(elID);
		if (container) {
			var classes = container.getAttribute('class');
			if (classes.match(/accessible/))
				classes = classes.replace(/accessible/,'');
			else
				classes += ' accessible ';
			container.setAttribute('class', classes);
		}
	},
	
	toggleCheckbox: function(myDiv)
	{
		if (myDiv) {
			var myID = myDiv.getAttribute('id');
			var myState = myDiv.getAttribute('toggled');
			if (myID) {
				var myCheckbox = document.getElementById(myID.replace('-toggle', ''));
				if (myCheckbox) {
					if (myCheckbox.getAttribute('type') == 'checkbox') {
						if (myState == 'true')
							myCheckbox.checked = true;
						else
							myCheckbox.checked = false;
					}
				}
			}
		}
	},
	
	togglePlugin: function(myUrl)
	{
		window.location.href = myUrl;
	},
	
	/**
	 * this function exists as a stop gap solution for the fact that
	 * Safari, unlike other browsers, does not seem to be passing the name
	 * parameter of button elements via the POST method. To be further
	 * investigated, but also reported by other developers on other
	 * projects.
	 */
	submitForm: function(button)
	{
		if (button) {
			if (button.name.match(/-unused/))
				button.name = button.name.replace(/-unused/, '');
			var myForm = this.findParent(button, 'form');
			var newField = document.createElement('input');
			newField.type  = 'hidden';
			newField.name  = button.name;
			newField.value = button.innerHTML;
			myForm.appendChild(newField);
			button.name = button.name + '-unused';
			return true;
		}
		else {
			return false;
		}
	}
}

addEventListener("load", function() {
	setTimeout(scrollTo, 100, 0, 1);
}, false);

})();
