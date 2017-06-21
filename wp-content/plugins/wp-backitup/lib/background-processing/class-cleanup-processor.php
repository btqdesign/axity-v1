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
 * @since      1.14.3
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
	 * Each item is a different task. This handler will handle all cleanup tasks.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed  False when task is complete|return updated item for further processing
	 */
	protected function task( $item ) {

	    // method need to be triggered.
        $hook_name = sprintf('wpbackitup_cleanup_%s', $item);
        WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME,__METHOD__, 'Cleanup task - method to be triggered: '. $hook_name);

        if(has_action($hook_name)) {
            do_action($hook_name);
            WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME, __METHOD__, 'Hook should be triggered');
        }else{
            WPBackItUp_Logger::log_info(self::CLEANUP_LOG_NAME, __METHOD__, 'Hook is not available');
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