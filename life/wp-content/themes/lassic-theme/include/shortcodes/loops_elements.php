<?php
/**
 * File Type: Loops Shortcode Elements
 */

//======================================================================
// Clients html form for page builder start
//======================================================================

if ( ! function_exists( 'cs_pb_clients' ) ) {
	function cs_pb_clients($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$cs_counter = $_POST['counter'];
		$cs_clients_num = 0;
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$CS_PREFIX = 'cs_clients|clients_item';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array('cs_clients_view' => '','cs_client_gray' => '','cs_client_border' => '','cs_client_section_title' => '','cs_client_class' => '','cs_client_animation' => '');
		if(isset($cs_output['0']['atts']))
			$cs_atts = $cs_output['0']['atts'];
		else 
			$cs_atts = array();
		if(isset($cs_output['0']['content']))
			$cs_atts_content = $cs_output['0']['content'];
		else 
			$cs_atts_content = array();
		if(is_array($cs_atts_content))
				$cs_clients_num = count($cs_atts_content);
		$cs_clients_element_size = '25';
		foreach($cs_defaults as $key=>$values){
			if(isset($cs_atts[$key]))
				$$key = $cs_atts[$key];
			else 
				$$key =$values;
		 }
		$cs_name = 'cs_pb_clients';
		$cs_coloumn_class = 'column_'.$cs_clients_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
		$cs_randD_id = rand(34, 453453);
	?>
<div id="<?php echo cs_allow_special_char($cs_name.$cs_counter);?>_del" class="column  parentdelete <?php echo cs_allow_special_char($cs_coloumn_class);?> <?php echo cs_allow_special_char($cs_shortcode_view);?>" item="column" data="<?php echo element_size_data_array_index($cs_clients_element_size)?>" >
  <?php cs_element_setting($cs_name,$cs_counter,$cs_clients_element_size,'','weixin');?>
  <div class="cs-wrapp-class-<?php echo cs_allow_special_char($cs_counter)?> <?php echo cs_allow_special_char($cs_shortcode_element);?>" id="<?php echo cs_allow_special_char($cs_name.$cs_counter);?>" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Clients Options</h5>
      <a href="javascript:removeoverlay('<?php echo cs_allow_special_char($cs_name.$cs_counter)?>','<?php echo cs_allow_special_char($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-clone-append cs-pbwp-content" >
      <div class="cs-wrapp-tab-box">
        <div id="shortcode-item-<?php echo esc_attr($cs_counter);?>" data-shortcode-template="{{child_shortcode}} [/cs_clients]" data-shortcode-child-template="[clients_item {{attributes}}] {{content}} [/clients_item]">
          <div class="cs-wrapp-clone cs-shortcode-wrapp cs-disable-true" data-template="[cs_clients {{attributes}}]">
            <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
            <ul class="form-elements">
                <li class="to-label"><label>Section Title</label></li>
                <li class="to-field">
                    <input  name="cs_client_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_client_section_title);?>"   />
                </li>                  
             </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>View</label>
              </li>
              <li class="to-field select-style">
                <select class="cs_size" id="cs_size" name="cs_clients_view[]">
                  <option value="grid" <?php if($cs_clients_view == 'grid'){echo 'selected="selected"';}?>>Grid View</option>
                  <option value="slider" <?php if($cs_clients_view == 'slider'){echo 'selected="selected"';}?>>Slider View</option>
                </select>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Gray Scale</label>
              </li>
              <li class="to-field select-style">
                <select class="cs_client_gray" id="cs_client_gray" name="cs_client_gray[]">
                  <option value="yes" <?php if($cs_client_gray == 'yes'){echo 'selected="selected"';}?>>Yes</option>
                  <option value="no" <?php if($cs_client_gray == 'no'){echo 'selected="selected"';}?>>No</option>
                </select>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Border</label>
              </li>
              <li class="to-field select-style">
                <select class="cs_client_border" id="cs_client_border" name="cs_client_border[]">
                  <option value="yes" <?php if($cs_client_border == 'yes'){echo 'selected="selected"';}?>>Yes</option>
                  <option value="no" <?php if($cs_client_border == 'no'){echo 'selected="selected"';}?>>No</option>
                </select>
              </li>
            </ul>
            <ul class="form-elements">
                <li class="to-label"><label>Custom ID</label></li>
                <li class="to-field">
                    <input type="text" name="cs_client_class[]" class="txtfield"  value="<?php echo esc_attr($cs_client_class)?>" />
                </li>
             </ul>
            <ul class="form-elements">
                <li class="to-label"><label>Animation Class</label></li>
                <li class="to-field">
                    <div class="select-style">
                        <select class="dropdown" name="cs_client_animation[]">
                            <option value="">Select Animation</option>
                            <?php 
                                $cs_animation_array = cs_animation_style();
                                foreach($cs_animation_array as $animation_key=>$animation_value){
                                    echo '<optgroup label="'.$animation_key.'">';	
                                    foreach($animation_value as $key=>$value){
                                        $cs_active_class = '';
                                        if($cs_client_animation == $key){$cs_active_class = 'selected="selected"';}
                                        echo '<option value="'.$key.'" '.$cs_active_class.'>'.$value.'</option>';
                                    }
                                }
                             ?>
                          </select>
                      </div>
                </li>
            </ul>
          </div>
          <?php
		  		if ( isset($cs_clients_num) && $cs_clients_num <> '' && isset($cs_atts_content) && is_array($cs_atts_content)){
					$cs_itemCounter  = 0 ;		
					foreach ( $cs_atts_content as $cs_clients_items ){
						$cs_itemCounter++;
						$cs_rand_id = $cs_counter.''.cs_generate_random_string(3);
						$cs_defaults = array('cs_bg_color'=>'','cs_website_url'=>'','cs_client_title'=>'','cs_client_logo'=>'');
						foreach($cs_defaults as $key=>$values){
							if(isset($cs_clients_items['atts'][$key]))
								$$key = $cs_clients_items['atts'][$key];
							else 
								$$key =$values;
						 }
				?>
                      <div class='cs-wrapp-clone cs-shortcode-wrapp'  id="cs_infobox_<?php echo cs_allow_special_char($cs_rand_id);?>">
                        <header>
                          <h4><i class='icon-arrows'></i>Clients</h4>
                          <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                         <ul class="form-elements">
                          <li class="to-label">
                            <label>Title</label>
                          </li>
                          <li class="to-field">
                            <input type="text" id="cs_client_title" class="" name="cs_client_title[]" value="<?php echo cs_allow_special_char($cs_client_title);?>" />
                          </li>
                        </ul>
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Background Color</label>
                          </li>
                          <li class="to-field">
                            <input type="text" id="cs_bg_color" class="bg_color" name="cs_bg_color[]" value="<?php echo esc_attr($cs_bg_color);;?>" />
                          </li>
                        </ul>
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Website URL</label>
                          </li>
                          <li class="to-field">
                            <div class="input-sec">
                              <input type="text" id="cs_website_url" class="" name="cs_website_url[]" value="<?php echo esc_url($cs_website_url);?>" />
                            </div>
                            <div class="left-info">
                              <p>e.g. http://yourdomain.com/</p>
                            </div>
                          </li>
                        </ul>
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Client Logo</label>
                          </li>
                          <li class="to-field">
                            <input id="cs_client_logo<?php echo cs_allow_special_char($cs_itemCounter)?>" name="cs_client_logo[]" type="hidden" class="" value="<?php echo cs_allow_special_char($cs_client_logo);?>"/>
                            <input name="cs_client_logo<?php echo cs_allow_special_char($cs_itemCounter)?>"  type="button" class="uploadMedia left" value="Browse"/>
                          </li>
                        </ul>
                        <div class="page-wrap" style="overflow:hidden; display:<?php echo cs_allow_special_char($cs_client_logo) && trim($cs_client_logo) !='' ? 'inline' : 'none';?>" id="cs_client_logo<?php echo cs_allow_special_char($cs_itemCounter)?>_box" >
                          <div class="gal-active">
                            <div class="dragareamain" style="padding-bottom:0px;">
                              <ul id="gal-sortable">
                                <li class="ui-state-default" id="">
                                  <div class="thumb-secs"> <img src="<?php echo cs_allow_special_char($cs_client_logo);?>"  id="cs_client_logo<?php echo cs_allow_special_char($cs_itemCounter)?>_img" width="100" height="150"  />
                                    <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_client_logo<?php echo cs_allow_special_char($cs_itemCounter)?>')" class="delete"></a> </div>
                                  </div>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
          <?php }
			 }
			?>
        </div>
        <div class="hidden-object">
          <input type="hidden" name="clients_num[]" value="<?php echo (int)$cs_clients_num;?>" class="fieldCounter"  />
        </div>
        <div class="wrapptabbox no-padding-lr">
          <div class="opt-conts">
            <ul class="form-elements noborder">
              <li class="to-field"> <a href="#" class="add_servicesss cs-main-btn" onclick="cs_shortcode_element_ajax_call('clients', 'shortcode-item-<?php echo cs_allow_special_char($cs_counter);?>', '<?php echo cs_allow_special_char(admin_url('admin-ajax.php'));?>')"><i class="icon-plus-circle"></i>Add Client</a> </li>
               <div id="loading" class="shortcodeload"></div>
            </ul>
          </div>
          <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
          <ul class="form-elements insert-bg">
            <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo cs_allow_special_char(str_replace('cs_pb_','',$cs_name));?>','shortcode-item-<?php echo cs_allow_special_char($cs_counter);?>','<?php echo cs_allow_special_char($cs_filter_element);?>')" >INSERT</a> </li>
          </ul>
          <div id="results-shortocde"></div>
          <?php } else {?>
          <ul class="form-elements noborder no-padding-lr">
            <li class="to-label"></li>
            <li class="to-field">
              <input type="hidden" name="cs_orderby[]" value="clients" />
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
	add_action('wp_ajax_cs_pb_clients', 'cs_pb_clients');
}
// Clients Html form for page builder End

//======================================================================
// Content Slider html form for page builder start
//======================================================================
if ( ! function_exists( 'cs_pb_contentslider' ) ) {
	function cs_pb_contentslider($die = 0){
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
			$CS_PREFIX = 'cs_contentslider';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array('cs_contentslider_title' => '','cs_contentslider_dcpt_cat'=>'','cs_contentslider_orderby'=>'DESC','cs_contentslider_description'=>'yes','cs_contentslider_excerpt'=>'255', 'cs_contentslider_num_post'=>get_option("posts_per_page"),'cs_contentslider_class' => '','cs_contentslider_animation' => '');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();

			$cs_contentslider_element_size = '50';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_contentslider';
			$cs_coloumn_class = 'column_'.$cs_contentslider_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="blog" data="<?php echo element_size_data_array_index($cs_contentslider_element_size)?>">
      <?php cs_element_setting($cs_name,$cs_counter,$cs_contentslider_element_size,'','newspaper-o');?>
      <div class="cs-wrapp-class-<?php echo esc_attr($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter);?>" data-shortcode-template="[cs_contentslider {{attributes}}]"  style="display: none;">
        <div class="cs-heading-area">
          <h5>Edit Content Slider Options</h5>
          <a href="javascript:removeoverlay('<?php echo esc_attr($cs_name.$cs_counter);?>','<?php echo esc_attr($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
        <div class="cs-pbwp-content">
         <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <ul class="form-elements">
          <li class="to-label">
            <label>Section Title</label>
          </li>
          <li class="to-field">
            <div class="input-sec">
              <input type="text" name="cs_contentslider_title[]" class="txtfield" value="<?php echo cs_allow_special_char(htmlspecialchars($cs_contentslider_title));?>" />
            </div>
          </li>
        </ul>
        <ul class="form-elements">
          <li class="to-label">
            <label>Select Catgory</label>
          </li>
          <li class="to-field">
            <div class="input-sec">
              <div class="select-style">
                <select name="cs_contentslider_dcpt_cat[]" class="dropdown"  >
                  <option value="0">-- Select Category --</option>
                      <?php show_all_cats('', '',$cs_contentslider_dcpt_cat, "category");?>
                </select>
              </div>
            </div>
          </li>
        </ul>
        <div id="Blog-listing">
          <ul class="form-elements">
            <li class="to-label">
              <label>Post Order</label>
            </li>
            <li class="to-field">
              <div class="input-sec">
                <div class="select-style">
                  <select name="cs_contentslider_orderby[]" class="dropdown" >
                    <option <?php if($cs_contentslider_orderby=="ASC")echo "selected";?> value="ASC">ASC</option>
                    <option <?php if($cs_contentslider_orderby=="DESC")echo "selected";?> value="DESC">DESC</option>
                  </select>
                </div>
              </div>
            </li>
          </ul>
          <ul class="form-elements">
            <li class="to-label">
              <label>Post Description</label>
            </li>
            <li class="to-field">
              <div class="input-sec">
                <div class="select-style">
                  <select name="cs_contentslider_description[]" class="dropdown" >
                    <option <?php if($cs_contentslider_description=="yes")echo "selected";?> value="yes">Yes</option>
                    <option <?php if($cs_contentslider_description=="no")echo "selected";?> value="no">No</option>
                  </select>
                </div>
              </div>
            </li>
          </ul>
          <ul class="form-elements">
            <li class="to-label">
              <label>Length of Excerpt</label>
            </li>
            <li class="to-field">
              <div class="input-sec">
                <input type="text" name="cs_contentslider_excerpt[]" class="txtfield" value="<?php echo esc_attr($cs_contentslider_excerpt);?>" />
              </div>
              <div class="left-info">
                <p>Enter number of character for short description text.</p>
              </div>
            </li>
          </ul>
        </div>
        <ul class="form-elements">
          <li class="to-label">
            <label>No. of Post Per Page</label>
          </li>
          <li class="to-field">
            <div class="input-sec">
              <input type="text" name="cs_contentslider_num_post[]" class="txtfield" value="<?php echo esc_attr($cs_contentslider_num_post); ?>" />
            </div>
          </li>
        </ul>
        <?php 
			if ( function_exists( 'cs_shortcode_custom_classes' ) ) {
				cs_shortcode_custom_dynamic_classes($cs_contentslider_class,$cs_contentslider_animation,'','cs_contentslider');
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
          <input type="hidden" name="cs_orderby[]" value="contentslider" />
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
	add_action('wp_ajax_cs_pb_contentslider', 'cs_pb_contentslider');
}
// Content Slider html form for page builder end

//======================================================================
// Blog html form for page builder start
//======================================================================
if ( ! function_exists( 'cs_pb_blog' ) ) {
	function cs_pb_blog($die = 0){
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
			$CS_PREFIX = 'cs_blog';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array('cs_blog_section_title'=>'','cs_blog_view'=>'','cs_blog_cat'=>'','cs_blog_orderby'=>'DESC','orderby'=>'ID','cs_blog_description'=>'yes','cs_blog_excerpt'=>'255', 'cs_blog_filterable'=>'','cs_blog_num_post'=>'10','cs_blog_pagination'=>'','cs_blog_class' => '','cs_blog_animation' => '');
			if(isset($cs_output['0']['atts']))
				$cs_atts = $cs_output['0']['atts'];
			else 
				$cs_atts = array();
			$cs_blog_element_size = '50';
			foreach($cs_defaults as $key=>$values){
				if(isset($cs_atts[$key]))
					$$key = $cs_atts[$key];
				else 
					$$key =$values;
			 }
			$cs_name = 'cs_pb_blog';
			$cs_coloumn_class = 'column_'.$cs_blog_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="blog" data="<?php echo element_size_data_array_index($cs_blog_element_size)?>">
      <?php cs_element_setting($cs_name,$cs_counter,$cs_blog_element_size,'','newspaper-o');?>
      <div class="cs-wrapp-class-<?php echo esc_attr($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter);?>" data-shortcode-template="[cs_blog {{attributes}}]"  style="display: none;">
        <div class="cs-heading-area">
          <h5>Edit Blog Options</h5>
          <a href="javascript:removeoverlay('<?php echo esc_attr($cs_name.$cs_counter);?>','<?php echo esc_attr($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
        <div class="cs-pbwp-content">
          <div class="cs-wrapp-clone cs-shortcode-wrapp">
            <?php
             if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
            <ul class="form-elements">
                <li class="to-label"><label>Section Title</label></li>
                <li class="to-field">
                    <input  name="cs_blog_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_blog_section_title)?>"   />
                </li>                  
             </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Choose Category</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <div class="select-style">
                    <select name="cs_blog_cat[]" class="dropdown">
                      <option value="0">-- Select Category --</option>
                      <?php show_all_cats('', '', $cs_blog_cat, "category");?>
                    </select>
                  </div>
                </div>
                <div class="left-info">
                  <p>Please select category to show posts. If you dont select category it will display all posts.</p>
                </div>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Blog Design Views</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <div class="select-style">
                    <select name="cs_blog_view[]" class="dropdown">
                      <option value="default" <?php if($cs_blog_view == 'default'){echo 'selected="selected"';}?>>Default</option>
                      <option value="timeline" <?php if($cs_blog_view == 'timeline'){echo 'selected="selected"';}?>>Timeline</option>
                      <option value="small" <?php if($cs_blog_view == 'small'){echo 'selected="selected"';}?>>Small</option>
                      <option value="clean" <?php if($cs_blog_view == 'clean'){echo 'selected="selected"';}?>>Clean</option>
                      <option value="medium" <?php if($cs_blog_view == 'medium'){echo 'selected="selected"';}?>>Medium</option>
                      <option value="grid" <?php if($cs_blog_view == 'grid'){echo 'selected="selected"';}?>>Grid</option>
                      <option value="masonry" <?php if($cs_blog_view == 'masonry'){echo 'selected="selected"';}?>>Masonry</option>
                      <option value="boxed" <?php if($cs_blog_view == 'boxed'){echo 'selected="selected"';}?>>Boxed</option>
                    </select>
                  </div>
                </div>
                <div class="left-info">
                  <p>Please select category to show posts. If you dont select category it will display all posts.</p>
                </div>
              </li>
            </ul>
            <div id="Blog-listing<?php echo esc_attr($cs_counter);?>" >
              <ul class="form-elements">
                <li class="to-label">
                  <label>Post Order</label>
                </li>
                <li class="to-field">
                  <div class="input-sec">
                    <div class="select-style">
                      <select name="cs_blog_orderby[]" class="dropdown" >
                        <option <?php if($cs_blog_orderby=="ASC")echo "selected";?> value="ASC">ASC</option>
                        <option <?php if($cs_blog_orderby=="DESC")echo "selected";?> value="DESC">DESC</option>
                      </select>
                    </div>
                  </div>
                </li>
              </ul>
              <ul class="form-elements">
                <li class="to-label">
                  <label>Post Description</label>
                </li>
                <li class="to-field">
                  <div class="input-sec">
                    <div class="select-style">
                      <select name="cs_blog_description[]" class="dropdown" >
                        <option <?php if($cs_blog_description=="yes")echo "selected";?> value="yes">Yes</option>
                        <option <?php if($cs_blog_description=="no")echo "selected";?> value="no">No</option>
                      </select>
                    </div>
                  </div>
                </li>
              </ul>
              <ul class="form-elements">
                  <li class="to-label">
                    <label>
                      <?php _e('Filterable','lassic'); ?>
                    </label>
                  </li>
                  <li class="to-field">
                    <div class="input-sec">
                      <div class="select-style">
                        <select name="cs_blog_filterable[]" class="dropdown">
                          <option value="yes" <?php if($cs_blog_filterable=="yes")echo "selected";?> >
                          <?php _e('Yes','lassic'); ?>
                          </option>
                          <option value="no" <?php if($cs_blog_filterable=="no")echo "selected";?> >
                          <?php _e('No','lassic'); ?>
                          </option>
                        </select>
                      </div>
                    </div>
                  </li>
              </ul>
              <ul class="form-elements">
                <li class="to-label">
                  <label>Length of Excerpt</label>
                </li>
                <li class="to-field">
                  <div class="input-sec">
                    <input type="text" name="cs_blog_excerpt[]" class="txtfield" value="<?php echo esc_attr($cs_blog_excerpt);?>" />
                  </div>
                  <div class="left-info">
                    <p>Enter number of character for short description text.</p>
                  </div>
                </li>
              </ul>
            </div>
            <ul class="form-elements">
              <li class="to-label">
                <label>No. of Post Per Page</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <input type="text" name="cs_blog_num_post[]" class="txtfield" value="<?php echo esc_attr($cs_blog_num_post); ?>" />
                </div>
                <div class="left-info">
                  <p>To display all the records, leave this field blank.</p>
                </div>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Pagination</label>
              </li>
              <li class="to-field select-style">
                <select name="cs_blog_pagination[]" class="dropdown">
                  <option <?php if($cs_blog_pagination=="Show Pagination")echo "selected";?> >Show Pagination</option>
                  <option <?php if($cs_blog_pagination=="Single Page")echo "selected";?> >Single Page</option>
                </select>
              </li>
            </ul>
            <?php 
                if ( function_exists( 'cs_shortcode_custom_classes' ) ) {
                    cs_shortcode_custom_dynamic_classes($cs_blog_class,$cs_blog_animation,'','cs_blog');
                }
            ?>
            <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
            <ul class="form-elements insert-bg">
              <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$cs_name));?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a> </li>
            </ul>
            <div id="results-shortocde"></div>
            <?php } else {?>
            <ul class="form-elements">
              <li class="to-label"></li>
              <li class="to-field">
                <input type="hidden" name="cs_orderby[]" value="blog" />
                <input type="button" value="Save" style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))" />
              </li>
            </ul>
            <?php }?>
          </div>
        </div>
      </div>
    </div>
<?php
		if ( $die <> 1 ) die();
	}
	add_action('wp_ajax_cs_pb_blog', 'cs_pb_blog');
}
// Blog html form for page builder end

//======================================================================
// Team html form for page builder start
//======================================================================
if ( ! function_exists( 'cs_pb_teams' ) ) {
	function cs_pb_teams($die = 0){
		global $cs_node, $post;
		$cs_shortcode_element = '';
		$cs_filter_element = 'filterdrag';
		$cs_shortcode_view = '';
		$cs_output = array();
		$counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$CS_POSTID = '';
			$cs_shortcode_element_id = '';
			$cs_counter = $_POST['counter'];
		} else {
			$CS_POSTID = $_POST['POSTID'];
			$cs_counter = $_POST['counter'];
			$CS_PREFIX = 'cs_team';
			$cs_shortcode_element_id = $_POST['shortcode_element_id'];
			$cs_shortcode_str = stripslashes ($cs_shortcode_element_id);
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array( 'cs_team_section_title' => '','cs_team_view' => 'default','cs_team_name' => '','cs_team_designation' => '','cs_team_title' => '','cs_team_profile_image' => '','cs_team_fb_url' => '','cs_team_twitter_url' => '','cs_team_googleplus_url' => '','cs_team_skype_url' => '','cs_team_email' => '','cs_teams_class' => '','cs_teams_animation' => '');

		if(isset($cs_output['0']['atts']))
			$cs_atts = $cs_output['0']['atts'];
		else 
			$cs_atts = array();
		
		if(isset($cs_output['0']['content']))
			$cs_team_description = $cs_output['0']['content'];
		else 
			$cs_team_description = "";
			
		$cs_teams_element_size = '25';
		foreach($cs_defaults as $key=>$values){
			if(isset($cs_atts[$key]))
				$$key = $cs_atts[$key];
			else 
				$$key =$values;
		 }
		$cs_name = 'cs_pb_teams';
		$cs_coloumn_class = 'column_'.$cs_teams_element_size;
	if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
		$cs_shortcode_element = 'shortcode_element_class';
		$cs_shortcode_view = 'cs-pbwp-shortcode';
		$cs_filter_element = 'ajax-drag';
		$cs_coloumn_class = '';
	}
	$cs_rand_counter = rand(888, 9999999);
	?>

<div id="<?php echo esc_attr( $cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="blog" data="<?php echo element_size_data_array_index($cs_teams_element_size)?>">
      <?php cs_element_setting($cs_name,$cs_counter,$cs_teams_element_size,'','newspaper-o');?>
      <div class="cs-wrapp-class-<?php echo esc_attr($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter);?>" data-shortcode-template="[cs_team {{attributes}}]{{content}}[/cs_team]"  style="display: none;">
        <div class="cs-heading-area">
          <h5>Edit Member Options</h5>
          <a href="javascript:removeoverlay('<?php echo esc_attr($cs_name.$cs_counter);?>','<?php echo esc_attr($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
        <div class="cs-pbwp-content">
          <div class="cs-wrapp-clone cs-shortcode-wrapp">
                <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
                <ul class="form-elements">
                    <li class="to-label"><label>Section Title</label></li>
                    <li class="to-field">
                        <input  name="cs_team_section_title[]" type="text"  value="<?php echo cs_allow_special_char($cs_team_section_title)?>"   />
                    </li>                  
                 </ul>
                <ul class="form-elements">
                  <li class="to-label">
                    <label>Team Views</label>
                  </li>
                  <li class="to-field">
                  	<div class="select-style">
                    <select class="cs_size" name="cs_team_view[]">
                      <option value="default" <?php if($cs_team_view == 'default'){echo 'selected="selected"';}?>>Default View</option>
                      <option value="thumb" <?php if($cs_team_view == 'thumb'){echo 'selected="selected"';}?>>Thumbnails View</option>
                    </select>
                    </div>
                  </li>
                </ul>
                <ul class="form-elements">
                  <li class="to-label">
                    <label>Name</label>
                  </li>
                  <li class="to-field">
                    <input type="text" name="cs_team_name[]" value="<?php echo esc_attr($cs_team_name);?>" />
                  </li>
                </ul>
                <ul class="form-elements">
                  <li class="to-label">
                    <label>Designation</label>
                  </li>
                  <li class="to-field">
                    <input type="text" name="cs_team_designation[]" value="<?php echo esc_attr($cs_team_designation);?>" />
                  </li>
                </ul>
              
                 <ul class="form-elements">
                  <li class="to-label">
                    <label>short Description</label>
                  </li>
                  <li class="to-field">
                    <textarea name="cs_team_description[]" rows="8" cols="40" data-content-text="cs-shortcode-textarea"><?php echo esc_textarea($cs_team_description);?></textarea>
                  </li>
                </ul>
             
                <ul class="form-elements">
                  <li class="to-label">
                    <label>Team Profile Image</label>
                  </li>
                  <li class="to-field">
                    <input id="cs_team_profile_image<?php echo esc_attr($cs_rand_counter)?>" name="cs_team_profile_image[]" type="hidden" class="" value="<?php echo esc_url($cs_team_profile_image);?>"/>
                    <input name="cs_team_profile_image<?php echo esc_attr($cs_rand_counter);?>"  type="button" class="uploadMedia left" value="Browse"/>
                  </li>
                </ul>
                <div class="page-wrap" style="overflow:hidden; display:<?php echo esc_url($cs_team_profile_image) && trim($cs_team_profile_image) !='' ? 'inline' : 'none';?>" id="cs_team_profile_image<?php echo esc_attr($cs_rand_counter)?>_box" >
                  <div class="gal-active">
                    <div class="dragareamain" style="padding-bottom:0px;">
                      <ul id="gal-sortable">
                        <li class="ui-state-default" id="">
                          <div class="thumb-secs"> <img src="<?php echo esc_url($cs_team_profile_image);?>"  id="cs_team_profile_image<?php echo esc_attr($cs_rand_counter)?>_img" width="100" height="150"  />
                            <div class="gal-edit-opts"> <a href="javascript:del_media('cs_team_profile_image<?php echo esc_attr($cs_rand_counter);?>')" class="delete"></a> </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <ul class="form-elements">
                  <li class="to-label">
                    <label>Facebook</label>
                  </li>
                  <li class="to-field">
                    <input type="text" name="cs_team_fb_url[]" value="<?php echo esc_url($cs_team_fb_url);?>" />
                  </li>
                </ul>
                <ul class="form-elements">
                  <li class="to-label">
                    <label>Twitter URL</label>
                  </li>
                  <li class="to-field">
                    <input type="text" name="cs_team_twitter_url[]" value="<?php echo esc_url($cs_team_twitter_url);?>" />
                  </li>
                </ul>
                <ul class="form-elements">
                  <li class="to-label">
                    <label>Google+</label>
                  </li>
                  <li class="to-field">
                    <input type="text" name="cs_team_googleplus_url[]" value="<?php echo esc_url($cs_team_googleplus_url);?>" />
                  </li>
                </ul>
                <ul class="form-elements">
                  <li class="to-label">
                    <label>Skype</label>
                  </li>
                  <li class="to-field">
                    <input type="text" name="cs_team_skype_url[]" value="<?php echo esc_url($cs_team_skype_url);?>" />
                  </li>
                </ul>
                <ul class="form-elements">
                  <li class="to-label">
                    <label>Email</label>
                  </li>
                  <li class="to-field">
                    <input type="text" name="cs_team_email[]" value="<?php echo sanitize_email($cs_team_email);?>" />
                  </li>
                </ul>
                <ul class="form-elements">
                    <li class="to-label"><label>Custom ID</label></li>
                    <li class="to-field">
                        <input type="text" name="cs_teams_class[]" class="txtfield"  value="<?php echo esc_attr($cs_teams_class)?>" />
                    </li>
                 </ul>
                <ul class="form-elements">
                    <li class="to-label"><label>Animation Class </label></li>
                    <li class="to-field select-style">
                        <select class="dropdown" name="cs_teams_animation[]">
                            <option value="">Select Animation</option>
                            <?php 
                                $cs_animation_array = cs_animation_style();
                                foreach($cs_animation_array as $animation_key=>$animation_value){
                                    echo '<optgroup label="'.$animation_key.'">';	
                                    foreach($animation_value as $key=>$value){
                                        $selected = '';
                                        if($cs_teams_animation == $key){$selected = 'selected="selected"';}
                                        echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                    }
                                }
                             ?>
                          </select>
                    </li>
                </ul>
              </div>
            <div class="wrapptabbox no-padding-lr">
              <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
              <ul class="form-elements insert-bg">
                <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$cs_name));?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >INSERT</a> </li>
              </ul>
              <div id="results-shortocde"></div>
              <?php } else {
				   ?>
              	<ul class="form-elements noborder">
                <li class="to-label"></li>
                <li class="to-field">
                  <input type="hidden" name="cs_orderby[]" value="teams" />
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
        add_action('wp_ajax_cs_pb_teams', 'cs_pb_teams');
    }
// Clients Html form for page builder End

//======================================================================
// Twitter html form for page builder start
//======================================================================
if ( ! function_exists( 'cs_pb_tweets' ) ) {
	function cs_pb_tweets($die = 0){
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
			$CS_PREFIX = 'cs_tweets';
			$cs_parseObject 	= new ShortcodeParse();
			$cs_output = $cs_parseObject->cs_shortcodes( $cs_output, $cs_shortcode_str , true , $CS_PREFIX );
		}
		$cs_defaults = array( 'cs_tweets_section_title' => '','cs_tweets_user_name' => 'default','cs_no_of_tweets' => '','cs_tweets_color'=>'','cs_tweets_class' => '','cs_tweets_animation' => '');
		if(isset($cs_output['0']['atts']))
			$cs_atts = $cs_output['0']['atts'];
		else 
			$cs_atts = array();
		$cs_tweets_element_size = '25';
		foreach($cs_defaults as $key=>$values){
			if(isset($cs_atts[$key]))
				$$key = $cs_atts[$key];
			else 
				$$key =$values;
		 }
		$cs_name = 'cs_pb_tweets';
		$cs_coloumn_class = 'column_'.$cs_tweets_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$cs_shortcode_element = 'shortcode_element_class';
			$cs_shortcode_view = 'cs-pbwp-shortcode';
			$cs_filter_element = 'ajax-drag';
			$cs_coloumn_class = '';
		}
	?>
<div id="<?php echo esc_attr($cs_name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($cs_coloumn_class);?> <?php echo esc_attr($cs_shortcode_view);?>" item="blog" data="<?php echo element_size_data_array_index($cs_tweets_element_size)?>" >
		<?php cs_element_setting($cs_name,$cs_counter,$cs_tweets_element_size,'','twitter');?>
			<div class="cs-wrapp-class-<?php echo esc_attr($cs_counter)?> <?php echo esc_attr($cs_shortcode_element);?>" id="<?php echo esc_attr($cs_name.$cs_counter)?>" data-shortcode-template="[cs_tweets {{attributes}}]" style="display: none;">
				<div class="cs-heading-area">
					<h5>Edit Twitter Options</h5>
					<a href="javascript:removeoverlay('<?php echo esc_attr($cs_name.$cs_counter)?>','<?php echo esc_attr($cs_filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a>
				</div>
				<div class="cs-pbwp-content">
                 	<div class="cs-wrapp-clone cs-shortcode-wrapp">
                       <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
                   
                            <ul class="form-elements">
                              <li class="to-label">
                                <label>User Name</label>
                              </li>
                              <li class="to-field">
                                <input type="text" name="cs_tweets_user_name[]" value="<?php echo esc_attr($cs_tweets_user_name);?>" />
                              </li>
                            </ul>
                            <ul class="form-elements">
                                <li class="to-label"><label>Text Color</label></li>
                                <li class="to-field">
                                    <input type="text" name="cs_tweets_color[]" class="bg_color"  value="<?php echo esc_attr($cs_tweets_color)?>" />
                                </li>
                            </ul>
                            <ul class="form-elements">
                              <li class="to-label">
                                <label>No of Tweets</label>
                              </li>
                              <li class="to-field">
                                <input type="text" name="cs_no_of_tweets[]" value="<?php echo (int)$cs_no_of_tweets;?>" />
                              </li>
                            </ul>
                              <?php 
                                if ( function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
                                   cs_shortcode_custom_dynamic_classes($cs_tweets_class,$cs_tweets_animation,'','cs_tweets');
                                }
                              ?>
                      </div>
					  <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
							<ul class="form-elements insert-bg">
								<li class="to-field">
									<a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$cs_name));?>','<?php echo esc_js($cs_name.$cs_counter)?>','<?php echo esc_js($cs_filter_element);?>')" >Insert</a>
								</li>
							</ul>
							<div id="results-shortocde"></div>
						<?php } else {?>
							<ul class="form-elements noborder">
								<li class="to-label"></li>
								<li class="to-field">
									<input type="hidden" name="cs_orderby[]" value="tweets" />
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
        add_action('wp_ajax_cs_pb_tweets', 'cs_pb_tweets');
    }
// Twitter Html form for page builder End