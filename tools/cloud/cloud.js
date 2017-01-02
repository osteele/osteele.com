var username = 'user';
var password = 'xxxxxx';
var gData;
var tagtrail;

$(function() {
    if (0) {
      new Delicious.Data(username, password).drop();
      return;
    }
    run();
  });

function run() {
  tagtrail = [username];
  setBreadcrumb(tagtrail);
  $("#trail a").live('click', function() {
      var path = $(this).attr('href').slice(1).split('/');
      tagtrail = path.slice(0, path.length - 1);
      console.info(path, tagtrail);
      setBreadcrumb(tagtrail);
      return false;
    });
  var data = gData = new Delicious.Data(username, password);
  data.withPostCount(function(count) {
      console.info('using ', count, 'posts');
      //TODO update
      count || populate();
      //showPosts();
      showTags();
      Timeline.setup();
    }, function() { data.populate() });
}

function setBreadcrumb(trail) {
  var $trail = $("#trail");
  $trail.html('<ul/>');
  $.each(trail, function(i, label) {
      var hash = trail.slice(0,i+1).join('/');
      $('<a/>').attr('href', '#'+hash).text(label).appendTo('#trail ul');
    });
  $trail.find('ul a').wrap('<li/>');
  $trail.jBreadCrumb();
}

function showPosts() {
  gData.withPosts(function (row) {
      for (var i = 0; i < rs.rows.length; i++)
        console.info(rs.rows.item(i).href);
    });
}

function showTags() {
  var spans = [];
  var tag_names = [];
  gData.withTags(function(row) {
      tag_names.push('<a href="#',row.name,'" rel="',row.count,'">', row.name, '</a>');
      spans.push('<tr><td>',row.name,'</td><td>',row.count,'</td></tr>');
    }, function() {
      $('#tags').html(spans.join(''));
      $("#tagcloud").html(tag_names.join(''));
      $("#tagcloud a").tagcloud({
            color:{start:'#afa',end:'#0f0'},
            size:{start:8,end:24,unit:'pt'}});
      $('tr, #tagcloud a').click(show).mouseover(mo);
      function show() {
        var tagName = $('td:first', this).text() || $(this).text();
        tagtrail.push(tagName);
        setBreadcrumb(tagtrail);
        Timeline.setTag(tagName);
        var spans = [];
        gData.withPostsByTag({tagName: tagName}, function(row) {
            var thumb = 'http://api.thumbalizr.com/?url=' + encodeURIComponent(row.href) + '&width=100';
            //thumb = 'http://www.iwebtool2.com/img/?r=' + 'http://user.dev&domain=' + encodeURIComponent(row.href);
            
            spans.push('<tr><td><a href="',row.href,'">',row.description,'</a></td><td><img src="', thumb, '"/></td></tr>');
          }, function() {
            $('#posts').html(spans.join(''));
          });
        return false;
      }
      function mo() {
        var tagName = $('td:first', this).text() || $(this).text();
        Timeline.setTag(tagName, true);
      }
    });
}

var Timeline = {
  setup:function() {
    drawFunnel();
    var w = $('#timeline').width(), h = $('#timeline').height();
    var r = Raphael('timeline', w, h);
    r.rect(0, 0, w, h).attr({fill:'#d0d0ff', stroke:'none'});
    this.path = r.path({stroke:'#4040ff', 'stroke-width':.5});
    this.secondaryPath = r.path({stroke:'#40ff00', 'stroke-width':1, path:['M',[0,h+1],'L',[w,h+1],'z'].join('')});
    // scrim
    scrimOptions = {fill:'white', stroke:"none", opacity:.6};
    r.rect(0, 0, w/3, h).attr(scrimOptions);
    r.rect(w*2/3, 0, w/3, h).attr(scrimOptions);

    showTimeline(null, true);
  },

  setTag:function(tagName, secondary) {
    //showTimeline(tagName, false, secondary);
  }
};

function showTimeline(tagName, firstTime, secondary) {
  gData.withPostTimes({tagName:tagName},
    function(times) {
      if (firstTime) {
        Timeline.startTime = times[0];
        Timeline.endTime = times[times.length-1];
      }
      var startTime = Timeline.startTime;
      var endTime = Timeline.endTime;
      $('#timeline-start-date, #selection-start-date').text(new Date(startTime).toLocaleDateString());
      $('#timeline-end-date, #selection-end-date').text(new Date(endTime).toLocaleDateString());
      var w = $('#timeline').width(), h = $('#timeline').height();
      var hs = Histogram(times, {bucketCount:w, min:startTime, max:endTime});
      if (firstTime)
        Timeline.max = hs.bucketMax;
      var bucketMax = Timeline.max;
      var segments = ['M',[0,h]];
      for (var i = 0; i < w; i++)
        segments.push('L',[i,h - h * (hs.buckets[i]/bucketMax)]);
      segments.push('L',[w-1,h]);
      var path = secondary ? Timeline.secondaryPath : Timeline.path;
      var duration = firstTime ? 0 : secondary ? 500 : 500;
      path.animate({path:segments.join(''), fill:'#4040ff'}, duration);
    });
}

var Histogram = function(data, options) {
  var count = options.bucketCount;
  var min = 'min' in options ? options.min : Math.min.apply(null, data);
  var max = 'max' in options ? options.max : Math.max.apply(null, data);
  var buckets = [];
  for (var i = count; --i >= 0; )
    buckets[i] = 0;
  for (var i = 0; i < data.length; i++) {
    var n = Math.floor(count * (data[i]-min) / (max+1-min));
    buckets[n]++;
  }
  bucketMax = Math.max.apply(null, buckets);
  return {buckets:buckets, min:min, max:max, bucketMax:bucketMax};
};

function drawFunnel() {
  var w = $('#tagcloud').width();
  var h = $('#funnel').height();
  var p = window.rr = window.rr || Raphael('funnel', w, h);
  p.setSize(w,h);
  var tml = (w-610+2*610/3)/2, tmr = w - tml, r = 30;
  var path = ['M',[0,0],
              'Q',[0,r,r,r],
              'L',[tml-r,r],
              'Q',[tml,r,tml,r+r],
              'L',[tml,h],
              'L',[tmr,h],
              'L',[tmr,r+r],
              'Q',[tmr,r,tmr+r,r],
              'L',[w-r,r],
              'Q',[w,r,w,0],
              'z'];
  if (window.pp)
    pp.attr({path:path.join('')});
  else
    pp = p.path({path:path.join(''), gradient:'90-#d0d0ff-#f0f0ff', stroke:'none'});
}

$(function() {
    $(window).resize(function() {
        drawFunnel();
      });
    return;
    $('#posts a').live('mouseover', function() {
        console.info(this.href);
      });
  });
