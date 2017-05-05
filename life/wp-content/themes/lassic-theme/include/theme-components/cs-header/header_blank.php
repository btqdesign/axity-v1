<?php
/**
 * The template for displaying header
 */
 global $cs_theme_options, $cs_position, $cs_page_builder, $cs_meta_page, $cs_node, $cs_xmlObject,$options;
 //$cs_theme_options = get_option('cs_theme_options');
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
	<?php 
        if ( function_exists( 'cs_header_settings' ) ) { cs_header_settings(); }
        if ( is_singular() && get_option( 'thread_comments' ) )
            wp_enqueue_script( 'comment-reply' );  
            wp_head(); 
    ?>
    </head>
	<body <?php body_class();  if($cs_site_layout !='full_width'){ if ( function_exists( 'cs_bg_image' ) ) { cs_bg_image(); } } ?>>
     <?php if ( function_exists( 'cs_under_construction' ) ) { cs_under_construction(); } ?>
    	<!-- Wrapper Start -->
    <div class="wrapper <?php if ( function_exists( 'cs_header_postion_class' ) ) { echo cs_header_postion_class(); } ?> wrapper_<?php if ( function_exists( 'cs_wrapper_class' ) ) { cs_wrapper_class(); }?>">
	   	<!-- Header Start -->
	<?php 
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
		<?php	
		}
	?>
        <div class="clear"></div>
        <!-- Breadcrumb SecTion -->
        <?php 
			if ( function_exists( 'cs_subheader_style' ) ) { cs_subheader_style(); }
		
		?>
        <!-- Breadcrumb SecTion -->
        <!-- Main Content Section -->
        <main id="main-content">
            <!-- Main Section Start -->
            <div class="main-section">
