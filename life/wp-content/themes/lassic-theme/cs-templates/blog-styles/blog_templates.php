<?php 
/**
 * File Type: Blog Templates Class
 */
 

if ( !class_exists('cs_blog_templates') ) {
	
	class cs_blog_templates
	{
		function __construct()
		{
			// Constructor Code here..
		}
		//======================================================================
		// Blog Large View
		//======================================================================
		public function cs_large_view( $description,$excerpt,$cs_category,$query ) {
			global $post;
			$width = '790';
			$height = '464';
			$cs_title_limit = 1000;
 			if ( $query->have_posts() ) {  
 				while ( $query->have_posts() )  : $query->the_post();
				 	$cs_thumbnail = cs_get_post_img_src( $post->ID, $width, $height );
				  	$post_xml = get_post_meta(get_the_id(), "post", true);
				  	if ( $post_xml <> "" ) {
						$cs_xmlObject = new SimpleXMLElement($post_xml);
					  	$post_thumb_view = $cs_xmlObject->post_thumb_view;
					  	$post_thumb_audio = $cs_xmlObject->post_thumb_audio;
				  	}else{
					
						$post_thumb_view = '';
						$post_thumb_audio = '';
					}
				  	?>
					<article class="col-md-12">
						<?php 
							if ( $post_thumb_view == 'Single Image' ){
							  	if ( isset( $cs_thumbnail ) && $cs_thumbnail !='' ) {
									cs_post_image($cs_thumbnail);
						  		}
							} else if ($post_thumb_view == 'Slider') {
								echo '<div class="cs-media"><figure>';
								  	cs_post_flex_slider($width,$height,get_the_id(),'post-list');
								echo '</figure></div>';
							}
							
					   ?>
					   <div class="cs-bloginfo-sec">
							<h2><a href="<?php esc_url(the_permalink());?>"><?php cs_get_title($cs_title_limit); ?></a></h2>
						  	<?php
							if(get_bloginfo('language') == 'es-MX'){
								if ($description == 'yes') {?><p> <?php echo cs_get_the_excerpt($excerpt,'true','Leer M&aacute;s...'); ?></p><?php }
							}else{
								if ($description == 'yes') {?><p> <?php echo cs_get_the_excerpt($excerpt,'true','Read more...'); ?></p><?php }
							}
							?>
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
                            <div class="cs-blog-text">
                                <ul class="cs-post-options">
                                	<?php cs_featured(); ?>
                                    <!--<li><i class="icon-user9"></i><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a></li>-->
                                    <li><i class=" icon-calendar11"></i><time datetime="<?php echo get_the_date('Y-m-d',get_the_id());?>">
                                        <?php echo get_the_date('F d ,Y',get_the_id());?></time>
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
     		}		
		}
		//======================================================================
		// Blog Large View
		//======================================================================
		public function cs_medium_view( $description,$excerpt,$cs_category,$query ) {
			global $post;
			$width = '382';
			$height = '286';
			$cs_title_limit = 200;
 			if ( $query->have_posts() ) {  
 				while ( $query->have_posts() )  : $query->the_post();
				 	$cs_thumbnail = cs_get_post_img_src( $post->ID, $width, $height );
				  	$post_xml = get_post_meta(get_the_id(), "post", true);
				  	if ( $post_xml <> "" ) {
						$cs_xmlObject = new SimpleXMLElement($post_xml);
					  	$post_thumb_view = $cs_xmlObject->post_thumb_view;
					  	$post_thumb_audio = $cs_xmlObject->post_thumb_audio;
				  	}else{
						$post_thumb_view = '';
					  	$post_thumb_audio = '';
						
					}
				  	?>
					<article class="col-md-12">
						<?php if ( $post_thumb_view == 'Single Image' ){
							  	if ( isset( $cs_thumbnail ) && $cs_thumbnail !='' ) {
									cs_post_image($cs_thumbnail);
						  		}
							} else if ($post_thumb_view == 'Slider') {
								  echo '<div class="cs-media"><figure>';
								  cs_post_flex_slider($width,$height,get_the_id(),'post-list');
								  echo '</figure></div>';
							}
							cs_featured();
					   ?>
					   <div class="cs-bloginfo-sec">
						  <h4><a href="<?php esc_url(the_permalink());?>"><?php cs_get_title($cs_title_limit); ?></a></h4>
						  	<?php
							if(get_bloginfo('language') == 'es-MX'){
								if ($description == 'yes') {?><p> <?php echo cs_get_the_excerpt($excerpt,'true','Leer M&aacute;s...'); ?></p><?php }
							}else{
								if ($description == 'yes') {?><p> <?php echo cs_get_the_excerpt($excerpt,'true','Read more...'); ?></p><?php }
							}
							?>
							<div class="cs-blog-text">
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
									<!--<li><i class="icon-user9"></i><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a></li>-->
                                    <li><i class=" icon-calendar11"></i><time datetime="<?php echo get_the_date('Y-m-d',get_the_id());?>">
										<?php echo get_the_date('F d ,Y',get_the_id());?></time>
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
     		}		
 		}
 		//======================================================================
		// Blog Grid View
		//======================================================================
		public function cs_grid_view( $description,$excerpt,$cs_category ,$cs_blog_grid_layout='col-md-4',$query, $blog_col_class = 'col-md-4') {
			global $post;
			$width = '358';
			$height = '202';
			$cs_title_limit = 200;
			 
			if ( $query->have_posts() ) {  
				$postCounter	= 0;
        		while ( $query->have_posts() )  : $query->the_post();
				  	$cs_thumbnail = cs_get_post_img_src( $post->ID, $width, $height );
				  	$post_xml = get_post_meta(get_the_id(), "post", true);
				  	if ( $post_xml <> "" ) {
						$cs_xmlObject = new SimpleXMLElement($post_xml);
					  	$post_thumb_view = $cs_xmlObject->post_thumb_view;
					  	$post_thumb_audio = $cs_xmlObject->post_thumb_audio;
				  	}else{
					  	$post_thumb_view = '';
					  	$post_thumb_audio = '';

					}
					?>
		  			<article class="col-md-6">
						<?php 
							if ( $post_thumb_view == 'Single Image' ){
								if ( isset( $cs_thumbnail ) && $cs_thumbnail !='' ) {
									cs_post_image($cs_thumbnail);
						  		}
							} else if ($post_thumb_view == 'Slider') {
								echo '<div class="cs-media"><figure>';
									cs_post_flex_slider($width,$height,get_the_id(),'post-list');
								echo '</figure></div>';
							}
							
					   ?>
					   	<div class="cs-bloginfo-sec">
					  		<h4><a href="<?php esc_url(the_permalink());?>"><?php cs_get_title($cs_title_limit); ?></a></h4>
						  	<?php
							if(get_bloginfo('language') == 'es-MX'){
								if ($description == 'yes') {?><p> <?php echo cs_get_the_excerpt($excerpt,'true','Leer M&aacute;s...'); ?></p><?php }
							}else{
								if ($description == 'yes') {?><p> <?php echo cs_get_the_excerpt($excerpt,'true','Read more...'); ?></p><?php }
							}
							?>
							<div class="cs-blog-text">
								<ul class="cs-post-options">
                                	<?php cs_featured();?>
                                    <!--
									<li>
                                    	<i class="icon-user9"></i>
                                    	<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
											<?php the_author(); ?>
                                    	</a>
                                    </li>                               
                                    -->
                                    <li><i class=" icon-calendar11"></i><time datetime="<?php echo get_the_date('Y-m-d',get_the_id());?>">
                                        <?php echo get_the_date('F d ,Y',get_the_id());?></time>
                                    </li>
								</ul>
 							</div>
						</div>
					</article>
        		<?php 
			endwhile;
			}
		}
 		//======================================================================
		// Blog Crousel View
		//======================================================================
		public function cs_crousel_view( $description,$excerpt,$cs_category ,$cs_blog_grid_layout='col-md-4',$query, $blog_col_class = 'col-md-4') {
			global $post;
			$width = '358';
			$height = '202';
			$cs_title_limit = 200;
			 
			if ( $query->have_posts() ) {
				cs_owl_carousel();
				echo '<div class="blog-carousel owl-carousel cs-prv-next">';  
				$postCounter	= 0;
        		while ( $query->have_posts() )  : $query->the_post();
				  	$cs_thumbnail = cs_get_post_img_src( $post->ID, $width, $height );
				  	$post_xml = get_post_meta(get_the_id(), "post", true);
				  	if ( $post_xml <> "" ) {
						$cs_xmlObject = new SimpleXMLElement($post_xml);
					  	$post_thumb_view = $cs_xmlObject->post_thumb_view;
					  	$post_thumb_audio = $cs_xmlObject->post_thumb_audio;
				  	}else{
					  	$post_thumb_view = '';
					  	$post_thumb_audio = '';
					
					}
					?>
		  			<article class="col-md-4 item">
						<?php 
							if ( $post_thumb_view == 'Single Image' ){
								if ( isset( $cs_thumbnail ) && $cs_thumbnail !='' ) {
									cs_post_image($cs_thumbnail);
						  		}
							} else if ($post_thumb_view == 'Slider') {
								echo '<div class="cs-media"><figure>';
									cs_post_flex_slider($width,$height,get_the_id(),'post-list');
								echo '</figure></div>';
							}
							
					   ?>
					   	<div class="cs-bloginfo-sec">
							<h4><a href="<?php esc_url(the_permalink());?>"><?php cs_get_title($cs_title_limit); ?></a></h4>
						  	<?php
							if(get_bloginfo('language') == 'es-MX'){
								if ($description == 'yes') {?><p> <?php echo cs_get_the_excerpt($excerpt,'true','Leer M&aacute;s...'); ?></p><?php }
							}else{
								if ($description == 'yes') {?><p> <?php echo cs_get_the_excerpt($excerpt,'true','Read more...'); ?></p><?php }
							}
							?>
							<div class="cs-blog-text">
								<ul class="cs-post-options">
                                	<?php cs_featured(); ?>
                                    <!--
									<li>
                                    	<i class="icon-user9"></i>
                                    	<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
											<?php the_author(); ?>
                                    	</a>
                                    </li>                               
                                    -->
                                    <li><i class=" icon-calendar11"></i><time datetime="<?php echo get_the_date('Y-m-d',get_the_id());?>">
                                        <?php echo get_the_date('F d ,Y',get_the_id());?></time>
                                    </li>
								</ul>
 							</div>
						</div>
					</article>
        		<?php 
			endwhile;
			echo '</div>';
			}
			?>
			<script>
				jQuery(document).ready(function(){
					cs_owncrowsel_callback('blog-carousel');
				});
    		</script>
			<?php
		}
	}
}