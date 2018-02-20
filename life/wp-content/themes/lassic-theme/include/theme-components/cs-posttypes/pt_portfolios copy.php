<?php
	require_once 'pt_functions.php';

	//adding columns start
    add_filter('manage_csprojects_posts_columns', 'csprojects_columns_add');
	function csprojects_columns_add($columns) {
		$columns['category'] = 'Category';
		$columns['author'] = 'Author';
		return $columns;
	}
    add_action('manage_csprojects_posts_custom_column', 'csprojects_columns');
	function csprojects_columns($name) {
		global $post;
		switch ($name) {
			case 'category':
				$categories = get_the_terms( $post->ID, 'project-category' );
					if($categories <> ""){
						$couter_comma = 0;
						foreach ( $categories as $category ) {
							echo cs_allow_special_char($category->name);
							$couter_comma++;
							if ( $couter_comma < count($categories) ) {
								echo ", ";
							}
						}
					}
				break;
			case 'author':
				echo get_the_author();
				break;
		}
	}
	//adding columns end
	if ( ! function_exists( 'cs_csprojects_register' ) ) {
		function cs_csprojects_register() {
			$labels = array(
				'name' => 'Projects',
				'all_items' => __('Projects','lassic'),
				'add_new_item' => 'Add New Project',
				'edit_item' => 'Edit Project',
				'new_item' => 'New Project Item',
				'add_new' => 'Add New Project',
				'view_item' => 'View Project Item',
				'search_items' => 'Search Project',
				'not_found' =>  'Nothing found',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
			);
			$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'query_var' => true,
				'menu_icon' => 'dashicons-book',
				'rewrite' => true,
				'capability_type' => 'post',
				'has_archive' => false,
				'map_meta_cap' => true,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array('title','editor','thumbnail','comments')
			); 
			register_post_type( 'project' , $args );
		}
		add_action('init', 'cs_csprojects_register');
	}
		// adding cat start
		  $labels = array(
			'name' => 'Project Categories',
			'search_items' => 'Search Project Categories',
			'edit_item' => 'Edit Project Category',
			'update_item' => 'Update Project Category',
			'add_new_item' => 'Add New Category',
			'menu_name' => 'Categories',
		  ); 	
		  register_taxonomy('project-category',array('project'), array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'project-category' ),
		  ));
		// adding cat end
		// adding tag start
		  $labels = array(
			'name' => 'Project Tags',
			'singular_name' => 'project-tag',
			'search_items' => 'Search Tags',
			'popular_items' => 'Popular Tags',
			'all_items' => 'All Tags',
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => 'Edit Tag', 
			'update_item' => 'Update Tag',
			'add_new_item' => 'Add New Tag',
			'new_item_name' => 'New Tag Name',
			'separate_items_with_commas' => 'Separate tags with commas',
			'add_or_remove_items' => 'Add or remove tags',
			'choose_from_most_used' => 'Choose from the most used tags',
			'menu_name' => 'Tags',
		  ); 
		  register_taxonomy('project-tag','project',array(
			'hierarchical' => false,
			'labels' => $labels,
			'show_ui' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array( 'slug' => 'project-tag' ),
		  ));
		// adding tag end

	// adding Project meta info start
	add_action( 'add_meta_boxes', 'cs_meta_project_add' );
	function cs_meta_project_add(){
		add_meta_box( 'cs_meta_project', 'Project Options', 'cs_meta_project', 'project', 'normal', 'high' );  
	}
	function cs_meta_project( $post ) {
		global $post, $cs_xmlObject;
		$cs_theme_options = get_option('cs_theme_options');
		$cs_builtin_seo_fields = $cs_theme_options['cs_builtin_seo_fields'];
		$cs_header_position = $cs_theme_options['cs_header_position'];
		
		$cs_area		= get_post_meta($post->ID, "cs_area", true );
		$cs_investor	= get_post_meta($post->ID, "cs_investor", true );
		$cs_value		= get_post_meta($post->ID, "cs_value", true );
		$cs_construction_date	= get_post_meta($post->ID, "cs_construction_date", true );
									
		$cs_project = get_post_meta($post->ID, "csprojects", true);
		if ( $cs_project <> "" ) {
			$cs_xmlObject = new SimpleXMLElement($cs_project);
			$project_detail_view  = $cs_xmlObject->project_detail_view;
			$project_thumbnail_view  = $cs_xmlObject->project_thumbnail_view;
			$cs_project_btn_title  = $cs_xmlObject->cs_project_btn_title;
			$cs_project_btn_url  = $cs_xmlObject->cs_project_btn_url;
			$cs_project_btn_color  = $cs_xmlObject->cs_project_btn_color;
			$cs_project_shortcode  = $cs_xmlObject->cs_project_shortcode;
			$cs_project_members  = $cs_xmlObject->cs_project_members;
		    $cs_project_members = explode(",", $cs_project_members);
			
		} else {
			$project_detail_view  = '';
			$project_thumbnail_view  = '';
			$cs_project_btn_title  = '';
			$cs_project_btn_url  = '';
			$cs_project_btn_color  = '';
			$cs_project_shortcode  = '';
			$cs_project_members = array();
			
			if(!isset($cs_xmlObject))
				$cs_xmlObject = new stdClass();
		}
		
		cs_enqueue_timepicker_script();
		?>		
		<div class="page-wrap page-opts left" style="overflow:hidden; position:relative; height: 1432px;">
			<div class="option-sec" style="margin-bottom:0;">
				<div class="opt-conts">
					<div class="elementhidden">
						<div class="tabs vertical">
							<nav class="admin-navigtion">
								<ul id="myTab" class="nav nav-tabs">
									<li class="active"><a href="#tab-general-settings" data-toggle="tab"><i class="fa fa-cog"></i><?php _e('General','lassic'); ?></a></li>
									<li><a href="#tab-subheader-options" data-toggle="tab"><i class="fa fa-indent"></i><?php _e('Sub Header','lassic'); ?></a></li>
									<?php if($cs_header_position == 'absolute'){?>
                 						<li><a href="#tab-header-position-settings" data-toggle="tab"><i class="fa fa-forward"></i><?php _e('Header Absolute','lassic'); ?></a></li>
                 					<?php }?>
									<?php if($cs_builtin_seo_fields == 'on'){?>
									<li><a href="#tab-seo-advance-settings" data-toggle="tab"><i class="fa fa-dribbble"></i><?php _e('SEO Options','lassic'); ?></a></li>
									<?php }?>
                                    <li><a href="#tab-location-settings" data-toggle="tab"><i class="fa fa-globe"></i><?php _e('Location','lassic'); ?></a></li>
                                    <li><a data-toggle="tab" href="#tab-projects-settings-cs-projects"><i class="fa fa-briefcase"></i><?php _e('Project Options','lassic'); ?></a></li>
                                     <li><a href="#tab-post-options" data-toggle="tab"><i class="icon-list-alt"></i> Project Gallery </a></li>
 							  </ul>
						  </nav>
							<div class="tab-content">
							<div id="tab-subheader-options" class="tab-pane fade">
								<?php cs_subheader_element();?>
							</div>
							<div id="tab-general-settings" class="tab-pane fade active in">
								<?php 
									cs_general_settings_element();
									cs_sidebar_layout_options();
								?>
							</div>
 							<?php if($cs_builtin_seo_fields == 'on'){?>
							<div id="tab-seo-advance-settings" class="tab-pane fade">
								<?php cs_seo_settitngs_element();?>
							</div>
							<?php }
                            if($cs_header_position == 'absolute'){?>
                            <div id="tab-header-position-settings" class="tab-pane fade">
                                 <?php cs_header_postition_element();?>
                            </div>
                            <?php } ?>
							<div id="tab-location-settings" class="tab-pane fade">
                            	<?php cs_location_fields(); ?>
                            </div>
                            <div id="tab-projects-settings-cs-projects" class="tab-pane fade">
								<script>
                                jQuery(function(){
									jQuery('#cs_construction_date').datetimepicker({
										format:'d/m/Y',
										timepicker:false
									});
                                });
                                </script>
                                <div class="clear"></div>
                                <ul class="form-elements">
                                  <li class="to-label">
                                    <label><?php _e('Project Detail View', 'lassic'); ?></label>
                                  </li>
                                  <li class="to-field short-field">
                                    <div class="input-sec">
                                        <div class="select-style">
                                            <select name="project_detail_view" class="dropdown" onchange="javascript:cs_showhide_option(this.value)">
                                                <option <?php if($project_detail_view=="style_1")echo "selected";?> value="style_1" >Style 1</option>
                                                <option <?php if($project_detail_view=="style_2")echo "selected";?> value="style_2" >Style 2</option>
                                                <option <?php if($project_detail_view=="style_3")echo "selected";?> value="style_3">Style 3</option>
                                                <option <?php if($project_detail_view=="style_4")echo "selected";?> value="style_4" >Style 4</option>
                                                <option <?php if($project_detail_view=="style_5")echo "selected";?> value="style_5" >Style 5</option>
                                            </select>
                                        </div>
                                    </div>
                                  </li>
                                </ul>
                                <ul class="form-elements">
                                  <li class="to-label">
                                  	<label><?php _e('Project Thumbnail View', 'lassic'); ?></label>
                                  </li>
                                  <li class="to-field short-field">
                                    <div class="input-sec">
                                        <div class="select-style">
                                            <select name="project_thumbnail_view" class="dropdown">
                                                <option <?php if($project_thumbnail_view=="single_image")echo "selected";?> value="single_image" >Single Image</option>
                                                <option <?php if($project_thumbnail_view=="gallery")echo "selected";?> value="gallery" >Gallery</option>
                                                <option <?php if($project_thumbnail_view=="slider")echo "selected";?> value="slider">Slider</option>
                                            </select>
                                        </div>
                                    </div>
                                  </li>
                                </ul>
                                <ul class="form-elements bcevent_title">
                                  <li class="to-label">
                                    <label><?php _e('Button', 'lassic'); ?></label>
                                  </li>
                                  <li class="to-field">
                                    <div class="input-sec">
                                      <input type="text" id="cs_project_btn_title" name="cs_project_btn_title" value="<?php if(isset($cs_xmlObject->cs_project_btn_title)){echo cs_allow_special_char( $cs_xmlObject->cs_project_btn_title );}?>" />
                                      <label><?php _e('Title', 'lassic'); ?></label>
                                    </div>
                                    <div class="input-sec">
                                      <input type="text" id="cs_project_btn_url" name="cs_project_btn_url" value="<?php if(isset($cs_xmlObject->cs_project_btn_url)){echo cs_allow_special_char( $cs_xmlObject->cs_project_btn_url );}?>" />
                                      <label><?php _e('URL', 'lassic'); ?></label>
                                    </div>
                                    <div class="input-sec">
                                      <input type="text" class="bg_color" id="cs_project_btn_color" name="cs_project_btn_color" value="<?php if(isset($cs_xmlObject->cs_project_btn_color)){echo cs_allow_special_char( $cs_xmlObject->cs_project_btn_color );}?>" />
                                      <label><?php _e('Color', 'lassic'); ?></label>
                                    </div>
                                  </li>
                                </ul>
                                <ul class="form-elements">
                                  <li class="to-label">
                                    <label><?php _e('Construction Date', 'lassic'); ?></label>
                                  </li>
                                  <li class="to-field short-field">
                                    <input type="text" id="cs_construction_date" name="cs_construction_date" value="<?php if(isset($cs_construction_date ) && $cs_construction_date  <> '') echo cs_allow_special_char($cs_construction_date )?>" />
                                  </li>
                                </ul>
                                <ul class="form-elements">
                                  <li class="to-label">
                                    <label><?php _e('Surface Aera', 'lassic'); ?></label>
                                  </li>
                                  <li class="to-field short-field">
                                    <input type="text" id="cs_area" name="cs_area" value="<?php if(isset($cs_area ) && $cs_area  <> '') echo cs_allow_special_char($cs_area )?>" />
                                  </li>
                                </ul>
                                <ul class="form-elements">
                                  <li class="to-label">
                                    <label><?php _e('Contacting Investor', 'lassic'); ?></label>
                                  </li>
                                  <li class="to-field short-field">
                                    <input type="text" id="cs_investor" name="cs_investor" value="<?php if(isset($cs_investor ) && $cs_investor  <> '') echo cs_allow_special_char($cs_investor )?>" />
                                  </li>
                                </ul>
                                <ul class="form-elements">
                                  <li class="to-label">
                                    <label><?php _e('Value', 'lassic'); ?></label>
                                  </li>
                                  <li class="to-field short-field">
                                    <input type="text" id="cs_value" name="cs_value" value="<?php if(isset($cs_value ) && $cs_value  <> '') echo cs_allow_special_char($cs_value )?>" />
                                  </li>
                                </ul>
                                <div id="cs-showhide-option" style="display:<?php if(isset($project_detail_view) and $project_detail_view=="Single Image")echo 'inline"';else echo 'none';?>">
                               		<ul class="form-elements">
                                      <li class="to-label">
                                        <label>Team Members</label>
                                      </li>
                                      <li class="to-field">
                                        <select name="cs_project_members[]" multiple="multiple" style="min-height:100px;">
                                          <?php
                                             $custom_query = new WP_Query('post_type=member&posts_per_page=-1');
                                              while ( $custom_query->have_posts()) : $custom_query->the_post();
                                                    $selected = in_array(get_the_ID(),$cs_project_members)?'selected':'';
                                                    echo '<option '.$selected.' value="'.get_the_ID().'">'.get_the_title().'</option>';
                                              endwhile;
                                         ?>
                                        </select>
                                        <p>Only work for style 4</p>
                                      </li>
                                	</ul>
                               		<ul class="form-elements">
                                        <li class="to-label">
                                          <label><?php _e('Shortcode', 'lassic'); ?></label>
                                        </li>
                                      	<li class="to-field">
                                    		<textarea rows="20" cols="40"  name="cs_project_shortcode"><?php echo esc_textarea($cs_project_shortcode);?></textarea>
											<p>Only work for style 4</p>
                                  		</li>
                                	</ul>
                                </div>
                            </div>
                             <div id="tab-post-options" class="tab-pane fade">
								<?php if ( function_exists( 'cs_project_gallery' ) ) {cs_project_gallery();}?>
                            </div>
                            
 						  </div>
						</div>
					  </div>
					</div>
				<input type="hidden" name="csproject_meta_form" value="1" />
			</div>
		</div>
		<div class="clear"></div>
	<?php 
    }
 	// Course Meta option save
	if ( isset($_POST['csproject_meta_form']) and $_POST['csproject_meta_form'] == 1 ) {
		add_action( 'save_post', 'cs_meta_project_save' );  
		function cs_meta_project_save( $post_id ){  
			$sxe = new SimpleXMLElement("<project></project>");
			if (empty($_POST['cs_construction_date'])){ $_POST['cs_construction_date'] = '';}
			if (empty($_POST['cs_area'])){ $_POST['cs_area'] = '';}
			if (empty($_POST['cs_investor'])){ $_POST['cs_investor'] = '';}
			if (empty($_POST['cs_value'])){ $_POST['cs_value'] = '';}
			if (empty($_POST['project_detail_view'])){ $_POST['project_detail_view'] = '';}
			if (empty($_POST['project_thumbnail_view'])){ $_POST['project_thumbnail_view'] = '';}
			if (empty($_POST['cs_project_btn_title'])){ $_POST['cs_project_btn_title'] = '';}
			if (empty($_POST['cs_project_btn_url'])){ $_POST['cs_project_btn_url'] = '';}
			if (empty($_POST['cs_project_btn_color'])){ $_POST['cs_project_btn_color'] = '';}
			if (empty($_POST['cs_project_shortcode'])){ $_POST['cs_project_shortcode'] = '';}
			
			if (empty($_POST['cs_project_members'])){
				 $cs_project_members = "";
			} else {
				 $cs_project_members = implode(",", $_POST['cs_project_members']);
			}
													
			// Location Map
			if (empty($_POST['dynamic_post_location_latitude'])){ $_POST['dynamic_post_location_latitude'] = '';}
			if (empty($_POST['dynamic_post_location_longitude'])){ $_POST['dynamic_post_location_longitude'] = '';}
			if (empty($_POST['dynamic_post_location_zoom'])){ $_POST['dynamic_post_location_zoom'] = '';}
			if (empty($_POST['dynamic_post_location_address'])){ $_POST['dynamic_post_location_address'] = '';}
			if (empty($_POST['loc_city'])){ $_POST['loc_city'] = '';}
			if (empty($_POST['loc_postcode'])){ $_POST['loc_postcode'] = '';}
			if (empty($_POST['loc_region'])){ $_POST['loc_region'] = '';}
			if (empty($_POST['loc_country'])){ $_POST['loc_country'] = '';}
			if (empty($_POST['event_map_switch'])){ $_POST['event_map_switch'] = '';}
			if (empty($_POST['event_map_heading'])){ $_POST['event_map_heading'] = '';}
			
			$sxe->addChild('project_detail_view', $_POST['project_detail_view']);
			$sxe->addChild('project_thumbnail_view', $_POST['project_thumbnail_view']);
			$sxe->addChild('cs_project_btn_title', $_POST['cs_project_btn_title']);
			$sxe->addChild('cs_project_btn_url', $_POST['cs_project_btn_url']);
			$sxe->addChild('cs_project_btn_color', $_POST['cs_project_btn_color']);
			$sxe->addChild('cs_project_shortcode', $_POST['cs_project_shortcode']);
			$sxe->addChild('cs_project_members', $cs_project_members);
			// Location Map
			$sxe->addChild('dynamic_post_location_latitude', $_POST['dynamic_post_location_latitude']);
			$sxe->addChild('dynamic_post_location_longitude', $_POST['dynamic_post_location_longitude']);
			$sxe->addChild('dynamic_post_location_zoom', $_POST['dynamic_post_location_zoom']);
			$sxe->addChild('dynamic_post_location_address', $_POST['dynamic_post_location_address']);
			$sxe->addChild('loc_city', $_POST['loc_city']);
			$sxe->addChild('loc_postcode', $_POST['loc_postcode']);
			$sxe->addChild('loc_region', $_POST['loc_region']);
			$sxe->addChild('loc_country', $_POST['loc_country']);
			$sxe->addChild('event_map_switch', $_POST['event_map_switch']);
			$sxe->addChild('event_map_heading', $_POST['event_map_heading']);
			
			if ( isset($_POST['gallery_slider_meta_form']) and $_POST['gallery_slider_meta_form'] == 1 ) {
				$cs_counter = 0;
				if ( isset($_POST['cs_slider_path']) ) {
					foreach ( $_POST['cs_slider_path'] as $count ) {
						if (empty($_POST['cs_slider_path'][$cs_counter])){ $_POST['cs_slider_path'][$cs_counter] = "";}
						if (empty($_POST['cs_slider_title'][$cs_counter])){ $_POST['cs_slider_title'][$cs_counter] = "";}
						if (empty($_POST['slider_use_image_as'][$cs_counter])){ $_POST['slider_use_image_as'][$cs_counter] = "";}
						if (empty($_POST['slider_video_code'][$cs_counter])){ $_POST['slider_video_code'][$cs_counter] = "";}
						if (empty($_POST['cs_slider_link'][$cs_counter])){ $_POST['cs_slider_link'][$cs_counter] = "";}
						$galleryInside = $sxe->addChild('gallery_slider');
						
						$galleryInside->addChild('cs_slider_path', $_POST['cs_slider_path'][$cs_counter] );
						$galleryInside->addChild('cs_slider_title', htmlspecialchars($_POST['cs_slider_title'][$cs_counter]) );
						$galleryInside->addChild('slider_use_image_as', $_POST['slider_use_image_as'][$cs_counter] );
						$galleryInside->addChild('slider_video_code', htmlspecialchars($_POST['slider_video_code'][$cs_counter]) );
						$galleryInside->addChild('cs_slider_link', htmlspecialchars($_POST['cs_slider_link'][$cs_counter]) );
						$cs_counter++;
					}
				}
			}
						
			
			$sxe = cs_page_options_save_xml($sxe);
			
			update_post_meta( $post_id, 'csprojects', $sxe->asXML() );
			update_post_meta( $post_id, 'cs_construction_date', $_POST['cs_construction_date'] );
			update_post_meta( $post_id, 'cs_area', $_POST['cs_area'] );
			update_post_meta( $post_id, 'cs_investor', $_POST['cs_investor'] );
			update_post_meta( $post_id, 'cs_value', $_POST['cs_value'] );
			
		}
	}
		// adding Project meta info end

 ?>