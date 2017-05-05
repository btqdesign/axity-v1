<?php
/**
 * The template for Settings up Functions
 */
 
/** 
 * @Get logo
 *
 *
 */
 global $cs_theme_options;
if ( ! function_exists( 'cs_logo' ) ) {
	function cs_logo(){
		global $cs_theme_options;
		$logo = $cs_theme_options['cs_custom_logo'];
		?>
		<a href="<?php echo home_url(); ?>">	
			<img src="<?php echo esc_url($logo); ?>" style="width:<?php echo cs_allow_special_char($cs_theme_options['cs_logo_width']);?>px; height: <?php echo cs_allow_special_char($cs_theme_options['cs_logo_height']);?>px;" alt="<?php bloginfo('name'); ?>">
        </a>
	<?php
	}
}

/** 
 * @Set Header Position
 *
 *
 */
if ( ! function_exists( 'cs_header_postion_class' ) ) {
	function cs_header_postion_class(){
		global $cs_theme_options;
		return 'header-'.$cs_theme_options['cs_header_position'];
	}
}

/** 
 * @Set Header strip
 *
 *
 */
if ( ! function_exists( 'cs_header_strip' ) ) {
	function cs_header_strip($container = 'on'){
		global $cs_theme_options;
	//	$cs_header_options = $cs_theme_options['cs_header_options'];
		$cs_socail_icon_switch = $cs_theme_options['cs_socail_icon_switch'];
		$cs_search = $cs_theme_options['cs_search'];
		if(isset($cs_theme_options['cs_wpml_switch'])){ $cs_wpml_switch = $cs_theme_options['cs_wpml_switch']; }else{ $cs_wpml_switch = '';}
		$cs_header_strip_tagline_text = htmlspecialchars_decode($cs_theme_options['cs_header_strip_tagline_text']);
		if($cs_header_strip_tagline_text == 'on' || $cs_socail_icon_switch=='on'){ ?>
<!-- Top Strip -->

<?php
    if(isset($cs_theme_options['cs_header_top_strip']) and $cs_theme_options['cs_header_top_strip'] == 'on'){
    ?>
    <div class="top-bar"> 
      <!-- Container -->
      <?php if($container == 'on'){ ?>
      <div class="container"> 
      <?php } ?>
     	<!-- Left Side -->
        <aside class="left-side">
        <?php
          if(isset($cs_header_strip_tagline_text) and $cs_header_strip_tagline_text <> ''){ ?>
          		<?php echo do_shortcode($cs_header_strip_tagline_text);?>
          <?php 
            } 
            ?>
        </aside>
        <!-- Right Side -->
        <aside class="cs-right-side">
			 <div class="blog_name">
				 <?=get_bloginfo('name');?>
			 </div>
			 <?php if(isset($cs_socail_icon_switch) and $cs_socail_icon_switch=='on'){ 
                  echo '<div class="sg-socialmedia">';
                    cs_social_network();
                  echo '</div>';
               } 
			   if($cs_wpml_switch=='on'){
				   if ( function_exists('icl_object_id') ) {
			   ?>
              <div class="lang_sel_list_horizontal">
                    <ul>
                        <li>
                            <div class="language-sec"> 
                                <!-- Language Section --> 
								<?php echo do_action('icl_language_selector');?> 
                            </div>
                        </li>
                    </ul>
              </div>
          <?php } } ?>
        </aside>

        <!-- Right Section -->
 	 <!-- Container -->
      <?php if($container == 'on'){ ?>
      </div>
      <?php } ?>
      <!-- Container --> 
    </div>
<!-- Top Strip -->
<?php 
	}
		}
	}
}


//=================================================
//@Categories Mega Menus
//=================================================
if (!class_exists('cs_mega_menu_walker')) { 
	class cs_mega_menu_walker extends Walker_Nav_Menu {
		private $CurrentItem, $CategoryMenu, $menu_style;
		function cs_menu_start(){
			$sub_class = $last ='';
			$count_menu_posts = 0;
			$mega_menu_output = '';
		}
		function start_lvl( &$output, $depth = 0, $args = array(), $id=0 ) {
			$indent = str_repeat("\t", $depth);
			$bg =$this->CurrentItem->bg;
			$output .= $this->cs_menu_start();
			if( $this->CurrentItem->megamenu == 'on' && $depth == 0){
 					$output .= "\n$indent<ul class=\"mega-grid\" >\n";	
  			} else {
				$output .= "\n$indent<ul class=\"sub-dropdown\">\n";
			}
		}
		function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul> <!--End Sub Menu -->\n";
			
			if( $this->CurrentItem->megamenu == 'on' && $depth == 0){
			}
		}
		function start_el(&$output, $item, $depth = 0, $args = array() , $id = 0) {
			global $wp_query;
 			$this->CurrentItem = $item;
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			if($depth == 0){
				$class_names = $value = '';
				$mega_menu = 'dropdown sub-menu cs-mega-menu';
			} else if($args->has_children){
				$class_names = $value = '';
				$mega_menu = 'dropdown parentIcon  cs-sub-menu';
			} else {
				$class_names = $value = $mega_menu = '';
			}
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
  			if($item->object == 'page' && empty($item->menu_item_parent) or $item->object == 'custom'){
 				if( $this->CurrentItem->megamenu== 'on' ){
					$mega_menu = 'mega-menu';
					if( $this->CurrentItem->megamenu == 'on'){
						$mega_menu = 'dropdown mega-menu cs-mega-menu';
					}
					if( $this->CurrentItem->megamenu == 'on' &&  isset($category_options['menu_style']) && $category_options['menu_style'] == 'Category Post'){
						$mega_menu = 'dropdown mega-menu-v2';
					}
					if ( empty($args->has_children) ) $mega_menu .= ' full-mega-menu';
				} else {
					$mega_menu = 'dropdown sub-menu';
				}
			}
			$class_names = join( " $mega_menu ", apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
			$class_names = ' class="'. esc_attr( sanitize_html_class($class_names) ) . '"';
			$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
 			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			if( $this->CurrentItem->link != 'on'){
				$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
			}
			$item_output = $args->before;
			
			if( $this->CurrentItem->text != 'on'){
				$item_output .= '<a'. $attributes .'>';
				$item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
				$item_output .= $args->link_after;
				$item_output .= '</a>';
			}
			
			$item_output .= ! empty( $item->description )     ? ' <p>' . esc_attr( $item->description ) .'</p>' : '';
			$item_output .= $args->after;
			if( !empty($mega_menu) && empty($args->has_children) && $this->CurrentItem->megamenu == 'on' ){	
				$item_output .= $this->cs_menu_start();
			}
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args, $id );
		}
		function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
			$id_field = $this->db_fields['id'];
			if ( is_object( $args[0] ) ) {
				$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
			}
			return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}
	}
}

/**
 * @Top and Main Navigation
 *
 *
 */
if ( ! function_exists( 'cs_navigation' ) ) {
	function cs_navigation($nav='', $menus = 'menus', $menu_class = '', $depth='0'){
		global $cs_theme_options;	
		if ( has_nav_menu( $nav ) ) {
			if (class_exists('cs_mega_menu_walker')) {
				$defaults = array(
				'theme_location' => "$nav",
				'menu' => '',
				'container' => '',
				'container_class' => '',
				'container_id' => '',
				'menu_class' => "$menu_class",
				'menu_id' => "$menus",
				'echo' => false,
				'fallback_cb' => 'wp_page_menu',
				'before' => '',
				'after' => '',
				'link_before' => '',
				'link_after' => '',
				'items_wrap' => '<ul class="%1$s">%3$s</ul>',
				'depth' => "$depth",
				'walker' => new cs_mega_menu_walker());
	
				} else {
					
				$defaults = array(

					'theme_location' => "$nav",
					'menu' => '',
					'container' => '',
					'container_class' => '',
					'container_id' => '',
					'menu_class' => "$menu_class",
					'menu_id' => "$menus",
					'echo' => false,
					'fallback_cb' => 'wp_page_menu',
					'before' => '',
					'after' => '',
					'link_before' => '',
					'link_after' => '',
					'items_wrap' => '<ul class="%1$s">%3$s</ul>',
					'depth' => "$depth",
					'walker' => '',);
			}
			echo do_shortcode(wp_nav_menu($defaults));
		} else {
			
			
				$defaults = array(
				'theme_location' => "",
				'menu' => '',
				'container' => '',
				'container_class' => '',
				'container_id' => '',
				'menu_class' => "$menu_class",
				'menu_id' => "$menus",
				'echo' => false,
				'fallback_cb' => 'wp_page_menu',
				'before' => '',
				'after' => '',
				'link_before' => '',
				'link_after' => '',
				'items_wrap' => '<ul class="%1$s">%3$s</ul>',
				'depth' => "$depth",
				'walker' => '',);
	
			echo do_shortcode(str_replace('sub-menu', 'sub-dropdown',(wp_nav_menu($defaults))));
		}
		
	}
}

/** 
 * @Header search function
 *
 *
 */
if ( ! function_exists( 'cs_search' ) ) {
	function cs_search($search_class='cs-searchv2'){
		
			global $cs_theme_options;
		?>
    <div class="search-sec <?php echo cs_allow_special_char($search_class);?>">
        <a class="cs_searchbtn" href="#SearchModal" role="button" data-toggle="modal"><i class="icon-search7"></i></a>
      <div id="SearchModal" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-body">
                <div class="cssearch">
                        <form id="searchform" method="get" action="<?php echo home_url()?>"  role="search"> 
                            <input type="text" name="s" id="searchinput" value="<?php _e('Search','lassic'); ?>" onblur="if(this.value == '') { this.value ='<?php _e('Search','lassic'); ?>'; }" onfocus="if(this.value =='<?php _e('Search','lassic'); ?>') { this.value = ''; }"  >
                            <span><?php _e('Enter a keyword and press ENTER','lassic'); ?></span> 
                            <label>
          <input type="submit" value="" name="submit">
        </label> 
                        </form>
                    </div>
              </div>
          </div>
        </div>
    </div>
    </div>
<?php
	}
}
// Contribute Now Button
if(!function_exists('cs_contribute_now')){
	function cs_contribute_now(){
		global $cs_theme_options;
		$cs_contribute_now_link = (isset($cs_theme_options['cs_contribute_now_link'])) ? $cs_theme_options['cs_contribute_now_link'] : '';
		if(isset($cs_theme_options['cs_contribute_now']) and $cs_theme_options['cs_contribute_now']=='on' and $cs_contribute_now_link <> ''){
				echo '<a class="btn-style1" href="'.$cs_contribute_now_link.'"><i class="icon-database"></i>'.__('Contribute Now','lassic').'</a>';
		}
	}
	
}
/*
 *
 *@ Header 
 *
*/
if ( ! function_exists( 'cs_get_headers' ) ) {
	function cs_get_headers(){
		global $cs_theme_options;
	//	$cs_header_options = $cs_theme_options['cs_header_options'];
		$cs_socail_icon_switch = $cs_theme_options['cs_socail_icon_switch'];
		$cs_search = $cs_theme_options['cs_search'];
		if(isset($cs_theme_options['cs_wpml_switch'])){ $cs_wpml_switch = $cs_theme_options['cs_wpml_switch']; }else{ $cs_wpml_switch = '';}
		
		?>
		<header id="main-header" class="<?php // echo cs_allow_special_char($cs_header_options);?>">
				<!--//  TOPBAAR //-->
				<?php cs_header_strip( $cs_container = "off");?>
				<!--//  MainNavBaar //-->
				<div class="main-navbar">
                	<div class="container">
                        <div class="left-side"><div class="logo"><?php cs_logo(); ?></div></div>
                        <div class="cs-right-side">
                            <?php
                                $cs_close_icon = ''; 
                                if ( isset($_COOKIE['cs_close_toggle']) ){
                                    $cs_close_icon = ' addicon';
                                }
                                cs_main_navigation('header2-nav','');
                                if(isset($cs_search) and  $cs_search=='on'){
                                    cs_search();
                                }
                            ?>
                         </div>
                     </div>
 				</div>

			</header>
<!-- Header 2 End -->
<?php
			
	}
}

/** 
 * @Main navigation
 *
 *
 */
if ( ! function_exists( 'cs_header_main_navigation' ) ) {
function cs_header_main_navigation($nav=''){
		global $post,$cs_xmlObject;
		// check post type using post id
		$post_type = get_post_type(get_the_ID());
		if(is_page()){
			$meta_element = 'cs_page_builder';
		} else if(is_single() && $post_type != 'post'){
			$meta_element = 'dynamic_cusotm_post';
		} else {
			$meta_element = 'post';
		}
		$post_meta = get_post_meta(get_the_ID(), "$meta_element", true);
		if ( $post_meta <> "" ) {
			$cs_xmlObject = new SimpleXMLElement($post_meta);
		}
		if ( empty($cs_xmlObject->page_custom_menu) ) $page_custom_menu = ""; else $page_custom_menu = $cs_xmlObject->page_custom_menu;
		if($page_custom_menu != '' && $page_custom_menu != 'default'){
			cs_navigation("$page_custom_menu",'navbar-nav');
		} else {
			cs_navigation('main-menu','navbar-nav');	
		}
	}
}

 
/** 
 * @Subheader Style
 *
 *
 */
if ( ! function_exists( 'cs_subheader_style' ) ) {
	function cs_subheader_style($post_ID=''){
		global $post, $wp_query, $cs_theme_options, $cs_xmlObject;
 		$post_type = get_post_type(get_the_ID());
		$post_ID = get_the_ID();
		
 		if(is_page()){
			$meta_element = 'cs_page_builder';
		} else if(is_single() && $post_type == 'member'){
			$meta_element = 'member';
			
		} else if(is_single() && $post_type == 'project'){
			$meta_element = 'csprojects';
			
		} else if(is_single() && $post_type != 'post'){
			 $meta_element = 'dynamic_cusotm_post';
			 
		} else {
			$meta_element = 'post';
		}
		
 		$post_meta = get_post_meta($post_ID, "$meta_element", true);
		if ( $post_meta <> "" ){
			$cs_xmlObject = new SimpleXMLElement($post_meta);
		}

		if( is_author() || is_search() || is_archive() || is_category() ){ 
			$cs_xmlObject = new stdClass();
			$cs_xmlObject->header_banner_style = '';
		}
 			
			if(isset($cs_xmlObject->header_banner_style) && $cs_xmlObject->header_banner_style == 'no-header'){
				// Do Nothing
			} else if(isset($cs_xmlObject->header_banner_style) && $cs_xmlObject->header_banner_style == 'breadcrumb_header'){
				cs_breadcrumb_header( $post_ID );
			} else if(isset($cs_xmlObject->header_banner_style) && $cs_xmlObject->header_banner_style == 'custom_slider'){
				cs_shortcode_slider('pages');
			} else if(isset($cs_xmlObject->header_banner_style) && $cs_xmlObject->header_banner_style == 'map'){
				cs_shortcode_map();
			} else if ( $cs_theme_options['cs_default_header']) {
				if ( $cs_theme_options['cs_default_header']  == 'No sub Header') {
					// Do Noting
				} else if ( $cs_theme_options['cs_default_header']  == 'Breadcrumbs Sub Header') {
					cs_breadcrumb_header( $post_ID );
				} else if ( $cs_theme_options['cs_default_header']  == 'Revolution Slider') {
					cs_shortcode_slider('default-pages');
				}
			}
	}
}


/** 
 * @Custom Slider by using shortcode
 *
 *
 */
if ( ! function_exists( 'cs_shortcode_slider' ) ) {
	function cs_shortcode_slider($type=''){
		global $post, $cs_xmlObject,$cs_theme_options;
		if ( $type == 'pages' ){
			if ( empty($cs_xmlObject->custom_slider_id) ) $custom_slider_id = ""; else $custom_slider_id = htmlspecialchars($cs_xmlObject->custom_slider_id);
		} else {
			if ( empty($cs_theme_options['cs_custom_slider']) ) $custom_slider_id = ""; else $custom_slider_id = htmlspecialchars($cs_theme_options['cs_custom_slider']);
		}
		
		if(isset($custom_slider_id) && $custom_slider_id != ''){
		?>
			<div class="cs-banner"> <?php echo do_shortcode( '[rev_slider ' . $custom_slider_id . ']' );?> </div>
		<?php
		}
	}
}

/** 
 * @Custom Map by using shortcode
 *
 *
 */
if ( ! function_exists( 'cs_shortcode_map' ) ) {
	function cs_shortcode_map(){
		global $post, $cs_xmlObject,$header_map;
		if ( empty($cs_xmlObject->custom_map) ) $custom_map = ""; else $custom_map = html_entity_decode($cs_xmlObject->custom_map);
		if(isset($custom_map) && $custom_map != ''){
			$header_map	= true;
		?>
            <div class="cs-map"> <?php echo do_shortcode($custom_map);?> </div>
        <?php
		}
	}
}

/** 
 * @Breadcrumb Header
 *
 *
 */
if ( ! function_exists( 'cs_breadcrumb_header' ) ) {
	function cs_breadcrumb_header($post_ID=''){
		global $post, $wp_query, $cs_theme_options,$cs_xmlObject;
 		
		$breadcrumSectionStart	= '';
		$breadcrumSectionEnd	= '';
 		
 	 	if(is_page() || is_single()){
			if(isset($post) && $post <> ''){
				$post_ID = $post->ID;
			}else{
				$post_ID = '';
			}
			$post_type = get_post_type( $post_ID );
		}
		
		$staticContainerStart	 = '';
		$staticContainerEnd		 = '';
		$banner_image_height 	 = '200px';
		$cs_sh_paddingtop	 	 = '';
		$cs_sh_paddingbottom	 = '';
		$isDeafultSubHeader		 = 'false';
		if ( is_author() || is_search() || is_archive() || is_category() || is_404() ) {
			$isDeafultSubHeader	= 'true';
		}
		
		if ( isset( $cs_xmlObject->header_banner_style ) && $cs_xmlObject->header_banner_style == 'default_header' ) {
			//Padding Top & Bottom 
			if ( isset ( $cs_theme_options['subheader_padding_switch'] ) && $cs_theme_options['subheader_padding_switch'] == 'custom' ) {
				if ( empty($cs_theme_options['cs_sh_paddingtop']) ) $cs_sh_paddingtop = ""; else $cs_sh_paddingtop = 'padding-top:'.$cs_theme_options['cs_sh_paddingtop'].'px;';
				if ( empty($cs_theme_options['cs_sh_paddingbottom']) ) $cs_sh_paddingbottom = ""; else $cs_sh_paddingbottom = 'padding-bottom:'.$cs_theme_options['cs_sh_paddingbottom'].'px;';
			}
			
			//
			
			$page_subheader_color = (isset($cs_theme_options['cs_sub_header_bg_color']) and $cs_theme_options['cs_sub_header_bg_color']<>'' )?$cs_theme_options['cs_sub_header_bg_color']:'';
			$page_subheader_text_color = (isset($cs_theme_options['cs_sub_header_text_color']) and $cs_theme_options['cs_sub_header_text_color']<>'' )?$cs_theme_options['cs_sub_header_text_color']:'';
			
		
		
		 if ( isset( $cs_xmlObject->page_subheader_no_image ) && $cs_xmlObject->page_subheader_no_image !='' && $isDeafultSubHeader == 'false'  ) {  
				
				if ( isset( $cs_xmlObject->header_banner_image ) && $cs_xmlObject->header_banner_image !=''  ) { 
					$header_banner_image = $cs_xmlObject->header_banner_image;
				} else if ( isset( $cs_theme_options['cs_background_img'] ) && $cs_theme_options['cs_background_img'] !=''  ) { 
					$header_banner_image = $cs_theme_options['cs_background_img'];
				} else {
					$header_banner_image = "";
				}
				
				if ( isset( $cs_xmlObject->page_subheader_parallax ) && $cs_xmlObject->page_subheader_parallax !=''  ) { 
					$page_subheader_parallax = $cs_xmlObject->page_subheader_parallax;
				} else if ( isset( $cs_theme_options['cs_parallax_bg_switch'] ) && $cs_theme_options['cs_parallax_bg_switch'] !=''  ) { 
					$page_subheader_parallax = $cs_theme_options['cs_parallax_bg_switch'];
				} else {
					$page_subheader_parallax = "";
				}
			
			} else {
				$page_subheader_parallax = "";
				$header_banner_image     = "";
			}
		} else {
				if ( $isDeafultSubHeader == 'true' ) {
					
						if ( isset( $cs_theme_options['cs_background_img'] ) && $cs_theme_options['cs_background_img'] !=''  ) { 
							$header_banner_image = $cs_theme_options['cs_background_img'];
						} else {
							$header_banner_image = "";
						}
						
						if ( isset( $cs_theme_options['cs_parallax_bg_switch'] ) && $cs_theme_options['cs_parallax_bg_switch'] !=''  ) { 
							$page_subheader_parallax = $cs_theme_options['cs_parallax_bg_switch'];
						} else {
							$page_subheader_parallax = "";
						}

					$page_subheader_color = (isset($cs_theme_options['cs_sub_header_bg_color']) and $cs_theme_options['cs_sub_header_bg_color']<>'' )?$cs_theme_options['cs_sub_header_bg_color']:'';
			$page_subheader_text_color = (isset($cs_theme_options['cs_sub_header_text_color']) and $cs_theme_options['cs_sub_header_text_color']<>'' )?$cs_theme_options['cs_sub_header_text_color']:'';
			
					if ( isset( $cs_theme_options['cs_background_img'] ) && $cs_theme_options['cs_background_img'] !=''  ) { 
						$header_banner_image = $cs_theme_options['cs_background_img'];
					} else {
						$header_banner_image = "";
					}
					
					if ( isset( $cs_theme_options['cs_parallax_bg_switch'] ) && $cs_theme_options['cs_parallax_bg_switch'] !=''  ) { 
						$page_subheader_parallax = $cs_theme_options['cs_parallax_bg_switch'];
					} else {
						$page_subheader_parallax = "";
					}
					
					//Padding Top & Bottom 
					if ( isset ( $cs_theme_options['subheader_padding_switch'] ) && $cs_theme_options['subheader_padding_switch'] == 'custom' ) {
						if ( empty( $cs_theme_options['cs_sh_paddingtop'] ) ) { $cs_sh_paddingtop = "";} else { $cs_sh_paddingtop = 'padding-top:'.$cs_theme_options['cs_sh_paddingtop'].'px;';}
						if ( empty( $cs_theme_options['cs_sh_paddingbottom'] ) ) { $cs_sh_paddingbottom = ""; } else { $cs_sh_paddingbottom = 'padding-bottom:'.$cs_theme_options['cs_sh_paddingbottom'].'px';}
					
					}
					//
				} else {
					if ( empty($cs_xmlObject->page_subheader_color) ) $page_subheader_color = $cs_theme_options['cs_sub_header_bg_color']; else $page_subheader_color = $cs_xmlObject->page_subheader_color;
					if ( empty($cs_xmlObject->page_subheader_text_color) ) $page_subheader_text_color = ""; else $page_subheader_text_color = $cs_xmlObject->page_subheader_text_color;
				
					if ( isset( $cs_xmlObject->page_subheader_no_image ) && $cs_xmlObject->page_subheader_no_image !=''  ) {  
					
						if ( empty($cs_xmlObject->header_banner_image) ) $header_banner_image = ""; else $header_banner_image = $cs_xmlObject->header_banner_image;
						if ( empty($cs_xmlObject->page_subheader_parallax) ) $page_subheader_parallax = ""; else $page_subheader_parallax = $cs_xmlObject->page_subheader_parallax;
					} else {
						$page_subheader_parallax = "";
						$header_banner_image     = "";
					}
					//Padding Top & Bottom 
					if ( isset ( $cs_xmlObject->subheader_padding_switch ) && $cs_xmlObject->subheader_padding_switch == 'custom' ) {
						if ( empty($cs_xmlObject->subheader_padding_top) ) { $cs_sh_paddingtop = "";} else { $cs_sh_paddingtop = 'padding-top:'.$cs_xmlObject->subheader_padding_top.'px;';}
						if ( empty($cs_xmlObject->subheader_padding_bottom) ) { $cs_sh_paddingbottom = ""; } else { $cs_sh_paddingbottom = 'padding-bottom:'.$cs_xmlObject->subheader_padding_bottom.'px';}
					
					}
				}
		}
		
		if ( $page_subheader_color ){
			$subheader_style_elements = 'background: '.$page_subheader_color.';';
		} else {
			$subheader_style_elements = '';
		}
		
 		if(isset($header_banner_image) && $header_banner_image !='') {
 			   
			$image_exsist = @fopen($header_banner_image, 'r');
		   if($image_exsist <> ''){
 				$banner_image_height = getimagesize($header_banner_image);				
		   }else{
			   $banner_image_height = '';	
		  	}
  			if($banner_image_height <> ''){
				$banner_image_height = $banner_image_height[1].'px';
			}
			if ( $page_subheader_parallax == 'on'){
				$parallaxStatus	= 'fixed';
			} else {
				$parallaxStatus	= '';
			}
	
			if ( $page_subheader_parallax == 'on'){
				$header_banner_image = 'url('.$header_banner_image.') center top '.$parallaxStatus.'';
				$subheader_style_elements = 'background: '.$header_banner_image.' '.$page_subheader_color.';';
			} else {
				$subheader_style_elements = '';
				$header_banner_image = 'url('.$header_banner_image.') center top '.$parallaxStatus.'';
				$subheader_style_elements = 'background: '.$header_banner_image.' '.$page_subheader_color.';';
			}
			
			$breadcrumSectionStart	= '<div class="absolute-sec">';
			$breadcrumSectionEnd	= '</div>';
		 }
		 $parallax_class = '';
		 $parallax_data_type = '';
		 if(isset($page_subheader_parallax) && (string)$page_subheader_parallax == 'on'){
			 $parallax_class = 'parallex-bg';
			 $parallax_data_type = ' data-type="background"';
		 }
		 if($subheader_style_elements){
			$subheader_style_elements = 'style="'.$subheader_style_elements.'  '.$cs_sh_paddingtop.' '.$cs_sh_paddingbottom.'  "';	
		 } else {
		   $subheader_style_elements = 'style="min-height:'.$banner_image_height.'; '.$cs_sh_paddingtop.' '.$cs_sh_paddingbottom.' "';	
		 }
		 
		?>
<div class="breadcrumb-sec <?php echo cs_allow_special_char($parallax_class);?>" <?php echo cs_allow_special_char($subheader_style_elements);?> <?php echo cs_allow_special_char($parallax_data_type);?>> 
  
  <!-- Container --> 
  <?php echo balanceTags($breadcrumSectionStart, false);?>
      <div class="container">
        <div class="cs-table">
          <div class="cs-tablerow"> 
            <!-- PageInfo -->
            <?php
                if(is_page()){
                        get_subheader_title();
                }else if(is_single() && $post_type != 'post'){
                        get_subheader_title();
                }else if(is_single() && $post_type == 'post'){
                        get_subheader_title();
                } else {
					 if ( isset($cs_theme_options['cs_title_switch']) && $cs_theme_options['cs_title_switch'] == 'on' ){
                        get_default_post_title();
					 }
                }
            ?>
            <!-- PageInfo --><?php 
           $page_tile_align = get_subheader_text_align();
            if(is_page() or is_single() and ( isset($cs_xmlObject->page_breadcrumbs))){
                if(isset($cs_xmlObject->page_breadcrumbs) and $cs_xmlObject->page_breadcrumbs=='on'){
                        get_subheader_breadcrumb($page_tile_align);
                }else{
                    
                }
            }elseif ( isset($cs_theme_options['cs_breadcrumbs_switch']) && $cs_theme_options['cs_breadcrumbs_switch'] == 'on' ){
                get_subheader_breadcrumb($page_tile_align);
            }else{
                //get_subheader_breadcrumb($page_tile_align);
            }
           ?>
            
          </div>
        </div>
      </div>
  <?php echo balanceTags($breadcrumSectionEnd, false);?> 
  <!-- Container --> 
</div>

<?php
	}
}

/** 
 * @Page Sub header title and subtitle 
 *
 *
 */
if ( ! function_exists( 'get_subheader_breadcrumb' ) ) {
	function get_subheader_breadcrumb($page_tile_align=''){
	 global $post, $wp_query, $cs_theme_options, $cs_xmlObject;
	 
	$page_header_style = '';
	$page_bg_image = '';
	$page_subheader_text_color = '';
	if(is_page() || is_single()){
		$cs_post_type = get_post_type($post->ID);
		switch($cs_post_type){
			
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
			$page_bg_image = $cs_xmlObject->header_banner_image;
			$page_subheader_text_color = $cs_xmlObject->page_subheader_text_color;
		}
	}
		
//if( ( isset($cs_xmlObject->page_breadcrumbs) && $cs_xmlObject->page_breadcrumbs == 'on' ) || ( isset($cs_theme_options['cs_breadcrumbs_switch']) && $cs_theme_options['cs_breadcrumbs_switch'] == 'on' ) ){?>
<!-- BreadCrumb -->
<div class="breadcrumb <?php echo cs_allow_special_char($page_tile_align);?>">
	<div class="breadcrumbs">
  <?php 
  
		 if ( is_author() || is_search() || is_archive() || is_category() ) {
			  if ( isset( $cs_theme_options['cs_sub_header_text_color'] ) &&  $cs_theme_options['cs_sub_header_text_color'] <> ''  ){ ?>
				<style scoped="scoped">
					.breadcrumb-sec, .breadcrumb ul li a,.breadcrumb ul li.active,.breadcrumb ul li:first-child:after {
						color : <?php echo cs_allow_special_char($cs_theme_options['cs_sub_header_text_color']);?> !important;
					}	
				</style>
  <?php  	   }
		 } else {
				 if ( isset($page_header_style) and $page_header_style == 'default_header' ) {
					if ( isset( $cs_theme_options['cs_sub_header_text_color'] ) &&  $cs_theme_options['cs_sub_header_text_color'] <> ''  ){ ?>
  					<style scoped="scoped">
						.breadcrumb-sec, .breadcrumb ul li a,.breadcrumb ul li.active,.breadcrumb ul li:first-child:after {
							color : <?php echo cs_allow_special_char($cs_theme_options['cs_sub_header_text_color']);?> !important;
						}	
					</style>
  <?php  			} 
  				 }
				 else if(isset($page_header_style) && $page_header_style == 'breadcrumb_header'){?>
                 
                 	<?php
					if(isset($page_bg_image) && $page_bg_image <> ''){
					?>
  					<style scoped="scoped">
						.breadcrumb-sec {
							background:url('<?php echo cs_allow_special_char($page_bg_image); ?>');
						}	
						.breadcrumb-sec, .breadcrumb ul li a,.breadcrumb ul li.active,.breadcrumb ul li:first-child:after {
							color : <?php echo cs_allow_special_char($page_subheader_text_color);?> !important;
						}
					</style>
                    <?php 
					}
					?>
  <?php			}
				  else if(isset($page_subheader_text_color) && $page_subheader_text_color != ''){?>
  					<style scoped="scoped">
						.breadcrumb-sec, .breadcrumb ul li a,.breadcrumb ul li.active,.breadcrumb ul li:first-child:after {
							color : <?php echo cs_allow_special_char($page_subheader_text_color);?> !important;
						}	
					</style>
  <?php			}
  		}?>
  <?php cs_breadcrumbs();?>
  </div>
</div>
<div class="clear"></div>

<!-- BreadCrumb -->
<?php // }
                            
	}
}

/** 
 * @Page Sub header title and subtitle 
 *
 *
 */
if ( ! function_exists( 'get_subheader_text_align' ) ) {
	function get_subheader_text_align(){
		global $post, $cs_xmlObject,$cs_theme_options;
		
		$page_tile_align = '';
	    if ( isset($cs_xmlObject->header_banner_style) && $cs_xmlObject->header_banner_style == 'default_header' ) {
			
			if(isset($cs_theme_options['cs_title_align']) && $cs_theme_options['cs_title_align'] =='right'){
					$page_tile_align = 'page-title-align-right';
			}else if(isset($cs_theme_options['cs_title_align']) && $cs_theme_options['cs_title_align'] =='center'){
					$page_tile_align = 'page-title-align-center';
			}else {
					$page_tile_align = 'page-title-align-left';
			}
			
		} else {
			
			if(isset($cs_xmlObject->page_title_align) && $cs_xmlObject->page_title_align =='right'){
					$page_tile_align = 'page-title-align-right';
			}else if(isset($cs_xmlObject->page_title_align) && $cs_xmlObject->page_title_align =='center'){
					$page_tile_align = 'page-title-align-center';
			}else {
					$page_tile_align = 'page-title-align-left';
			}
		}
		
		return $page_tile_align;
	}
}

/** 
 * @Page Sub header title and subtitle 
 *
 *
 */
if ( ! function_exists( 'get_subheader_title' ) ) {
	function get_subheader_title($shop_id = ''){
		global $post, $cs_xmlObject,$cs_theme_options;
 		$page_tile_align = '';
	    $page_tile_align = get_subheader_text_align();
	
		if($shop_id <> ''){
			$post_ID = $shop_id;
		} else {
			$post_ID = $post->ID;
		}

		$text_color	= '';
 		echo '<div class="pageinfo '.$page_tile_align.'" >';
				$color = '';	
				if ( isset($cs_xmlObject->header_banner_style) and $cs_xmlObject->header_banner_style == 'default_header' ) {
				
					if ( empty($cs_theme_options['cs_sub_header_text_color']) ) $text_color = ""; else $text_color = $cs_theme_options['cs_sub_header_text_color'];
				} else {
					if (isset($cs_xmlObject->page_subheader_text_color) and $cs_xmlObject->page_subheader_text_color <> ''){
							$text_color	= $cs_xmlObject->page_subheader_text_color;
					}
				}
				
				$color	= 'style="color:'.$text_color.' !important"';
 				if(isset($cs_xmlObject)){
					if(isset($cs_xmlObject->page_title) && $cs_xmlObject->page_title == 'on'){
						if(isset($cs_xmlObject->seosettings->cs_seo_title) && $cs_xmlObject->seosettings->cs_seo_title != ''){
							echo '<h1 '.$color.'>'.$cs_xmlObject->seosettings->cs_seo_title.'</h1>';	
						} else {
							if((isset($_GET['uid']) and $_GET['uid']) <> '' or (isset($cs_theme_option['cs_dashboard']) and $cs_theme_option['cs_dashboard'] == get_the_ID())){
								$tagline_text = '';
								$tagline_text = get_the_author_meta('tagline',$_GET['uid']);
								echo '<h1 '.$color.'>'.get_the_author_meta('display_name',$_GET['uid']).'</h1>';
								if($tagline_text <> ''){
									echo '<p>';
									echo balanceTags($tagline_text, false);
									echo '</p>';
								}
							}else{
								echo '<h1 '.$color.'>'.get_the_title($post_ID).'</h1>';
							}
						}
					}
				} else {
					echo '<h1 '.$color.'>'.get_the_title($post_ID).'</h1>';
				}
				if(isset($cs_xmlObject->page_subheading_title) && $cs_xmlObject->page_subheading_title != ''){
					echo '<p '.$color.'>';
					echo do_shortcode(nl2br($cs_xmlObject->page_subheading_title));
					echo '</p>';	
				}

				
		echo '</div>';
	}
}
/** 
 * @ Default page title function
 *
 *
 */
if ( ! function_exists( 'get_default_post_title' ) ) {
	function get_default_post_title(){
		global $post,$cs_theme_options;
		$textAlign	=  $cs_theme_options['cs_title_align'];
		if ( empty($cs_theme_options['cs_sub_header_text_color']) ) $text_color = ""; else $text_color = 'style="color:'.$cs_theme_options['cs_sub_header_text_color'].'"';
		   ?>
    <div class="pageinfo <?php echo 'page-title-align-'.$textAlign;?>">
      <h1 <?php echo balanceTags($text_color, false);?>>
        <?php cs_post_page_title();?>
      </h1>
    </div>
<?php 
	}
}

/** 
 * @ Default Main Menu
 *
 *
 */
function cs_main_navigation($nav='',$class=''){
	$id = rand(1,99);
	echo '<nav class="navigation'.$class.'">
			<a class="cs-click-menu"><i class="icon-list8"></i></a>
			  ';
				cs_header_main_navigation($nav);
			echo '
          </nav>';	
}
