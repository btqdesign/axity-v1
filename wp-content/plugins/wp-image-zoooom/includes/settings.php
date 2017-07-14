<?php

if (!function_exists('wp_image_zoooom_settings')) {
function wp_image_zoooom_settings($type) {

    $plugin = array(
        'version'           => '1.13',
        'plugin_name'       => 'WP Image Zoom',
        'plugin_file'       => str_replace('includes/settings.php', 'image-zoooom-pro.php', __FILE__),
        'plugin_server'     => 'https://www.silkypress.com',
        'author'            => 'Diana Burduja',
        'testing'           => false,
    );
    if ($type == 'plugin') return $plugin;

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
            'description' => __('Tablets are also considered mobile devices'),
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
    if ($type == 'settings') return $settings;


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
    if ($type == 'pro_fields') return $pro_fields;

}
}

?>
