<?php

//=====================================================================
// Video & Sound Cloud Shortcode for page builder start
//=====================================================================
function cs_pb_video($die = 0){
	global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		$cs_album_num = 0;
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_video';
			$cs_parseObject = new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
			$cs_defaults = array('cs_video_section_title' => '','cs_video_url' => '','cs_video_width' => '500', 'cs_video_height' => '250','cs_video_custom_class'=>'','cs_video_custom_animation'=>'');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_atts_content = $cs_output['0']['content'];
			else 
				$cs_atts_content = array();
			if(is_array($cs_atts_content))
				$cs_album_num = count($cs_atts_content);
			$cs_video_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_video';
			$cs_coloumn_class = 'column_'.$cs_video_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
 ?>

<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="column" data="<?php echo element_size_data_array_index($cs_video_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_video_element_size,'','play-circle');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_video {{attributes}}]{{content}}[/cs_video]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Video Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
        <ul class="form-elements">
            <li class="to-label"><label>Section Title</label></li>
            <li class="to-field">
                <input  name="cs_video_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_video_section_title);?>"   />
                <p> This is used for the one page navigation, to identify the section below. Give a title  </p>
            </li>                  
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Video URL</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_video_url[]" class="txtfield" value="<?php echo esc_url($cs_video_url)?>" />
            <p>give the video url here</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Width</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_video_width[]" class="txtfield" value="<?php echo esc_attr($cs_video_width);?>" />
            <p>Add a width in pix, If you want to override the default</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Height</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_video_height[]" class="txtfield" value="<?php echo esc_attr($cs_video_height)?>" />
            <p>Provide height in px, if you want to override the default </p>
          </li>
        </ul>
        <?php 
			if ( function_exists( 'cs_shortcode_custom_classes_test' ) ) {
				cs_shortcode_custom_dynamic_classes($cs_video_custom_class,$cs_video_custom_animation,'','video');
			}
		?>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$cs_name));?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="video" />
          <input type="button" value="Save" style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))" />
        </li>
      </ul>
      <?php }?>
    </div>
  </div>
</div>
<?php
	if ( $die <> 1 ) die();
}
add_action('wp_ajax_cs_pb_video', 'cs_pb_video');
// Video & Sound Cloud Shortcode for page builder end 

//=====================================================================
// image frame html for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_image' ) ) {
	function cs_pb_image($die = 0){
		global $cs_node,$cs_count_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$CS_PREFIX = 'cs_image';
		$cs_defaultAttributes	= false;
		$cs_parseObject 	= new ShortcodeParse();
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
			$cs_defaultAttributes	= true;
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
 			$cs_defaults = array( 'cs_image_section_title' => '','cs_image_style' => '','cs_image_url' => '','cs_image_title' => '','cs_image_caption' => '','cs_image_custom_class'=>'','cs_image_custom_animation'=>'');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			
			if(isset($cs_output['0']['content']))
				$cs_atts_content = $cs_output['0']['content'];
			else 
				$cs_atts_content = "";
			
			$cs_image_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_image';
			$cs_count_node++;
			$cs_coloumn_class = 'column_'.$cs_image_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
		
		$cs_rand_id = rand(34, 443534);
 		
 	?>
<div id="<?php echo cs_allow_special_char($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo cs_allow_special_char($cs_coloumn_class);?> <?php echo cs_allow_special_char($cs_shortcode_view);?>" item="image" data="<?php echo element_size_data_array_index($cs_image_element_size); ?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_image_element_size,'','picture-o');?>
  <div class="cs-wrapp-class-<?php echo cs_allow_special_char($cs_counter)?> <?php echo cs_allow_special_char($cs_shortcode_element);?>" id="<?php echo cs_allow_special_char($cs_name.$cs_counter)?>" data-shortcode-template="[cs_image {{attributes}}]{{content}}[/cs_image]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Image Options</h5>
      <a href="javascript:removeoverlay('<?php echo cs_allow_special_char($cs_name.$cs_counter)?>','<?php echo cs_allow_special_char($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
        <ul class="form-elements">
            <li class="to-label"><label>Section Title</label></li>
            <li class="to-field">
                <input name="cs_image_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_image_section_title)?>"   />
                <p> This is used for the one page navigation, to identify the section below. Give a title </p>
            </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Image Style</label>
          </li>
          <li class="to-field select-style">
            <select class="cs_image_style" name="cs_image_style[]">
              <option <?php if($cs_image_style == 'frame-plane'){echo 'selected="selected"';}?> value="frame-plane">Plain</option>
              <option <?php if($cs_image_style == 'frame-clean'){echo 'selected="selected"';}?> value="frame-clean">Clean</option>
               <option <?php if($cs_image_style == 'frame-classic'){echo 'selected="selected"';}?> value="frame-classic">Classic</option>

              <option <?php if($cs_image_style == 'frame-simple'){echo 'selected="selected"';}?> value="frame-simple">Simple</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Image URL</label>
          </li>
          <li class="to-field">
            <input id="cs_image_url<?php echo esc_attr($cs_rand_id)?>" name="cs_image_url[]" type="hidden" class="" value="<?php echo esc_url($cs_image_url);?>"/>
            <input name="cs_image_url<?php echo esc_attr($cs_rand_id)?>"  type="button" class="uploadMedia left" value="Browse"/>
            <div class="left-info">
            <p>Browse the image </p>
            </div>
          </li>
        </ul>
        <ul class="form-elements">
        	<li class="image-frame">
            	<div class="page-wrap" style="overflow:hidden; display:<?php echo cs_allow_special_char($cs_image_url) && trim($cs_image_url) !='' ? 'inline' : 'none';?>" id="cs_image_url<?php echo cs_allow_special_char($cs_rand_id)?>_box" >
                  <div class="gal-active">
                    <div class="dragareamain" style="padding-bottom:0px;">
                      <ul id="gal-sortable">
                        <li class="ui-state-default" id="">
                          <div class="thumb-secs"> <img src="<?php echo cs_allow_special_char($cs_image_url);?>"  id="cs_image_url<?php echo cs_allow_special_char($cs_rand_id);?>_img" width="100" height="150"  />
                            <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_image_url<?php echo cs_allow_special_char($cs_rand_id);?>')" class="delete"></a> </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
            </li>
        </ul>
        
        <ul class="form-elements">
          <li class="to-label">
            <label>Title</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_image_title[]" class="txtfield" value="<?php echo cs_allow_special_char($cs_image_title)?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Caption</label>
          </li>
          <li class="to-field">
            <textarea name="cs_image_caption[]" rows="10" class="textarea" data-content-text="cs-shortcode-textarea"><?php echo esc_textarea($cs_atts_content); ?></textarea>
            <p>If you would like a caption to be shown below the image, add it here.</p>
          </li>
        </ul>
        <?php 
			if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
				cs_shortcode_custom_dynamic_classes($cs_image_custom_class,$cs_image_custom_animation,'','image');
			}
		?>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo cs_allow_special_char(str_replace('cs_pb_','',$cs_name));?>','<?php echo cs_allow_special_char($cs_name.$cs_counter)?>','<?php echo cs_allow_special_char($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="image" />
          <input type="button" value="Save" style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))" />
        </li>
      </ul>
      <?php }?>
    </div>
  </div>
</div>
<?php
		if ( $die <> 1 ) die();
	}
	add_action('wp_ajax_cs_pb_image', 'cs_pb_image');
}
// image frame html  for page builder end

//=====================================================================
// Promobox Shortcode Builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_promobox' ) ) {
	function cs_pb_promobox($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		$cs_album_num = 0;
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_promobox';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
			$cs_defaults = array( 'cs_promobox_section_title'=>'', 'cs_promo_style'=>'','cs_promobox_bg_color'=>'','cs_promo_image'=>'','cs_promo_icon'=>'', 'cs_promo_image_align'=>'','cs_promobox_title'=>'', 'cs_promobox_contents'=>'', 'cs_promobox_btn_bg_color'=>'','cs_promobox_title_color'=>'', 'cs_promobox_content_color'=>'' ,'cs_link_title'=>'Read More','cs_link'=>'#', 'cs_promobox_class' => '', 'cs_promobox_animation' => '','cs_text_align'=>'', 'cs_target'=>'_self');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_atts_content = $cs_output['0']['content'];
			else 
				$cs_atts_content = array();
			if(is_array($cs_atts_content))
				$cs_album_num = count($cs_atts_content);
			$cs_promobox_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			}
			$cs_name = 'cs_pb_promobox';
			$cs_coloumn_class = 'column_'.$cs_promobox_element_size;
			$cs_rand_id = $cs_counter.''.cs_generate_random_string(3);
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="promobox" data="<?php echo element_size_data_array_index($cs_promobox_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_promobox_element_size,'','life-ring');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_promobox {{attributes}}]{{content}}[/cs_promobox]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Promobox Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
        <ul class="form-elements">
            <li class="to-label"><label>Section Title</label></li>
            <li class="to-field">
                <input name="cs_promobox_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_promobox_section_title)?>" />
                <p> This is used for the one page navigation, to identify the section below. Give a title </p>
            </li>                  
        </ul>
        
        <ul class="form-elements">
          <li class="to-label">
            <label>Style</label>
          </li>
          <li class="to-field select-style">
            <select class="bg_repeat" name="cs_promo_style[]">
              <option <?php if($cs_promo_style == 'icon'){echo 'selected="selected"';}?> value="icon">icon</option>
              <option <?php if($cs_promo_style == 'image'){echo 'selected="selected"';}?> value="image">image</option>
            </select>
          </li>
        </ul>
        
        <ul class="form-elements">
          <li class="to-label">
            <label>Background Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_promobox_bg_color[]" class="bg_color" value="<?php echo esc_attr($cs_promobox_bg_color)?>" />
          </li>
        </ul>
        
        <ul class="form-elements">
          <li class="to-label">
            <label>Image</label>
          </li>
          <li class="to-field">
            <input id="cs_promo_image<?php echo esc_attr($cs_counter)?>" name="cs_promo_image[]" type="hidden" class="" value="<?php echo esc_url($cs_promo_image);?>" />
            <input name="cs_promo_image<?php echo esc_attr($cs_counter)?>" type="button" class="uploadMedia left" value="Browse"/>
            <div class="left-info">
            
            <p>Promobox image here</p>
            </div>
          </li>
        </ul>
                
        <div class="page-wrap" style="overflow:hidden; display:<?php echo esc_url($cs_promo_image) !='' ? 'inline' : 'none';?>" id="cs_promo_image<?php echo intval($cs_counter)?>_box" >
          <div class="gal-active">
            <div class="dragareamain" style="padding-bottom:0px;">
              <ul id="gal-sortable">
                <li class="ui-state-default" id="">
                  <div class="thumb-secs"> <img src="<?php echo esc_url($cs_promo_image);?>"  id="cs_promo_image<?php echo intval($cs_counter)?>_img" width="100" height="150"  />
                    <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_promo_image<?php echo intval($cs_counter)?>')" class="delete"></a> </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        <ul class='form-elements' id="cs_infobox_<?php echo esc_attr($cs_name.$cs_counter);?>">
          <li class='to-label'>
            <label> IcoMoon Icon:</label>
          </li>
          <li class="to-field">
           <?php cs_fontawsome_icons_box($cs_promo_icon,$cs_name.$cs_counter,'cs_promo_icon');?>
          </li>
        </ul>
        
        <ul class="form-elements">
          <li class="to-label">
            <label>Image Align</label>
          </li>
          <li class="to-field select-style">
            <select class="bg_repeat" name="cs_promo_image_align[]">
              <option <?php if($cs_promo_image_align == 'top-left'){echo 'selected="selected"';}?> value="top-left">Top Left</option>
              <option <?php if($cs_promo_image_align == 'top-right'){echo 'selected="selected"';}?> value="top-right">Top Right</option>
              <option <?php if($cs_promo_image_align == 'top-center'){echo 'selected="selected"';}?> value="top-center">Top Center</option>
            </select>
          </li>
        </ul>

        <ul class="form-elements">
          <li class="to-label">
            <label>Title</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_promobox_title[]" class="txtfield" value="<?php echo cs_allow_special_char($cs_promobox_title)?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Title Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_promobox_title_color[]" class="bg_color" value="<?php echo esc_attr($cs_promobox_title_color)?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Content(s)</label>
          </li>
          <li class="to-field">
            <textarea  name="cs_promobox_contents[]" rows="10" class="textarea" data-content-text="cs-shortcode-textarea"><?php echo esc_textarea($cs_promobox_contents);?></textarea>
            <p>Enter content here</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Content Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_promobox_content_color[]" class="bg_color" value="<?php echo esc_attr($cs_promobox_content_color)?>" />
          </li>
        </ul>
        
        <ul class="form-elements">
          <li class="to-label">
            <label>Link Title</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_link_title[]" class="txtfield" value="<?php echo cs_allow_special_char($cs_link_title);;?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Link URL</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_link[]" class="txtfield" value="<?php echo esc_url($cs_link);?>" />
            <p>Give external/internal promobox url</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Button Background Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_promobox_btn_bg_color[]" class="bg_color" value="<?php echo esc_attr($cs_promobox_btn_bg_color)?>" />
          </li>
        </ul>
        <?php 
			if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
				cs_shortcode_custom_dynamic_classes($cs_promobox_class,$cs_promobox_animation,'','cs_promobox');
			}
		?>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$cs_name));?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="promobox" />
          <input type="button" value="Save" style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))"/>
        </li>
      </ul>
      <?php }?>
    </div>
  </div>
</div>
<?php
		if ( $die <> 1 ) die();
	}
	add_action('wp_ajax_cs_pb_promobox', 'cs_pb_promobox');
}

//=====================================================================
// Slider Shortcode Builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_slider' ) ) {
	function cs_pb_slider($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		$cs_image_num = 0;
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_slider';
			$cs_parseObject = new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
			global $cs_node, $cs_counter_node;
			$cs_defaults = array('cs_column_size' => '1/1','cs_slider_header_title'=>'', 'cs_slider'=>'', 'cs_slider_id'=>'');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_atts_content = $cs_output['0']['content'];
			else 
				$cs_atts_content = array();
			
			if(is_array($cs_atts_content))
				$cs_slider_num = count($cs_atts_content);
			
			$cs_slider_element_size = '25';
			
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_slider';
			$cs_coloumn_class = 'column_'.$cs_slider_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="slider" data="<?php echo element_size_data_array_index($cs_slider_element_size)?>">
  <?php cs_element_setting($cs_name,$cs_counter,$cs_slider_element_size,'','picture-o');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_slider {{attributes}}]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Slider Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <ul class="form-elements">
          <li class="to-label">
            <label>Slider Section Title</label>
          </li>
          <li class="to-field">
            <div class="input-sec">
              <input type="text" name="cs_slider_header_title[]" class="txtfield" value="<?php echo cs_allow_special_char(htmlspecialchars($cs_slider_header_title));?>" />
            </div>
            <div class="left-info">
              <p>Please enter slider header title.</p>
            </div>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Choose Slider</label>
          </li>
          <li class="to-field">
            <div class="input-sec">
              <div class="select-style">
                <select name="cs_slider_id[]" id="cs_slider_id<?php echo intval($cs_counter)?>" class="dropdown">
                 <?php
						if(class_exists('RevSlider') && class_exists('cs_RevSlider')) {
							$cs_slider = new cs_RevSlider();
							$cs_arrSliders = $cs_slider->getAllSliderAliases();
							foreach ( $cs_arrSliders as $key => $entry ) {
								?>
								<option <?php cs_selected($cs_slider_id,$entry['alias']) ?> value="<?php echo cs_allow_special_char($entry['alias']);?>"><?php echo cs_allow_special_char($entry['title']) ;?></option>
								<?php
							}
						}
					?>
                </select>
              </div>
            </div>
          </li>
        </ul>
        
      </div>
      <script>
			var cs_slider_type	= jQuery( "#cs_slider_type<?php echo esc_js($cs_counter);?>" ).val();
			cs_toggle_height(cs_slider_type,'<?php echo esc_js($cs_counter)?>');
		</script>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$cs_name));?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="slider" />
          <input type="button" value="Save" style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))"/>
        </li>
      </ul>
      <?php }?>
    </div>
  </div>
</div>
<?php
		if ( $die <> 1 ) die();
	}
	add_action('wp_ajax_cs_pb_slider', 'cs_pb_slider');
}

?>