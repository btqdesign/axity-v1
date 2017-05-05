<?php

class ns_cloner_addon_content_users extends ns_cloner_addon {
	
	var $version = '1.0.2';
	
	function __construct(){
		$this->title = __( 'NS Cloner Content & Users', 'ns-cloner' );
		// set paths here since if we do that from the parent class they will be wrong.
		$this->plugin_path = plugin_dir_path( dirname(__FILE__) ); 
		$this->plugin_url = plugin_dir_url( dirname(__FILE__) );
		parent::__construct();
		// unregister call-to-action placeholder sections which this add-on creates the real thing for
		add_filter( 'ns_cloner_do_load_section_copy_tables_cta', '__return_false' );
		add_filter( 'ns_cloner_do_load_section_copy_users_cta', '__return_false' );
		add_filter( 'ns_cloner_do_load_section_copy_files_cta', '__return_false' );
	}
	
	function init( $cloner ){
		parent::init( $cloner );
		$this->cloner->register_mode(
			'clone_over', 
			array(
				'title' => __( 'Clone Over Existing Site', 'ns-cloner' ),
				'button_text' => __( 'Clone Over', 'ns-cloner' ),
				'description' => __( 'Rather than creating a new site as the clone destination, replace the content of one or more existing target sites with the source site\'s content. <br/><br/> Any uploads or plugin tables which are on the target site but not on the source site will be left in place (target site uploads won\'t be available in the media library, but the files will still be there).', 'ns-cloner' ),
				'report_message' => __( 'Clone over complete!', 'ns-cloner' ) 
			),
			array('select_source','search_replace','additional_settings')
		);
		$this->cloner->load_section( 'clone-over', $this->plugin_path );
		$this->cloner->load_section( 'select-posttypes', $this->plugin_path );
		$this->cloner->load_section( 'copy-users', $this->plugin_path );
		$this->cloner->load_section( 'copy-files', $this->plugin_path );
		// Filter source tables based on selection of replace or leave content tables in place
		if ( $this->cloner->current_clone_mode=='clone_over' && !$this->cloner->request['do_copy_posts'] ) {
			add_filter( 'ns_cloner_site_tables', array($this,'filter_tables_to_clone'), 10, 3 );
		}
	}

	function admin_init(){		
		// register js
		add_action( 'admin_enqueue_scripts', array($this,'admin_assets') );
	}
	
	function admin_assets(){
		// ouput js
		if( ns_is_admin_page($this->cloner->menu_slug) ){
			wp_enqueue_script( 'nsc-content-users', $this->plugin_url.'js/ns-cloner-addon-content-users.js', array('jquery','ns-cloner'), $this->version );
		}
	}
	
	function filter_tables_to_clone( $tables, $db, $prefix ){
		$clone_tables = array();
		$skip_tables = array();
		foreach( $tables as $table ) {
			if ( !preg_match( '/(posts|postmeta|comments|commentmeta|term_relationships|term_taxonomy|terms)$/', $table ) ) {
				array_push( $clone_tables, $table );
			} else {
				array_push( $skip_tables, $table );
			}
		}			
		$this->cloner->dlog( array('Skipping source tables with leave all posts selected:', $skip_tables ) );
		return $clone_tables;
	}
}

?>