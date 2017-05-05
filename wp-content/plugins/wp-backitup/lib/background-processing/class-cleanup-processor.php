<?php

if( ! class_exists( 'WPBackItUp_FileSystem' ) ) {
	include_once WPBACKITUP__PLUGIN_PATH . '/lib/includes/class-filesystem.php';
}


/**
 * Handles background cleanup task processing for WPBackItUp
 *
 * This class will be called by the background processor
 *
 * @link       http://www.wpbackitup.com
 * @since      1.13.4
 *             
 */

class WPBackItUp_Cleanup_Processor extends WPBackItUp_Background_Process {

	//override prefix
	protected $prefix = 'wpbackitup';

	/**
	 * @var string
	 */
	protected $action = 'cleanup_process';

	const CLEANUP_LOG_NAME = 'debug_cleanup_processor';

	/**
	 * Task
	 *
	 * Cleanup tasks are handled here.
	 * Each item is a differnt task. This handler will handle all cleanup tasks.
	 *
	 * Item will always be an array where the first element is the cleanup task identifier
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed  False when task is complete|return updated item for further processing
	 */
	protected function task( $item ) {
		WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME,__METHOD__,'Task to process:'. var_export( $item,true ));

		//Items should always be array
		if (! is_array($item )){
			WPBackItUp_Logger::log_error(self::CLEANUP_LOG_NAME,__METHOD__,'Task was not array:'. var_export( $item,true ));
			return false;
		}

		switch (current($item)) {
			case "cleanup-zip":
				//if only one left then all are deleted
				if (count($item)<=1) {
					WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME,__METHOD__,'No more files to delete.');
					return false;
				}

				array_shift($item);//pop off task and get file list
				
				$file_system = new WPBackItUp_FileSystem();
				if (true===$file_system->delete_files( $item )){
					WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME,__METHOD__,'Files deleted successfully.');
				} else {
					WPBackItUp_Logger::log_error(self::CLEANUP_LOG_NAME,__METHOD__,'File delete error');
				}
				
				return false;
				break;

			case "cleanup-files":
				//if only one left then all are deleted
				if (count($item)<=1) {
					WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME,__METHOD__,'No more files to delete.');
					return false;
				}

				array_shift($item);//pop off task and get file list

				$file_system = new WPBackItUp_FileSystem();
				if (true===$file_system->delete_files( $item )){
					WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME,__METHOD__,'Files deleted successfully.');
				} else {
					WPBackItUp_Logger::log_error(self::CLEANUP_LOG_NAME,__METHOD__,'File delete error');
				}

				return false;
				break;


			default: //task not defined
				WPBackItUp_Logger::log_error(self::CLEANUP_LOG_NAME,__METHOD__,'Task Undefined.');
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
	}

}