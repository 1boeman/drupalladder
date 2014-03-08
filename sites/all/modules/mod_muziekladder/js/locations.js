(function($){
	$('<div id="map-canvas" />')
		.css({'width':'100%',height:'900px'})
		.appendTo('.map-placeholder');
	hC.missingCities = function(missingCityArray,map){
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
			ajaxQueue('/uitgaan/getgeo/?l='+missingCityArray[i]);
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
			center: new google.maps.LatLng(52.3704,4.8983),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			panControl: true,
			zoomControl: false,
			mapTypeControl: false,
			scaleControl: false,
			streetViewControl: false,
			overviewMapControl: false	
		}
		var cityObj = {}; 
		var missingCityArray = [];	
		var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
		var cityAlias = {'The Hague':'Den Haag',"'s-Hertogenbosch":'Den Bosch'}
		
		$steden.each(function(){
			cityObj[this.title] = 0;
		});
		if (typeof hC.location_data === 'undefined') {
			missingCityArray = hC.objectKeys(cityObj);
		}else{
			for (var i = 0; i < hC.location_data.length; i++){
				hC.cityMarker(hC.location_data[i],map);
				if (hC.location_data[i]){
					cityObj[hC.location_data[i].results[0].address_components[0].long_name] = 1;
				}	
			}

			for (var name in cityObj){
				if (!cityObj[name])
				missingCityArray.push(name)
			}
		}
		hC.missingCities(missingCityArray,map);
	}
}(jQuery)); 

