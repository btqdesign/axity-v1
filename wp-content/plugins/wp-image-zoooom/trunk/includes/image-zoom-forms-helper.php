<?php

class ImageZoooom_FormsHelper {

    public $label_class = 'col-sm-5 control-label';

    public function input( $type, $settings = array() ) {
        if ( !isset($settings['label'] )) return;
        if ( !isset($settings['name'] )) return;
        $allowed_types = array( 'radio', 'input_text', 'buttons', 'input_color', 'checkbox' );

        if ( ! in_array( $type, $allowed_types ) ) {
            return;
        }
        $this->form_group_before( $settings, $type );
        call_user_func( array($this, $type), $settings );
        $this->form_group_after( $settings );
    }

    function form_group_before( $args = array(), $type ) {
        $disabled = ( isset($args['pro']) && $args['pro']) ? true : false;

        $output = "\t\t" . '<div class="form-group';
        if ( $type == 'radio' || $type == 'buttons' ) {
            $output .= ($disabled) ? ' disabled' : '';
        } else {
            $output .= ($disabled) ? ' disabled-short' : '';
        }
        $output .= '">' . PHP_EOL;

        $output .= "\t\t" . '<label class="'. $this->label_class .'">'. $args['label'] . PHP_EOL;
        if ( $disabled ) {
            $output .= "\t\t" . '<img src="'.IMAGE_ZOOM_URL.'assets/images/question_mark.svg" />' . PHP_EOL;
        }
        if ( isset($args['description']) && !$disabled ) {
            $output .= "\t\t" . $this->tooltip( $args['description'] ); 
        }
        $output .= "\t\t" . '</label>' . PHP_EOL;

        echo $output;
    }

    function form_group_after( $args = array() ) {
        echo "\t\t" . '</div>' . PHP_EOL;
    }
    
    public function radio($args = array()) {
        if ( !isset($args['values'] ) || count($args['values']) == 0 ) return;
        if ( !isset($args['active'] ) ) $args['active'] = '';
        $disabled = ( isset($args['pro']) && $args['pro']) ? ' disabled="disabled"': '';
        ?>
           <?php foreach ($args['values'] as $_id => $_label) : ?>
            <div class="radio"><label>
              <input type="radio" name="<?php echo $args['name'] ?>" id="<?php echo $_id ?>" value="<?php echo $_id ?>" <?php if ($_id == $args['active']) echo 'checked=""'; ?> <?php echo $disabled; ?>>
              <?php echo $_label ?>
            </label></div>
            <?php endforeach; ?>
        <?php
    }

    public function input_text( $args = array() ) {
        if ( ! isset($args['value'] ) ) $args['value'] = '';
        if ( ! isset($args['description'] ) ) $args['description'] = '';
        $disabled = ( isset($args['pro']) && $args['pro']) ? ' disabled="disabled"': '';
        ?>
            <?php if (isset($args['post_input'])) : ?>
                <div class="input-group">
            <?php else : ?>
                <div class="input-group">
            <?php endif; ?>
        <input type="text" class="form-control" id="<?php echo $args['name']?>" name="<?php echo $args['name'] ?>" value="<?php echo $args['value'] ?>" <?php echo $disabled; ?> />
            <?php if (isset($args['post_input'])) : ?><span class="input-group-addon"><?php echo $args['post_input'] ?></span>
            <?php endif; ?>
                </div>
        <?php
    }


    public function input_color( $args = array() ) {
        if ( ! isset($args['value'] ) ) $args['value'] = '';
        ?>
				<div class="input-group">
                <input type="color" class="form-control" id="<?php echo $args['name'] ?>" name="<?php echo $args['name'] ?>" value="<?php echo $args['value'] ?>">
                <span class="input-group-addon" id="color-text-color-hex"><?php echo $args['value'] ?></span>
				</div>

        <?php
    }

    public function checkbox( $args = array() ) {
        if ( ! isset($args['value'] ) ) $args['value'] = false;
        $disabled = ( isset($args['pro']) && $args['pro']) ? ' disabled="disabled"': '';
        ?>
                  <div class="input-group input-group-checkbox">
                    <label>
                    <input type="checkbox" id="<?php echo $args['name'] ?>" name="<?php echo $args['name'] ?>" <?php echo ($args['value'] == true) ? 'checked=""' : '' ?> <?php $disabled; ?>/>
                    </label>
                   </div>
        <?php
    }

    public function buttons( $args = array() ) {
        if ( ! isset($args['values'] ) || count($args['values']) == 0 ) return;
        if ( ! isset($args['value'] ) ) $args['value'] = '';
        if ( ! isset($args['buttons'] ) ) $args['buttons'] = 'image';
        $disabled = ( isset($args['pro']) && $args['pro']) ? ' disabled="disabled"': '';
        ?>
        <div class="col-sm-7">
          <div class="btn-group btn-group-no-margin" data-toggle="buttons" id="btn-group-style-circle">
            <?php foreach( $args['values'] as $_id => $_value ) : ?>
            <?php $toggle = ( ! empty($_value[1]) ) ? ' data-toggle="tooltip" data-placement="top" title="'.$_value[1].'" data-original-title="' . $_value[1] . '"' : ''; ?>
            <label class="btn btn-default<?php echo ($args['value'] == $_id) ? ' active' : '' ?> ">
            <input type="radio" name="<?php echo $args['name'] ?>" id="<?php echo $_id ?>" value="<?php echo $_id ?>" <?php echo  ($args['value'] == $_id) ? 'checked' : '' ?> <?php echo $disabled; ?> />
            <div class="icon-in-label ndd-spot-icon icon-style-1" <?php echo $toggle; ?>>
              <div class="ndd-icon-main-element">
                <?php if($args['buttons'] == 'image') : ?>
                    <img src="<?php echo IMAGE_ZOOM_URL.'assets/' . $_value[0] ?>"<?php echo $toggle; ?> />
                <?php else : ?>
                    <i class="<?php echo $_value[0]; ?>"></i>
                <?php endif; ?>
              </div>
            </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <?php
    }

    public function tooltip( $description = '' ) {
        if ( empty($description) ) return '';
        return '<img src="'.IMAGE_ZOOM_URL.'assets/images/question_mark.svg" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.$description.'" />';
    }

}

?>
