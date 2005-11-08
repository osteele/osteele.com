function commentAdded(request) {
  new Effect.BlindDown($('commentlist').lastChild);
  $('comment').value = '';
  $('comment').focus();
}

function failure(request) {
  $('errors').innerHTML = request.responseText;
  new Effect.Highlight('errors');
}

function loading() {
  $('submit').disabled = true;
  Element.show('comment_loading');
}

function complete(request) {
  Element.hide('comment_loading');
  Element.show('commentform');
  $('submit').disabled = false;  

  if (request.status == 200) {commentAdded()}
  else {failure(request)};
}
