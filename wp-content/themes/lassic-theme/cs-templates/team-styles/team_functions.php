<?php
/**
 * File Type: Team Shortcode
 */
	

//======================================================================
// Adding Team Posts Start
//======================================================================
if (!function_exists('cs_member_shortcode')) {
	function cs_member_shortcode( $atts ) {
		global $post,$wpdb,$cs_theme_options,$cs_counter_node,$cs_xmlObject;
		$defaults = array('cs_member_section_title'=>'','cs_member_cat' =>'','cs_member_excerpt_length'=>'255','cs_member_filterable' =>'','cs_member_orderby'=>'DESC','orderby'=>'ID','cs_member_num_post'=>'10','member_pagination'=>'','cs_member_class' => '','cs_member_animation' => '');
		extract( shortcode_atts( $defaults, $atts ) );
		
		$CustomId	= '';
		if ( isset( $cs_member_class ) && $cs_member_class ) {
			$CustomId	= 'id="'.$cs_member_class.'"';
		}
		
		if ( trim($cs_member_animation) !='' ) {
			$cs_custom_animation	= 'wow'.' '.$cs_member_animation;
		} else {
			$cs_custom_animation	= '';
		}
		$cs_counter_node++;
		ob_start();
		
		if (isset($cs_xmlObject->sidebar_layout) && $cs_xmlObject->sidebar_layout->cs_page_layout <> '' and $cs_xmlObject->sidebar_layout->cs_page_layout <> "none"){				
				$cs_member_layout = 'col-md-4';
		}else{
				$cs_member_layout = 'col-md-3';	
		}
		
		$cs_filter_category = $cs_member_cat;
		
		if (empty($_GET['page_id_all'])) $_GET['page_id_all'] = 1;

		$cs_member_num_post	= $cs_member_num_post ? $cs_member_num_post : '-1';
		
		$args = array('posts_per_page' => "-1", 'post_type' => 'member', 'order' => $cs_member_orderby,'post_status' => 'publish');
		if(isset($cs_filter_category) && $cs_filter_category <> '' &&  $cs_filter_category <> '0'){
			$cs_member_cats = array('member-category' => "$cs_filter_category");
			$args = array_merge($args, $cs_member_cats);
		}
		$query = new WP_Query( $args );
		$count_post = $query->post_count;
		$cs_member_num_post	= $cs_member_num_post ? $cs_member_num_post : '-1';
		$args = array('posts_per_page' => "$cs_member_num_post", 'post_type' => 'member', 'paged' => $_GET['page_id_all'], 'order' => $cs_member_orderby, 'post_status' => 'publish');

		if(isset($cs_filter_category) && $cs_filter_category <> '' &&  $cs_filter_category <> '0'){
			$cs_category = array('member-category' => "$cs_filter_category");
			$args = array_merge($args, $cs_category);
		}
		
		$outerDivStart	= '<div '.$CustomId.' class="'.sanitize_html_class($cs_custom_animation).'">';
		$outerDivEnd	= '</div>';
		$section_title  = '';
		
		if(isset($cs_member_section_title) && trim($cs_member_section_title) <> ''){
			$section_title = '<div class="main-title col-md-12"><div class="cs-section-title"><h2>'.$cs_member_section_title.'</h2></div></div>';
		}
		
		
 		$query = new WP_Query( $args );
		$post_count = $query->post_count;
		$memberObject = new MemberTemplates();
	  	echo cs_allow_special_char($outerDivStart);
		echo cs_allow_special_char( $section_title );
		
		$memberObject->cs_member_view($cs_member_excerpt_length,$query);
		 
		echo cs_allow_special_char( $outerDivEnd );
		//==Pagination Start
		if ( $member_pagination == "Show Pagination" && $count_post > $cs_member_num_post && $cs_member_num_post > 0 ) {
		  $qrystr = '';
		   if ( isset($_GET['page_id']) ) $qrystr .= "&amp;page_id=".$_GET['page_id'];
		
		  echo cs_pagination($count_post, $cs_member_num_post,$qrystr,'Show Pagination');
		}
	    wp_reset_postdata();	
        $post_data = ob_get_clean();
		return $post_data;
     }
	add_shortcode( 'cs_member', 'cs_member_shortcode' );
}
