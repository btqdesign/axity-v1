<?php
/**
 *
 * Plugin Name: File Manager
 * Author Name: Aftabul Islam
 * Version: 4.1.5
 * Author Email: toaihimel@gmail.com
 * License: GPLv2
 * Description: Manage your file the way you like. You can upload, delete, copy, move, rename, compress, extract files. You don't need to worry about ftp. It is realy simple and easy to use.
 *
 * */

// Directory Seperator
if( !defined( 'DS' ) ){
	
	PHP_OS == "Windows" || PHP_OS == "WINNT" ? define("DS", "\\") : define("DS", "/");
	
} 

// Including elFinder class
require_once('elFinder' . DS . 'elFinder.php');

// Including bootstarter
require_once('BootStart' . DS . 'BootStart.php');

class FM extends FM_BootStart {
	
	/**
	 * 
	 * @var $version Wordpress file manager plugin version
	 * 
	 * */
	public $version;
	
	/**
	 * 
	 * @var $site Site url
	 * 
	 * */
	public $site;
	
	/**
	 * 
	 * @var $giribaz_landing_page Landing page for giribaz
	 * 
	 * */
	public $giribaz_landing_page;
	
	/**
	 * 
	 * @var $support_page Support ticket page
	 * 
	 * */
	public $support_page;
	
	/**
	 * 
	 * @var $feedback_page Feedback page
	 * 
	 * */
	public $feedback_page;
	
	/**
	 * 
	 * @var $file_manager_view_path View path of file manager
	 * 
	 * */
	public $file_manager_view_path;

	public function __construct($name){
		
		$this->version = '4.1.2';
		$this->site = 'http://www.giribaz.com';
		$this->giribaz_landing_page = 'http://www.giribaz.com/wordpress-file-manager-plugin';
		$this->support_page = 'http://giribaz.com/support/';
		$this->feedback_page = 'https://wordpress.org/support/plugin/file-manager/reviews/';
		$this->file_manager_view_path = plugin_dir_path(__FILE__);
		
		// Adding Menu
		$this->menu_data = array(
			'type' => 'menu',
		);

		// Adding Ajax
		$this->add_ajax('connector'); // elFinder ajax call
		$this->add_ajax('fm_site_backup'); // Site backup function invoked

		parent::__construct($name);
		
		// Adding plugins page links
		add_filter('plugin_action_links', array(&$this, 'plugin_page_links'), 10, 2);
		
	}

	/**
	 *
	 * File manager connector function
	 *
	 * */
	public function connector(){
		
		// Checks if the current user have enough authorization to operate.
		if( ! wp_verify_nonce( $_POST['file_manager_security_token'] ,'file-manager-security-token') || !current_user_can( 'manage_options' ) ) wp_die();
		check_ajax_referer('file-manager-security-token', 'file_manager_security_token');
		
		//~ Holds the list of avilable file operations.
		$file_operation_list = array( 
			'open', // Open directory
			'ls',   // File list inside a directory
			'tree', // Subdirectory for required directory
			'parents', // Parent directory for required directory 
			'tmb', // Newly created thumbnail list  
			'size', // Count total file size 
			'mkdir', // Create directory
			'mkfile', // Create empty file
			'rm', // Remove dir/file
			'rename', // Rename file
			'duplicate', // Duplicate file - create copy with "copy %d" suffix
			'paste', // Copy/move files into new destination
			'upload', // Save uploaded file
			'get', // Return file content
			'put', // Save content into text file
			'archive', // Create archive
			'extract', // Extract files from archive
			'search', // Search files
			'info', // File info
			'dim', // Image dimmensions 
			'resize', // Resize image
			'url', // content URL
			'ban', // Ban a user
			'copy', // Copy a file/folder to another location
			'cut', // Cut for file/folder
			'edit', // Edit for files
			'upload', // Upload A file
			'download', // download A file
			);
		
		// Disabled file operations
		$file_operation_disabled = array( 'url', 'info' );
		
		// Allowed mime types 
		$mime_allowed = array( 
			'text',
			'image', 
			'video', 
			'audio', 
			'application',
			'model',
			'chemical',
			'x-conference',
			'message',
			 
			);
			
		$mime_denied = array();
		
		$opts = array(
			'bind' => array(
				'*' => 'logger'
			),
			'debug' => true,
			'roots' => array(
				array(
					'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
					'path'          => ABSPATH,                     // path to files (REQUIRED)
					'URL'           => site_url(),                  // URL to files (REQUIRED)
					'uploadDeny'    => $mime_denied,                // All Mimetypes not allowed to upload
					'uploadAllow'   => $mime_allowed,               // Mimetype `image` and `text/plain` allowed to upload
					'uploadOrder'   => array('allow', 'deny'),      // allowed Mimetype `image` and `text/plain` only
					'accessControl' => 'access',
					'disabled'      => $file_operations_disabled    // List of disabled operations
					//~ 'attributes'
				)
			)
		);
		
		/**
		 * 
		 * @filter fm_options :: Options filter
		 * Implementation Example: add_filter('fm_options', array($this, 'fm_options_test'), 10, 1);
		 * 
		 * */
		$opts = apply_filters('fm_options', $opts, 11, 1);
		$elFinder = new FM_EL_Finder();
		$elFinder = $elFinder->connect($opts);
		$elFinder->run();

		die();
	}
	
	/**
	 * 
	 * @function site_backup Backup functionality invoked
	 * 
	 * */
	public function fm_site_backup(){
		
		echo "Hello Backup";
		die();
		
	}
	
	/**
	 * 
	 * Adds plugin page links,
	 * 
	 * */
	public function plugin_page_links($links, $file){
		
		static $this_plugin;
		
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
		 
		if ($file == $this_plugin){
			array_unshift( $links, '<a target=\'blank\' href="http://www.giribaz.com/support/">'.__("Support", "file-manager").'</a>');
			
			array_unshift( $links, '<a href="admin.php?page=file-manager_settings">'.__("File Manager", "file-manager").'</a>');
				
			if( !defined('FILE_MANAGER_PREMIUM') && !defined('FILE_MANAGER_BACKEND') )
				array_unshift( $links, '<a target=\'blank\' class="file-manager-admin-panel-pro" href="http://www.giribaz.com/wordpress-file-manager-plugin/" style="color: white; font-weight: bold; background-color: red; padding-right: 5px; padding-left: 5px; border-radius: 40%;">'.__("Pro", "file-manager").'</a>');
		
		}
		
		return $links;
	}
	
}

/**
 * 
 * @function logger
 * 
 * Logs file file manager actions
 * 
 * */
function logger($cmd, $result, $args, $elfinder) {
	
	global $FileManager;
	
	$log = sprintf("[%s] %s: %s \n", date('r'), strtoupper($cmd), var_export($result, true));
	$logfile = $FileManager->upload_path . DS . 'log.txt';
	$dir = dirname($logfile);
	if (!is_dir($dir) && !mkdir($dir)) {
		return;
	}
	if (($fp = fopen($logfile, 'a'))) {
		fwrite($fp, $log);
		fclose($fp);
	}
	return;

	foreach ($result as $key => $value) {
		if (empty($value)) {
			continue;
		}
		$data = array();
		if (in_array($key, array('error', 'warning'))) {
			array_push($data, implode(' ', $value));
		} else {
			if (is_array($value)) { // changes made to files
				foreach ($value as $file) {
					$filepath = (isset($file['realpath']) ? $file['realpath'] : $elfinder->realpath($file['hash']));
					array_push($data, $filepath);
				}
			} else { // other value (ex. header)
				array_push($data, $value);
			}
		}
		$log .= sprintf(' %s(%s)', $key, implode(', ', $data));
	}
	$log .= "\n";

	$logfile = $FileManager->upload_path . DS . 'log.txt';
	$dir = dirname($logfile);
	if (!is_dir($dir) && !mkdir($dir)) {
		return;
	}
	if (($fp = fopen($logfile, 'a'))) {
		fwrite($fp, $log);
		fclose($fp);
	}
}

global $FileManager;
$FileManager = new FM('File Manager');
