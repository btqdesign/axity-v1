<?php
/**
 * Admin Actions
 *
 * @package     WPBackItUp
 * @subpackage  Admin/Actions
 * @copyright   Copyright (c) 2018, Chris Simmons
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.15.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Processes all WPBackItUp actions sent via POST and GET by looking for the 'wpbackitup-action'
 * request and running do_action() to call the function
 *
 * @since 1.0
 * @return void
 */
function wpbackitup_process_actions() {

	if ( isset( $_POST['wpbackitup_action'] ) ) {
		do_action( 'wpbackitup_' . $_POST['wpbackitup_action'], $_POST );
	}

	if ( isset( $_GET['wpbackitup_action'] ) ) {
		do_action( 'wpbackitup_' . $_GET['wpbackitup_action'], $_GET );
	}
}
add_action( 'admin_init', 'wpbackitup_process_actions' );

