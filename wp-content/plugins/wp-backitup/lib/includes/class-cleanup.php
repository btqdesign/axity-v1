<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * WP BackItUp -  Cleanup Class
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

// include backup class
if( !class_exists( 'WPBackItUp_Backup' ) ) {
    include_once 'class-backup.php';
}



class WPBackItUp_Cleanup {


	public static $CLEANUP_TASKS = array(
		'task_begin',
		'task_purge_job_control',
		'task_purge_prefixed_folder_and_files',
		'task_purge_orphan_folder_and_files',
		'task_purge_old_files',
		'task_secure_folders',
		'task_end'
	);

	const DEFAULT_LOG_NAME ='debug_cleanup';
	private $log_name = self::DEFAULT_LOG_NAME;

	/**
	 * WPBackItUp_Cleanup constructor.
	 */
	function __construct() {

		try{


		} catch(Exception $e) {
			error_log(var_export($e,true));
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Constructor Exception: ' .var_export($e,true));
		}

	}

	/**
	 * action init
	 */
	public function init(){
		add_action( 'wpbackitup_cleanup_begin', array( $this, 'begin_job' ));
		add_action( 'wpbackitup_cleanup_purge_job_control', array( $this, 'purge_job_control'));
		add_action( 'wpbackitup_cleanup_purge_prefixed_folder_and_files', array( $this,'purge_prefixed_folder_and_files'));
		add_action( 'wpbackitup_cleanup_purge_orphan_folder_and_files', array( $this, 'purge_orphan_folder_and_files'));
		add_action( 'wpbackitup_cleanup_purge_old_files', array( $this, 'purge_old_files' ) );
		add_action( 'wpbackitup_cleanup_secure_folders', array( $this, 'secure_folders' ) );
		add_action( 'wpbackitup_cleanup_end', array( $this, 'end' ) );
	}

	/**
	 * Queue Cleanup Job
	 *
	 * @return bool|WPBackItUp_Job
	 */
	public static function queue_job(){

		try {
			$job_type= WPBackItUp_Job::CLEANUP;
			$job_id= current_time('timestamp');
			$job_name = sprintf('%s_%s',$job_type, $job_id);

			$job_tasks = WPBackItUp_Job::get_job_tasks($job_type);
			return WPBackItUp_Job::queue_job($job_name,$job_id, $job_type, WPBackItUp_Job::SCHEDULED,$job_tasks);

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error(self::DEFAULT_LOG_NAME,__METHOD__,'Constructor Exception: ' .var_export($e,true));
		}
	}

	/**
	 * Get log name
	 *
	 * @param WPBackItUp_Job_Task $task
	 *
	 * @return string
	 */
	private function set_job_log($task){

		try {

			//default logname is set already
			if (is_object($task)) {
				$this->log_name = sprintf( 'JobLog_%s_%s', WPBackItUp_Job::CLEANUP, $task->getJobId() );
			}

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .var_export($e,true));
		}
	}


	/**
	 * Cleanup init
	 *
	 * @param WPBackItUp_Job_Task $task
	 *
	 */
    public function begin_job($task){

	    try{

		    $this->set_job_log($task);

	        WPBackItUp_Logger::log($this->log_name,'***BEGIN JOB***');
		    $job_id = $task->getJobId();
		    $job = WPBackItUp_Job::get_job_by_id($job_id);
		    if (false===$job){
			    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Cleanup job not be found: ' .var_export($job_id,true));
			    return false;
		    }

		    $job->setStatus(WPBackItUp_Job::ACTIVE);

	        WPBackItUp_Logger::log_sysinfo($this->log_name);

	        //Check License
	        WPBackItUp_Logger::log($this->log_name,'**CHECK LICENSE**');
	        do_action( 'wpbackitup_check_license');
	        WPBackItUp_Logger::log($this->log_name,'**END CHECK LICENSE**');

		    $task->setStatus(WPBackItUp_Job_Task::COMPLETE);

	    }catch(Exception $e) {
		    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .var_export($e,true));
		    $task->setStatus(WPBackItUp_Job_Task::ERROR);
	    }
    }


	/**
	 * purge job control
	 *
	 * @param WPBackItUp_Job_Task $task
	 */
    public function purge_job_control($task){
        global $WPBackitup;

		try {

			$this->set_job_log($task);

	        $backup_retention = $WPBackitup->backup_retained_number();
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Backup Retention:' .$WPBackitup->backup_retained_number());

	        // Purge post and post meta
	        WPBackItUp_Logger::log($this->log_name,'**CLEANUP JOB CONTROL RECORDS**' );

	        $backup_job_purge_count = WPBackItUp_Job::purge_jobs( WPBackItUp_Job::BACKUP,$backup_retention);
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Backup job control records purged:' .$backup_job_purge_count );

	        $cleanup_job_purge_count = WPBackItUp_Job::purge_jobs( WPBackItUp_Job::CLEANUP,2);
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Cleanup job control records purged:' .$cleanup_job_purge_count );

	        WPBackItUp_Logger::log($this->log_name,'**END CLEANUP JOB CONTROL RECORDS**' );

			$task->setStatus(WPBackItUp_Job_Task::COMPLETE);

		}catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .var_export($e,true));
			$task->setStatus(WPBackItUp_Job_Task::ERROR);
		}
    }

	/**
	 * purge prefixed folders and files.
	 *
	 * @param WPBackItUp_Job_Task $task
	 */
    public function purge_prefixed_folder_and_files($task){
        global $wp_backup;

        try {

	        $this->set_job_log($task);

	        $wp_backup = new WPBackItUp_Backup($this->log_name,'not_used',WPBackItUp_Job::SCHEDULED);

	        WPBackItUp_Logger::log($this->log_name,'**CLEAN UNFINISHED BACKUPS**' );

	        //cleanup any folders that have the TMP_ prefix
		    $wp_backup->cleanup_backups_by_prefix('TMP_');
	        WPBackItUp_Logger::log($this->log_name,'**END CLEAN UNFINISHED BACKUPS**' );

	        WPBackItUp_Logger::log($this->log_name,'**CLEAN DELETED BACKUPS**' );
	        //cleanup any folders that have the DLT_ prefix
	        $wp_backup->cleanup_backups_by_prefix('DLT_');

	        WPBackItUp_Logger::log($this->log_name,'**END CLEAN DELETED BACKUPS**' );
	        $task->setStatus(WPBackItUp_Job_Task::COMPLETE);

        } catch(Exception $e) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .var_export($e,true));
	        $task->setStatus(WPBackItUp_Job_Task::ERROR);
        }
    }

    /**
     * purge orphan folder and files.
     *
     * @param WPBackItUp_Job_Task $task
     */
    public function purge_orphan_folder_and_files($task){
        global $wp_backup;

        try {
	        $this->set_job_log($task);

	        $wp_backup = new WPBackItUp_Backup($this->log_name,'not_used',WPBackItUp_Job::SCHEDULED);

	        //Purge orphaned backup folders - folders with no job control record
	        WPBackItUp_Logger::log($this->log_name,'**CLEAN OLD BACKUPS**' );
	        $wp_backup->purge_orphaned_backups();
	        WPBackItUp_Logger::log($this->log_name,'**END CLEAN OLD BACKUPS**' );


	        //remove all the old restore folders
	        if( class_exists( 'WPBackItUp_Premium_Restore' ) ) {
	            WPBackItUp_Logger::log($this->log_name,'**CLEAN OLD RESTORES**' );
		        $wp_restore = new WPBackItUp_Premium_Restore($this->log_name,'not_used',null);

		        //Only available premium 1.14.6+
	            if (method_exists($wp_restore,'delete_restore_folders')) {
		            $wp_restore->delete_restore_folders();
		            WPBackItUp_Logger::log($this->log_name,'**END CLEAN OLD RESTORES**' );
	            }
	        }

	        $task->setStatus(WPBackItUp_Job_Task::COMPLETE);

        }catch(Exception $e) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .var_export($e,true));
	        $task->setStatus(WPBackItUp_Job_Task::ERROR);
        }
    }

    /**
     * purge old files
     * @param WPBackItUp_Job_Task $task
     */
    public function purge_old_files($task){

        try {
	        $this->set_job_log($task);

	        WPBackItUp_Logger::log($this->log_name,'**PURGE OLD FILES**' );

	        $backup_retained_number = WPBackItUp_Utility::get_option('_backup_retained_number',5);
	        $backup_path = WPBACKITUP__BACKUP_PATH .'/';


	        // purge old files from the backup and logs folders - this is NOT for backups
	        $fileSystem = new WPBackItUp_FileSystem( $this->log_name);

	        //Check the retention
	        $fileSystem->purge_FilesByDate($backup_retained_number,$backup_path);

	        //      --PURGE BACKUP FOLDER
	        //Purge logs in backup older than N days
	        $fileSystem->purge_files($backup_path,'*.log',WPBACKITUP__BACKUP_RETAINED_DAYS);

	        //Purge restore DB checkpoints older than 5 days
	        $fileSystem->purge_files($backup_path,'db*.cur',WPBACKITUP__BACKUP_RETAINED_DAYS);

	        //      --PURGE LOGS FOLDER
	        $logs_path = WPBACKITUP__PLUGIN_PATH .'/logs/';

	        //Purge logs in logs older than 5 days
	        $fileSystem->purge_files($logs_path,'*.log',WPBACKITUP__BACKUP_RETAINED_DAYS);

	        //Purge Zipped logs in logs older than 5 days
	        $fileSystem->purge_files($logs_path,'*.zip',WPBACKITUP__BACKUP_RETAINED_DAYS);

	        //check debug.log
	        //TODO: Add UI for setting to purge debug.log when gets too big - use MB in UI - 104857600(100mb)
	        $max_size_bytes = WPBackItUp_Utility::get_option('max_log_size',false);
	        $debug_log_path = WPBACKITUP__CONTENT_PATH . '/debug.log';
	        if (false!== $max_size_bytes && file_exists($debug_log_path)){
		        $debug_log_size = filesize($debug_log_path);
		        WPBackItUp_Logger::log($this->log_name,'Checking debug.log file size:'. $debug_log_size );
		        if ($debug_log_size>$max_size_bytes){
			        @unlink($debug_log_path);
			        WPBackItUp_Logger::log($this->log_name,'debug.log purged.' );
		        }
	        }

	        WPBackItUp_Logger::log($this->log_name,'**END PURGE OLD FILES**' );

	        $task->setStatus(WPBackItUp_Job_Task::COMPLETE);

        }catch(Exception $e) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .var_export($e,true));
	        $task->setStatus(WPBackItUp_Job_Task::ERROR);
        }
    }

    /**
     * Secure folders
     *
     * @param WPBackItUp_Job_Task $task
     */
    public function secure_folders($task){

    	try {
		    $this->set_job_log($task);

	        WPBackItUp_Logger::log($this->log_name,'**SECURE FOLDERS**' );
	        $file_system = new WPBackItUp_FileSystem($this->log_name);

	        //Make sure backup folder is secured
	        $backup_dir = WPBACKITUP__CONTENT_PATH . '/' . WPBACKITUP__BACKUP_FOLDER;
	        $file_system->secure_folder( $backup_dir);

	        //--Check restore folder folders
	        $restore_dir = WPBACKITUP__CONTENT_PATH . '/' . WPBACKITUP__RESTORE_FOLDER;
	        $file_system->secure_folder( $restore_dir);

	        //Make sure logs folder is secured
	        $logs_dir = WPBACKITUP__PLUGIN_PATH .'/logs/';
	        $file_system->secure_folder( $logs_dir);

	        WPBackItUp_Logger::log($this->log_name,'**END SECURE FOLDERS**' );
		    $task->setStatus(WPBackItUp_Job_Task::COMPLETE);

	    }catch(Exception $e) {
		    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .var_export($e,true));
		    $task->setStatus(WPBackItUp_Job_Task::ERROR);
	    }
    }

    /**
     * Finish & update job
     *
     * @param WPBackItUp_Job_Task $task
     */
    public function end($task){
        global $WPBackitup;

        try {
	        $this->set_job_log($task);

	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

	        //upadate the last run datetime
	        $current_datetime = current_time( 'timestamp' );
	        $WPBackitup->set_cleanup_lastrun_date($current_datetime);

	        $job_id = $task->getJobId();
	        $job = WPBackItUp_Job::get_job_by_id($job_id);
	        if (false===$job){
		        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Cleanup job not be found: ' .var_export($job_id,true));
	        	return false;
	        }

	        $task->setStatus(WPBackItUp_Job_Task::COMPLETE);
	        $job->setStatus(WPBackItUp_Job::COMPLETE);

	        $util = new WPBackItUp_Utility($this->log_name);
	        $seconds = $util->timestamp_diff_seconds($job->getJobStartTimeTimeStamp(),$job->getJobEndTimeTimeStamp());

	        $processing_minutes = round($seconds / 60);
	        $processing_seconds = $seconds % 60;

	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Script Processing Time:' .$processing_minutes .' Minutes ' .$processing_seconds .' Seconds');

	        WPBackItUp_Logger::log($this->log_name,'*** END JOB ***');

        }catch(Exception $e) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .var_export($e,true));
	        $task->setStatus(WPBackItUp_Job_Task::ERROR);
        }
    }





}