<?php
/**
 * File Type: Loops Shortcode Function
 */
 
//======================================================================
// Adding Clients Start
//======================================================================

if (!function_exists('cs_clients_shortcode')) {
	function cs_clients_shortcode($cs_atts, $content = "") {
		global	$cs_clients_view,$cs_client_border,$cs_client_gray;
		$cs_defaults = array('cs_column_size'=>'','cs_clients_view' => '','cs_client_gray' => '','cs_client_border' => '','cs_client_section_title' => '','cs_client_class' => '','cs_client_animation' => '','cs_custom_animation_duration' => '1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		
		$cs_CustomId	= '';
		if ( isset( $cs_client_class ) && $cs_client_class ) {
			$cs_CustomId	= 'id="'.$cs_client_class.'"';
		}
		
		if ( trim($cs_client_animation) !='' ) {
			$cs_client_animation	= 'wow'.' '.$cs_client_animation;
		} else {
			$cs_client_animation	= '';
		}
		
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		$cs_client_border = $cs_client_border == 'yes' ? 'has_border' : 'no-clients-border';
		$cs_owlcount = rand(40, 9999999);
		$cs_section_title = '';
		if(isset($cs_client_section_title) && trim($cs_client_section_title) <> ''){
			$cs_section_title = '<div class="cs-section-title"><h2>'.$cs_client_section_title.'</h2></div>';
		}
		$html  = '';
		$html .= '<div '.$cs_CustomId.' class="'.$cs_column_class.' '.$cs_client_animation.' '.$cs_client_class.'">';
		$html .= $cs_section_title;
		if ($cs_clients_view == 'grid') {
			$html	.= '<div class="cs-partner '.$cs_client_border.'">';
			$html	.= '<ul class="row">';
			$html	.= do_shortcode($content);
			$html	.= '</ul>';
			$html	.= '</div>';
		} else {
			cs_owl_carousel();
		?>
			<script>  
				jQuery(document).ready(function($) {
					$("#owl-demo-three-<?php echo esc_js($cs_owlcount);?>").owlCarousel({
						nav: true,
						margin: 0,
						navText: [
							"<i class='icon-angle-left'></i>",
							"<i class='icon-angle-right'></i>"
						],
						responsive: {
							0: {
								items: 1 // In this configuration 1 is enabled from 0px up to 479px screen size 
							},
							480: {
								items: 1, // from 480 to 677 
								nav: false // from 480 to max 
							},
							678: {
								items: 2, // from this breakpoint 678 to 959
								center: false // only within 678 and next - 959
							},
							960: {
								items: 3, // from this breakpoint 960 to 1199
								center: false,
								loop: false
				
							},
							1200: {
								items: 6
							}
						}
						});
				 }); 
			</script>
          <?php 
 			$html	.= '<div class="cs-partner partnerslide '.$cs_client_border.'">';
			$html	.= '<div class="row owl-carousel nxt-prv-v2 cs-theme-carousel " id="owl-demo-three-'.$cs_owlcount.'">';
			$html	.= do_shortcode($content);	
			$html	.= '</div>';
			$html	.= '</div>';
		}
		$html	.= '</div>';
		return $html;
	}
	add_shortcode('cs_clients', 'cs_clients_shortcode');
}

//======================================================================
// Adding Clients Logo Start
//======================================================================
if (!function_exists('cs_clients_item_shortcode')) {
	function cs_clients_item_shortcode($cs_atts, $content = "") {
		global	$cs_clients_view,$cs_client_border,$cs_client_gray;
		$cs_defaults = array('cs_bg_color'=>'','cs_website_url'=>'','cs_client_title'=>'','cs_client_logo'=>'');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		
		$html		 = '';
		$cs_grayScale	 = '';
		
		if (isset($cs_client_gray) && $cs_client_gray == 'yes'){
			$cs_grayScale	= 'grayscale';
		}
		
		$cs_tooltip	= '';
		
		if ( isset ( $cs_client_title ) && $cs_client_title != '' ) {
			$cs_tooltip	= 'title="'.$cs_client_title.'"';
		}
				
		$cs_url = $cs_website_url ?  $cs_website_url : 'javascript:;';
		if ($cs_clients_view == 'grid') {
			if (isset($cs_client_logo) && !empty($cs_client_logo)) {
				
				$html	.= '<li class="col-md-3"  style="background-color:'.$cs_bg_color.'"><figure><a '.$cs_tooltip.' href="'.$cs_url.'"><img class="'.$cs_grayScale.'" src="'.$cs_client_logo.'" alt="" ></a></figure></li>';
			}
		} else {
			if (isset($cs_client_logo) && !empty($cs_client_logo)) {
					$html	.= '<div class="item" style="background-color:'.$cs_bg_color.'"><figure><a href="'.$cs_url.'" '.$cs_tooltip.'><img class="'.$cs_grayScale.'" src="'.$cs_client_logo.'" alt=""></a></figure></div>';
			}
		}
		return $html;
	}
	add_shortcode('clients_item', 'cs_clients_item_shortcode');
}
// Adding Clients Logo End

//======================================================================
// Adding Content Slider ( Custom Posts ) Start 
//======================================================================
if (!function_exists('cs_contentslider_shortcode')) {
	function cs_contentslider_shortcode( $cs_atts ) {
		 global $post,$wpdb;
		$cs_defaults = array('cs_column_size'=>'1/1','cs_contentslider_title' => '','cs_contentslider_dcpt_cat'=>'','cs_contentslider_orderby'=>'DESC','orderby'=>'ID','cs_contentslider_description'=>'yes','cs_contentslider_excerpt'=>'255', 'cs_contentslider_num_post'=>'10','cs_contentslider_class' => '','cs_contentslider_animation' => '','cs_custom_animation_duration' => '');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		
		$cs_CustomId	= '';
		if ( isset( $cs_contentslider_class ) && $cs_contentslider_class ) {
			$cs_CustomId	= 'id="'.$cs_contentslider_class.'"';
		}
		
		if ( trim($cs_contentslider_animation) !='' ) {
			$cs_custom_animation	= 'wow'.' '.$cs_contentslider_animation;
		} else {
			$cs_custom_animation	= '';
		}
		
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		$cs_owlcount = rand(40, 9999999);
		ob_start();
		
		$cs_width	= 860;
		$cs_height	= 418;
		
		//==Get Post Type	
		$cs_args_all = array('posts_per_page' => "$cs_contentslider_num_post", 'post_type' => 'post', 'order' => $cs_contentslider_orderby, 'orderby' => $orderby, 'post_status' => 'publish');
		
		if(isset($cs_dcpt_cat) && $cs_dcpt_cat <> '' &&  $cs_dcpt_cat <> '0'){
			$cs_blog_category_array = array('category_name' => "$cs_dcpt_cat");
			$cs_args_all = array_merge($cs_args_all, $cs_blog_category_array);
		}
		if(isset($cs_contentslider_title) && $cs_contentslider_title <> ''){
			echo '<div class="'.cs_allow_special_char($cs_column_class).'"><div class="cs-section-title"><h2>'.cs_allow_special_char($cs_contentslider_title).'</h2></div></div>';
		}
		?>
        <div <?php echo cs_allow_special_char($cs_CustomId);?> class="col-md-12 <?php echo cs_allow_special_char($cs_contentslider_animation .' '.$cs_contentslider_class);?>" style="animation-duration:<?php echo cs_allow_special_char($cs_custom_animation_duration);?>s">
        <?php 
			
        
            $cs_query = new WP_Query( $cs_args_all ); 
			$cs_post_count = $cs_query->post_count;  
			cs_owl_carousel();
            if ( $cs_query->have_posts() ) { ?>
        <script>
		jQuery(document).ready(function($) {
		$('#owl-contents-slider-<?php echo esc_js($cs_owlcount) ;?>').owlCarousel({
				loop:true,
				nav:true,
				autoplay: true,
				margin: 15,
				navText: [
					   "<i class='icon-angle-left'></i>",
					   "<i class='icon-angle-right'></i>"
					  ],
				responsive:{
					0:{
						items:1
					},
					600:{
						items:1
					},
					1000:{
						items:1
					}
				}
			});
		 });
		</script>
      <div id="syncsliders">
            <div  class="owl-carousel content-slider" id="owl-contents-slider-<?php echo esc_attr($cs_owlcount) ;?>">
				<?php while ( $cs_query->have_posts() ) : $cs_query->the_post();?>
                <?php $cs_image_url = cs_attachment_image_src( get_post_thumbnail_id((int)get_the_id()),$cs_width,$cs_height );?>
                    <div class="item">
                    <figure><a href="<?php esc_url(the_permalink()); ?>"><img src="<?php echo esc_url($cs_image_url);?>" alt=""></a>
                        <?php if ($cs_contentslider_description == 'yes') {?>  
                        <figcaption>
                        	<h2><a href="<?php esc_url(the_permalink()); ?>"><?php the_title(); ?></a></h2>
                        	<p><?php echo cs_get_the_excerpt((int)$cs_contentslider_excerpt,false, '');?>  </p>
                        </figcaption>
                        <?php  } ?>
                    </figure>  
                </div>               
                <?php endwhile;  wp_reset_postdata();?>
            </div>
        </div>
        <?php  }
		$cs_post_data = ob_get_clean();
		return $cs_post_data;
		
	}
	add_shortcode( 'cs_contentslider', 'cs_contentslider_shortcode' );
}
//  Adding Content Slider ( Custom Posts ) End 

//======================================================================
// Adding Post Attachments
//=====================================================================
function cs_post_attachments($cs_gallery_meta_form){
  		$cs_galleryConter 		= rand(40, 9999999);
	?>		
    	<div class="to-social-network">
            <div class="gal-active">
                <div class="clear"></div>
                <div class="dragareamain">
                <div class="placehoder">Gallery is Empty. Please Select Media <img src="<?php echo esc_url(get_template_directory_uri().'/include/assets/images/bg-arrowdown.png');?>" alt="" />
                </div>
                <ul id="gal-sortable" class="gal-sortable-<?php echo esc_attr($cs_gallery_meta_form);?>">
                    <?php 
                        global $cs_node, $cs_xmlObject, $cs_counter,$post;
						
						$cs_counter_gal = 0; 
						if ( $cs_gallery_meta_form == 'gallery_slider_meta_form'){
							$type	= 'gallery_slider';
						} else {
							$type	= 'gallery';
						}

 						if( isset( $cs_xmlObject->gallery_slider ) && count($cs_xmlObject->gallery_slider)>0){
							foreach ( $cs_xmlObject->$type as $cs_node ){
								$cs_counter_gal++;
								$cs_counter = $post->ID.$cs_counter_gal;
								if ($type == 'gallery_slider'){
									cs_slider_clone();
								} else {
									cs_gallery_clone();
								}
							}
						}
                    ?>
                </ul>
                </div>
            </div>
            <div class="to-social-list">
                <div class="soc-head">
                    <h5>Select Media</h5>
                    <div class="right">
                        <?php if ( $cs_gallery_meta_form == 'gallery_slider_meta_form'){ ?>
							 <input type="button" class="button reload" value="Reload" onclick="refresh_media_slider(<?php echo esc_attr($cs_galleryConter);?>)" />
                        <?php } else {?>
                            <input type="button" class="button reload" value="Reload" onclick="refresh_media(<?php echo esc_attr($cs_galleryConter);?>)" />
                       <?php    }   ?>
                        <input id="cs_log" name="cs_logo" type="button" class="uploadfile button" value="Upload Media" />
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
                    <script type="text/javascript">
                        function show_next(page_id, total_pages){
                            //var dataString = 'action=media_pagination&id='+id+'&func='+func+'&page_id='+page_id+'&total_pages='+total_pages;
							var dataString = 'action=media_pagination&page_id='+page_id+'&total_pages='+total_pages;
							/*if (func == 'slider') {
								var	pagination	= 'pagination_slider';
							} else {
								var	pagination	= 'pagination_clone';
							}*/
                            jQuery("#pagination").html("<img src='<?php echo esc_js(esc_url(get_template_directory_uri().'/include/assets/images/ajax_loading.gif'))?>' />");
                            jQuery.ajax({
                                type:'POST', 
                                url: "<?php echo esc_js(esc_url(admin_url('admin-ajax.php')));?>",
                                data: dataString,
                                success: function(response) {
                                    jQuery("#pagination").html(response);
                                }
                            });
                        }
                        function slider_show_next(page_id, total_pages){
                            //var dataString = 'action=media_pagination&id='+id+'&func='+func+'&page_id='+page_id+'&total_pages='+total_pages;
							var dataString = 'action=slider_media_pagination&page_id='+page_id+'&total_pages='+total_pages;
							/*if (func == 'slider') {
								var	pagination	= 'pagination_slider';
							} else {
								var	pagination	= 'pagination_clone';
							}*/
                            jQuery(".pagination_slider").html("<img src='<?php echo esc_js(esc_url(get_template_directory_uri()))?>/include/assets/images/ajax_loading.gif' />");
                            jQuery.ajax({
                                type:'POST', 
                                url: "<?php echo esc_js(esc_url(admin_url('admin-ajax.php')));?>",
                                data: dataString,
                                success: function(response) {
                                    jQuery(".pagination_slider").html(response);
                                }
                            });
                        }
						function refresh_media(id){
                             var dataString = 'action=media_pagination&id='+id+'&func=slider';
                            jQuery(".pagination_clone").html("<img src='<?php echo esc_js(esc_url(get_template_directory_uri()))?>/include/assets/images/ajax_loading.gif' />");
                            jQuery.ajax({
                                type:'POST', 
                                url: "<?php echo esc_js(esc_url(admin_url('admin-ajax.php')));?>",
                                data: dataString,
                                success: function(response) {
                                    jQuery(".pagination_clone").html(response);
                                }
                            });
                        }
						
						function refresh_media_slider(id){
                            var dataString = 'action=slider_media_pagination&id='+id+'&func=slider';
                            jQuery(".pagination_slider").html("<img src='<?php echo esc_js(esc_url(get_template_directory_uri()))?>/include/assets/images/ajax_loading.gif' />");
                            jQuery.ajax({
                                type:'POST', 
                                url: "<?php echo esc_js(esc_url(admin_url('admin-ajax.php')));?>",
                                data: dataString,
                                success: function(response) {
                                    jQuery(".pagination_slider").html(response);
                                }
                            });
                        }
                    </script>
                     <script>
                        jQuery(document).ready(function($) {
                            $(".gal-sortable-<?php echo esc_js($cs_galleryConter);?>").sortable({
                                cancel:'li div.poped-up',
                            });
                            //$(this).append("#gal-sortable").clone() ;
                            });
                            var counter = 0;
                            var count_items = <?php echo esc_js($cs_counter_gal)?>;
                            if ( count_items > 0 ) {
                                jQuery(".dragareamain") .addClass("noborder");	
                            }

                            function clone(path,id){
                                counter = counter + 1;
								var cls = 'gal-sortable-gallery_meta_form';
                                var dataString = 'path='+path+'&counter='+counter+'&action=gallery_clone';
                                jQuery("."+cls).append("<li id='loading'><img src='<?php echo esc_js(esc_url(get_template_directory_uri()))?>/include/assets/images/ajax_loading.gif' /></li>");
                                jQuery.ajax({
                                    type:'POST', 
                                    url: "<?php echo esc_js(esc_url(admin_url('admin-ajax.php')));?>",
                                    data: dataString,
                                    success: function(response) {
                                        jQuery("#loading").remove();
                                        jQuery("."+cls).append(response);
                                        count_items = jQuery("."+cls +' '+"li") .length;
                                            if ( count_items > 0 ) {
                                                jQuery(".dragareamain") .addClass("noborder");	
                                            }
                                    }
                                });
                            }
							
							function slider(path,id){
                                counter = counter + 1;
								var cls = 'gal-sortable-gallery_slider_meta_form';
                                var dataString = 'path='+path+'&counter='+counter+'&action=slider_clone';
                                jQuery("."+cls).append("<li id='loading'><img src='<?php echo esc_js(esc_url(get_template_directory_uri()))?>/include/assets/images/ajax_loading.gif' /></li>");
                                jQuery.ajax({
                                    type:'POST', 
                                    url: "<?php echo esc_js(esc_url(admin_url('admin-ajax.php')));?>",
                                    data: dataString,
                                    success: function(response) {
                                        jQuery("#loading").remove();
                                        jQuery("."+cls).append(response);
                                        count_items = jQuery("."+cls +' '+"li") .length;
                                            if ( count_items > 0 ) {
                                                jQuery(".dragareamain") .addClass("noborder");	
                                            }
                                    }
                                });
                            }
                             function del_this(div,id){
                                jQuery("#"+div+' '+"#"+id).remove();
                                count_items = jQuery("#gal-sortable li") .length;
                                    if ( count_items == 0 ) {
                                        jQuery(".dragareamain") .removeClass("noborder");	
                                    }
                            }
                    </script>
 					<?php if ( $cs_gallery_meta_form == 'gallery_slider_meta_form'){ ?>
							 <div id="pagination" class="pagination_slider"><?php slider_media_pagination($cs_gallery_meta_form,'slider');?></div>
                        <?php } else {?>
                             <div id="pagination" class="pagination_clone"><?php media_pagination($cs_gallery_meta_form,'clone');?></div>
                    <?php    
 					}   ?>
                   
                 <input type="hidden" name="<?php echo esc_attr($cs_gallery_meta_form);?>" value="1" />
                <div class="clear"></div>
            </div>
         </div>
    <?php	
}

//=====================================================================
// Adding Posts flexslider 
//=====================================================================
if ( ! function_exists( 'cs_post_flex_slider' ) ) {

	function cs_post_flex_slider($width,$height,$postid,$view){
		global $cs_node,$cs_theme_options,$cs_counter_node;
		$cs_post_counter = rand(40, 9999999);
		$cs_counter_node++;
		
		if ( $view == 'post-list' ){
			$viewMeta	= 'post';  
		} else {
			$viewMeta	= $view;
		}
		
		$cs_meta_slider_options = get_post_meta("$postid", $viewMeta, true); 
		$totaImages = '';
		$cs_xmlObject_flex = new SimpleXMLElement($cs_meta_slider_options);
		$as_node = new stdClass();
		?>
		<!-- Flex Slider -->
		<div class="flexslider" id="flexslider<?php echo esc_attr($cs_post_counter); ?>">
				<ul class="slides">
					<?php 
						$cs_counter = 1;
						
						if ( $view == 'post' || $view == 'csprojects'){
							$path	= 'cs_slider_path';
							$sliderData	= $cs_xmlObject_flex->children()->gallery_slider;
							$totaImages	= count($cs_xmlObject_flex->children()->gallery_slider);
						} else if ( $view == 'post-list' ){
							$path	= 'path';
							$sliderData	= $cs_xmlObject_flex->children()->gallery;
							$totaImages	= count($cs_xmlObject_flex->children()->gallery);
						} else {
							$path	= 'path';
							$sliderData	= $cs_xmlObject_flex->children();
							$totaImages	= count($cs_xmlObject_flex->children());
						}
						
						foreach ( $sliderData as $as_node ){
 							$image_url = cs_attachment_image_src($as_node->$path,$width,$height); 
							echo '<li>
									<figure>
			                        	<img src="'.esc_url($image_url).'" alt="">';
										if($as_node->title != '' && $as_node->description != '' || $as_node->title != '' || $as_node->description != ''){ ?>         
                                        	<figcaption>
                                            	<div class="container">
                                                	<?php if($as_node->title <> ''){?>
                                                        <h2 class="colr">
                                                            <?php 
                                                                if($as_node->link <> ''){ 
                                                                     echo '<a href="'.esc_url($as_node->link).'" target="'.esc_attr($as_node->link_target).'">' . esc_attr($as_node->title) . '</a>';
                            
                                                                } else {
                            
                                                                    echo esc_attr($as_node->title);
                                                                }
															?>
                                                        </h2>
													<?php }
														if($as_node->description <> ''){
															echo '<p>'.substr($as_node->description, 0, 220);
                                                            if ( strlen($as_node->description) > 220 ) echo "...</p>";
														}
													?>
                                            	</div>
                                           </figcaption>
                              <?php }?>

                            </figure>
                        </li>
					<?php 
					$cs_counter++;
					}
				?>
			  </ul>
		</div>
		<?php 
		if ( function_exists( 'cs_enqueue_flexslider_script' ) ) {
			//add_action( 'wp_enqueue_scripts', 'cs_enqueue_flexslider_script' );
			cs_enqueue_flexslider_script();
		}
		//cs_enqueue_flexslider_script(); ?>

		<!-- Slider height and width -->

		<!-- Flex Slider Javascript Files -->

		<script type="text/javascript">
			jQuery(window).load(function(){
				jQuery('#flexslider<?php echo esc_js($cs_post_counter); ?> .cs-flex-total-slides').html("<?php echo esc_js($totaImages);?> - ");
				var speed = '6000'; 
				var slidespeed ='500';
				jQuery('#flexslider<?php echo esc_js($cs_post_counter); ?>').flexslider({
					animation: "fade", // fade
					slideshow: true,
					//slideshowSpeed:speed,
					//animationSpeed:slidespeed,
					prevText:"<em class='icon-arrow-left'></em>",
					nextText:"<em class='icon-arrow-right'></em>",
					start: function(slider) {
						jQuery('.flexslider').fadeIn();
					}
				});
			});
		</script>
	<?php
	}
}

//======================================================================
// Adding Team start
//=====================================================================
if (!function_exists('cs_teams_shortcode')) {
	function cs_teams_shortcode($cs_atts, $content = "") {
		$cs_defaults = array( 'column_size'=>'1/1','cs_team_section_title' => '','cs_team_view' => 'default','cs_team_name' => '','cs_team_designation' => '','cs_team_title' => '','cs_team_profile_image' => '','cs_team_fb_url' => '','cs_team_twitter_url' => '','cs_team_googleplus_url' => '','cs_team_skype_url' => '','cs_team_email' => '','cs_teams_class' => '','cs_teams_animation' => '');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class  = cs_custom_column_class($column_size);
		
		$cs_CustomId	= '';
		$cs_view		= '';
		if ( isset( $cs_teams_class ) && $cs_teams_class ) {
			$cs_CustomId	= 'id="'.$cs_teams_class.'"';
		}
		
		
		if ( isset( $cs_team_view ) && $cs_team_view == 'thumb' ) {
			$cs_view	= 'round';
		}
		
		
		$html = '';
		
		$html	.= '<article class="cs-team '.$cs_view.'">';
		
		if (isset($cs_team_profile_image) && $cs_team_profile_image !=''){
			$html	.= '<figure>';
			$html	.= '<img alt="'.$cs_team_name.'" src="'. $cs_team_profile_image .'">';
			$html	.= '</figure>';
		}
		
		$html	.= '<div class="text">';
		
		if ( isset( $cs_team_name ) &&  $cs_team_name !='' ) { 
			$html	.= '<h2 class="cs-post-title">'.$cs_team_name.'</h2>';
		}
		
		if ( isset( $cs_team_designation ) &&  $cs_team_designation !='' ) { 
			$html	.= '<ul class="post-option">';
			$html	.= '<li>'.$cs_team_designation.'</li>';
			$html	.= '</ul>';
		}
		
		if (isset($content) && $content !=''){
			$html .='<p>'.do_shortcode($content).'</p>';
		}
				
		if ($cs_team_fb_url || $cs_team_twitter_url || $cs_team_googleplus_url || $cs_team_skype_url || $cs_team_email ) { 
			$html .= '<ul class="social-media">';
				if (isset($cs_team_fb_url) && $cs_team_fb_url !=''){
					$html .='<li><a href="'.esc_url($cs_team_fb_url).'" target="_blank"><i class="icon-facebook"></i></a></li>';
				}
				if (isset($cs_team_twitter_url) && $cs_team_twitter_url !=''){
					$html .='<li><a href="'.esc_url($cs_team_twitter_url).'" target="_blank"><i class="icon-twitter"></i></a></li>';
				}
				if (isset($cs_team_googleplus_url) && $cs_team_googleplus_url !=''){
					$html .='<li><a href="'.esc_url($cs_team_googleplus_url).'" target="_blank"><i class="icon-google-plus"></i></a></li>';
				}
				if (isset($cs_team_skype_url) && $cs_team_skype_url !=''){
					$html .='<li><a href="'.esc_url($cs_team_skype_url).'" target="_blank"><i class="icon-skype"></i></a></li>';
				}
				if (isset($cs_team_email) && $cs_team_email !='' && is_email($cs_team_email)){
					$html .='<li><a href="mailto:'.sanitize_email($cs_team_email).'" target="_blank"><i class="icon-envelope-o"></i></a></li>';
				}
			$html .='</ul>';
		}
		$html	.= '</div>';
		$html	.= '</article>';

		
		$section_title = '';
		if(trim($cs_team_section_title) <> ''){
			$section_title = '<div class="cs-section-title"><h2>'.$cs_team_section_title.'</h2></div>';
		}
		return '<div class="'.$cs_column_class.'" '.$cs_CustomId.'>'.$section_title.' '. $html . '</div>';
	}
	add_shortcode('cs_team', 'cs_teams_shortcode');
}
// Adding Team  End

//=====================================================================
// Adding Twitter Tweets start
//=====================================================================
if (!function_exists('cs_tweets_shortcode')) {

	function cs_tweets_shortcode($cs_atts, $content = "") {
		$cs_defaults = array( 'cs_column_size'=>'','cs_tweets_section_title' => '','cs_tweets_user_name' => 'default','cs_tweets_color' => '','cs_no_of_tweets' => '','cs_tweets_class' => '','cs_tweets_animation' => '','cs_custom_animation_duration' => '1');
		extract( shortcode_atts( $cs_defaults, $cs_atts ) );
		$cs_column_class  = cs_custom_column_class($cs_column_size);
		
		$cs_CustomId	= '';
		if ( isset( $cs_tweets_class ) && $cs_tweets_class ) {
			$cs_CustomId	= 'id="'.$cs_tweets_class.'"';
		}
		
		$cs_rand_id = rand(5, 999999);
		$html = '';
		$section_title = '';
		if ($cs_tweets_section_title && trim($cs_tweets_section_title) !='') {
			//$section_title	= '<div class="cs-section-title '.$cs_column_class.'"><h2>'.$cs_tweets_section_title.'</h2></div>';
		}
		$html .= '<div '.$cs_CustomId.' class="twitter-section '.$cs_tweets_class.'" >';
		$html .= "<div class='widget_slider'><div class='flexslider flexslider".$cs_rand_id."'><ul class='slides'>";
		$html .= cs_get_tweets($cs_tweets_user_name,$cs_no_of_tweets,$cs_tweets_color);
		$html.='</div>';
		if ( function_exists( 'cs_enqueue_flexslider_script' ) ) {
			//add_action( 'wp_enqueue_scripts', 'cs_enqueue_flexslider_script' );
			cs_enqueue_flexslider_script();
		}
		 //cs_enqueue_flexslider_script();
				$html.='<script type="text/javascript">
						jQuery(document).ready(function(){
							jQuery(".widget_slider .flexslider'.intval($cs_rand_id).'").flexslider({
								animation: "fade",
								slideshow: true,
								slideshowSpeed: 7000,
								animationDuration: 600,
								prevText:"<em class=\'icon-angle-up\'></em>",
								nextText:"<em class=\'icon-angle-down\'></em>",
								start: function(slider) {
									jQuery(".flexslider").fadeIn();
								}
							});
						});
					</script>';
		return $html;
	}
	add_shortcode('cs_tweets', 'cs_tweets_shortcode');
}

// Adding Twitter Tweets  End

//=====================================================================
// Get Twitter Tweets  Start
//=====================================================================
if (!function_exists('cs_get_tweets')) {
		function cs_get_tweets($cs_username,$cs_numoftweets,$cs_tweets_color = ''){
			global $cs_theme_options;
			
			$cs_username = html_entity_decode($cs_username);
 			$cs_numoftweets = $cs_numoftweets;		
	 		if($cs_numoftweets == ''){ $cs_numoftweets = 2;}
			if(strlen($cs_username) > 1){
				
					$cs_text ='';
					$cs_return = '';
					$cs_cacheTime = 10000;
					$cs_transName = 'latest-tweets';
					require_once get_template_directory() . '/include/theme-components/cs-twitter/twitteroauth.php';
					$cs_consumerkey = $cs_theme_options['cs_consumer_key'];
					$cs_consumersecret = $cs_theme_options['cs_consumer_secret'];
					$cs_accesstoken = $cs_theme_options['cs_access_token'];
					$cs_accesstokensecret = $cs_theme_options['cs_access_token_secret'];
 					$cs_connection = new TwitterOAuth($cs_consumerkey, $cs_consumersecret, $cs_accesstoken, $cs_accesstokensecret);
 					$cs_tweets = $cs_connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$cs_username."&count=".$cs_numoftweets);
					if(!is_wp_error($cs_tweets) and is_array($cs_tweets)){
						set_transient($cs_transName, $cs_tweets, 60 * $cs_cacheTime);
					}else{
						$cs_tweets = get_transient('latest-tweets');
					}
  					if(!is_wp_error($cs_tweets) and is_array($cs_tweets)){
						$cs_twitter_text_color = '';
						if(!empty($cs_tweets_color)){
							$cs_twitter_text_color = "style='color: $cs_tweets_color !important'";	
						}
						$cs_rand_id    = rand(5, 300);
						$cs_exclude	= 0;
						foreach($cs_tweets as $cs_tweet) {
								$cs_exclude++;
								//if($cs_exclude > 1 ){
								$cs_text = $cs_tweet->{'text'};
								foreach($cs_tweet->{'user'} as $cs_type => $cs_userentity) {
										if($cs_type == 'profile_image_url') {	
											$cs_profile_image_url = $cs_userentity;
										} else if($cs_type == 'screen_name'){
											$cs_screen_name = '<a href="https://twitter.com/' . $cs_userentity . '" target="_blank" class="colrhover" title="' . $cs_userentity . '">@' . $cs_userentity . '</a>';
										}
									}
									foreach($cs_tweet->{'entities'} as $cs_type => $cs_entity) {
										if($cs_type == 'hashtags') {
											foreach($cs_entity as $j => $hashtag) {
												$update_with = '<a href="https://twitter.com/search?q=%23' . $hashtag->{'text'} . '&amp;src=hash" target="_blank" title="' . $hashtag->{'text'} . '">#' . $hashtag->{'text'} . '</a>';
												$cs_text = str_replace('#'.$hashtag->{'text'}, $update_with, $cs_text);
											}
										} 
									} 
									$large_ts = time();
									$n = $large_ts - strtotime($cs_tweet->{'created_at'});
									if($n < (60)){ $posted = sprintf(__('%d seconds ago','lassic'),$n); }
									elseif($n < (60*60)) { $minutes = round($n/60); $posted = sprintf(_n('About a Minute Ago','%d Minutes Ago',$minutes,'lassic'),$minutes); }
									elseif($n < (60*60*16)) { $hours = round($n/(60*60)); $posted = sprintf(_n('About an Hour Ago','%d Hours Ago',$hours,'lassic'),$hours); }
									elseif($n < (60*60*24)) { $hours = round($n/(60*60)); $posted = sprintf(_n('About an Hour Ago','%d Hours Ago',$hours,'lassic'),$hours); }
									elseif($n < (60*60*24*6.5)) { $days = round($n/(60*60*24)); $posted = sprintf(_n('About a Day Ago','%d Days Ago',$days,'lassic'),$days); }
									elseif($n < (60*60*24*7*3.5)) { $weeks = round($n/(60*60*24*7)); $posted = sprintf(_n('About a Week Ago','%d Weeks Ago',$weeks,'lassic'),$weeks); } 
									elseif($n < (60*60*24*7*4*11.5)) { $months = round($n/(60*60*24*7*4)) ; $posted = sprintf(_n('About a Month Ago','%d Months Ago',$months,'lassic'),$months);}
									elseif($n >= (60*60*24*7*4*12)){$years=round($n/(60*60*24*7*52)) ; $posted = sprintf(_n('About a year Ago','%d years Ago',$years,'lassic'),$years);}
									$cs_return .='<li><div class="text" style="color:'.$cs_tweets_color.'"><i class="icon-twitter2"></i>';
									$cs_return .= "" . $cs_text . "";
								//$cs_return .= "<p><a href='https://twitter.com/".$cs_username."'>@" . $cs_username . "</a></p>";
									$cs_return .= '<time datetime="2011-01-12" style="color:'.$cs_tweets_color.'">('. $posted. ')</time>';
									$cs_return .="</div></li>";

							//	}
						}
						$cs_return .= "</ul></div>";
						//if(isset($cs_profile_image_url) && $cs_profile_image_url <> ''){$cs_profile_image_url = '<img src="'.$cs_profile_image_url.'" alt="">';} else {$cs_profile_image_url = '';}
						$cs_return .= '<div class="follow-on">
                        			<div class="cs-tweet">
										<i class="icon-twitter"></i>
										<a href="https://twitter.com/'.$cs_username.'" target="_blank"  style="color:'.$cs_tweets_color.'">@'. $cs_username .'</a>
                        			</div>
                   				</div>';
				
				$cs_return .= "</div>";
				return  $cs_return;

 		}else{
			if(isset($cs_tweets->errors[0]) && $cs_tweets->errors[0] <> ""){
				return  '<div class="cs-twitter item" data-hash="dummy-one"><h4>'.$cs_tweets->errors[0]->message.". Please enter valid Twitter API Keys </h4></div>";
			}else{
				return  '<div class="cs-twitter item" data-hash="dummy-two"><h4>No Tweets Found.</h4></div>';
			}
		}
	}else{ 	
			return  '<div class="cs-twitter item" data-hash="dummy-three"><h4>No Tweets Found</h4></div>';
		}
  }
}