jQuery(document).ready(function () {
    /* search place */
    var map_search_place = document.getElementById("map_place_search");
    var searchBox = new google.maps.places.SearchBox(map_search_place);

    /* Bias the SearchBox results towards current map's viewport. */
    map_admin_view.addListener('bounds_changed', function () {
        searchBox.setBounds(map.getBounds());
    });

    var markers = [];
    /* Listen for the event fired when the user selects a prediction and retrieve
     more details for that place. */
    searchBox.addListener('places_changed', function () {
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        /* Clear out the old markers. */
        markers.forEach(function (marker) {
            marker.setMap(null);
        });
        markers = [];

        /* For each place, get the icon, name and location. */
        var bounds = new google.maps.LatLngBounds();
        places.forEach(function (place) {
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

            /* Create a marker for each place. */
            markers.push(new google.maps.Marker({
                map: map_admin_view,
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
        map_admin_view.fitBounds(bounds);
    });


    /*directions */
    var map_direction_start = document.getElementById("map_direction_start");
    var map_direction_end = document.getElementById("map_direction_end");

    var autocomplete_start = new google.maps.places.Autocomplete(map_direction_start);
    var autocomplete_end = new google.maps.places.Autocomplete(map_direction_end);

    /** Handle changing start point of direction */
    google.maps.event.addListener(autocomplete_start, 'place_changed', function () {
        var addr = jQuery("#map_direction_start").val();
        geocoder = new google.maps.Geocoder();
        geocoder.geocode({'address': addr}, function (results, status) {

            hugeitMapsUpdateDirectionInputsStart(results[0].geometry.location);

            map_admin_view.setCenter(results[0].geometry.location);
        });
    });


    /** Handle changing end point of direction */
    google.maps.event.addListener(autocomplete_end, 'place_changed', function () {
        var addr = jQuery("#map_direction_end").val();
        geocoder = new google.maps.Geocoder();
        geocoder.geocode({'address': addr}, function (results, status) {

            hugeitMapsUpdateDirectionInputsEnd(results[0].geometry.location);

            map_admin_view.setCenter(results[0].geometry.location);
        });
    });


    function hugeitMapsUpdateDirectionInputsStart(latlng) {
        jQuery('#map_direction_start_lat').val(latlng.lat);
        jQuery('#map_direction_start_lng').val(latlng.lng);

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'latLng': latlng}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                address = results[0].formatted_address;
                jQuery("#map_direction_start").val(address);
            }
        });

        hugeitMapsPlaceStartPoint(latlng);
    }

    function hugeitMapsUpdateDirectionInputsEnd(latlng) {
        jQuery('#map_direction_end_lat').val(latlng.lat);
        jQuery('#map_direction_end_lng').val(latlng.lng);

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'latLng': latlng}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                address = results[0].formatted_address;
                jQuery("#map_direction_end").val(address);
            }
        });

        hugeitMapsPlaceEndPoint(latlng);
    }


    function hugeitMapsPlaceStartPoint(latlng) {

        if (!newDirectionStartMarker) {
            newDirectionStartMarker = new google.maps.Marker({
                draggable: true,
                position: latlng,
                map: map_admin_view
            });
            google.maps.event.addListener(newDirectionStartMarker, "drag", function (event) {
                hugeitMapsUpdateDirectionInputsStart(event.latLng);
            });
        }
        else {
            newDirectionStartMarker.setMap(null);
            newDirectionStartMarker = new google.maps.Marker({
                draggable: true,
                position: latlng,
                map: map_admin_view
            });
        }

        startCoords = [
            {
                lat: newDirectionStartMarker.position.lat(),
                lng: newDirectionStartMarker.position.lng()
            },
            {
                lat: latlng.lat(),
                lng: latlng.lng()
            }
        ];

        /* end point picked */
        if (endCoords.length) {
            hugeitMapsCreateDirection();
        }
    }


    function hugeitMapsPlaceEndPoint(latlng) {
        if (!newDirectionEndMarker) {
            newDirectionEndMarker = new google.maps.Marker({
                draggable: true,
                position: latlng,
                map: map_admin_view
            });
            google.maps.event.addListener(newDirectionEndMarker, "drag", function (event) {
                hugeitMapsUpdateDirectionInputsEnd(event.latLng);
            });
        }
        else {
            newDirectionEndMarker.setMap(null);
            newDirectionEndMarker = new google.maps.Marker({
                draggable: true,
                position: latlng,
                map: map_admin_view
            });
        }

        endCoords = [
            {
                lat: newDirectionEndMarker.position.lat(),
                lng: newDirectionEndMarker.position.lng()
            },
            {
                lat: latlng.lat(),
                lng: latlng.lng()
            }
        ];
        /* start point picked */
        if (startCoords.length) {
            hugeitMapsCreateDirection();
        }
    }


    function hugeitMapsCreateDirection() {

        var request = {
            destination: endCoords[1],
            origin: startCoords[1],
            travelMode: google.maps.TravelMode[newDirectionMode]
        };

        directionsService.route(request, function (response, status) {


            if (status == google.maps.DirectionsStatus.OK) {
                if (!newDirection) {
                    newDirection = new google.maps.DirectionsRenderer({
                        map: map_admin_view,
                        draggable: true,
                        polylineOptions: {
                            clickable: false,
                            strokeColor: "#555",
                            strokeOpacity: 1,
                            strokeWeight: 3
                        }
                    });
                }

                newDirectionStartMarker.setMap(null);
                newDirectionEndMarker.setMap(null);

                newDirection.setDirections(response);


                var available_routes = response.routes;

                var html = '';
                jQuery.each(available_routes, function (k, available_route) {
                    var legs = available_route.legs;
                    var distance = legs[0].distance.text;
                    var duration = legs[0].duration.text;
                    var summary = available_route.summary;
                    var steps = legs[0].steps;
                    var direction_text = '';
                    jQuery.each(steps, function (f, step) {
                        direction_text += step.instructions + '<br>';
                    })

                    html += '<div class="dir-info-row"><span class="dir-name"> Via ' + summary + '</span><span class="dir-dist"> ' + distance + '</span> <span class="dir-dur"> ' + duration + '</span></div>  ';


                    jQuery('#dir-info-block').html(html);
                });

                newDirection.addListener('directions_changed', function () {
                });


            } else if (status == 'ZERO_RESULTS') {
                window.alert('Directions request failed due to ' + status);
            }
            else {
                newDirectionCoords.splice(1, 1);
                hugeitMapsShowNotice(directionL10n.invalidDirectionPoints);
            }
        });
    }


    jQuery(document).on('click', '.searchbox-directions', function () {
        markers.forEach(function (marker) {
            marker.setMap(null);
        });
        jQuery('#show-directions').hide();
        jQuery('#directions-block').show();
    })

    jQuery(document).on('click', '#travel-mode ul li', function () {
        jQuery('#travel-mode ul li').removeClass('current');
        jQuery(this).addClass('current');
        newDirectionMode = jQuery(this).attr('data-mode').toUpperCase();
    })

})
