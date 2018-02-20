<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/* include library file for WP tables */
if( ! class_exists( 'WPBackitup_WP_List_Table' ) ) {
    include_once( WPBACKITUP__PLUGIN_PATH .'vendor/WordPress/class-wp-list-table.php');
}
if( !class_exists( 'WPBackItUp_Filesystem' ) ) {
    include_once 'class-filesystem.php';
}
/**
 * WP BackItUp  - Download Logs class
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 * @since   1.13.6
 *
 */
class WPBackitup_Download_Logs extends WPBackitup_WP_List_Table {
	/**
	 * Constructor.
	 *
	 * The child class should call this constructor from its own constructor to override
	 * the default $args.
	 *
	 * @since 1.13.6
	 * @access public
	 *
	 * @param array|string $args {
	 *     Array or string of arguments.
	 *
	 *     @type string $plural   Plural value used for labels and the objects being listed.
	 *                            This affects things such as CSS class-names and nonces used
	 *                            in the list table, e.g. 'posts'. Default empty.
	 *     @type string $singular Singular label for an object being listed, e.g. 'post'.
	 *                            Default empty
	 *     @type bool   $ajax     Whether the list table supports Ajax. This includes loading
	 *                            and sorting data, for example. If true, the class will call
	 *                            the _js_vars() method in the footer to provide variables
	 *                            to any scripts handling Ajax events. Default false.
	 *     @type string $screen   String containing the hook name used to determine the current
	 *                            screen. If left null, the current screen will be automatically set.
	 *                            Default null.
	 * }
	 */
	function __construct(){
		global $page;
		
		error_reporting(0);
		parent::__construct( array(
			'singular'  => 'download_log',  
			'plural'    => 'download_logs',
			'ajax'      => false      
		 ));
		 
		add_action('admin_post_download_backup', array(__CLASS__,'admin_download_backup'));
	}

	/**
	 *
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return mixed|void
	 */
	function column_default($item, $column_name){
		switch($column_name){
			case 'create_date':
			case 'job_name':
			case 'job_size':
				return $item[$column_name];
			default:
				return print_r($item,true);
		}
	}
	
	/**
	* download logs file
	*/
	function admin_download_backup(){
		
		include_once( WPBACKITUP__PLUGIN_PATH.'/lib/includes/handler_download.php' );
	}

	/**
	 *
	 * @param object $item
	 *
	 * @return string
	 */
	function column_job_name($item){

		$actions = array(
			'delete'  	=> sprintf('<a href="?page=%s&action=%s&delete_log=%s&s=%s" onclick="%s">'.__('Delete', 'wp-backitup').'</a>','wp-backitup-support&tab=download-logs','delete',$item['job_name'],wp_create_nonce('wp-backitup'. "-delete_log"),"return confirm('".__('Are you sure?', 'wp-backitup')."')"),
			'download'	=> sprintf('<a href="'.$item['job_name'].'" class="logs_backup">'.__('Download', 'wp-backitup').'</a>',$item['job_name'],'download')

		);

		//Return the title contents
		return sprintf('%1$s %2$s',$item['job_name'],$this->row_actions($actions));
	}

	/**
	 *
	 * @param object $item
	 *
	 * @return string|void
	 */
	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  
			$item['job_name']               
		);
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since 3.1.0
	 * @access public
	 * @abstract
	 *
	 * @return array
	 */
	function get_columns(){
		$columns = array(
			'cb'        	=>'<input type="checkbox" />', 
			'create_date'	=>__('Time','wp-backitup'),
			'job_name'		=>__('Log File','wp-backitup'),
			'job_size'		=>__('Size','wp-backitup')
		 
		);
		return $columns;
	}
	
	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since 1.13.6
	 * @access public
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'create_date' => array('create_date',false),
			'job_name'    => array('job_name',false),
			'job_size'	  =>array('job_size',true),
			
		);
		return $sortable_columns;
	}
	
	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 1.13.6
	 * @access public
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array('delete' => 'Delete');
		return $actions;
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 1.13.6
	 * @access public
	 *
	 * @return array
	 */
	public function process_bulk_action() {

		$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
		$action = 'bulk-' . $this->_args['plural'];
		if ( false !== wp_verify_nonce( $nonce, $action ) ) {
			if( 'delete'===$this->current_action() ) {
				foreach($_POST['download_log'] as $single_val){
					$path = WPBACKITUP__LOGS_PATH.'/'.basename(sanitize_file_name($single_val));
					unlink($path);
				}

				$redirect = get_admin_url(null,'admin.php?page=wp-backitup-support&tab=download-logs' );
	            wp_safe_redirect($redirect);
			}
		}
	}

	/**
	 * Prepares the list of items for displaying.
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 * @since 1.13.6
	 * @access public
	 * @abstract
	 */
	function prepare_items() {

		$per_page 				= 15;
		$columns 				= $this->get_columns();
		$hidden 				= array();
		$sortable 				= $this->get_sortable_columns();
		$this->_column_headers 	= array($columns, $hidden, $sortable);        
		$this->process_bulk_action();
		$path = WPBACKITUP__LOGS_PATH;
		$extensions = "zip";
		$fileSystem = new WPBackItUp_FileSystem($this->log_name);
		$file_list  = $fileSystem->get_fileonly_list($path, $extensions);

		foreach($file_list as $file){
			$create_date = $fileSystem->get_filetime_with_filename($file);
			$log_name    = explode('logs/',$file);
			$job_name    = $log_name[1];
			$job_size    = $fileSystem->format_file_size_kb(ceil(filesize($file) /1024));
			$data[]      = array('create_date' => $create_date , 'job_name' =>$job_name , 'job_size'=>$job_size);
		}
		
		function usort_reorder($a,$b){
			$orderby 	 = isset($_REQUEST['orderby']) ? $_REQUEST['orderby']: "create_date";
        	$order 		 = isset($_REQUEST['order']) ? $_REQUEST['order']: "desc";
        	if(isset($orderby) && !empty($orderby)){
        		$result 	 = strcmp($a[$orderby], $b[$orderby]);	
        		return ($order==='asc') ? $result : -$result;
        	}
		}
		
	    usort($data, 'usort_reorder');
		$this->screen = get_current_screen();
		$current_page = $this->get_pagenum();        
		$total_items  = count($data);
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page); 
		$this->items = $data;
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items/$per_page)
		) );
	}
} //end class
