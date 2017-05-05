<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * ImageZoooom_Warnings
 */
class ImageZoooom_Warnings {

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

        $this->check_jetpack();
        $this->check_avada();
        $this->check_bwf_minify();
    }

    /**
     * Warning about BWF settings 
     */
    function check_bwf_minify() {

        if ( ! is_plugin_active( 'bwp-minify/bwp-minify.php' ) ) return false;

        $option = get_option( 'iz_dismiss_bwp_minify', '' );

        if ( !empty( $option ) ) {
            return;
        }

        add_action( 'admin_notices', array( $this, 'check_bwf_notice' ) );

    }

    /**
     * Show a warning about the BWF Minify Settings 
     */
    function check_bwf_notice() {
        $id = 'iz_dismiss_bwp_minify';
        $class = 'notice notice-warning is-dismissible';
        $url = 'https://www.silkypress.com/wp-content/uploads/2016/09/image-zoom-bwp.png';
        $message = sprintf(__( '<b>If the zoom does not show up</b> on your website, it could be because you need to add the “image_zoooom-init” and the “image_zoooom” to the “Scripts to NOT minify” option in the BWP Minify settings, as shown in <a href="%1$s" target="_blank">this screenshot</a>.', 'wp-image-zoooom' ), $url);;

        printf( '<div class="%1$s" id="%2$s"><p>%3$s</p></div>', $class, $id, $message );

        $this->dismiss_js( $id );

    }





    /**
     * Check if the Avada theme is active
     */
    function check_avada() {
        if ( get_template() != 'Avada' ) return false;


        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) return false;

        $option = get_option( 'iz_dismiss_avada', '' );

        if ( !empty( $option ) ) {
            return;
        }

        add_action( 'admin_notices', array( $this, 'check_avada_notice' ) );

    }


    /**
     * Show a warning about the Avada theme 
     */
    function check_avada_notice() {
        $id = 'iz_dismiss_avada';
        $class = 'notice notice-warning is-dismissible';
        $flexslider_url = 'https://woocommerce.com/flexslider/';
        $pro_url = 'https://www.silkypress.com/wp-image-zoom-plugin/?utm_source=wordpress&utm_campaign=iz_free&utm_medium=banner';
        $message = sprintf( __( 'The WP Image Zoom plugin <b>will not work</b> on the WooCommerce products gallery with the Avada theme. The Avada theme changes entirely the default WooCommerce gallery with the <a href="%1$s" target="_blank">Flexslider gallery</a> and the zoom plugin does not support the Flexslider gallery. Please check the <a href="%2$s" target="_blank">PRO version</a> of the plugin for compatibility with the Flexslider gallery.', 'wp-image-zoooom' ), $flexslider_url, $pro_url );

        printf( '<div class="%1$s" id="%2$s"><p>%3$s</p></div>', $class, $id, $message );

        $this->dismiss_js( $id );

    }


    /**
     * Check if Jetpack Photon module is active
     */
    function check_jetpack() {
        if ( ! defined('JETPACK__VERSION' ) ) {
            return;
        } 

        if ( ! Jetpack::is_module_active( 'photon' ) ) {
            return;
        }

        $option = get_option( 'iz_dismiss_jetpack', '' );

        if ( !empty( $option ) ) {
            return;
        }

        add_action( 'admin_notices', array( $this, 'check_jetpack_notice' ) );
    }

    /**
     * Show a warning about Jetpack Photon module
     */
    function check_jetpack_notice() {
        $id = 'iz_dismiss_jetpack';
        $class = 'notice notice-warning is-dismissible';
        $message = __( 'WP Image Zoom plugin is not compatible with the <a href="admin.php?page=jetpack">Jetpack Photon</a> module. If you find that the zoom is not working, try to deactivate the Photon module and see if that solves it.', 'wp-image-zoooom' );

        printf( '<div class="%1$s" id="%2$s"><p>%3$s</p></div>', $class, $id, $message );

        $this->dismiss_js( $id );

    }

    /**
     * Allow the dismiss button to remove the notice
     */
    function dismiss_js( $slug ) {
    ?>
        <script type='text/javascript'>
        jQuery(function($){
            $(document).on( 'click', '#<?php echo $slug; ?> .notice-dismiss', function() {
            var data = {
                action: 'iz_dismiss',
                option: '<?php echo $slug; ?>',
            };
            $.post(ajaxurl, data, function(response ) {
                $('#<?php echo $slug; ?>').fadeOut('slow');
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

        update_option( $option, 1 );

        wp_die();
    }

}


return new ImageZoooom_Warnings();
