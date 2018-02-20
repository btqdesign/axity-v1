<?php

require_once 'image-zoom-forms-helper.php';

$iz_admin = new ImageZoooom_Admin;
$iz_forms_helper = new ImageZoooom_FormsHelper;

$assets_url = IMAGE_ZOOM_URL . '/assets'; 

$settings = get_option('zoooom_general');
if ( $settings == false ) {
    $settings = $iz_admin->validate_general( null );
}

$messages = $iz_admin->show_messages();

include_once( 'premium-tooltips.php' );

?>
<style type="text/css">
    .form-group { display:flex; align-items: center; }
    .control-label{ height: auto; }
</style>

<script type="text/javascript">

    jQuery(document).ready(function($) {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<?php add_thickbox(); ?>
<div id="supported-lightboxes" style="display:none;">
     <p>
            The zoom is compatible with:
          <ul style="list-style: inside; padding-left: 20px;">
            <li>
                the lightbox created by the <a href="https://www.silkypress.com/i/wp-huge-it-gallery" target="_blank" rel="nofollow">Huge IT Gallery</a> plugin
            </li>
            <li>
                the lightbox created by the <a href="https://www.silkypress.com/i/wp-photo-gallery" target="_blank" rel="nofollow">Photo Gallery</a> plugin
            </li>
            <li>the iLightbox from the <a href="https://www.silkypress.com/i/avada-theme" target="_blank" rel="nofollow">Avada Theme</a></li>
            <li>the lightbox created by <a href="https://www.silkypress.com/i/jetpack-carousel" target="_blank" rel="nofollow">Carousel</a> from Jetpack</li>
            <li>the <a href="https://www.silkypress.com/i/js-prettyphoto" target="_blank" rel="nofollow">prettyPhoto</a> lightbox (also used by the <a href="https://www.silkypress.com/i/visual-composer" target="_blank" rel="nofollow">Visual Composer</a> gallery)</li>
            <li>the <a href="https://www.silkypress.com/i/js-fancybox" target="_blank" rel="nofollow">fancyBox</a> lightbox (also used by the 
          <a href="https://www.silkypress.com/i/wp-easy-fancybox" target="_blank" rel="nofollow">Easy Fancybox
          </a> plugin)</li>
            <li>the <a href="https://www.silkypress.com/i/js-featherlight" target="_blank" rel="nofollow">Featherlight.js</a> lightbox (also used by <a href="https://www.silkypress.com/i/wp-draw-attention" target="_blank" rel="nofollow">Draw Attention</a> plugin)</li>
            <li>the lightbox created by the Ultimate Product Catalogue by Etoile Web Design</li> 
            </ul>
     </p>
</div>


    <?php $brand = '<img src="'. $assets_url.'/images/silkypress_logo.png" /> <a href="https://www.silkypress.com/?utm_source=wordpress&utm_campaign=iz_free&utm_medium=banner" target="_blank">SilkyPress.com</a>';?>
<h2><?php printf(esc_html__('WP Image Zoom by %1$s', 'wp-image-zoooom'), $brand); ?></h2>

<div class="wrap">


<h3 class="nav-tab-wrapper woo-nav-tab-wrapper">

    <a href="?page=zoooom_settings&tab=general" class="nav-tab nav-tab-active"><?php _e('General Settings', 'wp-image-zoooom'); ?></a>

    <a href="?page=zoooom_settings&tab=settings" class="nav-tab"><?php _e('Zoom Settings', 'wp-image-zoooom'); ?></a>

</h3>

<div class="panel panel-default">
    <div class="panel-body">
    <div class="row">



    <div class="col-lg-12">
    <?php echo $messages; ?>
    <div id="alert_messages">
    </div>
    </div>


        

<form class="form-horizontal" method="post" action="" id="form_settings">

        <?php
            $iz_forms_helper->label_class = 'col-sm-6 control-label';

            $fields = array('enable_woocommerce', 'exchange_thumbnails', 'woo_cat', 'woo_variations', 'enable_mobile', 'remove_lightbox_thumbnails', 'remove_lightbox', 'force_attachments', 'custom_class', 'flexslider', 'owl', 'huge_it_gallery', 'enable_lightbox_zoom' );

            if ( class_exists('woocommerce') && version_compare( WC_VERSION, '3.0', '>') ) {
                unset($fields[array_search('exchange_thumbnails', $fields)]);
            }

        foreach ( $fields as $_field ) {
            $this_settings = $iz_admin->get_settings( $_field);
            $this_settings['value'] = '';
            if ( isset( $settings[$_field] ) ) {
                $this_settings['value'] = $settings[$_field];
            }
            $iz_forms_helper->input($this_settings['input_form'], $this_settings); 
        }
        
        ?> 

<div class="form-group">
      <div class="col-lg-6">
        <input type="hidden" name="tab" value="general" />
          <button type="submit" class="btn btn-primary"><?php _e('Save changes', 'wp-image-zoooom'); ?></button>
      </div>
    </div>

    <?php wp_nonce_field( 'iz_general' ); ?>

</form>


    </div>
    </div>
</div>
</div>

<?php include_once('right_columns.php'); ?>
