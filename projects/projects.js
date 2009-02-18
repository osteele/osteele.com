function selectProjects(indices) {
  var ids = {};
  for (var i in indices) ids['project-' + indices[i]] = true;
  $('.project').each(function(i) {
      $(this)[ids[this.id] ? 'show' : 'hide'](300);
    });
  var message = '';
  if (arguments.length > 1) {
    message = indices.length == 0 ? 'No matches' : ''+indices.length+ ' match';
    if (indices.length > 1)  message += 'es';
    if (indices.length) message += ':';
  }
  var status = document.getElementById('nomatches');
  status.style.display = message == '' ? 'none' : '';
  status.innerHTML = message;
}
