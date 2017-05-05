<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * WP BackItUp -  Backup Class
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

/*** Includes ***/
// include file system class
if( !class_exists( 'WPBackItUp_Filesystem' ) ) {
    include_once 'class-filesystem.php';
}

if( !class_exists( 'WPBackItUp_RecursiveFilterIterator' ) ) {
	include_once 'class-recursivefilteriterator.php';
}



class WPBackItUp_Backup {

	private $log_name;

	//Public Properties
	public  $backup_name;
	//public  $backup_filename;
	public  $backup_project_path;
	public  $backup_folder_root;
	public  $restore_folder_root;
	public  $backup_retained_number;
    public  $backup_retained_days;

    //scheduled,manual,none
    public  $backup_type;

    //private static $lockFileName;
   // private static $lockFile;


	//-------------STATIC FUNCTIONS-------------------//



	//-------------END STATIC FUNCTIONS-------------------//

	function __construct($log_name,$backup_name, $backup_type) {
		global $WPBackitup;
		try {
			$this->log_name = 'debug_backup';//default log name
			if (is_object($log_name)){
				//This is for the old logger
				$this->log_name = $log_name->getLogFileName();
			} else{
				if (is_string($log_name) && isset($log_name)){
					$this->log_name = $log_name;
				}
			}

            $this->backup_type=$backup_type;

			$this->backup_name=$backup_name;
			//$this->backup_filename=$backup_name . '.tmp';

			$backup_project_path = WPBACKITUP__BACKUP_PATH .'/TMP_'. $backup_name .'/';

			$backup_folder_root =WPBACKITUP__BACKUP_PATH  .'/';
			$restore_folder_root = WPBACKITUP__RESTORE_FOLDER;

			$this->backup_project_path=$backup_project_path;
			$this->backup_folder_root=$backup_folder_root;
			$this->restore_folder_root=$restore_folder_root;

			$this->backup_retained_number = $WPBackitup->backup_retained_number();
            $this->backup_retained_days = WPBACKITUP__BACKUP_RETAINED_DAYS; //Prob need to move this to main propery

		} catch(Exception $e) {
			error_log($e);
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Constructor Exception: ' .$e);
            throw $e;
		}
   }

   function __destruct() {
       //Call end just in case
       $this->end();
   }


    /**
     * Begin backup process - Only one may be running at a time
     * @return bool
     */
    public static function start (){
	    //$lockfile_logname='debug_lock';

         try {
            //locking handled in job engine/mutex now
			return true;


        } catch(Exception $e) {
	        // WPBackItUp_Logger::log_error($lockfile_logname,__METHOD__,'Process Lock error: ' .$e);
            return false;
      }


    }

    /**
     * End Backup Process
     * @return bool
     */
    public static function end (){
        //WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin - Unlock File:' . $this->lockFileName);

        try{
	        //locking handled in mutex now
            return true;

        }catch(Exception $e) {
            //WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Cant unlock file: ' .$e);
            return false;
        }
    }

	/**
	 * Delete backup folders by prefix
	 * @param $prefix
	 */
	public function cleanup_backups_by_prefix($prefix) {
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin' );
        $backup_root_path=$this->backup_folder_root;

        //get a list of all the temps
        $work_folder_list = glob($backup_root_path. $prefix .'*', GLOB_ONLYDIR);
        $file_system = new WPBackItUp_FileSystem($this->log_name);
        foreach($work_folder_list as $folder) {
            $file_system->recursive_delete($folder);
        }

	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End' );
    }

	/**
	 * Purge backups that don't have job control record
	 *  - job control records are purged first so orphaned folders should be deleted.
	 *
	 */
	public function purge_orphaned_backups() {
		$log_name = 'debug_purge_folders';
	    WPBackItUp_Logger::log_info($log_name,__METHOD__,'Begin' );

        //  --PURGE BACKUP FOLDER
		$folder_list = glob($this->backup_folder_root . '*', GLOB_ONLYDIR);
		foreach ($folder_list as $key => $folder)
		{

			//Check for job control record
			$folder_name = basename($folder);
			$folder_name_parts = explode('_',$folder_name);
			$job_id = end($folder_name_parts);
			$job = WPBackItUp_Job::get_jobs_by_job_name(WPBackItUp_Job::BACKUP,$folder_name);
			//$job = WPBackItUp_Job::get_job_by_id($job_id);

			//If no jobs found then purge
			if(false===$job){
				if (file_exists($folder)) {
					$file_system = new WPBackItUp_FileSystem($log_name);
					if (true===$file_system->recursive_delete($folder)){
						WPBackItUp_Logger::log_info($log_name,__METHOD__,'Folder Deleted:'.$folder);
					} else{
						WPBackItUp_Logger::log_error($log_name,__METHOD__,'Folder NOT Deleted:'.$folder);
					}
				}
			}
		}

		WPBackItUp_Logger::log_info($log_name,__METHOD__,'End');
		return true;
    }

	/**
	 * Cleanup current backup files(async)
	 *
	 * @param $file_extension_list Pipe delimited extension list ex 'txt|sql|db|config|safe'
	 *
	 *
	 * @return bool
	 */
	public function cleanup_current_backup_async($file_extension_list){
		$path= $this->getBackupProjectPath();
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin - Cleanup Backup Folder:' . $path);

		try {

	        $fileSystem = new WPBackItUp_FileSystem($this->log_name);
			$file_list = $fileSystem->get_fileonly_list($path, $file_extension_list);
	
			//If any files were found
			if (count( $file_list )>1){
	
				array_unshift($file_list, 'cleanup-files');//add task identifier to top of array
				$cleanup_tasks = array($file_list);//create array of tasks
				//
				//run background processor
				$cleanup_processor  = new WPBackItUp_Cleanup_Processor();
				//
				//realize there is only one task but wanted to put in for each for future
				foreach ( $cleanup_tasks as $cleanup_task ) {
					$cleanup_processor->push_to_queue( $cleanup_task );
				}
	
				$cleanup_processor->save()->dispatch();
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Cleanup job dispatched.');
	
			}
	
		    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End - Work Files Deleted');
	        return true;

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Error Occurred: ' .$e);
			return false;
		}
    }

	/**
	 * Purge old backup files
	 */
	public function purge_old_files(){
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');
        $fileSystem = new WPBackItUp_FileSystem( $this->log_name);

        //Check the retention
        $fileSystem->purge_FilesByDate($this->backup_retained_number,$this->backup_folder_root);

	    //      --PURGE BACKUP FOLDER
        //Purge logs in backup older than N days
	    $backup_path = WPBACKITUP__BACKUP_PATH .'/';
        $fileSystem->purge_files($backup_path,'*.log',$this->backup_retained_days);

	    //Purge restore DB checkpoints older than 5 days
	    $fileSystem->purge_files($backup_path,'db*.cur',$this->backup_retained_days);

	    //      --PURGE LOGS FOLDER
	    $logs_path = WPBACKITUP__PLUGIN_PATH .'/logs/';

	    //Purge logs in logs older than 5 days
	    $fileSystem->purge_files($logs_path,'*.log',$this->backup_retained_days);

        //Purge Zipped logs in logs older than 5 days
	    $fileSystem->purge_files($logs_path,'*.zip',$this->backup_retained_days);

	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End');

    }

	/**
	 * Make sure the root backup folder wpbackitup_backups exists
	 *
	 * @return bool
	 */
	public function backup_root_folder_exists(){
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin: ' .$this->backup_folder_root);
        $fileSystem = new WPBackItUp_FileSystem($this->log_name);
        if(!$fileSystem->create_dir($this->backup_folder_root)) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Cant create backup folder :'. $this->backup_folder_root);
            return false;
        }

	    $fileSystem->secure_folder($this->backup_folder_root);

	    //Make sure logs folder is secured
	    $logs_dir = WPBACKITUP__PLUGIN_PATH .'/logs/';
	    $fileSystem->secure_folder( $logs_dir);


	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End');
        return true;
    }

	/**
	 * Create the root folder for the current backup
	 *
	 * @return bool
	 */
	public function create_current_backup_folder(){
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin: ' .$this->backup_project_path);
        $fileSystem = new WPBackItUp_FileSystem($this->log_name);
        if(!$fileSystem->create_dir($this->backup_project_path)) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Cant create backup folder :'. $this->backup_project_path);
            return false;
        }

	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End');
        return true;
    }

	/**
	 * Check to see if the directory exists and is writeable
	 *
	 * @return bool
	 */
	public function backup_folder_exists(){
        $path=$this->backup_project_path;
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Is folder writeable: ' .$path);
        if(is_writeable($path)) {
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Folder IS writeable');
            return true;
        }

	    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Folder NOT writeable');
        return false;
    }


	/**
	 * Export the database using MYSQLDUMP Export
	 *  - This method will generate the SQL export using WP BackItIp export process
	 *
	 * @param $current_job
	 * @param $content_type
	 *
	 * @param $batch_size
	 * @param bool $with_mysqlpath
	 *
	 * @return bool|mixed
	 */
	public function export_database_mysqldump($current_job,$content_type, $batch_size, $with_mysqlpath=false){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin MYSQLDUMP Database Export.');

		$job_id = $current_job->getJobId();
		$sqlUtil = new WPBackItUp_SQL($this->log_name);

		$batch_id=current_time( 'timestamp' );
		$db = new WPBackItUp_DataAccess();

		//Get one item at a time because batch is split on inventory
		$item_batch = $db->get_batch_open_items($batch_id,1,$job_id,$content_type);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,var_export($item_batch,true));

		//It is possible that there are no file to backup so return count or false
		if($item_batch == false || $item_batch==0) return $item_batch;

		//Get table name
		$table = $item_batch[0]->item;
		$offset = $item_batch[0]->offset;
		$create_table=false;
		if (0==$offset) {
			$create_table=true;
		}

		//Generate the sql name - break up by offset so restore will also work in batches
		$sql_data_file_name=$this->backup_project_path. sprintf('db-%s-%d.sql',$table,$offset);
		if (false===$sqlUtil->mysqldump_export_data($sql_data_file_name,$table,$offset,$batch_size,$create_table,$with_mysqlpath)){
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'SQL EXPORT FAILED');
			return false;
		}
		$db->update_item_batch_complete($job_id,$batch_id,1);

		//get count of remaining items
		$remaining_count = $db->get_open_item_count($job_id,$content_type);

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End MYSQLDUMP Database Export');
		return $remaining_count; //return count;
	}

	/**
	 * Export the database using WP BackItUp DB Export
	 *  - This method will generate the SQL export using WP BackItIp export process
	 *
	 * @param $current_job
	 * @param $content_type
	 *
	 * @param $batch_size
	 *
	 * @return bool|mixed
	 */
	public function export_database_wpbackitup($current_job,$content_type,$batch_size){
		global $wpdb;
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin WPBackItUp Database Export.');

	    $job_id = $current_job->getJobId();
        $sqlUtil = new WPBackItUp_SQL($this->log_name);

	    $batch_id=current_time( 'timestamp' );
	    $db = new WPBackItUp_DataAccess();

	    //Get one item at a time because batch is split on inventory
	    $item_batch = $db->get_batch_open_items($batch_id,1,$job_id,$content_type);
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,var_export($item_batch,true));

	    //It is possible that there are no file to backup so return count or false
	    if($item_batch == false || $item_batch==0) return $item_batch;

	    //Get table name
	    $table = $item_batch[0]->item;
	    $offset = $item_batch[0]->offset;

		$create_table=false;
		if (0==$offset) {
			$create_table=true;
		}

	    //Generate the sql name - break up by offset so restore will also work in batches
	    $sql_data_file_name=$this->backup_project_path. sprintf('db-%s-%d.sql',$table,$offset);
	    if (false===$sqlUtil->wpbackitup_export_data($job_id,$sql_data_file_name,$table,$offset,$batch_size,$create_table)){
            WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'SQL EXPORT FAILED');
            return false;
        }

		//It is possible we may not make it here because of timeout. This is why we try export 3 times before error
	    $db->update_item_batch_complete($job_id,$batch_id,1);

	    //get count of remaining items
	    $remaining_count = $db->get_open_item_count($job_id,$content_type);

	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End WPBackItUp Database Export');
   	    return $remaining_count; //return count;
    }

    /**
     * Get sql file list and merge into a single file
     *
     * @param $task
     * @param $batch_size
     * @param $search_path
     * @param $toFile_path
     * @param $file_type
     *
     * @return bool|mixed
     */
    public function merge_sql_files_to_path($task, $batch_size, $search_path, $toFile_path, $file_type){

        $sql_files = $task->getTaskMetaValue('sql_files_to_merge', array());
        $file_system = new WPBackItUp_FileSystem();
        $counter = 0;

        if(empty($sql_files)){
            $sql_files   = $file_system->get_fileonly_list($search_path, $file_type);
            // delete DB file if exist for first time.
            @unlink($toFile_path);
        }

        foreach ( $sql_files as $i => $file ) {
            if($counter===$batch_size){
                $task->setTaskMetaValue('sql_files_to_merge',$sql_files);
                return count($sql_files);
            }
            if ( false === $file_system->append_file_chunked($file,$toFile_path)){
                @unlink($toFile_path); //delete DB file
                WPBackItUp_Logger::log_warning($this->log_name,__METHOD__, 'Could not merge SQL file:' . $file);

                return false;
            };

            WPBackItUp_Logger::log_info($this->log_name,__METHOD__, 'File Appended:' . $file);

            $counter++;
            // removing already appended file from the array.
            unset($sql_files[$i]);
        }

        $task->setTaskMetaValue('sql_files_to_merge',$sql_files);
        return count($sql_files);

    }


	/**
	 * Create siteinfo in project dir
	 *
	 * @return bool
	 */
	public function create_siteinfo_file($job_id){
        global $table_prefix; //from wp-config
        $path=$this->backup_project_path;
		$siteinfo_file = $path ."backupsiteinfo.config";

	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Create Site Info File:'.$siteinfo_file);
        try {

	        $outbut_buffer=array(
		        "site_url"          => site_url( '/' ),
		        "table_prefix"      => $table_prefix,
		        "snapshot_prefix"   => $job_id.'_',
		        "wp_version"        => get_bloginfo( 'version'),
		        "wpbackitup_version"    => WPBACKITUP__VERSION,
	        );

	        file_put_contents($siteinfo_file,json_encode($outbut_buffer));
            if (! file_exists($siteinfo_file)){
	            WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Site Info file was not created successfully.');
               return false;
            }

	        return true;

        }catch(Exception $e) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,' Exception: ' .$e);
	        return false;
        }
    }

	/**
	 * Save inventory of database
	 *
	 * @param $job_id
	 * @param $group_id
	 * @param $batch_size
	 * @param null $exclude
	 *
	 * @return bool
	 */
	public function save_database_inventory($job_id,$group_id,$batch_size,$exclude=null) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin:' .$group_id);

		//create a separate log file for inventory
		$inventory_logname = sprintf('debug_inventory_%s_%s',$group_id,$job_id);
		WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, '**BEGIN**');
		WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, 'Exclude: ' .var_export($exclude,true));
		WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, '***');
		try {

			//get the list of tables and row sizes
			$db = new WPBackItUp_DataAccess();
			$db_size_array = $db->get_table_rows();

			$datetime1 = new DateTime('now');
			$sql="";

			$total_counter=0;
			foreach ($db_size_array  as $table) {
				$table_name = $table['table_name'];
				$table_rows=$table['table_rows'];
				$table_size_kb=$table['table_size_kb'];

				//if empty default to 0
				if (empty($table_size_kb)){
					$table_size_kb=0;
				}

				//filter tables on exclude
				if (!empty($exclude) && in_array($table_name, $exclude)) {
					WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, sprintf('EXCLUDE table:%s %s %s kb ',$table_name,$table_rows,$table_size_kb));
					continue;
				}

				//BATCH records that should be written
				$batch_items = ceil($table_rows/$batch_size);
				if ($batch_items<=0) $batch_items = 1;//always write at least 1 batch record
				WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, sprintf('ROW_COUNT/BATCH_SIZE=BATCH RECORDS:%s %d/%d=%d ',$table_name,$table_rows,$batch_size,$batch_items));

				//generate the sql for all the batches
				$offset=0;
				for ($i=1; $i <= $batch_items; $i++){
					$sql.= "(".$job_id .", '" .$group_id."', '" .$table_name ."', " .$table_size_kb .","  .$offset .",'" .current_time('mysql') ."'),";
					WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, sprintf('Add table:%s Offset:%s',$table_name,$offset));
					$offset+=$batch_size;
					$total_counter++;
				}
			}

			//write all the batch records at one time
			if ($total_counter>0) {
				WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, '*Try Write Batch*');
				if (! $db->insert_job_items_with_offset($sql)) {
					return false;
				}
				WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, '*Write Batch SUCCESS*');
			}

			$datetime2 = new DateTime('now');

			WPBackItUp_Logger::log_info($inventory_logname, __METHOD__, '**END**');

			if(method_exists($datetime2, 'diff')) {
				$interval = $datetime1->diff($datetime2);
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Count/Time: ' .$total_counter . '-' . $interval->format('%s seconds'));
			} else {
				$util = new WPBackItUp_Utility($this->log_name);
				$interval = $util->date_diff_array($datetime1, $datetime2);
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Count/Time: ' .$total_counter . '-' . $interval['second'] . ' seconds');
			}


			return true;

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
			return false;
		}
	}

	/**
	 * Save inventory of folder to database
	 *
	 * @param $batch_insert_size
	 * @param $job_id
	 * @param $group_id
	 * @param $root_path
	 * @param null $exclude
	 *
	 * @return bool
	 */
	public function save_folder_inventory($batch_insert_size,$job_id,$group_id,$root_path,$exclude=null) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin:' .$group_id);

		//create a separate log file for inventory
		$inventory_logname = sprintf('debug_inventory_%s_%s',$group_id,$job_id);
		WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, '**BEGIN**');
		WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, 'Root Path: ' .$root_path);
		WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, 'Exclude: ' .var_export($exclude,true));
		WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, '***');
		try {
			$batch_counter = 0;
			$total_counter=0;

			//IF the path is not valid then cant create inventory
			if (! is_dir($root_path)){
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Folder does not exist: ' .$root_path);
				return false;
			}

			$directory_iterator=new RecursiveDirectoryIterator($root_path, 4096 | 8192 | RecursiveIteratorIterator::CATCH_GET_CHILD);
			$filter = new WPBackItUp_RecursiveFilterIterator($directory_iterator,$exclude);
			$item_iterator = new RecursiveIteratorIterator($filter,RecursiveIteratorIterator::SELF_FIRST);

			$datetime1 = new DateTime('now');
			$sql="";
			$db = new WPBackItUp_DataAccess();

			foreach ($item_iterator  as $file) {
				//Skip the item if its in the exclude array
				//This is a string compare starting in position 1

				//Fix the path to use backslash
				$file_path = str_replace('\\', "/",$file->getSubPathname());

				//Remove special characters
				$file_path = esc_sql($file_path);

				//TODO: Why does this function not exist for php 5.5
				// checking if file name contain illegal character
				if (function_exists ('mb_regex_encoding') && function_exists( 'mb_ereg_replace' )) {
					mb_regex_encoding("UTF-8");
					$temp_file_name = mb_ereg_replace(WPBACKITUP__VALID_FILENAME_REGEX, '', basename($file));
					if(strcmp($temp_file_name, basename($file)) != 0){
						WPBackItUp_Logger::log_error($inventory_logname,__METHOD__,'Skipping file: ' . $file );
						WPBackItUp_Logger::log_error($inventory_logname,__METHOD__,'Filename after removing illegal character: ' . $temp_file_name );
						continue;
					}
				}

				if ( $file->isFile()) {
					if ($batch_counter>=$batch_insert_size){
						WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, '*Try Write Batch*');
						if (! $db->insert_job_items($sql)) {
							return false;
						}
						WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, '*Write Batch SUCCESS*');
						$sql="";
						$batch_counter=0;
					}
					$total_counter++;
					$batch_counter++;
					$file_size=ceil($file->getSize()/1024);//round up
					WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, 'Add File: ' .$batch_counter . ' ' .$file_path);
					$sql.= "(".$job_id .", '" .$group_id."', '" .utf8_encode($file_path) ."', ".$file_size .",'" . current_time('mysql') ."'),";
				}
			}

			if ($batch_counter>0) {
				WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, '*Try Write Batch*');
				if (! $db->insert_job_items($sql)) {
					return false;
				}
				WPBackItUp_Logger::log_info($inventory_logname,__METHOD__, '*Write Batch SUCCESS*');
			}

			$datetime2 = new DateTime('now');

			WPBackItUp_Logger::log_info($inventory_logname, __METHOD__, '**END**');

            if(method_exists($datetime2, 'diff')) {
                $interval = $datetime1->diff($datetime2);
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Count/Time: ' .$total_counter . '-' . $interval->format('%s seconds'));
            } else {
                $util = new WPBackItUp_Utility($this->log_name);
                $interval = $util->date_diff_array($datetime1, $datetime2);
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Count/Time: ' .$total_counter . '-' . $interval['second'] . ' seconds');
            }


			return true;

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
			return false;
		}
	}

	/**
	 * Save inventory of array list to database
	 *
	 * @param $batch_insert_size
	 * @param $job_id
	 * @param $group_id
	 * @param $root_path
	 * @param $file_list
	 *
	 * @return bool
	 */
	public function save_file_list_inventory($batch_insert_size,$job_id,$group_id,$root_path,$file_list) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin:' .var_export($file_list,true));

		//check is array list
		if (! is_array($file_list)) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Array expected in file list:');
			return false;
		}

		try {
			$batch_counter = 0;
			$total_counter=0;

			$datetime1 = new DateTime('now');
			$sql="";
			$db = new WPBackItUp_DataAccess();
			foreach ($file_list as $file_path=>$file_size){

				//skip if folder
				if ( is_dir( $file_path ) ) {
					WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Skip folder:' . $file_path );
					continue;
				}


				if ($batch_counter>=$batch_insert_size){
					if (! $db->insert_job_items($sql)) {
						return false;
					}
					$sql="";
					$batch_counter=0;
				}
				$total_counter++;
				$batch_counter++;
				//$file_size=ceil(filesize($file_path) /1024);//round up

				//get rid of root path and utf8 encode
				$file_path = utf8_encode(str_replace($root_path,'',$file_path));

				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Add File: ' .$batch_counter . ' ' .$file_path);
				$sql.= "(".$job_id .", '" .$group_id."', '" .$file_path ."', ".$file_size .",'" . current_time('mysql') . "' ),";
			}

			if ($batch_counter>0) {
				if (! $db->insert_job_items($sql)) {
					return false;
				}
			}

			$datetime2 = new DateTime('now');

            if(method_exists($datetime2, 'diff')) {
                $interval = $datetime1->diff($datetime2);
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Count/Time: ' .$total_counter . '-' . $interval->format('%s seconds'));
            } else {
                $util = new WPBackItUp_Utility($this->log_name);
                $interval = $util->date_diff_array($datetime1, $datetime2);
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Count/Time: ' .$total_counter . '-' . $interval['second'] . ' seconds');
            }

            return true;

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
			return false;
		}
	}


	/**
	 *
	 * Fetch batch of files from DB and add to zip
	 *
	 * @param $job_id
	 * @param $source_root
	 * @param $content_type
	 *
	 * @return bool|mixed
	 */
	public function backup_files($job_id,$source_root,$content_type){
        global $WPBackitup;
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin ');

		//get files to backup
		$db = new WPBackItUp_DataAccess();

		switch($content_type)
		{
			case WPBackItUp_Job_Item::THEMES;
				$target_root='wp-content-themes';
				$batch_size=$WPBackitup->backup_themes_batch_size();
				break;
			case WPBackItUp_Job_Item::PLUGINS;
				$target_root='wp-content-plugins';
				$batch_size=$WPBackitup->backup_plugins_batch_size();
				break;
			case WPBackItUp_Job_Item::UPLOADS;
				$target_root='wp-content-uploads';
				$batch_size=$WPBackitup->backup_uploads_batch_size();
				break;
			case WPBackItUp_Job_Item::OTHERS;
				$target_root='wp-content-other';
				$batch_size=$WPBackitup->backup_others_batch_size();
				break;
			default:
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Content type not recognized:'.$content_type);
				return false;

		}

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Batch Size: '. $batch_size);

		//get a timestamp for the batch id
		$batch_id=current_time( 'timestamp' );
		$file_list = $db->get_batch_open_items($batch_id,$batch_size,$job_id,$content_type);

		// sometimes file list is an empty, still couldn't figure out why. 
		if(is_array($file_list)){
			if(empty($file_list)){
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Upload Folder is Empty');
				return 0;
			}
		}
		//It is possible that there are no file to backup so return count or false
		if($file_list == false || $file_list==0) return $file_list;

		//$zip_file_path = $this->backup_project_path . $this->backup_name .'-'.$content_type .'.zip';
		$zip_file_path = sprintf('%s%s-%s-%s.zip',$this->backup_project_path,$this->backup_name,$content_type,$batch_id);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Zip file path: '. $zip_file_path);

		//IF false error happened
		$file_count=$this->backup_files_to_zip($source_root,$target_root,$file_list,$zip_file_path);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Files added to zip:'.$file_count);
		if (false===$file_count){
			return false;
		}

        // Clears file status cache
        clearstatcache();

        //Check to see if the file exists, it is possible that it does not if only empty folders were contained
        if(! file_exists($zip_file_path) ) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Zip File NOT found:'.$zip_file_path);

	        $file_system = new WPBackItUp_FileSystem($this->log_name);
	        $files_in_temp_directory = $file_system->get_fileonly_list($this->backup_project_path, 'zip');
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Files In Temp Folder:');
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,$files_in_temp_directory);
	        return false;
        }

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Zip file FOUND:'.$zip_file_path);

		//update the batch with the number of files in the added count
		$db->update_item_batch_complete($job_id,$batch_id,$file_count);

		//get count of remaining
		$remaining_count = $db->get_open_item_count($job_id,$content_type);

		//if none remaining - check for items with retry count>3
		if (0==$remaining_count){
			//if any files werent backed up then return false
			$error_count = $db->get_error_item_count($job_id,$content_type);
			if ($error_count>0){
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Item Error count:'.$error_count);
				return false;
			}
		}

		//return count;
        return $remaining_count;
	}

	/**
	 *  Validate backup files by batch ID
	 *  A batch will typically be one zip file.
	 *
	 * @param $job_id
	 * @param $content_type
	 * @param $batch_id
	 *
	 * @return bool
	 */
	public function validate_backup_files_by_batch_id($job_id,$content_type,$batch_id){
        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin: '.$content_type . ' Batch ID: ' . $batch_id);

        //get files to backup
        $db = new WPBackItUp_DataAccess();

        switch($content_type)
        {
            case WPBackItUp_Job_Item::THEMES;
                $target_root='wp-content-themes';
                break;
            case WPBackItUp_Job_Item::PLUGINS;
                $target_root='wp-content-plugins';
                break;
            case WPBackItUp_Job_Item::UPLOADS;
                $target_root='wp-content-uploads';
                break;
            case WPBackItUp_Job_Item::OTHERS;
                $target_root='wp-content-other';
                break;
            //ADD exception when other
        }

        $file_list = $db->get_completed_items_by_batch_id($job_id,$content_type,$batch_id);

        //It is possible that there were no files backed up
        if( $file_list == false || $file_list==0 ) {
            WPBackItUp_Logger::log_info($this->log_name,__METHOD__, 'No files found to validate.');
            return true;
        }

        $current_zip_file=null;
        $zip=null;
        $file_counter=0;

        //get zip path
        $zip_file_path = sprintf('%s-%s-%s.zip',$this->backup_project_path . $this->backup_name, $content_type,$batch_id);
        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Zip file: '. $zip_file_path);

        if ( ! file_exists($zip_file_path) ) {
            WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Zip File not found:' . $zip_file_path );
            // Scanning Temp Directory.
            $files_on_temp_directory = scandir($this->backup_project_path);
            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin::Files on TMP Directory');
            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,$files_on_temp_directory);
            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End::Files on TMP Directory');
            return false;
        }

        $current_zip_file = $zip_file_path;
        $zip = new WPBackItUp_Zip($this->log_name,$current_zip_file);
        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Current Zip File:' . $current_zip_file );

        foreach($file_list as $file) {
            $item     = $target_root .'/' .utf8_decode( $file->item );

            //validate file exists in zip
            if (false===$zip->validate_file($item)) {
                WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File NOT found in zip :' . $item );
                $zip->close();
                return false;
            }
            $file_counter++;
        }

        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Validation Successful:'.$content_type . '-' .$file_counter);
        if (null!=$zip) $zip->close();
        return true;
    }

	/**
	 *
	 * Add files in file list to zip file
	 *
	 *
	 * @param $source_root
	 * @param $target_root
	 * @param $file_list (object collection)
	 * @param $zip_file_path
	 *
	 * @return bool|int False or count of files added to zip
	 */
	private function backup_files_to_zip($source_root,$target_root,$file_list, $zip_file_path){
		global $WPBackitup;

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin ');

        if (empty($file_list) || !isset($file_list)) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File list is not valid:');
	        WPBackItUp_Logger::log($this->log_name,var_export($file_list,true));
            return false;
        }

		$zip_max_file_size =  $WPBackitup->max_zip_size();

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin - Item Count: '. count($file_list));
        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'ZIP MAX FILE SIZE:' .WPBackItUp_FileSystem::format_file_size($zip_max_file_size));
		$zip = new WPBackItUp_Zip($this->log_name,$zip_file_path);

        $file_size_counter = 0;
		foreach($file_list as $file) {
			$item = $source_root. '/' .utf8_decode($file->item);
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File:' .$item);

			clearstatcache();
            $file_size_counter += filesize($item);
            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Size Counter:' .WPBackItUp_FileSystem::format_file_size($file_size_counter));
            if($file_size_counter >= $zip_max_file_size){
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Exceeded Max Zip Size.');
	            break;//jump out of the for loop
            }

			//replace the source path with the target & fix any pathing issues
			$target_item_path = str_replace(rtrim($source_root, '/'),rtrim($target_root,'/'),$item);
			$target_item_path= str_replace('//','/',$target_item_path);
			$target_item_path= str_replace('\\','/',$target_item_path);

			//WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Add File:' .$target_item_path );
			if ( $zip->add_file($item,$target_item_path)) {
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,sprintf('(%s)File Added:%s', $zip->get_zip_file_count(),$target_item_path));
			} else {
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File NOT added:' . $target_item_path );
				return false;
			}
		}

		$files_count = $zip->get_files_in_zip();

		//if we get here then close the zip
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,sprintf('Zip File Status BEFORE Close:%s', $zip->get_zip_status()));
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,sprintf('Number of files in zip:%s',$files_count ));
		$zip_closed = $zip->close();//close the zip - true for success/false error

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End');

		//return filecount on success and false on close error
		return true===$zip_closed? $files_count: false;
	}


	/**
	 * @param $source_root
	 * @param $target_root
	 * @param $suffix
	 * @param $file_list array reference value
	 * @param $batch_size
     * @param $batch_id
	 *
	 * @return bool|int  false on error OR count
	 */
	public function backup_file_list($source_root,$target_root,$suffix,&$file_list,$batch_size, $batch_id){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		if (! is_array($file_list)) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Array expected in file list:');
			WPBackItUp_Logger::log($this->log_name,var_export($file_list,true));
			return false;
		}

		//$zip_file_path = $this->backup_project_path . $this->backup_name .'-'.$suffix .'.tmp';
		$zip_file_path = sprintf('%s%s-%s-%s.zip',$this->backup_project_path,$this->backup_name,$suffix,$batch_id);
		$zip = new WPBackItUp_Zip($this->log_name,$zip_file_path);
		foreach($file_list as $item) {
			$item = utf8_decode($item);
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File:' . $item );

			//skip it if folder
			if ( is_dir( $item ) ) {
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Skip folder:' . $item );
				array_shift( $file_list ); //remove from list
				continue;
			}

			//replace the source path with the target
			$target_item_path = str_replace(rtrim($source_root, '/'),rtrim($target_root,'/'),$item);
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Add File:' .$target_item_path );
			if ( $zip->add_file($item,$target_item_path)) {
				array_shift($file_list);
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Added:' . $target_item_path );
				//If we have added X# of files or hit the size limit then lets close the zip and finish on the next pass
				if( $zip->get_zip_file_count()>=$batch_size){
					$zip->close();//close the zip
					WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End - Item Count:' . count($file_list));
					return count($file_list);
				}
			} else {
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File NOT added:' . $target_item_path );
				return false;
			}
		}

		//if we get here then close the zip
		$zip->close();//close the zip

		if( ! file_exists($zip_file_path) ){
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Zip file was not created.');
			return false;
		}

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End - Item Count:' . count($file_list));
		return count($file_list);
	}



	public function backup_file_to_zip($source_root,$target_root,$file_path, $zip_file_path){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		if ( empty($file_path)) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File path expected:');
			WPBackItUp_Logger::log($this->log_name,var_export($file_path,true));
			return false;
		}

		if ( !file_exists($file_path)) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File not found:');
			WPBackItUp_Logger::log($this->log_name,var_export($file_path,true));
			return false;
		}

		//create/open the zip file
		$zip = new WPBackItUp_Zip($this->log_name,$zip_file_path);

		$file_path = utf8_decode($file_path);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File:' . $file_path );

		//replace the source path with the target
		$target_item_path = str_replace(rtrim($source_root, '/'),rtrim($target_root,'/'),$file_path);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Add File:' .$target_item_path );
		if ( $zip->add_file($file_path,$target_item_path)) {
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Added:' . $target_item_path );
		} else {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File NOT added:' . $target_item_path );
			return false;
		}

		//if we get here then close the zip
		return $zip->close();//close the zip

	}

	/**
	 * Remove supporting zip files(async)
	 * This method will dispatch a cleanup task
	 *
	 * @param $zip_files
	 *
	 * @return bool
	 */
	public function remove_supporting_zips($zip_files){

		if (!is_array($zip_files) || count($zip_files)<=0) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Zip file list was not array ' .var_export( $zip_files,true));
			return false;
		}

		try {
			//build a list of files to pass to the task processor
			$file_list[]='cleanup-zip'; //add task identifier
			//zip files created above
			$backupset_found=false;
			foreach ($zip_files as $file_path => $file_size){
				if (false=== strpos(basename( $file_path ),'-backupset-')){
					$file_list[] =$file_path;
				} else {
					$backupset_found=true;
				}
			}

			//If there was a backup set found then OK to remove other zips
			if (true===$backupset_found) {

				//create the array of task(s) - one here
				$cleanup_tasks = array($file_list);
				//run background processor
				$cleanup_processor  = new WPBackItUp_Cleanup_Processor();
				//
				//realize there is only one task but wanted to put in for each for future
				foreach ( $cleanup_tasks as $cleanup_task ) {
					$cleanup_processor->push_to_queue( $cleanup_task );
				}

				$cleanup_processor->save()->dispatch();
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Cleanup support zips job dispatched.');
			}


			return true;

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Error Occurred: ' .$e);
			return false;
		}
	}

	/**
	 * Create manifest file
	 *
	 * @return bool
	 */
	public function create_backup_manifest(){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		//get a list of all the zips
		$backup_files_path = array_filter(glob($this->backup_project_path. '*.zip'), 'is_file');
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Zip files found:'. var_export($backup_files_path,true));
		if (is_array($backup_files_path) && count($backup_files_path)>0){

			//build the manifest with file size
			$backup_file_list = array();
			foreach($backup_files_path as $file){
				clearstatcache();
				$file_name =basename($file);
				$backup_file_list[$file_name] = filesize($file);
			}


			//get rid of the path.
			//$backup_files = str_replace($this->backup_project_path,'',$backup_files_path);
			$manifest_file=$this->backup_project_path . 'backupmanifest.config';

			$bytes = file_put_contents($manifest_file,json_encode($backup_file_list));
			if ( $bytes===false || $bytes<=0){
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Manifest file was not created successfully.');
				return false;
			}

            //Find the main zip in the array to get the path
            $main_zip_index = $this->search_array('-main-', $backup_files_path);

            //add it to the main zip file
            if ($main_zip_index!==false){
                $zip_file_path = $backup_files_path[$main_zip_index];
                $zip = new WPBackItUp_Zip($this->log_name,$zip_file_path);
                $target_item_path = str_replace(rtrim($this->backup_project_path, '/'),rtrim('site-data','/'),$manifest_file);
                if ($zip->add_file($manifest_file,$target_item_path)) {
                    $zip->close();//close the zip
	                WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End -  Manifest created.');
	                @unlink($manifest_file);
                    return true;
                }
             }else{
	            WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Main zip not found.');
            }
		}

		@unlink($manifest_file);
		WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'End -  Manifest not created.');
		return false;
	}

	/**
	 * Search Array for value
	 * @param $search
	 * @param $array
	 *
	 * @return bool|int|string
	 */
	private function search_array($search, $array)
    {
        foreach($array as $key => $value)
        {
            if (stristr($value, $search))
            {
                return $key;
            }
        }
        return false;
    }


	/**
	 * Rename Backup folder
	 *
	 * @return bool
	 */
	public function rename_backup_folder() {
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

        $backup_project_path = $this->backup_project_path;
        //remove the 4 character prefix
        $new_backup_path = str_replace('TMP_','',$backup_project_path);

        $file_system = new WPBackItUp_FileSystem($this->log_name);
        if (! $file_system->rename_file($backup_project_path,$new_backup_path)){
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Folder could not be renamed');
            return false;
        }

        $this->set_final_backup_path();

	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End');
        return true;
    }

	/**
	 * this is needed because it is set to TMP until finalization then needed a way to know where the current path is
	 *
	 */
	public function set_final_backup_path(){
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

        $backup_project_path = $this->backup_project_path;
        $new_backup_path = str_replace('TMP_','',$backup_project_path);

        //set the path to the new path
        $this->backup_project_path=$new_backup_path;

	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End');
    }

	/**
	 * @return string
	 */
	public function getBackupProjectPath() {
		return $this->backup_project_path;
	}

}
