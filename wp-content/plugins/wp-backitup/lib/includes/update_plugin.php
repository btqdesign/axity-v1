<?php if (!defined ('ABSPATH')) die('No direct access allowed');

// no PHP timeout for running updates
if( ini_get('safe_mode') ){
   @ini_set('max_execution_time', 0);
}else{
   @set_time_limit(0);
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
function wpbackitup_update_plugin() {

	$log_name = 'debug_PLUGIN_Upgrade';
	
	// this is the current database schema version number
	$current_plugin_major_ver = get_option( 'wp-backitup_major_version',0 );
	$current_plugin_minor_ver = get_option( 'wp-backitup_minor_version',0 );

	// this is the target version that we need to reach
	$target_plugin_major_ver = WPBACKITUP__MAJOR_VERSION;
	$target_plugin_minor_ver = WPBACKITUP__MINOR_VERSION;

	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Current Plugin Major Version:' . $current_plugin_major_ver );
	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Current Plugin Minor Version:' . $current_plugin_minor_ver );
	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Target Plugin Minor Version:' . $target_plugin_minor_ver );

	//fire to help troubleshoot upgrade issues
	$ut = new WPBackItUp_Usage();
	$ut->ut_event(true,true);

	//If current version = 0 then this is an install
	if ($current_plugin_major_ver==0){
		WPBackItUp_Logger::log_info($log_name,__METHOD__, 'New Install.');

		update_option( 'wp-backitup_major_version', $target_plugin_major_ver );
		update_option( 'wp-backitup_minor_version', $target_plugin_minor_ver );
		return;
	}

	//If the major version are the same then just run the minor updates
	if ($current_plugin_major_ver == $target_plugin_major_ver ){

		//Now run all the minor updates in the current version
		while ( $current_plugin_minor_ver < $target_plugin_minor_ver ) {

			$current_plugin_minor_ver ++;
			WPBackItUp_Logger::log_info($log_name,__METHOD__, sprintf('Upgrading plugin to MINOR version %s',$current_plugin_minor_ver) );

			$func = "wpbackitup_update_plugin_minor_routine_{$current_plugin_major_ver}_{$current_plugin_minor_ver}";
			if ( function_exists( $func ) ) {
				WPBackItUp_Logger::log_info($log_name,__METHOD__,  'Running update routine:' . $func );
				call_user_func( $func,$log_name );
				WPBackItUp_Logger::log_info($log_name,__METHOD__,  'Update Routine complete:' . $func );
			} else{
				WPBackItUp_Logger::log_info($log_name,__METHOD__,  'No updates for this version.');
			}

			update_option( 'wp-backitup_minor_version', $current_plugin_minor_ver );
			WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Updated plugin MINOR version in settings:'. $current_plugin_minor_ver );
		}
		return;
	}

	//Major Version change so run all the major and up to 50 minor upgrades
	if ($current_plugin_major_ver < $target_plugin_major_ver ) {

		//Major updates require routine

		// run update routines one by one until the current version number
		// reaches the target version number
		while ( $current_plugin_major_ver < $target_plugin_major_ver ) {

			// increment the current db_ver by one
			$current_plugin_major_ver ++;

			WPBackItUp_Logger::log_info($log_name,__METHOD__, sprintf('Upgrading plugin to MAJOR version %s',$current_plugin_major_ver) );

			// each version will require a separate update function
			// for example, for ver 3, the function name should be solis_update_routine_3
			$func = "wpbackitup_update_plugin_major_routine_{$current_plugin_major_ver}";
			if ( function_exists( $func ) ) {
				WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Running update routine:' . $func );
				call_user_func( $func,$log_name );
				WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Update Routine complete:' . $func );
			} else{
				WPBackItUp_Logger::log_info($log_name,__METHOD__,  'No updates for this version.');
			}

			// update the option in the database, so that this process can always
			// pick up where it left off
			update_option( 'wp-backitup_major_version', $current_plugin_major_ver );
			WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Updated plugin MAJOR version in settings:'. $current_plugin_major_ver );

			WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Check for MINOR version upgrades');

			//Check for up to 50 minor releases in this version
			for ( $i = 1; $i <= 50; $i ++ ) {
				$func = "wpbackitup_update_plugin_minor_routine_{$current_plugin_major_ver}_{$i}";
				if ( function_exists( $func ) ) {
					WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Running update routine:' . $func );
					call_user_func( $func );
					WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Update Routine complete:' . $func );
				} else{
					WPBackItUp_Logger::log_info($log_name,__METHOD__,  'No updates for this version:' .$i);
				}

			}

			update_option( 'wp-backitup_minor_version', $target_plugin_minor_ver );
			WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Updated plugin MINOR version in settings:'. $target_plugin_minor_ver );

		}
	}

}


/**
 *  Major plugin update 0 to 1
 */
function wpbackitup_update_plugin_major_routine_1($log_name){
	// dont think this will ever run
	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Begin upgrade plugin to V1' );

	//Need to reset the batch size for this release
	$batch_size = get_option('wp-backitup_backup_batch_size');
	if ($batch_size<100){
		delete_option('wp-backitup_backup_batch_size');
	}

	//Migrate old properties - can be removed in a few releases
	$old_lite_name = get_option('wp-backitup_lite_registration_first_name');
	if ($old_lite_name) {
		update_option('wp-backitup_license_customer_name',$old_lite_name);
		delete_option('wp-backitup_lite_registration_first_name');
	}

	$old_lite_email = get_option('wp-backitup_lite_registration_email');
	if ($old_lite_email) {
		update_option('wp-backitup_license_customer_email',$old_lite_email);
		delete_option('wp-backitup_lite_registration_email');
	}

	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'End upgrade plugin to V1' );
}

/*----------------------------------------------*/
/*          MINOR VERSION UPDATES               */
/*----------------------------------------------*/


/**
 *  Minor Version update 1.12
 */
function wpbackitup_update_plugin_minor_routine_1_12($log_name){
	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Begin upgrade plugin to V1.12' );

	//Update the db tables batch size - changes usage in this version
	$settings_tables_batch_size = get_option('wp-backitup_backup_dbtables_batch_size');
	if ($settings_tables_batch_size<10000){
		delete_option('wp-backitup_backup_dbtables_batch_size');
		WPBackItUp_Logger::log_info($log_name,__METHOD__, 'dbtables_batch_size removed');
	}

	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'End upgrade plugin to V1.12' );
}

///**
// *  Minor Version update 1.14
// */
//function wpbackitup_update_plugin_minor_routine_1_24($log_name){
//	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Begin upgrade plugin to V1.24' );
//
//	if (false===WPBackItUp_Utility::get_option( 'license_product_id') ||
//		empty(WPBackItUp_Utility::get_option( 'license_product_id'))&&
//	    WPBackItUp_Utility::get_option( 'license_type',-1)>0
//	){
//		WPBackItUp_Utility::set_option( 'license_product_id','679');
//		WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Product Id added to license.' );
//	} else {
//		WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Product Id NOT added to license.' );
//	}
//
//
//	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'End upgrade plugin to V1.24' );
//}

/**
 *  Minor Version update 1.25
 */
function wpbackitup_update_plugin_minor_routine_1_25($log_name){
	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Begin upgrade plugin to V1.24' );

	$product_id = get_option( 'wp-backitup_license_product_id',false );
	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Product Id:' .$product_id);
	$license_type = get_option( 'wp-backitup_license_type',-1);
	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'License Type:' .$license_type);

	//If no product ID and license type is premium
	if ( (false===$product_id || empty($product_id)) && $license_type>0)	{
		update_option('wp-backitup_license_product_id','679');
		WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Product Id added to license.' );
	} else {
		WPBackItUp_Logger::log_info($log_name,__METHOD__, 'Product Id NOT added to license.' );
	}


	WPBackItUp_Logger::log_info($log_name,__METHOD__, 'End upgrade plugin to V1.24' );
}


