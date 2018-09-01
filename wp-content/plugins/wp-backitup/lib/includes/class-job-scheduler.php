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

            //Get days scheduled to run on.
            $schedule = $this->get_backup_schedule();
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Schedule: ' .$schedule); //1=monday, 2=tuesday..

			if (WPBackItUp_Utility::isJSON($schedule)){
				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'IS JSON Schedule.');

				//get type of schedule
				$schedule= json_decode($schedule);
				if (null==$schedule) {
					WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Unable to json decode schedule.');
					return false;
				}

				$name       = $schedule->item->name;
		        $days       = $schedule->item->days;
			    $repeat_on  = $schedule->item->repeat_on;

				//make sure schedule is enabled
				$enabled    = json_decode($schedule->item->enabled);//saved as string so need to decode again
				if (true===$enabled) {
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Schedule is enabled.' );
				} else {
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Schedule is disabled.' );
					return false;
				}

				//check start date
				$start_date = strtotime($schedule->item->start_date);
				if (false===$start_date){
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Invalid start date:'.var_export($schedule->item->start_date,true) );
					return false;
				}

				//Check start date is today or later
				if (date('Ymd') < date('Ymd', $start_date)){
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Start date has not passed yet:' .$start_date);
					return false;
				}


				//check start time
				$start_time = $schedule->item->start_time;
				$valid_time = preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $start_time);
				if (false===$valid_time){
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Invalid start time:'.var_export($start_time,true) );
					return false;
				}

				//check start date and add time
				$start_date_time = $schedule->item->start_date .' ' .$schedule->item->start_time;
				$start_timestamp = strtotime($start_date_time);
				if (false===$start_timestamp){
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Invalid start date time:' . var_export($start_date_time,true));
					return false;
				}

				//check frequency
				$frequency   = $schedule->item->frequency;
				if ($frequency!='day' && $frequency!='week' && $frequency!='month'){
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Invalid frequency:' . var_export($frequency,true));
					return false;
				}

				//What is the current day of the week
				$current_datetime       = current_time( 'timestamp' );
				$current_dow            = date( "w", $current_datetime ); //0=sunday
				$current_day_of_month   = date( "d", $current_datetime ); //0=sunday

				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Current Date time:' . date( 'Y-m-d H:i:s', $current_datetime ) );
				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Current Day of Week:' . $current_dow );

				// Removing cache.
				wp_cache_delete( 'alloptions', 'options' );

				//Get Last RUN date
				$lastrun_datetime = WPBackItUp_Utility::get_option( 'backup_lastrun_date' );

				$lastrun_date = date( "Ymd", $lastrun_datetime );
				$lastrun_dow = date( "w", $lastrun_datetime );

				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Last Run Date Time:' . date( 'Y-m-d H:i:s', $lastrun_datetime ) );
				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Last Run Day of Week:' . $lastrun_dow );

				//has backup run today?
				if (date('Ymd') == date('Ymd', $lastrun_datetime)){
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Backup already ran today.' );
					return false;
				}

				//default to max future date
				$next_run_time = 2145916800; // 1.19.2038 max for 32-bit systems

				//Check backup frequency
				switch ($frequency) {
					case 'day': //Run Daily
						WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Daily Backups Scheduled.' );

						//Is it time to run the daily backup yet?
						$next_run_time = strtotime( date( 'Ymd' ) . ' ' . $start_time );
						break;

					case 'week': //Run multiple times per week on days selected

						WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Weekly Backups Schedule.' );

						//Is todays DOW turned on in scheduler?
						WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Todays Day of Week is:' . $current_dow );
						$is_today_on = json_decode( $days[ $current_dow ] );
						WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Is Today ON:' . var_export( $is_today_on, true ) );

						//IF today is on is it time to run
						if ( $is_today_on ) {
							$next_run_time = strtotime( date( 'Ymd' ) . ' ' . $start_time );
						} else {
							WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Today is not the correct DOW.' );
						}

						break;
					case 'month': //Run once per month
						WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Monthly Backups Schedule.' );

						//what day is today?
						WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Current day of month:' . $current_day_of_month );
						WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Repeat day of month:' . $repeat_on );

						//Is today the last day of the month
						$is_last_day_of_month = false;
						if ( date( 'Ymd' ) == date( "Ymt" ) ) {
							$is_last_day_of_month = true;
						}

						//if today IS repeat day then add the time and run
						if ( $repeat_on == $current_day_of_month ) {
							$next_run_time = strtotime( date( 'Ymd' ) . ' ' . $start_time );
						} //If today is the last day of the month AND customer selected, 30, 31 OR last day then run
						elseif ( $is_last_day_of_month && ( $repeat_on == 99 || $repeat_on == 30 || $repeat_on == 31 ) ) {
							$next_run_time = strtotime( date( 'Ymd' ) . ' ' . $start_time );
						} else {
							WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Today is NOT repeat day.' );
						}

						break;
				}

				//Is it time to run yet?
				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Backup next run datetime:' . date( 'Y-m-d H:i:s',$next_run_time));
				if (current_time( 'timestamp' )>=$next_run_time){
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Backup SHOULD be run now.' );
					return true;
				} else {
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Backup should NOT be run now.' );
					return false;
				}
			} else {


				//What is the current day of the week
				$current_datetime = current_time( 'timestamp' );
				$current_date     = date( "Ymd", $current_datetime );
				$current_dow      = date( "N", $current_datetime ); //1=monday

				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Current Date time:' . date( 'Y-m-d H:i:s', $current_datetime ) );
				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Current Day of Week:' . $current_dow );

				// Removing cache.
				wp_cache_delete( 'alloptions', 'options' );

				//Get Last RUN date
				$lastrun_datetime = WPBackItUp_Utility::get_option( 'backup_lastrun_date' );

				//Testing Only - need to add time and test for constant doesnt exist
				if ( defined( 'WPBACKITUP__TEST_RUN_HOURLY' ) && WPBACKITUP__TEST_RUN_HOURLY === true && $current_datetime >= $lastrun_datetime + 3600 ) {
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Run scheduled backup hourly' );

					return true;
				}

				$lastrun_date = date( "Ymd", $lastrun_datetime );
				$lastrun_dow  = 0;//0=none
				if ( $lastrun_datetime != - 2147483648 ) {// 1901-12-13:never run
					$lastrun_dow = date( "N", $lastrun_datetime );
				}

				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Last Run Date Time:' . date( 'Y-m-d H:i:s', $lastrun_datetime ) );
				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Last Run Day of Week:' . $lastrun_dow );


				//Did backup already run today
				if ( $current_date == $lastrun_date ) {
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Backup already ran today' );

					return false;
				}

				//Should it run on this day of the week
				if ( false === strpos( $schedule, $current_dow ) ) {
					WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Not scheduled for: ' . $current_dow );

					return false;
				}

				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Backup should be run now.' );

				return true;
			}

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