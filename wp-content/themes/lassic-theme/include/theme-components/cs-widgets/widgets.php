<?php 
/**
 * Widgets Classes & Functions
 */
 

/**
 * @Facebook widget Class
 *
 *
 */

if ( ! class_exists( 'facebook_module' ) ) { 
	class facebook_module extends WP_Widget {
	  
		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
 		/**
		 * @Facebook Module
		 *
		 *
		 */
		 function facebook_module(){
				$widget_ops = array('classname' => 'facebok_widget', 'description' => 'Facebook widget like box total customized with theme.' );
				$this->WP_Widget('facebook_module', 'CS : Facebook', $widget_ops);
		  }
	  	  
		/**
		 * @Facebook html Form
		 *
		 *
		 */
		 function form($instance) {
				$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
				$title = $instance['title'];
				$pageurl = isset( $instance['pageurl'] ) ? esc_attr( $instance['pageurl'] ) : '';
				$showfaces = isset( $instance['showfaces'] ) ? esc_attr( $instance['showfaces'] ) : '';
				$showstream = isset( $instance['showstream'] ) ? esc_attr( $instance['showstream'] ) : '';
				$showheader = isset( $instance['showheader'] ) ? esc_attr( $instance['showheader'] ) : '';
				$fb_bg_color = isset( $instance['fb_bg_color'] ) ? esc_attr( $instance['fb_bg_color'] ) : '';
				$likebox_height = isset( $instance['likebox_height'] ) ? esc_attr( $instance['likebox_height'] ) : '';						
			?>
            <p>
              <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"> Title:
                <input class="upcoming" id="<?php echo esc_attr($this->get_field_id('title')); ?>" size='40' name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
              </label>
            </p>
            <p>
              <label for="<?php echo esc_attr($this->get_field_id('pageurl')); ?>"> Page URL:
                <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('pageurl')); ?>" size='40' name="<?php echo esc_attr($this->get_field_name('pageurl'));?>" type="text" value="<?php echo esc_attr($pageurl); ?>" />
                <br />
                <small>Please enter your page or User profile url example: http://www.facebook.com/profilename OR <br />
                https://www.facebook.com/pages/wxyz/123456789101112 </small><br />
              </label>
            </p>
            <p>
              <label for="<?php echo esc_attr($this->get_field_id('showfaces')); ?>"> Show Faces:
                <input class="upcoming" id="<?php echo esc_attr($this->get_field_id('showfaces')); ?>" name="<?php echo esc_attr($this->get_field_name('showfaces')); ?>" type="checkbox" <?php if(esc_attr($showfaces) != '' ){echo 'checked';}?> />
              </label>
            </p>
            <p>
              <label for="<?php echo esc_attr($this->get_field_id('showstream')); ?>"> Show Stream:
                <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('showstream')); ?>" name="<?php echo cs_allow_special_char($this->get_field_name('showstream')); ?>" type="checkbox" <?php if(esc_attr($showstream) != '' ){echo 'checked';}?> />
              </label>
            </p>
            <p>
              <label for="<?php echo cs_allow_special_char($this->get_field_id('likebox_height')); ?>"> Like Box Height:
                <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('likebox_height')); ?>" size='2' name="<?php echo cs_allow_special_char($this->get_field_name('likebox_height')); ?>" type="text" value="<?php echo esc_attr($likebox_height); ?>" />
              </label>
            </p>
            <p>
              <label for="<?php echo cs_allow_special_char($this->get_field_id('fb_bg_color')); ?>"> Background Color:
                <input type="text" name="<?php echo cs_allow_special_char($this->get_field_name('fb_bg_color')); ?>" size='4' id="<?php echo cs_allow_special_char($this->get_field_id('fb_bg_color')); ?>"  value="<?php if(!empty($fb_bg_color)){ echo cs_allow_special_char($fb_bg_color);}else{ echo "#fff";}; ?>" class="fb_bg_color upcoming"  />
              </label>
            </p>
            
            <?php
		
	    }
		
		/**
		 * @Facebook Update Form Data
		 *
		 *
		 */
		 function update($new_instance, $old_instance) {
	
			$instance = $old_instance;
			$instance['title'] = $new_instance['title'];
			$instance['pageurl'] = $new_instance['pageurl'];
			$instance['showfaces'] = $new_instance['showfaces'];	
			$instance['showstream'] = $new_instance['showstream'];
			$instance['showheader'] = $new_instance['showheader'];
			$instance['fb_bg_color'] = $new_instance['fb_bg_color'];		
			$instance['likebox_height'] = $new_instance['likebox_height'];			
	
			return $instance;
			
		  }
		
		
		/**
		 * @Facebook Widget Display
		 *
		 *
		 */
		 function widget($args, $instance) {
	
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			$pageurl = empty($instance['pageurl']) ? ' ' : apply_filters('widget_title', $instance['pageurl']);
			$showfaces = empty($instance['showfaces']) ? ' ' : apply_filters('widget_title', $instance['showfaces']);
			$showstream = empty($instance['showstream']) ? ' ' : apply_filters('widget_title', $instance['showstream']);
			$showheader = empty($instance['showheader']) ? ' ' : apply_filters('widget_title', $instance['showheader']);
			$fb_bg_color = empty($instance['fb_bg_color']) ? ' ' : apply_filters('widget_title', $instance['fb_bg_color']);								
			$likebox_height = empty($instance['likebox_height']) ? ' ' : apply_filters('widget_title', $instance['likebox_height']);													
			if(isset($showfaces) AND $showfaces == 'on'){$showfaces ='true';}else{$showfaces = 'false';}
			if(isset($showstream) AND $showstream == 'on'){$showstream ='true';}else{$showstream ='false';}
			echo cs_allow_special_char($before_widget);	

			if (!empty($title) && $title <> ' '){
				echo cs_allow_special_char($before_title);
				echo cs_allow_special_char($title);
				echo cs_allow_special_char($after_title);
			}
	
		global $wpdb, $post;?>
			
        <div class="facebook">
          <div class="facebookOuter">
            <div class="facebookInner">
              <div class="fb-like-box" data-height="<?php echo cs_allow_special_char($likebox_height);?>"  data-width="190"  data-href="<?php echo esc_url($pageurl);?>" data-border-color="#fff" data-show-faces="<?php echo cs_allow_special_char($showfaces);?>"  data-show-border="false" data-stream="<?php echo cs_allow_special_char($showstream);?>" data-header="false"> </div>
            </div>
          </div>
        </div>
        <script type="text/javascript">(function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id;
              js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
            </script> 
<?php echo cs_allow_special_char($after_widget);
	
			}
		}	
}
add_action( 'widgets_init', create_function('', 'return register_widget("facebook_module");') );

	

/**
 * @Flickr widget Class
 *
 *
 */
if ( ! class_exists( 'cs_flickr' ) ) { 
	class cs_flickr extends WP_Widget {	
	
		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
			 
		/**
		 * @init Flickr Module
		 *
		 *
		 */
		function cs_flickr() {
			$widget_ops = array('classname' => 'widget-flickr widget-gallery', 'description' => 'Type a user name to show photos in widget.');
			$this->WP_Widget('cs_flickr', 'CS : Flickr Gallery', $widget_ops);
		}
		 
		 /**
		 * @Flickr html form
		 *
		 *
		 */
		function form($instance){
			$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
			$title = $instance['title'];
			$username = isset( $instance['username'] ) ? esc_attr( $instance['username'] ) : '';
			$no_of_photos = isset( $instance['no_of_photos'] ) ? esc_attr( $instance['no_of_photos'] ) : '';	
		?>
		<p>
            <label for="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>"> Title:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>" size="40" name="<?php echo cs_allow_special_char($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </label>
		</p>
        <p>
            <label for="<?php echo cs_allow_special_char($this->get_field_id('username')); ?>"> Flickr username:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('username')); ?>" size="40" name="<?php echo cs_allow_special_char($this->get_field_name('username')); ?>" type="text" value="<?php echo esc_attr($username); ?>" />
            </label>
		</p>
		<p>
            <label for="<?php echo cs_allow_special_char($this->get_field_id('no_of_photos')); ?>"> Number of Photos:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('no_of_photos')); ?>" size='2' name="<?php echo cs_allow_special_char($this->get_field_name('no_of_photos')); ?>" type="text" value="<?php echo esc_attr($no_of_photos); ?>" />
            </label>
		</p>
		<?php
		}
			
		/**
		 * @Flickr update form data
		 *
		 *
		 */
		function update($new_instance, $old_instance){
			$instance = $old_instance;
			$instance['title'] = $new_instance['title'];
			$instance['username'] = $new_instance['username'];
			$instance['no_of_photos'] = $new_instance['no_of_photos'];
			
			return $instance;
		}
	
		/**
		 * @Display Flickr widget
		 *
		 *
		 */
		function widget($args, $instance){
			global $cs_theme_options;
			
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			$username = empty($instance['username']) ? ' ' : apply_filters('widget_title', $instance['username']);			
			$no_of_photos = empty($instance['no_of_photos']) ? ' ' : apply_filters('widget_title', $instance['no_of_photos']);	
			if($instance['no_of_photos'] == ""){$instance['no_of_photos'] = '3';}
			
			echo cs_allow_special_char($before_widget);	
			
			if (!empty($title) && $title <> ' '){
				echo cs_allow_special_char($before_title);
				echo cs_allow_special_char($title);
				echo cs_allow_special_char($after_title);
			}
			
			$get_flickr_array = array();
					
			$apiKey = $cs_theme_options['flickr_key'];
			$apiSecret = $cs_theme_options['flickr_secret'];
			
			if($apiKey <> ''){
			
				// Getting transient
				$cachetime = 86400;
				$transient = 'flickr_gallery_data';
				$check_transient = get_transient($transient);
				
				// Get Flickr Gallery saved data
				$saved_data = get_option('flickr_gallery_data');
				
				$db_apiKey = '';
				$db_user_name = '';
				$db_total_photos = '';
				
				if($saved_data <> ''){
					$db_apiKey = isset($saved_data['api_key']) ? $saved_data['api_key'] : '';
					$db_user_name = isset($saved_data['user_name']) ? $saved_data['user_name'] : '';
					$db_total_photos = isset($saved_data['total_photos']) ? $saved_data['total_photos'] : '';
				}
				
				if( $check_transient === false || ($apiKey <> $db_apiKey || $username <> $db_user_name || $no_of_photos <> $db_total_photos) ){
				
					$user_id = "https://api.flickr.com/services/rest/?method=flickr.people.findByUsername&api_key=".$apiKey."&username=".$username."&format=json&nojsoncallback=1";
					
					$user_info = file_get_contents($user_id);
					$user_info = json_decode($user_info, true);
								
					if ($user_info['stat'] == 'ok') {
						
						$user_get_id = $user_info['user']['id'];
						
						$get_flickr_array['api_key'] = $apiKey;
						$get_flickr_array['user_name'] = $username;
						$get_flickr_array['user_id'] = $user_get_id;
						
						$url = "https://api.flickr.com/services/rest/?method=flickr.people.getPublicPhotos&api_key=".$apiKey."&user_id=".$user_get_id."&per_page=".$no_of_photos."&format=json&nojsoncallback=1";
						$content = file_get_contents($url);
						$content = json_decode($content, true);
						
						if ($content['stat'] == 'ok') {
							$counter = 0;
							echo '<ul class="gallery-list">';			 				
							foreach ((array)$content['photos']['photo'] as $photo) {
								
								$image_file = "https://farm{$photo['farm']}.staticflickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_s.jpg";
								
								$img_headers = get_headers($image_file);
								if(strpos($img_headers[0], '200') !== false) {
									
									$image_file = $image_file;
								}
								else{
									$image_file = "https://farm{$photo['farm']}.staticflickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_q.jpg";
									$img_headers = get_headers($image_file);
									if(strpos($img_headers[0], '200') !== false) {
										
										$image_file = $image_file;
									}
									else{
										$image_file = get_template_directory_uri().'/assets/images/no_image_thumb.jpg';
									}
								}
								
								echo '<li>';
								echo "<a target='_blank' title='" . $photo['title'] . "' href='https://www.flickr.com/photos/" . $photo['owner'] . "/" . $photo['id'] . "/'>";
								echo "<img alt='".$photo['title']."' src='".$image_file."'>";
								echo "</a>";
								echo '</li>';
														
								$counter++;
								
								$get_flickr_array['photo_src'][] = $image_file;
								$get_flickr_array['photo_title'][] = $photo['title'];
								$get_flickr_array['photo_owner'][] = $photo['owner'];
								$get_flickr_array['photo_id'][] = $photo['id'];
								
							}
							echo '</ul>';
							
							$get_flickr_array['total_photos'] = $counter;
							
							// Setting Transient
							set_transient( $transient, true, $cachetime );
							update_option('flickr_gallery_data', $get_flickr_array);
							
							if($counter == 0) _e('No result found.', 'lassic');
						}
						
						else {
							echo 'Error:' . $content['code'] . ' - ' . $content['message'];
						}
					}
					
					else {
						echo 'Error:' . $user_info['code'] . ' - ' . $user_info['message'];
					}
				
				}
				else{
					if( get_option('flickr_gallery_data') <> '' ){
						
						$flick_data = get_option('flickr_gallery_data');
						echo '<ul class="gallery-list">';
							if(isset($flick_data['photo_src'])):
								$i = 0;
								foreach($flick_data['photo_src'] as $ph){
									echo '<li>';
									echo "<a target='_blank' title='" . $flick_data['photo_title'][$i] . "' href='https://www.flickr.com/photos/" . $flick_data['photo_owner'][$i] . "/" . $flick_data['photo_id'][$i] . "/'>";
									echo "<img alt='".$flick_data['photo_title'][$i]."' src='".$flick_data['photo_src'][$i]."'>";
									echo "</a>";
									echo '</li>';
									$i++;
								}
							endif;
						echo '</ul>';
					}
					else{
						_e('No result found.', 'lassic');
					}
				}
			
			}
			else{
				_e('Please Enter Flickr API key from Theme Options.', 'lassic');
			}
			echo cs_allow_special_char($after_widget);
			
		}
	}
}
add_action('widgets_init', create_function('', 'return register_widget("cs_flickr");'));


/**
 * @Recent posts widget Class
 *
 *
 */

if ( ! class_exists( 'recentposts' ) ) { 
	class recentposts extends WP_Widget{
	
	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
		 
	/**
	 * @init Recent posts Module
	 *
	 *
	 */
	 function recentposts(){
		$widget_ops = array('classname' => 'widget-recent-blog widget_latest_post', 'description' => 'Recent Posts from category.' );
		$this->WP_Widget('recentposts', 'CS : Recent Posts', $widget_ops);
	 }
	 
	 /**
	 * @Recent posts html form
	 *
	 *
	 */
	 function form($instance){
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = $instance['title'];
		$select_category = isset( $instance['select_category'] ) ? esc_attr( $instance['select_category'] ) : '';
		$showcount = isset( $instance['showcount'] ) ? esc_attr( $instance['showcount'] ) : '';	
		$thumb = isset( $instance['thumb'] ) ? esc_attr( $instance['thumb'] ) : '';
	?>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>"> Title:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>" size="40" name="<?php echo cs_allow_special_char($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
          </label>
        </p>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('select_category')); ?>"> Select Category:
            <select id="<?php echo cs_allow_special_char($this->get_field_id('select_category')); ?>" name="<?php echo cs_allow_special_char($this->get_field_name('select_category')); ?>" style="width:225px">
              <option value="" >All</option>
              <?php
				$categories = get_categories();
				if($categories <> ""){
					foreach ( $categories as $category ) {?>
					  <option <?php if($select_category == $category->slug){echo 'selected';}?> value="<?php echo cs_allow_special_char($category->slug);?>" ><?php echo cs_allow_special_char($category->name);?></option>
					<?php 
					}
				}?>
            </select>
          </label>
        </p>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('showcount')); ?>"> Number of Posts To Display:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('showcount')); ?>" size='2' name="<?php echo cs_allow_special_char($this->get_field_name('showcount')); ?>" type="text" value="<?php echo esc_attr($showcount); ?>" />
          </label>
        </p>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('thumb')); ?>"> Display Thumbinals:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('thumb')); ?>" size='2' name="<?php echo cs_allow_special_char($this->get_field_name('thumb')); ?>" value="true" type="checkbox"  <?php if(isset($instance['thumb']) && $instance['thumb']=='true' ) echo 'checked="checked"'; ?> />
          </label>
        </p>
        <?php
        }
		
		/**
		 * @Recent posts update form data
		 *
		 *
		 */
		 function update($new_instance, $old_instance){
			  $instance = $old_instance;
			  $instance['title'] = $new_instance['title'];
			  $instance['select_category'] = $new_instance['select_category'];
			  $instance['showcount'] = $new_instance['showcount'];
			  $instance['thumb'] = $new_instance['thumb'];
			
			  return $instance;
		 }

		 /**
		 * @Display Recent posts widget
		 *
		 *
		 */
		 function widget($args, $instance){
			  global $cs_node;
		
			  extract($args, EXTR_SKIP);
			  $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			  $select_category = empty($instance['select_category']) ? ' ' : apply_filters('widget_title', $instance['select_category']);			
			  $showcount = empty($instance['showcount']) ? ' ' : apply_filters('widget_title', $instance['showcount']);	
			  $thumb = isset( $instance['thumb'] ) ? esc_attr( $instance['thumb'] ) : '';						
			  if($instance['showcount'] == ""){$instance['showcount'] = '-1';}
		
			  echo cs_allow_special_char($before_widget);	
		
			  if (!empty($title) && $title <> ' '){
				  echo cs_allow_special_char($before_title);
				  echo cs_allow_special_char($title);
				  echo cs_allow_special_char($after_title);
			  }
		
		global $wpdb, $post;?>
		<?php
			  wp_reset_query();
			  
			   /**
				 * @Display Recent posts
				 *
				 *
				 */
				if(isset($select_category) and $select_category <> ' ' and $select_category <> ''){
					$args = array( 'posts_per_page' => "$showcount",'post_type' => 'post','category_name' => "$select_category", 'ignore_sticky_posts' => 1);
				}else{
					$args = array( 'posts_per_page' => "$showcount",'post_type' => 'post', 'ignore_sticky_posts' => 1);
				}

			  $custom_query = new WP_Query($args);
			  if ( $custom_query->have_posts() <> "" ) {
				  $cs_title_limit = '200';
				  if($thumb <> true) echo '<ul>';
				  while ( $custom_query->have_posts()) : $custom_query->the_post();
				  $post_xml = get_post_meta($post->ID, "post", true);	
				  $cs_xmlObject = new stdClass();
				  $cs_noimage = '';
				  if ( $post_xml <> "" ) {
					  $cs_xmlObject = new SimpleXMLElement($post_xml);

				  }//43
				  
				  if($thumb <> true){
						?>
						 <li> 
                        	<span style="color:#999; font-size:12px; display:inline-block; border-bottom:1px dotted; margin-bottom:5px; text-transform:uppercase">
								<?php  
                                      $categories_list = get_the_term_list( get_the_id(), 'category', '', ',', '' );
									  $cs_terms = get_the_terms(get_the_id(), 'category' );
 									  $cs_terms = limit_terms($cs_terms);
 									 	foreach($cs_terms as $cs_term){
									  		$cs_link = get_term_link( $cs_term,'category' );
				  							echo '<a href="'.esc_url($cs_link).'">'.$cs_term->name.'</a>';
										}	
                                ?>
                            </span>
                          	<h5>
                            	<a class="cs-colrhvr" href="<?php the_permalink();?>">
									<?php cs_get_title($cs_title_limit); ?>
                            	</a>
                            </h5>
                          	<p><?php echo get_the_date('F d, Y',$post->ID);?></p>
                        </li>
						  <?php
				  }
				  else{
				  $cs_noimage = '';
				  $width = 150;
				  $height = 150;
				  $image_id = get_post_thumbnail_id( $post->ID );
				  $image_url = cs_attachment_image_src($image_id, $width, $height);
				  if($image_id == ''){
					  $cs_noimage = ' class="cs-noimage"';	
				  }
				  ?>
                  <article<?php echo cs_allow_special_char($cs_noimage); ?>>
                    <?php 
					if($image_id <> ''){
					?>
                    <figure><a href="<?php esc_url(the_permalink());?>">
                    	<img alt="<?php the_title();?>" width="70" height="70" src="<?php echo esc_url($image_url);?>"></a></figure>
                    <?php 
					}
					?>
                    <div class="infotext">
                      	<h5>
                      		<a class="cs-colrhvr" href="<?php esc_url(the_permalink());?>">
					  			<?php cs_get_title($cs_title_limit); ?>
					  		</a>
                      	</h5>
                      	<ul class="post-option">
                        	<li>
                           	<?php echo get_the_date('F d, Y',$post->ID);?>
                        	</li>
                      </ul>
                    </div>
                  </article>
                  <?php
				  }
				endwhile; 
				 if($thumb <> true) echo '</ul>';
                  }
                  else {
                      if ( function_exists( 'cs_no_result_found' ) ) { cs_no_result_found(false); }

                  }
			    echo cs_allow_special_char($after_widget);
			  }
		  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("recentposts");') );


/**
 * @Recent posts project widget Class
 *
 *
 */

if ( ! class_exists( 'recentpostsproj' ) ) { 
	class recentpostsproj extends WP_Widget{
	
	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
		 
	/**
	 * @init Recent posts Module
	 *
	 *
	 */
	 function recentpostsproj(){
		$widget_ops = array('classname' => 'widget-recentproj-blog widget_latestproj_post', 'description' => 'Recent Project from category.' );
		$this->WP_Widget('recentpostsproj', 'CS : Recent Projects', $widget_ops);
	 }
	 
	 /**
	 * @Recent posts html form
	 *
	 *
	 */
	 function form($instance){
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = $instance['title'];
		$select_category = isset( $instance['select_category'] ) ? esc_attr( $instance['select_category'] ) : '';
		$showcount = isset( $instance['showcount'] ) ? esc_attr( $instance['showcount'] ) : '';	
		$thumb = isset( $instance['thumb'] ) ? esc_attr( $instance['thumb'] ) : '';
	?>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>"> Title:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>" size="40" name="<?php echo cs_allow_special_char($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
          </label>
        </p>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('select_category')); ?>"> Select Category:
            <select id="<?php echo cs_allow_special_char($this->get_field_id('select_category')); ?>" name="<?php echo cs_allow_special_char($this->get_field_name('select_category')); ?>" style="width:225px">
              <option value="" >All</option>
              <?php
				$args = array(
					'taxonomy' => 'project-category'
				);
				$categories = get_categories($args);
				if($categories <> ""){
					foreach ( $categories as $category ) {?>
					  <option <?php if($select_category == $category->slug){echo 'selected';}?> value="<?php echo cs_allow_special_char($category->slug);?>" ><?php echo cs_allow_special_char($category->name);?></option>
					<?php 
					}
				}?>
            </select>
          </label>
        </p>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('showcount')); ?>"> Number of Posts To Display:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('showcount')); ?>" size='2' name="<?php echo cs_allow_special_char($this->get_field_name('showcount')); ?>" type="text" value="<?php echo esc_attr($showcount); ?>" />
          </label>
        </p>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('thumb')); ?>"> Display Thumbinals:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('thumb')); ?>" size='2' name="<?php echo cs_allow_special_char($this->get_field_name('thumb')); ?>" value="true" type="checkbox"  <?php if(isset($instance['thumb']) && $instance['thumb']=='true' ) echo 'checked="checked"'; ?> />
          </label>
        </p>
        <?php
        }
		
		/**
		 * @Recent posts update form data
		 *
		 *
		 */
		 function update($new_instance, $old_instance){
			  $instance = $old_instance;
			  $instance['title'] = $new_instance['title'];
			  $instance['select_category'] = $new_instance['select_category'];
			  $instance['showcount'] = $new_instance['showcount'];
			  $instance['thumb'] = $new_instance['thumb'];
			
			  return $instance;
		 }

		 /**
		 * @Display Recent posts widget
		 *
		 *
		 */
		 function widget($args, $instance){
			  global $cs_node;
		
			  extract($args, EXTR_SKIP);
			  $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			  $select_category = empty($instance['select_category']) ? ' ' : apply_filters('widget_title', $instance['select_category']);			
			  $showcount = empty($instance['showcount']) ? ' ' : apply_filters('widget_title', $instance['showcount']);	
			  $thumb = isset( $instance['thumb'] ) ? esc_attr( $instance['thumb'] ) : '';						
			  if($instance['showcount'] == ""){$instance['showcount'] = '-1';}
		
			  echo cs_allow_special_char($before_widget);	
		
			  if (!empty($title) && $title <> ' '){
				  echo cs_allow_special_char($before_title);
				  echo cs_allow_special_char($title);
				  echo cs_allow_special_char($after_title);
			  }
		
		global $wpdb, $post;?>
		<?php
			  wp_reset_query();
			  
			   /**
				 * @Display Recent posts
				 *
				 *
				 */
				if(isset($select_category) and $select_category <> ' ' and $select_category <> ''){
					$args = array( 'posts_per_page' => "$showcount",
									'post_type' => 'project',
									'tax_query' => array(
										array(
											'taxonomy' => 'project-category',
											'field'    => 'slug',
											'terms'    => "$select_category",
										),
									),
									'ignore_sticky_posts' => 1
					);
				}else{
					$args = array( 'posts_per_page' => "$showcount",'post_type' => 'project','ignore_sticky_posts' => 1);
				}
			  
			  $custom_query = new WP_Query($args);
			  //echo $wpdb->last_query;
			  if ( $custom_query->have_posts() <> "" ) {

				  $cs_title_limit = '200';
				  if($thumb <> true) echo '<ul>';
				  while ( $custom_query->have_posts()) : $custom_query->the_post();
				  $post_xml = get_post_meta($post->ID, "post", true);	
				  $cs_xmlObject = new stdClass();
				  $cs_noimage = '';
				  if ( $post_xml <> "" ) {
					  $cs_xmlObject = new SimpleXMLElement($post_xml);

				  }//43
				  
				  if($thumb <> true){
						?>
						 <li> 
                        	<span style="color:#999; font-size:12px; display:inline-block; border-bottom:1px dotted; margin-bottom:5px; text-transform:uppercase">
								<?php  
                                      $categories_list = get_the_term_list( get_the_id(), 'category', '', ',', '' );
									  $cs_terms = get_the_terms(get_the_id(), 'category' );
 									  $cs_terms = limit_terms($cs_terms);
 									 	foreach($cs_terms as $cs_term){
									  		$cs_link = get_term_link( $cs_term,'category' );
				  							echo '<a href="'.esc_url($cs_link).'">'.$cs_term->name.'</a>';
										}	
                                ?>
                            </span>
                          	<h5>
                            	<a class="cs-colrhvr" href="<?php the_permalink();?>">
									<?php cs_get_title($cs_title_limit); ?>
                            	</a>
                            </h5>
                          	<p><?php echo get_the_date('F d, Y',$post->ID);?></p>
                        </li>
						  <?php
				  }
				  else{
				  $cs_noimage = '';
				  $width = 150;
				  $height = 150;
				  $image_id = get_post_thumbnail_id( $post->ID );
				  $image_url = cs_attachment_image_src($image_id, $width, $height);
				  if($image_id == ''){
					  $cs_noimage = ' class="cs-noimage"';	
				  }
				  ?>
                  <article<?php echo cs_allow_special_char($cs_noimage); ?>>
                    <?php 
					if($image_id <> ''){
					?>
                    <figure><a href="<?php esc_url(the_permalink());?>">
                    	<img alt="<?php the_title();?>" width="70" height="70" src="<?php echo esc_url($image_url);?>"></a></figure>
                    <?php 
					}
					?>
                    <div class="infotext">
                      	<h5>
                      		<a class="cs-colrhvr" href="<?php esc_url(the_permalink());?>">
					  			<?php cs_get_title($cs_title_limit); ?>
					  		</a>
                      	</h5>
                      	<ul class="post-option">
                        	<li>
                           	<?php echo get_the_date('F d, Y',$post->ID);?>
                        	</li>
                      </ul>
                    </div>
                  </article>
                  <?php
				  }
				endwhile; 
				 if($thumb <> true) echo '</ul>';
                  }
                  else {
                      if ( function_exists( 'cs_no_result_found' ) ) { cs_no_result_found(false); }

                  }
			    echo cs_allow_special_char($after_widget);
			  }
		  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("recentpostsproj");') );

/**
 * @Related Posts widget Class
 *
 *
 */

if ( ! class_exists( 'relatedposts' ) ) { 
	class relatedposts extends WP_Widget{
	
	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
		 
	/**
	 * @init Recent posts Module
	 *
	 *
	 */
	 function relatedposts(){
		$widget_ops = array('classname' => 'widget-recentproj-blog widget_latestproj_post', 'description' => 'Related posts from category and tags.' );
		$this->WP_Widget('relatedposts', 'CS : Related Posts', $widget_ops);
	 }
	 
	 /**
	 * @Recent posts html form
	 *
	 *
	 */
	 function form($instance){
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = $instance['title'];
		$showcount = isset( $instance['showcount'] ) ? esc_attr( $instance['showcount'] ) : '';	
		$thumb = isset( $instance['thumb'] ) ? esc_attr( $instance['thumb'] ) : '';
	?>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>"> Title:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>" size="40" name="<?php echo cs_allow_special_char($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
          </label>
        </p>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('showcount')); ?>"> Number of Posts To Display:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('showcount')); ?>" size='2' name="<?php echo cs_allow_special_char($this->get_field_name('showcount')); ?>" type="text" value="<?php echo esc_attr($showcount); ?>" />
          </label>
        </p>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('thumb')); ?>"> Display Thumbinals:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('thumb')); ?>" size='2' name="<?php echo cs_allow_special_char($this->get_field_name('thumb')); ?>" value="true" type="checkbox"  <?php if(isset($instance['thumb']) && $instance['thumb']=='true' ) echo 'checked="checked"'; ?> />
          </label>
        </p>
        <?php
        }
		
		/**
		 * @Recent posts update form data
		 *
		 *
		 */
		 function update($new_instance, $old_instance){
			  $instance = $old_instance;
			  $instance['title'] = $new_instance['title'];
			  $instance['showcount'] = $new_instance['showcount'];
			  $instance['thumb'] = $new_instance['thumb'];
			
			  return $instance;
		 }

		 /**
		 * @Display Recent posts widget
		 *
		 *
		 */
		 function widget($args, $instance){
			  global $cs_node;
		
			  extract($args, EXTR_SKIP);
			  $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			  $showcount = empty($instance['showcount']) ? ' ' : apply_filters('widget_title', $instance['showcount']);	
			  $thumb = isset( $instance['thumb'] ) ? esc_attr( $instance['thumb'] ) : '';						
			  if($instance['showcount'] == ""){$instance['showcount'] = '-1';}
		
			  echo cs_allow_special_char($before_widget);	
		
			  if (!empty($title) && $title <> ' '){
				  echo cs_allow_special_char($before_title);
				  echo cs_allow_special_char($title);
				  echo cs_allow_special_char($after_title);
			  }
		
		global $wpdb, $post;?>
		<?php
			  wp_reset_query();
			  
			   /**
				 * @Display Recent posts
				 *
				 *
				 */
				$id_post_id = get_the_ID();
				$typo_post	= get_post_type($id_post_id);
				$category_post  = get_the_category($id_post_id);
				$taxArr		= array();
				$c_catgpost = "";
				$c_tagslug	= "";

				//echo $id_post_id.".-1<br>";
				//echo $typo_post.".-2<br>";

				array_push($taxArr, array('relation' => 'OR'));


				$posttags = get_the_tags();
				if ($posttags) {
					$c_tagslug = "'o'";
					foreach($posttags as $tag) {
						$c_tagslug = $c_tagslug.",'$tag->slug'";
					}
				}

				if($category_post){
					$c_catgpost = "'o'";
					foreach($category_post as $category) {
						$c_catgpost = $c_catgpost.",'$category->slug'";
					}

				}

				$ntypo_post = $typo_post=='post'?"category":"$typo_post-category";
				$args = array(
					'posts_per_page' => "$showcount",
					'post_type' => "$typo_post",
					'post_status' => 'publish',
					'orderby' => 'DESC',
					'tax_query' => array(
						'relation' => 'OR',
						array(
							'taxonomy' => "$typo_post-tag",
							'field'    => 'slug',
							'terms'    => explode(',',$c_tagslug),
						),
						array(
							'taxonomy' => "$ntypo_post",
							'field'    => 'slug',
							'terms'    => explode(',',$c_catgpost),
						),
					),
					'post__not_in' => array ($id_post_id),
				);

				//$args = array( 'posts_per_page' => "$showcount",'post_type' => 'project','ignore_sticky_posts' => 1);

			  //print_r($args);
			  $custom_query = new WP_Query($args);
			  //echo $wpdb->last_query;
			$cumple = '';
			if($custom_query->have_posts() <> "") {
				$cumple='1';
			} else {
				
				$cumple='1';	  
				$args = array(
					'posts_per_page' => "$showcount",
					'post_type' => "$typo_post",
					'post_status' => 'publish',
					'orderby' => 'rand',
					'post__not_in' => array ($id_post_id),
				);	
				$custom_query = new WP_Query($args);  
				  
				if ( $custom_query->have_posts() == "" ) {
					$cumple='0';
				}


			}
			
			if($cumple == '1'){
				  $cs_title_limit = '200';
				  if($thumb <> true) echo '<ul>';
				  while ( $custom_query->have_posts()) : $custom_query->the_post();
				  $post_xml = get_post_meta($post->ID, "post", true);	
				  $cs_xmlObject = new stdClass();
				  $cs_noimage = '';
				  if ( $post_xml <> "" ) {
					  $cs_xmlObject = new SimpleXMLElement($post_xml);

				  }//43
				  
				  if($thumb <> true){
						?>
						 <li> 
                        	<span style="color:#999; font-size:12px; display:inline-block; border-bottom:1px dotted; margin-bottom:5px; text-transform:uppercase">
								<?php  
                                      $categories_list = get_the_term_list( get_the_id(), 'category', '', ',', '' );
									  $cs_terms = get_the_terms(get_the_id(), 'category' );
 									  $cs_terms = limit_terms($cs_terms);
 									 	foreach($cs_terms as $cs_term){
									  		$cs_link = get_term_link( $cs_term,'category' );
				  							echo '<a href="'.esc_url($cs_link).'">'.$cs_term->name.'</a>';
										}	
                                ?>
                            </span>
                          	<h5>
                            	<a class="cs-colrhvr" href="<?php the_permalink();?>">
									<?php cs_get_title($cs_title_limit); ?>
                            	</a>
                            </h5>
                          	<p><?php echo get_the_date('F d, Y',$post->ID);?></p>
                        </li>
						  <?php
				  }
				  else{
				  $cs_noimage = '';
				  $width = 150;
				  $height = 150;
				  $image_id = get_post_thumbnail_id( $post->ID );
				  $image_url = cs_attachment_image_src($image_id, $width, $height);
				  if($image_id == ''){
					  $cs_noimage = ' class="cs-noimage"';	
				  }
				  ?>
                  <article<?php echo cs_allow_special_char($cs_noimage); ?>>
                    <?php 
					if($image_id <> ''){
					?>
                    <figure><a href="<?php esc_url(the_permalink());?>">
                    	<img alt="<?php the_title();?>" width="70" height="70" src="<?php echo esc_url($image_url);?>"></a></figure>
                    <?php 
					}
					?>
                    <div class="infotext">
                      	<h5>
                      		<a class="cs-colrhvr" href="<?php esc_url(the_permalink());?>">
					  			<?php cs_get_title($cs_title_limit); ?>
					  		</a>
                      	</h5>
                      	<ul class="post-option">
                        	<li>
                           	<?php echo get_the_date('F d, Y',$post->ID);?>
                        	</li>
                      </ul>
                    </div>
                  </article>
                  <?php
				  }
				endwhile; 
				 if($thumb <> true) echo '</ul>';
			}else{
				if ( function_exists( 'cs_no_result_found' ) ) { cs_no_result_found(false); }
			}
			echo cs_allow_special_char($after_widget);
		}
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("relatedposts");') );

/**
 * @Twitter Tweets widget Class
 *
 *
 */
if ( ! class_exists( 'cs_twitter_widget' ) ) { 
	class cs_twitter_widget extends WP_Widget {
		
		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
			 
		/**
		 * @init Twitter Module
		 *
		 *
		 */
		function cs_twitter_widget() {
			$widget_ops = array('classname' => 'twitter_widget', 'description' => 'Twitter Widget');
			$this->WP_Widget('cs_twitter_widget', 'CS : Twitter Widget', $widget_ops);
		}
		
		
		/**
		 * @Twitter html form
		 *
		 *
		 */
		 function form($instance) {
			$instance = wp_parse_args((array) $instance, array('title' => ''));
			$title = $instance['title'];
			$username = isset($instance['username']) ? esc_attr($instance['username']) : '';
			$numoftweets = isset($instance['numoftweets']) ? esc_attr($instance['numoftweets']) : '';
 		?>
            <label for="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>"> <span>Title: </span>
              <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>" size="40" name="<?php echo cs_allow_special_char($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </label>
            <label for="screen_name">User Name<span class="required">(*)</span>: </label>
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('username')); ?>" size="40" name="<?php echo cs_allow_special_char($this->get_field_name('username')); ?>" type="text" value="<?php echo esc_attr($username); ?>" />
            <label for="tweet_count">
            <span>Num of Tweets: </span>
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('numoftweets')); ?>" size="2" name="<?php echo cs_allow_special_char($this->get_field_name('numoftweets')); ?>" type="text" value="<?php echo esc_attr($numoftweets); ?>" />
            <div class="clear"></div>
            </label>
            <?php
		}
		/**
		 * @Twitter update form data 
		 *
		 *
		 */
		 function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = $new_instance['title'];
			$instance['username'] = $new_instance['username'];
			$instance['numoftweets'] = $new_instance['numoftweets'];
			
 			return $instance;
		 }
		/**
		 * @Display Twitter widget
		 *
		 *
		 */
  		 function widget($args, $instance) {
			global $cs_theme_options;
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			$username = $instance['username'];
 			$numoftweets = $instance['numoftweets'];		
	 		if($numoftweets == ''){$numoftweets = 2;}
			echo cs_allow_special_char($before_widget);
  			// WIDGET display CODE Start
			if (!empty($title) && $title <> ' '){
				echo cs_allow_special_char($before_title . $title . $after_title);
			}
			if(strlen($username) > 1){
					$text ='';
					$return = '';
					$cacheTime =10000;
					$transName = 'latest-tweets';
					require_once get_template_directory() . '/include/theme-components/cs-twitter/twitteroauth.php';
					
					$consumerkey = $cs_theme_options['cs_consumer_key'];
					$consumersecret = $cs_theme_options['cs_consumer_secret'];
					$accesstoken = $cs_theme_options['cs_access_token'];
					$accesstokensecret = $cs_theme_options['cs_access_token_secret'];
 					$connection = new TwitterOAuth($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
					
					$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$username."&count=".$numoftweets);
					//print_r($tweets);
					if(!is_wp_error($tweets) and is_array($tweets)){
						
						set_transient($transName, $tweets, 60 * $cacheTime);
					}else{
						$tweets= get_transient('latest-tweets');
					}
					
  					if(!is_wp_error($tweets) and is_array($tweets)){
						$rand_id = rand(5, 300);
						
						$return .= "";
							foreach($tweets as $tweet) {
 								$text = $tweet->{'text'};
  								foreach($tweet->{'user'} as $type => $userentity) {
									if($type == 'profile_image_url') {	
										$profile_image_url = $userentity;
									} else if($type == 'screen_name'){
										$screen_name = '<a href="https://twitter.com/' . $userentity . '" target="_blank" class="colrhover" title="' . $userentity . '">@' . $userentity . '</a>';
									}
								}
								foreach($tweet->{'entities'} as $type => $entity) {
								if($type == 'urls') {						
									foreach($entity as $j => $url) {
										$display_url = '<a href="' . $url->{'url'} . '" target="_blank" title="' . $url->{'expanded_url'} . '">' . $url->{'display_url'} . '</a>';
										$update_with = 'Read more at '.$display_url;
										$text = str_replace('Read more at '.$url->{'url'}, '', $text);
										$text = str_replace($url->{'url'}, '', $text);
									}
								} else if($type == 'hashtags') {
									foreach($entity as $j => $hashtag) {
										$update_with = '<a href="https://twitter.com/search?q=%23' . $hashtag->{'text'} . '&amp;src=hash" target="_blank" title="' . $hashtag->{'text'} . '">#' . $hashtag->{'text'} . '</a>';
										$hashtag->{'text'};
										$text = str_replace('#'.$hashtag->{'text'}, $update_with, $text);
									}
								} else if($type == 'user_mentions') {
										foreach($entity as $j => $user) {
											  $update_with = '<a href="https://twitter.com/' . $user->{'screen_name'} . '" target="_blank" title="' . $user->{'name'} . '">@' . $user->{'screen_name'} . '</a>';
											  $text = str_replace('@'.$user->{'screen_name'}, $update_with, $text);
										}
									}
								} 
								$large_ts = time();
								$n = $large_ts - strtotime($tweet->{'created_at'});
								if($n < (60)){ $posted = sprintf(__('%d seconds ago','lassic'),$n); }
								elseif($n < (60*60)) { $minutes = round($n/60); $posted = sprintf(_n('About a Minute Ago','%d Minutes Ago',$minutes,'lassic'),$minutes); }
								elseif($n < (60*60*16)) { $hours = round($n/(60*60)); $posted = sprintf(_n('About an Hour Ago','%d Hours Ago',$hours,'lassic'),$hours); }
								elseif($n < (60*60*24)) { $hours = round($n/(60*60)); $posted = sprintf(_n('About an Hour Ago','%d Hours Ago',$hours,'lassic'),$hours); }
								elseif($n < (60*60*24*6.5)) { $days = round($n/(60*60*24)); $posted = sprintf(_n('About a Day Ago','%d Days Ago',$days,'lassic'),$days); }
								elseif($n < (60*60*24*7*3.5)) { $weeks = round($n/(60*60*24*7)); $posted = sprintf(_n('About a Week Ago','%d Weeks Ago',$weeks,'lassic'),$weeks); } 
								elseif($n < (60*60*24*7*4*11.5)) { $months = round($n/(60*60*24*7*4)) ; $posted = sprintf(_n('About a Month Ago','%d Months Ago',$months,'lassic'),$months);}
								elseif($n >= (60*60*24*7*4*12)){$years=round($n/(60*60*24*7*52)) ; $posted = sprintf(_n('About a year Ago','%d years Ago',$years,'lassic'),$years);}
								$return .="<article><p>";
								$return .= $text;
								$return .= "</p><div class='text'><a href='https://twitter.com/" . $username . "'>@".$username."</a>&nbsp;<span>" . $posted. "</span>";
								$return .= "</div></article>";
						}
					
				$return .= "";
				if(isset($profile_image_url) && $profile_image_url <> ''){$profile_image_url = '<img src="'.$profile_image_url.'" alt="">';} else {$profile_image_url = '';}
				$return .= '';
				echo cs_allow_special_char($return);

 		}else{
			if(isset($tweets->errors[0]) && $tweets->errors[0] <> ""){
				echo '<span class="bad_authentication">'.$tweets->errors[0]->message.". Please enter valid Twitter API Keys </span>";
			}else{
				echo '<span class="bad_authentication">';
					cs_no_result_found(false);
				echo '</span>';
			}
		}
	}else{
			echo '<span class="bad_authentication">';			
				cs_no_result_found(false);
			echo '</span>';
		}
		echo cs_allow_special_char($after_widget);
		}
 	}
}
add_action('widgets_init', create_function('', 'return register_widget("cs_twitter_widget");'));

/**
 * @latest reviews widget Class
 *
 *
 */
class contactinfo extends WP_Widget{
	
	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
		 
	/**
	 * @init Contact Info Module
	 *
	 *
	 */
	 
	function contactinfo()	{
		$widget_ops = array('classname' => 'cs-widget-contact', 'description' => 'Fotter Contact Information.' );
		$this->WP_Widget('contactinfo', 'CS : Contact info', $widget_ops);
	}
	
	/**
	 * @Contact Info html form
	 *
	 *
	 */
	function form($instance){
		$instance = wp_parse_args( (array) $instance );
		$image_url 	= isset( $instance['image_url'] ) ? esc_attr( $instance['image_url'] ) : '';	
		$address 	= isset( $instance['address'] ) ? esc_attr( $instance['address'] ) : '';	
		$phone 		= isset( $instance['phone'] ) ? esc_attr( $instance['phone'] ) : '';
		$fax 		= isset( $instance['fax'] ) ? esc_attr( $instance['fax'] ) : '';	
		$email 		= isset( $instance['email'] ) ? esc_attr( $instance['email'] ) : '';
		$quick_link	= isset( $instance['quick_link'] ) ? esc_attr( $instance['quick_link'] ) : '';
		$randomID   = rand(40, 9999999);
 	?>
    <ul class="form-elements-widget">
      <li class="to-label" style="margin-top:20px;">
        <label>Image</label>
      </li>
      <li class="to-field">
        <input id="form-widget_cs_widget_logo<?php echo absint($randomID)?>" name="<?php echo cs_allow_special_char($this->get_field_name('image_url')); ?>" type="hidden" class="" value="<?php echo esc_url($image_url); ?>"/>
        <label class="browse-icon" style="width:100%;">
        <input name="form-widget_cs_widget_logo<?php echo absint($randomID)?>"  type="button" class="uploadMedia left" value="Browse"/>
        </label>
      </li>
    </ul>
    <div class="page-wrap"  id="form-widget_cs_widget_logo<?php echo absint($randomID);?>_box" style="margin-top:10px; margin-bottom:10px; float:left; overflow:hidden; display:<?php echo cs_allow_special_char($image_url)&& cs_allow_special_char($image_url) !='' ? 'inline' : 'none';?>">
      <div class="gal-active">
        <div class="dragareamain" style="padding-bottom:0px;">
          <ul id="gal-sortable" style="margin-bottom:0px;">
            <li class="ui-state-default" style="margin:6px">
              <div class="thumb-secs"> <img src="<?php echo cs_allow_special_char($image_url); ?>"  id="form-widget_cs_widget_logo<?php echo absint($randomID);?>_img" style="max-height:80px; max-width:180px" alt="" />
                <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_widget_logo<?php echo absint($randomID)?>')" class="delete"></a> </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
            
	<p style="margin-top:0px; float:left;">
		<label for="<?php echo cs_allow_special_char($this->get_field_id('address')); ?>"> Address:<br />
			<textarea cols="20" rows="5" id="<?php echo cs_allow_special_char($this->get_field_id('address')); ?>" name="<?php echo cs_allow_special_char($this->get_field_name('address')); ?>" style="width:315px"><?php echo esc_attr($address); ?></textarea>
		</label>
	</p>
	<p style="margin-top:0px; float:left;">
		<label for="<?php echo cs_allow_special_char($this->get_field_id('phone')); ?>"> Phone #:<br />
			<input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('phone')); ?>" size="40"
            name="<?php echo cs_allow_special_char($this->get_field_name('phone')); ?>" type="text" value="<?php echo esc_attr($phone); ?>" />
		</label>
     </p>
     
     <p style="margin-top:0px; float:left;">
        <label for="<?php echo cs_allow_special_char($this->get_field_id('fax')); ?>"> Fax #:<br />
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('fax')); ?>" size="40" 
            name="<?php echo cs_allow_special_char( $this->get_field_name('fax')); ?>" type="text" value="<?php echo esc_attr($fax); ?>" />
        </label>
    </p>
    
    <p style="margin-top:0px; float:left;">
        <label for="<?php echo cs_allow_special_char($this->get_field_id('email')); ?>"> Email #:<br />
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('email')); ?>" size="40" 
            name="<?php echo cs_allow_special_char($this->get_field_name('email')); ?>" type="text" value="<?php echo esc_attr($email); ?>" />
        </label>
    </p>
    <p style="margin-top:0px; float:left;">
        <label for="<?php echo cs_allow_special_char($this->get_field_id('quick_link')); ?>">Time :<br />
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('quick_link')); ?>" size="40" 
            name="<?php echo cs_allow_special_char($this->get_field_name('quick_link')); ?>" type="text" value="<?php echo esc_attr($quick_link); ?>" />
        </label>
    </p>
	<?php
	}
	
	/**
	 * @Update Info html form
	 *
	 *
	 */
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['image_url'] = $new_instance['image_url'];
		$instance['address']   = $new_instance['address'];
		$instance['phone']     = $new_instance['phone'];
		$instance['fax']    = $new_instance['fax'];
		$instance['email']     = $new_instance['email'];
		$instance['quick_link']     = $new_instance['quick_link'];
 		return $instance;
	}
	
	/**
	 * @Widget Info html form
	 *
	 *
	 */
	function widget($args, $instance){
		global $px_node;
		extract($args, EXTR_SKIP);
		$image_url = empty($instance['image_url']) ? '' : apply_filters('widget_title', $instance['image_url']);
		$address = empty($instance['address']) ? '' : apply_filters('widget_title', $instance['address']);		
		$phone = empty($instance['phone']) ? '' : apply_filters('widget_title', $instance['phone']);
		$fax = empty($instance['fax']) ? '' : apply_filters('widget_title', $instance['fax']);
		$email = empty($instance['email']) ? '' : apply_filters('widget_title', $instance['email']);
		$quick_link = empty($instance['quick_link']) ? '' : apply_filters('widget_title', $instance['quick_link']);
		echo cs_allow_special_char($before_widget);	
		if ( isset ( $image_url ) && $image_url != '' ) {
			echo '<div class="logo"><a href="'.esc_url( home_url() ).'"><img src="'.$image_url.'" alt="" /></a></div>';
		}
         
			if(isset($address) and $address<>''){
				echo '<p>'.do_shortcode(htmlspecialchars_decode($address)).'</p>';
			}
			echo '<ul>';
			if(isset($phone) and $phone<>''){
				echo '<li><i class="icon-phone8"></i>'.htmlspecialchars_decode($phone).'</li>';
			}
			if(isset($fax) and $fax<>''){
				echo '<li><i class="icon-printer3"></i>'.htmlspecialchars_decode($fax).'</li>';
			}
			if(isset($email) and $email<>''){
				echo '<li><i class="icon-envelope4"></i><a href="mailto:'.$email.'">'.htmlspecialchars_decode($email).'</a></li>';
			}
			
			if($quick_link<>''){
				echo '<li><i class="icon-clock7"></i>'.htmlspecialchars_decode($quick_link).'</li>';
 			}
			echo '</ul>';

    echo cs_allow_special_char($after_widget);
	}
}
add_action('widgets_init', create_function('', 'return register_widget("contactinfo");'));

/**
 * @Contact form widget Class
 *
 *
 */
if ( ! class_exists( 'cs_contact_msg' ) ) { 
	class cs_contact_msg extends WP_Widget {	
	
	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
		 
	/**
	 * @init Contact Module
	 *
	 *
	 */
	 function cs_contact_msg() {
		$widget_ops = array('classname' => 'widget-form', 'description' => 'Select contact form to show in widget.');
		$this->WP_Widget('cs_contact_msg', 'CS : Contact Form', $widget_ops);
	 }
	 
	 /**
	 * @Contact html form
	 *
	 *
	 */
	 function form($instance) {
		$instance = wp_parse_args((array) $instance, array('title' => '' ));
		$title = $instance['title'];
		$contact_email = isset($instance['contact_email']) ? esc_attr($instance['contact_email']) : '';
		$contact_succ_msg = isset($instance['contact_succ_msg']) ? esc_attr($instance['contact_succ_msg']) : '';
		?>
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>"> Title:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('title')); ?>" size="40" name="<?php echo cs_allow_special_char($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
          </label>
        </p>
        
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('contact_email')); ?>"> Contact Email:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('contact_email')); ?>" size="40" name="<?php echo cs_allow_special_char($this->get_field_name('contact_email')); ?>" type="text" value="<?php echo esc_attr($contact_email); ?>" />
          </label>
        </p>
        
        <p>
          <label for="<?php echo cs_allow_special_char($this->get_field_id('contact_succ_msg')); ?>"> Success Message:
            <input class="upcoming" id="<?php echo cs_allow_special_char($this->get_field_id('contact_succ_msg')); ?>" size="40" name="<?php echo cs_allow_special_char($this->get_field_name('contact_succ_msg')); ?>" type="text" value="<?php echo esc_attr($contact_succ_msg); ?>" />
          </label>
        </p>
        

<?php
 		}
		
		/**
		 * @Contact Update form data
		 *
		 *
		 */
		 function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = $new_instance['title'];
			$instance['contact_email'] = $new_instance['contact_email'];
			$instance['contact_succ_msg'] = $new_instance['contact_succ_msg'];
			
   			return $instance;
		}
		
		/**
		 * @Display Contact widget
		 *
		 *
		 */
		function widget($args, $instance) {
			extract($args, EXTR_SKIP);
			global $wpdb, $post;
			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			$contact_email = isset($instance['contact_email']) ? esc_attr($instance['contact_email']) : '';
			$contact_succ_msg = isset($instance['contact_succ_msg']) ? esc_attr($instance['contact_succ_msg']) : '';
			
			// WIDGET display CODE Start
			echo cs_allow_special_char($before_widget);
			if (strlen($title) <> 1 || strlen($title) <> 0) {
				echo cs_allow_special_char($before_title . $title . $after_title);
			}
			
			
            $msg_form_counter = rand(1, 999); 
			if ( function_exists( 'cs_enqueue_validation_script' ) ) { cs_enqueue_validation_script(); }
			$error	= __('An error Occured, please try again later.', 'lassic');
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					var container = $('');
					var validator = jQuery("#frm<?php echo absint($msg_form_counter)?>").validate({
						messages:{
							contact_name: '',
							contact_email:{
								required: '',
								email:'',
							},
							subject: {
								required:'',
							},
							contact_msg: '',
						},
						errorContainer: container,
						errorLabelContainer: jQuery(container),
						errorElement:'div',
						errorClass:'frm_error',
						meta: "validate"
					});
				});
				function frm_submit<?php echo cs_allow_special_char($msg_form_counter)?>(){
					var $ = jQuery;
					$("#submit_btn<?php echo cs_allow_special_char($msg_form_counter) ?>").hide();
					$("#loading_div<?php echo cs_allow_special_char($msg_form_counter) ?>").html('<img src="<?php echo esc_js(get_template_directory_uri());?>/assets/images/ajax-loader.gif" alt="" />');
					var datastring =$('#frm<?php echo cs_allow_special_char($msg_form_counter) ?>').serialize() +"&cs_contact_email=<?php echo esc_js($contact_email);?>&cs_contact_succ_msg=<?php echo cs_allow_special_char($contact_succ_msg);?>&cs_contact_error_msg=<?php echo cs_allow_special_char($error);?>&action=cs_contact_form_submit";
					$.ajax({
						type: 'POST', 
						url: '<?php echo esc_js(admin_url('admin-ajax.php')); ?>',
						data: datastring, 
						dataType: "json",
						success: function(response) {
							if (response.type == 'error'){
								$("#loading_div<?php echo cs_allow_special_char($msg_form_counter);?>").html('');
								$("#loading_div<?php echo cs_allow_special_char($msg_form_counter);?>").hide();
								$("#message<?php echo cs_allow_special_char($msg_form_counter); ?>").addClass('error_mess');
								$("#message<?php echo cs_allow_special_char($msg_form_counter); ?>").show();
								$("#message<?php echo cs_allow_special_char($msg_form_counter); ?>").html(response.message);
							} else if (response.type == 'success'){
								$("#loading_div<?php echo cs_allow_special_char($msg_form_counter); ?>").html('');
								$("#message<?php echo cs_allow_special_char($msg_form_counter); ?>").addClass('succ_mess');
								$("#message<?php echo cs_allow_special_char($msg_form_counter); ?>").show();
								$("#message<?php echo cs_allow_special_char($msg_form_counter); ?>").html(response.message);
							}
						}
					});
				}
			</script>
            
            <div id="form_hide<?php echo absint($msg_form_counter);?>">
            <form id="frm<?php echo absint($msg_form_counter);?>" name="frm<?php echo absint($msg_form_counter);?>" method="post" action="javascript:<?php echo "frm_submit".$msg_form_counter. "()";
                ?>" novalidate>
                <ul class="cs-group">
                   	<li>
                    	<label class="cs-for-user">
                      		<input type="text" placeholder="Name" name="contact_name" id="contact_name" class="nameinput {validate:{required:true}}">
                    	</label>
                    </li>
                    <li>
                    	<label class="cs-for-mail">
                      		<input type="text" placeholder="Email" name="contact_email" id="contact_email" class="emailinput {validate:{required:true ,email:true}}">
                        </label>
                    </li>
                    <li>
                      <textarea placeholder="Message" name="contact_msg" id="contact_msg" class="{validate:{required:true}}"></textarea>
                    </li>
                    <li>
                      <input type="hidden" value="<?php echo cs_allow_special_char($contact_succ_msg);?>" name="cs_contact_succ_msg">
                      <input type="hidden" name="bloginfo" value="<?php echo get_bloginfo() ?>" />
                      <input type="hidden" name="counter_node" value="<?php echo absint($msg_form_counter)?>" />
                      <span id="loading_div<?php echo absint($msg_form_counter)?>"><i class="icon-envelope"></i></span>
                      <div id="message<?php echo absint($msg_form_counter);?>" style="display:none;"></div>
                      <input type="submit" value="Send message" name="submit" id="submit_btn<?php echo absint($msg_form_counter)?>" class="cs-submit-btn">
                    </li>
                </ul>
            </form>
            </div>
			<?php
			
			echo cs_allow_special_char($after_widget); // WIDGET display CODE End
		}
	}
}
add_action('widgets_init', create_function('', 'return register_widget("cs_contact_msg");'));

/**
 * @Footer_main_menu widget Class
 *
 *
 */

if ( ! class_exists( 'footer_main_menu' ) ) { 
	class footer_main_menu extends WP_Widget {
	  
		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
 		/**
		 * @Footer_main_menu Module
		 *
		 *
		 */
		 function footer_main_menu(){
				$widget_ops = array('classname' => 'footer_main_menu_widget', 'description' => 'Main menu into footer.' );
				$this->WP_Widget('footer_main_menu', 'CS : Footer Main Menu', $widget_ops);
		  }
	  	  
		/**
		 * @Footer_main_menu html Form
		 *
		 *
		 */
		 function form($instance) {
				$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
				$title = $instance['title'];
				?>
				<p>
	              <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"> Title:
	                <input class="upcoming" id="<?php echo esc_attr($this->get_field_id('title')); ?>" size='40' name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	              </label>
	            </p>
				<?php
				
	    }
		
		/**
		 * @Footer_main_menu Update Form Data
		 *
		 *
		 */
		 function update($new_instance, $old_instance) {
	
			$instance = $old_instance;
			$instance['title'] = $new_instance['title'];		
	
			return $instance;
			
		  }
		
		
		/**
		 * @Footer_main_menu Widget Display
		 *
		 *
		 */
		 function widget($args, $instance) {
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			
			echo cs_allow_special_char($before_widget);	

			if (!empty($title) && $title <> ' '){
				echo cs_allow_special_char($before_title);
				echo cs_allow_special_char($title);
				echo cs_allow_special_char($after_title);
			}
			
			cs_main_navigation('header2-nav','');
			?><?php echo cs_allow_special_char($after_widget);
	
			}
		}	
}
add_action( 'widgets_init', create_function('', 'return register_widget("footer_main_menu");') );
