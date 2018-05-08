<?php if (!defined ('ABSPATH')) die('No direct access allowed');


class WPBackItUp_Job_Task {

	const DEFAULT_LOG_NAME='debug_job_task';

	//Task Status values
	const ERROR = 'error';
	const ACTIVE ='active';
	const COMPLETE ='complete';
	const CANCELLED='cancelled';
	const QUEUED = 'queued';

	private $log_name;

	private $job_id;
	private $task_name;
	private $task_meta;
	private $task_start=null;
	private $task_end=null;

	private $task_id;
	private $status;
	private $error;

	private $allocated_id;
	private $created_date;
	private $last_updated;
	private $retry_count;

	public function __construct($db_task) {

		try {
			$this->log_name = self::DEFAULT_LOG_NAME;

			$this->set_properties($db_task);

		} catch(Exception $e) {
			error_log($e);
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Constructor Exception: ' .$e);
		}
	}

	function __destruct() {

	}

	/**
	 * Set properties from db task
	 *
	 * @param $db_task database task
	 *
	 * @throws exception
	 */
	private function set_properties($db_task){

		if ( ! is_object($db_task)) {
			throw new exception( 'Cant create task object, missing db entity');
		}

		$this->task_id      = $db_task->task_id;
		$this->job_id       = $db_task->job_id;
		$this->task_name    = $db_task->task_name;

		//initialize task meta as array php 7.1
		$this->task_meta=array();
		if (null!=$db_task->task_meta) $this->task_meta    = maybe_unserialize($db_task->task_meta);

		//leave set to null default
		if (! empty($db_task->task_start) && '0000-00-00 00:00:00'!= $db_task->task_start) {
			$this->task_start = $db_task->task_start;
		}

		//leave set to null default
		if (! empty($db_task->task_end) && '0000-00-00 00:00:00'!= $db_task->task_end) {
			$this->task_end = $db_task->task_end;
		}

		$this->allocated_id = $db_task->allocation_id;
		$this->last_updated = $db_task->update_date;
		$this->status       = $db_task->task_status;
		$this->retry_count  = $db_task->retry_count;
		$this->error        = $db_task->error;

	}

	/**
	 * Get task by task ID
	 *
	 * @param $task_id
	 *
	 * @return bool|WPBackItUp_Job_Task
	 */
	public static function get_task_by_id($task_id){
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin');

		$db = new WPBackItUp_DataAccess();
		$task = $db->get_task_by_id($task_id);
		if (false===$task){
			WPBackItUp_Logger::log_error(self::DEFAULT_LOG_NAME,__METHOD__,'Task not found:'. $task_id);
			return false;
		}

		return  new WPBackItUp_Job_Task($task);

	}


	/**
	 * Get task list for job
	 *
	 * @param $job_id Job Id
	 * @param $status_list array of statuses to filter on
	 *
	 * @return bool| WPBackItUp_Job_Task[]
	 */
	public static function get_job_tasks($job_id,$status_list){
		WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Begin');

		//get all the queued and active
		$db = new WPBackItUp_DataAccess();
		$task_rows = $db->get_job_tasks($job_id,$status_list);
		if (false===$task_rows) return false;

		$task_list = array();
		foreach ($task_rows as $key => $row) {
			$task_list[] =  new WPBackItUp_Job_Task($row);
		}

		return $task_list;
	}

	/**
	 * Save the task info to the database
	 *
	 * @return mixed
	 * Returns Returns true on success and false on failure.
	 * NOTE: If the meta_value(Task Info) passed to this function is the same as the value that is already in the database, this function returns false.
	 *
	 */
	private function save(){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		$db = new WPBackItUp_DataAccess();
		$task_updated =  $db->update_task($this);

		if (! $task_updated){
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Task was not updated:' .var_export($this,true));
		}

		$this->set_properties($db->get_task_by_id($this->task_id));

		return $task_updated;
	}



	/**
	 *  Attempt to allocate the task to this job - will set task status to active
	 *
	 * @return bool
	 */
	public function try_allocate_task(){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		$db = new WPBackItUp_DataAccess();
		$allocated_task = $db->allocate_task($this->task_id);
		if (true===$allocated_task){
			$this->setStatus( WPBackItUp_Job_Task::ACTIVE);
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End - Task allocated');
			return true;
		}else{
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End - Task was not allocated');
			return false;
		}

	}

	/********************
	 * SETTERS
	 ********************/

	/**
	 * Increment the task retry count
	 */
	public function increment_retry_count(){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');
		$this->retry_count++;

		return $this->save();
	}

	/**
	 * Set task status
	 *
	 * @param $status  Task Status
	 *
	 * @param null $error Error Code
	 *
	 * @return mixed
	 */
	public function setStatus($status,$error=null){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');
		$this->status=$status;

		$now = current_time('mysql');

		//Update task start and end based on status
		switch ( $status ) {
			case self::ACTIVE:
				$this->task_start=$now;
				break;

			case self::COMPLETE:
				$this->task_end=$now;
				break;

			case self::CANCELLED:
				$this->task_end=$now;
				break;

			case self::ERROR:
				$this->task_end=$now;
				$this->error=$error;

				//Set the job status in error if any task ends in error.
				$job = WPBackItUp_Job::get_job_by_id($this->getJobId());
				$job->setStatus(WPBackItUp_Job::ERROR);
				break;

			case self::QUEUED:
				//do nothing
				break;
		}
		return $this->save();
	}

	/**
	 * Set Task Meta value
	 * @param $meta_name
	 * @param $meta_value
	 *
	 * @return mixed
	 */
	public function setTaskMetaValue($meta_name,$meta_value){

		$this->task_meta[$meta_name]=$meta_value;
        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Task Meta: '. var_export($this->task_meta, true));

		return $this->save();
	}

	/********************
	 * GETTERS
	 ********************/

	/**
	 * @return mixed
	 */
	public function getJobId() {
		return $this->job_id;
	}

	/**
	 * @return mixed
	 */
	public function getTaskId() {
		return $this->task_id;
	}

	/**
	 * @return mixed
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @return mixed
	 */
	public function getAllocatedId() {
		return $this->allocated_id;
	}

	/**
	 * @return mixed
	 */
	public function getLastUpdated() {
		return $this->last_updated;
	}

	/**
	 * @return mixed
	 */
	public function getLastUpdatedTimeStamp() {
		return strtotime($this->last_updated);
	}

	/**
	 * @return int
	 */
	public function getRetryCount() {
		return $this->retry_count;
	}

	/**
	 * @return mixed
	 */
	public function getTaskName() {
		return $this->task_name;
	}

	/**
	 * @return null
	 */
	public function getCreatedDate() {
		return $this->created_date;
	}

	/**
	 * @return mixed
	 */
	public function getTaskStart() {
		return $this->task_start;
	}

	/**
	 * @return mixed
	 */
	public function getTaskEnd() {
		return $this->task_end;
	}

	/**
	 * @return mixed
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * Get Task Meta info
	 *
	 * @param null $key
	 *
	 * @param null $default
	 *
	 * @return array returns array if key not passed and value if key passed
	 * If key doesnt exists then null will be returned
	 */
	public function getTaskMetaValue($key=null,$default=null) {
		$task_meta = $this->task_meta;

		if (null!=$key && is_array($task_meta)){
			if (array_key_exists($key,$task_meta) ){
				return $task_meta[$key];
			}else{
				return $default;
			}
		}else{
			return $default;
		}
	}

	/**
	 * @return mixed
	 */
	public function getTaskMeta() {
		return $this->task_meta;
	}
}