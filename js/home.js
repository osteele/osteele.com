/* Copyright 2009 by Oliver Steele.  Available under the MIT License. */

$(function() {
  var name = $('title').text().match(/(.+?)(?=\s+HTML)/)[0];
  $('#person-controls div').mouseover(function() {
    var p = $(this).text();
    $('body').
      removeClass('person-1 person-2 person-3').
      addClass('person-' + p);
    $('#person-controls div').removeClass('selected');
    $(this).addClass('selected');
    $('title').text($('title').text().replace(/(.+?)(?=\s+HTML)/, 
					      {1:'My', 2:'Your', 3:name}[p]));
  });
  $('p').filter('*:contains(Oliver), *:contains(his)').each(function() {
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

  var moving = false;
  $('h1 img').mouseover(function() {
    var $this = $(this);
    var $egg = $('h1 iframe');
    var small = $this.bounds();
    small.width = small.height = Math.max(small.width, small.height);
    var large = {left:small.width+$('body').width()-400,
		top:20,
		width:300,height:300};
    if (moving) return;
    if ($egg.filter(':visible').length) {
      large = $egg.bounds();
      $egg.css({position:'absolute', right:'inherit'});
      $egg.css(large).animate(small, function(){$egg.hide()});
      $('.candids').show('slow');
    } else {
      moving = true;
      $egg.show().css(small).animate(large, function(){
	moving = false;
	$egg.css({position:'fixed', left:'inherit', right:50});
      });
      $('.candids').hide('slow');
    }
  });

  $('img:not([title])').each(function() {
    var $this = $(this);
    $this.attr('title', $this.attr("alt"));
  });

  $('.candids img').
    mouseover(function() { $(this).stop().animate({opacity: 1}) }).
    mouseout(function() { $(this).stop().animate({opacity: .75}) });

  $('a').live('mouseover', function() {
    $(this).stop(true).css({backgroundColor:'yellow'});
  }).live('mouseout', function() {
    $(this).stop(true).animate({backgroundColor:'white'},'slow');
  });
});

$(function() {
  var $area = $('#projects-area');
  var $tab = $('#projects-tab');
  var $frame = $('#projects');
  var $iframe = $('#projects iframe');
  var closedTop;
  var openCss = {top:5};
  var closedCss = {top:$area.css('top'), bottom:$area.css('bottom')};
  var duration = 2000;

  $('#projects-tab').click(withBarrier(function(done) {
    $area.toggling('open', function() {
      // open
      closedTop = $area.offset().top;
      $iframe.attr('src') || $iframe.attr('src', '/projects');
      $frame.show();
      $area.css({top:closedTop, bottom:'inherit'}).
	animate(openCss, duration, done);
    }, function() {
      // close
      $area.animate({top:closedTop}, duration, function() {
	$area.css(closedCss);
	$frame.hide();
	done();
      });
    });
  }));
  jQuery.fn.toggling = function(className, onadd, onremove) {
    if (this.hasClass(className)) {
      this.removeClass(className);
      onremove(this);
    } else {
      this.addClass(className);
      onadd(this);
    }
  };
  function withBarrier(fn) {
    var mutex = false;
    return function() {
      if (mutex) return;
      mutex = true;
      return fn.call(this, function() { mutex = false; });
    }
  }
});

$.extend($.fn, {
  bounds: function() {
    if (!this[0]) return null;
    return $.extend(this.offset(), {width:this.width(), height:this.height()});
  }
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
