(function($) {
  var saved = $.ajax;
  $.ajax = function(options) {
    var auth = options.authentication;
    if (auth) {
      var auth = 'Basic ' + encode64(auth.username + ':' + auth.password);
      // TODO copy options
      options.beforeSend = function(req) { req.setRequestHeader('Authorization', auth) };
    }
    return saved.call($, options);
  }

  var letters = "abcdefghijklmnopqrstuvwxyz";
  var base64Chars = (letters.toUpperCase() + letters + "0123456789+/").match(/./g);

  function encode64(input){
    var result = '';
    var col = 0;
    var i = 0;
    function next() {
      return i < input.length && input.charCodeAt(i++) & 0xff;
    }
    while (true) {
      var b0 = next();
      if (b0 == false) break;
      result += base64Chars[b0 >> 2];
      var bits = b0 << 4;
      var b1 = next();
      bits |= b1 >> 4;
      result += base64Chars[bits & 0x3F];
      if (b1 == false) {result += '=='; break;}
      bits = b1 << 2;
      var b2 = next();
      bits |= b2 >> 6;
      result += base64Chars[bits & 0x3F];
      if (b2 == false) {result += '='; break;}
      result += base64Chars[b2 & 0x3F];
      if ((col += 4) >= 76){
        result += '\n';
        col = 0;
      }
    }
    return result;
  }
 })(jQuery);
