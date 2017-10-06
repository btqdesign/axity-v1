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
?>

<div id="wpbackitup-core-settings" v-cloak>

<!--    <div class="updated" v-show="updated">-->
<!--        <p>--><?php //_e( 'Settings updated successfully!', 'wp-backitup' ); ?><!--</p>-->
<!--    </div>-->


    <div id="wpbackitup-header">
        <h1><?php echo $page_title; ?></h1>
    </div>

    <div id="wpbackitup-settings" v-if="loading === false">
        <?php
            // Nonce
            echo '<input type="hidden" name="wpbackitup-core-ajax-nonce" id="wpbackitup-core-ajax-nonce" value="' . wp_create_nonce( 'wpbackitup-core-ajax-nonce' ) . '" />';
        ?>

        <vue-tabs active-tab-color="#f1f1f1" active-text-color="black">
            <v-tab title="<?php _e( 'General', 'wp-backitup' ); ?>" icon="fa fa-cog">

                <div class="widget">
                    <h3 class="promo"><i class="fa fa-envelope"></i> <?php _e('Email Notifications', 'wp-backitup')  ?></h3>
                    <p><b><?php _e('Please enter your email address if you would like to receive backup email notifications.', 'wp-backitup') ?></b></p>
                    <p><?php _e('Backup email notifications will be sent for every backup and will contain status information related to the backup.', 'wp-backitup'); ?></p>
                    <p><input-tags :on-change="handleEmailInput" :tags="emailsArray" validate="email"></input-tags></p>
                    <div class="submit">
                        <button class="button-primary" v-on:click="setSettings()"><?php _e("Save", 'wp-backitup') ?></button>
                    </div>

                    <p class="error" v-if="errorMessages['notification_email'] !== '' ">{{ errorMessages['notification_email'] }}</p>
                </div>


                <div class="widget">
                    <h3 class="promo"><i class="fa fa-trash-o"></i> <?php _e('Backup Retention', 'wp-backitup') ?></h3>
                    <p><b><?php _e('Enter the number of backup archives that you would like to remain on the server.', 'wp-backitup') ?></b></p>
                    <p><?php _e('Many hosts limit the amount of space that you can take up on their servers. This option tells WPBackItUp the maximum number of backup archives that should remain on your hosts server.  Don\'t worry, we will always remove the oldest backup archives first.', 'wp-backitup') ?></p>
                    <p><input type="text" v-model="backup_retained_number" size="4"></p>
                    <div class="submit">
                        <button class="button-primary" v-on:click="setSettings()"><?php _e("Save", 'wp-backitup') ?></button>
                    </div>

                    <p class="error" v-if="errorMessages['backup_retained_number'] !== '' ">{{ errorMessages['backup_retained_number'] }}</p>
                </div>

                <div class="widget">
                    <h3 class="promo"><i class="fa fa-file-text-o"></i> <?php _e('Logging?', 'wp-backitup') ?></h3>
                    <p><b><?php _e('Turn on WPBackItUp logging.', 'wp-backitup'); ?></b></p>
                    <p><?php _e('This option should only be turned on by advanced users or when troubleshooting issues with WPBackItUp support.', 'wp-backitup'); ?></p>
                    <p><input type="radio" v-model="logging" value="true" checked="logging === true"> <label><?php _e('Yes', 'wp-backitup'); ?></label></p>
                    <p><input type="radio" v-model="logging" value="false" checked="logging === false"> <label><?php _e('No', 'wp-backitup'); ?></label></p>

                    <div class="submit">
                        <button class="button-primary" v-on:click="setSettings()"><?php _e("Save", 'wp-backitup') ?></button>
                    </div>
                </div>

                <!-- Premium settings -->
                <?php do_action('wpbackitup_render_premium_settings'); ?>


                <div class="widget">
                    <h3 class="promo"><i class="fa fa-database"></i> <?php _e('Single File Database Export (db)', 'wp-backitup') ?></h3>
                    <p><input type="checkbox" v-model="single_file_db" checked="single_file_db === true">
                        <label for="wpbackitup_single_file_db"><?php _e('Check this box if you would like WPBackItUp to export your database into a single db file.', 'wp-backitup') ?></label></p>
                    <p><?php _e('When this setting is turned on WPBackItUp will attempt to create a single file that contains your entire database.  This option may not be possible with some hosting providers.  This setting will be turned off automatically if WPBackItUp is unable to complete this step for any reason.', 'wp-backitup') ?></p>

                    <div class="submit">
                        <button class="button-primary" v-on:click="setSettings()"><?php _e("Save", 'wp-backitup') ?></button>
                    </div>
                </div>

                <div class="widget dbfilters">
                    <h3 class="promo"><i class="fa fa-filter"></i> <?php _e('Filter Your Database Tables', 'wp-backitup') ?></h3>
                    <p><b><?php _e('Exclude custom database tables from the backup.', 'wp-backitup') ?></b></p>
                    <p><?php _e('If you would like to exclude a custom table from the backup then simply select it to the list on the right.  WordPress core tables may not be excluded from the backup. ', 'wp-backitup') ?></p>
                    <ui-select
                            has-search
                            :disabled="dbFilterHasSearch"
                            label=""
                            multiple
                            :placeholder="dbFilterPlaceholder"
                            type="image"
                            :options="dbFilterOptions"
                            v-model="db_filters"
                    ></ui-select>

                    <div class="submit">
                        <button class="button-primary" v-on:click="setSettings()"><?php _e("Save", 'wp-backitup') ?></button>
                    </div>

                    <p><?php _e('* These settings should only be modified by advanced users or when when working with WPBackItUp support.', 'wp-backitup') ?></p>
                </div>

                <div class="widget filters">
                    <h3 class="promo"><i class="fa fa-filter"></i> <?php _e('Filter Your Folders', 'wp-backitup') ?></h3>
                    <p><b><?php _e('Enter a comma separated list of folders that should be excluded from your backups.', 'wp-backitup') ?></b></p>
                    <p><?php _e('It is important to note that when a folder name is present in this list any occurrence of that folder, and all its contents, will be excluded from the backup.', 'wp-backitup') ?></p>
                    <p>
                        <label> <?php _e('Plugin Folders Filter', 'wp-backitup') ?></label>
                        <input-tags :on-change="handleTagsInput" :tags="backup_plugins_filter"></input-tags>
                    </p>

                    <p>
                        <label> <?php _e('Theme Folders Filter', 'wp-backitup') ?></label>
                        <input-tags :on-change="handleTagsInput" :tags="backup_themes_filter"></input-tags>
                    </p>

                    <p>
                        <label> <?php _e('Upload Folders Filter', 'wp-backitup') ?></label>
                        <input-tags :on-change="handleTagsInput" :tags="backup_uploads_filter"></input-tags>
                    </p>
                    <p>
                        <label> <?php _e('Other Folders Filter', 'wp-backitup') ?></label>
                        <input-tags :on-change="handleTagsInput" :tags="backup_others_filter"></input-tags>
                    </p>
                    <div class="submit">
                        <button class="button-primary" v-on:click="setSettings()"><?php _e("Save", 'wp-backitup') ?></button>
                    </div>
                    <p><?php _e('* These settings should only be modified by advanced users or when when working with WPBackItUp support.', 'wp-backitup') ?></p>
                </div>

            </v-tab>


            <v-tab title="<?php _e( 'Advanced', 'wp-backitup' ); ?>" icon="fa fa-cogs">

                <div class="widget">
                    <h3 class="promo"><i class="fa fa-wrench"></i> <?php _e('Batch Size', 'wp-backitup') ?></h3>
                    <p><b><?php _e('Enter the batch size for each of your content items.', 'wp-backitup') ?></b></p>
                    <p><?php _e('These settings tell WPBackItUp how many items that should be added to the backup at a time.  If you experience timeouts while running a backup then these settings can be used to help reduce the amount of time it takes WPBackItUp to complete each backup task .', 'wp-backitup') ?></p>
                    <p>
                        <input v-model="backup_dbtables_batch_size" type="text" size="3" />
                        <label> <?php _e('DB Tables Batch Size', 'wp-backitup') ?></label>
                    </p>
                    <p class="error" v-if="errorMessages['backup_dbtables_batch_size'] !== '' ">{{ errorMessages['backup_dbtables_batch_size'] }}</p>

                    <p>
                        <input v-model="backup_sql_merge_batch_size" type="text" size="3" />
                        <label> <?php _e('SQL Merge Batch Size', 'wp-backitup') ?></label>
                    </p>
                    <p class="error" v-if="errorMessages['backup_sql_merge_batch_size'] !== '' ">{{ errorMessages['backup_sql_merge_batch_size'] }}</p>

                    <p>
                        <input v-model="backup_sql_batch_size" type="text" size="3" />
                        <label> <?php _e('SQL Batch Size', 'wp-backitup') ?></label>
                    </p>
                    <p class="error" v-if="errorMessages['backup_sql_batch_size'] !== '' ">{{ errorMessages['backup_sql_batch_size'] }}</p>

                    <p>
                        <input v-model="backup_plugins_batch_size" type="text" size="3" />
                        <label> <?php _e('Plugins Batch Size', 'wp-backitup') ?></label>
                    </p>
                    <p class="error" v-if="errorMessages['backup_plugins_batch_size'] !== '' ">{{ errorMessages['backup_plugins_batch_size'] }}</p>

                    <p>
                        <input v-model="backup_themes_batch_size" type="text" size="3" />
                        <label> <?php _e('Themes Batch Size', 'wp-backitup') ?></label>
                    </p>
                    <p class="error" v-if="errorMessages['backup_themes_batch_size'] !== '' ">{{ errorMessages['backup_themes_batch_size'] }}</p>

                    <p>
                        <input v-model="backup_uploads_batch_size" type="text" size="3" />
                        <label> <?php _e('Uploads Batch Size', 'wp-backitup') ?></label>
                    </p>
                    <p class="error" v-if="errorMessages['backup_uploads_batch_size'] !== '' ">{{ errorMessages['backup_uploads_batch_size'] }}</p>

                    <p>
                        <input v-model="backup_others_batch_size" type="text" size="3" />
                        <label> <?php _e('Others Batch Size', 'wp-backitup') ?></label>
                    </p>
                    <p class="error" v-if="errorMessages['backup_others_batch_size'] !== '' ">{{ errorMessages['backup_others_batch_size'] }}</p>

                    <div class="submit">
                        <button class="button-primary" v-on:click="setSettings()"><?php _e("Save", 'wp-backitup') ?></button>
                    </div>

                    <p><?php _e('* These settings should only be modified by advanced users or when when working with WPBackItUp support.', 'wp-backitup') ?></p>

                    </p>
                </div>


                <div class="widget">
                    <h3 class="promo"><i class="fa fa-file-archive-o"></i> <?php _e('Maximum Zip File Size', 'wp-backitup') ?></h3>
                    <div class="wpbiu-select-box">
                        <p><b><label for="wpbackitup-max-zip-size"><?php _e('Select your maximum zip file size.', 'wp-backitup') ?></label></b></p>
                        <p><?php _e('Some hosting providers do not allow large zip files so if you are encountering backup errors then reducing this setting may help. Please note that this setting will impact performance so we recommend it is set as high as possible.', 'wp-backitup') ?></p>
                        <select class="form-control" v-model="backup_zip_max_size">
                            <option value="104857600"><?php _e('100MB', 'wp-backitup') ?></option>
                            <option value="209715200"><?php _e('200MB', 'wp-backitup') ?></option>
                            <option value="314572800"><?php _e('300MB', 'wp-backitup') ?></option>
                            <option value="419430400"><?php _e('400MB', 'wp-backitup') ?></option>
                            <option value="524288000"><?php _e('500MB', 'wp-backitup') ?></option>
                            <option value="1073741824"><?php _e('1GB', 'wp-backitup') ?></option>
                            <option value="1610612736"><?php _e('1.5GB', 'wp-backitup') ?></option>
                            <option value="2147483648"><?php _e('2GB', 'wp-backitup') ?></option>
                        </select>
                    </div>

                    <div class="submit">
                        <button class="button-primary" v-on:click="setSettings()"><?php _e("Save", 'wp-backitup') ?></button>
                    </div>
                </div>

                <div class="widget">
                    <h3 class="promo"><i class="fa fa-hourglass-end"></i> <?php _e('Task Timeout', 'wp-backitup') ?></h3>
                    <div class="wpbiu-select-box">
                        <p><b><label for="wpbackitup-max-zip-size"><?php _e('Select how long WPBackItUp should wait for tasks to complete.', 'wp-backitup') ?></label></b></p>
                        <p><?php _e('On some hosts background tasks are allowed to run for a very limited amount of time before they timeout. This setting will tell WPBackItUp how long to wait for each background task to complete.  This setting should only be used when working with WPBackItUp support.', 'wp-backitup') ?></p>
                        <select class="form-control" v-model="backup_max_timeout">
                            <option value="60"><?php _e('1 Minute', 'wp-backitup') ?></option>
                            <option value="120"><?php _e('2 Minute', 'wp-backitup') ?></option>
                            <option value="180"><?php _e('3 Minute', 'wp-backitup') ?></option>
                            <option value="240"><?php _e('4 Minute', 'wp-backitup') ?></option>
                            <option value="300"><?php _e('5 Minute', 'wp-backitup') ?></option>
                        </select>
                    </div>

                    <div class="submit">
                        <button class="button-primary" v-on:click="setSettings()"><?php _e("Save", 'wp-backitup') ?></button>
                    </div>
                </div>


                <div class="widget">
                    <h3 class="promo"><i class="fa fa-trash-o"></i> <?php _e('Remove Data on Uninstall?', 'wp-backitup') ?></h3>
                    <p>
                        <input type="checkbox" v-model="delete_all" checked="delete_all === true">
                        <label for="wpbackitup_delete_all"><?php _e('Check this box if you would like WPBackItUp to completely remove all of its data when the plugin is deleted.', 'wp-backitup') ?></label>
                    </p>
                    <div class="submit">
                        <button class="button-primary" v-on:click="setSettings()"><?php _e("Save", 'wp-backitup') ?></button>
                    </div>
                </div>
            </v-tab>
        </vue-tabs>
    </div>
</div>



<script type="text/x-template" id="input-tags-template">
    <div @click="focusNewTag()" v-bind:class="{'read-only': readOnly}" class="vue-input-tag-wrapper">
    <span v-for="(tag, index) in tags" v-bind:key="index" class="input-tag">
      <span>{{ tag }}</span>
      <a v-if="!readOnly" @click.prevent.stop="remove(index)" class="remove"></a>
    </span>
        <input v-if="!readOnly" v-bind:placeholder="placeholder" type="text" v-model="newTag" v-on:keydown.delete.stop="removeLastTag()" v-on:keydown.enter.188.prevent.stop="addNew(newTag)" v-on:keydown.space.prevent.stop="addNew(newTag)" class="new-tag"/>
    </div>
</script>