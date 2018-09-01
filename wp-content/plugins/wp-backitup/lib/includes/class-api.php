<?php if (!defined ('ABSPATH')) die('No direct access allowed');


/**
 * WPBackItUp API
 *
 * Class used to contain API's
 *
 * @since      1.15
 * @package    WPBackItUp
 * @author     WP BackItUp <info@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */
class WPBackItUp_API {

	private $log_name = 'debug_api';

	public function __construct() {

	}


	/**
	 * Get list of available backups
	 *
	 * Test URL: /wp-admin/admin-ajax.php?action=wpbackitup-get_available_backups&security=5965bb2bab
	 *
	 */
	public function get_available_backups() {

		try {
			if ( ! $this->is_authorized( __METHOD__ ) ) {
				return;
			}

			$backup_list_size = 10;//$this->backup_retained_number();
			//get available backups
			$backup_jobs = WPBackItUp_Job::get_jobs_by_status( WPBackItUp_Job::BACKUP, array(
				WPBackItUp_Job::COMPLETE,
				WPBackItUp_Job::ERROR
			), $backup_list_size );

			$available_backups = array();
			if (is_array($backup_jobs)) {
				foreach ( $backup_jobs as $backup_job ) {
					switch ( $backup_job->getJobStatus() ) {
						case WPBackItUp_Job::COMPLETE:
							$status = __( "Success", 'wp-backitup' );
							break;
						case WPBackItUp_Job::ACTIVE:
							$status = __( "Active", 'wp-backitup' );
							break;
						default:
							$status = __( "Error", 'wp-backitup' );
					}

					// Random status value
					// $cloud_status = array('uploading', 'uploaded', 'error', false);

					$available_backups[] = array(
						'backup_job_id'           => $backup_job->getJobId(),
						'backup_job_name'         => $backup_job->getJobName(),
						'backup_job_date'         => $backup_job->getJobDate(),
						'backup_job_type'         => $backup_job->getJobType(),
						'backup_job_run_type'     => $backup_job->getJobRunType(),
						'backup_job_start_time'   => $backup_job->getJobStartTime(),
						'backup_job_end_time'     => $backup_job->getJobEndTime(),
						'backup_job_status'       => $status,
						'backup_job_cloud_status' => $backup_job->getCloudStatus(),
						// pick a random $cloud_status[array_rand($cloud_status, 1)],
						'backup_job_zip_files'    => $backup_job->getJobMetaValue( 'backup_zip_files' ),
					);
				}
			}

			wp_send_json_success( $available_backups ); //Send JSON Response
			wp_die(); //end

		} catch ( Exception $e ) {
			$message = $e->getMessage();
			$errors  = array(
				'errorMessage' => $message
			);

			wp_send_json_error( $errors );
			wp_die(); //end
		}
	}

	/**
	 * Check user permission
	 *
	 * @param $action
	 *
	 * @return bool
	 * @throws Exception
	 * @internal param int|string $method_name Action nonce.
	 *
	 */
	private function is_authorized( $action ) {

		//get the method name only
		$action_array = explode( '::', $action );
		$method_name  = end( $action_array );

		//Make sure user is admin
		if ( ! current_user_can( 'manage_options' ) ) {
			throw new Exception( 'Access Denied(100)' );
		}

		if ( ! check_ajax_referer( $method_name, 'security', false ) ) {
			throw new Exception( 'Access Denied(200)' );
		}

		return true;
	}

	/**
	 * Get backup schedule  - API
	 *
	 */
	public function get_backup_schedule() {

		try {
			if (! $this->is_authorized(__METHOD__)) return;

			$backup_scheduler = new WPBackItUp_Job_Scheduler();
			//Get days scheduled to run on.
			$schedule = $backup_scheduler->get_backup_schedule();
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Schedule: ' .$schedule); //1=monday, 2=tuesday..

			if (WPBackItUp_Utility::isJSON($schedule)){
				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'IS JSON Schedule.');

				//get type of schedule
				$schedule= json_decode($schedule);
				if (null == $schedule) {
					$message = 'Unable to json decode schedule.';
					WPBackItUp_Logger::log_error($this->log_name,__METHOD__,$message);
					throw new Exception($message);
				}

				//booleans are treated like strings so need to convert
				$days = array();
				foreach ( $schedule->item->days as $index => $day ) {
					$days[] = json_decode($day);
				}

				$response = array (
					'name'=>$schedule->item->name,
					'frequency'=>$schedule->item->frequency,
					'days'=>$days,
					'day'=>$schedule->item->repeat_on,
					'start_time'=>$schedule->item->start_time,
					'start_date'=>strtotime($schedule->item->start_date),
					'enabled'=>json_decode($schedule->item->enabled),
				);

			} else {
				//This is the old schedule so convert to current format
				$days =array(false, false, false, false, false, false, false); //Sun,Mon, Tue
//				error_log(var_export($days,true));
				$days_original = explode(',',$schedule);
				if (is_array($days_original) && count($days_original)<=7){ //nothing in the schedule
					foreach ( $days_original as $index => $day ) {
						if ($day==7) $days[0]=true;
						else $days[$day] = true;
					}
				}

				$response = array (
					'name'=>'Weekly Backup(default)',
					'frequency'=>'week',
					'days'=> $days,
					'day'=>false,
					'start_time'=>'01:00',
					'start_date'=> strtotime('now'),
					'enabled'=>true,
				);
			}

			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Response: ' .var_export($response,true));
			wp_send_json_success($response); //Send JSON Response
			wp_die(); //end

		} catch (Exception $e) {
			$message = $e->getMessage();
			$errors = array(
				'errorMessage'=>$message
			);

			wp_send_json_error($errors);
			wp_die(); //end
		}
	}

	/**
	 * Set backup schedule - API
	 *
	 */
	public function set_backup_schedule() {

		try {
			if (! $this->is_authorized(__METHOD__)) return;

			$form_data= $_POST['data'];
			//error_log(var_export($form_data,true));

			$errors     = array();
			$this->validate_field($form_data['name'], 'name', $errors,null,false);
			$this->validate_field($form_data['start_date'], 'start_date', $errors,null,true);
			$this->validate_field($form_data['frequency'], 'frequency', $errors,array('day','week', 'month'),true);
			//days below
			$this->validate_field($form_data['start_time'], 'start_time', $errors,null,true);
			$this->validate_field($form_data['enabled'], 'enabled', $errors,null,true);
			//repeat on below

			//remove the time zone info
			$start_date =  preg_replace('/( \(.*)$/','',$form_data['start_date']);
			$start_date = strtotime($start_date);
			$form_data['start_date'] = date('Y-m-d', $start_date);
			//error_log(var_export($form_data['start_date'],true));

			//If weekly then days of the week are required
			$days_required = 'week' == $form_data['frequency'] ? true : false;
			//error_log(var_export($days,true));
			if ($days_required){
				if (! is_array($form_data['days']) || count($form_data['days'])!=7) {
					$errors['days'] = __('Invalid input', 'wp-backitup');
				}

			}

			//If monthly then day is required
			$day_required = 'month' == $form_data['frequency'] ? true : false;
			if ($day_required) {
				$this->validate_field($form_data['repeat_on'], 'repeat_on', $errors,null, $day_required);
			}

			if(!empty($errors)){
				wp_send_json_error($errors);
			}else{
				//save to DB
				$json_schedule= json_encode(array('item' => $form_data));
				$backup_scheduler = new WPBackItUp_Job_Scheduler();
				$backup_scheduler->set_backup_schedule($json_schedule);

				wp_send_json_success();
			}

			wp_die(); //end

		} catch (Exception $e) {
			$message = $e->getMessage();
			$errors = array(
				'errorMessage'=>$message
			);

			wp_send_json_error($errors);
			wp_die(); //end
		}
	}

	/**
	 * Private validate method
	 */
	private function validate_field(&$value, $field, &$errors,$value_list=null,$required=true){
		if (! $required && empty($value)) return true;

		if (!empty($value)) {
			$value = filter_var($value, FILTER_SANITIZE_STRING);
			if ($required && empty($value)) {
				$errors[$field] = __('Invalid input', 'wp-backitup');
				return false;
			}

			//List of valid values
			if (!empty($value_list) && is_array($value_list)) {
				//error_log(var_export($value_list,true));
				if ( ! in_array( $value, $value_list ) ) {
					$errors[ $field ] = __( 'Invalid Value (' .$value . ')', 'wp-backitup' );
				}
			}

		} else {
			$errors[$field] = __('Field should not be empty', 'wp-backitup');
			return false;
		}
		return true;
	}

	/*****************************************************************************
	 * This is an example action that should be used as a template
	 *
	 * Test URL:/wp-admin/admin-ajax.php?action=wpbackitup_action_template&security=269a08f156
	 *  - echo wp_create_nonce( "action_template" ); //Get Nonce with this method
	 *
	 ******************************************************************************/
	public function action_template() {

		try {
			if (! $this->is_authorized(__METHOD__)) return;

			//Do something useful here

			wp_send_json_success(); //Send JSON Response
			wp_die(); //end

		} catch (Exception $e) {
			$message = $e->getMessage();
			$errors = array(
				'errorMessage'=>$message
			);

			wp_send_json_error($errors);
			wp_die(); //end
		}
	}
}
