<?php
 
//======================================================================
// Adding Project Start
//======================================================================
if (!function_exists('cs_project_shortcode')) {
	function cs_project_shortcode( $atts ) {
		global $post, $wpdb, $cs_theme_options, $page_element_size,$cs_xmlObject;
		$defaults = array('column_size'=>'','cs_project_section_title'=>'','cs_project_view'=>'','cs_project_cat'=>'','cs_project_num_post'=>'10','cs_project_pagination'=>'','cs_project_class' => '','cs_project_animation' => '');
		
		extract( shortcode_atts( $defaults, $atts ) );

		if (isset($cs_xmlObject->sidebar_layout) && $cs_xmlObject->sidebar_layout->cs_page_layout <> '' and $cs_xmlObject->sidebar_layout->cs_page_layout <> "none"){				
				$cs_project_medium_layout = 'col-md-4';
		}else{
				$cs_project_medium_layout = 'col-md-3';	
		}
		$html = '';
		$CustomId = '';
		if ( isset( $cs_project_class ) && $cs_project_class ) {
			$CustomId = ' id="'.$cs_project_class.'"';
		}
		
		if ( trim($cs_project_animation) != '' ) {
			$cs_project_animation = ' class="wow'.' '.$cs_project_animation.'"';
		} else {
			$cs_project_animation = '';
		}
		
		$projectTemplates = new ProjectTemplates();
		
		if($cs_project_view == 'grid-view'){
			$html .= $projectTemplates->cs_grid_view( $atts, $cs_project_medium_layout );
		}else if($cs_project_view == 'no-gutter' || $cs_project_view == 'gutter'){
			$html .= $projectTemplates->cs_gutter_view( $atts );
		}else if($cs_project_view == 'modern'){
			$html .= $projectTemplates->cs_modern_view( $atts );
		}else if($cs_project_view == 'mesonry'){
			$html .= $projectTemplates->cs_mesonry_view( $atts );
		}else{
			$html .= $projectTemplates->cs_grid_view( $atts );
		}
    }
	
	add_shortcode( 'cs_project', 'cs_project_shortcode' );
}

//=====================================================================
// Portfolio Location Fields
//=====================================================================
if ( ! function_exists( 'cs_location_fields' ) ) {
	function cs_location_fields(){
		global $cs_xmlObject;
		if ( isset($cs_xmlObject)) {
			if(isset($cs_xmlObject->dynamic_post_location_latitude)){ $dynamic_post_location_latitude = $cs_xmlObject->dynamic_post_location_latitude;} else {$dynamic_post_location_latitude = '';}
			if(isset($cs_xmlObject->dynamic_post_location_longitude)){ $dynamic_post_location_longitude = $cs_xmlObject->dynamic_post_location_longitude;} else {$dynamic_post_location_longitude = '';}
			if(isset($cs_xmlObject->dynamic_post_location_zoom)){ $dynamic_post_location_zoom = $cs_xmlObject->dynamic_post_location_zoom;} else {$dynamic_post_location_zoom = '';}
			if(isset($cs_xmlObject->dynamic_post_location_address)){ $dynamic_post_location_address = $cs_xmlObject->dynamic_post_location_address;} else {$dynamic_post_location_address = '';}
			if(isset($cs_xmlObject->loc_city)){ $loc_city = $cs_xmlObject->loc_city;} else {$loc_city = '';}
			if(isset($cs_xmlObject->loc_postcode)){ $loc_postcode = $cs_xmlObject->loc_postcode;} else {$loc_postcode = '';}
			if(isset($cs_xmlObject->loc_region)){ $loc_region = $cs_xmlObject->loc_region;} else {$loc_region = '';}
			if(isset($cs_xmlObject->loc_country)){ $loc_country = $cs_xmlObject->loc_country;} else {$loc_country = '';}
			if(isset($cs_xmlObject->event_map_switch)){ $event_map_switch = $cs_xmlObject->event_map_switch;} else {$event_map_switch = '';}
			if(isset($cs_xmlObject->event_map_heading)){ $event_map_heading = $cs_xmlObject->event_map_heading;} else {$event_map_heading = '';}
	
		}
		else {
			$dynamic_post_location_latitude = '';
			$dynamic_post_location_longitude = '';
			$dynamic_post_location_zoom = '';
			$dynamic_post_location_address = '';
			$loc_city = '';
			$loc_postcode = '';
			$loc_region = '';
			$loc_country = '';
			$event_map_switch = '';
			$event_map_heading = 'Event Location';
		}							
		cs_enqueue_location_gmap_script();
			?>
   
	<fieldset class="gllpLatlonPicker"  style="width:100%; float:left;">
	  <div class="page-wrap page-opts left" style="overflow:hidden; position:relative;">
		<div class="option-sec" style="margin-bottom:0;">
		  <div class="opt-conts">
			<ul class="form-elements">
              <li class="to-label">
                <label>Location Map</label>
              </li>
              <li class="to-field has_input">
                <div class="input-sec">
                  <label class="pbwp-checkbox">
                    <input type="hidden" name="event_map_switch" value=""/>
                    <input type="checkbox" class="myClass" name="event_map_switch" <?php if (isset($cs_xmlObject->event_map_switch) && $cs_xmlObject->event_map_switch == "on") echo "checked" ?>/>
                    <span class="pbwp-box"></span> </label>
                    <input type="text" name="event_map_heading" value="<?php echo esc_attr( htmlspecialchars( $event_map_heading ) );?>" />
                </div>
              </li>
            </ul>
            <ul class="form-elements">
			  <li class="to-label">
				<label>Address</label>
			  </li>
			  <li class="to-field">
				<input name="dynamic_post_location_address" id="loc_address" type="text" value="<?php echo esc_attr( htmlspecialchars( $dynamic_post_location_address ) )?>" class="gllpSearchButton" onBlur="gll_search_map()" />
			  </li>
			</ul>
			
            <ul class="form-elements">
			  <li class="to-label">
				<label>City / Town</label>
			  </li>
			  <li class="to-field">
				<input name="loc_city" id="loc_city" type="text" value="<?php echo esc_attr( htmlspecialchars( $loc_city ) );?>" class="gllpSearchButton" onBlur="gll_search_map()" />
			  </li>
			</ul>
			<ul class="form-elements">
			  <li class="to-label">
				<label>Post Code</label>
			  </li>
			  <li class="to-field">
				<input name="loc_postcode" id="loc_postcode" type="text" value="<?php echo esc_attr( htmlspecialchars( $loc_postcode ) );?>" class="gllpSearchButton" onBlur="gll_search_map()" />
			  </li>
			</ul>
			<ul class="form-elements">
			  <li class="to-label">
				<label>Region</label>
			  </li>
			  <li class="to-field">
				<input name="loc_region" id="loc_region" type="text" value="<?php echo esc_attr( htmlspecialchars( $loc_region ) );?>" class="gllpSearchButton" onBlur="gll_search_map()" />
			  </li>
			</ul>
			<ul class="form-elements">
			  <li class="to-label">
				<label>Country</label>
			  </li>
			  <li class="to-field">
				<select name="loc_country" id="loc_country" class="gllpSearchButton" onBlur="gll_search_map()" >
				  <?php foreach( cs_get_countries() as $key => $val ):?>
				  <option <?php if($loc_country==$val)echo "selected"?> ><?php echo esc_attr( $val );?></option>
				  <?php endforeach; ?>
				</select>
			  </li>
			</ul>
			<ul class="form-elements">
			  <li class="to-label">
				<label></label>
			  </li>
			  <li class="to-field">
				<input type="button" class="gllpSearchButton" value="Search This Location on Map" onClick="gll_search_map()">
			  </li>
			</ul>
			<ul class="form-elements " style="float: left;" >
			  <li>
				<div class="clear"></div>
                <div class="clear"></div>
                 <div style="float:left; width:100%; height:100%;">
                  <div class="gllpMap" id="cs-map-location-id"></div>
                </div>
				<input type="hidden" name="add_new_loc" class="gllpSearchField" style="margin-bottom:10px;" >
				
				<input type="hidden" name="dynamic_post_location_latitude" value="<?php echo esc_attr( $dynamic_post_location_latitude );?>" class="gllpLatitude" />
				<input type="hidden" name="dynamic_post_location_longitude" value="<?php echo esc_attr( $dynamic_post_location_longitude );?>" class="gllpLongitude" />
				<input type="hidden" name="dynamic_post_location_zoom" value="<?php echo esc_attr( $dynamic_post_location_zoom );?>" class="gllpZoom" />
				<input type="button" class="gllpUpdateButton" value="update map" style="display:none">
				
			  </li>
			</ul>
           
		  </div>
		</div>
	  </div>
      
      
      <?php /*?><script type="text/javascript">
		  function initialize() {
			
			gllpLatitude	=  jQuery('.gllpLatitude').val();
			gllpLongitude	=  jQuery('.gllpLongitude').val();
			
			var mapOptions = {
			  center: { lat: gllpLatitude, lng: gllpLongitude},
			  zoom: 11
			};
			var map = new google.maps.Map(document.getElementById('cs-map-location-id'),
				mapOptions);
		  }
		  google.maps.event.addDomListener(window, 'load', initialize);
     
     </script><?php */?>


      <script>
/*		jQuery(document).ready(function() {
			setTimeout(function(){
				 gll_search_map();
			},10000);
		  });*/
		 
		jQuery(document).ready(function(){
			jQuery('.tab-location-map').click(function() {
					 gll_search_map();
			}); 
		});
	</script>
	</fieldset>
    
	<?php
	}
}

//=====================================================================
// Project Gallery
//=====================================================================
if ( ! function_exists( 'cs_project_gallery' ) ) {
	function cs_project_gallery() {
		global $cs_xmlObject;
		if ( empty($cs_xmlObject->post_thumb_view) ) $post_thumb_view = ""; else $post_thumb_view = $cs_xmlObject->post_thumb_view;
		if ( empty($cs_xmlObject->post_thumb_audio) ) $post_thumb_audio = ""; else $post_thumb_audio = $cs_xmlObject->post_thumb_audio;
		if ( empty($cs_xmlObject->post_thumb_video) ) $post_thumb_video = ""; else $post_thumb_video = $cs_xmlObject->post_thumb_video;
		if ( empty($cs_xmlObject->post_thumb_slider) ) $post_thumb_slider = ""; else $post_thumb_slider = $cs_xmlObject->post_thumb_slider;
		if ( empty($cs_xmlObject->post_thumb_slider_type) ) $post_thumb_slider_type = ""; else $post_thumb_slider_type = $cs_xmlObject->post_thumb_slider_type;
		if ( empty($cs_xmlObject->inside_post_thumb_view) ) $inside_post_thumb_view = ""; else $inside_post_thumb_view = $cs_xmlObject->inside_post_thumb_view;
		if ( empty($cs_xmlObject->inside_post_thumb_audio) ) $inside_post_thumb_audio = ""; else $inside_post_thumb_audio = $cs_xmlObject->inside_post_thumb_audio;
		if ( empty($cs_xmlObject->inside_post_thumb_video) ) $inside_post_thumb_video = ""; else $inside_post_thumb_video = $cs_xmlObject->inside_post_thumb_video;
		if ( empty($cs_xmlObject->inside_post_thumb_slider) ) $inside_post_thumb_slider = ""; else $inside_post_thumb_slider = $cs_xmlObject->inside_post_thumb_slider;
		if ( empty($cs_xmlObject->inside_post_thumb_slider_type) ) $inside_post_thumb_slider_type = ""; else $inside_post_thumb_slider_type = $cs_xmlObject->inside_post_thumb_slider_type;
		?>
		<ul class="form-elements noborder project-gallery">
        	<li>
                <div class="" id="inside_post_thumb_slider">
                     <?php echo cs_post_attachments('gallery_slider_meta_form');?>
                </div>
            </li>           	
		</ul>
<?php
}
}