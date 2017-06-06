jQuery(document).ready(function () {

    jQuery('.hugeit_maps_delete_map_from_list').on('click',function(){
        if( !confirm( "Are you sure you want to delete this item?" ) ){
            return false;
        }
    });

    jQuery('input[data-slider="true"]').bind("slider:changed", function (event, data) {
        jQuery(this).parent().find('span').html(data.value);
        jQuery(this).val(data.value);
    });

    jQuery('.admin_edit_section_container form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    jQuery('.help').hover(function () {
        jQuery(this).parent().find('.help-block').removeClass('active');
        var width = jQuery(this).parent().find('.help-block').outerWidth();
        jQuery(this).parent().find('.help-block').addClass('active');
    }, function () {
        jQuery(this).parent().find('.help-block').removeClass('active');
    });


    jQuery("#marker_add_button").on("click", function (e) {
        jQuery(this).hide("fast").addClass("tab_options_hidden_section");
        jQuery("#g_maps > div").not("#g_map_marker").addClass("hide");
        jQuery("#g_map_marker").removeClass("hide");
        jQuery("#markers_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery(".update_marker_list_item").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_marker_options .hidden_edit_content").show(200).addClass("tab_options_active_section");

        return false;
    });

    jQuery("#cancel_marker, #back_marker").on("click", function (e) {
        jQuery("#marker_add_button").show(200);
        jQuery("#g_maps > div").not("#g_map_canvas").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#markers_edit_exist_section").show(200);
        jQuery(".update_marker_list_item").show(200);
        jQuery(".marker_image_choose ul li.active").removeClass("active");
        jQuery("#g_map_marker_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    });

    jQuery("#cancel_edit_marker, #back_edit_marker").on("click", function () {
        jQuery("#marker_add_button").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery(".marker_image_choose ul li.active").removeClass("active");
        jQuery("#markers_edit_exist_section").show(200);
        jQuery(this).parentsUntil(".editing_section").find(".update_list_item").hide(200);
        jQuery("#marker_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    });

    jQuery("#cancel_polygone, #back_polygone").on("click", function (e) {
        jQuery("#polygon_add_button").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#polygone_edit_exist_section").show(200);
        jQuery("#g_map_polygone_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    });

    jQuery("#cancel_edit_polygone, #back_edit_polygone").on("click", function (e) {
        jQuery(".edit_polygone_list_delete a").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#polygone_edit_exist_section").show(200);
        jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").hide(200);
        jQuery("#polygon_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    });
    jQuery("#polygon_add_button").on('click', function (e) {
        jQuery(this).hide(100).addClass("tab_options_hidden_section");
        jQuery("#g_maps > div").not("#g_map_polygon").addClass("hide");
        jQuery("#g_map_polygon").removeClass("hide");
        jQuery("#polygone_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_polygone_options .hidden_edit_content").show(200).addClass("tab_options_active_section");
        jQuery("#polygone_coords").val("");
        return false;
    });

    jQuery("#cancel_polyline, #back_polyline").on("click", function (e) {
        jQuery("#polyline_add_button").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#polyline_edit_exist_section").show(200);
        jQuery("#g_map_polyline_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    });

    jQuery("#cancel_edit_polyline, #back_edit_polyline").on("click", function (e) {
        jQuery(".edit_polyline_list_delete a").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#polyline_edit_exist_section").show(200);
        jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").hide(200);
        jQuery("#polyline_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    });

    jQuery("#polyline_add_button").on('click', function (e) {
        jQuery(this).hide("fast").addClass("tab_options_hidden_section");
        jQuery("#g_maps > div").not("#g_map_polygon").addClass("hide");
        jQuery("#g_map_polyline").removeClass("hide");
        jQuery("#polyline_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_polyline_options .hidden_edit_content").show(200).addClass("tab_options_active_section");
        jQuery("#polyline_coords").val("");
        return false;
    });

    /** Add Direction Button handling */
    jQuery("#direction_add_button").on('click', function (e) {
        jQuery(this).hide("fast").addClass("tab_options_hidden_section");
        jQuery("#g_maps > div").not("#g_map_direction").addClass("hide");
        jQuery("#g_map_direction").removeClass("hide");
        jQuery("#direction_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_direction_options .hidden_edit_content").show(200).addClass("tab_options_active_section");
        jQuery("#direction_name,#direction_start_addr,#direction_start_lat,#direction_start_lng,#direction_end_addr,#direction_end_lat,#direction_end_lng").val("");
        jQuery("#direction_line_opacity").simpleSlider("setValue", '0.9');
        jQuery("#direction_line_color").val('FF0F0F');
        jQuery("#direction_line_width").val('5');
        jQuery("#hover_direction_line_opacity").simpleSlider("setValue", '0.5');
        jQuery("#hover_direction_line_color").val('FF80B7');

        google.maps.event.trigger(mapdirection, 'resize');
        mapdirection.setCenter(mapcenter);
        if (newDirection) {
            newDirection.setMap(null);
            newDirection = false;
            newDirectionStartMarker.setMap(null);
            newDirectionStartMarker = false;
            newDirectionCoords = [];
            newDirectionsDisplay = false;
        }

        return false;
    });

    /** Cancel creating a direction */
    jQuery("#cancel_direction, #back_direction").on("click", function (e) {
        jQuery("#direction_add_button").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#direction_edit_exist_section").show(200);
        jQuery("#g_map_direction_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        jQuery("#direction_start_addr, #direction_start_lat, #direction_start_lng, #direction_end_addr, #direction_end_lat, #direction_end_lng").val("");
        jQuery("#direction_options_input").removeAttr("checked");

        if(newDirectionStartMarker){
            newDirectionStartMarker.setMap(null);
        }

        newDirection = false;
        newDirectionStartMarker = false;
        newDirectionCoords = [];
        newDirectionsDisplay = false;
        directionsService = new google.maps.DirectionsService();
        stepDisplay = new google.maps.InfoWindow;
        newDirectionMode = 'DRIVING';
        newDirectionShowSteps = false;
        newDirectionMarkers = [];

        return false;
    });

    /** Cancel Editing a direction */
    jQuery("#cancel_edit_direction, #back_edit_direction").on("click", function (e) {
        jQuery(".edit_direction_list_delete a").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#direction_edit_exist_section").show(200);
        jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").hide(200);
        jQuery("#direction_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);

        editDirection.setMap(null);
        editDirection = false;
        editDirectionCoords = [];
        editDirectionsDisplay = false;
        editDirectionMode = 'DRIVING';
        editDirectionShowSteps = false;

        return false;
    });

    /** Add Store Locator Button handling */
    jQuery("#locator_add_button").on('click', function (e) {
        jQuery(this).hide("fast").addClass("tab_options_hidden_section");
/* Temporarily unnecessary      jQuery("#g_maps > div").not("#g_map_locator").addClass("hide"); */
        jQuery("#g_map_locator").removeClass("hide");
        jQuery("#locator_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_locator_options .hidden_edit_content").show(200).addClass("tab_options_active_section");
        jQuery("#locator_name,#locator_addr,#locator_lat,#locator_lng").val("");
        var input        = document.getElementById('locator_addr');
        var autocomplete = new google.maps.places.Autocomplete(input);
        var searchBox    = new google.maps.places.SearchBox(input);
        var geocoder = new google.maps.Geocoder();
        google.maps.event.addDomListener(searchBox,'places_changed',function () {
            var address= document.getElementById('locator_addr').value;
            geocoder.geocode({'address': address}, function(results, status) {
                if (status === 'OK') {

                    document.getElementById('locator_lat').value = results[0].geometry.location.lat();
                    document.getElementById('locator_lng').value = results[0].geometry.location.lng();

                }
                else {

                    alert('Geocode was not successful for the following reason: ' + status);
                }

            });

        });
        return false;
    });

    /** Cancel creating a Store Locator */
    jQuery("#cancel_locator, #back_locator").on("click", function (e) {
        jQuery("#locator_add_button").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#locator_edit_exist_section").show(200);
        jQuery("#g_map_locator_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        jQuery("#locator_addr, #locator_lat, #locator_lng").val("");
        jQuery("#locator_options_input").removeAttr("checked");

        return false;
    });

    /** Cancel Editing a Store Locator */
    jQuery("#cancel_edit_locator, #back_edit_locator").on("click", function (e) {
        jQuery(".edit_locator_list_delete a").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#locator_edit_exist_section").show(200);
        jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").hide(200);
        jQuery("#locator_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);


        return false;
    });

    jQuery("#cancel_circle, #back_circle").on("click", function (e) {
        jQuery("#circle_add_button").show("fast");
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#circle_edit_exist_section").show(200);
        jQuery("#g_map_circle_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    });

    jQuery("#cancel_edit_circle, #back_edit_circle").on("click", function (e) {
        jQuery("#g_maps > div").not("#g_map_polygon").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#circle_edit_exist_section").show(200);
        jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").hide(200);
        jQuery("#circle_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
    });

    jQuery("#circle_add_button").on("click", function (e) {
        jQuery(this).hide("fast").addClass("tab_options_hidden_section");
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_circle").removeClass("hide");
        jQuery("#circle_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_circle_options .hidden_edit_content").show(200).addClass("tab_options_active_section");
        return false;
    });

    jQuery(".marker_image_choose_button").on("click", function () {
        jQuery(this).parent().parent().find(".active").removeClass("active");
        jQuery(this).parent().addClass("active");
    });

    jQuery(".front_end_input_options").on("keyup change", function () {
        var width = parseInt(jQuery("#map_width").val()) / 2;
        var height = jQuery("#map_height").val();
        var border_radius = jQuery("#map_border_radius").val();
        jQuery(".g_map").css({width: width + "%", height: height + "px", borderRadius: border_radius + "px"})
    });

});
