<?php

if( ! class_exists( 'WPBackItUp_FileSystem' ) ) {
	include_once WPBACKITUP__PLUGIN_PATH . '/lib/includes/class-filesystem.php';
}


/**
 * Handles file cleanup task processing for WPBackItUp
 *
 * This class will be called by the cleanup task processor
 *
 * @link       http://www.wpbackitup.com
 * @since      1.14.3
 *             
 */

class WPBackItUp_File_Cleanup_Processor extends WPBackItUp_Background_Process {

	//override prefix
	protected $prefix = 'wpbackitup';

	/**
	 * @var string
	 */
	protected $action = 'file_cleanup_process';

	const CLEANUP_LOG_NAME = 'debug_file_cleanup_processor';

	/**
	 * Task
	 *
	 * File cleanup tasks are handled here.
	 * Each item is a different file. This handler will handle all cleanup tasks.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed  False when task is complete|return updated item for further processing
	 */
	protected function task( $item ) {

        if (file_exists($item)){

            //if any delete fails keep on going but return false
            if (false===unlink($item)){
                WPBackItUp_Logger::log_error(self::CLEANUP_LOG_NAME,__METHOD__,'File was NOT Deleted:' . $item);
            }

            WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME,__METHOD__,'Deleted:' . $item);
        } else {
            //not an error
            WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME,__METHOD__,'File was not found:' . $item);
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