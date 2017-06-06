function hugeitMapsInitializeAllMaps(id, response){
    hugeitMapsLoadMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit, response.animation);
    hugeitMapsLoadMarkerMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
    hugeitMapsLoadPolygonMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
    hugeitMapsLoadPolylineMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
    hugeitMapsLoadCircleMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
    hugeitMapsLoadDirectionsMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
    hugeitMapsLoadLocatorsMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
}

var data,
    marker = [],
    infowindow,
    polygone = [],
    polyline = [],
    circle = [],
    newcirclemarker = [],
    directions = [],
    directionMarkers = [],
    geocoder;

function hugeitMapsDeleteItem(id, table, li, x) {
    debugger;
    var delete_data = {
        action: 'hugeit_maps_delete_item',
        nonce: mapL10n.delete_nonce,
        id: id,
        table: table
    };

    jQuery.post(ajaxurl, delete_data, function (response) {
        if (response.success) {
            li.remove();
        }
    }, "json")
}

jQuery(document).ready(function () {

    jQuery('#map_zoom').bind("slider:changed", function (event, data) {
        jQuery(this).parent().find('span').html(parseInt(data.value));
        jQuery(this).val(parseInt(data.value));
        map_admin_view.setZoom(parseInt(jQuery(this).val()))
    });

    jQuery('#map_width').bind("slider:changed", function (event, data) {
        jQuery(this).parent().find('span').html(parseInt(data.value) + "%");
        jQuery(this).val(parseInt(data.value));
    });

    jQuery("#map_name_tab").on("keyup change", function () {
        var name = jQuery(this).val();
        var data = {
            action: 'hugeit_maps_change_map_name',
            nonce: mapL10n.save_nonce,
            id: mapL10n.map.id,
            name: name
        };
        jQuery.post(ajaxurl, data, function (response) {
            if (response.success) {
                jQuery("#map_name").val(name);
            }
        }, 'json')
    });

    jQuery("#map_name").on("keyup change", function () {
        var name = jQuery(this).val();
        var data = {
            action: 'hugeit_maps_change_map_name',
            nonce: mapL10n.save_nonce,
            id:mapL10n.map.id,
            name: name
        };
        jQuery.post(ajaxurl, data, function (response) {
            if (response.success) {
                jQuery("#map_name_tab").val(name);
            }
        }, 'json')
    });

    jQuery(".admin_edit_section_container").on("click",".edit_list_delete_submit", function () {
        var parent = jQuery(this).parent();
        var typeelement = parent.find(".edit_list_delete_type");
        var type = typeelement.val();
        var idelement = parent.find(".edit_list_delete_id");
        var tableelement = parent.find(".edit_list_delete_table");
        var id = idelement.val();
        var table = tableelement.val();
        var li = jQuery(this).parent().parent().parent();
        var x = li.index();
        switch(type){
            case "direction":
                directions[x].setMap(null);
                if(directionMarkers[x])	{
                    for (var o = 0; o < directionMarkers[x].length; o++) {
                        directionMarkers[x][o].setMap(null);
                    }
                    directionMarkers.splice(x,1);
                }

                hugeitMapsDeleteItem(id, table, li, x);
                directions.splice(x,1);
                break;
            case "circle":
                circle[x].setMap(null);
                circle.splice(x,1);
                hugeitMapsDeleteItem(id, table, li, x);
                break;
            case "polygone":
                polygone[x].setMap(null);
                polygone.splice(x,1);
                hugeitMapsDeleteItem(id, table, li, x);
                break;
            case "polyline":
                polyline[x].setMap(null);
                polyline.splice(x,1);
                hugeitMapsDeleteItem(id, table, li, x);
                break;
            case "marker":
                marker[x].setMap(null);
                marker.splice(x,1);
                hugeitMapsDeleteItem(id, table, li, x);
                break;
            case "locator":
                hugeitMapsDeleteItem(id, table, li, x);
                break;
        }
        return false;
    });

    hugeitMapsLoadMap(
        mapL10n.map.id,
        "#"+mapL10n.map.styling_hue,
        mapL10n.map.styling_saturation,
        mapL10n.map.styling_lightness,
        mapL10n.map.styling_gamma,
        mapL10n.map.zoom,
        mapL10n.map.type,
        mapL10n.map.bike_layer,
        mapL10n.map.traffic_layer,
        mapL10n.map.transit_layer,
        mapL10n.map.animation
    );



    /* frontend dir start */

    if(typeof frontdir_options!='undefined' && frontdir_options.frontdir_enabled!=0 ) {
        /* search place */
        var map_search_place = document.getElementById("map_place_search");
        var searchBox = new google.maps.places.SearchBox(map_search_place);

        /* Bias the SearchBox results towards current map's viewport. */

        var frontMarkers = [];
        /* Listen for the event fired when the user selects a prediction and retrieve
         more details for that place. */
        searchBox.addListener('places_changed', function () {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            /* Clear out the old markers. */
            frontMarkers.forEach(function (marker) {
                marker.setMap(null);
            });
            frontMarkers = [];

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
                frontMarkers.push(new google.maps.Marker({
                    map: map_admin_view,
                    icon: icon,
                    title: place.name,
                    position: place.geometry.location
                }));

                if (place.geometry.viewport) {
                    /* Only geocodes have viewport. */
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
            if (typeof endCoords !== 'undefined' && endCoords.length) {
                hugeitMapsCreateDirection();
            }
        }


        function hugeitMapsPlaceEndPoint(latlng) {
            if (typeof newDirectionEndMarker == 'undefined') {
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
            frontMarkers.forEach(function (marker) {
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
    } /* end frontend dir */

});
function hugeitMapsLoadMap(id, hue, saturation, lightness, gamma, zoom, type, bike, traffic, transit, animation) {

    data = {
        action: 'hugeit_maps_get_info',
        map_id: id,
        processShortcode:true,
    };
    jQuery.ajax({
        url: ajaxurl,
        dataType: 'json',
        method: 'post',
        data: data,
        beforeSend: function () {
        }
    }).done(function (response) {
        initializeMap(response);
    }).fail(function () {
        console.log('Failed to load response from database');
    });
    function initializeMap(response) {
        if (response.success) {
            window.infowindow = new google.maps.InfoWindow;
            var mapInfo = response.success,
                maps = mapInfo.maps,
                trafficLayer = new google.maps.TrafficLayer(),
                bikeLayer = new google.maps.BicyclingLayer(),
                transitLayer = new google.maps.TransitLayer();
            for (var i = 0; i < maps.length; i++) {

                var info_type = maps[i].info_type;
                var pan_controller = maps[i].pan_controller;
                var zoom_controller = maps[i].zoom_controller;
                var type_controller = maps[i].type_controller;
                var scale_controller = maps[i].scale_controller;
                var street_view_controller = maps[i].street_view_controller;
                var overview_map_controller = maps[i].overview_map_controller;
                var mapcenter = new google.maps.LatLng(
                    parseFloat(maps[i].center_lat),
                    parseFloat(maps[i].center_lng)
                );


                geocoder = new google.maps.Geocoder();
                geocoder.geocode({'latLng': mapcenter}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        address = results[0].formatted_address;
                        jQuery("#map_center_addr").val(address);
                    }
                });

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
                    disableDefaultUI: true,
                    styles: styles
                };


                map_admin_view = new google.maps.Map(document.getElementById('g_map_canvas'), mapOptions);


                var map_anim;

                function huge_animate_map() {
                    var block1 = jQuery(".admin_edit_section");
                    var block2 = jQuery("#g_map_canvas");

                    block1.css({display: "block"});
                    block1.addClass("animated bounceInLeft");
                    setTimeout(function () {
                        block2.removeClass("hide");
                        if (animation == "none") {
                            map_anim = "bounceInRight";
                        } else {
                            map_anim = animation;
                        }
                        block2.addClass("animated " + map_anim);
                        google.maps.event.trigger(map_admin_view, 'resize');
                        map_admin_view.setCenter(mapcenter);

                        block1.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                            hugeitMapsNoticeOptimize();
                        });

                    }, 500);
                }

                huge_animate_map();

                jQuery("#map_animation").on("change select", function () {
                    var map_block = jQuery("#g_map_canvas");
                    map_block.removeClass(map_anim);
                    map_anim = jQuery(this).val();
                    map_block.addClass(map_anim);
                });

                var input = document.getElementById("map_center_addr");
                var autocomplete = new google.maps.places.Autocomplete(input);
                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    var addr = jQuery("#map_center_addr").val();
                    var geocoder = geocoder = new google.maps.Geocoder();
                    geocoder.geocode({'address': addr}, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            address = results[0].geometry.location;
                            map_admin_view.setCenter(address);
                            jQuery("#map_center_lat").val(address.lat());
                            jQuery("#map_center_lng").val(address.lng());
                        }
                    })
                });
                jQuery("#map_center_addr").on("change input", function () {
                    var addr = jQuery("#map_center_addr").val();
                    var geocoder = geocoder = new google.maps.Geocoder();
                    geocoder.geocode({'address': addr}, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            address = results[0].geometry.location;
                            map_admin_view.setCenter(address);
                            jQuery("#map_center_lat").val(address.lat());
                            jQuery("#map_center_lng").val(address.lng());
                        }
                    });
                });
                jQuery(".editing_heading").on("click", function () {
                    google.maps.event.trigger(map_admin_view, 'resize');
                    map_admin_view.setCenter(mapcenter);
                    map_admin_view.setZoom(parseInt(zoom));
                });
                if (type == "ROADMAP") {
                    map_admin_view.setOptions({mapTypeId: google.maps.MapTypeId.ROADMAP})
                }
                if (type == "SATELLITE") {
                    map_admin_view.setOptions({mapTypeId: google.maps.MapTypeId.SATELLITE});
                }
                if (type == "HYBRID") {
                    map_admin_view.setOptions({mapTypeId: google.maps.MapTypeId.HYBRID});
                }
                if (type == "TERRAIN") {
                    map_admin_view.setOptions({mapTypeId: google.maps.MapTypeId.TERRAIN});
                }

                if (bike) {
                    bikeLayer.setMap(map_admin_view);
                }
                if (traffic) {
                    trafficLayer.setMap(map_admin_view);
                }
                if (transit) {
                    transitLayer.setMap(map_admin_view);
                }
                jQuery(".map_layers_inputs").on("click", function () {
                    if (jQuery("#traffic_layer_enable").is(":checked")) {

                        trafficLayer.setMap(map_admin_view);
                    } else {
                        trafficLayer.setMap(null);
                    }
                    if (jQuery("#bicycling_layer_enable").is(":checked")) {

                        bikeLayer.setMap(map_admin_view);
                    } else {
                        bikeLayer.setMap(null);
                    }
                    if (jQuery("#transit_layer_enable").is(":checked")) {

                        transitLayer.setMap(map_admin_view);
                    } else {
                        transitLayer.setMap(null);
                    }
                });

                jQuery(".map_styling_options_inputs").on("change", function () {
                    hugeitMapsUpdateMapStyles();
                });

                jQuery("#styling_set_default").on("click", function () {
                    hugeitMapsSetMapDefaultStyles();
                    return false;
                });


                if (pan_controller) {
                    map_admin_view.setOptions({
                        panControl: true
                    })
                }
                else {
                    map_admin_view.setOptions({
                        panControl: false
                    })
                }
                if (zoom_controller) {
                    map_admin_view.setOptions({
                        zoomControl: true
                    })
                }
                else {
                    map_admin_view.setOptions({
                        zoomControl: false
                    })
                }
                if (type_controller) {
                    map_admin_view.setOptions({
                        mapTypeControl: true
                    })
                }
                else {
                    map_admin_view.setOptions({
                        mapTypeControl: false
                    })
                }
                if (scale_controller) {
                    map_admin_view.setOptions({
                        scaleControl: true
                    })
                }
                else {
                    map_admin_view.setOptions({
                        scaleControl: false
                    })
                }
                if (street_view_controller) {
                    map_admin_view.setOptions({
                        streetViewControl: true
                    })
                }
                else {
                    map_admin_view.setOptions({
                        streetViewControl: false
                    })
                }
                if (overview_map_controller) {
                    map_admin_view.setOptions({
                        overviewMapControl: true
                    })
                }
                else {
                    map_admin_view.setOptions({
                        overviewMapControl: false
                    })
                }

                jQuery(".map_controller_input").on("click", function () {
                    if (jQuery('#map_controller_pan').is(':checked')) {
                        map_admin_view.setOptions({
                            panControl: true
                        })
                    }
                    else {
                        map_admin_view.setOptions({
                            panControl: false
                        })
                    }
                    if (jQuery('#map_controller_zoom').is(':checked')) {
                        map_admin_view.setOptions({
                            zoomControl: true
                        })
                    }
                    else {
                        map_admin_view.setOptions({
                            zoomControl: false
                        })
                    }
                    if (jQuery('#map_controller_type').is(':checked')) {
                        map_admin_view.setOptions({
                            mapTypeControl: true
                        })
                    }
                    else {
                        map_admin_view.setOptions({
                            mapTypeControl: false
                        })
                    }
                    if (jQuery('#map_controller_scale').is(':checked')) {
                        map_admin_view.setOptions({
                            scaleControl: true
                        })
                    }
                    else {
                        map_admin_view.setOptions({
                            scaleControl: false
                        })
                    }
                    if (jQuery('#map_controller_street_view').is(':checked')) {
                        map_admin_view.setOptions({
                            streetViewControl: true
                        })
                    }
                    else {
                        map_admin_view.setOptions({
                            streetViewControl: false
                        })
                    }
                    if (jQuery('#map_controller_overview').is(':checked')) {
                        map_admin_view.setOptions({
                            overviewMapControl: true
                        })
                    }
                    else {
                        map_admin_view.setOptions({
                            overviewMapControl: false
                        })
                    }
                });
                jQuery("#map_type").on("change", function () {
                    var type = jQuery(this).val();
                    if (type == "ROADMAP") {
                        map_admin_view.setMapTypeId(google.maps.MapTypeId.ROADMAP)
                    }
                    if (type == "SATELLITE") {
                        map_admin_view.setMapTypeId(google.maps.MapTypeId.SATELLITE)
                    }
                    if (type == "HYBRID") {
                        map_admin_view.setMapTypeId(google.maps.MapTypeId.HYBRID)
                    }
                    if (type == "TERRAIN") {
                        map_admin_view.setMapTypeId(google.maps.MapTypeId.TERRAIN)
                    }
                });
                var markers = mapInfo.markers;
                for (j = 0; j < markers.length; j++) {
                    var name = markers[j].name;
                    var address = markers[j].address;
                    var anim = markers[j].animation;
                    var description = markers[j].description;
                    var     markimg = markers[j].img;
                    var img = new google.maps.MarkerImage(markimg,
                        new google.maps.Size(20, 20));
                    var point = new google.maps.LatLng(
                        parseFloat(markers[j].lat),
                        parseFloat(markers[j].lng));
                    var html = "<b>" + name + "</b> <br/>" + address;
                    if (anim == 'DROP') {
                        marker[j] = new google.maps.Marker({
                            map: map_admin_view,
                            position: point,
                            title: name,
                            icon: markimg,
                            content: description,
                            animation: google.maps.Animation.DROP
                        });
                    }
                    if (anim == 'BOUNCE') {
                        marker[j] = new google.maps.Marker({
                            map: map_admin_view,
                            position: point,
                            title: name,
                            content: description,
                            icon: markimg,
                            animation: google.maps.Animation.BOUNCE
                        });
                    }
                    if (anim == 'NONE') {
                        marker[j] = new google.maps.Marker({
                            map: map_admin_view,
                            position: point,
                            icon: markimg,
                            content: description,
                            title: name
                        });
                    }

                    HugeitMapsBindInfoWindow(marker[j], map_admin_view, infowindow, description, info_type);

                    jQuery("#map_infowindow_type").on("click", HugeitMapsBindInfoWindow(marker[j], map_admin_view, infowindow, description, jQuery("#map_infowindow_type").val()));
                }
            }
            var polygones = mapInfo.polygons;
            for (var k = 0; k < polygones.length; k++) {

                var name = polygones[k].name;
                var new_line_opacity = polygones[k].line_opacity;
                var new_line_color = "#" + polygones[k].line_color;
                var new_fill_opacity = polygones[k].fill_opacity;
                var new_line_width = polygones[k].line_width;
                var new_fill_color = "#" + polygones[k].fill_color;
                var latlngs = polygones[k].latlng;
                var hover_new_line_opacity = polygones[k].hover_line_opacity;
                var hover_new_line_color = "#" + polygones[k].hover_line_color;
                var hover_new_fill_opacity = polygones[k].hover_fill_opacity;
                var hover_new_fill_color = "#" + polygones[k].hover_fill_color;
                polygoncoords = [];
                for (var g = 0; g < latlngs.length; g++) {
                    polygonpoints = new google.maps.LatLng(parseFloat(latlngs[g].lat),
                        parseFloat(latlngs[g].lng));
                    polygoncoords.push(polygonpoints);
                }

                polygone[k] = new google.maps.Polygon({
                    paths: polygoncoords,
                    map: map_admin_view,
                    strokeOpacity: new_line_opacity,
                    strokeColor: new_line_color,
                    strokeWeight: new_line_width,
                    fillOpacity: new_fill_opacity,
                    fillColor: new_fill_color,
                    draggable: false
                });
                google.maps.event.addListener(polygone[k], 'click', function (event) {
                    var polygone_index = polygone.indexOf(this);
                    var polygone_url = polygones[polygone_index].url;
                    if (polygone_url != "") {
                        window.open(polygone_url, '_blank');
                    }
                });
                google.maps.event.addListener(polygone[k], 'mouseover', function (event) {
                    var polygone_index = polygone.indexOf(this);
                    hover_new_line_opacity = polygones[polygone_index].hover_line_opacity;
                    hover_new_line_color = "#" + polygones[polygone_index].hover_line_color;
                    hover_new_fill_opacity = polygones[polygone_index].hover_fill_opacity;
                    hover_new_fill_color = "#" + polygones[polygone_index].hover_fill_color;
                    this.setOptions({
                        strokeColor: hover_new_line_color,
                        strokeOpacity: hover_new_line_opacity,
                        fillOpacity: hover_new_fill_opacity,
                        fillColor: hover_new_fill_color,
                    });
                });
                google.maps.event.addListener(polygone[k], 'mouseout', function (event) {
                    polygone_index = polygone.indexOf(this);
                    new_line_opacity = polygones[polygone_index].line_opacity;
                    new_line_color = "#" + polygones[polygone_index].line_color;
                    new_fill_opacity = polygones[polygone_index].fill_opacity;
                    new_line_width = polygones[polygone_index].line_width;
                    new_fill_color = "#" + polygones[polygone_index].fill_color;
                    this.setOptions({
                        strokeColor: new_line_color,
                        strokeOpacity: new_line_opacity,
                        fillOpacity: new_fill_opacity,
                        fillColor: new_fill_color,
                    });
                });


            }
            var polylines = mapInfo.polylines;
            for (var q = 0; q < polylines.length; q++) {
                var name = polylines[q].name;
                var line_opacity = polylines[q].line_opacity;
                var line_color = polylines[q].line_color;
                var line_width = polylines[q].line_width;
                var latlngs = polylines[q].latlng;
                var newpolylinecoords = [];
                for (var g = 0; g < latlngs.length; g++) {
                    polylinepoints = new google.maps.LatLng(parseFloat(latlngs[g].lat),
                        parseFloat(latlngs[g].lng));
                    newpolylinecoords.push(polylinepoints)
                }
                polyline[q] = new google.maps.Polyline({
                    path: newpolylinecoords,
                    map: map_admin_view,
                    strokeColor: "#" + line_color,
                    strokeOpacity: line_opacity,
                    strokeWeight: line_width
                });
                google.maps.event.addListener(polyline[q], 'mouseover', function (event) {
                    var polyline_index = polyline.indexOf(this);
                    hover_new_line_opacity = polylines[polyline_index].hover_line_opacity;
                    hover_new_line_color = "#" + polylines[polyline_index].hover_line_color;
                    this.setOptions({
                        strokeColor: hover_new_line_color,
                        strokeOpacity: hover_new_line_opacity
                    });
                });
                google.maps.event.addListener(polyline[q], 'mouseout', function (event) {
                    polyline_index = polyline.indexOf(this);
                    new_line_opacity = polylines[polyline_index].line_opacity;
                    new_line_color = "#" + polylines[polyline_index].line_color;
                    new_line_width = polylines[polyline_index].line_width;
                    this.setOptions({
                        strokeColor: new_line_color,
                        strokeOpacity: new_line_opacity
                    });
                });
            }
            var circles = mapInfo.circles;
            for (var u = 0; u < circles.length; u++) {
                var circle_name = circles[u].name;
                var circle_center_lat = circles[u].center_lat;
                var circle_center_lng = circles[u].center_lng;
                var circle_radius = circles[u].radius;
                var circle_line_width = circles[u].line_width;
                var circle_line_color = circles[u].line_color;
                var circle_line_opacity = circles[u].line_opacity;
                var circle_fill_color = circles[u].fill_color;
                var circle_fill_opacity = circles[u].fill_opacity;
                var circle_show_marker = parseInt(circles[u].show_marker);
                circlepoint = new google.maps.LatLng(parseFloat(circles[u].center_lat),
                    parseFloat(circles[u].center_lng));
                circle[u] = new google.maps.Circle({
                    map: map_admin_view,
                    center: circlepoint,
                    title: name,
                    radius: parseInt(circle_radius),
                    strokeColor: "#" + circle_line_color,
                    strokeOpacity: circle_line_opacity,
                    strokeWeight: circle_line_width,
                    fillColor: "#" + circle_fill_color,
                    fillOpacity: circle_fill_opacity
                });
                google.maps.event.addListener(circle[u], 'mouseover', function (event) {
                    var circle_index = circle.indexOf(this);
                    hover_new_line_opacity = circles[circle_index].hover_line_opacity;
                    hover_new_line_color = "#" + circles[circle_index].hover_line_color;
                    hover_new_fill_opacity = circles[circle_index].hover_fill_opacity;
                    hover_new_fill_color = "#" + circles[circle_index].hover_fill_color;
                    this.setOptions({
                        strokeColor: hover_new_line_color,
                        strokeOpacity: hover_new_line_opacity,
                        fillOpacity: hover_new_fill_opacity,
                        fillColor: hover_new_fill_color
                    });
                });
                google.maps.event.addListener(circle[u], 'mouseout', function (event) {
                    circle_index = circle.indexOf(this);
                    new_line_opacity = circles[circle_index].line_opacity;
                    new_line_color = "#" + circles[circle_index].line_color;
                    new_fill_opacity = circles[circle_index].fill_opacity;
                    new_fill_color = "#" + circles[circle_index].fill_color;
                    this.setOptions({
                        strokeColor: new_line_color,
                        strokeOpacity: new_line_opacity,
                        fillOpacity: new_fill_opacity,
                        fillColor: new_fill_color
                    });
                });

                if (circle_show_marker) {
                    newcirclemarker[i] = new google.maps.Marker({
                        position: circlepoint,
                        map: map_admin_view,
                        title: circle_name
                    })
                }
            }

            var info_directions = mapInfo.directions;
            for( var d = 0; d < info_directions.length; d++ ){
                var dir_name = info_directions[d].name;
                var dir_start_lat = info_directions[d].start_lat;
                var dir_start_lng = info_directions[d].start_lng;
                var dir_end_lat = info_directions[d].end_lat;
                var dir_end_lng = info_directions[d].end_lng;
                var dir_show_steps = info_directions[d].show_steps;
                var dir_travel_mode = info_directions[d].travel_mode;
                var dir_line_width = info_directions[d].line_width;
                var dir_line_color = info_directions[d].line_color;
                var dir_line_opacity = info_directions[d].line_opacity;
                var dir_show_steps = info_directions[d].show_steps == 1;

                var request = {
                    destination: new google.maps.LatLng(parseFloat(dir_end_lat),
                        parseFloat(dir_end_lng)),
                    origin: new google.maps.LatLng(parseFloat(dir_start_lat),
                        parseFloat(dir_start_lng)),
                    travelMode: google.maps.TravelMode[dir_travel_mode]
                };
                (function(d){
                    directionsService.route(request, function(response, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            directions[d] = new google.maps.DirectionsRenderer({
                                map: map_admin_view,
                                draggable: false,
                                preserveViewport : true,
                                polylineOptions : {
                                    clickable : false,
                                    strokeColor: "#" + info_directions[d].line_color,
                                    strokeOpacity: info_directions[d].line_opacity,
                                    strokeWeight: info_directions[d].line_width
                                }
                            });
                            directions[d].setDirections(response);

                            if( info_directions[d].show_steps){
                                if(directionMarkers[d]){
                                    for (var o = 0; o < directionMarkers[d].length; o++) {
                                        directionMarkers[d][o].setMap(null);
                                    }
                                }else{
                                    directionMarkers[d] = [];
                                }
                                var newRoute = directions[d].directions.routes[0].legs[0];
                                for (var w = 0; w < newRoute.steps.length; w++) {
                                    var marker = directionMarkers[d][w] = directionMarkers[d][w] || new google.maps.Marker;
                                    marker.setMap(map_admin_view);
                                    marker.setPosition(newRoute.steps[w].start_location);
                                    hugeitMapsAttachInstructionText(
                                        stepDisplay, marker, newRoute.steps[w].instructions, map_admin_view);
                                }
                            }
                        }
                    });
                }(d));
            }



        }

    }
}
function hugeitMapsSetMapDefaultStyles() {
    jQuery("#g_map_styling_hue").val("FFFFFF").css('backgroundColor','#FFFFFF');
    jQuery("#g_map_styling_lightness").simpleSlider("setValue", "0");
    jQuery("#g_map_styling_saturation").simpleSlider("setValue", "0");
    jQuery("#g_map_styling_gamma").simpleSlider("setValue", "1");

    var map_hue = "";
    var map_lightness = jQuery("#g_map_styling_lightness").val();
    var map_saturation = jQuery("#g_map_styling_saturation").val();
    var map_gamma = jQuery("#g_map_styling_gamma").val();
    var id = jQuery("#map_id").val();
    var styles = [
        {
            stylers: [
                {hue: map_hue},
                {saturation: map_saturation},
                {lightness: map_lightness},
                {gamma: map_gamma}
            ]
        }
    ];
    map_admin_view.setOptions({styles: styles});
    map.setOptions({styles: styles});
    map_marker_edit.setOptions({styles: styles});
    mappolygone.setOptions({styles: styles});
    map_polygone_edit.setOptions({styles: styles});
    mappolyline.setOptions({styles: styles});
    map_polyline_edit.setOptions({styles: styles});
    mapcircle.setOptions({styles: styles});
    map_circle_edit.setOptions({styles: styles});
    var default_data = {
        action: "hugeit_maps_save_stylings",
        nonce: mapL10n.stylingNonce,
        map_id: id,
        map_hue: map_hue,
        map_lightness: map_lightness,
        map_saturation: map_saturation,
        map_gamma: map_gamma
    };
    jQuery("#styling_set_default").parent().find( '.spinner' ).css( 'visibility', 'visible' );
    jQuery.post(ajaxurl, default_data, function (response) {
        jQuery("#styling_set_default").parent().find( '.spinner' ).css( 'visibility', 'hidden' );
        if (response.success) {
            hugeitMapsInitializeAllMaps( id, response );
        }
    }, "json");

}
function hugeitMapsUpdateMapStyles() {

    var map_hue = "#" + jQuery("#g_map_styling_hue").val();
    if (map_hue == "#FFFFFF") {
        map_hue = "";
    }
    var map_lightness = jQuery("#g_map_styling_lightness").val();
    var map_saturation = jQuery("#g_map_styling_saturation").val();
    var map_gamma = jQuery("#g_map_styling_gamma").val();
    var styles = [
        {
            stylers: [
                {hue: map_hue},
                {saturation: map_saturation},
                {lightness: map_lightness},
                {gamma: map_gamma}
            ]
        }
    ];
    map_admin_view.setOptions({styles: styles})
}
function HugeitMapsBindInfoWindow(marker, map, infowindow, description, info_type) {
    if (info_type == "click") {
        google.maps.event.addListener(marker, 'click', function () {
            infowindow.setContent(description);
            infowindow.open(map, marker);
        });
    }
    if (info_type == "hover") {
        google.maps.event.addListener(marker, 'mouseover', function () {
            infowindow.setContent(description);
            infowindow.open(map, marker);
        });
        google.maps.event.addListener(marker, 'mouseout', function () {
            infowindow.close(map, marker);
        });
    }

}
function hugeitMapsAttachInstructionText(stepDisplay, marker, text, map) {
    google.maps.event.addListener(marker, 'click', function() {
        /*Open an info window when the marker is clicked on, containing the text of the step.*/
        stepDisplay.setContent(text);
        stepDisplay.open(map, marker);
    });
}