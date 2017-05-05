<?php
/**
 * File Type: Content Blockd Shortcode Elements
 */

/** 
 * @Information Box html form for page builder
 */
if ( ! function_exists( 'cs_pb_infobox' ) ) {
	function cs_pb_infobox($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$cs_counter = $_POST['counter'];
		$cs_info_list_num = 0;
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_infobox|infobox_item';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array('cs_column_size'=>'1/1', 'cs_infobox_section_title' => '', 'cs_infobox_title' => '','cs_infobox_bg_color' => '','cs_infobox_list_text_color'=>'','cs_infobox_class' => '','cs_infobox_animation' => '');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_atts_content = $cs_output['0']['content'];
			else 
				$cs_atts_content = array();
			if(is_array($cs_atts_content))
					$cs_info_list_num = count($cs_atts_content);
			$cs_infobox_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_infobox';
			$cs_coloumn_class = 'column_'.$cs_infobox_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
		
	?>

<div id="<?php echo cs_allow_special_char($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo cs_allow_special_char($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="infobox" data="<?php echo element_size_data_array_index($cs_infobox_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_infobox_element_size,'','info-circle');?>
  <div class="cs-wrapp-class-<?php echo cs_allow_special_char($cs_counter)?> <?php echo cs_allow_special_char($cs_shortcode_element);?>" id="<?php echo cs_allow_special_char($cs_name.$cs_counter)?>" data-shortcode-template="[cs_infobox {{attributes}}]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Infobox Options</h5>
      <a href="javascript:removeoverlay('<?php echo cs_allow_special_char($cs_name.$cs_counter)?>','<?php echo cs_allow_special_char($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
     <div class="cs-clone-append cs-pbwp-content" >
       <div class="cs-wrapp-tab-box">
        <div id="shortcode-item-<?php echo esc_attr($cs_counter);?>" data-shortcode-template="{{child_shortcode}} [/cs_infobox]" data-shortcode-child-template="[infobox_item {{attributes}}] {{content}} [/infobox_item]">
          <div class="cs-wrapp-clone cs-shortcode-wrapp cs-disable-true" data-template="[cs_infobox {{attributes}}]">
            <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
            <ul class="form-elements">
              <li class="to-label">
                <label>Section Title</label>
              </li>
              <li class="to-field">
                <input  name="cs_infobox_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_infobox_section_title);?>"   />
                <p> This is used for the one page navigation, to identify the section below. Give a title  </p>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Title</label>
              </li>
              <li class="to-field">
                <input type="text" name="cs_infobox_title[]" class="txtfield" value="<?php echo cs_allow_special_char($cs_infobox_title);?>" />
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Background Color</label>
              </li>
              <li class="to-field">
                <input type="text" name="cs_infobox_bg_color[]" class="bg_color" value="<?php echo esc_attr($cs_infobox_bg_color);?>" />
                <div class="left-box">
                	<p>Provide a hex background colour code here (include #)</p>
                </div>
              </li>
            </ul>
            <ul class='form-elements'>
              <li class='to-label'>
                <label>Text Color:</label>
              </li>
              <li class='to-field'>
                <div class='input-sec'>
                  <input class='bg_color' type='text' name='cs_infobox_list_text_color[]' value="<?php echo esc_attr($cs_infobox_list_text_color); ?>" />
                  <div class="left-box">
                  	<p>Provide a hex colour code here (include #) if you want to override the default </p>
                  </div>
                </div>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Class</label>
              </li>
              <li class="to-field">
                <input type="text" name="cs_infobox_class[]" class="txtfield"  value="<?php echo esc_attr($cs_infobox_class)?>" />
                <p>Use this option if you want to use specified id for this element</p>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Animation Class</label>
              </li>
              <li class="to-field select-style">
                <select class="dropdown" name="cs_infobox_animation[]">
                  <option value="">Select Animation</option>
                  <?php 
						$cs_animation_array = cs_animation_style();
						foreach($cs_animation_array as $animation_key=>$animation_value){
							echo '<optgroup label="'.$animation_key.'">';	
							foreach($animation_value as $key=>$value){
								$cs_active_class = '';
								if($cs_infobox_animation == $key){$cs_active_class = 'selected="selected"';}
								echo '<option value="'.$key.'" '.$cs_active_class.'>'.$value.'</option>';
							}
						}
					?>
                </select>
                <p>Select Entrance animation type from the dropdown </p>
              </li>
            </ul>
          </div>
          <?php
		  if ( isset($cs_info_list_num) && $cs_info_list_num <> '' && isset($cs_atts_content) && is_array($cs_atts_content)){
							
			foreach ( $cs_atts_content as $cs_infobox_item ){
				
				$cs_rand_id = rand(45,996650);
				$cs_icon_rand_id = rand(5546,990978);
				$cs_infobox_list_description = $cs_infobox_item['content'];
				$cs_defaults = array('cs_infobox_list_icon'=>'','cs_infobox_list_color'=>'','cs_infobox_list_title'=>'');
				foreach($cs_defaults as $key=>$values){
					if(isset($cs_infobox_item['atts'][$key]))
						$$key = $cs_infobox_item['atts'][$key];
					else 
						$$key =$values;
				 }
			?>
          <div class='cs-wrapp-clone cs-shortcode-wrapp' id="cs_infobox_<?php echo cs_allow_special_char($cs_rand_id);?>">
            <header>
              <h4><i class='icon-arrows'></i>Infobox Item(s)</h4>
              <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
            <ul class='form-elements'>
              <li class='to-label'>
                <label>Info Box IcoMoon Icon:</label>
              </li>
              <li class="to-field" id="cs_infobox_<?php echo cs_allow_special_char($cs_icon_rand_id);?>">
                <?php cs_fontawsome_icons_box($cs_infobox_list_icon,$cs_rand_id, 'cs_infobox_list_icon');?>
              </li>
            </ul>
            <ul class='form-elements'>
              <li class='to-label'>
                <label>Icon Color:</label>
              </li>
              <li class='to-field'>
                <div class='input-sec'>
                  <input class='bg_color' type='text' name='cs_infobox_list_color[]' value="<?php echo cs_allow_special_char($cs_infobox_list_color); ?>" />
                </div>
              </li>
            </ul>
            <ul class='form-elements'>
              <li class='to-label'>
                <label>Title:</label>
              </li>
              <li class='to-field'>
                <div class='input-sec'>
                  <input class='txtfield' type='text' name='cs_infobox_list_title[]' value="<?php echo cs_allow_special_char($cs_infobox_list_title); ?>" />
                </div>
              </li>
            </ul>
            <ul class='form-elements'>
              <li class='to-label'>
                <label>Short Description:</label>
              </li>
              <li class='to-field'>
                <div class='input-sec'>
                  <textarea name='cs_infobox_list_description[]' rows="8" cols="20" data-content-text="cs-shortcode-textarea"><?php echo cs_allow_special_char($cs_infobox_list_description);?></textarea>
                </div>
              </li>
            </ul>
          </div>
          <?php
				}
			}
		?>
        </div>
        <div class="hidden-object">
          <input type="hidden" name="info_list_num[]" value="<?php echo (int)$cs_info_list_num;?>" class="fieldCounter"  />
        </div>
        <div class="wrapptabbox" style="padding:0">
          <div class="opt-conts">
            <ul class="form-elements noborder">
              <li class="to-field"> <a href="#" class="add_servicesss cs-main-btn" onclick="cs_shortcode_element_ajax_call('infobox_items', 'shortcode-item-<?php echo cs_allow_special_char($cs_counter);?>', '<?php echo cs_allow_special_char(admin_url('admin-ajax.php'));?>')"><i class="icon-plus-circle"></i>Add Item</a> </li>
               <div id="loading" class="shortcodeload"></div>
            </ul>
            <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){ ?>
            <ul class="form-elements insert-bg">
              <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$cs_name));?>','shortcode-item-<?php echo cs_allow_special_char($cs_counter);?>','<?php echo cs_allow_special_char($cs_filter_element);?>')" >INSERT</a> </li>
            </ul>
            <div id="results-shortocde"></div>
            <?php } else { ?>
            <ul class="form-elements noborder">
              <li class="to-label"></li>
              <li class="to-field">
                <input type="hidden" name="cs_orderby[]" value="infobox" />
                <input type="button" value="Save" style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))" />
              </li>
            </ul>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
		if ( $die <> 1 ) die();
	}
	add_action('wp_ajax_cs_pb_infobox', 'cs_pb_infobox');
}

/** 
 * @Icons html form for page builder
 */ 
if ( ! function_exists( 'cs_pb_icons' ) ) {
	function cs_pb_icons($die = 0){
		global $cs_node, $count_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_icons';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array( 'cs_font_type' => '','cs_icon_view' => '','cs_font_size' => '','cs_icon_color' => '','cs_icon_bg_color' => '','cs_font_icon' => '','cs_icons_class' => '','cs_icons_animation' => '');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			
			$cs_icons_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_icons';
			$cs_coloumn_class = 'column_'.$cs_icons_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
		$cs_rand_counter = cs_generate_random_string(10);
	?>
<script>
	cs_toggle_alerts();
</script>
<div id="<?php echo esc_attr($cs_name.$cs_counter);?>_del" class="column parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="icons" data="<?php echo element_size_data_array_index($cs_icons_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_icons_element_size,'','empire');?>
  <div class="cs-wrapp-class-<?php echo esc_attr($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_icons {{attributes}}]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Icon Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_attr($cs_name.$cs_counter)?>','<?php echo esc_attr($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <ul class="form-elements">
          <li class="to-label">
            <label>Choose View</label>
          </li>
          <li class="to-field select-style">
            <select class="cs_icon_view" id="cs_icon_view" name="cs_icon_view[]" onchange="cs_icon_toggle_view(this.value,'<?php echo esc_attr($cs_rand_counter);?>', jQuery(this))">
              <option <?php if($cs_icon_view == 'bg_style'){echo 'selected="selected"';}?> value="bg_style">Background Style</option>
              <option <?php if($cs_icon_view == 'border_style'){echo 'selected="selected"';}?> value="border_style">Border Style</option>
            </select>
          </li>
        </ul>
        
        <ul class="form-elements">
          <li class="to-label">
            <label>Icon Type</label>
          </li>
          <li class="to-field select-style">
            <select class="cs_font_type" id="cs_font_type" name="cs_font_type[]">
              <option <?php if($cs_font_type == 'circle'){echo 'selected="selected"';}?> value="circle">Circle</option>
              <option <?php if($cs_font_type == 'square'){echo 'selected="selected"';}?> value="square">Square</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Font Size</label>
          </li>
          <li class="to-field select-style">
            <select class="cs_font_size" id="cs_font_size" name="cs_font_size[]">
              <option <?php if($cs_font_size == 'small'){echo 'selected="selected"';}?> value="small">Small</option>
              <option <?php if($cs_font_size == 'medium'){echo 'selected="selected"';}?> value="medium">Medium</option>
              <option <?php if($cs_font_size == 'large'){echo 'selected="selected"';}?> value="large">Large</option>
            </select>
            <p>Select font size</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Icon Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_icon_color[]" class="txtfield bg_color" value="<?php echo esc_attr($cs_icon_color);?>" />
            <div class="left-info">
            <p>Provide a hex colour code, If you want to override the default</p>
            </div>
          </li>
        </ul>
        <ul class="form-elements" id="selected_cs_icon_view_<?php echo esc_attr($cs_rand_counter)?>">
          <li class="to-label">
            <label><div id="label-icon"><?php echo trim($cs_icon_view) == '' || $cs_icon_view == 'bg_style' ? 'Icon Background Color' : 'Border Color' ;?></div></label>
            
          </li>
          <li class="to-field">
            <input type="text" name="cs_icon_bg_color[]" class="txtfield bg_color" value="<?php echo esc_attr($cs_icon_bg_color)?>" />
            <div class="left-info">
            <p>Add a hex background colour code, If you want to override the default</p>
            </div>
          </li>
        </ul>
        <ul class='form-elements' id="cs_infobox_<?php echo esc_attr($cs_name.$cs_counter);?>">
          <li class='to-label'>
            <label>IcoMoon Icon:</label>
          </li>
          <li class="to-field">
            <input type="hidden" class="cs-search-icon-hidden" name="cs_font_icon[]" value="<?php echo esc_attr($cs_font_icon);?>" >
            
            <?php cs_fontawsome_icons_box($cs_font_icon,$cs_name.$cs_counter, 'cs_font_icon');?>
            <div class="left-info">
            <p> select the fontawsome Icons you would like to add to your menu items</p>
            </div>
          </li>
        </ul>
        <?php 
		if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
			cs_shortcode_custom_dynamic_classes($cs_icons_class,$cs_icons_animation,'','cs_icons');
		}
		?>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$cs_name));?>','<?php echo esc_js($cs_name.$cs_counter);?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="icons" />
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
	add_action('wp_ajax_cs_pb_icons', 'cs_pb_icons');
}

/** 
 * @Google map html form for page builder start
 */
if ( ! function_exists( 'cs_pb_map' ) ) {
	function cs_pb_map($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$cs_counter = $_POST['counter'];
 		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_map';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array('cs_map_section_title'=>'','cs_map_title'=>'','cs_map_height'=>'','cs_map_lat'=>'-0.127758','cs_map_lon'=>'51.507351','cs_map_zoom'=>'','cs_map_type'=>'','cs_map_info'=>'','cs_map_info_width'=>'','cs_map_info_height'=>'','cs_map_marker_icon'=>'','cs_map_show_marker'=>'true','cs_map_controls'=>'','cs_map_draggable' => '','cs_map_scrollwheel' => '','cs_map_conactus_content' => '','cs_map_border' => '','cs_map_color' => '','cs_map_border_color' => '','cs_map_class' => '','cs_map_animation' => '');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_atts_content = $cs_output['0']['content'];
			else 
				$cs_atts_content = array();
 			$cs_map_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_map';
			$cs_coloumn_class = 'column_'.$cs_map_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	$cs_rand_string = $cs_counter.''.cs_generate_random_string(3);	
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="blog" data="<?php echo element_size_data_array_index($cs_map_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_map_element_size,'','globe');?>
  <div class="cs-wrapp-class-<?php echo esc_attr($cs_counter);?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_map {{attributes}}]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Map Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
        <ul class="form-elements">
          <li class="to-label">
            <label>Section Title</label>
          </li>
          <li class="to-field">
            <input  name="cs_map_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_map_section_title)?>"   />
            <p> This is used for the one page navigation, to identify the section below. Give a title</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Title</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_map_title[]" class="txtfield" value="<?php echo cs_allow_special_char($cs_map_title)?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Map Height</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_map_height[]" class="txtfield" value="<?php echo esc_attr($cs_map_height)?>" />
            <p>Map height set here</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Latitude</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_map_lat[]" class="txtfield" value="<?php echo esc_attr($cs_map_lat)?>" />
            <p>The map will appear only if this field is filled correctly</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Longitude</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_map_lon[]" class="txtfield" value="<?php echo esc_attr($cs_map_lon)?>" />
            <p>The map will appear only if this field is filled correctly</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Zoom</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_map_zoom[]" class="txtfield" value="<?php echo esc_attr($cs_map_zoom)?>" />
            <p></p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Map Types</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_map_type[]" class="dropdown" >
              <option <?php if($cs_map_type=="ROADMAP")echo "selected";?> >ROADMAP</option>
              <option <?php if($cs_map_type=="HYBRID")echo "selected";?> >HYBRID</option>
              <option <?php if($cs_map_type=="SATELLITE")echo "selected";?> >SATELLITE</option>
              <option <?php if($cs_map_type=="TERRAIN")echo "selected";?> >TERRAIN</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Info Text</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_map_info[]" class="txtfield" value="<?php echo esc_attr($cs_map_info)?>" />
            <p>Enter the marker content</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Info Max Width</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_map_info_width[]" class="txtfield" value="<?php echo esc_attr($cs_map_info_width)?>" />
            <p>set max width for the google map</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Info Max Height</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_map_info_height[]" class="txtfield" value="<?php echo esc_attr($cs_map_info_height)?>" />
            <p>set max height for the google map</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Marker Icon Path</label>
          </li>
          <li class="to-field">
            <input id="cs_map_marker_icon<?php echo esc_attr($cs_rand_string)?>" name="cs_map_marker_icon[]" type="hidden" class="" value="<?php echo esc_attr($cs_map_marker_icon);?>"/>
            <label class="browse-icon"><input name="cs_map_marker_icon<?php echo esc_attr($cs_rand_string)?>"  type="button" class="uploadMedia left" value="Browse"/></label>
            <div class="left-info"><p>Give a link for your marker icon</p></div>
          </li>
        </ul>
        <div class="page-wrap" style="overflow:hidden; display:<?php echo esc_attr($cs_map_marker_icon) && trim($cs_map_marker_icon) !='' ? 'inline' : 'none';?>" id="cs_map_marker_icon<?php echo esc_attr($cs_rand_string);?>_box" >
          <div class="gal-active">
            <div class="dragareamain" style="padding-bottom:0px;">
              <ul id="gal-sortable">
                <li class="ui-state-default" id="">
                  <div class="thumb-secs"> <img src="<?php echo esc_url($cs_map_marker_icon);?>"  id="cs_map_marker_icon<?php echo esc_attr($cs_rand_string);?>_img" width="100" height="150"  />
                    <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_map_marker_icon<?php echo esc_js($cs_rand_string)?>')" class="delete"></a> </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <ul class="form-elements">
          <li class="to-label">
            <label>Show Marker</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_map_show_marker[]" class="dropdown" >
              <option value="true" <?php if($cs_map_show_marker=="true")echo "selected";?> >On</option>
              <option value="false" <?php if($cs_map_show_marker=="false")echo "selected";?> >Off</option>
            </select>
            <p>Set marker on/off for the map</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Disable Map Controls</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_map_controls[]" class="dropdown" >
              <option value="false" <?php if($cs_map_controls=="false")echo "selected";?> >Off</option>
              <option value="true" <?php if($cs_map_controls=="true")echo "selected";?> >On</option>
            </select>
            <p>You can set map control disable/enable</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Draggable</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_map_draggable[]" class="dropdown" >
              <option value="true" <?php if($cs_map_draggable=="true")echo "selected";?> >On</option>
              <option value="false" <?php if($cs_map_draggable=="false")echo "selected";?> >Off</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Scroll Wheel</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_map_scrollwheel[]" class="dropdown" >
              <option value="true" <?php if($cs_map_scrollwheel=="true")echo "selected";?> >On</option>
              <option value="false" <?php if($cs_map_scrollwheel=="false")echo "selected";?> >Off</option>
            </select>
            <p>Set scroll wheel</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Border</label>
          </li>
          <li class="to-field select-style">
            <select class="dropdown" name="cs_map_border[]">
              <option <?php if($cs_map_border == 'yes'){echo 'selected="selected"';}?> value="yes">Yes</option>
              <option <?php if($cs_map_border == 'no'){echo 'selected="selected"';}?> value="no">No</option>
            </select>
            <p>Set border for map</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Border Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_map_border_color[]" class="bg_color" value="<?php echo esc_attr($cs_map_border_color);?>" />
            <div class="left-info">
            <p>If you will select a border than select the border color</p>
            </div>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Map Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_map_color[]" class="bg_color" value="<?php echo esc_attr($cs_map_color);?>" />
            <div class="left-info">
            <p></p>
            </div>
          </li>
        </ul>
        <?php 
		if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
			cs_shortcode_custom_dynamic_classes($cs_map_class,$cs_map_animation,'','cs_map');
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
          <input type="hidden" name="cs_orderby[]" value="map" />
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
add_action('wp_ajax_cs_pb_map', 'cs_pb_map');
}

/** 
 * @Offer Slider html form for page builder start
 */
if ( ! function_exists( 'cs_pb_offerslider' ) ) {
	function cs_pb_offerslider($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$cs_counter = $_POST['counter'];
 		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_offerslider|offer_item';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array('cs_column_size'=>'1/1','cs_offerslider_section_title' => '','cs_offerslider_class' => '','cs_offerslider_animation' => '');
		if(isset($cs_output['0']['atts']))
			$cs_atts = $cs_output['0']['atts'];
		else 
			$cs_atts = array();
			
		if(isset($cs_output['0']['content']))
			$cs_atts_content = $cs_output['0']['content'];
		else 
			$cs_atts_content = array();
			
		if(is_array($cs_atts_content))
				$cs_offerslider_num = count($cs_atts_content);
					
		$cs_offerslider_element_size = '50';
		foreach($cs_defaults as $key=>$values){
			if(isset($cs_atts[$key]))
				$$key = $cs_atts[$key];
			else 
				$$key =$values;
		 }
		
		$cs_name = 'cs_pb_offerslider';
		$cs_coloumn_class = 'column_'.$cs_offerslider_element_size;
		
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
    <div id="<?php echo esc_attr($cs_name.$cs_counter);?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="offerslider" data="<?php echo element_size_data_array_index($cs_offerslider_element_size)?>" >
			<?php cs_element_setting($cs_name,$cs_counter,$cs_offerslider_element_size, '', 'trophy',$type='');?>
			<div class="cs-wrapp-class-<?php echo esc_attr($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter);?>" style="display: none;">
				<div class="cs-heading-area">
					<h5>Edit Offer Slider Options</h5>
					<a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter);?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a>
				</div>
					<div class="cs-clone-append cs-pbwp-content">
                    <div class="cs-wrapp-tab-box">
                        	<div id="shortcode-item-<?php echo esc_attr($cs_counter);?>" data-shortcode-template="{{child_shortcode}} [/cs_offerslider]" data-shortcode-child-template="[offer_item {{attributes}}] {{content}} [/offer_item]">
                        		<div class="cs-wrapp-clone cs-shortcode-wrapp cs-disable-true" data-template="[cs_offerslider {{attributes}}]">
                                <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
									?>
                                    <ul class="form-elements">
                                        <li class="to-label"><label>Size</label></li>
                                        <li class="to-field select-style">
                                            <select class="cs_column_size" id="cs_column_size" name="cs_column_size[]">
                                                <option value="1/1" <?php if($cs_column_size == '1/1'){echo 'selected="selected"';}?>>Full width</option>
                                                <option value="1/2" <?php if($cs_column_size == '1/2'){echo 'selected="selected"';}?>>One half</option>
                                                <option value="2/3" <?php if($cs_column_size == '2/3'){echo 'selected="selected"';}?>>Two third</option>
                                                <option value="3/4" <?php if($cs_column_size == '3/4'){echo 'selected="selected"';}?>>Three fourth</option>
                                            </select>
                                            <p>Select column width. This width will be calculated depend page width</p>
                                        </li>                  
                                    </ul>
                                    <?php
									}
									?>
                               <ul class="form-elements">
                                  <li class="to-label">
                                    <label>Section Title</label>
                                  </li>
                                  <li class="to-field">
                                    <input  name="cs_offerslider_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_offerslider_section_title);?>"   />
                                    <p> This is used for the one page navigation, to identify the section below. Give a title </p>
                                  </li>
                                </ul>
                               <?php  
							   	if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
									cs_shortcode_custom_dynamic_classes($cs_offerslider_class,$cs_offerslider_animation,'','cs_offerslider');
								}
								?>
                        	</div>
                            <?php
							if ( isset($cs_offerslider_num) && $cs_offerslider_num <> '' && isset($cs_atts_content) && is_array($cs_atts_content)){
							
								foreach ( $cs_atts_content as $cs_offerslider){
									
									$cs_rand_string = $cs_counter.''.cs_generate_random_string(3);
									$cs_offerslider_text = $cs_offerslider['content'];
									$cs_defaults = array( 'cs_slider_image' => '','cs_slider_title' => '','cs_slider_contents' => '','cs_readmore_link' => '','cs_offerslider_link_title' => '');
									
									foreach($cs_defaults as $key=>$values){
										if(isset($cs_offerslider['atts'][$key]))
											$$key = $cs_offerslider['atts'][$key];
										else 
											$$key =$values;
									 }
									?>
									<div class='cs-wrapp-clone cs-shortcode-wrapp'  id="cs_infobox_<?php echo esc_attr($cs_rand_string);;?>">
										<header><h4><i class='icon-arrows'></i>Testimonial</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
										<ul class="form-elements">
                                          <li class="to-label">
                                            <label>Image</label>
                                          </li>
                                          <li class="to-field">
                                            <input id="cs_slider_image<?php echo esc_attr($cs_rand_string)?>" name="cs_slider_image[]" type="hidden" class="" value="<?php echo esc_url($cs_slider_image);?>"/>
                                            <input name="cs_slider_image<?php echo esc_attr($cs_rand_string)?>"  type="button" class="uploadMedia left" value="Browse"/>
                                          </li>
                                        </ul>
                                        <div class="page-wrap" style="overflow:hidden; display:<?php echo esc_url($cs_slider_image) && trim($cs_slider_image) !='' ? 'inline' : 'none';?>" id="cs_slider_image<?php echo esc_attr($cs_counter);?>_box" >
                                          <div class="gal-active">
                                            <div class="dragareamain" style="padding-bottom:0px;">
                                              <ul id="gal-sortable">
                                                <li class="ui-state-default" id="">
                                                  <div class="thumb-secs"> <img src="<?php echo esc_url($cs_slider_image);?>"  id="cs_slider_image<?php echo esc_attr($cs_rand_string);?>_img" width="100" height="150"  />
                                                    <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_slider_image<?php echo esc_attr($cs_rand_string);?>')" class="delete"></a> </div>
                                                  </div>
                                                </li>
                                              </ul>
                                            </div>
                                          </div>
                                        </div>
                                        <ul class="form-elements">
                                          <li class="to-label">
                                            <label>Title</label>
                                          </li>
                                          <li class="to-field">
                                            <input type="text" name="cs_slider_title[]" class="txtfield" value="<?php echo cs_allow_special_char($cs_slider_title);?>" />
                                          </li>
                                        </ul>
                                        <ul class="form-elements">
                                          <li class="to-label">
                                            <label>Content(s)</label>
                                          </li>
                                          <li class="to-field">
                                            <textarea name="cs_slider_contents[]" data-content-text="cs-shortcode-textarea"><?php echo esc_textarea($cs_offerslider_text);?></textarea>
                                            <p>Enter your content.</p>
                                          </li>
                                        </ul>
                                        <ul class="form-elements">
                                          <li class="to-label">
                                            <label>Read More Link</label>
                                          </li>
                                          <li class="to-field">
                                            <input type="text" name="cs_readmore_link[]" class="txtfield" value="<?php echo esc_attr($cs_readmore_link)?>" />
                                          </li>
                                        </ul>
                                        <ul class="form-elements">
                                          <li class="to-label">
                                            <label>Link Title</label>
                                          </li>
                                          <li class="to-field">
                                            <input type="text" name="cs_offerslider_link_title[]" class="txtfield" value="<?php echo cs_allow_special_char($cs_offerslider_link_title);?>" />
                                            <p>give the link title here</p>
                                          </li>
                                        </ul>
                                        
								</div>
							<?php
								}
							}
							?>
                        </div>
                   		<div class="hidden-object"><input type="hidden" name="offerslider_num[]" value="<?php echo (int)$cs_offerslider_num?>" class="fieldCounter"/></div>
                        <div class="wrapptabbox" style="padding:0">
                            <div class="opt-conts">
                                <ul class="form-elements noborder">
                                    <li class="to-field">
                                    <a href="#" class="add_servicesss cs-main-btn" onclick="cs_shortcode_element_ajax_call('offerslider', 'shortcode-item-<?php echo esc_attr($cs_counter);?>', '<?php echo admin_url('admin-ajax.php');?>')"><i class="icon-plus-circle"></i>Add Offer</a>
                                     <div id="loading" class="shortcodeload"></div>
                                    </li>
                                </ul>
                                <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
                                        <ul class="form-elements insert-bg">
                                            <li class="to-field">
                                                <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$cs_name));?>','shortcode-item-<?php echo esc_js($cs_counter);?>','<?php echo esc_js($cs_filter_element);?>')" >INSERT</a>
                                            </li>
                                        </ul>
                                        <div id="results-shortocde"></div>
                                    <?php } else {?>
                                    <ul class="form-elements noborder">
                                        <li class="to-label"></li>
                                        <li class="to-field">
                                            <input type="hidden" name="cs_orderby[]" value="offerslider" />
                                            <input type="button" value="Save" style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))" />
                                        </li>
                                    </ul>
                                   <?php }?>
                            </div>
                        </div>
					 </div>			
				</div>
		   </div>
		</div>

<?php
		if ( $die <> 1 ) die();
	}
	add_action('wp_ajax_cs_pb_offerslider', 'cs_pb_offerslider');
}

/** 
 * @Spacer html form for page builder
 */
if ( ! function_exists( 'cs_pb_spacer' ) ) {
	function cs_pb_spacer($die = 0){
		global $cs_node, $post;
		$shortcode_element = '';
		$filter_element = 'filterdrag';
		$shortcode_view = '';
		$output = array();
		$cs_counter = $_POST['counter'];
 		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$POSTID = '';
			$shortcode_element_id = '';
		} else {
			$POSTID = $_POST['POSTID'];
			$shortcode_element_id = $_POST['shortcode_element_id'];
			$shortcode_str = stripslashes ($shortcode_element_id);
			$PREFIX = 'cs_spacer';
			$parseObject 	= new ShortcodeParse();
			$output = $parseObject->cs_shortcodes( $output, $shortcode_str , true , $PREFIX );
		}
			$defaults = array('cs_spacer_height'=>'25');
			if(isset($output['0']['atts']))
				$atts = $output['0']['atts'];
			else 
				$atts = array();
			
			foreach($defaults as $key=>$values){
				if(isset($atts[$key]))
					$$key = $atts[$key];
				else 
					$$key =$values;
			 }
			$name = 'cs_pb_spacer';
			$coloumn_class = 'column_100';
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$shortcode_element = 'shortcode_element_class';
			$shortcode_view = 'cs-pbwp-shortcode';
			$filter_element = 'ajax-drag';
			$coloumn_class = '';
		}
?>
<div id="<?php echo esc_attr($name.$cs_counter)?>_del" class="column  parentdelete column_100 column_100 <?php echo esc_attr($shortcode_view);?>" item="spacer" data="0" >
  <?php cs_element_setting($name,$cs_counter,'column_100','','arrows-v');?>
  <div class="cs-wrapp-class-<?php echo esc_attr($cs_counter);?> <?php echo esc_attr($shortcode_element);?>" id="<?php echo esc_attr($name.$cs_counter)?>" data-shortcode-template="[cs_spacer {{attributes}}]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Spacer Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($name.$cs_counter)?>','<?php echo esc_js($filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <ul class="form-elements">
          <li class="to-label">
            <label>Height</label>
          </li>
          <li class="to-field">
            <div class="cs-drag-slider" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="<?php echo esc_attr($cs_spacer_height);?>"></div>
            <input  class="cs-range-input"  name="cs_spacer_height[]" type="text" value="<?php echo esc_attr($cs_spacer_height);?>"   />
          </li>
        </ul>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$name));?>','<?php echo esc_js($name.$cs_counter);?>','<?php echo esc_js($filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="spacer" />
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
	add_action('wp_ajax_cs_pb_spacer', 'cs_pb_spacer');
}

?>