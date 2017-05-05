<?php
/**
 * The template for displaying all single posts
 */
 	global $cs_node,$post,$cs_theme_options,$cs_counter_node;
	
	$cs_uniq = rand(40, 9999999);

	$cs_node = new stdClass();
  	get_header();
 	$cs_layout = '';
	$leftSidebarFlag	= false;
	$rightSidebarFlag	= false;
	if (have_posts()):
	while (have_posts()) : the_post();	
		$cs_tags_name = 'post_tag';
		$cs_categories_name = 'category';
		$postname = 'post';
		$image_url = cs_get_post_img_src($post->ID, 844, 475);	
		$post_xml = get_post_meta($post->ID, "post", true);	
		if ( $post_xml <> "" ) {
			$cs_xmlObject = new SimpleXMLElement($post_xml);
			$cs_layout 			= $cs_xmlObject->sidebar_layout->cs_page_layout;
			$cs_sidebar_left 	= $cs_xmlObject->sidebar_layout->cs_page_sidebar_left;
			$cs_sidebar_right   = $cs_xmlObject->sidebar_layout->cs_page_sidebar_right;
			if(isset($cs_xmlObject->cs_related_post))
				$cs_related_post = $cs_xmlObject->cs_related_post;
			else 
				$cs_related_post = '';
			if(isset($cs_xmlObject->cs_post_tags_show))
				$post_tags_show = $cs_xmlObject->cs_post_tags_show;
			else 
				$post_tags_show = '';
				
			if(isset($cs_xmlObject->post_social_sharing))
				$cs_post_social_sharing = $cs_xmlObject->post_social_sharing;
			else 
				$cs_post_social_sharing = '';
			if ( $cs_layout == "left") {
				$cs_layout = "page-content blog-editor";
				$custom_height = 459;
				$leftSidebarFlag	= true;
			}
			else if ( $cs_layout == "right" ) {
				$cs_layout = "page-content blog-editor";
				$custom_height = 459;
				$rightSidebarFlag	= true;
			}
			else {
				$cs_layout = "page-content-fullwidth";
				$custom_height = 459;
			}
			$postname = 'post';
		}else{
			$cs_layout 	=  $cs_theme_options['cs_single_post_layout'];
			if ( isset( $cs_layout ) && $cs_layout == "sidebar_left") {
				$cs_layout = "page-content blog-editor";
				$cs_sidebar_left	= $cs_theme_options['cs_single_layout_sidebar'];
				$custom_height = 459;
				$leftSidebarFlag	= true;
			} else if ( isset( $cs_layout ) && $cs_layout == "sidebar_right" ) {
				$cs_layout = "page-content blog-editor";
				$cs_sidebar_right	= $cs_theme_options['cs_single_layout_sidebar'];
				$custom_height = 459;
				$rightSidebarFlag	= true;
			} else {
				$cs_layout = "page-content-fullwidth";
				$custom_height = 459;
			}
			$post_pagination_show = 'on';
			$post_tags_show = '';
			$cs_related_post = '';
			$post_social_sharing = '';
			$post_social_sharing = '';
			$postname = 'post';
			$cs_post_social_sharing = '';
		}
		if ($post_xml <> "") {
			$cs_xmlObject = new SimpleXMLElement($post_xml);
			$post_view = $cs_xmlObject->post_thumb_view;
			$inside_post_view = $cs_xmlObject->inside_post_thumb_view;
			$post_video = $cs_xmlObject->inside_post_thumb_video;
			$post_audio = $cs_xmlObject->inside_post_thumb_audio;
			$post_slider = $cs_xmlObject->inside_post_thumb_slider;
			$post_featured_image = $cs_xmlObject->inside_post_featured_image_as_thumbnail;
			$cs_related_post = $cs_xmlObject->cs_related_post;
			$cs_post_social_sharing = $cs_xmlObject->post_social_sharing;
			$post_tags_show = $cs_xmlObject->post_tags_show;
			$post_pagination_show = $cs_xmlObject->post_pagination_show;
			$cs_bg_image = $cs_xmlObject->cs_bg_image;
			$postname = 'post';
		}
		else {
			$cs_xmlObject = new stdClass();
			$post_view = '';
			$post_video = '';
			$post_audio = '';
			$post_slider = '';
			$post_slider_type = '';
			$cs_related_post = '';
			$post_pagination_show = '';
			$image_url = '';
			$width = 0;
			$height = 0;
			$image_id = 0;
			$postname = 'post';
			$cs_xmlObject->post_social_sharing = '';
			$cs_bg_image = '';
		}		
		$custom_height  = 459;	
		$width 			= 818;
		$height 		= 460;
		$image_url 		= cs_get_post_img_src($post->ID, $width, $height);
	?>
	<!-- PageSection Start -->
	<div class="page-section" style=" padding-top:40px; margin-top:-40px; background:url(<?php echo esc_url($cs_bg_image); ?>) no-repeat center top;"> 
  		<!-- Container -->
        <div class="container"> 
        	<!-- Row -->
            <div class="row">
                 <!--Left Sidebar Starts-->
                <?php if ($leftSidebarFlag == true){ ?>
           	    	<aside class="page-sidebar">
            	    	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar_left) ) : ?>
                		<?php endif; ?>
                	</aside>
                <?php } ?>
                <!--Left Sidebar End--> 
                <!-- Blog Detail Start -->
                <div class="<?php echo esc_attr($cs_layout); ?>"> 
                    <div class="col-md-12">
                    <?php 
                        if (isset($inside_post_view) and $inside_post_view <> '') {
                            if( $inside_post_view == "Slider"){
                                echo ' <figure class="cs-detailpost">';
                                    cs_post_flex_slider($width,$height,get_the_id(),'post');
                                echo '</figure>';
                            } else if ($inside_post_view == "Single Image" && $image_url <> '') { 
                                echo '<figure  class="cs-detailpost">';
                                    echo '<img src="'.$image_url.'" alt="" >';
                                echo '</figure>';
                            } elseif ( $inside_post_view == "Video" and $post_video <> '' and $inside_post_view <> '' ) {
                                 echo '<figure class="cs-detailpost">';
                                    $url = parse_url($post_video);
                                    if($url['host'] == $_SERVER["SERVER_NAME"]) {
                                    echo do_shortcode('[video width="'.$width.'" height="'.$height.'" mp4="'.$post_video.'"][/video]');
                                } else {
                                    $video	= wp_oembed_get($post_video,array('height' => $custom_height));
                                    $search = array('webkitallowfullscreen', 'mozallowfullscreen','scrolling="no"','frameborder="0"');
                                    $video	=  str_replace($search,'',$video);
                                    $video	=  str_replace('frameborder="no"','',$video);
                                    $characters = array('&');
                                    echo str_replace($characters,'&amp;',$video);
                                }
                               echo '</figure>';
                        } elseif ($inside_post_view == "Audio" and $inside_post_view <> ''){  
                        ?>
                             <figure class="cs-detailpost">
                              <?php echo do_shortcode('[audio mp3="'.$post_audio.'"][/audio]');?>
                             </figure>
                    <?php    
                        }
                    }
                    ?>
                    <div class="cs-post-panel">
                        <h2><?php the_title(); ?></h2>
                        <ul class="cs-category">
                            <?php  
                                $categories_list = get_the_term_list ( get_the_id(), 'category', '', '', '' );
                                  if ( $categories_list ){
                                      echo '<li>';
                                          printf( __( '%1$s', 'lassic'),$categories_list );
                                      echo '<i class="icon-arrow-down10"></i></li>';
                                }
                            ?>
                        </ul> 
                        <ul class="cs-post-options">
                            <!--<li><i class="icon-user9"></i><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a></li>-->
                            <li><i class=" icon-calendar11"></i><time datetime="<?php echo get_the_date('y-m-d',get_the_id()); ?>">
                                <?php echo get_the_date('F d ,Y',get_the_id()); ?></time>
                            </li>
                            <li><i class=" icon-comment2"></i>
                                <span><?php echo comments_number(__('0', 'lassic'), __('1', 'lassic'), __('%', 'lassic') );?></span>
                                <a href="<?php comments_link(); ?>"><?php _e('Comments','lassic');?></a>
                            </li>
                        </ul>
                        <div class="cs-editor-text cs-blog-editor lightbox">
                            <?php 
                                the_content();
                                wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'lassic' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); 
                                if(isset($post_tags_show) &&  $post_tags_show == 'on'){ 
                                    $categories_list = get_the_term_list ( get_the_id(), 'post_tag', '<li>', '</li><li>', '</li>' );
                                if ( $categories_list ){?>
                                    <div class="cs-tags">
                                        <i class="icon-tags2"></i>
                                        <ul><?php printf( __( '%1$s', 'lassic'),$categories_list );?></ul>
                                    </div>
                                <?php } 
                             }?>  
                        </div>
                        <?php
                         $thumb_ID = get_post_thumbnail_id( $post->ID );
                         if ( $images = get_children(array(
                           'post_parent' => get_the_ID(),
                           'post_type' => 'attachment',
                          // 'post_mime_type' => 'image',
                           'exclude' => $thumb_ID,
                          ))) { 
                        ?>
                            <div class="cs-attachments">
                              <h6><?php _e('Attachments','lassic');?></h6>
                              <ul>
                                <?php 
                                  foreach( $images as $image ) {  ?>
                                    <li>
                                    <?php if ( $image->post_mime_type == 'image/png' 
                                            || $image->post_mime_type == 'image/gif' 
                                            || $image->post_mime_type == 'image/jpg'
                                            || $image->post_mime_type == 'image/jpeg'
                                          ) { 
                                            
                                            $image_url = cs_attachment_image_src($image->ID, 358, 202 );
                                            
                                            ?>
                                             <figure> <a href="<?php echo esc_url($image->guid);?>"><img src="<?php echo esc_url($image_url);?>" alt="<?php echo esc_attr($image->post_name);?>"></a> </figure>
                                             <?php } else if ( $image->post_mime_type == 'application/zip' ) { ?>
                                                            <figure> <a href="<?php echo esc_url($image->guid);?>"><i class="icon-file-zip-o"></i></a> </figure>
                                             <?php }else if ( $image->post_mime_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ) { ?>
                                                            <figure> <a href="<?php echo esc_url($image->guid);?>"><i class="icon-file-word-o"></i></a> </figure>
                                             <?php } else if ( $image->post_mime_type == 'text/plain' ) { ?>
                                                            <figure> <a href="<?php echo esc_url($image->guid);?>"><i class="icon-file-text"></i></a> </figure>
                                             <?php } else if ( $image->post_mime_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ) { ?>
                                                            <figure> <a href="<?php echo esc_url($image->guid);?>"><i class="icon-file-excel-o"></i></a> </figure>
                                             <?php } else { ?>
                                                            <figure> <a href="<?php echo esc_url($image->guid);?>"><i class="icon-play6"></i></a> </figure>
                                             <?php } ?>
                                    </li>
                                <?php } ?>
                              </ul>
                           </div>
                        <?php }
                            if(isset($cs_post_social_sharing) and $cs_post_social_sharing <> ''){?>
                                <div class="cs-social-share">
                                    <?php cs_social_share(false,true,'yes'); ?>
                                </div>
                        <?php } ?>
                    </div>
                    <?php 
                        if(isset($post_pagination_show) &&  $post_pagination_show == 'on'){
                            cs_next_prev_custom_links('post');
                        }
                    ?>
                    <!-- Col Recent Posts Start -->
                    <?php 
                    if($cs_related_post =='on'){
                        if ( empty($cs_xmlObject->cs_related_post_title) ) 
                            $cs_related_post_title = __('Related Posts', 'lassic'); else $cs_related_post_title = $cs_xmlObject->cs_related_post_title;
						$cs_related_post_title = __('Related Posts', 'lassic');
                        ?>
                        <div class="cs-related-post cs-blog cs-blog-grid">
                            <div class="cs-section-title"><h2><?php echo esc_attr($cs_related_post_title);?></h2></div>
                                <div class="row">
                                   <?php 
                                      $custom_taxterms='';
                                      $width  = 358;
                                      $height = 202;
                                      $cs_title_limit  = 200 ;
                                      $custom_taxterms = wp_get_object_terms( $post->ID, array($cs_categories_name, $cs_tags_name), array('fields' => 'ids') );
                                      $args = array(
                                          'post_type' => $postname,
                                          'post_status' => 'publish',
                                          'posts_per_page' => 3,
                                          'orderby' => 'DESC',
                                          'tax_query' => array(
                                              'relation' => 'OR',
                                              array(
                                                  'taxonomy' => $cs_tags_name,
                                                  'field' => 'id',
                                                  'terms' => $custom_taxterms
                                              ),
                                              array(
                                                  'taxonomy' => $cs_categories_name,
                                                  'field' => 'id',
                                                  'terms' => $custom_taxterms
                                              )
                                          ),
                                          'post__not_in' => array ($post->ID),
                                      );
                                     $custom_query = new WP_Query($args);
                                     while ($custom_query->have_posts()) : $custom_query->the_post();
                                        $cs_thumbnail = cs_get_post_img_src( $post->ID, $width, $height );					 
                                        ?>
                                        <article class="col-md-4">
                                            <?php cs_post_image($cs_thumbnail); ?>
                                            <div class="cs-bloginfo-sec">
                                                <h4><a href="<?php esc_url(the_permalink());?>"><?php cs_get_title($cs_title_limit); ?></a></h4>
                                                <!--
                                                <div class="cs-blog-text">
                                                    <ul class="cs-post-options">
                                                        <li>
                                                            <i class="icon-user9"></i>
                                                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                                                <?php the_author(); ?>
                                                            </a>
                                                        
                                                        </li>                               
                                                    </ul>
                                                </div>
                                                -->
                                            </div>
                                        </article>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </div>
                            </div>
                    <?php } ?>
                     <!-- Col Comments Start -->
                    <?php comments_template('', true); ?>
                    <!-- Col Comments End --> 
                     </div>
                 </div>
                <?php if ($rightSidebarFlag == true){ ?>
                    <aside class="page-sidebar">
                        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar_right) ) : endif; ?>
                    </aside>
                <?php } ?>
             </div>
      	</div>
	</div>
    <?php 
		endwhile;   
    	endif; 
	?>
	<!-- PageSection End --> 
	<!-- Footer -->
	<?php get_footer(); ?>