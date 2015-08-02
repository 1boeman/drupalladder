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
  var settings = Drupal.settings;


  var allPageHandlers = {
    showUserOptions:function(){
      var $userContainer = $('.user-container');
      $('.userOptions')
        .toggleClass('nodisplay')
        .mouseleave(function(){
          $(this).addClass('nodisplay')    
        })
    },
    presentLogin:function(){
      var $loginblock = $('#block-user-login');
      if ( $loginblock.length ) {
        var $close = $loginblock.find('.close_button');
        if (!$close.length){
          $close = $('<div class="close_button">&times;</div>');  
          $loginblock.prepend($close)
            .appendTo('body')
            .animate({top:'110px'});
          $close.click(function(){
            $loginblock.removeClass('present')
            $('#page').removeClass('login_open');
          });
        } 
        $loginblock.addClass('present')
        $('#page').addClass('login_open');
        $('html,body').animate({scrollTop: 0}, 500);
      }else{
        window.location = settings.basePath+settings.pathPrefix+'user/login';
      }
    }
  };
  // this is also in main.js, but main.js is not included on all pages, 
  // so if the handlers need to be on all pages define it above
  $('body').on('click','.handleMe',function(e){
    var $this = $(this);
    if ( typeof allPageHandlers[$this.data('handler')] === 'function' ){
      e.preventDefault();
      allPageHandlers[$this.data('handler')].apply(this,[e]);
    }
  });


// To understand behaviors, see https://drupal.org/node/756722#behaviors
Drupal.behaviors.muziekladder_menu = {
  attach: function(context, settings) {
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
    if (screen.width > 500){
      $window.scroll (function(){
        // prevent resource hogging:
        if (scrollTimer) {
           clearTimeout(scrollTimer);   // clear any previous pending timer
        }
        scrollTimer = setTimeout(respond_to_scroll, 10);
      }); 
    respond_to_scroll(); 
    }
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
