<?php
//=====================================================================
// Post slider html form for page builder start
//=====================================================================
if ( ! function_exists( 'cs_pb_postslider' ) ) {
	function cs_pb_postslider($die = 0){
		global $cs_node, $cs_count_node, $post;
		$shortcode_element = '';
		$filter_element = 'filterdrag';
		$shortcode_view = '';
		if ( isset($_POST['action']) ) {
			$name = $_POST['action'];
			$cs_counter 				= $_POST['counter'];
			$postslider_element_size 		= '50';
			$cs_slider_header_title_db  = '';
			$cs_slider_type_db 			= '';
			$cs_slider_db 				= '';
			$cs_slider_width_db 		= '';
			$cs_slider_height_db 		= '';
			$slider_view				= '';
			$cs_slider_id				= '';
			$cs_post_db					= '';
			$coloumn_class = 'column_'.$postslider_element_size;
			if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){
				$shortcode_element  = 'shortcode_element_class';
				$shortcode_view 	= 'cs-pbwp-shortcode';
				$filter_element 	= 'ajax-drag';
				$coloumn_class 		= '';
			}
		} else {
				$name = $cs_node->getName();
				$cs_count_node++;
				$postslider_element_size 			= $cs_node->postslider_element_size;
				$cs_slider_header_title_db 			= $cs_node->slider_header_title;
				$cs_slider_type_db 					= $cs_node->slider_type;
				$cs_slider_db 						= $cs_node->slider;
				$slider_view						= $cs_node->slider_view;
				$slider_id 							= $cs_node->slider_id;
				$cs_slider_width_db 				= $cs_node->width;
				$cs_slider_height_db 				= $cs_node->height;
				$cs_counter 						= $post->ID.$cs_count_node;
				$coloumn_class 						= 'column_'.$slider_element_size;
	
		}
	?>
<div id="<?php echo esc_attr($name.$cs_counter)?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class);?> <?php echo esc_attr($shortcode_view);?>" item="slider" data="<?php echo element_size_data_array_index($postslider_element_size)?>">
  <?php cs_element_setting($name,$cs_counter,$postslider_element_size,'','');?>
  <div class="cs-wrapp-class-<?php echo intval($cs_counter)?> <?php echo esc_attr($shortcode_element);?>" id="<?php echo esc_attr($name.$cs_counter)?>" data-shortcode-template="[cs_postslider {{attributes}}]" style="display: none;">
    <div class="cs-heading-area">
      <h5>Edit Post Slider Options</h5>
      <a href="javascript:removeoverlay('<?php echo esc_js($name.$cs_counter)?>','<?php echo esc_js($filter_element);?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
    <div class="cs-pbwp-content">
      <div class="cs-wrapp-clone cs-shortcode-wrapp">
        <ul class="form-elements noborder">
          <li class="to-label">
            <label>Slider Header Title</label>
          </li>
          <li class="to-field">
            <div class="input-sec">
              <input type="text" name="cs_slider_header_title[]" class="txtfield" value="<?php echo cs_allow_special_char(htmlspecialchars($cs_slider_header_title_db));?>" />
            </div>
            <div class="left-info">
              <p>Please enter slider header title.</p>
            </div>
          </li>
        </ul>
        <ul class="form-elements noborder">
          <li class="to-label">
            <label>Choose Slider Type</label>
          </li>
          <li class="to-field">
            <div class="input-sec">
              <div class="select-style">
                <select name="cs_slider_type[]" class="dropdown" onchange="cs_toggle_height(this.value,'cs_slider_height<?php echo esc_attr($name.$cs_counter)?>')">
                  <option <?php if($cs_slider_type_db=="Post Slider"){echo "selected";}?> >Post Slider</option>
                  <option <?php if($cs_slider_type_db=="Flex Slider"){echo "selected";}?> >Flex Slider</option>
                  <option <?php if($cs_slider_type_db=="Custom Slider"){echo "selected";}?> >Custom Slider</option>
                </select>
              </div>
            </div>
          </li>
        </ul>
        <ul class="form-elements noborder" id="post_slider" style="display:inline">
          <li class="to-label">
            <label>Choose Post Category</label>
          </li>
          <li class="to-field">
            <div class="input-sec">
              <div class="select-style">
                <select name="cs_post[]" class="dropdown">
                 <?php
				 $query = array('type' => 'post','child_of' => 0,'parent' => '','orderby' => 'name','order' => 'ASC','hide_empty' => 0,
'hierarchical' => 1,'taxonomy' => 'category','pad_counts'=> false ); 

				 $categories = get_categories( $query );
				 foreach ($categories as $category) { ?>
                  <option <?php if($category->term_id ==$cs_post_db)echo "selected";?> value="<?php echo esc_attr($category->term_id); ?>"> <?php echo esc_attr($category->cat_name); ?> </option>
                  <?php  }?>
                </select>
              </div>
            </div>
          </li>
        </ul>
        <ul class="form-elements noborder" id="choose_slider" style="display:none">
          <li class="to-label">
            <label>Choose Slider</label>
          </li>
          <li class="to-field">
            <div class="input-sec">
              <div class="select-style">
                <select name="cs_slider[]" class="dropdown">
                  <?php			$query = array( 'posts_per_page' => '-1', 'post_type' => 'cs_slider', 'orderby'=>'ID', 'post_status' => 'publish' );
                                    $wp_query = new WP_Query($query);
                                    while ($wp_query->have_posts()) : $wp_query->the_post();?>
                  <option <?php if($post->post_name==$cs_slider_db)echo "selected";?> value="<?php echo esc_attr($post->post_name); ?>">
                  <?php the_title()?>
                  </option>
                  <?php endwhile;?>
                </select>
              </div>
            </div>
          </li>
        </ul>
        <ul class="form-elements" id="layer_slider" style="display:none" >
          <li class="to-label">
            <label>Use Short Code</label>
          </li>
          <li class="to-field">
            <input type="text" name="cs_slider_id[]" class="txtfield" value="<?php echo htmlspecialchars($cs_slider_id);?>" />
          </li>
          <li class="to-label"></li>
          <li class="to-field">
            <p>Please enter the Revolution/Other Slider Short Code like [rev_slider name]</p>
          </li>
        </ul>
      </div>
      <?php if(isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode'){?>
      <ul class="form-elements">
        <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('cs_pb_','',$name);?>','<?php echo esc_js($name.$cs_counter)?>','<?php echo esc_js($filter_element);?>')" >Insert</a> </li>
      </ul>
      <div id="results-shortocde"></div>
      <?php } else {?>
      <ul class="form-elements noborder">
        <li class="to-label"></li>
        <li class="to-field">
          <input type="hidden" name="cs_orderby[]" value="slider" />
          <input type="button" value="Save" style="margin-right:10px;" onclick="javascript:removeoverlay('<?php echo esc_js($name.$cs_counter)?>','<?php echo esc_js($filter_element);?>')" />
        </li>
      </ul>
      <?php }?>
    </div>
  </div>
</div>
<?php
	if ( $die <> 1 ) die();
	}
	add_action('wp_ajax_cs_pb_postslider', 'cs_pb_postslider');
}
// Post slider html form for page builder End
