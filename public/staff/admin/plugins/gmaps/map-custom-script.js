  //map.js

//Set up some of our variables.
var map; //Will contain map object.
var marker = false; ////Has the user plotted their location marker? 
        
//This function will get the marker's current location and then add the lat/long
//values to our textfields so that we can save the location


function initMaps() {
     map = new google.maps.Map(document.getElementById('map'), {
      center: {lat: -1.35946, lng: 36.82430},
      zoom: 7
    });
    
  
    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  
    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function() {
      searchBox.setBounds(map.getBounds());
    });
  
    var markers = [];
    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.

    google.maps.event.addListener(map, 'click', function(event) {                
        //Get the location that the user clicked.
        var clickedLocation = event.latLng;
        //If the marker hasn't been added.
        if(marker === false){
            //Create the marker.
            marker = new google.maps.Marker({
                position: clickedLocation,
                map: map,
                draggable: true //make it draggable
            });
            //Listen for drag events!
            google.maps.event.addListener(marker, 'dragend', function(event){
                markerLocation();
            });
        } else{
            //Marker has already been added, so just change its location.
            marker.setPosition(clickedLocation);
        }
        //Get the marker's location.
        markerLocation();
    });

    searchBox.addListener('places_changed', function(e) {
      var places = searchBox.getPlaces();
          console.log("ldkasfjlksdajflksdjfasl");
          var marker1 = new google.maps.Marker({
            position: places.latLng,
            map: map,
            draggable: true,
          });
  
  
      if (places.length == 0) {
        return;
      }
  
      // Clear out the old markers.
      markers.forEach(function(marker) {
        marker.setMap(null);
      });
      markers = [];
  
      // For each place, get the icon, name and location.
      var bounds = new google.maps.LatLngBounds();
      places.forEach(function(place) {
        if (!place.geometry) {
          console.log("Returned place contains no geometry");
          return;
        }
        var icon = {
          url: place.icon,
          size: new google.maps.Size(71, 71),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(17, 34),
          scaledSize: new google.maps.Size(25, 25)
        };
  
        // Create a marker for each place.
        markers.push(new google.maps.Marker({
          map: map,
          icon: icon,
          title: place.name,
          position: place.geometry.location
        }));
  
        if (place.geometry.viewport) {
          // Only geocodes have viewport.
          bounds.union(place.geometry.viewport);
        } else {
          bounds.extend(place.geometry.location);
        }
      });
      map.fitBounds(bounds);
    });
  }

  function markerLocation(){
    //Get location.
    var currentLocation = marker.getPosition();
    var geocoder = new google.maps.Geocoder();
    var infowindow = new google.maps.InfoWindow();
    // geocoder.geocode({
    //     'latLng': currentLocation
    // }, function (results, status) {
    //     if (status ==
    //         google.maps.GeocoderStatus.OK) {
    //         if (results[1]) {
    //             alert(results[1].formatted_address);
    //         } else {
    //             alert('No results found');
    //         }
    //     } else {
    //         alert('Geocoder failed due to: ' + status);
    //     }
    // });
    //Add lat and lng values to a field that we can save.
    
    document.getElementById('lat').value = currentLocation.lat(); //latitude
    document.getElementById('lng').value = currentLocation.lng(); //longitude 
    document.getElementById('latitude').value = currentLocation.lat(); //latitude
    document.getElementById('longitude').value = currentLocation.lng(); //longitude 
}
        
//Load the map when the page has finished loading.
// google.maps.event.addDomListener(window, 'load', initMaps);
