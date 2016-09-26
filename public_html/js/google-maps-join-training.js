$( document ).ready(function() {
//http://fiddle.jshell.net/3AdSG/
if($('div').is('.map-on-single')){
	
	var div = document.getElementById("location_target");
    var myData = div.textContent;
	var locationsArr = $.parseJSON(myData);
	
	
	

function replaceAll(str, find, replace) {
  return str.replace(new RegExp(find.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1"), 'g'), replace);
}


function computeTotalDistance(result) {
	var total = 0;
	var myroute = result.routes[0];
	for (var i = 0; i < myroute.legs.length; i++) {
	  total += myroute.legs[i].distance.value;
	}
	total = total / 1000;
	document.getElementById('total').innerHTML = total + ' km';
	
	totDistance = total;
}
	
	
function initMapJoin() {
	console.log(locationsArr[3]);
	
	if(locationsArr[3] == "current_user_event"){
		var rendererOptions = {
	  draggable: true,
	  suppressInfoWindows: true
	  };
	} else {
		var rendererOptions = {
	  draggable: false,
	  suppressInfoWindows: false
	  };
	}
	
	
	var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
	var directionsService = new google.maps.DirectionsService();
	var infowindow = new google.maps.InfoWindow();
	var map;
	var total;
	var waypoint_markers = [];
	var initialWaypointsArr;
	
	//console.log(initialWaypointsArr());
	
	var myOptions = {
		zoom: 13,
		center: JSON.parse(locationsArr[0]),
		mapTypeId: 'terrain',
		scrollwheel: false, 
		disableDoubleClickZoom: true
	};
	var markers = [];
	
	$(function() {
	  map = new google.maps.Map($('#map-single')[0], myOptions);
	
	  directionsDisplay.setMap(map);
	  //directionsDisplay.setPanel($("#directionsPanel")[0]);
	  
	  
	  
	
	  google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
		watch_waypoints();
		computeTotalDistance(directionsDisplay.getDirections());
		sendMarkers(directionsDisplay.getDirections());
	  });
	  
	  google.maps.event.addListener(directionsDisplay, 'click', function() {
		sendMarkers(directionsDisplay.getDirections());
		makeJoiner();
	  });
	  
	  calcRoute(false);
	});
	
	function calcRoute(waypoints) {
	  var selectedMode = "WALKING"; //document.getElementById("mode").value;
	  var ary;
	  if(waypoints) {
		ary = waypoints.map(function(wpt) {return {location: wpt, stopover: false};});
	  } else {
		 ary = [];
		
			var waypointArr = locationsArr[1].match(/[^,]+,[^,]+/g);
			if(locationsArr[1] != ""){
				for(var i=0; i<waypointArr.length; i++) {
					var objectPos = JSON.parse(waypointArr[i]);
					ary.push({ location: objectPos, stopover: false });
				}
			
			}
	  }
	
	  var request = {
		origin: JSON.parse(locationsArr[0]),
		destination: JSON.parse(locationsArr[2]),
		waypoints: ary,
		travelMode: google.maps.TravelMode[selectedMode],
		unitSystem: google.maps.UnitSystem["IMPERIAL"]
	  };
	  directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
		  directionsDisplay.setDirections(response);
		}
	  });
	}
	
	function watch_waypoints() {
	  clear_markers();
	  var wpts = directionsDisplay.directions.routes[0].legs[0].via_waypoints;
	  for(var i=0; i<wpts.length; i++) {
		var marker = new google.maps.Marker({
			map: map,
			//icon: "/images/blue_dot.png",
			position: new google.maps.LatLng(wpts[i].lat(), wpts[i].lng()),
			title: i.toString()
			});
			
		if(locationsArr[3] != "current_user_event"){
			marker.setVisible(false);
		}
			
		waypoint_markers.push(marker);
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent("right click to delete this waypoint");
			infowindow.open(map, this);
		});
		google.maps.event.addListener(marker, 'rightclick', function() {
			marker.setMap(null);
			wpts.splice(parseInt(this.title), 1);
			calcRoute(wpts);
			directionsDisplay.setOptions({ preserveViewport: true, draggable: true});
		});
	  }
	}
	
	function clear_markers() {
	  for(var i=0; i<waypoint_markers.length; i++){
		waypoint_markers[i].setMap(null);
	  }
	}
	
	
	function sendMarkers(result){
		
		markers_send = [];
		
		markers_send.push(result.routes[0].legs[0].start_location);
		
		var wpts = result.routes[0].legs[0].via_waypoints;
		for(var i=0; i<wpts.length; i++) {
		var marker = new google.maps.Marker({
			map: map,
			//icon: "/images/blue_dot.png",
			position: new google.maps.LatLng(wpts[i].lat(), wpts[i].lng()),
			title: i.toString()
			});
		marker.setVisible(false);
		markers_send.push(marker.getPosition());
		}
		
		markers_send.push(result.routes[0].legs[0].end_location);
		
		for(var i = 0; i < markers_send.length; i++)
		{
			console.log("waypoint_markers" + i + ": " + markers_send[i]);
		}
	}
	
	function makeJoiner(){
		/*
		markers_join = [];
		
		var marker = new google.maps.Marker({
			map: map,
			//icon: "/images/blue_dot.png",
			position: new google.maps.LatLng(wpts[i].lat(), wpts[i].lng()),
			title: i.toString()
			});
		markers_send.push(marker.getPosition());
		
		var request = {
		origin: markers_join[0],
		destination: markers_join[1],
		waypoints: [],
		travelMode: google.maps.TravelMode[selectedMode],
		unitSystem: google.maps.UnitSystem["IMPERIAL"]
	  };
	  directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
		  directionsDisplay.setDirections(response);
		}
	  });
	  */
	}

}





function computeTotalDistance(result) {
	var total = 0;
	var myroute = result.routes[0];
	for (var i = 0; i < myroute.legs.length; i++) {
	  total += myroute.legs[i].distance.value;
	}
	total = total / 1000;
	document.getElementById('total').innerHTML = total + ' km';
	
	totDistance = total;
}

google.maps.event.addDomListener(window, 'load', initMapJoin);
// Variable to hold request
var request;

function sendAddress(latlng) {
	
	//TO DO GET THE ADRESS OUT OF GEOCODER
	
	 if (request) {
        request.abort();
    }
	
	var geocoder = new google.maps.Geocoder();
	var geoccoderna = geocoder.geocode({ 'latLng': latlng }, function (results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[0]) {
				var address = results[0].formatted_address;
				//var latlngen = results[0].geometry.location;
				
				var latlongJson = JSON.stringify(latlng);
				
				// Fire off the request to /form.php
				request = $.ajax({
					url: "/training-add-session.php",
					type: "post",
					data: {"Adress": address, "latlng": latlongJson}
				});
			
				// Callback handler that will be called on success
				request.done(function (response, textStatus, jqXHR){
					// Log a message to the console
					console.log("Hooray, it worked!");
				});
			
				// Callback handler that will be called on failure
				request.fail(function (jqXHR, textStatus, errorThrown){
					// Log the error to the console
					console.error(
						"The following error occurred: "+
						textStatus, errorThrown
					);
				});
				
			}
		}
	});
}


// Bind to the submit event of our form
$("#update-route").submit(function(event){
	
	$("#update-route input, #update-route textarea").each(function() {
        if($(this).val() == ''){
			alert("Please fill in all fileds");
			throw new Error("Please fill in all required");
		}
    });
	
	var markersJson = JSON.stringify(markers_send);
	
    // Abort any pending request
    if (request) {
        request.abort();
    }
	
    // setup some local variables
    var $form = $(this);
	
	$form.hide(800);
	
    // Let's select and cache all the fields
    var $inputs = $form.find("input, select, button, textarea");

    // Serialize the data in the form
    var serializedData = $form.serialize();

    // Let's disable the inputs for the duration of the Ajax request.
    // Note: we disable elements AFTER the form data has been serialized.
    // Disabled form elements will not be serialized.
    $inputs.prop("disabled", true);
	
    // Fire off the request to /form.php
    request = $.ajax({
        url: "/training-add-session.php",
        type: "post",
        data: {"waypoints": markersJson, "totalDistance": totDistance, "FormData": serializedData}
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
        // Log a message to the console
		//sends the address via Ajax
		sendAddress(markers_send[0]);
        console.log("Hooray, it worked!");
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // Log the error to the console
        console.error(
            "The following error occurred: "+
            textStatus, errorThrown
        );
    });
	
	
    // Callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        // Reenable the inputs
        $inputs.prop("disabled", false);
    });
	
	
	
	 // Prevent default posting of form
    event.preventDefault();

});




}
});