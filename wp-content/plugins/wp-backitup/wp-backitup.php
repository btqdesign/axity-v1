<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * Plugin Name: WP BackItUp Community Edition
 * Plugin URI: https://www.wpbackitup.com
 * Description: Backup your content, settings, themes, plugins and media in just a few simple clicks.
 * Author: WPBackItUp
 * Author URI: https://www.wpbackitup.com
 * Version: 1.14.4
 * Text Domain: wp-backitup
 * Domain Path: /languages
 *
 * License: GPL3
 *
 * Copyright 2012-2015 WPBackItUp  (email : support@wpbackitup.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


define( 'WPBACKITUP__NAMESPACE', 'wp-backitup' );
define( 'WPBACKITUP__CLASSNAMESPACE', 'WPBackItUp' );

define( 'WPBACKITUP__MAJOR_VERSION', 1);
define( 'WPBACKITUP__MINOR_VERSION', 14);
define( 'WPBACKITUP__MAINTENANCE_VERSION', 4); //Dont forget to update version in header on WP release
define( 'WPBACKITUP__BUILD_VERSION', 0); //Used for hotfix releases

define( 'WPBACKITUP__VERSION',sprintf("%d.%d.%d.%d", WPBACKITUP__MAJOR_VERSION, WPBACKITUP__MINOR_VERSION,WPBACKITUP__MAINTENANCE_VERSION,WPBACKITUP__BUILD_VERSION));

define( 'WPBACKITUP__DB_VERSION', 4); //DATABASE VERSION

define( 'WPBACKITUP__DEBUG', false );

//define( 'WPBACKITUP__TEST_RUN_HOURLY', true );
define( 'WPBACKITUP__MINIMUM_WP_VERSION', '3.0' );
//define( 'WPBACKITUP__ITEM_NAME', 'WP Backitup' );
define( 'WPBACKITUP__ITEM_NAME', 'WP BackItUp Premium' );

define( 'WPBACKITUP__FRIENDLY_NAME', 'WPBackItUp' );

define( 'WPBACKITUP__CONTENT_PATH', WP_CONTENT_DIR  );

define( 'WPBACKITUP__SITE_URL', 'http://www.wpbackitup.com');
define( 'WPBACKITUP__SECURESITE_URL', 'https://www.wpbackitup.com' );
define( 'WPBACKITUP__SUPPORTSITE_URL', 'http://support.wpbackitup.com' );
define( 'WPBACKITUP__API_URL', 'https://7aj6amlu38.execute-api.us-east-1.amazonaws.com/prod/v1' );

define( 'WPBACKITUP__PLUGIN_FILE_PATH', __FILE__ );//path to main plugin file

define( 'WPBACKITUP__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPBACKITUP__PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPBACKITUP__PLUGIN_FOLDER',basename(dirname(__FILE__)));
define( 'WPBACKITUP__VENDOR_PATH', plugin_dir_path( __FILE__ ) . '/vendor');

define( 'WPBACKITUP__BACKUP_FOLDER', 'wpbackitup_backups' );
define( 'WPBACKITUP__BACKUP_URL', content_url() . "/" .WPBACKITUP__BACKUP_FOLDER);
define( 'WPBACKITUP__BACKUP_PATH',WPBACKITUP__CONTENT_PATH  . '/' . WPBACKITUP__BACKUP_FOLDER);
define( 'WPBACKITUP__UPLOAD_FOLDER','TMP_Uploads');
define( 'WPBACKITUP__UPLOAD_PATH',WPBACKITUP__BACKUP_PATH . '/' .WPBACKITUP__UPLOAD_FOLDER);
define( 'WPBACKITUP__LOGS_PATH',WPBACKITUP__PLUGIN_PATH . 'logs') ;

define( 'WPBACKITUP__RESTORE_FOLDER', 'wpbackitup_restore' );
define( 'WPBACKITUP__RESTORE_PATH',WPBACKITUP__CONTENT_PATH . '/' . WPBACKITUP__RESTORE_FOLDER);

define( 'WPBACKITUP__PLUGINS_ROOT_PATH',WP_PLUGIN_DIR );
define( 'WPBACKITUP__THEMES_ROOT_PATH',get_theme_root() );
define( 'WPBACKITUP__THEMES_FOLDER',basename(get_theme_root()));

define( 'WPBACKITUP__SQL_DBBACKUP_FILENAME', 'db-backup.sql');

//VALID character list   - _ ~ , . ; @ [] ()
define( 'WPBACKITUP__VALID_FILENAME_REGEX', '([^\w\s\d\-_~,.;@\[\]\(\).])'); // regex to validate filename.

define( 'WPBACKITUP__BACKUP_GLOBAL_IGNORE_LIST','.htaccess');//comma separated list with no spaces after comma

define( 'WPBACKITUP__TASK_TIMEOUT_SECONDS', 120);
define( 'WPBACKITUP__SCRIPT_TIMEOUT_SECONDS', 900);//900 = 15 minutes

define( 'WPBACKITUP__TASK_WAIT_SECONDS', 2);

define( 'WPBACKITUP__BACKUP_RETAINED_DAYS', 5);//5 days
define( 'WPBACKITUP__SUPPORT_EMAIL', 'wpbackitupcomsupport@wpbackitup.freshdesk.com');

define( 'WPBACKITUP__SQL_BULK_INSERT_SIZE', 1000);
define( 'WPBACKITUP__ZIP_MAX_FILE_SIZE', 524288000 ); // 104857600, 209715200, 314572800, 419430400, 524288000; # 100-500Mb
define( 'WPBACKITUP__THEMES_BATCH_SIZE', 5000); //~100kb each = 5000*100 = 500000 kb = 500 mb
define( 'WPBACKITUP__PLUGINS_BATCH_SIZE', 5000); //~100kb each = 5000*100 = 500000 kb = 500 mb
define( 'WPBACKITUP__OTHERS_BATCH_SIZE', 500); //~100kb each = 5000*100 = 500000 kb = 500 mb
define( 'WPBACKITUP__UPLOADS_BATCH_SIZE', 500); //anyones guess here
define( 'WPBACKITUP__DATABASE_BATCH_SIZE', 10000);
define( 'WPBACKITUP__SQL_MERGE_BATCH_SIZE', 10000);
define( 'WPBACKITUP__SQL_BATCH_SIZE', 10000);

//activation hooks
register_activation_hook( __FILE__, array( 'WPBackitup_Admin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WPBackitup_Admin', 'deactivate' ) );

function wpbackitup_modify_cron_schedules($schedules) {
    $schedules['every4hours'] = array('interval' => 14400, 'display' => sprintf(__('Every %s hours', 'wp-backitup'), 4));
    $schedules['every8hours'] = array('interval' => 28800, 'display' => sprintf(__('Every %s hours', 'wp-backitup'), 8));
	$schedules['every_1_minutes'] = array('interval' => 60, 'display'  => sprintf(__('WPBUP - Every %s minutes', 'wp-backitup'), 1));
	$schedules['every_3_minutes'] = array('interval' => 180, 'display'  => sprintf(__('WPBUP - Every %s minutes', 'wp-backitup'), 3));
	$schedules['every_5_minutes'] = array('interval' => 300, 'display'  => sprintf(__('WPBUP - Every %s minutes', 'wp-backitup'), 5));
	$schedules['every_10_minutes'] = array('interval' => 600, 'display'  => sprintf(__('WPBUP - Every %s minutes', 'wp-backitup'), 10));
	$schedules['every_30_minutes'] = array('interval' => 1800, 'display'  => sprintf(__('WPBUP - Every %s minutes', 'wp-backitup'), 30));

	$schedules['weekly'] = array('interval' => 604800, 'display' => __('WPBUP - Once Weekly', 'wp-backitup'));
    $schedules['monthly'] = array('interval' => 2592000, 'display' => __('WPBUP - Once Monthly', 'wp-backitup'));
    $schedules['every4hours'] = array('interval' => 14400, 'display' => sprintf(__('WPBUP - Every %s hours', 'wp-backitup'), 4));
    $schedules['every8hours'] = array('interval' => 28800, 'display' => sprintf(__('WPBUP - Every %s hours', 'wp-backitup'), 8));
    return $schedules;
}

add_filter('cron_schedules', 'wpbackitup_modify_cron_schedules', 30);

function wpbackitup_register_post_types() {

	$backup_args = array(
		'labels'              => array( 'name' => __( 'Local File', 'wp-backitup' ) ),
		'public'              => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_ui'             => false,
		'query_var'           => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor' ),
		'can_export'          => true
	);
	register_post_type( 'wpb_local-file', $backup_args );

//	$backup_args = array(
//		'labels'              => array( 'name' => __( 'AWS-S3 File', 'wp-backitup' ) ),
//		'public'              => false,
//		'exclude_from_search' => true,
//		'publicly_queryable'  => false,
//		'show_ui'             => false,
//		'query_var'           => false,
//		'rewrite'             => false,
//		'capability_type'     => 'post',
//		'supports'            => array( 'title', 'editor' ),
//		'can_export'          => true
//	);
//	register_post_type( 'wpb_S3-file', $backup_args );


	$backup_args = array(
		'labels'              => array( 'name' => __( 'Backup', 'wp-backitup' ) ),
		'public'              => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_ui'             => false,
		'query_var'           => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor' ),
		'can_export'          => true
	);
	register_post_type( WPBACKITUP__NAMESPACE.'_backup', $backup_args );

	$restore_args = array(
		'labels'              => array( 'name' => __( 'Restore', 'wp-backitup' ) ),
		'public'              => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_ui'             => false,
		'query_var'           => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor' ),
		'can_export'          => true
	);
	register_post_type( WPBACKITUP__NAMESPACE.'_restore', $restore_args );

	$cleanup_args = array(
		'labels'              => array( 'name' => __( 'Cleanup', 'wp-backitup' ) ),
		'public'              => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_ui'             => false,
		'query_var'           => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor' ),
		'can_export'          => true
	);
	register_post_type( WPBACKITUP__NAMESPACE.'_cleanup', $cleanup_args );

}

add_action( 'init', 'wpbackitup_register_post_types',1 );

function  wpbackitup_custom_post_status(){

	register_post_status( 'queued', array(
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => true,
	));

	register_post_status( 'active', array(
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => true,
	));

	register_post_status( 'error', array(
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => true,
	));

	register_post_status( 'complete', array(
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => true,
	));

	register_post_status( 'cancelled', array(
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => true,
	));

	register_post_status( 'deleted', array(
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => true,
	));

}
add_action( 'init', 'wpbackitup_custom_post_status',1);

/**
 * Action: Display Dependency Admin Notices
 * Display only on WPBackItUp AND Plugins pages
 *
 * @since     1.0.0*
 *
 */
function wpbackitup_dependency_notice() {
	global $pagenow;
	$notices = array();

	if (version_compare(PHP_VERSION, '5.2.0' , '<')) {
		$notices[] =  __( 'WPBackItUp requires PHP Version 5.2 or later', 'wp-backitup' );
	}

	//Display messages on plugins AND wp-backitup only
	if ( $pagenow == 'plugins.php' || ( isset($_GET['page']) && false !== strpos($_GET['page'],'wp-backitup') ) ) {
		//Write the notices to the output buffer
		if( count($notices)>0 ) {
			ob_start(); ?>
			<div class="notice notice-error is-dismissible">
				<p><?php
					foreach ($notices  as $notice ) {
						echo $notice .'<br/>';
					}
					?></p>
			</div>
			<?php
			echo ob_get_clean();//flush the buffer
		}
	}
}
add_action('admin_notices','wpbackitup_dependency_notice');


// Admin class will not be instantiate if any of these conditions are met
if (!is_admin()
    && (!defined('DOING_CRON') || !DOING_CRON)
    && (!defined('XMLRPC_REQUEST') || !XMLRPC_REQUEST)
    && empty($_SERVER['SHELL'])
    && empty($_SERVER['USER'])) {

	return;  //END HERE
}

require_once( WPBACKITUP__PLUGIN_PATH .'/lib/includes/class-wpbackitup-admin.php' );

global $WPBackitup;
$WPBackitup = WPBackitup_Admin::get_instance();
$WPBackitup->initialize();


