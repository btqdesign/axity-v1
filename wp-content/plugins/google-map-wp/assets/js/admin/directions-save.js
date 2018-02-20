jQuery(document).ready(function(){
    jQuery("#direction_edit_submit").on("click",function(){
        if(!editDirection){
            alert("Oops, looks like somethin is wrong, We can't find the direction to save");
        }

        var _this = jQuery(this),
            name = jQuery("#direction_edit_name").val(),
            startLat = jQuery("#direction_edit_start_lat").val(),
            startLng = jQuery("#direction_edit_start_lng").val(),
            endLat = jQuery("#direction_edit_end_lat").val(),
            endLng = jQuery("#direction_edit_end_lng").val(),
            travelMode = jQuery("#direction_edit_travelmode").val(),
            showSteps = jQuery("#direction_edit_show_steps").is(":checked") ? 1 : 0,
            lineOpacity = jQuery( "#direction_edit_line_opacity" ).val(),
            lineColor = jQuery( "#direction_edit_line_color" ).val(),
            lineWidth = jQuery( "#direction_edit_line_width" ).val(),
            id = jQuery("#direction_get_id").val(),
            map_id = jQuery("#map_id").val();

        var data = {
            action: "hugeit_maps_edit_direction",
            nonce : directionSaveL10n.nonce,
            id: id,
            map_id : map_id,
            name: name,
            startLat: startLat,
            startLng: startLng,
            endLat: endLat,
            endLng: endLng,
            travelMode: travelMode,
            showSteps: showSteps,
            lineOpacity: lineOpacity,
            lineColor: lineColor,
            lineWidth: lineWidth
        };

        jQuery.ajax({
            url:ajaxurl,
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function(xhr) {
                _this.parent().find(".spinner").css("visibility","visible");
            }
        }).done(function (response) {
            _this.parent().find(".spinner").css("visibility","hidden");
            if(response.success){

                hugeitMapsInitializeAllMaps(map_id, response);
                jQuery("#cancel_edit_direction").trigger("click");
                jQuery(document).scrollTop(0);
                jQuery("#direction_edit_exist_section li").each(function () {
                    if (jQuery(this).attr("data-list_id") == id) {
                        jQuery(this).find(".edit_list_item").html(name)
                    }
                })
            }

        }).fail(function(){
            console.log("Failed to save the direction");
        });

        return false;
    });

    jQuery("#direction_submit").on("click",function(){

        if(!newDirection){
            alert("First create a directino please");
            return false;
        }

        var _this = jQuery(this),
            name = jQuery("#direction_name").val(),
            startLat = jQuery("#direction_start_lat").val(),
            startLng = jQuery("#direction_start_lng").val(),
            endLat = jQuery("#direction_end_lat").val(),
            endLng = jQuery("#direction_end_lng").val(),
            travelMode = jQuery("#direction_travelmode").val(),
            showSteps = jQuery("#direction_show_steps").is(":checked") ? 1 : 0,
            lineOpacity = jQuery( "#direction_line_opacity" ).val(),
            lineColor = jQuery( "#direction_line_color" ).val(),
            lineWidth = jQuery( "#direction_line_width" ).val(),
            map_id = jQuery("#map_id").val();

        var data = {
            action: "hugeit_maps_save_new_direction",
            nonce : directionSaveL10n.nonce,
            map_id: map_id,
            name: name,
            startLat: startLat,
            startLng: startLng,
            endLat: endLat,
            endLng: endLng,
            travelMode: travelMode,
            showSteps: showSteps,
            lineOpacity: lineOpacity,
            lineColor: lineColor,
            lineWidth: lineWidth
        };

        jQuery.ajax({
            url:ajaxurl,
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function(xhr) {
                _this.parent().find(".spinner").css("visibility","visible");
            }
        }).done(function (response) {
            _this.parent().find(".spinner").css("visibility","hidden");
            if (response.success) {
                hugeitMapsInitializeAllMaps(map_id, response);
                jQuery("#cancel_direction").trigger("click");
                jQuery(document).scrollTop(0);
                if (jQuery(".empty_direction").html() != undefined) {
                    jQuery(".empty_direction").after("<ul>" +
                        "<li class='edit_list has_background' data-list_id='" + response.last_id + "'>" +
                        "<div class='list_number' >1</div><div class='edit_list_item'>" + name + "</div>" +
                        "<div class='edit_direction_list_delete edit_list_delete'>" +
                        "<form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='" + map_id + "'>" +
                        "<input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' />" +
                        "<input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='direction' />" +
                        "<input type='hidden' class='edit_list_delete_table' value='hugeit_maps_directions' />" +
                        "<input type='hidden' name='delete_direction_id' class='edit_list_delete_id' value='" + response.last_id + "' />" +
                        "</form>" +
                        "<a href='#' class='button' class='edit_direction_list_item' ></a>" +
                        "<input type='hidden' class='direction_edit_id' name='direction_edit_id' value='" + response.last_id + "' />" +
                        "</div>" +
                        "</li>" +
                        "</ul>");
                    jQuery(".empty_direction").remove();
                } else {
                    var last_id = jQuery("#direction_edit_exist_section .edit_list").last().find(".list_number").html();
                    var this_id = parseInt(last_id) + 1;
                    jQuery("#direction_edit_exist_section .edit_list").last().after("<li class='edit_list has_background' data-list_id='" + response.last_id + "'>" +
                        "<div class='list_number' >" + this_id + "</div><div class='edit_list_item'>" + name + "</div>" +
                        "<div class='edit_direction_list_delete edit_list_delete'>" +
                        "<form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='" + map_id + "'>" +
                        "<input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' />" +
                        "<input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='direction' />" +
                        "<input type='hidden' class='edit_list_delete_table' value='hugeit_maps_directions' />" +
                        "<input type='hidden' name='delete_direction_id' class='edit_list_delete_id' value='" + response.last_id + "' />" +
                        "</form>" +
                        "<a href='#' class='button' class='edit_direction_list_item' ></a>" +
                        "<input type='hidden' class='direction_edit_id' name='direction_edit_id' value='" + response.last_id + "' />" +
                        "</div>" +
                        "</li>");
                }
            } else {
                console.log("Oops, something went wrong");
            }
        }).fail(function () {
            console.log('Failed to save the direction');
        });
        return false;
    });
})