<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * WP BackItUp  - Scheduler Class
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

class WPBackItUp_Scheduler {

    private $log_name;

    /**
     * Constructor
     */
    function __construct() {
	    try {

	        $this->log_name = 'debug_scheduler';//default log name

	    } catch(Exception $e) {
		    error_log($e);
		    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Constructor Exception: ' .$e);
	    }
    }

    /**
     * Destructor
     */
    function __destruct() {

    }


    /**
     * Check to see if task is ready to run
     *
     * @param $task
     * @return bool
     */
    public function isJobScheduled($task){
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Check schedule for task: ' . $task);

        //Check for tasks
	    $jobs = array();
	    $jobs = apply_filters('wpbackitup_jobs_scheduled', $jobs); //Check add ons for scheduled jobs

        switch ($task) {
            case "backup":
	            return in_array("backup", $jobs);
                break;
            case "cleanup":
                return $this->check_cleanup_schedule(); //this could be added to filter
                break;
        }

	    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Task not found:' . $task);
        return false;
    }

	/**
	 * Check job schedule to make sure job is running ever 5 minutes
	 *
	 */
	public function check_queue_jobs_schedule(){
		$schedule = wp_get_schedule( 'wpbackitup_queue_scheduled_jobs');
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Queue Scheduled Job runs every:'. var_export($schedule,true));;

		if ('every_5_minutes'!=$schedule){
			wp_clear_scheduled_hook( 'wpbackitup_queue_scheduled_jobs');
			wp_schedule_event( time()+300, 'every_5_minutes', 'wpbackitup_queue_scheduled_jobs');
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Scheduled updated to every 5 minutes.');
		}
	}

    /**
     * Check the cleanup schedule to determine if the task should be run today.
     * Cleanup will be run once per day
     *
     * @return bool
     */
    private function check_cleanup_schedule(){
        global $WPBackitup;
	    WPBackItUp_Logger::log($this->log_name,'**Check Cleanup Schedule**');
        try {

            //What is the current day of the week
            $current_datetime = current_time( 'timestamp' );
            $current_date = date("Ymd",$current_datetime);

	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Current Date time:' . date( 'Y-m-d H:i:s',$current_datetime));

            //Get Last RUN date
            $lastrun_datetime = $WPBackitup->cleanup_lastrun_date();

            $lastrun_date = date("Ymd",$lastrun_datetime);
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Last Run Date Time:' . date( 'Y-m-d H:i:s',$lastrun_datetime));

            //Has it been at least an hour since the last cleanup?

	        $next_run_datetime=$lastrun_datetime+3600; //1 hour
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Next Run Date Time:' . date( 'Y-m-d H:i:s',$next_run_datetime));

	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'TimeToRun:' . $current_datetime . ':'.$next_run_datetime );
            if ($current_datetime>=$next_run_datetime){
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Cleanup should be run now.');
                return true;
            }

	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Not yet time to run Cleanup.');
            return false;

        }catch(Exception $e) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
            return false;
        }

    }
} 