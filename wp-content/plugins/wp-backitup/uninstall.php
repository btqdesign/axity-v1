<?php
/**
 * Uninstall WP BackItUp
 *
 * @package     WP BackItUp
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2015, Chris Simmmons
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

include_once( 'wp-backitup.php' );

global $wpdb, $wp_roles;

$delete_all = (bool) get_option(WPBACKITUP__NAMESPACE .'_delete_all');

if( true===$delete_all ) {

	error_log('wp-backitup remove cron events.');

	/** Cleanup Cron Events */
	wp_clear_scheduled_hook( 'wpbackitup_queue_scheduled_jobs');


	/** Delete All the Custom Post Types */
	$wpbackitup_post_types = array(
		WPBACKITUP__NAMESPACE.'_backup',
		WPBACKITUP__NAMESPACE.'_restore',
		WPBACKITUP__NAMESPACE.'_cleanup'
	);

	error_log('wp-backitup remove custom post types.');
	foreach ( $wpbackitup_post_types as $post_type ) {

		$items = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );

		if ( $items ) {
			foreach ( $items as $item ) {
				wp_delete_post( $item, true);
			}
		}
	}


	/** Delete all the Plugin Options */
	error_log('wp-backitup remove all settings');
	$wpdb->query( "DELETE FROM " . $wpdb->options . " WHERE option_name like '" .WPBACKITUP__NAMESPACE ."_%' " );

	/** Remove all customer database tables **/
	error_log('wp-backitup remove custom tables.');
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "wpbackitup_job" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "wpbackitup_job_control" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "wpbackitup_job_tasks" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "wpbackitup_job_items" );

	error_log('wp-backitup uninstall end');
}
