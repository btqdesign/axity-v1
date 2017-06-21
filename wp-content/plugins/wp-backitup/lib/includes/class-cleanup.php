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

    /**
     * @var array
     */
    public static $TASK_ITEMS = array(
        'init',
        'purge_job_control',
        'purge_prefixed_folder_and_files',
        'purge_orphan_folder_and_files',
        'purge_old_files',
        'secure_folders',
        'end'
    );

    /**
     * @var string
     */
    private $cleanup_logname;

    /**
     * @var mixed
     */
    private $job;

    /**
     * @var string
     */
    private $job_name;

    /**
     * @var string
     */
    private $job_id;

    /**
     * @var string
     */
    private $job_type;

    /**
     * action init
     */
    public function init(){
        add_action( 'wpbackitup_cleanup_init', array( $this, 'wpbackitup_cleanup_init' ) );
        add_action( 'wpbackitup_cleanup_purge_job_control', array( $this, 'wpbackitup_cleanup_purge_job_control' ) );
        add_action( 'wpbackitup_cleanup_purge_prefixed_folder_and_files', array( $this, 'wpbackitup_cleanup_purge_prefixed_folder_and_files' ) );
        add_action( 'wpbackitup_cleanup_purge_orphan_folder_and_files', array( $this, 'wpbackitup_cleanup_purge_orphan_folder_and_files' ) );
        add_action( 'wpbackitup_cleanup_purge_old_files', array( $this, 'wpbackitup_cleanup_purge_old_files' ) );
        add_action( 'wpbackitup_cleanup_secure_folders', array( $this, 'wpbackitup_cleanup_secure_folders' ) );
        add_action( 'wpbackitup_cleanup_end', array( $this, 'wpbackitup_cleanup_end' ) );
    }

    /**
     * Cleanup init
     */
    public function wpbackitup_cleanup_init(){
        global $WPBackitup;

        $this->job_type= WPBackItUp_Job::CLEANUP;
        $this->job_id= current_time('timestamp');
        $this->job_name = sprintf('%s_%s',$this->job_type, $this->job_id);
        $this->cleanup_logname = sprintf('JobLog_%s', $this->job_name);
        $backup_retention = $WPBackitup->backup_retained_number();
        $job_tasks = WPBackItUp_Job::get_job_tasks($this->job_type);

        $this->job = WPBackItUp_Job::queue_job($this->job_name,$this->job_id, $this->job_type, WPBackItUp_Job::SCHEDULED,$job_tasks);

        WPBackItUp_Logger::log($this->cleanup_logname,'***BEGIN JOB***');
        WPBackItUp_Logger::log_sysinfo($this->cleanup_logname);

        WPBackItUp_Logger::log($this->cleanup_logname,'Backup Retention:' .$backup_retention);

        //Check License
        WPBackItUp_Logger::log($this->cleanup_logname,'**CHECK LICENSE**');
        do_action( 'wpbackitup_check_license');
        WPBackItUp_Logger::log($this->cleanup_logname,'**END CHECK LICENSE**');
    }


    /**
     * purge job control
     */
    public function wpbackitup_cleanup_purge_job_control(){
        global $WPBackitup;
        $backup_retention = $WPBackitup->backup_retained_number();

        // Purge post and post meta
        WPBackItUp_Logger::log($this->cleanup_logname,'**CLEANUP JOB CONTROL RECORDS**' );

        $backup_job_purge_count = WPBackItUp_Job::purge_jobs_async( WPBackItUp_Job::BACKUP,$backup_retention);
        WPBackItUp_Logger::log($this->cleanup_logname,'Backup job control records purged:' .$backup_job_purge_count );

        $cleanup_job_purge_count = WPBackItUp_Job::purge_jobs_async( WPBackItUp_Job::CLEANUP,2);
        WPBackItUp_Logger::log_info($this->cleanup_logname,__METHOD__,'Cleanup job control records purged:' .$cleanup_job_purge_count );

        WPBackItUp_Logger::log($this->cleanup_logname,'**END CLEANUP JOB CONTROL RECORDS**' );
    }

    /**
     * purge prefixed folders and files.
     */
    public function wpbackitup_cleanup_purge_prefixed_folder_and_files(){
        global $WPBackitup;
        global $wp_backup;
        $wp_backup = new WPBackItUp_Backup($this->cleanup_logname,$this->job_name,$WPBackitup->backup_type);

        WPBackItUp_Logger::log($this->cleanup_logname,'**CLEAN UNFINISHED BACKUPS**' );

        //cleanup any folders that have the TMP_ prefix
        $wp_backup->cleanup_backups_by_prefix_async('TMP_');
        WPBackItUp_Logger::log($this->cleanup_logname,'**END CLEAN UNFINISHED BACKUPS**' );

        WPBackItUp_Logger::log($this->cleanup_logname,'**CLEAN DELETED BACKUPS**' );
        //cleanup any folders that have the DLT_ prefix
        $wp_backup->cleanup_backups_by_prefix_async('DLT_');

        WPBackItUp_Logger::log($this->cleanup_logname,'**END CLEAN DELETED BACKUPS**' );
    }

    /**
     * purge orphan folder and files.
     */
    public function wpbackitup_cleanup_purge_orphan_folder_and_files(){
        global $WPBackitup;
        global $wp_backup;
        $wp_backup = new WPBackItUp_Backup($this->cleanup_logname,$this->job_name,$WPBackitup->backup_type);

        //Purge orphaned backup folders - folders with no job control record
        WPBackItUp_Logger::log($this->cleanup_logname,'**CLEAN OLD BACKUPS**' );
        $wp_backup->purge_orphaned_backups_async();
        WPBackItUp_Logger::log($this->cleanup_logname,'**END CLEAN OLD BACKUPS**' );


        //remove all the old restore folders
        if( class_exists( 'WPBackItUp_Premium_Restore' ) ) {
            WPBackItUp_Logger::log($this->cleanup_logname,'**CLEAN OLD RESTORES**' );
            $wp_restore = new WPBackItUp_Premium_Restore($this->cleanup_logname,$this->job_name,null);
            $wp_restore->delete_restore_folder_async();
            WPBackItUp_Logger::log($this->cleanup_logname,'**END CLEAN OLD RESTORES**' );
        }
    }

    /**
     * purge old files
     */
    public function wpbackitup_cleanup_purge_old_files(){
        global $wp_backup;

        WPBackItUp_Logger::log($this->cleanup_logname,'**PURGE OLD FILES**' );

        // purge old files from the backup and logs folders - this is NOT for backups
        $wp_backup->purge_old_files_async();

        WPBackItUp_Logger::log($this->cleanup_logname,'**END PURGE OLD FILES**' );
    }

    /**
     * Secure folders
     */
    public function wpbackitup_cleanup_secure_folders(){
        WPBackItUp_Logger::log($this->cleanup_logname,'**SECURE FOLDERS**' );
        $file_system = new WPBackItUp_FileSystem($this->cleanup_logname);

        //Make sure backup folder is secured
        $backup_dir = WPBACKITUP__CONTENT_PATH . '/' . WPBACKITUP__BACKUP_FOLDER;
        $file_system->secure_folder( $backup_dir);

        //--Check restore folder folders
        $restore_dir = WPBACKITUP__CONTENT_PATH . '/' . WPBACKITUP__RESTORE_FOLDER;
        $file_system->secure_folder( $restore_dir);

        //Make sure logs folder is secured
        $logs_dir = WPBACKITUP__PLUGIN_PATH .'/logs/';
        $file_system->secure_folder( $logs_dir);

        WPBackItUp_Logger::log($this->cleanup_logname,'**END SECURE FOLDERS**' );
    }

    /**
     * end
     */
    public function wpbackitup_cleanup_end(){
        global $WPBackitup;
        $success=99;

        $this->job->setStatus(WPBackItUp_Job::COMPLETE);

        WPBackItUp_Logger::log_info($this->cleanup_logname,__METHOD__,'Begin');

        $current_datetime = current_time( 'timestamp' );
        $WPBackitup->set_cleanup_lastrun_date($current_datetime);

        $util = new WPBackItUp_Utility($this->cleanup_logname);
        $seconds = $util->timestamp_diff_seconds($this->job->getJobStartTimeTimeStamp(),$this->job->getJobEndTimeTimeStamp());

        $processing_minutes = round($seconds / 60);
        $processing_seconds = $seconds % 60;

        WPBackItUp_Logger::log_info($this->cleanup_logname,__METHOD__,'Script Processing Time:' .$processing_minutes .' Minutes ' .$processing_seconds .' Seconds');

        if (true===$success) WPBackItUp_Logger::log($this->cleanup_logname,'Cleanup completed: SUCCESS');
        if (false===$success) WPBackItUp_Logger::log($this->cleanup_logname,'Cleanup completed: ERROR');
        WPBackItUp_Logger::log($this->cleanup_logname,'*** END JOB ***');
    }


}