var hC = Drupal.settings.muziekladder;
(function($){
    hC.jsDir = '/js/'; 
    hC.pathToTheme = Drupal.settings.basePath + "sites/all/themes/" + Drupal.settings.ajaxPageState.theme;
    var pageHandlers = {};
    laad.pathConfig = {
        "bootstrap"     :hC.muziekladderBasePath + hC.jsDir+"bootstrap.min.js",
        "util"          :hC.muziekladderBasePath + hC.jsDir+"util.js",
        "locationpage"  :hC.muziekladderBasePath + hC.jsDir+"locationpage.js",
        "locations"     :hC.muziekladderBasePath + hC.jsDir+"locations.js",
        "maps"          :'//maps.googleapis.com/maps/api/js?key=AIzaSyDEVR7pVblikD8NSlawdwv8nFnOxzx8PBo&sensor=false&callback=hC.mapInitialize',
        "addthis"       :'//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50d57cf9178d8bc1'
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
            selectmenu();
            var i,bodyClass =  document.body.className.split(/\s+/),
                lngth = bodyClass.length; 
            
            for (i = 0; i < lngth; i++){
                if (typeof pageHandlers[bodyClass[i]] === 'function')
                    handlers = pageHandlers[bodyClass[i]]();
            }
        });
    });
    
    var selectmenu = function(){
        $('.navi').eq({
            'index':0,
            'articlefull':0,
            'locationPage':2,
            'detail' : 1,
            'dagoverzicht' : 1,
            'locaties' : 2}[document.body.className])
        .addClass('selected');
    };

    pageHandlers.articlefull = function(){
        externalLinks();
        return {};
    };
    
    pageHandlers.front = function(){
        var handlers = {openLink:cityMenuLinkHandler}; 
        externalLinks();
        return handlers;
    };
    
    pageHandlers.locationPage = function(){
        laad.js('locationpage');
        externalLinks();
        return {};
    }
    
    pageHandlers.detail = function(){
        externalLinks();
        showDetailImages();
        shareButton();
        return {};
    }
    
    pageHandlers.locaties = function(){
        laad.js('util');
        laad.wait('locations');
        laad.wait('maps');
        
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
            window.location = $(this).find('a')[0].href;
        });
    
        hC.cityselect = cityselect();
        backtotopButtons();
        dateSelecter();
        
        $('#messengerContainer')[0].href='m'+'a'+'ilto'+':'+'info'+'@'+'hardcode'+'.'+'nl';
        
        if (screen.width > 767){
            shareButtons();
        }
        
        showImages();
        externalLinks();
        $('body').addClass('doneloading');
        $('.locationUnit').css({'opacity':1});
        
        return handlers;
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
        return hC.muziekDataJson+'/'+hC.date.year+'/'+hC.date.day+'-'+hC.date.month+'.json';               
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
        laad.js('addthis',function(){
            $('.eventfull .location').before('<a class="addthisicon"></a>')
            $('.addthisicon').each(function(){
                try {
                    var $this = $(this),
                        venue = $('location h3').eq(0).text(),
                        ur = location.href,
                        ttl = $.trim($('h1').text()) + ' - ' + $.trim(venue);  
                    addthis.button(this, {}, {url: ur,title:ttl,description:ttl });
                }catch(err){}
            })

        });
    };
    
    var shareButtons = function(){
        laad.js('addthis',function(){
            $('<a class="addthisicon"></a>')
                .prependTo('.event');
                
            $('.addthisicon')
                .each(function(){
                    try{
                        var $this = $(this),
                            $parent = $this.parents('.locationUnit'),
                            $vhead = $parent.find('h2'),    
                            venue = $vhead.text(),
                            ur = $this.parents('.event').find('h4 a')[0].href
                            event = $this.parents('.event').find('h4').text().replace(/[\s\t\n]+/g,' '),
                            ttl = event+ ' - ' + venue;  
                        
                        addthis.button(this, {}, {url: ur,title:ttl,description:ttl });
                    }catch(err){}
                })
        })
        
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
            d = new Date(),
            today = d.getDate(),
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
            }else if (monthdiff == 1){ 
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
        
        function setCity(selectedval,setCheckboxes){
            if (!hC.isArray(selectedval)){
                throw('setCity first argument must be an array');
            }

            if (selectedval.length){
                if (setCheckboxes){
                    //this should only be necessary on pageload - to reveal the stored options :
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
                
                setSessionCity(selectedval.join(','));
                //localStorage.setItem('cities',selectedval.join(','));
                
            } else {
                hideandshow(0);
                setSessionCity(0);
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
