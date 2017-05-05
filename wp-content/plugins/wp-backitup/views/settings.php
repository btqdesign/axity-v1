<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * WP BackItUp  - Settings View
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

    $namespace = $this->namespace;
    $page_title = sprintf(__('%s Settings', 'wp-backitup'), $this->friendly_name );

    $wpbackitup_single_file_backupset = $this->single_file_backupset();
    $wpbackitup_remove_supporting_zip_files = $this->is_remove_supporting_zip_files();
    $wpbackitup_single_file_db = $this->single_file_db();
    $wpbackitup_zip_max_size = $this->max_zip_size();
    $wpbackitup_max_timeout = $this->max_timeout();


    $backup_dbtables_batch_size=$this->backup_dbtables_batch_size();
    $backup_sql_merge_batch_size=$this->backup_sql_merge_batch_size();
    $backup_sql_batch_size=$this->backup_sql_batch_size();
    $backup_plugins_batch_size=$this->backup_plugins_batch_size();
    $backup_themes_batch_size=$this->backup_themes_batch_size();
    $backup_uploads_batch_size=$this->backup_uploads_batch_size();
    $backup_others_batch_size=$this->backup_others_batch_size();
    $wpbackitup_delete_all = $this->delete_all();

	// Filters
	$backup_plugins_filter = $this->backup_plugins_filter();
	$backup_themes_filter = $this->backup_themes_filter();
	$backup_uploads_filter = $this->backup_uploads_filter();
	$backup_others_filter = $this->backup_others_filter();

	$backup_dbtables_filterable = $this->backup_dbtables_filterable();
	$backup_dbtables_filter_list = $this->backup_dbtables_filter_list();

    $disabled='';

?> 

<?php if (!empty($_GET["update"]) && '1' == $_GET["update"]) : ?>
  <div class="updated">
    <p><?php _e( 'Settings updated successfully!', 'wp-backitup' ); ?></p>
  </div>
<?php endif; ?>

<div class="wrap" id="wpbackitup-settings">
  <h2><?php echo $page_title; ?></h2>
  <div id="content">


        <!-- Display Settings widget -->
        <form action="<?php echo get_admin_url(),"admin-post.php"; ?>" method="post" id="<?php echo $namespace; ?>-form">
          <?php wp_nonce_field($namespace . "-update-options"); ?>
          <div class="widget">
	            <h3 class="promo"><i class="fa fa-envelope"></i> <?php _e('Email Notifications', 'wp-backitup')  ?></h3>
	            <p><b><?php _e('Please enter your email address if you would like to receive backup email notifications.', 'wp-backitup') ?></b></p>
	            <p><?php _e('Backup email notifications will be sent for every backup and will contain status information related to the backup.', 'wp-backitup'); ?></p>
	            <p><input type="text" id="wpbiu-notification-emails" name="data[notification_email]" value="<?php echo $this->notification_email(); ?>" size="30"></p>
	            <div class="submit"><input <?php echo($disabled) ; ?> type="submit" name="Save_Email" class="button-primary" value="<?php _e("Save", 'wp-backitup') ?>" />
            </div>
            <?php
            if ( false !== ( $msg = get_transient('settings-error-email') ) && $msg)
            {
              echo '<div class="error wpbiu-transient-msg">'.$msg.'</div>';
              delete_transient('settings-error-email');
            }
            ?>
            <!-- frontend validation -->
            <div class="error hidden" id="wpbiu-invalid-email">Please enter valid email</div>
          </div>


      <div class="widget">
        <h3 class="promo"><i class="fa fa-trash-o"></i> <?php _e('Backup Retention', 'wp-backitup') ?></h3>
        <p><b><?php _e('Enter the number of backup archives that you would like to remain on the server.', 'wp-backitup') ?></b></p>
        <p><?php _e('Many hosts limit the amount of space that you can take up on their servers. This option tells WPBackItUp the maximum number of backup archives that should remain on your hosts server.  Don\'t worry, we will always remove the oldest backup archives first.', 'wp-backitup') ?></p>
        <p><input type="text" name="data[backup_retained_number]" value="<?php echo $this->backup_retained_number(); ?>" size="4"></p>
        <div class="submit"><input type="submit" name="Save_Retention" class="button-primary" value="<?php _e("Save", 'wp-backitup') ?>" /></div>
        <?php
        if ( false !== ( $msg = get_transient('settings-error-number') ) && $msg)
        {
          echo '<p class="error">'.$msg.'</p>';
          delete_transient('settings-error-number');
        }

        if ( false !== ( $msg = get_transient('settings-license-error') ) && $msg)
        {
          echo '<p class="error">'.$msg.'</p>';
          delete_transient('settings-license-error');
        }
        ?>
      </div>

      <div class="widget">
        <h3 class="promo"><i class="fa fa-file-text-o"></i> <?php _e('Logging?', 'wp-backitup') ?></h3>
        <p><b><?php _e('Turn on WPBackItUp logging.', 'wp-backitup'); ?></b></p>
        <p><?php _e('This option should only be turned on by advanced users or when troubleshooting issues with WPBackItUp support.', 'wp-backitup'); ?></p>
        <p><input type="radio" name="data[logging]" value="true" <?php if ($this->logging()) echo 'checked'; ?>> <label><?php _e('Yes', 'wp-backitup'); ?></label></p>
        <p><input type="radio" name="data[logging]" value="false" <?php if (!$this->logging()) echo 'checked'; ?>> <label><?php _e('No', 'wp-backitup'); ?></label></p>

        <p class="submit"><input type="submit" name="Save_Logging" class="button-primary" value="<?php _e("Save", 'wp-backitup') ?>" /></p>
      </div>

      <?php do_action('wpbackitup_render_premium_settings'); ?>

      <div class="widget">
          <h3 class="promo"><i class="fa fa-database"></i> <?php _e('Single File Database Export (db)', 'wp-backitup') ?></h3>
          <p><input type="checkbox" id="wpbackitup_single_file_db" value="1" name="data[single_file_db]" <?php if ($wpbackitup_single_file_db) echo 'checked'; ?>>
              <label for="wpbackitup_single_file_db"><?php _e('Check this box if you would like WPBackItUp to export your database into a single db file.', 'wp-backitup') ?></label></p>
          <p><?php _e('When this setting is turned on WPBackItUp will attempt to create a single file that contains your entire database.  This option may not be possible with some hosting providers.  This setting will be turned off automatically if WPBackItUp is unable to complete this step for any reason.', 'wp-backitup') ?></p>
          <div class="submit"><input type="submit" name="Save_Single_File_Db" class="button-primary" value="<?php _e("Save", 'wp-backitup') ?>" /></div>
      </div>

      <div class="widget">
          <h3 class="promo"><i class="fa fa-file-archive-o"></i> <?php _e('Maximum Zip File Size', 'wp-backitup') ?></h3>
          <div class="wpbiu-select-box">
            <p><b><label for="wpbackitup-max-zip-size"><?php _e('Select your maximum zip file size.', 'wp-backitup') ?></label></b></p>
              <p><?php _e('Some hosting providers do not allow large zip files so if you are encountering backup errors then reducing this setting may help. Please note that this setting will impact performance so we recommend it is set as high as possible.', 'wp-backitup') ?></p>
              <select class="form-control" id="wpbackitup-max-zip-size" name="data[backup_zip_max_size]">
              <option <?php if($wpbackitup_zip_max_size=='104857600') echo 'selected'; ?> value="104857600"><?php _e('100MB', 'wp-backitup') ?></option>
              <option <?php if($wpbackitup_zip_max_size=='209715200') echo 'selected'; ?> value="209715200"><?php _e('200MB', 'wp-backitup') ?></option>
              <option <?php if($wpbackitup_zip_max_size=='314572800') echo 'selected'; ?> value="314572800"><?php _e('300MB', 'wp-backitup') ?></option>
              <option <?php if($wpbackitup_zip_max_size=='419430400') echo 'selected'; ?> value="419430400"><?php _e('400MB', 'wp-backitup') ?></option>
              <option <?php if($wpbackitup_zip_max_size=='524288000') echo 'selected'; ?> value="524288000"><?php _e('500MB', 'wp-backitup') ?></option>
            </select>
          </div>
          <div class="submit"><input type="submit" name="Save_max_zip_size" class="button-primary" value="<?php _e("Save", 'wp-backitup') ?>" /></div>
      </div>

      <div class="widget">
          <h3 class="promo"><i class="fa fa-hourglass-end"></i> <?php _e('Task Timeout', 'wp-backitup') ?></h3>
          <div class="wpbiu-select-box">
            <p><b><label for="wpbackitup-max-zip-size"><?php _e('Select how long WPBackItUp should wait for tasks to complete.', 'wp-backitup') ?></label></b></p>
              <p><?php _e('On some hosts background tasks are allowed to run for a very limited amount of time before they timeout. This setting will tell WPBackItUp how long to wait for each background task to complete.  This setting should only be used when working with WPBackItUp support.', 'wp-backitup') ?></p>
              <select class="form-control" id="wpbackitup-max-zip-size" name="data[backup_max_timeout]">
              <option <?php if($wpbackitup_max_timeout=='60') echo 'selected'; ?> value="60"><?php _e('1 Minute', 'wp-backitup') ?></option>
              <option <?php if($wpbackitup_max_timeout=='120') echo 'selected'; ?> value="120"><?php _e('2 Minute', 'wp-backitup') ?></option>
              <option <?php if($wpbackitup_max_timeout=='180') echo 'selected'; ?> value="180"><?php _e('3 Minute', 'wp-backitup') ?></option>
              <option <?php if($wpbackitup_max_timeout=='240') echo 'selected'; ?> value="240"><?php _e('4 Minute', 'wp-backitup') ?></option>
              <option <?php if($wpbackitup_max_timeout=='300') echo 'selected'; ?> value="300"><?php _e('5 Minute', 'wp-backitup') ?></option>
            </select>
          </div>
          <div class="submit"><input type="submit" name="Save_max_timeout" class="button-primary" value="<?php _e("Save", 'wp-backitup') ?>" /></div>
      </div>

     <div class="widget">
	    <h3 class="promo"><i class="fa fa-wrench"></i> <?php _e('Batch Size', 'wp-backitup') ?></h3>
        <p><b><?php _e('Enter the batch size for each of your content items.', 'wp-backitup') ?></b></p>
  		<p><?php _e('These settings tell WPBackItUp how many items that should be added to the backup at a time.  If you experience timeouts while running a backup then these settings can be used to help reduce the amount of time it takes WPBackItUp to complete each backup task .', 'wp-backitup') ?></p>
        <p>
            <input name="data[backup_dbtables_batch_size]" id="wpbackitup_dbtables_batch_size" type="text" size="3" value="<?php echo $backup_dbtables_batch_size; ?>"/>
            <label> <?php _e('DB Tables Batch Size', 'wp-backitup') ?></label>
        </p>

         <p>
             <input name="data[backup_sql_merge_batch_size]" id="wpbackitup_sql_merge_batch_size" type="text" size="3" value="<?php echo $backup_sql_merge_batch_size; ?>"/>
             <label> <?php _e('SQL Merge Batch Size', 'wp-backitup') ?></label>
         </p>

         <p>
             <input name="data[backup_sql_batch_size]" id="wpbackitup_sql_batch_size" type="text" size="3" value="<?php echo $backup_sql_batch_size; ?>"/>
             <label> <?php _e('SQL Batch Size', 'wp-backitup') ?></label>
         </p>

  		<p>
        <input name="data[backup_plugins_batch_size]" id="wpbackitup_plugins_batch_size" type="text" size="3" value="<?php echo $backup_plugins_batch_size; ?>"/>
        <label> <?php _e('Plugins Batch Size', 'wp-backitup') ?></label>
      </p>

      <p>
        <input name="data[backup_themes_batch_size]" id="wpbackitup_themes_batch_size" type="text" size="3" value="<?php echo $backup_themes_batch_size; ?>"/>
  		  <label> <?php _e('Themes Batch Size', 'wp-backitup') ?></label>
      </p>

      <p>
        <input name="data[backup_uploads_batch_size]" id="wpbackitup_uploads_batch_size" type="text" size="3" value="<?php echo $backup_uploads_batch_size; ?>"/>
        <label> <?php _e('Uploads Batch Size', 'wp-backitup') ?></label>
      </p>

      <p>
        <input name="data[backup_others_batch_size]" id="wpbackitup_others_batch_size" type="text" size="3" value="<?php echo $backup_others_batch_size; ?>"/>
        <label> <?php _e('Others Batch Size', 'wp-backitup') ?></label>
      </p>
	    <p class="submit"><input type="submit" name="Save_AdvancedSettings" class="button-primary" value="<?php _e("Save", 'wp-backitup') ?>" />
        <p><?php _e('* These settings should only be modified by advanced users or when when working with WPBackItUp support.', 'wp-backitup') ?></p>
	     <?php
	     if ( false !== ( $msg = get_transient('batch_size_settings-error-number') ) && $msg)
	     {
		     echo '<p class="error">'.$msg.'</p>';
		     delete_transient('batch_size_settings-error-number');
	     }
	     ?>
	    </p>
 	 </div>

    <div class="widget filters">
      <h3 class="promo"><i class="fa fa-filter"></i> <?php _e('Filter Your Folders', 'wp-backitup') ?></h3>
      <p><b><?php _e('Enter a comma separated list of folders that should be excluded from your backups.', 'wp-backitup') ?></b></p>
      <p><?php _e('It is important to note that when a folder name is present in this list any occurrence of that folder, and all its contents, will be excluded from the backup.', 'wp-backitup') ?></p>
      <p>
        <label> <?php _e('Plugin Folders Filter', 'wp-backitup') ?></label>
        <input name="data[backup_plugins_filter]" id="wpbackitup_backup_plugins_filter" type="text" size="30" value="<?php echo $backup_plugins_filter; ?>"/>
      </p>

      <p>
        <label> <?php _e('Theme Folders Filter', 'wp-backitup') ?></label>
        <input name="data[backup_themes_filter]" id="wpbackitup_backup_themes_filter" type="text" size="30" value="<?php echo $backup_themes_filter; ?>"/>
      </p>

      <p>
        <label> <?php _e('Upload Folders Filter', 'wp-backitup') ?></label>
        <input name="data[backup_uploads_filter]" id="wpbackitup_backup_uploads_filter" type="text" size="30" value="<?php echo $backup_uploads_filter; ?>"/>
      </p>
      <p>
        <label> <?php _e('Other Folders Filter', 'wp-backitup') ?></label>
        <input name="data[backup_others_filter]" id="wpbackitup_backup_others_filter" type="text" size="30" value="<?php echo $backup_others_filter; ?>"/>
      </p>
      <p class="submit"><input type="submit" name="Save_Filters" class="button-primary" value="<?php _e("Save", 'wp-backitup') ?>" />
      <p><?php _e('* These settings should only be modified by advanced users or when when working with WPBackItUp support.', 'wp-backitup') ?></p>
    </div>


    <div class="widget dbfilters">
      <h3 class="promo"><i class="fa fa-filter"></i> <?php _e('Filter Your Database Tables', 'wp-backitup') ?></h3>
      <p><b><?php _e('Exclude custom database tables from the backup.', 'wp-backitup') ?></b></p>
      <p><?php _e('If you would like to exclude a custom table from the backup then simply drag it to the list on the right.  WordPress core tables may not be excluded from the backup. ', 'wp-backitup') ?></p>
      <div class="dbfilters_section">
        <div class="dbfilters_section_left">
          <h4>Eligible Database Tables</h4>
          <ul id="wpbackitup_backup_dbtables_filterable_list" class="wpbackitup_connectedSortable"
          <?php if(!empty($backup_dbtables_filter_list)){ echo 'style="min-height:100px"'; } ?>>
            <?php
              if(!empty($backup_dbtables_filterable)){
                foreach($backup_dbtables_filterable as $filterable){
                    echo "<li class=\"ui-state-default\">$filterable</li>";
                }
              }

            ?>
          </ul>
        </div>
        <div class="dbfilters_section_right">
          <h4>Tables Excluded from the backup</h4>
          <ul id="wpbackitup_backup_dbtables_filtered_list" class="wpbackitup_connectedSortable"
          <?php if(!empty($backup_dbtables_filter_list)){ echo 'style="min-height:100px"'; } ?>>
              <?php
                  if(!empty($backup_dbtables_filter_list)){
                    $backup_dbtables_filter_list = explode(", ", $backup_dbtables_filter_list);
                    foreach($backup_dbtables_filter_list as $filterlist){
                        echo "<li class=\"ui-state-default\">$filterlist</li>";
                    }
                  }
              ?>
          </ul>
        </div>
      </div>
      <input type="hidden" id="wpbackitup_backup_dbtables_filter_list" name="data[backup_dbtables_filter_list]" value="" />
      <p class="submit"><input type="submit" id="Save_DBFilters" name="Save_DBFilters" class="button-primary" value="<?php _e("Save", 'wp-backitup') ?>" />
      <p><?php _e('* These settings should only be modified by advanced users or when when working with WPBackItUp support.', 'wp-backitup') ?></p>
    </div>


      <div class="widget">
        <h3 class="promo"><i class="fa fa-trash-o"></i> <?php _e('Remove Data on Uninstall?', 'wp-backitup') ?></h3>
        <p><input type="checkbox" id="wpbackitup_delete_all" value="0" name="data[delete_all]" <?php if ($wpbackitup_delete_all) echo 'checked'; ?>>
        <label for="wpbackitup_delete_all"><?php _e('Check this box if you would like WPBackItUp to completely remove all of its data when the plugin is deleted.', 'wp-backitup') ?></label></p>
        <div class="submit"><input type="submit" name="Save_RemoveAll" class="button-primary" value="<?php _e("Save", 'wp-backitup') ?>" /></div>
      </div>


    </form>
  </div>
</div>