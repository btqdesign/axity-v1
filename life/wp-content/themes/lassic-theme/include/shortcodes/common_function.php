<?php
/**
 * File Type: Common Elements Shortcode Functions
 */

//======================================================================
// Adding pricetable
//======================================================================
if (!function_exists('cs_pricetable_shortcode')) {
	function cs_pricetable_shortcode($cs_atts, $content = "") {
		global $cs_pricetable_style;
		$cs_defaults = array('cs_column_size'=>'1/1','cs_pricetable_style'=>'simple','cs_pricetable_title'=>'','cs_pricetable_title_bgcolor'=>'','cs_pricetable_desc'=>'','cs_pricetable_price'=>'','cs_pricetable_img'=>'','cs_pricetable_period'=>'','cs_pricetable_bgcolor'=>'','cs_btn_text'=>'','cs_btn_bg_color'=>'','cs_pricetable_featured'=>'','cs_pricetable_class'=>'','cs_pricetable_animation'=>'');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		
		$cs_CustomId	= '';
		if ( isset( $cs_pricetable_class ) && $cs_pricetable_class ) {
			$cs_CustomId	= 'id="'.$cs_pricetable_class.'"';
		}
		
		if ( trim($cs_pricetable_animation) !='' ) {
			$cs_pricetable_animation	= 'wow'.' '.$cs_pricetable_animation;
		} else {
			$cs_pricetable_animation	= '';
		}
		
		$cs_pricetableViewClass = '';
		
		 if(isset($cs_pricetable_style) && $cs_pricetable_style == 'simple'){
			$cs_pricetableViewClass = 'pr-simple';
			$cs_title_color = '#fff';
		} else {
			$cs_pricetableViewClass = 'pr-classic';
			$cs_title_color = '#fff';
		}
		
		$html = '';

		$cs_bgcolor_style = '';
		
		if(isset($cs_btn_bg_color) && trim($cs_btn_bg_color) <> ''){
			$cs_btn_bg_color = ' style="background-color:'.$cs_btn_bg_color.'"';
		}
		
		if(isset($cs_pricetable_bgcolor) && trim($cs_pricetable_bgcolor) <> ''){
			$cs_bgcolor_style = ' style="background-color:'.$cs_pricetable_bgcolor.'"';
		}
		
		if(isset($cs_pricetable_featured) && $cs_pricetable_featured == 'Yes'){
			$cs_featured = 'featured';
		} else {
			$cs_featured = '';
		}
			
			$html .= '<div class="col-md-12"><article class="cs-price-table '.$cs_pricetableViewClass.' '.$cs_pricetable_animation.' '.$cs_pricetable_class.' '.$cs_featured.'">';
			if(isset($cs_pricetable_title) && $cs_pricetable_title !=''){
				$html .= '<h3 style=" background-color:'.$cs_pricetable_title_bgcolor.' !important;">'.$cs_pricetable_title .'</h3>';
			}
			
			$cs_btn_text = $cs_btn_text ? $cs_btn_text : 'Sign Up';
			$html .= '<div class="cs-price" '.$cs_bgcolor_style.'><div class="inner-sec">';
			
			if(isset($cs_pricetable_img) && $cs_pricetable_img !=''){
				$html .= '<figure><img src="'.$cs_pricetable_img.'"></figure>';
			}
			
			if(isset($cs_pricetable_price) && $cs_pricetable_price !=''){
				$html .= $cs_pricetable_price;
			}
			
			if(isset($cs_pricetable_period) && $cs_pricetable_period !=''){
				$html .= '<small>'.$cs_pricetable_period.'</small>';
			}
 			$html .= '</div>';
 			$html .='</div>';
			if(isset($cs_pricetable_desc) && $cs_pricetable_desc !=''){
				$html .= '<p>'.do_shortcode($cs_pricetable_desc).'</p>';
			}
			$html .= '<ul class="features">';
			$html .= do_shortcode($content);
			$html .= '</ul>';
			$html .= ' <a class="sigun_up" href="" '.$cs_btn_bg_color.'>'.$cs_btn_text.'</a>';
			$html .= '</article></div>';
			return '<div '.$cs_CustomId.'>'.$html.'</div>';
		//}
	}
	add_shortcode('cs_pricetable', 'cs_pricetable_shortcode');
}

//======================================================================
// Pricing Item
//======================================================================
if (!function_exists('cs_pricing_item')) {
	function cs_pricing_item($cs_atts, $content = "") {
		global $cs_pricetable_style;
		$cs_defaults = array('cs_pricing_feature' => '');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$html	 = '';
		$cs_priceCheck	= '';
		if ( $cs_pricetable_style =='simple' ) {
			$cs_priceCheck	= '<i class="icon-check"></i>';
		}
		
		if ( isset( $content ) && $content !='' ){
			$html	.= '<li>'.$cs_priceCheck.do_shortcode($content).'</li>';
		}
		
		return $html;
	}
	add_shortcode('pricing_item', 'cs_pricing_item');
}

//======================================================================
//Table Start
//======================================================================
if (!function_exists('cs_table_shortcode_func')) {
	function cs_table_shortcode_func($cs_atts, $content = "") {
		global $cs_table_style;
		$cs_defaults = array('cs_table_style'=>'modern','cs_table_section_title'=>'','cs_column_size'=>'1/1','cs_table_class'=>'','cs_table_animation'=>'','cs_table_custom_animation_duration'=>'1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		
		$cs_CustomId	= '';
		if ( isset( $cs_table_class ) && $cs_table_class ) {
			$cs_CustomId	= 'id="'.$cs_table_class.'"';
		}
		
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		
		if ( trim($cs_table_animation) !='' ) {
			$cs_table_animation	= 'wow'.' '.$cs_table_animation;
		} else {
			$cs_table_animation	= '';
		}

		$cs_section_title = '';
		
		if(isset($cs_table_section_title) && trim($cs_table_section_title) <> ''){
			$cs_section_title = '<div class="cs-section-title"><h2>'.esc_attr($cs_table_section_title).'</h2></div>';
		}
		return '<div '.$cs_CustomId.' class="'.sanitize_html_class($cs_column_class).' '.sanitize_html_class($cs_table_class).' '.sanitize_html_class($cs_table_animation).'">'.$cs_section_title.do_shortcode($content).'</div>';
	}
	add_shortcode('cs_table', 'cs_table_shortcode_func');
}

//======================================================================
// Adding table
//======================================================================

if (!function_exists('cs_table_shortcode')) {
	function cs_table_shortcode($cs_atts, $content = "") {
		global $cs_table_style;
		$cs_defaults = array('cs_column_size'=>'1/1','cs_table_section_title'=>'','cs_table_content'=>'','cs_table_class'=>'','cs_table_animation'=>'');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$content = str_replace('<br />','',$content);
		$cs_table_data = '';
		$cs_class = '';
		if($cs_table_style == 'classic'){
			$cs_class = 'table tablev2';
		}else if( $cs_table_style == 'modren' ) {
			$cs_class = 'table tablev1';
		}
		/*if(isset($color) && $color <> ''){
			$cs_table_class = "table_" . rand(55,6555);
		}*/
		return $cs_table_data . '<table class="'.sanitize_html_class($cs_class).'">'.do_shortcode($content).'</table>';
	}
	add_shortcode('table', 'cs_table_shortcode');
}

//======================================================================
// Table Head
//======================================================================
if (!function_exists('cs_table_body_shortcode')) {
	function cs_table_body_shortcode($cs_atts, $content = "") {
		$cs_defaults = array();
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		return '<tbody>'.do_shortcode($content).'</tbody>';
	}
	add_shortcode('tbody', 'cs_table_body_shortcode');
}

//======================================================================
// Table Head
//======================================================================
if (!function_exists('cs_table_head_shortcode')) {
	function cs_table_head_shortcode($cs_atts, $content = "") {
		$cs_defaults = array();
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		return '<thead>'.do_shortcode($content).'</thead>';
	}
	add_shortcode('thead', 'cs_table_head_shortcode');
}

//======================================================================
// Table Row
//======================================================================
if (!function_exists('cs_table_row_shortcode')) {
	function cs_table_row_shortcode($cs_atts, $content = "") {
		$cs_defaults = array();
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		return '<tr>'.do_shortcode($content).'</tr>';
	
	}
	add_shortcode('tr', 'cs_table_row_shortcode');
}

//======================================================================
// Table Heading
//======================================================================
if (!function_exists('cs_table_heading_shortcode')) {
	function cs_table_heading_shortcode($cs_atts, $content = "") {
		$cs_defaults = array();
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$html	 = '';
		$html	.= '<th>';
		$html	.= do_shortcode($content);
		$html	.= '</th>';
		
		return $html;
	}
	add_shortcode('th', 'cs_table_heading_shortcode');
}

//======================================================================
// Table data
//======================================================================
if (!function_exists('cs_table_data_shortcode')) {
	function cs_table_data_shortcode($cs_atts, $content = "") {
		$cs_defaults = array();
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		return '<td>'.do_shortcode($content).'</td>';
	}
	add_shortcode('td', 'cs_table_data_shortcode');
}

//======================================================================
// adding accordion
//======================================================================
if (!function_exists('cs_accordion_shortcode')) {
	function cs_accordion_shortcode($cs_atts, $content = "") {
		global $cs_acc_counter,$cs_accordian_style;
		$cs_acc_counter = rand(40, 9999999);;
		$html	= '';
		$cs_defaults = array('cs_column_size'=>'1/1', 'cs_class' => 'cs-accrodian','cs_accordian_style' => '','cs_accordion_class' => '','cs_accordion_animation' => '','cs_accordian_section_title'=>'');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		
		$cs_CustomId	= '';
		if ( isset( $cs_accordion_class ) && $cs_accordion_class ) {
			$cs_CustomId	= 'id="'.$cs_accordion_class.'"';
		}
		
		if ( trim($cs_accordion_animation) !='' ) {
			$cs_accordion_animation	= 'wow'.' '.$cs_accordion_animation;
		} else {
			$cs_accordion_animation	= '';
		}
		$cs_section_title = '';
		if(isset($cs_accordian_section_title) && trim($cs_accordian_section_title) <> ''){
			$cs_section_title = '<div class="cs-section-title"><h2>'.$cs_accordian_section_title.'</h2></div>';
		}
		if ( $cs_accordian_style == 'default' ) {
			$cs_styleClass	= 'csdefault';
		}else{
			$cs_styleClass	= 'box';
		}
		$html   .= '<div '.$cs_CustomId.' class="'.sanitize_html_class($cs_column_class).'">';
		$html	.= '<div class="panel-group '.$cs_styleClass.' '.$cs_accordion_class.' '.$cs_accordion_animation.'" id="accordion-' . $cs_acc_counter . '">'.$cs_section_title.do_shortcode($content).'</div>';
		$html	.= '</div>';
		return $html;
	}
	
	add_shortcode('cs_accordian', 'cs_accordion_shortcode');
}

//======================================================================
// Adding accordion item start
//======================================================================
if (!function_exists('cs_accordion_item_shortcode')) {
	function cs_accordion_item_shortcode($cs_atts, $content = "") {
		global $cs_acc_counter,$cs_accordian_style,$cs_accordion_animation;
		$cs_defaults = array( 'cs_accordion_title' => 'Title','cs_accordion_active' => '','cs_accordian_icon' => '');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_accordion_count = 0;
		$cs_accordion_count = rand(40, 9999999);
		$html = "";
		$cs_active_in = '';
		$cs_active_class = '';
		$cs_styleColapse = 'collapse collapsed';
		
		if(isset($cs_accordion_active) && $cs_accordion_active == 'yes'){
			$cs_active_in = 'in';
			$cs_styleColapse = '';
		}
		else{
			$cs_active_class = 'collapsed';
		}
		$cs_faq_style = '';
		
		$cs_accordian_icon_class = '';
		if(isset($cs_accordian_icon)){
			$cs_accordian_icon = '<i class="'.sanitize_html_class($cs_accordian_icon).'"></i>';
		}
		$html = '<div class="panel panel-default">
					<div class="panel-heading">
					  <h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion-'.$cs_acc_counter.'" href="#accordion-'.$cs_accordion_count.'" class="'.sanitize_html_class($cs_active_class).'">
						   ' . $cs_accordian_icon . $cs_accordion_title . '
						</a>
					  </h4>
					</div>
					<div id="accordion-'.$cs_accordion_count.'" class="panel-collapse collapse '.$cs_active_in.' ">
					  <div class="panel-body"><p>'.$content.'</p></div>
					</div>
				  </div>';
		return $html;
	}
	add_shortcode('accordian_item', 'cs_accordion_item_shortcode');
}

//======================================================================
// Tabs Shortcodes 
//======================================================================
if (!function_exists('cs_tabs_shortcode')) {
	function cs_tabs_shortcode( $cs_atts, $content = null ) {
		global $cs_tabs_content;
		$cs_tabs_content = '';
		extract(shortcode_atts(array(  
			'cs_tab_style' => '',
			'cs_tabs_class' => '',
			'cs_column_size'=>'1/1', 
			'cs_tabs_section_title' => '',
			'cs_tabs_animation' => '',
			'cs_custom_animation_duration' => ''
		), $cs_atts));  
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		
		$cs_CustomId	= '';
		if ( isset( $cs_tabs_class ) && $cs_tabs_class ) {
			$cs_CustomId	= 'id="'.$cs_tabs_class.'"';
		}
		
		if ( trim($cs_tabs_animation) !='' ) {
			$cs_tabs_animation	= 'wow'.' '.$cs_tabs_animation;
		} else {
			$cs_tabs_animation	= '';
		}
		$cs_randid = rand(8,9999);
		$cs_section_title = '';
		$cs_tabs_output = '';
		
		if ( isset($cs_tabs_section_title) && trim($cs_tabs_section_title) !='' ) {
			$cs_section_title	= '<div class="cs-heading"><h2>'.$cs_tabs_section_title.'</h2></div>';
		}
		
		if($cs_tab_style == 'vertical'){
			$cs_tabs_class = 'cs-tabs vertical';
		}else if($cs_tab_style == 'box'){
			$cs_tabs_class = 'cs-tabs box';
		}else{
			$cs_tabs_class = 'cs-tabs modren-view';
		}
		
		$cs_tabs_output .= '<div class="'.sanitize_html_class($cs_tabs_class).' '.$cs_tabs_animation.'"  id="cstabs'.$cs_randid.'">';
		$cs_tabs_output .= $cs_section_title;
		$cs_tabs_output .= '<ul class="nav nav-tabs" > ';
		$cs_tabs_output .= force_balance_tags ( do_shortcode($content) );
		$cs_tabs_output .= '</ul>';
		$cs_tabs_output .= '<div class="tab-content">'.$cs_tabs_content.'</div>';
		$cs_tabs_output .= '</div>';
		return '<div '.$cs_CustomId.' class="'.sanitize_html_class($cs_column_class).' '.$cs_tabs_class.'">'.$cs_tabs_output.'</div>';  
	}  
	add_shortcode('cs_tabs', 'cs_tabs_shortcode');
}

//======================================================================
// Tab Items 
//======================================================================
if (!function_exists('cs_tab_item_shortcode')) {
	function cs_tab_item_shortcode($cs_atts, $content = null) {  
		global $cs_tabs_content;
		extract(shortcode_atts(array(  
			'cs_tab_icon' => '',
			'cs_tab_title' => '',
			'cs_tab_icon' => '',
			'cs_tab_active'=>'no' 
		), $cs_atts));  
		$cs_activeClass = $cs_tab_active == 'yes' ? 'active in' :'';
		$cs_fa_icon='';
		if($cs_tab_icon){
			$cs_fa_icon = '<i class="'.sanitize_html_class($cs_tab_icon).'"></i> ';
		}
		$cs_randid = rand(877,9999);
		$cs_output = ' <li class="'.sanitize_html_class($cs_activeClass).'"> <a href="#cs-tab-'.sanitize_title($cs_tab_title).$cs_randid.'"  data-toggle="tab">'.$cs_fa_icon.$cs_tab_title.'</a></li>';
		$cs_tabs_content.= '<div class="tab-pane fade '.$cs_activeClass.'" id="cs-tab-'.sanitize_title($cs_tab_title).$cs_randid.'">'.force_balance_tags ( do_shortcode($content) ).'</div>';
		return $cs_output;
	}
	add_shortcode( 'tab_item', 'cs_tab_item_shortcode' );
}

//======================================================================
// Toggle Start
//======================================================================
if (!function_exists('cs_toggle_shortcode')) {
	function cs_toggle_shortcode($cs_atts, $content = "") {
		$cs_defaults = array( 'cs_column_size'=>'1/1','cs_toggle_section_title' => '','cs_toggle_title' => '','cs_toggle_state' => '','cs_toggle_icon' => '','cs_toggle_custom_class' => '','cs_toggle_custom_animation' => '','cs_toggle_custom_animation_duration' => '1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_toggle_counter = rand(1,99999);
		$cs_active = "";
		$cs_collapse = "collapsed";
		$cs_toggle_icon_class = '';
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		
		$cs_CustomId	= '';
		if ( isset( $cs_toggle_custom_class ) && $cs_toggle_custom_class ) {
			$cs_CustomId	= 'id="'.$cs_toggle_custom_class.'"';
		}
		
		if ( trim($cs_toggle_custom_animation) !='' ) {
			$cs_toggle_custom_animation	= 'wow'.' '.$cs_toggle_custom_animation;
		} else {
			$cs_toggle_custom_animation	= '';
		}
		
		if ( $cs_toggle_state == "open" ){ $cs_active = "in";}
		if ( $cs_toggle_icon <> "" ){ $cs_toggle_icon_class = '<i class="'.$cs_toggle_icon.'"></i>';}
		$cs_section_title = '';
		if(isset($cs_toggle_section_title) && trim($cs_toggle_section_title) <> ''){
			$cs_section_title = '<div class="cs-section-title"><h2>'.$cs_toggle_section_title.'</h2></div>';
		}
		$html = '<div class="panel-group" id="#accordion' . $cs_toggle_counter . '">
				  <div class="panel panel-default">
					<div class="panel-heading">
					  <h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion' . $cs_toggle_counter . '" href="#toggle' . $cs_toggle_counter . '">
						  '.$cs_toggle_icon_class.$cs_toggle_title.'
						</a>
					  </h4>
					</div>
					<div id="toggle' . $cs_toggle_counter . '" class="panel-collapse collapse '.$cs_active.'">
					  <div class="panel-body">
					   ' . do_shortcode($content) . '
					  </div>
					</div>
				  </div>
				';
		
		return '<div '.$cs_CustomId.' class="'.$cs_column_class.'">'.$cs_section_title.'<div class="'.$cs_toggle_custom_class.' '.$cs_toggle_custom_animation.'" style="animation-duration: '.$cs_toggle_custom_animation_duration.'s;">'.do_shortcode($html) . '</div></div>';
	}
	add_shortcode('cs_toggle', 'cs_toggle_shortcode');
}

//======================================================================
// button shortcode start
//======================================================================
if (!function_exists('cs_button_shortcode')) {
	function cs_button_shortcode($cs_atts) {
		$cs_defaults = array( 'cs_button_size'=>'btn-lg','cs_button_border' => '','cs_border_cs_button_color' => '','cs_button_title' => '','cs_button_link' => '#','cs_button_color' => '#fff','cs_button_bg_color' => '#000','cs_button_icon_position' => 'left','cs_button_icon'=>'', 'cs_button_type' => 'rounded','cs_button_target' => '_self','cs_button_class' => '','cs_button_animation' => '');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_CustomId	= '';
		if ( isset( $cs_button_class ) && $cs_button_class ) {
			$cs_CustomId	= 'id="'.$cs_button_class.'"';
		}
		
		$cs_button_type_class = 'no_circle';
		 if ( trim($cs_button_animation) !='' ) {
			$cs_button_animation	= 'wow'.' '.$cs_button_animation;
		 } else {
			$cs_button_animation	= '';
		 }
		$cs_border	= '';
		$cs_has_icon = '';	
		
		if($cs_button_size =='btn-xlg'){
			$cs_button_size = 'extra-large-btn';
		}elseif($cs_button_size == 'btn-lg'){
			$cs_button_size = 'large-btn';
		}elseif($cs_button_size == 'btn-sml'){
			$cs_button_size = 'small-btn';
		}else{
			$cs_button_size = 'medium-btn';
		}
		
		if( isset($cs_button_border) && $cs_button_border == 'yes' ){
			$cs_border = ' border: 2px solid '.$cs_border_cs_button_color.';';	
		}
		
		if(isset($cs_button_type) && $cs_button_type == 'rounded'){
			$cs_button_type_class = 'circle';
		}
		if(isset($cs_button_type) && $cs_button_type == 'three-d'){
			$cs_button_type_class = 'three-d has-shadow';
			$cs_border	= '';
		}

		if(isset($cs_button_icon) && $cs_button_icon <> ''){
			$cs_has_icon = 'has_icon';	
		}
		
		$html  = '';
		$html .= '<div '.$cs_CustomId.' class="button_style">';
		
		$html .= '<a href="' . $cs_button_link. '" class="default '.$cs_button_type_class. ' ' . $cs_button_size. ' bg-color ' . $cs_button_class. ' ' . $cs_button_animation. ' '.$cs_has_icon.'" style="'.$cs_border.'  background-color: ' . $cs_button_bg_color . '; color:' . $cs_button_color . ';">';
		if(isset($cs_button_icon) && $cs_button_icon <> ''){
			$html .= '<i class="'.$cs_button_icon.' button-icon-'. $cs_button_icon_position.'"></i>';
		}
		if(isset($cs_button_title) && $cs_button_title <> ''){
			$html .= $cs_button_title;
		}
		$html .= '</a>';
		$html .= '</div>';
		return do_shortcode($html);
	}
	add_shortcode('cs_button', 'cs_button_shortcode');
}

//======================================================================
// Number Counter Item Shortcode Start
//======================================================================
if (!function_exists('cs_counter_item_shortcode')) {
	function cs_counter_item_shortcode($cs_atts, $content = null) {
		global $cs_counter_style;
		extract(shortcode_atts(array(  
			'cs_column_size' => '1/1',
			'cs_counter_icon_type' => '',
			'cs_counter_logo' => '',
			'cs_counter_icon'=>'',
			'cs_counter_icon_align'=>'',
			'cs_counter_icon_color' => '#21cdec',
			'cs_counter_numbers' => '',
			'cs_counter_number_color' => '#333333',
			'cs_counter_title' => '',
			'cs_counter_link_url' => '',
			'cs_counter_text_color' => '#818181',
			'cs_counter_border' => '',
			'cs_counter_class' => '',
			'cs_counter_animation' => '',
			'cs_custom_animation_duration' => '1'
		 ), $cs_atts));
		 
		 $cs_column_class  = cs_custom_column_class($cs_column_size);
		 
		 $cs_CustomId	= '';
		 if ( isset( $cs_counter_class ) && $cs_counter_class ) {
			$cs_CustomId	= 'id="'.$cs_counter_class.'"';
		 }
		 
		 if ( trim($cs_counter_animation) !='' ) {
			$cs_counter_animation	= 'wow'.' '.$cs_counter_animation;
		 } else {
			$cs_counter_animation	= '';
		 }
			$cs_rand_id = rand(98,56666);
			$cs_output = '';
			$cs_counter_style_class = '';
			$cs_pattren_bg          = '';
			$cs_has_border 	= '';
			$cs_output = '';
			$cs_border_class =  '';
			
			cs_count_numbers_script();
			
			$cs_output .= '
				<script>
					jQuery(document).ready(function($){
						jQuery(".custom-counter-'.esc_js($cs_rand_id).'").counterUp({
							delay: 10,
							time: 1000
						});
					});	
				</script>';
			
			$cs_combine_cs_counter_icon = '';	
			
			$cs_counter_numbers = is_numeric($cs_counter_numbers) ? number_format($cs_counter_numbers) : $cs_counter_numbers;
				if($cs_counter_icon_type == 'icon' && $cs_counter_icon <> ''){
					$cs_combine_cs_counter_icon = '<i class="'.$cs_counter_icon.'" style=" color: '.$cs_counter_icon_color.'; "></i>';
				}
				else if($cs_counter_icon_type == 'image' && $cs_counter_logo <> ''){
					$cs_combine_cs_counter_icon = '<img src="'.$cs_counter_logo.'" alt="">';
				}
				
					$cs_counter_style_class = 'cs_counter classic '.$cs_counter_icon_align;
				
				$cs_output .= '<figure>';
							  
							  $cs_output .='
							  <figcaption>';
							  	$cs_output .= $cs_combine_cs_counter_icon;
								if($cs_counter_numbers <> ''){
								$cs_output .= '<a href="'.$cs_counter_link_url.'" class="cs-numcount custom-counter-'.$cs_rand_id.'" style=" color: '.$cs_counter_number_color.';">'.$cs_counter_numbers.'</a>';
								}
								
								if($cs_counter_title <> ''){
									$cs_output .= '<span style="color:'.$cs_counter_text_color.';">'.$cs_counter_title.'</span>';
								}
								
								$cs_output .= '<p>'.do_shortcode($content).'</p>';
								
								if($cs_counter_link_url <> ''){
									$cs_output .= '<a class="defualt small-btn" href="'.$cs_counter_link_url.'">'.__('Read More','lassic').'</a>';
								}
							    
								$cs_output .= '
							  </figcaption>
							</figure>';
							
							
				
			$html = '<div '.$cs_CustomId.' class="'.$cs_column_class.' '.$cs_counter_animation.'"><article  class="'.$cs_counter_style_class.' '.$cs_counter_class.''.$cs_border_class.'">'.$cs_output.'</article></div>';
		return $html;
	}
	add_shortcode( 'cs_counter', 'cs_counter_item_shortcode' );
}


//======================================================================
// Services item
//======================================================================
if (!function_exists('cs_services_shortcode')) {
	function cs_services_shortcode( $cs_atts, $content = null ) {
		global $service_type,$cs_servicesNode;
		
		$cs_defaults = array( 'cs_column_size'=>'1/2', 'cs_service_type' => '','cs_service_icon_type' => '','cs_service_icon' => '','cs_service_title_color' => '','cs_service_text_color' => '','cs_service_icon_color' => '','cs_service_bg_image' => '','cs_service_bg_color' => '','cs_service_icon_size' => '','cs_service_postion_modern' => '','cs_service_postion_classic' => '','cs_service_title'=>'','cs_service_content' => '','cs_service_link_text' => '', 'cs_service_link_color'=>'','cs_service_url' => '', 'cs_service_class'=>'','cs_service_animation' => '');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		
		$cs_serviceClass 	= '';
		$html		  	= '';
		$cs_bgColor		= '';
		$cs_bgColorClass	= '';
		$cs_align			= '';
		$cs_linkColor 		= '';
		$cs_LinkIcon	 	= '';
		
		$cs_CustomId	= '';
		if ( isset( $cs_service_class ) && $cs_service_class ) {
			$cs_CustomId	= 'id="'.$cs_service_class.'"';
		}
		if ( trim($cs_service_animation) !='' ) {
			$cs_service_animation	= 'wow'.' '.$cs_service_animation;
		} else {
			$cs_service_animation	= '';
		}
		
		if ( isset( $cs_service_link_text ) && $cs_service_link_text !='' ) {
			$cs_more		= $cs_service_link_text;
		} else{
			$cs_more	= 'Read More';
		}
		
		if ( isset( $cs_service_icon_color ) && $cs_service_icon_color !='' ) {
			$cs_iconColor		= 'style="color:'.$cs_service_icon_color.'"';
		} else{
			$cs_iconColor 		=	 '';
		}
		
		if ( isset( $cs_service_text_color ) && $cs_service_text_color !='' ) {
			$cs_servTextColor = 'style="color: '.$cs_service_text_color.' !important"';
		} else{
			$cs_servTextColor = '';
		}
		
		if ( isset( $cs_service_title_color ) && $cs_service_title_color !='' ) {
			$cs_servTitleColor = 'style="color: '.$cs_service_title_color.' !important"';
		} else{
			$cs_servTitleColor = '';
		}
		
		if ($cs_service_type == 'modern'){
			$cs_serviceClass   =	 'modren';
			$cs_align			= $cs_service_postion_modern;
			
			if ( isset( $cs_service_link_color ) && $cs_service_link_color !='' ) {
				$cs_linkColor = 'style="background-color: '.$cs_service_link_color.'"';
				$cs_linkTextColor = 'style="color: '.$cs_service_link_color.' !important"';
			} else{
				$cs_linkColor = '';
				$cs_linkTextColor = '';
			}
			
			if ( isset( $cs_service_bg_color ) && $cs_service_bg_color !='' ) {
				$cs_bgColor = 'style="background-color: '.$cs_service_bg_color.' !important;"';
				$cs_bgColorClass	= 'bg-color';
			} else{
				$cs_bgColor = '';
			}
			
		} else {
			$cs_serviceClass =	 $cs_service_type;
			$cs_align		  = $cs_service_postion_classic;
			$cs_LinkIcon	  = '<i class="icon-angle-right"></i>';
			if ( isset( $cs_service_link_color ) && $cs_service_link_color !='' ) {
				$cs_linkColor = 'style="background-color: '.$cs_service_link_color.' !important;"';
				$cs_linkTextColor = 'style="color: '.$cs_service_link_color.' !important"';
			} else{
				$cs_linkColor = '';
				$cs_linkTextColor = '';
			}
		}
		
		$html	.= '<div class="col-md-12 '.$cs_service_animation.'" '.$cs_CustomId.'>';
		$html	.= '<article class="cs-services '.$cs_serviceClass.' '.$cs_align.' '.$cs_bgColorClass.'"  '.$cs_bgColor.'>';
		if ( isset ( $cs_service_icon ) && $cs_service_icon !='' && $cs_service_icon_type == 'icon' ) {
			$html	.= '<figure><i class="'.$cs_service_icon .' '.$cs_service_icon_size.'" '.$cs_iconColor.'></i></figure>';
		}else if ( isset ( $cs_service_bg_image ) && $cs_service_bg_image !='' && $cs_service_icon_type == 'image' ) {
			$html	.= '<figure><img alt="" src="'.$cs_service_bg_image.'"></figure>';
		}
		
		$html	.= '<div class="text">';
		
		if ( isset ( $cs_service_title ) && $cs_service_title !='' ) {
			$html	.= '<h4 '.$cs_servTitleColor.'>'.$cs_service_title.'</h4>';
		}
		
		if ( isset ( $content ) && $content !='' ) {
			if ($cs_service_type == 'modern'){
				$html	.= '<p '.$cs_servTextColor.'>'.$content.'</p>';
				}else{
					$html	.= '<p '.$cs_servTextColor.'>'.$content.'</p>';
				}
		}
		
		if ( isset ( $cs_service_url ) && $cs_service_url !='' ) {
			$html	.= '<a '.$cs_linkColor.' class="read-more" href="'.$cs_service_url.'">'.$cs_more.' '.$cs_LinkIcon.'</a>';
		}
		
		$html	.= '</div>';
		$html	.= '</article>';
		$html	.= '</div>';
		
		return $html;
		
	}
	add_shortcode( 'cs_services', 'cs_services_shortcode' );
}

//======================================================================
// Services Content
//======================================================================
if (!function_exists('cs_service_content')) {
	function cs_service_contentt( $cs_atts, $content = null ) {
		$cs_defaults = array( 'content' => '' );
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		return '<p>'. $content .'</p>';
	}
	add_shortcode( 'content', 'cs_service_content' );
}

//======================================================================
// Adding Call to Action start
//======================================================================
if (!function_exists('cs_call_to_action_shortcode')) {
	function cs_call_to_action_shortcode($cs_atts, $content = "") {
		
		$cs_defaults = array('cs_column_size' => '1/1','cs_call_to_action_section_title'=>'','cs_content_type'=>'','cs_call_action_title'=>'','cs_btn_bg_color'=>'','cs_call_action_contents'=>'','cs_contents_color'=>'', 'cs_call_action_icon'=>'','cs_icon_color'=>'#FFF','cs_call_to_action_icon_background_color'=>'','cs_call_to_action_button_text'=>'','cs_call_to_action_button_link'=>'#','cs_call_to_action_bg_img'=>'','cs_animate_style'=>'slide','cs_class'=>'cs-article-box','cs_call_to_action_class'=>'','cs_call_to_action_animation'=>'','cs_custom_animation_duration'=>'1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		$cs_cell_button = '';
		$cs_CustomId	= '';
		$cs_cell_icon = '';
		if ( isset( $cs_call_to_action_class ) && $cs_call_to_action_class ) {
			$cs_CustomId	= 'id="'.$cs_call_to_action_class.'"';
		}
		if ( trim($cs_call_to_action_animation) !='' ) {
			$cs_call_to_action_animation	= 'wow'.' '.$cs_call_to_action_animation;
		} else {
			$cs_call_to_action_animation	= '';
		}
		$cs_section_title = '';
		if(isset($cs_call_to_action_section_title) && trim($cs_call_to_action_section_title) <> ''){
			$cs_section_title = '<div class="cs-section-title"><h2 class="'.$cs_call_to_action_animation .' ">'.$cs_call_to_action_section_title.'</h2></div>';
		}
		$cs_image = '';
		if (trim($cs_call_to_action_bg_img)) {
			$cs_image	= 'background-image:url('.$cs_call_to_action_bg_img.');';
		}
		$html	= '';
		if ($cs_content_type == 'normal'){ $cs_class = 'ac-classic';}else{ $cs_class = 'in-center ac-clean text-center';}
		
		$html	.= '<div class="call-actions '.$cs_class.' ' . $cs_call_to_action_class . ' '.$cs_call_to_action_animation .'" style=" background-color:'.$cs_call_to_action_icon_background_color.'; background-image: url('.$cs_call_to_action_bg_img.');  animation-duration: '.$cs_custom_animation_duration.'s; '.$cs_image.' " >';
		$cs_action_icon = $cs_call_action_icon ?  $cs_call_action_icon : '';
		$cs_contents_color	= $cs_contents_color ? $cs_contents_color : '#FFF';
		
		$cs_cell_heading = '<div class="cell text-area">
                                <h3 style=" color: '.$cs_contents_color.' !important;">'.$cs_call_action_title.'</h3>
								<p style=" color: '.$cs_contents_color.';">'. do_shortcode($content). '</p>
                        </div>';
		$cs_cell_heading1 = '<div class="cell heading">
                                <h3 style=" color: '.$cs_contents_color.' !important;">'.$cs_call_action_title.'</h3>
                        </div>';				
		
		if ( isset( $cs_action_icon ) && $cs_action_icon !='' ) {
		$cs_cell_icon = '<div class="cell icon">
						  <i style=" color: '.$cs_icon_color.' !important; " class="'.$cs_action_icon.'"></i>
					  </div>';
		}
		
		$cs_cell_text ='<div class="cell text-area">
								<p style=" color: '.$cs_contents_color.';">'. do_shortcode($content). '</p>
                        </div>';
		if ($cs_call_to_action_button_text <> '' and $cs_call_to_action_button_link<>'') {
				$cs_cell_button ='<div class="cs-call-to-btn"><a class="three-d medium-btn" style="background:'.$cs_btn_bg_color.'" href="'.$cs_call_to_action_button_link.'">'.$cs_call_to_action_button_text.'</a></div>';
		}
		if ($cs_content_type == 'normal'){
				$html .= $cs_cell_icon.$cs_cell_heading.$cs_cell_button;
		}else{
				$html .= $cs_cell_heading1.$cs_cell_icon.$cs_cell_text;
		}
		$html	.= '</div>';
		return '<div '.$cs_CustomId.' class="'.$cs_column_class.'">'.$cs_section_title.'' . $html . '</div>';
	}
	add_shortcode('call_to_action', 'cs_call_to_action_shortcode');
}
//======================================================================
// adding progressbars start
//======================================================================
if (!function_exists('cs_progressbars_shortcode')) {
	function cs_progressbars_shortcode($cs_atts, $content = "") {
		global $cs_progressbars_style;
		$cs_defaults = array('cs_column_size'=>'1/1','cs_progressbars_style'=>'skills-sec','cs_progressbars_class'=>'','cs_progressbars_animation'=>'');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		$cs_CustomId	= '';
		if ( isset( $cs_progressbars_class ) && $cs_progressbars_class ) {
			$cs_CustomId	= 'id="'.$cs_progressbars_class.'"';
		}
		if ( trim($cs_progressbars_animation) !='' ) {
			$cs_progressbars_animation	= ' wow'.' '.$cs_progressbars_animation;
		} else {
			$cs_progressbars_animation	= '';
		}
		cs_skillbar_script();
		$cs_output = '<script>
						jQuery(document).ready(function($){
							cs_skill_bar();
						});	
				  </script>';
		$cs_progressbars_style_class = '';
		$cs_progressbars_bar_class_v2 = '';
		$cs_progressbars_bar_class = 'skills-v3';
		$cs_heading_size = 'span';
		if(isset($cs_progressbars_style) && $cs_progressbars_style == 'strip-progressbar'){
			$cs_progressbars_bar_class_v2 = 'skills-v2';
			$cs_heading_size = 'span';
			$cs_progressbars_bar_class = '';
		}
		else if(isset($cs_progressbars_style) && $cs_progressbars_style == 'plain-progressbar'){
			$cs_progressbars_bar_class_v2 = 'plain';
			$cs_heading_size = 'span';
			$cs_progressbars_bar_class = '';
		}
		$cs_output .= '<div '.$cs_CustomId.' class="'.$cs_column_class.$cs_progressbars_animation.'"><div class="skills-element '.$cs_progressbars_style.' '.$cs_progressbars_bar_class_v2.' ' . $cs_progressbars_class . ' '.$cs_progressbars_animation .'">';
		$cs_output .= do_shortcode($content);	
		$cs_output .= '</div></div>';
		return $cs_output;
	}
	add_shortcode('cs_progressbars', 'cs_progressbars_shortcode');
}

//======================================================================
// adding progressbars Item start
//======================================================================
if (!function_exists('cs_progressbar_item_shortcode')) {
	function cs_progressbar_item_shortcode($cs_atts, $content = "") {
		global $cs_progressbars_style;
		$cs_defaults = array('cs_progressbars_title'=>'','cs_progressbars_color'=>'#4d8b0c','cs_progressbars_percentage'=>'50');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_output = '';
		$cs_output_title ='';
		$cs_progressbars_style_class = '';
		$cs_heading_size = 'span';
		if(isset($cs_progressbars_style) && $cs_progressbars_style == 'strip-progressbar'){
			$cs_progressbars_bar_class_v2 = 'skills-v2';
			$cs_heading_size = 'span';
			$cs_progressbars_bar_class = '';
		} 
		else if(isset($cs_progressbars_style) && $cs_progressbars_style == 'plain-progressbar'){
			$cs_progressbars_bar_class_v2 = 'plain';
			$cs_heading_size = 'span';
			$cs_progressbars_bar_class = '';
		}
		else {
			$cs_progressbars_bar_class = 'skills-v3';
		}
		if(isset($cs_progressbars_title) && $cs_progressbars_title <>''){
			$cs_output_title .= '<'.$cs_heading_size.'>'.$cs_progressbars_title.'</'.$cs_heading_size.'>';
		}
		if(isset($cs_progressbars_percentage) && $cs_progressbars_percentage <>''){
			
			/*if(isset($cs_progressbars_style) && $cs_progressbars_style == 'strip-progressbar'){
				$cs_output .= $cs_output_title;
			}*/
			$cs_output .= '<div class="skills-sec '.$cs_progressbars_bar_class.'" data-percent="'.$cs_progressbars_percentage.'%">';
			if(isset($cs_progressbars_style) && $cs_progressbars_style == 'strip-progressbar'){
				$cs_output .= $cs_output_title;
				$cs_output .= '<small>'.$cs_progressbars_percentage.'%</small><div class="skillbar"><div class="skillbar-bar" style="background-color: '.$cs_progressbars_color.' !important;width:'.$cs_progressbars_percentage.'%;"></div></div>';
			} 
			else if(isset($cs_progressbars_style) && $cs_progressbars_style == 'plain-progressbar'){
				$cs_output .= $cs_output_title;
				$cs_output .= '<small>'.$cs_progressbars_percentage.'%</small><div class="skillbar"><div class="skillbar-bar" style="background-color: '.$cs_progressbars_color.' !important;width:'.$cs_progressbars_percentage.'%;"></div></div>';
			} 
			else {
				$cs_output .= '<div class="skillbar"><div class="skillbar-bar" style="background: '.$cs_progressbars_color.' !important;width:'.$cs_progressbars_percentage.'%;">'.$cs_output_title.'<small>'.$cs_progressbars_percentage.'%</small></div></div>';
			}
			$cs_output .= '</div>';
		}
		return $cs_output;
	}
	add_shortcode('progressbar_item', 'cs_progressbar_item_shortcode');
}

//======================================================================
// adding piecharts start
//======================================================================
if (!function_exists('cs_piecharts_shortcode')) {
	function cs_piecharts_shortcode($cs_atts, $content = "") {
		$cs_defaults = array('cs_column_size'=>'1/2','cs_piechart_section_title'=>'','cs_piechart_info'=>'','cs_piechart_text'=>'','cs_piechart_dimensions'=>'250','cs_piechart_width'=>'10','cs_piechart_fontsize'=>'50','cs_piechart_percent'=>'35','cs_piechart_icon'=>'','cs_piechart_icon_color'=>'','cs_piechart_icon_size'=>'30','cs_piechart_fgcolor'=>'#61a9dc','cs_piechart_bg_color'=>'#eee','cs_piechart_bg_image'=>'','cs_piechart_class'=>'','cs_piechart_animation'=>'');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		cs_skillbar_script();
		$cs_CustomId	= '';
		if ( isset( $cs_piechart_class ) && $cs_piechart_class ) {
			$cs_CustomId	= 'id="'.$cs_piechart_class.'"';
		}
		$cs_rand_id = rand(98,56666);
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		if ( trim($cs_piechart_animation) !='' ) {
			$cs_piechart_animation	= 'wow'.' '.$cs_piechart_animation;
		} else {
			$cs_piechart_animation	= '';
		}
		$cs_output = '<script>
						jQuery(document).ready(function($){
							// Circul Progress Function
							jQuery("#chart'.$cs_rand_id.'").waypoint(function(direction) {
								jQuery(this).circliful();
							}, {
								offset: "100%",
								triggerOnce: true
							});
						});	
		</script>';
		$cs_section_title = '';
		if ($cs_piechart_section_title && trim($cs_piechart_section_title) !='') {
			$cs_section_title	= '<div class="cs-section-title"><h2 class="' . $cs_piechart_class . ' '.$cs_piechart_animation .'">'.$cs_piechart_section_title.'</h2></div>';
		}
		$cs_piechart_data_elements = '';
		if(isset($cs_piechart_info) && $cs_piechart_info !=''){
			$cs_piechart_data_elements .= ' data-info="'.$cs_piechart_info.'"';
		}
		if(isset($cs_piechart_dimensions) && $cs_piechart_dimensions !=''){
			$cs_piechart_data_elements .= ' data-dimension="'.$cs_piechart_dimensions.'"';
		}
		if(isset($cs_piechart_width) && $cs_piechart_width !=''){
			$cs_piechart_data_elements .= ' data-width="'.$cs_piechart_width.'"';
		}
		if(isset($cs_piechart_fontsize) && $cs_piechart_fontsize !=''){
			$cs_piechart_data_elements .= ' data-fontsize="'.$cs_piechart_fontsize.'"';
		}
		if(isset($cs_piechart_percent) && $cs_piechart_percent !=''){
			$cs_piechart_data_elements .= ' data-text="'.$cs_piechart_percent.'%"';
			$cs_piechart_data_elements .= ' data-percent="'.$cs_piechart_percent.'"';
		}
		if(isset($cs_piechart_icon) && $cs_piechart_icon !=''){
			$cs_piechart_data_elements .= ' data-icon="'.$cs_piechart_icon.'"';
		}
		if(isset($cs_piechart_icon_size) && $cs_piechart_icon_size !=''){
			$cs_piechart_data_elements .= ' data-iconsize="'.$cs_piechart_icon_size.'"';
		}
		if(isset($cs_piechart_icon_color) && $cs_piechart_icon_color !=''){
			$cs_piechart_data_elements .= ' data-iconcolor="'.$cs_piechart_icon_color.'"';
		}
		if(isset($cs_piechart_fgcolor) && $cs_piechart_fgcolor !=''){
			$cs_piechart_data_elements .= ' data-fgcolor="'.$cs_piechart_fgcolor.'"';
		}
		if(isset($cs_piechart_bg_color) && $cs_piechart_bg_color !=''){
			$cs_piechart_data_elements .= ' data-bgcolor="'.$cs_piechart_bg_color.'"';
		}
		if(isset($cs_piechart_bg_image) && $cs_piechart_bg_image !=''){
			$cs_piechart_data_elements .=  ' data-bgimage="'.$cs_piechart_bg_image.'"';
		}
		$cs_output .= '<div id="chart'.$cs_rand_id.'" class="chartskills ' . $cs_piechart_class . ' '.$cs_piechart_animation .'" '.$cs_piechart_data_elements.'></div>';
		return '<div '.$cs_CustomId.' class="'.$cs_column_class.'">'.$cs_section_title.'<div class="piechart col-md-12">'.$cs_output.'</div></div>';
	}
	add_shortcode('cs_piechart', 'cs_piecharts_shortcode');
}

//======================================================================
// adding Faq
//======================================================================
if (!function_exists('cs_faq_shortcode')) {
	function cs_faq_shortcode($cs_atts, $content = "") {
		global $cs_acc_counter;
		$cs_acc_counter = rand(40, 9999999);
		$html	= '';
		$cs_defaults = array('cs_column_size'=>'1/1', 'cs_class' => 'cs-faq','cs_faq_class' => '','cs_faq_animation' => '','cs_faq_section_title'=>'');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		
		$cs_CustomId	= '';
		if ( isset( $cs_faq_class ) && $cs_faq_class ) {
			$cs_CustomId	= 'id="'.$cs_faq_class.'"';
		}
		
		if ( trim($cs_faq_animation) !='' ) {
			$cs_faq_animation	= 'wow'.' '.$cs_faq_animation;
		} else {
			$cs_faq_animation	= '';
		}
		
		$cs_section_title = '';
		if(isset($cs_faq_section_title) && trim($cs_faq_section_title) <> ''){
			$cs_section_title = '<div class="cs-section-title"><h2>'.$cs_faq_section_title.'</h2></div>';
		}
		$html   .= '<div '.$cs_CustomId.' class="'.$cs_column_class.'">';
		$html	.= '<div class="panel-group simple'.$cs_faq_class.' '.$cs_faq_animation.'" id="faq-' . $cs_acc_counter . '">'.$cs_section_title. force_balance_tags (do_shortcode($content) ).'</div>';
		$html	.= '</div>';
		return do_shortcode($html);
	}
	
	add_shortcode('cs_faq', 'cs_faq_shortcode');
}

//======================================================================
// Adding Faq item start
//======================================================================
if (!function_exists('cs_faq_item_shortcode')) {
	function cs_faq_item_shortcode($cs_atts, $content = "") {
		global $cs_acc_counter,$cs_faq_animation;
		$cs_defaults = array( 'cs_faq_title' => 'Title','cs_faq_active' => 'yes','cs_faq_icon' => '');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_faq_count = 0;
		$cs_faq_count = rand(40, 9999999);
		$html = "";
		$cs_active_in = '';
		$cs_active_class = '';
		$cs_styleColapse = '';
		
		$cs_styleColapse	= 'collapse collapsed';
		
		if(isset($cs_faq_active) && $cs_faq_active == 'yes'){
			$cs_styleColapse	= '';
			$cs_active_in = 'in';
		} else {
			$cs_active_class = 'collapsed';
		}
		
		$cs_faq_icon_class = '';
		if(isset($cs_faq_icon)){
			$cs_faq_icon_class = '<i class="'.$cs_faq_icon.'"></i>';
		}
		$html = '<div class="panel panel-default">
					<div class="panel-heading">
					  <h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#faq-'.$cs_acc_counter.'" href="#faq-'.$cs_faq_count.'" class="'.$cs_active_class.'">
						    <span>Q.</span>' . $cs_faq_icon_class . $cs_faq_title . '
						</a>
					  </h4>
					</div>
					<div id="faq-'.$cs_faq_count.'" class="panel-collapse collapse '.$cs_active_in.' ">
					  <div class="panel-body">'.force_balance_tags ( do_shortcode( $content ) ).'</div>
					</div>
				  </div>';
		return $html;
	}
	add_shortcode('faq_item', 'cs_faq_item_shortcode');
}


/**
*@ Alow Spcial Char For Textfield 
*
**/
function cs_allow_special_char($input = ''){
	$output  = $input;
	return $output;
}