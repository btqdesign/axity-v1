<?php if (!defined ('ABSPATH')) die('No direct access allowed');


/**
 * WPBackItUp License Class
 *
 * @link       http://www.wpbackitup.com
 * @since      1.14.0
 *
 * @package    WPBackItUp
 *
 */


class WPBackItUp_License {
	
	private static $default_log = 'debug_activation';
	private $log_name;

	private $license_key=null;
	private $license_type=null;
	private $license_type_description=null;
	private $license_status=null;
	private $license_status_message=null;
	private $customer_email=null;
	private $customer_name=null;
	private $license_expires_date=null;
	private $license_last_check_date=null;
	private $license_product_id=null;

	const   PREMIUM_PRODUCT_ID  =679;
	const   LITE_PRODUCT_ID     =448151;
	const   SAFE_PRODUCT_ID     =436624;

	//Default license property values
	private static $defaults = array(
		'license_key' => "",
		'license_last_check_date'=> "1970-01-01 00:00:00",
		'license_status' => "",
		'license_status_message'=> "",
		'license_type' => "-1",
		'license_expires'=> "1970-01-01 00:00:00",
		'license_limit'=> "1",
		'license_sitecount'=> "",
		'license_customer_name' => "",
		'license_customer_email' => "",
		'license_product_id' => "ce",
	);

	function __construct() {

		try {

			$this->log_name = self::$default_log; //default log name
			

		} catch ( Exception $e ) {
			WPBackItUp_Logger::log_error( $this->log_name, __METHOD__, 'Constructor Exception: ' . $e );
			throw $e;
		}

	}

	/**
	 * Is license active
	 * - license key will be empty for unregistered ce customers
	 * - license key will contain free for ce user license status
	 *
	 */
	public function is_license_active(){

		if ($this->get_license_key() && 'valid'==$this->get_license_status() ) {
			return true;
		}

		return false;
	}


	/**
	 * Does customer have a valid license key
	 * - may be expired or valid
	 *
	 * @return bool
	 */
	public function is_license_valid(){

		$license_key=$this->get_license_key();
		$license_status = $this->get_license_status();

		if (! empty($license_key) &&
		    ('valid'==$license_status  ||
		     'expired'==$license_status ))
		{
			return true;
		}

		return false;
	}

	/**
	 * Does customer have a valid license key that is expired
	 *
	 *
	 * @return bool Lite license will always return false
	 */
	public function is_license_expired(){

		$license_status = $this->get_license_status();

		if ($this->is_license_valid() && 'expired' == $license_status ) {
			return true;
		}

		return false;
	}


	/**
	 * Getter - License Key Getter
	 *
	 * @return String Lite when NO license key
	 */
	public function get_license_key(){

		if ( is_null($this->license_key)){
			$this->license_key = WPBackItUp_Utility::get_option( 'license_key',self::$defaults['license_key'] );
		}

		return $this->license_key;
	}

	/**
	 * Getter - License Status Getter
	 *
	 * @return String Empty string when no status
	 */
	public function get_license_status() {
		if ( is_null($this->license_status)){
			$this->license_status = WPBackItUp_Utility::get_option( 'license_status',self::$defaults['license_status'] );
		}

		return $this->license_status;
	}

	/**
	 * Getter - Retrieve License expires date
	 *
	 * * @return string default 1970-01-01 00:00:00
	 */
	public function get_license_expires_date(){

		if ( is_null($this->license_expires_date)){
			$this->license_expires_date = WPBackItUp_Utility::get_option( 'license_expires',self::$defaults['license_expires'] );
		}

		return $this->license_expires_date;
	}


	/**
	 * Getter - License Status Message Getter
	 *
	 * @return string empty string when no message
	 */
	public function get_license_status_message() {
		if ( is_null($this->license_status_message)){
			$this->license_status_message = WPBackItUp_Utility::get_option( 'license_status_message',self::$defaults['license_status_message']);
		}

		return $this->license_status_message;
	}

	/**
	 * Getter: Get license type or default
	 *
	 * @return string default -1
	 */
	public function get_license_type() {

		if ( is_null($this->license_type)){
			$this->license_type = WPBackItUp_Utility::get_option( 'license_type',self::$defaults['license_type'] );
		}

		return $this->license_type;

	}

	/**
	 * Getter: Get license product id
	 *
	 * @return string
	 */
	public function get_license_product_id() {

		if ( is_null($this->license_product_id)){
			$this->license_product_id = WPBackItUp_Utility::get_option( 'license_product_id',self::$defaults['license_product_id'] );
		}

		return $this->license_product_id;

	}


	/**
	 * Getter - Is ce license registered
	 *
	 * @return bool
	 */
	function is_ce_registered(){
		if (-1 == $this->get_license_type() && $this->get_customer_email()) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Getter - license type description - derived property
	 *
	 */
	public function get_license_type_description(){

		if (is_null($this->license_type_description)) {

			switch ($this->get_license_type()) {
				case -1:
					$this->license_type_description = 'community edition';
					break;
				case 0:
					$this->license_type_description = 'lite';
					break;
				case 1:
					$this->license_type_description = 'personal';
					break;

				case 2:
					$this->license_type_description = 'professional';
					break;

				case 3:
					$this->license_type_description = 'premium';
					break;
			}
		}

		return $this->license_type_description;
	}

	/**
	 * Getter - Customer Email Address associated with license
	 *
	 * @return string default empty string
	 */
	function get_customer_email(){
		if ( is_null($this->customer_email)){
			$this->customer_email = WPBackItUp_Utility::get_option( 'license_customer_email',self::$defaults['license_customer_email'] );
		}

		return $this->customer_email;

	}

	/**
	 * Getter - Customer Name associated with license
	 *
	 * @return string default empty
	 */
	function get_customer_name(){
		if ( is_null($this->customer_name)){
			$this->customer_name = WPBackItUp_Utility::get_option( 'license_customer_name',self::$defaults['license_customer_name'] );
		}

		return $this->customer_name;

	}


	/**
	 * Getter - Last DateTime license was checked
	 *
	 * @return DateTime default 1970-01-01 00:00:00
	 */
	public function get_license_last_check_date() {
		if ( is_null($this->license_last_check_date)){
			$license_last_check_date = WPBackItUp_Utility::get_option('license_last_check_date',self::$defaults['license_last_check_date'] );
			$this->license_last_check_date = new DateTime($license_last_check_date);
		}

		return $this->license_last_check_date;
	}

	/**
	 * Activate WPBackItUp License
	 *
	 * @param $license
	 *
	 * @param $item_nid
	 *
	 * @return bool|mixed
	 */
	public function activate_license($license, $item_id){

		$request_data = array(
			'license' 	=> $license,
			'item_id' 	=> $item_id,
			'url'       => home_url()
		);

		$license_data =  $this->edd_license_api_request(WPBACKITUP__API_URL_V2, 'activate_license', $request_data);

		//if false try using site directly
		if ( false === $license_data) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__, 'Unable to activate using Gateway  - attempting direct');
			$license_data= $this->edd_license_api_request(WPBACKITUP__SECURESITE_URL,'activate_license', $request_data);
		}

		return $license_data;

	}

//	/**
//	 * Check WPBackItUp License - dont think this is being used anywhere
//	 *
//	 * @param $license
//	 *
//	 * @param $item_id
//	 *
//	 * @return bool|mixed
//	 */
//	public function check_license($license, $item_id){
//
//		$request_data = array(
//			'license' 	=> $license,
//			'item_id' 	=> $item_id ,
//			'url'       => home_url()
//		);
//
//		$license_data =  $this->edd_license_api_request(WPBACKITUP__API_URL_V2, 'check_license', $request_data);
//
//		//if false try using site directly
//		if ( false === $license_data) {
//			WPBackItUp_Logger::log_error($this->log_name,__METHOD__, 'Unable to activate using Gateway  - attempting direct');
//			$license_data= $this->edd_license_api_request(WPBACKITUP__SECURESITE_URL,'activate_license', $request_data);
//		}
//
//		return $license_data;
//
//	}

	/**
	 * Acion: Validate License Info
	 *  - This is only used for PREMIUM and LITE plugins - not used in CE
	 *
	 * @since    1.24
	 *
	 */
	public function check_license($force_check=false){

		//Get License Info
		$license_key=$this->get_license_key();
		$license_product_id=$this->get_license_product_id();
		$license_type=$this->get_license_type();

		//If there is no product id but the license type is premium then default product id
//		error_log(var_export($license_key,true));
//		error_log(var_export($license_product_id,true));
//		error_log(var_export($license_type,true));

		$license_last_check_date=$this->get_license_last_check_date();
		//error_log('Last License Check:' . $license_last_check_date->format('Y-m-d H:i:s'));

		$now = new DateTime('now');//Get NOW
		$yesterday = $now->modify('-1 day');//subtract a day
		//error_log('Yesterday:' .$yesterday->format('Y-m-d H:i:s'));

		//Validate License
		//error_log('Check:' . ($license_last_check_date<$yesterday || $force_check?'true' :'false') );
		if ($license_last_check_date<$yesterday || $force_check)
		{
			//error_log('Checking license...');
			$this->update_license_options($license_key,$license_product_id);
		}
	}

	/**
	 * Deactivate WPBackItUp License for site identified in home_url value
	 *
	 * @param $license
	 *
	 * @param $item_id
	 *
	 * @return bool|mixed
	 */
	public function deactivate_license($license, $item_id){

		$request_data = array(
			'license' 	=> $license,
			'item_id' 	=> $item_id,
			'url'       => home_url()
		);

		$license_data =  $this->edd_license_api_request(WPBACKITUP__API_URL_V2, 'deactivate_license', $request_data);

		//if false try using site directly
		if ( false === $license_data) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__, 'Unable to activate using Gateway  - attempting direct');
			$license_data= $this->edd_license_api_request(WPBACKITUP__SECURESITE_URL,'activate_license', $request_data);
		}

		return $license_data;

	}

	/**
	 * Is premium plugin and license 30 days past expiration
	 *
	 * @return bool
	 */
	public function is_license_30_days_past_expire(){

		//if premium customer
		if ($this->is_premium_license()) {
			$expiration_date    = $this->get_license_expires_date();
			$expiration_plus_30 = date( 'Y-m-d', strtotime( "+30 days", strtotime( $expiration_date ) ) );
			$today              = date( 'Y-m-d', current_time('timestamp') );

			if ( $today > $expiration_plus_30 ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Customer has premium license
	 *
	 * @return bool
	 */
	public function is_premium_license(){

		if ($this->get_license_type()>0){
			return true;
		}

		return false;
	}

	/**
	 * Customer has lite license
	 *
	 * @return bool
	 */
	public function is_lite_license(){

		if (0==$this->get_license_type()){
			return true;
		}

		return false;
	}

	/**
	 * Customer has CE
	 *
	 * @return bool
	 */
	public function is_ce_license(){

		if (-1==$this->get_license_type()){
			return true;
		}

		return false;
	}

	/**
	 * Update ALL the license options
	 * Product ids
     * 679      -  Premium
	 * 448151   -  Lite
	 * 436624   -  Safe
	 *
	 *
	 */
	public function update_license_options($license,$product_id)
	{
		// Clearing any old notices
		delete_transient( 'wpbackitup_admin_notices' );

		$activation_logname='debug_activation';
		WPBackItUp_Logger::log_info($activation_logname,__METHOD__, 'Update License Options:' .$license);

		$license=trim($license);

		//Load the defaults
		$data['license_key'] = self::$defaults['license_key'];
		$dt = new DateTime('now');
		$data['license_last_check_date'] = $dt->format('Y-m-d H:i:s');

		$data['license_status'] = self::$defaults['license_status'];
		$data['license_status_message']= self::$defaults['license_status_message'];
		$data['license_expires']= self::$defaults['license_expires'];
		$data['license_limit']= self::$defaults['license_limit'];
		$data['license_sitecount']= self::$defaults['license_sitecount'];
		$data['license_type']= self::$defaults['license_type'];

		$data['license_customer_name'] = $this->get_customer_name();
		$data['license_customer_email'] = $this->get_customer_email();

		$data['license_product_id'] = self::$defaults['license_product_id'];

		//If no value then default to CE
		if (empty($license)){
			//error_log('empty_license');
			$data['license_status'] = 'free';
			$data['license_expires']= self::$defaults['license_expires'];
			$data['license_limit']= 1;
			$data['license_sitecount']= 1;
			$data['license_type']= -1;
		} else {

			//error_log('activate license');
			//activate license using SSL
			$license_data= $this->activate_license($license,$product_id);
			//error_log('here:' .var_export($license_data,true));

			if ( false === $license_data){
				//update license last checked date and
				WPBackItUp_Utility::set_option('license_last_check_date', $data['license_last_check_date']);

				$admin_notices = array();
				array_push($admin_notices,
					array(
						'message_type' => 'warning',
						'message' => __('License could not be activated. Please try again in a few hours and contact support if this error continues.', 'wp-backitup')
					)
				);

				// Setting transient for notification widget
				set_transient( 'wpbackitup_admin_notices', $admin_notices , DAY_IN_SECONDS);

				return false; //Exit and don't update license
			}


			//If no json object than error
			if (null==$license_data || false===$license_data){
				//update license last checked date and
				WPBackItUp_Utility::set_option('license_last_check_date', $data['license_last_check_date']);

				$admin_notices = array();
				array_push($admin_notices,
					array(
						'message_type' => 'warning',
						'message' => __('License could not be activated. Please try again in a few hours and contact support if this error continues.', 'wp-backitup')
					)
				);

				// Setting transient for notification widget
				set_transient( 'wpbackitup_admin_notices', $admin_notices , DAY_IN_SECONDS);

				return false;
			}

			$data['license_key'] = $license;
			$data['license_status'] = $license_data->license;

			$data['license_product_id'] = $license_data->item_id;

			if (property_exists($license_data,'error')) {
				$data['license_status_message'] = $license_data->error;
			}

			$data['license_limit'] = $license_data->license_limit;
			$data['license_sitecount'] = $license_data->site_count;
			$data['license_expires'] = $license_data->expires;

			$data['license_customer_name'] = $license_data->customer_name;
			$data['license_customer_email'] = $license_data->customer_email;

			$item_id = urldecode($license_data->item_id);

			//Which product? - Would be better to use item ID when refactored
			if (self::PREMIUM_PRODUCT_ID==$item_id) {

				//This is how we determine the type of license because
				//there is no difference in EDD
				if ( is_numeric( $license_data->price_id ) ) {

					switch ( $license_data->price_id ) {
						case 0: //personal
							$data['license_type'] = 1;
							break;
						case 1://professional
							$data['license_type'] = 2;
							break;
						case 2://premium
							$data['license_type'] = 3;
							break;
					}
				}
			} else if (self::LITE_PRODUCT_ID==$item_id) {
				$data['license_type'] = 0;
			} else {
				$data['license_type'] = -1; //CE
			}

			// admin notices
			$admin_notices = array();

			//EDD sends back expired in the error
			if (($license_data->license=='invalid')) {
				$data['license_status_message'] = __('License is invalid.', 'wp-backitup');

				//EDD sends back expired in the error
				if ($license_data->error == 'expired') {
					$data['license_status']         = 'expired';
					$data['license_status_message'] = __('License has expired.', 'wp-backitup');

					$renew_link = esc_url(sprintf('%s/checkout?edd_license_key=%s&download_id=679&nocache=true&utm_medium=plugin&utm_source=wp-backitup&utm_campaign=premium&utm_content=license&utm_term=license+expired', WPBACKITUP__SECURESITE_URL,$license));
					$license_expired_notice = sprintf( __('Your license has expired. Please <a href="%s" target="blank">renew</a> now for another year of <strong>product updates</strong> and access to our <strong>world class support</strong> team.','wp-backitup'),$renew_link);

					// adding license expired notice
					array_push($admin_notices,
						array(
							'message_type' => 'error',
							'message' =>$license_expired_notice
						)
					);

					// add scheduler stopped
					array_push($admin_notices,
						array(
							'message_type' => 'warning',
							'message' => __('License Expired: Scheduled backups are no longer active.', 'wp-backitup')
						)
					);

					WPBackItUp_Logger::log_info($activation_logname,__METHOD__, 'Expire License.' );
				}

				if ( ( $license_data->error == 'no_activations_left' ) ) {
					$data['license_status_message'] = __('Activation limit has been reached.', 'wp-backitup');

					// adding activation limit exceed notice
					array_push($admin_notices,
						array(
							'message_type' => 'warning',
							'message' => __('Your Activation limit has been reached', 'wp-backitup')
						)
					);
				}

				// Setting transient for notification widget
				set_transient( 'wpbackitup_admin_notices', $admin_notices , DAY_IN_SECONDS);
			}
		}

		WPBackItUp_Logger::log_info($activation_logname,__METHOD__, 'Updating License Options');
		foreach($data as $key => $val ) {
			WPBackItUp_Utility::set_option($key, $val);
			WPBackItUp_Logger::log_info($activation_logname,__METHOD__, 'Updated Option: ' .$key .':' .$val);
		}
		return true;
	}

	public function register_ce($form_data,$use_ssl=true){
		$registration_logname='debug_registration';
		$post_url = '/api/wpbackitup/register_lite';

		if (true===$use_ssl) {
			$post_url = WPBACKITUP__SECURESITE_URL . $post_url;
		} else {
			$post_url = WPBACKITUP__SITE_URL . $post_url;
		}
		WPBackItUp_Logger::log_info($registration_logname,__METHOD__, 'CE User Registration Post URL: ' . $post_url );
		WPBackItUp_Logger::log_info($registration_logname,__METHOD__, 'CE User Registration Post Form Data: ' );
		WPBackItUp_Logger::log($registration_logname,$form_data );

		$response = wp_remote_post( $post_url, array(
				'method'   => 'POST',
				'timeout'  => 45,
				'blocking' => true,
				'headers'  => array(),
				'body'     => $form_data,
				'cookies'  => array()
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			WPBackItUp_Logger::log_error($registration_logname,__METHOD__, 'CE User Registration Error: ' . $error_message );

			return false;

		} else {
			WPBackItUp_Logger::log_info($registration_logname,__METHOD__, 'CE User Registered Successfully:' );
			WPBackItUp_Logger::log($registration_logname,$response );

			return true;
		}

	}
	/**
	 * Calls the API and, if successful, returns the object delivered by the API.
	 * http://docs.easydigitaldownloads.com/article/384-software-licensing-api
	 *  Action Types Supported:
	 *
	 *  activate_license - Used to remotely activate a license key
	 *  deactivate_license - Used to remotely deactivate a license key
	 *  check_license - Used to remotely check if a license key is activated, valid, and not expired
	 *  get_version - Used to remotely retrieve the latest version information for a product
	 *
	 *
	 */
	private function edd_license_api_request( $activation_url,$action, $request_data ) {

		$api_params = array(
			'edd_action' 	=> $action,
			'license' 		=> $request_data['license'],
			'item_id' 	    => $request_data['item_id'],
			'url'           => $request_data['url']
		);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__, 'Activate License Request Info:');
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'API URL:' .$activation_url);
		WPBackItUp_Logger::log($this->log_name,$api_params);

		// Call the custom API.
		$response = wp_remote_post( $activation_url,
			array( 'timeout' => 15,
	        'sslverify' => false,
            'body' => $api_params )
		);

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__, 'API Response:'. var_export( $response,true ));

		$response_code = wp_remote_retrieve_response_code($response);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__, 'Response Code:'. $response_code);

		//IF no error
		if ( !is_wp_error( $response )  &&  200 == $response_code  ) {
			$response = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $response  && property_exists($response,'sections')) {
				$response->sections = maybe_unserialize( $response->sections );
			}

			return $response;

		} else { //Error
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__, 'Requesting Server Name:'. $_SERVER['SERVER_NAME']);
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__, 'Requesting IP:'. $_SERVER['SERVER_ADDR']);

			WPBackItUp_Logger::log_info($this->log_name,__METHOD__, 'Validation Response:');
			WPBackItUp_Logger::log($this->log_name,var_export($response,true));

			return false;
		}
	}
}