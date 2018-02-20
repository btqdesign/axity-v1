<?php 
/**
 * File Type: Directory Single Templates Class
 */
 
if ( !class_exists('cs_single_templates') ) {
	
	class cs_single_templates
	{
		
		function __construct()
		{
			// Constructor Code here..
		}

		//=====================================================================
		// project view 1
		//=====================================================================
		public function cs_project_view1( $cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view){
			global $post;
			$cs_width 		= 790;
			$cs_height 		= 464;
			$cs_image_url	= cs_get_post_img_src($post->ID, $cs_width, $cs_height);
			
			$cs_project = get_post_meta($post->ID, "csprojects", true);
			if ( $cs_project <> "" ) {
				$cs_xmlObject = new SimpleXMLElement($cs_project);
			}
			if(isset($cs_xmlObject->dynamic_post_location_address)){ $dynamic_post_location_address = $cs_xmlObject->dynamic_post_location_address;} else {$dynamic_post_location_address = '';}
			?>
				<div class="cs-portfolio cs-portfolio-detail">
                	<div class="cs-short-info col-md-3">
                    	<h2><?php the_title(); ?></h2>
                      	<ul>
                        	<?php if( isset( $dynamic_post_location_address ) && $dynamic_post_location_address !='' ){?>
                            <li>
                              <span><?php echo esc_attr('Location','lassic')?></span>
                              <a><?php echo esc_attr($dynamic_post_location_address); ?></a>
                            </li>
                            <?php }?>
                           
						    <?php $cs_term_list	= get_the_term_list ( $post->ID, 'project-category', '', ', ', '' ); ?>
                            <?php if ( isset( $cs_term_list ) && $cs_term_list!='' ) {?>
                            <li>
                               <span><?php echo esc_attr('Category:','lassic')?></span>
                               <?php echo cs_allow_special_char($cs_term_list); ?>
                            </li>
                            <?php }?>
                            
                      	</ul>
                    </div>
                    <div class="cs-editor-text col-md-6">
                        <?php the_content(); ?>
                        <?php $this->cs_project_link_btn(); ?>
                    </div>
                    <div class="cs-project-status col-md-3">
                        <span class="cs-status"><?php echo esc_attr('Project Stats','lassic')?></span>
                        <ul>
                         <?php
                             $cs_area	= get_post_meta($post->ID, "cs_area", true );
                            $cs_investor	= get_post_meta($post->ID, "cs_investor", true );
                            $cs_value	= get_post_meta($post->ID, "cs_value", true );
                            $cs_construction_date	= get_post_meta($post->ID, "cs_construction_date", true );
                          	if(isset( $cs_construction_date ) && $cs_construction_date != '' ){?>
                                <li>
                                  <span><?php echo esc_attr('Construction Date','lassic')?></span>
                                  <?php echo date_i18n(get_option('date_format'), strtotime($cs_construction_date)); ?>
                                </li>
                                <?php }?>
                                
                                <?php if(isset( $cs_area ) && $cs_area !='' ){?>
                                <li>
                                  <span><?php echo esc_attr('Surface Area','lassic')?></span>
                                  <?php echo cs_allow_special_char($cs_area);?>
                                </li>
                                <?php }?>
                                <?php if(isset( $cs_investor ) && $cs_investor !='' ){?>
                                <li>
                                  <span><?php echo esc_attr('Contracting Investor','lassic')?></span>
                                  <?php echo cs_allow_special_char($cs_investor);?>
                                </li>
                                <?php }?>
                                <?php if(isset( $cs_value ) && $cs_value !='' ){?>
                                <li>
                                  <span><?php echo esc_attr('Value','lassic')?></span>
                                  <?php echo cs_allow_special_char($cs_value);?>
                                </li>
                          <?php }?>
                        </ul>
                      </div>
                </div>
                <div class="cs-box-seprator col-md-12">
                    <div class="cs-seprator">
                      <div class="cs-seprator-holder">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                      </div>
                    </div>
                </div>
                <div class="col-md-12">
					<?php 
						if($cs_thumb_view == "gallery"){
							cs_post_gallery($cs_width,$cs_height,get_the_id());
						}elseif($cs_thumb_view == "slider"){
							cs_post_flex_slider($cs_width,$cs_height,get_the_id(),'csprojects');
						}else{
						if($cs_image_url <> ''){?>
                			<figure class="cs-postthumb"><img alt="<?php the_title(); ?>" src="<?php echo esc_url($cs_image_url); ?>"></figure>
                		<?php } 
						}?>
                </div>
                <?php 
					if((isset($cs_post_tags_show) &&  $cs_post_tags_show == 'on') || (isset($cs_share_post) &&  $cs_share_post == 'on')){
                    	if($cs_post_tags_show == 'on'){
                        ?>
                        	<!-- cs Tages Start -->
                            <div class="cs-tags col-md-6">
                             <i class="icon-tags2"></i>
                              <ul>
                                <?php  
                                    $categories_list = get_the_term_list ( get_the_id(), 'project-tag', '<li>', '</li><li>', '</li>' );
                                    if ( $categories_list ){
                                    	printf( __( '%1$s', 'lassic'),$categories_list );
                                    }
                                ?>
                              </ul>
                            </div>
                            <!-- cs Tages End -->
                        <?php 
                        }
                        if($cs_share_post == 'on'){?>
                        	<div class="cs-social-share col-md-6">
              						<?php cs_social_share(false,true,'yes'); ?>
              					</div>
                            <?php 
                        }
					}
					
					echo '<div class="cs-box-seprator col-md-12">
                    <div class="cs-seprator">
                      <div class="cs-seprator-holder">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                      </div>
                    </div>
                  </div>';
					
                    if(isset($post_pagination_show) &&  $post_pagination_show == 'on'){
                    	cs_next_prev_custom_links('project');
                    }
		}
		//=====================================================================
		// project view 2
		//=====================================================================
		public function cs_project_view2( $cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view){
			global $post,$cs_xmlObject;
			$cs_width 		= 790;
			$cs_height 		= 464;
			$cs_image_url 		= cs_get_post_img_src($post->ID, $cs_width, $cs_height);
			
			$event_map_switch  = $cs_xmlObject->event_map_switch;
			$event_map_heading = $cs_xmlObject->event_map_heading;
			
			echo '<div class="cs-portfolio cs-view-2">';
				echo '<div class="col-md-12">';
					if($cs_thumb_view == "gallery"){
						cs_post_gallery($cs_width,$cs_height,get_the_id());
					}elseif($cs_thumb_view == "slider"){
						cs_post_flex_slider($cs_width,$cs_height,get_the_id(),'csprojects');
					}else{
					if($cs_image_url <> ''){?>
						<figure class="cs-postthumb"><img alt="<?php the_title(); ?>" src="<?php echo esc_url($cs_image_url); ?>"></figure>
					<?php } 
					}
				echo '</div>';
				?>
				<div class="cs-portfolio-detail">
					<div class="cs-editor-text col-md-9">
						<?php the_content(); ?>
					</div>
					<div class="cs-project-status cs-project-style col-md-3">
						<span class="cs-status"><?php echo esc_attr('Project Stats','lassic')?></span>
                        <ul>
                            <?php
                                $cs_area	= get_post_meta($post->ID, "cs_area", true );
                                $cs_investor	= get_post_meta($post->ID, "cs_investor", true );
                                $cs_value	= get_post_meta($post->ID, "cs_value", true );
                                $cs_construction_date	= get_post_meta($post->ID, "cs_construction_date", true );
                                if(isset( $cs_construction_date ) && $cs_construction_date !='' ){?>
                                    <li>
                                        <span><?php echo esc_attr('Construction Date','lassic')?></span>
                                        <?php echo date_i18n(get_option('date_format'), strtotime($cs_construction_date)); ?>
                                    </li>
                                    <?php }?>
                                    <?php if(isset( $cs_area ) && $cs_area !='' ){?>
                                    <li>
                                        <span><?php echo esc_attr('Surface Area','lassic')?></span>
                                        <?php echo cs_allow_special_char($cs_area);?>
                                    </li>
                                    <?php }?>
                                    <?php if(isset( $cs_investor ) && $cs_investor !='' ){?>
                                    <li>
                                      <span><?php echo esc_attr('Contracting Investor','lassic')?></span>
                                      <?php echo cs_allow_special_char($cs_investor);?>
                                    </li>
                                    <?php }?>
                                    <?php if(isset( $cs_value ) && $cs_value !='' ){?>
                                    <li>
                                      <span><?php echo esc_attr('Value','lassic')?></span>
                                      <?php echo cs_allow_special_char($cs_value);?>
                                    </li>
                             	    <?php }?>
                                    
									<?php $cs_term_list	= get_the_term_list ( $post->ID, 'project-category', '', ', ', '</span>' ); ?>
								    <?php if ( isset( $cs_term_list ) && $cs_term_list!='' ) {?>
                                    	<li>
                                        <span><?php echo esc_attr('Category:','lassic')?></span>
                                        <?php echo cs_allow_special_char($cs_term_list);?>
                                        </li>
                                    <?php }?>
                              
                        	</ul>
						<?php $this->cs_project_link_btn(); ?>
 					</div>
				</div>
                <?php if ( isset( $event_map_switch ) && $event_map_switch =='on' ) {?>
                <div class="col-md-12">
                	<div class="cs-section-title"><h2><?php echo isset( $event_map_heading ) && trim( $event_map_heading)  !='' ? $event_map_heading : 'Project Location';?></h2></div>
                	<?php $this->cs_direcotry_map_location_display();?>
                </div>
                <?php }?>
                
                <?php 
					if((isset($cs_post_tags_show) &&  $cs_post_tags_show == 'on') || (isset($cs_share_post) &&  $cs_share_post == 'on')){
                    	if($cs_post_tags_show == 'on'){
                        ?>
                        	<!-- cs Tages Start -->
                            <div class="cs-tags col-md-6">
                             <i class="icon-tags2"></i>
                              <ul>
                                <?php  
                                    $categories_list = get_the_term_list ( get_the_id(), 'project-tag', '<li>', '</li><li>', '</li>' );
                                    if ( $categories_list ){
                                        printf( __( '%1$s', 'lassic'),$categories_list );
                                    }
                                ?>
                              </ul>
                            </div>
                            <!-- cs Tages End -->
                        <?php 
                        }
                        if($cs_share_post == 'on'){?>
                        	<div class="cs-social-share col-md-6">
              						<?php cs_social_share(false,true,'yes'); ?>
              					</div>
                            <?php 
                        }
					}
					echo '<div class="cs-box-seprator col-md-12">
                    <div class="cs-seprator">
                      <div class="cs-seprator-holder">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                      </div>
                    </div>
                  </div>';
					
                    if(isset($post_pagination_show) &&  $post_pagination_show == 'on'){
                    	cs_next_prev_custom_links('project');
                    }
 			echo '</div>';
		
		}
		//=====================================================================
		// project view 3
		//=====================================================================
		public function cs_project_view3( $cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view){
			global $post;
			$cs_width 		= 790;
			$cs_height 		= 464;
			$cs_image_url 		= cs_get_post_img_src($post->ID, $cs_width, $cs_height);
			echo '<div class="cs-portfolio cs-view-3">';
				echo '<div class="col-md-9"><div class="cs-inner-portfolio">'; ?>
	                <h2><?php the_title(); ?></h2>
					<div class="cs-editor-text">
						<?php the_content(); ?>
					</div>
	                <?php
					if($cs_thumb_view == "gallery"){
						cs_post_gallery($cs_width,$cs_height,get_the_id());
					}elseif($cs_thumb_view == "slider"){
						cs_post_flex_slider($cs_width,$cs_height,get_the_id(),'csprojects');
					}else{
					if($cs_image_url <> ''){?>
						<figure class="cs-postthumb"><img alt="<?php the_title(); ?>" src="<?php echo esc_url($cs_image_url); ?>"></figure>
					<?php } 
					}
					if((isset($cs_post_tags_show) &&  $cs_post_tags_show == 'on') || (isset($cs_share_post) &&  $cs_share_post == 'on')){
                    	echo '<div class="row">';
						if($cs_post_tags_show == 'on'){
                        ?>
                        	<!-- cs Tages Start -->
                            <div class="cs-tags col-md-6">
                             <i class="icon-tags2"></i>
                              <ul>
                                <?php  
                                    $categories_list = get_the_term_list ( get_the_id(), 'project-tag', '<li>', '</li><li>', '</li>' );
                                    if ( $categories_list ){
                                        printf( __( '%1$s', 'lassic'),$categories_list );
                                    }
                                ?>
                              </ul>
                            </div>
                            <!-- cs Tages End -->
                        <?php 
                        }
                        if($cs_share_post == 'on'){?>
                        	<div class="cs-social-share col-md-6">
              						<?php cs_social_share(false,true,'yes'); ?>
              					</div>
                            <?php 
                        }
						echo '</div>';
					}
					echo '<div class="cs-box-seprator col-md-12">
                    <div class="cs-seprator">
                      <div class="cs-seprator-holder">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                      </div>
                    </div>
                  </div>';
                    if(isset($post_pagination_show) &&  $post_pagination_show == 'on'){
                    	cs_next_prev_custom_links('project');
                    }
					echo '</div></div>';
				?>
				<div class="cs-project-status cs-portfolio-detail cs-project-style col-md-3">
					<h2><?php echo esc_attr('Project Stats','lassic')?></h2>
                    <ul>
                        <?php
                            $cs_area	= get_post_meta($post->ID, "cs_area", true );
                            $cs_investor	= get_post_meta($post->ID, "cs_investor", true );
                            $cs_value	= get_post_meta($post->ID, "cs_value", true );
                            $cs_construction_date	= get_post_meta($post->ID, "cs_construction_date", true );
                            if(isset( $cs_construction_date ) && $cs_construction_date !='' ){?>
                                <li>
                                    <span><?php echo esc_attr('Construction Date','lassic')?></span>
                                    <?php echo date_i18n(get_option('date_format'), strtotime($cs_construction_date)); ?>
                                </li>
                                <?php }?>
                                <?php if(isset( $cs_area ) && $cs_area !='' ){?>
                                <li>
                                    <span><?php echo esc_attr('Surface Area','lassic')?></span>
                                    <?php echo cs_allow_special_char($cs_area);?>
                                </li>
                                <?php }?>
                                <?php if(isset( $cs_investor ) && $cs_investor !='' ){?>
                                <li>
                                  <span><?php echo esc_attr('Contracting Investor','lassic')?></span>
                                  <?php echo cs_allow_special_char($cs_investor);?>
                                </li>
                                <?php }?>
                                <?php if(isset( $cs_value ) && $cs_value !='' ){?>
                                <li>
                                  <span><?php echo esc_attr('Value','lassic')?></span>
                                  <?php echo cs_allow_special_char($cs_value);?>
                                </li>
                         		<?php }?>
                                
                                <?php $cs_term_list	= get_the_term_list ( $post->ID, 'project-category', '', ', ', '</span>' ); ?>
								<?php if ( isset( $cs_term_list ) && $cs_term_list!='' ) {?>
                                   <li>
                                    <span><?php echo esc_attr('Category:','lassic')?></span>
                                    <?php echo cs_allow_special_char($cs_term_list);?>
                                  </li>
                                <?php }?>
                        </ul>
					<?php $this->cs_project_link_btn(); ?>
				</div>
               <?php 
                
 			echo '</div>';
		
		}
		//=====================================================================
		// project view 4
		//=====================================================================
		public function cs_project_view4( $cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view){
			global $post,$cs_xmlObject;
			$cs_width 		= '';
			$cs_height 		= '';
			$cs_image_url 		= cs_get_post_img_src($post->ID, $cs_width, $cs_height);
			$cs_project_members		= $cs_xmlObject->cs_project_members;
			$cs_project_shortcode	= $cs_xmlObject->cs_project_shortcode;
			
			$cs_project_members	=  explode(',',$cs_project_members);
			echo '<div class="col-md-12 cs-portfolio">';
			if($cs_thumb_view == "gallery"){
				cs_post_gallery($cs_width,$cs_height,get_the_id());
			}elseif($cs_thumb_view == "slider"){
				cs_post_flex_slider($cs_width,$cs_height,get_the_id(),'csprojects');
			}else{
			if($cs_image_url <> ''){?>
				<figure class="cs-postthumb"><img alt="<?php the_title(); ?>" src="<?php echo esc_url($cs_image_url); ?>"></figure>
			<?php } 
			}
			
			echo '</div>';
			echo '<div class="cs-portfolio cs-view-4">';
			?>
				<div class="cs-project-status  cs-portfolio-detail cs-project-style col-md-3">
	                <h2><?php the_title(); ?></h2>
                    <ul>
                        <?php
                            $cs_area	= get_post_meta($post->ID, "cs_area", true );
                            $cs_investor	= get_post_meta($post->ID, "cs_investor", true );
                            $cs_value	= get_post_meta($post->ID, "cs_value", true );
                            $cs_construction_date	= get_post_meta($post->ID, "cs_construction_date", true );
                            if(isset( $cs_construction_date ) && $cs_construction_date !='' ){?>
                                <li>
                                    <span><?php echo esc_attr('Construction Date','lassic')?></span>
                                    <?php echo date_i18n(get_option('date_format'), strtotime($cs_construction_date)); ?>
                                </li>
                                <?php }?>
                                <?php if(isset( $cs_area ) && $cs_area !='' ){?>
                                <li>
                                    <span><?php echo esc_attr('Surface Area','lassic')?></span>
                                    <?php echo cs_allow_special_char($cs_area);?>
                                </li>
                                <?php }?>
                                <?php if(isset( $cs_investor ) && $cs_investor !='' ){?>
                                <li>
                                  <span><?php echo esc_attr('Contracting Investor','lassic')?></span>
                                  <?php echo cs_allow_special_char($cs_investor);?>
                                </li>
                                <?php }?>
                                <?php if(isset( $cs_value ) && $cs_value !='' ){?>
                                <li>
                                  <span><?php echo esc_attr('Value','lassic')?></span>
                                  <?php echo cs_allow_special_char($cs_value);?>
                                </li>
                          		<?php }?>
                          		<?php $cs_term_list	= get_the_term_list ( $post->ID, 'project-category', '', ', ', '</span>' ); ?>
								<?php if ( isset( $cs_term_list ) && $cs_term_list!='' ) {?>
                                    <li>
                                        <span><?php echo esc_attr('Category:','lassic')?></span>
                                        <?php echo cs_allow_special_char($cs_term_list);?>
                                    </li>
                                <?php }?>
                        </ul>
					<?php $this->cs_project_link_btn(); ?>
                    <?php
					if(isset($cs_share_post) &&  $cs_share_post == 'on'){
						?>
                        	<div class="cs-social-share">
              						<?php cs_social_share(false,true,'yes'); ?>
              					</div>
                            <?php 
					}?>                    
				</div>
                <?php
				echo '<div class="col-md-9">'; ?>
					<div class="cs-editor-text">
						<?php the_content(); ?>
					</div>
                    <?php
					if(isset($cs_post_tags_show) &&  $cs_post_tags_show == 'on'){
                         ?>
                        	<!-- cs Tages Start -->
                            <div class="cs-tags">
                             <i class="icon-tags2"></i>
                              <ul>
                                <?php  
                                    $categories_list = get_the_term_list ( get_the_id(), 'project-tag', '<li>', '</li><li>', '</li>' );
                                    if ( $categories_list ){
                                        printf( __( '%1$s', 'lassic'),$categories_list );
                                    }
                                ?>
                              </ul>
                            </div>
                            <!-- cs Tages End -->
               <?php }?>
					<div class="cs-box-seprator col-md-12">
                    <div class="cs-seprator">
                      <div class="cs-seprator-holder">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                      </div>
                    </div>
                  </div>
                   <?php 
                    if(isset($post_pagination_show) &&  $post_pagination_show == 'on'){
                    	cs_next_prev_custom_links('project');
                    }
				 
					 if(isset($cs_project_shortcode) &&  $cs_project_shortcode != ''){
						 echo do_shortcode( $cs_project_shortcode );
					 }
					 
					 if(isset($cs_project_members) &&  $cs_project_members != ''){
						
						echo '<div class="col-md-12"><div class="cs-section-title"><h2>Project Team</h2></div>';
						$args = array('posts_per_page' => "-1", 'post_type' => 'member', 'order' => 'ID', 'orderby' => 'DESC', 'post_status' => 'publish', 'post__in' => $cs_project_members);
						$args = new WP_Query( $args );
						$this->cs_member_view('grid-4-column',255,$args);
						echo '</div>';
				 }?>
                </div>
 			</div>
		
		<?php }
		//=====================================================================
		// project view 5
		//=====================================================================
		public function cs_project_view5( $cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view){
			global $post;
			$cs_width 		= '';
			$cs_height 		= '';
			$cs_image_url 	= cs_get_post_img_src($post->ID, $cs_width, $cs_height);
			
			$cs_project = get_post_meta($post->ID, "csprojects", true);
			if ( $cs_project <> "" ) {
				$cs_xmlObject = new SimpleXMLElement($cs_project);
			}
			if( isset($cs_xmlObject) && is_object($cs_xmlObject) ){
				$cs_location_address = (string)$cs_xmlObject->dynamic_post_location_address;
			}
			?>
				<div class="cs-portfolio cs-portfolio-detail cs-view-5">
                	<div class="cs-short-info col-md-2">
                    	<h2><?php the_title(); ?></h2>
                      	<ul>
                        	<?php if( isset($cs_location_address) && $cs_location_address <> '' ) { ?>
                            <li>
                              <span><?php echo esc_attr('Location','lassic')?></span>
                              <a><?php echo cs_allow_special_char($cs_location_address); ?></a>
                            </li>
                            <?php } ?>
                            <li>
                              <span><?php echo esc_attr('Category:','lassic')?></span>
                               <?php $cs_term_list	= get_the_term_list ( $post->ID, 'project-category', '', ', ', '</span>' ); ?>
                               <?php if ( isset( $cs_term_list ) && $cs_term_list!='' ) {?>
                                    <?php echo cs_allow_special_char($cs_term_list);?>
                                <?php }?>
                            </li>
                      	</ul>
                        <ul>
						   <?php
                                $cs_area	= get_post_meta($post->ID, "cs_area", true );
                                $cs_investor	= get_post_meta($post->ID, "cs_investor", true );
                                $cs_value	= get_post_meta($post->ID, "cs_value", true );
                                $cs_construction_date	= get_post_meta($post->ID, "cs_construction_date", true );
                           		if(isset( $cs_construction_date ) && $cs_construction_date !='' ){?>
                                    <li>
                                      <span><?php echo esc_attr('Construction Date','lassic')?></span>
                                      <?php echo date_i18n(get_option('date_format'), strtotime($cs_construction_date)); ?>
                                    </li>
                                    <?php }?>
                                    
                                    <?php $cs_term_list	= get_the_term_list ( $post->ID, 'project-category', '', ', ', '</span>' ); ?>
									<?php if ( isset( $cs_term_list ) && $cs_term_list!='' ) {?>
                                        <li>
                                        <span><?php echo esc_attr('Category:','lassic')?></span>
                                        <?php echo cs_allow_special_char($cs_term_list);?>
                                    	</li>
									<?php }?>
                                
                                    <?php if(isset( $cs_area ) && $cs_area !='' ){?>
                                    <li>
                                      <span><?php echo esc_attr('Surface Area','lassic')?></span>
                                      <?php echo get_post_meta($post->ID, "cs_area", true );?>
                                    </li>
                                    <?php }?>
                                    <?php if(isset( $cs_investor ) && $cs_investor !='' ){?>
                                    <li>
                                      <span><?php echo esc_attr('Contracting Investor','lassic')?></span>
                                      <?php echo get_post_meta($post->ID, "cs_investor", true );?>
                                    </li>
                                    <?php }?>
                                    <?php if(isset( $cs_value ) && $cs_value !='' ){?>
                                    <li>
                                      <span><?php echo esc_attr('Value','lassic')?></span>
                                      <?php echo get_post_meta($post->ID, "cs_value", true );?>
                                    </li>
                            <?php }?>
                      </ul>
                      <?php
                      	if((isset($cs_post_tags_show) &&  $cs_post_tags_show == 'on') || (isset($cs_share_post) &&  $cs_share_post == 'on')){
                    		if($cs_post_tags_show == 'on'){
                        	?>
                        	<!-- cs Tages Start -->
                            <div class="cs-tags">
                             <i class="icon-tags2"></i>
                              <ul>
                                <?php  
                                    $categories_list = get_the_term_list ( get_the_id(), 'project-tag', '<li>', '</li><li>', '</li>' );
                                    if ( $categories_list ){
                                        printf( __( '%1$s', 'lassic'),$categories_list );
                                    }
                                ?>
                              </ul>
                            </div>
                            <!-- cs Tages End -->
                        <?php 
                        }
                        if($cs_share_post == 'on'){?>
                        	<div class="cs-social-share">
              						<?php cs_social_share(false,true,'yes'); ?>
              					</div>
                            <?php 
                        }
					}
		            if(isset($post_pagination_show) &&  $post_pagination_show == 'on'){
                    	cs_next_prev_custom_links('project',false);
                    }
?>
                    </div>
                    <div class="cs-editor-text col-md-10">
					<?php
                        if($cs_thumb_view == "gallery"){
                            cs_post_gallery($cs_width,$cs_height,get_the_id());
                        }elseif($cs_thumb_view == "slider"){
                            cs_post_flex_slider($cs_width,$cs_height,get_the_id(),'csprojects');
                        }else{
                        if($cs_image_url <> ''){?>
                            <figure class="cs-postthumb"><img alt="<?php the_title(); ?>" src="<?php echo esc_url($cs_image_url); ?>"></figure>
                        <?php } 
                        }
					?>	
                     
                    </div>
                </div>
				                
			<?php
		
		}
		
		//=====================================================================
		// Adding Posts flexslider 
		//=====================================================================
		
		public function cs_directory_flex_slider( $sliderData, $thumbArray , $is_thumb ){
				global $cs_node,$cs_theme_options;
				$cs_post_counter = rand(40, 9999999);
 				?>
				<!-- Flex Slider -->
				<div id="slider-<?php echo esc_attr( $cs_post_counter );?>" class="flexslider cs-loading">
                  <ul class="slides">
                   <?php 
						$cs_counter = 1;
						foreach ( $sliderData as $as_node ){
							echo '<li>
									<figure>
										<a href="'.esc_url( $as_node ).'"  data-rel="prettyPhoto[gallery]"><img src="'.esc_url( $as_node ).'" alt=""></a>
									</figure>
							</li>';
 							$cs_counter++;
						}
					?>
                    <!-- items mirrored twice, total of 12 -->
                  </ul>
                </div>
                <?php if ( isset( $is_thumb ) && $is_thumb == 'true' ){?>
                <div id="carousel-<?php echo esc_attr( $cs_post_counter );?>" class="flexslider">
                  <ul class="slides">
                   <?php 
						$cs_counter = 1;
						foreach ( $thumbArray as $as_node ){
							echo '<li>
									<figure>
										<img src="'.esc_url( $as_node ).'" alt="">';
									?>
									</figure>
								  </li>
						<?php 
						$cs_counter++;
						}
					?>
                    <!-- items mirrored twice, total of 12 -->
                  </ul>
                </div>
				<?php } ?>
				<?php cs_enqueue_flexslider_script(); ?>
				<!-- Flex Slider Javascript Files -->
				<script type="text/javascript">
					jQuery(window).load(function() {
					  // The slider being synced must be initialized first
					  var target_flexslider = jQuery('#slider-<?php echo esc_attr( $cs_post_counter );?>');
					  <?php if (isset( $is_thumb ) && $is_thumb == 'true' ){?>
					  jQuery('#carousel-<?php echo esc_attr( $cs_post_counter );?>').flexslider({
						animation: "slide",
						controlNav: false,
						smoothHeight : true,
						animationLoop: false,
						slideshow: false,
						itemWidth: 65,
						itemMargin: 5,
						asNavFor: '#slider-<?php echo esc_attr( $cs_post_counter );?>'

					  });
					  <?php } ?>
					   
					  jQuery('#slider-<?php echo esc_attr( $cs_post_counter );?>').flexslider({
						animation: "slide",
						controlNav: false,
						smoothHeight : true,
						animationLoop: false,
						slideshow: false,
						sync: "#carousel-<?php echo esc_attr( $cs_post_counter );?>",
						start: function(slider) {
						   target_flexslider.removeClass('cs-loading');
					   }						
					  });
					});
				</script>
			<?php
			}
		
		//======================================================================
		// Team Grid View
		//======================================================================
		public function cs_member_view($member_view='',$cs_member_excerpt_length = '',$cs_query ='') {
			global $post;
			$width  = '264';
			$height = '368';
			$cs_title_limit = 1000;
			$layout_view = 'col-md-3 member-grid-4';
			$cs_classes	 = 'cs-team team-grid';
				
			if ( $cs_query->have_posts() ) {
				 while ( $cs_query->have_posts() )  : $cs_query->the_post();
				$cs_member = get_post_meta(get_the_ID(), "member", true);
				if ( $cs_member <> "" ) {
					$cs_xmlObject = new SimpleXMLElement($cs_member);
					$cs_team_designation = $cs_xmlObject->cs_team_designation;
					
					
				} else {
					$cs_team_designation = '';
					if(!isset($cs_xmlObject))
						$cs_xmlObject = new stdClass();
				}

				$cs_thumbnail = cs_get_post_img_src( $post->ID, $width, $height );
				
			?>
			<div class="<?php echo cs_allow_special_char( $layout_view ); ?>">
            	<article class="<?php echo esc_attr( $cs_classes );?>">
                   	<?php if($cs_thumbnail <> ''){ ?> 
                  		<figure>
                      		<a href="<?php esc_url(the_permalink());?>"><img src="<?php echo esc_url($cs_thumbnail); ?>" alt="<?php the_title();?>"></a>
                  		</figure>
				  	<?php } ?>
                   	<div class="text">
                    	<header>
                          <h2 class="cs-post-title"><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
                      	</header>
                      	<?php 
					  		if ( isset( $cs_team_designation ) && $cs_team_designation !='' ) {
						  		echo '<span>'.$cs_team_designation.'</span>';
						 	}
					  	?>
                  </div>
              </article>
      		</div>
			<?php 
				endwhile;
			}
		}
		
		//======================================================================
		// Single Map
		//======================================================================
		public function cs_direcotry_map_location_display(){
			global $post, $cs_xmlObject,$cs_theme_options;
			$map_height	= '300';
			$event_map_heading = '';
			$map_attribute = array('cs_column_size'=>'','cs_map_section_title'=> $event_map_heading,'cs_map_title'=>'','cs_map_height'=> $map_height,'cs_map_type'=>'ROADMAP','cs_map_info'=>'','cs_map_info_width'=>'200','cs_map_info_height'=>'70','cs_map_marker_icon'=>'','cs_map_show_marker'=>'true','cs_map_controls'=>'false','cs_map_draggable' => 'true','cs_map_scrollwheel' => 'true','cs_map_conactus_content' => '','cs_map_border' => '','cs_map_border_color' => '','cs_map_class' => '','cs_map_animation' => '','cs_custom_animation_duration'=>'1');
						
			$cs_project = get_post_meta($post->ID, "csprojects", true);
			if ( $cs_project <> "" ) {
				$cs_xmlObject = new SimpleXMLElement($cs_project);
			}
			
			$cs_zoom = 17;
			
			$address_map   		= (string)$cs_xmlObject->dynamic_post_location_address;
			$cs_latitude   		= (string)$cs_xmlObject->dynamic_post_location_latitude;
			$cs_longitude   	= (string)$cs_xmlObject->dynamic_post_location_longitude;
			$cs_zoom   			= (string)$cs_xmlObject->dynamic_post_location_zoom;
			
			
			
			if(isset($cs_latitude) && $cs_latitude <> ''){
				$map_attribute['cs_map_lat'] = (string)$cs_latitude;
			}
			if(isset($cs_longitude) && $cs_longitude <> ''){
				$map_attribute['cs_map_lon'] = (string)$cs_longitude;
			}
 
			$map_marker_icon = get_template_directory_uri().'/assets/images/map-marker.png';
			$map_attribute['cs_map_marker_icon'] = $map_marker_icon;
			
			if(isset($address_map) && $address_map <> ''){
				$map_attribute['cs_map_info'] = $address_map;
			}
			
			if(isset($cs_zoom) && $cs_zoom <> ''){
				$map_attribute['cs_map_zoom'] = (int)$cs_zoom;
			} else {
				$map_attribute['cs_map_zoom'] = 14;
			}

			echo cs_map_shortcode($map_attribute);
		}
		
		
		public function cs_project_link_btn(){
			global $post;
			
			$cs_project = get_post_meta($post->ID, "csprojects", true);
			if ( $cs_project <> "" ) {
				$cs_xmlObject = new SimpleXMLElement($cs_project);
				$cs_project_btn_title  = $cs_xmlObject->cs_project_btn_title;
				$cs_project_btn_url  = $cs_xmlObject->cs_project_btn_url;
				$cs_project_btn_color  = $cs_xmlObject->cs_project_btn_color;
				
			} else {
				$cs_project_btn_title  = '';
				$cs_project_btn_url  = '';
				$cs_project_btn_color  = '';
				
				if(!isset($cs_xmlObject))
					$cs_xmlObject = new stdClass();
			}
			
			$cs_btn_color = '';
			if( $cs_project_btn_color <> '' ) $cs_btn_color = ' style="background-color:'.$cs_project_btn_color.';"';
			if( $cs_project_btn_title == '' ) $cs_project_btn_title = __('Project Plan', 'lassic');
			if( $cs_project_btn_url <> '' ) echo '<a'.$cs_btn_color.' href="'.$cs_project_btn_url.'" class="cs-planbtn">'.$cs_project_btn_title.'</a>';
		}
	}
}