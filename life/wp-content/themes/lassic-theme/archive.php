<?php
/**
 * The template for displaying Achive(s) pages
 *
 * @package Lassic
 * @since Lassic  1.0
 * @Auther Chimp Solutions
 * @copyright Copyright (c) 2014, Chimp Studio 
 */
	get_header();

	global $cs_node,$cs_theme_options,$cs_counter_node;
	
	$cs_layout 	= '';
	if(isset($cs_theme_options['cs_excerpt_length']) && $cs_theme_options['cs_excerpt_length'] <> ''){ 
        $default_excerpt_length = $cs_theme_options['cs_excerpt_length']; }else{ $default_excerpt_length = '255';
    }
    $cs_layout = isset($cs_theme_options['cs_default_page_layout']) ? $cs_theme_options['cs_default_page_layout']:'';
     if ( isset( $cs_layout ) && ($cs_layout == "sidebar_left" || $cs_layout == "sidebar_right")) {
        $cs_page_layout = "page-content";
     } else {
        $cs_page_layout = "page-content-fullwidth";
     }
    $cs_sidebar    = isset($cs_theme_options['cs_default_layout_sidebar']) ? $cs_theme_options['cs_default_layout_sidebar']:'';
			$cs_tags_name = 'post_tag';
			$cs_categories_name = 'category';
 ?>
	<!-- PageSection Start -->
    <div class="page-section" style=" padding: 0; ">
        <!-- Container -->
        <div class="container">
            <!-- Row -->
            <div class="row">
                 <!--Left Sidebar Starts-->
                <?php if ($cs_layout == 'sidebar_left'){ ?>
                    <div class="page-sidebar"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar) ) : ?><?php endif; ?></div>
                <?php } ?>
                <!--Left Sidebar End-->
                 <div class="<?php echo esc_attr(sanitize_html_class($cs_page_layout)); ?>">
                    <div class="cs-blog cs-blog-medium">
                    <!-- Blog Post Start -->
                    <?php 
                    if(is_author()){
                        global $author;
                       $userdata = get_userdata($author);
                    }
                    if(category_description() || is_tag() || (is_author() && isset($userdata->description) && !empty($userdata->description))){
                        echo '<div class="widget evorgnizer">';
                        if(is_author()){
                        ?>
                          <figure><a><?php echo get_avatar($userdata->user_email, apply_filters('cs_author_bio_avatar_size', 70));?></a></figure>
                          <div class="left-sp">
                              <h5><a><?php echo esc_attr($userdata->user_nicename); ?></a></h5>
                              <p><?php echo balanceTags($userdata->description, true); ?></p>
                          </div>
                        <?php } elseif ( is_category()) {
                            $category_description = category_description();
                             if ( ! empty( $category_description ) ) {
                          ?>
                          <div class="left-sp">
                              <p><?php  echo category_description();?></p>
                          </div>
                         <?php }
                         } elseif(is_tag()){  
                          $tag_description = tag_description();
                          if ( ! empty( $tag_description ) ) {
                          ?>
                          <div class="left-sp">
                              <p><?php echo apply_filters( 'tag_archive_meta', $tag_description );?></p>
                          </div>
                      <?php }
                    }
                    echo '</div>';
                    }
                    if (empty($_GET['page_id_all']))
                      $_GET['page_id_all'] = 1;
                    if (!isset($_GET["s"])) {
                      $_GET["s"] = '';
                    }
                    $description = 'yes';
                    $taxonomy = 'category';
                    $taxonomy_tag = 'post_tag';
                    $args_cat = array();
                    if(is_author()){
                      $args_cat = array('author' => $wp_query->query_vars['author']);
                      $post_type = array( 'post',);
                    } elseif(is_date()){
                      if(is_month() || is_year() || is_day() || is_time()){
                          $args_cat = array('m' => $wp_query->query_vars['m'],'year' => $wp_query->query_vars['year'],'day' => $wp_query->query_vars['day'],'hour' => $wp_query->query_vars['hour'], 'minute' => $wp_query->query_vars['minute'], 'second' => $wp_query->query_vars['second']);
                      }
                      $post_type = array( 'post');
                    } else if ((isset( $wp_query->query_vars['taxonomy']) && !empty( $wp_query->query_vars['taxonomy'] )) ) {
                      $taxonomy = $wp_query->query_vars['taxonomy'];
                      $taxonomy_category='';
                      $taxonomy_category=$wp_query->query_vars[$taxonomy];
                      if ( $wp_query->query_vars['taxonomy']=='member-category') {
                        $args_cat = array( $taxonomy => "$taxonomy_category");
                        $post_type='member';
                      }else if ( $wp_query->query_vars['taxonomy']=='project-category') {
                        $args_cat = array( $taxonomy => "$taxonomy_category");
                        $post_type='project';
                      }else {
                          $taxonomy = 'category';
                          $args_cat = array();
                          $post_type='post';
                      }
                    } else if( is_category() ) {
                      $taxonomy = 'category';
                      $args_cat = array();
                      $category_blog = $wp_query->query_vars['cat'];
                      $post_type='post';
                      $args_cat = array( 'cat' => "$category_blog");
                    
                    } else if ( is_tag() ) {
                      
                      $taxonomy = 'category';
                      $args_cat = array();
                      $tag_blog = $wp_query->query_vars['tag'];
                      $post_type='post';
                      $args_cat = array( 'tag' => "$tag_blog");
                    
                    } else {
                      $taxonomy = 'category';
                      $args_cat = array();
                      $post_type='post';
                    }
                    
                    $args = array( 
                    'post_type'		 => $post_type, 
                    'paged'			 => $_GET['page_id_all'],
                    'post_status'	 => 'publish', 
                    'order'			 => 'ASC',
                    );
                    
                    $args = array_merge( $args_cat,$args );
                    $custom_query = new WP_Query( $args );
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
                          $post_thumb_audio = $cs_xmlObject->post_thumb_audio;
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
                              <?php if ($cs_description == 'yes') {?><p> <?php echo cs_get_the_excerpt($default_excerpt_length,'ture','');?></p><?php } ?>									 								 	<div class="cs-blog-text">
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
                                        <!--<li><i class="icon-user9"></i><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                            <?php the_author(); ?></a>
                                        </li>-->
                                        <li><i class=" icon-calendar11"></i><time datetime="<?php echo get_the_date('Y-m-d',$post->ID);?>">
                                        <?php echo get_the_date('F d,Y',$post->ID);?></time>
                                        </li>
                                        <!--<li><i class=" icon-comment2"></i>
                                          <span><?php echo comments_number(__('0', 'lassic'), __('1', 'lassic'), __('%', 'lassic') );?></span>
                                          <a href="<?php comments_link(); ?>"><?php _e('Comments','lassic');?></a>
                                        </li>-->
                                    </ul>
                                </div>
                            </div>
                      </article>
                      <?php
                      endwhile;
                      wp_reset_query();
                    else:
                       if ( function_exists( 'cs_no_result_found' ) ) { cs_no_result_found(); }
                    endif; 
                    $qrystr = '';
                    // pagination start
                      if ($custom_query->found_posts > get_option('posts_per_page')) {
                       if ( isset($_GET['page_id']) ) $qrystr .= "&page_id=".$_GET['page_id'];
                       if ( isset($_GET['author']) ) $qrystr .= "&author=".$_GET['author'];
                       if ( isset($_GET['tag']) ) $qrystr .= "&tag=".$_GET['tag'];
                       if ( isset($_GET['cat']) ) $qrystr .= "&cat=".$_GET['cat'];
                       if ( isset($_GET['member-category']) ) $qrystr .= "&member-category=".$_GET['member-category'];
                       if ( isset($_GET['project-category']) ) $qrystr .= "&project-category=".$_GET['project-category'];
                       if ( isset($_GET['m']) ) $qrystr .= "&m=".$_GET['m'];
                         echo cs_pagination($custom_query->found_posts,get_option('posts_per_page'), $qrystr); 
                     }
                    ?>
                    </div>
                 </div>
                <!-- Right Sidebar Start -->
                <?php if ( $cs_layout  == 'sidebar_right'){ ?>
                   <div class="page-sidebar"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar) ) : ?><?php endif; ?></div>
                <?php } ?>
            </div>
         </div>
    </div>
<?php get_footer(); ?> 