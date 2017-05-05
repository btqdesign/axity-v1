<?php if (!defined ('ABSPATH')) die('No direct access allowed');

// Checking safe mode is on/off and set time limit
if( ini_get('safe_mode') ){
   @ini_set('max_execution_time', WPBACKITUP__SCRIPT_TIMEOUT_SECONDS);
}else{
   @set_time_limit(WPBACKITUP__SCRIPT_TIMEOUT_SECONDS);
}

/**
 * WP BackItUp  - Cleanup Job
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

/*** Includes ***/

if( !class_exists( 'WPBackItUp_Utility' ) ) {
	include_once 'class-utility.php';
}


if( !class_exists( 'WPBackItUp_SQL' ) ) {
	include_once 'class-sql.php';
}

// include backup class
if( !class_exists( 'WPBackItUp_Backup' ) ) {
	include_once 'class-backup.php';
}


// include zip class
if( !class_exists( 'WPBackItUp_Zip' ) ) {
	include_once 'class-zip.php';
}

// include file system class
if( !class_exists( 'WPBackItUp_Filesystem' ) ) {
	include_once 'class-filesystem.php';
}

/*** Globals ***/
global $WPBackitup;

global $status_array,$inactive,$active,$complete,$failure,$warning,$success;
$inactive=0;
$active=1;
$complete=2;
$failure=-1;
$warning=-2;
$success=99;

//*************************//
//***   MAIN CODE       ***//
//*************************//

if (! is_object ($current_job)){
	WPBackItUp_Logger::log_error($events_logname,$process_id,'Current job not object:');
	WPBackItUp_Logger::log_error($events_logname,$process_id,var_export($current_job));
	return false;
}

if (! is_object ($current_task)){
	WPBackItUp_Logger::log_error($events_logname,$process_id,'Current task not object');
	WPBackItUp_Logger::log_error($events_logname,$process_id,var_export($current_task));
	return false;
}

WPBackItUp_Logger::log_info($events_logname,$process_id ,'Run task:' .$current_task->getTaskName());


//*************************//
//***   CLEANUP TASKS   ***//
//*************************//

global $cleanup_logname;
$job_name =  $current_job->getJobName();
$cleanup_logname =   sprintf('JobLog_%s',$current_job->getJobName());

global $wp_backup;
$wp_backup = new WPBackItUp_Backup($cleanup_logname,$job_name,$WPBackitup->backup_type);

$backup_retention = $WPBackitup->backup_retained_number();


//TODO: Break this up into multiple tasks

//Run cleanup task
if ('task_scheduled_cleanup'==$current_task->getTaskName()) {

	/* -----------------------------------------------------------------------*/
	/*                  ** INIT **
	/* -----------------------------------------------------------------------*/

	WPBackItUp_Logger::log($cleanup_logname,'***BEGIN JOB***');
	WPBackItUp_Logger::log_sysinfo($cleanup_logname);

	WPBackItUp_Logger::log($cleanup_logname,'Backup Retention:' .$backup_retention);

	//Check License
	WPBackItUp_Logger::log($cleanup_logname,'**CHECK LICENSE**');
	do_action( 'wpbackitup_check_license');
	WPBackItUp_Logger::log($cleanup_logname,'**END CHECK LICENSE**');
	/* -----------------------------------------------------------------------*/


	/* -----------------------------------------------------------------------*/
	/*                  ** PURGE: JOB CONTROL **
	/* -----------------------------------------------------------------------*/

	// Purge post and post meta
	WPBackItUp_Logger::log($cleanup_logname,'**CLEANUP JOB CONTROL RECORDS**' );

	$backup_job_purge_count = WPBackItUp_Job::purge_jobs( WPBackItUp_Job::BACKUP,$backup_retention);
	WPBackItUp_Logger::log($cleanup_logname,'Backup job control records purged:' .$backup_job_purge_count );

	$cleanup_job_purge_count = WPBackItUp_Job::purge_jobs( WPBackItUp_Job::CLEANUP,2);
	WPBackItUp_Logger::log_info($cleanup_logname,__METHOD__,'Cleanup job control records purged:' .$cleanup_job_purge_count );

	WPBackItUp_Logger::log($cleanup_logname,'**END CLEANUP JOB CONTROL RECORDS**' );
	/* -----------------------------------------------------------------------*/


	/* -----------------------------------------------------------------------*/
	/*                  ** PURGE: PREFIXED FOLDERS/FILES **
	/* -----------------------------------------------------------------------*/

	// Purge all folders if they have temp prefix
	WPBackItUp_Logger::log($cleanup_logname,'**CLEAN UNFINISHED BACKUPS**' );

	//cleanup any folders that have the TMP_ prefix
	$wp_backup->cleanup_backups_by_prefix('TMP_');
	WPBackItUp_Logger::log($cleanup_logname,'**END CLEAN UNFINISHED BACKUPS**' );

	WPBackItUp_Logger::log($cleanup_logname,'**CLEAN DELETED BACKUPS**' );
	//cleanup any folders that have the DLT_ prefix
	$wp_backup->cleanup_backups_by_prefix('DLT_');

	WPBackItUp_Logger::log($cleanup_logname,'**END CLEAN DELETED BACKUPS**' );
	/* -----------------------------------------------------------------------*/


	/* -----------------------------------------------------------------------*/
	/*                  ** PURGE: BACKUP/RESTORE FOLDERS/FILES **             */
	/* -----------------------------------------------------------------------*/
	//Purge orphaned backup folders - folders with no job control record
	WPBackItUp_Logger::log($cleanup_logname,'**CLEAN OLD BACKUPS**' );
	$wp_backup->purge_orphaned_backups();
	WPBackItUp_Logger::log($cleanup_logname,'**END CLEAN OLD BACKUPS**' );


	//remove all the old restore folders
	if( class_exists( 'WPBackItUp_Premium_Restore' ) ) {
		WPBackItUp_Logger::log($cleanup_logname,'**CLEAN OLD RESTORES**' );
		$wp_restore = new WPBackItUp_Premium_Restore($cleanup_logname,$job_name,null);
		$wp_restore->delete_restore_folder();
		WPBackItUp_Logger::log($cleanup_logname,'**END CLEAN OLD RESTORES**' );
	}
	/* -----------------------------------------------------------------------*/


	/* -----------------------------------------------------------------------*/
	/*                     ** PURGE OLD FILES **                              */
	/* -----------------------------------------------------------------------*/
	WPBackItUp_Logger::log($cleanup_logname,'**PURGE OLD FILES**' );

	// purge old files from the backup and logs folders - this is NOT for backups
	$wp_backup->purge_old_files();

	WPBackItUp_Logger::log($cleanup_logname,'**END PURGE OLD FILES**' );
	/* -----------------------------------------------------------------------*/



	/* -----------------------------------------------------------------------*/
	/*                     ** SECURE FOLDERS **                               */
	/* -----------------------------------------------------------------------*/
	WPBackItUp_Logger::log($cleanup_logname,'**SECURE FOLDERS**' );
	$file_system = new WPBackItUp_FileSystem($cleanup_logname);

	//Make sure backup folder is secured
	$backup_dir = WPBACKITUP__CONTENT_PATH . '/' . WPBACKITUP__BACKUP_FOLDER;
	$file_system->secure_folder( $backup_dir);

	//--Check restore folder folders
	$restore_dir = WPBACKITUP__CONTENT_PATH . '/' . WPBACKITUP__RESTORE_FOLDER;
	$file_system->secure_folder( $restore_dir);

	//Make sure logs folder is secured
	$logs_dir = WPBACKITUP__PLUGIN_PATH .'/logs/';
	$file_system->secure_folder( $logs_dir);

	WPBackItUp_Logger::log($cleanup_logname,'**END SECURE FOLDERS**' );
	/* -----------------------------------------------------------------------*/

	$current_task->setStatus(WPBackItUp_Job_Task::COMPLETE);
}

$current_job->setStatus(WPBackItUp_Job::COMPLETE);
end_job(null,true);
return true;

//*** END SCHEDULED TASKS ***//

/******************/
/*** Functions ***/
/******************/
function end_job($err=null, $success=null){
	global $WPBackitup, $cleanup_logname, $current_job;
	WPBackItUp_Logger::log_info($cleanup_logname,__METHOD__,'Begin');

	$current_datetime = current_time( 'timestamp' );
	$WPBackitup->set_cleanup_lastrun_date($current_datetime);

	$util = new WPBackItUp_Utility($cleanup_logname);
	$seconds = $util->timestamp_diff_seconds($current_job->getJobStartTimeTimeStamp(),$current_job->getJobEndTimeTimeStamp());

	$processing_minutes = round($seconds / 60);
	$processing_seconds = $seconds % 60;

	WPBackItUp_Logger::log_info($cleanup_logname,__METHOD__,'Script Processing Time:' .$processing_minutes .' Minutes ' .$processing_seconds .' Seconds');

	if (true===$success) WPBackItUp_Logger::log($cleanup_logname,'Cleanup completed: SUCCESS');
	if (false===$success) WPBackItUp_Logger::log($cleanup_logname,'Cleanup completed: ERROR');
	WPBackItUp_Logger::log($cleanup_logname,'*** END JOB ***');

	echo('cleanup has completed');
}

