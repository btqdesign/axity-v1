<?php
//=====================================================================
// Adding mce custom button for short codes start
//=====================================================================
class ShortcodesEditorSelector {
    var $buttonName = 'shortcode';
    function addSelector() {
        add_filter('mce_external_plugins', array($this, 'registerTmcePlugin'));
        add_filter('mce_buttons', array($this, 'registerButton'));
    }
    function registerButton($buttons) {
        array_push($buttons, "separator", $this->buttonName);
        return $buttons;
    }
    function registerTmcePlugin($plugin_array) {
        return $plugin_array;
    }
}

if (!isset($shortcodesES)) {
   $shortcodesES = new ShortcodesEditorSelector();
    add_action('admin_head', array($shortcodesES, 'addSelector'));
}

//=====================================================================
//Bootstrap Coloumn Class
//=====================================================================
if ( ! function_exists( 'cs_custom_column_class' ) ) {
	function cs_custom_column_class($column_size){
		$coloumn_class = 'col-md-12';
		if(isset($column_size) && $column_size <> ''){
			list($top, $bottom) = explode('/', $column_size);
				$width = $top / $bottom * 100;
				$width =(int)$width;
				$coloumn_class = '';
				if(round($width) == '25' || round($width) < 25){
					$coloumn_class = 'col-md-3';			
				} elseif(round($width) == '33' || (round($width) < 33 && round($width) > 25)){
					$coloumn_class = 'col-md-4';	
				} elseif(round($width) == '50' || (round($width) < 50 && round($width) > 33)){
					$coloumn_class = 'col-md-6';	
				} elseif(round($width) == '67' || (round($width) < 67 && round($width) > 50)){
					$coloumn_class = 'col-md-8';	
				} elseif(round($width) == '75' || (round($width) < 75 && round($width) > 67)){
					$coloumn_class = 'col-md-9';	
				} else {
					$coloumn_class = 'col-md-12';
				}
		}
		return $coloumn_class;
	}
}

//=====================================================================
// Column Width
//=====================================================================
if ( ! function_exists( 'cs_custom_column_type' ) ) {
	function cs_custom_column_type($width){
		$coloumn_class = '1/1';
		if(isset($width) && $width <> ''){
			$width = (int)$width;
				if(round($width) == '25' || round($width) < 25){
					$coloumn_class = '1/4';			
				} elseif(round($width) == '33' || (round($width) < 33 && round($width) > 25)){
					$coloumn_class = '1/3';	
				} elseif(round($width) == '50' || (round($width) < 50 && round($width) > 33)){
					$coloumn_class = '1/2';	
				} elseif(round($width) == '67' || (round($width) < 67 && round($width) > 50)){
					$coloumn_class = '2/3';	
				} elseif(round($width) == '75' || (round($width) < 75 && round($width) > 67)){
					$coloumn_class = '3/4';	
				} else {
					$coloumn_class = '1/1';
				}
		}
		return  $coloumn_class;
	}
}

//=====================================================================
//Progress bars Shortcode
//=====================================================================
if (!function_exists('cs_bar_shortcode')) {
	function cs_chart_shortcode($atts, $content = "") {
		$defaults = array('class'=>'cs-chart','percent'=>'50','icon'=>'','title'=>'Title','text'=>'Text Description', 'background_color'=>'#ccc','animate_style'=>'slide');
		extract( shortcode_atts( $defaults, $atts ) );
		$html = '';
		$html .= '<div class="tiny-green" data-loadbar="'.$percent.'" data-loadbar-text="'.$text.'"><p>'.$title.'</p><div '.$style.'></div><span class="infotxt"></span></div>';
		return '<div class="skills"><div class="cs-chart '.$class.' progress_bar">' . $html . '</div><div class="clear"></div></div>';
	}
	add_shortcode('bar', 'cs_chart_shortcode');
}
//Skills Shortcode end

//=====================================================================
// Adding icon start
//=====================================================================
if (!function_exists('cs_icon_shortcode')) {
	function cs_icon_shortcode($atts, $content = "") {
			$defaults = array( 'border' => '','color' => '','bgcolor' => '','type' => '','cs_custom_class'=>'cs-tooltip-shortcode', 'cs_custom_animation'=>'', 'cs_custom_animation_duration'=>'1');
			extract( shortcode_atts( $defaults, $atts ) );
			
			$CustomId	= '';
			if ( isset( $cs_custom_class ) && $cs_custom_class ) {
				$CustomId	= 'id="'.$cs_custom_class.'"';
			}
		
			if ( trim($cs_custom_animation) !='' ) {
				$cs_quote_animation	= 'wow'.' '.$cs_custom_animation;
			} else {
				$cs_custom_animation	= '';
			}
			$icon_border = "";
			if ( $border == "yes" ){ $icon_border = "icon-border";}
		$html = '<i '.$CustomId.' class="'.$cs_custom_class.' '.$cs_custom_animation.' '.$type.' '.$size.' '.$icon_border. ' '. $class.'" style="color:'.$color.'; animation-duration: '.$cs_custom_animation_duration.'s; background-color:'.$bgcolor.'"></i>';
		return $html;
	}
	add_shortcode('icon', 'cs_icon_shortcode');
}
// adding icon end

//=====================================================================
// Adding code start
//=====================================================================
if (!function_exists('cs_code_shortcode')) {
	function cs_code_shortcode($atts, $content = "") {
		$defaults = array( 'title' => 'Title','content' => '','class'=>'cs-code-shortcode');
		extract( shortcode_atts( $defaults, $atts ) );
		$content = str_replace("<br />", "", $content);
		$title ='<h2 class="section-title">'.$title.'</h2>';
		$html = $title . '<div class="code-element '.$class.'"><pre>' . $content . '</pre></div>';
		return $html . '<div class="clear"></div>';
	}
	add_shortcode('code', 'cs_code_shortcode');
}
// adding code end


//=====================================================================
// get shortcode content
//=====================================================================
if (!function_exists('cs_content_render')) {
	function cs_content_render($atts, $content = ""){
		global $post;
		ob_start();
		 the_content();
		 wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'lassic' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) );
		$content_data = ob_get_clean();
		return $content_data;
	}
	add_shortcode('cs_content', 'cs_content_render');
}

//=====================================================================
// get post attachement
//=====================================================================
if (!function_exists('cs_post_attachment_render')) {
	function cs_post_attachment_render($atts, $content = ""){
		global $post,$cs_xmlObject;
		ob_start();
		$post_attachment = '';
		$args = array(
		   'post_type' => 'attachment',
		   'numberposts' => -1,
		   'post_status' => null,
		   'post_parent' => $post->ID
		  );
		  $attachments = get_posts( $args );
			if ( $attachments ) {
		 ?>
                <div class="cs-media-attachment mediaelements-post">
                <?php 
                foreach ( $attachments as $attachment ) {
					$attachment_title = apply_filters( 'the_title', $attachment->post_title );
					$type = get_post_mime_type( $attachment->ID );
					if($type=='image/jpeg'){
					  ?>
					<a <?php if ( $attachment_title <> '' ) { echo 'data-title="'.$attachment_title.'"'; }?> href="<?php echo esc_url($attachment->guid); ?>" data-rel="<?php echo "prettyPhoto[gallery1]"?>" class="me-imgbox"><?php echo wp_get_attachment_image( $attachment->ID, array(240,180),true ) ?></a>
					<?php
					
					} elseif($type=='audio/mpeg') {
						?>
						<!-- Button to trigger modal --> 
						<a href="#audioattachment<?php echo intval($attachment->ID);?>" role="button" data-toggle="modal" class="iconbox"><i class="icon-microphone"></i></a> 
						<!-- Modal -->
						<div class="modal fade" id="audioattachment<?php echo intval($attachment->ID);?>" tabindex="-1" role="dialog" aria-hidden="true">
						  <div class="modal-dialog">
							<div class="modal-content">
							  <div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							  </div>
							  <div class="modal-body">
								<audio style="width:100%;" src="<?php echo esc_url($attachment->guid); ?>" type="audio/mp3" controls="controls"></audio>
							  </div>
							</div>
							<!-- /.modal-content --> 
						  </div>
						</div>
						<?php
					} elseif($type=='video/mp4') {
					 ?>
					<a href="#videoattachment<?php echo intval($attachment->ID);?>" role="button" data-toggle="modal" class="iconbox"><i class="icon-video-camera"></i></a>
					<div class="modal fade" id="videoattachment<?php echo intval($attachment->ID);?>" tabindex="-1" role="dialog" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						  </div>
						  <div class="modal-body">
							<video width="100%" height="360" poster="">
							  <source src="<?php echo esc_url($attachment->guid); ?>" type="video/mp4" title="mp4">
							</video>
						  </div>
						</div>
						<!-- /.modal-content --> 
					  </div>
					</div>
					<?php
					}
                }
                ?>
                </div>
                <?php  }
		$post_attachment_data = ob_get_clean();
		return $post_attachment_data;
	}
	add_shortcode('cs_post_attachment', 'cs_post_attachment_render');
}



//=====================================================================
// attachments
//=====================================================================
if ( ! function_exists( 'cs_mediaattachments_render' ) ) {
	function cs_mediaattachments_render($atts, $content = ""){
		global $post,$cs_node;
		$defaults = array( 'icon' => '');
		extract( shortcode_atts( $defaults, $atts ) );
		$post_xml = get_post_meta($post->ID, "dynamic_cusotm_post", true);
		global $cs_xmlObject;
		if ( $post_xml <> "" ) {
			$cs_xmlObject = new SimpleXMLElement($post_xml);
		}
		$media_attachment = '';
		if($icon){
			$media_attachment .= '<i class="'.$icon.'"></i>';
		}
		if(count($cs_xmlObject->gallery)>0){
			$media_attachment .= count($cs_xmlObject->gallery);
		}
		return $media_attachment;
	}
	add_shortcode('cs_mediaattachments', 'cs_mediaattachments_render');
}



//=====================================================================
// multi select option
//=====================================================================
if ( ! function_exists( 'cs_multiselect_render' ) ) {
	function cs_multiselect_render($atts, $content = ""){
		global $post,$cs_node;
		$defaults = array( 'name' => '', 'title' => '', 'icon'=>'');
		extract( shortcode_atts( $defaults, $atts ) );
		$post_xml = get_post_meta($post->ID, "dynamic_cusotm_post", true);
		global $cs_xmlObject;
		if ( $post_xml <> "" ) {
			$cs_xmlObject = new SimpleXMLElement($post_xml);
		}
		$cs_multiselect = '';
		if($icon){
			$cs_multiselect .= '<i class="'.$icon.'"></i>';
		}
		if($title){
			$cs_multiselect .= $title;
		}
		if(isset($name)){
			$name = trim($name);
			$cs_multiselect .= $cs_xmlObject->$name;
		}
		return $cs_multiselect;
	}
	add_shortcode('cs_multiselect', 'cs_multiselect_render');
}

//=====================================================================
// post url
//=====================================================================
if ( ! function_exists( 'cs_url_render' ) ) {

	function cs_url_render($atts, $content = ""){
		
		global $post,$cs_node;
		$defaults = array( 'name' => '', 'title' => '', 'icon'=>'');
		extract( shortcode_atts( $defaults, $atts ) );
		
		$post_xml = get_post_meta($post->ID, "dynamic_cusotm_post", true);
		global $cs_xmlObject;
		if ( $post_xml <> "" ) {
			$cs_xmlObject = new SimpleXMLElement($post_xml);
		}
		$cs_url_render = '';
		if($icon){
			$cs_url_render .= '<i class="'.sanitize_html_class($icon).'"></i>';
		}
		if($title){
			$cs_url_render .= $title;
		}
		
		if(isset($name)){
			$name = trim($name);
			$cs_url_render .= $cs_xmlObject->$name;
		}
		return $cs_url_render;
	}
	add_shortcode('cs_url', 'cs_url_render');
}

//=====================================================================
// count media attachments
//=====================================================================
if ( ! function_exists( 'cs_mediaattachment_count_render' ) ) {

	function cs_mediaattachment_count_render($atts, $content = ""){
		
		global $post,$cs_node;
		$defaults = array( 'title' => '', 'icon'=>'fa-camera');
		extract( shortcode_atts( $defaults, $atts ) );
		
		$post_xml = get_post_meta($post->ID, "dynamic_cusotm_post", true);
		global $cs_xmlObject;
		if ( $post_xml <> "" ) {
			$cs_xmlObject = new SimpleXMLElement($post_xml);
		}
		$cs_mediaattachment_count .= '<i class="'.$icon.'"></i> <span class="viewcount cs-bg-color">'.count($cs_xmlObject->gallery).'</span>';
		return $cs_mediaattachment_count;
	}
	add_shortcode('cs_mediaattachment_count', 'cs_mediaattachment_count_render');
}

if ( ! function_exists( 'cs_map_location_link_render' ) ) {

	function cs_map_location_link_render($atts, $content = ""){
		
		global $post;
		$defaults = array( 'icon' => 'fa-map-marker', 'link'=>'#map');
		extract( shortcode_atts( $defaults, $atts ) );
		
		$cs_map_location .= '<a href="'.esc_url(get_permalink()).$link.'"><i class="'.$icon.'"></i></a>';
		return $cs_map_location;
	}
	add_shortcode('cs_map_location', 'cs_map_location_link_render');
}

//=====================================================================
// get location address
//=====================================================================
if ( ! function_exists( 'cs_location_address_render' ) ) {

	function cs_location_address_render($atts, $content = ""){
		global $post;
		$defaults = array( 'icon' => 'fa-map-marker', 'link'=>'#map');
		extract( shortcode_atts( $defaults, $atts ) );
		$post_xml = get_post_meta($post->ID, "dynamic_cusotm_post", true);
		global $cs_xmlObject;
		if ( $post_xml <> "" ) {
			$cs_xmlObject = new SimpleXMLElement($post_xml);
		}
		$cs_location_address = '';
		if(isset($cs_xmlObject->dynamic_post_location_address_icon)){
			$cs_location_address .= '<i class="'.$cs_xmlObject->dynamic_post_location_address_icon.'"></i>';
		}
		if(isset($cs_xmlObject->dynamic_post_location_address)){
			$cs_location_address .= $cs_xmlObject->dynamic_post_location_address;
		}
		return $cs_location_address;

	}
	add_shortcode('cs_location_address', 'cs_location_address_render');
}

//=====================================================================
// Shortcode Array Start
//=====================================================================
if ( ! function_exists( 'cs_shortcode_names' ) ) {
	function cs_shortcode_names(){
	global $post;
	$dcpt_elements_array = array();


		$shortcode_array = array(
		          'accordion'=>array(
						'title'=>'Accordian',
						'name'=>'accordion',
						'icon'=>'icon-list-ul',
						'categories'=>'commonelements',
				 ),

				 'blog'=>array(
						'title'=>'Blog',
						'name'=>'blog',
						'icon'=>'icon-newspaper4',
						'categories'=>'loops',
				 ),
				
				 'button'=>array(
						'title'=>'Button',
						'name'=>'button',
						'icon'=>'icon-heart11',
						'categories'=>'commonelements',
				 ),
				 
				 'call_to_action'=>array(
						'title'=>'Call to Action',
						'name'=>'call_to_action',
						'icon'=>'icon-info-circle',
						'categories'=>'commonelements',
				 ),
			
				 'clients'=>array(
						'title'=>'Clients',
						'name'=>'clients',
						'icon'=>'icon-users',
						'categories'=>'loops',
				 ),
				 
				 'contactus'=>array(
					'title'=>'Form',
					'name'=>'contactus',
					'icon'=>'icon-file-text-o',
					'categories'=>'contentblocks',
				 ),
				 'counter'=>array(
						'title'=>'Counter',
						'name'=>'counter',
						'icon'=>'icon-sort-numeric-asc',
						'categories'=>'commonelements',
				 ),
			 	'contentslider'=>array(
						'title'			=>'Content Slider',
						'name'			=>'contentslider',
						'icon'=>'icon-sliders',
						'categories'	=>'loops',
				 ),
				 'divider'=>array(
						'title'=>'Divider',
						'name'=>'divider',
						'icon'=>'icon-ellipsis',
						'categories'=>'typography misc',
				 ),
				  'dropcap'=>array(
						'title'=>'Dropcap',
						'name'=>'dropcap',
						'icon'=>'icon-font',
						'categories'=>'typography misc',
				 ),
				 'flex_column'=>array(
						'title'=>'Column',
						'name'=>'flex_column',
						'icon'=>'icon-columns',
						'categories'=>'typography misc',
				 ),
				 'heading'=>array(
						'title'=>'Heading',
						'name'=>'heading',
						'icon'=>'icon-h-square',
						'categories'=>'typography misc',
				 ),
				 'highlight'=>array(
						'title'=>'Highlight',
						'name'=>'highlight',
						'icon'=>'icon-pencil-square',
						'categories'=>'typography misc',
				 ),
				 'icons'=>array(
						'title'=>'Icons',
						'name'=>'icons',
						'icon'=>'icon-compass3',
						'categories'=>' contentblocks',
				 ),
				 'infobox'=>array(
						'title'=>'Info box',
						'name'=>'infobox',
						'icon'=>'icon-info-circle',
						'categories'=>' contentblocks',
				 ),
				 'image'=>array(
						'title'=>'Image Frame',
						'name'=>'image',
						'icon'=>'icon-pictures5',
						'categories'=>'mediaelement',
				 ),
				 'list'=>array(
						'title'=>'List',
						'name'=>'list',
						'icon'=>'icon-list-ol',
						'categories'=>'typography misc	',
				 ),
				 'map'=>array(
						'title'=>'Map',
						'name'=>'map',
						'icon'=>'icon-globe4',
						'categories'=>'contentblocks',
				 ),
				 'mesage'=>array(
						'title'=>'Message',
						'name'=>'mesage',
						'icon'=>'icon-envelope-o',
						'categories'=>'typography misc	',
				 ),
				
				 'offerslider'=>array(
						'title'=>'Offer slider',
						'name'=>'offerslider',
						'icon'=>' icon-trophy2',
						'categories'=>' contentblocks',
				 ),
				 'progressbars'=>array(
						'title'=>'Progressbars',
						'name'=>'progressbars',
						'icon'=>'icon-list-alt',
						'categories'=>' commonelements',
				 ),
				 'promobox'=>array(
						'title'=>'Promobox',
						'name'=>'promobox',
						'icon'=>'icon-lifebuoy',
						'categories'=>' mediaelement',
				 ),
				 'pricetable'=>array(
						'title'=>'Pricetable',
						'name'=>'pricetable',
						'icon'=>'icon-money',
						'categories'=>'commonelements',
				 ),
				  'quote'=>array(
						'title'=>'Quote',
						'name'=>'quote',
						'icon'=>'icon-quote-left',
						'categories'=>'typography misc',
				 ),
				 'slider'=>array(
						'title'=>'Slider',
						'name'=>'slider',
						'icon'=>'icon-pictures5',
						'categories'=>'loops',
				 ),
				 'services'=>array(
						'title'=>'Services',
						'name'=>'services',
						'icon'=>'icon-check-square-o',
						'categories'=>' commonelements',
				 ),
				'teams'=>array(
						'title'=>'Team',
						'name'=>'teams',
						'icon'=>'icon-users',
						'categories'=>'loops misc',
				 ),
				 'member'=>array(
						'title'=>'Team Post',
						'name'=>'member',
						'icon'=>'icon-users5',
						'categories'=>'loops misc',
				 ),
				 'tooltip'=>array(
						'title'=>'Tooltip',
						'name'=>'tooltip',
						'icon'=>'icon-comment2',
						'categories'=>'typography misc',
				 ),
				 'tabs'=>array(
						'title'=>'Tabs',
						'name'=>'tabs',
						'icon'=>'icon-list-alt',
						'categories'=>'commonelements',
				 ),
				 'toggle'=>array(
						'title'=>'Toggle',
						'name'=>'toggle',
						'icon'=>'icon-lifebuoy',
						'categories'=>'commonelements',
				 ),
				  'testimonials'=>array(
						'title'=>'Testimonials',
						'name'=>'testimonials',
						'icon'=>'icon-comments2',
						'categories'=>'typography misc',
				 ),
				 'table'=>array(
						'title'=>'Table',
						'name'=>'table',
						'icon'=>'icon-table',
						'categories'=>'commonelements',
				 ),
				 'tweets'=>array(
						'title'=>'Tweets',
						'name'=>'tweets',
						'icon'=>'icon-twitter6',
						'categories'=>'loops',
				 ),
				 'video'=>array(
						'title'=>'Video',
						'name'=>'video',
						'icon'=>'icon-play-circle-o',
						'categories'=>'mediaelement',
				 ),
				 'spacer'=>array(
						'title'=>'Spacer',
						'name'=>'spacer',
						'icon'=>'icon-arrows-v',
						'categories'=>'commonelements',
				 ), 
				 'faq'=>array(
						'title'=>'FAQ',
						'name'=>'faq',
						'icon'=>'icon-help',
						'categories'=>'typography',
				 ),
				  'project'=>array(
						'title'=>'Project',
						'name'=>'project',
						'icon'=>'icon-help',
						'categories'=>'typography',
				 ),
				 
		);
		
		ksort($shortcode_array);
		return $shortcode_array;
	}
}

//=====================================================================
// Shortcode Buttons
//=====================================================================
add_action('media_buttons','cs_shortcode_popup',11);
// 
if ( ! function_exists( 'cs_shortcode_popup' ) ) {
	function cs_shortcode_popup($die = 0, $shortcode='shortcode'){
		$i = 1;
		$style='';
		if ( isset($_POST['action']) ) {
			$name = $_POST['action'];
			$cs_counter = $_POST['counter'];
			$randomno = cs_generate_random_string('5');
			$rand = rand(1,999);
			$style='';
		} else {
			$name = '';
			$cs_counter = '';
			$rand = rand(1,999);
			$randomno = cs_generate_random_string('5');
			if(isset($_REQUEST['action']))
				$name = $_REQUEST['action'];
			$style='style="display:none;"';
		}
		$cs_page_elements_name = array();
		$cs_page_elements_name = cs_shortcode_names();
 
 		$cs_page_categories_name =  cs_elements_categories();
		
	?> 
		<div class="cs-page-composer  <?php echo sanitize_html_class($shortcode);?> composer-<?php echo intval($rand) ?>" id="composer-<?php echo intval($rand) ?>" style="display:none">
			<div class="page-elements">
			<div class="cs-heading-area">
				 <h5>
					<i class="icon-plus-circle"></i> Add Element
				</h5>
				<span class='cs-btnclose' onclick='javascript:removeoverlay("composer-<?php echo esc_js($rand) ?>","append")'><i class="icon-times"></i></span>
			</div>
			<script>
				jQuery(document).ready(function($){
					cs_page_composer_filterable('<?php echo esc_js($rand)?>');
				});
			</script>
		 <div class="cs-filter-content shortcode">
			<p><input type="text" id="quicksearch<?php echo intval($rand) ?>" placeholder="Search" /></p>
			  <div class="cs-filtermenu-wrap">
				<h6>Filter by</h6>
				<ul class="cs-filter-menu" id="filters<?php echo intval($rand) ?>">
				  <li data-filter="all" class="active">Show all</li>
                  <?php foreach($cs_page_categories_name as $key=>$value){
				  		echo '<li data-filter="'.$key.'">'.$value.'</li>';
					}?>
				</ul>
			  </div>
				<div class="cs-filter-inner" id="page_element_container<?php echo intval($rand) ?>">
                	<?php foreach($cs_page_elements_name as $key=>$value){
                    		echo '<div class="element-item '.$value['categories'].'">';
                              $icon = isset($value['icon']) ? $value['icon'] : 'accordion-icon'; ?>
                              <a href='javascript:cs_shortocde_selection("<?php echo esc_js($key);?>","<?php echo admin_url('admin-ajax.php');?>","composer-<?php echo intval($rand) ?>")'><?php cs_page_composer_elements($value['title'], $icon)?></a>
                          </div>
                    <?php }?>
				</div>
			  </div>
			</div>
			<div class="cs-page-composer-shortcode"></div>
		</div>
	   <?php 
		if(isset($shortcode) && $shortcode <> ''){
			?>
			<a class="button" href="javascript:_createpop('composer-<?php echo esc_js($rand) ?>', 'filter')"><i class="icon-plus-circle"></i> CS: Insert shortcode</a>
			<?php
		}
	}
}

//=====================================================================
// Column Size Dropdown Function Start
//=====================================================================
if ( ! function_exists( 'cs_shortcode_element_size' ) ) {
	function cs_shortcode_element_size($column_size =''){
		?>
			<ul class="form-elements">
                <li class="to-label"><label>Size</label></li>
                <li class="to-field select-style">
                    <select class="column_size" id="column_size" name="column_size[]">
                        <option value="1/1" <?php if($column_size == '1/1'){echo 'selected="selected"';}?>>Full width</option>
                        <option value="1/2" <?php if($column_size == '1/2'){echo 'selected="selected"';}?>>One half</option>
                        <option value="1/3" <?php if($column_size == '1/3'){echo 'selected="selected"';}?>>One third</option
                        ><option value="2/3" <?php if($column_size == '2/3'){echo 'selected="selected"';}?>>Two third</option>
                        <option value="1/4" <?php if($column_size == '1/4'){echo 'selected="selected"';}?>>One fourth</option>
                        <option value="3/4" <?php if($column_size == '3/4'){echo 'selected="selected"';}?>>Three fourth</option>
                    </select>
                    <p>Select column width. This width will be calculated depend page width</p>
                </li>                  
            </ul>
		<?php
	}
}
// Column Size Dropdown Function end

//=====================================================================
// Animation Styles
//=====================================================================
function cs_animation_style(){
	return $animation_style = array(
						'Entrances'=>array('slideDown'=>'slideDown','slideUp'=>'slideUp','slideLeft'=>'slideLeft','slideRight'=>'slideRight','slideExpandUp'=>'slideExpandUp','expandUp'=>'expandUp','expandOpen'=>'expandOpen','bigEntrance'=>'bigEntrance','hatch'=>'hatch'),
						'Misc'=>array('floating'=>'floating','tossing'=>'tossing','pullUp'=>'pullUp','pullDown'=>'pullDown','stretchLeft'=>'stretchLeft','stretchRight'=>'stretchRight'),
						'Attention Seekers'=>array('bounce'=>'bounce','flash'=>'flash','pulse'=>'pulse','rubberBand'=>'rubberBand','shake'=>'shake','swing'=>'swing','tada'=>'tada','wobble'=>'wobble'),
						'Bouncing Entrances'=>array('bounceIn'=>'bounceIn','bounceInDown'=>'bounceInDown','bounceInLeft'=>'bounceInLeft','bounceInRight'=>'bounceInRight','bounceInUp'=>'bounceInUp'),
                 		'Fading Entrances'=>array('fadeIn'=>'fadeIn','fadeInDown'=>'fadeInDown','fadeInDownBig'=>'fadeInDownBig','fadeInLeft'=>'fadeInLeft','fadeInLeftBig'=>'fadeInRight','fadeInRightBig'=>'fadeInRightBig','fadeInUp'=>'fadeInUp','fadeInUpBig'=>'fadeInUpBig'),
						'Flippers'=>array('flip'=>'flip','flipInX'=>'flipInX','flipInY'=>'flipInY'),
						'Lightspeed'=>array('lightSpeedIn'=>'lightSpeedIn'),
						 'Rotating Entrances'=>array('rotateIn'=>'rotateIn','rotateInDownLeft'=>'rotateInDownLeft','rotateInDownRight'=>'rotateInDownRight','rotateInUpLeft'=>'rotateInUpLeft','rotateInUpRight'=>'rotateInUpRight'),
						'Specials'=>array('hinge'=>'hinge','rollIn'=>'rollIn'),
						'Zoom Entrances'=>array('zoomIn'=>'zoomIn','zoomInDown'=>'zoomInDown','zoomInLeft'=>'zoomInLeft','zoomInRight'=>'zoomInRight','zoomInUp'=>'zoomInUp'),
						);	
}

//=====================================================================
// Custom Class and Animations Function Start
//=====================================================================
if ( ! function_exists( 'cs_shortcode_custom_classes' ) ) {
	function cs_shortcode_custom_classes($cs_custom_class = '',$cs_custom_animation='',$cs_custom_animation_duration='1'){
		?>
        	<ul class="form-elements">
                <li class="to-label"><label>Custom ID</label></li>
                <li class="to-field">
                    <input type="text" name="cs_custom_class[]" class="txtfield"  value="<?php echo sanitize_text_field($cs_custom_class); ?>" />
                    <p>Use this option if you want to use specified Class for this element	</p>
                </li>
            </ul>
            <?php $custom_animation_array = array('fade'=>'Fade','slide'=>'Slide','left-slide'=>'left Slide');?>
            
            <ul class="form-elements">
                <li class="to-label"><label>Animation Class <?php echo sanitize_text_field($cs_custom_animation);?></label></li>
                <li class="to-field select-style">
                	<select class="dropdown" name="cs_custom_animation[]">
                    	<option value="">Animation Class</option>
                        <?php 
								$animation_array = cs_animation_style();
								foreach($animation_array as $animation_key=>$animation_value){
									echo '<optgroup label="'.$animation_key.'">';	
									foreach($animation_value as $key=>$value){
										$active_class = '';
										if($cs_custom_animation == $key){$active_class = 'selected="selected"';}
										echo '<option value="'.$key.'" '.$active_class.'>'.$value.'</option>';
									}
								}
						?>
                      </select>
                      <p>Select Entrance animation type from the dropdown </p>
                </li>
            </ul>
        <?php
	}
}
// Custom Class and Animations Function end

//=====================================================================
// Dynamic Custom Class and Animations Function Start
//=====================================================================
if ( ! function_exists( 'cs_shortcode_custom_dynamic_classes' ) ) {
	function cs_shortcode_custom_dynamic_classes($cs_custom_class = '',$cs_custom_animation='',$cs_custom_animation_duration='1',$prefix){
		?>
        	<ul class="form-elements">
                <li class="to-label"><label>Custom ID</label></li>
                <li class="to-field">
                    <input type="text" name="<?php echo sanitize_text_field($prefix);?>_class[]" class="txtfield"  value="<?php echo sanitize_text_field($cs_custom_class)?>" />
                    <p>Use this option if you want to use specified id for this element</p>
                </li>
            </ul>
            <?php $custom_animation_array = array('fade'=>'Fade','slide'=>'Slide','left-slide'=>'left Slide');?>
            <ul class="form-elements">
                <li class="to-label"><label>Animation Class <?php echo sanitize_text_field($cs_custom_animation);?></label></li>
                <li class="to-field select-style">
                	<select class="dropdown" name="<?php echo sanitize_text_field($prefix);?>_animation[]">
                    	<option value="">Select Animation</option>
                        <?php 
								$animation_array = cs_animation_style();
								foreach($animation_array as $animation_key=>$animation_value){
									echo '<optgroup label="'.$animation_key.'">';	
									foreach($animation_value as $key=>$value){
										$active_class = '';
										if($cs_custom_animation == $key){$active_class = 'selected="selected"';}
										echo '<option value="'.$key.'" '.$active_class.'>'.$value.'</option>';
									}
								}
						
						?>
                      </select>
                      <p>Select Entrance animation type from the dropdown</p>
                </li>
            </ul>  
        <?php
	}
}
// Dynamic Custom Class and Animations Function end


//=====================================================================
// Shortcode Add box Ajax Function
//=====================================================================
if ( ! function_exists( 'cs_shortcode_element_ajax_call' ) ) {
	function cs_shortcode_element_ajax_call(){?>
	<?php 	
		if(isset($_POST['shortcode_element']) && $_POST['shortcode_element']){
			if($_POST['shortcode_element'] == 'services'){
				$rand_id = rand(8,7777);
				?>
				<div class='cs-wrapp-clone cs-shortcode-wrapp'  id="cs_infobox_<?php echo intval( $rand_id);?>">
					<header><h4><i class='icon-arrows'></i>Service</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                    
                    <?php if ( function_exists( 'cs_shortcode_element_size' ) ) {cs_shortcode_element_size();}?>
					<ul class='form-elements'>
						<li class='to-label'><label>Service Title:</label></li>
						<li class='to-field'> <div class='input-sec'><input class='txtfield' type='text' name='cs_service_title[]' /></div>
						<div class='left-info'><p>Title of the Service.</p></div>
						</li>
					</ul>
					<ul class='form-elements'>
						<li class='to-label'><label>Service View:</label></li>
						<li class='to-field select-style'> <div class='input-sec'><select name='cs_service_type[]' class='dropdown'>
                        <option value='size_large'>Large Boxed</option>
                        <option value='size_large_normal'>Large Normal</option>
                        <option value='size_circle'>Circle</option>
                        <option  value="size_medium" >Medium</option>
                        <option value='size_small'>Small</option>
                        </select></div>
						<div class='left-info'><p>Type of the Service.</p></div>
						</li>
					</ul>
					 <ul class='form-elements' id="<?php echo intval( $rand_id);?>">
							<li class='to-label'><label>IcoMoon Icon:</label></li>
							<li class="to-field">
								<?php cs_fontawsome_icons_box('',$rand_id,'cs_service_icon');?>
							</li>
					</ul>
                    <ul class="form-elements">
                        <li class="to-label"><label>Icon Postion</label></li>
                        <li class="to-field select-style">
                            <select class="service_icon_postion" name="cs_service_icon_postion[]">
                                <option value="left">left</option>
                                <option value="right">Right</option>
                                <option value="top">Top</option>
                                <option value="center">Center</option>
                            </select>
                        </li>                  
                    </ul>
                    <ul class="form-elements">
                        <li class="to-label"><label>Icon Type</label></li>
                        <li class="to-field select-style">
                            <select class="service_icon_type" name="cs_service_icon_type[]">
                                <option value="circle">Circle</option>
                                <option value="square">Square</option>
                            </select>
                        </li>                  
                    </ul>
                    <ul class="form-elements">
                          <li class="to-label">
                            <label>Service Bg Image</label>
                          </li>
                          <li class="to-field">
                            <input id="cs_service_bg_image<?php echo intval( $rand_id);?>" name="cs_service_bg_image[]" type="hidden" class="" value=""/>
                            <input name="cs_service_bg_image<?php echo intval( $rand_id);?>"  type="button" class="uploadMedia left" value="Browse"/>
                          </li>
                        </ul>
                        <div class="page-wrap" style="overflow:hidden; display:none;?>" id="cs_service_bg_image<?php echo intval( $rand_id);?>_box" >
                          <div class="gal-active">
                            <div class="dragareamain" style="padding-bottom:0px;">
                              <ul id="gal-sortable">
                                <li class="ui-state-default" id="">
                                  <div class="thumb-secs"> <img src="<?php echo esc_url($service_bg_image);?>"  id="cs_service_bg_image<?php echo intval( $rand_id);?>_img" width="100" height="150"  />
                                    <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_service_bg_image<?php echo intval( $rand_id);?>')" class="delete"></a> </div>
                                  </div>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
					<ul class='form-elements'>
						<li class='to-label'><label>Service Link URL:</label></li>
						<li class='to-field'> <div class='input-sec'><input class='txtfield' type='text' name='cs_service_link_url[]' /></div>
						<div class='left-info'><p>Service Link Url</p></div>
						</li>
					</ul>
                    <ul class="form-elements">
                        <li class="to-label"><label>Border</label></li>
                        <li class="to-field select-style">
                            <select class="service_border" id="cs_service_border" name="cs_service_border[]">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </li>                  
                    </ul>
					<ul class='form-elements'>
						<li class='to-label'><label>Service Text:</label></li>
						<li class='to-field'> <div class='input-sec'><textarea class='txtfield' data-content-text="cs-shortcode-textarea" name='cs_service_text[]'></textarea></div>
						</li>
					</ul>
                    <ul class="form-elements">
                        <li class="to-label"><label>Divider</label></li>
                        <li class="to-field select-style">
                            <select class="service_divider" name="cs_service_divider[]">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </li>                  
                    </ul>
                    <ul class="form-elements">
                        <li class="to-label"><label>Icon Color</label></li>
                        <li class="to-field">
                            <input type="text" name="cs_service_icon_color[]" class="bg_color"  value="" />
                        </li>
                    </ul>
                    <ul class="form-elements">
                        <li class="to-label"><label>Icon Background Color</label></li>
                        <li class="to-field">
                            <input type="text" name="cs_service_icon_bg_color[]" class="bg_color"  value="" />
                        </li>
                    </ul>
                  	 <ul class="form-elements">
                        <li class="to-label"><label>Custom ID</label></li>
                        <li class="to-field">
                            <input type="text" name="cs_service_class[]" class="txtfield"  value="" />
                        </li>
                     </ul>
                    <ul class="form-elements">
                        <li class="to-label"><label>Animation Class </label></li>
                        <li class="to-field select-style">
                            <select class="dropdown" name="cs_service_animation[]">
                                <option value="">Select Animation</option>
                                <?php 
                                    $animation_array = cs_animation_style();
                                    foreach($animation_array as $animation_key=>$animation_value){
                                        echo '<optgroup label="'.$animation_key.'">';	
                                        foreach($animation_value as $key=>$value){
                                            echo '<option value="'.$key.'" >'.$value.'</option>';
                                        }
                                    }
                                
                                 ?>
                              </select>
                        </li>
                    </ul>
				</div>
				<?php     
			} 
			else if($_POST['shortcode_element'] == 'accordions'){
				 $rand_id = rand(5,999);
				?>
                	<div class='cs-wrapp-clone cs-shortcode-wrapp'  id="cs_infobox_<?php echo intval( $rand_id);?>">
                        <header><h4><i class='icon-arrows'></i>Accordion</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                        <ul class='form-elements'>
                            <li class='to-label'><label>Active</label></li>
                            <li class='to-field select-style'> <select name='cs_accordion_active[]'><option value="no">No</option><option value="yes">Yes</option></select>
                            <div class='left-info'>
                              <p>You can set the section that is active here by select dropdown</p>
                            </div>
                            </li>
                        </ul>
                        <ul class='form-elements'>
                            <li class='to-label'><label>Accordion Title:</label></li>
                            <li class='to-field'> <div class='input-sec'><input class='txtfield' type='text' name='cs_accordion_title[]' /></div>
                            <div class='left-info'>
                              <p>Enter accordion title</p>
                            </div>
                            </li>
                        </ul>
                        <ul class='form-elements' id="<?php echo intval($rand_id);?>">
							<li class='to-label'><label>IcoMoon Icon:</label></li>
							<li class="to-field">
                                    <?php cs_fontawsome_icons_box('',$rand_id,'cs_accordian_icon');?>
                                    
                                    <div class='left-info'>
                                      <p>select the fontawsome Icons you would like to add to your menu items</p>
                                    </div>
                            </li>
                        </ul>
                        <ul class='form-elements'>
                            <li class='to-label'><label>Accordion Text:</label></li>
                            <li class='to-field'> <div class='input-sec'><textarea class='txtfield' data-content-text="cs-shortcode-textarea" name='accordion_text[]'></textarea></div>
                            <div class='left-info'>
                              <p>Enter your content.</p>
                            </div>
                            </li>
                        </ul>
                    </div>
                <?php	
			
		
		    }
			else if($_POST['shortcode_element'] == 'faq'){
				 $rand_id = rand(5,999);
				?>
                	<div class='cs-wrapp-clone cs-shortcode-wrapp'  id="cs_infobox_<?php echo intval( $rand_id);?>">
                        <header><h4><i class='icon-arrows'></i>FAQ</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                        <ul class='form-elements'>
                            <li class='to-label'><label>Active</label></li>
                            <li class='to-field select-style'> <select name='cs_faq_active[]'><option value="no">No</option><option value="yes">Yes</option></select>
                            </li>
                        </ul>
                        <ul class='form-elements'>
                            <li class='to-label'><label>Faq Title:</label></li>
                            <li class='to-field'> <div class='input-sec'><input class='txtfield' type='text' name='cs_faq_title[]' /></div>
                            </li>
                        </ul>
                        <ul class='form-elements' id="<?php echo intval( $rand_id);?>">
							<li class='to-label'><label>IcoMoon Icon:</label></li>
							<li class="to-field">
                                    <?php cs_fontawsome_icons_box('',$rand_id,'cs_faq_icon');?>
                            </li>
                        </ul>
                        <ul class='form-elements'>
                            <li class='to-label'><label>Faq Text:</label></li>
                            <li class='to-field'> <div class='input-sec'><textarea class='txtfield' data-content-text="cs-shortcode-textarea" name='faq_text[]'></textarea></div>
                            </li>
                        </ul>
                    </div>
                <?php	
				
		 }
		else if($_POST['shortcode_element'] == 'tabs'){
			$rand_id = rand(40, 9999999);
		?>
                	<div class='cs-wrapp-clone cs-shortcode-wrapp add_tabs  cs-pbwp-content'  id="cs_infobox_<?php echo intval( $rand_id);?>">
								<header><h4><i class='icon-arrows'></i>Tab</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
								<ul class='form-elements'>
									<li class='to-label'><label>Active</label></li>
									<li class='to-field'> 
                                    	<div class="select-style"><select name='cs_tab_active[]'><option value="no">No</option><option value="yes">Yes</option></select></div>
                                        <div class='left-info'>
                                          <p>You can set the section that is active here by select dropdown</p>
                                        </div>
									</li>
								</ul>
								<ul class='form-elements'>
									<li class='to-label'><label>Tab Title:</label></li>
									<li class='to-field'> <div class='input-sec'><input class='txtfield' type='text' name='cs_tab_title[]' /></div>
									</li>
								</ul>
                                <ul class='form-elements' id="<?php echo intval( $rand_id);?>">
                                	<li class='to-label'><label>IcoMoon Icon:</label></li>
                                	<li class="to-field">
                                        <?php cs_fontawsome_icons_box('',$rand_id,'cs_tab_icon');?>
                                        <div class='left-info'>
                                          <p> select the fontawsome Icons you would like to add to your menu items</p>
                                        </div>
                                	</li>
                                </ul>
                                <ul class='form-elements'>
									<li class='to-label'><label>Tab Text:</label></li>
									<li class='to-field'> <div class='input-sec'><textarea class='txtfield' data-content-text="cs-shortcode-textarea" name='cs_tab_text[]'></textarea></div>
                                    <div class='left-info'>
                                      <p>Enter tab body content here</p>
                                    </div>
									</li>
								</ul>
							</div>
                <?php	
			}
			else if($_POST['shortcode_element'] == 'testimonials'){
				 $rand_id = rand(5,999);
				?>
                    <div class='cs-wrapp-clone cs-shortcode-wrapp cs-pbwp-content'  id="cs_infobox_<?php echo intval( $rand_id);?>">
                        <header><h4><i class='icon-arrows'></i>Testimonial</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                        <ul class='form-elements'>
                            <li class='to-label'><label>Text:</label></li>
                            <li class='to-field'> <div class='input-sec'><textarea class='txtfield' data-content-text="cs-shortcode-textarea" name='cs_testimonial_text[]'></textarea></div>
                            </li>
                        </ul>
                        <ul class='form-elements'>
                            <li class='to-label'><label>Author:</label></li>
                            <li class='to-field'> <div class='input-sec'><input class='txtfield' type='text' name='cs_testimonial_author[]' /></div></li>
                        </ul>
                        <ul class='form-elements'>
                            <li class='to-label'><label>Company:</label></li>
                            <li class='to-field'> <div class='input-sec'><input class='txtfield' type='text' name='cs_testimonial_company[]' /></div>
                            <div class='left-info'><p>Company Name</p></div>
                            </li>
                        </ul>
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Background Image</label>
                          </li>
                          <li class="to-field">
                            <input id="cs_testimonial_img<?php echo intval( $rand_id);?>" name="cs_testimonial_img[]" type="hidden" class="" value=""/>
                            <input name="cs_testimonial_img<?php echo intval( $rand_id);?>"  type="button" class="uploadMedia left" value="Browse"/>
                          </li>
                        </ul>
                        <div class="page-wrap" style="overflow:hidden; display:none" id="cs_testimonial_img<?php echo intval( $rand_id);?>_box" >
                          <div class="gal-active">
                            <div class="dragareamain" style="padding-bottom:0px;">
                              <ul id="gal-sortable">
                                <li class="ui-state-default" id="">
                                  <div class="thumb-secs"> <img src=""  id="cs_testimonial_img<?php echo intval( $rand_id);?>_img" width="100" height="150"  />
                                    <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_testimonial_img<?php echo intval( $rand_id);?>')" class="delete"></a> </div>
                                  </div>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                </div>
                <?php	
			} 
			else if($_POST['shortcode_element'] == 'counter'){
				$counter_count = rand(40, 9999999);
				?>
                <div class='cs-wrapp-clone cs-shortcode-wrapp' id="cs_infobox_<?php echo intval($counter_count);?>">
                        <header><h4><i class='icon-arrows'></i>Counter</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                        <ul class="form-elements">
                            <li class="to-label"><label>Counter Title</label></li>
                            <li class="to-field"><input type="text"  name="cs_counter_title[]"  class="txtfield"  /></li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Type</label></li>
                            <li class="to-field">
                                <div class="select-style">
                                    <select name="cs_counter_style[]" class="dropdown" >
                                        <option value="one" >Counter Style One</option>
                                        <option value="two" >Counter Style Two</option>
                                        <option value="three" >Counter Style Three</option>
                                        <option value="four" >Counter Style Four</option>
                                     </select>
                                 </div>
                            </li>
                        </ul>
                        
                        <ul class="form-elements">
                            <li class="to-label"><label>Choose Icon</label></li>
                            <li class="to-field">
                                <div class="select-style">
                                    <select name="cs_counter_icon_type[]" class="dropdown" onchange="cs_counter_image(this.value,'<?php echo esc_js($counter_count)?>','')">
                                        <option <?php if($counter_item->counter_icon_type=="icon")echo "selected";?> value="icon" >Icon</option>
                                        <option <?php if($counter_item->counter_icon_type=="image")echo "selected";?> value="image" >Image</option>
                                     </select>
                                 </div>
                            </li>
                        </ul>
                        
                        <div class="selected_icon_type" id="selected_icon_type<?php echo intval($counter_count)?>">
                        	 <ul class='form-elements' id="<?php echo intval($counter_count);?>">
								<li class='to-label'><label>IcoMoon Icon:</label></li>
								<li class="to-field">
                                     <?php cs_fontawsome_icons_box('',$counter_count,'cs_counter_icon');?>
                            	</li>
                         </ul>
                         	<ul class="form-elements">
                                <li class="to-label"><label>Icon Color</label></li>
                                <li><input type="text"  name="cs_icon_color[]"  class="bg_color"  /></li>
                            </ul>
                        </div>
                        <div class="selected_image_type" id="selected_image_type<?php echo intval($counter_count)?>" style="display:none">
                       		<ul class="form-elements">
                              <li class="to-label">
                                <label>Image</label>
                              </li>
                              <li class="to-field">
                                <input id="cs_counter_logo<?php echo intval($counter_count)?>" name="cs_counter_logo[]" type="hidden" class="" value=""/>
                                <input name="cs_counter_logo<?php echo intval($counter_count)?>"  type="button" class="uploadMedia left" value="Browse"/>
                              </li>
                            </ul>
                            <div class="page-wrap" style="overflow:hidden; display:<?php echo 'none';?>" id="cs_counter_logo<?php echo intval($counter_count)?>_box" >
                              <div class="gal-active">
                                <div class="dragareamain" style="padding-bottom:0px;">
                                  <ul id="gal-sortable">
                                    <li class="ui-state-default" id="">
                                      <div class="thumb-secs"> <img src="<?php echo cs_allow_special_char($counter_count);?>"  id="cs_counter_logo<?php echo intval($counter_count)?>_img" width="100" height="150"  />
                                        <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_counter_logo<?php echo esc_js($counter_count)?>')" class="delete"></a> </div>
                                      </div>
                                    </li>
                                  </ul>
                                </div>
                              </div>
                            </div>
        				</div>
						
                        <ul class="form-elements">
                            <li class="to-label"><label>Background Color</label></li>
                            <li><input type="text"  name="cs_counter_bg_color[]" class="bg_color" value="" /></li>
                        </ul>
                                        
                        <ul class="form-elements">
                            <li class="to-label"><label>Numbers</label></li>
                            <li class="to-field"><input type="text" name="cs_counter_numbers[]" class="txtfield" value="" /></li>
                        </ul>
                      	<ul class="form-elements">
                            <li class="to-label"><label>Count Text Color</label></li>
                            <li><input type="text" name="cs_counter_text_color[]" class="bg_color" /></li>
                        </ul>
                        
                        <ul class="form-elements">
                            <li class="to-label"><label>Link Title</label></li>
                            <li class="to-field"><input type="text" name="cs_counter_icon_linktitle[]" class="txtfield" /></li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Link</label></li>
                            <li class="to-field"><input type="text" name="cs_counter_icon_linkurl[]" class="txtfield"/></li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Button Color</label></li>
                            <li><input type="text"  name="cs_counter_link_bgcolor[]" class="bg_color"  /></li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Text</label></li>
                            <li class="to-field"><textarea type="text" name="counter_text[]" class="txtfield" data-content-text="cs-shortcode-textarea" /><?php echo cs_allow_special_char($counter_text)?></textarea></li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Custom ID</label></li>
                            <li class="to-field">
                                <input type="text" name="cs_coutner_class[]" class="txtfield"  value="" />
                            </li>
                         </ul>
                       
                        <ul class="form-elements">
                            <li class="to-label"><label>Animation Class </label></li>
                            <li class="to-field select-style">
                                <select class="dropdown" name="cs_coutner_animation[]">
                                    <option value="">Select Animation</option>
                                    <?php 
									
                                        $animation_array = cs_animation_style();
                                        foreach($animation_array as $animation_key=>$animation_value){
                                            echo '<optgroup label="'.$animation_key.'">';	
                                            foreach($animation_value as $key=>$value){
                                                echo '<option value="'.$key.'" >'.$value.'</option>';
                                            }
                                        }
                                    
                                     ?>
                                  </select>
                            </li>
                        </ul>
                      
                	</div>
                <?php	} 
			else if ($_POST['shortcode_element'] == 'list'){
							$rand_id = rand(40, 9999999);
						?>
                	<div class='cs-wrapp-clone cs-shortcode-wrapp' id="cs_infobox_<?php echo intval($rand_id);?>">
                            <header><h4><i class='icon-arrows'></i>List Item(s)</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                            <ul class='form-elements'>
                                <li class='to-label'><label>List Item:</label></li>
                                <li class='to-field'> <div class='input-sec'><input class='txtfield' type='text' name='cs_list_item[]' data-content-text="cs-shortcode-textarea" /></div>
                                </li>
                            </ul> 
                            <ul class='form-elements' id="<?php echo intval( $rand_id);?>">
								<li class='to-label'><label>IcoMoon Icon:</label></li>
								<li class="to-field">
                                <?php cs_fontawsome_icons_box('',$rand_id,'cs_list_icon');?>
                            </li>
                         </ul>
                    </div>
                <?php	
			}  
			else if ($_POST['shortcode_element'] == 'infobox_items'){
					$rand_id = rand(40, 9999999);
				?>
                    <div class='cs-wrapp-clone cs-shortcode-wrapp' id="cs_infobox_<?php echo intval( $rand_id);?>">
                            <header><h4><i class='icon-arrows'></i>Infobox Item(s)</h4> 
                                <a href='#' class='deleteit_node'>
                                    <i class='icon-minus-circle'></i>Remove
                                </a>
                            </header>
                            
                             <ul class='form-elements' id="<?php echo intval( $rand_id);?>">
								<li class='to-label'><label>IcoMoon Icon:</label></li>
								<li class="to-field">
                                   <?php cs_fontawsome_icons_box('',$rand_id,'cs_infobox_list_icon');?>
                           		 </li>
                         </ul>
                         <ul class='form-elements'>
                            <li class='to-label'><label>Icon Color:</label></li>
                            <li class='to-field'> <div class='input-sec'><input class='bg_color' type='text' name='cs_infobox_list_color[]' /></div>
                            </li>
                        </ul> 
                        <ul class='form-elements'>
                            <li class='to-label'><label>Title:</label></li>
                            <li class='to-field'> <div class='input-sec'><input class='txtfield' type='text' name='cs_infobox_list_title[]' /></div>
                            </li>
                        </ul>
                         <ul class='form-elements'>
                            <li class='to-label'><label>Short Description:</label></li>
                            <li class='to-field'> <div class='input-sec'><textarea name='cs_infobox_list_description[]' rows="8" cols="20" data-content-text="cs-shortcode-textarea" /></textarea></div>
                            </li>
                        </ul> 
                       <?php /*?> <ul class='form-elements'>
                            <li class='to-label'><label>Text Color:</label></li>
                            <li class='to-field'> <div class='input-sec'><input class='bg_color' type='text' name='cs_infobox_list_text_color[]' /></div>
                            </li>
                        </ul> <?php */?>
                    </div>
                <?php	
			} 
			else if ($_POST['shortcode_element'] == 'audio'){
				$rand_id = 'clinets_'.rand(40, 9999999);
				?>
                	<div class='cs-wrapp-clone cs-shortcode-wrapp' id="cs_infobox_<?php echo intval( $rand_id);?>">
                        <header><h4><i class='icon-arrows'></i>Album Item(s)</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                        <ul class="form-elements">
                            <li class="to-label"><label>Track Title</label></li>
                            <li class="to-field">
                                <input type="text" id="cs_album_track_title" name="cs_album_track_title[]" value="Track Title" />
                            </li>
                        </ul>
                        
                        <ul class="form-elements">
                            <li class="to-label"><label>Track MP3 URL</label></li>
                            <li class="to-field">
                                <input id="cs_album_track_mp3_url" name="cs_album_track_mp3_url[]" value="" type="text" class="small" />
                                <!--<input id="custom_media_upload" name="cs_album_track_mp3_url" type="button" class="uploadfile left" value="Browse"/>-->
                            </li>
                        </ul>
                        
                </div>
                <?php	
			}
			else if ($_POST['shortcode_element'] == 'clients'){
				$clients_count = 'clinets_'.rand(40, 9999999);
				?>
                	<div class='cs-wrapp-clone cs-shortcode-wrapp' id="cs_infobox_<?php echo cs_allow_special_char($clients_count);?>">
                        <header><h4><i class='icon-arrows'></i>Client</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Title</label>
                          </li>
                          <li class="to-field">
                            <input type="text" id="cs_client_title" class="" name="cs_client_title[]" value="" />
                          </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Background Color</label></li>
                            <li class="to-field">
                                <input type="text" id="cs_bg_color" class="bg_color" name="cs_bg_color[]" value="" />
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Website URL</label></li>
                            <li class="to-field">
                                <div class="input-sec"> <input type="text" id="cs_website_url" class="" name="cs_website_url[]" value="" /></div>
                                <div class="left-info"><p>e.g. http://yourdomain.com/</p></div>
                            </li>
                        </ul>
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Client Logo</label>
                          </li>
                          <li class="to-field">
                            <input id="cs_client_logo<?php echo cs_allow_special_char($clients_count)?>" name="cs_client_logo[]" type="hidden" class="" value=""/>
                            <input name="cs_client_logo<?php echo cs_allow_special_char($clients_count)?>"  type="button" class="uploadMedia left" value="Browse"/>
                          </li>
                        </ul>
                        <div class="page-wrap" style="overflow:hidden; display:<?php echo 'none';?>" id="cs_client_logo<?php echo cs_allow_special_char($clients_count)?>_box" >
                          <div class="gal-active">
                            <div class="dragareamain" style="padding-bottom:0px;">
                              <ul id="gal-sortable">
                                <li class="ui-state-default" id="">
                                  <div class="thumb-secs"> <img src="<?php echo cs_allow_special_char($clients_count);?>"  id="cs_client_logo<?php echo cs_allow_special_char($clients_count);?>_img" width="100" height="150"  />
                                    <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_client_logo<?php echo cs_allow_special_char($clients_count)?>')" class="delete"></a> </div>
                                  </div>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                </div>
                <?php	
			}
			else if ($_POST['shortcode_element'] == 'progressbars'){
				$rand_id = rand(40, 9999999);
				?>
                	<div class='cs-wrapp-clone cs-shortcode-wrapp cs-pbwp-content' id="cs_infobox_<?php echo intval( $rand_id);?>">
                        <header><h4><i class='icon-arrows'></i>Progressbars</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                        <ul class="form-elements">
                            <li class="to-label"><label>ProgressBars Title</label></li>
                            <li class="to-field">
                                <input type="text" name="cs_progressbars_title[]" class="txtfield" value="" />
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Skill (in percentage)</label></li>
                            <li class="to-field">
                                <div class="cs-drag-slider" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value=""></div>
                                <input  class="cs-range-input"  name="cs_progressbars_percentage[]" type="text" value=""   />
                                <p>Set the Skill (In %)</p>
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>ProgressBars Color</label></li>
                            <li class="to-field">
                                <input type="text" name="cs_progressbars_color[]" class="bg_color" value="#000" />
                            </li>
                        </ul>
                </div>
                <?php	
			}
			else if ($_POST['shortcode_element'] == 'offerslider'){
				$offer_count = 'offer_'.rand(40, 9999999);
				?>
                	<div class='cs-wrapp-clone cs-shortcode-wrapp' id="cs_infobox_<?php echo intval($offer_count);?>">
                        <header><h4><i class='icon-arrows'></i>Offer Slider Item</h4> <a href='#' class='deleteit_node'><i class='icon-minus-circle'></i>Remove</a></header>
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Image</label>
                          </li>
                          <li class="to-field">
                            <input id="cs_slider_image<?php echo intval($offer_count)?>" name="cs_slider_image[]" type="hidden" class="" value=""/>
                            <input name="cs_slider_image<?php echo intval($offer_count)?>"  type="button" class="uploadMedia left" value="Browse"/>
                          </li>
                        </ul>
                        <div class="page-wrap" style="overflow:hidden; display:<?php echo 'none';?>" id="cs_slider_image<?php echo intval($offer_count) ?>_box"  >
                          <div class="gal-active">
                            <div class="dragareamain" style="padding-bottom:0px;">
                              <ul id="gal-sortable">
                                <li class="ui-state-default" id="">
                                  <div class="thumb-secs"> <img src=""  id="cs_slider_image<?php echo intval($offer_count)?>_img" width="100" height="150"  />
                                    <div class="gal-edit-opts"> <a   href="javascript:del_media('cs_slider_image<?php echo esc_js($offer_count) ?>')" class="delete"></a> </div>
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
                            <input type="text" name="cs_slider_title[]" class="txtfield" value="" />
                          </li>
                        </ul>
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Content(s)</label>
                          </li>
                          <li class="to-field">
                            <textarea name="cs_slider_contents[]" data-content-text="cs-shortcode-textarea"></textarea>
                          </li>
                        </ul>
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Link</label>
                          </li>
                          <li class="to-field">
                            <input type="text" name="cs_readmore_link[]" class="txtfield" value="" />
                          </li>
                        </ul>
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Link Title</label>
                          </li>
                          <li class="to-field">
                            <input type="text" name="cs_offerslider_link_title[]" class="txtfield" value="" />
                          </li>
                        </ul>
                </div>
                <?php	
			}  
		}
		exit;
	}
	add_action('wp_ajax_cs_shortcode_element_ajax_call', 'cs_shortcode_element_ajax_call');
}