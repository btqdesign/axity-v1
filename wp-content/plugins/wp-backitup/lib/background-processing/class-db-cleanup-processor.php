<?php

if( ! class_exists( 'WPBackItUp_Job' ) ) {
	include_once WPBACKITUP__PLUGIN_PATH . '/lib/includes/class-job.php';
}


/**
 * Handles database cleanup task processing for WPBackItUp
 *
 * This class will be called by the cleanup task processor
 *
 * @link       http://www.wpbackitup.com
 * @since      1.14.3
 *             
 */

class WPBackItUp_DB_Cleanup_Processor extends WPBackItUp_Background_Process {

	//override prefix
	protected $prefix = 'wpbackitup';

	/**
	 * @var string
	 */
	protected $action = 'db_cleanup_process';

	const CLEANUP_LOG_NAME = 'debug_db_cleanup_processor';

	/**
	 * Task
	 *
	 * Database cleanup tasks are handled here.
	 * Each item is a different task. This handler will handle all cleanup tasks.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed  False when task is complete|return updated item for further processing
	 */
	protected function task( $item ) {
        WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME,__METHOD__,'Job to be purged: ' . $item);
        if($item) {
            WPBackItUp_Job::delete_job_records($item);
        }
        return false;

	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();
	}

}