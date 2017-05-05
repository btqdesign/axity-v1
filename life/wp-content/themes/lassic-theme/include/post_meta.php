<?php
add_action( 'add_meta_boxes', 'cs_meta_post_add' );
function cs_meta_post_add()
{  
	add_meta_box( 'cs_meta_post', 'Post Options', 'cs_meta_post', 'post', 'normal', 'high' );  
}
function cs_meta_post( $post ) {
	$post_xml = get_post_meta($post->ID, "post", true);
	global $cs_xmlObject;
	$cs_theme_options=get_option('cs_theme_options');
	$cs_builtin_seo_fields =$cs_theme_options['cs_builtin_seo_fields'];
	if ( $post_xml <> "" ) {
		$cs_xmlObject = new SimpleXMLElement($post_xml);
			$sub_title = $cs_xmlObject->sub_title;
			$post_thumb_view = $cs_xmlObject->post_thumb_view;
			$post_thumb_audio = $cs_xmlObject->post_thumb_audio;
			$post_thumb_video = $cs_xmlObject->post_thumb_video;
			$post_thumb_slider = $cs_xmlObject->post_thumb_slider;
			$post_thumb_slider_type = $cs_xmlObject->post_thumb_slider_type;						
			$inside_post_thumb_view = $cs_xmlObject->inside_post_thumb_view;
			$inside_post_thumb_audio = $cs_xmlObject->inside_post_thumb_audio;
			$inside_post_thumb_video = $cs_xmlObject->inside_post_thumb_video;
			$inside_post_thumb_slider = $cs_xmlObject->inside_post_thumb_slider;
			$inside_post_thumb_slider_type = $cs_xmlObject->inside_post_thumb_slider_type;
	} else {
		$sub_title = '';
		$post_thumb_view = '';
		$post_thumb_audio = '';
		$post_thumb_video = '';
		$post_thumb_slider = '';
		$post_thumb_slider_type = '';
		$inside_post_thumb_view = '';
		$inside_post_thumb_audio = '';
		$inside_post_thumb_video = '';
		$inside_post_thumb_slider = '';
		$inside_post_thumb_slider_type = '';

	}
?>
    <div class="page-wrap page-opts left" style="overflow:hidden; position:relative; height: 1432px;">
        <div class="option-sec" style="margin-bottom:0;">
            <div class="opt-conts">
                <div class="elementhidden">
                    <div class="tabs vertical">
                        <nav class="admin-navigtion">
                            <ul id="myTab" class="nav nav-tabs">
                                <li class="active"><a href="#tab-general-settings" data-toggle="tab"><i class="icon-toggle-right"></i>General Settings</a></li>
                                <li><a href="#tab-subheader-options" data-toggle="tab"><i class="icon-list-alt"></i> Sub Header Options </a></li>
                                <?php if($cs_builtin_seo_fields == 'on'){?>
                                <li><a href="#tab-seo-advance-settings" data-toggle="tab"><i class="icon-dribbble"></i> SEO Options</a></li>
                                <?php }?>
                                <li><a href="#tab-post-options" data-toggle="tab"><i class="icon-list-alt"></i> Post Settings </a></li>
                                
                          </ul>
                      </nav>
                        <div class="tab-content">
                         <div id="tab-general-settings" class="tab-pane fade active in">
                            <?php cs_general_settings_element(); ?>
                             <?php cs_sidebar_layout_options();?>
                        </div>
                        <div id="tab-subheader-options" class="tab-pane fade">
                            <?php cs_subheader_element();?>
                        </div>
                        <div id="tab-post-options" class="tab-pane fade">
                        	<?php if ( function_exists( 'cs_blog_post_general_options' ) ) {cs_blog_post_general_options();}?>
                        </div>
                       
                       <?php if($cs_builtin_seo_fields == 'on'){?>
                            <div id="tab-seo-advance-settings" class="tab-pane fade">
                                <div class="theme-help">
                                    <h4 style="padding-bottom:0px;">SEO Options</h4>
                                    <div class="clear"></div>
                                </div>
                                <?php cs_seo_settitngs_element();?>
                            </div>
                        <?php }?>
                      </div>
                    </div>
                  </div>
                </div>
           <input type="hidden" name="post_meta_form" value="1" />
        </div>
    </div>
    <div class="clear"></div>
<?php
}
	if ( isset($_POST['post_meta_form']) and $_POST['post_meta_form'] == 1 ) {
		add_action( 'save_post', 'cs_meta_post_save' );
		function cs_meta_post_save( $post_id ) {
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
				if ( empty($_POST['post_social_sharing']) ) $_POST['post_social_sharing'] = "";
				if (empty($_POST["post_thumb_view"])){ $_POST["post_thumb_view"] = "";}
				if (empty($_POST["post_thumb_audio"])){ $_POST["post_thumb_audio"] = "";}
				if (empty($_POST["post_thumb_video"])){ $_POST["post_thumb_video"] = "";}
				if (empty($_POST["post_thumb_slider"])){ $_POST["post_thumb_slider"] = "";}
				if (empty($_POST["post_thumb_slider_type"])){ $_POST["post_thumb_slider_type"] = "";}
				if (empty($_POST["inside_post_thumb_view"])){ $_POST["inside_post_thumb_view"] = "";}
				if (empty($_POST["inside_post_thumb_audio"])){ $_POST["inside_post_thumb_audio"] = "";}
				if (empty($_POST["inside_post_thumb_video"])){ $_POST["inside_post_thumb_video"] = "";}
				if (empty($_POST["inside_post_thumb_slider"])){ $_POST["inside_post_thumb_slider"] = "";}
				if (empty($_POST["inside_post_thumb_slider_type"])){ $_POST["inside_post_thumb_slider_type"] = "";}
				if (empty($_POST["cs_bg_image"])){ $_POST["cs_bg_image"] = "";}
					$sxe = new SimpleXMLElement("<cs_meta_post></cs_meta_post>");
						$sxe->addChild('post_thumb_view', $_POST['post_thumb_view'] );
						$sxe->addChild('post_thumb_audio', $_POST['post_thumb_audio'] );
						$sxe->addChild('post_thumb_video', $_POST['post_thumb_video'] );
						$sxe->addChild('post_thumb_slider', $_POST['post_thumb_slider'] );
						$sxe->addChild('post_thumb_slider_type', $_POST['post_thumb_slider_type'] );
						$sxe->addChild('inside_post_thumb_view', $_POST['inside_post_thumb_view'] );
						$sxe->addChild('inside_post_thumb_audio', $_POST['inside_post_thumb_audio'] );
						$sxe->addChild('inside_post_thumb_video', $_POST['inside_post_thumb_video'] );
						$sxe->addChild('inside_post_thumb_slider', $_POST['inside_post_thumb_slider'] );
						$sxe->addChild('cs_bg_image', $_POST['cs_bg_image'] );
						if ( isset($_POST['gallery_meta_form']) and $_POST['gallery_meta_form'] == 1 ) {
						$cs_counter = 0;
							if ( isset($_POST['path']) ) {
								foreach ( $_POST['path'] as $count ) {
										if (empty($_POST['path'][$cs_counter])){ $_POST['path'][$cs_counter] = "";}
										if (empty($_POST['title'][$cs_counter])){ $_POST['title'][$cs_counter] = "";}
										if (empty($_POST['use_image_as'][$cs_counter])){ $_POST['use_image_as'][$cs_counter] = "";}
										if (empty($_POST['video_code'][$cs_counter])){ $_POST['video_code'][$cs_counter] = "";}
										if (empty($_POST['link_url'][$cs_counter])){ $_POST['link_url'][$cs_counter] = "";}
										$gallery = $sxe->addChild('gallery');
										$gallery->addChild('path', $_POST['path'][$cs_counter] );
										$gallery->addChild('title', htmlspecialchars($_POST['title'][$cs_counter]) );
										$gallery->addChild('use_image_as', $_POST['use_image_as'][$cs_counter] );
										$gallery->addChild('video_code', htmlspecialchars($_POST['video_code'][$cs_counter]) );
										$gallery->addChild('link_url', htmlspecialchars($_POST['link_url'][$cs_counter]) );
										$cs_counter++;
								}
							}
						}
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
						update_post_meta( $post_id, 'post', $sxe->asXML() );
			}
		}
 			if ( ! function_exists( 'cs_blog_post_general_options' ) ) {
					function cs_blog_post_general_options() {
						global $cs_xmlObject;
						if ( empty($cs_xmlObject->post_thumb_view) ) $post_thumb_view = ""; else $post_thumb_view = $cs_xmlObject->post_thumb_view;
						if ( empty($cs_xmlObject->post_thumb_slider) ) $post_thumb_slider = ""; else $post_thumb_slider = $cs_xmlObject->post_thumb_slider;
						if ( empty($cs_xmlObject->post_thumb_slider_type) ) $post_thumb_slider_type = ""; else $post_thumb_slider_type = $cs_xmlObject->post_thumb_slider_type;
						if ( empty($cs_xmlObject->inside_post_thumb_view) ) $inside_post_thumb_view = ""; else $inside_post_thumb_view = $cs_xmlObject->inside_post_thumb_view;
						if ( empty($cs_xmlObject->inside_post_thumb_audio) ) $inside_post_thumb_audio = ""; else $inside_post_thumb_audio = $cs_xmlObject->inside_post_thumb_audio;
						if ( empty($cs_xmlObject->inside_post_thumb_video) ) $inside_post_thumb_video = ""; else $inside_post_thumb_video = $cs_xmlObject->inside_post_thumb_video;
						if ( empty($cs_xmlObject->inside_post_thumb_slider) ) $inside_post_thumb_slider = ""; else $inside_post_thumb_slider = $cs_xmlObject->inside_post_thumb_slider;
						if ( empty($cs_xmlObject->inside_post_thumb_slider_type) ) $inside_post_thumb_slider_type = ""; else $inside_post_thumb_slider_type = $cs_xmlObject->inside_post_thumb_slider_type;
						$cs_bg_image = isset($cs_xmlObject->cs_bg_image) ? $cs_xmlObject->cs_bg_image : $cs_xmlObject->cs_bg_image = '';
 						?>
                        <ul class="form-elements">
                            <li class="to-label">
                            	<label>Background Image</label>
                            </li>
                            <li class="to-field">
                            	<input id="cs_bg_image" name="cs_bg_image" type="hidden" class="" value="<?php echo cs_allow_special_char($cs_bg_image);?>"/>
                            	<label class="browse-icon"><input name="cs_bg_image"  type="button" class="uploadMedia left" value="Browse"/></label>
                            </li>
                        </ul>
                        <div class="page-wrap">
                            <div class="gal-active">
                            <div class="dragareamain" style="padding-bottom:0px;">
                                <ul id="gal-sortable">
                                    <li class="ui-state-default" id="">
                                        <div class="thumb-secs"> <img src="<?php echo esc_url($cs_bg_image);?>"  id="cs_bg_image_img" width="100" height="150" alt="" />
                                        <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_bg_image')" class="delete"></a> </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            </div>
                        </div>
						<ul class="form-elements noborder">
						<li class="to-label"><label>Thumbnail View</label></li>
						<li class="to-field">
							<div class="input-sec">
								<div class="select-style">
									<select name="post_thumb_view" class="dropdown" onchange="javascript:new_toggle(this.value)">
										<option <?php if($post_thumb_view=="Single Image")echo "selected";?> >Single Image</option>
										<option <?php if($post_thumb_view=="Slider")echo "selected";?> >Slider</option>
									</select>
								</div>
							</div>
							<div class="left-info">
								<p id="post_thumb_image" style="display:<?php if($post_thumb_view=="Single Image" or $post_thumb_view == "")echo 'inline"';else echo 'none';?>">Use Featured Image as Thumbnail</p>
							</div>
						</li>
					</ul>
						<div class="noborder post_slider" id="post_thumb_slider" style="display:<?php if($post_thumb_view=="Slider")echo 'inline';else echo 'none';?>" >
						   <?php echo cs_post_attachments('gallery_meta_form');?>
						</div>
						<ul class="form-elements noborder">
							<li class="to-label"><label>Inside Post Thumbnail View</label></li>
							<li class="to-field">
								<div class="input-sec">
									<div class="select-style">
										<select name="inside_post_thumb_view" class="dropdown" onchange="javascript:new_toggle_inside_post(this.value)">
											<option <?php if($inside_post_thumb_view=="Single Image")echo "selected";?> >Single Image</option>
											<option <?php if($inside_post_thumb_view=="Audio")echo "selected";?> >Audio</option>
											<option <?php if($inside_post_thumb_view=="Video")echo "selected";?> value="Video">Video/Soundcloud</option>
											<option <?php if($inside_post_thumb_view=="Slider")echo "selected";?> >Slider</option>
										</select>
									</div>
								</div>
								<div class="left-info">
									<p id="inside_post_thumb_image" style="display:<?php if($inside_post_thumb_view=="Single Image" or $inside_post_thumb_view=="")echo 'inline"';else echo 'none';?>">Use Featured Image as Thumbnail</p>
								</div>
							</li>
								<ul class="form-elements" id="inside_post_thumb_audio" style="display:<?php if($inside_post_thumb_view=="Audio")echo 'inline"';else echo 'none';?>" >
									<li class="to-label"><label>Audio URL</label></li>
									<li class="to-field">
										<div class="input-sec">
											<input type="text" id="inside_post_thumb_audio2" name="inside_post_thumb_audio" value="<?php echo htmlspecialchars($inside_post_thumb_audio)?>" class="txtfield" />
											<label class="cs-browse">
												<input type="button" id="inside_post_thumb_audio2" name="inside_post_thumb_audio2" class="uploadfile left" value="Browse"/>
											</label>
										</div>
										<div class="left-info">
											<p>Enter Specific Audio URL (Youtube, Vimeo and all otheres wordpress supported)</p>
										</div>
									</li>
								</ul>
								<ul class="form-elements" id="inside_post_thumb_video" style="display:<?php if($inside_post_thumb_view=="Video")echo 'inline"';else echo 'none';?>" >
									<li class="to-label"><label>Thumbnail Video URL</label></li>
									<li class="to-field">
										<div class="input-sec">
											<input id="inside_post_thumb_video2" name="inside_post_thumb_video" value="<?php echo cs_allow_special_char($inside_post_thumb_video)?>" type="text" class="small" />
											<label class="cs-browse">
												<input id="inside_post_thumb_video2" name="inside_post_thumb_video2" type="button" class="uploadfile left" value="Browse"/>
											</label>
										</div>
										<div class="left-info">
											<p>Enter Specific Video URL (Youtube, Vimeo and all otheres wordpress supported) OR you can select it from your media library</p>
										</div>
									</li>
								</ul>
						</ul>
						<div class="" id="inside_post_thumb_slider" style="display:<?php if($inside_post_thumb_view=="Slider") echo 'inline';else echo 'none';?>" >
							<?php echo cs_post_attachments('gallery_slider_meta_form');?>
						</div>
		<?php
		}
			}
 