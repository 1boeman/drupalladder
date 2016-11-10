(function($){
  $('.desc-opener').click(function(e){
    e.preventDefault();
    $(this).parents('.iswaar-link-container').toggleClass('expanded');
    return false; 
  })

})(jQuery);
