(function($){
  
  $('<div id="map-canvas" />')
    .css({'width':'100%',height:'1200px'})
    .appendTo('.map-placeholder');
  
  //
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
  hC.prepareCityData = function(citydata){
    
    
  }
  hC.mapInitialize = function() {
    $steden = $('.steden li > a');
    if (!$steden.length) return; 

    var mapOptions = {
      zoom: 8,
      center: new google.maps.LatLng(52.3704,5.1589),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      panControl: true,
      zoomControl: false,
      mapTypeControl: false,
      scaleControl: false,
      streetViewControl: false,
      overviewMapControl: false,
      draggable: true, zoomControl: false, 
      scrollwheel: false, disableDoubleClickZoom: true  
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
  }
  
  // listeners for 
  $('ul.steden li').click(function(e){
   // e.preventDefault();
  });  

}(jQuery)); 

