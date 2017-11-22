<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * WP BackItUp  - Job Item Class
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

class WPBackItUp_Job_Item {

	const DEFAULT_LOG_NAME='debug_job_item';

	// ** ITEM CONSTANTS **

	const JOB_ITEM_RECORD ="I";

	//Group values
	const DATABASE  = 'database';
	const PLUGINS   = 'plugins';
	const THEMES    = 'themes';
	const UPLOADS   = 'uploads';
	const OTHERS    = 'others';
	const BACKUPS   = 'backups';

	//Status values
	const OPEN      = '';
	const QUEUED    = 'queued';
	const COMPLETE  = 'complete';
	const ERROR     = 'error';
	const CANCELLED  = 'cancelled';


	// ** END ITEM CONSTANTS **

	private $item_id;
	private $job_id;
	private $batch_id;
	private $group_id;
	private $item;
	private $size_kb;
	private $retry_count;
	private $offset;
	private $create_date;
	private $last_updated;
	private $record_type;
	private $item_status;


	private function __construct($db_item) {
		try {
			$this->log_name = self::DEFAULT_LOG_NAME;//default log name

			$this->set_properties($db_item);

		} catch(Exception $e) {
			error_log($e); //Log to debug
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Constructor Exception: ' .$e);
		}
	}

	function __destruct() {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End');
	}

	/**
	 * Set properties from db item
	 *
	 * @param $db_item database item
	 *
	 * @throws exception
	 */
	private function set_properties($db_item){

		if ( ! is_object($db_item)) {
			throw new exception( 'Cant create item object, missing db entity');
		}

		$this->item_id      = $db_item->item_id;
		$this->job_id       = $db_item->job_id;
		$this->batch_id     = $db_item->batch_id;
		$this->group_id     = $db_item->group_id;

		$this->item         = $db_item->item;
		$this->size_kb      = $db_item->size_kb;
		$this->retry_count  = $db_item->retry_count;
		$this->offset       = $db_item->offset;

		$this->create_date  = $db_item->create_date;
		$this->last_updated = $db_item->update_date;

		$this->record_type  = $db_item->record_type;
		$this->item_status  = $db_item->item_status;

	}

	/**
	 * Create Job Item
	 *
	 * @param $job_id
	 * @param $group_id
	 * @param $batch_id
	 * @param $item
	 * @param $size_kb
	 *
	 * @return bool|WPBackItUp_Job_Item
	 */
	public static function create_item($job_id, $group_id, $batch_id,$item, $size_kb) {
		WPBackItUp_Logger::log_info( self::DEFAULT_LOG_NAME, __METHOD__, 'Begin' );

		try {

			$db        = new WPBackItUp_DataAccess();
			$db_item = $db->insert_job_item($job_id, $group_id, $batch_id, $item, $size_kb);
			if ( false === $db_item ) return false;


			return new WPBackItUp_Job_Item( $db_item );

		} catch(Exception $e) {
			error_log($e); //Log to debug
			WPBackItUp_Logger::log_error(self::DEFAULT_LOG_NAME,__METHOD__,'Constructor Exception: ' .$e);
			return false;
		}
	}

	/**
	 * Get item by id
	 *
	 */
	public static function get_item_by_id($item_id) {
		WPBackItUp_Logger::log_info( self::DEFAULT_LOG_NAME, __METHOD__, 'Begin' );
		try {
			$db        = new WPBackItUp_DataAccess();
			$db_item = $db->get_item_by_id( $item_id);
			WPBackItUp_Logger::log_info(self::DEFAULT_LOG_NAME,__METHOD__,'Item: ' .var_export($db_item,true));
			if ( empty($db_item)) return false;

			return new WPBackItUp_Job_Item( $db_item );

		} catch(Exception $e) {
			error_log($e); //Log to debug
			WPBackItUp_Logger::log_error(self::DEFAULT_LOG_NAME,__METHOD__,'Constructor Exception: ' .$e);
			return false;
		}
	}

	/**
	 * Fetch all a batch of open items by group
	 *
	 * @param $job_id
	 * @param $batch_size
	 * @param $group_id
	 *
	 * @return bool| WPBackItUp_Job_Item[]
	 */
	public static function get_item_batch_by_group($job_id,$batch_size,$group_id) {
		WPBackItUp_Logger::log_info( self::DEFAULT_LOG_NAME, __METHOD__, 'Begin' );

		$batch_id=current_time( 'timestamp' );

		$db        = new WPBackItUp_DataAccess();
		$item_rows = $db->get_batch_open_items( $batch_id,$batch_size, $job_id, $group_id );
		if ( false === $item_rows ) {
			return false;
		}

		$item_list = array();
		foreach ( $item_rows as $key => $row ) {
			$item_list[] = new WPBackItUp_Job_Item( $row );
		}

		return $item_list;
	}


	/**
	 *  Get open items(OPEN,QUEUED) for group list
	 *
	 * @param array|string $groups
	 *
	 * @return array|bool
	 */
	public static function get_open_items_by_group($groups,$batch_size=25) {
		WPBackItUp_Logger::log_info( self::DEFAULT_LOG_NAME, __METHOD__, 'Begin' );

		$db        = new WPBackItUp_DataAccess();
		$item_rows = $db->get_open_items_by_group( $groups,$batch_size);
		if ( false === $item_rows ) {
			return false;
		}

		$item_list = array();
		foreach ( $item_rows as $key => $row ) {
			$item_list[] = new WPBackItUp_Job_Item( $row );
		}

		return $item_list;
	}


	/**
	 * Get Job items by status
	 *
	 * @param $job_id
	 * @param $status_list[]
	 *
	 * @return bool| WPBackItUp_Job_Item[]
	 */
	public static function get_job_items($job_id,$status_list) {
		WPBackItUp_Logger::log_info( self::DEFAULT_LOG_NAME, __METHOD__, 'Begin' );

		$db        = new WPBackItUp_DataAccess();
		$item_rows = $db->get_job_items( $job_id,$status_list);
		if ( false === $item_rows ) {
			return false;
		}

		$item_list = array();
		foreach ( $item_rows as $key => $row ) {
			$item_list[] = new WPBackItUp_Job_Item( $row );
		}

		return $item_list;
	}

	/**
	 *  Get open items(OPEN,QUEUED) for group list
	 *
	 * @param array|string $groups
	 *
	 * @return array|bool
	 */
	public static function get_open_items_by_group_last_day($groups) {
		WPBackItUp_Logger::log_info( self::DEFAULT_LOG_NAME, __METHOD__, 'Begin' );

		$db        = new WPBackItUp_DataAccess();
		$item_rows = $db->get_open_items_by_group_last_day( $groups);
		if ( false === $item_rows ) {
			return false;
		}

		$item_list = array();
		foreach ( $item_rows as $key => $row ) {
			$item_list[] = new WPBackItUp_Job_Item( $row );
		}

		return $item_list;
	}


	/**
	 * Get the number of open items remaining for a group
	 *
	 * @param $job_id
	 * @param $group_id
	 *
	 * @return mixed
	 */
	public static function get_open_item_count($job_id,$group_id) {
		WPBackItUp_Logger::log_info( self::DEFAULT_LOG_NAME, __METHOD__, 'Begin' );

		$db        = new WPBackItUp_DataAccess();
		$remaining_count = $db->get_open_item_count($job_id,$group_id);

		return $remaining_count;
	}

	/**
	 * Get a count of all items by status
	 *
	 * @param  int   $job_id
	 *
	 * @param  array $item_status_list
	 *
	 * @return mixed
	 */
	public static function get_item_status_count($job_id,$item_status_list=null) {
		WPBackItUp_Logger::log_info( self::DEFAULT_LOG_NAME, __METHOD__, 'Begin' );

		//IF not null and not array, stick in array
		if ( ! WPBackItUp_Utility::is_null_or_empty($item_status_list) && ! is_array($item_status_list)){
			$item_status_list = array( $item_status_list);
		}

		$db        = new WPBackItUp_DataAccess();
		$count = $db->get_item_status_count($job_id,$item_status_list);

		return (int) $count;
	}


	/**
	 * Set item status
	 *
	 * @param $item_status  Item Status
	 *
	 * @return mixed
	 */
	public function setStatus($item_status){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		$db        = new WPBackItUp_DataAccess();
		$updated = $db->update_item_status($this->item_id,$item_status);
		if (true===$updated){
			$this->item_status=$item_status;
		}

		return $updated;
	}

	/**
	 * Set item offset
	 *
	 * @param $offset
	 *
	 * @return mixed
	 */
	public function setOffset($offset){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		$db        = new WPBackItUp_DataAccess();
		$updated = $db->update_item_offset($this->item_id,$offset);
		if (true===$updated){
			$this->offset=$offset;
		}

		return $updated;
	}

	/**
	 * Get Item
	 * @return mixed
	 */
	public function getItem() {
		return $this->item;
	}

	/**
	 * Get Job Id
	 * @return mixed
	 */
	public function getJobId() {
		return $this->job_id;
	}

	/**
	 * Get Item Id
	 * @return mixed
	 */
	public function getItemId() {
		return $this->item_id;
	}

	/**
	 * Get Item Status
	 * @return mixed
	 */
	public function getItemStatus() {
		return $this->item_status;
	}

	/**
	 * Get Group Id
	 * @return string
	 */
	public function getGroupId() {
		return $this->group_id;
	}

	/**
	 * Get Size in KB
	 * @return string
	 */
	public function getSizeKB() {
		return $this->size_kb;
	}

	/**
	 * Get Offset
	 * @return string
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * Get Create Date
	 * @return timestamp
	 */
	public function getCreateDate() {

		return strtotime($this->create_date);
	}


	/**
	 * Percentage Complete
	 * @return string
	 */
	public function getPercentComplete() {
		$size = (int) $this->size_kb;
		$offset= (int)$this->offset;
		$percent = round(($offset/$size * 100 ),2);


		return$percent;
	}
}