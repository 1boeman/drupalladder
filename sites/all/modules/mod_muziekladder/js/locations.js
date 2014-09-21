(function($){
  // these are set when a city is selected:
  var current_country = 0;
  var current_city_title = '';  
  var selectCity = function(title,countryno){
    if (typeof countryno == 'undefined' ){
      var c = location.hash.split('?').pop();
      console.log(c)   
      $('ul.steden > li > a').each(function(){
        if (this.href.indexOf(c) != -1){
           current_country = $(this).data('countryno');
           current_city_title = this.title; 
           return false;     
        }
      });
     
    } else { 
      current_city_title = title;
      current_country = countryno;  
    }
  };

  var removeCruft = function( ){
      $('.city-container, .club-container').remove();
  }

  var getCountry = function(cityno){
    var country = ''; 
    $('ul.steden > li > a').each(function(){
      if($(this).data('cityno') == cityno){
          var $this= $(this); 
          country = Drupal.settings.city_names['nl'][$this.data('countryno')]; 
           return false;     
      }
    });
    return country;
  };
  var drawCanvas = function(){
    // draw the initial canvas
    if (!$('#map-canvas').length){
       $('<div id="map-canvas" />')
        .css({'width':'100%',height:'1200px'})
        .appendTo('.map-placeholder');
    }
  }

  // listeners for steden links, venue links
  var loadHashLocation = function() {
     var href = window.location.hash.replace(/^#/,'')
     // hash contains either uitgaan or locaties or doesn't exist
     removeCruft();  
     if (href.length){
       if (href.match(/\/uitgaan\//)) {
         // stad overzicht clubs
         var city = href.split(/\?c=/)[1]
         $.get(href+'&ajax=1',function(resp){
            $('ul.steden').hide(); 
            $('.stedencontainer')
              .append('<div class="city-container" data-href="'+href+'">'+resp+'</div>')
              .find('.locatiebeschrijving a').click(function(e){
                  e.preventDefault();
                  window.location.hash = this.href;
              });
              
         });
         if (!current_city_title.length) selectCity(); 
         drawCanvas(); 
         $.get(Drupal.settings.basePath+'uitgaan/getgeo/?l='+current_city_title+'&c='+current_country,function(resp){
            hC.city_mapInitialize(resp);     
         },'json');
       
       } else if(href.match(/\/locaties\//)){
          // club overzicht optredens
          $.get(href+'&ajax=1',function(resp) {
            $('ul.steden').hide(); 
            $('.citycontainer').hide();
            var $container = $('<div class="club-container" />')
            $('.stedencontainer').append($container);
            $container.html(resp); 
            var mapContainer = $('.map-placeholder');
            var straatnaam = $.trim($container.find('.straatnaam').text()); 
            var straatnummer = $.trim($container.find('.straatnummer').text());
            var locatie_naam = $.trim($container.find('.location-title').text()); 
            var stad = $.trim($container.find('.city').text());
            var country = getCountry($container.find('.city').data('cityno')); 
            var iframe_src ='https://www.google.com/maps/embed/v1/place?key='+hC.mapsKey+'&q='+encodeURIComponent(straatnaam+ ' '+straatnummer +' ' + stad + ' ' + country  );
            var iframe_height = '800';   
            var iframe_width = mapContainer.width(); 
            mapContainer.html('<iframe frameborder="0" style="border:0"'+
                                'width="'+iframe_width+'" height="'+iframe_height+'"' +
                                  'src="'+iframe_src+'"></iframe>');
          }); 
       }
     }else{
        // landelijk overzicht
        country_overview();   
     }
  }

 
  /**
   * Draw the country map overview with all cities
   */
  var country_overview = function(){
    var $steden = $('.steden li, .steden li > a');
    $steden.click(function(e){
      e.preventDefault(); 
      e.stopPropagation(); 
      if (this.tagName == 'a'|| this.tagName == 'A'){
        $a = $(this); 
      }else{
        $a = $(this).find('a'); 
      }

      selectCity($a[0].title,$a.data('countryno'));
      window.location.hash = $a[0].href;
    });
    
    drawCanvas();  
      hC.missingCities = function(missingCityArray,cityCountries,map){
      if (!missingCityArray.length) return;   
      var aQueue = [];
      var processing = false;    
      var ajaxQueue = function(urlString,varsObject){
        aQueue.push({"u":urlString});
      
        if (processing) return; 
        ajaxQueueProcess();
      }
       
      var ajaxQueueProcess = function(){
        if (aQueue.length){
          processing = true; 
          var currentRequest = aQueue.shift();
          $.get(currentRequest['u'],function(resp){
            hC.cityMarker(resp,map);
            ajaxQueueProcess();
          },'json');
        }else{
          processing = false;
        } 
      }
      
      for (var i = 0; i < missingCityArray.length; i++){
        ajaxQueue('/uitgaan/getgeo/?l='+missingCityArray[i]+'&c='+cityCountries[missingCityArray[i]]);
      }
    }
    hC.markerListener = function(e){
      location.href = $('.steden li > a[title="'+this.title+'"]')[0].href;
    }

    hC.cityMarker = function(citydata,map){
      if (!citydata || !citydata.results) return;
      var locdat = citydata.results[0].geometry.location;
      var marker = new google.maps.Marker({
        position: new google.maps.LatLng(locdat.lat,locdat.lng),
        title:citydata.results[0].address_components[0].long_name
      });
      google.maps.event.addListener(marker, 'click', hC.markerListener);
      marker.setMap(map);   
    }
   
    var mapOptions = {
      zoom: 8,
      center: new google.maps.LatLng(51.6704,5.2589),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      panControl: true,
      zoomControl: false,
      mapTypeControl: false,
      scaleControl: true,
      streetViewControl: false,
      overviewMapControl: false,
      draggable: true, 
      scrollwheel: false, disableDoubleClickZoom: false 
    }
    var cityObj = {}; 
    var cityCountries = [];
    var missingCityArray = [];  
    var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
    var cityAlias = {'The Hague':'Den Haag',"'s-Hertogenbosch":'Den Bosch'}
    
    $steden.each(function(){
      cityObj[this.title] = 0;
      cityCountries[this.title] = $(this).data('countryno');
    });
    if (typeof hC.location_data === 'undefined') {
      // no locations cached yet
      missingCityArray = hC.objectKeys(cityObj);
    }else{
      // draw markers for all cached locations
      for (var i = 0; i < hC.location_data.length; i++){
        hC.cityMarker(hC.location_data[i],map);
            cityObj[hC.location_data[i].results[0].address_components[0].long_name] = 1;
      }
      // register all cities missing in map
      for (var name in cityObj){
        if (!cityObj[name]) missingCityArray.push(name)
      }
    }
    hC.missingCities(missingCityArray,cityCountries,map);
  };     
 /**
  * mapIinitialize is called by the maps api after it has loaded
  */ 
  hC.mapInitialize = function() {
    $(window).on('hashchange',loadHashLocation) ;
    loadHashLocation(); 
  }
  // citymap  
  hC.city_mapInitialize = function(data) {
    var lat = data.results[0].geometry.location.lat;
    var lng = data.results[0].geometry.location.lng;
    var mapOptions = {
      zoom: 13,
      center: new google.maps.LatLng(lat,lng),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      panControl: true,
      zoomControl: false,
      mapTypeControl: false,
      scaleControl: true,
      streetViewControl: false,
      overviewMapControl: false,
      draggable: true, 
      scrollwheel: false, 
      disableDoubleClickZoom: false 
    }
    var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
  }
 }(jQuery)); 
