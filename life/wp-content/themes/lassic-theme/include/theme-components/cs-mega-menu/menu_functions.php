<?php
class cs_mega_custom_menu {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {

		// load the plugin translation files
		add_action( 'init', array( $this, 'textdomain' ) );
		
		// add custom menu fields to menu
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'cs_mega_add_custom_nav_fields' ) );

		// save menu custom fields
		add_action( 'wp_update_nav_menu_item', array( $this, 'cs_mega_update_custom_nav_fields'), 10, 3 );
		
		// edit menu walker
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'cs_mega_edit_walker'), 10, 2 );

	} // end constructor
	
	
	/**
	 * Load the plugin's text domain
		 * @since 1.0
		 * @return void
	 */
	public function textdomain() {
		load_plugin_textdomain( 'cs_mega', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	/**
	 * Add custom fields to $item nav object
	 * in order to be used in custom Walker
		 * @access      public
	 * @since       1.0 
	 * @return      void
	*/
	function cs_mega_add_custom_nav_fields( $menu_item ) {
	
	    $menu_item->megamenu = get_post_meta( $menu_item->ID, '_menu_item_megamenu', true );
		$menu_item->text = get_post_meta( $menu_item->ID, '_menu_item_text', true );
	  	$menu_item->link = get_post_meta( $menu_item->ID, '_menu_item_link', true );
		//$menu_item->bg = get_post_meta( $menu_item->ID, '_menu_item_bg', true );
	    return $menu_item;
	    
	}
	
	/**
	 * Save menu custom fields
		 * @access      public
	 * @since       1.0 
	 * @return      void
	*/
	function cs_mega_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
	
 		// Check if element is properly sent
	    	$megamenu_value = 'off';
			$text_value = 'off';
			$link_value = 'off';
 	        if(isset($_REQUEST['menu-item-megamenu'][$menu_item_db_id]) ){ $megamenu_value =$_REQUEST['menu-item-megamenu'][$menu_item_db_id];}else{
				$megamenu_value = 'off';
			}
			 if(isset($_REQUEST['menu-item-text'][$menu_item_db_id]) ){ $text_value =$_REQUEST['menu-item-text'][$menu_item_db_id];}else{
				$text_value = 'off';
			}
			 if(isset($_REQUEST['menu-item-link'][$menu_item_db_id]) ){ $link_value =$_REQUEST['menu-item-link'][$menu_item_db_id];}else{
				$link_value = 'off';
			}
		//	if(isset($_REQUEST['menu-item-bg'][$menu_item_db_id])){ $bg_value = $_REQUEST['menu-item-bg'][$menu_item_db_id]; }else{
			//	$bg_value = '';
			//}
			//if(isset($megamenu_value) and $megamenu_value ){ $megamenu_value= "on"; }else{ $megamenu_value = 'off';}
	        update_post_meta( $menu_item_db_id, '_menu_item_megamenu', $megamenu_value );
			update_post_meta( $menu_item_db_id, '_menu_item_text', $text_value );
			update_post_meta( $menu_item_db_id, '_menu_item_link', $link_value );
		//update_post_meta( $menu_item_db_id, '_menu_item_bg', $bg_value );
	    
	    
	}
	
	/**
	 * Define new Walker edit
		 * @access      public
	 * @since       1.0 
	 * @return      void
	*/
	function cs_mega_edit_walker($walker,$menu_id) {
	
	    return 'Walker_Nav_Menu_Edit_Custom';
	    
	}

}

// instantiate plugin's class
$GLOBALS['sweet_custom_menu'] = new cs_mega_custom_menu();
?>