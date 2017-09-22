<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'ImageZoooom' ) ) :
/**
 * Main ImageZoooom Class
 *
 * @class ImageZoooom
 */
final class ImageZoooom {
    public $version = '1.0.0';
    protected static $_instance = null; 

    /**
     * Main ImageZoooom Instance
     *
     * Ensures only one instance of ImageZoooom is loaded or can be loaded
     *
     * @static
     * @return ImageZoooom - Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
      * Cloning is forbidden.
      */
    public function __clone() {
         _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'zoooom' ), '1.0' );
    }

    /**
     * Unserializing instances of this class is forbidden.
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'zoooom' ), '1.0' );
    }

    /**
     * Image Zoooom Constructor
     * @access public
     * @return ImageZoooom
     */
    public function __construct() {
        
        if ( is_admin() ) {
            include_once( 'includes/image-zoom-admin.php' );
            add_action( 'admin_menu', 'add_menu' );
            add_action( 'admin_init', 'register_mysettings' );
        } else {
            include_once( 'includes/image-zoom-page.php' ); 
        }
    }

}

endif; 

/**
 * Returns the main instance of ImageZoooom
 *
 * @return ImageZoooom
 */
function ImageZoooom() {
    return ImageZoooom::instance();
}
