<?php

class ns_cloner_section_select_posttypes extends ns_cloner_section {
	
	public $modes_supported = array('core','clone_over');
	public $id = 'select_posttypes';
	public $ui_priority = 300;
	
	function __construct($cloner){
		parent::__construct($cloner);
		// If table manager is installed and the select_tables section is installed, add this section's ui on to the end. Otherwise this one will do it's own section box.
		add_action( 'ns_cloner_close_section_box_select_tables', array($this,'render_posttypes_ui') );
		// Set up ajax action for js to fetch post types
		add_action( 'wp_ajax_nsc_content_get_post_types', array($this,'ajax_get_post_types') );
	}
	
	function init(){
		// For each table drop operation, check if the do_copy_posts is turned off and don't drop posts & postmeta if it is
		add_filter( 'ns_cloner_do_drop_target_table', array($this,'should_drop_table'), 10, 2 );
		// For each row copy operation, check if it's in the posts table and if it is reject it if the post type was de-selected in the admin controls
		add_filter( 'ns_cloner_do_copy_row', array($this,'should_copy_row'), 10, 3 );
	}
	
	function render(){
		// only show metabox if table manager is not installed
		if( !class_exists('ns_cloner_addon_table_manager') ){
			$this->open_section_box( $this->id, __('Clone Content','ns-cloner'), '', __('Clone Content','ns-cloner') );
			$this->render_posttypes_ui();
			$this->close_section_box();
		}
	}
	
	function render_posttypes_ui(){
		global $wpdb;
		?>
		<h5><?php _e( 'Which post types should be cloned?', 'ns-cloner' ); ?></h5>
		<label>
			<input type="radio" name="do_copy_posts" value="0" /><?php _e('Leave all posts (posts/pages/comments/categories) on target site in place and clone no post types from the source site','ns-cloner'); ?>  
		</label>
		<label>
			<input type="radio" name="do_copy_posts" value="1" checked /><?php _e('Empty all posts (posts/pages/comments/categories) from target site and clone the following post types from the source site:','ns-cloner'); ?>  
		</label>
		<div class="ns-cloner-multi-checkbox-wrapper ns-cloner-select-posttypes-control">
			<img src='<?php echo NS_CLONER_V3_PLUGIN_URL; ?>images/loading.gif' />
		</div>
		<?php		
	}
	
	function ajax_get_post_types(){
		check_ajax_referer( 'ns_cloner', 'nonce' );
		// set up source db access so we can get list of source post types
		$source_id = $_REQUEST['source_id'];
		$this->cloner->set_up_source_vars( $source_id, array('prefix') );
		// fetch all tables including global ones by filtering the global patterns down to just a dummy string (so it won't break the regex)
		$post_types = array();
		$used_post_types = $this->cloner->source_db->get_col("SELECT post_type FROM {$this->cloner->source_prefix}posts GROUP BY post_type");
		foreach( $used_post_types as $post_type ){
			$post_type_object = get_post_type_object($post_type);
			$post_types[ $post_type ] = !is_null($post_type_object)? $post_type_object->label : $post_type;
		}
		// return as json
		header('Content-Type: application/json');
		echo json_encode( array('post_types'=>$post_types) );
		exit;
	}

	function should_drop_table( $bool, $table ){
		// if in clone over mode with 'do_copy_posts' off, leave all posts related tables untouched on target site
		if( $this->cloner->current_clone_mode=='clone_over' && !$this->cloner->request['do_copy_posts'] && preg_match('/(posts|postmeta|comments|commentmeta|term_relationships|term_taxonomy|terms)$/',$table) ){
			$bool = false;
		}
		return $bool;
	}
	
	function should_copy_row( $bool, $row, $table ){
		if( preg_match('/posts$/',$table) && isset($row['post_type']) ){
			return in_array( $row['post_type'], $this->cloner->request['post_types_to_clone'] );
		}
		else{
			return true;
		}
	}
	
}
