<?php

if ( ! defined( 'grpdocs_assembly_PLUGIN_URL' ) )  define( 'grpdocs_assembly_PLUGIN_URL', WP_PLUGIN_URL . '/groupdocs-assembly');

function grpdocs_assembly_getGuid($link) {
  
    preg_match('/([0-9a-f]){32}/', $link, $matches);
    return isset($matches[0]) ? $matches[0] : '';
}

function grpdocs_assembly_mce_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;

   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "grpdocs_assembly_add_tinymce_plugin");
     add_filter('mce_buttons', 'grpdocs_assembly_register_mce_button');
   }
}

function grpdocs_assembly_register_mce_button($buttons) {
   array_push($buttons, "separator", "grpdocs_assembly");
   return $buttons;
}

function grpdocs_assembly_add_tinymce_plugin($plugin_array) {
	// Load the TinyMCE plugin
   $plugin_array['grpdocs_assembly'] = grpdocs_assembly_PLUGIN_URL.'/js/grpdocs_assembly_plugin.js';
   return $plugin_array;
}

function grpdocs_assembly_admin_print_scripts($arg) {
	global $pagenow;
	if (is_admin() && ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) ) {
		$js = grpdocs_assembly_PLUGIN_URL.'/js/grpdocs-quicktags.js';
		wp_enqueue_script("grpdocs_assembly_qts", $js, array('quicktags') );
	}
}

// footer credit
function grpdocs_assembly_admin_footer() {
	$pdata = get_plugin_data(__FILE__);
	printf('%1$s plugin | Version %2$s<br />', $pdata['Title'], $pdata['Version']);
}
