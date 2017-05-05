function theme_option_save(admin_url, theme_url){
  			jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);
			function newValues() {
			  var serializedValues = jQuery("#frm input,#frm select,#frm textarea[name!=cs_export_theme_options]").serialize();
			  return serializedValues;
			}
			var serializedReturn = newValues();
             jQuery.ajax({
                type:"POST",
                url: admin_url,
				data:serializedReturn, 
                success:function(response){
					
					jQuery(".loading_div").hide();
                    jQuery(".form-msg .innermsg").html(response);
					jQuery(".form-msg").show();
					jQuery(".outerwrapp-layer").delay(100).fadeOut(100)
					
                    slideout();
					window.location.reload(true);
                }
            });
            //return false;
        }
		
function cs_badge_save(admin_url, theme_url){
  			jQuery(".outerwrapp-layer,.loading_div").fadeIn(100);
			
			function newValues() {
			  var serializedValues = jQuery("#bdg_form input,#bdg_form select,#bdg_form input[name!=action]").serialize();
			  return serializedValues;
			}
			var serializedReturn = newValues();
             jQuery.ajax({
                type:"POST",
                url: admin_url,
				data:serializedReturn, 
                success:function(response){
					
					jQuery(".loading_div").hide();
                    jQuery(".form-msg .innermsg").html(response);
					jQuery(".form-msg").show();
					jQuery(".outerwrapp-layer").delay(100).fadeOut(100)
					//window.location.reload(true);
                    slideout();
                }
            });
            //return false;
        }
		
 jQuery(document).ready(function($){
	$('.bg_color').wpColorPicker(); 
 });
 function cs_rest_all_options(admin_url, theme_url){
 			//jQuery(".loading_div").show('');
			var var_confirm = confirm("You current theme options will be replaced with the default theme activation options.");
			if ( var_confirm == true ){
				var dataString = 'action=theme_option_rest_all';
				jQuery.ajax({
					type:"POST",
					url: admin_url+"/admin-ajax.php",
					data: dataString,
					success:function(response){
						
						jQuery(".form-msg").show();
						jQuery(".form-msg").html(response);
						jQuery(".loading_div").hide();
						//var loc = window.location;
						var url = admin_url+"/themes.php?page=cs_options_page";
						//window.location.reload(true);
						//slideout();
					}
				});
			}
            //return false;
		}

 function cs_remove_image(id){
 		var $ = jQuery;
 		$('#'+id).val('');
		$('#'+id+'_img_div').hide();
		//$('#'+id+'_div').attr('src', '');
	}
	
function social_icon_del(id){
	jQuery("#del_"+id).remove();
	jQuery("#"+id).remove();
}

function cs_google_font_att(admin_url, att_id, id){
	
	var $ = jQuery;
	if ( att_id != "" ) {
		jQuery('#'+id).parent().next().remove(0);
		jQuery('#'+id).parent().parent().append('<i style="font-size:20px;color:#ff6363;" class="icon-spinner8 icon-spin"></i>');
		jQuery('#'+id).parent().parent().css('text-align','center');
		jQuery('#'+id).parent().hide(0);		
		var dataString = 'index=' + att_id + '&id='+ id + 
						'&action=cs_get_google_font_attributes';
		jQuery.ajax({
			type:"POST",
			url: admin_url,
			data: dataString,
			success:function(response){
				jQuery('#'+id).parent().show(0);	
				jQuery('#'+id).parent().html(response);
				jQuery('#'+id).parent().next().remove(0);
				
			}
		});
		//return false;
	}
}

var counter_google_font = 0;
function cs_add_google_font(admin_url){
	counter_google_font++;
	var google_font_name_path = jQuery("#google_font_name_input").val();
	var google_font_url_path = jQuery("#google_font_url_input").val();

	if ( google_font_name_path != "" && google_font_url_path != "" ) {
		var dataString = 'google_font_family_name=' + google_font_name_path + 
						'&google_font_family_url=' + google_font_url_path +
						'&action=add_google_fonts';
		jQuery.ajax({
			type:"POST",
			url: admin_url,
			data: dataString,
			success:function(response){
				jQuery("#google_fonts_area").append(response);
				jQuery(".gfonts-area").show(200);
				jQuery("#google_font_name_input,#google_font_url_input").val("");
			}
		});
		//return false;
	}
}
 	
var counter_social_network = 0;
function cs_add_social_icon(admin_url){
	counter_social_network++;
	var social_net_icon_path = jQuery("#social_net_icon_path_input").val();
	var social_net_awesome = jQuery(".selected-icon i").attr("class");
	//var social_net_color_input = jQuery("#social_net_color_input").val();
	var social_net_url = jQuery("#social_net_url_input").val();
	var social_net_tooltip = jQuery("#social_net_tooltip_input").val();
	var social_font_awesome_color = jQuery("#social_font_awesome_color").val();
	if ( social_net_url != "" && (social_net_icon_path != "" || social_net_awesome != "" ) ) {
		var dataString = 'social_net_icon_path=' + social_net_icon_path + 
						'&social_net_awesome=' + social_net_awesome +
						'&social_net_url=' + social_net_url +
						'&social_net_tooltip=' + social_net_tooltip +
						'&counter_social_network=' + counter_social_network +
						'&social_font_awesome_color=' + social_font_awesome_color +
						'&action=add_social_icon';
		//jQuery("#loading").html("<img src='"+theme_url+"/include/assets/images/ajax_loading.gif' />");
		jQuery.ajax({
			type:"POST",
			url: admin_url,
			data: dataString,
			success:function(response){
				jQuery("#social_network_area").append(response);
				jQuery(".social-area").show(200);
				jQuery("#social_net_icon_path_input,#social_net_awesome_input,#social_net_url_input,#social_net_tooltip_input").val("");
				jQuery("#social_font_awesome_color").val("#fff");
			}
		});
		//return false;
	}
}

var counter_badges = 0;
function cs_add_badges(admin_url){
	counter_badges++;
	
	var badges_net_icons = jQuery("#badges_net_icons_input").val();
	var badges_net_icons_short_name = jQuery("#badges_net_icons_short_name_input").val();
	var badges_net_icons_paths = jQuery("#badges_net_icons_paths_input").val();
	if ( badges_net_icons != "" && (badges_net_icons_short_name != "" || badges_net_icons_paths != "" ) ) {
		jQuery(".badge_spiner").show();
		var dataString = 'badges_net_icons=' + badges_net_icons + 
						'&badges_net_icons_short_name=' + badges_net_icons_short_name +
						'&badges_net_icons_paths=' + badges_net_icons_paths +
						'&counter_badges=' + counter_badges +
						'&action=add_badge';
		
		jQuery.ajax({
			type:"POST",
			url: admin_url,
			data: dataString,
			success:function(response){
				jQuery(".badge_spiner").hide();
				jQuery("#badges_area").append(response);
				jQuery(".badges-area").show(200);
				jQuery("#badges_net_icons_input,#badges_net_icons_short_name_input,#badges_net_icons_paths_input").val("");
				jQuery("#badges_net_icons_paths_input_box img").attr('src', '');
				jQuery("#badges_net_icons_paths_input_box").hide();
				jQuery(".browse-icon input#badges_net_icons_paths_input").val('Brows');
				
			}
		});
		//return false;
	}
}
	
function select_bg(layout,value,theme_url,admin_url) {
	var $ = jQuery;
	
 	jQuery('input[name="'+layout+'"]').live('click', function(){
 		jQuery(this).parent().parent().find(".check-list").removeClass("check-list");
		jQuery(this).siblings("label").children("#check-list").addClass("check-list");
		jQuery(this).addClass('selected').siblings().removeClass('selected');
	});
	if(value=='boxed' && layout=='cs_layout'){
		jQuery('.horizontal_tabs,.main_tab').show();
	}else if(value=='full_width' && layout=='cs_layout'){
		jQuery('.horizontal_tabs,.main_tab').hide();
	}
}
function cs_toggle(id) {
	jQuery("#" + id).slideToggle("slow");
	jQuery("#" + id + " #cs-icon-wrap").removeClass('hideicon').addClass('hideicon');
}

function cs_div_remove(id) {
	jQuery("#" + id).remove();
}

var counter_sidebar = 0;
        function add_sidebar(){
            counter_sidebar++;
            var sidebar_input = jQuery("#sidebar_input").val();
            if ( sidebar_input != "" ) {
                jQuery("#sidebar_area").append('<tr id="'+counter_sidebar+'"> \
                            <td><input type="hidden" name="sidebar[]" value="'+sidebar_input+'" />'+sidebar_input+'</td> \
                            <td class="centr"> <a class="remove-btn" onclick="javascript:return confirm(\'Are you sure! You want to delete this\')" href="javascript:cs_div_remove('+counter_sidebar+')"><i class="icon-times"></i></a> </td> \
                        </tr>');
                jQuery("#sidebar_input").val("");
				jQuery(".sidebar-area").slideDown();
            }
}

// set header bg options

function cs_set_headerbg(value){

   	if(value=='absolute'){

  		jQuery('#cs_headerbg_options_header,#cs_headerbg_image_upload,#cs_headerbg_color_color,#cs_headerbg_slider_1,#cs_headerbg_image_box').show();
				if(jQuery('#cs_headerbg_options').val()=='cs_rev_slider'){
					jQuery('#cs_headerbg_slider_1').show();
					jQuery('#cs_headerbg_image_upload,#cs_headerbg_color_color,#tab-header-options #cs_headerbg_image_box').hide();
				}else if(jQuery('#cs_headerbg_options').val()=='cs_bg_image_color'){
					jQuery('#cs_headerbg_slider_1').hide();
					jQuery('#cs_headerbg_image_upload,#cs_headerbg_color_color,#tab-header-options #cs_headerbg_image_box').show();
				}else{
					jQuery('#cs_headerbg_slider_1').hide();
					jQuery('#cs_headerbg_image_upload,#cs_headerbg_color_color,#tab-header-options #cs_headerbg_image_box,#cs_headerbg_slider_1').hide();
				}

	}else if(value=='relative'){

		jQuery('#cs_headerbg_options_header,#cs_headerbg_image_upload,#cs_headerbg_color_color,#cs_headerbg_slider_1,#tab-header-options #cs_headerbg_image_box').hide();

 	}else if(value=='cs_rev_slider'){ 

		jQuery('#cs_headerbg_slider_1').show();

		jQuery('#cs_headerbg_image_upload,#cs_headerbg_color_color,#tab-header-options #cs_headerbg_image_box').hide();

	}else if(value=='cs_bg_image_color'){

		jQuery('#cs_headerbg_slider_1').hide();

		jQuery('#cs_headerbg_image_upload,#cs_headerbg_color_color,#tab-header-options #cs_headerbg_image_box').show();

 	}else if(value=='none'){

		jQuery('#cs_headerbg_slider_1').hide();

		jQuery('#cs_headerbg_image_upload,#cs_headerbg_color_color,#tab-header-options #cs_headerbg_image_box,#cs_headerbg_slider_1').hide();

	}

 }
 
 
var counter_member_field = 0;
function cs_add_member_fields(admin_url, theme_url){
	counter_member_field++;

	var member_field = jQuery("#member_field_input").val();
	var member_field_value =  jQuery("#member_field_value_input").val();
	if ( member_field != "" && member_field_value != "" ) {
		jQuery("#member-loader").html("<img src='" + theme_url + "/include/assets/images/ajax_loading.gif' alt=''/>");
		var dataString = 'member_field=' + member_field + 
						'&member_field_value=' + member_field_value +
						'&action=cs_add_member_fields';
		jQuery.ajax({
			type:"POST",
			url: admin_url,
			data: dataString,
			success:function(response){
				jQuery("#member-loader").html("");
				jQuery("#cs_member_fields").append(response);
				jQuery(".member-fields-area").show(200);
				jQuery("#member_field_input,#member_field_value_input").val("");
			}
		});
		//return false;
	} else {
		alert("Oops! please enter field name and its value.");
	}
}