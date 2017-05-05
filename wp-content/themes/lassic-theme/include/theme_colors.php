<?php
/**
 * The template Theme Colors
 */
 
/** 
 * @Set Theme Colors
 *
 *
 */
 
 if ( ! function_exists( 'cs_theme_colors' ) ) {
	function cs_theme_colors(){
		global $post,$cs_theme_options,$page_colors;
 		$cs_theme_color = isset($cs_theme_options['cs_theme_color']) ? $cs_theme_options['cs_theme_color'] : '';
		$sub_header_border_color = isset($cs_theme_options['cs_sub_header_border_color']) ? $cs_theme_options['cs_sub_header_border_color'] : '';
		$main_header_border_color = isset($cs_theme_options['cs_header_border_color']) ? $cs_theme_options['cs_header_border_color'] : '';
		
		$page_header_style = '';
		$page_header_border_colr = '';
		$page_subheader_border_color = '';
		$page_subheader_text_color = '';
		if(is_page() || is_single()){
			$cs_post_type = get_post_type($post->ID);
			switch($cs_post_type){
				case 'post':
					$post_type_meta = 'post';
					break;
				case 'member':
					$post_type_meta = 'member';
					break;
				case 'project':
					$post_type_meta = 'csprojects';
					break;
				default:
					$post_type_meta = 'cs_page_builder';
			}
			
			$cs_page_bulider = get_post_meta($post->ID, "$post_type_meta", true);
			$cs_xmlObject = new stdClass();
			if(isset($cs_page_bulider) && $cs_page_bulider <> ''){
				$cs_xmlObject = new SimpleXMLElement($cs_page_bulider);
				$page_header_style = $cs_xmlObject->header_banner_style;
				$page_header_border_colr = $cs_xmlObject->page_main_header_border_color;
				$page_subheader_border_color = $cs_xmlObject->page_subheader_border_color;
				$page_subheader_text_color = $cs_xmlObject->page_subheader_text_color;
			}
		}
 ?>
<style type="text/css">
/*!
/*============================================ Orange Color ( Color 1 ) ==============================================*/

.cs-colr,.cs-color,.cs-hovercolor:hover, .pagination ul li a.active, blockquote, .comment-reply-link, .question-mark p:before, .simple .panel-title a span,
/* Blog Setup */.cs-blog  article:hover h2 a, .blog-grid article:hover h4 a, .pagination ul li:hover a,
/* Widget Setup */.widget_categories ul li:hover a, .widget.twitter_widget ul li:before, .widget_pages ul li a:hover, .widget_meta ul li:hover a, .widget_recent_entries ul li:hover a, .widget_nav_menu ul li a:hover,.cs-post-options li a:hover,.infotext h2 a:hover,
.widget_nav_menu ul li a:hover i, .widget_archive ul li:hover a, .cs-section-title h2 span, .widget_recent_comments ul li a:hover, .recentblog_post article:hover h6 a, .cs-post-sharebtn article h5 a:hover, .simple .panel-title a.collapsed span,blockquote p,.cs-portfoliolist article:hover .textinfo-sec h2 a,.post-options li a:hover,
#copyright p a, .breadcrumb ul li.active, .cssearch form span, .cs-section-title h2 span, .cs-portfoliolist ul li:hover h2 a, .cs-portfoliolist .caption-inner a i,
.cs-team article:hover .text h2 a,.page-not-found h2, .heading-description span, .modern .panel-title a, .top-bar .social-media ul li a:hover, .top-bar #lang_sel ul li a:hover, .navigation ul > li > a:hover, #footer-sec .widget ul li a:hover, .cs-bloginfo-sec h4 a:hover, .breadcrumb ul li a:hover,.main-section h3 a:hover, .cs-portfolio-detail ul li a:hover, .cs-search-results ul li a:hover{
 color:<?php echo cs_allow_special_char($cs_theme_color);?> !important;
}

.cs-bg-color,.csbg-color,.csbg-hovercolor:hover, .cs-tags ul li a:hover, .cs-blog figure figcaption a span, .cs-services.modren figure i, .cs-prv-next .owl-controls .owl-nav .owl-prev:hover, .cs-prv-next .owl-controls .owl-nav .owl-next:hover,.comment-respond form p input[type='submit'],.cs-attachments ul li i,.nav-tabs > li> a:hover
.nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover,
/* Widget Setup */.widget_search form label input[type='submit'], #wp-calendar caption, .widget_tag_cloud .tagcloud a:hover, .default-btn, .cs-category li,
.cs-filter-menu li.active, .cs-portfoliolist .caption-inner, .cs-filter-menu li:hover, .user-signup form input[type='button'],.cs-status,.cs-planbtn {
	background-color:<?php echo cs_allow_special_char($cs_theme_color); ?> !important;
}

.csborder-color,.csborder-hovercolor:hover, .pagination ul li a.active:before, .fancy .panel-title a span, .widget_categories ul li:hover:before,
.widget_pages ul li a:hover:before, .widget_meta ul li:hover:before, .widget_nav_menu ul li a:hover:before, .widget_archive ul li:hover:before {
 border-color:<?php echo cs_allow_special_char($cs_theme_color);
?> !important;
}

.hd-search .dropdown-menu {
 border-bottom-color:<?php echo cs_allow_special_char($cs_theme_color);
?> !important;
}


<?php
if((is_page() || is_single()) and ($page_header_style == 'breadcrumb_header' and $page_subheader_border_color <> '')){
	?>
	.breadcrumb-sec {
		border-top: 1px solid <?php echo cs_allow_special_char($page_subheader_border_color); ?>;
		
	}
	.breadcrumb-sec{
		border-bottom: 1px solid <?php echo cs_allow_special_char($page_subheader_border_color); ?> !important;
		}
	<?php
}
else{
	if($sub_header_border_color <> ''){
	?>
		.breadcrumb-sec {
			border-top: 1px solid <?php echo cs_allow_special_char($sub_header_border_color); ?>;
			
		}
		.breadcrumb-sec {
			border-bottom: 1px solid <?php echo cs_allow_special_char($sub_header_border_color); ?>;
		}
	<?php
	}
}

if((is_page() || is_single()) and ($page_header_style == 'no-header' and $page_header_border_colr <> '')){
	?>
	#main-header {
		border-bottom: 1px solid <?php echo cs_allow_special_char($page_header_border_colr); ?>;
	}
	<?php
}
else{
	if(isset($cs_theme_options['cs_default_header']) and $cs_theme_options['cs_default_header'] == 'No sub Header'){
		if($main_header_border_color <> ''){
		?>
			#main-header {
				border-bottom: 1px solid <?php echo cs_allow_special_char($main_header_border_color); ?>;
			}
		<?php
		}
	}
}


?>

</style>
<?php } 
}


/** 
 * @Set Header color Css
 *
 *
 */
if ( ! function_exists( 'cs_header_color' ) ) {
	function cs_header_color(){
		global $cs_theme_options;
		
		$cs_header_bgcolor	= (isset($cs_theme_options['cs_header_bgcolor']) and $cs_theme_options['cs_header_bgcolor']<>'') ? $cs_theme_options['cs_header_bgcolor']: '';
		
		$cs_nav_bgcolor =  (isset($cs_theme_options['cs_nav_bgcolor']) and $cs_theme_options['cs_nav_bgcolor']<>'') ? $cs_theme_options['cs_nav_bgcolor']: '';
		
		$cs_menu_color = (isset($cs_theme_options['cs_menu_color']) and $cs_theme_options['cs_menu_color']<>'') ? $cs_theme_options['cs_menu_color']:'';
		
		$cs_menu_active_color = (isset($cs_theme_options['cs_menu_active_color']) and $cs_theme_options['cs_menu_active_color']<>'') ? $cs_theme_options['cs_menu_active_color']: '';
		
		$cs_submenu_bgcolor = (isset($cs_theme_options['cs_submenu_bgcolor']) and $cs_theme_options['cs_submenu_bgcolor']<>'' ) ? $cs_theme_options['cs_submenu_bgcolor']: '';
		
		$cs_submenu_color = (isset($cs_theme_options['cs_submenu_color']) and $cs_theme_options['cs_submenu_color']<>'') ? $cs_theme_options['cs_submenu_color']: '';
		
		$cs_submenu_hover_color = (isset($cs_theme_options['cs_submenu_hover_color']) and $cs_theme_options['cs_submenu_hover_color']<>'') ? $cs_theme_options['cs_submenu_hover_color']: '';
		
		$cs_topstrip_bgcolor = (isset($cs_theme_options['cs_topstrip_bgcolor']) and $cs_theme_options['cs_topstrip_bgcolor']<>'') ? $cs_theme_options['cs_topstrip_bgcolor']: '';
		
		$cs_topstrip_text_color = (isset($cs_theme_options['cs_topstrip_text_color']) and $cs_theme_options['cs_topstrip_text_color']<>'') ? $cs_theme_options['cs_topstrip_text_color']: '';
		
		$cs_topstrip_link_color = (isset($cs_theme_options['cs_topstrip_link_color']) and $cs_theme_options['cs_topstrip_link_color']<>'') ? $cs_theme_options['cs_topstrip_link_color']: '';
		
		$cs_menu_activ_bg = (isset($cs_theme_options['cs_theme_color'])) ? $cs_theme_options['cs_theme_color']: '';
		
		/* logo margins*/
		$cs_logo_margintb = (isset($cs_theme_options['cs_logo_margintb']) and  $cs_theme_options['cs_logo_margintb'] <> '') ? $cs_theme_options['cs_logo_margintb']: '0';
		$cs_logo_marginlr = (isset($cs_theme_options['cs_logo_marginlr']) and  $cs_theme_options['cs_logo_marginlr'] <> '') ? $cs_theme_options['cs_logo_marginlr']: '0';

		/* font family */
		$cs_content_font = (isset($cs_theme_options['cs_content_font'])) ? $cs_theme_options['cs_content_font']: '';
		$cs_content_font_att = (isset($cs_theme_options['cs_content_font_att'])) ? $cs_theme_options['cs_content_font_att']: '';
		
		$cs_mainmenu_font = (isset($cs_theme_options['cs_mainmenu_font'])) ? $cs_theme_options['cs_mainmenu_font']: '';
		$cs_mainmenu_font_att = (isset($cs_theme_options['cs_mainmenu_font_att'])) ? $cs_theme_options['cs_mainmenu_font_att']: '';
		
		$cs_heading_font = (isset($cs_theme_options['cs_heading_font'])) ? $cs_theme_options['cs_heading_font']: '';
		$cs_heading_font_att = (isset($cs_theme_options['cs_heading_font_att'])) ? $cs_theme_options['cs_heading_font_att']: '';
		
		$cs_widget_heading_font = (isset($cs_theme_options['cs_widget_heading_font'])) ? $cs_theme_options['cs_widget_heading_font']: '';
		$cs_widget_heading_font_att = (isset($cs_theme_options['cs_widget_heading_font_att'])) ? $cs_theme_options['cs_widget_heading_font_att']: '';
		
		// setting content fonts
		$cs_content_fonts = preg_split('#(?<=\d)(?=[a-z])#i', $cs_content_font_att);
		
		$cs_content_font_atts = cs_get_font_att_array($cs_content_fonts);
		
		// setting main menu fonts
		$cs_mainmenu_fonts = preg_split('#(?<=\d)(?=[a-z])#i', $cs_mainmenu_font_att);
		
		$cs_mainmenu_font_atts = cs_get_font_att_array($cs_mainmenu_fonts);
		
		// setting heading fonts
		$cs_heading_fonts = preg_split('#(?<=\d)(?=[a-z])#i', $cs_heading_font_att);
		
		$cs_heading_font_atts = cs_get_font_att_array($cs_heading_fonts);
		
		// setting widget heading fonts
		$cs_widget_heading_fonts = preg_split('#(?<=\d)(?=[a-z])#i', $cs_widget_heading_font_att);
		
		$cs_widget_heading_font_atts = cs_get_font_att_array($cs_widget_heading_fonts);
 		
		/* font size */
		$cs_content_size = (isset($cs_theme_options['cs_content_size'])) ? $cs_theme_options['cs_content_size']: '';
		$cs_mainmenu_size = (isset($cs_theme_options['cs_mainmenu_size'])) ? $cs_theme_options['cs_mainmenu_size']: '';
		$cs_section_title_size = (isset($cs_theme_options['cs_section_title_size'])) ? $cs_theme_options['cs_section_title_size']: '';
		$cs_heading_1_size = (isset($cs_theme_options['cs_heading_1_size'])) ? $cs_theme_options['cs_heading_1_size']: '';
		$cs_heading_2_size = (isset($cs_theme_options['cs_heading_2_size'])) ? $cs_theme_options['cs_heading_2_size']: '';
		$cs_heading_3_size = (isset($cs_theme_options['cs_heading_3_size'])) ? $cs_theme_options['cs_heading_3_size']: '';
		$cs_heading_4_size = (isset($cs_theme_options['cs_heading_4_size'])) ? $cs_theme_options['cs_heading_4_size']: '';
		$cs_heading_5_size = (isset($cs_theme_options['cs_heading_5_size'])) ? $cs_theme_options['cs_heading_5_size']: '';
		$cs_heading_6_size = (isset($cs_theme_options['cs_heading_6_size'])) ? $cs_theme_options['cs_heading_6_size']: '';
		
		/* font Color */
		$cs_heading_h1_color = (isset($cs_theme_options['cs_heading_h1_color']) and $cs_theme_options['cs_heading_h1_color'] <> '') ? $cs_theme_options['cs_heading_h1_color']: '';
		$cs_heading_h2_color = (isset($cs_theme_options['cs_heading_h2_color']) and $cs_theme_options['cs_heading_h2_color'] <> '') ? $cs_theme_options['cs_heading_h2_color']: '';
		$cs_heading_h3_color = (isset($cs_theme_options['cs_heading_h3_color']) and $cs_theme_options['cs_heading_h3_color'] <> '') ? $cs_theme_options['cs_heading_h3_color']: '';
		$cs_heading_h4_color = (isset($cs_theme_options['cs_heading_h4_color']) and $cs_theme_options['cs_heading_h4_color'] <> '') ? $cs_theme_options['cs_heading_h4_color']:'';
		$cs_heading_h5_color = (isset($cs_theme_options['cs_heading_h5_color']) and $cs_theme_options['cs_heading_h5_color'] <> '') ? $cs_theme_options['cs_heading_h5_color']: '';
		$cs_heading_h6_color = (isset($cs_theme_options['cs_heading_h6_color']) and $cs_theme_options['cs_heading_h6_color'] <> '') ? $cs_theme_options['cs_heading_h6_color']: '';
		$cs_text_color = $cs_theme_options['cs_text_color'];
 		
		
		$cs_widget_heading_size = (isset($cs_theme_options['cs_widget_heading_size'])) ? $cs_theme_options['cs_widget_heading_size']: '';
		
		if(
			( isset( $cs_theme_options['cs_custom_font_woff'] ) && $cs_theme_options['cs_custom_font_woff'] <> '' ) &&
			( isset( $cs_theme_options['cs_custom_font_ttf'] ) && $cs_theme_options['cs_custom_font_ttf'] <> '' ) &&
			( isset( $cs_theme_options['cs_custom_font_svg'] ) && $cs_theme_options['cs_custom_font_svg'] <> '' ) &&
			( isset( $cs_theme_options['cs_custom_font_eot'] ) && $cs_theme_options['cs_custom_font_eot'] <> '' )
		):
		
		$font_face_html = "
		@font-face {
			font-family: 'cs_custom_font';
			src: url('".$cs_theme_options['cs_custom_font_eot']."');
			src:
				url('".$cs_theme_options['cs_custom_font_eot']."?#iefix') format('eot'),
				url('".$cs_theme_options['cs_custom_font_woff']."') format('woff'),
				url('".$cs_theme_options['cs_custom_font_ttf']."') format('truetype'),
				url('".$cs_theme_options['cs_custom_font_svg']."#cs_custom_font') format('svg');
			font-weight: 400;
			font-style: normal;
		}";
		
        $custom_font = true; else: $custom_font = false; endif;
 	?>
		<style type="text/css">
			<?php 
				if($custom_font == true){
					echo cs_allow_special_char($font_face_html);
				}
				else{
					echo cs_get_font_family($cs_content_font, $cs_content_font_att);
					echo cs_get_font_family($cs_mainmenu_font, $cs_mainmenu_font_att);
					echo cs_get_font_family($cs_heading_font, $cs_heading_font_att);
					echo cs_get_font_family($cs_widget_heading_font, $cs_widget_heading_font_att);
				}
			?>
	body,.main-section p {
		<?php 
		if($custom_font == true){
			echo 'font-family: cs_custom_font;';
			echo 'font-size: '.$cs_content_size.';';
		}
		else{
			echo cs_font_font_print($cs_content_font_atts, $cs_content_size, $cs_content_font);
		}
		?>
 		color:<?php echo cs_allow_special_char($cs_text_color);?>;
	}
	
	header .logo {
		margin:<?php echo cs_allow_special_char($cs_logo_margintb);?>px <?php echo cs_allow_special_char($cs_logo_marginlr);?>px !important;
   	}
	.nav li a,header .btn-style1,.footer-nav ul li a {
		<?php 
		if($custom_font == true){
			echo 'font-family: cs_custom_font;';
			echo 'font-size: '.$cs_mainmenu_size.';';
		}
		else{
			echo cs_font_font_print($cs_mainmenu_font_atts, $cs_mainmenu_size, $cs_mainmenu_font, true);
		}
		?>
	}
	.cs-section-title h2{
		<?php 
		if($custom_font == true){
			echo 'font-size: '.$cs_section_title_size.';';
		}
		else{
			echo cs_font_font_print($cs_mainmenu_font_atts, $cs_section_title_size, $cs_heading_font, true);
		}
		?>
	}
 	h1{
	<?php 
	if($custom_font == true){
		echo 'font-family: cs_custom_font;';
		echo 'font-size: '.$cs_heading_1_size.';';
	}
	else{
		echo cs_font_font_print($cs_heading_font_atts, $cs_heading_1_size, $cs_heading_font, true);
	}
	 
	?>}
	h2{
	<?php 
	if($custom_font == true){
		echo 'font-family: cs_custom_font;';
		echo 'font-size: '.$cs_heading_2_size.';';
	}
	else{
		echo cs_font_font_print($cs_heading_font_atts, $cs_heading_2_size, $cs_heading_font, true);
	}
	
	?>}
	h3{
	<?php 
	if($custom_font == true){
		echo 'font-family: cs_custom_font;';
		echo 'font-size: '.$cs_heading_3_size.';';
	}
	else{
		echo cs_font_font_print($cs_heading_font_atts, $cs_heading_3_size, $cs_heading_font, true);
	}
	
	?>}
	h4{
	<?php 
	if($custom_font == true){
		echo 'font-family: cs_custom_font;';
		echo 'font-size: '.$cs_heading_4_size.';';
	}
	else{
		echo cs_font_font_print($cs_heading_font_atts, $cs_heading_4_size, $cs_heading_font, true);
	}
	
	?>}
	h5{
	<?php 
	if($custom_font == true){
		echo 'font-family: cs_custom_font;';
		echo 'font-size: '.$cs_heading_5_size.';';
	}
	else{
		echo cs_font_font_print($cs_heading_font_atts, $cs_heading_5_size, $cs_heading_font, true);
	}
	
	?>}
	h6{
	<?php 
	if($custom_font == true){
		echo 'font-family: cs_custom_font;';
		echo 'font-size: '.$cs_heading_6_size.';';
	}
	else{
		echo cs_font_font_print($cs_heading_font_atts, $cs_heading_6_size, $cs_heading_font, true);
	}
	
	?>}
	
	.main-section h1, .main-section h1 a {color: <?php echo cs_allow_special_char($cs_heading_h1_color);?> !important;}
	.main-section h2, .main-section h2 a{color: <?php echo cs_allow_special_char($cs_heading_h2_color);?> !important;}
	.main-section h3, .main-section h3 a{color: <?php echo cs_allow_special_char($cs_heading_h3_color);?> !important;}
	.main-section h4, .main-section h4 a{color: <?php echo cs_allow_special_char($cs_heading_h4_color);?> !important;}
	.main-section h5, .main-section h5 a{color: <?php echo cs_allow_special_char($cs_heading_h5_color);?> !important;}
	.main-section h6, .main-section h6 a{color: <?php echo cs_allow_special_char($cs_heading_h6_color);?> !important;}
	.widget .widget-section-title h2{
		<?php
		if($custom_font == true){
			echo 'font-family: cs_custom_font;';
			echo 'font-size: '.$cs_widget_heading_size.';';
		}
		else{
			echo cs_font_font_print($cs_widget_heading_font_atts, $cs_widget_heading_size, $cs_widget_heading_font, true);
		}
		?>
	}
	.top-bar,#lang_sel ul ul {background-color:<?php echo cs_allow_special_char($cs_topstrip_bgcolor);?>;}
	#lang_sel ul ul:before { border-bottom-color: <?php echo cs_allow_special_char($cs_topstrip_bgcolor);?>; }
	.top-bar p {color:<?php echo cs_allow_special_char($cs_topstrip_text_color);?> !important;}
	.top-bar a,.top-bar i {color:<?php echo cs_allow_special_char($cs_topstrip_link_color);?> !important;}
 	.logo-section,.main-head{background:<?php echo cs_allow_special_char($cs_header_bgcolor);?> !important;}
	.main-navbar {background:<?php echo cs_allow_special_char($cs_nav_bgcolor);?> !important;}
	.navbar-nav > li > a,li.parentIcon a:after,.navigation ul .sub-dropdown li a i {color:<?php echo cs_allow_special_char($cs_menu_color);?> !important;}
	.navbar-nav > li > .dropdown-menu,.navbar-nav > li > .dropdown-menu > li > .dropdown-menu,.mega-grid,.sub-dropdown { background-color:<?php echo cs_allow_special_char($cs_submenu_bgcolor);?> !important;}
	.navbar-nav .sub-menu .dropdown-menu li a, .sub-dropdown li a {color:<?php echo cs_allow_special_char($cs_submenu_color);?> !important;}
	.navigation ul .sub-dropdown > li:hover > a,.navbar-nav .sub-menu .dropdown-menu > li:hover > a,ul ul li.current-menu-ancestor.parentIcon > a:after,ul ul li.parentIcon:hover > a:after,.navigation ul .sub-dropdown >  li:hover > a > i {border-bottom-color:<?php echo cs_allow_special_char($cs_submenu_hover_color);?>;color:<?php echo cs_allow_special_char($cs_submenu_hover_color);?> !important;}
	.navigation .navbar-nav > li.current-menu-item > a,.navigation .navbar-nav > li.current-menu-ancestor > a,.navigation .navbar-nav > li:hover > a,li.current-menu-ancestor.parentIcon > a:after,li.parentIcon:hover > a:after {color:<?php echo cs_allow_special_char($cs_menu_active_color);?> !important;}
	{ border-bottom-color:<?php echo cs_allow_special_char($cs_menu_active_color);?> !important; }
	.cs-user,.cs-user-login { border-color:<?php echo cs_allow_special_char($cs_menu_active_color);?> !important; }
	.navigation ul .sub-dropdown > li:hover > a,.navigation ul > li > a:after { background-color:<?php echo cs_allow_special_char($cs_menu_active_color);?> !important; }
	{
		box-shadow: 0 4px 0 <?php echo cs_allow_special_char($cs_topstrip_bgcolor); ?> inset !important;
	}
	.header_2 .nav > li:hover > a,.header_2 .nav > li.current-menu-ancestor > a {
       
	}
	</style>
<?php
	}
}



/** 
 * @Set Footer colors
 *
 *
 */
if ( ! function_exists( 'cs_footer_color' ) ) {
	function cs_footer_color(){
		global $cs_theme_options;
		$cs_footerbg_color = (isset($cs_theme_options['cs_footerbg_color']) and $cs_theme_options['cs_footerbg_color'] <> '') ? $cs_theme_options['cs_footerbg_color']: '';
		
		$cs_footerbg_image = (isset($cs_theme_options['cs_footer_background_image']) and $cs_theme_options['cs_footer_background_image'] <> '') ? $cs_theme_options['cs_footer_background_image']: '';
		
		$cs_title_color = (isset($cs_theme_options['cs_title_color']) and $cs_theme_options['cs_title_color'] <> '') ? $cs_theme_options['cs_title_color']: '';
		
		$cs_footer_text_color = (isset($cs_theme_options['cs_footer_text_color']) and $cs_theme_options['cs_footer_text_color'] <> '') ? $cs_theme_options['cs_footer_text_color']: '';
		
		$cs_link_color = (isset($cs_theme_options['cs_link_color']) and $cs_theme_options['cs_link_color'] <> '') ? $cs_theme_options['cs_link_color']: '';
				
		$cs_copyright_text_color = (isset($cs_theme_options['cs_copyright_text_color']) and $cs_theme_options['cs_copyright_text_color'] <> '') ? $cs_theme_options['cs_copyright_text_color']: '';
		
?>
<style type="text/css">
		#footer-sec {
            background-image:url(<?php echo esc_url($cs_footerbg_image); ?>) !important;
        }
        #footer-sec {
            background-color:<?php echo cs_allow_special_char($cs_footerbg_color); ?> !important;
        }
        #copyright p {
            color:<?php echo cs_allow_special_char($cs_copyright_text_color); ?> !important;
        }
		footer#footer-sec a,footer#footer-sec .widget-form ul li input[type="submit"],footer#footer-sec .tagcloud a,footer#footer-sec .widget ul li a{
            color:<?php echo cs_allow_special_char($cs_link_color); ?> !important;
        }
        footer#footer-sec .widget h2, footer#footer-sec .widget h5,footer#footer-sec h2,footer#footer-sec h3,footer#footer-sec h4,footer#footer-sec h5,footer#footer-sec h6 {
            color:<?php echo cs_allow_special_char($cs_title_color); ?> !important;
        }
        footer#footer-sec .widget ul li,footer#footer-sec .widget p, footer#footer-sec .widget_calendar tr td,footer#footer-sec,footer#footer-sec .col-md-3 p,footer#footer-sec .widget_latest_post .post-options li,footer#footer-sec .widget i,.widget-form ul li i,footer#footer-sec .widget_rss li,footer#footer-sec .widget_recent_comments span {
            color:<?php echo cs_allow_special_char($cs_footer_text_color); ?> !important;
        }
		
    </style>
<?php 
}
}