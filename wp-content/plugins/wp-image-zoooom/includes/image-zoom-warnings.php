<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * ImageZoooom_Warnings
 */
class ImageZoooom_Warnings {

    var $allowed_actions = array(
        'iz_dismiss_ajax_product_filters',
        'iz_dismiss_jetpack',
        'iz_dismiss_avada',
        'iz_dismiss_shopkeeper',
        'iz_dismiss_bwp_minify',
        'iz_dismiss_wooswipe',
    );

    var $notices = array();

    /**
     * Constructor
     */
    public function __construct() {

        add_action( 'wp_ajax_iz_dismiss', array( $this, 'notice_dismiss' ) );

        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        } 

        if ( isset( $_SERVER ) && isset( $_SERVER['REQUEST_URI'] ) ) {
            if ( strpos( $_SERVER['REQUEST_URI'], 'zoooom_settings' ) === false ) 
                return;
        }

        $this->iz_dismiss_ajax_product_filters();
        $this->iz_dismiss_jetpack();
        $this->iz_dismiss_avada();
        $this->iz_dismiss_shopkeeper();
        $this->iz_dismiss_bwp_minify();
        $this->iz_dismiss_wooswipe();

        add_action( 'admin_notices', array($this, 'show_admin_notice') );
    }


    /**
     * Warning about AJAX product filter plugins
     */
    function iz_dismiss_ajax_product_filters() {
        $continue = false;

        $general = get_option('zoooom_general');
        if ( isset($_POST['tab'] )) {
            $general['woo_cat'] = (isset($_POST['woo_cat'])) ? true : false;
        }
        if ( ! isset($general['woo_cat']) || $general['woo_cat'] != true ) return false;

        if ( is_plugin_active( 'woocommerce-ajax-filters/woocommerce-filters.php' ) ) $continue = true;
        if ( is_plugin_active( 'load-more-products-for-woocommerce/load-more-products.php' ) ) $continue = true;
        if ( is_plugin_active( 'wc-ajax-product-filter/wcapf.php' ) ) $continue = true;

        if ( !$continue ) return false;

        $article_url = 'https://www.silkypress.com/wp-image-zoom/zoom-woocommerce-category-page-ajax/';
        
        $message = sprintf(__( 'You are using the zoom on WooCommerce shop pages in combination with a plugin that loads more products with AJAX (a product filter plugin or a "load more" products plugin). You\'ll notice that the zoom isn\'t applied after new products are loaded with AJAX. Please read <a href="%1$s" target="_blank">this article for a solution</a>.', 'wp-image-zoooom' ), $article_url);

        $this->add_notice( 'iz_dismiss_ajax_product_filters', $message );
    }


    /**
     * Check if Jetpack Photon module is active
     */
    function iz_dismiss_jetpack() {
        if ( ! defined('JETPACK__VERSION' ) ) return false; 

        if ( ! Jetpack::is_module_active( 'photon' ) ) return false; 

        $message = __( 'WP Image Zoom plugin is not compatible with the <a href="admin.php?page=jetpack">Jetpack Photon</a> module. If you find that the zoom is not working, try to deactivate the Photon module and see if that solves it.', 'wp-image-zoooom' );

        $this->add_notice( 'iz_dismiss_jetpack', $message );
    }


    /**
     * Check if the Avada theme is active
     */
    function iz_dismiss_avada() {
        if ( get_template() != 'Avada' ) return false;

        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) return false;

        $flexslider_url = 'https://woocommerce.com/flexslider/';
        $pro_url = 'https://www.silkypress.com/wp-image-zoom-plugin/?utm_source=wordpress&utm_campaign=iz_free&utm_medium=banner';
        
        $message = sprintf( __( 'The WP Image Zoom plugin <b>will not work</b> on the WooCommerce products gallery with the Avada theme. The Avada theme changes entirely the default WooCommerce gallery with the <a href="%1$s" target="_blank">Flexslider gallery</a> and the zoom plugin does not support the Flexslider gallery. Please check the <a href="%2$s" target="_blank">PRO version</a> of the plugin for compatibility with the Flexslider gallery.', 'wp-image-zoooom' ), $flexslider_url, $pro_url );

        $this->add_notice( 'iz_dismiss_avada', $message );
    }


    /**
     * Check if for the Shopkeeper theme 
     */
    function iz_dismiss_shopkeeper() {
        if ( get_template() != 'shopkeeper' ) return false;

        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) return false;

        $pro_url = 'https://www.silkypress.com/wp-image-zoom-plugin/?utm_source=wordpress&utm_campaign=iz_free&utm_medium=banner';

        $message = sprintf( __( 'The WP Image Zoom plugin <b>will not work</b> on the WooCommerce products gallery with the Shopkeeper theme. The Shopkeeper theme changes entirely the default WooCommerce gallery with a custom made gallery not supported by the free version of the WP Image Zoom plugin. Please check the <a href="%1$s" target="_blank">PRO version</a> of the plugin for compatibility with the Shopkeeper\'s gallery.', 'wp-image-zoooom' ), $pro_url );

        $this->add_notice( 'iz_dismiss_shopkeeper', $message, 'updated settings-error notice is-dismissible' );
    }


    /**
     * Warning about BWF Minify settings 
     */
    function iz_dismiss_bwp_minify() {
        if ( ! is_plugin_active( 'bwp-minify/bwp-minify.php' ) ) return false;

        $url = 'https://www.silkypress.com/wp-content/uploads/2016/09/image-zoom-bwp.png';

        $message = sprintf(__( '<b>If the zoom does not show up</b> on your website, it could be because you need to add the “image_zoooom-init” and the “image_zoooom” to the “Scripts to NOT minify” option in the BWP Minify settings, as shown in <a href="%1$s" target="_blank">this screenshot</a>.', 'wp-image-zoooom' ), $url);

        $this->add_notice( 'iz_dismiss_bwp_minify', $message );
    }


    /**
     * Warning about WooSwipe plugin 
     */
    function iz_dismiss_wooswipe() {

        if ( ! is_plugin_active( 'wooswipe/wooswipe.php' ) ) return false;

        $pro_url = 'https://www.silkypress.com/wp-image-zoom-plugin/?utm_source=wordpress&utm_campaign=iz_free&utm_medium=banner';
        $wooswipe_url = 'https://wordpress.org/plugins/wooswipe/';

        $message = sprintf( __( 'WP Image Zoom plugin is <b>not compatible with the <a href="%1$s">WooSwipe WooCommerce Gallery</a> plugin</b>. You can try the zoom plugin with the default WooCommerce gallery by deactivating the WooSwipe plugin. Alternatively, you can upgrade to the WP Image Zoom Pro version, where the issue with the WooSwipe plugin is fixed.' ), $wooswipe_url, $pro_url);

        $this->add_notice( 'iz_dismiss_woo_swipe', $message );
    }


    /**
     * Add this message to the $this->notices array
     */
    function add_notice($id, $message, $class = '') {
        if ( get_option($id) != false ) return false;

        $notice = array(
            'id'        => $id,
            'message'   => $message,
        );
        if ( !empty($class) ) $notice['class'] = $class;

        $this->notices[] = $notice;
    }


    /**
     * Show the admin notices
     * */
    function show_admin_notice() {
        if ( !is_array($this->notices) || count($this->notices) == 0 ) return;

        foreach( $this->notices as $_n ) {
            $nonce =  wp_create_nonce( $_n['id'] );
            if ( !isset($_n['class'])) $_n['class'] = 'notice notice-warning is-dismissible';
            $_n['class'] .= ' iz-notice-dismiss';
            printf( '<div class="%1$s" id="%2$s" data-nonce="%3$s"><p>%4$s</p></div>', $_n['class'], $_n['id'], $nonce, $_n['message'] );
        }
            ?>
                <script type='text/javascript'>
                jQuery(function($){
                    $(document).on( 'click', '.iz-notice-dismiss', function() {
                        var id = $(this).attr('id');
                        var data = {
                            action: 'iz_dismiss',
                            option: id, 
                            nonce: $(this).data('nonce'),
                        };
                        $.post(ajaxurl, data, function(response ) {
                            $('#'+id).fadeOut('slow');
                        });
                    });
                });
                </script>
            <?php
    }


    /**
     * Ajax response for `notice_dismiss` action
     */
    function notice_dismiss() {

        $option = $_POST['option'];

        if ( ! in_array($option, $this->allowed_actions ) ) return; 

        check_ajax_referer( $option, 'nonce' );

        update_option( $option, 1 );

        wp_die();
    }

}


return new ImageZoooom_Warnings();
