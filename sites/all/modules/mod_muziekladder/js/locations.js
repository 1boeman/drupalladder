(function($){
  laad.wait('maps');
  /* ajax breadcrumbs */
  var trail = function(modus){
    var html = '';
    var crumb =['<li><a href="/uitgaan/">Locaties</a>']; 
    switch(modus){
      case 'uitgaan':
        crumb.push ('<li><span>'+current_city_title+'</span>');
      break;
      case 'locaties':
        if (!current_city_title.length) selectCity('locaties'); 
        crumb.push ('<li><a href="'+current_city_link+'">'+current_city_title+'</a>');
        crumb.push ('<li><span>'+$('.location-title').text()+'</span></li>');
      break;

      default:
    }; 

    var $container = $('#content > p').eq(0);
    $container.html('<ul class="breadcrumb">'+crumb.join(' <span class="divider"><i class="icon-chevron-right"></i> </span> </li>')+'</ul>');
    $container.find('a').click(function(e){
      // only reload the page if we are going back to /uitgaan/
      if (!this.href.match(/\/uitgaan\/$/)){
        e.preventDefault();
        window.location.hash = this.href;
      }
    });
  };
  
  var getVenueInfo = function(locatiebeschrijving){
    var rv = {}
    var locatie = $(locatiebeschrijving);
        rv.straatnaam = $.trim(locatie.find('.straat').text());
        rv.nummer = $.trim(locatie.find('.straatnummer').text());
        rv.stad = locatie.find('.stad').text();
        rv.venue = locatie.find('.locatie-link').text();
    return rv;  
  }
  // marker for venue on citymap
  hC.venueMarker = function(data,map,element){ 
      if (!data || !data.results || !data.results.length) return;
      var location_title = $(element).find('.locatie-link').text(); 
      var locdat = data.results[0].geometry.location;
      var marker = new google.maps.Marker({
        position: new google.maps.LatLng(locdat.lat,locdat.lng),
        title:location_title +', '+ data.results[0].formatted_address,
        animation: google.maps.Animation.DROP

      });
      google.maps.event.addListener(marker, 'click', function(e){
        
        hC.venue_markerListener.apply(this,[e,element]) 
      });
      marker.setMap(map);   
  };
  hC.city_markerListener = function(e){
      $('.steden li > a[title="'+this.title+'"]').trigger('click');
  }
  var infoWindow;
  hC.venue_markerListener = function(e,list_element){
    var info = getVenueInfo(list_element);
    if (infoWindow) infoWindow.close();
    var content = $('<div class="maps_infowindowcontent">'+list_element.innerHTML+'</div>');
    infoWindow = new google.maps.InfoWindow({
       "content" : content[0],
    }); 
    
    infoWindow.open(this.map,this)
    content.find('a').click(function(e){
        e.preventDefault();
        window.location.hash = this.href;
  
    })
  
  }


  // citymap  (containing venue markers)
  hC.city_mapInitialize = function(data) {
    var lat = data.results[0].geometry.location.lat;
    var lng = data.results[0].geometry.location.lng;
    var mapOptions = {
      zoom: 13,
      center: new google.maps.LatLng(lat,lng),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      panControl: true,
      draggable: true, 
      scrollwheel: false, 
    }
    var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
    return map;
  };

  // simple ajaxQueue for retrieving geolocation data / drawing map markers
  var aQueue = [];
  var processing = false;
    var ajaxQueue = function(urlString,map,cityOrVenue,element) {
    var element = element || false;
    var cityOrVenue;
    cityOrVenue = cityOrVenue || 'city';
    aQueue.push({ "u" : urlString, "map": map, "element":element,"cityOrVenue":cityOrVenue });
    
    if (processing) return; 
    ajaxQueueProcess();
  }
   
  var ajaxQueueProcess = function(){
    if (aQueue.length){
      processing = true; 
      (function(){
        var currentRequest = aQueue.shift();
        $.get(currentRequest['u'],function(resp){
            if (currentRequest["cityOrVenue"] == 'city'){
              hC.cityMarker(resp,currentRequest["map"]);
            }else{
              hC.venueMarker(resp,currentRequest["map"],currentRequest["element"]);  
            }
            ajaxQueueProcess();
         },'json');
      }())
    }else{
      processing = false;
    } 
  }
    
  // these 3 must be set when a city is selected:
  var current_country = 0;
  var current_city_title = ''; 
  var current_city_link =''; 

  var selectCity = function(modus,element){
    if (modus == 'locaties'){
      var cityno= $('.club-container span.city').data('cityno')
      var city = $("ul.steden a[data-cityno ='"+cityno+"']"); 
      current_city_title = city[0].title;
      current_country = city.data('cityno');
      current_city_link = city[0].href;
    } else if (modus =='uitgaan' ){
      var c = location.hash.split('?').pop();
      $('ul.steden > li > a').each(function(){
        if (this.href.indexOf(c) != -1){
           current_country = $(this).data('countryno');
           current_city_title = this.title;
           current_city_link = this.href;
           return false; 
        }
      });
    } else if( modus == 'overview'){ 
      current_city_title = element.title;
      current_country = $(element).data('countryno');
      current_city_link = element.href;
    }
  };
  // delete drawings from previous states
  var removeCruft = function( ){
    $('.city-container, .club-container').remove();
    $('.map-placeholder').html('');
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

  var drawCanvas = function(height){
    var height = height || '1200px';
    // draw the initial canvas
    $('#map-canvas').remove();
    $('<div id="map-canvas" />')
        .css({'width':'100%',height:height})
        .appendTo('.map-placeholder');
   
  }

  // listener for hash changes
  var loadHashLocation = function() {
     var href = window.location.hash.replace(/^#/,'')
     // hash contains either uitgaan or locaties or doesn't exist 
     if (href.length){
       removeCruft();  
       // stad overzicht clubs
       if (href.match(/\/uitgaan\/\?c/)) {
         var citymap;
         selectCity('uitgaan'); 
         var city = href.split(/\?c=/)[1]
         $.get(href+'&ajax=1',function(resp){
            $('ul.steden').hide(); 
            $('.stedencontainer')
              .append('<div class="city-container" data-href="'+href+'">'+resp+'</div>')
              .find('.locatiebeschrijving a').click(function(e){
                  e.preventDefault();
                  window.location.hash = this.href;
              });
              drawCanvas('800px');
              // retrieve te main map 
              $.get('http://maps.googleapis.com/maps/api/geocode/json?address='+current_city_title+
                '+'+Drupal.settings.city_names['nl'][current_country],function(resp){
                citymap = hC.city_mapInitialize(resp);  
                // draw venuemarkers   
                $('.locatiebeschrijving').each(function(){
                    var l = getVenueInfo(this);
                    if (!!l.stad && l.stad.length && !!l.straatnaam && l.straatnaam.length && !!l.nummer && l.nummer.length){
                      var api_call ='http://maps.googleapis.com/maps/api/geocode/json?address='+l.straatnaam+'+'+l.nummer+'+'+l.stad
                      ajaxQueue(api_call,citymap,'venue',this);
                    }
                });   
              },'json');
         });
         
         trail('uitgaan');
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
            if (locatie_naam.indexOf('diverse locaties') > -1){
              $('.club-container p').hide();
            }
            var stad = $.trim($container.find('.city').text());
            var country = getCountry($container.find('.city').data('cityno')); 
            var iframe_src ='https://www.google.com/maps/embed/v1/place?key='+hC.mapsKey+'&q='+encodeURIComponent(straatnaam+ ' '+straatnummer +' ' + stad + ' ' + country  );
            var iframe_height = '800';   
            var iframe_width = mapContainer.width(); 
            mapContainer.html('<iframe frameborder="0" style="border:0"'+
                                'width="'+iframe_width+'" height="'+iframe_height+'"' +
                                  'src="'+iframe_src+'"></iframe>');
            trail('locaties'); 
          }); 
       }
     }else{
        // landelijk overzicht
        removeCruft(); 
        country_overview();   
        $('ul.steden').fadeIn('slow');
        trail();
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
      if(this.tagName == 'a' || this.tagName == 'A'){
        $a = $(this); 
      }else{
        $a = $(this).find('a'); 
      }
      selectCity('overview',$a[0]);
      window.location.hash = $a[0].href;
    });
    
    drawCanvas();  
    hC.missingCities = function(missingCityArray,cityCountries,map){
      if (!missingCityArray.length) return;      
      for (var i = 0; i < missingCityArray.length; i++){
        ajaxQueue('/uitgaan/getgeo/?l='+missingCityArray[i]+'&c='+cityCountries[missingCityArray[i]],map);
      }
    }

        hC.cityMarker = function(citydata,map){
      if (!citydata || !citydata.results || !citydata.results.length) return;
      var locdat = citydata.results[0].geometry.location;
      var marker = new google.maps.Marker({
          position: new google.maps.LatLng(locdat.lat,locdat.lng),
          title:citydata.results[0].address_components[0].long_name
      });
      google.maps.event.addListener(marker, 'click', hC.city_markerListener);
      marker.setMap(map);
    }
    
    var mapOptions = {
      zoom: 8,
      center: new google.maps.LatLng(51.6704,5.2589),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      scrollwheel: false, 
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
          if (!!hC.location_data[i].results[0]){
            cityObj[hC.location_data[i].results[0].address_components[0].long_name] = 1;
          }
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

 }(jQuery)); 
