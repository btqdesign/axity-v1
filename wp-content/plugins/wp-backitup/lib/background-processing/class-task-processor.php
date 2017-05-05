<?php

/**
 * Handles background task processing for WPBackItUp
 *
 * This class will be called by the background processor
 *
 * @link       http://www.wpbackitup.com
 * @since      1.0.0
 *
 * @package    WPBackItUp
 * @subpackage WPBackItUp/background-processing
 */

class WPBackItUp_Task_Processor extends WPBackItUp_Background_Process {

	//override prefix
	protected $prefix = 'wpbackitup';

	//override action
	protected $action = 'background_process';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * Fetch next task from job manager and invoke.
	 * When all tasks are complete, end background processor(return false)
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		$logname = 'debug_task_processor';

		WPBackItUp_Logger::log_info($logname,__METHOD__,'Task Processor Event:'. var_export( $item, true ) );

		try {

			if (empty($item)){
				WPBackItUp_Logger::log_error($logname,__METHOD__,'NO item passed to task processor.');
				return false;
			}

			$item_parts = explode('_',$item);
			if (false===$item_parts){
				WPBackItUp_Logger::log_error($logname,__METHOD__,'NO job id passed to task processor.');
				return false;
			}

			$job_id = end($item_parts);
			if (empty($job_id)){
				WPBackItUp_Logger::log_error($logname,__METHOD__,'NO JOB ID found.');
				return false;
			}

			//Get the job info
			$job = WPBackItUp_Job::get_job_by_id($job_id);
			if (false===$job){
				WPBackItUp_Logger::log_error($logname,__METHOD__,'NO JOB found:'.$job_id);
				return false;
			}

			WPBackItUp_Logger::log_info($logname,__METHOD__, sprintf('Job found:(%s)',var_export($job,true)));

			//If job is active or queued then fire task
			if (WPBackItUp_Job::ACTIVE == $job->getJobStatus() || WPBackItUp_Job::QUEUED == $job->getJobStatus()) {

				//Use the old hook for now
				$job_type=$job->getJobType();
				if (WPBackItUp_Job::BACKUP ==  $job_type||
				    WPBackItUp_Job::RESTORE ==  $job_type ||
				    WPBackItUp_Job::CLEANUP ==  $job_type) {

					$query_args= array(
						'action' => 'wp-backitup_run_task',
						'job_id' => $job_id,
						'nonce'  => wp_create_nonce( 'wp-backitup_run_task' ),
						'uid'    => current_time('timestamp'), //caching
					);

					$post_args =  array(
						'timeout'   => 5,
						'blocking'  => false,
						'body'      => '',
						'cookies'   => $_COOKIE,
						'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
					);


					WPBackItUp_Logger::log_info($logname,__METHOD__,'Before Fire Action:wp-backitup_run_task');

					$url  = add_query_arg( $query_args, admin_url( 'admin-ajax.php' ));
					WPBackItUp_Logger::log_info($logname,__METHOD__, sprintf('Request: %s ',var_export($url,true)));

					$response =  wp_remote_post( esc_url_raw( $url ), $post_args );
					WPBackItUp_Logger::log_info($logname,__METHOD__, sprintf('Remote Get Response:: %s ',var_export($response,true)));

					if( is_wp_error( $response ) ) { //timeout error is ok
						WPBackItUp_Logger::log_info($logname,__METHOD__, sprintf('Error on Remote Post Request:: %s ',var_export($response,true)));
					}

					WPBackItUp_Logger::log_info($logname,__METHOD__, sprintf('Wait %s seconds.',WPBACKITUP__TASK_WAIT_SECONDS));
					sleep(WPBACKITUP__TASK_WAIT_SECONDS);//wait N seconds then check again

					WPBackItUp_Logger::log_info($logname,__METHOD__,'After Fire Action:wp-backitup_run_task');
					return $item;
				}

				//NEW Tasks

				//get task info
				$task = $job->get_next_task();
				WPBackItUp_Logger::log_info( $logname, __METHOD__, sprintf( '(%s) Task Found: %s', $job->getJobType(), var_export($task,true)));
				if ( false === $task ) {
					//no tasks ready for processing
					//If task is still processing, will timeout in 1 minutes
					//review get_next_task to understand timeout/error
					$wait_seconds= 2;
					WPBackItUp_Logger::log_info($logname,__METHOD__, sprintf('No task ready for processing. Wait %s seconds.',$wait_seconds));
					sleep($wait_seconds);//wait 5 seconds then check again
					return $item;
				}

				//Was there an error on the previous run
				if ( WPBackItUp_Job::ERROR == $task->getStatus() ) {
					WPBackItUp_Logger::log_error($logname,__METHOD__,'Previous Task Error.');
					return false;
				}

				//Get task Name + add wpb prefix for hook
				$hook_name = 'wpbackitup_' . $task->getTaskName();

				WPBackItUp_Logger::log_info($logname,__METHOD__,sprintf('Before Fire Action:(%s)%s',$job_id,$hook_name));
				$task->increment_retry_count();
				do_action( $hook_name, $task );// RUN TASK
				WPBackItUp_Logger::log_info($logname,__METHOD__, sprintf('After Fire Action:(%s)%s',$job_id ,$hook_name));

				return $item;

			} else {
				WPBackItUp_Logger::log_info($logname,__METHOD__,'Job Completed:' . $job_id);
				return false; //job has completed
			}

		} catch (Exception $e) {
			WPBackItUp_Logger::log_error($logname,__METHOD__, $e->getMessage());
			return false;
		}
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}

}