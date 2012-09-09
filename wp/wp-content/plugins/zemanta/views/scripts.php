
<script type="text/javascript">
// tracking
(function () {
  var logGA = (function () {
      var ga = document.createElement('script'), 
        ns = 'ZemantaWordPressNS', ga_prefix = ns ? ns + '.' : '';
      
      ga.type = 'text/javascript';
      ga.async = true;
      ga.src = ('https:' === document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      
      document.getElementsByTagName('head')[0].appendChild(ga);
      
      if(!window._gaq) {
        window._gaq = [];
      }
      
      window._gaq.push([ga_prefix + '_setAccount', 'UA-1933864-10']);
      window._gaq.push([ga_prefix + '_setDomainName', 'none']);
      
      return function (d, page) {
        window._gaq.push([ga_prefix + '_trackPageview', '/tracking' + (page.substring(0, 1) !== '/' ? '/' + page : page)]);
      };
    })();

    logGA(null, '/wp-zemanta/views/settings');
})();

// settings page
jQuery(document).ready(function ($) {

  // obtain username by apikey and replace signin links with baked URL
  (function () {
    var api_key = "<?php echo esc_js($api_key); ?>",
      get_prefs_url = function (api_key) {
        var params = {
          method: 'zemanta.preferences',
          api_key: api_key,
          format: 'json'
        };
        $.post('http://api.zemanta.com/services/rest/0.0/', params, function (data) {
          if (data && data.config_url) {
            $('a.prefs-signin').attr('href', data.config_url);
          }
        });
      };

      if(api_key.length) {
        get_prefs_url(api_key);
      }
  }());

  // init twitter widget
  $.getScript('http://widgets.twimg.com/j/2/widget.js', function () {
    new TWTR.Widget({
        version: 2,
        type: 'profile',
        rpp: 4,
        interval: 30000,
        width: 250,
        height: 300,
        id: 'tweets_div',
        theme: {
          shell: {
            background: '#90a6b5',
            color: '#ffffff'
          },
          tweets: {
            background: '#ffffff',
            color: '#000000',
            links: '#f68720'
          }
        },
        features: {
          scrollbar: true,
          loop: false,
          live: true,
          behavior: 'all'
        }
      }).render().setUser('ZemantaSupport').start();
  });

  // init "path" fields
  $('.basepath').each(function () {
    var n = $(this).next('input');
    n.css('padding-left', parseInt($(this).width(),10)+2);
    $(this).click(function () {
      n.focus();
    });
  });

  // hide custom path field when checkbox is opt'd-out and setup initial state
  $('#zemanta_options_image_uploader_custom_path').click(function () {
    $('#zemanta_options_image_uploader_dir').parents('tr').toggle(!!$(this).attr('checked'));
  }).triggerHandler('click');

});
</script>
