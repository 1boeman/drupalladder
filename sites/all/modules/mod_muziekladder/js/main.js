var hC = Drupal.settings.muziekladder;
 
(function($){
  hC.jsDir = '/js/';
  hC.mapsKey = 'AIzaSyDEVR7pVblikD8NSlawdwv8nFnOxzx8PBo';
  hC.pathToTheme = Drupal.settings.basePath + "sites/all/themes/" + Drupal.settings.ajaxPageState.theme;
  laad.pathConfig = {
    "bootstrap"     :hC.muziekladderBasePath + hC.jsDir+"bootstrap.min.js",
    "util"          :hC.muziekladderBasePath + hC.jsDir+"util.js",
    "locationpage"  :hC.muziekladderBasePath + hC.jsDir+"locationpage.js",
    "autocomplete"  :hC.muziekladderBasePath + hC.jsDir+"jquery.autocomplete.min.js",
    "locations"     :hC.muziekladderBasePath + hC.jsDir+"locations.js",
    "maps"          :'//maps.googleapis.com/maps/api/js?key='+hC.mapsKey+'&sensor=false&callback=hC.mapInitialize',
    "addthis"       :'//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-542e60be78f12e17'
  };

  var pageHandlers = {}, 
      handlers = {};
     
  laad.wait(['util'],function(){
    $(function(){
        $('body').on('click','.handleMe',function(e){
            e.preventDefault();
            var $this = $(this);
            if ( typeof handlers[$this.data('handler')] === 'function' ){
                 handlers[$this.data('handler')].apply(this,[e]);
            }
        });
        var i,bodyClass =  document.body.className.split(/\s+/),
            lngth = bodyClass.length; 
        
        for (i = 0; i < lngth; i++){
            if ( typeof pageHandlers[bodyClass[i]] === 'function' )
                $.extend(handlers, pageHandlers[bodyClass[i]]());
        }
    });
  });

  pageHandlers.muziekformulier = function(){
    var $form = $('#mod-muziekladder-mailtipform'); 
    // not logged in / always
    $('.aanraadloginlink').parents('.btn').click(function(){
      location.href = $(this).find('a')[0].href 
    });
    
    // logged in? 
    var $datefield = $('#datepicker');
    var $textfield = $('#edit-date');
    if ($datefield.length){
      $datefield.datepicker({
        'multidate':true,
        'format':'dd-mm-yyyy'
      })
      .on("changeDate", function(event) {
        $textfield.val( $datefield.datepicker('getFormattedDate')); 
      })
      // dates already set 
      var set_dates =  $.trim($textfield.val());
      if (set_dates.length) {
        var date_objects = []; 
        var ddmmyyyy;
        var set_dates_arr = set_dates.split(',');
        for (var i=0; i < set_dates_arr.length; i++){
          ddmmyyyy = set_dates_arr[i].split('-');
          date_objects.push(new Date(ddmmyyyy[2],ddmmyyyy[1]-1,ddmmyyyy[0]))
        }
        $datefield.datepicker('setDates',date_objects);
      }
    
      return {};
    }
     
    // workaround for Drupal #states required-functionality only active client-side 
    $form.submit(function(){
        var omg = [];
        $('.form-required').each(function(){
           var $form_item =  $(this).parents('.form-item');
           var $field = $form_item.find('input, textarea, select').eq(0);
           if($.trim($field.val()).length < 1){
              omg.push('Het veld "' + $form_item.find('label').text() +'" moet nog worden ingevuld.')
           } 
        })
        if (omg.length){
          alert(omg.join("\n\n"))
          return false  
        }
    });
      
      
    showTipsButton()
    return {}; 
  }; 

  pageHandlers.zoekpagina = function(){
    var orderBy = Drupal.settings.muziekladder_search_orderby, 
        $orderBy = $('#orderBy'); 
    if (orderBy && orderBy != 'relevance'){
        $orderBy.val(orderBy); 
    }
    crumbTrail.set(location.href);
    $orderBy.change(function(){
       $('#advanced_search').submit();
    })
    showTipsButton()
    return {};     
  };

  pageHandlers.articlefull = function(){
      showTipsButton()
      externalLinks();
      return {};
  };

  pageHandlers.front = function(){
      var handlers = {
     //   openLink:cityMenuLinkHandleri
      }; 
      externalLinks();
  //        drawFrontNews();
  //        frontSlide(); 
      showTipsButton()

      return handlers;

  };

  pageHandlers.locationPage = function(){
      laad.js('locations');
      externalLinks();
      crumbTrail.set(location.href);
      showTipsButton()

      return {};
  }

  pageHandlers.detail = function(){
    externalLinks();
    showDetailImages();
    showTipsButton()
    shareButton();
    crumbTrail.backButton($('.breadcrumb li a').eq(0));
    return {};
  }

  pageHandlers.locaties = function(){
    laad.js('util');
    laad.wait('locations');
    showTipsButton();
    externalLinks();
     
    return {
      show_venuetips:function(){
        $(this).hide();
        $('.venue_tipformContainer').slideDown();
      }  
    };
  }

  pageHandlers.dagoverzicht = function(){
    if (Drupal.settings.pathPrefix == 'en/'){
      var nexttext = "Next page";
      var prevtext = "Previous page";   
    }else{
      var nexttext = "Volgende pagina";
      var prevtext = "Vorige pagina";   
    }
    var pathprefix = Drupal.settings.basePath+Drupal.settings.pathPrefix;
    var $cga = $('.city_gig_agenda');
    var page = parseInt($cga.data('page'));
    var result_count = parseInt($cga.data('count'));
    var result_per_page = parseInt($cga.data('rpp'));
    var handlers = {}; 
    //page_links 
    var pageless_url = location.href.replace(/\?pagina=[0-9]+/,''); 
    var pagenav = []; 
    if (page > 0){
      pagenav.push( '<a class="btn btn-inverse" href="' + pageless_url + '?pagina=' + (page-1) + '"> &laquo; '+prevtext+'</a>' );
    }

    if (result_count == result_per_page){
        pagenav.push( '<a class="btn btn-inverse" href="' + pageless_url + '?pagina=' + (page+1) + '">'+nexttext+'  &raquo;</a>' );
    } 
    $('.page-nav-container').append(pagenav.join('&nbsp;&nbsp;&nbsp;'));  
    
    //date selecter
    $('.agenda-date-selecter').change(function(e){
      var choice = $(this).val(); 
      var city_spec = location.href.match(/\/[0-9]+-[a-z]+\/*/i);
      city_spec = city_spec ? city_spec[0] : '/';
      if (!city_spec.match(/\/$/)) city_spec += '/';
      location.href = pathprefix+'muziek'+city_spec+'agenda-'+choice+'.html';
    }); 
    
    //city selecter
    $('.agenda_city_selecter').change(
      function(e){
        var choice = '/'+$(this).val();
        var agenda_spec = location.href.match(/agenda\-[0-9]+/); 
        if (choice == "/0") choice =''; 
        if (agenda_spec){
          agenda_spec = '/' + agenda_spec[0] +'.html';
        }else{
          agenda_spec = '/';   
        }
       
        location.href = pathprefix+'muziek' + choice + agenda_spec; 
     });

     // day buttons
     $('.prevnextlinks button').on('click',function(e){
         e.preventDefault();
         try {
            window.location = $(this).find('a')[0].href;
         } catch(err) {
            window.location = $(this).attr('data-href')     
         }
     });
    
     $(window).load(hC.loadAgendaImages);
     eventLinksListener();  
      // day buttons
     $('.prevnextlinks button').on('click',function(e){
         e.preventDefault();
         try {
            window.location = $(this).find('a')[0].href;
         } catch(err) {
            window.location = $(this).attr('data-href')     
         }
     });

     crumbTrail.set(location.href);
     externalLinks();
     showTipsButton()
    
     return handlers;   
  }

  function eventLinksListener(){
    var loading = false; 
    $('.city_gig > a').click(function(e){
      e.preventDefault(); 
      if (loading) return; 
      loading = true; 
      var that = this;
      var $pageContainer = $('<div class="page-gig detail loading"></div>');
      var $gigContainer = $(this).parents('.city_gig'); 
      $gigContainer.append($pageContainer);
      $.get(this.href+'&ajax=1',function(resp){
        $gigContainer.addClass('opened');
        $pageContainer
          .append(resp)
          .removeClass('loading');
        loading = false;
        $pageContainer.find('.eventfull').slideDown();
        showDetailImages($pageContainer); 
      }).fail(function(){
        location.href = that.href; 
        loading = false; 
      });
    });
  }

  function loadAgendaImages(){
    // temp disable:
    var i = 0; 
    $('.city_gig').each(function(){
      var $gig = $(this); 
      var src; 
      var img_code = $.trim($gig.data('imgsrc')); 
             
      if (!img_code || !img_code.length) return; 
      
      if (img_code.match(/^data/)){
        src = img_code;  
      }else{
        src = '/muziekdata/img/?s=1&p='+img_code;
      }
       
      i++;
      if (i>24) return
      var img = new Image;
      img.onload = function(){
        $gig.find('.first-cell').prepend('<div class="image-cell"><img src="'+src+'" /></div>')               
      }
      img.src = src; 
    });  
  };  
  hC.loadAgendaImages = loadAgendaImages; 

  function showTipsButton(){
     var other,other_selector,a,lnk,txt,lng = Drupal.settings.pathPrefix;
     if (lng.match(/nl/)){
       lnk = '/en/';
       txt = 'English';
       other_selector = '.en';
     } else {
        lnk ='/nl/';
        txt = 'Nederlands'; 
        other_selector = '.nl';
     }

     other_selector += ' a.language-link';

     a = $('<a class="tip-button" href="'+lnk+'">'+txt+' &raquo;</a>');
     a.click(function(e){
        other = $(other_selector);
        if (other.length){
          e.preventDefault(); 
          location = other[0].href;
        }
     });
     $('#page-title, h1').before(a);
  }    

  var crumbTrail = (function(){ 
      var cname = 'werwasib4'; 
      return {
          set:function(url){
              $.cookie(cname,url,{ expires: 7, path: '/' });
          },
          backButton:function($element){
              var backuri = $.cookie(cname);    
              if (backuri){
                  $element.click(function(e){
                      e.preventDefault(); 
                      location.href = backuri;
                  })
              }
          }
      }; 
  }());
  hC.crumbTrail = crumbTrail;

    function frontSlide(){
      var $article = $('#content > article')
      var i = $article.length-1;
        $article.each(function(index){
          var $this = $(this); 
          if (!$this.hasClass('processed')){
             var $header = $this.find('header').eq(0); 
             $header.find('h2').append('<button class="btn btn-inverse"><i class="icon-white icon-chevron-right"></i></button>')
             $this.addClass('processed')
          //      .append($this.find('img').eq(0))
                .click(function(e){
                  e.preventDefault()
                  location.href = $('#content article.current').find('.links a')[0]['href'] ; 
                });
          }
          if($this.hasClass('current')){
             i=index;
          }  
      });
      i+=1;
      if (i>=$article.length) i=0;
      $article.removeClass('current')
        .eq(i).addClass('current');
      setTimeout(frontSlide,6000); 
    }
/*
    function drawFrontNews(){
       $.get('/nieuws/ajax',function(resp){
          $('.after_content').html('<h3>Overig nieuws</h3>'+resp).fadeIn('slow')
          newsLinks();
          externalLinks();

       });  
    }
    function newsLinks(){
        $('.joriso-news-item h3 a').click(function(e){
            e.preventDefault();
            var $that = $(this);
            var $p = $that.parents('.joriso-news-item').find('p'); 
            if ($that.hasClass('visible')){
                $p.hide();
                $that.removeClass('visible');
            }else{
                $p.slideDown('fast',function(){
                    $that.addClass('visible');
                });
            }     
        }); 
    } 
/*        
    /**
     * City Menu functions.
     * The citybuttons on top of the index & dagoverzicht pages
     **/
/*    var cityPresets = {     //groupings of cities based on proximity
        '1':[2,1,3], //Amsterdam
        '8':[8,5,1388962814,1389535452],    //Rotterdam
        '5':[8,5,100], // Den Haag
        '4':[12,4,110,1389309298], //Utrecht

        '6':[6,14], //Groningen
        '7':[7,1404591646,15], //Eindhoven
        '100':[5,100] // Leiden
    }; 

    function selectCity (cityno,callback){
        if (typeof cityPresets[cityno] !== 'undefined'){
            setSessionCity(cityPresets[cityno].join(','),callback);
        } else {
            setSessionCity(cityno,callback);
        }
    }

    //handles click events on the citybuttons:
    function cityMenuLinkHandler(e) {
        e.preventDefault();
        e.stopPropagation();
        var lnk = typeof this.href === 'undefined' ? $(this).find('a')[0] : this;
        selectCity($(lnk).data('cityno'),function() {    
            if (!$('body').hasClass('dagoverzicht')) {
                location.href = lnk.href;
            } else {
                location.reload();
            }
        });     
    }

    function setSessionCity(val,callback){
        var cb = callback || function(){}
        $.post('/muziek/setcity/',{city:val},cb);
    }
    
    function setAlleStedenSelected(){
        $('.citymenu').each(function(){
            $(this).find('.span2').first().addClass('selected');
        }); 
    }

    function markCitymenu(){ // mark the selected menu item
        if (typeof hC.sessionLocations == 'undefined'){
            setAlleStedenSelected();    
            return; 
        }
        $('.citymenu').find('a').each(function(){
            var cityno = $(this).data('cityno'); 
            if (cityno && $.inArray(""+cityno,hC.sessionLocations) != -1){
                if (typeof cityPresets[cityno] != 'undefined'
                    && cityPresets[cityno].length === hC.sessionLocations.length
                    && cityPresets[cityno].sort().join('_') === hC.sessionLocations.sort().join('_')){//match
                    
                    $(this).parent().addClass('selected');                      
                } else if(hC.sessionLocations.length === 1){
                    $(this).parent().addClass('selected'); 
                }
            }
        });
    }
    // EOF Citymenu functions

    var cache = false;
    var getCurrentDateUrl = function(){
        return '/muziekdata/'+hC.date.year+'/'+hC.date.day+'-'+hC.date.month+'.json';               
    }       
   function getData(){
        var url = getCurrentDateUrl();
        // return either the cached value or an
        // jqXHR object (which contains a promise)
        return cache || $.ajax(url, {
            dataType: 'json',
            success: function( resp ){
                cache = resp;
            }
        });
    }
*/
   var a = new RegExp('/' + window.location.host + '/');
   var externalLinks = function(){
        $('a[href]').each(function() {
           if(!a.test(this.href)) $(this).attr('target','_blank');
        });     
   }

  var showDetailImages = function($container){
    if (typeof $container == 'undefined') $container = $('body'); 
    var $ev = $container.find('.eventfull');
    if ($ev.length){
      var href = $.trim($ev.eq(0).data('imgsrc'));
      if (href && href.length){
        var img = new Image();
        var imgcreate = function(){
          var lnk = $container.find('.eventlink').eq(0).attr('href');
          var a = document.createElement('a');
          a.href = lnk; 
          a.className="eventImg";
          a.target="_blank";
          a.appendChild(img); 
          $ev.prepend(a);
        }

        if (href.match(/^data/)){
          img.src = href; 
          imgcreate();
        } else {
          img.onload = imgcreate; 
          img.src = '/muziekdata/img?p='+href; 
        }
      }
    }
  };
    
  var shareButton = function(){
      $('.location').before('<div style="margin:20px 0px" class="addthis_native_toolbox"></div>')
      laad.js('addthis',function(){});
  };
  
  var backtotopButtons = function(){
      $('.backtotop').on('click',function(e){
          e.stopPropagation();
          window.scrollTo(0,0);
      })
  };
}(jQuery));
