/* Copyright 2009 by Oliver Steele.  Available under the MIT License. */

$(function() {
  // abbreviate sections that contain <!-- more -->
  $('.shorten').contractMores();

  // add titles
  $('a:not([title])').setTitlesFromMap(kLinkTitleMap);
  $('img:not([title])').setImageTitles();

  // image fade
  $('.candids img').
    mouseover(function() { $(this).stop().animate({opacity: 1}) }).
    mouseout(function() { $(this).stop().animate({opacity: .75}) });

  // link mouseover (disabled)
  0 && $('a:not(.no-link-icon)').live('mouseover', function() {
    $(this).stop(true).css({backgroundColor:'yellow'});
  }).live('mouseout', function() {
    $(this).stop(true).animate({backgroundColor:'transparent'},'slow');
  });

  // easter egg
  $('h1 img').mouseover(function(done) {
    var $this = $(this);
    var $egg = $('h1 iframe');
    var small = $this.bounds();
    small.width = small.height = Math.max(small.width, small.height);
    var large = {left:small.width+$('body').width()-400,
    top:20,
    width:300,height:300};
    var cycle = $('h1 .caption').crossfader();
    $('h1 img').attr('title', '');
    if ($egg.filter(':visible').length) {
      // do hide
      $('.hide-egg').removeClass('visible');
      large = $egg.bounds();
      $('.candids').show('slow');
      cycle.stop();
      $egg.css({position:'absolute', right:'inherit'})
        .css(large).animate(small, function(){
          $egg.hide();
          done();
        });
    } else {
      // do show
      //$('.hide-egg').addClass('visible');
      $egg.show().css(small).animate(large, function() {
  $egg.css({position:'fixed', left:'inherit', right:50});
        cycle.start();
        done();
      });
      $('.candids').hide('slow');
    }
  }.nullifyWhileExecutingK());
});


/*
 * Plugins
 */
(function($) { $.extend($.fn, {
  // -> {left:, top:, width:, right:}
  bounds: function() {
    if (!this[0]) return null;
    return $.extend(this.offset(), {width:this.width(), height:this.height()});
  },
  // replace elements that contain <!-- more --> with a link to
  // disclose the additional text.  The short and long content are
  // swapped, instead of just using CSS; this potentially breaks
  // dynamic use of the elements, but it allows the 'more' break to
  // occur in non-structural positions
  contractMores: function() {
    return this.each(function() {
      var $this = $(this), html = $this.html();
      contract();
      function contract() {
        $this.html(html.replace(/<!--\s*more\s*-->(.|\s|\n)*/,
              '<span class="more"></span>'));
        $this.find('.more').click(expand);
      }
      function expand() {
        $this.html(html + '<span class="less"></span>');
        $this.find('.less').click(contract);
      }
    });
  },
  // Display each of the target elements in sequence, cross-fading to
  // the next one.  This call doesn't actually run the animation; it
  // returns a singleton with 'start' and 'stop' methods to turn it on
  // and off.
  crossfader: function(options) {
    options = options || {};
    var period = options.period || 5000;     // display time for each element
    var hangTime = options.hangTime || 2000; // time to display w/out change
    var transitionTime = period - hangTime;  // duration of animation
    var period = (transitionTime + hangTime) * this.length;
    var $es = this;
    return {
      start: function() {
        $es.stop(true).
          css({display:'block', opacity:0}).
          each(function(i) { $(this).animate({opacity:0}, period*i/$es.length); }).
          each(function() { cycle($(this)); });
        function cycle($e) {
          $e.animate({opacity:1}, transitionTime)
            .animate({opacity:1}, hangTime)
            .animate({opacity:0}, transitionTime)
            .animate({opacity:0}, function() { cycle($e); });
        }
      },
      stop: function() {
        $es.stop(true).animate({opacity:0}, transitionTime/2);
      }
    };
  },
  // map is a hash from URL to title.  Set the titles of the target
  // elements to its values
  setTitlesFromMap: function(map) {
    return this.each(function() {
      var $this = $(this), href = $this.attr('href');
      if (href in map)
        $this.attr('title', map[href].replace(/\.\.\./g, '\u2026'));
      else if (window.location.search.match(/\breport-missing-titles\b/)
               && window.console && console.info && $.isFunction(console.info))
        console.info('No title entry for ', href);
    });
  },
  // Copy img[@alt] -> img[@title].  The caller should filter on
  // :not(title) to avoid overriding titles that already set.
  setImageTitles: function() {
    return this.each(function() {
      var $this = $(this);
      $this.attr('title', $this.attr('alt'));
    });
  },
  // like jQuery.toggle, but call onadd, onremove respectively when
  // the class name is added or removed
  toggling: function(className, onadd, onremove) {
    if (this.hasClass(className)) {
      this.removeClass(className);
      $.isFunction(onremove) && onremove(this);
    } else {
      this.addClass(className);
      $.isFunction(onadd) && onadd(this);
    }
  }
})})(jQuery);


/*
 * Utilities
 */

// Return a function like this function except that once called,
// calling it again does nothing, until the function invokes the
// function argument that is passed to it.  This is used to create
// non-reentrant functions that are used in continuation-passing
// style.
Function.prototype.nullifyWhileExecutingK = function() {
  var fn = this, guard = false;
  return function() {
    if (guard) return;
    guard = true;
    return fn.call(this, function() { guard = false; });
  };
};

Function.prototype.serializedK = function() {
  var fn = this, pending = [], active = false;
  return function() {
    args = Array.prototype.slice.call(arguments, 0);
    args.unshift(k);
    if (active)
      pending.push([this, args]);
    else {
      active = true;
      fn.apply(this, args);
    }
  };
  function k() {
    if (pending.length) {
      var ap = pending.shift();
      fn.apply(ap[0], ap[1]);
    } else
      active = false;
  }
};


/*
 * Projects tab
 */
$(function() {
  $('.bottom-tab').each(function() {
    var $tab = $(this);
    var $title = $tab.find('.tab-title');
    var $content = $tab.find('.content');
    var $iframe = $content.find('iframe');
    var openCss = {top:5};
    var closedCss = {position:$tab.css('position'), top:$tab.css('top'), bottom:$tab.css('bottom'), zIndex:$tab.css('zIndex')};
    var closedHeight;
    var duration = 2000;

    $title.mouseover(function() {
      $tab.hasClass('open') || $tab.stop(true).animate({bottom:-2});
      loadContent();
    }).mouseout(function() {
      $tab.hasClass('open') || $tab.stop(true).animate({bottom:closedCss.bottom});
    }).click(function(done) {
      $tab.toggling('open', function() {
        // do open
        var y = $tab.offset().top; // read this before $content.show() changes it
        closedHeight = $tab.height();
        loadContent();
        $content.show();
        $tab.css({position:'fixed', top:y, bottom:'inherit', zIndex:100}).
    animate(openCss, duration, done);
      }, function() {
        // do close
        var y = $(window).height() - closedHeight - parseInt(closedCss.bottom, 10);
        $tab.css(closedCss);
        $content.hide();
        done();
        return;
        // following doesn't work in ff
        $tab.animate({top:y}, duration, function() {
    $tab.css(closedCss);
    $content.hide();
    done();
        });
      });
    }.nullifyWhileExecutingK());
    function loadContent() {
      $iframe.attr('src') || $iframe.attr('src', '/projects');
    }
  });
});


/*
 * Personalize
 *
 * Quick-and-dirty code to replace my name by first- and second-person
 * references, switched off a person-{n} class on the 'body' element.
 */
$(function() {
  var name = $('title').text().match(/(.+?)(?=\s+HTML)/)[0];
  $('#person-controls .p').mouseover(function(k) {
    var $this = $(this), $title = $('title');
    var p = parseInt($this.text()), className = 'person-' + p;
    if ($('body').hasClass(className)) return;
    // animate a rectangle from the button over the window
    var $b = $('<div/>').css($.extend({position:'absolute',background:'blue',
               zIndex:5, opacity:.5}, $this.bounds())).
      appendTo('body');
    $b.animate({left:0, top:0,
    width:$(window).width()-1,
    height:$(window).height()-1,opacity:0},
         function() {
                 $b.remove();
                 setPersonClass(className);
                 $('.ego').stop().css('backgroundColor', '#88f').animate({'backgroundColor':'white'}, function() { $(this).css('backgroundColor', 'inherit') });
               });
    function setPersonClass(className) {
      $('body').
        removeClass('person-1 person-2 person-3').
        addClass(className);
      // switch the 'body' class
      $('#person-controls div').removeClass('selected');
      $this.addClass('selected');
      // update the title
      $title.text($title.text().replace(/(.+?)(?=\s+HTML)/,
                {1:'My', 2:'Your', 3:name}[p]));
    }
  }).each(function() {
    var $this = $(this), t = $this.text();
    $this.attr('title', 'Change the page text to ' + t + ' person.');
  });
  // replace each of the ego references by a classname-switched
  // structure that includes the text in each grammatical person
  $('p').personalize({fullName:'Oliver Steele', gender:'m'});
});

/*
 * TODO DRY regexp construction
 * TODO scan backwards to determine he/his capitalization
 */
(function($) {
  $.fn.personalize = function(options) {
    options = $.extend({}, options);
    if (options.fullName) {
      var names = options.fullName.match(/(.+?)\s+(.+)/);
      $.extend(options, {firstName:names[1], lastName:names[2]});
    }
    var map = $.extend({}, options);
    $.extend(map, options.gender.match(/^m/i)
             ? {he:'he', his:'his'}
             : {he:'she', his:'her'});
    $.extend(map, {He:map.he.capitalize(),
                   His:map.his.capitalize(),
                   expand: function(s) {
                     if (s instanceof RegExp)
                       return eval(this.expand(s.toString()));
                     return s.replace(/\b(firstName|lastName|he|his|He|His)\b/g,
                                      function(_, s) { return map[s] });
                   }});
    var sel = map.expand('*:contains(firstName), *:contains(he), *:contains(his)');
    var re = map.expand(/\b((firstName(\s+lastName)?|He|he)(\s+(is|was))?|His|his)\b/g);
    return this.filter(sel).each(function() {
      var $this = $(this);
      $this.html($this.html().replace(re,
        function(_, s) {
    return '<span class="ego">' +
      '<span class="person-1">' + person(s, 1, map) + '</span>' +
      '<span class="person-2">' + person(s, 2, map) + '</span>' +
      '<span class="person-3">' + s + ' </span>' +
      '</span>';
        }));
    });
  };

  var person1 = {He:'I', is:'am', was:'was', His:'My', his:'my'};
  var person2 = {He:'You', is:'are', was:'were', His:'Your', his:'your'};

  function person(str, person, map) {
    switch (person) {
    case 1:
      return applyMap(person1);
      break;
    case 2:
      return applyMap(person2);
      break;
    case 3:
      return str.replace(map.expand(/firstName(?:\s+lastName)?/g), 'He');
      break;
    }
    function applyMap(smap) {
      var re = map.expand(/\b((?:firstName(?:\s+lastName)?)|He|he)(?:\s+(is|was))?\b/g);
      return str.replace(re, function(_, s, v) {
        return smap.He + (v in smap ? ' ' + smap[v] : v || '');
      }).replace(/\bHis\b/, smap.His).replace(/\bhis\b/, smap.his);
    }
  }

  if (!String.prototype.capitalize)
    String.prototype.capitalize = function() {
      return this.slice(0,1).toUpperCase() + this.slice(1);
    };
})(jQuery);


$(function(){
  $('#container').masonry({
    // options
    itemSelector : '.item',
    columnWidth : 240
  });
});
