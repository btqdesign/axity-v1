<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * WP BackItUp  - Backup View
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */
		require_once( WPBACKITUP__PLUGIN_PATH .'/lib/includes/class-filesystem.php' );

        $namespace = $this->namespace;
		///TRANSLATORS: %s = plugin name.
		/// This string is in the header of one of my pages and looks like this: WP BackItUp Dashboard
		/// Similar to how WordPress uses the word dashboard at the in the left navigation.
        $page_title = sprintf( __("%s Dashboard",'wp-backitup'), $this->friendly_name );

        //Path Variables
        $backup_folder_root = WPBACKITUP__BACKUP_PATH;
		$logs_folder_root = WPBACKITUP__PLUGIN_PATH .'/logs';

        global $WPBackitup, $debug_backup_view_log;
		$debug_backup_view_log='debug_backup_view';

        //trim off build version if 0
        $version = rtrim ($this->version,'.0');

        // get retention number set
        $number_retained_archives = $this->backup_retained_number();

        $wpbackitup_license = new WPBackItUp_License();
        $is_lite_registered = $wpbackitup_license->is_lite_registered();


        //Make sure backup folder exists - should this be in activation?
        $backup_dir = WPBACKITUP__CONTENT_PATH . '/' . WPBACKITUP__BACKUP_FOLDER;
        $backup_folder_exists=false;
        if( !is_dir($backup_dir) ) {
            if (@mkdir($backup_dir, 0755)){
                $backup_folder_exists=true;
            }
        }else{
            $backup_folder_exists=true;
        }

        //Scan backup folder for backups and import

        scan_import_backups($backup_dir);

        $backup_list_size=$number_retained_archives;
        $backup_job_list =  WPBackItUp_Job::get_jobs_by_status(WPBackItUp_Job::BACKUP,array(WPBackItUp_Job::ACTIVE,WPBackItUp_Job::COMPLETE,WPBackItUp_Job::ERROR),$backup_list_size);
        //-------------------------

/**
 * Scan the root backups folder and import any backup archives that exist.
 *
 * @param $backup_dir
 */
function scan_import_backups($backup_dir){
    global $debug_backup_view_log;

    //Cleanup old backups - this can be removed in a few months.
    //Get Zip File List
    $file_system = new WPBackItUp_FileSystem();
    $file_list = $file_system->get_fileonly_list($backup_dir, 'zip|log');

    //If there are zip files then move them into their own folders
    WPBackItUp_Logger::log_info($debug_backup_view_log,__METHOD__,'Files in backup folder: ' .var_export($file_list,true));

    //If files to import
    $job_import_list = array();
    if (null != $file_list && is_array($file_list)) {
        foreach ( $file_list as $file ) {
            //WPBackItUp_Logger::log_info( $debug_backup_view_log, __METHOD__, 'File:' . $file );

            $file_name = substr( basename( $file ), 0, - 4 );

            //strip off the suffix
            //$prefix      = substr( $file_name, 0, 6 );
            $suffix      = null;
            $folder_name = null;
            $extension   = null;

            //split up filename into parts to figure out the array type
            $archive_type=null;
            $name_parts = explode( '-', $file_name );
            if (is_array($name_parts) && count($name_parts)>2){

                end($name_parts);
                $archive_type = prev($name_parts);

                //Only these types are valid WP BackItUp archives
                switch ($archive_type) {
                    case 'others':
                    case 'plugins':
                    case 'themes':
                    case 'uploads':
                    case 'main':
                    case 'backupset':
                        $folder_name = implode("-",array_slice($name_parts, 0, count($name_parts)-2));//grab all but the last two
                        $extension   = substr( $file, - 4 );
                        break;
                }

            }


            //Is this a BackItUp archive
            if ( empty( $folder_name )  || $extension!='.zip' ) {
                WPBackItUp_Logger::log_error( $debug_backup_view_log, __METHOD__, 'File does not appear to be a WPBackItUp backup archive:'. $file );
                unlink( $file );//get rid of it
                continue;
            }

            //Does folder exist
            $backup_archive_folder = WPBACKITUP__BACKUP_PATH . '/' . $folder_name;
            if ( ! is_dir( $backup_archive_folder ) ) {
                if ( ! mkdir( $backup_archive_folder, 0755 ) ) {
                    WPBackItUp_Logger::log_error( $debug_backup_view_log, __METHOD__, 'Folder is not writable:' . $backup_archive_folder );
                    continue;
                } else {
                    WPBackItUp_Logger::log_info( $debug_backup_view_log, __METHOD__, 'Folder created:' . $backup_archive_folder );
                }
            } else {
                WPBackItUp_Logger::log_info( $debug_backup_view_log, __METHOD__, 'Folder exists:' . $backup_archive_folder );
            }


            //move the file to the archive folder
            //will overwrite if exists
            $target_file = $backup_archive_folder . "/" . basename( $file );
            if ( ! rename( $file, $target_file ) ) {
                WPBackItUp_Logger::log_error( $debug_backup_view_log, __METHOD__, 'Cant move zip file to backup folder' );
                continue;
            } else{
                WPBackItUp_Logger::log_info( $debug_backup_view_log, __METHOD__, 'File Imported:' .$file);
            }

            //add folder to job array if doesnt already exist
            if (! array_key_exists($folder_name,$job_import_list)){
                $job_import_list[$folder_name]=$backup_archive_folder;
            }

        }

        WPBackItUp_Logger::log_info( $debug_backup_view_log,__METHOD__, 'Job Import List:' .var_export($job_import_list,true));
        //If any archives were imported then add the job meta
        if (is_array($job_import_list) && count($job_import_list)>0) {

            $ctr=0; //Can take less than a second so need to add 1 to time
            foreach ( $job_import_list as $job_name=>$job_path ) {
                $ctr++;
                //Import into job control table
                WPBackItUp_Logger::log_info( $debug_backup_view_log,__METHOD__, 'Import Backup To Post Table Started' );
                $folder_prefix     = substr( $job_name, 0, 4 );
                $folder_name_parts = explode( '_', $job_name );

                //Import archive
                if ( 'TMP_' != strtoupper( $folder_prefix ) && 'DLT_' != strtoupper( $folder_prefix ) ) {

                    //does COMPLETED job already exist
                    $jobs = WPBackItUp_Job::get_jobs_by_job_name( WPBackItUp_Job::BACKUP, $job_name, WPBackItUp_Job::COMPLETE );

                    if ( false === $jobs ) {
                        WPBackItUp_Logger::log_info( $debug_backup_view_log,__METHOD__, 'Import job' );
                        $job_id = current_time( 'timestamp' ) + $ctr; //Create new job id just in case there are deleted job cnotrol records
                        $job    = WPBackItUp_Job::import_completed_job( $job_name, $job_id, WPBackItUp_Job::BACKUP, $job_id );

                    } else {
                        WPBackItUp_Logger::log_info( $debug_backup_view_log,__METHOD__,'Selecting Existing job.' );
                        $job = is_array( $jobs ) ? current( $jobs ) : false;
                    }

                    WPBackItUp_Logger::log_info( $debug_backup_view_log,__METHOD__, $job );

                    //if job exists - update job meta
                    if ( false !== $job ) {
                        WPBackItUp_Logger::log_info( $debug_backup_view_log,__METHOD__, 'Update Job Meta' );

                        $file_system = new WPBackItUp_FileSystem();
                        $zip_files   = $file_system->get_fileonly_list_with_filesize( $job_path, 'zip' );

                        WPBackItUp_Logger::log_info( $debug_backup_view_log,__METHOD__, $zip_files );

                        $job->setJobMetaValue( 'backup_zip_files', $zip_files ); //list of zip files

                        //If we get this far job was imported successfully
                        WPBackItUp_Logger::log_info( $debug_backup_view_log, __METHOD__, 'Job Imported:' . $folder_name );
                    }
                }
            }
        }

    }

}

?>

<div class="wpbackitup-topbar">
    <a class="wpbackitup-logo" title="WPBackItUp" target="_blank" href="http://www.wpbackitup.com">
    <img height="60" src="<?php echo WPBACKITUP__PLUGIN_URL . "images/wpbackitup-logo-small.png";?>">
    </a>
    <h2>WPBackItUp Backup &amp; Restore </h2>
    <?php // Show the review button only when user has more than two successfull backup or restore 
        if( $this->successful_backup_count() > 2 || $this->successful_restore_count() > 2 ) {
    ?>
            <a target="_blank" href="https://wordpress.org/support/plugin/wp-backitup/reviews/?filter=5" class="button button-hero button-primary wpbiu-button">Review Plugin</a>
    <?php  } 
    ?>
</div>

<?php // Notification Widget
  $admin_notices = get_transient( 'wpbackitup_admin_notices' );

  if( !(false === $admin_notices) && count($admin_notices)>0){

	echo(
	'<div style="overflow: hidden;" class="notice-'.$admin_notices[0]['message_type'].' notice" id="wp-backitup-notification-widget">
	  <div style="float:left;" id="wp-backitup-notification-widget-message" >
	    <p>'. __($admin_notices[0]['message'],'wp-backitup') .'</p>
	  </div>');

	  echo('<div style="float:right;"><p><a id="wp-backitup-notification-widget-close"><i style="float:right" class="fa fa-close"> ' . __('Dismiss', 'wp-backitup') . '</i></a></p></div>
	</div>');
  }

  //Display the install Premium message if not installed.
  if( $wpbackitup_license->is_license_valid() && ! is_plugin_active('wp-backitup-premium/wp-backitup-premium.php')){
		ob_start(); ?>
		<div class="notice notice-error is-dismissible">
			<p>
				<?php printf(__('WPBackItUp Premium must be reinstalled with this release.  Please use this %s link %s to download WPBackItUp Premium.','wp-backitup'),"<a href='https://s3.amazonaws.com/wpbackitup-software/wpbackitup-plugin/wp-backitup-premium1.14.0.zip' target='_blank'>","</a>"); ?>
				<br/><?php printf(__('See our knowledge base %s article %s to find out why you are seeing this message.','wp-backitup'),"<a href='https://wpbackitup.freshdesk.com/support/solutions/articles/12000023567-wpbackitup-premium-must-be-reinstalled-with-this-release' target='_blank'>","</a>"); ?>
			</p>
		</div>
		<?php
		echo ob_get_clean();//flush the buffer
	}
?>


<?php //Add Notification to UI
if (!$backup_folder_exists) {
    echo(
    '<div style="overflow: hidden;" class="error" id="wp-backitup-notification-parent" class="updated">
        <div style="float:left;" id="wp-backitup-notification-message" ><p><strong>' . __('Error','wp-backitup') . ':</strong> ' .
            sprintf(__('Backup folder does not exist. Please contact %s for assistance.', 'wp-backitup'), WPBackItUp_Utility::get_anchor_with_utm(__('support','wp-backitup'),'support','backup+error','no+backup+folder')) );
    echo('</p></div>');

    echo('<div style="float:right;"><p><a id="wp-backitup-notification-close"><i style="float:right" class="fa fa-close"> ' . __('Close', 'wp-backitup') . '</i></a></p></div>
    </div>');
} else{
    echo(
    '<div style="overflow: hidden; display:none" id="wp-backitup-notification-parent" class="updated">
        <div style="float:left;" id="wp-backitup-notification-message" ></div>
        <div style="float:right;"><p><a id="wp-backitup-notification-close"><i style="float:right" class="fa fa-close"> ' . __('Close', 'wp-backitup') . '</i></a></p></div>
    </div>'
    );
}
?>

<script type="text/javascript">var __namespace = "<?php echo($namespace); ?>";</script>
<div class="wrap">
  <h2><?php //echo $page_title; ?></h2>

  <div id="content">

    <!--Manual Backups-->
    <div class="widget">
      <h3><i class="fa fa-cogs"></i> <?php _e('Backup', 'wp-backitup'); ?></h3>
      <p><b><?php _e('Click the backup button to create a zipped backup file of this site\'s database, plugins, themes and settings.','wp-backitup') ?></b></p>
      <p><?php _e('Once your backup file has been created it will appear in the available backups section below. This file may remain on your hosting providers server but we recommend that you download and save it somewhere safe.', 'wp-backitup') ?></p>
      <p> <?php _e('WPBackItUp premium customers can use these backup files to perform an automated restore of their site.', 'wp-backitup') ?></p>
      <p>
          <?php if ($backup_folder_exists) :?>
            <input type="submit" id="backup-button" class="backup-button button-primary" value="<?php _e("Backup", 'wp-backitup') ?>"/>
            <input type="submit" id="cancel-button" class="cancel-button button-secondary cancel-hidden" value="<?php _e("Cancel", 'wp-backitup') ?>"/>
            <img class="backup-icon status-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" />
          <?php endif; ?>
      </p>
      <?php

      echo apply_filters( 'wpbackitup_show_active',
          '<p> * ' . sprintf(__('WPBackItUp lite customers may use these backup files to manually restore their site.  Please visit %s for manual restore instructions.', 'wp-backitup'), WPBackItUp_Utility::get_anchor_with_utm('www.wpbackitup.com','documentation/restore/how-to-manually-restore-your-wordpress-database','backup','manual+restore')) .'</p>'
      ,false);

        if ($this->successful_backup_count()>=10) {
          echo apply_filters( 'wpbackitup_show_active',
          '*' . sprintf(__('Want to schedule your backups? Upgrade to %s and automate your backups today!', 'wp-backitup'), WPBackItUp_Utility::get_anchor_with_utm(__('premium','wp-backitup'),'pricing-purchase','get+license','purchase'))
          ,false
          );
        }
        ?>
    </div>


   <?php do_action('wpbackitup_render_scheduler'); ?>

    <!--Available Backups section-->
    <div class="widget">
      <h3><i class="fa fa-cloud-download"></i> <?php _e('Available Backups', 'wp-backitup'); ?></h3>

    <!--View Log Form-->
    <form id = "viewlog" name = "viewlog" action="<?php echo get_admin_url(),"admin-post.php"; ?>" method="post">
        <input type="hidden" name="action" value="viewlog">
        <input type="hidden" id="backup_name" name="backup_name" value="">
        <?php wp_nonce_field($this->namespace . "-viewlog"); ?>
    </form>


    <form id = "download_backup" name = "download_backup" action="<?php echo get_admin_url(),"admin-post.php"; ?>" method="post">
	    <input type="hidden" name="action" value="download_backup">
	    <input type="hidden" id="backup_file" name="backup_file" value="">
	    <?php wp_nonce_field($this->namespace . "-download_backup"); ?>
    </form>

      <table class="widefat" id="datatable">
          <thead>
            <tr>
                <th><?php _e('Backup', 'wp-backitup') ?></th>
                <th><?php _e('Type', 'wp-backitup') ?></th>
                <th><?php _e('Date', 'wp-backitup') ?></th>
                <th><?php _e('Duration', 'wp-backitup') ?></th>
                <th><?php _e('Status', 'wp-backitup') ?></th>
                <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
        <?php

        if ($backup_job_list!=false)
        {
          $i = 0;
          foreach ($backup_job_list as $job)
          {
	        $backup_name = $job->getJobName();
	        $file_datetime= $job->getJobDate();
          $backup_run_type = $job->getJobRunType();
          
              switch ($job->getJobStatus()) {
                  case WPBackItUp_Job::COMPLETE:
                      $status = __("Success", 'wp-backitup');
                      break;
                  case WPBackItUp_Job::ACTIVE:
                      $status = __("Active", 'wp-backitup');
                      break;
                  default:
                    $status = __("Error", 'wp-backitup');

              }


            $class = $i % 2 == 0 ? 'class="alternate"' : '';
            ?>

            <tr <?php echo $class ?> id="row<?php echo $i; ?>">
              <td data-th="<?php _e('Backup', 'wp-backitup') ?>">
                  <?php
                    $zip_files = $job->getJobMetaValue('backup_zip_files');
                    if(is_array($zip_files) && count($zip_files)>0) { ?>
                        <a href="#TB_inline?width=600&height=550&inlineId=<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', $backup_name) ?>" class="thickbox" title="Download Backup" name="<?php echo $backup_name ?>" data-jobid="<?php echo $job->getJobId(); ?>">
                        <i class="fa fa-download"></i> 
                    <?php echo $backup_name ?>
                  </a>
                  <?php } else {
                      echo $backup_name;
                  } ?>
              </td>

              <td class="word-capitalize" data-th="<?php _e('Type', 'wp-backitup') ?>"><?php echo $backup_run_type ?></td>
              <!--date-->
              <td data-th="<?php _e('Date', 'wp-backitup') ?>"><?php echo $file_datetime ?></td>

              <td data-th="<?php _e('Duration', 'wp-backitup') ?>"><?php echo $job->getJobDurationFormatted() ?></td>

              <td data-th="<?php _e('Status', 'wp-backitup') ?>"><?php echo $status ?></td>

               <td>
               <a href="#" title="Delete Backup" data-id="<?php echo $job->getJobId() ?>" class="deleteRow" id="deleteRow<?php echo $i; ?>"><i class="fa fa-trash-o"></i> <?php _e('Delete', 'wp-backitup') ?></a></td>
            </tr>

            <?php
              $i++;
          }
        }
        else
        {
          echo '<tr id="nofiles"><td colspan="3">' . __('No backup archives found.','wp-backitup'). '</td></tr>';
        }
        ?>
          </tbody>
      </table>  

      <?php
          echo apply_filters( 'wpbackitup_show_active',
              '<p>* ' . sprintf(__('The automated restore feature is only available to WPBackItUp premium customers.  Please visit %s to get WPBackItUp risk free for 30 days.', 'wp-backitup'), WPBackItUp_Utility::get_anchor_with_utm('www.wpbackitup.com','pricing-purchase','available+backups','risk+free')) . '</p>'
              ,false
          );
      ?>
    </div>		

    <div id="status" class="widget">
      <h3><i class="fa fa-check-square-o"></i> <?php _e('Status', 'wp-backitup'); ?></h3>

      <!--default status message-->
      <ul class="default-status">
        <li><?php _e('Nothing to report', 'wp-backitup'); ?></li>
      </ul>

      <!--backup status messages-->
      <ul class="backup-status">
        <li class="preparing"><?php _e('Preparing for backup', 'wp-backitup'); ?>...<span class='status-icon'><img class="preparing-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
        <li class='create_inventory'><?php _e('Creating inventory of files to backup', 'wp-backitup'); ?>...<span class='status-icon'><img class="create_inventory-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
        <li class='exportdb'><?php _e('Exporting database', 'wp-backitup'); ?>...<span class='status-icon'><img class="exportdb-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
        <li class='backupdb'><?php _e('Backing up database', 'wp-backitup'); ?>...<span class='status-icon'><img class="backupdb-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
	    <li class='backup_themes'><?php _e('Backing up themes', 'wp-backitup'); ?>...<span class='status-icon'><img class="backup_themes-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
	    <li class='backup_plugins'><?php _e('Backing up plugins', 'wp-backitup'); ?>...<span class='status-icon'><img class="backup_plugins-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
	    <li class='backup_uploads'><?php _e('Backing up uploads', 'wp-backitup'); ?>...<span class='status-icon'><img class="backup_uploads-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
	    <li class='backup_other'><?php _e('Backing up everything else', 'wp-backitup'); ?>...<span class='status-icon'><img class="backup_other-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
	    <li class='validate_backup'><?php _e('Validating backup', 'wp-backitup'); ?>...<span class='status-icon'><img class="validate_backup-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
        <?php  if (true===$WPBackitup->encrypt_files()) : ?>
              <li class='encrypt'><?php _e('Encrypting sensitive files', 'wp-backitup'); ?>...<span class='status-icon'><img class="encrypt-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
        <?php endif ?>
        <li class='finalize_backup'><?php _e('Finalizing backup', 'wp-backitup'); ?>...<span class='status-icon'><img class="finalize_backup-icon" src="<?php echo WPBACKITUP__PLUGIN_URL . "/images/loader.gif"; ?>" height="16" width="16" /></span><span class='status'><?php _e('Done', 'wp-backitup'); ?></span><span class='fail error'><?php _e('Failed', 'wp-backitup'); ?></span><span class='wpbackitup-warning'><?php _e('Warning', 'wp-backitup'); ?></span></li>
      </ul>

      <!--Error status messages-->
      <ul class="backup-error">
	      <!--Warning PlaceHolder-->
      </ul>

         <!--success messages-->
  	  <ul class="backup-success">
  		  <li class='isa_success'><?php _e('Backup completed successfully', 'wp-backitup'); ?>.</li>
  	  </ul>

        <ul class="backup-warning">
  	      <!--Warning PlaceHolder-->
  	  </ul>
        
        <!--cancelled messages-->
        <ul class="backup-cancelled">
          <li class='isa_cancelled'><?php _e('Backup Cancelled', 'wp-backitup'); ?>.</li>
      </ul>


    </div>

  </div> <!--content-->

  <div id="sidebar">


          <div class="widget">
              <h3 class="promo"><?php _e('Backups', 'wp-backitup'); ?> <span style="float: right"><?php _e('Version ' .$version, 'wp-backitup'); ?></span></h3>
              <?php if ($this->successful_backup_count()<1) : ?>
                  <p><?php _e('Welcome to WPBackItUp!', 'wp-backitup') ?><br/>  <?php _e('The simplest way to backup your WordPress site.', 'wp-backitup') ?></p>
                  <p><?php _e('Getting started is easy, just click the backup button on the left side of this page.', 'wp-backitup') ?></p>
              <?php endif ?>

              <?php if ($this->successful_backup_count()>=1) : ?>
                <p><?php printf(__('Congratulations! You have performed <span style="font-weight:bold;font-size:medium;color: green">%s</span> successful backups.', 'wp-backitup'),$this->successful_backup_count()) ?></p>
                <p><span style="font-weight:bold;font-size:medium"><?php _e('Tips', 'wp-backitup') ?></span>
                   <br/>1)&nbsp;<?php _e('Backup your site at least once per week','wp-backitup') ?>
                   <br/>2)&nbsp;<?php _e('Download all your backups and store them somewhere safe', 'wp-backitup') ?>
                   <br/>3)&nbsp;<?php _e('Verify your backup files are good by taking a look at what\'s inside', 'wp-backitup') ?>
                </p>
              <?php endif ?>

          </div>


      <?php
          if( has_action('wpbackitup_render_license_registration_form')) {
              do_action('wpbackitup_render_license_registration_form');
       } else {
          if (false===$is_lite_registered) { ?>
              <div class="widget">
                  <h3 class="promo"><span><?php _e('Register WPBackItUp', 'wp-backitup'); ?></span></h3>
                  <form action="" method="post" id="<?php echo $namespace; ?>-form">
                      <?php wp_nonce_field($namespace . "-register"); ?>
                      <p><?php _e('Enter your name and email address below to receive <b>special offers</b> and access to our world class <b>support</b> team.  <br />', 'wp-backitup'); ?></p>
                      <input type="text" name="license_name" id="license_name" placeholder="<?php _e('name','wp-backitup')?>" /><br/>
                      <input type="text" name="license_email" id="license_email" placeholder="<?php _e('email address','wp-backitup')?>" /><br/>
                      <div class="submit"><input type="submit" name="Submit" class="button-secondary" value="<?php _e("Register", 'wp-backitup') ?>" /></div>
                  </form>
              </div>
          <?php } ?>
      <?php } ?>

    <!-- Display links widget -->
    <div class="widget">
          <h3 class="promo"><?php _e('Useful Links', 'wp-backitup'); ?></h3>
          <ul>
              <li><?php echo(WPBackItUp_Utility::get_anchor_with_utm(__('Getting Started Video','wp-backitup'),'support/solutions/articles/5000691574-wp-backitup-getting-started-video','useful+links','getting+started+video',WPBACKITUP__SUPPORTSITE_URL))?></li>

              <?php
                  echo apply_filters( 'wpbackitup_show_active',
                  '<li>' .(WPBackItUp_Utility::get_anchor_with_utm(__('Your account','wp-backitup'),'account','useful+links','your+account')) . '</li>'
                  ,true
                  );
              ?>
              
              <li><?php echo(WPBackItUp_Utility::get_anchor_with_utm(__('Website Migration Service','wp-backitup'),'wordpress-site-migration' ,'useful+links','site+migration'))?></li>

              <li><?php echo(WPBackItUp_Utility::get_anchor_with_utm(__('Documentation','wp-backitup'),'support/solutions','useful+links','documentation',WPBACKITUP__SUPPORTSITE_URL))?></li>

              <li><?php echo(WPBackItUp_Utility::get_anchor_with_utm(__('Feature request','wp-backitup'),'contact' ,'useful+links','feature+request'))?></li>
              
              <li><?php echo(WPBackItUp_Utility::get_anchor_with_utm(__('Language Translations','wp-backitup'),'support/solutions/articles/5000675693-wp-backitup-in-your-language' ,'useful+links','translations',WPBACKITUP__SUPPORTSITE_URL))?></li>
              
              <li><?php echo(WPBackItUp_Utility::get_anchor_with_utm(__('Contact','wp-backitup') ,'contact','useful+links','contact'))?></li>

          </ul>
    </div>

  </div><!--Sidebar-->

</div> <!--wrap-->


<span class="hidden" id="popupbox">
  <?php  add_thickbox(); ?>
</span>