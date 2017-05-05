<?php
/**
 * The template for Home page
 
 */
 
 get_header();
 global $cs_node,$cs_theme_options,$cs_counter_node;

	if(isset($cs_theme_options['cs_excerpt_length']) && $cs_theme_options['cs_excerpt_length'] <> ''){ $default_excerpt_length = $cs_theme_options['cs_excerpt_length']; }else{ $default_excerpt_length = '255';}; 
			
    $cs_layout     =  $cs_theme_options['cs_default_page_layout'];
    if ( isset( $cs_layout ) && ($cs_layout == "sidebar_left" || $cs_layout == "sidebar_right")) {
    	$cs_page_layout = "page-content";
    } else {
    	$cs_page_layout = "page-content-fullwidth";
    }
	$cs_sidebar    = $cs_theme_options['cs_default_layout_sidebar'];
	$cs_tags_name = 'post_tag';
	$cs_categories_name = 'category';

	?>   
       <div class="page-section" style="padding:0;">
            <!-- Container -->
            <div class="container">
                <!-- Row -->
              <div class="row">     
                <!--Left Sidebar Starts-->
				 <?php if ($cs_layout == 'sidebar_left'){ ?>
                    <div class="page-sidebar"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar) ) : ?><?php endif; ?></div>
                <?php } ?>
                <!--Left Sidebar End-->
                <!-- Page Detail Start -->
        		<div class="<?php echo esc_attr($cs_page_layout); ?>">
                    <div class="cs-blog cs-blog-medium">
                      <?php 
                      if (empty($_GET['page_id_all']))
                                $_GET['page_id_all'] = 1;
                            if (!isset($_GET["s"])) {
                                $_GET["s"] = '';
                            }
                            
                      $cs_description = 'yes';
                      $cs_excerpt	 = '255'; 
                      $width = '382';
                      $height = '286';
                      $cs_title_limit = 1000;
                      if ( have_posts() ) : 
                      $postCounter	= 0;
                      $post_thumb_view = 'Single Image';
                    
                        while ( have_posts() ) : the_post();
                          $postCounter++;
                          $cs_thumbnail = cs_get_post_img_src( $post->ID, $width, $height );
                          $post_xml = get_post_meta(get_the_id(), "post", true);
                          if ( $post_xml <> "" ) {
                              $cs_xmlObject = new SimpleXMLElement($post_xml);
                              $post_thumb_view = $cs_xmlObject->post_thumb_view;
                          }
                          ?>
                          <article class="col-md-12">
                              <?php if ( $post_thumb_view == 'Single Image' ){
                                      if ( isset( $cs_thumbnail ) && $cs_thumbnail !='' ) {
                                          cs_post_image($cs_thumbnail);
                                      }
                                    }
									
                              ?>
                              <div class="cs-bloginfo-sec">
                                  <h2>
                                  	<a href="<?php esc_url(the_permalink());?>">
								  		<?php cs_get_title($cs_title_limit); ?>
									</a>
                                  </h2>
                                  <?php if ($cs_description == 'yes') {?><p> <?php echo cs_get_the_excerpt($default_excerpt_length,'ture','');?></p><?php } ?>									 								 <div class="cs-blog-text">
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
                                      	<?php cs_featured(); ?>
                                          <li><i class="icon-user9"></i><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a></li>
                                          <li><i class=" icon-calendar11"></i><time datetime="<?php echo get_the_date('Y-m-d',$post->ID);?>">
                                          <?php echo get_the_date('F d,Y',$post->ID);?></time>
                                          </li>
                                          <li><i class=" icon-comment2"></i>
                                              <span><?php echo comments_number(__('0', 'lassic'), __('1', 'lassic'), __('%', 'lassic') );?></span>
                                              <a href="<?php comments_link(); ?>"><?php _e('Comments','lassic');?></a>
                                          </li>
                                      </ul>
                                    </div>
                                </div>
                          </article>
                          <?php
                          endwhile;
                      else:
                           if ( function_exists( 'cs_no_result_found' ) ) { cs_no_result_found(); }
                      endif; 
                          $qrystr = '';
                          if ( isset($_GET['page_id']) ) $qrystr .= "&page_id=".$_GET['page_id'];
                          if ($wp_query->found_posts > get_option('posts_per_page')) {
                             if ( function_exists( 'cs_pagination' ) ) { echo cs_pagination(wp_count_posts()->publish,get_option('posts_per_page'), $qrystr); } 
                          }
                      ?>
                      </div>
             	</div>
             	<!-- Page Detail End -->
                
                <!-- Right Sidebar Start -->
                <?php if ( $cs_layout  == 'sidebar_right'){ ?>
                   <div class="page-sidebar"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar) ) : ?><?php endif; ?></div>
                <?php } ?>
                <!-- Right Sidebar End -->
   			</div> 	
        </div>
  </div>
<?php get_footer(); ?>