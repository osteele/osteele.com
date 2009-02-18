/**
 * This page uses Shadowbox, Copyright © 2007-2008 Michael
 * J. I. Jackson and licensed under the Creative Commons
 * Attribution-Noncommercial-Share Alike license.
 */

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

var shadowBase = '/javascripts/shadowbox-2.0';
Shadowbox.loadSkin('classic', shadowBase + '/skin');
//Shadowbox.loadLanguage('en', shadowBase + '/lang');
//Shadowbox.loadPlayer(['flv', 'html', 'iframe', 'img', 'qt', 'swf', 'wmp'],
//                   shadowBase + '/player');

$(function() { Shadowbox.init(); });
