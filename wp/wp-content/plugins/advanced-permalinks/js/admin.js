function edit_link (start)
{
  $('#item_' + start).load (wp_apl_base, { cmd: 'edit', id: start});
  return false;
}

function delete_link (start)
{
  if (confirm (wp_apl_delete))
  {
    $.post (wp_apl_base, { cmd: 'delete', id: start}, function(data) { if (data == 'OK') $('#item_' + start).fadeOut ()});
  }
  return false;
}

function save_link (form)
{
  $('#links').load (wp_apl_base, $(form).formToArray ());
  return false;
}

function cancel_link (start)
{
  $('#item_' + start).load (wp_apl_base, { cmd: 'cancel', id: start});
  return false;
}

function delete_migration (pos)
{
  if (confirm (wp_apl_delete))
  {
    $.post (wp_apl_base, { cmd: 'delete_migration', id: pos}, function(data) { if (data == 'OK') $('#item_' + pos).fadeOut ()});
  }
  return false;
}

function edit_migration (pos)
{
  $('#item_' + pos).load (wp_apl_base, { cmd: 'edit_migration', id: pos});
  return false;
}
