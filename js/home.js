/* Copyright 2009 by Oliver Steele.  Available under the MIT License. */

$(function() {
  // shorten
  $('.shorten').each(function() {
    var $this = $(this), html = $this.html();
    shrink();
    function shrink() {
      $this.html(html.replace(/<!--\s*more\s*-->(.|\s|\n)*/,
			      '<span class="more"></span>'));
      $this.find('.more').click(grow);
    }
    function grow() {
      $this.html(html + '<span class="less"></span>');
      $this.find('.less').click(shrink);
    }
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

  // link titles
  $('a:not([title])').each(function() {
    var $this = $(this), map = kLinkTitleMap, title = map[$this.attr('href')];
    title && $this.attr('title', title.replace(/\.\.\./g, '\u2026'));
    if (window.location.search.match(/\breport\b/) && !($this.attr('href') in map))
      console.info('Missing title:', $this.attr('href'));
  });

  // image titles
  $('img:not([title])').each(function() {
    var $this = $(this);
    $this.attr('title', $this.attr('alt'));
  });

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
});


/*
 * Projects tab
 */
$(function() {
  var $area = $('#projects-area');
  var $tab = $('#projects-tab');
  var $frame = $('#projects');
  var $iframe = $('#projects iframe');
  var closedHeight;
  var openCss = {top:5};
  var closedCss = {top:$area.css('top'), bottom:$area.css('bottom')};
  var duration = 2000;

  $('#projects-tab').mouseover(function() {
    $area.hasClass('open') || $area.stop(true).animate({bottom:0});
  }).mouseout(function() {
    $area.hasClass('open') || $area.stop(true).animate({bottom:closedCss.bottom});
  }).click(function(done) {
    $area.toggling('open', function() {
      // do open
      var y = $area.offset().top; // read this before $frame.show() changes it
      closedHeight = $area.height();
      $iframe.attr('src') || $iframe.attr('src', '/projects');
      $frame.show();
      $area.css({top:y, bottom:'inherit'}).
	animate(openCss, duration, done);
    }, function() {
      // do close
      var y = $(window).height() - closedHeight - parseInt(closedCss.bottom, 10);
      $area.css(closedCss);
      $frame.hide();
      done();
      return;
      // following doesn't work in ff
      $area.animate({top:y}, duration, function() {
        console.info('reset to' , closedCss);
	$area.css(closedCss);
	$frame.hide();
	done();
      });
    });
  }.withBarrier());
});


/*
 * Utilities
 */

jQuery.extend(jQuery.fn, {
  bounds: function() {
    if (!this[0]) return null;
    return jQuery.extend(this.offset(), {width:this.width(), height:this.height()});
  },
  cycle: function() {
    var changeTime = 3000, stayTime = 2000;
    var period = (changeTime + stayTime) * this.length;
    var $es = this;
    return {
      start: function() {
        $es.stop(true).
          css({display:'block', opacity:0}).
          each(function(i) { $(this).animate({opacity:0}, period*i/$es.length); }).
          each(function() { cycle($(this)); });
        function cycle($e) {
          $e.animate({opacity:1}, changeTime)
            .animate({opacity:1}, stayTime)
            .animate({opacity:0}, changeTime)
            .animate({opacity:0}, stayTime, function() { cycle($e); });
        }
      },
      stop: function() {
        $es.stop(true).animate({opacity:0}, changeTime/2);
      }
    };
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
