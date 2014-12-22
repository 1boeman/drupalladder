/*
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - https://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth
(function ($, Drupal, window, document, undefined) {

// To understand behaviors, see https://drupal.org/node/756722#behaviors
Drupal.behaviors.muziekladder_menu = {
  attach: function(context, settings) {
    $('.muziekladder_logo', context).once('muziekladder_menu',function(){
        $(this).click(function(){
          location.href = '/'; 
        })
    });

    $('#block-system-main-menu .menu li', context).once('muziekladder_menu',function(){
        $(this)
        .hover(
          function(){$(this).addClass('hover')},
          function(){$(this).removeClass('hover')}) 
        .click(function(){
            location.href = $(this).find('a')[0].href;
        }); 
    }); 
    var $window = $(window) 
    var $topmenu = $('#navigation');
    var scroll_trigger_height = 20;
    var menu_locked = false; 
    var scrollTimer = 0; 
    // attach handler to scroll event  - comment this out to disable fixed menu
    $window.scroll (function(){
      // prevent resource hogging:
      if (scrollTimer) {
         clearTimeout(scrollTimer);   // clear any previous pending timer
      }
      scrollTimer = setTimeout(respond_to_scroll, 10);
    }); 
    respond_to_scroll(); 

    // Fixes top-menu to top of page on when page scroll reaches scroll_trigger_height 
    function respond_to_scroll(){
      var scrollHeight = $window.scrollTop();
      if ( scrollHeight > scroll_trigger_height && !menu_locked  ) {
           menu_locked = true;
           $topmenu.addClass('scrolling'); 
      } else if ( scrollHeight < scroll_trigger_height && menu_locked ) {
           menu_locked = false;
           $topmenu.removeClass('scrolling');
      } 
    }
  }
};


})(jQuery, Drupal, this, this.document);
