var hugeitMaps = [];
function hugeitMapsBindInfoWindow(marker, map, infowindow, description, info_type, openOnload) {
    if(openOnload){
        google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
            infowindow.setContent(description);
            infowindow.open(map, marker);
        });
    }
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




jQuery(document).ready(function () {

    function hugeitMapsInitializeMap( elementId ) {
        var element = jQuery( "#"+elementId ),
            marker = [],
            polygone = [],
            polyline = [],
            polylinepoints,
            newpolylinecoords = [],
            polygonpoints,
            polygoncoords = [],
            directions = [],
            directionMarkers = [],
            newcircle = [],
            infowindows = [],
            infowindow = new google.maps.InfoWindow,
            newcirclemarker = [],
            circlepoint,
            width = element.width(),
            height = element.height(),
            maptypecontrolposition = element.data('map-type-position'),
            frontdirEnabled = element.data('frontdir-enabled'),
            locatorEnabled = element.data('locator-enabled'),
            div = parseInt(width) / parseInt(height),
            trafficLayer = new google.maps.TrafficLayer(),
            bikeLayer = new google.maps.BicyclingLayer(),
            transitLayer = new google.maps.TransitLayer(),
            dataMapId = element.data('map_id'),
            dataName = element.data('name'),
            dataType = element.data('type'),
            dataZoom = element.data('zoom'),
            dataMinZoom = element.data('min-zoom'),
            dataMaxZoom = element.data('max-zoom'),
            dataBorderRadius = element.data('border-radius'),
            dataCenterLat = element.data('center-lat'),
            dataCenterLng = element.data('center-lng'),
            dataPanController = parseInt(element.data('pan-controller')),
            dataZoomController = parseInt(element.data('zoom-controller')),
            dataTypeController = parseInt(element.data('type-controller')),
            dataScaleController = parseInt(element.data('scale-controller')),
            dataStreetViewController = parseInt(element.data('street-view-controller')),
            dataOverviewMapController = parseInt(element.data('overview-map-controller')),
            dataWidth = element.data('width'),
            dataHeight = element.data('height'),
            dataAlign = element.data('align'),
            dataInfoType = element.data('info-type'),
            dataAnimation = element.data('animation'),
            dataLanguage = element.data('language'),
            dataDraggable = element.data('draggable'),
            dataWheelScroll = element.data('wheel-scroll'),
            dataTrafficLayer = element.data('traffic-layer'),
            dataBikeLayer = element.data('bike-layer'),
            dataTransitLayer = element.data('transit-layer'),
            dataStylingHue = element.data('styling-hue'),
            dataStylingLightness = element.data('styling-lightness'),
            dataStylingGamma = element.data('styling-gamma'),
            dataStylingSaturation = element.data('styling-saturation');
            dataOpenInfowindowsOnload = element.data('open-infowindows-onload');


        jQuery(document).on("click tap drag scroll", function (e) {
            if (window.matchMedia('(max-width:768px)').matches) {
                var container = jQuery(element);
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    front_end_map.setOptions({
                        draggable: false,
                        scrollwheel: false
                    });
                } else {
                    front_end_map.setOptions({
                        draggable:dataDraggable,
                        scrollwheel:dataWheelScroll,
                    });
                }
            }
        });


        jQuery(window).on("resize", function () {
            var newwidth = element.width();
            var newheight = parseInt(newwidth) / parseInt(div) + "px";
            element.height(newheight);
        });


        var center_coords = new google.maps.LatLng(dataCenterLat, dataCenterLng);
        var center_coords_old=center_coords;

        var styles = [
            {
                stylers: [
                    {hue: "#" + dataStylingHue},
                    {saturation: dataStylingSaturation},
                    {lightness: dataStylingLightness},
                    {gamma: dataStylingGamma}
                ]
            }
        ];


        if(maptypecontrolposition=='left'){
            maptypecontrolposition=google.maps.ControlPosition.TOP_RIGHT;
        }
        else{
            maptypecontrolposition=google.maps.ControlPosition.TOP_LEFT;
        }

        var frontEndMapOptions = {
            zoom: parseInt(dataZoom),
            center: center_coords,
            disableDefaultUI: true,
            styles: styles,
            panControl: dataPanController,
            zoomControl: dataZoomController,
            mapTypeControl: dataTypeController,
            scaleControl: dataScaleController,
            streetViewControl: dataStreetViewController,
            overviewMapControl: dataOverviewMapController,
            mapTypeId: google['maps']['MapTypeId'][dataType],
            minZoom: dataMinZoom,
            maxZoom: dataMaxZoom,
            mapTypeControlOptions: {
                position: maptypecontrolposition
            },
            fullscreenControl: true

        };

        var front_end_map = new google.maps.Map(document.getElementById(elementId), frontEndMapOptions);

        if (window.matchMedia('(max-width:768px)').matches) {
            front_end_map.setOptions({
                draggable: false,
                scrollwheel: false
            });
        } else {
            front_end_map.setOptions({
                draggable: dataDraggable,
                scrollwheel: dataWheelScroll
            });
        }
        function hugeitMapsFrontAnimations() {
            var map_anim;
            if (dataAnimation == "none") {
                map_anim = "";
            } else {
                map_anim = dataAnimation;
            }
            element.removeClass("hide");
            element.addClass("animated " + map_anim);
            google.maps.event.trigger(front_end_map, 'resize');
            front_end_map.setCenter(center_coords);
        }

        if (jQuery(window).scrollTop() <= element.parent().offset().top
            && jQuery(window).scrollTop() + jQuery(window).height() >= element.parent().offset().top) {

            setTimeout(function () {
                hugeitMapsFrontAnimations();
            }, 500);

        }


        jQuery(window).scroll(function () {
            if (jQuery(window).scrollTop() <= element.parent().offset().top
                && jQuery(window).scrollTop() + jQuery(window).height() >= element.parent().offset().top
            ) {
                setTimeout(function () {
                    hugeitMapsFrontAnimations();
                }, 500);
            }
        });

        if (dataBikeLayer) {
            bikeLayer.setMap(front_end_map);
        }
        if (dataTrafficLayer) {
            trafficLayer.setMap(front_end_map);
        }
        if (dataTransitLayer) {
            transitLayer.setMap(front_end_map);
        }
        var front_end_data = {
            action: 'hugeit_maps_get_info',
            map_id: dataMapId ,
            processShortcode:true,
        };


        jQuery.ajax({
            url: ajaxurl,
            dataType: 'json',
            method: 'post',
            data: front_end_data,
            beforeSend: function () {
            }
        }).done(function (response) {
            hugeitMapsInitializeMap(response);
            locStores = response.success.locators;
        }).fail(function () {
            console.log('Failed to load response from database');
        });
        function hugeitMapsInitializeMap(response) {
            if (response.success) {
                var mapInfo = response.success;
                var markers = mapInfo.markers;
                for (var i = 0; i < markers.length; i++) {
                    var name = markers[i].name;
                    var address = markers[i].address;
                    var anim = markers[i].animation;
                    var description = markers[i].description;
                    var markimg = markers[i].img;
                    var img = new google.maps.MarkerImage(markimg,
                        new google.maps.Size(20, 20));
                    var point = new google.maps.LatLng(
                        parseFloat(markers[i].lat),
                        parseFloat(markers[i].lng));
                    var html = "<b>" + name + "</b> <br/>" + address;
                    marker[i] = new google.maps.Marker({
                        map: front_end_map,
                        position: point,
                        title: name,
                        icon: markimg,
                        content: description,
                        animation: google['maps']['Animation'][anim]
                    });
                    var currentInfoWindow;

                    if(dataOpenInfowindowsOnload){
                        infowindow = infowindows[i] = new google.maps.InfoWindow;
                    }else{
                        infowindow = infowindow;
                    }

                    hugeitMapsBindInfoWindow(marker[i], front_end_map, infowindow, description, dataInfoType ,dataOpenInfowindowsOnload);
                }

                var polygones = mapInfo.polygons;
                for (var i = 0; i < polygones.length; i++) {
                    var name = polygones[i].name;
                    var line_opacity = polygones[i].line_opacity;
                    var line_color = "#" + polygones[i].line_color;
                    var fill_opacity = polygones[i].fill_opacity;
                    var line_width = polygones[i].line_width;
                    var fill_color = "#" + polygones[i].fill_color;
                    var latlngs = polygones[i].latlng;
                    polygoncoords = [];
                    for (var j = 0; j < latlngs.length; j++) {
                        polygonpoints = new google.maps.LatLng(parseFloat(latlngs[j].lat),
                            parseFloat(latlngs[j].lng))
                        polygoncoords.push(polygonpoints)
                    }
                    polygone[i] = new google.maps.Polygon({
                        paths: polygoncoords,
                        map: front_end_map,
                        strokeOpacity: line_opacity,
                        strokeColor: line_color,
                        strokeWeight: line_width,
                        fillOpacity: fill_opacity,
                        fillColor: fill_color,
                        draggable: false
                    });
                    google.maps.event.addListener(polygone[i], 'click', function (event) {
                        var polygone_index = polygone.indexOf(this);
                        var polygone_url = polygones[polygone_index].url;
                        if (polygone_url != "") {
                            window.open(polygone_url, '_blank');
                        }
                    });
                    google.maps.event.addListener(polygone[i], 'mouseover', function (event) {
                        var polygone_index = polygone.indexOf(this);
                        hover_new_line_opacity = polygones[polygone_index].hover_line_opacity;
                        hover_new_line_color = "#" + polygones[polygone_index].hover_line_color;
                        hover_new_fill_opacity = polygones[polygone_index].hover_fill_opacity;
                        hover_new_fill_color = "#" + polygones[polygone_index].hover_fill_color;
                        this.setOptions({
                            strokeColor: hover_new_line_color,
                            strokeOpacity: hover_new_line_opacity,
                            fillOpacity: hover_new_fill_opacity,
                            fillColor: hover_new_fill_color
                        });
                    });
                    google.maps.event.addListener(polygone[i], 'mouseout', function (event) {
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
                            fillColor: new_fill_color
                        });
                    })
                }
                var polylines = mapInfo.polylines;
                for (var i = 0; i < polylines.length; i++) {
                    var name = polylines[i].name;
                    var line_opacity = polylines[i].line_opacity;
                    var line_color = polylines[i].line_color;
                    var line_width = polylines[i].line_width;
                    var latlngs = polylines[i].latlng;
                    newpolylinecoords = [];
                    for (var j = 0; j < latlngs.length; j++) {
                        polylinepoints = new google.maps.LatLng(parseFloat(latlngs[j].lat),
                            parseFloat(latlngs[j].lng));
                        newpolylinecoords.push(polylinepoints)
                    }
                    polyline[i] = new google.maps.Polyline({
                        path: newpolylinecoords,
                        map: front_end_map,
                        strokeColor: "#" + line_color,
                        strokeOpacity: line_opacity,
                        strokeWeight: line_width
                    });
                    google.maps.event.addListener(polyline[i], 'mouseover', function (event) {
                        var polyline_index = polyline.indexOf(this);
                        hover_new_line_opacity = polylines[polyline_index].hover_line_opacity;
                        hover_new_line_color = "#" + polylines[polyline_index].hover_line_color;
                        hover_new_fill_opacity = polylines[polyline_index].hover_fill_opacity;
                        hover_new_fill_color = "#" + polylines[polyline_index].hover_fill_color;
                        this.setOptions({
                            strokeColor: hover_new_line_color,
                            strokeOpacity: hover_new_line_opacity,
                            fillOpacity: hover_new_fill_opacity,
                            fillColor: hover_new_fill_color
                        });
                    })
                    google.maps.event.addListener(polyline[i], 'mouseout', function (event) {
                        polyline_index = polyline.indexOf(this);
                        new_line_opacity = polylines[polyline_index].line_opacity;
                        new_line_color = "#" + polylines[polyline_index].line_color;
                        new_line_width = polylines[polyline_index].line_width;
                        this.setOptions({
                            strokeColor: new_line_color,
                            strokeOpacity: new_line_opacity
                        });
                    })
                }
                var info_directions = mapInfo.directions;
                for( var d = 0; d < info_directions.length; d++ ){
                    var dir_start_lat = info_directions[d].start_lat;
                    var dir_start_lng = info_directions[d].start_lng;
                    var dir_end_lat = info_directions[d].end_lat;
                    var dir_end_lng = info_directions[d].end_lng;
                    var dir_travel_mode = info_directions[d].travel_mode;
                    var directionsService = new google.maps.DirectionsService();
                    var stepDisplay = new google.maps.InfoWindow;
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
                                    map: front_end_map,
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
                                        marker.setMap(front_end_map);
                                        marker.setPosition(newRoute.steps[w].start_location);
                                        hugeitMapsAttachInstructionText(
                                            stepDisplay, marker, newRoute.steps[w].instructions, front_end_map);
                                    }

                                }
                            }
                        });
                    }(d));
                }
                var circles = mapInfo.circles;
                for (var i = 0; i < circles.length; i++) {
                    var circle_name = circles[i].name;
                    var circle_center_lat = circles[i].center_lat;
                    var circle_center_lng = circles[i].center_lng;
                    var circle_radius = circles[i].radius;
                    var circle_line_width = circles[i].line_width;
                    var circle_line_color = circles[i].line_color;
                    var circle_line_opacity = circles[i].line_opacity;
                    var circle_fill_color = circles[i].fill_color;
                    var circle_fill_opacity = circles[i].fill_opacity;
                    var circle_show_marker = parseInt(circles[i].show_marker);
                    circlepoint = new google.maps.LatLng(parseFloat(circles[i].center_lat),
                        parseFloat(circles[i].center_lng));
                    newcircle[i] = new google.maps.Circle({
                        map: front_end_map,
                        center: circlepoint,
                        title: name,
                        radius: parseInt(circle_radius),
                        strokeColor: "#" + circle_line_color,
                        strokeOpacity: circle_line_opacity,
                        strokeWeight: circle_line_width,
                        fillColor: "#" + circle_fill_color,
                        fillOpacity: circle_fill_opacity
                    });
                    if (circle_show_marker) {
                        newcirclemarker[i] = new google.maps.Marker({
                            position: circlepoint,
                            map: front_end_map,
                            title: circle_name,
                        });
                    }
                    google.maps.event.addListener(newcircle[i], 'mouseover', function (event) {
                        var circle_index = newcircle.indexOf(this);
                        hover_new_line_opacity = circles[circle_index].hover_line_opacity;
                        hover_new_line_color = "#" + circles[circle_index].hover_line_color;
                        hover_new_fill_opacity = circles[circle_index].hover_fill_opacity;
                        hover_new_fill_color = "#" + circles[circle_index].hover_fill_color;
                        this.setOptions({
                            strokeColor: hover_new_line_color,
                            strokeOpacity: hover_new_line_opacity,
                            fillOpacity: hover_new_fill_opacity,
                            fillColor: hover_new_fill_color,
                        });
                    });
                    google.maps.event.addListener(newcircle[i], 'mouseout', function (event) {
                        circle_index = newcircle.indexOf(this);
                        new_line_opacity = circles[circle_index].line_opacity;
                        new_line_color = "#" + circles[circle_index].line_color;
                        new_fill_opacity = circles[circle_index].fill_opacity;
                        new_fill_color = "#" + circles[circle_index].fill_color;
                        this.setOptions({
                            strokeColor: new_line_color,
                            strokeOpacity: new_line_opacity,
                            fillOpacity: new_fill_opacity,
                            fillColor: new_fill_color,
                        });
                    });
                }
            }
        }

        /* frontend dir start */
        if(frontdirEnabled!=0){
            var map_id=dataMapId;
            var map_search_place = document.getElementById("hg-map_place_search_"+map_id);
            var searchBox = new google.maps.places.SearchBox(map_search_place);

            /* Bias the SearchBox results towards current map's viewport. */
            front_end_map.addListener('bounds_changed', function () {
                searchBox.setBounds(front_end_map.getBounds());
            });

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
                        map: front_end_map,
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
                    center_coords=new google.maps.LatLng(place.geometry.location.lat(),place.geometry.location.lng());

                });
                front_end_map.fitBounds(bounds);
            });


            /*directions */
            var VH = {};//Variable Holder Array
            VH["newDirectionMode"+map_id]='DRIVING';
            VH["directionsService"+map_id]=new google.maps.DirectionsService();
            VH["map_direction_start"+map_id]=document.getElementById("hg-map_direction_start_"+map_id);
            VH["map_direction_end"+map_id]=document.getElementById("hg-map_direction_end_"+map_id);

            VH["newDirectionUnit"+map_id]=google.maps.UnitSystem.METRIC;

            VH["autocomplete_start"+map_id]=new google.maps.places.Autocomplete(document.getElementById("hg-map_direction_start_"+map_id));
            VH["autocomplete_end"+map_id]=new google.maps.places.Autocomplete(document.getElementById("hg-map_direction_end_"+map_id));

            VH["directionsDisplay"+map_id]=new google.maps.DirectionsRenderer({ 'map': front_end_map });

            /** Handle changing start point of direction */
            google.maps.event.addListener(VH['autocomplete_start'+map_id], 'place_changed', function () {
                var addr = jQuery("#hg-map_direction_start_"+map_id).val();
                geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': addr}, function (results, status) {

                    hugeitMapsUpdateDirectionInputs('start', results[0].geometry.location,map_id);

                    hugeitMapsPlaceMarker('start',results[0].geometry.location,map_id,true);

                    center_coords=results[0].geometry.location;
                    front_end_map.setCenter(center_coords);
                });
            });


            /** Handle changing end point of direction */
            google.maps.event.addListener(VH['autocomplete_end'+map_id], 'place_changed', function () {
                var addr = jQuery("#hg-map_direction_end_"+map_id).val();
                geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': addr}, function (results, status) {

                    hugeitMapsUpdateDirectionInputs('end',results[0].geometry.location,map_id);

                    hugeitMapsPlaceMarker('end',results[0].geometry.location,map_id,true);

                    center_coords=results[0].geometry.location;
                    front_end_map.setCenter(center_coords);
                });
            });

            google.maps.event.addListener(front_end_map, 'rightclick', function (event) {
                if(jQuery('#hg-directions-block-'+map_id).css('display')=='block'){
                    latlng=event.latLng;
                    if (typeof VH['startCoords'+map_id] == 'undefined' ) {
                        hugeitMapsUpdateDirectionInputs('start',latlng,map_id);
                        hugeitMapsPlaceMarker('start',latlng,map_id,true);
                    }
                    else{
                        hugeitMapsUpdateDirectionInputs('end',latlng,map_id);
                        hugeitMapsPlaceMarker('end',latlng,map_id,true);
                    }
                }

            });

            function hugeitMapsUpdateDirectionInputs(startend,latlng,map_id) {
                jQuery('#hg-map_direction_'+startend+'_lat_'+map_id).val(latlng.lat);
                jQuery('#hg-map_direction_'+startend+'_lng_'+map_id).val(latlng.lng);

                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'latLng': latlng}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        address = results[0].formatted_address;
                        jQuery("#hg-map_direction_"+startend+"_"+map_id).val(address);
                    }
                });

            }



            function hugeitMapsPlaceMarker(startend,latlng,map_id,createDirection) {

                if(startend=='start'){
                    VH['newDirectionStartMarker'+map_id] = new google.maps.Marker({
                        draggable: true,
                        position: latlng,
                        map: front_end_map,
                        icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'
                    });


                    google.maps.event.addListener(VH['newDirectionStartMarker'+map_id], "dragend", function (event) {
                        hugeitMapsUpdateDirectionInputs('start', event.latLng,map_id);
                    });

                    VH['startCoords'+map_id] = { lat: latlng.lat(),lng: latlng.lng()}
                }else if(startend=='end'){
                    VH['newDirectionEndMarker'+map_id] = new google.maps.Marker({
                        draggable: true,
                        position: latlng,
                        map: front_end_map,
                        icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'
                    });


                    google.maps.event.addListener(VH['newDirectionEndMarker'+map_id], "dragend", function (event) {
                        hugeitMapsUpdateDirectionInputs('end',event.latLng, map_id);
                    });

                    VH['endCoords'+map_id] =  {lat: latlng.lat(),lng: latlng.lng() };
                }


                /* end point picked */
                if (createDirection && typeof VH['startCoords'+map_id] !== 'undefined' && typeof VH['endCoords'+map_id] !== 'undefined') {
                    hugeitMapsCreateDirection(map_id);
                }
            }

            function get_direction_class(text){
                var possibleClasses=['right','left','southeast','southwest','northeast','northwest','merge','roundabout'];
                for (var i = 0; i < possibleClasses.length; i++) {
                    if (text.indexOf(possibleClasses[i]) >= 0){
                        return possibleClasses[i];
                    }
                }

                return 'straight';


            }



            function hugeitMapsCreateDirection(map_id) {

                if(typeof VH['endCoords'+map_id]=='undefined' || typeof VH['startCoords'+map_id]=='undefined'){
                    return;
                }
                var request = {
                    destination: VH['endCoords'+map_id],
                    origin: VH['startCoords'+map_id],
                    travelMode: google.maps.TravelMode[VH['newDirectionMode'+map_id]],
                    unitSystem: VH['newDirectionUnit'+map_id],
                };

                VH['directionsService'+map_id].route(request, function (response, status) {

                    if (status == google.maps.DirectionsStatus.OK) {
                        if (typeof VH['newDirection'+map_id] !== 'undefined') {
                             VH['newDirection'+map_id].setMap(null);

                        }

                        VH['newDirection'+map_id] = new google.maps.DirectionsRenderer({
                                map: front_end_map,
                                draggable: false,
                                polylineOptions: {
                                    clickable: false,
                                    strokeColor: "#00a0d2",
                                    strokeOpacity: 1,
                                    strokeWeight: 3
                                }
                            });

                        VH['newDirectionStartMarker'+map_id].setMap(null);
                        VH['newDirectionEndMarker'+map_id].setMap(null);

                        VH['newDirection'+map_id].setDirections(response);

                        var available_routes = response.routes;

                        var html = '';
                        jQuery.each(available_routes, function (k, available_route) {
                            var legs = available_route.legs;
                            var distance = legs[0].distance.text;
                            var duration = legs[0].duration.text;
                            var summary = available_route.summary;
                            var steps = legs[0].steps;

                            var direction_text = '<div class="hgit-back">Back</div><ul>';
                            jQuery.each(steps, function (f, step) {
                                var step_class= get_direction_class(step.instructions);
                                direction_text += '<li class="'+step_class+'">'+step.instructions + '</li>';
                            })
                            direction_text+='</ul>';

                            instructions_box_height=jQuery('#'+elementId).height();


                            html += '<div class="hg-dir-info-row"><span class="hg-dir-name '+VH['newDirectionMode'+map_id]+'"><span>Via ' + summary + '</span></span><span class="hg-dir-dist"> ' + distance + '</span> <span class="hg-dir-dur"> ' + duration + '</span></div> <div class="hg-direction-instructions" style="overflow:hidden;height:'+instructions_box_height+'px">'+direction_text+'</div>';


                            jQuery('#hg-dir-info-block-'+map_id).html(html);

                        });

                        VH['newDirection'+map_id].addListener('directions_changed', function () {
                            /* for now direction dragging disabled */
                        });

                    } else if (status == 'ZERO_RESULTS') {
                        jQuery('#hg-dir-info-block-'+map_id+' .hg-dir-info-row').text('No Routes Available');
                    }
                    else {
                    }
                });
            }

            jQuery(document).on('click','#hg-dir-info-block-'+map_id+' .hg-dir-name>span', function () {
                    jQuery(this).closest('.hg-dir-info-block').toggleClass('hg-showInstructions');
            });



            /* reverse destination origin points */
            jQuery(document).on('click','#hg-directions-block-'+map_id+' .hg-widget-directions-reverse', function () {
                var lat1 = jQuery('#hg-map_direction_start_lat_'+map_id).val();
                var lng1 = jQuery('#hg-map_direction_start_lng_'+map_id).val();
                latlng1=new google.maps.LatLng(lat1, lng1);

                var lat2 = jQuery('#hg-map_direction_end_lat_'+map_id).val();
                var lng2 = jQuery('#hg-map_direction_end_lng_'+map_id).val();
                latlng2=new google.maps.LatLng(lat2, lng2);

                hugeitMapsUpdateDirectionInputs('start',latlng2,map_id);
                hugeitMapsPlaceMarker('start',latlng2,map_id,false);

                hugeitMapsUpdateDirectionInputs('end',latlng1,map_id);
                hugeitMapsPlaceMarker('end',latlng1,map_id,false);

                hugeitMapsCreateDirection(map_id);

            })

            jQuery(document).on('click','#hg-directions-block-'+map_id+' .hg-dist-units li', function () {
                jQuery(this).closest('.hg-dist-units').find('li').removeClass('checked');
                jQuery(this).addClass('checked');

                if(jQuery(this).attr('data-unit')=='mile'){
                    VH['newDirectionUnit'+map_id] = google.maps.UnitSystem.IMPERIAL;
                }
                else{
                    VH['newDirectionUnit'+map_id] = google.maps.UnitSystem.METRIC;
                }

                hugeitMapsCreateDirection(map_id);

            })

            /* hide instructions block */
            jQuery(document).on('click', '.hgit-back', function () {
                jQuery(this).closest('.hg-dir-info-block').removeClass('hg-showInstructions');
            })


            /* display directions block */
            jQuery(document).on('click', '.hg-searchbox-directions', function () {
                frontMarkers.forEach(function (marker) {
                    marker.setMap(null);
                });
                jQuery(this).closest('.hg-search-box').find('.hg-show-directions').removeClass('shown-block').addClass('hidden-block');
                jQuery(this).closest('.hg-search-box').find('.hg-directions-block').removeClass('hidden-block').addClass('shown-block');
            })

            /* change travel mode */
            jQuery(document).on('click', '#hg-travel-mode-'+map_id+' ul li', function () {
                jQuery('#hg-travel-mode-'+map_id+' ul li').removeClass('current');
                jQuery(this).addClass('current');
                VH['newDirectionMode'+map_id] = jQuery(this).attr('data-mode').toUpperCase();

                hugeitMapsCreateDirection(map_id);
            })

            /* reset directions block */
            jQuery(document).on('click', '#hg-directions-block-'+map_id+' .hg-close-dir-window', function () {
                /* hide direction, reset map */

                jQuery('#hg-show-directions-'+map_id).removeClass('hidden-block').addClass('shown-block');
                jQuery('#hg-directions-block-'+map_id).removeClass('shown-block').addClass('hidden-block');

                if(typeof VH['newDirection'+map_id] !=='undefined')VH['newDirection'+map_id].setMap(null);

                front_end_map.setCenter(center_coords_old);
                front_end_map.setZoom(dataZoom);
            })

            /* hide directions block */
            jQuery(document).on('click', '#hg-directions-block-'+map_id+' .hg-hide-dir-window', function () {
                /* hide direction, reset map */

                if(jQuery(this).closest('.hg-directions-block').hasClass('hidden')){
                    animate='0';
                } else{
                    animate='-'+jQuery(this).closest('.hg-directions-block').width();
                }
                if(jQuery(this).closest('.hg-search-box').hasClass('right')){
                    jQuery(this).closest('.hg-search-box').animate({
                        right: animate,
                    }, 500, function() {
                        jQuery(this).find('.hg-directions-block').toggleClass('hidden');
                    });
                }
                else{
                    jQuery(this).closest('.hg-search-box').animate({
                        left: animate,
                    }, 500, function() {
                        jQuery(this).find('.hg-directions-block').toggleClass('hidden');
                    });
                }


            })

            jQuery(document).on('click', '#hg-show-directions-'+map_id+' .hg-hide-search-window', function () {
                /* hide direction, reset map */

                if(jQuery(this).closest('.hg-show-directions').hasClass('hidden')){
                    animate='0';
                } else{
                    animate='-68px';
                }
                jQuery(this).closest('.hg-search-box').animate({
                        top: animate,
                    }, 500, function() {
                        jQuery(this).find('.hg-show-directions').toggleClass('hidden');
                });



            })
        }/* frontend dir end */


        /* locator start */
        if (locatorEnabled != 0) {
            var locpOptions = {
                map: front_end_map,
                strokeColor: "#00FF00",
                strokeOpacity: 0.9 ,
                strokeWeight: 4
            };
            var locDirectionsService = new google.maps.DirectionsService;
            var locDirectionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true, polylineOptions: locpOptions});
            var locRouteInfowindow = new google.maps.InfoWindow;
            var locClosest,locClosetPosition,def,locClosetAddress,locInfoWindow,locCurrent,locMarker,finalStores = [],locClosetArr=[],locMarkers=[];
            var locBounds = new google.maps.LatLngBounds();
            var locMap_id = dataMapId;
            var input = document.getElementById('searchLocator_' + locMap_id);
            var autocomplete = new google.maps.places.Autocomplete(input);
            var searchBox = new google.maps.places.SearchBox(input);

            function calcDistance(pointA, pointB) {

                return (google.maps.geometry.spherical.computeDistanceBetween(pointA, pointB) / 1000).toFixed(2);

            }

            function clearDistance() {
                for(var i in locMarkers){
                    if(locMarkers[i].get('name')=='closest'){
                        locMarkers[i].setMap(null);
                        locMarkers[i].length=0;
                    }

                    if(locCurrent){
                        locCurrent.setMap(null);
                        locCurrent.length = 0;
                    }

                }
            }

            function clearLocations() {
                for(var i in locMarkers){
                    locMarkers[i].setMap(null);
                }
                locMarkers.length= 0;
            }

            function clearDirections(){
                locDirectionsDisplay.setMap(null);
            }

            jQuery(document).on("click", "#submitLocator_" + locMap_id, function () {
                var locAddress = jQuery("#searchLocator_" + locMap_id).val();
                var locRadius = jQuery("#locatorRadius_" + locMap_id).val();
                if (locAddress != "" && locRadius != "") {
                    finalStores = [];
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({'address': locAddress}, function (result, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            fromLatLng = new google.maps.LatLng(result[0].geometry.location.lat(), result[0].geometry.location.lng());

                            for (var i in locStores) {

                                locStores[i].latLng = new google.maps.LatLng(locStores[i].locator_lat, locStores[i].locator_lng);

                                if (calcDistance(fromLatLng, locStores[i].latLng) < parseInt(locRadius)) {
                                    locStores[i].distance = parseFloat(calcDistance(fromLatLng, locStores[i].latLng));
                                    finalStores.push(locStores[i]);
                                }
                            }

                            if(finalStores.length>0){
                                locClosetArr=[];
                                clearDistance();
                                clearLocations();
                                clearDirections();
                                locClosest=parseInt(locRadius);

                                for (var i in finalStores){
                                    locClosetArr.push(finalStores[i].distance);
                                }
                                locClosest = Math.min.apply(null, locClosetArr);
                                for(var i in finalStores){

                                    locMarker = new google.maps.Marker({
                                        map:front_end_map,
                                        position:{lat:finalStores[i].locator_lat,lng:finalStores[i].locator_lng},
                                        id:i

                                    });
                                    locInfoWindow = new google.maps.InfoWindow;


                                    if(finalStores[i].distance <= locClosest){
                                        locClosest = finalStores[i].distance;
                                        locClosetPosition = {
                                            lat:parseFloat(finalStores[i].locator_lat),
                                            lng:parseFloat(finalStores[i].locator_lng)
                                        };
                                        locClosetAddress = finalStores[i].locator_addr;
                                        locMarker.set('name','closest');
                                        locMarker.set('label','B');
                                    }

                                    locBounds.extend(locMarker.getPosition());
                                    google.maps.event.addListener(locMarker, 'click', function(i) {
                                        return function() {
                                            locInfoWindow.setContent("<b>"+finalStores[i].name+"</b><br/>"+finalStores[i].locator_addr);
                                            locInfoWindow.open(front_end_map, this);
                                        }
                                    }(i));

                                locMarkers.push(locMarker);
                                }

                                locCurrent = new google.maps.Marker({
                                    map:front_end_map,
                                    icon:def,
                                    label: "A",
                                    position:fromLatLng
                                });

                                google.maps.event.addListener(locCurrent,"click",function () {
                                    locInfoWindow.setContent(locAddress);
                                    locInfoWindow.open(front_end_map,this);
                                });

                                locBounds.extend(locCurrent.getPosition());
                                front_end_map.fitBounds(locBounds);
                                center_coords=null;
                                var locRoute,locContent;
                                locDirectionsDisplay.setMap(front_end_map);
                                locDirectionsService.route({
                                    origin: locCurrent.getPosition(),
                                    destination: locClosetPosition,
                                    travelMode: 'DRIVING'
                                }, function(response, status) {
                                    if (status === google.maps.DirectionsStatus.OK) {
                                        locDirectionsDisplay.setDirections(response);
                                        locRoute = response.routes[0];
                                        locContent = "<b>Total distance: </b>"+locRoute.legs[0].distance.text;
                                        front_end_map.fitBounds(locBounds);
                                    }
                                    else {
                                        window.alert('Directions request failed due to ' + status);
                                    }
                                });
                                locRouteInfowindow.close();
                                google.maps.event.clearListeners(locpOptions, 'click');
                                google.maps.event.addListener(locpOptions,"click",function (e) {
                                    locRouteInfowindow.close();
                                    locRouteInfowindow.setPosition(e.latLng);
                                    locRouteInfowindow.setContent(locContent);
                                    locRouteInfowindow.open(front_end_map);
                                });
                            }
                            else {
                                clearDistance();
                                clearLocations();
                                clearDirections();

                            alert("Sorry, but there are not available stores in certain radius!");

                            }

                        }

                        else {

                            alert('Geocode was not successful for the following reason: ' + status);
                        }

                    });
                }
            });
        }
        
        /* locator end */

    }/* end initialize map */


    var allMaps = jQuery('.huge_it_google_map');

    if( allMaps.length ){

        allMaps.each(function(i){

            var id = jQuery(this).attr('id');

            hugeitMaps[i] = hugeitMapsInitializeMap( id );

        });

    }

    jQuery('.hg-direction-instructions').mCustomScrollbar({
            theme:"rounded-dots-dark",
            live:true,
            mouseWheel:{ scrollAmount: 100 }
    });


});