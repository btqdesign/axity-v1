<?php
	require_once 'pt_functions.php';

	//adding columns start
    add_filter('manage_member_posts_columns', 'member_columns_add');
	function member_columns_add($columns) {
		$columns['author'] = 'Author';
		return $columns;
	}
    add_action('manage_member_posts_custom_column', 'member_columns');
	function member_columns($name) {
		global $post;
		switch ($name) {
			case 'author':
				echo get_the_author();
				break;
		}
	}
	//adding columns end
	if ( ! function_exists( 'cs_member_register' ) ) {
		function cs_member_register() {
			$labels = array(
				'name' => 'Teams',
				'all_items' => __('Teams','lassic'),
				'add_new_item' => 'Add New Team',
				'edit_item' => 'Edit Team',
				'new_item' => 'New Team Item',
				'add_new' => 'Add New Team',
				'view_item' => 'View Team Item',
				'search_items' => 'Search Team',
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
			register_post_type( 'member' , $args );
		}
		add_action('init', 'cs_member_register');
	}
	
	// adding cat start
	  $labels = array(
		'name' => 'Team Department',
		'search_items' => 'Search Team Department',
		'edit_item' => 'Edit Team Department',
		'update_item' => 'Update Team Department',
		'add_new_item' => 'Add New Department',
		'menu_name' => 'Department',
	  ); 	
	  register_taxonomy('member-category',array('member'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'member-category' ),
	  ));
		  
	// adding Team meta info start
	add_action( 'add_meta_boxes', 'cs_meta_member_add' );
	function cs_meta_member_add(){
		add_meta_box( 'cs_meta_member', 'Team Options', 'cs_meta_member', 'member', 'normal', 'high' );  
	}
	
	function cs_meta_member( $post ) {
		global $post, $cs_xmlObject;
		$cs_theme_options = get_option('cs_theme_options');
		$cs_builtin_seo_fields = $cs_theme_options['cs_builtin_seo_fields'];
		$cs_header_position = $cs_theme_options['cs_header_position'];
		$cs_member = get_post_meta($post->ID, "member", true);
		if ( $cs_member <> "" ) {
			$cs_xmlObject = new SimpleXMLElement($cs_member);
			$cs_member_faqs_title 		= $cs_xmlObject->cs_member_faqs_title;
			$cs_team_designation 		= $cs_xmlObject->cs_team_designation;
		} else {
			$cs_member_faqs_title 	= '';
			$cs_team_designation 	= '';
			if(!isset($cs_xmlObject))
				$cs_xmlObject = new stdClass();
		}
		?>	
		<div class="page-wrap page-opts left" style="overflow:hidden; position:relative; height: 1432px;">
			<div class="option-sec" style="margin-bottom:0;">
				<div class="opt-conts">
					<div class="elementhidden">
						<div class="tabs vertical">
							<nav class="admin-navigtion">
								<ul id="myTab" class="nav nav-tabs">
									
									<li class="active"><a href="#tab-subheader-options" data-toggle="tab"><i class="icon-indent"></i><?php _e('Sub Header','lassic'); ?></a></li>
									<?php if($cs_header_position == 'absolute'){?>
                 						<li><a href="#tab-header-position-settings" data-toggle="tab"><i class="icon-forward"></i><?php _e('Header Absolute','lassic'); ?></a></li>
                 					<?php }?>
									<?php if($cs_builtin_seo_fields == 'on'){?>
									<li><a href="#tab-seo-advance-settings" data-toggle="tab"><i class="icon-dribbble"></i><?php _e('SEO Options','lassic'); ?></a></li>
									<?php }?>
                                    <li><a data-toggle="tab" href="#tab-members-settings-cs-members"><i class="icon-users5"></i><?php _e('Social Media','lassic'); ?></a></li>
                                    <li><a data-toggle="tab" href="#tab-members-settings-cs-dynamic-fields"><i class="icon-shield2"></i><?php _e('Team Custom Fields','lassic'); ?></a></li>
                                  
 							  </ul>
						    </nav>
							<div class="tab-content">
							<div id="tab-subheader-options" class="tab-pane fade active in">
								<?php cs_subheader_element();?>
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
                            <div id="tab-members-settings-cs-members" class="tab-pane fade">
                                <div class="clear"></div>
								<?php cs_member_social_section(); ?>
                            </div>
                            <div id="tab-members-settings-cs-dynamic-fields" class="tab-pane fade">
                            	<ul class="form-elements">
                                  <li class="to-label">
                                    <label><?php _e('Designation', 'lassic'); ?></label>
                                  </li>
                                  <li class="to-field short-field">
                                    <input type="text" id="cs_team_designation" name="cs_team_designation" value="<?php if(isset($cs_team_designation) && $cs_team_designation <> '') echo cs_allow_special_char($cs_team_designation)?>" />
                                  </li>
                                </ul>
								<?php cs_member_dynamic_fields_section(); ?>
                            </div>
 						  </div>
						</div>
					  </div>
					</div>
				<input type="hidden" name="csmember_meta_form" value="1" />
			</div>
		</div>
		<div class="clear"></div>
	<?php 
    }
 	// Course Meta option save
	if ( isset($_POST['csmember_meta_form']) and $_POST['csmember_meta_form'] == 1 ) {
		add_action( 'save_post', 'cs_meta_member_save' );  
		function cs_meta_member_save( $post_id ){  
			$sxe = new SimpleXMLElement("<member></member>");

			if (empty($_POST['cs_member_faqs_title'])){ $_POST['cs_member_faqs_title'] = '';}
			if (empty($_POST['cs_team_designation'])){ $_POST['cs_team_designation'] = '';}
			
			$sxe->addChild('cs_team_designation', $_POST['cs_team_designation']);
			
			$dynamic_fields_counter = 0;
			if (isset($_POST['dynamic_post_dynamic_fields']) && $_POST['dynamic_post_dynamic_fields'] == '1' && isset($_POST['dynamic_fields_title_array']) && is_array($_POST['dynamic_fields_title_array'])) {
				foreach ( $_POST['dynamic_fields_title_array'] as $type ){
					$dynamic_fields_list = $sxe->addChild('dynamic_fields');
					$dynamic_fields_list->addChild('dynamic_fields_title', htmlspecialchars($_POST['dynamic_fields_title_array'][$dynamic_fields_counter]) );
					$dynamic_fields_list->addChild('dynamic_fields_description', htmlspecialchars($_POST['dynamic_fields_description_array'][$dynamic_fields_counter]) );
					$dynamic_fields_counter++;
				}
			}
			$social_counter = 0;
			if (isset($_POST['dynamic_post_social']) && $_POST['dynamic_post_social'] == '1' && isset($_POST['social_title_array']) && is_array($_POST['social_title_array'])) {
				foreach ( $_POST['social_title_array'] as $type ){
					$social_list = $sxe->addChild('social_media');
					$social_list->addChild('social_title', htmlspecialchars($_POST['social_title_array'][$social_counter]) );
					$social_list->addChild('social_icon', htmlspecialchars($_POST['cs_social_icon'][$social_counter]) );
					$social_list->addChild('target_url', htmlspecialchars($_POST['var_cp_url_array'][$social_counter]) );
					$social_counter++;
				}
			}
			
			$sxe = cs_page_options_save_xml($sxe);
			
			update_post_meta( $post_id, 'member', $sxe->asXML() );
	
		}
	}
		// adding Team meta info end

 ?>