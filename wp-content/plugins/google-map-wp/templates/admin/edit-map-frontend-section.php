<?php
/**
 * @var $map Hugeit_Maps_Map
 */

?>

<li class="editing_section ">
    <div class="editing_heading">
        <span class="heading_icon"><img src="<?php echo HUGEIT_MAPS_IMAGES_URL.'menu-icons/dashboard.svg'; ?>" width="20" /></span>
        <?php _e( 'Frontend Directions', 'hugeit_maps' ); ?>
        <div class="help">?
            <div class="help-block">
                <span class="pnt"></span>
                <p><?php _e( 'Frontend Directions settings for current map', 'hugeit_maps' ); ?></p>
            </div>
        </div>
        <span class="heading_arrow"></span>
    </div>
    <div class="edit_content map_options hide">
        <form action="" method="post">
            <ul>

                <li>
                    <label for="frontdir_enabled"><?php _e( 'Enable Frontend Directions', 'hugeit_maps' ); ?></label>
                    <input type="checkbox" class="frontdir_enabled" id="frontdir_enabled" name="frontdir_enabled" value="1" <?php checked( $map->get_frontdir_enabled(), 1 ); ?> />
                </li>

                <li>
                    <label for="frontdir_align"><?php _e( 'Directions Window Align', 'hugeit_maps' ); ?></label>
                    <select class="frontdir_align" name="frontdir_align" id="frontdir_align">
                        <option value="left" <?php selected( $map->get_frontdir_align(), 'left' ); ?>><?php _e( 'left', 'hugeit_maps' ); ?></option>
                        <option value="right" <?php selected( $map->get_frontdir_align(), 'right' ); ?>><?php _e( 'right', 'hugeit_maps' ); ?></option>
                        <option value="bottom" <?php selected( $map->get_frontdir_align(), 'bottom' ); ?>><?php _e( 'bottom', 'hugeit_maps' ); ?></option>
                    </select>
                </li>

                <li>
                    <label for="dir_window_width"><?php _e( 'Directions Window Width', 'hugeit_maps' ); ?></label>
                    <input type="number" class="dir_window_width" id="dir_window_width" name="dir_window_width" value="<?php echo $map->get_dir_window_width(); ?>" />
                </li>

            </ul>
            <span class="spinner"></span>
            <input type="submit" class="button-primary" name="frontdir_submit" id="frontdir_submit" value="Save"/>
        </form>
    </div>
</li>
