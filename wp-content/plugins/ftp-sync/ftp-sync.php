<?php
/*
Plugin Name: FTP Sync - Theme, Media & Plugin Files
Plugin URI: 
Description: This plugin allows you to sync and backup local and remote media uploads, theme files, and plugins folders with one click via FTP. **For best results, setup your local site's wp-config.php to use your remote site's database. 
Version: 1.1.6
Author: Joe Ouillette
Author URI: http://buildcreate.com
*/

function set_ftp_sync_defaults(){
	// set defaults
	if(!get_option('ftp_sync_port')){
		update_option('ftp_sync_port', 21);
	}
	if(!get_option('ftp_sync_active_mode')){
		update_option('ftp_sync_active_mode', 'passive');
	}	
	if(!get_option('ftp_sync_remote_wp_content_dir')){
		update_option('ftp_sync_remote_wp_content_dir', '/');
	}	
	if(!get_option('ftp_sync_newer_by')){
		update_option('ftp_sync_newer_by', '-5 Minutes');
	}

	// surpress php warnings that ftp_chdir throws when no directory
	error_reporting(E_ERROR | E_PARSE);

}
add_action('admin_init', 'set_ftp_sync_defaults');

// add backend menu item
function ftp_sync() {
	add_options_page('FTP Sync Options', 'FTP Sync', 'administrator', 'ftp-sync-options', 'ftp_sync_options');
}
add_action('admin_menu', 'ftp_sync');


// add admin bar menu item if on localhost
if($_SERVER["SERVER_NAME"] == 'localhost'){
	function customize_menu(){
	    global $wp_admin_bar;
		$args = array(
		   "id" => "ftp-sync",
		   "title" => "FTP Sync",
		   "href" => admin_url() . "options-general.php?page=ftp-sync-options"
		);
		$wp_admin_bar->add_menu($args);
	}
	add_action("wp_before_admin_bar_render", "customize_menu");
}

function ftp_sync_options(){

	if($_SERVER["SERVER_NAME"] == 'localhost' || $_SERVER["SERVER_NAME"] == '127.0.0.1' || $_SERVER["SERVER_NAME"] == 'wp.localhost'){
		session_start(); // start session
		$_SESSION = array(); // get rid of all session data on page load


		if(isset($_POST['save'])){
			update_option('ftp_sync_host', sanitize_text_field($_POST['ftp_sync_host']));
			update_option('ftp_sync_user', sanitize_text_field($_POST['ftp_sync_user']));
			update_option('ftp_sync_pass', sanitize_text_field($_POST['ftp_sync_pass']));
			update_option('ftp_sync_port', sanitize_text_field($_POST['ftp_sync_port']));
			update_option('ftp_sync_active_mode', sanitize_text_field($_POST['ftp_sync_active_mode']));
			update_option('ftp_sync_remote_wp_content_dir', sanitize_text_field($_POST['ftp_sync_remote_wp_content_dir']));
			update_option('ftp_sync_newer_by', sanitize_text_field($_POST['ftp_sync_newer_by']));
			update_option('ftp_sync_ignore_files', sanitize_text_field($_POST['ftp_sync_ignore_files']));
			update_option('ftp_sync_ignore_directories', sanitize_text_field($_POST['ftp_sync_ignore_directories']));
			update_option('ftp_sync_ignore_extensions', sanitize_text_field($_POST['ftp_sync_ignore_extensions']));
		} 

		$ftp_sync_active_mode = get_option('ftp_sync_active_mode');
		$ftp_sync_port = get_option('ftp_sync_port');
		$ftp_sync_newer_by = get_option('ftp_sync_newer_by');

		$html .= '<div class="wrap"><h2><img style="vertical-align: middle;margin-right: 20px;" src="'. plugins_url() . '\ftp-sync\ftp-sync-logo.png" alt="FTP Sync"/> Theme, Media & Plugin Files - Sync and Backup <em style="font-size:14px;color:#ccc;">(version 1.1.6)</em></h2><hr/>';

		// terms and conditions
		if(!isset($_COOKIE['ftp-sync-terms-agreed'])){
			$html .= '<div id="ftp-sync-terms" style="margin-top:20px;">
						<div style="padding:20px;background:#fff;border-radius:5px;border:1px solid #ddd;">
							<h2>Before you use FTP Sync you must first agree to these terms and conditions:</h2>
							<ol>
								<li>You are using FTP Sync at your own risk!</li>
								<li>You will create a backup of all your files before using FTP Sync.</li>
								<li>FTP Sync is not responsible for any lost, damaged, or overwritten files.</li>
								<li>By clicking the button below you are agreeing to all the terms and conditions listed above. Enjoy!</li>
							</ol>
							<button id="ftp-sync-terms-agree" style="cursor:pointer;">I agree!</button>
						</div>
					</div>
					<div id="ftp-sync-main" style="display:none;">';
		}else{
			$html .= '<div id="ftp-sync-main">';
		}

		$html .= '<div style="float:left;width:55%;"><form action="" method="post">
			<table width="100%" cellpadding="10">
				<tbody>
					<tr>
						<td>
							<label>FTP Host: <em>(domain or IP)</em></label>
					 		<input style="width:100%;" type="text" name="ftp_sync_host" value="' . get_option('ftp_sync_host') . '" />
					 	</td>	
					</tr>
					<tr>
					 	<td>
					 		<label>FTP User:</label>
					 		<input style="width:100%;" type="text" name="ftp_sync_user" value="' . get_option('ftp_sync_user') . '" />
					 	</td>	
					</tr>
					<tr>
					 	<td>
					 		<label>FTP Pass:</label>
					 		<input style="width:100%;" type="password" name="ftp_sync_pass" value="' . get_option('ftp_sync_pass') . '" />
					 	</td>	
					</tr>
					<tr>
					 	<td>
					 		<label>FTP Mode:</label><br/>
							<select name="ftp_sync_active_mode">
							    <option value="passive"' . selected($ftp_sync_active_mode, 'passive', false) . '>Passive</option>
							    <option value="active"' . selected($ftp_sync_active_mode, 'active', false) . '>Active</option>
							</select>
					 	</td>	
					</tr>
					<tr>
					 	<td>
					 		<label>Remote wp-content FTP Server Path: <em>(ie. /public_html/wp-content)</em></label>
					 		<input style="width:100%;" type="text" name="ftp_sync_remote_wp_content_dir" value="' . get_option('ftp_sync_remote_wp_content_dir') . '" />
					 	</td>	
					</tr>
					<tr>
					 	<td>
					 		<label>To overwrite old files, sync files must be newer by at least: <em>(to compensate for file transfer time)</em></label><br/>
							<select name="ftp_sync_newer_by">							    
							    <option value="-5 minutes"' . selected($ftp_sync_newer_by, '-5 minutes', false) . '>5 Minutes</option> 
							    <option value="-15 minutes"' . selected($ftp_sync_newer_by, '-15 minutes', false) . '>15 Minutes</option>  
							    <option value="-30 minutes"' . selected($ftp_sync_newer_by, '-30 minutes', false) . '>30 Minutes</option> 
							    <option value="-1 hour"' . selected($ftp_sync_newer_by, '-1 hour', false) . '>1 hour</option>
							    <option value="-1 day"' . selected($ftp_sync_newer_by, '-1 day', false) . '>1 day</option>
							</select>
					 	</td>	
					</tr>
					<tr>
						<td>
							<label>Ignore these files (comma separated). <em>ie. example.php,another.php</em></label><br/>
							<input style="width:100%;" type="text" name="ftp_sync_ignore_files" value="'. get_option('ftp_sync_ignore_files') .'"/>
						</td>
					</tr>					
					<tr>
						<td>
							<label>Ignore these directories (comma separated). <em>ie. /example/directory/,another/example/</em></label><br/>
							<input style="width:100%;" type="text" name="ftp_sync_ignore_directories" value="'. get_option('ftp_sync_ignore_directories') .'"/>
						</td>
					</tr>					
					<tr>
						<td>
							<label>Ignore these file extensions (comma separated). <em>ie. .mp3,.jpg</em></label><br/>
							<input style="width:100%;" type="text" name="ftp_sync_ignore_extensions" value="'. get_option('ftp_sync_ignore_extensions') .'"/>
						</td>
					</tr>
					<tr>
					 	<td>
					 		<br/><input class="button button-primary" type="submit" name="save" value="Save Settings" />
					 	</td>
					</tr>
				</tbody>
			</table>
	 	</form></div>';

		$html .= '<div style="float:right;width:40%;margin-top:2em;"><table class="widefat" width="100%" cellpadding="10">
				<tbody>
					<tr>
						<td scope="row" align="left">
							<label style="font-size:1.5em;">Ready to FTP Sync? <em style="font-size:12px;color:#ddd;">(the first time might take a while)</em></label>
						</td>
					</tr> 
					<tr><td><div id="sync-status"></div></td></tr>
					<tr>
						<td scope="row" align="left">
							<div id="buttons">
							 	<button id="ftp-sync-theme-files" class="button">Sync Theme Files</button>	
							 	<button id="ftp-sync-media-files" class="button">Sync Media Files</button>	
							 	<button id="ftp-sync-plugin-files" class="button">Sync Plugin Files</button>
						 	</div>
						 	<div id="loading-gif" style="display:none;">
						 		<img src="' . plugins_url() . '/ftp-sync/ajax-loader.gif" alt="loading" />
						 		<a style="float:right;" href="">cancel sync</a>
						 	</div>
					 	</td>
					 </tr>
				</tbody>
			</table>
			<br/>
			<table class="widefat" width="100%" cellpadding="10">
				<tbody>
					<tr>
						<td scope="row" align="left">
							<label style="font-size:1.5em;">Create a remote backup? <em style="font-size:12px;color:#ddd;">(recommended before syncing)</em></label>
						</td>
					</tr>  
					<tr><td><div id="backup-status"></div></td></tr>
					<tr>
						<td scope="row" align="left">
							<div id="backup-buttons">
							 	<button id="ftp-backup-theme-files" class="button">Backup Theme Files</button>	
							 	<button id="ftp-backup-media-files" class="button">Backup Media Files</button>	
							 	<button id="ftp-backup-plugin-files" class="button">Backup Plugin Files</button>
						 	</div>
						 	<div id="backup-loading-gif" style="display:none;">
						 		<img src="' . plugins_url() . '/ftp-sync/ajax-loader.gif" alt="loading" />
						 		<a style="float:right;" href="">cancel backup</a>
						 	</div>
					 	</td>
					 </tr>
				</tbody>
			</table>
			<br/>
			<table style="background:#e5e5e5;" class="widefat" width="100%" cellpadding="10">
				<tbody>
					<tr>
						<td scope="row" align="left">
							<label style="font-size:1.2em;">Need Help? Here are some things that may be your issue.</label>
							<hr style="margin-bottom:0px;"/>
						</td>
					</tr> 
					<tr>
						<td>
							<p>Your FTP account:</p>
							<ul style="margin-left:30px;list-style:disc;">
								<li>May not have the required <a target="_blank" href="http://codex.wordpress.org/Changing_File_Permissions">read/write permissions</a></li>
								<li>May not be the required owner or group for the directory</li>
								<li>May be IP Banned from multiple failed login attempts. If you suspect this is the case, contact your web host to resolve the issue.</li>
							</ul>
							<p>Your FTP connection:</p>
							<ul style="margin-left:30px;list-style:disc;">
								<li>May require SFTP which is not currently supported</li>
								<li>May require that you use Active mode</li>
							</ul>
							<p>Your remote server directory:</p>
							<ul style="margin-left:30px;list-style:disc;">
								<li>Does not exist</li>
								<li>Is not named exactly as your local directory</li>
							</ul>
						</td>
					</tr>
				</tbody>
			</table>
	 		<br/>
	 		<div>
	 			<h3 style="line-height:1.2em;">Like this plugin? Show your support and buy me a cup of coffee! Thanks!</h3>
	 			<div style="text-align:right;">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="PSDEXB2UPGH3N">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
	 		</div>

	 		</div></div></div>';
	}else {
		$html .= '<p>Sorry... sync is for use on LOCALHOST only!</p></div>';
	}

	echo $html;
} 


function ftp_sync_javascript() {
?>
	<script type="text/javascript">
		jQuery(document).ready(function($){

			// terms click cookie
			$('#ftp-sync-terms-agree').click(function(){
				var today = new Date();
				var expire = new Date(today.getTime() + (30 * 24 * 60 * 60 * 1000)); // in 30 days
				document.cookie = "ftp-sync-terms-agreed=1;expires=" + expire + ";";
				$('#ftp-sync-terms').hide();
				$('#ftp-sync-main').fadeIn();
			});
			
			// do sync on button click
			$("#ftp-sync-theme-files").click(function(){
				// hide sync button
				$(this).parent('#buttons').hide();
				$('#loading-gif').show();
				// remove previous sync
				$('#sync-status').html('');
				do_ftp_sync(1, "theme");
			});	

			// do sync on button click
			$("#ftp-sync-media-files").click(function(){
				// hide sync button
				$(this).parent('#buttons').hide();
				$('#loading-gif').show();
				// remove previous sync
				$('#sync-status').html('');
				do_ftp_sync(1, "media");
			});	

			// do sync on button click
			$("#ftp-sync-plugin-files").click(function(){
				// hide sync button
				$(this).parent('#buttons').hide();
				$('#loading-gif').show();
				// remove previous sync
				$('#sync-status').html('');
				do_ftp_sync(1, "plugin");
			});				

			// do backup on button click
			$("#ftp-backup-theme-files").click(function(){
				// hide sync button
				$(this).parent('#backup-buttons').hide();
				$('#backup-loading-gif').show();
				// remove previous sync
				$('#backup-status').html('');
				do_ftp_backup(1, "theme");
			});	

			// do backup on button click
			$("#ftp-backup-media-files").click(function(){
				// hide sync button
				$(this).parent('#backup-buttons').hide();
				$('#backup-loading-gif').show();
				// remove previous sync
				$('#backup-status').html('');
				do_ftp_backup(1, "media");
			});	

			// do backup on button click
			$("#ftp-backup-plugin-files").click(function(){
				// hide sync button
				$(this).parent('#backup-buttons').hide();
				$('#backup-loading-gif').show();
				// remove previous sync
				$('#backup-status').html('');
				do_ftp_backup(1, "plugin");
			});	

			function do_ftp_sync(step, type){
				var data = {
					action: 'ftp_sync',
					step: step,
					type: type
				};

				$.ajax({
					type: "post",
					url: ajaxurl,
					data: data,
					dataType: "json",
					success: function(response) {
						// append and call next step
						$('#sync-status').append(response.html);
						if(response.step){
							do_ftp_sync(response.step, response.type);
						}else{
							// show sync
							$('#loading-gif').hide();
							$('#buttons').fadeIn('fast');
						}
					},
					error: function(xhr, status, err) {

			            // append error
						$('#sync-status').append('<br/><br/><strong>AJAX ERROR:<br/>' + xhr.responseText + '</strong>');

						// show sync
						$('#loading-gif').hide();
						$('#buttons').fadeIn('fast');
			        }
				});
			}			

			function do_ftp_backup(step, type){
				var data = {
					action: 'ftp_backup',
					step: step,
					type: type
				};

				$.ajax({
					type: "post",
					url: ajaxurl,
					data: data,
					dataType: "json",
					success: function(response) {
						// append and call next step
						$('#backup-status').append(response.html);
						if(response.step){
							do_ftp_backup(response.step, response.type);
						}else{
							// show sync
							$('#backup-loading-gif').hide();
							$('#backup-buttons').fadeIn('fast');
						}
					},
					error: function(xhr, status, err) {

						// clear session data


			            // append error
						$('#backup-status').append('<br/><br/><strong>AJAX ERROR:<br/>' + xhr.responseText + '</strong>');

						// show sync
						$('#backup-loading-gif').hide();
						$('#backup-buttons').fadeIn('fast');
			        }
				});
			}
		});
	</script>
<?php
}
add_action('admin_footer', 'ftp_sync_javascript');


//////////////////////////////
// define ftp_sync globals  //
//////////////////////////////

set_time_limit(3600); // up the server script finish time limit

$ftpUser = get_option('ftp_sync_user');
$ftpHost = get_option('ftp_sync_host');
$ftpPass = get_option('ftp_sync_pass');
$ftpPort = get_option('ftp_sync_port');
$activeMode = get_option('ftp_sync_active_mode');
if($activeMode == 'active'){$activeMode = true;}
else{$activeMode = false;}
$ftp_sync_newer_by = get_option('ftp_sync_newer_by');

// local dirs
$local_media_dir = ABSPATH . 'wp-content/uploads';
$local_theme_dir = get_template_directory();
$local_plugin_dir = ABSPATH . 'wp-content/plugins';

// get current theme folder name
$current_theme = explode('/', $local_theme_dir);
$num_items = count($current_theme);
$current_theme_folder = $current_theme[$num_items - 1];

// remote dirs - remove trailing slash for consistency
$remote_theme_dir = rtrim(get_option('ftp_sync_remote_wp_content_dir'), "/") . '/themes/' . $current_theme_folder;
$remote_media_dir = rtrim(get_option('ftp_sync_remote_wp_content_dir'), "/") . '/uploads';
$remote_plugin_dir = rtrim(get_option('ftp_sync_remote_wp_content_dir'), "/") . '/plugins';

// local ftp-sync plugin dir
//$local_ftp_sync_dir = ABSPATH . 'wp-content/plugins/ftp-sync';


// sync function
function ftp_sync_callback($data){
	global $ftpHost, $ftpPort, $ftpUser, $ftpPass, $activeMode, $local_media_dir, $remote_media_dir, $local_theme_dir, $remote_theme_dir, $local_plugin_dir, $remote_plugin_dir;

	// start session
	session_start();

	$type = sanitize_text_field($_POST['type']);

	switch($type){

		case "theme":
			$name = "Theme";
			$local_dir = $local_theme_dir;
			$remote_dir = $remote_theme_dir;
			break;

		case "media":
			$name = "Media";
			$local_dir = $local_media_dir;
			$remote_dir = $remote_media_dir;
			break;

		case "plugin":
			$name = "Plugin";
			$local_dir = $local_plugin_dir;
			$remote_dir = $remote_plugin_dir;
			break;

		default :
			break;
	}

	$step = sanitize_text_field($_POST['step']);

	switch($step){

		case 1:
			$html = "<p><strong>$name Files</strong></p>Testing FTP Connection... ";
			$step++;
			break;

		case 2:
			$result = get_ftp_connection($ftpHost, $ftpPort, $ftpUser, $ftpPass, $activeMode, true);
			$html = $result[1];
			if($result[0]){$step++;}
			else{$step = false;}
			break;

		case 3:
			$html = "Verifying remote $type directory... ";
			$step++;
			break;

		case 4:
			$verified = verify_remote_dir($remote_dir);
			if($verified){
				$html = "OK<br/>";
				$step++;
				$step = 7;
			}else{
				$html = "<br/>ERROR: <strong>$remote_dir</strong> does not exist on the server<br/>";
				$step = false;
			}
			break;

		case 5:
			// this is being skipped for now
			$html = "Syncing Timezones... ";
			$step++;
			break;

		case 6:
			// this is being skipped for now
			$ftp = get_ftp_connection($ftpHost, $ftpPort, $ftpUser, $ftpPass, $activeMode, false);
			$html = sync_timezones($ftp, $local_dir, $remote_dir);
			ftp_close($ftp);
			$step++;
			break;

		case 7:
			$html = "Scanning local $type files... ";
			$step++;
			break;

		case 8:
			$html = scan_local_files($local_dir . "/");
			$step++;
			break;

		case 9:
			$html = "Scanning remote $type files... ";
			$step++;
			break;

		case 10:
			$ftp = get_ftp_connection($ftpHost, $ftpPort, $ftpUser, $ftpPass, $activeMode, false);
			$html = scan_remote_files($ftp, $remote_dir);
			ftp_close($ftp);
			$step++;
			break;

		case 11:
			$html = "Comparing $type files... ";
			$step++;
			break;

		case 12:
			$html = compare_files($local_dir, $remote_dir);
			$step++;
			break;

		case 13:	
			$local_files = $_SESSION['local_files_to_upload'];
			if($local_files){
				$total = 0;
				foreach($local_files as $file){
					$total += filesize($file);
				}
				$html = "Uploading " . ceil(count($local_files)) . " newer $type files (" . round(($total/1024)/1024, 2) . " MB)... 
					<span id='upload'>(est. <span>
						<script type='text/javascript'>
							var sec = " . ceil(($total/1024)/15) . "
							var timer = setInterval(function() { 
							   jQuery('#upload span').text(sec--);
							   if (sec === -1) {
							      clearInterval(timer);
							   } 
							}, 1000);
						</script>
					</span> seconds)... </span> ";
				$step++;
			}else{
				$html = "No local $type files to upload.<br/>";
				$step = 15;
			}
			break;	

		case 14:	
			$local_files = $_SESSION['local_files_to_upload'];
			$ftp = get_ftp_connection($ftpHost, $ftpPort, $ftpUser, $ftpPass, $activeMode, false);
			$html = upload_files($ftp, $remote_dir, $local_dir, $local_files, $activeMode);
			ftp_close($ftp);
			$html = "OK<br/>";
			$step++;
			break;
		
		case 15:
			$html = "<script type='text/javascript'>jQuery('#upload').fadeOut('fast');</script>";
			$remote_files = $_SESSION['remote_files_to_download'];
			if($remote_files){
				$total = 0;
				$ftp = get_ftp_connection($ftpHost, $ftpPort, $ftpUser, $ftpPass, $activeMode, false);
				foreach($remote_files as $file){
					$total += ftp_size($ftp, $file);
				}
				ftp_close($ftp);
				$html .= "Downloading " . ceil(count($remote_files)) . " newer $type files (" . round(($total/1024)/1024, 2) . " MB)... 
					<span id='download'>(est. <span>
						<script type='text/javascript'>
							var sec2 = " . ceil(($total/1024)/64) . "
							var timer2 = setInterval(function() { 
							   jQuery('#download span').text(sec2--);
							   if (sec2 === -1) {
							      clearInterval(timer2);
							   } 
							}, 1000);
						</script>
					</span> seconds)... </span> ";
				$step++;
			}else{
				$html .= "No remote $type files to download.<br/>";
				$step = 17; // all done
			}
			break;

		case 16:
			$remote_files = $_SESSION['remote_files_to_download'];
			$local_dir_fixed = str_replace('/', '\\', $local_dir); // correct slashes in localRoot
			$ftp = get_ftp_connection($ftpHost, $ftpPort, $ftpUser, $ftpPass, $activeMode, false);
			$html = download($ftp, $remote_dir, $local_dir, $remote_files, $activeMode);
			ftp_close($ftp);
			$html = "OK<br/>";
			$step++;
			break;

		case 17:
			$html = "<script type='text/javascript'>jQuery('#download').fadeOut('fast');</script>";
			$step++;
			break;

		default:
			$_SESSION = array(); // get rid of all session data for next sync
			$step = false;
			break;
	}
	
	echo json_encode(array('step' => $step, 'html' => $html, 'type' => $type));

	die(); // this is required to return a proper result
}
add_action('wp_ajax_ftp_sync', 'ftp_sync_callback');


// backup function
function ftp_backup_callback($data){
	global $ftpHost, $ftpPort, $ftpUser, $ftpPass, $activeMode, $local_media_dir, $remote_media_dir, $local_theme_dir, $remote_theme_dir, $local_plugin_dir, $remote_plugin_dir;

	// start session
	session_start();

	$type = sanitize_text_field($_POST['type']);

	switch($type){

		case "theme":
			$name = "Theme";
			$local_dir = $local_theme_dir;
			$remote_dir = $remote_theme_dir;
			break;

		case "media":
			$name = "Media";
			$local_dir = $local_media_dir;
			$remote_dir = $remote_media_dir;
			break;

		case "plugin":
			$name = "Plugin";
			$local_dir = $local_plugin_dir;
			$remote_dir = $remote_plugin_dir;
			break;

		default :
			break;
	}

	$step = sanitize_text_field($_POST['step']);

	switch($step){

		case 1:
			$html = "<p><strong>$name Files</strong></p>Testing FTP Connection... ";
			$step++;
			break;

		case 2:
			$result = get_ftp_connection($ftpHost, $ftpPort, $ftpUser, $ftpPass, $activeMode, true);
			$html = $result[1];
			if($result[0]){$step++;}
			else{$step = false;}
			break;

		case 3:
			$html = "Verifying remote $type directory... ";
			$step++;
			break;

		case 4:
			$verified = verify_remote_dir($remote_dir);
			if($verified){
				$html = "OK<br/>";
				$step++;
			}else{
				$html = "<br/>ERROR: <strong>$remote_dir</strong> does not exist on the server<br/>";
				$step = false;
			}
			break;

		case 5:
			$html = "Creating a backup of your remote $type files... ";
			$step++;
			break;	

		case 6:	
			$ftp = get_ftp_connection($ftpHost, $ftpPort, $ftpUser, $ftpPass, $activeMode, false);
			$html = create_backup($ftp, $local_dir, $remote_dir, $type, $activeMode);
			ftp_close($ftp);
			$step++;
			break;

		default:
			$_SESSION = array(); // get rid of all session data for next sync
			$step = false;
			break;
	}

	echo json_encode(array('step' => $step, 'html' => $html, 'type' => $type));

	die(); // this is required to return a proper result

}
add_action('wp_ajax_ftp_backup', 'ftp_backup_callback');



//////////////////////
// BACKUP FUNCTIONS //
//////////////////////

function create_backup($ftp, $local_dir, $remote_dir, $type, $active){

	$html = "OK<br/>";

	// fix local dir
	$local_dir = str_replace('/', '\\', $local_dir);

    if(extension_loaded('ftp') && extension_loaded('zip')){
       
		// create new zip
		$zip = new ZipArchive;

		if(is_object($zip)){
			$time = time();
			$zip_name = "$type-backup-$time.zip";
			$remote_path = "$remote_dir/$zip_name";
			$local_path = "$local_dir\\$zip_name";
			
		    if($zip->open($local_path, ZipArchive::CREATE)){
		        
		        scan_remote_files($ftp, $remote_dir);
		        $backup_files = $_SESSION['remote_files'];

		        if($backup_files){
			        foreach($backup_files as $file => $mod){

		        		$localFile = str_replace('/', '\\', str_replace($remote_dir, "$local_dir\\ftp-sync-backup", $file));
						$d = dirname($localFile);

						// make local directory if it doesnt exist
						if(!is_dir($d)){
						    mkdir($d, 0777, true);
						}

						// switch modes
						ftp_pasv($ftp, !$active);

						// get the file for download
						ftp_get($ftp, $localFile, $file, FTP_BINARY);
					}
				}

				$local_files = scanLocal("$local_dir\\ftp-sync-backup");
				if($local_files){
					foreach ($local_files as $file => $mod) {
			        	$zip->addEmptyDir(dirname(str_replace("$local_dir\\", '', $file)));
			        	if(!strpos($file, "$type-backup")){
	                   		$zip->addFile($file, str_replace("$local_dir\\", '', $file));
	                   	}
              		}
          		}

          		$zip->close();
		        
            }

            // delete local temp files
            delete_dir("$local_dir\\ftp-sync-backup");

            // upload backup to server
            ftp_pasv($ftp, !$active);
  			ftp_put($ftp, $remote_path, $local_path, FTP_BINARY);	

		
		$html .= "<br/>Backup created in: $remote_path";
		    
		}

	}else{
		$html = "ERROR: Zip extension not loaded on your server.";
	}

	return $html;
}

function delete_dir($src) { 
    $dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                delete_dir($src . '/' . $file); 
            } 
            else { 
                unlink($src . '/' . $file); 
            } 
        } 
    } 
    rmdir($src);
    closedir($dir); 
}


////////////////////
// SYNC FUNCTIONS //
////////////////////

function get_ftp_connection($host, $port, $user, $pass, $active, $echo){

	if($port == "21"){$port = 21;}
	else{$port == 22;}

	$result = array();

	// connect
	$ftp = ftp_connect($host, $port, 3600); 
	if($ftp){
		$result[0] = 1; 
		$result[1] = "Host Connection OK... ";
	}
	else{
		$result[0] = 0;
		$result[1] = "Host Connection Failed!... ";
		return $result;
	}

	// login
	if(ftp_login($ftp, $user, $pass)){
		$result[1] .= "FTP Login... OK<br/>";
	}
	else{
		$result[0] = 0;
		$result[1] .= "FTP Login... Failed!<br/>";
		return $result;
	}

	// switch modes
	ftp_pasv($ftp, !$active);

	if($echo){
		ftp_close($ftp);
		return $result;
	}
	else{return $ftp;}
}

function verify_remote_dir($remoteRoot){
	global $ftpUser, $ftpPass, $ftpHost, $ftpPort;

	// set protocol
	if($ftpPort == 21){
		$protocol = "ftp";
	}else{
		$protocol = "sftp";
	}

	if(is_dir($protocol . "://" . $ftpUser . ':' . $ftpPass . "@" . $ftpHost . "/" . $remoteRoot)){
		return true;
	}else{
		return false;
	}
}

// function sync_timezones($ftp, $local_dir, $remote_dir){

// 	// make local file
// 	$local_file = 'timezone.txt';
// 	fopen($local_dir . '/' . $local_file, 'w') or die(); //implicitly creates file
	
// 	// get local file modified time
// 	$local_mod_time = filemtime($local_dir . '/' . $local_file);

// 	// upload file to ftp server
// 	ftp_put($ftp, $remote_dir . '/' . $local_file, $local_dir . '/' . $local_file, FTP_BINARY);

// 	// get FTP file modified time
// 	$remote_mod_time = ftp_mdtm($ftp, $remote_dir . '/' . $local_file);

// 	// save the timezone offset
// 	$_SESSION['timezone_offset'] = $local_mod_time - $remote_mod_time;

// 	// delete the files you made
// 	unlink($local_dir . '/' . $local_file); // local
// 	ftp_delete($ftp, $remote_dir . '/' . $local_file); // remote

// 	$html = "OK<br/>";
// 	//$html .= "local: " . date('h:i:s A', $local_mod_time) . ", remote: " . date('h:i:s A', $remote_mod_time) . ", diff: " . ($local_mod_time - $remote_mod_time);

// 	return $html;
// }


function scan_local_files($local_root){
	
	$local_files = scanLocal($local_root); //Scan local files.

	if(empty($local_files)){
		$html = "No local files found!<br/>";
	}else{
		$_SESSION['local_files'] = $local_files; // save for later
		$html = count($local_files) . " local files found!<br/>";
	}

	return $html;
}

$local_files = array();
$local_files_fixed = array();
function scanLocal($dir) {
	global $local_files;

	if(is_dir($dir)){

		// load helper class
		$fileinfos = new RecursiveIteratorIterator(
		    new RecursiveDirectoryIterator($dir)
		);

		// get ignored files
		$ignored_files = explode(',', str_replace(' ', '', get_option('ftp_sync_ignore_files')));	

		// get ignored directories
		$ignored_directories = explode(',', str_replace(' ', '', get_option('ftp_sync_ignore_directories')));	

		// get ignored extensions
		$ignored_extensions = explode(',', str_replace(' ', '', get_option('ftp_sync_ignore_extensions')));

		// merge ignored
		$ignore = array_merge($ignored_files, $ignored_directories, $ignored_extensions);

		foreach($fileinfos as $pathname => $fileinfo) {
			foreach($ignore as $ignore_str){
				if(strpos($pathname, $ignore_str) !== false) continue;
			}
		    if(!$fileinfo->isFile()) continue;
		    $local_files[$pathname] = filemtime($pathname);
		}

		// fix local directory slashes
		foreach ($local_files as $path => $mod) {
			$path_fixed = str_replace('/', '\\', $path);

			// exclude all files and folders that start with '.'
			if(!strpos($path_fixed,'\\.') !== false){
				$local_files_fixed[$path_fixed] = $mod;
			}
		}
	}

	return $local_files_fixed;
}

$remote_files = array();
function scan_remote_files($ftp, $remoteRoot){
	global $remote_files;

	$list = ftp_rawlist($ftp, $remoteRoot);

    $anzlist = count($list);
    $i = 0;
    while ($i < $anzlist) {
        $split = preg_split("/[\s]+/", $list[$i], 9, PREG_SPLIT_NO_EMPTY);
        $itemname = $split[8];
        $path = "$remoteRoot/$itemname";
        if(substr($itemname,0,1) != "."){
	        if(substr($list[$i],0,1) === "d"){
	            scan_remote_files($ftp, $path);
	        }else if(strlen($itemname) > 2){
	        	$remote_files[$path] = ftp_mdtm($ftp, $path);
	        }
	    }
        $i++;
    }

	if(empty($remote_files)){
		$html = "No remote files found!<br/>";
	}else{
		$_SESSION['remote_files'] = $remote_files;
		$html = count($remote_files) . " remote files found!<br/>";
	}

	return $html;
}

function compare_files($local_dir, $remote_dir){
	global $ftp_sync_newer_by;
	
	// get saved files
	$local_files = $_SESSION['local_files'];
	$remote_files = $_SESSION['remote_files'];
	$local_files_to_upload = array();
	$remote_files_to_download = array();

	// get timezone offset
	//$timezone_offset = $_SESSION['timezone_offset'];

	$html .= "OK<br/>";

	// Find out newer or missing files to upload
	if($local_files){
		foreach($local_files as $file => $local_mod) {

			// change filenames to check against each other
			$local_fixed = str_replace('\\', '/', str_replace(str_replace('/', '\\', $local_dir), $remote_dir, $file));

			// check if exists and newer than remote
			if(isset($remote_files[$local_fixed]) && (strtotime($ftp_sync_newer_by, $local_mod) >=  $remote_files[$local_fixed])){
				$local_files_to_upload[] = $file;
			}

			elseif(!isset($remote_files[$local_fixed])){
				$local_files_to_upload[] = $file;
			}
		}
		$_SESSION['local_files_to_upload'] = $local_files_to_upload;
	}

	// Find out newer or missing files to download
	if($remote_files){
		foreach ($remote_files as $file => $remote_mod) {

			//change filenames to check against each other
			$remote_fixed = str_replace('/', '\\', str_replace($remote_dir . '/', str_replace('/', '\\', $local_dir) . '\\', $file));

			// check if exists and newer than remote
			if(isset($local_files[$remote_fixed]) && (strtotime($ftp_sync_newer_by, $remote_mod) >=  $local_files[$remote_fixed])){
				$remote_files_to_download[] = $file;
			}

			elseif(!isset($local_files[$remote_fixed])){
				$remote_files_to_download[] = $file;
			}
		}
		$_SESSION['remote_files_to_download'] = $remote_files_to_download;
	}

	
	return $html;
}


function upload_files($ftp, $remoteRoot, $localRoot, $uploadFiles, $active){
	global $ftpUser, $ftpHost, $ftpPass, $ftpPort;

	// set protocol
	if($ftpPort == 21){
		$protocol = "ftp";
	}else{
		$protocol = "sftp";
	}

	// make root directory if not there
	if(!ftp_chdir($ftp, $remoteRoot)){
		ftp_mkdir($ftp, $remoteRoot);
	}

	// change dir to root to make the new directories from
	ftp_chdir($ftp, $remoteRoot);

 	// correct slashes in localRoot
	$local_dir_fixed = str_replace('/', '\\', $localRoot);

	// iterate through files and upload
	foreach($uploadFiles as $file) {

		// make remote file path from local file
		$remote_dir = str_replace('\\', '/', str_replace($local_dir_fixed, $remoteRoot, $file));

		$dir = '';
		$parts = explode('/', $remote_dir);
		if($parts){
			foreach($parts as $part){
				if($part != end($parts)){
					$dir .= '/' . $part;
					// make root directory if not there
					if(!ftp_chdir($ftp, $dir)){
						ftp_mkdir($ftp, $dir);
						
					}
					ftp_chdir($ftp, $dir);
				}
			}
		}
		
		// change directory and upload file
		ftp_chdir($ftp, dirname($remote_dir));
		ftp_put($ftp, basename($remote_dir), $file, FTP_BINARY);
	}

	// switch modes
	ftp_pasv($ftp, !$active);

	$html .= "OK<br/>";
	return $html;
}

function download($ftp, $remoteRoot, $localRoot, $downloadFiles, $active){

	// switch modes
	ftp_pasv($ftp, !$active);

	// change directory to root
	ftp_chdir($ftp, $remoteRoot);

	foreach($downloadFiles as $f){
		$localFile = str_replace($remoteRoot, $localRoot, $f);
		$d = dirname($localFile);

		// make local directory if it doesnt exist
		if(!is_dir($d)){
		    mkdir($d, 0777, true);
		}

		// switch modes
		ftp_pasv($ftp, !$active);

		// get the file for download
		ftp_get($ftp, $localFile, $f, FTP_BINARY);
		
	}

	$html = "OK<br/>";

	return $html;
}

?>