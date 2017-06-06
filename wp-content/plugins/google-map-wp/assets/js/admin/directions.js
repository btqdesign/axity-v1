var newDirection = false,
    newDirectionStartMarker = false,
    newDirectionCoords = [],
    newDirectionsDisplay = false,
    directionsService = new google.maps.DirectionsService(),
    stepDisplay = new google.maps.InfoWindow,
    newDirectionMode = 'DRIVING',
    newDirectionShowSteps = false,
    newDirectionMarkers = [],
    editDirection =false,
    editDirectionMarkers = [],
    editDirectionCoords = [],
    editDirectionsDisplay = false,
    editDirectionsService = new google.maps.DirectionsService(),
    editStepDisplay = new google.maps.InfoWindow,
    editDirectionMode = 'DRIVING',
    editDirectionShowSteps = false,
    mapdirection,
    mapcenter,
    map_direction_edit;

jQuery(document).ready(function(){
    if( typeof directionL10n.map == "object" && directionL10n.map != null ){
        hugeitMapsLoadDirectionsMap(
            directionL10n.map.id,
            directionL10n.map.styling_hue,
            directionL10n.map.styling_saturation,
            directionL10n.map.styling_lightness,
            directionL10n.map.styling_gamma,
            directionL10n.map.zoom,
            directionL10n.map.type,
            directionL10n.map.bike_layer,
            directionL10n.map.traffic_layer,
            directionL10n.map.transit_layer
        );
    }
});

function hugeitMapsLoadDirectionsMap(id, hue, saturation, lightness, gamma, zoom, type, bike, traffic, transit) {
    data = {
        action: 'hugeit_maps_get_info',
        map_id: id
    };
    jQuery.ajax({
        url: ajaxurl,
        dataType: 'json',
        method: 'post',
        data: data,
        beforeSend: function () {}
    }).done(function (response) {
        hugeitMapsInitializeDirectionMap(response);
    }).fail(function () {
        console.log('Failed to load response from database');
    });

    function hugeitMapsInitializeDirectionMap(response){
        if( !response.success ){
            return false;
        }

        var mapInfo = response.success;
        var maps = mapInfo.maps;
        for (var i = 0; i < maps.length; i++) {
            var trafficLayer = new google.maps.TrafficLayer();
            var trafficLayer1 = new google.maps.TrafficLayer();
            var bikeLayer = new google.maps.BicyclingLayer();
            var bikeLayer1 = new google.maps.BicyclingLayer();
            var transitLayer = new google.maps.TransitLayer();
            var transitLayer1 = new google.maps.TransitLayer();
            mapcenter = new google.maps.LatLng(
                parseFloat(maps[i].center_lat),
                parseFloat(maps[i].center_lng));
            var styles = [
                {
                    stylers: [
                        {hue: hue},
                        {saturation: saturation},
                        {lightness: lightness},
                        {gamma: gamma}
                    ]
                }
            ];
            var mapOptions = {
                zoom: parseInt(zoom),
                center: mapcenter,
                styles: styles
            };

            mapdirection = new google.maps.Map(document.getElementById('g_map_direction'), mapOptions);
            map_direction_edit = new google.maps.Map(document.getElementById('g_map_direction_edit'), mapOptions);


            if (type == "ROADMAP") {
                mapdirection.setOptions({mapTypeId: google.maps.MapTypeId.ROADMAP})
                map_direction_edit.setOptions({mapTypeId: google.maps.MapTypeId.ROADMAP})
            }
            if (type == "SATELLITE") {
                mapdirection.setOptions({mapTypeId: google.maps.MapTypeId.SATELLITE});
                map_direction_edit.setOptions({mapTypeId: google.maps.MapTypeId.SATELLITE});
            }
            if (type == "HYBRID") {
                mapdirection.setOptions({mapTypeId: google.maps.MapTypeId.HYBRID});
                map_direction_edit.setOptions({mapTypeId: google.maps.MapTypeId.HYBRID});
            }
            if (type == "TERRAIN") {
                mapdirection.setOptions({mapTypeId: google.maps.MapTypeId.TERRAIN});
                map_direction_edit.setOptions({mapTypeId: google.maps.MapTypeId.TERRAIN});
            }

            if (bike == "true") {
                bikeLayer.setMap(mapdirection);
                bikeLayer1.setMap(map_direction_edit);
            }
            if (traffic == "true") {
                trafficLayer.setMap(mapdirection);
                trafficLayer1.setMap(map_direction_edit);
            }
            if (transit == "true") {
                transitLayer.setMap(mapdirection);
                transitLayer1.setMap(map_direction_edit);
            }

            google.maps.event.addListener(mapdirection, 'rightclick', function (event) {
                hugeitMapsPlaceDirection(event.latLng);
            });


            var input_direction_start = document.getElementById("direction_start_addr");
            var input_direction_end = document.getElementById("direction_end_addr");

            var autocomplete_start = new google.maps.places.Autocomplete(input_direction_start);
            var autocomplete_end = new google.maps.places.Autocomplete(input_direction_end);

            /** Handle changing start point of direction */
            google.maps.event.addListener(autocomplete_start, 'place_changed', function () {
                var addr = jQuery("#direction_start_addr").val();
                geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': addr}, function (results, status) {
                    newDirectionMode = jQuery("#direction_travelmode").val();
                    newDirectionCoords[0] = results[0].geometry.location;
                    if( newDirection ){
                        var request = {
                            destination: newDirectionCoords[1],
                            origin: newDirectionCoords[0],
                            travelMode: google.maps.TravelMode[newDirectionMode]
                        };

                        directionsService.route(request, function(response, status) {
                            if (status == google.maps.DirectionsStatus.OK) {
                                newDirection.setDirections(response);
                            }
                        });
                    }else{
                        hugeitMapsPlaceDirection( results[0].geometry.location );
                        hugeitMapsupdateDirectionInputsStart( results[0].geometry.location );
                    }
                    mapdirection.setCenter(results[0].geometry.location);
                });
            });
            /** Handle changing end point of direction */
            google.maps.event.addListener(autocomplete_end, 'place_changed', function () {
                var addr = jQuery("#direction_end_addr").val();
                geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': addr}, function (results, status) {
                    newDirectionMode = jQuery("#direction_travelmode").val();

                    newDirectionCoords[1] = results[0].geometry.location;
                    if( newDirection ){
                        var request = {
                            destination: newDirectionCoords[1],
                            origin: newDirectionCoords[0],
                            travelMode: google.maps.TravelMode[newDirectionMode]
                        };

                        directionsService.route(request, function(response, status) {
                            if (status == google.maps.DirectionsStatus.OK) {
                                newDirection.setDirections(response);
                            }
                        });
                    }else{
                        hugeitMapsPlaceDirection( results[0].geometry.location );
                    }
                    mapdirection.setCenter(results[0].geometry.location);
                });
            });

            jQuery(".direction_options_input").on("change",function(){
                if(!newDirection){
                    return false;
                }
                var tryMode = jQuery("#direction_travelmode").val(),
                    lineOpacity = jQuery("#direction_line_opacity").val(),
                    lineColor = jQuery("#direction_line_color").val(),
                    lineWidth = jQuery("#direction_line_width").val(),
                    tryShowSteps = jQuery("#direction_show_steps").is(":checked");

                if( tryShowSteps != newDirectionShowSteps ){
                    newDirectionShowSteps = tryShowSteps;
                    hugeitMapsShowHideSteps( newDirection.directions, tryShowSteps, mapdirection );
                }

                if( tryMode != newDirectionMode ){
                    directionsService.route({
                        origin: newDirectionCoords[0],
                        destination: newDirectionCoords[1],
                        /*Note that Javascript allows us to access the constant using square brackets and a string value as its "property."*/
                        travelMode: google.maps.TravelMode[tryMode]
                    }, function(response, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            newDirection.setDirections(response);
                            newDirectionMode = tryMode;
                        } else {
                            jQuery("#direction_travelmode").val(newDirectionMode);
                            hugeitMapsShowNotice( directionL10n.invalidDirectionPoints );
                        }
                    });
                }

                newDirection.setOptions({
                    polylineOptions: {
                        strokeColor: "#" + lineColor,
                        strokeOpacity: lineOpacity,
                        strokeWeight: lineWidth
                    }
                });

                var request = {
                    destination: newDirectionCoords[1],
                    origin: newDirectionCoords[0],
                    travelMode: google.maps.TravelMode[newDirectionMode]
                };

                directionsService.route(request, function(response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        newDirection.setDirections(response);
                    }
                });

            });


            jQuery(".edit_direction_list_delete a").on("click",function(){
                if (editDirection) {
                    editDirection.setMap(null);
                    if(editDirectionMarkers){
                        for (var i = 0; i < editDirectionMarkers.length; i++) {
                            editDirectionMarkers[i].setMap(null);
                        }
                    }

                }

                editDirection = false;
                editDirectionCoords = [];
                editDirectionsDisplay = false;
                editDirectionMode = 'DRIVING';
                editDirectionShowSteps = false;

                var parent = jQuery(this).parent();
                var idelement = parent.find(".direction_edit_id");
                var directionid = idelement.val();
                jQuery("#g_maps > div").addClass("hide");
                jQuery("#g_map_direction_edit").removeClass("hide");
                jQuery("#direction_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
                jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").show(200).addClass("tab_options_active_section");
                jQuery("#direction_add_button").hide(200).addClass("tab_options_hidden_section");
                google.maps.event.trigger(map_direction_edit, 'resize');

                jQuery("#direction_get_id").val(directionid);

                var info_directions = mapInfo.directions;

                for (var e = 0; e < info_directions.length; e++) {
                    var id = info_directions[e].id;
                    if (directionid != id) {
                        continue;
                    }

                    var dir_name = info_directions[e].name;
                    var dir_start_lat = info_directions[e].start_lat;
                    var dir_start_lng = info_directions[e].start_lng;
                    var dir_end_lat = info_directions[e].end_lat;
                    var dir_end_lng = info_directions[e].end_lng;
                    var dir_travel_mode = info_directions[e].travel_mode;
                    var dir_line_width = info_directions[e].line_width;
                    var dir_line_color = info_directions[e].line_color;
                    var dir_line_opacity = info_directions[e].line_opacity;
                    var dir_show_steps = info_directions[e].show_steps == 'yes';
                    editDirectionShowSteps = dir_show_steps;
                    jQuery("#direction_edit_name").val(dir_name);
                    jQuery("#direction_edit_start_lat").val(dir_start_lat);
                    jQuery("#direction_edit_start_lng").val(dir_start_lng);
                    jQuery("#direction_edit_end_lat").val(dir_end_lat);
                    jQuery("#direction_edit_end_lng").val(dir_end_lng);
                    jQuery("#direction_edit_travelmode").val(dir_travel_mode);
                    jQuery("#direction_edit_travelmode").val(dir_travel_mode);
                    if(dir_show_steps){
                        jQuery("#direction_edit_show_steps").attr("checked","checked");
                    }

                    jQuery("#direction_edit_line_opacity").simpleSlider("setValue",dir_line_opacity);
                    jQuery("#direction_edit_line_width").simpleSlider("setValue",dir_line_width);
                    jQuery("#direction_edit_line_color").val(dir_line_color).focus().blur();

                    editDirectionCoords = [
                        new google.maps.LatLng(parseFloat(dir_start_lat), parseFloat(dir_start_lng)),
                        new google.maps.LatLng(parseFloat(dir_end_lat), parseFloat(dir_end_lng))
                    ];

                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({'latLng': editDirectionCoords[0] }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var address = results[0].formatted_address;
                            jQuery("#direction_edit_start_addr").val(address);
                        }
                    });

                    geocoder.geocode({'latLng': editDirectionCoords[1] }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var address = results[0].formatted_address;
                            jQuery("#direction_edit_end_addr").val(address);
                        }
                    });

                    var request = {
                        destination: editDirectionCoords[1],
                        origin: editDirectionCoords[0],
                        travelMode: google.maps.TravelMode[dir_travel_mode]
                    };

                    (function(e){
                        directionsService.route(request, function(response, status) {
                            if (status == google.maps.DirectionsStatus.OK) {
                                editDirection = new google.maps.DirectionsRenderer({
                                    map: map_direction_edit,
                                    draggable: true,
                                    preserveViewport : false,
                                    polylineOptions : {
                                        clickable : false,
                                        strokeColor: "#" + info_directions[e].line_color,
                                        strokeOpacity: info_directions[e].line_opacity,
                                        strokeWeight: info_directions[e].line_width
                                    }
                                });
                                editDirection.setDirections(response);

                                editDirection.addListener('directions_changed', function() {
                                    hugeitMapsupdateEditDirectionInputsDirDrag(editDirection.getDirections());
                                    hugeitMapsEditShowHideSteps( editDirection.directions, editDirectionShowSteps, map_direction_edit );
                                });
                                if( info_directions[e].show_steps == "yes" ){
                                    if(editDirectionMarkers[e]){
                                        for (var o = 0; o < editDirectionMarkers.length; o++) {
                                            editDirectionMarkers[o].setMap(null);
                                        }
                                    }else{
                                        editDirectionMarkers = [];
                                    }

                                    var newRoute = editDirection.directions.routes[0].legs[0];
                                    for (var w = 0; w < newRoute.steps.length; w++) {
                                        var marker = editDirectionMarkers[w] = editDirectionMarkers[w] || new google.maps.Marker;
                                        marker.setMap(map_direction_edit);
                                        marker.setPosition(newRoute.steps[w].start_location);
                                        hugeitMapsAttachInstructionText(
                                            stepDisplay, marker, newRoute.steps[w].instructions, map_direction_edit);
                                    }

                                }
                            }
                        });
                    }(e));

                    var input_edit_direction_start = document.getElementById("direction_edit_start_addr");
                    var input_edit_direction_end = document.getElementById("direction_edit_end_addr");

                    var autocomplete_edit_start = new google.maps.places.Autocomplete(input_edit_direction_start);
                    var autocomplete_edit_end = new google.maps.places.Autocomplete(input_edit_direction_end);

                    google.maps.event.addListener(autocomplete_edit_start, 'place_changed', function () {
                        var addr = jQuery("#direction_edit_start_addr").val();
                        geocoder = new google.maps.Geocoder();
                        geocoder.geocode({'address': addr}, function (results, status) {
                            editDirectionMode = jQuery("#direction_edit_travelmode").val();
                            editDirectionCoords[0] = results[0].geometry.location;
                            if( editDirection ){
                                var request = {
                                    destination: editDirectionCoords[1],
                                    origin: editDirectionCoords[0],
                                    travelMode: google.maps.TravelMode[editDirectionMode]
                                };

                                directionsService.route(request, function(response, status) {
                                    if (status == google.maps.DirectionsStatus.OK) {
                                        editDirection.setDirections(response);
                                    }
                                });
                            }
                            map_direction_edit.setCenter(results[0].geometry.location);
                        });
                    });
                    /** Handle changing end point of direction */
                    google.maps.event.addListener(autocomplete_edit_end, 'place_changed', function () {
                        var addr = jQuery("#direction_edit_end_addr").val();
                        geocoder = new google.maps.Geocoder();
                        geocoder.geocode({'address': addr}, function (results, status) {
                            editDirectionMode = jQuery("#direction_edit_travelmode").val();

                            editDirectionCoords[1] = results[0].geometry.location;
                            if( editDirection ){
                                var request = {
                                    destination: editDirectionCoords[1],
                                    origin: editDirectionCoords[0],
                                    travelMode: google.maps.TravelMode[editDirectionMode]
                                };

                                directionsService.route(request, function(response, status) {
                                    if (status == google.maps.DirectionsStatus.OK) {
                                        editDirection.setDirections(response);
                                    }
                                });
                            }
                            map_direction_edit.setCenter(results[0].geometry.location);
                        });
                    });

                    google.maps.event.addListener(map_direction_edit, 'rightclick', function (event) {

                        hugeitMapsPlaceEditDirection(event.latLng);
                    });

                    jQuery(".direction_edit_options_input").on("change",function(){
                        if(!editDirection){
                            return false;
                        }
                        var tryMode = jQuery("#direction_edit_travelmode").val(),
                            lineOpacity = jQuery("#direction_edit_line_opacity").val(),
                            lineColor = jQuery("#direction_edit_line_color").val(),
                            lineWidth = jQuery("#direction_edit_line_width").val(),
                            tryShowSteps = jQuery("#direction_edit_show_steps").is(":checked");

                        if( tryShowSteps != editDirectionShowSteps ){
                            editDirectionShowSteps = tryShowSteps;
                            hugeitMapsShowHideSteps( editDirection.directions, tryShowSteps, map_direction_edit );
                        }

                        if( tryMode != editDirectionMode ){
                            directionsService.route({
                                origin: editDirectionCoords[0],
                                destination: editDirectionCoords[1],
                                /*Note that Javascript allows us to access the constant using square brackets and a string value as its "property."*/
                                travelMode: google.maps.TravelMode[tryMode]
                            }, function(response, status) {
                                if (status == google.maps.DirectionsStatus.OK) {
                                    editDirection.setDirections(response);
                                    editDirectionMode = tryMode;
                                } else {
                                    jQuery("#direction_edit_travelmode").val(editDirectionMode);
                                    hugeitMapsShowNotice( directionL10n.invalidDirectionPoints );
                                }
                            });
                        }

                        editDirection.setOptions({
                            polylineOptions: {
                                clickable : false,
                                strokeColor: "#" + lineColor,
                                strokeOpacity: lineOpacity,
                                strokeWeight: lineWidth
                            }
                        });

                        var request = {
                            destination: editDirectionCoords[1],
                            origin: editDirectionCoords[0],
                            travelMode: google.maps.TravelMode[editDirectionMode]
                        };

                        directionsService.route(request, function(response, status) {
                            if (status == google.maps.DirectionsStatus.OK) {
                                editDirection.setDirections(response);
                            }
                        });

                    });

                }
                return false;
            });


        }

    }
}

function hugeitMapsPlaceEditDirection(latlng){
    editDirectionMode = jQuery("#direction_edit_travelmode").val();
    var request = {
        destination: { lat:latlng.lat(),lng:latlng.lng() },
        origin: editDirectionCoords[0],
        travelMode: google.maps.TravelMode[editDirectionMode]
    };

    (function(latlng){
        directionsService.route(request, function(response, status) {

            if (status == google.maps.DirectionsStatus.OK) {
                if(!editDirection){
                    var lineOpacity = jQuery("#direction_edit_line_opacity").val(),
                        lineColor = jQuery("#direction_edit_line_color").val(),
                        lineWidth = jQuery("#direction_edit_line_width").val();

                    editDirection = new google.maps.DirectionsRenderer({
                        map: map_direction_edit,
                        draggable: true,
                        polylineOptions : {
                            clickable : false,
                            strokeColor: "#" + lineColor,
                            strokeOpacity: lineOpacity,
                            strokeWeight: lineWidth

                        }
                    });
                }

                editDirection.setDirections(response);
                editDirectionCoords[1] = latlng;
                hugeitMapsupdateEditDirectionInputsEnd( editDirectionCoords[1] );

            }else{
                hugeitMapsShowNotice( directionL10n.invalidDirectionPoints );
            }
        });
    }(latlng));

}

function hugeitMapsupdateEditDirectionInputsEnd(latlng){
    jQuery( '#direction_edit_end_lat' ).val(latlng.lat);
    jQuery( '#direction_edit_end_lng' ).val(latlng.lng);

    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({'latLng': latlng}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            address = results[0].formatted_address;
            jQuery("#direction_edit_end_addr").val(address);
        }
    });
}

function hugeitMapsupdateEditDirectionInputsDirDrag(event){
    editDirectionCoords = [
        {
            lat : event.routes[0].legs[0].start_location.lat(),
            lng : event.routes[0].legs[0].start_location.lng()
        },
        {
            lat : event.routes[0].legs[0].end_location.lat(),
            lng : event.routes[0].legs[0].end_location.lng()
        }
    ];

    jQuery( '#direction_edit_start_lat' ).val(event.routes[0].legs[0].start_location.lat());
    jQuery( '#direction_edit_start_lng' ).val(event.routes[0].legs[0].start_location.lng());
    jQuery( '#direction_edit_start_addr' ).val(event.routes[0].legs[0].start_address);
    jQuery( '#direction_edit_end_lat' ).val(event.routes[0].legs[0].end_location.lat());
    jQuery( '#direction_edit_end_lng' ).val(event.routes[0].legs[0].end_location.lng());
    jQuery( '#direction_edit_end_addr' ).val(event.routes[0].legs[0].end_address);
}

function hugeitMapsEditShowHideSteps(direction, show, map ){
    if(show){
        if(editDirectionMarkers){
            for (var j = 0; j < editDirectionMarkers.length; j++) {
                editDirectionMarkers[j].setMap(null);
            }
        }


        var newRoute = direction.routes[0].legs[0];
        for (var i = 0; i < newRoute.steps.length; i++) {
            var marker = editDirectionMarkers[i] = editDirectionMarkers[i] || new google.maps.Marker;
            marker.setMap(map);
            marker.setPosition(newRoute.steps[i].start_location);
            hugeitMapsAttachInstructionText(
                stepDisplay, marker, newRoute.steps[i].instructions, map);
        }
    }else{
        if(editDirectionMarkers){
            for (var i = 0; i < editDirectionMarkers.length; i++) {
                editDirectionMarkers[i].setMap(null);
            }
            editDirectionMarkers = [];
        }
    }
}

function hugeitMapsShowHideSteps(direction, show, map ){
    if(show){
        for (var i = 0; i < newDirectionMarkers.length; i++) {
            newDirectionMarkers[i].setMap(null);
        }

        var newRoute = direction.routes[0].legs[0];
        for (var i = 0; i < newRoute.steps.length; i++) {
            var marker = newDirectionMarkers[i] = newDirectionMarkers[i] || new google.maps.Marker;
            marker.setMap(map);
            marker.setPosition(newRoute.steps[i].start_location);
            hugeitMapsAttachInstructionText(
                stepDisplay, marker, newRoute.steps[i].instructions, map);
        }
    }else{
        for (var i = 0; i < newDirectionMarkers.length; i++) {
            newDirectionMarkers[i].setMap(null);
        }
        newDirectionMarkers = [];
    }
}

function hugeitMapsAttachInstructionText(stepDisplay, marker, text, map) {
    google.maps.event.addListener(marker, 'click', function() {
        /*Open an info window when the marker is clicked on, containing the text of the step.*/
        stepDisplay.setContent(text);
        stepDisplay.open(map, marker);
    });
}

function hugeitMapsPlaceDirection(latlng){
    if ( newDirectionCoords.length == 2 ){
        hugeitMapsPlaceEndPoint(latlng);
        return false;
    }

    if( !newDirectionStartMarker ){
        hugeitMapsupdateDirectionInputsStart(latlng);
        newDirectionStartMarker = new google.maps.Marker({
            draggable: true,
            position: latlng,
            title: directionL10n.startPointTitle,
            map: mapdirection
        });
        google.maps.event.addListener(newDirectionStartMarker, "drag", function (event) {
            hugeitMapsupdateDirectionInputsStart( event.latLng );
        });
        hugeitMapsupdateDirectionInputsStart( latlng );
    }else{
        hugeitMapsPlaceEndPoint(latlng);
    }
}

function hugeitMapsPlaceEndPoint(latlng ){
    newDirectionCoords = [
        {
            lat : newDirectionStartMarker.position.lat(),
            lng : newDirectionStartMarker.position.lng()
        },
        {
            lat : latlng.lat(),
            lng : latlng.lng()
        }
    ];
    hugeitMapsCreateDirection();
}

function hugeitMapsCreateDirection(){

    newDirectionMode = jQuery("#direction_travelmode").val();

    var request = {
        destination: newDirectionCoords[1],
        origin: newDirectionCoords[0],
        travelMode: google.maps.TravelMode[newDirectionMode]
    };

    directionsService.route(request, function(response, status) {

        if (status == google.maps.DirectionsStatus.OK) {
            if(!newDirection){
                var lineOpacity = jQuery("#direction_line_opacity").val(),
                    lineColor = jQuery("#direction_line_color").val(),
                    lineWidth = jQuery("#direction_line_width").val();

                newDirection = new google.maps.DirectionsRenderer({
                    map: mapdirection,
                    draggable: true,
                    polylineOptions : {
                        clickable : false,
                        strokeColor: "#" + lineColor,
                        strokeOpacity: lineOpacity,
                        strokeWeight: lineWidth

                    }
                });
            }

            newDirection.setDirections(response);

            newDirection.addListener('directions_changed', function() {
                hugeitMapsupdateDirectionInputsDirDrag(newDirection.getDirections());
                hugeitMapsShowHideSteps( newDirection.directions, newDirectionShowSteps, mapdirection );
            });

            hugeitMapsupdateDirectionInputsEnd( newDirectionCoords[1] );

            newDirectionStartMarker.setMap(null);

        }else{
            newDirectionCoords.splice(1, 1);
            hugeitMapsShowNotice( directionL10n.invalidDirectionPoints );
        }
    });
}

function hugeitMapsupdateDirectionInputsStart(latlng ){
    jQuery( '#direction_start_lat' ).val(latlng.lat);
    jQuery( '#direction_start_lng' ).val(latlng.lng);

    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({'latLng': latlng}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            address = results[0].formatted_address;
            jQuery("#direction_start_addr").val(address);
        }
    });
}

function hugeitMapsupdateDirectionInputsEnd(latlng ){
    jQuery( '#direction_end_lat' ).val(latlng.lat);
    jQuery( '#direction_end_lng' ).val(latlng.lng);

    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({'latLng': latlng}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            address = results[0].formatted_address;
            jQuery("#direction_end_addr").val(address);
        }
    });
}

function hugeitMapsupdateDirectionInputsDirDrag(event){
    newDirectionCoords = [
        {
            lat : event.routes[0].legs[0].start_location.lat(),
            lng : event.routes[0].legs[0].start_location.lng()
        },
        {
            lat : event.routes[0].legs[0].end_location.lat(),
            lng : event.routes[0].legs[0].end_location.lng()
        }
    ];



    jQuery( '#direction_start_lat' ).val(event.routes[0].legs[0].start_location.lat());
    jQuery( '#direction_start_lng' ).val(event.routes[0].legs[0].start_location.lng());
    jQuery( '#direction_start_addr' ).val(event.routes[0].legs[0].start_address);
    jQuery( '#direction_end_lat' ).val(event.routes[0].legs[0].end_location.lat());
    jQuery( '#direction_end_lng' ).val(event.routes[0].legs[0].end_location.lng());
    jQuery( '#direction_end_addr' ).val(event.routes[0].legs[0].end_address);
}