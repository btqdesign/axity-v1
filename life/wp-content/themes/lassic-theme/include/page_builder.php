<?php
global $post,$cs_node, $cs_count_node, $cs_xmlObject,$cs_theme_option;
add_action( 'add_meta_boxes', 'cs_page_bulider_add' );
add_action( 'add_meta_boxes', 'cs_page_options_add' );
function cs_page_options_add() {
	add_meta_box( 'id_page_options', 'CS Page Options', 'cs_page_options', 'page', 'normal', 'high' );  
}
function cs_page_bulider_add() {
	add_meta_box( 'id_page_builder', 'CS Page Builder', 'cs_page_bulider', 'page', 'normal', 'high' );  
}  
function cs_page_bulider( $post ) {
	global $post,$cs_xmlObject, $cs_node, $cs_count_node, $post, $column_container, $coloum_width;
	wp_reset_query();
	$postID = $post->ID;
	$count_widget = 0;
	$page_title = '';
	$page_content = '';
	$page_sub_title = '';
	$builder_active = 0;
	$cs_page_bulider = get_post_meta($post->ID, "cs_page_builder", true);
	/*$cs_tmp_fn = 'base'.'64_decode';
	$cs_page_bulider = unserialize(call_user_func($cs_tmp_fn, $cs_page_bulider));*/
	if ( $cs_page_bulider <> "" ){
		$cs_xmlObject = new stdClass();
		$cs_xmlObject = new SimpleXMLElement($cs_page_bulider);
		$builder_active = $cs_xmlObject->builder_active;
	}
?>
<input type="hidden" name="builder_active" value="<?php echo cs_allow_special_char($builder_active) ?>" />
  <div class="clear"></div>
  <div id="add_page_builder_item">
  	<div id="cs_shortcode_area"></div>
  
<?php
		if ( $cs_page_bulider <> "" ) {
			if ( isset($cs_xmlObject->page_title) ) $page_title = $cs_xmlObject->page_title;
			if ( isset($cs_xmlObject->page_content) ) $page_content = $cs_xmlObject->page_content;
			if ( isset($cs_xmlObject->page_sub_title) ) $page_sub_title = $cs_xmlObject->page_sub_title;
				foreach ( $cs_xmlObject->column_container as $column_container ){
					cs_column_pb(1);
				}
		}
?>
    <?php //if(!isset($cs_xmlObject->column_container) || count($cs_xmlObject->column_container)<1){?>
    <!-- <div id="no_widget" class="placehoder">Page Builder in Empty, Please Select Page Element. <img src="<?php echo get_template_directory_uri()?>/include/assets/images/bg-arrowup.png" alt="" /></div> -->
    <?php //}?>
  </div>
   <div class="clear"></div>
   <div class="add-widget"> <span class="addwidget"> <a href="javascript:ajaxSubmit('cs_column_pb','1','column_full')"><i class="icon-plus-circle"></i> Add Page Sections</a> </span> 
  <div id="loading" class="builderload"></div>
  <div class="clear"></div>
  <input type="hidden" name="page_builder_form" value="1" />
  <div class="clear"></div>
</div>
<div class="clear"></div>
<script>
	jQuery(function() {
		// jQuery( "#add_page_builder_item" ).sortable({
		// 	cancel : 'div div.poped-up'
		// });
		//jQuery( "#add_page_builder_item" ).disableSelection();
	});
</script> 
<script type="text/javascript">
		var count_widget = <?php echo cs_allow_special_char($count_widget) ; ?>;
		jQuery(function() {
		   jQuery( ".draginner" ) .sortable({
				connectWith: '.draginner',
				handle:'.column-in',
				start: function( event, ui ) {jQuery(ui.item).css({"width":"25%"});},
				cancel:'.draginner .poped-up,#confirmOverlay',
				revert:false,
				receive: function( event, ui ) {callme();},
				placeholder: "ui-state-highlight",
				forcePlaceholderSize:true
		   });
			jQuery( "#add_page_builder_item" ).sortable({
				handle:'.column-in',
				connectWith: ".columnmain",
				cancel:'.column_container,.draginner,#confirmOverlay',
				revert:false,
				placeholder: "ui-state-highlight",
				forcePlaceholderSize:true
			 });
		   // jQuery( "#add_page_builder_item" ).disableSelection();
		  });
		function ajaxSubmit(action,total_column, column_class){
			counter++;
			count_widget++;
			jQuery('.builderload').html("<img src='<?php echo get_template_directory_uri();?>/include/assets/images/ajax_loading.gif' alt='' />");
            var newCustomerForm = "action=" + action + '&counter=' + counter + '&total_column=' + total_column + '&column_class=' + column_class + '&postID=<?php echo esc_js($postID);?>';
            jQuery.ajax({
                type:"POST",
                url: "<?php echo admin_url('admin-ajax.php')?>",
                data: newCustomerForm,
                success:function(data){
					jQuery('.builderload').html("");
                    jQuery("#add_page_builder_item").append(data);
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
					jQuery('.bg_color').wpColorPicker(); 
					 jQuery( ".draginner" ) .sortable({
							connectWith: '.draginner',
							handle:'.column-in',
							cancel:'.draginner .poped-up,#confirmOverlay',
							revert:false,
							start: function( event, ui ) {jQuery(ui.item).css({"width":"25%"})},
							receive: function( event, ui ) {callme();},
							placeholder: "ui-state-highlight",
							forcePlaceholderSize:true
			  		 });
					 // if (count_widget > 0) jQuery("#no_widget").hide();
					//alert(count_widget);
                }
            });
            //return false;
        }
		
		function ajaxSubmitwidget(action,id){
			SuccessLoader ();
			counter++;
            var newCustomerForm = "action=" + action + '&counter=' + counter;
			var edit_url = action + counter;
			//jQuery('.composer-'+id).hide();
            jQuery.ajax({
                type:"POST",
                url: "<?php echo admin_url('admin-ajax.php')?>",
                data: newCustomerForm,
                success:function(data){
                jQuery("#counter_"+id).append(data);
				jQuery("#"+action+counter).append('<input type="hidden" name="cs_widget_element_num[]" value="form" />');
				jQuery('.bg_color').wpColorPicker(); 
				  jQuery( ".draginner" ) .sortable({
					connectWith: '.draginner',
					handle:'.column-in',
					cancel:'.draginner .poped-up,#confirmOverlay',
					revert:false,
					// start: function( event, ui ) {jQuery(ui.item).css({"width":"25%"})},
					receive: function( event, ui ) {callme();},
					placeholder: "ui-state-highlight",
					forcePlaceholderSize:true
			   });
				removeoverlay("composer-"+id,"append");
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
				callme(); 
                }
            });
		}
		function ajaxSubmitwidget_element(action,id,name){
			 SuccessLoader ();
			counter++;
            var newCustomerForm = "action=" + action + '&element_name=' + name + '&counter=' + counter;
			var edit_url = action + counter;
			//jQuery('.composer-'+id).hide();
            jQuery.ajax({
                type:"POST",
                url: "<?php echo admin_url('admin-ajax.php')?>",
                data: newCustomerForm,
                success:function(data){
                jQuery("#counter_"+id).append(data);
				//results-shortocde-id-form
				jQuery("#counter_"+id+" #results-shortocde-id-form").append('<input type="hidden" name="cs_widget_element_num[]" value="form" />');
				jQuery('.bg_color').wpColorPicker(); 
				  jQuery( ".draginner" ) .sortable({
					connectWith: '.draginner',
					handle:'.column-in',
					cancel:'.draginner .poped-up,#confirmOverlay',
					revert:false,
					// start: function( event, ui ) {jQuery(ui.item).css({"width":"25%"})},
					receive: function( event, ui ) {callme();},
					placeholder: "ui-state-highlight",
					forcePlaceholderSize:true
			   });
				removeoverlay("composer-"+id,"append");
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
				callme(); 
                }
            });
		}
        function ajaxSubmittt(action){
 			counter++;
			count_widget++;
            var newCustomerForm = "action=" + action + '&counter=' + counter;
            jQuery.ajax({
                type:"POST",
                url: "<?php echo admin_url()?>/admin-ajax.php",
                data: newCustomerForm,
                success:function(data){
                    jQuery("#add_page_builder_item").append(data);
					if (count_widget > 0) jQuery("#add_page_builder_item").addClass('hasclass');
					//alert(count_widget);
                }
            });
            //return false;
        }
    </script>
<?php  
}
function cs_page_options( $post ) {
	
	global $post, $cs_xmlObject,$cs_theme_options;
 	$cs_page_bulider = get_post_meta($post->ID, "cs_page_builder", true);
	
	if ( $cs_page_bulider <> "" ){
		$cs_xmlObject = new stdClass();
		$cs_xmlObject = new SimpleXMLElement($cs_page_bulider);
		$builder_active = $cs_xmlObject->builder_active;
	}
	//$cs_theme_options=get_option('cs_theme_options');
	$cs_builtin_seo_fields =$cs_theme_options['cs_builtin_seo_fields'];
	$cs_header_position =$cs_theme_options['cs_header_position'];
		?>
		<div class="elementhidden">
		<div class="tabs vertical">
        	<nav class="admin-navigtion">
               <ul id="myTab" class="nav nav-tabs">
                 <li class="active"><a href="#tab-general-settings" data-toggle="tab"><i class="icon-gear"></i> General Settings</a></li>
                 <li><a href="#tab-slideshow" data-toggle="tab"><i class="icon-forward2"></i> Subheader</a></li>
                <?php if($cs_builtin_seo_fields == 'on'){?>
                 <li><a href="#tab-seo-advance-settings" data-toggle="tab"><i class="icon-globe6"></i> SEO Options</a></li>
                 <?php }?>
                <?php if($cs_header_position == 'absolute'){?>
                 <li><a href="#tab-header-position-settings" data-toggle="tab"><i class="icon-forward2"></i>Header Absolute</a></li>
                 <?php }?>
	
                </ul>
           </nav>
          <!--- Tab Content --->
		  <div class="tab-content">
          	<!--- Content Tab --->
			<div id="tab-general-settings" class="tab-pane fade active in">
                <?php  //cs_general_settings_element();
					   cs_sidebar_layout_options();
					   cs_pagebuilder_themeoptions();
				?>
			</div>
            <!--- Content Tab --->
			<!--- Content Tab --->
			<div id="tab-slideshow" class="tab-pane fade">
				<?php cs_subheader_element();?>
			</div>
            <!--- Content Tab --->
                <!--- Content Tab --->
                <?php if($cs_builtin_seo_fields == 'on'){?>
                    <!--- Content Tab --->
                    <div id="tab-seo-advance-settings" class="tab-pane fade">
                        
                        <?php cs_seo_settitngs_element();?>
                    </div>
                    <!--- Content Tab --->
            	<?php }?>
				<?php if($cs_header_position == 'absolute'){?>
                <!--- Content Tab --->
                <div id="tab-header-position-settings" class="tab-pane fade">
                
                <?php cs_header_postition_element();?>
                </div>
                <!--- Content Tab --->
                <?php }?>

			<!--- Content Tab --->
		  </div>
          <!--- Tab Content --->
		</div>
	  </div>
	<?php
}
if ( isset($_POST['page_builder_form']) and $_POST['page_builder_form'] == 1 ) {
		add_action( 'save_post', 'save_page_builder' );
		function save_page_builder( $post_id ) {
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
				if ( isset($_POST['builder_active']) ) {
					$sxe = new SimpleXMLElement("<pagebuilder></pagebuilder>");
					if ( empty($_POST["builder_active"]) ) $_POST["builder_active"] = "";
					if ( empty($_POST["page_content"]) ) $_POST["page_content"] = "";
					$sxe->addChild('builder_active', $_POST['builder_active']);
					$sxe->addChild('page_content', $_POST['page_content']);
					$sxe = cs_page_options_save_xml($sxe);
								//if ( isset($_POST['cs_orderby']) ) {
									$cs_counter 			= 0;
									$page_element_id = 0;
									$cs_counter_gal 		= 0;
									$cs_counter_port 		= 0;
									$counter_team 			= 0;
									$cs_counter_slider 		= 0;
									$cs_counter_blog_slider = 0;
									$cs_counter_blog = 0;
									$cs_counter_class = 0;
									$cs_counter_cause 		= 0;
									$cs_counter_news 		= 0;
									$cs_counter_contact 	= 0;
									$cs_counter_contactus 	= 0;
									$cs_counter_testimonial = 0;
									$cs_counter_column 		= 0;
									$cs_counter_mb 			= 0;
									$cs_counter_image 		= 0;
									$cs_counter_map 				= 0;
									$cs_counter_services_node 		= 0;
									$cs_counter_services 			= 0;
									$cs_counter_tabs_node 			= 0;
									$cs_counter_accordion_node 	  	= 0;
									$cs_counter_highlight 			= 0;
									$cs_counter_testimonials_node 	= 0;
									$cs_shortcode_counter_testimonial = 0;
									$cs_global_counter_testimonials = 0;
									$cs_counter_testimonials		= 0;
									$cs_counter_list 				= 0;
									$cs_counter_lists_node 			= 0;
									$cs_counter_team 				= 0;
									$cs_counter_team_node 			= 0;
									$cs_counter_quote 					= 0;
									$cs_counter_video 					= 0;
									$cs_counter_quote 					= 0;
									$cs_counter_services 				= 0;
									$counter_services_node 			= 0;
									$cs_global_counter_services = 0;
									$cs_shortcode_counter_services = 0;
									$cs_counter_tabs 					= 0;
									$counter_tabs_node 				= 0;
									$cs_shortcode_counter_tabs 		= 0;
									$cs_global_counter_tabs 		= 0;
									$cs_counter_accordion 				= 0;
									$counter_accordion_node 		= 0;
									$cs_global_counter_accordion    = 0;
									$cs_shortcode_counter_accordion = 0;
									$cs_counter_faq 					= 0;
									$cs_counter_faq_node 				= 0;
									$cs_global_counter_faq    		= 0;
									$cs_shortcode_counter_faq 		= 0;
									$cs_counter_toggle 				= 0;
									$cs_global_counter_toggle = 0;
									$cs_shortcode_counter_toggle = 0;
									$cs_counter_parallax 			= 0;
									$widget_no 						= 0;
									$column_container_no 			= 0;
									$cs_counter_dcpt 				= 0;
									$cs_counter_pricetables 		= 0;
									$cs_counter_pricetables_node 	= 0;
									$cs_global_counter_pricetables  = 0;
									$cs_shortcode_counter_pricetables = 0;
									$cs_counter_client				= 0;
									$cs_counter_image				= 0;
									$cs_counter_dropcap				= 0;
									$cs_counter_divider				= 0;
									$cs_counter_tooltip				= 0;

									$cs_counter_progressbars		= 0;
									$cs_counter_progressbars_node 	= 0;
									$cs_global_counter_progressbars = 0;
									$cs_shortcode_counter_progressbars = 0;
									$cs_counter_table				= 0;
									$cs_global_counter_table 		= 0;
									$cs_shortcode_counter_table 	= 0;
									$cs_counter_message				= 0;
									$cs_counter_heading 			= 0;
									$cs_counter_button				= 0;
									$cs_counter_call_to_action 		= 0;
									$cs_global_counter_call_to_action = 0;
									$cs_shortcode_counter_call_to_action = 0;
									$cs_counter_fancyheading 		= 0;
									$cs_counter_promobox 			= 0;
									$cs_counter_iconbox 			= 0;
									$cs_counter_audio				= 0;
									$cs_counter_audio_node			= 0;
									$cs_counter_infobox 			= 0;
									$cs_counter_infobox_node 		= 0;
									$cs_counter_coutner				= 0;
									$cs_global_counter_counter = 0;
									$cs_shortcode_counter_counter = 0;
									$counter_counter_item_node		= 0;
									$cs_counter_icons 				= 0;
									$cs_counter_map 				= 0;
									$cs_parallax_slider 			= 0;
									$cs_parallax_video_url 			= 0;
									$cs_parallax_video_mute 		= 0;
									$cs_counter_offerslider 		= 0;
									$cs_counter_clients				= 0;
									$cs_counter_clients_node		= 0;
									$cs_counter_contentslider 		= 0;
									$cs_counter_page_element		= 0;
									$cs_counter_members 			= 0;
									$cs_counter_spacer 				= 0;
									$cs_counter_teams				= 0;
									$cs_counter_tweets				= 0;
									$cs_counter_apple				= 0;
									$cs_global_counter_message		= 0;
									$cs_shortcode_counter_message	= 0;
									$cs_global_counter_button 		= 0;
									$cs_shortcode_counter_button	= 0;
									$cs_global_counter_column	= 0;
									$cs_shortcode_counter_column	= 0;
									$cs_global_counter_contactus	= 0;
									$cs_shortcode_counter_contactus	= 0;
									$cs_global_counter_tooltip	= 0;
									$cs_shortcode_counter_tooltip	= 0;
									$cs_global_counter_tweets	= 0;
									$cs_shortcode_counter_tweets	= 0;
									$cs_global_counter_heading	= 0;
									$cs_shortcode_counter_heading	= 0;
									$cs_global_counter_divider	= 0;
									$cs_shortcode_counter_divider	= 0;
									$cs_global_counter_quote	= 0;
									$cs_shortcode_counter_quote	= 0;
									$cs_global_counter_highlight	= 0;
									$cs_shortcode_counter_highlight	= 0;
									$cs_global_counter_dropcap	= 0;
									$cs_shortcode_counter_dropcap	= 0;
									$cs_global_counter_list	= 0;
									$cs_shortcode_counter_list	= 0;
									$cs_global_counter_blog_slider = 0;
									$cs_shortcode_counter_blog_slider = 0;
									$cs_global_counter_blog = 0;
									$cs_shortcode_counter_blog = 0;
									$cs_global_counter_class = 0;
									$cs_shortcode_counter_class = 0;
									$cs_global_counter_teams = 0;
									$cs_shortcode_counter_teams = 0;
									$cs_global_counter_clients = 0;
									$cs_shortcode_counter_clients = 0;
									$cs_global_counter_page_element = 0;
									$cs_shortcode_counter_page_element = 0;
									$cs_global_counter_image= 0;
									$cs_shortcode_counter_image = 0;
									$cs_global_counter_promobox = 0;
									$cs_shortcode_counter_promobox = 0;
									$cs_global_counter_gallery = 0;
									$cs_shortcode_counter_gallery = 0;
									$cs_global_counter_video=0;
									$cs_shortcode_counter_video =0;
									$cs_global_counter_audio=0;
									$cs_shortcode_counter_audio =0;
									$cs_counter_offerslider_node = 0;
									$cs_global_counter_offerslider=0;
									$cs_shortcode_counter_offerslider =0;
									$cs_global_counter_spacer=0;
									$cs_shortcode_counter_spacer =0;
									$cs_global_counter_map=0;
									$cs_shortcode_counter_map =0;
									$cs_global_counter_icons =0;
									$cs_shortcode_counter_icons =0;
									$cs_global_counter_contentslider = 0;
									$cs_shortcode_counter_contentslider = 0;
									$cs_global_counter_members = 0;
									$cs_shortcode_counter_members = 0;
									$cs_global_counter_page_element = 0;
									$cs_shortcode_counter_page_element = 0;
									$cs_global_counter_infobox = 0;
									$cs_shortcode_counter_infobox = 0;
									$cs_shortcode_counter_slider	= 0;
									$cs_global_counter_slider	= 0;
									
									$counter_badges 				= 0;
									$cs_global_counter_badges 		= 0;
									$cs_shortcode_counter_badges 	= 0;
									
									$cs_counter_events 				= 0;
									$cs_global_counter_events 		= 0;
									$cs_shortcode_counter_events 	= 0;
									
									$cs_global_counter_cause = 0;
									$cs_shortcode_counter_cause = 0;
									$cs_counter_cause = 0;
									
									$cs_global_counter_latest_cause = 0;
									$cs_shortcode_counter_latest_cause = 0;
									$cs_counter_latest_cause = 0;
									
																	
									$cs_global_counter_member = 0;
									$cs_shortcode_counter_member = 0;
									$cs_counter_member = 0;
																		
									$cs_global_counter_project = 0;
									$cs_shortcode_counter_project = 0;
									$cs_counter_project = 0;
									
									
								if(isset($_POST['total_column'])){	
										foreach ( $_POST['total_column'] as $count_column ) {
										// Sections Element Attributes start
										$column_container = $sxe->addChild('column_container');
										if ( empty($_POST['column_class'][$column_container_no]) ) $_POST['column_class'][$column_container_no] = "";
										$column_container->addAttribute('class', $_POST['column_class'][$column_container_no] );
										$column_rand_id = $_POST['column_rand_id'][$column_container_no];
										
										//cs_section_background_option
										if ( empty($_POST['cs_section_background_option'][$column_container_no]) ) $_POST['cs_section_background_option'][$column_container_no] = "";
										if ( empty($_POST['cs_section_bg_image'][$column_container_no]) ) $_POST['cs_section_bg_image'][$column_container_no] = "";
										if ( empty($_POST['cs_section_bg_image_position'][$column_container_no]) ) $_POST['cs_section_bg_image_position'][$column_container_no] = "";
										if ( empty($_POST['cs_section_flex_slider'][$column_container_no]) ) $_POST['cs_section_flex_slider'][$column_container_no] = "";
										if ( empty($_POST['cs_section_video_url'][$column_container_no]) ) $_POST['cs_section_video_url'][$column_container_no] = "";
										if ( empty($_POST['cs_section_video_mute'][$column_container_no]) ) $_POST['cs_section_video_mute'][$column_container_no] = "";
										if ( empty($_POST['cs_section_video_autoplay'][$column_container_no]) ) $_POST['cs_section_video_autoplay'][$column_container_no] = "";
										if ( empty($_POST['cs_section_bg_color'][$column_container_no]) ) $_POST['cs_section_bg_color'][$column_container_no] = "";
										if ( empty($_POST['cs_section_padding_top'][$column_container_no]) ) $_POST['cs_section_padding_top'][$column_container_no] = "";
										if ( empty($_POST['cs_section_padding_bottom'][$column_container_no]) ) $_POST['cs_section_padding_bottom'][$column_container_no] = "";
										if ( empty($_POST['cs_section_parallax'][$column_container_no]) ) $_POST['cs_section_parallax'][$column_container_no] = "";
										if ( empty($_POST['cs_section_css_id'][$column_container_no]) ) $_POST['cs_section_css_id'][$column_container_no] = "";
										if ( empty($_POST['cs_section_view'][$column_rand_id]['0']) ) $_POST['cs_section_view'][$column_rand_id] = "";
										if ( empty($_POST['cs_layout'][$column_rand_id]['0']) ) $_POST['cs_layout'][$column_rand_id]['0'] = "";
										
										
										$column_container->addAttribute('cs_section_background_option', $_POST['cs_section_background_option'][$column_container_no] );
										$column_container->addAttribute('cs_section_bg_image', $_POST['cs_section_bg_image'][$column_container_no] );
										$column_container->addAttribute('cs_section_bg_image_position', $_POST['cs_section_bg_image_position'][$column_container_no] );
										$column_container->addAttribute('cs_section_flex_slider', $_POST['cs_section_flex_slider'][$column_container_no] );
										$column_container->addAttribute('cs_section_custom_slider', $_POST['cs_section_custom_slider'][$column_container_no] );
										$column_container->addAttribute('cs_section_video_url', $_POST['cs_section_video_url'][$column_container_no] );
										$column_container->addAttribute('cs_section_video_mute', $_POST['cs_section_video_mute'][$column_container_no] );
										$column_container->addAttribute('cs_section_video_autoplay', $_POST['cs_section_video_autoplay'][$column_container_no] );
										$column_container->addAttribute('cs_section_bg_color', $_POST['cs_section_bg_color'][$column_container_no] );
										$column_container->addAttribute('cs_section_padding_top', $_POST['cs_section_padding_top'][$column_container_no] );
										$column_container->addAttribute('cs_section_padding_bottom', $_POST['cs_section_padding_bottom'][$column_container_no] );
										$column_container->addAttribute('cs_section_border_bottom', $_POST['cs_section_border_bottom'][$column_container_no] );
										$column_container->addAttribute('cs_section_border_top', $_POST['cs_section_border_top'][$column_container_no] );
										$column_container->addAttribute('cs_section_border_color', $_POST['cs_section_border_color'][$column_container_no] );
										$column_container->addAttribute('cs_section_margin_top', $_POST['cs_section_margin_top'][$column_container_no] );
										$column_container->addAttribute('cs_section_margin_bottom', $_POST['cs_section_margin_bottom'][$column_container_no] );
										$column_container->addAttribute('cs_section_parallax', $_POST['cs_section_parallax'][$column_container_no] );
										$column_container->addAttribute('cs_section_css_id', $_POST['cs_section_css_id'][$column_container_no] );
										$column_container->addAttribute('cs_section_view', $_POST['cs_section_view'][$column_container_no] );
										$column_container->addAttribute('cs_layout', $_POST['cs_layout'][$column_rand_id]['0'] );
										$column_container->addAttribute('cs_sidebar_left', $_POST['cs_sidebar_left'][$column_container_no] );
										$column_container->addAttribute('cs_sidebar_right', $_POST['cs_sidebar_right'][$column_container_no] );
										// Sections Element Attributes end
										for ( $i = 0; $i < $count_column; $i++ ) {
											$column = $column_container->addChild('column');
											$a = $_POST['total_widget'][$widget_no];
											for ( $j = 1; $j <= $a; $j++ ){	
											$page_element_id++;
										// Typography Start
										// Save Column page element 
										if ( $_POST['cs_orderby'][$cs_counter] == "flex_column" ) {
 												$shortcode = '';
												$cs_flex_column = $column->addChild('flex_column');
												$cs_flex_column->addChild('page_element_size', htmlspecialchars($_POST['flex_column_element_size'][$cs_global_counter_column]) );
												$cs_flex_column->addChild('flex_column_element_size', htmlspecialchars($_POST['flex_column_element_size'][$cs_global_counter_column]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str =stripslashes(htmlspecialchars(( $_POST['shortcode']['flex_column'][$cs_shortcode_counter_column]), ENT_QUOTES ));
													$cs_shortcode_counter_column++;
													$cs_flex_column->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
													
												} else {
													$shortcode = '[cs_column ';
													if(isset($_POST['cs_flex_column_section_title'][$cs_counter_column]) && $_POST['cs_flex_column_section_title'][$cs_counter_column] != ''){
														$shortcode .= 	'cs_flex_column_section_title="'.stripslashes(htmlspecialchars(($_POST['cs_flex_column_section_title'][$cs_counter_column]), ENT_QUOTES )).'" ';
													}
													if(isset($_POST['cs_column_class'][$cs_counter_column]) && $_POST['cs_column_class'][$cs_counter_column] != ''){
														$shortcode .= 	'cs_column_class="'.htmlspecialchars($_POST['cs_column_class'][$cs_counter_column], ENT_QUOTES).'" ';
													}
													if(isset($_POST['flex_column_bg_color'][$cs_counter_column]) && $_POST['flex_column_bg_color'][$cs_counter_column] != ''){
														$shortcode .= 	'flex_column_bg_color="'.htmlspecialchars($_POST['flex_column_bg_color'][$cs_counter_column]).'" ';
													}
													if(isset($_POST['flex_column_text_color'][$cs_counter_column]) && $_POST['flex_column_text_color'][$cs_counter_column] != ''){
														$shortcode .= 	'flex_column_text_color="'.htmlspecialchars($_POST['flex_column_text_color'][$cs_counter_column]).'" ';
													}
													if(isset($_POST['cs_column_animation'][$cs_counter_column]) && $_POST['cs_column_animation'][$cs_counter_column] != ''){
														$shortcode .= 	'cs_column_animation="'.htmlspecialchars($_POST['cs_column_animation'][$cs_counter_column]).'" ';
													}
													$shortcode .= 	']';
													if(isset($_POST['flex_column_text'][$cs_counter_column]) && $_POST['flex_column_text'][$cs_counter_column] != ''){
														$shortcode .= 	esc_textarea(cs_custom_shortcode_encode($_POST['flex_column_text'][$cs_counter_column])).' ';
													}
 													$shortcode .= 	'[/cs_column]';
													$cs_flex_column->addChild('cs_shortcode', $shortcode );
													$cs_counter_column++;
												}
											$cs_global_counter_column++;
										}
									
										else if ( $_POST['cs_orderby'][$cs_counter] == "contactus" ) {
											$shortcode = '';
											$cs_contact_us = $column->addChild('contactus');
											$cs_contact_us->addChild('page_element_size', htmlspecialchars($_POST['contactus_element_size'][$cs_global_counter_contactus]) );
											$cs_contact_us->addChild('contactus_element_size', htmlspecialchars($_POST['contactus_element_size'][$cs_global_counter_contactus]) );
											if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
												$cs_shortcode_str = stripslashes ($_POST['shortcode']['contactus'][$cs_shortcode_counter_contactus]);
												$cs_shortcode_counter_contactus++;
												$cs_contact_us->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
											} else {
												$shortcode = '[cs_contactus ';
												if(isset($_POST['cs_contactus_section_title'][$cs_counter_contactus]) && $_POST['cs_contactus_section_title'][$cs_counter_contactus] != ''){
													$shortcode .= 	'cs_contactus_section_title="'.htmlspecialchars($_POST['cs_contactus_section_title'][$cs_counter_contactus], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_contactus_vacancies'][$cs_counter_contactus]) && $_POST['cs_contactus_vacancies'][$cs_counter_contactus] != ''){
													$shortcode .= 	'cs_contactus_vacancies="'.htmlspecialchars($_POST['cs_contactus_vacancies'][$cs_counter_contactus], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_contactus_label'][$cs_counter_contactus]) && $_POST['cs_contactus_label'][$cs_counter_contactus] != ''){
													$shortcode .= 	'cs_contactus_label="'.htmlspecialchars($_POST['cs_contactus_label'][$cs_counter_contactus], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_contactus_view'][$cs_counter_contactus]) && $_POST['cs_contactus_view'][$cs_counter_contactus] != ''){
													$shortcode .= 	'cs_contactus_view="'.htmlspecialchars($_POST['cs_contactus_view'][$cs_counter_contactus], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_contactus_send'][$cs_counter_contactus]) && $_POST['cs_contactus_send'][$cs_counter_contactus] != ''){
													$shortcode .= 	'cs_contactus_send="'.htmlspecialchars($_POST['cs_contactus_send'][$cs_counter_contactus], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_success'][$cs_counter_contactus]) && $_POST['cs_success'][$cs_counter_contactus] != ''){
													$shortcode .= 	'cs_success="'.htmlspecialchars($_POST['cs_success'][$cs_counter_contactus], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_error'][$cs_counter_contactus]) && $_POST['cs_error'][$cs_counter_contactus] != ''){
													$shortcode .= 	'cs_error="'.htmlspecialchars($_POST['cs_error'][$cs_counter_contactus], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_form_id'][$cs_counter_contactus]) && $_POST['cs_form_id'][$cs_counter_contactus] != ''){
													$shortcode .= 	'cs_form_id="'.htmlspecialchars($_POST['cs_form_id'][$cs_counter_contactus], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_contact_class'][$cs_counter_contactus]) && $_POST['cs_contact_class'][$cs_counter_contactus] != ''){
													$shortcode .= 	'cs_contact_class="'.htmlspecialchars($_POST['cs_contact_class'][$cs_counter_contactus], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_contact_animation'][$cs_counter_contactus]) && $_POST['cs_contact_animation'][$cs_counter_contactus] != ''){
													$shortcode .= 	'cs_contact_animation="'.htmlspecialchars($_POST['cs_contact_animation'][$cs_counter_contactus], ENT_QUOTES).'" ';
												}
												$shortcode .= 	']';
												$cs_contact_us->addChild('cs_shortcode', $shortcode );
												$cs_counter_contactus++;
											}
										$cs_global_counter_contactus++;
										}
										// Save Tooltip page element 
										else if ( $_POST['cs_orderby'][$cs_counter] == "tooltip" ) {
											$shortcode = '';
											
											$cs_tooltip = $column->addChild('tooltip');
											$cs_tooltip->addChild('page_element_size', htmlspecialchars($_POST['tooltip_element_size'][$cs_global_counter_tooltip]));
											$cs_tooltip->addChild('tooltip_element_size', htmlspecialchars($_POST['tooltip_element_size'][$cs_global_counter_tooltip]));
											if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
												$cs_shortcode_str = stripslashes ($_POST['shortcode']['tooltip'][$cs_shortcode_counter_tooltip]);
												$cs_shortcode_counter_tooltip++;
												$cs_tooltip->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
											} else {
												$shortcode = '[cs_tooltip ';
												if(isset($_POST['cs_tooltip_hover_title'][$cs_counter_tooltip]) && $_POST['cs_tooltip_hover_title'][$cs_counter_tooltip] != ''){
													$shortcode .= 	'cs_tooltip_hover_title="'.htmlspecialchars($_POST['cs_tooltip_hover_title'][$cs_counter_tooltip], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_tooltip_data_placement'][$cs_counter_tooltip]) && $_POST['cs_tooltip_data_placement'][$cs_counter_tooltip] != ''){
													$shortcode .= 	'cs_tooltip_data_placement="'.htmlspecialchars($_POST['cs_tooltip_data_placement'][$cs_counter_tooltip], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_tooltip_class'][$cs_counter_tooltip]) && $_POST['cs_tooltip_class'][$cs_counter_tooltip] != ''){
													$shortcode .= 	'cs_tooltip_class="'.htmlspecialchars($_POST['cs_tooltip_class'][$cs_counter_tooltip], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_tooltip_animation'][$cs_counter_tooltip]) && $_POST['cs_tooltip_animation'][$cs_counter_tooltip] != ''){
													$shortcode .= 	'cs_tooltip_animation="'.htmlspecialchars($_POST['cs_tooltip_animation'][$cs_counter_tooltip]).'" ';
												}
												$shortcode .= 	']';
												if(isset($_POST['tooltip_content'][$cs_counter_tooltip])){
													$shortcode .= 	htmlspecialchars($_POST['tooltip_content'][$cs_counter_tooltip], ENT_QUOTES);
												}
												$shortcode .= 	'[/cs_tooltip]';
												$cs_tooltip->addChild('cs_shortcode', $shortcode );
												$cs_counter_tooltip++;
											}
										}
										// Save heading page element 
										else if ( $_POST['cs_orderby'][$cs_counter] == "heading" ) {
												$shortcode = '';
												$cs_heading = $column->addChild('heading');
												$cs_heading->addChild('page_element_size', htmlspecialchars($_POST['heading_element_size'][$cs_global_counter_heading]) );
												$cs_heading->addChild('heading_element_size', htmlspecialchars($_POST['heading_element_size'][$cs_global_counter_heading]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes($_POST['shortcode']['heading'][$cs_shortcode_counter_heading]);
													$cs_shortcode_counter_heading++;
													$cs_heading->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													$shortcode = '[cs_heading ';
													if(isset($_POST['cs_heading_title'][$cs_counter_heading]) && $_POST['cs_heading_title'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_heading_title="'.htmlspecialchars($_POST['cs_heading_title'][$cs_counter_heading], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_heading_style'][$cs_counter_heading]) && $_POST['cs_heading_style'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_heading_style="'.htmlspecialchars($_POST['cs_heading_style'][$cs_counter_heading]).'" ';
													}
													if(isset($_POST['cs_heading_size'][$cs_counter_heading]) && $_POST['cs_heading_size'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_heading_size="'.htmlspecialchars($_POST['cs_heading_size'][$cs_counter_heading], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_heading_align'][$cs_counter_heading]) && $_POST['cs_heading_align'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_heading_align="'.htmlspecialchars($_POST['cs_heading_align'][$cs_counter_heading], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_heading_font_style'][$cs_counter_heading]) && $_POST['cs_heading_font_style'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_heading_font_style="'.htmlspecialchars($_POST['cs_heading_font_style'][$cs_counter_heading], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_heading_divider'][$cs_counter_heading]) && $_POST['cs_heading_divider'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_heading_divider="'.htmlspecialchars($_POST['cs_heading_divider'][$cs_counter_heading]).'" ';
													}
													if(isset($_POST['cs_heading_divider_icon'][$cs_counter_heading]) && $_POST['cs_heading_divider_icon'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_heading_divider_icon="'.htmlspecialchars($_POST['cs_heading_divider_icon'][$cs_counter_heading]).'" ';
													}
													if(isset($_POST['cs_heading_color'][$cs_counter_heading]) && $_POST['cs_heading_color'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_heading_color="'.htmlspecialchars($_POST['cs_heading_color'][$cs_counter_heading], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_color_title'][$cs_counter_heading]) && $_POST['cs_color_title'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_color_title="'.htmlspecialchars($_POST['cs_color_title'][$cs_counter_heading], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_heading_content_color'][$cs_counter_heading]) && $_POST['cs_heading_content_color'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_heading_content_color="'.htmlspecialchars($_POST['cs_heading_content_color'][$cs_counter_heading], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_heading_animation'][$cs_counter_heading]) && $_POST['cs_heading_animation'][$cs_counter_heading] != ''){
														$shortcode .= 	'cs_heading_animation="'.htmlspecialchars($_POST['cs_heading_animation'][$cs_counter_heading]).'" ';
													}
													$shortcode .= 	']';
													if(isset($_POST['heading_content'][$cs_counter_heading]) && $_POST['heading_content'][$cs_counter_heading] != ''){
														$shortcode .= 	htmlspecialchars($_POST['heading_content'][$cs_counter_heading], ENT_QUOTES);
													}
													$shortcode .= 	'[/cs_heading]';
													$cs_heading->addChild('cs_shortcode', $shortcode );
													$cs_counter_heading++;
												}
											$cs_global_counter_heading++;
										}
										// Save divider page element 
										else if ( $_POST['cs_orderby'][$cs_counter] == "divider" ) {
												$shortcode = '';
												$cs_divider   = $column->addChild('divider');
												$cs_divider->addChild('page_element_size', htmlspecialchars($_POST['divider_element_size'][$cs_global_counter_divider]) );
												$cs_divider->addChild('divider_element_size', htmlspecialchars($_POST['divider_element_size'][$cs_global_counter_divider]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['divider'][$cs_shortcode_counter_divider]);
													$cs_shortcode_counter_divider++;
													$cs_divider->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													$shortcode = '[cs_divider ';
														if(isset($_POST['cs_divider_style'][$cs_counter_divider]) && $_POST['cs_divider_style'][$cs_counter_divider] != ''){
															$shortcode .= 	'cs_divider_style="'.htmlspecialchars($_POST['cs_divider_style'][$cs_counter_divider]).'" ';
														}
														if(isset($_POST['cs_divider_backtotop'][$cs_counter_divider]) && $_POST['cs_divider_backtotop'][$cs_counter_divider] != ''){
															$shortcode .= 	'cs_divider_backtotop="'.htmlspecialchars($_POST['cs_divider_backtotop'][$cs_counter_divider]).'" ';
														}
														if(isset($_POST['cs_divider_margin_top'][$cs_counter_divider]) && $_POST['cs_divider_margin_top'][$cs_counter_divider] != ''){
															$shortcode .= 	'cs_divider_margin_top="'.htmlspecialchars($_POST['cs_divider_margin_top'][$cs_counter_divider]).'" ';
														}
														if(isset($_POST['cs_divider_margin_bottom'][$cs_counter_divider]) && $_POST['cs_divider_margin_bottom'][$cs_counter_divider] != ''){
															$shortcode .= 	'cs_divider_margin_bottom="'.htmlspecialchars($_POST['cs_divider_margin_bottom'][$cs_counter_divider]).'" ';
														}
														if(isset($_POST['cs_divider_height'][$cs_counter_divider]) && $_POST['cs_divider_height'][$cs_counter_divider] != ''){
															$shortcode .= 	'cs_divider_height="'.htmlspecialchars($_POST['cs_divider_height'][$cs_counter_divider]).'" ';
														}
														if(isset($_POST['cs_divider_class'][$cs_counter_divider]) && $_POST['cs_divider_class'][$cs_counter_divider] != ''){
															$shortcode .= 	'cs_divider_class="'.htmlspecialchars($_POST['cs_divider_class'][$cs_counter_divider], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_divider_animation'][$cs_counter_divider]) && $_POST['cs_divider_animation'][$cs_counter_divider] != ''){
															$shortcode .= 	'cs_divider_animation="'.htmlspecialchars($_POST['cs_divider_animation'][$cs_counter_divider]).'" ';
														}
														$shortcode .= 	']';
													$cs_divider->addChild('cs_shortcode', $shortcode );
													$cs_counter_divider++;
												}
												$cs_global_counter_divider++;
										}// Save divider page element 
										
										else if ( $_POST['cs_orderby'][$cs_counter] == "spacer" ) {
											$shortcode = '';
											$cs_spacer 		= $column->addChild('spacer');
 											$cs_spacer->addChild('page_element_size', '100');
											if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
												$cs_shortcode_str = stripslashes ($_POST['shortcode']['spacer'][$cs_shortcode_counter_spacer]);
												$cs_shortcode_counter_spacer++;
												$cs_spacer->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str) );
											} else {
  												$shortcode = '[cs_spacer ';
												if(isset($_POST['cs_spacer_height'][$cs_counter_spacer]) && $_POST['cs_spacer_height'][$cs_counter_spacer] != ''){
													$shortcode .= 	'cs_spacer_height="'.htmlspecialchars($_POST['cs_spacer_height'][$cs_counter_spacer]).'" ';
												}
												$shortcode .= 	']';
												$cs_spacer->addChild('cs_shortcode', $shortcode );
												$cs_counter_spacer++;
											}
											$cs_global_counter_spacer++;
										}
										// Save quote page element 
										else if ( $_POST['cs_orderby'][$cs_counter] == "quote" ) {
											$shortcode = '';
											$cs_quote = $column->addChild('quote');
											$cs_quote->addChild('page_element_size', htmlspecialchars($_POST['quote_element_size'][$cs_global_counter_quote]) );
											$cs_quote->addChild('quote_element_size', htmlspecialchars($_POST['quote_element_size'][$cs_global_counter_quote]) );
											if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
												$cs_shortcode_str = stripslashes ($_POST['shortcode']['quote'][$cs_shortcode_counter_quote]);
												$cs_shortcode_counter_quote++;
												$cs_quote->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
											} else {
												$shortcode = '[cs_quote ';
												
												if(isset($_POST['cs_quote_style'][$cs_counter_quote]) && $_POST['cs_quote_style'][$cs_counter_quote] != ''){
													$shortcode .= 	'cs_quote_style="'.htmlspecialchars($_POST['cs_quote_style'][$cs_counter_quote], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_quote_cite'][$cs_counter_quote]) && $_POST['cs_quote_cite'][$cs_counter_quote] != ''){
													$shortcode .= 	'cs_quote_cite="'.htmlspecialchars($_POST['cs_quote_cite'][$cs_counter_quote], ENT_QUOTES).'" ';
												}
												
												if(isset($_POST['cs_quote_cite_url'][$cs_counter_quote]) && $_POST['cs_quote_cite_url'][$cs_counter_quote] != ''){
													$shortcode .= 	'cs_quote_cite_url="'.htmlspecialchars($_POST['cs_quote_cite_url'][$cs_counter_quote], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_quote_text_color'][$cs_counter_quote]) && $_POST['cs_quote_text_color'][$cs_counter_quote] != ''){
													$shortcode .= 	'cs_quote_text_color="'.htmlspecialchars($_POST['cs_quote_text_color'][$cs_counter_quote]).'" ';
												}
												if(isset($_POST['cs_quote_align'][$cs_counter_quote]) && $_POST['cs_quote_align'][$cs_counter_quote] != ''){
													$shortcode .= 	'cs_quote_align="'.htmlspecialchars($_POST['cs_quote_align'][$cs_counter_quote]).'" ';
												}
												if(isset($_POST['cs_quote_section_title'][$cs_counter_quote]) && $_POST['cs_quote_section_title'][$cs_counter_quote] != ''){
													$shortcode .= 	'cs_quote_section_title="'.htmlspecialchars($_POST['cs_quote_section_title'][$cs_counter_quote], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_quote_class'][$cs_counter_quote]) && $_POST['cs_quote_class'][$cs_counter_quote] != ''){
													$shortcode .= 	'cs_quote_class="'.htmlspecialchars($_POST['cs_quote_class'][$cs_counter_quote], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_quote_animation'][$cs_counter_quote]) && $_POST['cs_quote_animation'][$cs_counter_quote] != ''){
													$shortcode .= 	'cs_quote_animation="'.htmlspecialchars($_POST['cs_quote_animation'][$cs_counter_quote]).'" ';
												}
												$shortcode .= 	']';
												if(isset($_POST['quote_content'][$cs_counter_quote])){
													$shortcode .= 	htmlspecialchars($_POST['quote_content'][$cs_counter_quote], ENT_QUOTES);
												}
												$shortcode .= 	'[/cs_quote]';
												$cs_quote->addChild('cs_shortcode', $shortcode );
												$cs_counter_quote++;
											}
										$cs_global_counter_quote++;
										}
										// Save highlight page element 
										else if ( $_POST['cs_orderby'][$cs_counter] == "highlight" ) {
											$shortcode = '';
											$cs_highlight = $column->addChild('highlight');
											$cs_highlight->addChild('page_element_size', htmlspecialchars($_POST['highlight_element_size'][$cs_global_counter_highlight]) );
											$cs_highlight->addChild('highlight_element_size', htmlspecialchars($_POST['highlight_element_size'][$cs_global_counter_highlight]) );
											if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
												$cs_shortcode_str = stripslashes ($_POST['shortcode']['highlight'][$cs_shortcode_counter_highlight]);
												$cs_shortcode_counter_highlight++;
												$cs_highlight->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
											} else {
												$shortcode = '[cs_highlight ';
												if(isset($_POST['cs_highlight_bg_color'][$cs_counter_highlight]) && $_POST['cs_highlight_bg_color'][$cs_counter_highlight] != ''){
													$shortcode .= 	'cs_highlight_bg_color="'.htmlspecialchars($_POST['cs_highlight_bg_color'][$cs_counter_highlight]).'" ';
												}
												if(isset($_POST['cs_highlight_color'][$cs_counter_highlight]) && $_POST['cs_highlight_color'][$cs_counter_highlight] != ''){
													$shortcode .= 	'cs_highlight_color="'.htmlspecialchars($_POST['cs_highlight_color'][$cs_counter_highlight]).'" ';
												}
												if(isset($_POST['cs_highlight_class'][$cs_counter_highlight]) && $_POST['cs_highlight_class'][$cs_counter_highlight] != ''){
													$shortcode .= 	'cs_highlight_class="'.htmlspecialchars($_POST['cs_highlight_class'][$cs_counter_highlight], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_highlight_animation'][$cs_counter_highlight]) && $_POST['cs_highlight_animation'][$cs_counter_highlight] != ''){
													$shortcode .= 	'cs_custom_animation="'.htmlspecialchars($_POST['cs_highlight_animation'][$cs_counter_highlight]).'" ';
												}
												$shortcode .= 	']';
												if(isset($_POST['highlight_content'][$cs_counter_highlight]) && $_POST['highlight_content'][$cs_counter_highlight] != ''){
													$shortcode .= 	htmlspecialchars($_POST['highlight_content'][$cs_counter_highlight], ENT_QUOTES);
												}
												$shortcode .= 	'[/cs_highlight]';
												$cs_highlight->addChild('cs_shortcode', $shortcode );
												$cs_counter_highlight++;
											}	
											$cs_global_counter_highlight++;
										}
										
 										// Save dropcap page element 
										 else if ( $_POST['cs_orderby'][$cs_counter] == "dropcap" ) {
											$shortcode = '';
											$cs_dropcap = $column->addChild('dropcap');
											$cs_dropcap->addChild('page_element_size', htmlspecialchars($_POST['dropcap_element_size'][$cs_global_counter_dropcap]) );
											$cs_dropcap->addChild('dropcap_element_size', htmlspecialchars($_POST['dropcap_element_size'][$cs_global_counter_dropcap]) );
											if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
												$cs_shortcode_str = stripslashes ($_POST['shortcode']['dropcap'][$cs_shortcode_counter_dropcap]);
												$cs_shortcode_counter_dropcap++;
												$cs_dropcap->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
											} else {
												$shortcode = '[cs_dropcap ';
												if(isset($_POST['cs_dropcap_style'][$cs_counter_dropcap]) && $_POST['cs_dropcap_style'][$cs_counter_dropcap] != ''){
													$shortcode .= 	'cs_dropcap_style="'.htmlspecialchars($_POST['cs_dropcap_style'][$cs_counter_dropcap]).'" ';
												}
												if(isset($_POST['cs_dropcap_size'][$cs_counter_dropcap]) && $_POST['cs_dropcap_size'][$cs_counter_dropcap] != ''){
													$shortcode .= 	'cs_dropcap_size="'.htmlspecialchars($_POST['cs_dropcap_size'][$cs_counter_dropcap]).'" ';
												}
												if(isset($_POST['cs_dropcap_section_title'][$cs_counter_dropcap]) && $_POST['cs_dropcap_section_title'][$cs_counter_dropcap] != ''){
													$shortcode .= 	'cs_dropcap_section_title="'.htmlspecialchars($_POST['cs_dropcap_section_title'][$cs_counter_dropcap], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_dropcap_color'][$cs_counter_dropcap]) && $_POST['cs_dropcap_color'][$cs_counter_dropcap] != ''){
													$shortcode .= 	'cs_dropcap_color="'.htmlspecialchars($_POST['cs_dropcap_color'][$cs_counter_dropcap]).'" ';
												}
												if(isset($_POST['cs_dropcap_bg_color'][$cs_counter_dropcap]) && $_POST['cs_dropcap_bg_color'][$cs_counter_dropcap] != ''){
													$shortcode .= 	'cs_dropcap_bg_color="'.htmlspecialchars($_POST['cs_dropcap_bg_color'][$cs_counter_dropcap]).'" ';
												}
												
												if(isset($_POST['cs_dropcap_class'][$cs_counter_dropcap]) && $_POST['cs_dropcap_class'][$cs_counter_dropcap] != ''){
													$shortcode .= 	'cs_dropcap_class="'.htmlspecialchars($_POST['cs_dropcap_class'][$cs_counter_dropcap], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_dropcap_animation'][$cs_counter_dropcap]) && $_POST['cs_dropcap_animation'][$cs_counter_dropcap] != ''){
													$shortcode .= 	'cs_dropcap_animation="'.htmlspecialchars($_POST['cs_dropcap_animation'][$cs_counter_dropcap]).'" ';
												}
												$shortcode .= 	']';
												if(isset($_POST['dropcap_content'][$cs_counter_dropcap]) && $_POST['dropcap_content'][$cs_counter_dropcap] != ''){
													$shortcode .= 	htmlspecialchars($_POST['dropcap_content'][$cs_counter_dropcap], ENT_QUOTES);
												}
												$shortcode .= 	'[/cs_dropcap]';
												$cs_dropcap->addChild('cs_shortcode', $shortcode );
												$cs_counter_dropcap++;
											}
											$cs_global_counter_dropcap++;
										} 
										// Save testimonials page element 
										else if ( $_POST['cs_orderby'][$cs_counter] == "testimonials" ) {
											$shortcode = $shortcode_item = '';
											$cs_testimonials = $column->addChild('testimonials');
											$cs_testimonials->addChild('page_element_size', htmlspecialchars($_POST['testimonials_element_size'][$cs_global_counter_testimonials]) );
											$cs_testimonials->addChild('testimonials_element_size', htmlspecialchars($_POST['testimonials_element_size'][$cs_global_counter_testimonials]) );
											if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
												$cs_shortcode_str = stripslashes ($_POST['shortcode']['testimonials'][$cs_shortcode_counter_testimonial]);
												$cs_shortcode_counter_testimonial++;
												$cs_testimonials->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
											} else {
												if(isset($_POST['testimonials_num'][$cs_counter_testimonials]) && $_POST['testimonials_num'][$cs_counter_testimonials]>0){
													for ( $i = 1; $i <= $_POST['testimonials_num'][$cs_counter_testimonials]; $i++ ){
														$shortcode_item .= '[testimonial_item ';
														
														if(isset($_POST['cs_testimonial_company'][$cs_counter_testimonials_node]) && $_POST['cs_testimonial_company'][$cs_counter_testimonials_node] != ''){
															$shortcode_item .= 	'cs_testimonial_company="'.htmlspecialchars($_POST['cs_testimonial_company'][$cs_counter_testimonials_node], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_testimonial_img'][$cs_counter_testimonials_node]) && $_POST['cs_testimonial_img'][$cs_counter_testimonials_node] != ''){
															$shortcode_item .= 	'cs_testimonial_img="'.htmlspecialchars($_POST['cs_testimonial_img'][$cs_counter_testimonials_node], ENT_QUOTES).'" ';
														}
														
														if(isset($_POST['cs_testimonial_author'][$cs_counter_testimonials_node]) && $_POST['cs_testimonial_author'][$cs_counter_testimonials_node] != ''){
															$shortcode_item .= 	'cs_testimonial_author="'.htmlspecialchars($_POST['cs_testimonial_author'][$cs_counter_testimonials_node], ENT_QUOTES).'" ';
														}
														$shortcode_item .= 	']';
														if(isset($_POST['cs_testimonial_text'][$cs_counter_testimonials_node]) && $_POST['cs_testimonial_text'][$cs_counter_testimonials_node] != ''){
															$shortcode_item .= 	htmlspecialchars($_POST['cs_testimonial_text'][$cs_counter_testimonials_node], ENT_QUOTES);
														}
														$shortcode_item .= 	'[/testimonial_item]'; 
														$cs_counter_testimonials_node++;
													}
												}
												$cs_section_title = '';
												if(isset($_POST['cs_testimonial_section_title'][$cs_counter_testimonials]) && $_POST['cs_testimonial_section_title'][$cs_counter_testimonials] != ''){
													$cs_section_title = 	'cs_testimonial_section_title="'.htmlspecialchars($_POST['cs_testimonial_section_title'][$cs_counter_testimonials], ENT_QUOTES).'" ';
												}
												$shortcode = '[cs_testimonials cs_testimonial_style="'.htmlspecialchars($_POST['cs_testimonial_style'][$cs_counter_testimonials], ENT_QUOTES).'" 
												 cs_testimonial_text_color="'.htmlspecialchars($_POST['cs_testimonial_text_color'][$cs_counter_testimonials]).'"
												 cs_testimonial_text_align="'.htmlspecialchars($_POST['cs_testimonial_text_align'][$cs_counter_testimonials]).'"
												 cs_testimonial_class="'.htmlspecialchars($_POST['cs_testimonial_class'][$cs_counter_testimonials], ENT_QUOTES).'"
												 cs_testimonial_animation="'.htmlspecialchars($_POST['cs_testimonial_animation'][$cs_counter_testimonials]).'"
												 '.$cs_section_title.' ]'.$shortcode_item.'[/cs_testimonials]';
												$cs_testimonials->addChild('cs_shortcode', $shortcode );
												$cs_counter_testimonials++;
											}
											$cs_global_counter_testimonials++;
										}
										// Save List page element 
										 else if ( $_POST['cs_orderby'][$cs_counter] == "list" ) {
											$shortcode = $shortcode_item = '';
											$cs_lists = $column->addChild('list');
											$cs_lists->addChild('page_element_size', htmlspecialchars($_POST['list_element_size'][$cs_global_counter_list]) );
											$cs_lists->addChild('list_element_size', htmlspecialchars($_POST['list_element_size'][$cs_global_counter_list]) );
											if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
												$cs_shortcode_str = stripslashes ($_POST['shortcode']['list'][$cs_shortcode_counter_list]);
												$cs_shortcode_counter_list++;
												$cs_lists->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
											} else {
												if(isset($_POST['list_num'][$cs_counter_list]) && $_POST['list_num'][$cs_counter_list]>0){
													for ( $i = 1; $i <= $_POST['list_num'][$cs_counter_list]; $i++ ){
														$shortcode_item .= '[list_item ';
														if(isset($_POST['cs_list_icon'][$cs_counter_lists_node])){
															$shortcode_item .= 	'cs_list_icon="'.htmlspecialchars($_POST['cs_list_icon'][$cs_counter_lists_node], ENT_QUOTES).'" ';
														}
														$shortcode_item .= 	']';
														if(isset($_POST['cs_list_item'][$cs_counter_lists_node])){
															$shortcode_item .= 	htmlspecialchars($_POST['cs_list_item'][$cs_counter_lists_node], ENT_QUOTES);
														}
														$shortcode_item .= 	'[/list_item]'; 
														$cs_counter_lists_node++;
													}
												}
												$shortcode = '[cs_list ';
												
												$shortcode .= 	'column_size="1/1" ';
												if(isset($_POST['cs_list_type'][$cs_counter_list]) && $_POST['cs_list_type'][$cs_counter_list] != ''){
													$shortcode .= 	'cs_list_type="'.htmlspecialchars($_POST['cs_list_type'][$cs_counter_list]).'" ';
												}
												if(isset($_POST['cs_border'][$cs_counter_list]) && $_POST['cs_border'][$cs_counter_list] != ''){
													$shortcode .= 	'cs_border="'.htmlspecialchars($_POST['cs_border'][$cs_counter_list]).'" ';
												}
												if(isset($_POST['cs_list_section_title'][$cs_counter_list]) && $_POST['cs_list_section_title'][$cs_counter_list] != ''){
													$shortcode .= 	'cs_list_section_title="'.htmlspecialchars($_POST['cs_list_section_title'][$cs_counter_list], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_list_class'][$cs_counter_list]) && $_POST['cs_list_class'][$cs_counter_list] != ''){
													$shortcode .= 	'cs_list_class="'.htmlspecialchars($_POST['cs_list_class'][$cs_counter_list], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_list_animation'][$cs_counter_list]) && $_POST['cs_list_animation'][$cs_counter_list] != ''){
													$shortcode .= 	'cs_list_animation="'.htmlspecialchars($_POST['cs_list_animation'][$cs_counter_list]).'" ';
												}
												$shortcode .= 	']'.$shortcode_item.'[/cs_list]';
												$cs_lists->addChild('cs_shortcode', $shortcode );
												$cs_counter_list++;
											}
											$cs_global_counter_list++;
										}
										// Save message page element 
										else if ( $_POST['cs_orderby'][$cs_counter] == "mesage" ) {
												$shortcode = $shortcode_item = '';
											$cs_message = $column->addChild('mesage');
											$cs_message->addChild('page_element_size', htmlspecialchars($_POST['mesage_element_size'][$cs_global_counter_message]) );
											$cs_message->addChild('message_element_size', htmlspecialchars($_POST['mesage_element_size'][$cs_global_counter_message]) );
											if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
												$cs_shortcode_str = stripslashes ($_POST['shortcode']['mesage'][$cs_shortcode_counter_message]);
												$cs_shortcode_counter_message++;
												$cs_message->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
											} else {
												$shortcode = '[cs_message ';
												if(isset($_POST['cs_message_title'][$cs_counter_message]) && $_POST['cs_message_title'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_message_title="'.htmlspecialchars($_POST['cs_message_title'][$cs_counter_message], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_title_color'][$cs_counter_message]) && $_POST['cs_title_color'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_title_color="'.htmlspecialchars($_POST['cs_title_color'][$cs_counter_message]).'" ';
												}
												if(isset($_POST['cs_text_color'][$cs_counter_message]) && $_POST['cs_text_color'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_text_color="'.htmlspecialchars($_POST['cs_text_color'][$cs_counter_message]).'" ';
												}
												if(isset($_POST['cs_background_color'][$cs_counter_message]) && $_POST['cs_background_color'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_background_color="'.htmlspecialchars($_POST['cs_background_color'][$cs_counter_message]).'" ';
												}
												if(isset($_POST['cs_icon_color'][$cs_counter_message]) && $_POST['cs_icon_color'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_icon_color="'.htmlspecialchars($_POST['cs_icon_color'][$cs_counter_message]).'" ';
												}
												if(isset($_POST['cs_message_box_title'][$cs_counter_message]) && $_POST['cs_message_box_title'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_message_box_title="'.htmlspecialchars($_POST['cs_message_box_title'][$cs_counter_message]).'" ';
												}
												if(isset($_POST['cs_button_text'][$cs_counter_message]) && $_POST['cs_button_text'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_button_text="'.htmlspecialchars($_POST['cs_button_text'][$cs_counter_message], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_button_link'][$cs_counter_message]) && $_POST['cs_button_link'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_button_link="'.htmlspecialchars($_POST['cs_button_link'][$cs_counter_message], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_message_icon'][$cs_counter_message]) && $_POST['cs_message_icon'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_message_icon="'.htmlspecialchars($_POST['cs_message_icon'][$cs_counter_message]).'" ';
												}
												if(isset($_POST['cs_message_type'][$cs_counter_message]) && $_POST['cs_message_type'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_message_type="'.htmlspecialchars($_POST['cs_message_type'][$cs_counter_message]).'" ';
												}
												if(isset($_POST['cs_style_type'][$cs_counter_message]) && $_POST['cs_style_type'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_style_type="'.htmlspecialchars($_POST['cs_style_type'][$cs_counter_message]).'" ';
												}
												if(isset($_POST['cs_message_close'][$cs_counter_message]) && $_POST['cs_message_close'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_message_close="'.htmlspecialchars($_POST['cs_message_close'][$cs_counter_message]).'" ';
												}
												if(isset($_POST['cs_alert_style'][$cs_counter_message]) && $_POST['cs_alert_style'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_alert_style="'.htmlspecialchars($_POST['cs_alert_style'][$cs_counter_message]).'" ';
												}
												if(isset($_POST['cs_msg_section_title'][$cs_counter_message]) && $_POST['cs_msg_section_title'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_msg_section_title="'.htmlspecialchars($_POST['cs_msg_section_title'][$cs_counter_message], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_message_class'][$cs_counter_message]) && $_POST['cs_message_class'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_message_class="'.htmlspecialchars($_POST['cs_message_class'][$cs_counter_message], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_message_animation'][$cs_counter_message]) && $_POST['cs_message_animation'][$cs_counter_message] != ''){
													$shortcode .= 	'cs_message_animation="'.htmlspecialchars($_POST['cs_message_animation'][$cs_counter_message]).'" ';
												}
												$shortcode .= 	']';
												if(isset($_POST['cs_message_text'][$cs_counter_message]) && $_POST['cs_message_text'][$cs_counter_message] != ''){
													$shortcode .= 	htmlspecialchars($_POST['cs_message_text'][$cs_counter_message], ENT_QUOTES);
												}
												$shortcode .= 	'[/cs_message]';
												$cs_message->addChild('cs_shortcode', $shortcode );
												$cs_counter_message++;
											}
											$cs_global_counter_message++;
										}
										// Typography end
										
										// Common Elements Start
											
											// Services
											else if ( $_POST['cs_orderby'][$cs_counter] == "services" ) {
													$shortcode = $shortcode_item = '';
													$cs_services  = $column->addChild('services');
													$cs_services->addChild('page_element_size', $_POST['services_element_size'][$cs_global_counter_services]);
													$cs_services->addChild('services_element_size',$_POST['services_element_size'][$cs_global_counter_services]);
													
													if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
														$cs_shortcode_str = stripslashes ($_POST['shortcode']['services'][$cs_shortcode_counter_services]);
														$cs_shortcode_counter_services++;
														$cs_services->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
													} else {
														$shortcode_item .= '[cs_services ';
														if(isset($_POST['cs_service_type'][$cs_counter_services]) && $_POST['cs_service_type'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_type="'.htmlspecialchars($_POST['cs_service_type'][$cs_counter_services], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_service_icon_type'][$cs_counter_services]) && $_POST['cs_service_icon_type'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_icon_type="'.htmlspecialchars($_POST['cs_service_icon_type'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_icon'][$cs_counter_services]) && $_POST['cs_service_icon'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_icon="'.htmlspecialchars($_POST['cs_service_icon'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_icon_color'][$cs_counter_services]) && $_POST['cs_service_icon_color'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_icon_color="'.htmlspecialchars($_POST['cs_service_icon_color'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_bg_image'][$cs_counter_services]) && $_POST['cs_service_bg_image'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_bg_image="'.htmlspecialchars($_POST['cs_service_bg_image'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_bg_color'][$cs_counter_services]) && $_POST['cs_service_bg_color'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_bg_color="'.htmlspecialchars($_POST['cs_service_bg_color'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_title_color'][$cs_counter_services]) && $_POST['cs_service_title_color'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_title_color="'.htmlspecialchars($_POST['cs_service_title_color'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_text_color'][$cs_counter_services]) && $_POST['cs_service_text_color'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_text_color="'.htmlspecialchars($_POST['cs_service_text_color'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_icon_size'][$cs_counter_services]) && $_POST['cs_service_icon_size'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_icon_size="'.htmlspecialchars($_POST['cs_service_icon_size'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_postion_modern'][$cs_counter_services]) && $_POST['cs_service_postion_modern'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_postion_modern="'.htmlspecialchars($_POST['cs_service_postion_modern'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_postion_classic'][$cs_counter_services]) && $_POST['cs_service_postion_classic'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_postion_classic="'.htmlspecialchars($_POST['cs_service_postion_classic'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_title'][$cs_counter_services]) && $_POST['cs_service_title'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_title="'.htmlspecialchars($_POST['cs_service_title'][$cs_counter_services], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_service_link_text'][$cs_counter_services]) && $_POST['cs_service_link_text'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_link_text="'.htmlspecialchars($_POST['cs_service_link_text'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_link_color'][$cs_counter_services]) && $_POST['cs_service_link_color'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_link_color="'.htmlspecialchars($_POST['cs_service_link_color'][$cs_counter_services], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_service_url'][$cs_counter_services]) && $_POST['cs_service_url'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_url="'.htmlspecialchars($_POST['cs_service_url'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_class'][$cs_counter_services]) && $_POST['cs_service_class'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_class="'.htmlspecialchars($_POST['cs_service_class'][$cs_counter_services]).'" ';
														}
														if(isset($_POST['cs_service_animation'][$cs_counter_services]) && $_POST['cs_service_animation'][$cs_counter_services] != ''){
															$shortcode_item .= 	'cs_service_animation="'.htmlspecialchars($_POST['cs_service_animation'][$cs_counter_services]).'" ';
														}
														
														$shortcode_item .= 	']';
														if(isset($_POST['cs_service_content'][$cs_counter_services]) && $_POST['cs_service_content'][$cs_counter_services] != ''){
															$shortcode_item .= 	htmlspecialchars($_POST['cs_service_content'][$cs_counter_services], ENT_QUOTES);
														}
														$shortcode_item .= 	'[/cs_services]';
														$cs_services->addChild('cs_shortcode', $shortcode_item );
												   $cs_counter_services++;
												}
												$cs_global_counter_services++;
											}
											// Accrodian
											else if ( $_POST['cs_orderby'][$cs_counter] == "accordion" ) {
												$shortcode = $shortcode_item = '';
												$cs_accordions = $column->addChild('accordion');
												$cs_accordions->addChild('page_element_size', htmlspecialchars($_POST['accordion_element_size'][$cs_global_counter_accordion]) );
												$cs_accordions->addChild('accordion_element_size', htmlspecialchars($_POST['accordion_element_size'][$cs_global_counter_accordion]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['accordion'][$cs_shortcode_counter_accordion]);
													$cs_shortcode_counter_accordion++;
													$cs_accordions->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													if(isset($_POST['accordion_num'][$cs_counter_accordion]) && $_POST['accordion_num'][$cs_counter_accordion]>0){			
														for ( $i = 1; $i <= $_POST['accordion_num'][$cs_counter_accordion]; $i++ ){
																$shortcode_item .= '[accordian_item ';
																if(isset($_POST['cs_accordion_title'][$cs_counter_accordion_node]) && $_POST['cs_accordion_title'][$cs_counter_accordion_node] != ''){
																	$shortcode_item .= 	'cs_accordion_title="'.htmlspecialchars($_POST['cs_accordion_title'][$cs_counter_accordion_node], ENT_QUOTES).'" ';
																}
																if(isset($_POST['cs_accordion_active'][$cs_counter_accordion_node]) && $_POST['cs_accordion_active'][$cs_counter_accordion_node] != ''){
																	$shortcode_item .= 	'cs_accordion_active="'.htmlspecialchars($_POST['cs_accordion_active'][$cs_counter_accordion_node]).'" ';
																}
																if(isset($_POST['cs_accordian_icon'][$cs_counter_accordion_node]) && $_POST['cs_accordian_icon'][$cs_counter_accordion_node] != ''){
																	$shortcode_item .= 	'cs_accordian_icon="'.htmlspecialchars($_POST['cs_accordian_icon'][$cs_counter_accordion_node], ENT_QUOTES).'" ';
																}
															
																$shortcode_item .= 	']';
																if(isset($_POST['accordion_text'][$cs_counter_accordion_node]) && $_POST['accordion_text'][$cs_counter_accordion_node] != ''){
																	$shortcode_item .= 	htmlspecialchars($_POST['accordion_text'][$cs_counter_accordion_node], ENT_QUOTES);
																}
																$shortcode_item .= 	'[/accordian_item]'; 
																	 
																$cs_counter_accordion_node++;
															}
													}
													
													$cs_section_title = '';
													if(isset($_POST['cs_accordian_section_title'][$cs_counter_accordion]) && $_POST['cs_accordian_section_title'][$cs_counter_accordion] != ''){
														$cs_section_title = 	'cs_accordian_section_title="'.htmlspecialchars($_POST['cs_accordian_section_title'][$cs_counter_accordion], ENT_QUOTES).'" ';
													}
													$shortcode = '[cs_accordian cs_accordian_style="'.htmlspecialchars($_POST['cs_accordian_style'][$cs_counter_accordion]).'" '.$cs_section_title;
													
													
													if(isset($_POST['cs_accordion_class'][$cs_counter_accordion]) && $_POST['cs_accordion_class'][$cs_counter_accordion] != ''){
														$shortcode .= 	' cs_accordion_class="'.htmlspecialchars($_POST['cs_accordion_class'][$cs_counter_accordion], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_accordion_animation'][$cs_counter_accordion]) && $_POST['cs_accordion_animation'][$cs_counter_accordion] != ''){
														$shortcode .= 	' cs_accordion_animation="'.htmlspecialchars($_POST['cs_accordion_animation'][$cs_counter_accordion]).'" ';
													}
													$shortcode .= ']'.$shortcode_item.'[/cs_accordian]';
													
													$cs_accordions->addChild('cs_shortcode', $shortcode );
													$cs_counter_accordion++;
												}
												$cs_global_counter_accordion++;
											}
											// Faq
											else if ( $_POST['cs_orderby'][$cs_counter] == "faq" ) {
												$shortcode = $shortcode_item = '';
												$cs_faqs = $column->addChild('faq');
												$cs_faqs->addChild('page_element_size', htmlspecialchars($_POST['faq_element_size'][$cs_global_counter_faq]) );
												$cs_faqs->addChild('faq_element_size', htmlspecialchars($_POST['faq_element_size'][$cs_global_counter_faq]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['faq'][$cs_shortcode_counter_faq]);
													$cs_shortcode_counter_faq++;
													$cs_faqs->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );

												} else {
													if(isset($_POST['faq_num'][$cs_counter_faq]) && $_POST['faq_num'][$cs_counter_faq]>0){			
														for ( $i = 1; $i <= $_POST['faq_num'][$cs_counter_faq]; $i++ ){
																$shortcode_item .= '[faq_item ';
																if(isset($_POST['cs_faq_title'][$cs_counter_faq_node]) && $_POST['cs_faq_title'][$cs_counter_faq_node] != ''){
																	$shortcode_item .= 	'cs_faq_title="'.htmlspecialchars($_POST['cs_faq_title'][$cs_counter_faq_node], ENT_QUOTES).'" ';
																}
																if(isset($_POST['cs_faq_active'][$cs_counter_faq_node]) && $_POST['cs_faq_active'][$cs_counter_faq_node] != ''){
																	$shortcode_item .= 	'cs_faq_active="'.htmlspecialchars($_POST['cs_faq_active'][$cs_counter_faq_node]).'" ';
																}
																if(isset($_POST['cs_faq_icon'][$cs_counter_faq_node]) && $_POST['cs_faq_icon'][$cs_counter_faq_node] != ''){
																	$shortcode_item .= 	'cs_faq_icon="'.htmlspecialchars($_POST['cs_faq_icon'][$cs_counter_faq_node]).'" ';
																}
															
																$shortcode_item .= 	']';
																if(isset($_POST['faq_text'][$cs_counter_faq_node]) && $_POST['faq_text'][$cs_counter_faq_node] != ''){
																	$shortcode_item .= 	htmlspecialchars($_POST['faq_text'][$cs_counter_faq_node], ENT_QUOTES);
																}
																$shortcode_item .= 	'[/faq_item]'; 
																	 
																$cs_counter_faq_node++;
															}
													}
													
													$cs_section_title = '';
													if(isset($_POST['cs_faq_section_title'][$cs_counter_faq]) && $_POST['cs_faq_section_title'][$cs_counter_faq] != ''){
														$cs_section_title = 	'cs_faq_section_title="'.htmlspecialchars($_POST['cs_faq_section_title'][$cs_counter_faq], ENT_QUOTES).'" ';
													}
													$shortcode = '[cs_faq '.$cs_section_title;
													
													
													if(isset($_POST['cs_faq_class'][$cs_counter_faq]) && $_POST['cs_faq_class'][$cs_counter_faq] != ''){
														$shortcode .= 	' cs_faq_class="'.htmlspecialchars($_POST['cs_faq_class'][$cs_counter_faq], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_faq_animation'][$cs_counter_faq]) && $_POST['cs_faq_animation'][$cs_counter_faq] != ''){
														$shortcode .= 	' cs_faq_animation="'.htmlspecialchars($_POST['cs_faq_animation'][$cs_counter_faq]).'" ';
													}
													$shortcode .= ']'.$shortcode_item.'[/cs_faq]';
													
													$cs_faqs->addChild('cs_shortcode', $shortcode );
													$cs_counter_faq++;
												}
												$cs_global_counter_faq++;
											}
											// Tabs
											else if ( $_POST['cs_orderby'][$cs_counter] == "tabs" ) {
												$shortcode = $shortcode_item = '';
												$cs_tabs = $column->addChild('tabs');
												$cs_tabs->addChild('page_element_size', htmlspecialchars($_POST['tabs_element_size'][$cs_global_counter_tabs]) );
												$cs_tabs->addChild('tabs_element_size', htmlspecialchars($_POST['tabs_element_size'][$cs_global_counter_tabs]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['tabs'][$cs_shortcode_counter_tabs]);
													$cs_shortcode_counter_tabs++;
													$cs_tabs->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												}else {
														if(isset($_POST['tabs_num'][$cs_counter_tabs]) && $_POST['tabs_num'][$cs_counter_tabs]>0){
															for ( $i = 1; $i <= $_POST['tabs_num'][$cs_counter_tabs]; $i++ ){
															$shortcode_item .= '[tab_item ';
															if(isset($_POST['cs_tab_title'][$cs_counter_tabs_node]) && $_POST['cs_tab_title'][$cs_counter_tabs_node] != ''){
																$shortcode_item .= 	'cs_tab_title="'.htmlspecialchars($_POST['cs_tab_title'][$cs_counter_tabs_node], ENT_QUOTES).'" ';
															}
															if(isset($_POST['cs_tab_active'][$cs_counter_tabs_node]) && $_POST['cs_tab_active'][$cs_counter_tabs_node] != ''){
																$shortcode_item .= 	'cs_tab_active="'.htmlspecialchars($_POST['cs_tab_active'][$cs_counter_tabs_node]).'" ';
															}
															if(isset($_POST['cs_tab_icon'][$cs_counter_tabs_node]) && $_POST['cs_tab_icon'][$cs_counter_tabs_node] != ''){
																$shortcode_item .= 	'cs_tab_icon="'.htmlspecialchars($_POST['cs_tab_icon'][$cs_counter_tabs_node], ENT_QUOTES).'" ';
															}
														
															$shortcode_item .= 	']';
															if(isset($_POST['cs_tab_text'][$cs_counter_tabs_node]) && $_POST['cs_tab_text'][$cs_counter_tabs_node] != ''){
																$shortcode_item .=htmlspecialchars($_POST['cs_tab_text'][$cs_counter_tabs_node], ENT_QUOTES);
															}
															$shortcode_item .= 	'[/tab_item]'; 
															$cs_counter_tabs_node++;
														}
													 }
													$cs_section_title = '';
													if(isset($_POST['cs_tabs_section_title'][$cs_counter_tabs]) && $_POST['cs_tabs_section_title'][$cs_counter_tabs] != ''){
														$cs_section_title = 	'cs_tabs_section_title="'.htmlspecialchars($_POST['cs_tabs_section_title'][$cs_counter_tabs], ENT_QUOTES).'" ';
													}
													$shortcode = '[cs_tabs '.$cs_section_title.'  cs_tab_style="'.htmlspecialchars($_POST['cs_tab_style'][$cs_counter_tabs]).'" cs_tabs_class="'.htmlspecialchars($_POST['cs_tabs_class'][$cs_counter_tabs], ENT_QUOTES).'"   cs_tabs_animation="'.htmlspecialchars($_POST['cs_tabs_animation'][$cs_counter_tabs]).'"]'.$shortcode_item.'[/cs_tabs]';
													$cs_tabs->addChild('cs_shortcode', $shortcode );
												$cs_counter_tabs++;
												}
										    $cs_global_counter_tabs++;
											}
											// Toggle
											else if ( $_POST['cs_orderby'][$cs_counter] == "toggle" ) {
												$shortcode = '';
												$cs_toggle = $column->addChild('toggle');
												$cs_toggle->addChild('page_element_size', htmlspecialchars($_POST['toggle_element_size'][$cs_global_counter_toggle]) );
												$cs_toggle->addChild('toggle_element_size', htmlspecialchars($_POST['toggle_element_size'][$cs_global_counter_toggle]) );
												
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['toggle'][$cs_shortcode_counter_toggle]);
													$cs_shortcode_counter_toggle++;
													$cs_toggle->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													$shortcode .= '[cs_toggle ';
													if(isset($_POST['cs_toggle_section_title'][$cs_counter_toggle]) && $_POST['cs_toggle_section_title'][$cs_counter_toggle] != ''){
														$shortcode .= 	'cs_toggle_section_title="'.htmlspecialchars($_POST['cs_toggle_section_title'][$cs_counter_toggle]).'" ';
													}
													if(isset($_POST['cs_toggle_title'][$cs_counter_toggle]) && trim($_POST['cs_toggle_title'][$cs_counter_toggle]) <> ''){
														$shortcode .= 	'cs_toggle_title="'.htmlspecialchars($_POST['cs_toggle_title'][$cs_counter_toggle], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_toggle_icon'][$cs_counter_toggle]) && $_POST['cs_toggle_icon'][$cs_counter_toggle] != ''){
														$shortcode .= 	'cs_toggle_icon="'.htmlspecialchars($_POST['cs_toggle_icon'][$cs_counter_toggle], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_toggle_state'][$cs_counter_tabs_node]) && $_POST['cs_toggle_state'][$cs_counter_toggle] != ''){
														$shortcode .= 	'cs_toggle_state="'.htmlspecialchars($_POST['cs_toggle_state'][$cs_counter_toggle], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_toggle_custom_class'][$cs_counter_toggle]) && $_POST['cs_toggle_custom_class'][$cs_counter_toggle] != ''){
														$shortcode .= 	'cs_toggle_custom_class="'.htmlspecialchars($_POST['cs_toggle_custom_class'][$cs_counter_toggle], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_toggle_custom_animation'][$cs_counter_toggle]) && $_POST['cs_toggle_custom_animation'][$cs_counter_toggle] != ''){
														$shortcode .= 	'cs_toggle_custom_animation="'.htmlspecialchars($_POST['cs_toggle_custom_animation'][$cs_counter_toggle]).'" ';
													}
												
													$shortcode .= 	']';
													if(isset($_POST['cs_toggle_text'][$cs_counter_toggle]) && $_POST['cs_toggle_text'][$cs_counter_toggle] != ''){
														$shortcode .= 	htmlspecialchars($_POST['cs_toggle_text'][$cs_counter_toggle], ENT_QUOTES);
													}
													$shortcode .= 	'[/cs_toggle]';
													$cs_toggle->addChild('cs_shortcode', $shortcode );
													$cs_counter_toggle++;
												}
												$cs_global_counter_toggle++;
											}
											// Counters
											else if ( $_POST['cs_orderby'][$cs_counter] == "counter" ) {
												$shortcode_item = '';
												$cs_counter_sh = $column->addChild('counter');
												$cs_counter_sh->addChild('counter_element_size', htmlspecialchars($_POST['counter_element_size'][$cs_global_counter_counter]) );
												$cs_counter_sh->addChild('page_element_size', htmlspecialchars($_POST['counter_element_size'][$cs_global_counter_counter]) );
												
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['counter'][$cs_shortcode_counter_counter]);
													$cs_shortcode_counter_counter++;
													$cs_counter_sh->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													$shortcode_item .= '[cs_counter ';
													
													if(isset($_POST['cs_counter_title'][$cs_counter_coutner]) && $_POST['cs_counter_title'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_title="'.htmlspecialchars($_POST['cs_counter_title'][$cs_counter_coutner], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_counter_link_url'][$cs_counter_coutner]) && $_POST['cs_counter_link_url'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_link_url="'.htmlspecialchars($_POST['cs_counter_link_url'][$cs_counter_coutner], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_counter_icon_type'][$cs_counter_coutner]) && $_POST['cs_counter_icon_type'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_icon_type="'.htmlspecialchars($_POST['cs_counter_icon_type'][$cs_counter_coutner]).'" ';
													}
													if(isset($_POST['cs_counter_icon'][$cs_counter_coutner]) && $_POST['cs_counter_icon'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_icon="'.htmlspecialchars($_POST['cs_counter_icon'][$cs_counter_coutner], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_counter_icon_align'][$cs_counter_coutner]) && $_POST['cs_counter_icon_align'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_icon_align="'.htmlspecialchars($_POST['cs_counter_icon_align'][$cs_counter_coutner], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_counter_logo'][$cs_counter_coutner]) && $_POST['cs_counter_logo'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_logo="'.htmlspecialchars($_POST['cs_counter_logo'][$cs_counter_coutner], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_counter_icon_color'][$cs_counter_coutner]) && $_POST['cs_counter_icon_color'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_icon_color="'.htmlspecialchars($_POST['cs_counter_icon_color'][$cs_counter_coutner], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_counter_numbers'][$cs_counter_coutner]) && $_POST['cs_counter_numbers'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_numbers="'.htmlspecialchars($_POST['cs_counter_numbers'][$cs_counter_coutner]).'" ';
													}
													if(isset($_POST['cs_counter_number_color'][$cs_counter_coutner]) && $_POST['cs_counter_number_color'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_number_color="'.htmlspecialchars($_POST['cs_counter_number_color'][$cs_counter_coutner]).'" ';
													}
													if(isset($_POST['cs_counter_text_color'][$cs_counter_coutner]) && $_POST['cs_counter_text_color'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_text_color="'.htmlspecialchars($_POST['cs_counter_text_color'][$cs_counter_coutner]).'" ';
													}
													if(isset($_POST['cs_counter_border'][$cs_counter_coutner]) && $_POST['cs_counter_border'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_border="'.htmlspecialchars($_POST['cs_counter_border'][$cs_counter_coutner]).'" ';
													}
													if(isset($_POST['cs_counter_class'][$cs_counter_coutner]) && $_POST['cs_counter_class'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_class="'.htmlspecialchars($_POST['cs_counter_class'][$cs_counter_coutner], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_counter_animation'][$cs_counter_coutner]) && $_POST['cs_counter_animation'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	'cs_counter_animation="'.htmlspecialchars($_POST['cs_counter_animation'][$cs_counter_coutner]).'" ';
													}
													$shortcode_item .= 	']';
													if(isset($_POST['counter_text'][$cs_counter_coutner]) && $_POST['counter_text'][$cs_counter_coutner] != ''){
														$shortcode_item .= 	htmlspecialchars($_POST['counter_text'][$cs_counter_coutner], ENT_QUOTES);
													}
													$shortcode_item .= 	'[/cs_counter]'; 
													$cs_counter_sh->addChild('cs_shortcode', $shortcode_item );
												$cs_counter_coutner++;
											  }
											  $cs_global_counter_counter++;
											}
											// Pricetable
											else if ( $_POST['cs_orderby'][$cs_counter] == "pricetable" ) {
												$shortcode = $cs_price_item = $shortcode_item = '';
												$cs_pricetable_item = $column->addChild('pricetable');
												$cs_pricetable_item->addChild('page_element_size', htmlspecialchars($_POST['pricetable_element_size'][$cs_global_counter_pricetables]) );
												$cs_pricetable_item->addChild('pricetable_element_size', htmlspecialchars($_POST['pricetable_element_size'][$cs_global_counter_pricetables]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['pricetable'][$cs_shortcode_counter_pricetables]);
													$cs_shortcode_counter_pricetables++;
													$cs_pricetable_item->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													
													if(isset($_POST['cs_price_num'][$cs_counter_pricetables]) && $_POST['cs_price_num'][$cs_counter_pricetables]>0){
														for ( $i = 1; $i <= $_POST['cs_price_num'][$cs_counter_pricetables]; $i++ ){
															$cs_price_item .= '[pricing_item ';
															$cs_price_item .= 	']';
															if(isset($_POST['cs_pricing_feature'][$cs_counter_pricetables_node]) && $_POST['cs_pricing_feature'][$cs_counter_pricetables_node] != ''){
																$cs_price_item .= stripslashes(htmlspecialchars($_POST['cs_pricing_feature'][$cs_counter_pricetables_node], ENT_QUOTES));
															}
															$cs_price_item .= 	'[/pricing_item]'; 
															$cs_counter_pricetables_node++;
														}
													}
													 
													$cs_section_title = '';
													if(isset($_POST['cs_pricetable_section_title'][$cs_counter_pricetables]) && $_POST['cs_pricetable_section_title'][$cs_counter_pricetables] != ''){
														$cs_section_title = ' cs_pricetable_section_title="'.htmlspecialchars($_POST['cs_pricetable_section_title'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_pricetable_style'][$cs_counter_pricetables]) && $_POST['cs_pricetable_style'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_style="'.htmlspecialchars($_POST['cs_pricetable_style'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_pricetable_title'][$cs_counter_pricetables]) && $_POST['cs_pricetable_title'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_title="'.htmlspecialchars($_POST['cs_pricetable_title'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_pricetable_title_bgcolor'][$cs_counter_pricetables]) && $_POST['cs_pricetable_title_bgcolor'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_title_bgcolor="'.htmlspecialchars($_POST['cs_pricetable_title_bgcolor'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_pricetable_desc'][$cs_counter_pricetables]) && $_POST['cs_pricetable_desc'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_desc="'.htmlspecialchars($_POST['cs_pricetable_desc'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_pricetable_price'][$cs_counter_pricetables]) && $_POST['cs_pricetable_price'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_price="'.htmlspecialchars($_POST['cs_pricetable_price'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_pricetable_img'][$cs_counter_pricetables]) && $_POST['cs_pricetable_img'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_img="'.htmlspecialchars($_POST['cs_pricetable_img'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_pricetable_period'][$cs_counter_pricetables]) && $_POST['cs_pricetable_period'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_period="'.htmlspecialchars($_POST['cs_pricetable_period'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_pricetable_bgcolor'][$cs_counter_pricetables]) && $_POST['cs_pricetable_bgcolor'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_bgcolor="'.htmlspecialchars($_POST['cs_pricetable_bgcolor'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_btn_text'][$cs_counter_pricetables]) && $_POST['cs_btn_text'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_btn_text="'.htmlspecialchars($_POST['cs_btn_text'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_btn_bg_color'][$cs_counter_pricetables]) && $_POST['cs_btn_bg_color'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_btn_bg_color="'.htmlspecialchars($_POST['cs_btn_bg_color'][$cs_counter_pricetables]).'" ';
													}
													if(isset($_POST['cs_pricetable_featured'][$cs_counter_pricetables]) && $_POST['cs_pricetable_featured'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_featured="'.htmlspecialchars($_POST['cs_pricetable_featured'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_pricetable_class'][$cs_counter_pricetables]) && $_POST['cs_pricetable_class'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_class="'.htmlspecialchars($_POST['cs_pricetable_class'][$cs_counter_pricetables], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_pricetable_animation'][$cs_counter_pricetables]) && $_POST['cs_pricetable_animation'][$cs_counter_pricetables] != ''){
														$shortcode_item .= 	'cs_pricetable_animation="'.htmlspecialchars($_POST['cs_pricetable_animation'][$cs_counter_pricetables]).'" ';
													}
													$shortcode = '[cs_pricetable '.$cs_section_title.' '.$shortcode_item.']';
													$shortcode .= 	$cs_price_item . '[/cs_pricetable]';
													$cs_pricetable_item->addChild('cs_shortcode', $shortcode );
													$cs_counter_pricetables++;
												}
											  $cs_global_counter_pricetables++;
											}
											// Progressbar
											else if ( $_POST['cs_orderby'][$cs_counter] == "progressbars" ) {
												$shortcode = $shortcode_item = '';
												$cs_progressbars = $column->addChild('progressbars');
												$cs_progressbars->addChild('progressbars_element_size', $_POST['progressbars_element_size'][$cs_global_counter_progressbars] );
												$cs_progressbars->addChild('page_element_size', $_POST['progressbars_element_size'][$cs_global_counter_progressbars] );
												
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['progressbars'][$cs_shortcode_counter_progressbars]);
													$cs_shortcode_counter_progressbars++;
													$cs_progressbars->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													if(isset($_POST['progressbars_num'][$cs_counter_progressbars]) && $_POST['progressbars_num'][$cs_counter_progressbars]>0){
														for ( $i = 1; $i <= $_POST['progressbars_num'][$cs_counter_progressbars]; $i++ ){
															$shortcode_item .= '[progressbar_item ';
															if(isset($_POST['cs_progressbars_title'][$cs_counter_progressbars_node]) && $_POST['cs_progressbars_title'][$cs_counter_progressbars_node] != ''){
																$shortcode_item .= 	'cs_progressbars_title="'.htmlspecialchars($_POST['cs_progressbars_title'][$cs_counter_progressbars_node], ENT_QUOTES).'" ';
															}
															if(isset($_POST['cs_progressbars_percentage'][$cs_counter_progressbars_node]) && $_POST['cs_progressbars_percentage'][$cs_counter_progressbars_node] != ''){
																$shortcode_item .= 	'cs_progressbars_percentage="'.htmlspecialchars($_POST['cs_progressbars_percentage'][$cs_counter_progressbars_node], ENT_QUOTES).'" ';
															}
															if(isset($_POST['cs_progressbars_color'][$cs_counter_progressbars_node]) && $_POST['cs_progressbars_color'][$cs_counter_progressbars_node] != ''){
																$shortcode_item .= 	'cs_progressbars_color="'.htmlspecialchars($_POST['cs_progressbars_color'][$cs_counter_progressbars_node], ENT_QUOTES).'" ';
															}
															$shortcode_item .= 	']'; 
															 
															$cs_counter_progressbars_node++;
														}
													}
													$shortcode .= '[cs_progressbars ';
													
													if(isset($_POST['cs_progressbars_style'][$cs_counter_progressbars]) && trim($_POST['cs_progressbars_style'][$cs_counter_progressbars]) <> ''){
														$shortcode .= 	'cs_progressbars_style="'.htmlspecialchars($_POST['cs_progressbars_style'][$cs_counter_progressbars]).'" ';
													}
													if(isset($_POST['cs_progressbars_class'][$cs_counter_progressbars]) && $_POST['cs_progressbars_class'][$cs_counter_progressbars] != ''){
														$shortcode .= 	'cs_progressbars_class="'.htmlspecialchars($_POST['cs_progressbars_class'][$cs_counter_progressbars], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_progressbars_animation'][$cs_counter_progressbars]) && $_POST['cs_progressbars_animation'][$cs_counter_progressbars] != ''){
														$shortcode .= 	'cs_progressbars_animation="'.htmlspecialchars($_POST['cs_progressbars_animation'][$cs_counter_progressbars]).'" ';
													}
													
													$shortcode .= 	']'.$shortcode_item.'[/cs_progressbars]';
													
													$cs_progressbars->addChild('cs_shortcode', $shortcode );
												
												$cs_counter_progressbars++;
											}
											$cs_global_counter_progressbars++;
										}
											// Table
											else if ( $_POST['cs_orderby'][$cs_counter] == "table" ) {
												$shortcode = '';
												$cs_table 	   = $column->addChild('table');
												$cs_table->addChild('table_element_size', $_POST['table_element_size'][$cs_global_counter_table] );
												$cs_table->addChild('page_element_size', $_POST['table_element_size'][$cs_global_counter_table] );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
 													$cs_shortcode_str = stripslashes ($_POST['shortcode']['table'][$cs_shortcode_counter_table]);
													$cs_shortcode_counter_table++;
													$cs_table->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str) );
												} else {
													$shortcode .= '[cs_table ';
													if(isset($_POST['cs_table_section_title'][$cs_counter_table]) && $_POST['cs_table_section_title'][$cs_counter_table] != ''){
														$shortcode .= ' cs_table_section_title="'.htmlspecialchars($_POST['cs_table_section_title'][$cs_counter_table], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_table_style'][$cs_counter_table]) && trim($_POST['cs_table_style'][$cs_counter_table]) <> ''){
														$shortcode .= 	'cs_table_style="'.htmlspecialchars($_POST['cs_table_style'][$cs_counter_table], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_table_class'][$cs_counter_table]) && $_POST['cs_table_class'][$cs_counter_table] != ''){
														$shortcode .= 	'cs_table_class="'.htmlspecialchars($_POST['cs_table_class'][$cs_counter_table], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_table_animation'][$cs_counter_table]) && $_POST['cs_table_animation'][$cs_counter_table] != ''){
														$shortcode .= 	'cs_table_animation="'.htmlspecialchars($_POST['cs_table_animation'][$cs_counter_table]).'" ';
													}
													$shortcode .= ']';
													if(isset($_POST['cs_table_content'][$cs_counter_table]) && $_POST['cs_table_content'][$cs_counter_table] != ''){
														$shortcode .= htmlspecialchars($_POST['cs_table_content'][$cs_counter_table], ENT_QUOTES);
													}
													$shortcode .= 	'[/cs_table]';
													$cs_table->addChild('cs_shortcode', $shortcode );															
													$cs_counter_table++;
												}
												$cs_global_counter_table++;
											}
											// Button
											else if ( $_POST['cs_orderby'][$cs_counter] == "button" ) {
												$shortcode  = '';
												$cs_button = $column->addChild('button');
												$cs_button->addChild('page_element_size', htmlspecialchars($_POST['button_element_size'][$cs_global_counter_button]) );
												$cs_button->addChild('button_element_size', htmlspecialchars($_POST['button_element_size'][$cs_global_counter_button]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['button'][$cs_shortcode_counter_button]);
													$cs_button->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
													$cs_shortcode_counter_button++;
												} else {
													$shortcode .= '[cs_button  ';
													if(isset($_POST['cs_button_size'][$cs_counter_button]) && trim($_POST['cs_button_size'][$cs_counter_button]) <> ''){
														$shortcode .= 	'cs_button_size="'.htmlspecialchars($_POST['cs_button_size'][$cs_counter_button]).'" ';
													}
													if(isset($_POST['cs_button_title'][$cs_counter_button]) && trim($_POST['cs_button_title'][$cs_counter_button]) <> ''){
														$shortcode .= 	'cs_button_title="'.htmlspecialchars($_POST['cs_button_title'][$cs_counter_button], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_button_link'][$cs_counter_button]) && trim($_POST['cs_button_link'][$cs_counter_button]) <> ''){
														$shortcode .= 	'cs_button_link="'.htmlspecialchars($_POST['cs_button_link'][$cs_counter_button], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_button_bg_color'][$cs_counter_button]) && trim($_POST['cs_button_bg_color'][$cs_counter_button]) <> ''){
														$shortcode .= 	'cs_button_bg_color="'.htmlspecialchars($_POST['cs_button_bg_color'][$cs_counter_button], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_button_color'][$cs_counter_button]) && trim($_POST['cs_button_color'][$cs_counter_button]) <> ''){
														$shortcode .= 	'cs_button_color="'.htmlspecialchars($_POST['cs_button_color'][$cs_counter_button], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_border_cs_button_color'][$cs_counter_button]) && trim($_POST['cs_border_cs_button_color'][$cs_counter_button]) <> ''){
														$shortcode .= 	'cs_border_cs_button_color="'.htmlspecialchars($_POST['cs_border_cs_button_color'][$cs_counter_button]).'" ';
													}
													if(isset($_POST['cs_button_icon'][$cs_counter_button]) && trim($_POST['cs_button_icon'][$cs_counter_button]) <> ''){
														$shortcode .= 	'cs_button_icon="'.htmlspecialchars($_POST['cs_button_icon'][$cs_counter_button], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_button_icon_position'][$cs_counter_button]) && trim($_POST['cs_button_icon_position'][$cs_counter_button]) <> ''){
														$shortcode .= 	'cs_button_icon_position="'.htmlspecialchars($_POST['cs_button_icon_position'][$cs_counter_button]).'" ';
													}
													if(isset($_POST['cs_button_type'][$cs_counter_button]) && trim($_POST['cs_button_type'][$cs_counter_button]) <> ''){
														$shortcode .= 	'cs_button_type="'.htmlspecialchars($_POST['cs_button_type'][$cs_counter_button]).'" ';
													}
													if(isset($_POST['cs_button_target'][$cs_counter_button]) && trim($_POST['cs_button_target'][$cs_counter_button]) <> ''){
														$shortcode .= 	'cs_button_target="'.htmlspecialchars($_POST['cs_button_target'][$cs_counter_button]).'" ';
													}
													if(isset($_POST['cs_button_class'][$cs_counter_button]) && $_POST['cs_button_class'][$cs_counter_button] != ''){
														$shortcode .= 	'cs_button_class="'.htmlspecialchars($_POST['cs_button_class'][$cs_counter_button], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_button_animation'][$cs_counter_button]) && $_POST['cs_button_animation'][$cs_counter_button] != ''){
														$shortcode .= 	'cs_button_animation="'.htmlspecialchars($_POST['cs_button_animation'][$cs_counter_button]).'" ';
													}
													$shortcode .= 	']';
													$cs_button->addChild('cs_shortcode', $shortcode );
													$cs_counter_button++;
												}
												$cs_global_counter_button++;
											}
											
											// Call to action
											else if ( $_POST['cs_orderby'][$cs_counter] == "call_to_action" ) {
												$shortcode 		= '';
												$cs_call_to_action = $column->addChild('call_to_action');
												$cs_call_to_action->addChild('page_element_size', htmlspecialchars($_POST['call_to_action_element_size'][$cs_global_counter_call_to_action]) );
												$cs_call_to_action->addChild('call_to_action_element_size', htmlspecialchars($_POST['call_to_action_element_size'][$cs_global_counter_call_to_action]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = htmlspecialchars( stripslashes ($_POST['shortcode']['call_to_action'][$cs_shortcode_counter_call_to_action]));
													$cs_shortcode_counter_call_to_action++;
													$cs_call_to_action->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str) );
												} else {
													$shortcode .= '[call_to_action ';
													if(isset($_POST['cs_call_to_action_section_title'][$cs_counter_call_to_action]) && $_POST['cs_call_to_action_section_title'][$cs_counter_call_to_action] != ''){
														$shortcode .= ' cs_call_to_action_section_title="'.htmlspecialchars($_POST['cs_call_to_action_section_title'][$cs_counter_call_to_action], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_content_type'][$cs_counter_call_to_action]) && trim($_POST['cs_content_type'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_content_type="'.htmlspecialchars($_POST['cs_content_type'][$cs_counter_call_to_action]).'" ';
													}
													if(isset($_POST['cs_call_action_title'][$cs_counter_call_to_action]) && trim($_POST['cs_call_action_title'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_call_action_title="'.htmlspecialchars($_POST['cs_call_action_title'][$cs_counter_call_to_action], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_contents_color'][$cs_counter_call_to_action]) && trim($_POST['cs_contents_color'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_contents_color="'.htmlspecialchars($_POST['cs_contents_color'][$cs_counter_call_to_action]).'" ';
													}
													
													if(isset($_POST['cs_title_color'][$cs_counter_call_to_action]) && trim($_POST['cs_title_color'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_title_color="'.htmlspecialchars($_POST['cs_title_color'][$cs_counter_call_to_action]).'" ';
													}
													if(isset($_POST['cs_contents_color'][$cs_counter_call_to_action]) && trim($_POST['cs_contents_color'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_contents_color="'.htmlspecialchars($_POST['cs_contents_color'][$cs_counter_call_to_action]).'" ';
													}
													if(isset($_POST['cs_call_action_icon'][$cs_counter_call_to_action]) && trim($_POST['cs_call_action_icon'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_call_action_icon="'.htmlspecialchars($_POST['cs_call_action_icon'][$cs_counter_call_to_action]).'" ';
													}
													if(isset($_POST['cs_icon_color'][$cs_counter_call_to_action]) && trim($_POST['cs_icon_color'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_icon_color="'.htmlspecialchars($_POST['cs_icon_color'][$cs_counter_call_to_action]).'" ';
													}
													if(isset($_POST['cs_call_to_action_icon_background_color'][$cs_counter_call_to_action]) && trim($_POST['cs_call_to_action_icon_background_color'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_call_to_action_icon_background_color="'.htmlspecialchars($_POST['cs_call_to_action_icon_background_color'][$cs_counter_call_to_action]).'" ';
													}
													if(isset($_POST['cs_show_button'][$cs_counter_call_to_action]) && trim($_POST['cs_show_button'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_show_button="'.htmlspecialchars($_POST['cs_show_button'][$cs_counter_call_to_action]).'" ';
													}
													if(isset($_POST['cs_call_to_action_button_text'][$cs_counter_call_to_action]) && trim($_POST['cs_call_to_action_button_text'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_call_to_action_button_text="'.htmlspecialchars($_POST['cs_call_to_action_button_text'][$cs_counter_call_to_action], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_call_to_action_button_link'][$cs_counter_call_to_action]) && trim($_POST['cs_call_to_action_button_link'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_call_to_action_button_link="'.htmlspecialchars($_POST['cs_call_to_action_button_link'][$cs_counter_call_to_action]).'" ';
													}
													
													if(isset($_POST['cs_call_to_action_bg_img'][$cs_counter_call_to_action]) && trim($_POST['cs_call_to_action_bg_img'][$cs_counter_call_to_action]) <> ''){
														$shortcode .= 	'cs_call_to_action_bg_img="'.htmlspecialchars($_POST['cs_call_to_action_bg_img'][$cs_counter_call_to_action]).'" ';
													}
													if(isset($_POST['cs_call_to_action_class'][$cs_counter_call_to_action]) && $_POST['cs_call_to_action_class'][$cs_counter_call_to_action] != ''){
														$shortcode .= 	'cs_call_to_action_class="'.htmlspecialchars($_POST['cs_call_to_action_class'][$cs_counter_call_to_action], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_call_to_action_animation'][$cs_counter_call_to_action]) && $_POST['cs_call_to_action_animation'][$cs_counter_call_to_action] != ''){
														$shortcode .= 	'cs_call_to_action_animation="'.htmlspecialchars($_POST['cs_call_to_action_animation'][$cs_counter_call_to_action]).'" ';
													}
													
													if(isset($_POST['cs_btn_bg_color'][$cs_counter_call_to_action]) && $_POST['cs_btn_bg_color'][$cs_counter_call_to_action] != ''){
														$shortcode .= 	'cs_btn_bg_color="'.htmlspecialchars($_POST['cs_btn_bg_color'][$cs_counter_call_to_action]).'" ';
													}
													$shortcode .= 	']';
													if(isset($_POST['cs_call_action_contents'][$cs_counter_call_to_action]) && $_POST['cs_call_action_contents'][$cs_counter_call_to_action] != ''){
														$shortcode .= 	htmlspecialchars($_POST['cs_call_action_contents'][$cs_counter_call_to_action], ENT_QUOTES);
													}
													$shortcode .= 	'[/call_to_action]';
													
													$cs_call_to_action->addChild('cs_shortcode', $shortcode );
													$cs_counter_call_to_action++;
												}
												$cs_global_counter_call_to_action++;
												
												
											}
										
										// Common Elements end
										
										// Media Elements Shortcode
										else if ( $_POST['cs_orderby'][$cs_counter] == "slider" ) {
												$shortcode  = '';
												$cs_slider 	= $column->addChild('slider');
												$cs_slider->addChild('page_element_size', $_POST['slider_element_size'][$cs_global_counter_slider] );
												$cs_slider->addChild('slider_element_size', $_POST['slider_element_size'][$cs_global_counter_slider] );

												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['slider'][$cs_shortcode_counter_slider]);
													$cs_shortcode_counter_slider++;
													$cs_slider->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													$shortcode .= '[cs_slider ';
													if(isset($_POST['cs_slider_header_title'][$cs_counter_slider]) && trim($_POST['cs_slider_header_title'][$cs_counter_slider]) <> ''){
														$shortcode .= 	'cs_slider_header_title="'.htmlspecialchars($_POST['cs_slider_header_title'][$cs_counter_slider], ENT_QUOTES).'" ';
													}
 													if(isset($_POST['cs_slider'][$cs_counter_slider]) && trim($_POST['cs_slider'][$cs_counter_slider]) <> ''){
														$shortcode .= 	'cs_slider="'.htmlspecialchars($_POST['cs_slider'][$cs_counter_slider]).'" ';
													}
													if(isset($_POST['cs_slider_id'][$cs_counter_slider]) && trim($_POST['cs_slider_id'][$cs_counter_slider]) <> ''){
														$shortcode .= 	'cs_slider_id="'.htmlspecialchars($_POST['cs_slider_id'][$cs_counter_slider]).'" ';
													}
													 
													$shortcode .= 	']';
													$cs_slider->addChild('cs_shortcode', $shortcode );
													$cs_counter_slider++;
												}
												$cs_global_counter_slider++;
											} 
										else if ( $_POST['cs_orderby'][$cs_counter] == "promobox" ) {
												$shortcode  = '';
												$cs_promobox = $column->addChild('promobox');
 												$cs_promobox->addChild('page_element_size', htmlspecialchars($_POST['promobox_element_size'][$cs_global_counter_promobox]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['promobox'][$cs_shortcode_counter_promobox]);
													$cs_shortcode_counter_promobox++;
													$cs_promobox->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													$shortcode .= '[cs_promobox ';
													if(isset($_POST['cs_promobox_section_title'][$cs_counter_promobox]) && trim($_POST['cs_promobox_section_title'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promobox_section_title="'.htmlspecialchars($_POST['cs_promobox_section_title'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_promo_style'][$cs_counter_promobox]) && trim($_POST['cs_promo_style'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promo_style="'.htmlspecialchars($_POST['cs_promo_style'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_promobox_bg_color'][$cs_counter_promobox]) && trim($_POST['cs_promobox_bg_color'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promobox_bg_color="'.htmlspecialchars($_POST['cs_promobox_bg_color'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_promo_icon'][$cs_counter_promobox]) && trim($_POST['cs_promo_icon'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promo_icon="'.htmlspecialchars($_POST['cs_promo_icon'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_promo_image_align'][$cs_counter_promobox]) && trim($_POST['cs_promo_image_align'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promo_image_align="'.htmlspecialchars($_POST['cs_promo_image_align'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_promo_image'][$cs_counter_promobox]) && trim($_POST['cs_promo_image'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promo_image="'.htmlspecialchars($_POST['cs_promo_image'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													
													if(isset($_POST['cs_promobox_title'][$cs_counter_promobox]) && trim($_POST['cs_promobox_title'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promobox_title="'.htmlspecialchars($_POST['cs_promobox_title'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_promobox_contents'][$cs_counter_promobox]) && trim($_POST['cs_promobox_contents'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promobox_contents="'.htmlspecialchars($_POST['cs_promobox_contents'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_link'][$cs_counter_promobox]) && trim($_POST['cs_link'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_link="'.htmlspecialchars($_POST['cs_link'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_promobox_title_color'][$cs_counter_promobox]) && trim($_POST['cs_promobox_title_color'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promobox_title_color="'.htmlspecialchars($_POST['cs_promobox_title_color'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_promobox_content_color'][$cs_counter_promobox]) && trim($_POST['cs_promobox_content_color'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promobox_content_color="'.htmlspecialchars($_POST['cs_promobox_content_color'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_promobox_btn_bg_color'][$cs_counter_promobox]) && trim($_POST['cs_promobox_btn_bg_color'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_promobox_btn_bg_color="'.htmlspecialchars($_POST['cs_promobox_btn_bg_color'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_link_title'][$cs_counter_promobox]) && trim($_POST['cs_link_title'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_link_title="'.htmlspecialchars($_POST['cs_link_title'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
												
													if(isset($_POST['cs_text_align'][$cs_counter_promobox]) && trim($_POST['cs_text_align'][$cs_counter_promobox]) <> ''){
														$shortcode .= 	'cs_text_align="'.htmlspecialchars($_POST['cs_text_align'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_promobox_class'][$cs_counter_promobox]) && $_POST['cs_promobox_class'][$cs_counter_promobox] != ''){
														$shortcode .= 	'cs_promobox_class="'.htmlspecialchars($_POST['cs_promobox_class'][$cs_counter_promobox], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_promobox_animation'][$cs_counter_promobox]) && $_POST['cs_promobox_animation'][$cs_counter_promobox] != ''){
														$shortcode .= 	'cs_promobox_animation="'.htmlspecialchars($_POST['cs_promobox_animation'][$cs_counter_promobox]).'" ';
													}
													$shortcode .= 	']';
													if(isset($_POST['cs_promobox_contents'][$cs_counter_promobox]) && $_POST['cs_promobox_contents'][$cs_counter_promobox] != ''){
														$shortcode .= 	htmlspecialchars($_POST['cs_promobox_contents'][$cs_counter_promobox], ENT_QUOTES);
													}
													$shortcode .= 	'[/cs_promobox]';				 
													$cs_promobox->addChild('cs_shortcode', $shortcode );
													$cs_counter_promobox++;
												}
											$cs_global_counter_promobox++;
										}
										else if ( $_POST['cs_orderby'][$cs_counter] == "team" ) {
													$shortcode = '';
													$team = $column->addChild('team');
													$team->addChild('page_element_size', htmlspecialchars($_POST['team_element_size'][$cs_counter_team]) );
													$team->addChild('team_element_size', htmlspecialchars($_POST['team_element_size'][$cs_counter_team]) );
													$team->addChild('cs_size', htmlspecialchars($_POST['cs_size'][$cs_counter_team]) );
													$team->addChild('cs_image_position', $_POST['cs_image_position'][$cs_counter_team] );
													$team->addChild('cs_text_align', $_POST['cs_text_align'][$cs_counter_team] );
													$team->addChild('cs_attached_media', $_POST['cs_attached_media'][$cs_counter_team] );
													$team->addChild('cs_team_website', $_POST['cs_team_website'][$cs_counter_team] );
													$team->addChild('cs_team_title', $_POST['cs_team_title'][$cs_counter_team] );
													$team->addChild('cs_team_designation', $_POST['cs_team_designation'][$cs_counter_team] );
													$team->addChild('cs_team_about', $_POST['cs_team_about'][$cs_counter_team] );
													$team->addChild('cs_team_fb', htmlspecialchars($_POST['cs_team_fb'][$cs_counter_team]) );
													$team->addChild('cs_team_tw', $_POST['cs_team_tw'][$cs_counter_team] );
													$team->addChild('cs_team_gm', $_POST['cs_team_gm'][$cs_counter_team] );
													$team->addChild('cs_team_yt', $_POST['cs_team_yt'][$cs_counter_team] );
													$team->addChild('cs_team_sky', $_POST['cs_team_sky'][$cs_counter_team] );
													$team->addChild('cs_team_fs', $_POST['cs_team_fs'][$cs_counter_team] );
													$team->addChild('cs_button_target', $_POST['cs_button_target'][$cs_counter_team] );
													$team->addChild('cs_team_class', htmlspecialchars($_POST['cs_team_class'][$cs_counter_team]) );
													$team->addChild('cs_team_animation', htmlspecialchars($_POST['cs_team_animation'][$cs_counter_team]) );
												$shortcode .= '[cs_team ';
												if(isset($_POST['cs_size'][$cs_counter_team]) && trim($_POST['cs_size'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_size="'.htmlspecialchars($_POST['cs_size'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_image_position'][$cs_counter_team]) && trim($_POST['cs_image_position'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_image_position="'.htmlspecialchars($_POST['cs_image_position'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_text_align'][$cs_counter_team]) && trim($_POST['cs_text_align'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_text_align="'.htmlspecialchars($_POST['cs_text_align'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_attached_media'][$cs_counter_team]) && trim($_POST['cs_attached_media'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_attached_media="'.htmlspecialchars($_POST['cs_attached_media'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_team_website'][$cs_counter_team]) && trim($_POST['cs_team_website'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_team_website="'.htmlspecialchars($_POST['cs_team_website'][$cs_counter_team]).'" ';
												}
												
												if(isset($_POST['cs_team_title'][$cs_counter_team]) && trim($_POST['cs_team_title'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_team_title="'.htmlspecialchars($_POST['cs_team_title'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_team_designation'][$cs_counter_team]) && trim($_POST['cs_team_designation'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_team_designation="'.htmlspecialchars($_POST['cs_team_designation'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_team_about'][$cs_counter_team]) && trim($_POST['cs_team_about'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_team_about="'.htmlspecialchars($_POST['cs_team_about'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_team_fb'][$cs_counter_team]) && trim($_POST['cs_team_fb'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_team_fb="'.htmlspecialchars($_POST['cs_team_fb'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_team_tw'][$cs_counter_team]) && trim($_POST['cs_team_tw'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_team_tw="'.htmlspecialchars($_POST['cs_team_tw'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_team_gm'][$cs_counter_team]) && trim($_POST['cs_team_gm'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_team_gm="'.htmlspecialchars($_POST['cs_team_gm'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_team_yt'][$cs_counter_team]) && trim($_POST['cs_team_yt'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_team_yt="'.htmlspecialchars($_POST['cs_team_yt'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_team_sky'][$cs_counter_team]) && trim($_POST['cs_team_sky'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_team_sky="'.htmlspecialchars($_POST['cs_team_sky'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_team_fs'][$cs_counter_team]) && trim($_POST['cs_team_fs'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_team_fs="'.htmlspecialchars($_POST['cs_team_fs'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_button_target'][$cs_counter_team]) && trim($_POST['cs_button_target'][$cs_counter_team]) <> ''){
													$shortcode .= 	'cs_button_target="'.htmlspecialchars($_POST['cs_button_target'][$cs_counter_team]).'" ';
												}
												
												if(isset($_POST['cs_team_class'][$cs_counter_team]) && $_POST['cs_team_class'][$cs_counter_team] != ''){
													$shortcode .= 	'cs_team_class="'.htmlspecialchars($_POST['cs_team_class'][$cs_counter_team]).'" ';
												}
												if(isset($_POST['cs_team_animation'][$cs_counter_team]) && $_POST['cs_team_animation'][$cs_counter_team] != ''){
													$shortcode .= 	'cs_cs_team_animation="'.htmlspecialchars($_POST['cs_team_animation'][$cs_counter_team]).'" ';
												}
												$shortcode .= 	']';
													$team->addChild('cs_shortcode', $shortcode );
													$cs_counter_team++;
											}
											else if ( $_POST['cs_orderby'][$cs_counter] == "offerslider" ) {
												$shortcode = $shortcode_item = '';
  												$cs_offerslider = $column->addChild('offerslider');
												$cs_offerslider->addChild('page_element_size', htmlspecialchars($_POST['offerslider_element_size'][$cs_global_counter_offerslider]) );
												$cs_offerslider->addChild('offerslider_element_size', htmlspecialchars($_POST['offerslider_element_size'][$cs_global_counter_offerslider]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['offerslider'][$cs_shortcode_counter_offerslider]);
													$cs_shortcode_counter_offerslider++;
													$cs_offerslider->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													
													if(isset($_POST['offerslider_num'][$cs_counter_offerslider]) && $_POST['offerslider_num'][$cs_counter_offerslider]>0){			
														for ( $i = 1; $i <= $_POST['offerslider_num'][$cs_counter_offerslider]; $i++ ){
															
															$shortcode_item .= '[offer_item ';
															
															if(isset($_POST['cs_slider_image'][$cs_counter_offerslider_node]) && trim($_POST['cs_slider_image'][$cs_counter_offerslider]) <> ''){
																$shortcode_item .= 	'cs_slider_image="'.htmlspecialchars($_POST['cs_slider_image'][$cs_counter_offerslider_node]).'" ';
															}
															if(isset($_POST['cs_slider_title'][$cs_counter_offerslider_node]) && trim($_POST['cs_slider_title'][$cs_counter_offerslider]) <> ''){
																$shortcode_item .= 	'cs_slider_title="'.htmlspecialchars($_POST['cs_slider_title'][$cs_counter_offerslider_node], ENT_QUOTES).'" ';
															}
															if(isset($_POST['cs_readmore_link'][$cs_counter_offerslider_node]) && trim($_POST['cs_readmore_link'][$cs_counter_offerslider]) <> ''){
																$shortcode_item .= 	'cs_readmore_link="'.htmlspecialchars($_POST['cs_readmore_link'][$cs_counter_offerslider_node], ENT_QUOTES).'" ';
															}
															if(isset($_POST['cs_offerslider_link_title'][$cs_counter_offerslider_node]) && trim($_POST['cs_offerslider_link_title'][$cs_counter_offerslider_node]) <> ''){
																$shortcode_item .= 	'cs_offerslider_link_title="'.htmlspecialchars($_POST['cs_offerslider_link_title'][$cs_counter_offerslider_node], ENT_QUOTES).'" ';
															}
															
															$shortcode_item .= 	']';
															if(isset($_POST['cs_slider_contents'][$cs_counter_offerslider]) && $_POST['cs_slider_contents'][$cs_counter_offerslider] != ''){
																$shortcode_item .= 	htmlspecialchars($_POST['cs_slider_contents'][$cs_counter_offerslider_node], ENT_QUOTES);
															}
															$shortcode_item .= 	'[/offer_item]'; 
															$cs_counter_offerslider_node++;
														}
													}
													
													$cs_section_title = '';
													if(isset($_POST['cs_offerslider_section_title'][$cs_counter_offerslider]) && trim($_POST['cs_offerslider_section_title'][$cs_counter_offerslider]) <> ''){
														$cs_section_title  = 	'cs_offerslider_section_title="'.htmlspecialchars($_POST['cs_offerslider_section_title'][$cs_counter_offerslider], ENT_QUOTES).'" ';
													}
													
													$shortcode = '[cs_offerslider cs_offerslider_class="'.htmlspecialchars($_POST['cs_offerslider_class'][$cs_counter_testimonials]).'"  cs_offerslider_animation="'.htmlspecialchars($_POST['cs_offerslider_animation'][$cs_counter_testimonials]).'"  '.$cs_section_title.' ]'.$shortcode_item.'[/cs_offerslider]';
													$cs_offerslider->addChild('cs_shortcode', $shortcode );
								
													$cs_counter_offerslider++;
												}
												$cs_global_counter_offerslider++;
										  }
										else if ( $_POST['cs_orderby'][$cs_counter] == "members" ) {
												$shortcode  = '';
												$members 	= $column->addChild('members');
												$members->addChild('page_element_size', htmlspecialchars($_POST['members_element_size'][$cs_global_counter_members]) );
												$members->addChild('members_element_size', htmlspecialchars($_POST['members_element_size'][$cs_global_counter_members]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['members'][$cs_shortcode_counter_members]);
													$cs_shortcode_counter_members++;
													$cs_output = array();
													$CS_PREFIX = 'cs_members';
													$cs_parseObject = new ShortcodeParse();
													$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
													$cs_defaults = array('var_pb_members_title' => '','var_pb_members_profile_inks'=>'','var_pb_members_description'=>'','var_pb_members_roles'=>'','var_pb_members_filterable'=>'','var_pb_members_pagination'=>'','var_pb_members_all_tab'=>'', 'var_pb_members_per_page'=>get_option("posts_per_page"),'var_pb_member_view'=>'','cs_members_class' => '','cs_members_animation' => '');
													if(isset($cs_output['0']['atts']))
														$cs_atts = $cs_output['0']['atts'];
													else 
														$cs_atts = array();
													foreach($cs_defaults as $key=>$values){
														if(isset($cs_atts[$key]))
															$$key = $cs_atts[$key];
														else 
															$$key =$values;
													 }
													$members->addChild('var_pb_members_title', htmlspecialchars($var_pb_members_title, ENT_QUOTES) );
													$members->addChild('var_pb_member_view', htmlspecialchars($var_pb_member_view) );
													$members->addChild('var_pb_members_roles', htmlspecialchars($var_pb_members_roles));
													$members->addChild('var_pb_members_filterable', htmlspecialchars($var_pb_members_filterable) );
													$members->addChild('var_pb_members_all_tab', htmlspecialchars($var_pb_members_all_tab) );
													$members->addChild('var_pb_members_profile_inks', htmlspecialchars($var_pb_members_profile_inks) );
													$members->addChild('var_pb_members_description', htmlspecialchars($var_pb_members_description) );
													$members->addChild('var_pb_members_pagination', htmlspecialchars($var_pb_members_pagination) );
													$members->addChild('var_pb_members_per_page', htmlspecialchars($var_pb_members_per_page, ENT_QUOTES) );
													$members->addChild('cs_members_class', htmlspecialchars($cs_members_class, ENT_QUOTES) );
													$members->addChild('cs_members_animation', htmlspecialchars($cs_members_animation) );
													$members->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													if (isset($_POST['cs_members_counter'][$cs_counter_members])){
														 $cs_members_counter = htmlspecialchars($_POST['cs_members_counter'][$cs_counter_members]);
												 	}
													$members->addChild('var_pb_members_title', htmlspecialchars($_POST['var_pb_members_title'][$cs_counter_members], ENT_QUOTES) );
													$members->addChild('var_pb_member_view', htmlspecialchars($_POST['var_pb_member_view'][$cs_counter_members]) );
													if (empty($_POST['var_pb_members_roles'][$cs_members_counter])){
														 $var_pb_members_roles = "";
												 	} else {
														$var_pb_members_roles = implode(",", $_POST['var_pb_members_roles'][$cs_members_counter]);
													}
													$members->addChild('var_pb_members_roles', htmlspecialchars($var_pb_members_roles));
													$members->addChild('var_pb_members_filterable', htmlspecialchars($_POST['var_pb_members_filterable'][$cs_counter_members] ));
													$members->addChild('var_pb_members_all_tab', htmlspecialchars($_POST['var_pb_members_all_tab'][$cs_counter_members]) );
													$members->addChild('var_pb_members_profile_inks', htmlspecialchars($_POST['var_pb_members_profile_inks'][$cs_counter_members]) );
													$members->addChild('var_pb_members_description', htmlspecialchars($_POST['var_pb_members_description'][$cs_counter_members]) );
													$members->addChild('var_pb_members_pagination', htmlspecialchars($_POST['var_pb_members_pagination'][$cs_counter_members]) );
													$members->addChild('var_pb_members_per_page', htmlspecialchars($_POST['var_pb_members_per_page'][$cs_counter_members] ));
													$members->addChild('cs_members_class', htmlspecialchars($_POST['cs_members_class'][$cs_counter_members], ENT_QUOTES) );
													$members->addChild('cs_members_animation', htmlspecialchars($_POST['cs_members_animation'][$cs_counter_members]) );
													$shortcode .= '[cs_members ';
													if(isset($_POST['var_pb_members_title'][$cs_counter_members]) && trim($_POST['var_pb_members_title'][$cs_counter_members]) <> ''){
														$shortcode .= 	'var_pb_members_title="'.htmlspecialchars($_POST['var_pb_members_title'][$cs_counter_members], ENT_QUOTES).'" ';
													}
													if(isset($_POST['var_pb_member_view'][$cs_counter_members]) && trim($_POST['var_pb_member_view'][$cs_counter_members]) <> ''){
														$shortcode .= 	'var_pb_member_view="'.htmlspecialchars($_POST['var_pb_member_view'][$cs_counter_members]).'" ';
													}
													if(isset($var_pb_members_roles) && trim($var_pb_members_roles) <> ''){
														$shortcode .= 	'var_pb_members_roles="'.htmlspecialchars($var_pb_members_roles).'" ';
													}
													if(isset($_POST['var_pb_members_filterable'][$cs_counter_members]) && trim($_POST['var_pb_members_filterable'][$cs_counter_members]) <> ''){
														$shortcode .= 	'var_pb_members_filterable="'.htmlspecialchars($_POST['var_pb_members_filterable'][$cs_counter_members]).'" ';
													}
													if(isset($_POST['var_pb_members_all_tab'][$cs_counter_members]) && trim($_POST['var_pb_members_all_tab'][$cs_counter_members]) <> ''){
														$shortcode .= 	'var_pb_members_all_tab="'.htmlspecialchars($_POST['var_pb_members_all_tab'][$cs_counter_members]).'" ';
													}
													if(isset($_POST['var_pb_members_profile_inks'][$cs_counter_members]) && trim($_POST['var_pb_members_profile_inks'][$cs_counter_members]) <> ''){
														$shortcode .= 	'var_pb_members_profile_inks="'.htmlspecialchars($_POST['var_pb_members_profile_inks'][$cs_counter_members]).'" ';
													}
													if(isset($_POST['var_pb_members_description'][$cs_counter_members]) && trim($_POST['var_pb_members_description'][$cs_counter_members]) <> ''){
														$shortcode .= 	'var_pb_members_description="'.htmlspecialchars($_POST['var_pb_members_description'][$cs_counter_members]).'" ';
													}
													if(isset($_POST['var_pb_members_pagination'][$cs_counter_members]) && trim($_POST['var_pb_members_pagination'][$cs_counter_members]) <> ''){
														$shortcode .= 	'var_pb_members_pagination="'.htmlspecialchars($_POST['var_pb_members_pagination'][$cs_counter_members]).'" ';
													}
													if(isset($_POST['var_pb_members_per_page'][$cs_counter_members]) && trim($_POST['var_pb_members_per_page'][$cs_counter_members]) <> ''){
														$shortcode .= 	'var_pb_members_per_page="'.htmlspecialchars($_POST['var_pb_members_per_page'][$cs_counter_members]).'" ';
													}
													if(isset($_POST['cs_members_class'][$cs_counter_members]) && $_POST['cs_members_class'][$cs_counter_members] != ''){
														$shortcode .= 	'cs_members_class="'.htmlspecialchars($_POST['cs_members_class'][$cs_counter_members], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_members_animation'][$cs_counter_members]) && $_POST['cs_members_animation'][$cs_counter_members] != ''){
														$shortcode .= 	'cs_members_animation="'.htmlspecialchars($_POST['cs_members_animation'][$cs_counter_members]).'" ';
													}
													$shortcode .= 	']';
													$members->addChild('cs_shortcode', $shortcode );

													$cs_counter_members++;
												}
												$cs_global_counter_members++;
											}
											else if ( $_POST['cs_orderby'][$cs_counter] == "video" ) {
												$shortcode = '';
												$cs_video = $column->addChild('video');
 												$cs_video->addChild('page_element_size', htmlspecialchars($_POST['video_element_size'][$cs_global_counter_video]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['video'][$cs_shortcode_counter_video]);
													$cs_shortcode_counter_video++;
													$cs_video->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													$shortcode = '[cs_video ';
													if(isset($_POST['cs_video_section_title'][$cs_counter_video]) && $_POST['cs_video_section_title'][$cs_counter_video] != ''){
														$shortcode .= 	'cs_video_section_title="'.htmlspecialchars($_POST['cs_video_section_title'][$cs_counter_video], ENT_QUOTES).'" ';
													}if(isset($_POST['cs_video_url'][$cs_counter_video]) && $_POST['cs_video_url'][$cs_counter_video] != ''){
														$shortcode .= 	'cs_video_url="'.htmlspecialchars($_POST['cs_video_url'][$cs_counter_video], ENT_QUOTES).'" ';
													}if(isset($_POST['cs_video_width'][$cs_counter_video]) && $_POST['cs_video_width'][$cs_counter_video] != ''){
														$shortcode .= 	'cs_video_width="'.htmlspecialchars($_POST['cs_video_width'][$cs_counter_video]).'" ';
													}if(isset($_POST['cs_video_height'][$cs_counter_video]) && $_POST['cs_video_height'][$cs_counter_video] != ''){
														$shortcode .= 	'cs_video_height="'.htmlspecialchars($_POST['cs_video_height'][$cs_counter_video]).'" ';
													}if(isset($_POST['cs_video_custom_class'][$cs_counter_video]) && $_POST['cs_video_custom_class'][$cs_counter_video] != ''){
														$shortcode .= 	'cs_video_custom_class="'.htmlspecialchars($_POST['cs_video_custom_class'][$cs_counter_video], ENT_QUOTES).'" ';
													}
													$shortcode .= 	']';
												$cs_video->addChild('cs_shortcode', $shortcode );
												$cs_counter_video++;
												}
												$cs_global_counter_video++;
											}
											else if ( $_POST['cs_orderby'][$cs_counter] == "audio" ) {
												$shortcode = $shortcode_item = '';
												$section_title = '';
												$audio = $column->addChild('audio');
 												$audio->addChild('page_element_size', htmlspecialchars($_POST['audio_element_size'][$cs_global_counter_audio]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['audio'][$cs_shortcode_counter_audio]);
													$cs_shortcode_counter_audio++;
													$audio->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													if(isset($_POST['album_num'][$cs_counter_audio]) && $_POST['album_num'][$cs_counter_audio]>0){
														for ( $i = 1; $i <= $_POST['album_num'][$cs_counter_audio]; $i++ ){
															
															$shortcode_item .= '[album_item ';
															if(isset($_POST['cs_album_track_title'][$cs_counter_audio_node]) && $_POST['cs_album_track_title'][$cs_counter_audio_node] != ''){
																$shortcode_item .= 	'cs_album_track_title="'.htmlspecialchars($_POST['cs_album_track_title'][$cs_counter_audio_node]).'" ';
															}if(isset($_POST['cs_album_speaker'][$cs_counter_audio_node]) && $_POST['cs_album_speaker'][$cs_counter_audio_node] != ''){
																$shortcode_item .= 	'cs_album_speaker="'.htmlspecialchars($_POST['cs_album_speaker'][$cs_counter_audio_node]).'" ';
															}
															if(isset($_POST['cs_album_track_mp3_url'][$cs_counter_audio_node]) && $_POST['cs_album_track_mp3_url'][$cs_counter_audio_node] != ''){
																$shortcode_item .= 	'cs_album_track_mp3_url="'.htmlspecialchars($_POST['cs_album_track_mp3_url'][$cs_counter_audio_node]).'" ';
															}
															if(isset($_POST['cs_album_track_buy_mp3'][$cs_counter_audio_node]) && $_POST['cs_album_track_buy_mp3'][$cs_counter_audio_node] != ''){
																$shortcode_item .= 	'cs_album_track_buy_mp3="'.htmlspecialchars($_POST['cs_album_track_buy_mp3'][$cs_counter_audio_node]).'" ';
															}
															$shortcode_item .= 	']';
															$cs_counter_audio_node++;
														}
													}
													if(isset($_POST['cs_audio_section_title'][$cs_counter_audio]) && $_POST['cs_audio_section_title'][$cs_counter_audio] != ''){
														$section_title = 	'cs_audio_section_title="'.htmlspecialchars($_POST['cs_audio_section_title'][$cs_counter_audio], ENT_QUOTES).'" ';
													}
													$shortcode = '[cs_album 
													  '.$section_title.' 
													 cs_audio_section_title="'.htmlspecialchars($_POST['cs_audio_section_title'][$cs_counter_audio], ENT_QUOTES).'"   cs_audio_class="'.htmlspecialchars($_POST['cs_audio_class'][$cs_counter_audio], ENT_QUOTES).'"   cs_audio_animation="'.htmlspecialchars($_POST['cs_audio_animation'][$cs_counter_audio]).'"  ]'.$shortcode_item.'[/cs_album]';
													$audio->addChild('cs_shortcode', $shortcode );
													$cs_counter_audio++;
												}
												$cs_global_counter_audio++;
											}
										else if ( $_POST['cs_orderby'][$cs_counter] == "map" ) {
													$shortcode  =  '';
 													$cs_map = $column->addChild('map');
 													$cs_map->addChild('page_element_size', htmlspecialchars( $_POST['map_element_size'][$cs_global_counter_map] ));
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['map'][$cs_shortcode_counter_map]);
													$cs_shortcode_counter_map++;
													$cs_map->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
 													$shortcode = '[cs_map ';
													if(isset($_POST['cs_map_section_title'][$cs_counter_map]) && $_POST['cs_map_section_title'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_section_title="'.htmlspecialchars($_POST['cs_map_section_title'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_title'][$cs_counter_map]) && $_POST['cs_map_title'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_title="'.htmlspecialchars($_POST['cs_map_title'][$cs_counter_map], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_map_height'][$cs_counter_map]) && $_POST['cs_map_height'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_height="'.htmlspecialchars($_POST['cs_map_height'][$cs_counter_map], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_map_lat'][$cs_counter_map]) && $_POST['cs_map_lat'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_lat="'.htmlspecialchars($_POST['cs_map_lat'][$cs_counter_map], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_map_lon'][$cs_counter_map]) && $_POST['cs_map_lon'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_lon="'.htmlspecialchars($_POST['cs_map_lon'][$cs_counter_map], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_map_zoom'][$cs_counter_map]) && $_POST['cs_map_zoom'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_zoom="'.htmlspecialchars($_POST['cs_map_zoom'][$cs_counter_map], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_map_type'][$cs_counter_map]) && $_POST['cs_map_type'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_type="'.htmlspecialchars($_POST['cs_map_type'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_info'][$cs_counter_map]) && $_POST['cs_map_info'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_info="'.htmlspecialchars($_POST['cs_map_info'][$cs_counter_map], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_map_info_width'][$cs_counter_map]) && $_POST['cs_map_info_width'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_info_width="'.htmlspecialchars($_POST['cs_map_info_width'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_info_height'][$cs_counter_map]) && $_POST['cs_map_info_height'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_info_height="'.htmlspecialchars($_POST['cs_map_info_height'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_marker_icon'][$cs_counter_map]) && $_POST['cs_map_marker_icon'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_marker_icon="'.htmlspecialchars($_POST['cs_map_marker_icon'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_show_marker'][$cs_counter_map]) && $_POST['cs_map_show_marker'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_show_marker="'.htmlspecialchars($_POST['cs_map_show_marker'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_controls'][$cs_counter_map]) && $_POST['cs_map_controls'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_controls="'.htmlspecialchars($_POST['cs_map_controls'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_draggable'][$cs_counter_map]) && $_POST['cs_map_draggable'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_draggable="'.htmlspecialchars($_POST['cs_map_draggable'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_scrollwheel'][$cs_counter_map]) && $_POST['cs_map_scrollwheel'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_scrollwheel="'.htmlspecialchars($_POST['cs_map_scrollwheel'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['map_view'][$cs_counter_map]) && $_POST['map_view'][$cs_counter_map] != ''){
														$shortcode .= 	'map_view="'.htmlspecialchars($_POST['map_view'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_border'][$cs_counter_map]) && $_POST['cs_map_border'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_border="'.htmlspecialchars($_POST['cs_map_border'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_border_color'][$cs_counter_map]) && $_POST['cs_map_border_color'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_border_color="'.htmlspecialchars($_POST['cs_map_border_color'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_color'][$cs_counter_map]) && $_POST['cs_map_color'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_color="'.htmlspecialchars($_POST['cs_map_color'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_conactus_content'][$cs_counter_map]) && $_POST['cs_map_conactus_content'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_conactus_content="'.htmlspecialchars($_POST['cs_map_conactus_content'][$cs_counter_map], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_map_border'][$cs_counter_map]) && $_POST['cs_map_border'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_border="'.htmlspecialchars($_POST['cs_map_border'][$cs_counter_map]).'" ';
													}
													if(isset($_POST['cs_map_class'][$cs_counter_map]) && $_POST['cs_map_class'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_class="'.htmlspecialchars($_POST['cs_map_class'][$cs_counter_map], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_map_animation'][$cs_counter_map]) && $_POST['cs_map_animation'][$cs_counter_map] != ''){
														$shortcode .= 	'cs_map_animation="'.htmlspecialchars($_POST['cs_map_animation'][$cs_counter_map]).'" ';
													}
													$shortcode .= 	']';
													$cs_map->addChild('cs_shortcode', $shortcode );
												$cs_counter_map++;
												}
												$cs_global_counter_map++;
										}
										else if ( $_POST['cs_orderby'][$cs_counter] == "infobox" ) {
											$shortcode = $shortcode_item = '';
											$cs_infobox   = $column->addChild('infobox');
											$cs_infobox->addChild('page_element_size', htmlspecialchars($_POST['infobox_element_size'][$cs_counter_infobox]) );
											$cs_infobox->addChild('infobox_element_size', htmlspecialchars($_POST['infobox_element_size'][$cs_counter_infobox]) );
											if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
												$cs_shortcode_str = stripslashes ($_POST['shortcode']['infobox'][$cs_shortcode_counter_infobox]);
												$cs_shortcode_counter_infobox++;
												$cs_infobox->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
											} else {
												if(isset($_POST['info_list_num'][$cs_counter_infobox]) && $_POST['info_list_num'][$cs_counter_infobox]>0){	
													for ( $i = 1; $i <= $_POST['info_list_num'][$cs_counter_infobox]; $i++ ){
														$shortcode_item .= '[infobox_item ';
														if(isset($_POST['cs_infobox_list_icon'][$cs_counter_infobox_node]) && $_POST['cs_infobox_list_icon'][$cs_counter_infobox_node] != ''){
															$shortcode_item .= 	'cs_infobox_list_icon="'.htmlspecialchars($_POST['cs_infobox_list_icon'][$cs_counter_infobox_node]).'" ';
														}
														if(isset($_POST['cs_infobox_list_color'][$cs_counter_infobox_node]) && $_POST['cs_infobox_list_color'][$cs_counter_infobox_node] != ''){
															$shortcode_item .= 	'cs_infobox_list_color="'.htmlspecialchars($_POST['cs_infobox_list_color'][$cs_counter_infobox_node]).'" ';
														}
														if(isset($_POST['cs_infobox_list_title'][$cs_counter_infobox_node]) && $_POST['cs_infobox_list_title'][$cs_counter_infobox_node] != ''){
															$shortcode_item .= 	'cs_infobox_list_title="'.htmlspecialchars($_POST['cs_infobox_list_title'][$cs_counter_infobox_node], ENT_QUOTES).'" ';
														}
														$shortcode_item .= 	']';
														if(isset($_POST['cs_infobox_list_description'][$cs_counter_infobox_node]) && $_POST['cs_infobox_list_description'][$cs_counter_infobox_node] != ''){
															$shortcode_item .= 	htmlspecialchars($_POST['cs_infobox_list_description'][$cs_counter_infobox_node], ENT_QUOTES);
														}
														$shortcode_item .= 	'[/infobox_item]';
														$cs_counter_infobox_node++;
													}
												}
												$shortcode .= '[cs_infobox ';
												if(isset($_POST['cs_infobox_section_title'][$cs_counter_infobox]) && trim($_POST['cs_infobox_section_title'][$cs_counter_infobox]) <> ''){
													$shortcode .= 	'cs_infobox_section_title="'.htmlspecialchars($_POST['cs_infobox_section_title'][$cs_counter_infobox], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_infobox_title'][$cs_counter_infobox]) && trim($_POST['cs_infobox_title'][$cs_counter_infobox]) <> ''){
													$shortcode .= 	'cs_infobox_title="'.htmlspecialchars($_POST['cs_infobox_title'][$cs_counter_infobox], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_infobox_bg_color'][$cs_counter_infobox]) && trim($_POST['cs_infobox_bg_color'][$cs_counter_infobox]) <> ''){
													$shortcode .= 	'cs_infobox_bg_color="'.htmlspecialchars($_POST['cs_infobox_bg_color'][$cs_counter_infobox]).'" ';
												}
												
												if(isset($_POST['cs_infobox_list_text_color'][$cs_counter_infobox]) && trim($_POST['cs_infobox_list_text_color'][$cs_counter_infobox]) <> ''){
													$shortcode .= 	'cs_infobox_list_text_color="'.htmlspecialchars($_POST['cs_infobox_list_text_color'][$cs_counter_infobox]).'" ';
												}
												if(isset($_POST['cs_infobox_class'][$cs_counter_infobox]) && trim($_POST['cs_infobox_class'][$cs_counter_infobox]) <> ''){
													$shortcode .= 	'cs_infobox_class="'.htmlspecialchars($_POST['cs_infobox_class'][$cs_counter_infobox], ENT_QUOTES).'" ';
												}
												if(isset($_POST['cs_infobox_animation'][$cs_counter_infobox]) && trim($_POST['cs_infobox_animation'][$cs_counter_infobox]) <> ''){
													$shortcode .= 	'cs_infobox_animation="'.htmlspecialchars($_POST['cs_infobox_animation'][$cs_counter_infobox]).'" ';
												}
												$shortcode .= 	']'.$shortcode_item.'[/cs_infobox]';
												$cs_infobox->addChild('cs_shortcode', $shortcode );
												$cs_counter_infobox++;
											}
											$cs_global_counter_infobox++;
													
										} 
										elseif ( $_POST['cs_orderby'][$cs_counter] == "icons" ) {
												$shortcode  = '';
												$cs_icons 	= $column->addChild('icons');
												$cs_icons->addChild('page_element_size', htmlspecialchars($_POST['icons_element_size'][$cs_global_counter_icons]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
													$cs_shortcode_str = stripslashes ($_POST['shortcode']['icons'][$cs_shortcode_counter_icons]);
													$cs_shortcode_counter_icons++;
													$cs_icons->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													$shortcode = '[cs_icons ';
													if(isset($_POST['cs_font_type'][$cs_counter_icons]) && $_POST['cs_font_type'][$cs_counter_icons] != ''){
														$shortcode .= 	'cs_font_type="'.htmlspecialchars($_POST['cs_font_type'][$cs_counter_icons]).'" ';
													}if(isset($_POST['cs_font_size'][$cs_counter_icons]) && $_POST['cs_font_size'][$cs_counter_icons] != ''){
														$shortcode .= 	'cs_font_size="'.htmlspecialchars($_POST['cs_font_size'][$cs_counter_icons]).'" ';
													}if(isset($_POST['cs_icon_color'][$cs_counter_icons]) && $_POST['cs_icon_color'][$cs_counter_icons] != ''){
														$shortcode .= 	'cs_icon_color="'.htmlspecialchars($_POST['cs_icon_color'][$cs_counter_icons]).'" ';
													}if(isset($_POST['cs_icon_bg_color'][$cs_counter_icons]) && $_POST['cs_icon_bg_color'][$cs_counter_icons] != ''){
														$shortcode .= 	'cs_icon_bg_color="'.htmlspecialchars($_POST['cs_icon_bg_color'][$cs_counter_icons]).'" ';
													}if(isset($_POST['cs_font_icon'][$cs_counter_icons]) && $_POST['cs_font_icon'][$cs_counter_icons] != ''){
														$shortcode .= 	'cs_font_icon="'.htmlspecialchars($_POST['cs_font_icon'][$cs_counter_icons]).'" ';
													}if(isset($_POST['cs_icon_view'][$cs_counter_icons]) && $_POST['cs_icon_view'][$cs_counter_icons] != ''){
														$shortcode .= 	'cs_icon_view="'.htmlspecialchars($_POST['cs_icon_view'][$cs_counter_icons]).'" ';
													}if(isset($_POST['cs_icons_class'][$cs_counter_icons]) && $_POST['cs_icons_class'][$cs_counter_icons] != ''){
														$shortcode .= 	'cs_icons_class="'.htmlspecialchars($_POST['cs_icons_class'][$cs_counter_icons]).'" ';
													}if(isset($_POST['cs_icons_animation'][$cs_counter_icons]) && $_POST['cs_icons_animation'][$cs_counter_icons] != ''){
														$shortcode .= 	'cs_icons_animation="'.htmlspecialchars($_POST['cs_icons_animation'][$cs_counter_icons]).'" ';
													}
													$shortcode .= 	'[/cs_icons]';	
													$cs_icons->addChild('cs_shortcode', $shortcode );
												$cs_counter_icons++;
												}
												$cs_global_counter_icons++;
												
									}
										else if ( $_POST['cs_orderby'][$cs_counter] == "image" ) {
												$shortcode  = '';
												
												$cs_image = $column->addChild('image');
 												$cs_image->addChild('page_element_size', htmlspecialchars($_POST['image_element_size'][$cs_global_counter_image]) );
												if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
 													$cs_shortcode_str = stripslashes ($_POST['shortcode']['image'][$cs_shortcode_counter_image]);
													$cs_shortcode_counter_image++;
													$cs_image->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
												} else {
													$shortcode = '[cs_image ';
													if(isset($_POST['cs_image_section_title'][$cs_counter_image]) && $_POST['cs_image_section_title'][$cs_counter_image] != ''){
														$shortcode .= 	'cs_image_section_title="'.htmlspecialchars($_POST['cs_image_section_title'][$cs_counter_image], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_image_url'][$cs_counter_image]) && $_POST['cs_image_url'][$cs_counter_image] != ''){
														$shortcode .= 	'cs_image_url="'.htmlspecialchars($_POST['cs_image_url'][$cs_counter_image], ENT_QUOTES).'" ';
													}
													if(isset($_POST['cs_image_style'][$cs_counter_image]) && $_POST['cs_image_style'][$cs_counter_image] != ''){
														$shortcode .= 	'cs_image_style="'.htmlspecialchars($_POST['cs_image_style'][$cs_counter_image]).'" ';
													}
													if(isset($_POST['cs_image_title'][$cs_counter_image]) && $_POST['cs_image_title'][$cs_counter_image] != ''){
														$shortcode .= 	'cs_image_title="'.htmlspecialchars($_POST['cs_image_title'][$cs_counter_image], ENT_QUOTES).'" ';
													}if(isset($_POST['cs_custom_class'][$cs_counter_image]) && $_POST['cs_custom_class'][$cs_counter_image] != ''){
														$shortcode .= 	'cs_custom_class="'.htmlspecialchars($_POST['cs_custom_class'][$cs_counter_image], ENT_QUOTES).'" ';
													}if(isset($_POST['cs_custom_animation'][$cs_counter_image]) && $_POST['cs_custom_animation'][$cs_counter_image] != ''){
														$shortcode .= 	'cs_custom_animation="'.htmlspecialchars($_POST['cs_custom_animation'][$cs_counter_image]).'" ';
													}
													$shortcode .= 	']';	 
													if(isset($_POST['cs_image_caption'][$cs_counter_image]) && $_POST['cs_image_caption'][$cs_counter_image] != ''){
														$shortcode .= 	htmlspecialchars($_POST['cs_image_caption'][$cs_counter_image], ENT_QUOTES);
													}				 
													$shortcode .= 	'[/cs_image]';					 
													$cs_image->addChild('cs_shortcode', $shortcode );
													
													$cs_counter_image++;
												}
											$cs_global_counter_image++;
										}
										// Loops Short Code Start
										
										// Blog
										else if ( $_POST['cs_orderby'][$cs_counter] == "blog" ) {
													$shortcode = '';
													$blog = $column->addChild('blog');
													$blog->addChild('page_element_size', htmlspecialchars($_POST['blog_element_size'][$cs_global_counter_blog]) );
													$blog->addChild('blog_element_size', htmlspecialchars($_POST['blog_element_size'][$cs_global_counter_blog]) );
													if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
														$shortcode_str = stripslashes ($_POST['shortcode']['blog'][$cs_shortcode_counter_blog]);
														$cs_shortcode_counter_blog++;
														$blog->addChild('cs_shortcode', htmlspecialchars($shortcode_str) );
													} else {
														$shortcode = '[cs_blog ';
														if(isset($_POST['cs_blog_section_title'][$cs_counter_blog]) && $_POST['cs_blog_section_title'][$cs_counter_blog] != ''){
															$shortcode .= 	'cs_blog_section_title="'.htmlspecialchars($_POST['cs_blog_section_title'][$cs_counter_blog], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_blog_description'][$cs_counter_blog]) && $_POST['cs_blog_description'][$cs_counter_blog] != ''){
															$shortcode .= 	'cs_blog_description="'.htmlspecialchars($_POST['cs_blog_description'][$cs_counter_blog], ENT_QUOTES).'" ';
														}if(isset($_POST['cs_blog_cat'][$cs_counter_blog]) && $_POST['cs_blog_cat'][$cs_counter_blog] != ''){
															$shortcode .= 	'cs_blog_cat="'.htmlspecialchars($_POST['cs_blog_cat'][$cs_counter_blog]).'" ';
														}if(isset($_POST['cs_blog_view'][$cs_counter_blog]) && $_POST['cs_blog_view'][$cs_counter_blog] != ''){
															$shortcode .= 	'cs_blog_view="'.htmlspecialchars($_POST['cs_blog_view'][$cs_counter_blog]).'" ';
														}if(isset($_POST['cs_blog_excerpt'][$cs_counter_blog]) && $_POST['cs_blog_excerpt'][$cs_counter_blog] != ''){
															$shortcode .= 	'cs_blog_excerpt="'.htmlspecialchars($_POST['cs_blog_excerpt'][$cs_counter_blog], ENT_QUOTES).'" ';
														}if(isset($_POST['cs_blog_num_post'][$cs_counter_blog]) && $_POST['cs_blog_num_post'][$cs_counter_blog] != ''){
															$shortcode .= 	'cs_blog_num_post="'.htmlspecialchars($_POST['cs_blog_num_post'][$cs_counter_blog]).'" ';
														}if(isset($_POST['cs_blog_orderby'][$cs_counter_blog]) && $_POST['cs_blog_orderby'][$cs_counter_blog] != ''){
															$shortcode .= 	'cs_blog_orderby="'.htmlspecialchars($_POST['cs_blog_orderby'][$cs_counter_blog]).'" ';
														}if(isset($_POST['blog_pagination'][$cs_counter_blog]) && $_POST['blog_pagination'][$cs_counter_blog] != ''){
															$shortcode .= 	'blog_pagination="'.htmlspecialchars($_POST['blog_pagination'][$cs_counter_blog]).'" ';
														}if(isset($_POST['cs_blog_class'][$cs_counter_blog]) && $_POST['cs_blog_class'][$cs_counter_blog] != ''){
															$shortcode .= 	'cs_blog_class="'.htmlspecialchars($_POST['cs_blog_class'][$cs_counter_blog], ENT_QUOTES).'" ';
														}if(isset($_POST['cs_blog_animation'][$cs_counter_blog]) && $_POST['cs_blog_animation'][$cs_counter_blog] != ''){
															$shortcode .= 	'cs_blog_animation="'.htmlspecialchars($_POST['cs_blog_animation'][$cs_counter_blog]).'" ';
														}
														$shortcode .= 	']';
														$blog->addChild('cs_shortcode', $shortcode );
														$cs_counter_blog++;
													}
												$cs_global_counter_blog++;
										}
																				
										// member
										else if ( $_POST['cs_orderby'][$cs_counter] == "member" ) {
													$shortcode = '';
													$member = $column->addChild('member');
													$member->addChild('page_element_size', htmlspecialchars($_POST['member_element_size'][$cs_global_counter_member]) );
													$member->addChild('member_element_size', htmlspecialchars($_POST['member_element_size'][$cs_global_counter_member]) );
													if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
														$shortcode_str = stripslashes ($_POST['shortcode']['member'][$cs_shortcode_counter_member]);
														$cs_shortcode_counter_member++;
														$member->addChild('cs_shortcode', htmlspecialchars($shortcode_str) );
													} else {
														$shortcode = '[cs_member ';
														if(isset($_POST['cs_member_section_title'][$cs_counter_member]) && $_POST['cs_member_section_title'][$cs_counter_member] != ''){
															$shortcode .= 	'cs_member_section_title="'.htmlspecialchars($_POST['cs_member_section_title'][$cs_counter_member], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_member_view'][$cs_counter_member]) && $_POST['cs_member_view'][$cs_counter_member] != ''){
															$shortcode .= 	'cs_member_view="'.htmlspecialchars($_POST['cs_member_view'][$cs_counter_member]).'" ';
														}if(isset($_POST['cs_member_num_post'][$cs_counter_member]) && $_POST['cs_member_num_post'][$cs_counter_member] != ''){
															$shortcode .= 	'cs_member_num_post="'.htmlspecialchars($_POST['cs_member_num_post'][$cs_counter_member]).'" ';
														}if(isset($_POST['cs_member_cat'][$cs_counter_member]) && $_POST['cs_member_cat'][$cs_counter_member] != ''){
															$shortcode .= 	'cs_member_cat="'.htmlspecialchars($_POST['cs_member_cat'][$cs_counter_member]).'" ';
														}if(isset($_POST['cs_member_orderby'][$cs_counter_member]) && $_POST['cs_member_orderby'][$cs_counter_member] != ''){
															$shortcode .= 	'cs_member_orderby="'.htmlspecialchars($_POST['cs_member_orderby'][$cs_counter_member]).'" ';
														}if(isset($_POST['cs_member_excerpt_length'][$cs_counter_member]) && $_POST['cs_member_excerpt_length'][$cs_counter_member] != ''){
															$shortcode .= 	'cs_member_excerpt_length="'.htmlspecialchars($_POST['cs_member_excerpt_length'][$cs_counter_member]).'" ';
														}if(isset($_POST['cs_member_filterable'][$cs_counter_member]) && $_POST['cs_member_filterable'][$cs_counter_member] != ''){
															$shortcode .= 	'cs_member_filterable="'.htmlspecialchars($_POST['cs_member_filterable'][$cs_counter_member]).'" ';
														}if(isset($_POST['member_pagination'][$cs_counter_member]) && $_POST['member_pagination'][$cs_counter_member] != ''){
															$shortcode .= 	'member_pagination="'.htmlspecialchars($_POST['member_pagination'][$cs_counter_member]).'" ';
														}if(isset($_POST['cs_member_class'][$cs_counter_member]) && $_POST['cs_member_class'][$cs_counter_member] != ''){
															$shortcode .= 	'cs_member_class="'.htmlspecialchars($_POST['cs_member_class'][$cs_counter_member], ENT_QUOTES).'" ';
														}if(isset($_POST['cs_member_animation'][$cs_counter_member]) && $_POST['cs_member_animation'][$cs_counter_member] != ''){
															$shortcode .= 	'cs_member_animation="'.htmlspecialchars($_POST['cs_member_animation'][$cs_counter_member]).'" ';
														}
														$shortcode .= 	']';
														$member->addChild('cs_shortcode', $shortcode );
														$cs_counter_member++;
													}
												$cs_global_counter_member++;
										}
										
										// Clients
										else if ( $_POST['cs_orderby'][$cs_counter] == "clients" ) {
													$shortcode = $shortcode_item = '';
													$cs_clients = $column->addChild('clients');
													$cs_clients->addChild('page_element_size', htmlspecialchars($_POST['clients_element_size'][$cs_global_counter_clients]) );
													$cs_clients->addChild('clients_element_size', htmlspecialchars($_POST['clients_element_size'][$cs_shortcode_counter_clients]) );
													if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
														$cs_shortcode_str = stripslashes ($_POST['shortcode']['clients'][$cs_shortcode_counter_clients]);
														$cs_shortcode_counter_clients++;
														$cs_clients->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str) );
													} else {
														if(isset($_POST['clients_num'][$cs_counter_clients]) && $_POST['clients_num'][$cs_counter_clients]>0){
															for ( $i = 1; $i <= $_POST['clients_num'][$cs_counter_clients]; $i++ ){
																$cs_clients_item = $cs_clients->addChild('clients_item');
													
																$shortcode_item .= '[clients_item ';
																if(isset($_POST['cs_bg_color'][$cs_counter_clients_node])  && $_POST['cs_bg_color'][$cs_counter_clients_node] != ''){
																	$shortcode_item .= 	'cs_bg_color="'.htmlspecialchars($_POST['cs_bg_color'][$cs_counter_clients_node]).'" ';
																}	
																if(isset($_POST['cs_website_url'][$cs_counter_clients_node])  && $_POST['cs_website_url'][$cs_counter_clients_node] != ''){
																	$shortcode_item .= 	'cs_website_url="'.htmlspecialchars($_POST['cs_website_url'][$cs_counter_clients_node]).'" ';
																}
																if(isset($_POST['cs_client_title'][$cs_counter_clients_node])  && $_POST['cs_client_title'][$cs_counter_clients_node] != ''){
																	$shortcode_item .= 	'cs_client_title="'.htmlspecialchars($_POST['cs_client_title'][$cs_counter_clients_node], ENT_QUOTES).'" ';
																}
																if(isset($_POST['cs_client_logo'][$cs_counter_clients_node])  && $_POST['cs_client_logo'][$cs_counter_clients_node] != ''){
																	$shortcode_item .= 	'cs_client_logo="'.htmlspecialchars($_POST['cs_client_logo'][$cs_counter_clients_node]).'" ';
																}	
																$shortcode_item .= 	']';
																$cs_counter_clients_node++;
															}
														}
													$cs_section_title = '';
													if(isset($_POST['cs_client_section_title'][$cs_counter_clients])  && $_POST['cs_client_section_title'][$cs_counter_clients] != ''){
														$cs_section_title = 	'cs_client_section_title="'.htmlspecialchars($_POST['cs_client_section_title'][$cs_counter_clients], ENT_QUOTES).'" ';
													}
													$shortcode = '[cs_clients ';
													if(isset($_POST['cs_clients_view'][$cs_counter_clients])  && $_POST['cs_clients_view'][$cs_counter_clients] != ''){
														$shortcode .= 	'cs_clients_view="'.htmlspecialchars($_POST['cs_clients_view'][$cs_counter_clients]).'" ';
													}
													if(isset($_POST['cs_client_section_title'][$cs_counter_clients])  && $_POST['cs_client_section_title'][$cs_counter_clients] != ''){
														$shortcode .= 	'cs_client_section_title="'.htmlspecialchars($_POST['cs_client_section_title'][$cs_counter_clients]).'" ';
													}
													if(isset($_POST['cs_client_border'][$cs_counter_clients])  && $_POST['cs_client_border'][$cs_counter_clients] != ''){
														$shortcode .= 	'cs_client_border="'.htmlspecialchars($_POST['cs_client_border'][$cs_counter_clients]).'" ';
													}
													if(isset($_POST['cs_client_gray'][$cs_counter_clients])  && $_POST['cs_client_gray'][$cs_counter_clients] != ''){
														$shortcode .= 	'cs_client_gray="'.htmlspecialchars($_POST['cs_client_gray'][$cs_counter_clients]).'" ';
													}		
													if(isset($_POST['cs_client_class'][$cs_counter_clients])  && $_POST['cs_client_class'][$cs_counter_clients] != ''){
														$shortcode .= 	'cs_client_class="'.htmlspecialchars($_POST['cs_client_class'][$cs_counter_clients], ENT_QUOTES).'" ';
													}		
													if(isset($_POST['cs_client_animation'][$cs_counter_clients])  && $_POST['cs_client_animation'][$cs_counter_clients] != ''){
														$shortcode .= 	'cs_client_animation="'.htmlspecialchars($_POST['cs_client_animation'][$cs_counter_clients]).'" ';
													}
													$shortcode .= 	']'.$shortcode_item.'[/cs_clients]';		
													$cs_clients->addChild('cs_shortcode', $shortcode );
													$cs_counter_clients++;
												}
											$cs_global_counter_clients++;		
										}
										// Teams
										else if ( $_POST['cs_orderby'][$cs_counter] == "teams" ) {
													$shortcode = '';
													$cs_team = $column->addChild('teams');
													$cs_team->addChild('page_element_size', htmlspecialchars($_POST['teams_element_size'][$cs_global_counter_teams]) );
													$cs_team->addChild('teams_element_size', htmlspecialchars($_POST['teams_element_size'][$cs_global_counter_teams]) );
													if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
														$cs_shortcode_str = stripslashes ($_POST['shortcode']['teams'][$cs_shortcode_counter_teams]);
														$cs_shortcode_counter_teams++;
														$cs_team->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
													} else {
														$shortcode = '[cs_team ';
														if(isset($_POST['cs_team_section_title'][$cs_counter_teams])  && $_POST['cs_team_section_title'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_section_title="'.htmlspecialchars($_POST['cs_team_section_title'][$cs_counter_teams], ENT_QUOTES).'" ';
														}		
														if(isset($_POST['cs_team_view'][$cs_counter_teams])  && $_POST['cs_team_view'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_view="'.htmlspecialchars($_POST['cs_team_view'][$cs_counter_teams]).'" ';
														}
														if(isset($_POST['cs_team_name'][$cs_counter_teams])  && $_POST['cs_team_name'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_name="'.htmlspecialchars($_POST['cs_team_name'][$cs_counter_teams], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_team_designation'][$cs_counter_teams])  && $_POST['cs_team_designation'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_designation="'.htmlspecialchars($_POST['cs_team_designation'][$cs_counter_teams]).'" ';
														}
														if(isset($_POST['cs_team_description'][$cs_counter_teams])  && $_POST['cs_team_description'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_description="'.htmlspecialchars($_POST['cs_team_description'][$cs_counter_teams], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_team_profile_image'][$cs_counter_teams])  && $_POST['cs_team_view'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_profile_image="'.htmlspecialchars($_POST['cs_team_profile_image'][$cs_counter_teams]).'" ';
														}
														if(isset($_POST['cs_team_fb_url'][$cs_counter_clients])  && $_POST['cs_team_fb_url'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_fb_url="'.htmlspecialchars($_POST['cs_team_fb_url'][$cs_counter_teams]).'" ';
														}
														if(isset($_POST['cs_team_twitter_url'][$cs_counter_clients])  && $_POST['cs_team_twitter_url'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_twitter_url="'.htmlspecialchars($_POST['cs_team_twitter_url'][$cs_counter_teams]).'" ';
														}
														if(isset($_POST['cs_team_googleplus_url'][$cs_counter_teams])  && $_POST['cs_team_googleplus_url'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_googleplus_url="'.htmlspecialchars($_POST['cs_team_googleplus_url'][$cs_counter_teams]).'" ';
														}
														if(isset($_POST['cs_team_skype_url'][$cs_counter_clients])  && $_POST['cs_team_skype_url'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_skype_url="'.htmlspecialchars($_POST['cs_team_skype_url'][$cs_counter_teams]).'" ';
														}
														if(isset($_POST['cs_team_email'][$cs_counter_clients])  && $_POST['cs_team_email'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_team_email="'.htmlspecialchars($_POST['cs_team_email'][$cs_counter_teams]).'" ';
														}
														if(isset($_POST['cs_teams_class'][$cs_counter_teams])  && $_POST['cs_teams_class'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_teams_class="'.htmlspecialchars($_POST['cs_teams_class'][$cs_counter_teams], ENT_QUOTES).'" ';
														}		

														if(isset($_POST['cs_teams_animation'][$cs_counter_teams])  && $_POST['cs_teams_animation'][$cs_counter_teams] != ''){
															$shortcode .= 	'cs_teams_animation="'.htmlspecialchars($_POST['cs_teams_animation'][$cs_counter_teams]).'" ';
														}
														$shortcode .= 	']';
														if(isset($_POST['cs_team_description'][$cs_counter_teams])  && $_POST['cs_team_description'][$cs_counter_teams] != ''){
															$shortcode .= 	htmlspecialchars($_POST['cs_team_description'][$cs_counter_teams], ENT_QUOTES);
														}
														$shortcode .= 	'[/cs_team]';
														$cs_team->addChild('cs_shortcode', $shortcode );
														$cs_counter_teams++;
													}
												$cs_global_counter_teams++;	
										}
										// Save Twitter page element 
										else if ( $_POST['cs_orderby'][$cs_counter] == "tweets" ) {
													$shortcode = '';
													$cs_tweet = $column->addChild('tweets');
													$cs_tweet->addChild('page_element_size', htmlspecialchars($_POST['tweets_element_size'][$cs_global_counter_tweets]));
													$cs_tweet->addChild('tweets_element_size', htmlspecialchars($_POST['tweets_element_size'][$cs_global_counter_tweets]));
													if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
														$cs_shortcode_str = stripslashes ($_POST['shortcode']['tweets'][$cs_shortcode_counter_tweets]);
														$cs_shortcode_counter_tooltip++;
														$cs_tweet->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
													} else {
														$shortcode = '[cs_tweets ';
														if(isset($_POST['cs_tweets_section_title'][$cs_counter_contactus]) && $_POST['cs_tweets_section_title'][$cs_counter_tweets] != ''){
															//$shortcode .= 	'cs_tweets_section_title="'.htmlspecialchars($_POST['cs_tweets_section_title'][$cs_counter_tweets]).'" ';
														}
														if(isset($_POST['cs_tweets_user_name'][$cs_counter_tweets]) && $_POST['cs_tweets_user_name'][$cs_counter_tweets] != ''){
															$shortcode .= 	'cs_tweets_user_name="'.htmlspecialchars($_POST['cs_tweets_user_name'][$cs_counter_tweets]).'" ';
														}
														if(isset($_POST['cs_tweets_color'][$cs_counter_tweets]) && $_POST['cs_tweets_color'][$cs_counter_tweets] != ''){
															$shortcode .= 	'cs_tweets_color="'.htmlspecialchars($_POST['cs_tweets_color'][$cs_counter_tweets]).'" ';
														}
														if(isset($_POST['cs_no_of_tweets'][$cs_counter_tweets]) && $_POST['cs_no_of_tweets'][$cs_counter_tweets] != ''){
															$shortcode .= 	'cs_no_of_tweets="'.htmlspecialchars($_POST['cs_no_of_tweets'][$cs_counter_tweets]).'" ';
														}
														if(isset($_POST['cs_tweets_class'][$cs_counter_tweets]) && $_POST['cs_tweets_class'][$cs_counter_tweets] != ''){
															$shortcode .= 	'cs_tweets_class="'.htmlspecialchars($_POST['cs_tweets_class'][$cs_counter_tweets], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_tweets_animation'][$cs_counter_tweets]) && $_POST['cs_tweets_animation'][$cs_counter_tweets] != ''){
															$shortcode .= 	'cs_tweets_animation="'.htmlspecialchars($_POST['cs_tweets_animation'][$cs_counter_tweets]).'" ';
														}
														$shortcode .= 	']';
														$cs_tweet->addChild('cs_shortcode', $shortcode );
														$cs_counter_tweets++;
													}
												$cs_global_counter_tweets++;
										}
										
									  // Project
									  else if ( $_POST['cs_orderby'][$cs_counter] == "project" ) {
													$shortcode = '';
													$project = $column->addChild('project');
													$project->addChild('page_element_size', htmlspecialchars($_POST['project_element_size'][$cs_global_counter_project]) );
													$project->addChild('project_element_size', htmlspecialchars($_POST['project_element_size'][$cs_global_counter_project]) );
													if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
														$shortcode_str = stripslashes ($_POST['shortcode']['project'][$cs_shortcode_counter_project]);
														$cs_shortcode_counter_project++;
														$project->addChild('cs_shortcode', htmlspecialchars($shortcode_str) );
													} else {
														$shortcode = '[cs_project ';
														if(isset($_POST['cs_project_section_title'][$cs_counter_project]) && $_POST['cs_project_section_title'][$cs_counter_project] != ''){
															$shortcode .= 	'cs_project_section_title="'.htmlspecialchars($_POST['cs_project_section_title'][$cs_counter_project], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_project_cat'][$cs_counter_project]) && $_POST['cs_project_cat'][$cs_counter_project] != ''){
															$shortcode .= 	'cs_project_cat="'.htmlspecialchars($_POST['cs_project_cat'][$cs_counter_project]).'" ';
														}if(isset($_POST['cs_project_view'][$cs_counter_project]) && $_POST['cs_project_view'][$cs_counter_project] != ''){
															$shortcode .= 	'cs_project_view="'.htmlspecialchars($_POST['cs_project_view'][$cs_counter_project]).'" ';
														}if(isset($_POST['cs_project_num_post'][$cs_counter_project]) && $_POST['cs_project_num_post'][$cs_counter_project] != ''){
															$shortcode .= 	'cs_project_num_post="'.htmlspecialchars($_POST['cs_project_num_post'][$cs_counter_project]).'" ';
														}if(isset($_POST['cs_project_pagination'][$cs_counter_project]) && $_POST['cs_project_pagination'][$cs_counter_project] != ''){
															$shortcode .= 	'cs_project_pagination="'.htmlspecialchars($_POST['cs_project_pagination'][$cs_counter_project]).'" ';
														}if(isset($_POST['cs_filterable'][$cs_counter_project]) && $_POST['cs_filterable'][$cs_counter_project] != ''){
															$shortcode .= 	'cs_filterable="'.htmlspecialchars($_POST['cs_filterable'][$cs_counter_project]).'" ';
														}if(isset($_POST['cs_text_align'][$cs_counter_project]) && $_POST['cs_text_align'][$cs_counter_project] != ''){
															$shortcode .= 	'cs_text_align="'.htmlspecialchars($_POST['cs_text_align'][$cs_counter_project]).'" ';
														}if(isset($_POST['cs_project_class'][$cs_counter_project]) && $_POST['cs_project_class'][$cs_counter_project] != ''){
															$shortcode .= 	'cs_project_class="'.htmlspecialchars($_POST['cs_project_class'][$cs_counter_project], ENT_QUOTES).'" ';
														}if(isset($_POST['cs_project_animation'][$cs_counter_project]) && $_POST['cs_project_animation'][$cs_counter_project] != ''){
															$shortcode .= 	'cs_project_animation="'.htmlspecialchars($_POST['cs_project_animation'][$cs_counter_project]).'" ';
														}
														$shortcode .= 	']';
														$project->addChild('cs_shortcode', $shortcode );
														$cs_counter_project++;
													}
												$cs_global_counter_project++;
										}
									  // Content Slider
									  else if ( $_POST['cs_orderby'][$cs_counter] == "contentslider" ) {
													$shortcode = '';
													$cs_contentslider = $column->addChild('contentslider');
													$cs_contentslider->addChild('page_element_size', htmlspecialchars($_POST['contentslider_element_size'][$cs_global_counter_contentslider]) );
													$cs_contentslider->addChild('contentslider_element_size', htmlspecialchars($_POST['contentslider_element_size'][$cs_global_counter_contentslider]) );
													if(isset($_POST['cs_widget_element_num'][$cs_counter]) && $_POST['cs_widget_element_num'][$cs_counter] == 'shortcode'){
														$cs_shortcode_str = stripslashes ($_POST['shortcode']['contentslider'][$cs_shortcode_counter_contentslider]);
														$cs_shortcode_counter_contentslider++;
														$cs_contentslider->addChild('cs_shortcode', htmlspecialchars($cs_shortcode_str, ENT_QUOTES) );
													} else {
														$shortcode = '[cs_contentslider ';
														if(isset($_POST['cs_contentslider_title'][$cs_counter_contentslider]) && $_POST['cs_contentslider_title'][$cs_counter_contentslider] != ''){
															$shortcode .= 	'cs_contentslider_title="'.htmlspecialchars($_POST['cs_contentslider_title'][$cs_counter_contentslider], ENT_QUOTES).'" ';
														}	
														if(isset($_POST['cs_contentslider_post_type'][$cs_counter_contentslider]) && $_POST['cs_contentslider_post_type'][$cs_counter_contentslider] != ''){
															$shortcode .= 	'cs_contentslider_post_type="'.htmlspecialchars($_POST['cs_contentslider_post_type'][$cs_counter_contentslider]).'" ';
																										}	
														if(isset($_POST['cs_contentslider_dcpt_cat'][$cs_counter_contentslider]) && $_POST['cs_contentslider_dcpt_cat'][$cs_counter_contentslider] != ''){
															$shortcode .= 	'cs_contentslider_dcpt_cat="'.htmlspecialchars($_POST['cs_contentslider_dcpt_cat'][$cs_counter_contentslider]).'" ';
														}	
														if(isset($_POST['cs_contentslider_orderby'][$cs_counter_contentslider]) && $_POST['cs_contentslider_orderby'][$cs_counter_contentslider] != ''){
															$shortcode .= 	'cs_contentslider_orderby="'.htmlspecialchars($_POST['cs_contentslider_orderby'][$cs_counter_contentslider]).'" ';
														}	
														if(isset($_POST['cs_contentslider_description'][$cs_counter_contentslider]) && $_POST['cs_contentslider_description'][$cs_counter_contentslider] != ''){
															$shortcode .= 	'cs_contentslider_description="'.htmlspecialchars($_POST['cs_contentslider_description'][$cs_counter_contentslider], ENT_QUOTES).'" ';
														}	
														if(isset($_POST['cs_contentslider_excerpt'][$cs_counter_contentslider]) && $_POST['cs_contentslider_excerpt'][$cs_counter_contentslider] != ''){
															$shortcode .= 	'cs_contentslider_excerpt="'.htmlspecialchars($_POST['cs_contentslider_excerpt'][$cs_counter_contentslider]).'" ';
																										}
														if(isset($_POST['cs_contentslider_num_post'][$cs_counter_contentslider]) && $_POST['cs_contentslider_num_post'][$cs_counter_contentslider] != ''){
															$shortcode .= 	'cs_contentslider_num_post="'.htmlspecialchars($_POST['cs_contentslider_num_post'][$cs_counter_contentslider]).'" ';
														}	
														if(isset($_POST['cs_contentslider_class'][$cs_counter_contentslider]) && $_POST['cs_contentslider_class'][$cs_counter_contentslider] != ''){
															$shortcode .= 	'cs_contentslider_class="'.htmlspecialchars($_POST['cs_contentslider_class'][$cs_counter_contentslider], ENT_QUOTES).'" ';
														}
														if(isset($_POST['cs_contentslider_animation'][$cs_counter_contentslider]) && $_POST['cs_contentslider_animation'][$cs_counter_contentslider] != ''){
															$shortcode .= 	'cs_contentslider_animation="'.htmlspecialchars($_POST['cs_contentslider_animation'][$cs_counter_contentslider]).'" ';
														}
														$shortcode .= 	']';
														$cs_contentslider->addChild('cs_shortcode', $shortcode );
														$cs_counter_contentslider++;
												}
												$cs_global_counter_contentslider++;
									}
									// Course Search
									 //===Loops Short Code End	
											$cs_counter++;
								}
								$widget_no++;
							}
							$column_container_no++;
						}
					 }
					
					//$cs_tmp_fn = 'base'.'64_encode';
//					$new = call_user_func($cs_tmp_fn, serialize($sxe->asXML()));
					update_post_meta( $post_id, 'cs_page_builder', $sxe->asXML() );
					//exit;
				//creating xml page builder end
			}
		}
	}
//add_action( 'init', 'stop_heartbeat', 1 );
function stop_heartbeat() {
 wp_deregister_script('heartbeat');
}
?>