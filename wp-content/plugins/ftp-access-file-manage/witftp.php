<?php
/*
Plugin Name: WitFTP
Plugin URI: http://witsolution.in
Description: WitFTP is a smart, fast and lightweight file manager component. It operates from WordPress back-end so you don't have to use any FTP program anymore.
Author: witsolution Team
Version: 1.0.6
Author URI: http://witsolution.in

*/

defined('ABSPATH') or die('MIWI');

define('MPATH_WITFTP_QX', plugin_dir_path(__FILE__).'admin/quixplorer');
define('MURL_WITFTP', plugins_url('', __FILE__));

add_action('admin_init', 'check_init_action');
add_action('admin_menu', 'miwoftp_menu');

function miwoftp_menu() {
    add_menu_page('WitFTP', 'WitFTP', 'manage_options', 'miwoftp', 'miwoftp_echo', MURL_WITFTP.'/admin/assets/images/icon-16-miwoftp.png', '33.0099');
}

function miwoftp_echo() {
	if (!current_user_can('manage_options')) {
		return;
	}
	
    echo '<div class="wrap">';
    echo '<h2>WitFTP</h2>';

    ob_start();
    require_once(MPATH_WITFTP_QX.'/index.php');
    $output = ob_get_contents();
    ob_end_clean();

    $replace_output = array(
        'index.php?action=' => 'admin.php?page=miwoftp&action=',
        'src="_img' => 'src="'.MURL_WITFTP.'/admin/quixplorer/_img',
        //'<TABLE WIDTH="95%">' => '<TABLE WIDTH="95%" class="wp-list-table widefat">'
    );

    foreach($replace_output as $key => $value){
        $output = str_replace($key, $value, $output);
    }

    echo $output;

    echo '<div style="margin: 10px; text-align: center;"><a style="text-decoration: none;" href="http://witsolution.in" target="_blank">WitFTP | Copyright &copy; 2009-2016 WIT Solution</a></div>';
    echo '</div>';
}

function check_init_action() {
    if (empty($_GET['action']) or (isset($_GET['action']) and $_GET['action'] != 'download') ){
        return;
    }

    if (!isset($_GET['option']) or (isset($_GET['option']) and $_GET['option'] != 'com_miwoftp') ){
        return;
    }

    if (!isset($_GET['item'])) {
        return;
    }
	
	if (!current_user_can('manage_options')) {
		return;
	}

    require MPATH_WITFTP_QX."/_include/init.php";

    ob_start(); // prevent unwanted output
   	require MPATH_WITFTP_QX."/_include/fun_down.php";
   	ob_end_clean(); // get rid of cached unwanted output
   	download_item($GLOBALS["dir"], $GLOBALS["item"]);
   	ob_start(false); // prevent unwanted output
	
   	exit;
}