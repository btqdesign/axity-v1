<?php

/*
Plugin Name: GroupDocs Assembly Embedder
Plugin URI: http://www.groupdocs.com/
Description: Lets you embed PPT, PPTX, XLS, XLSX, DOC, DOCX, PDF and many other formats from your GroupDocs acount in a web page using the GroupDocs Embedded Viewer (no Flash or PDF browser plug-ins required).
Author: GroupDocs Team <support@groupdocs.com>
Author URI: http://www.groupdocs.com/
Version: 1.0.4
License: GPLv2
*/

include_once('grpdocs-functions.php');


function grpdocs_assembly_getdocument($atts) {

	extract(shortcode_atts(array(
		'file' => '',
		'width' => '',
		'height' => '',
		'page' => 0,
		'version' => 1,
	), $atts));

	$guid = grpdocs_assembly_getGuid(urlencode($file));


	$no_iframe = 'If you can see this text, your browser does not support iframes. Please enable iframe support in your browser or use the latest version of any popular web browser such as Mozilla Firefox or Google Chrome. For more help, please check our documentation Wiki: <a href="http://groupdocs.com/docs/display/assembly/GroupDocs+Assembly+Integration+with+3rd+Party+Platforms">http://groupdocs.com/docs/display/assembly/GroupDocs+Assembly+Integration+with+3rd+Party+Platforms</a>';

	$code = '<iframe src="https://apps.groupdocs.com/assembly2/questionnaire-assembly/'. $guid .'?referer=wordpress-assembly/1.0.4" frameborder="0" width="'. $width .'" height="'. $height .'">' . $no_iframe . '</iframe>';

	$code = str_replace("%W%", $width, $code);
	$code = str_replace("%H%", $height, $code);
	$code = str_replace("%P%", $page, $code);
	$code = str_replace("%V%", $version, $code);
	$code = str_replace("%A%", '', $code);
	$code = str_replace("%B%", $download, $code);
	$code = str_replace("%GUID%", $guid, $code);

	return $code;

}

//activate shortcode
add_shortcode('grpdocsassembly', 'grpdocs_assembly_getdocument');


// editor integration

// add quicktag
add_action( 'admin_print_scripts', 'grpdocs_assembly_admin_print_scripts' );

// add tinymce button
add_action('admin_init','grpdocs_assembly_mce_addbuttons');

// add an option page
add_action('admin_menu', 'grpdocs_assembly_option_page');
function grpdocs_assembly_option_page() {
	global $grpdocs_assembly_settings_page;

	$grpdocs_assembly_settings_page = add_options_page('GroupDocs Assembly', 'GroupDocs Assembly', 'manage_options', basename(__FILE__), 'grpdocs_assembly_options');

}
function grpdocs_assembly_options() {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) die(t('An error occurred.'));
	if (! user_can_access_admin_page()) wp_die('You do not have sufficient permissions to access this page');

	require(ABSPATH. 'wp-content/plugins/groupdocs-assembly/options.php');
}
