<?php

if (!function_exists('wp_image_zoooom_settings')) {
function wp_image_zoooom_settings($type) {

    $l = 'wp-image-zoooom';

    $plugin = array(
        'version'           => '1.16',
        'plugin_name'       => 'WP Image Zoom',
        'plugin_file'       => str_replace('includes/settings.php', 'image-zoooom.php', __FILE__),
        'plugin_server'     => 'https://www.silkypress.com',
        'author'            => 'Diana Burduja',
        'testing'           => false,
    );
    if ($type == 'plugin') return $plugin;

    $settings = array(
        'lensShape' => array(
            'label' => __('Lens Shape', $l),
            'values' => array(
                'none' => array('icon-lens_shape_none', __('No Lens', $l)),
                'round' => array('icon-lens_shape_circle', __('Circle Lens', $l)),
                'square' => array('icon-lens_shape_square', __('Square Lens', $l)),
                'zoom_window' => array('icon-type_zoom_window', __('With Zoom Window', $l)),
            ),
            'value' => 'zoom_window',
            'input_form' => 'buttons',
            'buttons' => 'i',
        ),
        'cursorType' => array(
            'label' => __('Cursor Type', $l),
            'values' => array(
                'default' => array('icon-cursor_type_default', __('Default', $l ) ),
                'pointer' => array('icon-cursor_type_pointer', __('Pointer', $l ) ),
                'crosshair' => array('icon-cursor_type_crosshair', __('Crosshair', $l ) ),
                'zoom-in' => array('icon-zoom-in', __('Zoom', $l ) ),
            ),
            'value' => 'default',
            'input_form' => 'buttons',
            'buttons' => 'i',
        ),
        'zwEasing' => array(
            'label' => __('Animation Easing Effect', $l ),
            'value' => 12,
            'description' => __('A number between 0 and 200 to represent the degree of the Animation Easing Effect', $l ),
            'input_form' => 'input_text',
        ),

        'lensSize' => array(
            'label' => __('Lens Size', $l ),
            'post_input' => 'px',
            'value' => 200,
            'description' => __('For Circle Lens it means the diameters, for Square Lens it means the width', $l ),
            'input_form' => 'input_text',
        ),
        'borderThickness' => array(
            'label' => __('Border Thickness', $l ),
            'post_input' => 'px',
            'value' => 1,
            'input_form' => 'input_text',
        ),
        'borderColor' => array(
            'label' => __('Border Color', $l ),
            'value' => '#ffffff',
            'input_form' => 'input_color',
        ),
        'lensFade' => array(
            'label' => __('Fade Time', $l ),
            'post_input' => 'sec',
            'value' => 1,
            'description' => __('The amount of time it takes for the Lens to slowly appear or dissapear', $l),
            'input_form' => 'input_text',
        ),
        'tint' => array(
            'label' => __('Tint', $l),
            'value' => false,
            'description' => __('A color that will layed on top the of non-magnified image in order to emphasize the lens', $l),
            'input_form' => 'checkbox',
        ),
        'tintColor' =>array(
            'label' => __('Tint Color', $l),
            'value' => '#ffffff',
            'input_form' => 'input_color',
        ),
        'tintOpacity' => array(
            'label' => __('Tint Opacity', $l),
            'value' => '0.5',
            'post_input' => '%',
            'input_form' => 'input_text',
        ),
        'zwWidth' => array(
            'label' => __('Zoom Window Width', $l),
            'post_input' => 'px',
            'value' => 400,
            'input_form' => 'input_text',
        ),
        'zwHeight' => array(
            'label' => __('Zoom Window Height', $l),
            'post_input' => 'px',
            'value' => 360,
            'input_form' => 'input_text',
        ),
        'zwPadding' => array(
            'label' => __('Distance from the Main Image', $l),
            'post_input' => 'px',
            'value' => 10,
            'input_form' => 'input_text',
        ),
        'zwBorderThickness' => array(
            'label' => __('Border Thickness', $l),
            'post_input' => 'px',
            'value' => 4,
            'input_form' => 'input_text',
        ),
        'zwShadow' => array(
            'label' => __('Shadow Thickness', $l),
            'post_input' => 'px',
            'value' => 4,
            'input_form' => 'input_text',
            'description' => __('Use 0px to remove the shadow', $l),
        ),
        'zwBorderColor' => array(
            'label' => __('Border Color', $l),
            'value' => '#888888',
            'input_form' => 'input_color',
        ),
        'zwBorderRadius' => array(
            'label' => __('Rounded Corners', $l),
            'post_input' => 'px',
            'value' => 0,
            'input_form' => 'input_text',
        ),
        'zwFade' => array(
            'label' => __('Fade Time', $l),
            'post_input' => 'sec',
            'value' => 0,
            'description' => __('The amount of time it takes for the Zoom Window to slowly appear or disappear', $l),
            'input_form' => 'input_text',
        ),
        'enable_woocommerce' => array(
            'label' => __('Enable the zoom on WooCommerce products', $l),
            'value' => true,
            'input_form' => 'checkbox',
        ),
        'exchange_thumbnails' => array(
            'label' => __('Exchange the thumbnail with main image on WooCommerce products', $l),
            'value' => true,
            'input_form' => 'checkbox',
            'description' => __('On a WooCommerce gallery, when clicking on a thumbnail, not only the main image will be replaced with the thumbnail\'s image, but also the thumbnail will be replaced with the main image', $l),
        ),
        'enable_mobile' => array(
            'label' => __('Enable the zoom on mobile devices', $l),
            'value' => false,
            'input_form' => 'checkbox',
            'description' => __('Tablets are also considered mobile devices'),
        ),
        'woo_cat' => array(
            'label' => __('Enable the zoom on WooCommerce category pages', $l),
            'value' => false,
            'input_form' => 'checkbox',
        ),

        'force_woocommerce' => array(
            'label' => __('Force it to work on WooCommerce', $l),
            'value' => true,
            'input_form' => 'checkbox',
        ),
    );
    if ($type == 'settings') return $settings;


    $pro_fields = array(
        'remove_lightbox_thumbnails' => array(
            'label' => __('Remove the Lightbox on thumbnail images', $l),
            'value' => false,
            'pro' => true,
            'input_form' => 'checkbox',
            'description' => __('Some themes implement a Lightbox for WooCommerce galleris that opens on click. Enabling this checkbox will remove the Lightbox on thumbnail images and leave it only on the main image', 'wp-image-zoooom'),
        ),
        'remove_lightbox' => array(
            'label' => __('Remove the Lightbox', $l),
            'value' => false,
            'pro' => true,
            'input_form' => 'checkbox',
            'description' => __('Some themes implement a Lightbox that opens on click on the image. Enabling this checkbox will remove the Lightbox'),
        ),
        'woo_variations' => array(
            'label' => __('Enable on WooCommerce variation products', $l),
            'value' => false,
            'pro' => true,
            'input_form' => 'checkbox',
        ),
        'force_attachments' => array(
            'label' => __('Enable on attachments pages', $l),
            'value' => false,
            'pro' => true,
            'input_form' => 'checkbox',
        ),
        'flexslider' => array(
            'label' => __('FlexSlider container class', $l),
            'value' => '',
            'pro' => true,
            'input_form' => 'input_text',
        ),
        'enable_fancybox' => array(
            'label' => __('Enable inside <a href="http://fancyapps.com/fancybox/" target="_blank">fancyBox</a> lightbox', $l),
            'value' => false,
            'pro' => true,
            'input_form' => 'checkbox',
        ),
            'enable_jetpack_carousel' => array(
            'label' => __('Enable inside <a href="https://jetpack.com/ support/carousel/" target="_blank">Jetpack Carousel</a> lightbox', $l),
            'value' => false,
            'pro' => true,
            'input_form' => 'checkbox',
        ),

        'huge_it_gallery' => array(
            'label' => __('Huge IT Gallery id', $l),
            'value' => '',
            'pro' => true,
            'input_form' => 'input_text',
        ),
        'onClick' => array(
            'label' => __('Enable the zoom on ...', $l),
            'values' => array(
                'false' => 'mouse hover',
                'true' => 'mouse click',
            ),
            'value' => 'false',
            'input_form' => 'radio',
            'pro' => true,
        ),
        'ratio' => array(
            'label' => __('Zoom Level', $l),
            'values' => array(
                'default' => array( 'icon-zoom_level_default', __('Default', $l) ),
                '1.5' => array( 'icon-zoom_level_15', __('1,5 times', $l) ),
                '2' => array( 'icon-zoom_level_2', __('2 times', $l) ),
                '2.5' => array( 'icon-zoom_level_25', __('2,5 times', $l) ),
                '3' => array( 'icon-zoom_level_3', __('3 times', $l) ),
            ),
            'value' => 'default',
            'input_form' => 'buttons',
            'pro' => true,
            'buttons' => 'i',
        ),
        'lensColour' => array(
            'label' => __('Lens Color', $l ),
            'value' => '#ffffff',
            'pro' => true,
            'input_form' => 'input_color',
        ),
        'lensOverlay' => array(
            'label' => __('Show as Grid', $l ),
            'value' => false,
            'pro' => true,
            'input_form' => 'checkbox',
        ),
        'zwResponsive' => array(
            'label' => __('Responsive', $l),
            'input_form' => 'checkbox',
            'pro' => true,
            'value' => false,
        ),
        'zwResponsiveThreshold' => array(
            'label' => __('Responsive Threshold', $l),
            'pro' => true,
            'post_input' => 'px',
            'value' => '',
            'input_form' => 'input_text',
        ),
        'zwPositioning' => array(
            'label' => __('Positioning', $l),
            'values' => array(
                'right_top' => array('icon-type_zoom_window_right_top', __('Right Top', $l)),
                'right_bottom' => array('icon-type_zoom_window_right_bottom', __('Right Bottom', $l)),
                'right_center' => array('icon-type_zoom_window_right_center', __('Right Center', $l)),
                'left_top' => array('icon-type_zoom_window_left_top', __('Left Top', $l)),
                'left_bottom' => array('icon-type_zoom_window_left_bottom', __('Left Bottom', $l)),
                'left_center' => array('icon-type_zoom_window_left_center', __('Left Center', $l)),
            ),
            'pro' => true,
            'value' => '',
            'disabled' => true,
            'input_form' => 'buttons',
            'buttons' => 'i',
        ),
        'mousewheelZoom' => array(
            'label' => __('Mousewheel Zoom', $l),
            'value' => '',
            'pro' => true,
            'input_form' => 'checkbox',
        ),
        'customText' => array(
            'label' => __('Text on the image', $l),
            'value' => __('', $l),
            'input_form' => 'input_text',
            'pro' => true,
        ),
        'customTextSize' => array(
            'label' => __('Text Size', $l),
            'post_input' => 'px',
            'value' => '',
            'input_form' => 'input_text',
            'pro' => true,
        ),
        'customTextColor' => array(
            'label' => __('Text Color', $l),
            'value' => '',
            'input_form' => 'input_color',
            'pro' => true,
        ),            
        'customTextAlign' => array(
            'label' => __('Text Align', $l),
            'values' => array(
                'top_left' => array('icon-text_align_top_left', __('Top Left', $l ) ),
                'top_center' => array('icon-text_align_top_center', __('Top Center', $l ) ),
                'top_right' => array('icon-text_align_top_right', __('Top Right', $l ) ),
                'bottom_left' => array('icon-text_align_bottom_left', __('Bottom Left', $l ) ),
                'bottom_center' => array('icon-text_align_bottom_center', __('Bottom Center', $l ) ),
                'bottom_right' => array('icon-text_align_bottom_right', __('Bottom Right', $l ) ),
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
