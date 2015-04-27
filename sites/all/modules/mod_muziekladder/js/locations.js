(function($){
  /** Pagehandlers
   ** Called based on the Drupal locatiepagina status setting
   **/
  var pagehandlers = {
    index:function(){  
      laad.wait('maps');
      var autocomplete = function(){
        var auto_options = [];
        var s = Drupal.settings.muziek_cities;
        for (var i = 0;  i < s.length; i++) {
          auto_options.push({value:s[i].Name, data:s[i].Id})
        }
        
        $('#city_autocomplete').autocomplete ({
          lookup: auto_options,
          onSelect: function (suggestion) {
            location.href= $('a[data-cityno="'+suggestion.data+'"]')[0].href;
        }});
      };
      
      laad.js('autocomplete',autocomplete);
    },
    city_main:function() {
      laad.wait('maps');
    },
    venue:function(){
      var $container = $('.club-container'); 
      var mapContainer = $('.map-placeholder');
      var straatnaam = $.trim($container.find('.straatnaam').text());
      var straatnummer = $.trim($container.find('.straatnummer').text());
      var locatie_naam = $.trim($container.find('.location-titel').text());
      var land_naam = $.trim($container.find('.country-name').text());
      var stad = $.trim($container.find('.city').text());
      var iframe_src ='https://www.google.com/maps/embed/v1/place?key='+hC.mapsKey+'&q='+encodeURIComponent(straatnaam+ ' '+straatnummer +' ' + stad + ' ' + land_naam  );
      var iframe_height = '300';   
      var iframe_width = mapContainer.width(); 
      mapContainer.html('<iframe frameborder="0" style="border:0"'+
        'width="'+iframe_width+'" height="'+iframe_height+'"' +
         'src="'+iframe_src+'"></iframe>');
       
    }
  };
  
  // execute the pagehandler    
  pagehandlers[Drupal.settings['locatiepagina']['status']]();

  /**
    * mapIinitialize is called by the maps api after it has loaded
  **/ 
  hC.mapInitialize = function() {
    switch ( Drupal.settings['locatiepagina']['status'] ) {
      case 'index':
        landelijk_overzicht(); 
        break; 
      case 'city_main':
        city_overview();  
        break; 
    }
  }

  // google mapLoadHandlers:
  // landelijk overzicht
  function landelijk_overzicht(){
    country_overview();   
    $('ul.steden').fadeIn('slow');
  }

 
  // used for city-venue maps
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

      });
      google.maps.event.addListener(marker, 'click', function(e){
        hC.venue_markerListener.apply(this,[e,element]) 
      });
      marker.setMap(map);   
  };

  var infoWindow;
  hC.venue_markerListener = function(e,list_element){
    var info = getVenueInfo(list_element);
    if (infoWindow) infoWindow.close();
    var content = $('<div class="maps_infowindowcontent">'+list_element.innerHTML+'</div>');
    infoWindow = new google.maps.InfoWindow({
       "content" : content[0],
    }); 
    
    infoWindow.open(this.map,this)
  }

  // citymap  (containing venue markers)
  hC.city_mapInitialize = function(data) {
    var lat = data.results[0].geometry.location.lat;
    var lng = data.results[0].geometry.location.lng;
    var mapOptions = {
      zoom: 12,
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
            setTimeout(ajaxQueueProcess,200);
         },'json');
      }())
    }else{
      processing = false;
    } 
  }
  
   /* 
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
*/
 
  // draw the map container
  function drawCanvas(height){
    var height = height || '1200px';
    // draw the initial canvas
    $('#map-canvas').remove();
    $('<div id="map-canvas" />')
        .css({'width':'100%',height:height})
        .appendTo('.map-placeholder');
   
  }
 /******************************************/
 /** Country map overview with all cities **/
 /******************************************/
  var country_overview = function() {
    // object containing citymarkers
    hC.citymarkers = {};
    var muziek_cities = Drupal.settings.muziek_cities; 
    
    var $steden = $('.steden li').click(function(){
      window.location.hash = $(this).find('a')[0].href;
    });
    
    drawCanvas(); 
    
    /**
     **
     **/ 
    hC.missingCities = function(missingCityArray,map){
      if (!missingCityArray.length) return;      
      for (var i = 0; i < missingCityArray.length; i++){
        ajaxQueue('/uitgaan/getgeo/?l='+missingCityArray[i],map);
      }
    }

    // method for drawing citymarker on map
    hC.cityMarker = function(citydata,map){
      if (!citydata || !citydata.results || !citydata.results.length) return;
      var locdat = citydata.results[0].geometry.location;
      var marker = new google.maps.Marker({
          position: new google.maps.LatLng(locdat.lat,locdat.lng),
          title:citydata.results[0].address_components[0].long_name
      });
      google.maps.event.addListener(marker, 'click', function (){
        hC.city_markerListener(citydata)
      });
      marker.setMap(map);
    }
    hC.city_markerListener = function(citydata){
      for (var x in hC.location_data){
        if (hC.location_data[x]== citydata){
          var link = $("a[data-cityno="+x+"]");
          if (link.length) {
            location =link[0]['href']; 
          } else {
            location.reload();   
          }
        }    
      }
    }
   
    var mapOptions = {
      zoom: 8,
      center: new google.maps.LatLng(51.6704,5.2589),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      scrollwheel: false, 
    }
    var cityObj = {}; 
    var missingCityArray = [];  
    var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
    
    if (typeof hC.location_data === 'undefined') {
      // no locations cached yet
      // mark all cities as missing
      for (var i =0; i < muziek_cities.length; i++){
        missingCityArray.push(muziek_cities[i]['Id'])
      }
    } else {
      
      for (var i =0; i < muziek_cities.length; i++){
        if (typeof hC.location_data[ muziek_cities[i]['Id'] ] != 'undefined'){
          hC.cityMarker(hC.location_data[ muziek_cities[i]['Id'] ],map);
        } else {
          missingCityArray.push(muziek_cities[i]['Id'])
        }
      }
    }
    hC.missingCities(missingCityArray,map);
  }; 
 

  /*******************************************/
  /** Map containing all venues in the city **/
  /*******************************************/
  var city_overview = function(){
    var s = Drupal.settings;  
    var citymap;
    var cityno = location.href.match(/uitgaan\/([0-9])+/)[1];
          drawCanvas('250px');
          // retrieve te main map 
          $.get('http://maps.googleapis.com/maps/api/geocode/json?address='+
            s.locatiepagina.city.Name+
            '+'+ 
            s.locatiepagina.city.Country_name,function(resp){
              citymap = hC.city_mapInitialize(resp);  
                // draw venuemarkers   
                $('.locatiebeschrijving')
                  .click(function(){
                    location = $(this).find('a')[0].href;
                  })
                  .each(function(){
                    var l = getVenueInfo(this);
                    if (!!l.stad && l.stad.length && !!l.straatnaam && l.straatnaam.length && !!l.nummer && l.nummer.length){
                      var api_call ='http://maps.googleapis.com/maps/api/geocode/json?address='+l.straatnaam+'+'+l.nummer+'+'+l.stad
                      ajaxQueue(api_call,citymap,'venue',this);
                    }
                  });   
            });
  };

      
}(jQuery)); 
