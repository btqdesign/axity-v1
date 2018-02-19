jQuery(document).ready(function(){
    jQuery("#styling_submit").on("click", function () {
        var g_map_styling_hue = jQuery("#g_map_styling_hue").val();
        if (g_map_styling_hue == "FFFFFF") {
            g_map_styling_hue = "";
        }
        var g_map_styling_lightness = jQuery("#g_map_styling_lightness").val();
        var g_map_styling_saturation = jQuery("#g_map_styling_saturation").val();
        var g_map_styling_gamma = jQuery("#g_map_styling_gamma").val();
        var id = jQuery("#map_id").val();
        var styling_data = {
            action: "hugeit_maps_save_stylings",
            nonce: mapSaveL10n.stylingNonce,
            map_id: id,
            map_hue: g_map_styling_hue,
            map_lightness: g_map_styling_lightness,
            map_saturation: g_map_styling_saturation,
            map_gamma: g_map_styling_gamma
        };
        jQuery("#styling_submit").parent().find('.spinner').css('visibility','visible');
        jQuery.post(ajaxurl, styling_data, function (response) {
            jQuery("#styling_submit").parent().find('.spinner').css('visibility','hidden');
            if (response.success) {
                hugeitMapsInitializeAllMaps( id, response );
            }
        }, "json");
        return false;
    });


    jQuery("#submit_layers").on("click", function () {
        if (jQuery("#traffic_layer_enable").is(":checked")) {
            var traffic = jQuery("#traffic_layer_enable").val();
        }
        else {
            var traffic = 0;
        }
        if (jQuery("#bicycling_layer_enable").is(":checked")) {
            var bike = jQuery("#bicycling_layer_enable").val();
        }
        else {
            var bike = 0;
        }
        if (jQuery("#transit_layer_enable").is(":checked")) {
            var transit = jQuery("#transit_layer_enable").val();
        }
        else {
            var transit = 0;
        }
        var id = jQuery("#map_id").val();
        var layers_data = {
            action: "hugeit_maps_save_layers",
            nonce: mapSaveL10n.nonce,
            map_id: id,
            traffic: traffic,
            bike: bike,
            transit: transit
        };
        jQuery("#submit_layers").parent().find('.spinner').css('visibility','visible');
        jQuery.post(ajaxurl, layers_data, function (response) {
            jQuery("#submit_layers").parent().find('.spinner').css('visibility','hidden');
            if (response.success) {
                hugeitMapsInitializeAllMaps( id, response );
            }
        }, "json");
        return false;
    });


    jQuery("#frontdir_submit").on("click", function () {

        if (jQuery("#frontdir_enabled").is(":checked")) {
            var frontdir_enabled = jQuery("#frontdir_enabled").val();
        }
        else {
            var frontdir_enabled = 0;
        }

        var frontdir_align = jQuery("#frontdir_align").val();
        var dir_window_width = jQuery("#dir_window_width").val();

        var id = jQuery("#map_id").val();

        var frontdir_data = {
            action: "hugeit_maps_save_frontdir",
            nonce: mapSaveL10n.nonce,
            map_id: id,
            frontdir_enabled:frontdir_enabled,
            frontdir_align: frontdir_align,
            dir_window_width:dir_window_width
        };
        jQuery("#frontdir_submit").parent().find('.spinner').css('visibility','visible');
        jQuery.post(ajaxurl, frontdir_data, function (response) {
            jQuery("#frontdir_submit").parent().find('.spinner').css('visibility','hidden');
            if (response.success) {
                hugeitMapsInitializeAllMaps( id, response );
            }
        }, "json");
        return false;
    });

    jQuery("#locator_enabled").on("change",function () {
        var locator_data,locatorCheck;
        var id = jQuery("#map_id").val();
        if(jQuery("#locator_enabled").is(":checked")){
            locatorCheck = confirm("Enable Store Locator?");
            if(locatorCheck) {
                    locator_data = {
                    action: "hugeit_maps_save_locator",
                    nonce: mapSaveL10n.nonce,
                    map_id: id,
                    locator_enabled:1
                };
                jQuery("#locator_submit").parent().find('.spinner').css('visibility','visible');
                jQuery.post(ajaxurl, locator_data, function (response) {
                    jQuery("#locator_submit").parent().find('.spinner').css('visibility','hidden');
                    if (!response.success) {
                        console.log(response);
                    }
                }, "json");
            }
            else {
                jQuery("#locator_enabled").prop("checked",false);

            }
        }
        else {
            locatorCheck = confirm("Disable Store Locator?");
            if(locatorCheck) {
                locator_data = {
                    action: "hugeit_maps_save_locator",
                    nonce: mapSaveL10n.nonce,
                    map_id: id,
                    locator_enabled:0
                };
                jQuery("#locator_submit").parent().find('.spinner').css('visibility','visible');
                jQuery.post(ajaxurl, locator_data, function (response) {
                    jQuery("#locator_submit").parent().find('.spinner').css('visibility','hidden');
                    if (!response.success) {
                        console.log(response);
                    }
                }, "json");
            }
            else {

                jQuery("#locator_enabled").prop("checked",true);

            }

        }
        return false;
    });

    jQuery("#map_submit").on("click", function () {
        var map_name = jQuery("#map_name").val();
        var map_type = jQuery("#map_type").val();
        var map_infowindow_type = jQuery("#map_infowindow_type").val();
        if (jQuery("#map_controller_pan").is(":checked")) {
            var map_controller_pan = jQuery("#map_controller_pan").val();
        }
        else {
            var map_controller_pan = 0;
        }
        if (jQuery("#map_controller_zoom").is(":checked")) {
            var map_controller_zoom = jQuery("#map_controller_zoom").val();
        }
        else {
            var map_controller_zoom = 0;
        }
        if (jQuery("#map_controller_type").is(":checked")) {
            var map_controller_type = jQuery("#map_controller_type").val();
        }
        else {
            var map_controller_type = 0;
        }
        if (jQuery("#map_controller_scale").is(":checked")) {
            var map_controller_scale = jQuery("#map_controller_scale").val();
        }
        else {
            var map_controller_scale = 0;
        }
        if (jQuery("#map_controller_street_view").is(":checked")) {
            var map_controller_street_view = jQuery("#map_controller_street_view").val();
        }
        else {
            var map_controller_street_view = 0;
        }
        if (jQuery("#map_controller_overview").is(":checked")) {
            var map_controller_overview = jQuery("#map_controller_overview").val();
        }
        else {
            var map_controller_overview = 0;
        }
        var map_zoom = jQuery("#map_zoom").val();
        var map_center_lat = jQuery("#map_center_lat").val();
        var map_center_lng = jQuery("#map_center_lng").val();
        var map_width = jQuery("#map_width").val();
        var map_height = jQuery("#map_height").val();
        var map_align = jQuery("#map_align").val();
        var wheel_scroll = jQuery("#wheel_scroll").val();
        var draggable = jQuery("#map_draggable").val();
        var map_language = jQuery("#map_language").val();
        var min_zoom = jQuery("#min_zoom").val();
        var max_zoom = jQuery("#max_zoom").val();
        var map_border_radius = jQuery("#map_border_radius").val();
        var open_infowindows_onload = jQuery("#open_infowindows_onload").is(":checked") ? 1 : 0;
        var map_animation = jQuery("#map_animation").val();
        var id = jQuery("#map_id").val();

        var general_data = {
            action: "hugeit_maps_save_map",
            nonce: mapSaveL10n.nonce,
            map_id: id,
            map_name: map_name,
            map_type: map_type,
            map_infowindow_type: map_infowindow_type,
            map_controller_pan: map_controller_pan,
            map_controller_zoom: map_controller_zoom,
            map_controller_type: map_controller_type,
            map_controller_scale: map_controller_scale,
            map_controller_street_view: map_controller_street_view,
            map_controller_overview: map_controller_overview,
            map_zoom: map_zoom,
            min_zoom: min_zoom,
            max_zoom: max_zoom,
            map_center_lat: map_center_lat,
            map_center_lng: map_center_lng,
            map_width: map_width,
            map_height: map_height,
            map_align: map_align,
            wheel_scroll: wheel_scroll,
            draggable: draggable,
            map_language: map_language,
            map_border_radius: map_border_radius,
            open_infowindows_onload: open_infowindows_onload,
            map_animation: map_animation,
        };
        jQuery("#map_submit").parent().find(".spinner").css("visibility","visible");
        jQuery.post(ajaxurl, general_data, function (response) {
            jQuery("#map_submit").parent().find(".spinner").css("visibility","hidden");
            if (response.success) {
                hugeitMapsInitializeAllMaps( id, response );

            } else {

            }
        }, "json");

        return false;
    });
});