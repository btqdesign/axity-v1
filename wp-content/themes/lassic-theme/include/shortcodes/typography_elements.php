<?php
//=====================================================================
// divider html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_divider' ) ) {
	function cs_pb_divider($die = 0){
		global $cs_node, $post;
		
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		
		$counter = $_POST['counter'];
		
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$CS_PREFIX = 'cs_divider';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		
		$cs_defaults = array( 'cs_column_size' => '1/1', 'cs_divider_style' => 'divider1','cs_divider_height' => '1','cs_divider_backtotop' => '','cs_divider_margin_top' => '','cs_divider_margin_bottom' =>'','cs_line' => 'Wide','cs_color'=>'#000', 'cs_divider_class'=>'','cs_divider_animation'=>'');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_atts_content = $cs_output['0']['content'];
			else 
				$cs_atts_content = '';
			$cs_divider_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_divider';
			$cs_coloumn_class = 'column_'.$cs_divider_element_size;
		
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
		
	?>

<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="blog" data="<?php echo element_size_data_array_index($cs_divider_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_divider_element_size, '', 'ellipsis-h',$type='');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_divider {{attributes}}]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Divider Option</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <ul class="form-elements">
          <li class="to-label">
            <label>Style</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_divider_style[]" class="dropdown" >
              <option <?php if($cs_divider_style=="crossy")echo "selected";?> value="crossy" >Crossy</option>
              <option <?php if($cs_divider_style=="plain")echo "selected";?> value="plain" >Plain</option>
              <option <?php if($cs_divider_style=="zigzag")echo "selected";?> value="zigzag" >Zigzag</option>
              <option <?php if($cs_divider_style=="small-zigzag")echo "selected";?> value="small-zigzag" >Small Zigzag</option>
              <option <?php if($cs_divider_style=="3box")echo "selected";?> value="3box" >3 Box</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Back to Top</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_divider_backtotop[]" class="dropdown" >
              <option value="yes" <?php if($cs_divider_backtotop=="yes")echo "selected";?> >Yes</option>
              <option value="no" <?php if($cs_divider_backtotop=="no")echo "selected";?> >No</option>
            </select>
            <p>set back to top from the dropdown</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Margin Top</label>
          </li>
          <li class="to-field">
            <div class="cs-drag-slider" data-slider-min="0" data-slider-max="50" data-slider-step="1" data-slider-value="<?php echo esc_attr($cs_divider_margin_top);?>"></div>
            <input  class="cs-range-input"  name="cs_divider_margin_top[]" type="text" value="<?php echo esc_attr($cs_divider_margin_top);?>"   />
            <p>set margin top for the divider in px</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Margin Bottom</label>
          </li>
          <li class="to-field">
            <div class="cs-drag-slider" data-slider-min="0" data-slider-max="50" data-slider-step="1" data-slider-value="<?php echo esc_attr($cs_divider_margin_bottom);?>"></div>
            <input  class="cs-range-input"  name="cs_divider_margin_bottom[]" type="text" value="<?php echo esc_attr($cs_divider_margin_bottom);?>"   />
            <p>set a margin bottom for the divider in px</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Height</label>
          </li>
          <li class="to-field">
            <div class="cs-drag-slider" data-slider-min="0" data-slider-max="20" data-slider-step="1" data-slider-value="<?php echo esc_attr($cs_divider_height);?>"></div>
            <input  class="cs-range-input"  name="cs_divider_height[]" type="text" value="<?php echo esc_attr($cs_divider_height);?>"   />
            <p>set the divider height</p>
          </li>
        </ul>
        <?php 
			if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
				cs_shortcode_custom_dynamic_classes($cs_divider_class,$cs_divider_animation,'','cs_divider');
			}
		?>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="divider" />
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
	add_action('wp_ajax_cs_pb_divider', 'cs_pb_divider');
}
// divider html form for page builder end

//=====================================================================
// Tooltip html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_tooltip' ) ) {
	function cs_pb_tooltip($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$CS_PREFIX = 'cs_tooltip';
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array( 'column_size' => '1/1', 'cs_tooltip_hover_title' => '','cs_tooltip_data_placement' => 'top','cs_tooltip_class'=>'', 'cs_tooltip_animation'=>'');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_tooltip_content = $cs_output['0']['content'];
			else 
				$cs_tooltip_content = '';
			$cs_tooltip_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_tooltip';
			$cs_coloumn_class = 'column_'.$cs_tooltip_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="tooltip" data="<?php echo element_size_data_array_index($cs_tooltip_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_tooltip_element_size, '', 'comment-o',$type='');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_tooltip {{attributes}}]{{content}}[/cs_tooltip]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Tooltip Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <ul class="form-elements">
          <li class="to-label">
            <label>Hover Title</label>
          </li>
          <li class="to-field">
            <input  name="cs_tooltip_hover_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_tooltip_hover_title)?>"   />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Hover Title</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_tooltip_data_placement[]" class="dropdown" >
              <option <?php if($cs_tooltip_data_placement=="top")echo "selected";?> value="top" >Top</option>
              <option <?php if($cs_tooltip_data_placement=="left")echo "selected";?> value="left" >Left</option>
              <option <?php if($cs_tooltip_data_placement=="bottom")echo "selected";?> value="bottom" >Bottom</option>
              <option <?php if($cs_tooltip_data_placement=="right")echo "selected";?> value="right" >Right</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Content</label>
          </li>
          <li class="to-field">
            <textarea name="tooltip_content[]" data-content-text="cs-shortcode-textarea"><?php echo esc_attr($cs_tooltip_content)?></textarea>
            <p>Enter your content.</p>
          </li>
        </ul>
        <?php 
			if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
				cs_shortcode_custom_dynamic_classes($cs_tooltip_class,$cs_tooltip_animation,'','cs_tooltip');
			}
		?>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="tooltip" />
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
	add_action('wp_ajax_cs_pb_tooltip', 'cs_pb_tooltip');
}
// tooltip html form for page builder end


//=====================================================================
// Flex Column html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_flex_column' ) ) {
	function cs_pb_flex_column($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$CS_PREFIX = 'cs_column';
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
 			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
			
		}

		$cs_defaults = array('cs_flex_column_section_title'=>'','flex_column_bg_color'=>'','flex_column_text_color'=>'','cs_column_class'=>'','cs_column_animation'=>'');
		if(isset($cs_output['0']['atts']))
			$cs_atts = $cs_output['0']['atts'];
		else 
			$cs_atts = array();
		if(isset($cs_output['0']['content']))
			$cs_flex_column_text = $cs_output['0']['content'];
		else 
			$cs_flex_column_text = '';
		$cs_flex_column_element_size = '25';
		foreach($cs_defaults as $key=>$values){
			if(isset($cs_atts[$key]))
				$$key = $cs_atts[$key];
			else 
				$$key =$values;
		 }
		$cs_name = 'cs_pb_flex_column';
		$cs_coloumn_class = 'column_'.$cs_flex_column_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
		?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="flex_column" data="<?php echo element_size_data_array_index($cs_flex_column_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_flex_column_element_size, '', 'columns',$type='');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_column {{attributes}}]{{content}}[/cs_column]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Flex Column Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <?php
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
        <ul class="form-elements">
          <li class="to-label">
            <label>Section Title</label>
          </li>
          <li class="to-field">
            <input  name="cs_flex_column_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_flex_column_section_title);?>"   />
            <p> This is used for the one page navigation, to identify the section below. Give a title </p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Column Text</label>
          </li>
          <li class="to-field">
            <textarea name="flex_column_text[]" data-content-text="cs-shortcode-textarea"><?php echo esc_textarea(cs_custom_shortcode_decode($cs_flex_column_text))?></textarea>
            <p>Enter your content.</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Background Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="flex_column_bg_color[]" class="bg_color"  value="<?php echo esc_attr($flex_column_bg_color);?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Text Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="flex_column_text_color[]" class="bg_color"  value="<?php echo esc_attr($flex_column_text_color);?>" />
          </li>
        </ul>
         <?php 
			if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
				cs_shortcode_custom_dynamic_classes($cs_column_class,$cs_column_animation,'','cs_column');
			}
		?>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="flex_column" />
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
	add_action('wp_ajax_cs_pb_flex_column', 'cs_pb_flex_column');
}
// Flex Column html form for page builder end

//=====================================================================
// dropcap html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_dropcap' ) ) {
	function cs_pb_dropcap($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_dropcap';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array('cs_dropcap_section_title' => '', 'cs_dropcap_style' => 'dropcap','cs_dropcap_bg_color' => '#ec2c3b','cs_dropcap_color' => '#fff','cs_dropcap_size' => '', 'cs_dropcap_class'=>'', 'cs_dropcap_animation'=>'');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_dropcap_content = $cs_output['0']['content'];
			else 
				$cs_dropcap_content = '';
			$cs_dropcap_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_dropcap';
			$cs_coloumn_class = 'column_'.$cs_dropcap_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="blog" data="<?php echo element_size_data_array_index($cs_dropcap_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_dropcap_element_size, '', 'bold',$type='');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_dropcap {{attributes}}]{{content}}[/cs_dropcap]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Dropcap Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
        <ul class="form-elements">
          <li class="to-label">
            <label>Section Title</label>
          </li>
          <li class="to-field">
            <input  name="cs_dropcap_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_dropcap_section_title)?>"   />
            <p> This is used for the one page navigation, to identify the section below. Give a title </p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Style</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_dropcap_style[]">
              <option <?php if($cs_dropcap_style=="box")echo "selected";?> value="box" >Box</option>
              <option <?php if($cs_dropcap_style=="plain")echo "selected";?> value="plain" >Plain</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Font Size</label>
          </li>
          <li class="to-field">
            <div class="cs-drag-slider" data-slider-min="0" data-slider-max="80" data-slider-step="1" data-slider-value="<?php echo esc_attr($cs_dropcap_size)?>"></div>
            <input  class="cs-range-input"  name="cs_dropcap_size[]" type="text" value="<?php echo intval($cs_dropcap_size)?>"   />
            <p>add your font size for the dropcap text</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_dropcap_color[]" class="bg_color"  value="<?php echo esc_attr($cs_dropcap_color);?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Background Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_dropcap_bg_color[]" class="bg_color"  value="<?php echo esc_attr($cs_dropcap_bg_color);?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Content</label>
          </li>
          <li class="to-field">
            <textarea name="dropcap_content[]" data-content-text="cs-shortcode-textarea"><?php echo esc_attr($cs_dropcap_content)?></textarea>
            <p>Enter content here</p>
          </li>
        </ul>
        <?php 
			if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
				cs_shortcode_custom_dynamic_classes($cs_dropcap_class,$cs_dropcap_animation,'','cs_dropcap');
			}
		?>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="dropcap" />
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
	add_action('wp_ajax_cs_pb_dropcap', 'cs_pb_dropcap');
}
// dropcap html form for page builder end

//=====================================================================
// highlight html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_highlight' ) ) {
	function cs_pb_highlight($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_highlight';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array( 'cs_highlight_bg_color' => '','cs_highlight_color' => '','cs_highlight_class' => '','cs_highlight_animation'=>'');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_highlight_content = $cs_output['0']['content'];
			else 
				$cs_highlight_content = '';
			$cs_highlight_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_highlight';
			$cs_coloumn_class = 'column_'.$cs_highlight_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo cs_allow_special_char($cs_coloumn_class);?> <?php echo cs_allow_special_char($cs_shortcode_view);?>" item="highlight" data="<?php echo element_size_data_array_index($cs_highlight_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_highlight_element_size, '', 'pencil',$type='');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo cs_allow_special_char($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>"  data-shortcode-template="[cs_highlight {{attributes}}]{{content}}[/cs_highlight]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Highlight Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <ul class="form-elements">
          <li class="to-label">
            <label>Color</label>
          </li>
          <li class="to-field">
            <div class="pic-color"><input type="text" name="cs_highlight_color[]" class="bg_color" value="<?php echo esc_attr($cs_highlight_color);?>" /></div>
            <p>Provide a hex colour code here (include #) if you want to override the default </p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Background Color</label>
          </li>
          <li class="to-field">
            <div class="pic-color"><input type="text" name="cs_highlight_bg_color[]" class="bg_color" value="<?php echo esc_attr($cs_highlight_bg_color);?>" /></div>
            <p>Provide a hex colour code here (include #) if you want to override the default </p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Content</label>
          </li>
          <li class="to-field">
            <textarea name="highlight_content[]" data-content-text="cs-shortcode-textarea" ><?php echo esc_textarea($cs_highlight_content)?></textarea>
            <p>Enter the content</p>
          </li>
        </ul>
        <?php 
			if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
					cs_shortcode_custom_dynamic_classes($cs_highlight_class,$cs_highlight_animation,'','cs_highlight');
			}
		?>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg noborder">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="highlight" />
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
	add_action('wp_ajax_cs_pb_highlight', 'cs_pb_highlight');
}
// highlight html form for page builder end

//=====================================================================
// Heading html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_heading' ) ) {
	function cs_pb_heading($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_g_fonts = cs_get_google_fonts();
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_heading';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array( 'cs_heading_title' => '','cs_color_title'=>'','cs_heading_color' => '#000', 'cs_class'=>'cs-heading-shortcode', 'cs_heading_style'=>'1','cs_heading_style_type'=>'1', 'cs_heading_size'=>'', 'cs_font_weight'=>'', 'cs_heading_font_style'=>'', 'cs_heading_align'=>'center', 'cs_heading_divider'=>'', 'cs_heading_divider_icon'=>'', 'cs_heading_color' => '', 'cs_heading_content_color' => '', 'cs_heading_animation'=>'');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_heading_content = $cs_output['0']['content'];
			else 
				$cs_heading_content = '';
			$cs_heading_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_heading';
			$cs_coloumn_class = 'column_'.$cs_heading_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
		
		$cs_rand_id = rand(344, 4354590);
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="heading" data="<?php echo element_size_data_array_index($cs_heading_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_heading_element_size, '', 'h-square',$type='');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>"  data-shortcode-template="[cs_heading {{attributes}}]{{content}}[/cs_heading]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Heading Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
        <ul class="form-elements">
          <li class="to-label">
            <label>Title</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_heading_title[]" class="txtfield" value="<?php echo cs_allow_special_char($cs_heading_title);?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Color Title</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_color_title[]" class="txtfield" value="<?php echo cs_allow_special_char($cs_color_title);?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Content</label>
          </li>
          <li class="to-field">
            <textarea name="heading_content[]" rows="8" cols="40" data-content-text="cs-shortcode-textarea"><?php echo esc_textarea($cs_heading_content);?></textarea>
            <p>Enter content here</p>
          </li>

        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Style</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_heading_style[]">
              <option <?php if($cs_heading_style=="1")echo "selected";?> value="1" >h1</option>
              <option <?php if($cs_heading_style=="2")echo "selected";?> value="2" >h2</option>
              <option <?php if($cs_heading_style=="3")echo "selected";?> value="3" >h3</option>
              <option <?php if($cs_heading_style=="4")echo "selected";?> value="4" >h4</option>
              <option <?php if($cs_heading_style=="5")echo "selected";?> value="5" >h5</option>
              <option <?php if($cs_heading_style=="6")echo "selected";?> value="6" >h6</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Font Size</label>
          </li>
          <li class="to-field">
            <div class="cs-drag-slider" data-slider-min="0" data-slider-max="50" data-slider-step="1" data-slider-value="<?php echo intval($cs_heading_size)?>"></div>
            <input  class="cs-range-input"  name="cs_heading_size[]" type="text" value="<?php echo esc_attr($cs_heading_size)?>"   />
            <p>add font size number for the heading</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Align</label>
          </li>
          <li class="to-field select-style">
            <select class="dropdown" name="cs_heading_align[]">
              <option value="left" <?php if($cs_heading_align=='left'){echo 'selected="selected"';}?>>Left</option>
              <option  value="center" <?php if($cs_heading_align=='center'){echo 'selected="selected"';}?>>Center</option>
              <option value="right" <?php if($cs_heading_align=='right'){echo 'selected="selected"';}?>>Right</option>
            </select>
            <p>Align the content position</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Divider On/Off</label>
          </li>
          <li class="to-field select-style">
            <select class="dropdown" name="cs_heading_divider[]">
              <option value="on" <?php if($cs_heading_divider=='on'){echo 'selected="selected"';}?>>On</option>
              <option  value="off" <?php if($cs_heading_divider=='off'){echo 'selected="selected"';}?>>Off</option>
            </select>
            <p>set divider on/off for the list bottom border </p>
          </li>
        </ul>
        <ul class='form-elements' id="cs_infobox_<?php echo esc_attr($cs_rand_id);?>">
          <li class='to-label'>
            <label>Divider Icon:</label>
          </li>
          <li class="to-field">
            <?php cs_fontawsome_icons_box($cs_heading_divider_icon,$cs_rand_id,'cs_heading_divider_icon');?>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Font Style</label>
          </li>
          <li class="to-field select-style">
            <select class="dropdown" name="cs_heading_font_style[]">
              <option value="normal" <?php if($cs_heading_font_style=='normal'){echo 'selected="selected"';}?>>Normal</option>
              <option value="italic" <?php if($cs_heading_font_style=='italic'){echo 'selected="selected"';}?>>Italic</option>
              <option value="oblique" <?php if($cs_heading_font_style=='oblique'){echo 'selected="selected"';}?>>Oblique</option>
            </select>
            <p>select a font style from the drop down</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Heading Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_heading_color[]" class="bg_color"  value="<?php echo esc_attr($cs_heading_color);?>" />
            <div class="left-info">
              <p>heading color for the heading element</p>
            </div>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Content Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_heading_content_color[]" class="bg_color"  value="<?php echo esc_attr($cs_heading_content_color);?>" />
            <div class="left-info">
              <p>set a content color for the heading element</p>
            </div>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Animation Class</label>
          </li>
          <li class="to-field">
            <div class="select-style">
              <select class="dropdown" name="cs_heading_animation[]">
                <option value="">Select Animation</option>
                <?php 
					$cs_animation_array = cs_animation_style();
					foreach($cs_animation_array as $animation_key=>$animation_value){
						echo '<optgroup label="'.$animation_key.'">';	
						foreach($animation_value as $key=>$value){
							$cs_active_class = '';
							if($cs_heading_animation == $key){$cs_active_class = 'selected="selected"';}
							echo '<option value="'.$key.'" '.$cs_active_class.'>'.$value.'</option>';
						}
					}
				 ?>
              </select>
              <p>Select Entrance animation type from the dropdown </p>
            </div>
          </li>
        </ul>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="heading" />
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
	add_action('wp_ajax_cs_pb_heading', 'cs_pb_heading');
}
// Heading html form for page builder end

//=====================================================================
// List Item html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_list' ) ) {
	function cs_pb_list($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		$cs_list_num = 0;
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_list|list_item';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array('cs_list_section_title'=>'','cs_list_type'=>'','cs_list_icon'=>'','cs_border'=>'','cs_list_item'=>'','cs_list_class'=>'','cs_list_animation'=>'');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_atts_content = $cs_output['0']['content'];
			else 
				$cs_atts_content = array();
			if(is_array($cs_atts_content))
					$cs_list_num = count($cs_atts_content);
			$cs_list_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_list';
			$cs_coloumn_class = 'column_'.$cs_list_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="list" data="<?php echo element_size_data_array_index($cs_list_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_list_element_size, '', 'list-ol',$type='');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit List Style Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-wrapp-tab-box">
      <div class="cs-clone-append cs-pbwp-content" >
        <div id="shortcode-item-<?php echo intval($cs_counter);?>" data-shortcode-template="{{child_shortcode}} [/cs_list]" data-shortcode-child-template="[list_item {{attributes}}] {{content}} [/list_item]">
          <div class="cs-wrapp-clone cs-shortcode-wrapp cs-disable-true" data-template="[cs_list {{attributes}}]">
            <ul class="form-elements">
              <li class="to-label">
                <label>Section Title</label>
              </li>
              <li class="to-field">
                <input  name="cs_list_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_list_section_title)?>"   />
                <p> This is used for the one page navigation, to identify the section below. Give a title </p>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>List Style</label>
              </li>
              <li class="to-field select-style">
                <select class="dropdown" id="cs_list_type_selected" name="cs_list_type[]" onchange="cs_toggle_list(this.value,'cs_slider_height<?php echo esc_attr($cs_name.$cs_counter)?>')">
                  <option value="none" <?php if($cs_list_type =="none")echo "selected";?>>None</option>
                  <option value="icon" <?php if($cs_list_type =="icon")echo "selected";?>>Icon </option>
                  <option value="built" <?php if($cs_list_type =="built")echo "selected";?>>Built</option>
                  <option value="decimal" <?php if($cs_list_type =="decimal") echo "selected";?> >Decimal</option>
       
                  <!-- <option value="custom_icon">Custom Icon</option>-->
                </select>
                <p>set a list style from the dropdown</p>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Border Bottom</label>
              </li>
              <li class="to-field select-style">
                <select class="dropdown" name="cs_border[]">
                  <option <?php if($cs_border == "yes")echo "selected";?> value="yes">Yes</option>
                  <option value="no" <?php if($cs_border == "no")echo "selected";?>>No</option>
                </select>
                <p>set on/off for the list bottom border </p>
              </li>
            </ul>
            <?php 
				if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
					cs_shortcode_custom_dynamic_classes($cs_list_class,$cs_list_animation,'','cs_list');
				}
			?>
          </div>
          <?php
			   if ( isset($cs_list_num) && $cs_list_num <> '' && isset($cs_atts_content) && is_array($cs_atts_content)){
				foreach ( $cs_atts_content as $list_items ){
					$cs_rand_id = $cs_counter.''.cs_generate_random_string(3);
					$cs_list_item = $list_items['content'];
					$cs_defaults = array('cs_list_icon'=>'','cs_cusotm_class'=>'','cs_custom_animation'=>'');
					foreach($cs_defaults as $key=>$values){
						if(isset($list_items['atts'][$key]))
							$$key = $list_items['atts'][$key];
						else 
							$$key =$values;
					}
				?>
                <div class='cs-wrapp-clone cs-shortcode-wrapp'  id="cs_infobox_<?php echo esc_attr($cs_rand_id);?>">
                    <header>
                      <h4><i class='icon-arrows'></i>List Item(s)</h4>
                      <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                    <ul class='form-elements'>
                      <li class='to-label'>
                        <label>List Item:</label>
                      </li>
                      <li class='to-field'>
                        <div class='input-sec'>
                          <input class='txtfield' type='text' name='cs_list_item[]' data-content-text="cs-shortcode-textarea"  value="<?php echo cs_allow_special_char($cs_list_item) ?>" />
                        </div>
                      </li>
                    </ul>
                    <ul class='form-elements' id="cs_infobox_<?php echo esc_attr($cs_name.$cs_counter);?>">
                      <li class='to-label'>
                        <label> IcoMoon Icon:</label>
                      </li>
                      <li class="to-field">
                       <?php cs_fontawsome_icons_box($cs_list_icon,$cs_rand_id,'cs_list_icon');?>
                      </li>
                    </ul>
                  </div>
                <?php
                        }
                    }
                    ?>
        </div>
        <div class="hidden-object">
          <input type="hidden" name="list_num[]" value="<?php echo intval($cs_list_num);?>" class="fieldCounter"  />
        </div>
        <div class="wrapptabbox no-padding-lr">
          <div class="opt-conts">
            <ul class="form-elements">
              <li class="to-field"> <a href="#" class="add_servicesss cs-main-btn" onclick="cs_shortcode_element_ajax_call('list', 'shortcode-item-<?php echo esc_js($cs_counter);?>', '<?php echo admin_url('admin-ajax.php');?>')"><i class="icon-plus-circle"></i>Add List Item</a> </li>
              <div id="loading" class="shortcodeload"></div>
            </ul>
          </div>
          <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
          <ul class="form-elements insert-bg noborder">
            <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','shortcode-item-<?php echo esc_js($cs_counter);?>','<?php echo esc_js($cs_filter_element);?>')" >INSERT</a> </li>
          </ul>
          <div id="results-shortocde"></div>
          <?php } else {?>
          <ul class="form-elements noborder no-padding-lr">
            <li class="to-label"></li>
            <li class="to-field">
              <input type="hidden" name="cs_orderby[]" value="list" />
              <input type="button" value="Save" style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))" />
            </li>
          </ul>
          <?php }?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
		if ( $die <> 1 ) die();
	}
	add_action('wp_ajax_cs_pb_list', 'cs_pb_list');
}
// List Item html form for page builder End

//=====================================================================
// Page Builder Element
//=====================================================================
function cs_get_pagebuilder_element($cs_shortcode_element_id,$CS_POSTID){
		$cs_page_bulider = get_post_meta($CS_POSTID, "cs_page_builder", true);
		if(isset($cs_page_bulider) && $cs_page_bulider<>''){
			$cs_xmlObject = new SimpleXMLElement($cs_page_bulider);
		}
		$cs_shortcode_element_array = explode('_',$cs_shortcode_element_id);
		$section_no = $cs_shortcode_element_array['0'];
		$columnn_no = $cs_shortcode_element_array['1'];
		$section = 0;
		$colummmn = 0;
		foreach ($cs_xmlObject->column_container as $column_container) {
			$section++;
			if($section ==$section_no){
				foreach ($column_container->children() as $column) {
					foreach ($column->children() as $cs_node) {
						$colummmn++;
						if($colummmn ==$columnn_no){
							break;
						}
					}
				}
			}
			break;
		}
		return $cs_node;
}

//=====================================================================
// Message html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_mesage' ) ) {
	function cs_pb_mesage($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$CS_PREFIX = 'cs_message';
		$cs_counter = $_POST['counter'];
		$cs_parseObject 	= new ShortcodeParse();
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		
		$cs_defaults = array( 'cs_column_size' => '1/1', 'cs_msg_section_title' => '', 'cs_message_title' => '','cs_message_type' => '','cs_alert_style' => '','cs_style_type' => '', 'cs_message_icon' => '','cs_title_color' => '','cs_message_box_title' => '','cs_background_color' => '','cs_text_color' => '','cs_button_text' => '','cs_button_link' => '','cs_icon_color' => '','cs_message_close' => '','cs_message_class' => '','cs_message_animation' => '');
			
		if(isset($cs_output['0']['atts']))
			$cs_atts = $cs_output['0']['atts'];
		else 
			$cs_atts = array();
		
		if(isset($cs_output['0']['content']))
			$cs_atts_content = $cs_output['0']['content'];
		else 
			$cs_atts_content = "";
			
		$cs_message_element_size = '25';
		foreach($cs_defaults as $key=>$values){
			if(isset($cs_atts[$key]))
				$$key = htmlentities( $cs_atts[$key], ENT_QUOTES);
			else 
				$$key = htmlentities( $values, ENT_QUOTES);
		 }
		$cs_name = 'cs_pb_mesage';
		$cs_coloumn_class = 'column_'.$cs_message_element_size;
	if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
		$cs_shortcode_element = 'shortcode_element_class';
		$cs_shortcode_view = 'cs-pbwp-shortcode';
		$cs_filter_element = 'ajax-drag';
		$cs_coloumn_class = '';
	}
	
	?>
<div id="<?php echo cs_allow_special_char($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo cs_allow_special_char($cs_coloumn_class);?> <?php echo cs_allow_special_char($cs_shortcode_view);?>" item="mesage" data="<?php echo element_size_data_array_index($cs_message_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_message_element_size, '', 'envelope',$type='');?>
  <div class="cs-wrapp-class-<?php echo cs_allow_special_char($cs_counter)?> <?php echo cs_allow_special_char($cs_shortcode_element);?>" id="<?php echo cs_allow_special_char($cs_name.$cs_counter)?>" data-shortcode-template="[cs_message {{attributes}}]{{content}}[/cs_message]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Message Options</h5>
      <a href="javascript:removeoverlay('<?php echo cs_allow_special_char($cs_name.$cs_counter)?>','<?php echo cs_allow_special_char($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp cs-pbwp-content">
        <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
        <ul class="form-elements">
          <li class="to-label">
            <label>Section Title</label>
          </li>
          <li class="to-field">
            <input  name="cs_msg_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_msg_section_title);?>"   />
            <p> This is used for the one page navigation, to identify the section below. Give a title </p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Title</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_message_title[]" class="txtfield" value="<?php echo cs_allow_special_char($cs_message_title);?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Message Type</label>
          </li>
          <li class="to-field select-style">
            <select id="cs_message_type<?php echo cs_allow_special_char($cs_counter)?>" name="cs_message_type[]" onchange="cs_toggle_alerts(this.value,<?php echo cs_allow_special_char($cs_counter)?>)">
              <option <?php if($cs_message_type=="alert")echo "selected";?> value="alert" >Alert</option>
              <option <?php if($cs_message_type=="message")echo "selected";?> value="message" >Message</option>
            </select>
            <p>Select the display type for the Message</p>
          </li>
        </ul>
        
        <ul class='form-elements' id="cs_infobox_<?php echo cs_allow_special_char($cs_name.$cs_counter);?>">
          <li class='to-label'>
            <label>IcoMoon Icon:</label>
          </li>
          <li class="to-field">
            <?php cs_fontawsome_icons_box( $cs_message_icon ,$cs_name.$cs_counter,'cs_message_icon');?>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Icon Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_icon_color[]" class="bg_color" value="<?php echo esc_attr($cs_icon_color);?>" />
          </li>
        </ul>
        <div id="fancy_active<?php echo intval($cs_counter)?>">
          <ul class="form-elements">
            <li class="to-label">
              <label>Button Text</label>
            </li>
            <li class="to-field">
              <input type="text" name="cs_button_text[]" class="" value="<?php echo esc_attr($cs_button_text)?>" />
            </li>
          </ul>
          <ul class="form-elements">
            <li class="to-label">
              <label>Button Link</label>
            </li>
            <li class="to-field">
              <input type="text" name="cs_button_link[]" class="" value="<?php echo esc_attr($cs_button_link);?>" />
            </li>
          </ul>
          <ul class="form-elements">
            <li class="to-label">
              <label>Message Box Title</label>
            </li>
            <li class="to-field">
              <input type="text" name="cs_message_box_title[]" class="" value="<?php echo esc_attr($cs_message_box_title)?>" />
            </li>
          </ul>
          <ul class="form-elements">
            <li class="to-label">
              <label>Title Color</label>
            </li>
            <li class="to-field">
              <input type="text" name="cs_title_color[]" class="bg_color" value="<?php echo esc_attr($cs_title_color);?>" />
            </li>
          </ul>
        </div>
        <ul class="form-elements">
          <li class="to-label">
            <label>Text Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_text_color[]" class="bg_color" value="<?php echo esc_attr($cs_text_color);?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Background Color</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_background_color[]" class="bg_color" value="<?php echo esc_attr($cs_background_color);?>" />
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Style Type</label>
          </li>
          <li class="to-field select-style">
            <select name="cs_style_type[]" >
              <option <?php if($cs_style_type == "fancy")echo "selected";?> value="fancy" >Fancy</option>
              <option <?php if($cs_style_type == "plain")echo "selected";?> value="plain" >Plain</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Close Button</label>
          </li>
          <li class="to-field">
            <div class="select-style">
              <select name="cs_message_close[]">
                <option <?php if($cs_message_close=="yes")echo "selected";?> value="yes" >Yes</option>
                <option <?php if($cs_message_close=="no")echo "selected";?> value="no" >No</option>
              </select>
              <p>Set close button on/off</p>
            </div>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Text</label>
          </li>
          <li class="to-field">
            <textarea rows="20" cols="40" data-content-text="cs-shortcode-textarea" name="cs_message_text[]"><?php echo esc_textarea($cs_atts_content);?></textarea>
            <p>Enter content here</p>
          </li>
        </ul>
        <?php 
			if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
				cs_shortcode_custom_dynamic_classes($cs_message_class,$cs_message_animation,'','cs_message');
			}
		?>
      </div>
      <script>
			var cs_message_type		= jQuery("#cs_message_type<?php echo intval($cs_counter);?>" ).val();
			var cs_message_style	= jQuery("#cs_message_style<?php echo intval($cs_counter);?>" ).val();
			cs_toggle_alerts(cs_message_type,'<?php echo esc_js($cs_counter)?>');
			cs_toggle_fancybutton(cs_message_style,'<?php echo esc_js($cs_counter)?>');
	</script>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg noborder">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else { ?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="mesage" />
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
	add_action('wp_ajax_cs_pb_mesage', 'cs_pb_mesage');
}
// Message html form for page builder end


//=====================================================================
// Testimonial html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_testimonials' ) ) {
	function cs_pb_testimonials($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$cs_counter = $_POST['counter'];
		$cs_testimonials_num = 0;
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_testimonials|testimonial_item';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array('cs_column_size'=>'1/1','cs_testimonial_text_color'=>'','cs_testimonial_style'=>'','cs_testimonial_text_align'=>'','cs_testimonial_section_title'=>'','cs_testimonial_class'=>'','cs_testimonial_animation'=>'');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_atts_content = $cs_output['0']['content'];
			else 
				$cs_atts_content = array();
			if(is_array($cs_atts_content))
					$cs_testimonials_num = count($cs_atts_content);
			$cs_testimonials_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_testimonials';
			$cs_coloumn_class = 'column_'.$cs_testimonials_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo cs_allow_special_char($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo cs_allow_special_char($cs_coloumn_class);?> <?php echo cs_allow_special_char($cs_shortcode_view);?>" item="testimonials" data="<?php echo element_size_data_array_index($cs_testimonials_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_testimonials_element_size, '', 'comments-o',$type='');?>
  <div class="cs-wrapp-class-<?php echo cs_allow_special_char($cs_counter)?> <?php echo cs_allow_special_char($cs_shortcode_element);?>" id="<?php echo cs_allow_special_char($cs_name.$cs_counter)?>" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Testimonials Options</h5>
      <a href="javascript:removeoverlay('<?php echo cs_allow_special_char($cs_name.$cs_counter)?>','<?php echo cs_allow_special_char($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
      <div class="cs-clone-append cs-pbwp-content">
      <div class="cs-wrapp-tab-box">
        <div id="shortcode-item-<?php echo cs_allow_special_char($cs_counter);?>" data-shortcode-template="{{child_shortcode}} [/cs_testimonials]" data-shortcode-child-template="[testimonial_item {{attributes}}] {{content}} [/testimonial_item]">
          <div class="cs-wrapp-clone cs-shortcode-wrapp cs-disable-true cs-pbwp-content" data-template="[cs_testimonials {{attributes}}]">
            <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
            <ul class="form-elements">
              <li class="to-label">
                <label>Section Title</label>
              </li>
              <li class="to-field">
                <input  name="cs_testimonial_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_testimonial_section_title)?>"   />
                <p> This is used for the one page navigation, to identify the section below. Give a title </p>
              </li>
            </ul>
            <ul class='form-elements'>
              <li class='to-label'>
                <label>Style:</label>
              </li>
              <li class='to-field'>
                <div class='input-sec select-style'>
                  <select name='cs_testimonial_style[]' class='dropdown'>
                    <option value='simple' <?php if($cs_testimonial_style == 'simple'){echo 'selected';}?>>Classic Slider </option>
                    <option value='fancy' <?php if($cs_testimonial_style == 'fancy'){echo 'selected';}?>>Modern Slider </option>
                    <option value='slider' <?php if($cs_testimonial_style == 'slider'){echo 'selected';}?>>Simple Slider </option>
                  </select>
                </div>
                <div class='left-info'>
                  <p>Testimonial Style</p>
                </div>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Align</label>
              </li>
              <li class="to-field select-style">
                <select name="cs_testimonial_text_align[]" class="dropdown" >
                  <option <?php if($cs_testimonial_text_align=="left")echo "selected";?> >left</option>
                  <option <?php if($cs_testimonial_text_align=="right")echo "selected";?> >right</option>
                  <option <?php if($cs_testimonial_text_align=="center")echo "selected";?> >center</option>
                </select>
                <p> This will not apply in Slider View.</p>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Text Color</label>
              </li>
              <li class="to-field">
                <input  name="cs_testimonial_text_color[]" type="text" class="bg_color"  value="<?php echo esc_attr($cs_testimonial_text_color)?>"/>
              </li>
            </ul>
            <?php  
				if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
					cs_shortcode_custom_dynamic_classes($cs_testimonial_class,$cs_testimonial_animation,'','cs_testimonial');
				}
				?>
          </div>
          <?php
			if ( isset($cs_testimonials_num) && $cs_testimonials_num <> '' && isset($cs_atts_content) && is_array($cs_atts_content)){
			
				foreach ( $cs_atts_content as $cs_testimonials ){
					
					$cs_rand_string = $cs_counter.''.cs_generate_random_string(3);
					$cs_testimonial_text = $cs_testimonials['content'];
					$cs_defaults = array('cs_testimonial_author' =>'','cs_testimonial_img' => '','cs_testimonial_company' => '');
					foreach($cs_defaults as $key=>$values){
						if(isset($cs_testimonials['atts'][$key]))
							$$key = $cs_testimonials['atts'][$key];
						else 
							$$key =$values;
					 }
					?>
          <div class='cs-wrapp-clone cs-shortcode-wrapp cs-pbwp-content'  id="cs_infobox_<?php echo cs_allow_special_char($cs_rand_string);?>">
            <header>
              <h4><i class='icon-arrows'></i>Testimonial</h4>
              <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
            <ul class='form-elements'>
              <li class='to-label'>
                <label>Text:</label>
              </li>
              <li class='to-field'>
                <div class='input-sec'>
                  <textarea class='txtfield' data-content-text="cs-shortcode-textarea" name='cs_testimonial_text[]'><?php echo cs_allow_special_char($cs_testimonial_text);?></textarea>
                </div>
              </li>
            </ul>
            <ul class='form-elements'>
              <li class='to-label'>
                <label>Author:</label>
              </li>
              <li class='to-field'>
                <div class='input-sec'>
                  <input class='txtfield' type='text' name='cs_testimonial_author[]' value="<?php echo cs_allow_special_char($cs_testimonial_author);?>" />
                </div>
              </li>
            </ul>
            <ul class='form-elements'>
              <li class='to-label'>
                <label>Company:</label>
              </li>
              <li class='to-field'>
                <div class='input-sec'>
                  <input class='txtfield' type='text' name='cs_testimonial_company[]' value="<?php echo cs_allow_special_char($cs_testimonial_company);?>" />
                </div>
                <div class='left-info'>
                  <p>Company Name</p>
                </div>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Image</label>
              </li>
              <li class="to-field">
                <input id="cs_testimonial_img<?php echo cs_allow_special_char($cs_rand_string)?>" name="cs_testimonial_img[]" type="hidden" class="" value="<?php echo cs_allow_special_char($cs_testimonial_img);?>"/>
                <input name="cs_testimonial_img<?php echo cs_allow_special_char($cs_rand_string)?>"  type="button" class="uploadMedia left" value="Browse"/>
              </li>
            </ul>
            <div class="page-wrap" style="overflow:hidden; display:<?php echo cs_allow_special_char($cs_testimonial_img) && trim($cs_testimonial_img) !='' ? 'inline' : 'none';?>" id="cs_testimonial_img<?php echo cs_allow_special_char($cs_rand_string)?>_box" >
              <div class="gal-active">
                <div class="dragareamain" style="padding-bottom:0px;">
                  <ul id="gal-sortable">
                    <li class="ui-state-default" id="">
                      <div class="thumb-secs"> <img src="<?php echo cs_allow_special_char($cs_testimonial_img);?>"  id="cs_testimonial_img<?php echo cs_allow_special_char($cs_rand_string)?>_img" width="100" height="150"  />
                        <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_testimonial_img<?php echo cs_allow_special_char($cs_rand_string)?>')" class="delete"></a> </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <?php
				}
			}
			?>
        </div>
        <div class="hidden-object">
          <input type="hidden" name="testimonials_num[]" value="<?php echo cs_allow_special_char($cs_testimonials_num)?>" class="fieldCounter"/>
        </div>
        <div class="wrapptabbox cs-pbwp-content" style="padding:0">
          <div class="opt-conts">
            <ul class="form-elements">
              <li class="to-field"> <a href="#" class="add_servicesss cs-main-btn" onclick="cs_shortcode_element_ajax_call('testimonials', 'shortcode-item-<?php echo cs_allow_special_char($cs_counter);?>', '<?php echo admin_url('admin-ajax.php');?>')"><i class="icon-plus-circle"></i>Add testimonials</a> </li>
              <div id="loading" class="shortcodeload"></div>
            </ul>
            <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
            <ul class="form-elements insert-bg noborder">
              <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','shortcode-item-<?php echo cs_allow_special_char($cs_counter);?>','<?php echo cs_allow_special_char($cs_filter_element);?>')" >INSERT</a> </li>
            </ul>
            <div id="results-shortocde"></div>
            <?php } else {?>
            <ul class="form-elements noborder">
              <li class="to-label"></li>
              <li class="to-field">
                <input type="hidden" name="cs_orderby[]" value="testimonials" />
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
	add_action('wp_ajax_cs_pb_testimonials', 'cs_pb_testimonials');
}
// Testimonial html form for page builder end

//=====================================================================
// quote html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_quote' ) ) {
	function cs_pb_quote($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_quote';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array(
				'cs_quote_style' => 'default',
				'cs_quote_section_title' => '',
				'cs_quote_cite'   => '',
				'cs_quote_cite_url'   => '#',
				'cs_quote_text_color'   => '',
				'cs_quote_align'   => 'center',
				'cs_quote_class'   => '',
				'cs_quote_animation'   => ''
			);
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			if(isset($cs_output['0']['content']))
				$cs_quote_content = $cs_output['0']['content'];
			else 
				$cs_quote_content = '';
			$cs_quote_element_size = '25';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_quote';
			$cs_coloumn_class = 'column_'.$cs_quote_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo cs_allow_special_char($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo cs_allow_special_char($cs_coloumn_class);?> <?php echo cs_allow_special_char($cs_shortcode_view);?>" item="column" data="<?php echo element_size_data_array_index($cs_quote_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_quote_element_size, '', 'quote-right',$type='');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo cs_allow_special_char($cs_shortcode_element);?>" id="<?php echo cs_allow_special_char($cs_name.$cs_counter)?>"  data-shortcode-template="[cs_quote {{attributes}}]{{content}}[/cs_quote]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Quote Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <div class="cs-pbwp-content cs-wrapp-tab-box">
          <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
          <ul class="form-elements">
            <li class="to-label">
              <label>Section Title</label>
            </li>
            <li class="to-field">
              <input  name="cs_quote_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_quote_section_title)?>"   />
              <p> This is used for the one page navigation, to identify the section below. Give a title </p>
            </li>
          </ul>
          
          <ul class="form-elements">
            <li class="to-label">
              <label>Style</label>
            </li>
            <li class="to-field select-style">
              <select name="cs_quote_style[]" class="dropdown" >
                <option value="default" <?php if($cs_quote_style=="default")echo "selected";?>>Default</option>
                <option value="icon" <?php if($cs_quote_style=="icon")echo "selected";?>>With Icon</option>
              </select>
              <p>Select the style</p>
            </li>
          </ul>
          
          <ul class="form-elements">
            <li class="to-label">
              <label>Author</label>
            </li>
            <li class="to-field">
              <input type="text" name="cs_quote_cite[]" class="txtfield" value="<?php echo esc_attr($cs_quote_cite)?>" />
              <p>give the name of the author</p>
            </li>
          </ul>
          <ul class="form-elements">
            <li class="to-label">
              <label>Author url</label>
            </li>
            <li class="to-field">
              <input type="text" name="cs_quote_cite_url[]" class="txtfield" value="<?php echo esc_url($cs_quote_cite_url);?>" />
              <p>give the author external/internal url</p>
            </li>
          </ul>
          <ul class="form-elements">
            <li class="to-label">
              <label>Text Color</label>
            </li>
            <li class="to-field">
              <input type="text" name="cs_quote_text_color[]" class="bg_color" value="<?php echo esc_attr($cs_quote_text_color)?>" />
              <div class="left-box">
                <p>Provide a hex colour code here (include #) if you want to override the default.</p>
              </div>
            </li>
          </ul>
          <ul class="form-elements">
            <li class="to-label">
              <label>Align</label>
            </li>
            <li class="to-field select-style">
              <select name="cs_quote_align[]" class="dropdown" >
                <option <?php if($cs_quote_align=="left")echo "selected";?> >left</option>
                <option <?php if($cs_quote_align=="right")echo "selected";?> >right</option>
                <option <?php if($cs_quote_align=="center")echo "selected";?> >center</option>
              </select>
              <p>Align the content position</p>
            </li>
          </ul>
          <ul class="form-elements">
            <li class="to-label">
              <label>Quote Content</label>
            </li>
            <li class="to-field">
              <textarea name="quote_content[]" data-content-text="cs-shortcode-textarea"><?php echo esc_textarea($cs_quote_content);?></textarea>
              <p>Enter your content</p>
            </li>
          </ul>
          <?php 
				if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
					cs_shortcode_custom_dynamic_classes($cs_quote_class,$cs_quote_animation,'','cs_quote');
				}
			?>
        </div>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="quote" />
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
	add_action('wp_ajax_cs_pb_quote', 'cs_pb_quote');
}
// quote html form for page builder end

//=====================================================================
// Contact Form html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_contactus' ) ) {
	function cs_pb_contactus($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_contactus';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array( 'cs_contactus_section_title' => '', 'cs_contactus_label' => '', 'cs_contactus_view' => '', 'cs_contactus_vacancies' => '', 'cs_contactus_send' => '','cs_success' => '','cs_error' => '','cs_contact_class' => '','cs_contact_animation' => '');
		if(isset($cs_output['0']['atts']))
			$cs_atts = $cs_output['0']['atts'];
		else 
			$cs_atts = array();
		$cs_contactus_element_size = '25';
		foreach($cs_defaults as $key=>$values){
			if(isset($cs_atts[$key]))
				$$key = $cs_atts[$key];
			else 
				$$key =$values;
		 }
		$cs_name = 'cs_pb_contactus';
		$cs_coloumn_class = 'column_'.$cs_contactus_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="contactus" data="<?php echo element_size_data_array_index($cs_contactus_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_contactus_element_size, '', 'building-o',$type='');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_contactus {{attributes}}]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Contact Form Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
        <ul class="form-elements">
          <li class="to-label">
            <label>Section Title</label>
          </li>
          <li class="to-field">
            <input  name="cs_contactus_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_contactus_section_title);?>"   />
            <p> This is used for the one page navigation, to identify the section below. Give a title</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Job vacancies</label>
          </li>
          <li class="to-field select-style">
            <select class="cs_contactus_vacancies" id="cs_contactus_vacancies" name="cs_contactus_vacancies[]">
              <option <?php if($cs_contactus_vacancies == "on")echo "selected";?> value="on">ON</option>
              <option <?php if($cs_contactus_vacancies == "off")echo "selected";?> value="off">OFF</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>View</label>
          </li>
          <li class="to-field select-style">
            <select class="cs_contactus_view" id="cs_contactus_view" name="cs_contactus_view[]">
              <option <?php if($cs_contactus_view == "plain")echo "selected";?> value="plain">Plain</option>
              <option <?php if($cs_contactus_view == "fancy")echo "selected";?> value="fancy">Fancy</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Label On/Off</label>
          </li>
          <li class="to-field select-style">
            <select class="cs_contactus_label" id="cs_contactus_label" name="cs_contactus_label[]">
              <option <?php if($cs_contactus_label == "on")echo "selected";?> value="on">ON</option>
              <option <?php if($cs_contactus_label == "off")echo "selected";?> value="off">OFF</option>
            </select>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Send To</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_contactus_send[]" class="txtfield" value="<?php echo esc_attr($cs_contactus_send);?>" />
            <p>add a email which you want to receive email</p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Success Message</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_success[]" class="txtfield" value="<?php echo esc_attr($cs_success);?>" />
            <p>set a meesage if your email sent successfully </p>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Error Message</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_error[]" class="txtfield" value="<?php echo esc_attr($cs_error);?>" />
            <p>set a message for error message</p>
          </li>
        </ul>
        <!--<input type="hidden" name="cs_form_id[]" class="txtfield" value="<?php echo esc_attr($cs_random_id);?>" />-->
        <?php 
		if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
				cs_shortcode_custom_dynamic_classes($cs_contact_class,$cs_contact_animation,'','cs_contact');
			}
		?>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements insert-bg">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$cs_name);?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="contactus" />
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
	add_action('wp_ajax_cs_pb_contactus', 'cs_pb_contactus');
}
// Contact Form html form for page builder end