<?php

if( ! class_exists( 'WPBackItUp_FileSystem' ) ) {
	include_once WPBACKITUP__PLUGIN_PATH . '/lib/includes/class-filesystem.php';
}


/**
 * Handles directory cleanup task processing for WPBackItUp
 *
 * This class will be called by the cleanup processor
 *
 * @link       http://www.wpbackitup.com
 * @since      1.14.3
 *             
 */

class WPBackItUp_Directory_Cleanup_Processor extends WPBackItUp_Background_Process {

	//override prefix
	protected $prefix = 'wpbackitup';

	/**
	 * @var string
	 */
	protected $action = 'directory_cleanup_process';

	const CLEANUP_LOG_NAME = 'debug_directory_cleanup_processor';

	/**
	 * Task
	 *
	 * Directory cleanup tasks are handled here.
	 * Each item is a different task. This handler will handle all cleanup tasks.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed  False when task is complete|return updated item for further processing
	 */
	protected function task( $item ) {

        WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME,__METHOD__,'Directory to be deleted: ' . $item);

        $file_system = new WPBackItUp_FileSystem(self::CLEANUP_LOG_NAME);

        if(file_exists($item)) {
            $file_system->recursive_delete($item);
        }else{
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