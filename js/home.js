/* Copyright 2009 by Oliver Steele.  Available under the MIT License. */

$(function() {
  $('.shorten').contractMores();

  // add titles
  $('a:not([title])').setTitlesFromMap(kLinkTitleMap);
  $('img:not([title])').setImageTitles();

  // image fade
  $('.candids img').
    mouseover(function() { $(this).stop().animate({opacity: 1}) }).
    mouseout(function() { $(this).stop().animate({opacity: .75}) });

  // link mouseover
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
    var cycle = $('h1 .caption').cycle();
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
  }.withBarrier());
});


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
    }.withBarrier());
    function loadContent() {
      $iframe.attr('src') || $iframe.attr('src', '/projects');
    }
  });
});


/*
 * Plugins
 */

$.extend($.fn, {
  bounds: function() {
    if (!this[0]) return null;
    return $.extend(this.offset(), {width:this.width(), height:this.height()});
  },
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
  cycle: function() {
    var transitionTime = 3000, hangTime = 2000;
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
  setImageTitles: function() {
    return this.each(function() {
      var $this = $(this);
      $this.attr('title', $this.attr('alt'));
    });
  },
  toggling: function(className, onadd, onremove) {
    if (this.hasClass(className)) {
      this.removeClass(className);
      onremove(this);
    } else {
      this.addClass(className);
      onadd(this);
    }
  }
});


/*
 * Utilities
 */

Function.prototype.withBarrier = function() {
  var fn = this, guard = false;
  return function() {
    if (guard) return;
    guard = true;
    return fn.call(this, function() { guard = false; });
  }
}


/*
 * Personalize
 *
 * TODO parameterize the name, gender
 * TODO DRY regexp construction
 * TODO scan backwards to determine he/his capitalization
 */
$(function() {
  var name = $('title').text().match(/(.+?)(?=\s+HTML)/)[0];
  $('#person-controls .p').mouseover(function() {
    var $this = $(this), $title = $('title');
    var p = parseInt($(this).text()), className = 'person-' + p;
    if ($('body').hasClass(className)) return;
    $('body').
      removeClass('person-1 person-2 person-3').
      addClass(className);
    $('#person-controls div').removeClass('selected');
    $this.addClass('selected');
    $title.text($title.text().replace(/(.+?)(?=\s+HTML)/,
				      {1:'My', 2:'Your', 3:name}[p]));
    var $b = $('<div/>').css($.extend({position:'absolute',background:'blue',
				       zIndex:5, opacity:.5}, $this.bounds())).
      appendTo('body');
    $b.animate({left:0, top:0,
		width:$(window).width()-1,
		height:$(window).height()-1,opacity:0},
	       function() { $b.remove(); });
  });
  $('p').filter('*:contains(Oliver), *:contains(he), *:contains(his)').each(function() {
    var $this = $(this), html = $this.html();
    $this.html(html.replace(
	/((Oliver(\s+Steele)?|\b(He|he)\b)(\s+(is|was))?|\bHis\b|\bhis\b)/g,
      function(_, s) {
	return '<span class="ego">' +
	  '<span class="person-1">' + person(s, 1) + '</span>' +
	  '<span class="person-2">' + person(s, 2) + '</span>' +
	  '<span class="person-3">' + s + ' </span>' +
	  '</span>';
      }));
  });
});

function person(str, person) {
  switch (person) {
  case 1:
    return map({He:'I', is:'am', was:'was', His:'My', his:'my'});
    break;
  case 2:
    return map({He:'You', is:'are', was:'were', His:'Your', his:'your'});
    break;
  case 3:
    return html.replace(/Oliver(?:\s+Steele)?/g, 'He');
    break;
  }
  function map(map) {
    return str.replace(/((?:Oliver(?:\s+Steele)?)|\bHe\b|\bhe\b)(?:\s+(is|was))?/g, function(_, s, v) {
      return map.He + ({is:' '+map.is, was:' '+map.was}[v]||v||'');
    }).replace(/\bHis\b/, map.His).replace(/\bhis\b/, map.his);
  }
}
