var hC = Drupal.settings.muziekladder;
 
(function($){
    hC.jsDir = '/js/';
    hC.mapsKey = 'AIzaSyDEVR7pVblikD8NSlawdwv8nFnOxzx8PBo';
    hC.pathToTheme = Drupal.settings.basePath + "sites/all/themes/" + Drupal.settings.ajaxPageState.theme;
    var pageHandlers = {};
    laad.pathConfig = {
        "bootstrap"     :hC.muziekladderBasePath + hC.jsDir+"bootstrap.min.js",
        "util"          :hC.muziekladderBasePath + hC.jsDir+"util.js",
        "locationpage"  :hC.muziekladderBasePath + hC.jsDir+"locationpage.js",
        "locations"     :hC.muziekladderBasePath + hC.jsDir+"locations.js",
        "maps"          :'//maps.googleapis.com/maps/api/js?key='+hC.mapsKey+'&sensor=false&callback=hC.mapInitialize',
        "addthis"       :'//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-542e60be78f12e17'
    }
    
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
                if (typeof pageHandlers[bodyClass[i]] === 'function')
                    handlers = pageHandlers[bodyClass[i]]();
            }
        });
   });

   pageHandlers.muziekformulier = function(){
        function setFormClass(){
            var $form = $('#mod-muziekladder-mailtipform'); 
            var $this = $(this); 

            if($this.val().length){
                $form[0].className = $(this).val() + ' active';
            } else {             
                $form[0].className = '';
            }
        }
        
        setFormClass.apply( $('#edit-soort')[0]); 
        $('#edit-soort').change(setFormClass);       
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

        return {};     
    };

    pageHandlers.articlefull = function(){
        externalLinks();
        return {};
    };
    
    pageHandlers.front = function(){
        var handlers = {openLink:cityMenuLinkHandler}; 
        externalLinks();
//        drawFrontNews();
        frontSlide(); 
        $('#block-mod-muziekladder-muziekladder-nieuws-block-1')
          .prepend('<div class="event clearfix"><h4 class="title"><a href="/muziekformulier">Tips?</a></h4>'+
          '<p><a href="/muziekformulier"><em>Interessant muzikaal optreden, feest of evenement dat nog ontbreekt op de Muziekladder agenda?</em>'+
          '<br><strong>Laat het ons weten aub!</strong></a></p></div>');
       
        return handlers;
    };
    
    pageHandlers.locationPage = function(){
        laad.js('locationpage');
        externalLinks();
        crumbTrail.set(location.href);
        return {};
    }
    
    pageHandlers.detail = function(){
        externalLinks();
        showDetailImages();
        shareButton();
        crumbTrail.backButton($('.breadcrumb li a').eq(0));

        return {};
    }
    
    pageHandlers.locaties = function(){
        laad.js('util');
        laad.wait('locations');
        showTipsButton();
        externalLinks();
        return {};
    }

    pageHandlers.dagoverzicht = function(){
        markCitymenu();
        var handlers = {
            'openLink': cityMenuLinkHandler,
            'resetcity':function(){
                hC.cityselect.reset();
            },
            'openLocationInfo':function() {
                var $this = $(this); 
                var $locationinf = $this.find('i')
                        .toggleClass('icon-plus-sign')
                        .toggleClass('icon-minus-sign')
                        .parents('.locationUnit')
                        .find('.locationInfo'); 
                if (!$locationinf.length) {
                    $locationinf = $('<div class="locationInfo"><img src="'+
                        hC.pathToTheme + '/img/ajax-loader.gif"></div>');
                    $.get('/locaties/info',{
                        "l":$this.parents('.locationUnit').find('.locationName')[0].id }
                        ,function(resp){
                            $locationinf.html('<div class="nodisplay">'+resp+'</div>');
                            $locationinf.find('.nodisplay').show();
                    });
                    $this.after($locationinf); 
                    $locationinf.show();
                } else {

                }
            }
        };
        $('.prevnextlinks button').on('click',function(e){
            e.preventDefault();
            try {
              window.location = $(this).find('a')[0].href;
            } catch(err) {
              window.location = $(this).attr('data-href')     
            }
        });
    
        hC.cityselect = cityselect();
        backtotopButtons();
        dateSelecter();
         
        $('#messengerContainer')[0].href='m'+'a'+'ilto'+':'+'info'+'@'+'hardcode'+'.'+'nl';
        
        showImages();
        $('body').addClass('doneloading');
        $('.locationUnit').css({'opacity':1});
        
        crumbTrail.set(location.href);
        externalLinks();
        showTipsButton()
        $('.locationEvents .title').hover(function(){
        }) 
        return handlers;
    }

    function showTipsButton(){
       $('#page-title, h1').before('<a class="tip-button" href="/muziekformulier">Tips?</a>');
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
      
    function frontSlide(){
      var $article = $('#content > article')
      var i = $article.length-1;
      $article.each(function(index){
          var $this = $(this); 
          if (!$this.hasClass('processed')){
             var $header = $this.find('header').eq(0); 
             $header.find('h2').append('<button class="btn btn-inverse"><i class="icon-white icon-chevron-right"></i></button>')
             $this.addClass('processed')
                .append($this.find('img').eq(0))
                .append($header)
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
        
    /**
     * City Menu functions.
     * The citybuttons on top of the index & dagoverzicht pages
     **/
    var cityPresets = {     //groupings of cities based on proximity
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
    var a = new RegExp('/' + window.location.host + '/');
    var externalLinks = function(){
        $('a[href]').each(function() {
           if(!a.test(this.href)) $(this).attr('target','_blank');
        });     
    }

    var showDetailImages = function(){
        var $ev = $('.eventfull');
        if ($ev.length){
            var href = $ev.eq(0).data('imgsrc');
            if (href && href.length){
                href = decodeURIComponent(href);
                var img = new Image();
                img.onload = function(){
                    var lnk = $('.eventlink').eq(0).attr('href');
                    var a = document.createElement('a');
                    a.href = lnk; 
                    a.className="eventImg";
                    a.target="_blank";
                    a.appendChild(img); 
                    $ev.prepend(a);
                }
                img.src = href; 
            }
        }
    };
    
    var showImages = function(){    
        $.when(getData()).then(function(resp){
            $('.locationUnit')
                .not('.nodisplay')
                .find('.event[data-imgsrc]')
                .not('.imag').each(
                function(){                 
                    var $this =  $(this),
                        imgsrc = $this.data('imgsrc'),
                        $h4 = $this.find('h4')
                        href = $h4.find('a')[0].href; 
                    if (typeof resp[imgsrc] !== 'undefined'){   
                        $h4.before('<a href="'+href+'" class="eventImg"><img src="'+resp[imgsrc]+'"></a>');
                    }
                    $this.addClass('imag');
                }
            )
        });
    };
    
    var shareButton = function(){
        $('.location').before('<div style="margin:20px 0px" class="addthis_native_toolbox"></div>')
 
        laad.js('addthis',function(){});
    };
  
    var dateSelecter = function(){
        var monthNames = [ "", "januari", "februari", "maart", "april", "mei", "juni",
            "juli", "augustus", "september", "october", "november", "december"],
            monthAbbrs = [ "", "jan", "feb", "mrt", "apr", "mei", "jun",
            "jul", "aug", "sep", "oct", "nov", "dec"],
            dayNames = ["zondag","maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag"];

        var $cnt = $('.dateselectContainer');
        
        var i,$ms = $cnt.find('.monthselect'),
            $ds = $cnt.find('.dayselect'),
            years ={},
            d = new Date();
            d.setHours(d.getHours()-4);
            
        var today = d.getDate(),
            pagedate = new Date(hC.date.year,(hC.date.month-1),parseFloat(hC.date.day)),
            curmonth = d.getMonth(),
            thisyear = d.getFullYear(),  //this year
            this1basedMonth = zerofix(curmonth +1),
            next1basedMonth = zerofix(parseFloat(this1basedMonth) +1 <= 12 ? parseFloat(this1basedMonth) +1 : 1),
            second1basedMonth = zerofix(parseFloat(next1basedMonth) + 1 <= 12 ? parseFloat(next1basedMonth) + 1 : 1),
            mnthhtml=[],
            mnth,
            mnths = [this1basedMonth,next1basedMonth,second1basedMonth];
            years[this1basedMonth] = thisyear
            years[next1basedMonth] = next1basedMonth === '01' ? thisyear + 1 : thisyear;
            years[second1basedMonth] = next1basedMonth === '01' || second1basedMonth === '01' ? thisyear + 1 : thisyear;
        
        if(pagedate < d && (pagedate.getFullYear() < thisyear || pagedate.getMonth() < curmonth )){         
            $cnt.remove()
            return; 
        }
    
        //month select:                 
        for (i = 0; i <  mnths.length; i++){
            var selected = hC.date.month === mnths[i] ? 'selected' : ''
            mnthhtml.push('<option value = "'+mnths[i]+'" '+selected+'>'
                +monthNames[parseFloat(mnths[i])]+'</option>');
        }   
        
        $ms.append(mnthhtml.join(''))
            .on('change',function(){
                var s = $(this).val();
                if (this1basedMonth === s){
                    location.href = '/muziek/';
                    return;
                }                
                location.href = '/muziek/agenda-'+getDayOffset(years[s],s,1)+'.html'
        });
        
        var getDayOffset = function(year,onebasedmonth,day){
            var offset = 0; 
            var monthdiff = onebasedmonth - this1basedMonth;
            if (!monthdiff){
                return day - today; 
            }else if ( monthdiff == -11 || monthdiff == 1){ 
                return daysInMonth(parseFloat(this1basedMonth),year) - today + parseFloat(day);
            }else{
                return daysInMonth(parseFloat(this1basedMonth)+1,years[next1basedMonth]) + 
                    daysInMonth(parseFloat(this1basedMonth),year) - today + parseFloat(day);
            }
        }
        
        var adjustDays = function(mnth){
            var dy,
                selected,
                no_of_days = daysInMonth(mnth,(years[mnth])),
                dayhtml = [],
                daynameIndex = new Date((years[mnth]-2000), parseFloat(mnth)-1,0).getDay(),
                incrementday = function(){
                    daynameIndex+=1;
                    if (daynameIndex === 7) daynameIndex = 0;
                },
                mnthabbr = monthAbbrs[parseFloat(hC.date.month)].substring(0,3);
                
            for (i=0; i<no_of_days; i++){
                if (mnth === this1basedMonth && i < today-1){
                    incrementday();
                    continue;
                }
                dy = zerofix(i+1);
                selected = hC.date.day === dy ? ' selected ' : '';
                
                dayhtml.push('<option value = "' + dy + '" '+selected+'>' +dy+' - '+mnthabbr+' - '+dayNames[daynameIndex] +'</option>');
                incrementday()
            }

            $ds.html(dayhtml.join(''))
                .on('change',function(){ //dayselecter
                    location.href = '/muziek/agenda-' +
                        getDayOffset(years[hC.date.month],hC.date.month,$(this).val()) +
                         '.html';
            });
        }
                
        var daysInMonth = function(onebasedmonth, year) {
            return new Date(year, onebasedmonth, 0).getDate();
        }           
        
        function zerofix (vl){
            var tmp  = '0'+vl;
            return tmp.substring(tmp.length-2)
        }
        adjustDays(hC.date.month);
        $('.controls').css('visibility','visible');
        
        if ($('.dayselect option').length){     
            $('.dateselectContainer').css('display','block');
        }
        var $sf = $('.prevnextlinks button')
        if( location.href.match('agenda-0') || location.href.match('\/muziek\/$')){
            $sf.filter('.nextday').fadeIn('slow')
        }else{
            $sf.fadeIn('slow')
        }
        
    };
    
    var backtotopButtons = function(){
        $('.backtotop').on('click',function(e){
            e.stopPropagation();
            window.scrollTo(0,0);
        })
    };
    
    var cityselect = function(){
        var ct = typeof hC.sessionLocations !== 'undefined' ? hC.sessionLocations : 0,
            $checkboxes = $('.city-select input'),
            $checkboxLabels = $('.city-select label'),
            $selectallbox = $('.city-select input[value="0"]'),
            checked = [];
        
        if (ct){
            setCity(ct,1);
        }else{
            $selectallbox.attr('checked',true )
        }
        
        $checkboxes.on('change',function(){
            $('.citymenu .span2' ).removeClass('selected')
            
            if (this.value == 0){//alle steden
                setAlleStedenSelected();
                $checkboxLabels.removeClass('checked'); 
                toggleAll(false)
                this.checked = true;
                setCity([]);
                showImages();
             }else{
                $selectallbox.attr('checked',false);                
                setCity(getchecked());
                showImages();
            }
        });
        
        function setParentClass($checkbox){
            //set the label class
            $checkbox.parent().addClass('checked');
        }

        function toggleAll(val){
            $checkboxes.each(function(){
                $(this).attr("checked",val);
            })
        }
        
        function getchecked(){
            var checked = [];
            $checkboxLabels.removeClass('checked'); 
            $checkboxes.filter(":checked").each(function (){
                var $this = $(this);
                checked.push($this.val());  
                setParentClass($this);
            });
            return checked;
        }

        function showFilterLabels(selectedval){
            var $filterrow = $('.filter-row'); 
 
            if (selectedval) {
             if (!$filterrow.length){
                $filterrow= $('<div class="filter-row" />');
                $('.controls').eq(0).after($filterrow);
              }
              var htmlcntnt = ['<a data-cityno="0" class="label label-important cityfilterlabel">&times; Actieve filters:</a>'];
              
              $.each(selectedval,function(index, value){
                  if (typeof muziek_cities[value] !== 'undefined'){
                    htmlcntnt.push( '<a data-cityno="'+value+'" class="label label-inverse cityfilterlabel"> &times; '+muziek_cities[value].name+'</a>' ); 
                  }
              });

              $filterrow.html(htmlcntnt.join('')).find('.cityfilterlabel').click(function(){
                 var cityno = $(this).attr('data-cityno');
                 var box = $checkboxes.filter("[value='"+cityno+"']");
                 box.trigger('click');
                 $(this).hide();
              });
            } else { 
              $filterrow.remove(); 
            }
        }
       
        function setCity(selectedval,setCheckboxes) {
            if (!hC.isArray(selectedval)){
                throw('setCity first argument must be an array');
            }

            if (selectedval.length){
                if (setCheckboxes){
                    // this should only be necessary on pageload - to reveal the stored options :
                    $.each(selectedval,function(index, value){
                        var box = $checkboxes.filter("[value='"+value+"']");
                        box.attr('checked',true);
                        setParentClass(box);
                    });

                    hideandshow(selectedval);
                    $('#hideLocations').remove();
               }else{
                    hideandshow(selectedval);
               }
               showFilterLabels(selectedval)
                
               setSessionCity(selectedval.join(','));
                
            } else {
                hideandshow(0);
                setSessionCity(0);
                showFilterLabels(0);
                $checkboxes[0].checked = true; 
            }   
        }
        
        function hideandshow(chosencities){
            var $l = $('.locationmenu');
            
            var $s = $l.find('.locationmenuitem');
            
            var $locationUnit = $('.locationUnit'); 
            $s = $s.add($locationUnit);
            
            if( chosencities ) {
                // hide all venues, -> show only chosen cities venues
                $s.addClass('nodisplay');
                for (var i = 0; i <chosencities.length; i++){
                    $('.locationmenuitem'+chosencities[i]+', .locationUnit'+chosencities[i])
                        .removeClass('nodisplay');
                }
                
            }else{
                $s.removeClass('nodisplay');
            }   
            //check if anything is visble & if not excuse ourselves
            if ($locationUnit.length === $locationUnit.filter('.nodisplay').length){
                $('.noresults').fadeIn('fast')
                
            }else{
                $('.noresults').hide()
            }
        }
        
        var pub = {
            reset:function(){
                //localStorage.removeItem('cities');
                setSessionCity(0,function(){
                    location.reload();
                });
            }
        };
        
        return pub;
    };      
}(jQuery));
