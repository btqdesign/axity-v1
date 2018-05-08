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