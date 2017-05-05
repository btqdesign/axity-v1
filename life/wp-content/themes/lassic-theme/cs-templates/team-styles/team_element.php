<?php
/**
 * File Type: Team Page Builder Element
 */


//======================================================================
// Team html form for page builder start
//======================================================================
if ( ! function_exists( 'cs_pb_member' ) ) {
	function cs_pb_member($die = 0){
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
			$PREFIX = 'cs_member';
			$parseObject 	= new ShortcodeParse();
			$output = $parseObject->cs_shortcodes( $output, $shortcode_str , true , $PREFIX );
		}
		$defaults = array('cs_member_section_title'=>'','cs_member_cat' =>'','cs_member_excerpt_length' =>'255','cs_member_filterable' =>'','cs_member_orderby'=>'DESC','orderby'=>'ID','cs_member_num_post'=>'10','member_pagination'=>'','cs_member_class' => '','cs_member_animation' => '');
			if(isset($output['0']['atts']))
				$atts = $output['0']['atts'];
			else 
				$atts = array();
			$member_element_size = '50';
			foreach($defaults as $key=>$values){
				if(isset($atts[$key]))
					$$key = $atts[$key];
				else 
					$$key =$values;
			 }
			$name = 'cs_pb_member';
			$coloumn_class = 'column_'.$member_element_size;
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
			$shortcode_element = 'shortcode_element_class';
			$shortcode_view = 'cs-pbwp-shortcode';
			$filter_element = 'ajax-drag';
			$coloumn_class = '';
		}
	?>
    <div id="<?php echo esc_attr( $name.$cs_counter );?>_del" class="column  parentdelete <?php echo esc_attr( $coloumn_class );?> <?php echo esc_attr( $shortcode_view );?>" item="member" data="<?php echo element_size_data_array_index($member_element_size)?>">
      <?php cs_element_setting($name,$cs_counter,$member_element_size);?>
      <div class="cs-wrapp-class-<?php echo intval( $cs_counter )?> <?php echo esc_attr( $shortcode_element );?>" id="<?php echo esc_attr( $name.$cs_counter )?>" data-shortcode-template="[cs_member {{attributes}}]"  style="display: none;">
        <div class="cs-heading-area">
          <h5>Edit Teams Options</h5>
          <a href="javascript:removeoverlay('<?php echo esc_js( $name.$cs_counter );?>','<?php echo esc_js( $filter_element );?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
        <div class="cs-pbwp-content">
          <div class="cs-wrapp-clone cs-shortcode-wrapp">
            <?php
            if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){cs_shortcode_element_size();}?>
            <ul class="form-elements">
                <li class="to-label"><label>Section Title</label></li>
                <li class="to-field">
                    <input  name="cs_member_section_title[]" type="text"  value="<?php echo esc_attr( $cs_member_section_title )?>"   />
                </li>                  
            </ul>
			
            <ul class="form-elements">
              <li class="to-label">
                <label>Choose Category</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <div class="select-style">
                    <select name="cs_member_cat[]" class="dropdown">
                      <option value="0">-- Select Category --</option>
                      <?php show_all_cats('', '', $cs_member_cat, "member-category");?>
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
                <label>Length of Excerpt </label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <input type="text" name="cs_member_excerpt_length[]" class="txtfield" value="<?php echo esc_attr( $cs_member_excerpt_length ); ?>" />
                </div>
                <div class="left-info">
                  <p>Enter number of character for short description text</p>
                </div>
              </li>
            </ul>
            <div id="Team-listing<?php echo intval($cs_counter);?>" >
              <ul class="form-elements">
                <li class="to-label">
                  <label>Team Order</label>
                </li>
                <li class="to-field">
                  <div class="input-sec">
                    <div class="select-style">
                      <select name="cs_member_orderby[]" class="dropdown" >
                        <option <?php if($cs_member_orderby=="ASC")echo "selected";?> value="ASC">ASC</option>
                        <option <?php if($cs_member_orderby=="DESC")echo "selected";?> value="DESC">DESC</option>
                      </select>
                    </div>
                  </div>
				</li>
			</ul>
			
            </div>
            <ul class="form-elements">
              <li class="to-label">
                <label>Filterable</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <div class="select-style">
                    <select name="cs_member_filterable[]" class="dropdown">
                      
                      <option value="yes" <?php if($cs_member_filterable == 'yes'){echo 'selected="selected"';}?>>Yes</option>
                      <option value="no" <?php if($cs_member_filterable == 'no'){echo 'selected="selected"';}?>>No</option>
                      
                    </select>
                  </div>
                </div>
                <div class="left-info">
                  <p>Enable/Disable Filterable</p>
                </div>
              </li>
            </ul>
            <ul class="form-elements">
              <li class="to-label">
                <label>No. of Post Per Page</label>
              </li>
              <li class="to-field">
                <div class="input-sec">
                  <input type="text" name="cs_member_num_post[]" class="txtfield" value="<?php echo esc_attr( $cs_member_num_post ); ?>" />
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
                <select name="member_pagination[]" class="dropdown">
                  <option <?php if($member_pagination=="Show Pagination")echo "selected";?> >Show Pagination</option>
                  <option <?php if($member_pagination=="Single Page")echo "selected";?> >Single Page</option>
                </select>
              </li>
            </ul>
            <?php 
                if ( function_exists( 'cs_shortcode_custom_classes' ) ) {
                    cs_shortcode_custom_dynamic_classes($cs_member_class,$cs_member_animation,'','cs_member');
                }
            ?>
            <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
            <ul class="form-elements insert-bg">
              <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js( str_replace('cs_pb_','',$name) );?>','<?php echo esc_js( $name.$cs_counter )?>','<?php echo esc_js( $filter_element );?>')" >Insert</a> </li>
            </ul>
            <div id="results-shortocde"></div>
            <?php } else {?>
            <ul class="form-elements">
              <li class="to-label"></li>
              <li class="to-field">
                <input type="hidden" name="cs_orderby[]" value="member" />
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
	add_action('wp_ajax_cs_pb_member', 'cs_pb_member');
}