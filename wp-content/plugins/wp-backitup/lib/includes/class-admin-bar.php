<?php

/**
 * Toolbar for admin dashboard - used to display notices to user
 *
 * @since      1.21
 * @package    Wpbackitup_Premium
 * @subpackage Wpbackitup_Premium/includes
 * @author     WP BackItUp <wpbackitup@wpbackitup.com>
 *
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


class WPBackItUp_Admin_Bar {

	private $status;

	public function __construct() {

		add_action( 'admin_bar_menu', array( $this, 'toolbar_notices_link' ), 999, 1 );
		//add_action( 'wp_head', array( $this,'wpbackitup_load_admin_bar_style') );
		add_action( 'admin_head', array( $this, 'load_admin_bar_style' ) );

		$this->status = self::get_notice_status();
	}


	/**
	 *  Toolbar notices link
	 *
	 * @param $wp_admin_bar
	 */
	function toolbar_notices_link( $wp_admin_bar ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		//Style based on status
		$message = esc_html__( 'All systems go!', 'wp-backitup' );
		switch ($this->status) {
			case 1: //Notice
				$message = esc_html__( 'Important notices available!', 'wp-backitup' );
				break;
			case 2: //Error
				$message = esc_html__( 'Backup has encountered errors!', 'wp-backitup' );
				break;
		}



		$args          = array( 'page' => 'wp-backitup-backup' );
		$node = array(
			'id'     => 'wpbackitup_notices',
			'parent' => null,
			'group'  => null,
			'title'  => '<span class="ab-icon"></span><span>WPBackItUp</span> ',
			'href'   => add_query_arg( $args, admin_url( 'admin.php' ) ),
			'meta'   => array(
				'target' => '_self',
				'title'  => $message,
				'class'  => 'wpbackitup-notices',
			),
		);

		$wp_admin_bar->add_node( $node );
	}


	/**
	 * Load styles for toolbar
	 *
	 */
	function load_admin_bar_style() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		//Dash-Icons
		//https://developer.wordpress.org/resource/dashicons/#flag

		//Style based on status
		switch ($this->status) {
			case 1: //Notice
				echo '<style>#wpadminbar #wp-admin-bar-wpbackitup_notices .ab-icon:before { content: \'\\f227\'; top: 2px;  color:orange;}</style>';
				break;
			case 2: //Error
				echo '<style>#wpadminbar #wp-admin-bar-wpbackitup_notices .ab-icon:before { content: \'\\f534\'; top: 2px; color:red; }</style>';
				break;
			default: //Normal
				echo '<style>#wpadminbar #wp-admin-bar-wpbackitup_notices .ab-icon:before { content: \'\\f321\'; top: 2px;}</style>';
		}

	}

	private static function get_notice_status() {
		return WPBackItUp_Utility::get_option('notice_status',0);
	}

	private static function set_notice_status( $status ) {
		return WPBackItUp_Utility::set_option( 'notice_status',$status);
	}


	public static function backup_error( ) {
		self::set_notice_status(2);
	}

	public static function backup_success( ) {
		self::set_notice_status(0);
	}

	public static function set_notices_on( ) {
		//If error already then dont update to warning
		if (2!=self::get_notice_status()) {
			self::set_notice_status(1);
		}
	}

	public static function set_notices_off( ) {
		//If error already then dont update to warning
		if (2!=self::get_notice_status()) {
			self::set_notice_status(0);
		}
	}
}
$wpb_admin_bar = new WPBackItUp_Admin_Bar();


