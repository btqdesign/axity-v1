<?php
/**
 * The template for displaying all single posts
 */
 	global $cs_node,$post,$cs_theme_options,$cs_counter_node;
	
	$cs_uniq = rand(40, 9999999);
	$cs_node = new stdClass();
  	get_header();
 	?>
	<!-- PageSection Start -->
	<div class="page-section team-detail" style=" padding:0; "> 
  	<!-- Container -->
  	<div class="container"> 
    	<!-- Row -->
    	<div class="row">
      	<?php
		if (have_posts()):
		while (have_posts()) : the_post();	
		$postname = 'member';
  		$post_xml = get_post_meta($post->ID, "member", true);	
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
				if(isset($cs_xmlObject->cs_post_author_info_show))
					 $cs_post_author_info_show = $cs_xmlObject->cs_post_author_info_show;
				else 
					$cs_post_author_info_show = '';
					$postname = 'member';
			}else{
  				$post_pagination_show = 'on';
				$post_tags_show = '';
				$cs_related_post = '';
				$post_social_sharing = '';
				$post_social_sharing = '';
				$cs_post_author_info_show = '';
				$postname = 'member';
				$cs_post_social_sharing = '';
			}
			if ($post_xml <> "") {
				$cs_xmlObject = new SimpleXMLElement($post_xml);
				$cs_team_designation = $cs_xmlObject->cs_team_designation;
				$postname = 'member';
			}
			else {
				$cs_xmlObject = new stdClass();
				$cs_team_designation = '';
				$postname = 'member';
			}		
		$width	= 0;
		$height	= 0;
		$image_url = cs_get_post_img_src($post->ID, $width, $height);
		$cs_content_section	= 'element-size-100';
		
		if ( $image_url == '') {
			$image_url = get_template_directory_uri().'/assets/images/no-image.jpg';
		}
		
		$cs_is_designation = false;
		?>
     	 <!-- Team Detail Start -->
        <div class=" col-md-3"> 
	        <figure class="cs-team-featured"><img src="<?php echo esc_attr( $image_url );?>" alt="" ></figure>
        </div>
        <div class="col-md-9">
          <div class="cs-team-detail">
              <div class="rich_editor_text lightbox">
                 <?php 
                      the_content();
                      wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'lassic' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); 
                  ?>
                <?php 
                  if ( isset( $cs_xmlObject->social_media ) && is_object( $cs_xmlObject ) && count( $cs_xmlObject->social_media )>0 ) {?>
                       <div class="cs-socialmedia">
                        <ul>
                          <?php 
                              foreach ( $cs_xmlObject as $social ){
                              if ( $social->getName() == "social_media" ) {
        
                                  $cs_social_title 		= $social->social_title;
                                  $cs_social_icon 		= $social->social_icon;
                                  $cs_url 				= $social->target_url;
                                  ?>
                                  <li><a data-original-title="<?php echo esc_attr( $cs_social_title );?>" href="<?php echo esc_url( $cs_url );?>"><i class="<?php echo esc_attr( $cs_social_icon );?>"></i></a></li>
                              <?php 
                              }
                          }?>
                       </ul>
                      </div>
                  <?php }?>
                </div>
              <div class="cs-spreater cs-team-spreater">
                  <div class="spreater-inn">
                      <img src="<?php echo get_template_directory_uri();?>/assets/images/spreater1.png" alt="">
                  </div>
              </div>
              <div class="cs-teacher-information">
                  <div class="cs-section-title"><h2><?php _e('Employee information','lassic');?></h2></div>
                  	<?php 
						if( isset( $cs_team_designation ) && $cs_team_designation !='' ) {
                    		$cs_background	= 'style=background-color:#f8f5f8;';
                       	 $cs_is_designation	= true;
                   		?>
                       <article>
                          <span><?php _e('Designation','lassic');?></span>
                          <h3><?php echo esc_attr( $cs_team_designation );?></h3>
                       </article>
                   <?php }
                      if ( isset( $cs_xmlObject->social_media ) && is_object( $cs_xmlObject ) && count( $cs_xmlObject->social_media )>0 ) {
						  if ( isset ( $cs_is_designation ) && $cs_is_designation == true ){
                          	 $cs_count		= 1; 
                            } else {
                            	$cs_count		= 0; 
                            }
                               foreach ( $cs_xmlObject as $social ){
                              $cs_background	= '';
                              if ( $social->getName() == "dynamic_fields" ) { 
                                  $cs_fields_title 		= $social->dynamic_fields_title;
                                  $cs_description 		= $social->dynamic_fields_description;
                                  if ( isset( $cs_description ) && $cs_description !='' ) {
                                      $cs_count++;
                                      if ( $cs_count % 2 != 0 ){
                                          //$cs_background	= 'style=background-color:#f8f5f8;';
                                      }
                                      $cs_description = do_shortcode($cs_description);
                                  ?>
                                  <article>
                                      <span><?php echo esc_attr( $cs_fields_title );?></span>
                                      <h3><?php echo nl2br( $cs_description );?></h3>
                                  </article>
                         <?php }
                             }
                           }
						 }?>
            </div>
          </div>
         </div>
           
      <?php 
	  endwhile;   
	  endif; 
	  ?>
      </div>
    </div>
</section>
<!-- PageSection End --> 
<!-- Footer -->
<?php get_footer(); ?>