<?php
/**
 * Tracking functions for reporting plugin usage for users that have opted in
 *
 * @since      1.15.4
 * @package    Wpbackitup_Premium
 * @subpackage Wpbackitup_Premium/includes
 * @author     WP BackItUp <wpbackitup@wpbackitup.com>
 *
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Usage tracking
 *
 * @return void
 */
class WPBackItUp_Usage {

	/**
	 * Log Name
	 *
	 * @access private
	 */
	private $log_name;

	/**
	 * Usage data
	 *
	 * @access private
	 */
	private $data;

	/**
	 * Usage data Schema Version
	 *
	 * @access private
	 */
	private $data_schema_version="20180501";


	public function __construct() {
		$this->log_name = 'debug_usage';//default log name

		add_action( 'init', array( $this, 'schedule_events' ) );
		add_action( 'wpbackitup_opt_into_tracking',   array( $this, 'optin_usage_tracking'  ) );
		add_action( 'wpbackitup_opt_out_of_tracking', array( $this, 'optout_usage_tracking' ) );
		add_action( 'wpbackitup_ut_event', array( $this, 'ut_event' ),10,3 );
		add_action( 'admin_notices', array( $this, 'admin_notice'     ) );
	}

	/**
	 * Check if the user has opted into tracking
	 *
	 * @access private
	 * @return bool
	 */
	public function is_tracking_allowed() {
		return (bool) WPBackItUp_Utility::get_option( 'allow_usage_tracking', false );
	}

	public function set_tracking_allowed($value) {

		//When set to true - fire event
		if (true===$value){
			$this->ut_event( true );
		}

		return WPBackItUp_Utility::set_option( 'allow_usage_tracking', true===$value ? 1: 0);
	}

	/**
	 * Generate usage data
	 *
	 * @access private
	 * @return void
	 * @throws Exception
	 */
	private function setup_data($source) {

		try {
			$data = array();

			//add home url but strip out the http
			$domain = parse_url( get_home_url(), PHP_URL_HOST );
			$data['ut-id']              = $domain; //pk

			$data['version']            = $this->data_schema_version;
			$data['domain']             = $domain;

			// Retrieve current theme info
			$theme_data = wp_get_theme();
			$theme      = $theme_data->Name . ' ' . $theme_data->Version;

			$data['php_version'] = phpversion();

			$data['wp_version'] = get_bloginfo( 'version' );
			$data['server']     = isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '';

			$data['multisite'] = is_multisite();
			$data['url']       = home_url();
			$data['theme']     = $theme;

			// Retrieve current plugin information
			if ( ! function_exists( 'get_plugins' ) ) {
				include ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$plugins        = array_keys( get_plugins() );
			$active_plugins = get_option( 'active_plugins', array());
			$inactive_plugins = array();

			//$current_path = dirname(plugin_basename(__FILE__));
			//$current_root = current(explode("/",$current_path));
			//$current_plugin=$current_root;

			foreach ( $plugins as $key => $plugin ) {
				if ( ! in_array( $plugin, $active_plugins ) ) {
					// Remove active plugins from list so we can show active and inactive separately
					//unset( $plugins[ $key ] );
					$inactive_plugins[] = $plugins[ $key ];
				}
			}

			$mu_plugins = array();
			foreach ( get_mu_plugins() as $mu_key => $mu_plugin ) {
				$mu_plugins[]=$mu_key;
			}

			$data['site_id']          = WPBackitup_Settings::get_site_id();
			$data['source']           = $source;
			$data['active_plugins']   = $active_plugins;
			$data['inactive_plugins'] = $inactive_plugins;
			$data['mu_plugins']       = $mu_plugins;
			$data['locale']           = get_locale();


			/** Plugin Data Points  **/
			$data['wpbackitup_version'] = WPBACKITUP__VERSION;

			//Get license info if exists
			$wpb_license = new WPBackItUp_License();
			$data['wpbackitup_license_key']     =$wpb_license->get_license_key();
			$data['wpbackitup_license_type']    =$wpb_license->get_license_type();
			$data['wpbackitup_license_expires'] =$wpb_license->get_license_expires_date();
			$data['wpbackitup_license_status']  =$wpb_license->get_license_status();

			$data['wpbackitup_backup_count'] = WPBackItUp_Utility::get_option( 'successful_backup_count' );
			$data['wpbackitup_backup_last_run_date'] = date("Y-m-d",WPBackItUp_Utility::get_option( 'backup_lastrun_date' ));

			//get additional data if exists
			$this->data =  apply_filters('wpbackitup_ut_data',$data);

		} catch (Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
		}
	}



	/**
	 * Send the data to the usage service
	 *
	 * @access private
	 *
	 * @param  bool $override            If we should override the tracking setting.
	 * @param  bool $ignore_last_checkin If we should ignore when the last check in was.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function ut_event( $override = false, $ignore_last_checkin = false, $source='wpbackitup' ) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin:' . var_export($override,true) .':' . var_export($ignore_last_checkin,true) );

//		error_log('override:' .var_export($override,true));
//		error_log('last_checkin:' .var_export($ignore_last_checkin,true));
//		error_log('Source:' .var_export($source,true));

		try {

			//Check for user permission
			if ( ! $this->is_tracking_allowed() && ! $override ) {
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Exit 1' );
				return false;
			}

			//get last send date
			$last_send = $this->get_last_send();

			//Exit IF we sent within last day - never send more than once per day
			if ( is_numeric( $last_send ) && $last_send > strtotime( '-1 day' )) {
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Exit 2' );
				return false;
			}

			//Exit IF we have sent within last week - only send once per week IF allowed
			if ( is_numeric( $last_send ) && $last_send > strtotime( '-1 week' ) && ! $ignore_last_checkin ) {
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Exit 3' );
				return false;
			}

			$blocking=false;
			if (true===WPBACKITUP__DEBUG) $blocking=true;
			$this->setup_data($source);
			$response = wp_remote_post( 'https://5bx6m4uwn0.execute-api.us-east-1.amazonaws.com/prd/utv2', array(
				'method'      => 'POST',
				'timeout'     => 8,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking'    => $blocking,
				'body'        => json_encode( $this->data ),
				'user-agent'  => 'WPBACKITUP/' . WPBACKITUP__VERSION . '; ' . get_bloginfo( 'url' )
			) );

			//Wont wait for response unless blocking is turned on.
			if (true===WPBACKITUP__DEBUG){
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Usage Tracking Response:' . var_export( $response, true ));
			}

			$this->set_last_send();
			return true;

		} catch (Exception $e) {
			$this->set_last_send();
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
			return false;
		}

	}

	/**
	 * Get the last time a checkin was sent
	 *
	 * @access private
	 * @return false|string
	 */
	private function get_last_send() {
		return WPBackItUp_Utility::get_option( 'ut_last_send' );
	}

	/**
	 * Set the send value
	 *
	 * @param null $value
	 * @return bool
	 */
	private function set_last_send($value=null){
		if (null==$value) $value=time();
		return WPBackItUp_Utility::set_option( 'ut_last_send', $value );
	}

	/**
	 * Set the tracking notice off
	 *
	 * @return bool
	 */
	private function set_tracking_notice_off(){
		return WPBackItUp_Utility::set_option( 'tracking_notice', 1 );
	}

	/**
	 * is the tracking notice off
	 *
	 * @return bool
	 */
	private function is_tracking_notice_off(){
		return (bool) WPBackItUp_Utility::get_option( 'tracking_notice',false );
	}

	/**
	 * Check for a new opt-in via the admin notice
	 *
	 * @return void
	 */
	public function optin_usage_tracking( $data ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->set_tracking_allowed(true);
		$this->set_tracking_notice_off();

		wp_redirect( remove_query_arg( 'wpbackitup_action' ) ); exit;
	}

	/**
	 * Check for a new opt-in via the admin notice
	 *
	 * @return void
	 */
	public function optout_usage_tracking( $data ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->set_tracking_allowed(false);
		$this->set_tracking_notice_off();
		wp_redirect( remove_query_arg( 'wpbackitup_action' ) ); exit;
	}


	/**
	 * Display the admin notice to users that have not opted-in or out
	 *
	 * @return void
	 */
	public function admin_notice() {

		//only show on wpbackitup pages
		if(! isset($_GET['page']) || false === strpos($_GET['page'],'wp-backitup-backup')) return;

		if ( $this->is_tracking_notice_off()) {
			return;
		}

		if ( $this->is_tracking_allowed() ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if (
			(   stristr( network_site_url( '/' ), 'dev'       ) !== false ||
				stristr( network_site_url( '/' ), 'localhost' ) !== false ||
				stristr( network_site_url( '/' ), ':8888'     ) !== false // This is common with MAMP on OS X
			) && WPBACKITUP__DEBUG!==true ){

			//Turn off usage for dev
			$this->set_tracking_notice_off();

		} else {
			$optin_url  = add_query_arg( 'wpbackitup_action', 'opt_into_tracking' );
			$optout_url = add_query_arg( 'wpbackitup_action', 'opt_out_of_tracking' );

			echo '<div class="updated"><p>';
			//echo sprintf( __( 'Allow WPBackItUp to anonymously track how this plugin is used so we can make it better? Only data needed to help support and improve this plugin will ever be collected. No sensitive data is tracked and we\'ll never share this data with anyone.', 'wp-backitup' ) );
			echo sprintf( __( 'Allow WPBackItUp to anonymously track how this plugin is used so we can make it better?', 'wp-backitup' ) );
			echo '&nbsp;<a href="' . esc_url( $optin_url ) . '" class="button-secondary">' . __( 'Allow', 'wp-backitup' ) . '</a>';
			echo '&nbsp;<a href="' . esc_url( $optout_url ) . '" class="button-secondary">' . __( 'Do not allow', 'wp-backitup' ) . '</a>';
			echo sprintf( __( '<br/>Only data needed to help support and improve this plugin will ever be collected. No sensitive data is tracked and we\'ll never share this data with anyone.', 'wp-backitup' ) );
			echo '</p></div>';
		}
	}
	/**
	 * Schedule a daily check to see if we have checked in lately
	 *
	 * @return void
	 */
	public function schedule_events() {
		if ( WPBackItUp_Cron::doing_cron() ) {
			add_action( 'wpbackitup_daily_scheduled_events', array( $this, 'ut_event' ) );
		}
	}

}
$wpb_tracking = new WPBackItUp_Usage();