<?php if (!defined ('ABSPATH')) die('No direct access allowed');

// no PHP timeout for running updates
if( ini_get('safe_mode') ){
   @ini_set('max_execution_time', 0);
}else{
   @set_time_limit(0);
}

if( !class_exists( 'WPBackItUp_Filesystem' ) ) {
	include_once 'class-filesystem.php';
}


/**
 * Run the incremental updates one by one.
 *
 * For example, if the current DB version is 3, and the target DB version is 6,
 * this function will execute update routines if they exist:
 *  - wpbackitup_update_routine_4()
 *  - wpbackitup_routine_5()
 *  - wpbackitup_update_routine_6()
 *
 */
function wpbackitup_update_database() {
	// no PHP timeout for running updates
	set_time_limit( 0 );

	$log_name = 'debug_DATABASE_Upgrade';

	// this is the current database schema version number
	$current_db_ver = get_option( 'wp-backitup_db_version',0 );

	// this is the target version that we need to reach
	$target_db_ver = WPBackitup_Admin::DB_VERSION;

	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Running database upgrade routines.');
	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Current DB version:'.$current_db_ver );
	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Target DB version:'.$target_db_ver );

	//If current version = 0 then this is an install so just create the custom tables
	if ($current_db_ver==0){
		WPBackItUp_Logger::log_info($log_name,__METHOD__, 'New Install.');

		wpbackitup_create_custom_tables($log_name);

		wpbackitup_scan_import_backups($log_name);

		update_option( 'wp-backitup_db_version', $target_db_ver );
		WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Updated Db version in settings:'. $target_db_ver );

		return;
	}


	// run update routines one by one until the current version number
	// reaches the target version number
	while ( $current_db_ver < $target_db_ver ) {

		// increment the current db_ver by one
		$current_db_ver ++;
		WPBackItUp_Logger::log_info($log_name,__METHOD__, sprintf('Upgrading database to version %s',$current_db_ver) );

		// each db version will require a separate update function
		// for example, for db_ver 3, the function name should be solis_update_routine_3
		$func = "wpbackitup_update_database_routine_{$current_db_ver}";
		if ( function_exists( $func ) ) {
			WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Running update routine:' .$func);
			call_user_func( $func ,$log_name);
			WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Update Routine complete:' .$func);
		} else{
			WPBackItUp_Logger::log_info($log_name,__METHOD__,  'No updates for this version.');
		}

		// update the option in the database, so that this process can always
		// pick up where it left off
		update_option( 'wp-backitup_db_version', $current_db_ver );
		WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Updated Db version in settings:'. $current_db_ver );
	}

}


/**
 * - Introduce JOBS table
 * DB version 0 to 1 update
 * - routine no longer needed - removed with DB v4 1.12.1
 *
 */
//function wpbackitup_update_database_routine_1($log_name){
//	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Begin upgrade database to V1');
//	global $wpdb;
//
//	$charset_collate = 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'; //default value
//	if (method_exists($wpdb,'get_charset_collate')){
//		$charset_collate = $wpdb->get_charset_collate();
//	}
//
//	$table_name = $wpdb->prefix . "wpbackitup_job";
//
//	$sql = "CREATE TABLE $table_name (
//			  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
//			  job_id bigint(20) NOT NULL,
//			  batch_id bigint(20) DEFAULT NULL,
//			  group_id varchar(15) DEFAULT NULL,
//			  item longtext,
//			  size_kb bigint(20) DEFAULT NULL,
//			  retry_count int(11) NOT NULL DEFAULT '0',
//			  status int(11) NOT NULL DEFAULT '0',
//			  create_date datetime DEFAULT NULL,
//			  update_date datetime DEFAULT NULL,
//			  record_type varchar(1) NOT NULL DEFAULT 'I',
//			  PRIMARY KEY (id)
//			) $charset_collate;";
//
//	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//	dbDelta( $sql );
//	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Begin upgrade database to V1');
//}

/**
 * DB version 1 to 2 update
 *  - migrate backups from file system to post table
 * - routine no longer needed - removed with DB v4 1.12.1
 * - migrate post table job removal to cleanup routine
 *
 */
//function wpbackitup_update_database_routine_2() {
//	error_log('Begin upgrade database to V2');
//	$backup_folder_root = WPBACKITUP__BACKUP_PATH;
//
//	//get all the folders in the backup path
//	$work_folder_list = glob($backup_folder_root. '/*', GLOB_ONLYDIR);
//	error_log(var_export($work_folder_list,true));
//
//	//MUST register status or query will not work - activate is called before init
////	register_post_status( 'complete', array(
////		'public'                    => false,
////		'exclude_from_search'       => false,
////		'show_in_admin_all_list'    => false,
////		'show_in_admin_status_list' => true,
////	));
//
//	//if not TMP and DLT add to post table
//	foreach($work_folder_list as $folder) {
//		$folder_name= basename($folder);
//		$folder_prefix = substr($folder_name,0,4);
//		$folder_name_parts = explode('_',$folder_name);
//
//		if ('TMP_'!= strtoupper($folder_prefix) && 'DLT_'!= strtoupper($folder_prefix)){
//			//Add to post table
//			$external_id = end($folder_name_parts);
//
//			//does job already exist
//			$jobs = WPBackItUp_Job::get_jobs_by_external_id(WPBackItUp_Job::BACKUP,$external_id);
//			if (false===$jobs){
//				$job = WPBackItUp_Job::import_completed_job($folder_name,$external_id,WPBackItUp_Job::BACKUP,$external_id);
//				if (false!== $job){
//					$file_system = new WPBackItUp_FileSystem();
//					$zip_files = $file_system->get_fileonly_list($folder, 'zip');
//					// Save all backup job meta with size
//					$backup_zip_files_container = array();
//					foreach($zip_files as $zip_file){
//						$backup_zip_files_container[$zip_file] = WPBackItUp_FileSystem::format_file_size(filesize($zip_file));
//					}
//					$job->setJobMetaValue('backup_zip_files' , $backup_zip_files_container ); //list of zip files
//					error_log('Job Imported:' .$folder_name);
//				}
//			}else {
//				error_log('Job already exists, not imported:' .$folder_name);
//			}
//		}
//	}


/**
 * DB version 2 to 3 update
 *
 */
function wpbackitup_update_database_routine_3($log_name) {
	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Begin upgrade database to V3');


	//Drop & Create the custom job tables
	wpbackitup_create_custom_tables($log_name);

	//scan the host for backups - import them into job tables
	wpbackitup_scan_import_backups($log_name);

	WPBackItUp_Logger::log_info($log_name,__METHOD__,'End upgrade database to V3');
}

/**
 * DB version 3 to 4 update
 *  - original job table job name column was varchar(45) - this will update to varchar(100)
 *
 */
function wpbackitup_update_database_routine_4($log_name) {
	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Begin upgrade database to V4' );
	global $wpdb;

	$job_control_table  = $wpdb->prefix . "wpbackitup_job_control";

	$sql = "ALTER TABLE $job_control_table
			CHANGE COLUMN job_name job_name VARCHAR(100)
			";
	WPBackItUp_Logger::log_info($log_name,__METHOD__,'UPDATE SQL:'. $sql);

	$wpdb_result = $wpdb->query($sql);
	$last_error = $wpdb->last_error;
	if ($wpdb_result === FALSE && !empty($last_error)) {
		WPBackItUp_Logger::log_error($log_name,__METHOD__,'Last Error:' .var_export( $last_error,true ) );
	}else{
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Job Name column updated successfully');
	}

	//scan the host for backups - import them into job tables
	wpbackitup_scan_import_backups($log_name); //needed because update 3 might have failed

	WPBackItUp_Logger::log_info($log_name,__METHOD__,'END upgrade database to V4');
}


/**
 * Drop & Create the current version of the WP BAckItUp Job tables
 *
 */
function wpbackitup_create_custom_tables($log_name){
	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$charset_collate = 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'; //default value
	if (method_exists($wpdb,'get_charset_collate')){
		$charset_collate = $wpdb->get_charset_collate();
	}

	$old_job_table = $wpdb->prefix . "wpbackitup_job";

	$job_control_table  = $wpdb->prefix . "wpbackitup_job_control";
	$job_tasks_table    = $wpdb->prefix . "wpbackitup_job_tasks";
	$job_items_table    = $wpdb->prefix . "wpbackitup_job_items";

	WPBackItUp_Logger::log($log_name,'*** DROP JOB TABLES ***');

	//drop the original job table
	$sql = "DROP TABLE IF EXISTS $old_job_table;";
	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Drop OLD Job Table:' .$sql);
	$wpdb_result = $wpdb->query($sql);
	$last_error = $wpdb->last_error;
	if ($wpdb_result === FALSE && !empty($last_error)) {
		WPBackItUp_Logger::log_error($log_name,__METHOD__,'Last Error:' .var_export( $last_error,true ) );
	}else{
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'OLD Job Table Dropped successfully');
	}


	//drop the existing job table
	$sql = "DROP TABLE IF EXISTS $job_control_table;";
	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Drop Job Table:' .$sql);
	$wpdb_result = $wpdb->query($sql);
	$last_error = $wpdb->last_error;
	if ($wpdb_result === FALSE && !empty($last_error)) {
		WPBackItUp_Logger::log_error($log_name,__METHOD__,'Last Error:' .var_export( $last_error,true ) );
	}else{
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Job Table Dropped successfully');
	}

	//drop the existing Task table
	$sql = "DROP TABLE IF EXISTS $job_tasks_table;";
	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Drop Task Table:' .$sql);
	$wpdb_result = $wpdb->query($sql);
	$last_error = $wpdb->last_error;
	if ($wpdb_result === FALSE && !empty($last_error)) {
		WPBackItUp_Logger::log_error($log_name,__METHOD__,'Last Error:' .var_export( $last_error,true ) );
	}else{
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Task Table Dropped successfully');
	}

	//drop the existing item table
	$sql = "DROP TABLE IF EXISTS $job_items_table;";
	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Drop Job Table:' .$sql);
	$wpdb_result = $wpdb->query($sql);
	$last_error = $wpdb->last_error;
	if ($wpdb_result === FALSE && !empty($last_error)) {
		WPBackItUp_Logger::log_error($log_name,__METHOD__,'Last Error:' .var_export( $last_error,true ) );
	}else{
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Item Table Dropped successfully');
	}

	WPBackItUp_Logger::log($log_name,'*** END DROP JOB TABLES ***');

	WPBackItUp_Logger::log($log_name,'*** CREATE JOB TABLES ***');

	$sql = "CREATE TABLE $job_control_table (
			job_id bigint(20) NOT NULL,
  			job_type varchar(45) NOT NULL,
  			job_run_type varchar(45) NOT NULL,
  			job_name varchar(100) NOT NULL,
			job_meta longtext,
			job_start datetime DEFAULT NULL,
			job_end datetime DEFAULT NULL,
			create_date datetime DEFAULT NULL,
			update_date datetime DEFAULT NULL,
			job_status varchar(10) NOT NULL,
			PRIMARY KEY  (job_id)
			) $charset_collate;";
	dbDelta( $sql );
	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Table Created:'. $job_control_table);

	$sql = "CREATE TABLE $job_tasks_table (
			 task_id bigint(20) NOT NULL AUTO_INCREMENT,
			 job_id bigint(20) NOT NULL,
			 task_name varchar(45) NOT NULL,
			 task_meta longtext ,
			 task_start datetime DEFAULT NULL,
			 task_end datetime DEFAULT NULL,
			 create_date datetime DEFAULT NULL,
			 update_date datetime DEFAULT NULL,
			 allocation_id bigint(20) DEFAULT NULL,
			 retry_count int(11) NOT NULL DEFAULT '0',
			 error int(11) NOT NULL DEFAULT '0',
			 task_status varchar(10) NOT NULL,
			 PRIMARY KEY  (task_id)
			) $charset_collate;";
	dbDelta( $sql );
	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Table Created:'. $job_tasks_table);

	$sql = "CREATE TABLE $job_items_table (
		  item_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  job_id bigint(20) NOT NULL,
		  batch_id bigint(20) DEFAULT NULL,
		  group_id varchar(15) DEFAULT NULL,
		  item longtext,
		  size_kb bigint(20) DEFAULT NULL,
		  retry_count int(11) NOT NULL DEFAULT '0',
		  offset bigint(20) NOT NULL DEFAULT '0',
		  create_date datetime DEFAULT NULL,
		  update_date datetime DEFAULT NULL,
		  record_type varchar(1) NOT NULL DEFAULT 'I',
		  item_status varchar(10) NOT NULL,
		  PRIMARY KEY  (item_id)
			) $charset_collate;";
	dbDelta( $sql );
	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Table Created:'. $job_items_table);

	WPBackItUp_Logger::log($log_name,'*** END CREATE JOB TABLES ***');
}

/**
 * Scan and import any backups that are on the host
 *
 * @param $log_name
 */
function  wpbackitup_scan_import_backups($log_name){
	//Import the backups into the custom tables
	WPBackItUp_Logger::log($log_name,'***SCAN & IMPORT BACKUPS ***');

	//get all the folders in the backup path
	$backup_folder_root = WPBACKITUP__BACKUP_PATH;
	$backup_folders = glob($backup_folder_root. '/*', GLOB_ONLYDIR);
	WPBackItUp_Logger::log_info($log_name,__METHOD__,'Backup Sets Found:' .var_export($backup_folders,true));

	//if not TMP and DLT add to job table
	foreach($backup_folders as $folder) {
		WPBackItUp_Logger::log_info($log_name,__METHOD__,'Importing:' .$folder);

		$folder_name= basename($folder);
		$folder_prefix = substr($folder_name,0,4);
		$folder_name_parts = explode('_',$folder_name);

		//Dont import TMP or DLT folders - leave cleanup for job
		if ('TMP_'!= strtoupper($folder_prefix) && 'DLT_'!= strtoupper($folder_prefix)){
			//Add to job table
			$job_id = end($folder_name_parts); //grab the  last element

			//does job already exist
			$jobs = WPBackItUp_Job::get_job_by_id($job_id);
			if (false===$jobs){

				//are there any zip files in this folder
				$file_system = new WPBackItUp_FileSystem();
				$zip_files = $file_system->get_fileonly_list_with_filesize($folder, 'zip');

				//If no zip files found then skip this folder
				if (! is_array($zip_files) || count($zip_files)<=0){
					WPBackItUp_Logger::log_info($log_name,__METHOD__,'No backups found in folder:' .$folder);
					continue;
				}

				//Check for existing job in job control table
				$job = WPBackItUp_Job::import_completed_job($folder_name,$job_id,WPBackItUp_Job::BACKUP);
				if (false!== $job){
					//remove the log zip files
					foreach($zip_files  as $key=>$query) {
						$pos = strpos($key,'logs_');
						if ($pos!==false) unset($zip_files[$key]);;
					}

					WPBackItUp_Logger::log_info($log_name,__METHOD__,'Update Job Meta:' .var_export($zip_files,true));

					//update post meta
					$job->setJobMetaValue('backup_zip_files' , $zip_files ); //list of zip files
					WPBackItUp_Logger::log_info($log_name,__METHOD__,'Job Imported:' .$folder_name);
				}
			}else {
				WPBackItUp_Logger::log_info($log_name,__METHOD__,'Job already exists, not imported:' .$folder_name);
			}
		} else{
			WPBackItUp_Logger::log_info($log_name,__METHOD__,'Backup was not imported:' .$folder);
		}
	}

	WPBackItUp_Logger::log($log_name,'***END SCAN & IMPORT BACKUPS ***');

}