<?php
// Save Theme Options
if ( ! function_exists( 'theme_option_save' ) ) {
	function theme_option_save() {
		global $reset_date,$options;
		$_POST = stripslashes_htmlspecialchars($_POST);
  		if(isset($_POST['cs_import_theme_options']) and $_POST['cs_import_theme_options'] <> ''){
 			if (is_serialized(base64_decode($_POST['cs_import_theme_options'])) =="1"){
				update_option( "cs_theme_options", unserialize(base64_decode($_POST['cs_import_theme_options'])));
				_e("All Settings Saved",'lassic');
 				
			} else {
				echo is_serialized(base64_decode($_POST['cs_import_theme_options']));
				_e('Data is Not Valid','lassic');
 			}
			
		}else{
			update_option( "cs_theme_options",$_POST );
			_e("All Settings Saved",'lassic');
		}
		die();
	}
	add_action('wp_ajax_theme_option_save', 'theme_option_save');
}


// saving all the theme options end
if ( ! function_exists( 'theme_option_rest_all' ) ) {
	function theme_option_rest_all() {
		delete_option('cs_theme_options');
		update_option( "cs_theme_options", cs_reset_data());
		_e("All Settings Saved",'lassic');
		die();
	 }
	add_action('wp_ajax_theme_option_rest_all', 'theme_option_rest_all');
}
// theme activation
if ( ! function_exists( 'cs_activation_data' ) ) {
	function cs_activation_data(){
		update_option('cs_theme_options',cs_reset_data());
	}
}

/* return array for reset theme options*/
if ( ! function_exists( 'cs_reset_data' ) ) {
	function cs_reset_data(){
		global $reset_data,$options;
			foreach ($options as $value) {
			//update_option('cs_theme_reset',$reset_data);
			if($value['type'] <> 'heading' and $value['type'] <> 'sub-heading' and $value['type']<>'main-heading'){
				if($value['type']=='sidebar' || $value['type']=='networks' || $value['type']=='badges'){
					$reset_data=(array_merge($reset_data,$value['options']));
				}elseif($value['type']=='check_color'){
					$reset_data[$value['id']] = $value['std'];
					$reset_data[$value['id'].'_switch'] = 'off';
				}else{
					$reset_data[$value['id']] = $value['std'];
				}
			}
		}
		return $reset_data;
	}
}
function cs_headerbg_slider(){
	if(class_exists('RevSlider') && class_exists('cs_RevSlider')) {
		$slider = new cs_RevSlider();
		$arrSliders = $slider->getArrSlidersShort();
		foreach ( $arrSliders as $key => $entry ) {
			$selected = '';
			 if($select_value != '') {
				 if ( $select_value == $entry['alias']) { $selected = ' selected="selected"';} 
			 } else {
				 if ( isset($value['std']) )
					 if ($value['std'] == $entry['alias']) { $selected = ' selected="selected"'; }
			 }
			$output.= '<option '.$selected.' value="'.$entry['alias'].'">'.$entry['title'].'</option>';
		}
	}		
}
?>