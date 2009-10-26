/* JQuery subpixel font plugin.  A quick hack to render miha's font
 * at http://typophile.com/node/61920
 *
 * Copyright 2009 by Oliver Steele.  Available under the MIT License.
 *
 * Home: http://osteele.com/sources/javascript/jquery.subpixelate/
 */

/*
 * To do:
 * - document the options
 * - prevent word breaks
 * - italic, roman
 * - html entities
 * - missing character glyph
 * - document the table format
 */

(function() {
    $.fn.subpixelate = function(options) {
      options = $.extend(defaultOptions, options||{});
      return this.each(function() {
          var font = options.font || mihaFont,
          var imageSource = options.imageSource || font.imageSource;
          var debugOptions = options.debug ? {border:'1px solid red', padding:1} : {};
          var css = $.extend({color:'transparent', 'font-size':1, 'margin-right':1},
                             options.float ? {float:'left'} : {display:'inline-block'},
                             debugOptions, options.css||{});
          var $this = $(this), text = $this.text();
          $this.text(''); // since we're going to add characters
          $.each(text, function(_, c) {
              var bounds = font.getBounds(c);
              var background = 'url('+ imageSource +') '+
                -bounds.x +'px '+ -bounds.y +'px no-repeat';
              var e = $('<div/>').
                text(c).
                css({background:background, width:bounds.w, height:bounds.h}).
                css(css);
              if (c.match(/\s/)) e.html('&nbsp;'); // for copy/paste
              e.appendTo($this);
            });
          if (options.float)
            $('<div/>').css({clear:'left'}).appendTo($this);
        });
    };

    var defaultOptions = {
    float: true,
    };

    var mihaFont = {
    imageSource: 'http://typophile.com/files/sbpx2_5653.gif',
    getBounds: function(c) {
        var ix;
        var section =
        ' \n\t\f'.indexOf(c) >= 0 ? this.space
        : (ix = this.punct1.chars.indexOf(c)) >= 0 ? this.punct1
        : (ix = this.punct2.chars.indexOf(c)) >= 0 ? this.punct2
        : c >= 'a' ? this.lowercase
        : c >= 'A' ? this.uppercase
        : c >= '0' ? this.digits
        : this.space;
        var firstCharCode = 0;
        function code(n) { return ix >= 0 ? ix : n; }
        with (section) {
          var n = code(c.charCodeAt(0) - firstCharCode);
          return {
            x: x + w * n + (xds[c]||0)+1,
            y: y,
            w: w + (wds[c]||0)-2,
            h: h
          };
        }
      },

    space: {
      x: 21, y: 145, w: 3, h: 5, xds: {}, wds: {},
      code: function() { return 0; }
    },
    lowercase: {
      firstCharCode: 97,
      x: 21, y: 159, w: 5, h: 7,
      xds: {e:1,g:2,h:2,i:3,j:3,k:2,l:3,m:2,n:5,o:5,p:6,q:6,r:6,
            s:6,t:6,u:7,v:8,w:9,x:12,y:13,z:15},
      wds: {i:-2,j:-2,l:-2,m:2,r:-1,s:-1,w:2}
    },
    uppercase: {
      firstCharCode: 65,
      x: 21, y: 194, w: 6, h: 6,
      xds: {C:-1,D:-2,E:-2,F:-3,G:-4,H:-4,I:-4,J:-7,K:-9,L:-10,M:-11,N:-10,
            O:-10,P:-9,Q:-10,R:-9,S:-9,T:-9,U:-9,V:-9,W:-9,X:-19,Y:-19,Z:-19},
      wds: {B:-1,C:-1,E:-1,F:-1,I:-3,J:-2,K:-1,L:-1,M:1,P:-1,R:-1,S:-1,T:-1,Z:-1}
    },
    digits: {
      firstCharCode: 48,
      code: function(n) { return (n + 9) % 10 },
      x: 21, y: 175, w: 5, h: 6,
      xds: {5:1,6:1,7:1,8:1},
      wds: {4:1,5:0}
    },
    punct1: {
      chars: '.!: ,;   _-+=()| ][<>',
      x: 21, y: 207, w: 5, h: 8,
      xds: {'!':-1,'!':-1,':':-2,',':1,';':2,'_':3,'-':3,'+':2,'=':1,'(':1,
            '|':0,']':-1,'[':1,'<':5,'>':5},
      wds: {'.':-2,'!':-2,':':-2,',':-1,';':-1,'(':-1,')':-1,'|':-2,']':-1,'[':-1}
    },
    punct2: {
      chars: '#?&% /*${}"',
      x: 22, y: 229, w: 6, h: 8,
      xds: {'?':1,'%':2,'/':11,'*':12,'$':13,'{':12,'}':11,'"':11},
      wds: {'#':1,'?':-1,'&':1,'%':2,'/':1,'*':1,'$':-1,'{':-1,'}':-1}
    }
    };

 })(jQuery);
