var contheight;
// hide page section
jQuery(".uploadMedia").live('click', function() {
	var $ = jQuery;
	var id = $(this).attr("name");
	var custom_uploader = wp.media({
		title: 'Select File',
		button: {
			text: 'Add File'
		},
		multiple: false
	})
		.on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			jQuery('#' + id).val(attachment.url);
			jQuery('#' + id + '_img').attr('src', attachment.url);
			jQuery('#' + id + '_box').show();
		}).open();
		
});

function _delIcon(id){
	var	delId	= '#cs_infobox_'+id;
	jQuery(""+delId+ " .cs-search-icon-hidden").val('');
	jQuery(""+delId+ " .lead i").attr('class', '');
	jQuery(""+delId+ " .lead i").addClass('picker-target');
	jQuery(""+delId+ " .drop_icon_box").hide();
	jQuery(""+delId+ " .dp_icon").val('Choose Icon');
	jQuery(""+delId+ " .choose_icon_box").show();
	jQuery(""+delId+ " #cs-icon-wrap").removeClass('hideicon');
}
					
function del_media(id) {
	var $ = jQuery;
	jQuery('#' + id + '_box').hide();
	jQuery('#' + id).val('');
}

function openpopedup(id) {
	var $ = jQuery;
	$(".elementhidden,.opt-head,.to-table thead,.to-table tr").hide();
	$("#" + id).parents("tr").show();
	$("#" + id).parents("td").css("width", "100%");
	$("#" + id).parents("td").prev().hide();
	$("#" + id).parents("td").find("a.actions").hide();
	$("#" + id).children(".opt-head").show();
	$("#" + id).slideDown();

	$("#" + id).animate({
		top: 0,
	}, 400, function() {
		// Animation complete.
	});
	/*$.scrollTo('#normal-sortables', 800, {
		easing: 'swing'
	});*/
};

function closepopedup(id) {
	var $ = jQuery;
	$("#" + id).slideUp(800);

	$(".to-table tr").css("width", "");
	$(".elementhidden,.opt-head,.option-sec,.to-table thead,.to-table tr,a.actions,.to-table tr td").delay(600).fadeIn(200);

	$.scrollTo('.elementhidden', 800, {
		
	});
};

function update_title(id) {
	var val;
	val = jQuery('#address_name' + id).val();
	jQuery('#address_name' + id).html(val);
}


function gll_search_map() {
	var vals;
	vals = jQuery('#loc_address').val();
	vals = vals + ", " + jQuery('#loc_city').val();
	vals = vals + ", " + jQuery('#loc_postcode').val();
	vals = vals + ", " + jQuery('#loc_region').val();
	vals = vals + ", " + jQuery('#loc_country').val();
	jQuery('.gllpSearchField').val(vals);
}

function remove_image(id) {
	var $ = jQuery;
	$('#' + id).val('');
	$('#' + id + '_img_div').hide();
	//$('#'+id+'_div').attr('src', '');
}

function slideout() {
	setTimeout(function() {
		jQuery(".form-msg").slideUp("slow", function() {});
	}, 5000);
}

function cs_div_remove(id) {
	jQuery("#" + id).remove();
}

function cs_toggle(id) {
	jQuery("#" + id).slideToggle("slow");
}

function toggle_with_value(id, value) {
	if (value == 0) jQuery("#" + id).hide("slow");
	else jQuery("#" + id).show("slow");
}

function cs_toggle_height(value, id) {
	var $ = jQuery;
	if (value == "Post Slider") {
		jQuery("#post_slider" + id).show();
		jQuery("#choose_slider" + id).hide();
		jQuery("#layer_slider" + id).hide();
		jQuery("#show_post" + id).show();
	} else if (value == "Flex Slider") {
		jQuery("#choose_slider" + id).show();
		jQuery("#layer_slider" + id).hide();
		jQuery("#post_slider" + id).hide();
		jQuery("#show_post" + id).hide();
	} else if (value == "Custom Slider") {
		jQuery("#layer_slider" + id).show();
		jQuery("#choose_slider" + id).hide();
		jQuery("#post_slider" + id).hide();
		jQuery("#show_post" + id).hide();
	} else {
		jQuery("#" + id).removeClass("no-display");
		jQuery("#post_slider" + id).show();
		jQuery("#choose_slider" + id).hide();
		jQuery("#layer_slider" + id).hide();
		jQuery("#show_post" + id).hide();
	}
}

function cs_toggle_list(value, id) {
	var $ = jQuery;

	if (value == "custom_icon") {
		jQuery("#" + id).addClass("no-display");
		jQuery("#cs_list_icon").show();
	} else {
		jQuery("#" + id).removeClass("no-display");
		jQuery("#cs_list_icon").hide();
	}
}

/* -- Fancy Heading Icon List Show Hide End --*/
/* -- Alert Messages Show Hide Start --*/

function cs_toggle_alerts(value, id) {
	var $ = jQuery;
	
	if (value == "alert") {
		jQuery("#fancy_active"+id).hide();
	} else {
		jQuery("#fancy_active"+id).show();
	}

}

function cs_counter_image(value, id) {
	var $ = jQuery;
	
	if (value == "icon") {
		jQuery(".selected_image_type"+id).hide();
		jQuery(".selected_icon_type"+id).show();
	} else {
		jQuery(".selected_image_type"+id).show();
		jQuery(".selected_icon_type"+id).hide();
	}

}

function cs_counter_view_type(value, id) {
	var $ = jQuery;
	
	if (value == "icon-border") {
		jQuery("#selected_view_icon_type"+id).hide();
		jQuery("#selected_view_border_type"+id).show();
		jQuery("#selected_view_icon_image_type"+id).hide();
		jQuery("#selected_view_icon_icon_type"+id).show();
	} else {
		jQuery("#selected_view_icon_type"+id).show();
		jQuery("#selected_view_border_type"+id).hide();
		jQuery("#selected_view_icon_image_type"+id).show();
	}

}
/* -- Alert Messages Show Hide End --*/

/* -- Alert Messages Show Hide Start --*/

function cs_toggle_fancyalert(value, id) {
	var $ = jQuery;
	var cs_message_type = jQuery("#cs_message_type" + id).val()

	if (value == "threed_messagebox" && cs_message_type == 'alert') {
		jQuery("#cs_style_type" + id).show();
		jQuery("#fancy_active" + id).hide();
	} else {
		jQuery("#cs_style_type" + id).hide();
		jQuery("#fancy_active" + id).show();
	}
}
/* -- Alert Messages Show Hide End --*/
/* -- Alert Messages Show Hide Start --*/

function cs_toggle_fancybutton(value, id) {
	var $ = jQuery;
	if (value == "btn_style") {
		jQuery("#fancy_button"+id).show();
	} else {
		jQuery("#fancy_button"+id).hide();
	}
}
/* -- Alert Messages Show Hide End --*/

/* -- Counter Image Show Hide Start --*/

function cs_service_toggle_image(value, id, object) {
	var $ = jQuery;
	var selectedValue = $('#cs_service_type-'+id).val();
	if (value == "image") {
		jQuery("#modern-size-"+id).hide();
		jQuery("#selected_image_type"+id).show();
		jQuery("#selected_icon_type"+id).hide();
		
	} else if (value == "icon") {
		if ( selectedValue  == 'modern' ){
			jQuery("#modern-size-"+id).show();
		} else {
			jQuery("#modern-size-"+id).hide();
		}
		
		jQuery("#selected_image_type"+id).hide();
		jQuery("#selected_icon_type"+id).show();
	}

}

function cs_service_toggle_view(value, id, object) {
	var $ = jQuery;
	if (value == "modern") {
		jQuery("#cs-service-bg-color-"+id).show();
		jQuery("#modern-size-"+id).show();
		jQuery("#service-position-classic-"+id).hide();
		jQuery("#service-position-modern-"+id).show();
		jQuery("#cs-modern-bg-color-"+id+" #bg-service").html('Button bg Color');
		
	} else if (value == "classic") {
		jQuery("#modern-size-"+id).hide();
		jQuery("#cs-service-bg-color-"+id).hide();
		jQuery("#service-position-modern-"+id).hide();
		jQuery("#service-position-classic-"+id).show();
		jQuery("#cs-modern-bg-color-"+id+" #bg-service").html('Text Color');
	}

}

function cs_icon_toggle_view(value, id, object) {
	var $ = jQuery;
	if (value == "bg_style") {
		jQuery("#selected_icon_view_"+id+" #label-icon").html('Icon Background Color');
		
	} else if (value == "border_style") {
		jQuery("#selected_icon_view_"+id+" #label-icon").html('Border Color');
	}

}

/* -- Counter Image Show Hide End --*/

/* -- Pricetable Title Show Hide Start --*/

function cs_pricetable_style_vlaue(value, id) {
	var $ = jQuery;
	if (value == "classic") {
		jQuery("#pricetbale-title"+id).hide();
	} else {
		jQuery("#pricetbale-title"+id).show();
	}
}

function show_sidebar(id, random_id) {
	var $ = jQuery;
	jQuery('input[class="radio_cs_sidebar"]').click(function() {
		jQuery(this).parent().parent().find(".check-list").removeClass("check-list");
		jQuery(this).siblings("label").children("#check-list").addClass("check-list");
	});
	var randomeID = "#" + random_id;
	if ((id == 'left') || (id == 'right')) {
		$(randomeID + "_sidebar_right,"+randomeID+"_sidebar_left").hide();
		$(randomeID+"_sidebar_"+id).show();
	} else if ((id == 'both') || (id == 'none'))  {
		$(randomeID + "_sidebar_right,"+randomeID+"_sidebar_left").hide();
	} 
}

function show_sidebar_page(id) {
	var $ = jQuery;
	jQuery('input[name="cs_page_layout"]').live('click', function() {
		jQuery(this).parent().parent().find(".check-list").removeClass("check-list");
		jQuery(this).siblings("label").children("#check-list").addClass("check-list");
	});
	if ((id == 'left') || (id == 'right') ) {
		$("#sidebar_right,#sidebar_left").hide();
		$("#sidebar_"+id).show();
	}  else if (id == 'both') {
		$("#sidebar_left,#sidebar_right").show();
	} else if (id == 'none') {
		$("#sidebar_left,#sidebar_right").hide();
	}
}


function cs_toggle_gal(id, counter) {
	if (id == 0) {
		jQuery("#link_url" + counter).hide();
		jQuery("#video_code" + counter).hide();
	} else if (id == 1) {
		jQuery("#link_url" + counter).hide();
		jQuery("#video_code" + counter).show();
	} else if (id == 2) {
		jQuery("#link_url" + counter).show();
		jQuery("#video_code" + counter).hide();
	}
}


function blog_toggle(id, counter) {
	if (id == "blog-carousel-view") {
		jQuery("#Blog-listing" + counter).hide();
	} else {
		jQuery("#Blog-listing" + counter).show();
	}
}

var counter = 0;
function delete_this(id) {
	jQuery('#' + id).remove();
	jQuery('#' + id + '_del').remove();
	count_widget--;
	if (count_widget < 1) jQuery("#add_page_builder_item").addClass("hasclass");
}

var Data = [{
	"Class": "column_100",	"title": "100",	"element": ["class","gallery", "slider", "blog","event", "team", "column", "accordions", "team", "client", "contact", "column", "divider", "message_box",'image', "image_frame", "map", "video", "quote", "dropcap", "pricetable", "tabs", "accordion", "prayer", "advance_search", "parallax","table","call_to_action","flex_column","clients","spacer","heading","testimonials","infobox","promobox","offerslider","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter","member","timetable"]}, 
	{"Class": "column_75","title": "75",	"element": ["class","gallery", "slider", "blog", "event", "team", "column", "accordions", "team", "client", "contact", "column", "divider", "message_box", "image_frame",'image', "map", "video", "quote", "dropcap", "pricetable", "tabs", "accordion", "advance_search", "prayer","table","flex_column","clients","spacer","heading","testimonials","infobox","promobox","offerslider","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter","member","timetable"]}, 
	{"Class": "column_67","title": "67","element": ["class","gallery", "slider", "blog", "event", "team", "column", "accordions", "team", "client", "contact", "divider", "message_box",'image', "image_frame", "map", "video", "quote", "dropcap", "pricetable", "tabs", "accordion", "advance_search", "prayer", "pointtable","table","flex_column","clients","spacer","heading","testimonials","testimonials","infobox","promobox","offerslider","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter","member","timetable"]}, 
	{"Class": "column_50","title": "50","element": ["class","gallery", "slider", "blog", "event", "team", "column", "services", "accordions", "team", "client", "contact", "column", "divider", "message_box", "image_frame",'image', "map", "video", "quote", "dropcap", "pricetable", "services", "tabs", "accordion", "advance_search", "prayer","table","flex_column","clients","spacer","heading","testimonials","infobox","promobox","offerslider","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter","member","timetable"]},
	{"Class": "column_33","title": "33","element": [,"gallery", "slider", "event", "team", "column", "accordions", "message_box",'image', "fixtures", "map", "video", "quote", "dropcap", "pricetable", "services", "tabs", "accordion", "prayer", "pointtable","flex_column","spacer","heading","testimonials","infobox","promobox","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter"]}, 
	{"Class": "column_25","title": "25","element": ["column", "divider", "message_box", "image_frame", "map", "video", "quote", "dropcap", "pricetable", "services", "pastor",'services','counter',"flex_column","spacer","heading","testimonials","infobox","promobox","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter"]}, ];

var DataElement = [{
	"ClassName": "col_width_full",
	"element": ["gallery", "slider", "blog", "event", "contact", "parallax"]
}];


var _commonshortcode = (function(id) {
	var mainConitem = jQuery("#" + id)
	var totalItemCon = mainConitem.find(".cs-wrapp-clone").size();
	mainConitem.find(".fieldCounter").val(totalItemCon);
	mainConitem.sortable({
		cancel: '.cs-clone-append .form-elements,.cs-disable-true',
		placeholder: "ui-state-highlight"
	});

});
var counter_ingredient = 0;
var html_popup = "<div id='confirmOverlay' style='display:block'> \
								<div id='confirmBox'><div id='confirmText'>Are you sure to do this?</div> \
								<div id='confirmButtons'><div class='button confirm-yes'>Delete</div>\
								<div class='button confirm-no'>Cancel</div><br class='clear'></div></div></div>"
// deleting the accordion start
jQuery("a.deleteit_node").live('click', function() {
	var mainConitem = jQuery(this).parents(".cs-wrapp-tab-box");
	jQuery(this).parent().append(html_popup);
	jQuery(this).parents(".cs-wrapp-clone").addClass("warning");
	jQuery(".confirm-yes").click(function() {
		var totalItemCon = mainConitem.find(".cs-wrapp-clone").size();
		var totalItems = jQuery(".cs-wrapp-tab-box .fieldCounter").val();
		mainConitem.find(".fieldCounter").val(totalItems - 1);
		jQuery(this).parents(".cs-wrapp-clone").fadeOut(400, function() {
			jQuery(this).remove();
		});

		jQuery("#confirmOverlay").remove();
	});

	jQuery(".confirm-no").click(function() {
		jQuery(".cs-wrapp-clone").removeClass("warning");
		jQuery("#confirmOverlay").remove();
	});
	return false;
});

//page Section items delete start
jQuery(".btndeleteitsection").live("click", function() {
	jQuery(this).parents(".parentdeletesection").addClass("warning");
	jQuery(this).parent().append(html_popup);

	jQuery(".confirm-yes").click(function() {
		jQuery(this).parents(".parentdeletesection").fadeOut(400, function() {
			jQuery(this).remove();
		});
		jQuery("#confirmOverlay").remove();
		count_widget--;
		if (count_widget == 0) jQuery("#add_page_builder_item").removeClass("hasclass");
	});
	jQuery(".confirm-no").click(function() {
		jQuery(this).parents(".parentdeletesection").removeClass("warning");
		jQuery("#confirmOverlay").remove();
	});
	return false;
});


//page Builder items delete start
jQuery(".btndeleteit").live("click", function() {
	
	jQuery(this).parents(".parentdelete").addClass("warning");
	jQuery(this).parent().append(html_popup);

	jQuery(".confirm-yes").click(function() {
		jQuery(this).parents(".parentdelete").fadeOut(400, function() {
			jQuery(this).remove();
		});
		
		jQuery(this).parents(".parentdelete").each(function(){
			var lengthitem = jQuery(this).parents(".dragarea").find(".parentdelete").size() - 1;
			jQuery(this).parents(".dragarea").find("input.textfld") .val(lengthitem);
		});

		jQuery("#confirmOverlay").remove();
		count_widget--;
		if (count_widget == 0) jQuery("#add_page_builder_item").removeClass("hasclass");
	
	});
	jQuery(".confirm-no").click(function() {
		jQuery(this).parents(".parentdelete").removeClass("warning");
		jQuery("#confirmOverlay").remove();
	});
	
	return false;
});
//page Builder items delete end

// adding social network start

function social_icon_del(id) {
	jQuery("#del_" + id).remove();
	jQuery("#" + id).remove();
}


jQuery(document).ready(function() {
	// Map Fix
	jQuery('a[href="#tab-location-settings-cs-events"]').click(function (e){
		var map = jQuery("#cs-map-location-id")[0];
		setTimeout(function(){google.maps.event.trigger(map, 'resize');},400)
     });
	// End here
	jQuery('#wrapper_boxed_layoutoptions1').click(function() {
		var theme_option_layout = jQuery('#wrapper_boxed_layoutoptions1 input[name=layout_option]:checked').val();
		if (theme_option_layout == 'wrapper_boxed') {
			jQuery("#layout-background-theme-options").show();
		} else {
			jQuery("#layout-background-theme-options").hide();
		}
	});
	jQuery('#wrapper_boxed_layoutoptions2').click(function() {
		var theme_option_layout = jQuery('#wrapper_boxed_layoutoptions2 input[name=layout_option]:checked').val();
		if (theme_option_layout == 'wrapper_boxed') {
			jQuery("#layout-background-theme-options").show();
		} else {
			jQuery("#layout-background-theme-options").hide();

		}

	});
});

//===============================================

function cs_slider_element_toggle(id) {
	if (id == 'default_header') {
		jQuery("#subheader-background-image").hide();
		jQuery("#default_header_div").show();
		jQuery("#subheader_custom_slider").hide();
		jQuery("#subheader_map").hide();
		jQuery("#subheader_no_header").hide();
	} else if (id == 'custom_slider') {
		jQuery("#subheader-background-image").hide();
		jQuery("#default_header_div").hide();
		jQuery("#subheader_custom_slider").show();
		jQuery("#subheader_map").hide();
		jQuery("#subheader_no_header").hide();
	} else if (id == 'no-header') {
		jQuery("#subheader-background-image").hide();
		jQuery("#default_header_div").hide();
		jQuery("#subheader_custom_slider").hide();
		jQuery("#subheader_map").hide();
		jQuery("#subheader_no_header").show();
	} else if (id == 'breadcrumb_header') {
		jQuery("#subheader-background-image").show();
		jQuery("#default_header_div").show();
		jQuery("#subheader_custom_slider").hide();
		jQuery("#subheader_map").hide();
		jQuery("#subheader_no_header").hide();
	}else if (id == 'map') {
		jQuery("#subheader-background-image").hide();
		jQuery("#subheader_custom_slider").hide();
		jQuery("#subheader_map").show();
		jQuery("#default_header_div").hide();
		jQuery("#subheader_no_header").hide();
	} else {
		jQuery("#subheader-background-image").hide();
		jQuery("#subheader_custom_slider").hide();
		jQuery("#subheader_map").hide();
		jQuery("#subheader_no_header").hide();
	}

}
function cs_hide_show_toggle(id,div,type) {
	
	if ( type == 'theme_options') {
		if (id == 'default') {
			jQuery("#cs_sh_paddingtop_range").hide();
			jQuery("#cs_sh_paddingbottom_range").hide();
		} else if (id == 'custom') {
			jQuery("#cs_sh_paddingtop_range").show();
			jQuery("#cs_sh_paddingbottom_range").show();
		}
		
	} else {
		if (id == 'default') {
			jQuery("#"+div).hide();
		} else if (id == 'custom') {
			jQuery("#"+div).show();
		}
	}
}
// background options

function cs_background_settings_toggle(id) {

	for (var i = 1; i <= 5; i++) {
		jQuery("#home_v" + i).hide();
	}
	if (id == "no-image") {
		jQuery("#home_v1").show();

	} else if (id == "custom-background-image") {
		jQuery("#home_v3").show();

	} else if (id == "background_video") {
		jQuery("#home_v2").show();

	} else if (id == "background_gallery") {
		jQuery("#home_v5").show();

	} else if (id == "featured-image") {
		jQuery("#home_v4").hide();

	} else if (id == "default-options") {
		jQuery("#home_v4").hide();

	} else {
		jQuery("#home_v4").show();
	}
}



function cs_section_background_settings_toggle(id, rand_no) {

	if (id == "no-image") {
		jQuery(".section-custom-background-image-" + rand_no).hide();
		jQuery(".section-slider-" + rand_no).hide();
		jQuery(".section-custom-slider-" + rand_no).hide();
		jQuery(".section-background-video-" + rand_no).hide();
	} else if (id == "section-custom-background-image") {
		jQuery(".section-slider-" + rand_no).hide();
		jQuery(".section-custom-slider-" + rand_no).hide();
		jQuery(".section-background-video-" + rand_no).hide();
		jQuery(".section-custom-background-image-" + rand_no).show();
	} else if (id == "section-slider") {
		jQuery(".section-custom-background-image-" + rand_no).hide();
		jQuery(".section-slider-" + rand_no).show();
		jQuery(".section-custom-slider-" + rand_no).hide();
		jQuery(".section-background-video-" + rand_no).hide();

	} else if (id == "section-custom-slider") {
		jQuery(".section-custom-background-image-" + rand_no).hide();
		jQuery(".section-slider-" + rand_no).hide();
		jQuery(".section-custom-slider-" + rand_no).show();
		jQuery(".section-background-video-" + rand_no).hide();

	} else if (id == "section_background_video") {
		jQuery(".section-custom-background-image-" + rand_no).hide();
		jQuery(".section-slider-" + rand_no).hide();
		jQuery(".section-custom-slider-" + rand_no).hide();
		jQuery(".section-background-video-" + rand_no).show();

	} else {
		jQuery(".section-custom-background-image-" + rand_no).hide();
		jQuery(".section-slider-" + rand_no).hide();
		jQuery(".section-custom-slider-" + rand_no).hide();
		jQuery(".section-background-video-" + rand_no).hide();
	}
}


jQuery(document).ready(function($) {
	$('.bg_color').wpColorPicker();
	/*jQuery("#date").datetimepicker({
		format: 'd.m.Y H:i'
	});*/
});

function new_toggle(id) {
	if (id == "Single Image") {
		jQuery("#post_thumb_image, #post_thumb_audio, #post_thumb_video, #post_thumb_slider, #post_thumb_map").hide();
		jQuery("#post_thumb_image").show();
	} else if (id == "Audio") {
		jQuery("#post_thumb_image, #post_thumb_audio, #post_thumb_video, #post_thumb_slider, #post_thumb_map").hide();
		jQuery("#post_thumb_audio").show();
	} else if (id == "Video") {
		jQuery("#post_thumb_image, #post_thumb_audio, #post_thumb_video, #post_thumb_slider, #post_thumb_map").hide();
		jQuery("#post_thumb_video").show();
	} else if (id == "Slider") {
		jQuery("#post_thumb_image, #post_thumb_audio, #post_thumb_video, #post_thumb_slider, #post_thumb_map").hide();
		jQuery("#post_thumb_slider").show();
	} else if (id == "Map") {
		jQuery("#post_thumb_image, #post_thumb_audio, #post_thumb_video, #post_thumb_slider, #post_thumb_map").hide();
		jQuery("#post_thumb_map").show();
	} else jQuery("#post_thumb_image, #post_thumb_audio, #post_thumb_video, #post_thumb_slider, #post_thumb_map").hide();
}

function new_toggle_inside_post(id) {
	if (id == "Single Image") {
		jQuery("#inside_post_thumb_image, #inside_post_thumb_audio, #inside_post_thumb_video, #inside_post_thumb_slider, #inside_post_thumb_map").hide();
		jQuery("#inside_post_thumb_image").show();
	} else if (id == "Audio") {
		jQuery("#inside_post_thumb_image, #inside_post_thumb_audio, #inside_post_thumb_video, #inside_post_thumb_slider, #inside_post_thumb_map").hide();
		jQuery("#inside_post_thumb_audio").show();
	} else if (id == "Video") {
		jQuery("#inside_post_thumb_image, #inside_post_thumb_audio, #inside_post_thumb_video, #inside_post_thumb_slider, #inside_post_thumb_map").hide();
		jQuery("#inside_post_thumb_video").show();
	} else if (id == "Slider") {
		jQuery("#inside_post_thumb_image, #inside_post_thumb_audio, #inside_post_thumb_video, #inside_post_thumb_slider, #inside_post_thumb_map").hide();
		jQuery("#inside_post_thumb_slider").show();
	} else if (id == "Map") {
		jQuery("#inside_post_thumb_image, #inside_post_thumb_audio, #inside_post_thumb_video, #inside_post_thumb_slider, #inside_post_thumb_map").hide();
		jQuery("#inside_post_thumb_map").show();
	} else jQuery("inside_post_thumb_image, #inside_post_thumb_audio, #inside_post_thumb_video, #inside_post_thumb_slider, #inside_post_thumb_map").hide();
}


//
function cs_show_slider(value){
	if(value=='Revolution Slider'){
  		jQuery('#tab-sub-header-options ul,#tab-sub-header-options #cs_background_img_box').hide();
		jQuery('#cs_default_header_header').show();
		jQuery('#cs_custom_slider_1').show();
	}else if(value=='No sub Header'){
		jQuery('#tab-sub-header-options ul,#tab-sub-header-options #cs_background_img_box').not('#tab-sub-header-options ul#cs_header_border_color_color').hide();
		jQuery('#cs_default_header_header,#tab-sub-header-options ul#cs_header_border_color_color').show();
	} else{
		jQuery('#tab-sub-header-options ul,#tab-sub-header-options #cs_background_img_box').show();
		jQuery('#cs_custom_slider_1,#cs_header_border_color_color').hide();
	}
	
	
}

jQuery(document).ready(function($){
	$('textarea.header_code_indent').keydown(function(e) {
		if(e.keyCode == 9) {
		  var start = $(this).get(0).selectionStart;
		  $(this).val($(this).val().substring(0, start) + "    " + $(this).val().substring($(this).get(0).selectionEnd));
		  $(this).get(0).selectionStart = $(this).get(0).selectionEnd = start + 4;
		  return false;
		}
	});
});

function _iconSearch() {
	   jQuery('.icp-auto').iconpicker({
			title: 'Choose an Icon',
			selectedCustomClass: 'cs-search-icon-hidden',
			hideOnSelect: true,
			selected: true, // use this value as the current item and ignore the original
			defaultValue: false,
		});			
}
function cs_add_field(id,type){
	var wrapper         = jQuery("#"+id+" .input_fields_wrap"); //Fields wrapper
	var items			= jQuery("#"+id+" .input_fields_wrap > div").length + 1;
	
	var uniqueNum 		= type+'_'+Math.floor( Math.random()*99999 );
	
	var remove = 'javascript:cs_remove_field("'+uniqueNum+'","'+id+'")';
	
	jQuery("#"+id+"  .counter_num").val(items);
	
	jQuery(wrapper).append('<div class="cs-wrapp-clone cs-shortcode-wrapp  cs-pbwp-content" id="'+uniqueNum+'"><ul class="form-elements bcevent_title"><li class="to-label"><label>Pricing Feature '+items+'</label></li><li class="to-field"><div class="input-sec"><textarea data-content-text="cs-shortcode-textarea" name="cs_pricing_feature[]"></textarea></div><div id="price_remove"><a class="remove_field" onclick='+remove+'><i class="icon-minus-circle" style="color:#000; font-size:18px"></i></div></a></li></ul></div>'); //add input box
}

function cs_remove_field(id,wrapper){
	var totalItems	= jQuery("#"+wrapper+"  .counter_num").val() - 1;
	jQuery("#"+wrapper+"  .counter_num").val(totalItems);
	jQuery("#"+wrapper+" #"+id+"").remove();
}

jQuery('#tab-location-settings-cs-events').bind('tabsshow', function(event, ui) {
    if (ui.panel.id == "map-tab") {
        resizeMap();
    }
});

// Members 
function cs_members_all_tab(value, counter){
	if(value == 'on'){
		jQuery('#members_all_tab'+counter).show();
	} else {
		jQuery('#members_all_tab'+counter).hide();
	}
}


function del_media(id) {
	var $ = jQuery;
	jQuery('#' + id + '_box').hide();
	jQuery('#' + id).val('');
}

 function _createclone(object,id,section,post){

		var _this = object.closest(".column");
		_this.clone().insertAfter(_this);
		//jQuery('.bg_color').wpColorPicker();
		callme();
		jQuery( ".draginner" ) .sortable({
				connectWith: '.draginner',
				handle:'.column-in',
				cancel:'.draginner .poped-up,#confirmOverlay',
				revert:false,
				start: function( event, ui ) {jQuery(ui.item).css({"width":"25%"})},
				receive: function( event, ui ) {callme(); getsorting (ui)},
				placeholder: "ui-state-highlight",
				forcePlaceholderSize:true
		 });
		return false;
  }

	function ajax_shortcode_widget_element(object,admin_url,POSTID,name){
			var wraper	=  object.closest(".column-in") .next();
			var _structure = "<div id='cs-pbwp-outerlay'><div id='cs-widgets-list'></div></div>",
			$elem = jQuery('#cs-widgets-list');
			
			jQuery(wraper).wrap(_structure).delay(100).fadeIn(150);
			var shortcodevalue = object.closest(".column-in") .next().find(".cs-textarea-val").val();
			if(shortcodevalue){
				
				var elementnamevalue = object.closest(".column-in") .next().find(".cs-dcpt-element").val();
				SuccessLoader ();
				//_createpop(wraper, "filterdrag");
				counter++;
				var dcpt_element_data = '';
				if(elementnamevalue){
					var dcpt_element_data = '&element_name=' + elementnamevalue;
				}
				var random_num = Math.floor((Math.random() * 56855367) + 1);
				var newCustomerForm = "action=cs_pb_" + name + '&counter=' + random_num + '&shortcode_element_id=' + encodeURIComponent(shortcodevalue) + '&POSTID=' + POSTID + dcpt_element_data;
				var edit_url = action + counter;
				//_createpop();
				jQuery.ajax({
					type:"POST",
					url: admin_url,
					data: newCustomerForm,
					success:function(data){
					rsponse = jQuery(data);
					var response_html = rsponse.find(".cs-pbwp-content").html();	
					object.closest(".column-in") .next() .find(".pagebuilder-data-load").html(response_html);
					object.closest(".column-in") .next().find(".cs-wiget-element-type").val('form');
					jQuery('.loader').remove();
						jQuery('.bg_color').wpColorPicker(); 
						jQuery('div.cs-drag-slider').each(function() {
						var _this = jQuery(this);
							_this.slider({
								range:'min',
								step: _this.data('slider-step'),
								min: _this.data('slider-min'),
								max: _this.data('slider-max'),
								value: _this.data('slider-value'),
								slide: function (event, ui) {
									jQuery(this).parents('li.to-field').find('.cs-range-input').val(ui.value)
								}
							});
						});
						  jQuery( ".draginner" ) .sortable({
							connectWith: '.draginner',
							handle:'.column-in',
							cancel:'.draginner .poped-up,#confirmOverlay',
							revert:false,
							receive: function( event, ui ) {callme(); getsorting (ui)},
							placeholder: "ui-state-highlight",
							forcePlaceholderSize:true
							
					   });
					}
				});
			}
		}

	
	function _removerlay (object) {
			jQuery("#cs-widgets-list .loader").remove();
				var _elem1 = "<div id='cs-pbwp-outerlay'></div>",
					_elem2 = "<div id='cs-widgets-list'></div>";
				$elem = object.closest('div[class*="cs-wrapp-class-"]') ;
				$elem.unwrap(_elem2);
				$elem.unwrap(_elem1);
				$elem.hide()
		}
	
	function _createpopshort (object) {
			var _structure = "<div id='cs-pbwp-outerlay'><div id='cs-widgets-list'></div></div>",
			$elem = jQuery('#cs-widgets-list');
			var a = object.closest(".column-in").next();
			jQuery(a).wrap(_structure).delay(100).fadeIn(150);
		}
	function _createpop(data, type) {

		var _structure = "<div id='cs-pbwp-outerlay'><div id='cs-widgets-list'></div></div>",
			$elem = jQuery('#cs-widgets-list');
		jQuery('body').addClass("cs-overflow");
		if (type == "csmedia") {
			$elem.append(data);
		}
		if (type == "filter") {
			jQuery('#' + data).wrap(_structure).delay(100).fadeIn(150);
			jQuery('#' + data).parent().addClass("wide-width");
		}
		if (type == "filterdrag") {
			jQuery('#' + data).wrap(_structure).delay(100).fadeIn(150);
		}

		}


	
	// Post xml import
 

// Header Options
function cs_header_option(val){
	if(val=='none'){
		jQuery('#cs_rev_slider,#cs_headerbg_image_div').hide();
	}else if(val=='cs_rev_slider'){
				jQuery('#cs_rev_slider').fadeIn();
				jQuery('#cs_headerbg_image_div').hide();
	}else if(val=='cs_bg_image_color'){
				jQuery('#cs_headerbg_image_div').fadeIn();
				jQuery('#cs_rev_slider').hide();
	}
}


/* ---------------------------------------------------------------------------
	 * Toggle Function
* --------------------------------------------------------------------------- */
jQuery(document).ready(function(){
	jQuery(".hidediv").hide();
	  jQuery(".showdiv").click(function(){
		  jQuery(this).parents("article").stop().find(".hidediv").toggle(300);
	  });
});



var counter_faq = 0;
function add_faq_to_list(admin_url, theme_url) {
	counter_faq++;
	var dataString = 'counter_faq=' + counter_faq +
		'&directory_faq_title=' + jQuery("#faq_title").val() +
		'&directory_faq_description=' + jQuery("#faq_description").val() +
		'&action=cs_add_faq_to_list';
	jQuery("#faq-loading").html("<img src='" + theme_url + "/include/assets/images/ajax_loading.gif' alt=''/>");
	jQuery.ajax({
		type: "POST",
		url: admin_url,
		data: dataString,
		success: function(response) {
			jQuery("#total_faq").append(response);
			jQuery("#faq-loading").html("");
			removeoverlay('add_faq_title', 'append');
			jQuery("#faq_title").val("Title");
			jQuery("#faq_description").val("");
		}
	});
	return false;
}

var counter_pattren = 0;
function add_pattren_to_list(admin_url, theme_url, id) {
	counter_pattren++;
	var dataString = 'counter_pattren=' + counter_pattren +
		'&directory_pattren_title=' + jQuery("#pattren_title").val() +
		'&directory_pattren_icon=' + jQuery("#e9_element_"+id).val() +
		'&directory_pattren_description=' + jQuery("#pattren_description").val() +
		'&action=cs_add_pattren_to_list';
	jQuery("#pattren-loading").html("<img src='" + theme_url + "/include/assets/images/ajax_loading.gif' alt=''/>");
	jQuery.ajax({
		type: "POST",
		url: admin_url,
		data: dataString,
		success: function(response) {
			jQuery("#total_pattren").append(response);
			jQuery("#pattren-loading").html("");
			removeoverlay('add_pattren_title', 'append');
			jQuery("#pattren_title").val("Title");
			jQuery("#pattren_icon").val("icon-star");
			jQuery("#pattren_description").val("");
		}
	});
	return false;
}

var counter_social = 0;
function add_social_to_list(admin_url, theme_url, id) {
	counter_social++;
	var	id	= "#cs_infobox_"+id;
	var social_net_awesome = jQuery(id+" .selected-icon i").attr("class");
	var dataString = 'counter_social=' + counter_social +
		'&social_title=' + jQuery("#social_title").val() +
		'&cs_social_icon=' + social_net_awesome +
		'&var_cp_url=' + jQuery("#var_cp_url").val() +
		'&action=cs_add_social_to_list';
	jQuery("#social-loading").html("<img src='" + theme_url + "/include/assets/images/ajax_loading.gif' alt='' />");
	jQuery.ajax({
		type: "POST",
		url: admin_url,
		data: dataString,
		success: function(response) {
			jQuery("#total_socials").append(response);
			jQuery("#social-loading").html("");
			removeoverlay('add_social_title', 'append');
			jQuery("#social_title").val("Title");
			jQuery("#var_cp_image_url").val("");
			jQuery("#var_cp_url").val("");
		}
	});
	return false;
}


var counter_dynamic_fields = 0;
function add_dynamic_fields_to_list(admin_url, theme_url) {
	counter_dynamic_fields++;
	var dataString = 'counter_dynamic_fields=' + counter_dynamic_fields +
		'&directory_dynamic_fields_title=' + jQuery("#dynamic_fields_title").val() +
		'&directory_dynamic_fields_description=' + jQuery("#dynamic_fields_description").val() +
		'&action=cs_add_dynamic_fields_to_list';
	jQuery("#fields-loading").html("<img src='" + theme_url + "/include/assets/images/ajax_loading.gif' />");
	jQuery.ajax({
		type: "POST",
		url: admin_url,
		data: dataString,
		success: function(response) {
			jQuery("#total_dynamic_fieldss").append(response);
			jQuery("#fields-loading").html("");
			removeoverlay('add_dynamic_fields_title', 'append');
			jQuery("#dynamic_fields_title").val("Title");
			jQuery("#dynamic_fields_description").val("");
		}
	});
	return false;
}


jQuery(".uploadMediaInput").live('click', function() {
	var $ = jQuery;
	var id = $(this).attr("name");
	var custom_uploader = wp.media({
		title: 'Select File',
		button: {
			text: 'Add File'
		},
		multiple: false
	})
		.on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			jQuery('#' + id).val(attachment.url);
		}).open();
		
});

function cs_table_td_remove(id ){
	jQuery("#"+id ).html("");
}
	
function calculateTime(valuestart, valuestop) {
	var timeStart = new Date("01/01/2007 " + valuestart).getMinutes();
	var timeEnd = new Date("01/01/2007 " + valuestop).getMinutes();
	
	return hourDiff = timeEnd - timeStart;    
}
	
		
/*function openpopedup(id){
  var $ = jQuery;
	$(".elementhidden,.opt-head,.option-sec,.to-table thead,.to-table tr")  .hide();
	$("#"+id) .parents("tr") .show();
	$("#"+id) .parents("td") .css("width","100%");
	$("#"+id) .parents("td") .prev() .hide();
	$("#"+id) .parents("td") .find("a.actions") .hide();
	$("#"+id).children(".opt-head") .show();
    $("#"+id).slideDown();
   
  $("#"+id).animate({
   top: 0,
  }, 400, function() {
  });
  $.scrollTo( '#normal-sortables', 800, {easing:'swing'} );
 };
 
function closepopedup(id){
  var $ = jQuery;
  $("#"+id).slideUp(800);

	$(".to-table tr") .css("width","");
	$(".elementhidden,.opt-head,.option-sec,.to-table thead,.to-table tr,a.actions,.to-table tr td").delay(600)  .fadeIn(200);
	
	$.scrollTo( '.elementhidden', 800, {easing:'swing'} );
 };
*/
function cs_showhide_option(value){
	if(value == 'style_4'){
		jQuery("#link_url").show();
	}else{
		jQuery("#video_code").hide();
	}
}