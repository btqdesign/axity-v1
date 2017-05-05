<?php
/**
 * The template for displaying header
 */
global  $options,$cs_theme_options, $cs_position, $cs_page_builder, $cs_meta_page, $cs_node, $cs_xmlObject,$options,$page_option,$post,$page_colors;
$slider_position = '';	
$header_style = '';
	if(!get_option('cs_theme_options')){
			$activation_data=cs_reset_data();
			$cs_theme_options =  $activation_data;
			$cs_theme_options['cs_default_layout_sidebar'] = 'sidebar-1';
			$cs_theme_options['cs_single_layout_sidebar'] = 'sidebar-1';			
			$cs_theme_options['cs_footer_widget'] = 'off';
	}else{
		$cs_theme_options = get_option('cs_theme_options');	
	}
 	/* theme unit testing code end */
 	$cs_builtin_seo_fields =$cs_theme_options['cs_builtin_seo_fields'];
	if(isset($cs_theme_options['cs_layout'])){ $cs_site_layout =$cs_theme_options['cs_layout'];} else { $cs_site_layout == '';}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
	<?php 
		if(isset($cs_theme_options['cs_custom_css']) and $cs_theme_options['cs_custom_css']<>''){
			echo '<style type="text/css"  scoped="scoped">
						'. $cs_theme_options['cs_custom_css'].'
				  </style> ';
		}
		if(isset($cs_theme_options['cs_custom_js']) and $cs_theme_options['cs_custom_js']<>''){
	    echo '<script type="text/javascript">
   			'. $cs_theme_options['cs_custom_js'].'
		</script> ';
  		}
        if ( function_exists( 'cs_header_settings' ) ) { cs_header_settings(); }
   		
		if ( is_singular() && get_option( 'thread_comments' ) )
            wp_enqueue_script( 'comment-reply' );  
			
            
		
		if(isset($cs_theme_options['cs_style_rtl']) and $cs_theme_options['cs_style_rtl']=='on'){
				//cs_rtl();
		}
     	//=====================================================================
		// Header Colors
		//=====================================================================
		if ( function_exists( 'cs_header_color' ) ) { cs_header_color(); }
		
		//=====================================================================
		// Theme Colors
		//=====================================================================
		if ( function_exists( 'cs_footer_color' ) ) { cs_footer_color(); }
		if ( function_exists( 'cs_theme_colors' ) ) { cs_theme_colors(); } 
		
		wp_head();
    ?>
    <script type="text/javascript">
	    var stateObj = { foo: "bar" };
		window.history.pushState(stateObj, "Intellego Life", "/life/");
    </script>
    </head>
    <?php flush(); ?>
	<body <?php body_class();  if($cs_site_layout !='full_width'){ if ( function_exists( 'cs_bg_image' ) ) { echo cs_bg_image(); } } ?>>
     <?php  if ( function_exists( 'cs_under_construction' ) ) { cs_under_construction(); } ?>
    	<!-- Wrapper Start -->
    <div class="wrapper <?php if ( function_exists( 'cs_header_postion_class' ) ) { echo cs_header_postion_class(); } ?> wrapper_<?php if ( function_exists( 'cs_wrapper_class' ) ) { cs_wrapper_class(); }?>">
	   	<!-- Header Start -->
	<?php
 		if($header_style == 'custom_slider' && $slider_position == 'above'){
			if ( function_exists( 'cs_subheader_style' ) ) { cs_subheader_style(); }
			if ( function_exists( 'cs_get_headers' ) ) { cs_get_headers(); }
			if(isset($cs_theme_options['cs_smooth_scroll']) and $cs_theme_options['cs_smooth_scroll'] == 'on'){
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					cs_nicescroll();	
				});
			</script>
			<?php			
			}
			if (isset($cs_theme_options['cs_sitcky_header_switch']) and $cs_theme_options['cs_sitcky_header_switch'] == "on"){
				cs_scrolltofix();
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery('.main-navbar').scrollToFixed();	
				});
			</script>
			<?php }?>
			<div class="clear"></div>
 			<?php
		}else{
 			if ( function_exists( 'cs_get_headers' ) ) { cs_get_headers(); }
			if(isset($cs_theme_options['cs_smooth_scroll']) and $cs_theme_options['cs_smooth_scroll'] == 'on'){
				cs_scrolltofix();
			?>
            
			<script type="text/javascript">
				jQuery(document).ready(function($){
					cs_nicescroll();	
				});
			</script>
			<?php			
			}
			if (isset($cs_theme_options['cs_sitcky_header_switch']) and $cs_theme_options['cs_sitcky_header_switch'] == "on"){
				cs_scrolltofix();
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery('.main-navbar').scrollToFixed();	
				});
			</script>
			<?php }?>
			<div class="clear"></div>
             <?php
				cs_header_position_settings();
			?>
			<!-- Breadcrumb SecTion -->
			<?php 
				if ( function_exists( 'cs_subheader_style' ) ) { cs_subheader_style(); }
			}
			
			$cs_padding	= '';
			if ( is_single() && get_post_type( $post->ID ) == 'project' ){
					$cs_project = get_post_meta($post->ID, "csprojects", true);
					if ( $cs_project <> "" ) {
						$cs_xmlObject = new SimpleXMLElement($cs_project);
						$cs_detail_view =$cs_xmlObject->project_detail_view;
						if ( isset( $cs_detail_view ) && ( $cs_detail_view == 'style_4' || $cs_detail_view == 'style_5' ) ) {
							$cs_padding	= "style='padding:0px;'";
						}
					}
			}
			?>
        <!-- Main Content Section -->
        <main id="main-content">
            <!-- Main Section Start -->
            <div class="main-section" <?php echo cs_allow_special_char($cs_padding);?>>