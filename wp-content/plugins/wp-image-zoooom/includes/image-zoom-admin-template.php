<?php

require_once 'image-zoom-forms-helper.php';

$iz_admin = new ImageZoooom_Admin;
$iz_forms_helper = new ImageZoooom_FormsHelper;

$assets_url = IMAGE_ZOOM_URL . '/assets';

$settings = get_option( 'zoooom_settings' );
if ( $settings == false ) {
    $settings = $iz_admin->validate_settings( array() );
}
$messages = $iz_admin->show_messages();


include_once( 'premium-tooltips.php' );

?>

<?php $brand = '<img src="'. IMAGE_ZOOM_URL .'assets/images/silkypress_logo.png" /> <a href="https://www.silkypress.com/?utm_source=wordpress&utm_campaign=iz_free&utm_medium=banner" target="_blank">SilkyPress.com</a>';?>
<h2><?php printf(esc_html__('WP Image Zoom by %1$s', 'wp-image-zoooom'), $brand); ?></h2>

<div class="wrap">


<h3 class="nav-tab-wrapper woo-nav-tab-wrapper">

    <a href="?page=zoooom_settings&tab=general" class="nav-tab"><?php _e('General Settings', 'wp-image-zoooom'); ?></a>

    <a href="?page=zoooom_settings&tab=settings" class="nav-tab nav-tab-active"><?php _e('Zoom Settings', 'wp-image-zoooom'); ?></a>

</h2>

<div class="panel panel-default">
    <div class="panel-body">
    <div class="row">


    <?php echo $messages; ?>
    <div id="alert_messages">
    </div>
        
<form class="form-horizontal" method="post" action="" id="form_settings">

<div class="form-group">
        <?php echo load_steps(__('Step 1', 'wp-image-zoooom'), __('Choose the Lens Shape', 'wp-image-zoooom')); ?>

        <?php 
            $lensShape = $iz_admin->get_settings( 'lensShape', $settings['lensShape']);

            $lensShape['value'] = $settings['lensShape'];
            if ( ! isset($lensShape['value'] ) ) $lensShape['value'] = '';
        ?>
          <div class="btn-group" data-toggle="buttons" id="btn-group-style-circle">
            <?php foreach( $lensShape['values'] as $_id => $_value ) : ?>
            <?php $toggle = ( ! empty($_value[1]) ) ? ' data-toggle="tooltip" data-placement="top" title="'.$_value[1].'" data-original-title="' . $_value[1] . '"' : ''; ?>
            <label class="btn btn-default<?php echo ($lensShape['value'] == $_id) ? ' active' : '' ?> ">
            <input type="radio" name="<?php echo $lensShape['name'] ?>" id="<?php echo $_id ?>" value="<?php echo $_id ?>" <?php echo ($lensShape['value'] == $_id) ? 'checked' : '' ?> />
            <div class="icon-in-label ndd-spot-icon icon-style-1"<?php echo $toggle; ?>>
              <div class="ndd-icon-main-element">
                    <i class="<?php echo $_value[0]; ?>"></i>
              </div>
            </div>
            </label>
            <?php endforeach; ?>
          </div>

    <div style="clear: both; margin-bottom: 50px;"></div>


    <?php echo load_steps(__('Step 2', 'wp-image-zoooom'), __('Check your configuration changes on the image', 'wp-image-zoooom')); ?>
    <img id="demo" src="<?php echo $assets_url ?>/images/img1_medium.png" data-zoom-image="<?php echo $assets_url ?>/images/img1_large.png" width="300" />


    <div style="clear: both; margin-bottom: 50px;"></div>

    <?php echo load_steps(__('Step 3', 'wp-image-zoooom'), __('Make more fine-grained configurations on the zoom', 'wp-image-zoooom')); ?>
    <ul class="nav nav-tabs">
        <li class="" id="tab_padding" style="width: 40px;"> &nbsp; </li>
        <li class="active" id="tab_general">
        <a href="#general_settings" data-toggle="tab" aria-expanded="true"><?php _e('General', 'wp-image-zoooom'); ?></a>
        </li>
        <li class="" id="tab_lens">
        <a href="#lens_settings" data-toggle="tab" aria-expanded="false"><?php _e('Lens', 'wp-image-zoooom'); ?></a>
        </li>
        <li class="" id="tab_zoom_window">
        <a href="#zoom_window_settings" data-toggle="tab" aria-expanded="false"><?php _e('Zoom Window', 'wp-image-zoooom'); ?></a>
        </li>
        <li class="" id="tab_custom_text">
        <a href="#custom_text" data-toggle="tab" aria-expanded="false"><?php _e('Custom Text', 'wp-image-zoooom'); ?></a>
        </li>
    </ul>

<div class="tab-content">
    <div class="tab-pane fade active in" id="general_settings">
        <?php

        foreach ( array('cursorType', 'zwEasing', 'onClick', 'ratio' ) as $_field ) {
            $this_settings = $iz_admin->get_settings( $_field);
            $this_settings['value'] = '';
            if ( isset( $settings[$_field] ) ) {
                $this_settings['value'] = $settings[$_field];
            }
            $iz_forms_helper->input($this_settings['input_form'], $this_settings); 
        }
        ?> 

    </div>
    <div class="tab-pane fade" id="lens_settings">
        <?php

        $fields = array(
            'lensSize',
            'lensColour',
            'lensOverlay',
            'borderThickness',
            'borderColor',
            'lensFade',
            'tint',
            'tintColor',
            'tintOpacity',
        );

        foreach ( $fields as $_field ) {
            $this_settings = $iz_admin->get_settings( $_field);
            $this_settings['value'] = '';
            if ( isset( $settings[$_field] ) ) {
                $this_settings['value'] = $settings[$_field];
            }
            $iz_forms_helper->input($this_settings['input_form'], $this_settings); 
        }

        ?>
    </div>

    <div class="tab-pane fade" id="zoom_window_settings">
        <?php

        $fields = array(
            'zwWidth',
            'zwHeight',
            'zwResponsive',
            'zwResponsiveThreshold',
            'zwPositioning',
            'zwPadding',
            'zwBorderThickness',
            'zwBorderColor',
            'zwShadow',
            'zwBorderRadius',
            'mousewheelZoom',
            'zwFade',
        );

        foreach ( $fields as $_field ) {
            $this_settings = $iz_admin->get_settings( $_field);
            $this_settings['value'] = '';
            if ( isset( $settings[$_field] ) ) {
                $this_settings['value'] = $settings[$_field];
            }
            $iz_forms_helper->input($this_settings['input_form'], $this_settings); 
        }

       ?>
    </div>

    <div class="tab-pane fade" id="custom_text">
        <?php

        $fields = array(
            'customText',
            'customTextSize',
            'customTextColor',
            'customTextAlign',
        );

        foreach ( $fields as $_field ) {
            $this_settings = $iz_admin->get_settings( $_field);
            $this_settings['value'] = '';
            if ( isset( $settings[$_field] ) ) {
                $this_settings['value'] = $settings[$_field];
            }
            $iz_forms_helper->input($this_settings['input_form'], $this_settings); 
        }

       ?>
    </div>

</div><!-- close "tab-content" -->


    <?php echo load_steps(__('Step 4', 'wp-image-zoooom'), __('Don\'t forget to save the changes in order to apply them on the website', 'wp-image-zoooom')); ?>
    <div class="form-group">
      <div class="col-lg-6">
      <button type="submit" class="btn btn-primary"><?php _e('Save changes', 'wp-image-zoooom'); ?></button>
      </div>
    </div>

</div><!-- close "form-group" -->

    <?php wp_nonce_field( 'iz_template' ); ?>
</form>


    </div>
</div>
</div>


</div><!-- close wrap -->


<?php include_once('right_columns.php'); ?>

<?php

function load_steps($step, $description) {
    return '<div class="steps">
        <span class="steps_nr">'. $step .':</span>
        <span class="steps_desc">' . $description . '</span>
        </div>' . "\n";
}

?>
