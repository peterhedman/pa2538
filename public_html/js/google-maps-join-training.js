$( document ).ready(function() {
//http://fiddle.jshell.net/3AdSG/
if($('div').is('.map-on-single')){
	
	var div = document.getElementById("location_target");
    var myData = div.textContent;
	var locationsArr = $.parseJSON(myData);
	
	
	

function replaceAll(str, find, replace) {
  return str.replace(new RegExp(find.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1"), 'g'), replace);
}

var totDistance;
var startPointLocation = locationsArr[0];
var endPointLocation = locationsArr[2];

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
	  suppressPolylines: true,
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
	var directions = {};
	var polylines = [];
	var coordinates = [];
	
	
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
		if(locationsArr[3] == "user_logged_in"){
			displayUserJoinStartData(null);
			displayUserJoinEndData(null);
		} else {
			computeTotalDistance(directionsDisplay.getDirections());
		}
		sendMarkers(directionsDisplay.getDirections());
		
		//var coordinates = [];
		//if(locationsArr[3] == "user_logged_in"){
			/****** SSSTAAARTTTT DRAGMARKER*******/
			coordinates = renderDirectionsPolylines(directionsDisplay.getDirections());
			
			//console.log(coordinates);
			if(locationsArr[3] == "user_logged_in"){
				//polyline shit 
				var latLngStart = JSON.parse(locationsArr[0]);
				var latLngEnd = JSON.parse(locationsArr[2]);
				
				console.log(latLngStart);
				
				console.log("locationsArr[11]:" + locationsArr[11] + " locationsArr[12]:" + locationsArr[12]);
				console.log("locationsArr[0]:" + locationsArr[0] + " locationsArr[2]:" + locationsArr[2]);
				if(locationsArr[11] != locationsArr[0] && locationsArr[11] !== null){
					console.log("herheher");
					latLngStart = JSON.parse(locationsArr[11]);
					if(latLngStart != null)
					{
					var checkPos = new google.maps.LatLng(latLngStart.lat, latLngStart.lng);
					var distance = calcDistance(checkPos , coordinates)
					}
					displayUserJoinStartData(distance)
				}
				if(locationsArr[12] != locationsArr[2] && (locationsArr[12] !== null && locationsArr[12] != "0")){
					latLngEnd = JSON.parse(locationsArr[12]);
					
					var checkPos = new google.maps.LatLng(latLngEnd.lat, latLngEnd.lng);
					var distance = calcDistance(checkPos, coordinates)
					displayUserJoinStartData(distance)
				}
				
				
				var markerStart = new google.maps.Marker({
					position: latLngStart,
					map: map,
					draggable: true
				});
				
				var markerEnd = new google.maps.Marker({
					position: latLngEnd,
					map: map,
					draggable: true
				});
				
				
				infowindow.setContent("Drag the markers to change your start and end position on route");
				infowindow.setPosition(latLngStart);
				infowindow.open(map);
			}
			
	
			google.maps.event.addDomListener(markerStart, 'dragend', function(e) {
				markerStart.setPosition(find_closest_point_on_path(e.latLng,coordinates));
				
				startPointLocation = '{"lat":' + e.latLng.lat() +',"lng":' + e.latLng.lng() + '}';
				
				var distance = calcDistance(e.latLng, coordinates)
				displayUserJoinStartData(distance)
				//console.log(distance);
				infowindow.close(map);
				
			});
	
			google.maps.event.addDomListener(markerStart, 'drag', function(e) {
				infowindow.close(map);
				markerStart.setPosition(find_closest_point_on_path(e.latLng,coordinates));
				infowindow.setContent("Your Start position");
			 	infowindow.setPosition(e.latLng);
			 	infowindow.open(map);
			});
			
			google.maps.event.addDomListener(markerEnd, 'dragend', function(e) {
				markerEnd.setPosition(find_closest_point_on_path(e.latLng,coordinates));
				
				//tartPointLocation: (56.13590636065909, 15.46107911015622) endPointLocation: {"lat":56.1796233,"lng":15.5993574}
				
				endPointLocation = '{"lat":' + e.latLng.lat() +',"lng":' + e.latLng.lng() + '}';
				
				var distance = calcDistance(e.latLng, coordinates)
				displayUserJoinEndData(distance)
				//console.log(distance);
				infowindow.close(map);
			});
	
			google.maps.event.addDomListener(markerEnd, 'drag', function(e) {
				infowindow.close(map);
				markerEnd.setPosition(find_closest_point_on_path(e.latLng,coordinates));
				infowindow.setContent("Your End position");
			 	infowindow.setPosition(e.latLng);
			 	infowindow.open(map);
			});
		//}
	  });
	  
	  google.maps.event.addListener(directionsDisplay, 'click', function() {
		//sendMarkers(directionsDisplay.getDirections());
		
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

	
	var polylineOptions = {
	  strokeColor: '#0e6fe7',
	  strokeOpacity: 1,
	  strokeWeight: 4
	};
	
	function renderDirectionsPolylines(response) {
	  for (var i=0; i<polylines.length; i++) {
		polylines[i].setMap(null);
	  }
	  
	  var legs = response.routes[0].legs;
	  for (i = 0; i < legs.length; i++) {
		var steps = legs[i].steps;
		for (j = 0; j < steps.length; j++) {
		  var nextSegment = steps[j].path;
		  var stepPolyline = new google.maps.Polyline(polylineOptions);
		  
		  for (k = 0; k < nextSegment.length; k++) {
			
			stepPolyline.getPath().push(nextSegment[k]);
			
		  }		  
		  
		  
		  stepPolyline.setMap(map);
		  polylines.push(stepPolyline);
		  
		  stepPolyline.getPath().forEach(function(latLng) {
			coordinates.push(latLng);
		  });
		  
		
		  /*
		  google.maps.event.addListener(stepPolyline,'click', function(evt) {
			 infowindow.setContent("you clicked on the route<br>"+evt.latLng.toUrlValue(6));
			 infowindow.setPosition(evt.latLng);
			 infowindow.open(map);
		  })
		  */
		}
	  }
	  
	  return coordinates;
	  
	}
	
	
	function calcDistance(checkTo, coordinates){
		var polylineLength = 0;
		var path = [];
		
		for (var i = 0, l = coordinates.length; i < l; i++) {
			var obj = coordinates[i];
			var pointPath = new google.maps.LatLng(obj.lat(),obj.lng());
			path.push(pointPath);
		  if (i > 0) {
			  polylineLength += google.maps.geometry.spherical.computeDistanceBetween(path[i], path[i-1]);
				
			var betweenCheckerPoly = new google.maps.Polyline();
			betweenCheckerPoly.getPath().push(path[i]);
			betweenCheckerPoly.getPath().push(path[i-1]);
											
			if (google.maps.geometry.poly.isLocationOnEdge(checkTo, betweenCheckerPoly, 10e-4)) {
				break;	//breaks the loop if the checker location is on the "part" path
			}
		  }
		}
		return polylineLength;
	}
	
	 
	
	function find_closest_point_on_path(drop_pt,path_pts){
		
        distances = new Array();//Stores the distances of each pt on the path from the marker point 
        distance_keys = new Array();//Stores the key of point on the path that corresponds to a distance
        
        //For each point on the path
        $.each(path_pts,function(key, path_pt){
            //Find the distance in a linear crows-flight line between the marker point and the current path point
            var R = 6371; // km
            var dLat = (path_pt.lat()-drop_pt.lat()).toRad();
            var dLon = (path_pt.lng()-drop_pt.lng()).toRad();
            var lat1 = drop_pt.lat().toRad();
            var lat2 = path_pt.lat().toRad();

            var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2); 
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            var d = R * c;
            //Store the distances and the key of the pt that matches that distance
            distances[key] = d;
            distance_keys[d] = key; 
            
        });
        //Return the latLng obj of the second closest point to the markers drag origin. If this point doesn't exist snap it to the actual closest point as this should always exist
        return (typeof path_pts[distance_keys[_.min(distances)]+1] === 'undefined')?path_pts[distance_keys[_.min(distances)]]:path_pts[distance_keys[_.min(distances)]+1];
    }

    /** Converts numeric degrees to radians */
    if (typeof(Number.prototype.toRad) === "undefined") {
      Number.prototype.toRad = function() {
        return this * Math.PI / 180;
      }
    }

	
}

function displayUserJoinStartData(distance){
	var time = locationsArr[4];
	var date = new Date(time.replace(" ", "T"));
	time = date.getHours() + ":" + (date.getMinutes()<10?'0':'') + date.getMinutes();
	
	if(distance == null){
		distance = "0";
		
	} else {
		distance = +distance.toFixed(0);
		
		var theSpeed = parseFloat(locationsArr[9]) * parseFloat(locationsArr[10]);
		var extraTime = distance/theSpeed; 
		//5 meters per secund (speed) 
		date.setSeconds(date.getSeconds() + extraTime);
		time = date.getHours() + ":" + (date.getMinutes()<10?'0':'') + date.getMinutes();
	}
	
	//console.log(locationsArr[4]);	 //Time
	if(locationsArr[5] == "0"){
		console.log("japp rank är noll");	
	}
	
	document.getElementById('distanceStart').innerHTML = distance + ' m';
	document.getElementById('timeStart').innerHTML = time;
	
}

function displayUserJoinEndData(distance){
	var time = locationsArr[4];
	var date = new Date(time.replace(" ", "T"));
	//time = date.getHours() + ":" + (date.getMinutes()<10?'0':'') + date.getMinutes();
	
	if(distance == null){
		distance = locationsArr[6];
	} else {
		distance = +distance.toFixed(0);
	}
	
	
	
		var theSpeed = parseFloat(locationsArr[9]) * parseFloat(locationsArr[10]);
		var extraTime = distance/theSpeed; 
		//5 meters per secund (speed) 
		date.setSeconds(date.getSeconds() + extraTime);
		time = date.getHours() + ":" + (date.getMinutes()<10?'0':'') + date.getMinutes();
	
	document.getElementById('distanceEnd').innerHTML = distance + ' m';
	document.getElementById('timeEnd').innerHTML = time;
	
	extraTime = extraTime/60;
	extraTimeMinutes = +extraTime.toFixed(0)
	extraTimeFloat = +extraTime.toFixed(2)
	ExtraTimeSecunds = extraTimeFloat - extraTimeMinutes;
	ExtraTimeSecunds = ExtraTimeSecunds*60;
	ExtraTimeSecunds = +ExtraTimeSecunds.toFixed(0)
	
	if(ExtraTimeSecunds < 0){
		extraTimeMinutes--;
		ExtraTimeSecunds = 60 + ExtraTimeSecunds;
	}
	
	extraTime = extraTimeMinutes + "min " + ExtraTimeSecunds + "sec"
	
	document.getElementById('totalTime').innerHTML = extraTime;
}




function computeTotalDistance(result) {
	var total = 0;
	var myroute = result.routes[0];
	for (var i = 0; i < myroute.legs.length; i++) {
	  total += myroute.legs[i].distance.value;
	}
	
	totDistance = total;
	
	document.getElementById('distanceEnd').innerHTML = total + ' m';
	
	var time = locationsArr[4];
	var date = new Date(time.replace(" ", "T"));
	
	//console.log("pace: " + parseFloat(locationsArr[9]) + " - speed: " +  parseFloat(locationsArr[10]));
	
	var theSpeed = parseFloat(locationsArr[9]) * parseFloat(locationsArr[10]);
	var extraTime = total/theSpeed; 
	//5 meters per secund (speed)
	
	date.setSeconds(date.getSeconds() + extraTime);
	time = date.getHours() + ":" + (date.getMinutes()<10?'0':'') + date.getMinutes();
	
	extraTime = extraTime/60;
	extraTimeMinutes = +extraTime.toFixed(0)
	extraTimeFloat = +extraTime.toFixed(2)
	ExtraTimeSecunds = extraTimeFloat - extraTimeMinutes;
	ExtraTimeSecunds = ExtraTimeSecunds*60;
	ExtraTimeSecunds = +ExtraTimeSecunds.toFixed(0)
	
	if(ExtraTimeSecunds < 0){
		extraTimeMinutes--;
		ExtraTimeSecunds = 60 + ExtraTimeSecunds;
	}
	
	extraTime = extraTimeMinutes + "min " + ExtraTimeSecunds + "sec"
	
	document.getElementById('totalTime').innerHTML = extraTime;
	document.getElementById('timeEnd').innerHTML = time;
	
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
					url: "/training-single.php",
					type: "post",
					data: {"Adress": address, "latlng": latlongJson}
				});
			
				// Callback handler that will be called on success
				request.done(function (response, textStatus, jqXHR){
					// Log a message to the console
					console.log("Hooray, it worked!");
					window.location.href = document.location.origin + '/training-history.php';
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
        url: "/training-single.php",
        type: "post",
        data: {"waypoints": markersJson, "totalDistance": totDistance, "FormData": serializedData, "id": locationsArr[8]}
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

$("#save-join-route").submit(function(event){
	
	event.preventDefault();
	
	var markersJson = JSON.stringify(markers_send);
	
	//console.log("startPointLocation: " + startPointLocation + " endPointLocation: " + endPointLocation);
	
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
        url: "/training-single.php",
        type: "post",
        data: {"waypoints": markersJson, "totalDistance": locationsArr[6], "FormData": serializedData, "id": locationsArr[8], "startLoc": startPointLocation, "stopLoc": endPointLocation}
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
	 
  

});




}
});