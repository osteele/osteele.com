// $Id: openid.js,v 1.1.2.6 2008/01/30 20:43:20 walkah Exp $

$(document).ready(
  function() {
    if ($("#edit-openid-identifier").val()) {
      $("#edit-name-wrapper").hide();
      $("#edit-pass-wrapper").hide();
      $("#edit-openid-identifier-wrapper").show();
      $("a.openid-link").hide();
      $("a.user-link").show();
    }
    $("a.openid-link").click( function() {
      $("#edit-pass-wrapper").hide();
      $("#edit-name-wrapper").fadeOut('medium', function() {
          $("#edit-openid-identifier-wrapper").fadeIn('medium');
        });
      $("a.openid-link").hide();
      $("a.user-link").show();
      return false;
    });
    $("a.user-link").click( function() {
      $("#edit-openid-identifier-wrapper").hide();
      $("#edit-pass-wrapper").show();
      $("#edit-name-wrapper").show();
      $("a.user-link").hide();
      $("a.openid-link").show();
      return false;
    });
  });


