var postType = '';
var disableReturn = false;

jQuery(document).ready(function() {
  // determine what type this is from url
  postType = window.location.href.indexOf('edit.php') != -1 ? 'post' : 'page';
  
  // add events to links and make rows double-clickable
  var links = jQuery('.edit-link');
  for(var i = 0; i < links.length; i++) {
    var id = getRowId(links[i]);
    jQuery(links[i]).click(function() { return editRow(this); });
    var row = jQuery('#'+postType+'-'+id);
    row.dblclick(function() { toggleRow(this); });
    row.attr('title', 'Double-click to edit');
  }
});

function toggleRow(el) {
  jQuery('#'+postType+'-'+getRowId(el)).hasClass('manageable') ? revertRow(el) : editRow(el);
}

function editRow(el) {
  var id = getRowId(el);
  showLoading(id);

  // revert all edit rows back to normal
  var open = jQuery('.manageable');
  for(var i = 0; i < open.length; i++) {
    revertRow(open[i]);
  }
  
  // get edit row
  jQuery.post(
    mgblUrl+'manageable.php',
    { action: 'edit', type: postType, id: id }, 
    function(html) { 
      updateRow(id, 'edit', html);
      
      // enable autocomplete for tags
      if(postType == 'post') {
        var tags = jQuery('#tags-'+id);
        tags.autocomplete('admin-ajax.php?action=ajax-tag-search', {
          width: 200,
          multiple: true,
          matchContains: true,
          onShow: function() { disableReturn = true; },
          onHide: function() { setTimeout(function() { disableReturn = false; }, 100); }
        });
      }
    }
  );
    
  return false;
}

function updateRow(id, action, html) {
  var row = jQuery('#'+postType+'-'+id);
  if(action == 'edit') {
    row.addClass('manageable');
    row.html(html);
    row.attr('title', 'Double-click to cancel');
    row.keypress(function(event) { 
      if(event.which == 13 && !disableReturn) {
        saveRow(this);
        return false;
      }
    });
    jQuery('#cancel-'+id).click(function() { return revertRow(this); });
    jQuery('#save-'+id).click(function() { return saveRow(this); });
  } else {
    row.removeClass('manageable');
    row.html(html);
    row.animate( { backgroundColor: '#FFFBCC' }, 200).animate( { backgroundColor: row.css('background-color') }, 500);
    row.attr('title', 'Double-click to edit');
    jQuery('#edit-'+id).click(function() { 
      return editRow(this); 
    });
  }
}

function saveRow(el) {
  var id = getRowId(el);
  showLoading(id);
  
  var params = {
    action:         'save',
    post_type:      postType,
    post_ID:        id,
    post_title:     jQuery('#title-'+id).val(),
    post_name:      jQuery('#slug-'+id).val(),
    post_author:    jQuery('#author-'+id).val(),
    post_status:    jQuery('#status-' + id).val(),
    comment_status: jQuery('#comment-' + id + ':checked').val(),
    ping_status:    jQuery('#ping-' + id + ':checked').val()
  };

  // if date selector is present, add to params
  if(jQuery('#aa')) {
    jQuery.extend(params, {
      edit_date:   'true',
      aa:          jQuery('#aa').val(),
      mm:          jQuery('#mm').val(),
      jj:          jQuery('#jj').val(),
      hh:          jQuery('#hh').val(),
      mn:          jQuery('#mn').val(),
      ss:          jQuery('#ss').val()
    });
  }

  if(postType == 'post') {
    // figure out what categories are selected
    var cats = jQuery('#categories-'+id+' input');
    var selCats = '';
    for(var i = 0; i < cats.length; i++) {
      if(cats[i].checked) selCats += cats[i].value + ',';
    }
    selCats = selCats.substring(0, selCats.length - 1);
    
    jQuery.extend(params, {
      tags_input:  jQuery('#tags-'+id).val(),
      categories:  selCats      
    });
  }
  if(postType == 'page') {
    jQuery.extend(params, {
      post_parent:   jQuery('#parent-'+id).val(),
      menu_order:    jQuery('#order-'+id).val(),
      post_password: jQuery('#password-'+id).val(),
      page_template: jQuery('#template-'+id).val(),
      page_private:  jQuery('#private-'+id+':checked').val()
    });    
  }

  // make ajax request
  jQuery.post(
    mgblUrl+'manageable.php', params,
    function(html) { 
      updateRow(id, 'get', html); 
      var row = jQuery('#'.postType+'-'+id);
      row.fadeOut('fast');
    }
  );  
  return false;
}

function revertRow(el) {
  var id = getRowId(el);
  showLoading(id);

  jQuery.post(mgblUrl+'manageable.php', 
    { action: 'get', type: postType, id: id },
    function(html) { 
      updateRow(id, 'get', html); 
    }
  ); 
  return false;
}

function getRowId(obj) {
  var parts = obj.id.split('-');
  return parts[parts.length - 1];
}

function showLoading(id) {
  jQuery('#loading-'+id).fadeIn('fast');
}