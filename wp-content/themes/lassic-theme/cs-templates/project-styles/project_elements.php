<?php

//======================================================================
// Project html form for page builder start
//======================================================================
if ( ! function_exists( 'cs_pb_project' ) ) {
	function cs_pb_project($die = 0){
		global $cs_node, $post;
		$shortcode_element = '';
		$filter_element = 'filterdrag';
		$shortcode_view = '';
		$output = array();
		$counter = $_POST['counter'];
		$cs_counter = $_POST['counter'];
		if ( isset($_POST['action']) && !isset($_POST['shortcode_element_id']) ) {
			$POSTID = '';
			$shortcode_element_id = '';
		} else {
			$POSTID = $_POST['POSTID'];
			$shortcode_element_id = $_POST['shortcode_element_id'];
			$shortcode_str = stripslashes ($shortcode_element_id);
			$PREFIX = 'cs_project';
			$parseObject 	= new ShortcodeParse();
			$output = $parseObject->cs_shortcodes( $output, $shortcode_str , true , $PREFIX );
		}
		$defaults = array('column_size'=>'','cs_project_section_title'=>'','cs_project_view'=>'','cs_filterable'=>'','cs_text_align'=>'','cs_project_cat'=>'',
		'cs_project_num_post'=>'10','cs_project_pagination'=>'','cs_project_class' => '','cs_project_animation' => '');
			if(isset($output['0']['atts']))
				$atts = $output['0']['atts'];
			else 
				$atts = array();
			$project_element_size = '50';
			foreach($defaults as $key=>$values){
				if(isset($atts[$key]))
					$$key = $atts[$key];
				else 
					$$key =$values;
			 }
			$name = 'cs_pb_project';
			$coloumn_class = 'column_'.$project_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$shortcode_element = 'shortcode_element_class';
			$shortcode_view = 'cs-pbwp-shortcode';
			$filter_element = 'ajax-drag';
			$coloumn_class = '';
		}
	?>
    <div id="<?php echo esc_attr($name.$cs_counter)?>_del" class="column parentdelete <?php echo esc_attr($coloumn_class);?> <?php echo esc_attr($shortcode_view);?>" item="project" data="<?php echo element_size_data_array_index($project_element_size)?>">
      <?php cs_element_setting($name,$cs_counter,$project_element_size);?>
      <div class="cs-wrapp-class-<?php echo esc_attr($cs_counter); ?> <?php echo cs_allow_special_char($shortcode_element); ?>" id="<?php echo cs_allow_special_char($name.$cs_counter)?>" data-shortcode-template="[cs_project {{attributes}}]"  style="display: none;">
        <div class="cs-heading-area">
          <h5>Edit Project Options</h5>
          <a href="javascript:removeoverlay('<?php echo cs_allow_special_char($name.$cs_counter)?>','<?php echo cs_allow_special_char($filter_element);?>')" class="cs-btnclose"><i class="fa fa-times"></i></a> 
        </div>
        <div class="cs-pbwp-content">
          <div class="cs-wrapp-clone cs-shortcode-wrapp">
            <?php
             if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
            <ul class="form-elements">
                <li class="to-label"><label>Project Title</label></li>
                <li class="to-field">
                    <input  name="cs_project_section_title[]" type="text"  value="<?php echo esc_attr($cs_project_section_title); ?>"   />
                </li>                  
             </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Choose Category</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <div class="select-style">
                    <select name="cs_project_cat[]" class="dropdown">
                      <option value="0">-- Select Category --</option>
                      <?php show_all_cats('', '', $cs_project_cat, "project-category");?>
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
                <label>Project Design Views</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <div class="select-style">
				    <select name="cs_project_view[]" class="dropdown">
                      <option value="grid-view" <?php if($cs_project_view == 'grid-view'){echo 'selected="selected"';}?>>4 Column Grid</option>
                      <option value="gutter" <?php if($cs_project_view == 'gutter'){echo 'selected="selected"';}?>>Gutter</option>
                      <option value="no-gutter" <?php if($cs_project_view == 'no-gutter'){echo 'selected="selected"';}?>>No Gutter</option>
                      <option value="modern" <?php if($cs_project_view == 'modern'){echo 'selected="selected"';}?>>Modern</option>
                      <option value="mesonry" <?php if($cs_project_view == 'mesonry'){echo 'selected="selected"';}?>>Mesonry</option>
                    </select>
                  </div>
                </div>
                <div class="left-info">
                  <p>Please select the View.</p>
                </div>
              </li>
            </ul>
            <ul class="form-elements" id="modern-view-text">
              <li class="to-label">
                <label>Text Align</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <div class="select-style">
				    <select name="cs_text_align[]" class="dropdown">
                      <option value="left" <?php if($cs_text_align == 'left'){echo 'selected="selected"';}?>>Left</option>
                      <option value="center" <?php if($cs_text_align == 'center'){echo 'selected="selected"';}?>>Center</option>
                    </select>
                  </div>
                </div>
                <div class="left-info">
                  <p>Text align work only on Modern View</p>
                </div>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>Filterable</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <div class="select-style">
				    <select name="cs_filterable[]" class="dropdown">
                      <option value="yes" <?php if($cs_filterable == 'yes'){echo 'selected="selected"';}?>>Yes</option>
                      <option value="no" <?php if($cs_filterable == 'no'){echo 'selected="selected"';}?>>No</option>
                    </select>
                  </div>
                </div>
                <div class="left-info">
                  <p>Filterable will not work on Mesonry View</p>
                </div>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>No. of Post Per Page</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <input type="text" name="cs_project_num_post[]" class="txtfield" value="<?php echo esc_attr($cs_project_num_post); ?>" />
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
                <select name="cs_project_pagination[]" class="dropdown">
                  <option <?php if($cs_project_pagination=="Show Pagination")echo "selected";?> >Show Pagination</option>
                  <option <?php if($cs_project_pagination=="Single Page")echo "selected";?> >Single Page</option>
                </select>
              </li>
            </ul>
            <?php 
                if ( function_exists( 'cs_shortcode_custom_classes' ) ) {
                    cs_shortcode_custom_dynamic_classes($cs_project_class,$cs_project_animation,'','cs_project');
                }
            ?>
            <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
            <ul class="form-elements insert-bg">
              <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('cs_pb_','',$name));?>','<?php echo esc_js($name.$cs_counter)?>','<?php echo esc_js($filter_element);?>')" >Insert</a> </li>
            </ul>
            <div id="results-shortocde"></div>
            <?php } else {?>
            <ul class="form-elements">
              <li class="to-label"></li>
              <li class="to-field">
                <input type="hidden" name="cs_orderby[]" value="project" />
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
	add_action('wp_ajax_cs_pb_project', 'cs_pb_project');
}
// Project html form for page builder end