<?php
//=====================================================================
// Adding column start
//=====================================================================
if (!function_exists('cs_column_shortocde')) {
	function cs_column_shortocde($cs_atts, $content = "") {
		$cs_defaults = array('column_size'=>'1/1','cs_flex_column_section_title'=>'','cs_column_class'=>'','flex_column_bg_color'=>'','flex_column_text_color'=>'','cs_column_animation'=>'','cs_column_animation_duration'=>'1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_class = cs_custom_column_class($column_size);
		
		$cs_column_bg_color = '';
		$cs_column_text_color = '';
		$cs_column_bg_class = '';
		$cs_section_title = '';
		if ( trim($cs_column_animation) !='' ) {
			$cs_column_animation	= 'wow'.' '.$cs_column_animation;
		} else {
			$cs_column_animation	= '';
		}
		
		if ( trim($cs_column_class) !='' ) {
			$cs_column_class_id = ' id="'.$cs_column_class.'"';
		}
		else{
			$cs_column_class_id = '';
		}
		
		if ( $flex_column_text_color != '' ) {
			$cs_column_text_color = ' color:'.$flex_column_text_color;
		}
		
		if ( $flex_column_bg_color != '' ) {
			$flex_column_bg_color = cs_hex2rgb($flex_column_bg_color);
			$cs_column_bg_color = ' style="background-color:rgba('.$flex_column_bg_color[0].', '. $flex_column_bg_color[1].', '.$flex_column_bg_color[2].', 0.8);'.$cs_column_text_color.'"';
			$cs_column_bg_class = 'has-bg ';
		}
		
		if ($cs_flex_column_section_title && trim($cs_flex_column_section_title) !='') {
			$cs_section_title	= '<div class="cs-section-title"><h2>'.$cs_flex_column_section_title.'</h2></div>';
		}
	 	$content = nl2br($content);
		$content = cs_custom_shortcode_decode($content);
		$html = do_shortcode($content);
		
		return '<div class="'.$cs_column_animation.' lightbox '.$cs_column_bg_class.$cs_class.'" '.$cs_column_class_id.$cs_column_bg_color.'>'.$cs_section_title.' '.$html.'</div>';
	}
	add_shortcode('cs_column', 'cs_column_shortocde');
}
// adding column end

//=====================================================================
// Adding Tooltip start
//=====================================================================
if (!function_exists('cs_tooltip_shortcode')) {
	function cs_tooltip_shortcode($cs_atts, $content = "") {
		$cs_defaults = array( 'cs_column_size'=>'1/1','cs_tooltip_hover_title' => '','cs_tooltip_data_placement' => 'top','cs_tooltip_class'=>'', 'cs_tooltip_animation'=>'', 'cs_tooltip_animation_duration'=>'1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class = cs_custom_column_class($cs_column_size);
		$html = "<script>
			jQuery(document).ready(function($) {
				jQuery('.tolbtn').tooltip('hide');
				jQuery('.tolbtn').popover('hide')
			});
		</script>";
		if ( trim($cs_tooltip_animation) !='' ) {
			$cs_tooltip_animation	= ' wow'.' '.$cs_tooltip_animation;
		} else {
			$cs_tooltip_animation	= '';
		}
        
		$html .= '<div class="tooltip-info">'.do_shortcode($content).'</div>
		<span class="tolbtn btn btn-default custom-btn" data-toggle="tooltip" data-placement="'.$cs_tooltip_data_placement.'" title="'.$cs_tooltip_hover_title.'" style="background-color:#fff; color:#333; box-shadow:0px 4px 4px -2px rgba(0, 0, 0, 0.5);">'.$cs_tooltip_hover_title.'</span>';
		
		return do_shortcode('<div class="'.$cs_column_class.$cs_tooltip_animation.'">'.$html.'</div>');
	}

	add_shortcode('cs_tooltip', 'cs_tooltip_shortcode');
}
// adding Tooltip end

//=====================================================================
// Adding dropcap start
//=====================================================================
if (!function_exists('cs_dropcap_shortcode')) {
	function cs_dropcap_shortcode($cs_atts, $content = "") {
		$cs_defaults = array( 'column_size' => '1/1', 'cs_dropcap_section_title' => '', 'cs_dropcap_style' => 'dropcap','cs_dropcap_bg_color' => '','cs_dropcap_color' => '','cs_dropcap_size' => '', 'cs_dropcap_class'=>'', 'cs_dropcap_animation'=>'', 'cs_dropcap_animation_duration'=>'1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_randomID = rand(0, 999);
		$cs_column_class 			= cs_custom_column_class($column_size);
		$cs_dropcap_style_class 	= '';
		$cs_dropcap_css 	= '';

		$cs_font_size				= $cs_dropcap_size ? $cs_dropcap_size : '40';
		$cs_bg_color				= $cs_dropcap_bg_color ? $cs_dropcap_bg_color : '';
		

		$html = '';
		$cs_section_title = '';
		if ( trim($cs_dropcap_animation) !='' ) {
			$cs_dropcap_animation	= 'wow'.' '.$cs_dropcap_animation;
		} else {
			$cs_dropcap_animation	= '';
		}
		if ($cs_dropcap_section_title && trim($cs_dropcap_section_title) !='') {
			$cs_section_title = '<div class="cs-section-title"><h2 class="'.$cs_dropcap_animation.'">'.$cs_dropcap_section_title.'</h2></div>';
		}
		if(isset($cs_dropcap_style) && $cs_dropcap_style == 'box'){		
       		$cs_dropcap_style_class = 'cs-dropcap dropcap-one';
			$html .= '<style scoped="scoped">
					.dropcap-'.$cs_randomID.' P:first-letter {
						color: '.$cs_dropcap_color.';
						background-color: '.$cs_bg_color.' !important;
						font-size: '.$cs_font_size.'px !important;
						text-transform:uppercase;
					}
				 </style>';
		}
		else{
			$cs_dropcap_style_class = 'dropcap-two 3D-dropcap';
			$html .= '<style scoped="scoped">
					.dropcap-'.$cs_randomID.' P:first-letter {
						color: '.$cs_dropcap_color.';
						font-size: '.$cs_font_size.'px !important;
						text-transform:uppercase;
					}
				 </style>';
		}
		if($cs_dropcap_class <> ''){
			$cs_drop_cap_id = ' id="'.$cs_dropcap_class.'"';
		}
		else{
			$cs_drop_cap_id = '';
		}
				
		$html .= '<div class="'.$cs_column_class.'">'.$cs_section_title.'<div class="'.$cs_dropcap_style_class . ' '.$cs_dropcap_class.' '.$cs_dropcap_animation.' dropcap-'.$cs_randomID.'"'.$cs_drop_cap_id.'><p>'.do_shortcode($content).'</p></div></div>';
		
		
		return $html;
		
	}
	add_shortcode('cs_dropcap', 'cs_dropcap_shortcode');
}
// adding dropcap end

//=====================================================================
// Diveder Shortcode Start
//=====================================================================
if (!function_exists('cs_divider_shortcode')) {
	function cs_divider_shortcode($cs_atts) {
		$cs_defaults = array( 'cs_column_size' => '1/1', 'cs_divider_style' => 'crossy','cs_divider_height' => '1','cs_divider_backtotop' => '','cs_divider_margin_top' => '','cs_divider_margin_bottom' =>'','cs_line' => 'Wide','cs_color'=>'#000', 'cs_divider_class'=>'','cs_divider_animation'=>'','cs_divider_animation_duration' => '1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		
		$cs_column_class = cs_custom_column_class($cs_column_size);
		
		$html = '';
		$cs_backtotop = '';
		if ( trim($cs_divider_animation) !='' ) {
			$cs_divider_animation	= 'wow'.' '.$cs_divider_animation;
		} else {
			$cs_divider_animation	= '';
		}
		if ($cs_divider_backtotop == 'yes' ){
			$cs_backtotop = '<span class="backtotop"><a class="btn-back-top btnnext" href="#"><i class="icon-angle-up"></i></a></span>';
		}
		
		if($cs_divider_style == 'crossy'){
			$cs_divider_style_class = 'cs-seprator';
			$cs_div_html = '<div class="devider1"></div>';
		}
		else if($cs_divider_style == 'plain'){
			$cs_divider_style_class = 'cs-seprator';
			$cs_div_html = '<div class="spreater-inn"><img src="'. get_template_directory_uri().'/assets/images/spreater1.png" alt=""></div>';
		}
		else if($cs_divider_style == 'zigzag'){
			$cs_divider_style_class = 'cs-seprator';
			$cs_div_html = '<div class="devider3"></div>';
		}
		else if($cs_divider_style == 'small-zigzag'){
			$cs_divider_style_class = 'cs-seprator';
			$cs_div_html = '<div class="devider5"></div>';
		}
		else{
			$cs_divider_style_class = 'spreater';
			$cs_div_html = '<div class="cs-seprator">
							  <div class="cs-seprator-holder">
								<span></span>
								<span></span>
								<span></span>
								<span></span>
							  </div>
							</div>';
		}
		
		$cs_divider_class_id = '';
		if($cs_divider_class <> ''){
			$cs_divider_class_id = ' id="'.$cs_divider_class.'"';
		}
		
		$html = '<div class="'.$cs_column_class.' '.$cs_divider_class.' '.$cs_divider_animation.'"'.$cs_divider_class_id.' style="animation-duration: '.$cs_divider_animation_duration.'s; margin-top:'.$cs_divider_margin_top.'px; margin-bottom:'.$cs_divider_margin_bottom.'px;height: '.$cs_divider_height.'px;">';
					if($cs_divider_style == '3box'){
					$html .= '<div class="cs-box-seprator" >';
					}
					$html .= '
					<div class="'.$cs_divider_style_class.'" >
						'.$cs_div_html;
						if($cs_divider_style != '3box'){
						$html .= $cs_backtotop;
						}
					$html .= '
					</div>';
					if($cs_divider_style == '3box'){
					$html .= $cs_backtotop;
					$html .= '</div>';
					}
					$html .= '
					
				 </div>';
		
		return do_shortcode($html);
	}
	add_shortcode('cs_divider', 'cs_divider_shortcode');
}
// Diveder Shortcode end

//=====================================================================
// Quote Shortcode Shortcode Start
//=====================================================================
if (!function_exists('cs_quote_shortcode')) {
	function cs_quote_shortcode( $cs_atts, $content = null ) {
		extract(shortcode_atts(array(
			'cs_column_size' => '1/1',
			'cs_quote_style' => 'default',
			'cs_quote_section_title' => '',
			'cs_quote_cite'   => '',
			'cs_quote_cite_url'   => '#',
			'cs_quote_text_color'   => '',
			'cs_quote_align'   => 'center',
			'cs_quote_class'   => '',
			'cs_quote_animation_duration'   => '1',
			'cs_quote_animation'   => ''
	    ), $cs_atts));
		$cs_author_name = '';
		$html		 = '';
		
		$cs_column_class = cs_custom_column_class($cs_column_size);
		
		if ( trim($cs_quote_animation) !='' ) {
			$cs_quote_animation	= 'wow'.' '.$cs_quote_animation;
		} else {
			$cs_quote_animation	= '';
		}
		
		$cs_CustomId	= '';
		if ( isset( $cs_quote_class ) && $cs_quote_class ) {
			$cs_CustomId	= 'id="'.$cs_quote_class.'"';
		}
		
		if(isset($cs_quote_cite) && $cs_quote_cite <> ''){
			$cs_author_name .= '<div class="cs-auther-name"><span>';
			if(isset($cs_quote_cite_url) && $cs_quote_cite_url <> ''){
				$cs_author_name .= '<a href="'.esc_url($cs_quote_cite_url).'">';
			}
			$cs_author_name .= '- '.$cs_quote_cite;
			if(isset($cs_quote_cite_url) && $cs_quote_cite_url <> ''){
				$cs_author_name .= '</a>';
			}
			
			$cs_author_name .= '</span></div>';
		}
		if($cs_quote_style =='icon'){
			$cs_quote_class='has-qoute';
		}else{
			$cs_quote_class='';
		}
		if(isset($cs_quote_align)){
			if($cs_quote_align == 'left') $cs_quote_align = 'text-left-align';
			if($cs_quote_align == 'right') $cs_quote_align = 'text-right-align';
			if($cs_quote_align == 'center') $cs_quote_align = 'text-center-align';
		}
		
		$cs_section_title = '';
		if ($cs_quote_section_title && trim($cs_quote_section_title) !='') {
			$cs_section_title = '<div class="cs-section-title"><h2 class="'.$cs_quote_animation.'">'.$cs_quote_section_title.'</h2></div>';
		}
		
		$html	.= '<blockquote class="cs-qoute '.$cs_quote_class.' '.$cs_quote_align.' '.$cs_quote_animation.'" '.$cs_CustomId.' style="animation-duration: '.$cs_quote_animation_duration.'s; color:'.$cs_quote_text_color.'"><span>' . do_shortcode($content) . $cs_author_name .'</span></blockquote>';
		
		return '<div class="'.sanitize_html_class($cs_column_class).'">'.$cs_section_title.$html.'</div>';
	}
	add_shortcode('cs_quote', 'cs_quote_shortcode');
}
// Quote Shortcode Shortcode End

//=====================================================================
// Adding hightlight start
//=====================================================================
if (!function_exists('cs_hightlight_shortcode')) {
	function cs_hightlight_shortcode($cs_atts, $content = "") {
		$cs_defaults = array( 'cs_highlight_bg_color' => '','cs_highlight_color' => '','cs_highlight_class' => '','cs_highlight_animation'=>'','cs_highlight_animation_duration'   => '10');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		
		if ( trim($cs_highlight_animation) !='' ) {
			$cs_highlight_animation	= 'wow'.' '.$cs_highlight_animation;
		} else {
			$cs_highlight_animation	= '';
		}
		$cs_highlight_class_id = '';
		if($cs_highlight_class_id <> ''){
			$cs_highlight_class_id = ' id="'.$cs_highlight_class.'"';
		}
		
		$html = '<mark'.$cs_highlight_class_id.' style="background:'.$cs_highlight_bg_color.'; color:'.$cs_highlight_color.'; animation-duration: '.$cs_highlight_animation_duration.'s;" class="highlights '.$cs_highlight_class.' '.$cs_highlight_animation.'">'.$content.'</mark>';
		return do_shortcode($html);
	}
	add_shortcode('cs_highlight', 'cs_hightlight_shortcode');
}
// adding hightlight end

//=====================================================================
// Adding heading start
//=====================================================================
if (!function_exists('cs_heading_shortcode')) {
	function cs_heading_shortcode($cs_atts, $content = "") {
		$cs_defaults = array( 'column_size'=>'1/1','cs_heading_title' => '','cs_color_title'=>'','cs_heading_color' => '#000', 'cs_class'=>'cs-heading-shortcode', 'cs_heading_style'=>'1','cs_heading_style_type'=>'1', 'cs_heading_size'=>'', 'cs_font_weight'=>'', 'cs_heading_font_style'=>'', 'cs_heading_align'=>'center', 'cs_heading_divider'=>'', 'cs_heading_divider_icon'=>'', 'cs_heading_color' => '', 'cs_heading_content_color' => '', 'cs_heading_animation'=>'');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		if ( isset( $column_size ) && $column_size !='' ) {
			$cs_column_class = cs_custom_column_class($column_size);
		} else {
			$cs_column_class = '';
		}
		$html = '';
		$cs_css = '';
		
		$cs_he_font_style = '';
		if($cs_heading_font_style <> ''){
			$cs_he_font_style = ' font-style:'.$cs_heading_font_style;
		}
		
		echo balanceTags($cs_css, false);
		$html .= '<div class="'.$cs_heading_animation.' lightbox '.$cs_column_class.'" >';
			if($cs_heading_title <> ''){
				if($cs_color_title<>''){
					$cs_color_title ='<span class="cs-color">'.$cs_color_title.'</span>';
				}
				$html .= '<h'.$cs_heading_style.' style="color:'.$cs_heading_color.' !important; font-size: '.$cs_heading_size.'px !important; text-align: '.$cs_heading_align.';'.$cs_he_font_style.';">'.$cs_heading_title.$cs_color_title.'</h'.$cs_heading_style.'>';
				
			}
			if($content <> ''){
				$html	.= '<div class="heading-description" style="color:'.$cs_heading_content_color.' !important; text-align: '.$cs_heading_align.';'.$cs_he_font_style.';">'.do_shortcode(nl2br($content)).'</div>';
		
			}
			if($cs_heading_divider == 'on'){
				$html	.= '<div class="box_spreater">
								<div class="spreater" style="text-align: '.$cs_heading_align.';">
									<div class="fullwidth-sepratore" >
										<div class="dividerstyle">
											<i class="icon-text"></i>
										</div>
									</div>
								</div>
							</div>';
			}
		$html .= '</div>';
		return do_shortcode($html);
	}
	add_shortcode('cs_heading', 'cs_heading_shortcode');
}
// adding heading end

//=====================================================================
// Adding list start
//=====================================================================
if (!function_exists('cs_list_shortcode')) {
	function cs_list_shortcode($cs_atts, $content = "") {
		global $cs_border,$cs_list_type;
		$cs_defaults = array('column_size'=>'','cs_list_section_title'=>'','cs_list_type'=>'','cs_list_icon'=>'','cs_border'=>'','cs_list_item'=>'','cs_list_class'=>'','cs_list_animation'=>'','cs_list_animation_duration'=>'1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_customID = '';
		if ( isset( $column_size ) && $column_size !='' ) {
			$cs_column_class = cs_custom_column_class($column_size);
		} else {
			$cs_column_class = '';
		}
		
		if ( isset( $cs_list_class ) && $cs_list_class !='' ) {
			$cs_customID = 'id="'.$cs_list_class.'"';
		}
		
		$html	 = "";
		$cs_list_typeClass	= '';
		$cs_section_title = '';
		if ($cs_list_section_title && trim($cs_list_section_title) !='' ) {
			$cs_section_title	= '<div class="cs-section-title"><h2>'.$cs_list_section_title.'</h2></div>';
		}
		
		$cs_list_type	= $cs_list_type ? $cs_list_type : 'cs-bulletslist';
		
		if ($cs_list_type == 'none'){
			$cs_list_typeClass	= 'cs-unorderedlist';
		} else if ($cs_list_type == 'icon'){
			$cs_list_typeClass	= 'cs-iconlist';
		} else if ($cs_list_type == 'built'){
			$cs_list_typeClass	= 'cs-bull-list';
		} else if ($cs_list_type == 'decimal'){
			$cs_list_typeClass	= 'cs-number-list';
		}

		$html	.= '<div '.$cs_customID.' class="'.$cs_column_class.' '.$cs_list_animation.' '.$cs_list_class.'">';
		$html	.= $cs_section_title;
		$html	.= '<div class="liststyle">';
		$html	.= '<ul class="' .$cs_list_typeClass. '">';
		$html	.= do_shortcode($content);
		$html	.= '</ul>';
		$html	.= '</div>';
		$html	.= '</div>';
		return $html;
	}
	add_shortcode('cs_list', 'cs_list_shortcode');
}

//=====================================================================
// Adding list item start
//=====================================================================
if (!function_exists('cs_list_item_shortcode')) {
	function cs_list_item_shortcode($cs_atts, $content = "") {
		global $cs_border,$cs_list_type;
		$html='';
		$cs_defaults = array('cs_list_icon'=>'','cs_list_item'=>'','cs_cusotm_class'=>'','cs_custom_animation'=>'','cs_custom_animation'=>'');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		
		if ($cs_border == 'yes') {
			$border	= 'has_border';
		} else {
			$border	= '';
		}
		
		if ($cs_list_icon && $cs_list_type == 'icon' ) {
			$html	.= '<li class="'.$border.'"><i class="'.$cs_list_icon.'"></i>' .do_shortcode($content). '</li>'; 
		} 
		else if ($cs_list_icon && $cs_list_type == 'numeric-icon' ) {
			$html	.= '<li class="'.$border.'">' .force_balance_tags ( do_shortcode($content) ). '<i class="cs-color '.$cs_list_icon.'"></i></li>'; 
		} 
		else {
			$html	.= '<li class="'.$border.'">' .force_balance_tags ( do_shortcode($content) ). '</li>';
		}
		
		return $html;
	}
	add_shortcode('list_item', 'cs_list_item_shortcode');
}
// adding list item end

//=====================================================================
// Adding Contact Us Form start
//=====================================================================
if (!function_exists('cs_contactus_shortcode')) {
	function cs_contactus_shortcode($cs_atts, $content = "") {
		$cs_defaults = array( 'column_size' => '1/1', 'cs_contactus_section_title' => '', 'cs_contactus_label' => '', 'cs_contactus_vacancies' => '', 'cs_contactus_view' => '','cs_contactus_send' => '','cs_success' => '','cs_error' => '','cs_contact_class' => '','cs_contact_animation' => '','cs_contact_animation_duration'=>'1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class	= cs_custom_column_class($column_size);
		$cs_email_counter 	= cs_generate_random_string(3);
		$html 	 = '';
		$cs_class	 = '';
		$cs_section_title = '';
		
		if ($cs_contactus_section_title && trim($cs_contactus_section_title) !='') {
			$cs_section_title	= '<div class="cs-section-title"><h2 class="'.$cs_contact_animation.'">'.$cs_contactus_section_title.'</h2></div>';
		}
			 
		if (trim($cs_success) && trim($cs_success) !='') {
			$success	= $cs_success;
		} else {
			$success	= 'Email has been sent Successfully.';
		}
		
		if (trim($cs_error) && trim($cs_error) !='') {
			$error	= $cs_error;
		} else {
			$error	= 'An error Occured, please try again later.';
		}
		
		if (trim($cs_contactus_view) == 'plain') {
			$cs_view_class	= 'cs-plain-form';
		} else {
			$cs_view_class	= 'cs-classic-form';
		}

		?>
        <script type="text/javascript">
			function frm_submit<?php echo esc_js($cs_email_counter);?>(){
				
				var $ = jQuery;
				$("#loading_div<?php echo esc_js($cs_email_counter);?>").html('<img src="<?php echo esc_js(esc_url(get_template_directory_uri()));?>/assets/images/ajax-loader.gif" alt="" />');
				$("#loading_div<?php echo esc_js($cs_email_counter);?>").show();
				$("#message<?php echo esc_js($cs_email_counter);?>").html('');
				var datastring =$('#frm<?php echo esc_js($cs_email_counter);?>').serialize() +"&cs_contact_email=<?php echo esc_js($cs_contactus_send);?>&cs_contact_succ_msg=<?php echo esc_js($success);?>&cs_contact_error_msg=<?php echo esc_js($error);?>&action=cs_contact_form_submit";

				$.ajax({
					type:'POST', 
					url: '<?php echo esc_js(esc_url(admin_url('admin-ajax.php')));?>',
					data: datastring, 
					dataType: "json",
					success: function(response) {
						
						if (response.type == 'error'){
							$("#loading_div<?php echo esc_js($cs_email_counter);?>").html('');
							$("#loading_div<?php echo esc_js($cs_email_counter);?>").hide();
							$("#message<?php echo esc_js($cs_email_counter);?>").addClass('error_mess');
							$("#message<?php echo esc_js($cs_email_counter);?>").show();
							$("#message<?php echo esc_js($cs_email_counter)?>").html(response.message);
						} else if (response.type == 'success'){
							$("#frm<?php echo esc_js($cs_email_counter);?>").slideUp();
							$("#loading_div<?php echo esc_js($cs_email_counter);?>").html('');
							$("#loading_div<?php echo esc_js($cs_email_counter);?>").hide();
							$("#message<?php echo esc_js($cs_email_counter);?>").addClass('succ_mess');
							$("#message<?php echo esc_js($cs_email_counter)?>").show();
							$("#message<?php echo esc_js($cs_email_counter);?>").html(response.message);
						}
						
					}
				});
			}
    	 </script>
         
        <?php 
		
		if ( trim($cs_contact_animation) !='' ) {
			$cs_contact_animation	= 'wow'.' '.$cs_contact_animation;
		} else {
			$cs_contact_animation	= '';
		}
		
		$html	.= '<div class="'.$cs_contact_animation.' '.$cs_contact_class.' '.$cs_view_class.' cs_form_styling">';
		$html	.= '<div class="form-style" id="contact_form'.$cs_email_counter.'">';
		$html	.= '<form id="frm'.$cs_email_counter.'" name="frm'.$cs_email_counter.'" method="post" action="javascript:frm_submit'.$cs_email_counter.'(\''.admin_url("admin-ajax.php").'\');" >';
		
		if ( isset( $cs_contactus_label ) && $cs_contactus_label == 'on' ) {
			$html	.= '';
		}
		if ( isset( $cs_contactus_label ) && $cs_contactus_label <> 'on' ) {
			$html	.= '';
		}
		$html	.= '<label> <i class="icon-user9"></i> <input type="text" name="contact_name" onfocus="if(this.value == \''.__('Name','lassic').'\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \''.__('Name','lassic').'\'; }" value="'.__('Name','lassic').'" class="'.sanitize_html_class($cs_class).'" required /></label>';
		if ( isset( $cs_contactus_vacancies ) && $cs_contactus_vacancies == 'on' ) {
			$html	.= '<label> <i class="icon-user9"></i> <input type="text" name="contact_lastname" onfocus="if(this.value == \'Apellido\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Apellido\'; }" value="Apellido" class="'.sanitize_html_class($cs_class).'" required /></label>';
		}
		$html	.= '<label> <i class="icon-envelope4"></i> <input type="email" name="contact_email" onfocus="if(this.value == \'Email: ejemplo@ejemplo.com\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Email: ejemplo@ejemplo.com\'; }" value="Email: ejemplo@ejemplo.com" class="'.sanitize_html_class($cs_class).'" required /></label>';
		
		$html	.= '<label> <i class="icon-globe4"></i> <input type="text" name="subject" onfocus="if(this.value == \''.__('Subjet','lassic').'\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \''.__('Subjet','lassic').'\'; }" value="'.__('Subjet','lassic').'" class="'.sanitize_html_class($cs_class).'" required /></label>';
		
		$html	.= '<label> <i class="icon-phone8"></i> <input type="text" name="contact_number" onfocus="if(this.value == \''.__('Phone','lassic').'\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \''.__('Phone','lassic').'\'; }" value="'.__('Phone','lassic').'" class="'.sanitize_html_class($cs_class).'" required /></label>';
		
		if ( isset( $cs_contactus_vacancies ) && $cs_contactus_vacancies == 'on' ) {
			$html	.= '<label> <i class="icon-user9"></i> <input type="text" name="contact_birthdate" onfocus="if(this.value == \'Fecha de Nacimiento\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Fecha de Nacimiento\'; }" value="Fecha de Nacimiento" class="'.sanitize_html_class($cs_class).'" required /></label>';
			$html	.= '<label> <i class="icon-user9"></i> <input type="text" name="contact_city" onfocus="if(this.value == \'Ciudad\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Ciudad\'; }" value="Ciudad" class="'.sanitize_html_class($cs_class).'" required /></label>';
			$html	.= '<label> <i class="icon-user9"></i> <input type="text" name="contact_country" onfocus="if(this.value == \'País\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'País\'; }" value="País" class="'.sanitize_html_class($cs_class).'" required /></label>';
			$html	.= '<label> <i class="icon-user9"></i> <input type="text" name="contact_interest" onfocus="if(this.value == \'Área de Interés\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Área de Interés\'; }" value="Área de Interés" class="'.sanitize_html_class($cs_class).'" required /></label>';
			$html	.= '<label> <i class="icon-user9"></i> <input type="text" name="contact_education" onfocus="if(this.value == \'Educación Formal\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Educación Formal\'; }" value="Educación Formal" class="'.sanitize_html_class($cs_class).'" required /></label>';
			$html	.= '<label> <i class="icon-user9"></i> <input type="text" name="contact_studies" onfocus="if(this.value == \'Estudios Universitarios\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Estudios Universitarios\'; }" value="Estudios Universitarios" class="'.sanitize_html_class($cs_class).'" required /></label>';
		}
		
		// reCAPTCHA
		$html	.= '<div class="g-recaptcha" data-sitekey="6Le7lgYTAAAAAHv4nen0SGCUT780SE158YkhxODx"></div>';
		
		if ( isset( $cs_contactus_vacancies ) && $cs_contactus_vacancies == 'on' ) {
		    $html	.= '<label class="textaera-sec"> <i class="icon-quote4"></i> <textarea placeholder="CV" class="'.sanitize_html_class($cs_class).' '.sanitize_html_class($cs_view_class).'" name="contact_msg" style="height: 754px !important;" required></textarea></label>';
		}
		else {
			if ( isset( $cs_contactus_label ) && $cs_contactus_label == 'on' ) {
				$html	.= '<label class="textaera-sec"> <i class="icon-quote4"></i> <textarea placeholder="'.__('Mensaje', 'lassic').'" class="'.sanitize_html_class($cs_class).' '.sanitize_html_class($cs_view_class).'" name="contact_msg" required></textarea></label>';
			}
			else{
				$html	.= '<label class="textaera-sec"><i class="icon-comments-o"></i><textarea placeholder="'.__('Mensaje', 'lassic').'" class="'.sanitize_html_class($cs_class).' '.sanitize_html_class($cs_view_class).'" name="contact_msg" required></textarea></label>';
			}
		}
		$html	.= '<label class="submit-sec"><span><i class="icon-checkmark6"></i><input type="submit" value="Enviar" class="custom-btn cs-bg-color" id="submit_btn'.$cs_email_counter.'"></span></label>';
		$html	.= '</form>';
		$html	.= '<div id="loading_div'.$cs_email_counter.'"></div>';
		$html	.= '<div id="message'.$cs_email_counter.'"  style="display:none;"></div>';
		$html	.= '</div>';
		$html	.= '</div>';
		
		$cs_contact_class_id = '';
		if($cs_contact_class <> ''){
			$cs_contact_class_id = ' id="'.$cs_contact_class.'"';
		}
		return '<div class="'.$cs_column_class.'"'.$cs_contact_class_id.'>'.$cs_section_title.$html.'</div>';
	}
	add_shortcode('cs_contactus', 'cs_contactus_shortcode');
}
// adding Contact Us Form  End

//=====================================================================
// Adding message start
//=====================================================================
if (!function_exists('cs_message_shortcode')) {
	function cs_message_shortcode($cs_atts, $content = "") {
		$cs_defaults = array( 'cs_column_size' => '1/1', 'cs_msg_section_title' => '', 'cs_message_title' => '','cs_message_type' => '','cs_alert_style' => '','cs_style_type' => '', 'cs_message_icon' => '','cs_title_color' => '','cs_message_box_title' => '','cs_background_color' => '','cs_text_color' => '','cs_button_text' => '','cs_button_link' => '','cs_icon_color' => '','cs_message_close' => '','cs_message_class' => '','cs_message_animation' => '','cs_message_animation_duration' => '');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) ); 
		$html = '';
		$cs_column_class	= cs_custom_column_class($cs_column_size);
		$cs_section_title = '';
		
		if ( trim($cs_message_animation) !='' ) {
			$cs_message_animation	= ' wow'.' '.$cs_message_animation;
		} else {
			$cs_message_animation	= '';
		}
		
		if ($cs_msg_section_title && trim($cs_msg_section_title) !='' ) {
			$html .= '<div class="cs-section-title"><h2>'.$cs_msg_section_title.'</h2></div>';
		}
		
		if(isset($cs_message_type) && $cs_message_type == 'alert'){
			if(isset($cs_style_type) && $cs_style_type == 'fancy'){
				$html .= '
				<div class="messagebox alert alert-info align-left" style=" background-color:'.$cs_background_color.'; border:1px solid '.$cs_icon_color.';">';
				if(isset($cs_message_close) && $cs_message_close == 'yes'){
				$html .= '<button type="button" class="close" data-dismiss="alert"><em class="icon-times"></em></button>';
				}
				if(isset($cs_message_icon) && $cs_message_icon <> ''){
				$html .= '<i style="color:'.$cs_icon_color.';" class="'.$cs_message_icon.'"></i>';
				}
				$html .= '
				<span style="color:'.$cs_text_color.';">'.do_shortcode($cs_message_title).' <a style="color:'.$cs_text_color.';">'.do_shortcode($content).'</a></span> 
				</div>';
			}
			else{
				$html .= '
				<div class="messagebox strokebox alert alert-info align-left" style=" background:'.$cs_background_color.';">';
				if(isset($cs_message_close) && $cs_message_close == 'yes'){
				$html .= '<button type="button" class="close" data-dismiss="alert"><em class="icon-times"></em></button>';
				}
				if(isset($cs_message_icon) && $cs_message_icon <> ''){
				$html .= '<i style="color:'.$cs_icon_color.';" class="'.$cs_message_icon.'"></i>';
				}
				$html .= '
				<span style="color:'.$cs_text_color.';">'.do_shortcode($cs_message_title).'<a>'.do_shortcode($content).'</a></span> 
				</div>';
			}
		}
		else{
			$html .= '
			<div style="background:'.$cs_background_color.';" class="messagebox-v1 alert alert-info has_pattern icon_position_left no_border has_bgicon">';
				if(isset($cs_message_close) && $cs_message_close == 'yes'){
				$html .= '<button data-dismiss="alert" class="close" type="button" style="color:'.$cs_title_color.';"><em class="icon-times"></em></button>';
				}
				if(isset($cs_message_icon) && $cs_message_icon <> ''){
				$html .= '<i style="color:'.$cs_icon_color.';" class="'.$cs_message_icon.'"></i>';
				}
				if(isset($cs_message_box_title) && $cs_message_box_title <> ''){
				$html .= '<span style="color:'.$cs_title_color.';">'.do_shortcode($cs_message_box_title).'</span>';
				}
				$html .= '<p style="color:'.$cs_text_color.';">'.do_shortcode($content).'</p>';
				if(isset($cs_button_text) && $cs_button_text <> ''){
				$html .= '<button class="custom-btn cs-bg-color" onclick="javascript:location.href=\''.$cs_button_link.'\'">'.$cs_button_text.'</button>';
				}
			$html .= '
			</div>';
		}
		
		$cs_message_class_id = '';
		if($cs_message_class <> ''){
			$cs_message_class_id = ' id="'.$cs_message_class.'"';
		}
		return do_shortcode('<div class="'.$cs_column_class.$cs_message_animation.'"'.$cs_message_class_id.'>'.$html.'</div>');
	}
	add_shortcode('cs_message', 'cs_message_shortcode');
}
// adding message end

//=====================================================================
// Adding Testimonial start
//=====================================================================
if (!function_exists('cs_testimonials_shortcode')) {
	function cs_testimonials_shortcode( $cs_atts, $content = null ) {
		global $cs_testimonial_style,$cs_testimonial_class,$cs_column_class,$cs_testimonial_text_color,$cs_section_title;
		$cs_randomid = rand(0,999);
		//cs_owl_carousel();
		$cs_defaults = array('cs_column_size'=>'1/1','cs_testimonial_style'=>'','cs_testimonial_text_color'=>'','cs_testimonial_text_align'=>'','cs_testimonial_section_title'=>'','cs_testimonial_class'=>'','cs_testimonial_animation'=>'','cs_testimonial_animation_duration' => '');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class = cs_custom_column_class($cs_column_size);
		$html = '';
		$cs_section_title = '';
		
		if ( trim($cs_testimonial_animation) !='' ) {
			$cs_testimonial_animation	= ' wow'.' '.$cs_testimonial_animation;
		} else {
			$cs_testimonial_animation	= '';
		}
		
		if ($cs_testimonial_section_title && trim($cs_testimonial_section_title) !='' ) {
			$cs_section_title	= '<div class="cs-section-title"><h2>'.$cs_testimonial_section_title.'</h2></div>';
		}
		if($cs_testimonial_style == 'fancy' || $cs_testimonial_style == 'slider'){
		cs_enqueue_flexslider_script();
		?>
		<script type='text/javascript'>
			jQuery(document).ready(function(){
				jQuery("#cs-testimonial-<?php echo cs_allow_special_char($cs_randomid); ?>").flexslider({
					animation: 'fade',
					slideshow: true,
					controlNav: true,
					directionNav: true,
					slideshowSpeed: 7000,
					animationDuration: 600,
					prevText:"<em class='icon-angle-left'></em>",
					nextText:"<em class='icon-angle-right'></em>",
					start: function(slider) {
						jQuery('.cs-testimonial').fadeIn();
					}
				});
			});
		</script>
		<?php
		}
		if(isset($cs_testimonial_style) && $cs_testimonial_style == 'fancy'){
			$cs_testim_clas = ' box-style';
			$html .= '
			' .$cs_section_title. '
			<div id="cs-testimonial-'.cs_allow_special_char($cs_randomid).'" class="flexslider testimonial '.$cs_testimonial_text_align.$cs_testim_clas.'">
 				<ul class="slides">
				' . do_shortcode( $content ) . ' 
				</ul>
			</div>';
		}
		else if(isset($cs_testimonial_style) && $cs_testimonial_style == 'slider'){
			$cs_testim_clas = ' testimonial-slider';
			$html .= '
			' .$cs_section_title. '
			<div id="cs-testimonial-'.cs_allow_special_char($cs_randomid).'" class="flexslider testimonial '.$cs_testimonial_text_align.$cs_testim_clas.'">
				<div class="flex-viewport">
				  <ul class="slides">
				  ' . do_shortcode( $content ) . ' 
				  </ul>
				</div>
			</div>';
		}
		else{
			cs_owl_carousel();
			$cs_owlcount = rand(543, 464554);
			$html .= '<script type="text/javascript">
			  	jQuery(document).ready(function($) {
					jQuery(\'#cs-testimonial-carousel-'.cs_allow_special_char($cs_owlcount).'\').owlCarousel({
					  nav: true,
					  margin: 30,
					  navText: [
						"<i class=\' icon-angle-left\'></i>",
						"<i class=\'icon-angle-right\'></i>"
					  ],
				  responsive: {
					0: {
					  items: 1 // In this configuration 1 is enabled from 0px up to 479px screen size 
					},
					480: {
					  items: 2, // from 480 to 677 
					  nav: false // from 480 to max 
					},
		
					678: {
					  items: 2, // from this breakpoint 678 to 959
					  center: false // only within 678 and next - 959
					},
					960: {
					  items: 2, // from this breakpoint 960 to 1199
					  center: false,
					  loop: false
		
					},
					1200: {
					  items: 2
					}
				  }
				});
			  });
			</script>';
			$cs_testim_clas = ' italic-style';
			$html .= '
			' .$cs_section_title. '
			<div id="cs-testimonial-carousel-'.cs_allow_special_char($cs_owlcount).'" class="testimonial cs-prv-next '.$cs_testimonial_text_align.$cs_testim_clas.'">
				' . do_shortcode( $content ) . '
			</div>';
		}
		
		return '<div class="'.$cs_column_class.$cs_testimonial_animation.'">'.$html.'</div>';
	}
	add_shortcode( 'cs_testimonials', 'cs_testimonials_shortcode' );
}
// adding Testimonial end

//=====================================================================
// Adding Testimonial Item start
//=====================================================================

if (!function_exists('cs_testimonial_item')) {
	function cs_testimonial_item( $cs_atts, $content = null ) {
		global $cs_testimonial_style,$cs_testimonial_class,$cs_column_class,$cs_testimonial_text_color;
		$cs_defaults = array('cs_testimonial_author' =>'','cs_testimonial_img' => '','cs_testimonial_text_align'=>'','cs_testimonial_company' => '');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_figure = '';
		
		if(isset($cs_testimonial_img) && $cs_testimonial_img <> ''){
			$cs_figure = '<figure><img src="'.$cs_testimonial_img.'" alt="" /></figure>';
		}
		$cs_tc_color = '';
		if(isset($cs_testimonial_text_color) && $cs_testimonial_text_color <> ''){
			$cs_tc_color = 'style=color:'.$cs_testimonial_text_color.'!important';
		}
		
		if(isset($cs_testimonial_style) && $cs_testimonial_style == 'fancy'){
			
	    return '<li>
				<div class="cs-testimonial boxed">
					<div class="skew-style"></div>
					<div class="question-mark">
						<p '.$cs_tc_color.'>'. do_shortcode( $content ) .'</p>
					</div>
					'.$cs_figure.'
					<h3 class="cs-author">'.$cs_testimonial_author.'<br>
					  <span>'.$cs_testimonial_company.'</span>
					</h3>
				</div>
			</li>';
		
		} else if(isset($cs_testimonial_style) && $cs_testimonial_style == 'slider'){
			return '<li>
						<div class="cs-testimonial">
							<div class="skew-style"></div>
							<div class="question-mark">
								<p '.$cs_tc_color.'>'. do_shortcode( $content ) .'</p>
							</div>
							'.$cs_figure.'
							<h3 class="cs-author">'.$cs_testimonial_author.'<br>
							  <span>'.$cs_testimonial_company.'</span>
							</h3>
						</div>
					</li>';
		} else {
			
			 return '<div class="cs-testimonial center">
							<div class="skew-style"></div>
							<div class="question-mark">
								<p '.$cs_tc_color.'>'. do_shortcode( $content ) .'</p>
							</div>
							'.$cs_figure.'
							<h3 class="cs-author">'.$cs_testimonial_author.'<br>
							  <span>'.$cs_testimonial_company.'</span>
							</h3>
						</div>';
		} 
	}
	add_shortcode( 'testimonial_item', 'cs_testimonial_item' );
}
// adding Testimonial Item end