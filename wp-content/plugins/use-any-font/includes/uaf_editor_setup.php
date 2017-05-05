<?php

// DEFAULT WORDPRESS EDITOR
function uaf_mce_before_init( $init_array ) {
	$theme_advanced_fonts = '';
	$fontsRawData 	= get_option('uaf_font_data');
	$fontsData		= json_decode($fontsRawData, true);
	if (!empty($fontsData)):
		foreach ($fontsData as $key=>$fontData):
			$theme_advanced_fonts .= ucfirst(str_replace('_',' ', $fontData['font_name'])) .'='.$fontData['font_name'].';';		
		endforeach;
	endif;
	
	$init_array['font_formats'] = $theme_advanced_fonts.'Andale Mono=Andale Mono, Times;Arial=Arial, Helvetica, sans-serif;Arial Black=Arial Black, Avant Garde;Book Antiqua=Book Antiqua, Palatino;Comic Sans MS=Comic Sans MS, sans-serif;Courier New=Courier New, Courier;Georgia=Georgia, Palatino;Helvetica=Helvetica;Impact=Impact, Chicago;Symbol=Symbol;Tahoma=Tahoma, Arial, Helvetica, sans-serif;Terminal=Terminal, Monaco;Times New Roman=Times New Roman, Times;Trebuchet MS=Trebuchet MS, Geneva;Verdana=Verdana, Geneva;Webdings=Webdings;Wingdings=Wingdings';
	return $init_array;
}

function wp_editor_fontsize_filter( $options ) {
	array_unshift( $options, 'fontsizeselect');
	array_unshift( $options, 'fontselect');
	return $options;
}

// DIVI CUSTOMIZER AND BUILDER

add_filter('et_websafe_fonts', 'uaf_send_fonts_divi_list',10,2);
function uaf_send_fonts_divi_list($fonts){
    $fontsRawData 	= get_option('uaf_font_data');
	$fontsData		= json_decode($fontsRawData, true);
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		foreach ($fontsData as $key=>$fontData):
			$fonts_uaf[$fontData['font_name']] = array(
				'styles' 		=> '400',
				'character_set' => 'cyrillic,greek,latin',
				'type'			=> 'serif'
			);	
		endforeach;
	endif;
  	return array_merge($fonts_uaf,$fonts);
}

// SITE ORIGIN BUILDER

add_filter('siteorigin_widgets_font_families', 'uaf_send_fonts_siteorigin_list',10,2);
function uaf_send_fonts_siteorigin_list($fonts){
    $fontsRawData 	= get_option('uaf_font_data');
	$fontsData		= json_decode($fontsRawData, true);
	$fonts_uaf		= array();
	if (!empty($fontsData)):
		foreach ($fontsData as $key=>$fontData):
			$fonts_uaf[$fontData['font_name']] = $fontData['font_name'];
		endforeach;
	endif;
  	return array_merge($fonts_uaf,$fonts);
}