/**
 * @author Jason Roy for CompareNetworks Inc.
 *
 * Verision 0.1, improvements to be made.
 * Copyright (c) 2008 CompareNetworks Inc.
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
(function($) {
    // Private variables
    
    var _options = {};
    var _breadCrumbElements = {};
	var _autoIntervalArray = [];
    
    // Public functions
    
    $.fn.jBreadCrumb = function(options) {
      _options = $.extend({}, $.fn.jBreadCrumb.defaults, options);
      return this.each(function() {
          setupBreadCrumb($(this));
        });
    };
    
    // Private functions
    
    function setupBreadCrumb($container) {
        //The reference object containing all of the breadcrumb elements
        var $items = _breadCrumbElements = $container.find('li');
        
        //Keep it from overflowing in ie6 & 7
		$container.find('ul').wrap('<div style="overflow:hidden; position:relative;  width: ' + $container.css("width") + ';"><div>');
		//Set an abitrary wide width to avoid float drop on the animation
        $container.find('ul').width(5000);
        
        //If the breadcrumb contains nothing, don't do anything
        if (!$items.length) return;

        $($items[$items.length - 1]).addClass('last');
        $($items[0]).addClass('first');
            
        //If the breadcrumb object length is long enough, compress.
        if ($items.length > _options.minimumCompressionElements)
          compressBreadCrumb($items);
 	};
    
    function compressBreadCrumb($items) {
        // Factor to determine if we should compress the element at all
        var $finalElement = $($items[$items.length - 1]);
        
        // If the final element is really long, compress more elements
        if ($finalElement.width() > _options.maxFinalElementLength) {
            if (_options.beginningElementsToLeaveOpen > 0) {
                _options.beginningElementsToLeaveOpen--;
            }
            if (_options.endElementsToLeaveOpen > 0) {
                _options.endElementsToLeaveOpen--;
            }
        }
        // If the final element is within the short and long range,
        // compress to the default end elements and 1 less beginning
        // elements
        if (_options.maxFinalElementLength < $finalElement.width() &&
            $finalElement.width() > _options.minFinalElementLength &&
            _options.beginningElementsToLeaveOpen > 0) 
          _options.beginningElementsToLeaveOpen--;
        
        var itemsToRemove = _breadCrumbElements.length - 1 - _options.endElementsToLeaveOpen;
        
        // We compress only elements determined by the formula setting below
        
        //TODO : Make this smarter, it's only checking the final
        //element's length.  It could also check the amount of
        //elements.
        $($items[items.length - 1]).css({background: 'none'});
        
        $($items).each(function(i, listElement) {
            var $listElement = $(listElement);
            var $a = $listElement.find('a');
            if (i > _options.beginningElementsToLeaveOpen && i < itemsToRemove) {
              $a.wrap('<span></span>').width($a.width() + 10);
              // Add the overlay png.
              $listElement.append($(_options.overlayClass + '.main').clone().
                                  removeClass('main').css({display: 'block'})
                                  ).css({background: 'none'});
              if (isIE6OrLess()) {
                fixPNG($listElement.find(_options.overlayClass).css({width: '20px', right: "-1px"}));
              }
              
              var $span = $listElement.find('span');
              var options = {
                  id: i,
                  width: $listElement.width(),
                  listElement: $span,
                  isAnimating: false,
                  element: $span
              };
              $listElement.bind('mouseover', options, expandBreadCrumb).bind('mouseout', options, shrinkBreadCrumb);
              $a.unbind('mouseover', expandBreadCrumb).unbind('mouseout', shrinkBreadCrumb);
              setTimeout(function() {
                  $span.animate({width: _options.previewWidth},
                                _options.timeInitialCollapse, _options.easing);
              }, 150 * (i - 2));
            }
        });
    };
    
    function expandBreadCrumb(e) {
        var elementID = e.data.id;
        var originalWidth = e.data.width;
        $(e.data.element).stop().
          animate({width: originalWidth}, {
              duration: _options.timeExpansionAnimation,
              easing: _options.easing,
              queue: false
          });
        return false;
    };
    
    function shrinkBreadCrumb(e) {
        var elementID = e.data.id;
        $(e.data.element).stop().
          animate({width: _options.previewWidth}, {
            duration: _options.timeCompressionAnimation,
            easing: _options.easing,
            queue: false
          });
        return false;
    };
    
    function isIE6OrLess() {
        var isIE6 = $.browser.msie && /MSIE\s(5\.5|6\.)/.test(navigator.userAgent);
        return isIE6;
    };
    // Fix The Overlay for IE6
    function fixPNG(element) {
      var $element = $(element);
      var image;
      if ($element.is('img')) {
        image = $element.attr('src');
      } else {
        image = $element.css('backgroundImage');
        image.match(/^url\(["']?(.*\.png)["']?\)$/i); // "'));
        image = RegExp.$1;
      }
      $element.css({
          backgroundImage: 'none',
          filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=scale, src='" + image + "')"
      });
    };
    
    // Public global variables
    $.fn.jBreadCrumb.defaults = {
        maxFinalElementLength: 400,
        minFinalElementLength: 200,
        minimumCompressionElements: 4,
        endElementsToLeaveOpen: 1,
        beginningElementsToLeaveOpen: 1,
        minElementsToCollapse: 4,
        timeExpansionAnimation: 800,
        timeCompressionAnimation: 500,
        timeInitialCollapse: 600,
        easing: 'easeOutQuad',
        overlayClass: '.chevronOverlay',
        previewWidth: 5
    };
})(jQuery);
