<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * WP BackItUp  - Job Scheduler Class
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

class WPBackItUp_Job_Scheduler {

    private $log_name;

    /**
     * Constructor
     */
    function __construct() {
	    try {

	        $this->log_name = 'debug_job_scheduler';//default log name

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
     * Check the backup schedule to determine if the backup
     * task should be run today.
     *
     * @return bool
     */
    public function is_backup_scheduled(){
	    WPBackItUp_Logger::log($this->log_name,'**Check Backup Schedule**');

        try {
 //       	$wpbackitup_license = new WPBackItUp_License();

//            ///ONLY active premium get this feature
//            if (! $wpbackitup_license->is_premium_license()) {
//                WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'No premium license.');
//                return false;
//            }
//
//            ///ONLY work for 30 days after expiration
//            if ($wpbackitup_license->is_license_30_days_past_expire()){
//	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'License expired 30 days ago');
//                return false;
//            }


            //Get days scheduled to run on.
            $scheduled_dow = $this->get_backup_schedule();
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Scheduled Days of week: ' .$scheduled_dow); //1=monday, 2=tuesday..

            //What is the current day of the week
            $current_datetime = current_time( 'timestamp' );
            $current_date = date("Ymd",$current_datetime);
            $current_dow = date("N",$current_datetime); //1=monday

	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Current Date time:' . date( 'Y-m-d H:i:s',$current_datetime));
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Current Day of Week:' . $current_dow );

	        // Removing cache.
            wp_cache_delete ( 'alloptions', 'options' );

            //Get Last RUN date
            $lastrun_datetime = WPBackItUp_Utility::get_option('backup_lastrun_date');

	        //Testing Only - need to add time and test for constant doesnt exist
	        if (defined ('WPBACKITUP__TEST_RUN_HOURLY') && WPBACKITUP__TEST_RUN_HOURLY===true  && $current_datetime>=$lastrun_datetime+3600) {
		        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Run scheduled backup hourly');
		        return true;
	        }

            $lastrun_date = date("Ymd",$lastrun_datetime);
            $lastrun_dow =0;//0=none
            if ($lastrun_datetime!=-2147483648){// 1901-12-13:never run
                $lastrun_dow = date("N",$lastrun_datetime);
            }

	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Last Run Date Time:' . date( 'Y-m-d H:i:s',$lastrun_datetime));
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Last Run Day of Week:' . $lastrun_dow);


            //Did backup already run today
            if ($current_date==$lastrun_date){
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Backup already ran today');
                return false;
            }

            //Should it run on this day of the week
            if (false===strpos($scheduled_dow,$current_dow)){
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Not scheduled for: ' .$current_dow);
                return false;
            }

	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Backup should be run now.');
            return true;

        }catch(Exception $e) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
            return false;
        }

    }

	/**
	 * Save the days selected and add cron job if needed
	 *
	 * @param $days_selected
	 *
	 * @return bool
	 */
	public function save_backup_schedule($days_selected) {
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__, 'Save Schedule');
			WPBackItUp_Logger::log($this->log_name,$days_selected);

			//save option to DB even if empty
			$rtn = $this->set_backup_schedule($days_selected);

			//Add backup scheduled if doesnt exist
			if(!wp_next_scheduled( 'wpbackitup_queue_scheduled_jobs' ) ){
				wp_schedule_event( time()+3600, 'hourly', 'wpbackitup_queue_scheduled_jobs');
			}

			return $rtn;
	}

	/**
	 *  Job Schedule Filter
	 *
	 * @param $jobs
	 *
	 * @return array
	 */
	public function jobs_scheduled($jobs) {

		if (true === $this->is_backup_scheduled()){
			$jobs[] = "backup";
		}

		//Add additional scheduled jobs here.

		return $jobs;
	}

	/**
	 * Getter- Get backup schedule
	 *
	 * @return mixed
	 */
	public function get_backup_schedule(){
	    return WPBackItUp_Utility::get_option('backup_schedule','');
    }

	/**
	 * Setter- Set backup schedule
	 *
	 * @return mixed
	 */
	public function set_backup_schedule($value){
		WPBackItUp_Utility::set_option('backup_schedule',$value);
	}
} 