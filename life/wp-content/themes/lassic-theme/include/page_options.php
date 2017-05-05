<?php 
// Page Elements
// page/post General Settings Function
if ( ! function_exists( 'cs_general_settings_element' ) ) {
	function cs_general_settings_element(){
		global $cs_xmlObject, $post;
		
		if(!isset($cs_xmlObject))
		$cs_xmlObject = new stdClass();
		if ( !isset($cs_xmlObject->post_social_sharing) ){ $cs_xmlObject->post_social_sharing = "on";}
		if ( !isset($cs_xmlObject->post_author_info_show) ){ $cs_xmlObject->post_author_info_show = "on";}
		if ( empty($cs_xmlObject->post_author_info_text) ) $post_author_info_text = "About Author"; else $post_author_info_text = $cs_xmlObject->post_author_info_text;
		if ( !isset($cs_xmlObject->post_tags_show) ){ $cs_xmlObject->post_tags_show = "on";}
		if ( !isset($cs_xmlObject->cs_related_post) ){ $cs_xmlObject->cs_related_post = "on";}
		if ( !isset($cs_xmlObject->post_pagination_show) ){ $cs_xmlObject->post_pagination_show = "on";}
		
		if ( empty($cs_xmlObject->post_social_sharing) ) $post_social_sharing = ""; else $post_social_sharing = $cs_xmlObject->post_social_sharing;
		if ( empty($cs_xmlObject->post_author_info_show) ) $post_author_info_show = ""; else $post_author_info_show = $cs_xmlObject->post_author_info_show;
		if ( empty($cs_xmlObject->post_tags_show) ) $post_tags_show = ""; else $post_tags_show = $cs_xmlObject->post_tags_show;
		if ( empty($cs_xmlObject->post_tags_show_text) ) $post_tags_show_text = "Tags"; else $post_tags_show_text = $cs_xmlObject->post_tags_show_text;
		if ( empty($cs_xmlObject->cs_related_post) ) $cs_related_post = ""; else $cs_related_post = $cs_xmlObject->cs_related_post;
		if ( empty($cs_xmlObject->cs_related_post_title) ) $cs_related_post_title = "Related Post"; else $cs_related_post_title = $cs_xmlObject->cs_related_post_title;
		if ( empty($cs_xmlObject->post_pagination_show) ) $post_pagination_show = ""; else $post_pagination_show = $cs_xmlObject->post_pagination_show;
		
		$cs_post_type = get_post_type($post->ID);
			
		$cs_sidebar_visibility = '';
		if( $cs_post_type == 'project' ){
			$cs_sidebar_visibility = ' style="display:none;"';
		}
		
		?>

<ul class="form-elements">
  <li class="to-label">
    <label>Social Sharing</label>
  </li>
  <li class="to-field has_input">
    <label class="pbwp-checkbox">
      <input type="hidden" name="post_social_sharing" value="" />
      <input type="checkbox" name="post_social_sharing" value="on" class="myClass" <?php if($post_social_sharing=='on')echo "checked"?> />
      <span class="pbwp-box"></span> </label>
  </li>
</ul>
<ul class="form-elements">
  <li class="to-label">
    <label>Tags</label>
  </li>
  <li class="to-field has_input">
    <label class="pbwp-checkbox">
      <input type="hidden" name="post_tags_show" value="" />
      <input type="checkbox" name="post_tags_show" value="on" class="myClass" <?php if(isset($post_tags_show) && $post_tags_show=='on')echo "checked"?> />
      <span class="pbwp-box"></span> </label>
      <input style="display:none;" type="text" name="post_tags_show_text" value="<?php echo cs_allow_special_char($post_tags_show_text);?>" />
  </li>
</ul>
<ul class="form-elements"<?php echo cs_allow_special_char($cs_sidebar_visibility); ?>>
  <li class="to-label">
	<label><?php _e('Related Posts', 'lassic'); ?></label>
  </li>
  <li class="to-field has_input">
    <label class="pbwp-checkbox">
      <input type="hidden" name="cs_related_post" value="" />
      <input type="checkbox" name="cs_related_post" value="on" class="myClass" <?php if(isset($cs_related_post) && $cs_related_post=='on')echo "checked"; ?> />
      <span class="pbwp-box"></span>
    </label>
    <input type="text" name="cs_related_post_title" value="<?php echo cs_allow_special_char($cs_related_post_title);?>" />
  </li>
</ul>
<ul class="form-elements">
  <li class="to-label">
    <label>Next Previous</label>
  </li>
  <li class="to-field has_input">
    <label class="pbwp-checkbox">
      <input type="hidden" name="post_pagination_show" value="" />
      <input type="checkbox" name="post_pagination_show" value="on" class="myClass" <?php if(isset($post_pagination_show) && $post_pagination_show=='on')echo "checked"?> />
      <span class="pbwp-box"></span> </label>
  </li>
</ul>
<ul class="form-elements" style="display:none;">
  <li class="to-label">
    <label>Show Author</label>
  </li>
  <li class="to-field has_input">
    <label class="pbwp-checkbox">
      <input type="hidden" name="post_author_info_show" value="" />
      <input type="checkbox" name="post_author_info_show" value="on" class="myClass" <?php if(isset($post_author_info_show) && $post_author_info_show =='on')echo "checked"?> />
      <span class="pbwp-box"></span> </label>
    <input type="text" name="post_author_info_text" value="<?php echo cs_allow_special_char($post_author_info_text);?>" />
  </li>
</ul>
<?php
	}
}
// Slider options
if ( ! function_exists( 'cs_subheader_element' ) ) {
	function cs_subheader_element(){
		global $cs_xmlObject, $post;
		$page_subheader_no_image = '';
		$cs_map	= '[cs_map column_size="1/1" cs_map_section_title=" Section Title " map_title=" Title " map_height="500" map_lat="31.5497" map_lon="74.3436" map_zoom="11" map_type="ROADMAP" map_info="Information Text here." map_info_width="300" map_info_height="300" map_marker_icon="http://irfan/education/wp-content/uploads/map-marker.png" map_marker_icon1i0A="Browse" map_show_marker="true" map_controls="false" map_draggable="true" map_scrollwheel="true" map_border="yes" map_border_color="#dd3333" cs_map_class="cs_class" cs_map_animation="expandUp"]';
		
		if ( empty($cs_xmlObject->page_custom_title) ) $page_custom_title = ""; else $page_custom_title = $cs_xmlObject->page_custom_title;
		if ( empty($cs_xmlObject->header_banner_style) ) $header_banner_style = ""; else $header_banner_style = $cs_xmlObject->header_banner_style;
		if ( empty($cs_xmlObject->page_title) ) $page_title = "on"; else $page_title = $cs_xmlObject->page_title;
		if ( empty($cs_xmlObject->page_breadcrumbs) ) $page_breadcrumbs = "on"; else $page_breadcrumbs = $cs_xmlObject->page_breadcrumbs;
		if ( empty($cs_xmlObject->subheader_padding_switch) ) $subheader_padding_switch = "default"; else $subheader_padding_switch = $cs_xmlObject->subheader_padding_switch;
		if ( empty($cs_xmlObject->subheader_padding_top) ) $subheader_padding_top = "45"; else $subheader_padding_top = $cs_xmlObject->subheader_padding_top;
		if ( empty($cs_xmlObject->subheader_padding_bottom) ) $subheader_padding_bottom = "45"; else $subheader_padding_bottom = $cs_xmlObject->subheader_padding_bottom;
		if ( empty($cs_xmlObject->page_subheader_no_image)) $page_subheader_no_image == ""; else $page_subheader_no_image = $cs_xmlObject->page_subheader_no_image;
		if ( empty($cs_xmlObject->page_title_align) ) $page_title_align = ""; else $page_title_align = $cs_xmlObject->page_title_align;
		
		if ( empty($cs_xmlObject->page_subheader_color) ) $page_subheader_color = "#f6921f"; else $page_subheader_color = $cs_xmlObject->page_subheader_color;
		if ( empty($cs_xmlObject->page_subheader_text_color) ) $page_subheader_text_color = ""; else $page_subheader_text_color = $cs_xmlObject->page_subheader_text_color;
		if ( empty($cs_xmlObject->page_subheader_border_color) ) $page_subheader_border_color = ""; else $page_subheader_border_color = $cs_xmlObject->page_subheader_border_color;
		if ( empty($cs_xmlObject->header_banner_image) ) $header_banner_image = ''; else $header_banner_image = $cs_xmlObject->header_banner_image;
		if ( empty($cs_xmlObject->custom_slider_id) ) $custom_slider_id = ""; else $custom_slider_id = htmlspecialchars($cs_xmlObject->custom_slider_id);
		if ( empty($cs_xmlObject->slider_position) ) $slider_position = ""; else $slider_position = htmlspecialchars($cs_xmlObject->slider_position);
		if ( empty($cs_xmlObject->custom_map) ) $custom_map = $cs_map; else $custom_map = htmlspecialchars($cs_xmlObject->custom_map);
		if ( empty($cs_xmlObject->page_main_header_border_color) ) $page_main_header_border_color = ""; else $page_main_header_border_color = htmlspecialchars($cs_xmlObject->page_main_header_border_color);
		
		// header absolute position setting
		if ( empty($cs_xmlObject->cs_rev_slider_id) ) $cs_rev_slider_id = ""; else $cs_rev_slider_id = htmlspecialchars($cs_xmlObject->cs_rev_slider_id);
		// end header absolute position setting
		
		if ( empty($cs_xmlObject->page_subheading_title) ) $page_subheading_title = ""; else $page_subheading_title = $cs_xmlObject->page_subheading_title;
		$rand_id = rand(7,555);
		?>
<ul class="form-elements">
  <li class="to-label">
    <label><?php _e('Choose Sub-header','lassic');?></label>
  </li>
  <li class="to-field">
    <div class="input-sec">
     
      <div class="select-style">
        <select name="header_banner_style" class="dropdown" onchange="javascript:cs_slider_element_toggle(this.value)">
          <option <?php if(isset($header_banner_style) and $header_banner_style=="default_header" || $header_banner_style=="" ){echo "selected";}?> value="default_header" ><?php _e('Default Subheader','lassic');?></option>
          <option <?php if(isset($header_banner_style) and $header_banner_style=="breadcrumb_header" ) {echo "selected";}?> value="breadcrumb_header" ><?php _e('Custom Subheader','lassic');?></option>
          <option <?php if(isset($header_banner_style) and $header_banner_style=="custom_slider"){echo "selected";}?> value="custom_slider" ><?php _e('Revolution Slider','lassic');?></option>
          <option <?php if(isset($header_banner_style) and $header_banner_style=="map"){echo "selected";}?> value="map" ><?php _e('Map','lassic');?></option>
          <option <?php if(isset($header_banner_style) and $header_banner_style=="no-header"){echo "selected";}?> value="no-header" ><?php _e('No Subheader','lassic');?></option>
        </select>
        <script>
			<?php  if( $header_banner_style=="default_header" || $header_banner_style=="" ) {?>
					cs_slider_element_toggle('default_header');
			<?php } else {?>
					cs_slider_element_toggle('<?php echo cs_allow_special_char($header_banner_style);?>');
			<?php }?>
					
	    </script>
      </div>
    </div>
  </li>
</ul>
<div id="subheader-background-image" style="display:<?php if($header_banner_style=="breadcrumb_header")echo 'inline"'; else echo 'none';?>" >
  <ul class="form-elements">
    <li class="to-label">
      <label><?php _e('Text Align','lassic')?></label>
    </li>
    <li class="to-field">
      <div class="input-sec">
        <div class="select-style">
          <select name="page_title_align">
            <option value="left" <?php if (isset($cs_xmlObject->page_title_align) and $cs_xmlObject->page_title_align == "left") echo 'selected="selected"';?>>Left</option>
            <option value="right" <?php if (isset($cs_xmlObject->page_title_align) and $cs_xmlObject->page_title_align == "right") echo 'selected="selected"';?>>Right</option>
            <option value="center" <?php if (isset($cs_xmlObject->page_title_align) and $cs_xmlObject->page_title_align == "center") echo 'selected="selected"';?>>Center</option>
          </select>
        </div>
      </div>
    </li>
  </ul>
  <ul class="form-elements">
    <li class="to-label">
      <label>Title</label>
    </li>
    <li class="to-field">
      <label class="pbwp-checkbox">
        <input type="hidden" name="page_title" value="off"/>
        <input type="checkbox" class="myClass" name="page_title" <?php if ($page_title == "on") echo "checked" ?>/>
        <span class="pbwp-box"></span> </label>
    </li>
  </ul>
  <ul class="form-elements">
  <li class="to-label">
    <label>Sub Heading</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <textarea  rows="5" cols="30" name="page_subheading_title"><?php echo cs_allow_special_char($page_subheading_title);?></textarea>
    </div>
  </li>
</ul>
  <ul class="form-elements">
    <li class="to-label">
      <label>Breadcrumbs</label>
    </li>
    <li class="to-field">
      <label class="pbwp-checkbox">
        <input type="hidden" name="page_breadcrumbs" value="off"/>
        <input type="checkbox" class="myClass" name="page_breadcrumbs" <?php if ($page_breadcrumbs== "on" ) echo "checked" ?>/>
        <span class="pbwp-box"></span> </label>
    </li>
  </ul>
  <ul class="form-elements">
    <li class="to-label">
      <label>Padding</label>
    </li>
    <li class="to-field">
      <div class="input-sec">
        <div class="select-style">
          <select name="subheader_padding_switch" onchange="javascript:cs_hide_show_toggle(this.value,'cs_padding_type','page_options')">
            <option value="default" <?php if (isset($cs_xmlObject->subheader_padding_switch) and $cs_xmlObject->subheader_padding_switch == "default") echo 'selected="selected"';?>>Default</option>
            <option value="custom" <?php if (isset($cs_xmlObject->subheader_padding_switch) and $cs_xmlObject->subheader_padding_switch == "custom") echo 'selected="selected"';?>>Custom</option>
            
          </select>
           
        </div>
      </div>
    </li>
  </ul>
  <div id="cs_padding_type" style=" <?php if ( isset($subheader_padding_switch) && $subheader_padding_switch =='custom' ){ echo 'display:block'; } else { echo 'display:none';}?>">
      <ul class="form-elements">
        <li class="to-label"><label>Padding Top</label></li>
        <li class="to-field">
            <div class="cs-drag-slider" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="<?php echo cs_allow_special_char($subheader_padding_top)?>"></div>
            <input  class="cs-range-input"  name="subheader_padding_top[]" type="text" value="<?php echo cs_allow_special_char($subheader_padding_top)?>"   />
            <p>Set the top padding (In PX)</p>
        </li>
      </ul>
      <ul class="form-elements">
        <li class="to-label"><label>Padding Bottom</label></li>
        <li class="to-field">
            <div class="cs-drag-slider" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="<?php echo cs_allow_special_char($subheader_padding_bottom)?>"></div>
            <input  class="cs-range-input"  name="subheader_padding_bottom[]" type="text" value="<?php echo cs_allow_special_char($subheader_padding_bottom)?>"   />
            <p>Set the bottom padding (In PX)</p>
        </li>
      </ul>
  </div>
  <ul class="form-elements">
    <li class="to-label">
      <label>Background Color</label>
    </li>
    <li class="to-field">
      <div class="input-sec">
        <input type="text" name="page_subheader_color"  class="bg_color" value="<?php echo cs_allow_special_char($page_subheader_color) ?>" />
      </div>
    </li>
  </ul>
  <ul class="form-elements">
    <li class="to-label">
      <label>Text Color</label>
    </li>
    <li class="to-field">
      <div class="input-sec">
        <input type="text" name="page_subheader_text_color"  class="bg_color" value="<?php echo cs_allow_special_char($page_subheader_text_color) ?>" />
      </div>
    </li>
  </ul>
  <ul class="form-elements">
    <li class="to-label">
      <label>Border Color</label>
    </li>
    <li class="to-field">
      <div class="input-sec">
        <input type="text" name="page_subheader_border_color"  class="bg_color" value="<?php echo cs_allow_special_char($page_subheader_border_color) ?>" />
      </div>
    </li>
  </ul>
</div>
<div id="default_header_div" <?php  if( ( $header_banner_style=="no-header" || $header_banner_style=="custom_slider" ||  $header_banner_style=="map" ) && $header_banner_style != "" ) { echo 'style="display:none;"';}?>>
<ul class="form-elements">
  <li class="to-label">
    <label>Show Image</label>
  </li>
  <li class="to-field">
    <label class="pbwp-checkbox">
      <input type="hidden" name="page_subheader_no_image" value=""/>
      <input type="checkbox" class="myClass" name="page_subheader_no_image" <?php if ($page_subheader_no_image== "on") echo "checked" ?>/>
      <span class="pbwp-box"></span> </label>
  </li>
</ul>
<ul class="form-elements">
  <li class="to-label">
    <label>Background Image</label>
  </li>
  <li class="to-field">
    <input id="header_banner_image<?php echo cs_allow_special_char($rand_id)?>" name="header_banner_image" type="hidden" class="" value="<?php echo cs_allow_special_char($header_banner_image);?>"/>
    <label class="browse-icon"><input name="header_banner_image<?php echo cs_allow_special_char($rand_id)?>"  type="button" class="uploadMedia left" value="Browse"/></label>
  </li>
</ul>
<div class="page-wrap" style="overflow:hidden; display:<?php echo esc_attr($header_banner_image) && trim($header_banner_image) !='' ? 'inline' : 'none';?>" id="header_banner_image<?php echo cs_allow_special_char($rand_id)?>_box" >
  <div class="gal-active">
    <div class="dragareamain" style="padding-bottom:0px;">
      <ul id="gal-sortable">
        <li class="ui-state-default" id="">
          <div class="thumb-secs"> <img src="<?php echo cs_allow_special_char($header_banner_image);?>"  id="header_banner_image<?php echo cs_allow_special_char($rand_id)?>_img" width="100" height="150" alt="" />
            <div class="gal-edit-opts"> <a   href="javascript:del_media('header_banner_image<?php echo cs_allow_special_char($rand_id)?>')" class="delete"></a> </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
<ul class="form-elements">
  <li class="to-label">
    <label>Parallax Bg Image </label>
  </li>
  <li class="to-field">
    <label class="pbwp-checkbox">
      <input type="hidden" name="page_subheader_parallax" value=""/>
      <input type="checkbox" class="myClass" name="page_subheader_parallax" <?php if (isset($cs_xmlObject->page_subheader_parallax) && $cs_xmlObject->page_subheader_parallax== "on") echo "checked" ?>/>
      <span class="pbwp-box"></span> </label>
  </li>
</ul>
</div>
<div id="subheader_custom_slider" <?php if(isset($header_banner_style) && $header_banner_style != '' && $header_banner_style == "custom_slider"){echo 'style="display:block"';} else {echo 'style="display:none"';} ?> >
 
  <ul class="form-elements">
    <li class="to-label">
      <label>Select Slider</label>
    </li>
    <li class="to-field">
      <div class="select-style">
       <select name="custom_slider_id" id="custom_slider_id">
      	<?php
        	if(class_exists('RevSlider') && class_exists('cs_RevSlider')) {
				$slider = new cs_RevSlider();
				$arrSliders = $slider->getAllSliderAliases();
				foreach ( $arrSliders as $key => $entry ) {
					?>
					<option <?php cs_selected($custom_slider_id,$entry['alias']) ?> value="<?php echo cs_allow_special_char($entry['alias']);?>"><?php echo cs_allow_special_char($entry['title']) ;?></option>
					<?php
				}
         	}  
		?>
        </select>
      </div>
      <div class="left-info">
        <p>Please select Revolution Slider if already included in package. Otherwise buy Sliders from <a href="<?php _e('http://codecanyon.net/','lassic');?>" target="_blank">Codecanyon</a>. But its optional</p>
      </div>
    </li>
  </ul>
  <ul class="form-elements">
    <li class="to-label">
      <label>Select Slider Position</label>
    </li>
    <li class="to-field">
      <div class="select-style">
       <select name="slider_position" id="slider_position">
        <option value="below" <?php echo trim($slider_position) == 'below' ? 'selected="selected"' : '' ;?>>Below Header</option>
      	<option value="above" <?php echo trim($slider_position) == 'above' ? 'selected="selected"' : '' ;?>>Abover Header</option>
       </select>
      </div>
    </li>
  </ul>
</div>
<div id="subheader_map" <?php if(isset($header_banner_style) && $header_banner_style != '' && $header_banner_style == "map"){echo 'style="display:block"';} else {echo 'style="display:none"';} ?> >
  <ul class="form-elements">
    <li class="to-label">
      <label>Custom Map Short Code</label>
    </li>
    <li class="to-field">
      <div class="input-sec">
         <textarea  rows="5" cols="30" name="custom_map" ><?php if(isset( $custom_map)) echo cs_allow_special_char($custom_map);?></textarea>
      </div>
      <div class="left-info">
        <p>Please Add/Edit the short code for Map.</p>
      </div>
    </li>
  </ul>
</div>

<div id="subheader_no_header" <?php if(isset($header_banner_style) && $header_banner_style != '' && $header_banner_style == "no-header"){echo 'style="display:block"';} else {echo 'style="display:none"';} ?> >
  <ul class="form-elements">
    <li class="to-label">
      <label>Header Border Color</label>
    </li>
    <li class="to-field">
      <div class="input-sec">
        <input type="text" name="page_main_header_border_color" class="bg_color" value="<?php echo cs_allow_special_char($page_main_header_border_color); ?>" />
      </div>
    </li>
  </ul>
</div>
<?php
	}
}
// Backgorund Settings
if ( ! function_exists( 'cs_background_settitngs_element' ) ) {
	function cs_background_settitngs_element(){
		global $cs_xmlObject, $post;
		if ( empty($cs_xmlObject->backgrounds->cs_background_option) ) $cs_background_option = ""; else $cs_background_option = $cs_xmlObject->backgrounds->cs_background_option;
		if ( empty($cs_xmlObject->cs_background_v2_video) ) $cs_background_v2_video = ""; else $cs_background_v2_video = $cs_xmlObject->backgrounds->cs_background_v2_video;
		if ( empty($cs_xmlObject->cs_background_v2_video_mute) ) $cs_background_v2_video_mute = ""; else $cs_background_v2_video_mute = $cs_xmlObject->backgrounds->cs_background_v2_video_mute;
		if ( empty($cs_xmlObject->backgrounds->cs_custom_bg_image) ) $cs_custom_bg_image = ""; else $cs_custom_bg_image = $cs_xmlObject->backgrounds->cs_custom_bg_image;
		if ( empty($cs_xmlObject->backgrounds->cs_background_v4_slider) ) $cs_background_v4_slider = ""; else $cs_background_v4_slider = $cs_xmlObject->backgrounds->cs_background_v4_slider;
		if ( empty($cs_xmlObject->backgrounds->cs_background_color) ) $cs_background_color = ""; else $cs_background_color = $cs_xmlObject->backgrounds->cs_background_color;
		?>
<ul class="form-elements  noborder">
  <li class="to-label">
    <label>Background View</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <div class="select-style">
        <select name="cs_background_option" class="dropdown"  onchange="javascript:cs_background_settings_toggle(this.value)">
          <option <?php if($cs_background_option=='no-image') echo "selected";?> value="no-image"> No Image</option>
          <option <?php if($cs_background_option=='custom-background-image') echo "selected";?> value="custom-background-image"> V1 Custom Background Image</option>
          <option <?php if($cs_background_option=='big-image-zoom') echo "selected";?> value="big-image-zoom"> V2 Big Image Zoom</option>
          <option <?php if($cs_background_option=='fade-slider') echo "selected";?> value="fade-slider">V3 Fade Slider</option>
          <option <?php if($cs_background_option=='left-slider')echo "selected";?> value="left-slider">V4 Left Slide</option>
          <option  <?php if($cs_background_option=='background_video')echo "selected";?> value="background_video" >V6 Video</option>
        </select>
      </div>
    </div>
  </li>
</ul>
<div class="form-elements meta-body noborder" style=" <?php if($cs_background_option == "background_video"){echo "display:block";}else echo "display:none";?>" id="home_v2" >
  <ul class="form-elements noborder">
    <li class="to-label">
      <label>Video URL</label>
    </li>
    <li class="to-field">
      <div class="input-sec">
        <input type="text" name="cs_home_v2_video" class="txtfield" value="<?php echo htmlspecialchars($cs_home_v2_video)?>" />
      </div>
      <div class="left-info">
        <p>Please enter Vimeo/Youtube Video URL.</p>
      </div>
    </li>
  </ul>
  <ul class="form-elements noborder">
    <li class="to-label">
      <label>Choose Mute</label>
    </li>
    <li class="to-field">
      <div class="input-sec">
        <div class="select-style">
          <select name="cs_home_v2_video_mute" class="dropdown">
            <option value="Yes" <?php if($cs_home_v2_video_mute == "Yes"){ echo 'selected';}?>> Yes</option>
            <option value="No" <?php if($cs_home_v2_video_mute == "No"){ echo 'selected';}?>> No</option>
          </select>
        </div>
      </div>
    </li>
  </ul>
</div>
<div class="form-elements meta-body noborder" style=" <?php if($cs_background_option == "custom-background-image"){echo "display:block";}else echo "display:none";?>" id="home_v3" >
  <ul class="form-elements noborder">
    <li class="to-label">
      <label>Background Image</label>
    </li>
    <li class="to-field">
      <div class="input-sec">
        <input id="cs_custom_bg_image" name="cs_custom_bg_image" value="<?php echo cs_allow_special_char($cs_custom_bg_image)?>" type="text" class="small " />
        <label class="cs-browse">
          <input id="cs_custom_bg_image" name="cs_custom_bg_image" type="button" class="uploadfile left" value="Browse" />
        </label>
        <?php if ( $cs_custom_bg_image <> "" ) { ?>
        <div class="thumb-preview" id="cs_custom_bg_image_img_div"> <img width="100%" height="100%" src="<?php echo cs_allow_special_char($cs_custom_bg_image)?>" alt=""/> <a href="javascript:remove_image('cs_custom_bg_image')" class="del">&nbsp;</a> </div>
        <?php } ?>
      </div>
    </li>
  </ul>
</div>
<div class="form-elements meta-body noborder" style=" <?php if($cs_background_option == "big-image-zoom" || $px_theme_option['varto_bg_option'] == "fade-slider" ||  $px_theme_option['varto_bg_option'] == "left-slider"){echo "display:block";}else echo "display:none";?>" id="home_v4" >
  <ul class="form-elements noborder">
    <li class="to-label">
      <label>Choose Slider/Gallery</label>
    </li>
    <li class="to-field">
      <div class="input-sec">
        <div class="select-style">
          <select name="cs_home_v4_slider" class="dropdown">
          	<?php
				$query = array( 'posts_per_page' => '-1', 'post_type' => 'px_gallery', 'orderby'=>'ID', 'post_status' => 'publish' );
				$wp_query = new WP_Query($query);
				while ($wp_query->have_posts()) : $wp_query->the_post();
					?>
					<option <?php if($post->post_name==$cs_home_v4_slider)echo "selected";?> value="<?php echo cs_allow_special_char($post->post_name); ?>">
						<?php the_title()?>
					</option>
					<?php
				endwhile;
				wp_reset_query();
			?>
          </select>
        </div>
      </div>
      <div class="left-info">
        <p>Slider/Gallery images resolution should befull size. Create new Slider/Gallery from <a style="color:#06F; text-decoration:underline;" href="<?php echo get_site_url(); ?>/wp-admin/post-new.php?post_type=cs_gallery">here</a></p>
      </div>
    </li>
  </ul>
</div>
<ul class="form-elements">
  <li class="to-label">
    <label>Background Color</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <input type="text" name="cs_background_color"  class="bg_color" value="<?php echo cs_allow_special_char($cs_background_color) ?>" />
    </div>
  </li>
</ul>
<?php	
		
	}
}
// SEO Settings
if ( ! function_exists( 'cs_seo_settitngs_element' ) ) {
	function cs_seo_settitngs_element(){
		global $cs_xmlObject, $post;
		if ( empty($cs_xmlObject->seosettings->cs_seo_title) ) $cs_seo_title = ""; else $cs_seo_title = $cs_xmlObject->seosettings->cs_seo_title;
		if ( empty($cs_xmlObject->seosettings->cs_seo_description) ) $cs_seo_description = ""; else $cs_seo_description = $cs_xmlObject->seosettings->cs_seo_description;
		if ( empty($cs_xmlObject->seosettings->cs_seo_keywords) ) $cs_seo_keywords = ""; else $cs_seo_keywords = $cs_xmlObject->seosettings->cs_seo_keywords;
		?>
<ul class="form-elements ">
  <li class="to-label">
    <label>SEO Title </label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <input type="text" name="cs_seo_title"  value="<?php echo cs_allow_special_char($cs_seo_title) ?>" />
    </div>
  </li>
</ul>
<ul class="form-elements ">
  <li class="to-label">
    <label>SEO Description</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <textarea name="cs_seo_description" rows="5" cols="30" ><?php echo cs_allow_special_char($cs_seo_description);?></textarea>
    </div>
  </li>
</ul>
<ul class="form-elements ">
  <li class="to-label">
    <label>SEO Keywords</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <input type="text" name="cs_seo_keywords"  value="<?php echo cs_allow_special_char($cs_seo_keywords) ?>" />
    </div>
  </li>
</ul>
<?php
	}
}

/* Header Setting for in case of position absolute*/
if ( ! function_exists( 'cs_header_postition_element' ) ) {
	function cs_header_postition_element(){
		global $cs_xmlObject, $post;
		
		if ( empty($cs_xmlObject->header_bg_options) ) $header_bg_options = ""; else $header_bg_options = $cs_xmlObject->header_bg_options;
		if ( empty($cs_xmlObject->cs_rev_slider_id) ) $cs_rev_slider_id = ""; else $cs_rev_slider_id = $cs_xmlObject->cs_rev_slider_id;
		if ( empty($cs_xmlObject->cs_headerbg_image) ) $cs_headerbg_image = ""; else $cs_headerbg_image = $cs_xmlObject->cs_headerbg_image;
		if ( empty($cs_xmlObject->cs_headerbg_color) ) $cs_headerbg_color = ""; else $cs_headerbg_color = $cs_xmlObject->cs_headerbg_color;
		
		?>
        <ul class="form-elements ">
          <li class="to-label">
            <label>Header Background</label>
          </li>
          <li class="to-field">
            <div class="input-sec">
              	<select name="header_bg_options" class="dropdown" onchange="javascript:cs_header_option(this.value)">
                	<option <?php cs_selected($header_bg_options,'none') ?> value="none" >None</option>
                	<option <?php cs_selected($header_bg_options,'cs_rev_slider') ?> value="cs_rev_slider" >Revolution Slider</option>
                	<option <?php cs_selected($header_bg_options,'cs_bg_image_color') ?> value="cs_bg_image_color" >Bg Image / bg Color</option>
            	</select>
            </div>
          </li>
        </ul>
        <div id="cs_rev_slider" style="display:<?php echo ($header_bg_options=='cs_rev_slider')?'block':'none'; ?>">
          <ul class="form-elements">
            <li class="to-label">
              <label>Select Slider</label>
            </li>
            <li class="to-field">
              <div class="select-style">
               <select name="cs_rev_slider_id" id="cs_rev_slider_id">
                <?php
                    if(class_exists('RevSlider') && class_exists('cs_RevSlider')) {
						
							$slider = new cs_RevSlider();
							$arrSliders = $slider->getAllSliderAliases();
							foreach ( $arrSliders as $key => $entry ) {
								?>
								<option <?php cs_selected($cs_rev_slider_id,$entry['alias']) ?> value="<?php echo cs_allow_special_char($entry['alias']);?>"><?php echo cs_allow_special_char($entry['title']) ;?></option>
								<?php
							}
						
					 }  
                ?>
                </select>
              </div>
              <div class="left-info">
                <p>Please select Revolution Slider if already included in package. Otherwise buy Sliders from <a href="<?php _e('http://codecanyon.net/','lassic');?>" target="_blank">Codecanyon</a>. But its optional</p>
              </div>
            </li>
          </ul>
 		</div>
		<div id="cs_headerbg_image_div" style="display:<?php echo ($header_bg_options=='cs_bg_image_color')?'block':'none'; ?>">
            <ul class="form-elements noborder">
                <li class="to-label">
                <label>Background Image</label>
                </li>
            	<li class="to-field">
               		<div class="input-sec">
               			<input id="cs_headerbg_image" name="cs_headerbg_image" value="<?php echo cs_allow_special_char($cs_headerbg_image)?>" type="text" class="small " />
                        <label class="cs-browse">
                        <input id="cs_headerbg_image" name="cs_headerbg_image" type="button" class="uploadfile left" value="Browse" />
                        </label>
						<?php if ( $cs_headerbg_image <> "" ) { ?>
                        <div class="thumb-preview" id="cs_headerbg_image_img_div"> 
                        	<img width="100%" height="100%" src="<?php echo cs_allow_special_char($cs_headerbg_image)?>" /> 
                            <a href="javascript:remove_image('cs_headerbg_image')" class="del">&nbsp;</a> 
                        </div>
                        <?php } ?>
                	</div>
            	</li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Background Color</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <input type="text" name="cs_headerbg_color"  class="bg_color" value="<?php echo cs_allow_special_char($cs_headerbg_color) ?>" />
                </div>
              </li>
            </ul>
        </div>
		
<?php
	}
}

// Custon Syle Settings
if ( ! function_exists( 'cs_customstyling_element' ) ) {
	function cs_customstyling_element(){
		global $cs_xmlObject, $post;
		//Header Color
		if ( empty($cs_xmlObject->customstyle->cs_custom_classes) ) $cs_custom_classes = ""; else $cs_custom_classes = $cs_xmlObject->customstyle->cs_custom_classes;
		if ( empty($cs_xmlObject->customstyle->cs_custom_ids) ) $cs_custom_ids = ""; else $cs_custom_ids = $cs_xmlObject->customstyle->cs_custom_ids;
		if ( empty($cs_xmlObject->customstyle->cs_header_color) ) $cs_header_color = ""; else $cs_header_color = $cs_xmlObject->customstyle->cs_header_color;
		if ( empty($cs_xmlObject->customstyle->cs_subheader_color) ) $cs_subheader_color = ""; else $cs_subheader_color = $cs_xmlObject->customstyle->cs_subheader_color;
		?>
<ul class="form-elements noborder">
  <li class="to-label">
    <label>Custom Classes</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <input type="text" name="cs_custom_classes"  value="<?php echo cs_allow_special_char($cs_custom_classes);?>" />
    </div>
  </li>
</ul>
<ul class="form-elements noborder">
  <li class="to-label">
    <label>Custom IDs</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <input type="text" name="cs_custom_ids"  value="<?php echo cs_allow_special_char($cs_custom_ids);?>" />
    </div>
  </li>
</ul>
<ul class="form-elements noborder">
  <li class="to-label">
    <label>Sub Header Color</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <input type="text" name="cs_subheader_color"  class="bg_color" value="<?php echo cs_allow_special_char($cs_subheader_color) ?>" />
    </div>
  </li>
</ul>
<ul class="form-elements noborder">
  <li class="to-label">
    <label>Header Color</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <input type="text" name="cs_header_color"  class="bg_color" value="<?php echo cs_allow_special_char($cs_header_color) ?>" />
    </div>
  </li>
</ul>
<?php
	}
}
//Sidebar Layout Options
/*function upload_admin_scripts() {    
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
   	wp_register_script('my-upload', get_template_directory_uri().'/scripts/admin/my_script.js', array('jquery','media-upload','thickbox'));
    wp_enqueue_script('my-upload');
}
function upload_admin_styles() {

    wp_enqueue_style('thickbox');
}*/

// better use get_current_screen(); or the global $current_screen//
//if (isset($_GET['page']) && $_GET['page'] == 'my_plugin_page') {

  //  add_action('admin_enqueue_scripts', 'upload_admin_scripts');
//    add_action('admin_enqueue_styles', 'upload_admin_styles');
//}
if ( ! function_exists( 'cs_sidebar_layout_options' ) ) {
	function cs_sidebar_layout_options(){
			global $post , $cs_xmlObject,$cs_theme_options, $page_option;
 			if ( empty($cs_xmlObject->sidebar_layout->cs_page_layout) ) $cs_page_layout = ""; else $cs_page_layout = $cs_xmlObject->sidebar_layout->cs_page_layout;
	
			if ( empty($cs_xmlObject->sidebar_layout->cs_page_sidebar_left) ) $cs_page_sidebar_left = ""; else $cs_page_sidebar_left = $cs_xmlObject->sidebar_layout->cs_page_sidebar_left;
		
			if ( empty($cs_xmlObject->sidebar_layout->cs_page_sidebar_right) ) $cs_page_sidebar_right = ""; else $cs_page_sidebar_right = $cs_xmlObject->sidebar_layout->cs_page_sidebar_right;
			
			$cs_post_type = get_post_type($post->ID);
			
			$cs_sidebar_visibility = '';
			if( $cs_post_type == 'project' ){
				$cs_sidebar_visibility = ' style="display:none;"';
			}
  			
			?>

<ul class="form-elements"<?php echo cs_allow_special_char($cs_sidebar_visibility); ?>>
  <li class="to-label">
    <label> Choose Sidebar</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <div class="meta-input pattern">
        <div class='radio-image-wrapper'>
          <input <?php if($cs_page_layout=="none")echo "checked"?> onclick="show_sidebar_page('none')" type="radio" name="cs_page_layout" class="radio" value="none" id="page_radio_1" />
          <label for="page_radio_1"> <span class="ss"><img src="<?php echo get_template_directory_uri()?>/include/assets/images/no_sidebar.png"  alt="" /></span> <span <?php if($cs_page_layout=="none")echo "class='check-list'"?> id="check-list"></span> </label>
          <span class="title-theme">Full Width</span> </div>
        <div class='radio-image-wrapper'>
          <input <?php if($cs_page_layout=="right")echo "checked"?> onclick="show_sidebar_page('right')" type="radio" name="cs_page_layout" class="radio" value="right" id="page_radio_2"  />
          <label for="page_radio_2"> <span class="ss"><img src="<?php echo get_template_directory_uri()?>/include/assets/images/sidebar_right.png" alt="" /></span> <span <?php if($cs_page_layout=="right")echo "class='check-list'"?> id="check-list"></span> </label>
          <span class="title-theme">Sidebar Right</span> </div>
        <div class='radio-image-wrapper'>
          <input <?php if($cs_page_layout=="left")echo "checked"?> onclick="show_sidebar_page('left')" type="radio" name="cs_page_layout" class="radio" value="left" id="page_radio_3" />
          <label for="page_radio_3"> <span class="ss"><img src="<?php echo get_template_directory_uri()?>/include/assets/images/sidebar_left.png" alt="" /></span> <span <?php if($cs_page_layout=="left")echo "class='check-list'"?> id="check-list"></span> </label>
          <span class="title-theme">Sidebar Left</span> </div>
      </div>
    </div>
  </li>
</ul>
<ul class="form-elements meta-body" style=" <?php if($cs_page_sidebar_left == ""){echo "display:none";}else echo "display:block";?>" id="sidebar_left" >
  <li class="to-label">
    <label>Select Left Sidebar</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <div class="select-style">
        <select name="cs_page_sidebar_left" class="select_dropdown" id="page-option-choose-left-sidebar">
          <?php
			$cs_theme_sidebar=get_option('cs_theme_options');
			if ( isset($cs_theme_sidebar['sidebar']) and count($cs_theme_sidebar['sidebar']) > 0 ) {
				foreach ( $cs_theme_sidebar['sidebar'] as $sidebar ){
				?>
<option <?php if ($cs_page_sidebar_left==$sidebar)echo "selected";?> ><?php echo cs_allow_special_char($sidebar);?></option>
<?php

				}

			}

			?>
        </select>
      </div>
    </div>
    <div class="left-info">
      <p> Add New Sidebar. <a href="<?php echo admin_url();?>themes.php?page=cs_options_page#tab-sidebar-show" target="_blank">Click Here</a></p>
    </div>
  </li>
</ul>
<ul class="form-elements meta-body" style=" <?php if($cs_page_sidebar_right == ""){echo "display:none";}else echo "display:block";?>" id="sidebar_right" >
  <li class="to-label">
    <label>Select Right Sidebar </label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <div class="select-style">
        <select name="cs_page_sidebar_right" class="select_dropdown" id="page-option-choose-right-sidebar">
          <?php
			
								if ( isset($cs_theme_sidebar['sidebar']) and count($cs_theme_sidebar['sidebar']) > 0 ) {
			
									foreach ( $cs_theme_sidebar['sidebar'] as $sidebar ){
			
									?>
          <option <?php if ($cs_page_sidebar_right==$sidebar)echo "selected";?> ><?php echo cs_allow_special_char($sidebar);?></option>
          <?php
			
									}
			
								}
			
								?>
        </select>
      </div>
    </div>
    <div class="left-info">
      <p> Add New Sidebar. <a href="<?php echo admin_url();?>themes.php?page=cs_options_page#tab-sidebar-show" target="_blank">Click Here</a></p>
      <input type="hidden" name="cs_orderby[]" value="meta_layout" />
    </div>
  </li>
</ul>
<?php
	}
}
function cs_pagebuilder_themeoptions(){
	global $post,$cs_xmlObject,$cs_theme_options, $page_option;
	$cs_page_options = (!empty($cs_xmlObject->cs_page_options))? $cs_xmlObject->cs_page_options : '' ;
 	$current_user = wp_get_current_user();
	//get_post_type($post->ID) !='page' and 
	if($current_user->user_login =='peachclub-admin'){
	 ?>
    <ul class="form-elements">
      <li class="to-label">
        <label> <?php _e('Page Options','lassic'); ?> </label>
      </li>
      <li class="to-field">
        <div class="input-sec">
          <div class="select-style">
          
             <select name="cs_page_options" class="select_dropdown" id="cs_page_options">
                    <option>default</option>
                    <?php
                        if(isset($page_option['theme_options'])){
                            
                            foreach($page_option['theme_options']['select'] as $key=>$options){
                                ?>
                                <option <?php cs_selected($cs_page_options,$key);?> value="<?php echo cs_allow_special_char($key); ?>" >
                                    <?php echo cs_allow_special_char($options); ?>
                                </option>
                                <?php
                            }
                        }
                     ?>
            </select>
          </div>
        </div>
      </li>
    </ul>

<?php
}
	
}
// Footer top area Settings
if ( ! function_exists( 'cs_footer_settings_element' ) ) {
	function cs_footer_settings_element(){ 
		global $cs_xmlObject, $post;
		//sidebar_layout,footertoparea,customstyle,seosettings,backgrounds
		if ( empty($cs_xmlObject->footertoparea->cs_footer_toparea_bg_color) ) $cs_footer_toparea_bg_color = ""; else $cs_footer_toparea_bg_color = $cs_xmlObject->footertoparea->cs_footer_toparea_bg_color;
		if ( empty($cs_xmlObject->footertoparea->cs_background_skin) ) $cs_background_skin = ""; else $cs_background_skin = $cs_xmlObject->footertoparea->cs_background_skin;
		?>
<ul class="form-elements">
  <li class="to-label">
    <label>Background Color</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <input type="text" name="cs_footer_toparea_bg_color"  class="bg_color" value="<?php echo cs_allow_special_char($cs_footer_toparea_bg_color) ?>" />
    </div>
  </li>
</ul>
<ul class="form-elements">
  <li class="to-label">
    <label>Background Skin</label>
  </li>
  <li class="to-field">
    <div class="input-sec">
      <div class="select-style">
        <select name="cs_background_skin">
          <option value="light" <?php if($cs_background_skin == 'light'){echo 'selected="selected"';}?>>Light</option>
          <option value="dark" <?php if($cs_background_skin == 'dark'){echo 'selected="selected"';}?>>Dark</option>
        </select>
      </div>
    </div>
  </li>
</ul>
<?php
	}
}
// Default xml data save
if ( ! function_exists( 'cs_page_options_save_xml' ) ) {
	function cs_page_options_save_xml($sxe) {
		// page/post General save Settings
		
		if (empty($_POST['header_banner_style'])){ $_POST['header_banner_style'] = "";}
		if (empty($_POST['page_breadcrumbs'])){ $_POST['page_breadcrumbs'] = "";}
		if (empty($_POST['subheader_padding_switch'])){ $_POST['subheader_padding_switch'] = "";}
		if (empty($_POST['subheader_padding_top'])){ $_POST['subheader_padding_top'] = "";}
		if (empty($_POST['subheader_padding_bottom'])){ $_POST['subheader_padding_bottom'] = "";}
		if (empty($_POST['page_custom_menu'])){ $_POST['page_custom_menu'] = "";}
		if (empty($_POST['page_title_align'])){ $_POST['page_title_align'] = "";}
		if (empty($_POST['page_custom_title'])){ $_POST['page_custom_title'] = "";}
		if (empty($_POST['page_title'])){ $_POST['page_title'] = "";}
		if (empty($_POST['page_subheading_title'])){ $_POST['page_subheading_title'] = "";}
		if (empty($_POST['header_banner_image'])){ $_POST['header_banner_image'] = "";}
		if (empty($_POST['page_subheader_color'])){ $_POST['page_subheader_color'] = "";}
		if (empty($_POST['page_subheader_text_color'])){ $_POST['page_subheader_text_color'] = "#fff";}
		if (empty($_POST['page_subheader_border_color'])){ $_POST['page_subheader_border_color'] = "";}
		if (empty($_POST['custom_slider_id'])){ $_POST['custom_slider_id'] = "";}
		if (empty($_POST['slider_position'])){ $_POST['slider_position'] = "";}
		if (empty($_POST['custom_map'])){ $_POST['custom_map'] = "";}
		if (empty($_POST['page_subheader_parallax'])){ $_POST['page_subheader_parallax'] = "";}
		if (empty($_POST['page_subheader_no_image'])){ $_POST['page_subheader_no_image'] = "";}
		if (empty($_POST['post_social_sharing'])){ $_POST['post_social_sharing'] = "";}
		if (empty($_POST['post_tags_show'])){ $_POST['post_tags_show'] = "";}
		if (empty($_POST['post_tags_show_text'])){ $_POST['post_tags_show_text'] = "";}
		if (empty($_POST['cs_related_post'])){ $_POST['cs_related_post'] = "";}
		if (empty($_POST['cs_related_post_title'])){ $_POST['cs_related_post_title'] = "";}
		if (empty($_POST['post_pagination_show'])){ $_POST['post_pagination_show'] = "";}
		if (empty($_POST['post_author_info_show'])){ $_POST['post_author_info_show'] = "";}
		if (empty($_POST['post_author_info_text'])){ $_POST['post_author_info_text'] = "";}
		if (empty($_POST['cs_page_options'])){ $_POST['cs_page_options'] = "";}
		if (empty($_POST['page_main_header_border_color'])){ $_POST['page_main_header_border_color'] = "";}
		// header position setting
		if (empty($_POST['header_bg_options'])){ $_POST['header_bg_options'] = "";}
		if (empty($_POST['cs_rev_slider_id'])){ $_POST['cs_rev_slider_id'] = "";}
		if (empty($_POST['cs_headerbg_image'])){ $_POST['cs_headerbg_image'] = "";}
		if (empty($_POST['cs_headerbg_color'])){ $_POST['cs_headerbg_color'] = "";}
 		// end header position setting
		
		$sxe->addChild('post_author_info_show', $_POST['post_author_info_show']);
		$sxe->addChild('post_author_info_text', $_POST['post_author_info_text']);
		$sxe->addChild('post_social_sharing', $_POST['post_social_sharing']);
		$sxe->addChild('post_tags_show', $_POST['post_tags_show']);
		$sxe->addChild('post_tags_show_text', $_POST['post_tags_show_text']);
		$sxe->addChild('cs_related_post', $_POST['cs_related_post']);
		$sxe->addChild('cs_related_post_title', $_POST['cs_related_post_title']);
		$sxe->addChild('post_pagination_show', $_POST['post_pagination_show']);
		
		$sxe->addChild('header_banner_style', $_POST['header_banner_style']);
		$sxe->addChild('page_title', $_POST['page_title']);
		$sxe->addChild('page_breadcrumbs', $_POST['page_breadcrumbs']);
		$sxe->addChild('subheader_padding_switch', $_POST['subheader_padding_switch']);
		$sxe->addChild('subheader_padding_top', $_POST['subheader_padding_top'][0]);
		$sxe->addChild('subheader_padding_bottom', $_POST['subheader_padding_bottom'][0]);
		$sxe->addChild('page_custom_menu', $_POST['page_custom_menu']);
		$sxe->addChild('page_title_align', $_POST['page_title_align']);
		$sxe->addChild('page_custom_title', $_POST['page_custom_title']);
		$sxe->addChild('page_subheading_title', htmlspecialchars($_POST['page_subheading_title']));
		$sxe->addChild('header_banner_image', $_POST['header_banner_image']);
		$sxe->addChild('page_subheader_color', $_POST['page_subheader_color']);
		$sxe->addChild('page_subheader_text_color', $_POST['page_subheader_text_color']);
		$sxe->addChild('page_subheader_border_color', $_POST['page_subheader_border_color']);
		$sxe->addChild('custom_slider_id', $_POST['custom_slider_id']);
		$sxe->addChild('slider_position', $_POST['slider_position']);
		$sxe->addChild('custom_map', htmlspecialchars($_POST['custom_map']));
		$sxe->addChild('page_subheader_parallax', $_POST['page_subheader_parallax']);
		$sxe->addChild('page_subheader_no_image', $_POST['page_subheader_no_image']);
		$sxe->addChild('cs_page_options', $_POST['cs_page_options']);
		$sxe->addChild('page_main_header_border_color', $_POST['page_main_header_border_color']);
		// SEO Settings save Settings
		// header position setting
 		$sxe->addChild('header_bg_options', $_POST['header_bg_options']);
		$sxe->addChild('cs_rev_slider_id', $_POST['cs_rev_slider_id']);
		$sxe->addChild('cs_headerbg_image', $_POST['cs_headerbg_image']);
		$sxe->addChild('cs_headerbg_color', $_POST['cs_headerbg_color']);
		
		// end header position setting
		if (empty($_POST['cs_seo_title'])){ $_POST['cs_seo_title'] = "";}
		if (empty($_POST['cs_seo_description'])){ $_POST['cs_seo_description'] = "";}
		if (empty($_POST['cs_seo_keywords'])){ $_POST['cs_seo_keywords'] = "";}
	
		$seosettings_layout = $sxe->addChild('seosettings');
		$seosettings_layout->addChild('cs_seo_title', htmlspecialchars($_POST['cs_seo_title']));
		$seosettings_layout->addChild('cs_seo_description',htmlspecialchars( $_POST['cs_seo_description']));
		$seosettings_layout->addChild('cs_seo_keywords', htmlspecialchars($_POST['cs_seo_keywords']));
		
		// Sidebar Laoyout Settings save Settings
		if (empty($_POST['cs_page_layout'])){ $_POST['cs_page_layout'] = "";}
		if (empty($_POST['cs_page_sidebar_left'])){ $_POST['cs_page_sidebar_left'] = "";}
		if (empty($_POST['cs_page_sidebar_right'])){ $_POST['cs_page_sidebar_right'] = "";}
	
		$sidebar_layout = $sxe->addChild('sidebar_layout');
		$sidebar_layout->addChild('cs_page_layout', $_POST["cs_page_layout"]);
		if ($_POST["cs_page_layout"] == "left") {
			$sidebar_layout->addChild('cs_page_sidebar_left', $_POST['cs_page_sidebar_left']);
		} else if ($_POST["cs_page_layout"] == "right") {
			$sidebar_layout->addChild('cs_page_sidebar_right', $_POST['cs_page_sidebar_right']);
		}else if ($_POST["cs_page_layout"] == "both_right" or $_POST["cs_page_layout"] == "both_left" or $_POST["cs_page_layout"] == "both") {
			$sidebar_layout->addChild('cs_page_sidebar_left', $_POST['cs_page_sidebar_left']);
			$sidebar_layout->addChild('cs_page_sidebar_right', $_POST['cs_page_sidebar_right']);
		}
		
		return $sxe;
	}
}