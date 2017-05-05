<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * ImageZoooom_Admin 
 */
class ImageZoooom_Admin {

    public $messages = array();
    private $tab = 'general';

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_head', array( $this, 'iz_add_tinymce_button' ) );
    }

    /**
     * Add menu items
     */
    public function admin_menu() {
        add_menu_page(
            __( 'WP Image Zoom', 'wp-image-zoooom' ),
            __( 'WP Image Zoom', 'wp-image-zoooom' ),
            'administrator',
            'zoooom_settings',
            array( $this, 'admin_settings_page' ),
            ImageZoooom()->plugins_url() . '/assets/images/icon.svg'
        );
    }

    /**
     * Load the javascript and css scripts
     */
    public function admin_enqueue_scripts( $hook ) {
        if ( $hook != 'toplevel_page_zoooom_settings' )
            return false;

        $iz = ImageZoooom();
        $v = ImageZoooom::$version;

        // Register the javascript files
        if ( $iz->testing == true ) {
//            wp_register_script( 'bootstrap', $iz->plugins_url( '/assets/js/bootstrap.min.js' ), array( 'jquery' ), $v, true  );
            wp_register_script( 'bootstrap', $iz->plugins_url( '/assets/js/bootstrap.3.2.0.min.js' ), array( 'jquery' ), $v, true  );
            wp_register_script( 'image_zoooom', $iz->plugins_url( '/assets/js/jquery.image_zoom.js' ), array( 'jquery' ), $v, true );
            if ( !isset($_GET['tab']) || $_GET['tab'] == 'settings' ) {
                wp_register_script( 'zoooom-settings', $iz->plugins_url( '/assets/js/image_zoom.settings.free.js' ), array( 'image_zoooom' ), $v, true );
            }
        } else {
//          wp_register_script( 'bootstrap', $iz->plugins_url( '/assets/js/bootstrap.min.js' ), array( 'jquery' ), $v, true  );
            wp_register_script( 'bootstrap', $iz->plugins_url( '/assets/js/bootstrap.3.2.0.min.js' ), array( 'jquery' ), $v, true  );
            wp_register_script( 'image_zoooom', $iz->plugins_url( '/assets/js/jquery.image_zoom.min.js' ), array( 'jquery' ), $v, true );
            if ( !isset($_GET['tab']) || $_GET['tab'] == 'settings' ) {
                wp_register_script( 'zoooom-settings', $iz->plugins_url( '/assets/js/image_zoom.settings.min.js' ), array( 'image_zoooom' ), $v, true );
            }
        }

        // Enqueue the javascript files
        wp_enqueue_script( 'bootstrap' );
        wp_enqueue_script( 'image_zoooom' );
        wp_enqueue_script( 'zoooom-settings' );

        // Register the css files
        wp_register_style( 'bootstrap', $iz->plugins_url( '/assets/css/bootstrap.min.css' ), array(), $v );
        if ( $iz->testing == true ) {
            wp_register_style( 'zoooom', $iz->plugins_url( '/assets/css/style.css' ), array(), $v );
        } else {
            wp_register_style( 'zoooom', $iz->plugins_url( '/assets/css/style.min.css' ), array(), $v );
        }

        // Enqueue the css files
        wp_enqueue_style( 'bootstrap' );
        wp_enqueue_style( 'zoooom' );
    }

    /**
     * Build an array with settings that will be used in the form
     * @access public
     */
    public function get_settings( $id  = '' ) {
        $settings = array(
            'lensShape' => array(
                'label' => __('Lens Shape', 'wp-image-zoooom'),
                'values' => array(
                    'none' => array('icon-lens_shape_none', __('No Lens', 'zoooom')),
                    'round' => array('icon-lens_shape_circle', __('Circle Lens', 'zoooom')),
                    'square' => array('icon-lens_shape_square', __('Square Lens', 'zoooom')),
                    'zoom_window' => array('icon-type_zoom_window', __('With Zoom Window', 'zoooom')),
                ),
                'value' => 'zoom_window',
                'input_form' => 'buttons',
                'buttons' => 'i',
            ),
            'cursorType' => array(
                'label' => __('Cursor Type', 'wp-image-zoooom'),
                'values' => array(
                    'default' => array('icon-cursor_type_default', __('Default', 'zoooom' ) ),
                    'pointer' => array('icon-cursor_type_pointer', __('Pointer', 'zoooom' ) ),
                    'crosshair' => array('icon-cursor_type_crosshair', __('Crosshair', 'zoooom' ) ),
                    'zoom-in' => array('icon-zoom-in', __('Zoom', 'zoooom' ) ),
                ),
                'value' => 'default',
                'input_form' => 'buttons',
                'buttons' => 'i',
            ),
            'zwEasing' => array(
                'label' => __('Animation Easing Effect', 'wp-image-zoooom' ),
                'value' => 12,
                'description' => __('A number between 0 and 200 to represent the degree of the Animation Easing Effect', 'wp-image-zoooom' ),
                'input_form' => 'input_text',
            ),

            'lensSize' => array(
                'label' => __('Lens Size', 'wp-image-zoooom' ),
                'post_input' => 'px',
                'value' => 200,
                'description' => __('For Circle Lens it means the diameters, for Square Lens it means the width', 'wp-image-zoooom' ),
                'input_form' => 'input_text',
            ),
            'borderThickness' => array(
                'label' => __('Border Thickness', 'wp-image-zoooom' ),
                'post_input' => 'px',
                'value' => 1,
                'input_form' => 'input_text',
            ),
            'borderColor' => array(
                'label' => __('Border Color', 'wp-image-zoooom' ),
                'value' => '#ffffff',
                'input_form' => 'input_color',
            ),
            'lensFade' => array(
                'label' => __('Fade Time', 'wp-image-zoooom' ),
                'post_input' => 'sec',
                'value' => 1,
                'description' => __('The amount of time it takes for the Lens to slowly appear or dissapear', 'wp-image-zoooom'),
                'input_form' => 'input_text',
            ),
            'tint' => array(
                'label' => __('Tint', 'wp-image-zoooom'),
                'value' => false,
                'description' => __('A color that will layed on top the of non-magnified image in order to emphasize the lens', 'wp-image-zoooom'),
                'input_form' => 'checkbox',
            ),
            'tintColor' =>array(
                'label' => __('Tint Color', 'wp-image-zoooom'),
                'value' => '#ffffff',
                'input_form' => 'input_color',
            ),
            'tintOpacity' => array(
                'label' => __('Tint Opacity', 'wp-image-zoooom'),
                'value' => '0.5',
                'post_input' => '%',
                'input_form' => 'input_text',
            ),
            'zwWidth' => array(
                'label' => __('Zoom Window Width', 'wp-image-zoooom'),
                'post_input' => 'px',
                'value' => 400,
                'input_form' => 'input_text',
            ),
            'zwHeight' => array(
                'label' => __('Zoom Window Height', 'wp-image-zoooom'),
                'post_input' => 'px',
                'value' => 360,
                'input_form' => 'input_text',
            ),
            'zwPadding' => array(
                'label' => __('Distance from the Main Image', 'wp-image-zoooom'),
                'post_input' => 'px',
                'value' => 10,
                'input_form' => 'input_text',
            ),
            'zwBorderThickness' => array(
                'label' => __('Border Thickness', 'wp-image-zoooom'),
                'post_input' => 'px',
                'value' => 4,
                'input_form' => 'input_text',
            ),
            'zwShadow' => array(
                'label' => __('Shadow Thickness', 'wp-image-zoooom'),
                'post_input' => 'px',
                'value' => 4,
                'input_form' => 'input_text',
                'description' => __('Use 0px to remove the shadow', 'wp-image-zoooom'),
            ),
            'zwBorderColor' => array(
                'label' => __('Border Color', 'wp-image-zoooom'),
                'value' => '#888888',
                'input_form' => 'input_color',
            ),
            'zwBorderRadius' => array(
                'label' => __('Rounded Corners', 'wp-image-zoooom'),
                'post_input' => 'px',
                'value' => 0,
                'input_form' => 'input_text',
            ),
            'zwFade' => array(
                'label' => __('Fade Time', 'wp-image-zoooom'),
                'post_input' => 'sec',
                'value' => 0,
                'description' => __('The amount of time it takes for the Zoom Window to slowly appear or disappear', 'wp-image-zoooom'),
                'input_form' => 'input_text',
            ),
            'enable_woocommerce' => array(
                'label' => __('Enable the zoom on WooCommerce products', 'wp-image-zoooom'),
                'value' => true,
                'input_form' => 'checkbox',
            ),
            'exchange_thumbnails' => array(
                'label' => __('Exchange the thumbnail with main image on WooCommerce products', 'wp-image-zoooom'),
                'value' => true,
                'input_form' => 'checkbox',
                'description' => __('On a WooCommerce gallery, when clicking on a thumbnail, not only the main image will be replaced with the thumbnail\'s image, but also the thumbnail will be replaced with the main image', 'wp-image-zoooom'),
            ),
            'enable_mobile' => array(
                'label' => __('Enable the zoom on mobile devices', 'wp-image-zoooom'),
                'value' => false,
                'input_form' => 'checkbox',
            ),
            'woo_cat' => array(
                'label' => __('Enable the zoom on WooCommerce category pages', 'wp-image-zoooom'),
                'value' => false,
                'input_form' => 'checkbox',
            ),

            'force_woocommerce' => array(
                'label' => __('Force it to work on WooCommerce', 'wp-image-zoooom'),
                'value' => true,
                'input_form' => 'checkbox',
            ),
        );

        $pro_fields = array(
            'remove_lightbox_thumbnails' => array(
                'label' => __('Remove the Lightbox on thumbnail images', 'wp-image-zoooom'),
                'value' => false,
                'pro' => true,
                'input_form' => 'checkbox',
            ),
            'remove_lightbox' => array(
                'label' => __('Remove the Lightbox', 'wp-image-zoooom'),
                'value' => false,
                'pro' => true,
                'input_form' => 'checkbox',
            ),
            'woo_variations' => array(
                'label' => __('Enable on WooCommerce variation products', 'wp-image-zoooom'),
                'value' => false,
                'pro' => true,
                'input_form' => 'checkbox',
            ),
            'force_attachments' => array(
                'label' => __('Enable on attachments pages', 'wp-image-zoooom'),
                'value' => false,
                'pro' => true,
                'input_form' => 'checkbox',
            ),
            'flexslider' => array(
                'label' => __('FlexSlider container class', 'wp-image-zoooom'),
                'value' => '',
                'pro' => true,
                'input_form' => 'input_text',
            ),
            'enable_fancybox' => array(
                'label' => __('Enable inside <a href="http://fancyapps.com/fancybox/" target="_blank">fancyBox</a> lightbox', 'wp-image-zoooom'),
                'value' => false,
                'pro' => true,
                'input_form' => 'checkbox',
            ),
                'enable_jetpack_carousel' => array(
                'label' => __('Enable inside <a href="https://jetpack.com/ support/carousel/" target="_blank">Jetpack Carousel</a> lightbox', 'wp-image-zoooom'),
                'value' => false,
                'pro' => true,
                'input_form' => 'checkbox',
            ),

            'huge_it_gallery' => array(
                'label' => __('Huge IT Gallery id', 'wp-image-zoooom'),
                'value' => '',
                'pro' => true,
                'input_form' => 'input_text',
            ),
            'onClick' => array(
                'label' => __('Enable the zoom on ...', 'wp-image-zoooom'),
                'values' => array(
                    'false' => 'mouse hover',
                    'true' => 'mouse click',
                ),
                'value' => 'false',
                'input_form' => 'radio',
                'pro' => true,
            ),
            'ratio' => array(
                'label' => __('Zoom Level', 'wp-image-zoooom'),
                'values' => array(
                    'default' => array( 'icon-zoom_level_default', __('Default', 'zoooom') ),
                    '1.5' => array( 'icon-zoom_level_15', __('1,5 times', 'zoooom') ),
                    '2' => array( 'icon-zoom_level_2', __('2 times', 'zoooom') ),
                    '2.5' => array( 'icon-zoom_level_25', __('2,5 times', 'zoooom') ),
                    '3' => array( 'icon-zoom_level_3', __('3 times', 'zoooom') ),
                ),
                'value' => 'default',
                'input_form' => 'buttons',
                'pro' => true,
                'buttons' => 'i',
            ),
            'lensColour' => array(
                'label' => __('Lens Color', 'wp-image-zoooom' ),
                'value' => '#ffffff',
                'pro' => true,
                'input_form' => 'input_color',
            ),
            'lensOverlay' => array(
                'label' => __('Show as Grid', 'wp-image-zoooom' ),
                'value' => false,
                'pro' => true,
                'input_form' => 'checkbox',
            ),
            'zwResponsive' => array(
                'label' => __('Responsive', 'wp-image-zoooom'),
                'input_form' => 'checkbox',
                'pro' => true,
                'value' => false,
            ),
            'zwResponsiveThreshold' => array(
                'label' => __('Responsive Threshold', 'wp-image-zoooom'),
                'pro' => true,
                'post_input' => 'px',
                'value' => '',
                'input_form' => 'input_text',
            ),
            'zwPositioning' => array(
                'label' => __('Positioning', 'wp-image-zoooom'),
                'values' => array(
                    'right_top' => array('icon-type_zoom_window_right_top', __('Right Top', 'zoooom')),
                    'right_bottom' => array('icon-type_zoom_window_right_bottom', __('Right Bottom', 'zoooom')),
                    'right_center' => array('icon-type_zoom_window_right_center', __('Right Center', 'zoooom')),
                    'left_top' => array('icon-type_zoom_window_left_top', __('Left Top', 'zoooom')),
                    'left_bottom' => array('icon-type_zoom_window_left_bottom', __('Left Bottom', 'zoooom')),
                    'left_center' => array('icon-type_zoom_window_left_center', __('Left Center', 'zoooom')),
                ),
                'pro' => true,
                'value' => '',
                'disabled' => true,
                'input_form' => 'buttons',
                'buttons' => 'i',
            ),
            'mousewheelZoom' => array(
                'label' => __('Mousewheel Zoom', 'wp-image-zoooom'),
                'value' => '',
                'pro' => true,
                'input_form' => 'checkbox',
            ),
            'customText' => array(
                'label' => __('Text on the image', 'wp-image-zoooom'),
                'value' => __('', 'wp-image-zoooom'),
                'input_form' => 'input_text',
                'pro' => true,
            ),
            'customTextSize' => array(
                'label' => __('Text Size', 'wp-image-zoooom'),
                'post_input' => 'px',
                'value' => '',
                'input_form' => 'input_text',
                'pro' => true,
            ),
            'customTextColor' => array(
                'label' => __('Text Color', 'wp-image-zoooom'),
                'value' => '',
                'input_form' => 'input_color',
                'pro' => true,
            ),            
            'customTextAlign' => array(
                'label' => __('Text Align', 'wp-image-zoooom'),
                'values' => array(
                    'top_left' => array('icon-text_align_top_left', __('Top Left', 'zoooom' ) ),
                    'top_center' => array('icon-text_align_top_center', __('Top Center', 'zoooom' ) ),
                    'top_right' => array('icon-text_align_top_right', __('Top Right', 'zoooom' ) ),
                    'bottom_left' => array('icon-text_align_bottom_left', __('Bottom Left', 'zoooom' ) ),
                    'bottom_center' => array('icon-text_align_bottom_center', __('Bottom Center', 'zoooom' ) ),
                    'bottom_right' => array('icon-text_align_bottom_right', __('Bottom Right', 'zoooom' ) ),
                ),
                'value' => '',
                'input_form' => 'buttons',
                'pro' => true,
                'buttons' => 'i',
            ),


        );

        $settings = array_merge( $settings, $pro_fields );

        if ( isset( $settings[$id] ) ) {
            $settings[$id]['name'] = $id;
            return $settings[$id];
        } elseif ( empty( $id ) ) {
            return $settings;
        }
        return false;
    }

    /**
     * Output the admin page
     * @access public
     */
    public function admin_settings_page() {

        if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'general' ) {
            if ( ! empty( $_POST ) ) {
                check_admin_referer('iz_general');
                $new_settings = $this->validate_general( $_POST );
                update_option( 'zoooom_general', $new_settings );
                $this->add_message( 'success', '<b>'.__('Your settings have been saved.', 'wp-image-zoooom') . '</b>' );
            }

            $template = ImageZoooom()->plugin_dir_path() . "/includes/image-zoom-admin-general.php";
            load_template( $template );

            $this->tab = 'general';

            return;
        }

        if ( ! empty( $_POST ) ) {
            check_admin_referer('iz_template');
            $new_settings = $this->validate_settings( $_POST );
            $new_settings_js = $this->generate_js_settings( $new_settings );
            update_option( 'zoooom_settings', $new_settings );
            update_option( 'zoooom_settings_js', $new_settings_js );
            $this->add_message( 'success', '<b>'.__('Your settings have been saved.', 'wp-image-zoooom') . '</b>' );
        }

        $template = ImageZoooom()->plugin_dir_path() . "/includes/image-zoom-admin-template.php";
        load_template( $template );

        $this->tab = 'settings';
    }

    /**
     * Build the jquery.image_zoom.js options and save them directly in the database
     * @access private
     */
    private function generate_js_settings( $settings ) {
        $options = array();
        switch ( $settings['lensShape'] ) {
            case 'none' : 
                $options[] = 'zoomType : "inner"';
                $options[] = 'cursor: "'.$settings['cursorType'].'"';
                $options[] = 'easingAmount: '.$settings['zwEasing'];
                break;
            case 'square' :
            case 'round' :
                $options[] = 'lensShape     : "' .$settings['lensShape'].'"';
                $options[] = 'zoomType      : "lens"';
                $options[] = 'lensSize      : "' .$settings['lensSize'].'"';
                $options[] = 'borderSize    : "' .$settings['borderThickness'].'"'; 
                $options[] = 'borderColour  : "' .$settings['borderColor'].'"';
                $options[] = 'cursor        : "' .$settings['cursorType'].'"';
                $options[] = 'lensFadeIn    : "' .$settings['lensFade'].'"';
                $options[] = 'lensFadeOut   : "' .$settings['lensFade'].'"';
                if ( $settings['tint'] == true ) {
                    $options[] = 'tint     : true';
                    $options[] = 'tintColour:  "' . $settings['tintColor'] . '"';
                    $options[] = 'tintOpacity:  "' . $settings['tintOpacity'] . '"';
                }
 
                break;
            case 'square' :
                break;
            case 'zoom_window' :
               $options[] = 'lensShape       : "square"';
               $options[] = 'lensSize        : "' .$settings['lensSize'].'"'; 
               $options[] = 'lensBorderSize  : "' .$settings['borderThickness'].'"'; 
               $options[] = 'lensBorderColour: "' .$settings['borderColor'].'"'; 
               $options[] = 'borderRadius    : "' .$settings['zwBorderRadius'].'"'; 
               $options[] = 'cursor          : "' .$settings['cursorType'].'"';
               $options[] = 'zoomWindowWidth : "' .$settings['zwWidth'].'"';
               $options[] = 'zoomWindowHeight: "' .$settings['zwHeight'].'"';
               $options[] = 'zoomWindowOffsetx: "' .$settings['zwPadding'].'"';
               $options[] = 'borderSize      : "' .$settings['zwBorderThickness'].'"';
               $options[] = 'borderColour    : "' .$settings['zwBorderColor'].'"';
               $options[] = 'zoomWindowShadow : "' .$settings['zwShadow'].'"';
               $options[] = 'lensFadeIn      : "' .$settings['lensFade'].'"';
               $options[] = 'lensFadeOut     : "' .$settings['lensFade'].'"';
               $options[] = 'zoomWindowFadeIn  :"' .$settings['zwFade'].'"';
               $options[] = 'zoomWindowFadeOut :"' .$settings['zwFade'].'"';
               $options[] = 'easingAmount  : "'.$settings['zwEasing'].'"';
                if ( $settings['tint'] == true ) {
                    $options[] = 'tint     : true';
                    $options[] = 'tintColour:  "' . $settings['tintColor'] . '"';
                    $options[] = 'tintOpacity:  "' . $settings['tintOpacity'] . '"';
                }

                break;
        }
        if (count($options) == 0) return false;

        $options = implode(', ', $options);

        return $options;
    }


    /**
     * Check the validity of the settings. The validity has to be the same as the javascript validation in image-zoom.settings.js
     * @access public
     */
    public function validate_settings( $post ) {
        $settings = $this->get_settings();

        $new_settings = array();
        foreach ( $settings as $_key => $_value ) {
            if ( isset( $post[$_key] ) && $post[$_key] != $_value['value'] ) {
                $new_settings[$_key] = $post[$_key]; 
            } else {
                $new_settings[$_key] = $_value['value'];
            } 
        }

        $new_settings['lensShape'] = $this->validateValuesSet('lensShape', $new_settings['lensShape']);
        $new_settings['cursorType'] = $this->validateValuesSet('cursorType', $new_settings['cursorType']);
        $new_settings['zwEasing'] = $this->validateRange('zwEasing', $new_settings['zwEasing'], 'int', 0, 200);
        $new_settings['lensSize'] = $this->validateRange('lensSize', $new_settings['lensSize'], 'int', 20, 2000);
        $new_settings['borderThickness'] = $this->validateRange('borderThickness', $new_settings['borderThickness'], 'int', 0, 200);
        $new_settings['borderColor'] = $this->validateColor('borderColor', $new_settings['borderColor']);
        $new_settings['lensFade'] = $this->validateRange('lensFade', $new_settings['lensFade'], 'float', 0, 10);
        $new_settings['tint'] = $this->validateCheckbox('tint', $new_settings['tint']);
        $new_settings['tintColor'] = $this->validateColor('tintColor', $new_settings['tintColor']);
        $new_settings['tintOpacity'] = $this->validateRange('tintOpacity', $new_settings['tintOpacity'], 'float', 0, 1);
        $new_settings['zwWidth'] = $this->validateRange('zwWidth', $new_settings['zwWidth'], 'int', 0, 2000);
        $new_settings['zwHeight'] = $this->validateRange('zwHeight', $new_settings['zwHeight'], 'int', 0, 2000);
        $new_settings['zwPadding'] = $this->validateRange('zwPadding', $new_settings['zwPadding'], 'int', 0, 200 );
        $new_settings['zwBorderThickness'] = $this->validateRange('zwBorderThickness', $new_settings['zwBorderThickness'], 'int', 0, 200);
        $new_settings['zwBorderRadius'] = $this->validateRange('zwBorderRadius', $new_settings['zwBorderRadius'], 'int', 0, 500);
        $new_settings['zwShadow'] = $this->validateRange('zwShadow', $new_settings['zwShadow'], 'int', 0, 500);
        $new_settings['zwFade'] = $this->validateRange('zwFade', $new_settings['zwFade'], 'float', 0, 10);

        return $new_settings; 
    }

    public function validate_general( $post = null) {
        $settings = $this->get_settings();

        if( $post == null ) {
            return array(
                'enable_woocommerce' => true,
                'exchange_thumbnails' => true,
                'enable_mobile' => false,
                'woo_cat' => false,
                'force_woocommerce' => true,
            );
        }

        if ( ! isset( $post['enable_woocommerce'] ) ) 
            $post['enable_woocommerce'] = false;
        if ( ! isset( $post['exchange_thumbnails'] ) ) 
            $post['exchange_thumbnails'] = false;
        if ( ! isset( $post['enable_mobile'] ) ) 
            $post['enable_mobile'] = false;
        if ( ! isset( $post['woo_cat'] ) ) 
            $post['woo_cat'] = false;
        if ( ! isset( $post['force_woocommerce'] ) ) 
            $post['force_woocommerce'] = false;

        $new_settings = array(
            'enable_woocommerce' => $this->validateCheckbox('enable_woocommerce', $post['enable_woocommerce']),
            'exchange_thumbnails' => $this->validateCheckbox('exchange_thumbnails', $post['exchange_thumbnails']),
            'enable_mobile' => $this->validateCheckbox('enable_mobile', $post['enable_mobile']),
            'woo_cat' => $this->validateCheckbox('woo_cat', $post['woo_cat']),
            'force_woocommerce' => $this->validateCheckbox('force_woocommerce', $post['force_woocommerce']),
        );

        return $new_settings;
    }

    /**
     * Helper to validate a checkbox
     * @access private
     */
    private function validateCheckbox( $id, $value ) {
        $settings = $this->get_settings();

        if ( $value == 'on' ) $value = true;

        if ( !is_bool($value) ) {
            $value = $settings[$id]['value'];
            $this->add_message('info', __('Unrecognized <b>'.$settings[$id]['label'].'</b>. The value was reset to default', 'wp-image-zoooom') );
        } else {
        }
        return $value;
    }

    /**
     * Helper to validate a color
     * @access private
     */
    private function validateColor( $id, $value ) {
        $settings = $this->get_settings();

        if ( !preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value) ) {
            $value = $settings[$id]['value'];
            $message = __('Unrecognized <b>%1$s</b>. The value was reset to <b>%2$s</b>', 'wp-image-zoooom');
            $message = wp_kses($message, array('b' => array()));
            $message = sprintf($message, $settings[$id]['label'], $settings[$id]['value']);
            $this->add_message('info', $message);
        }
        return $value;
    }

    /**
     * Helper to validate the value out of a set of values
     * @access private
     */
    private function validateValuesSet( $id, $value ) {
        $settings = $this->get_settings();

        if ( !array_key_exists($value, $settings[$id]['values']) ) {
            $value = $settings[$id]['value'];
            $message = __('Unrecognized <b>%1$s</b>. The value was reset to <b>%2$s</b>', 'wp-image-zoooom');
            $message = wp_kses($message, array('b' => array()));
            $message = sprintf($message, $settings[$id]['label'], $settings[$id]['value']);
            $this->add_message('info', $message);
        }
        return $value;
    }

    /**
     * Helper to validate an integer of a float
     * @access private
     */
    private function validateRange( $id, $value, $type, $min, $max ) {
        $settings = $this->get_settings();

        if ( $type == 'int' ) $new_value = (int)$value;
        if ( $type == 'float' ) $new_value = (float)$value;

        if ( !is_numeric($value) || $new_value < $min || $new_value > $max ) {
            $new_value = $settings[$id]['value'];
            $message = __('<b>%1$s</b> accepts values between %2$s and %3$s. Your value was reset to <b>%4$s</b>', 'wp-image-zoooom');
            $message = wp_kses($message, array('b' => array()));
            $message = sprintf($message, $settings[$id]['label'], $settings[$id]['value']);
            $this->add_message('info', $message);
        }
        return $new_value;
    }


    /**
     * Add a message to the $this->messages array
     * @type    accepted types: success, error, info, block
     * @access private
     */
    private function add_message( $type = 'success', $text ) {
        global $comment;
        $messages = $this->messages;
        $messages[] = array('type' => $type, 'text' => $text);
        $comment[] = array('type' => $type, 'text' => $text);
        $this->messages = $messages;
    }

    /**
     * Output the form messages
     * @access public
     */
    public function show_messages() {
        global $comment;
        if ( sizeof( $comment ) == 0 ) return;
        $output = '';
        foreach ( $comment as $message ) {
            $output .= '<div class="alert alert-'.$message['type'].'">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  '. $message['text'] .'</div>';
        }
        return $output;
    }


    /**
     * Add a button to the TinyMCE toolbar
     * @access public
     */
    function iz_add_tinymce_button() {
        global $typenow;

        if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
            return;
        }

        $allowed_types = array( 'post', 'page' );

        if ( defined('LEARNDASH_VERSION') ) {
            $learndash_types = array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-certificates', 'sfwd-assignment'); 
            $allowed_types = array_merge( $allowed_types, $learndash_types );

        }
        if( ! in_array( $typenow, $allowed_types ) )
            return;

        if ( isset( $_GET['page'] ) && $_GET['page'] == 'wplister-templates' ) 
            return;

        if ( get_user_option('rich_editing') != 'true') 
            return;

        add_filter('mce_external_plugins', array( $this, 'iz_add_tinymce_plugin' ) );
        add_filter('mce_buttons', array( $this, 'iz_register_tinymce_button' ) );
    }

    /**
     * Register the plugin with the TinyMCE plugins manager
     * @access public
     */
    function iz_add_tinymce_plugin($plugin_array) {
        $plugin_array['image_zoom_button'] = ImageZoooom()->plugins_url() . '/assets/js/tinyMCE-button.js'; 
        return $plugin_array;
    }

    /**
     * Register the button with the TinyMCE manager
     */
    function iz_register_tinymce_button($buttons) {
        array_push($buttons, 'image_zoom_button');
        return $buttons;
    }


}


return new ImageZoooom_Admin();
