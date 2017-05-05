<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * WP BackItUp  - Job Class
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */


//Includes
if( !class_exists( 'WPBackItUp_Utility' ) ) {
	include_once 'class-utility.php';
}

if( !class_exists( 'WPBackItUp_Mutex' ) ) {
	include_once 'class-mutex.php';
}

if( !class_exists( 'WPBackItUp_DataAccess' ) ) {
	include_once 'class-database.php';
}


class WPBackItUp_Job {

	const JOB_TITLE='wpbackitup_job';
	const DEFAULT_LOG_NAME='debug_job';

	//Status values
	const ERROR = 'error';
	const ACTIVE ='active';
	const COMPLETE ='complete';
	const CANCELLED='cancelled';
	const QUEUED = 'queued';
	const DELETED = 'deleted';

	//Job types
	const BACKUP =  'backup';
	const RESTORE = 'restore';
	const CLEANUP = 'cleanup';

	//Job run type
	const SCHEDULED = 'scheduled';
	const MANUAL = 'manual';
	const IMPORTED = 'imported';

	//Properties
	private $job_id;
	private $job_name;
	private $job_type;
	private $job_run_type;
	private $instance_id;

	private $log_name;

	private $job_start_time=null;
	private $job_end_time=null;

	private $locked;
	private $mutex;

	private $job_meta;

	public  $job_status;

	// ** JOB TASK CONSTANTS **
	// Task can be defined outside this class.
	// These are added here because they are core tasks

	//Backup Tasks
	public static $BACKUP_TASKS = array(
		'task_preparing',
		"task_inventory_database",
		'task_inventory_plugins',
		'task_inventory_themes',
		'task_inventory_uploads',
		'task_inventory_others',
		'task_backup_siteinfo',
		'task_export_db' ,
		'task_merge_sql',
		'task_backup_db',
		'task_backup_themes',
		'task_backup_plugins',
		'task_backup_uploads',
		'task_backup_other',
		'task_validate_backup',
		'task_create_manifest',
		'task_encrypt_files',
		'task_create_backupset',
		'task_finalize_backup',
	);

	public static $RESTORE_TASKS = array(
		'task_preparing',
		'task_inventory_backupset',
		'task_unpack_backupset',
		'task_unzip_backup_files',
		'task_validate_backup',
		'task_import_database',
		'task_update_snapshot',
		'task_stage_wpcontent',
		'task_restore_wpcontent',
		'task_restore_database',
	);

	public static $CLEANUP_TASKS = array(
		'task_scheduled_cleanup'
	);

	// ** END JOB TASK CONSTANTS **

	 private function __construct($db_job) {
		try {
			$this->log_name = self::DEFAULT_LOG_NAME;//default log name

			$this->set_properties($db_job);

		} catch(Exception $e) {
			error_log($e); //Log to debug
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Constructor Exception: ' .$e);
		}
	}

	function __destruct() {

		//Release lock
		if (true===$this->locked){
			$this->release_lock();
		}

	}

	private function set_properties($db_job){

		if ( ! is_object($db_job)) {
			throw new exception( 'Cant create job object, missing db entity' );
		}

		$this->job_id=$db_job->job_id;
		$this->job_name=$db_job->job_name;
		$this->job_type=$db_job->job_type;
		$this->job_run_type=$db_job->job_run_type;

		$this->instance_id=current_time('timestamp');
		$this->job_status=$db_job->job_status;

		$this->job_meta = maybe_unserialize($db_job->job_meta);

		//leave set to null default
		if (! empty($db_job->job_start) && '0000-00-00 00:00:00'!= $db_job->job_start) {
			$this->job_start_time=$db_job->job_start;
		}

		//leave set to null default
		if (! empty($db_job->job_end) && '0000-00-00 00:00:00'!= $db_job->job_end) {
			$this->job_end_time = $db_job->job_end;
		}


	}

	/**
	 * Get lock if possible
	 *
	 * @param $lock_name
	 *
	 * @return bool
	 *
	 */
	public function get_lock ($lock_name){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin:'.$lock_name);

		try {
			$lock_file_path = WPBACKITUP__PLUGIN_PATH .'/logs';
			$this->mutex = new WPBackItUp_Mutex($lock_name,$lock_file_path);
			if ($this->mutex->lock(false)) {
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Process LOCK acquired');
				$this->locked=true;
			} else {
				//This is not an error, just means another process has it allocated
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Process LOCK Failed');
				$this->locked=false;
			}

			return $this->locked;

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Process Lock error: ' .$e);
			$this->locked=false;
			return $this->locked;
		}
	}

	/**
	 * Release lock
	 *
	 * @return bool
	 */
	public function release_lock (){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		try{
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Mutex:'.var_export($this->mutex,true));
			if (null!=$this->mutex) {
				$this->mutex->releaseLock();
				$this->mutex = null;
			}

			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Lock released');
			$this->locked=false;
		}catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Process UNLOCK error: ' .$e);
		}
	}

	/**
	 * check if  job is complete
	 *  -  no active or queues tasks
	 *  -  method will update job status
	 *
	 * @return bool
	 */
	public function is_job_complete() {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin' );

		$error_tasks = WPBackItUp_Job_Task::get_job_tasks($this->job_id,array(WPBackItUp_Job::ERROR));
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Active or Queued Tasks found' .count($error_tasks) );
		if (count($error_tasks)>0) {
			$this->setStatus(WPBackItUp_Job::ERROR);
			return false;
		}

		//get all the queued, active
		$queues_active_tasks = WPBackItUp_Job_Task::get_job_tasks($this->job_id,array(WPBackItUp_Job::ACTIVE, WPBackItUp_Job::QUEUED));
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Active or Queued Tasks found' .count($queues_active_tasks) );
		if (count($queues_active_tasks)>0) {
			return false;
		}

		//Job is complete
		$this->setStatus(WPBackItUp_Job::COMPLETE);
		return true;
	}


	/**
	 * Fetch the next task in the stack
	 *
	 * @return bool|object False on try again, task object on success
	 */
	public function get_next_task(){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		//get all the queued, active. error tasks
		$tasks = WPBackItUp_Job_Task::get_job_tasks($this->job_id,array(WPBackItUp_Job::ACTIVE, WPBackItUp_Job::QUEUED,WPBackItUp_Job::ERROR));

		//Enumerate the tasks
		foreach ($tasks as $task) {

			//if next task in stack is queued then its time to get to work
			switch ($task->getStatus()) {

				case self::QUEUED:

					//Try to allocate the task
					if (true===$task->try_allocate_task()){
						$this->setStatus(WPBackItUp_Job::ACTIVE);
						return $task;

					} else{
						//couldnt allocate task
						return false;
					}

				case self::ACTIVE:
					//Error if >= 1 minutes since the last update
					global $WPBackitup;
					$task_timeout_value = $WPBackitup->max_timeout();
					
					if (current_time('timestamp')>=$task->getLastUpdatedTimeStamp()+$task_timeout_value){
						$task->setStatus(WPBackItUp_Job_Task::ERROR);

						//Update job to error also
						$this->setStatus(WPBackItUp_Job::ERROR);

						return $task;

					}else {

						WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Job:' .$task->getJobId() .'-' . $task->getTaskName() . ' is still active' );
						//if its been less than 3 minutes then wait
						return false;
					}

				case self::ERROR:
					//Job should already be error but update if not
					//Update job to error also
					$this->setStatus(WPBackItUp_Job::ERROR);;

					return $task;
			}
		}

		//If no more tasks then job must be done
		$this->setStatus(WPBackItUp_Job::COMPLETE);

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End - no tasks to allocate');
		return false; //no tasks to allocate now but job should be complete next time
	}



	/**
	 * Set Job Meta Property
	 *
	 * @param $meta_name
	 * @param $meta_value
	 *
	 * @return bool
	 */
	public function setJobMetaValue($meta_name,$meta_value){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin - Update job meta:' .$this->job_id .'-'. $meta_name);

		$this->job_meta[$meta_name]=$meta_value;

		$db = new WPBackItUp_DataAccess();
		return $db->update_job_meta($this->job_id,$this->job_meta);
	}

	/**
	 * Set job status to active
	 *
	 * @param $status
	 *
	 * @return bool true on success/false on error
	 */
	public function setStatus( $status ) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		$db= new WPBackItUp_DataAccess();
		if (! $db->update_job_status($this->job_id,$status)) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'End - Job status NOT set.');
			return false;
		}

		$this->job_status = $status;

		//Set job end Time
		switch ( $status ) {
			case self::ACTIVE:
				$this->setJobStartTime();
				break;

			case self::COMPLETE:
			case self::CANCELLED:
			case self::ERROR:
				$this->setJobEndTime();
				//set the folder prefix
				break;

			default:
				break;
		}


		WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'End - Backup Job status set to:' . $this->job_id . '-' . $status );
		return true;

	}


	/**
	 * Set job start time
	 *
	 * @return bool
	 */
	private function setJobStartTime() {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		if($this->job_start_time == null) {
			$job_start_time= current_time('mysql');

			$db = new WPBackItUp_DataAccess();
			if ($db->update_job_start_time($this->job_id,$job_start_time)){
				$this->job_start_time= $job_start_time;
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End - Backup Job start time set');
				return true;
			} else{
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'End - Backup Job start time NOT set.');
				return false;
			}
		}

	}

	/**
	 * Set job end time
	 *
	 * @return bool
	 */
	private function setJobEndTime() {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		if($this->job_end_time == null) {
			$job_end_time = current_time('mysql');
			$db = new WPBackItUp_DataAccess();

			if ( $db->update_job_end_time( $this->job_id, $job_end_time ) ) {
				WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'End - Backup Job end time set' );
				$this->job_end_time = $job_end_time;
				return true;
			} else {
				WPBackItUp_Logger::log_error( $this->log_name, __METHOD__, 'End - Backup Job end time NOT set.' );
				return false;
			}
		}

	}


	/**
	 * Set job run type
	 *
	 * @param $job_run_type
	 *
	 * @return bool
	 */
	public function setJobRunType( $job_run_type ) {

		$db = new WPBackItUp_DataAccess();
		if ($db->update_job_run_type($this->job_id,$job_run_type)){
			$this->job_run_type = $job_run_type;
			return true;
		} else {
			return false;
		}

	}


	/**-----  private helpers       ----***/
	/**
	 * Add slashes to a string or array of strings.
	 *
	 * This should be used when preparing data for core API that expects slashed data.
	 * This should not be used to escape data going directly into an SQL query.
	 *
	 * @since 3.6.0
	 *
	 * @param string|array $value String or array of strings to slash.
	 * @return string|array Slashed $value
	 */
	private function wpb_slash( $value ) {
		//only use on strings and arrays
		if(! is_array($value) && ! is_string($value)){
			return $value;
		}

		//only available 3.6 or later
		if (function_exists('wp_slash')) return wp_slash($value);

		if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				if ( is_array( $v ) ) {
					$value[$k] = $this->wpb_slash( $v );
				} else {
					$value[ $k ] = addslashes( $v );
				}
			}
		} else {
			$value = addslashes( $value );
		}

		return $value;
	}

	/**
	 * Remove slashes from a string or array of strings.
	 *
	 * This should be used to remove slashes from data passed to core API that
	 * expects data to be unslashed.
	 *
	 * @since 3.6.0
	 *
	 * @param string|array $value String or array of strings to unslash.
	 * @return string|array Unslashed $value
	 */
	private function wpb_unslash( $value ) {
		return stripslashes_deep( $value );
	}



	/**---------STATICS---------***/

	/**
	 * Is there at least 1 job queued or active for job type?
	 *
	 * @param $job_type
	 *
	 * @return bool|job
	 */
	public static function is_job_queued_active($job_type) {
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin - Check Job Queue:' . $job_type);


		$jobs = WPBackItUp_Job::get_jobs_by_status($job_type,array(WPBackItUp_Job::QUEUED,WPBackItUp_Job::ACTIVE));
		WPBackItUp_Logger::log(self::DEFAULT_LOG_NAME,$jobs);

		if (is_array($jobs) && count($jobs)>0) {
			//if more than one get first one in stack
			$job=current($jobs);
			WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Jobs found:' . count($jobs) );
			return $job;
		}

		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'No jobs found:' . $job_type);
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'End');
		return false;
	}


	/**
	 * Is there at least 1 job queued or active?
	 *
	 * @return bool
	 */
	public static function is_any_job_queued_active() {
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin - Check Job Queue');

		$db = new WPBackItUp_DataAccess();

		$queued_active_job_count = $db->get_queued_active_job_count(array(WPBackItUp_Job::BACKUP,WPBackItUp_Job::RESTORE,WPBackItUp_Job::CLEANUP));
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Jobs found:' . $queued_active_job_count );

		if ($queued_active_job_count>0) return true;

		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'End');
		return false;
	}

	/**
	 * get completed jobs
	 *      - complete, cancelled, error, deleted
	 *
	 * @param $job_type
	 *
	 * @return bool
	 */
	public static function get_finished_jobs_by_type($job_type) {
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin');

		$db= new WPBackItUp_DataAccess();
		$jobs=$db->get_jobs_by_status($job_type,array(WPBackItUp_Job::COMPLETE,WPBackItUp_Job::CANCELLED,WPBackItUp_Job::ERROR, WPBackItUp_Job::DELETED));
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Jobs found:' . count($jobs));

		return count( $jobs ) > 0 ? $jobs : false;
	}

	/**
	 * Cancel all queued or active jobs by job_type
	 *
	 * @param $job_type
	 *
	 * @return int Count of jobs cancelled
	 */
	public static function cancel_all_jobs($job_type) {
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin - Cancel all jobs:'.$job_type);

		$counter=0;
		$jobs=self::get_jobs_by_status($job_type,array(WPBackItUp_Job::QUEUED,WPBackItUp_Job::ACTIVE));
		if (is_array($jobs) && count($jobs)>0){
			foreach($jobs as $job){
				$counter++;
				$job->setStatus(WPBackItUp_Job::CANCELLED);
				WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Job Cancelled:' . $job->getJobId());
			}
		}

		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'End - Jobs cancelled:' .$counter);
		return $counter;
	}


	/**
	 * Delete all job records
	 *
	 * @param $job_id
	 */
	private static function delete_job_records($job_id){
		$log_name='debug_purge_jobs';
		$db= new WPBackItUp_DataAccess();

		//delete items
		$items_deleted = $db->delete_job_items($job_id);
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Deleted items:'.$items_deleted);


		//delete tasks
		$tasks_deleted = $db->delete_job_tasks($job_id);
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Deleted Tasks:'.$tasks_deleted);

		//delete job
		$jobs_deleted = $db->delete_job_by_id($job_id);
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Deleted Jobs:'.$jobs_deleted);

		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Deleted Job:' .$job_id);

	}

	/**
	 * purge job records by job type
	 *  - complete, cancelled, error, deleted
	 *
	 * @param $job_type Backup, Cleanup, restore
	 *
	 * @param int $dont_purge - dont purge this many
	 *
	 * @return int
	 */
	public static function purge_jobs($job_type,$dont_purge=5) {
		$log_name='debug_purge_jobs';
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Begin - Purge Jobs:'.$job_type );

		$jobs_purged=0;

		/*------------------------------------------------------*/
		// Purge jobs with status: cancelled, error, deleted
		/*------------------------------------------------------*/

		$jobs = self::get_jobs_by_status($job_type,array(WPBackItUp_Job::DELETED,WPBackItUp_Job::ERROR,WPBackItUp_Job::CANCELLED));
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Total finished jobs found:' .count($jobs));

		/*   Delete everything but the successfully completed jobs */
		if (is_array($jobs) && count($jobs)>0) {

			foreach ($jobs  as $key=>$job){

				self::delete_job_records($job->getJobId());
				$jobs_purged+=1;

				WPBackItUp_Logger::log_info($log_name,__METHOD__,'Deleted Job:');
				WPBackItUp_Logger::log_info($log_name,__METHOD__,var_export($job,true));
			}
		}

		/*------------------------------------------------------*/
		// Purge the completed BACKUP jobs with no backups attached
		/*------------------------------------------------------*/

		if (WPBackItUp_Job::BACKUP==$job_type) {
			$jobs = self::get_jobs_by_status($job_type,array(WPBackItUp_Job::COMPLETE));
			WPBackItUp_Logger::log_info($log_name,__METHOD__,'Total finished jobs found:' .count($jobs));

			/*   Check all remaining to make sure there is a backup folder associated with each */
			if (is_array($jobs) && count($jobs)>0) {
				foreach ($jobs  as $key=>$job){
					WPBackItUp_Logger::log_info($log_name,__METHOD__,var_export($job,true));

					$backups_exist = false;

					//get the backup zips
					$zip_files = $job->getJobMetaValue('backup_zip_files');
					if(is_array($zip_files) && count($zip_files)>0) {
						//check for each file
						foreach ($zip_files as $zip_file_path=>$zip_file_size){
							//if any file exists then break out
							if ( file_exists($zip_file_path)) {
								$backups_exist=true;
								break;
							}
						}
					}

					//Delete the job control if backups dont exist
					if (false===$backups_exist) {
						WPBackItUp_Logger::log_info($log_name,__METHOD__,'No backups found for job:'. $job->getJobId());

						self::delete_job_records($job->getJobId());
						$jobs_purged+=1;

						WPBackItUp_Logger::log_info($log_name,__METHOD__,'Backups Missing - Deleted Job:');
						WPBackItUp_Logger::log_info($log_name,__METHOD__,var_export($job,true));
					}
				}
			}
		}

		/*------------------------------------------------------*/
		//Now purge the ones that exceed the retention limit
		/*------------------------------------------------------*/
		$jobs = self::get_jobs_by_status($job_type,array(WPBackItUp_Job::COMPLETE));
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Remaining Jobs After File Check:' .count($jobs));

		if (is_array($jobs) && count($jobs)>0) {

			//if ALL delete them all
			if ('ALL'==$dont_purge){
				$purge_jobs =  $jobs;
			}else{
				//Leave the last n and purge the remaining
				$purge_jobs = array_slice( $jobs, $dont_purge);
			}

			$purge_count=count($purge_jobs);
			WPBackItUp_Logger::log_info($log_name,__METHOD__,'Jobs to be purged:' .$purge_count);
			if ($purge_count>0){
				WPBackItUp_Logger::log_info($log_name,__METHOD__,var_export($jobs,true));
				foreach ($purge_jobs  as $key=>$job){

					self::delete_job_records($job->getJobId());
					$jobs_purged+=1;

					WPBackItUp_Logger::log_info($log_name,__METHOD__,'Deleted Job:');
					WPBackItUp_Logger::log_info($log_name,__METHOD__,var_export($job,true));
				}
			}

		}

		WPBackItUp_Logger::log_info($log_name,__METHOD__,'End - job purge complete.  Jobs Purged:' .$jobs_purged);

		return $jobs_purged;
	}


	/**
	 * Gets a job by id
	 *
	 * @param $job_id
	 *
	 * @return bool|WPBackItUp_Job
	 */
	public static function get_job_by_id($job_id) {
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin');

		$db = new WPBackItUp_DataAccess();
		$job = $db->get_job_by_id($job_id);

		if (null!=$job) {
			WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Job found:' . var_export($job,true));
			return new WPBackItUp_Job($job);
		}

		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'No job found with id.' . $job_id);
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'End');
		return false;
	}

	/**
	 * get jobs by type and status
	 *
	 * @param $job_type
	 * @param $job_status
	 *
	 * @param $limit Number of jobs in results
	 *
	 * @return mixed Array of jobs or false when no jobs found
	 */
	public static function get_jobs_by_status($job_type,$job_status, $limit=100) {
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin');

		$db = new WPBackItUp_DataAccess();
		$job_rows = $db->get_jobs_by_status($job_type,$job_status,$limit);
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Jobs found:' . count($job_rows));

		if (false===$job_rows) return false;

		$jobs_list = array();
		foreach ($job_rows as $key => $row) {
			$jobs_list[] =  new WPBackItUp_Job($row);
		}

		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'End');
		return count( $jobs_list ) > 0 ? $jobs_list : false;
	}

	/**
	 * Get jobs by type and job name and status
	 *
	 * @param $job_type
	 * @param $job_name
	 * @param $job_status
	 *
	 * @return array|bool Returns jobs or false when none found
	 */
	public static function get_jobs_by_job_name($job_type,$job_name, $job_status=null) {
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,sprintf('Begin:%s-%s',$job_type,$job_name));

		$db = new WPBackItUp_DataAccess();
		$db_jobs = $db->get_jobs_by_name($job_type,$job_name, $job_status);
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,sprintf('Job Ids:%s',var_export($db_jobs,true)));

		$job_list = array();
		foreach ($db_jobs as $key => $row) {
			$job_list[] =  new WPBackItUp_Job($row);
		}

		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'End');
		return count( $job_list ) > 0 ? $job_list : false;
	}


	/**
	 *  Queue a job
	 *
	 * @param $job_name
	 * @param $job_id
	 * @param $job_type
	 * @param $job_run_type
	 * @param $tasks
	 *
	 * @return bool|WPBackItUp_Job
	 */
	public static function queue_job($job_name, $job_id,$job_type,$job_run_type,$tasks){
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin -  Job:'. $job_type);

		$db = new WPBackItUp_DataAccess();
		if (! $db->create_job($job_id,$job_type,$job_run_type,$job_name,self::QUEUED)){
			WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Job was not created:' . $job_id );
			return false;
		}
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Job Created:' .$job_id);

		//add the tasks
		if ( false === self::create_tasks( $job_id,$tasks ) ) {
			WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Job tasks not Created - deleting job:' . $job_id );
			$db->update_job_status($job_id,WPBackItUp_Job::DELETED);
			return false;
		}

		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'End');
		return self::get_job_by_id($job_id);
	}

	/**
	 *  Import a completed job
	 *
	 * @param $job_name job name
	 * @param $job_id job id
	 * @param $job_type job type
	 *
	 * @return bool|WPBackItUp_Job false on failure/Job on success
	 */
	public static function import_completed_job($job_name, $job_id,$job_type){
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin -  Job:'. $job_type);

		$db= new WPBackItUp_DataAccess();
		if (false=== $db->create_job($job_id,WPBackItUp_Job::BACKUP,WPBackItUp_Job::IMPORTED,$job_name,WPBackItUp_Job::COMPLETE)){
			WPBackItUp_Logger::log_error(self::DEFAULT_LOG_NAME,__METHOD__,'Job NOT Created:' .$job_id);
			return false;
		}

		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Job Created successfully:' .$job_id);
		return self::get_job_by_id($job_id);
	}

	/**
	 * Create all the tasks for a job
	 *
	 * @param $job_id
	 *
	 * @param $tasks
	 *
	 * @return bool
	 */
	private static function create_tasks($job_id,  $tasks){
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin');

		$db = new WPBackItUp_DataAccess();

		//Create the job tasks
		foreach ($tasks as $key => $task_name){

			$task_created = $db->create_task($job_id,$task_name);
			if (false===$task_created){
				WPBackItUp_Logger::log_error(self::DEFAULT_LOG_NAME,__METHOD__,'Tasks NOT created');
				return false;
			}
			WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'task created:' . $task_created .':'. $task_name);
		}

		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'End');
		return true;

	}


	/**
	 * Get all job tasks for a job type
	 *
	 * @param $job_type
	 *
	 * @return array|bool  task array on success|False when no tasks found
	 */
	public static function get_job_tasks($job_type){

		switch ($job_type) {
			case self::RESTORE:
				return self::$RESTORE_TASKS;
				break;

			case self::BACKUP:
				return self::$BACKUP_TASKS;
                break;

			case self::CLEANUP:
				return self::$CLEANUP_TASKS;
                break;

			default:
				return false;
		}
	}


	/**
	 * Get last run datetime for job type.
	 *
	 * @param $job_type
	 *
	 * @return mixed  timestamp | 0 when none found
	 */
	public static function get_job_lastRunDate($job_type) {
		$job_setting = sprintf('%s_%s_lastrun_date',WPBACKITUP__NAMESPACE,$job_type);
		$last_runDate = get_option($job_setting,false);
		if (false===$last_runDate) return 0;
		return $last_runDate;
	}

	/**
	 * Set last run datetime for job type.
	 *
	 * @param      $job_type
	 *
	 * @param null $timestamp - null defaults to current datetime
	 *
	 * @return boolean true(success)|false(failure)
	 */
	public static function set_job_lastRunDate($job_type, $timestamp=null) {
		if (empty($timestamp)){
			$timestamp=current_time( 'timestamp' );
		}

		$job_setting = sprintf('%s_%s_lastrun_date',WPBACKITUP__NAMESPACE,$job_type);
		return  update_option($job_setting,$timestamp);
	}

	/*******************
	 * Getters & Setters
	 ******************/

	/**
	 * @return mixed
	 */
	public function getJobStartTime() {
		return $this->job_start_time;
	}

	/**
	 * @return mixed
	 */
	public function getJobStartTimeTimeStamp() {
		return strtotime($this->job_start_time);
	}


	/**
	 * @return mixed
	 */
	public function getJobEndTime() {
		return $this->job_end_time;
	}

	/**
	 * @return mixed
	 */
	public function getJobEndTimeTimeStamp() {
		return strtotime($this->job_end_time);
	}


	/**
	 * Get Job status
	 * @return mixed
	 */
	public function getJobStatus() {
		return $this->job_status;
	}

	/**
	 * Get job id
	 * @return mixed
	 */
	public function getJobId() {
		return $this->job_id;
	}

	/**
	 * @return int
	 */
	public function getInstanceId() {
		return $this->instance_id;
	}

	/**
	 * Get job info
	 *
	 * @param null $key
	 *
	 * @param null $default
	 *
	 * @return array returns array if key not passed and value if key passed
	 * If key doesnt exists then null will be returned
	 */
	public function getJobMetaValue($key=null,$default=null) {
		$job_meta = $this->job_meta;

		if (null!=$key && is_array($job_meta)){
			if (array_key_exists($key,$job_meta) ){
				return $job_meta[$key];
			}else{
				return $default;
			}
		}else{
			return $default;
		}
	}

	/**
	 * Get all the entire meta property
	 *
	 * @return mixed Returns the meta array
	 *
	 */
	public function getJobMeta() {
		return $this->job_meta;
	}

	/**
	 * Get duration formatted in minutes/seconds
	 *
	 * @return mixed returns false when no end date set
	 */
	public function getJobDurationFormatted() {

		if (null==$this->job_start_time || null==$this->job_end_time)
			return false;

		//calculate duration
		$util = new WPBackItUp_Utility();
		$total_seconds = $util->timestamp_diff_seconds($this->getJobStartTimeTimeStamp(),$this->getJobEndTimeTimeStamp());

		$formatted_duration='';
		$processing_hours=0;
		$processing_minutes = round($total_seconds / 60);
		$processing_seconds = $total_seconds % 60;

		if ($processing_minutes >= 60)
		{
			$processing_hours = (int)($processing_minutes / 60);
			$processing_minutes = $processing_minutes % 60;
		}

		if($processing_hours > 0) {
			$formatted_duration .= "{$processing_hours}h";
		}

		if($processing_minutes > 0) {
			$formatted_duration .= " {$processing_minutes}m";
		}

		if($processing_seconds > 0) {
			$formatted_duration .= " {$processing_seconds}s";
		}

		return  $formatted_duration;
	}

	/**
	 * Job Date
	 *
	 * @return mixed
	 */
	public function getJobDate() {

		//Job id is a timestamp
		return  date('Y-m-d H:i:s',$this->job_id);

	}

	/**
	 * Job Name
	 * @return mixed
	 */
	public function getJobName() {
		return $this->job_name;
	}

	/**
	 * JOb Run Type
	 * @return mixed
	 */
	public function getJobRunType() {
		return $this->job_run_type;
	}

	/**
	 * @return mixed
	 */
	public function getJobType() {
		return $this->job_type;
	}

}

