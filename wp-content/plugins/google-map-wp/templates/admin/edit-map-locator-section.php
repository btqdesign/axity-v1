<?php
/**
 * @var $map Hugeit_Maps_Map
 */

?>

<li class="editing_section ">
    <div class="editing_heading">
        <span class="heading_icon"><img src="<?php echo HUGEIT_MAPS_IMAGES_URL . 'menu-icons/store.svg'; ?>"
                                        width="20"/></span>
        <?php _e('Store Locator', 'hugeit_maps'); ?>
        <div class="help">?
            <div class="help-block">
                <span class="pnt"></span>
                <p><?php _e('Store Locator settings for current map', 'hugeit_maps'); ?></p>
            </div>
        </div>
        <span class="heading_arrow"></span>
    </div>
    <div class="edit_content hide">
        <div id="g_map_locator_options">
            <form action="" method="post">
                <a id="locator_add_button" class="add_button clear" href="#">+<?php _e( 'Add New Store Locator', 'hugeit_maps' ); ?></a>
                <div class="hidden_edit_content hide">
                    <a href="#" id="back_locator" class="cancel left">◄ <?php _e( 'Back', 'hugeit_maps' ); ?></a>
                    <ul>
                        <li class="has_background">
                            <label for="locator_name"><?php _e( 'Store Name', 'hugeit_maps' ); ?></label>
                            <input type="text" name="locator_name" id="locator_name" placeholder="<?php _e( 'Optional Name', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="locator_addr"><?php _e( 'Store Address', 'hugeit_maps' ); ?></label>
                            <input type="text" name="locator_addr" id="locator_addr" placeholder="<?php _e( 'Store Address', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="locator_lat"><?php _e( 'Store Latitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly" name="locator_lat" id="locator_lat" placeholder="<?php _e( 'Latitude', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="locator_lng"><?php _e( 'Store Longitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly"  name="locator_lng" id="locator_lng" placeholder="<?php _e( 'Longitude', 'hugeit_maps' ); ?>" />
                        </li>
                        <li>
                            <ul class=""></ul>
                        </li>
                    </ul>
                    <div>
                        <input type="submit" class="button-primary" name="locator_submit" id="locator_submit" value="<?php _e( 'Save', 'hugeit_maps' ); ?>"/>
                        <span class="spinner"></span>
                        <a href="#" id="cancel_locator" class="cancel">Cancel</a>
                    </div>
                </div>
            </form>
            <div id="locator_edit_exist_section">
                <div class="edit_list_heading">
                    <div class="list_number"><?php _e( 'ID', 'hugeit_maps' ); ?></div>
                    <div class="edit_list_item"><?php _e( 'Title', 'hugeit_maps' ); ?></div>
                    <div class="edit_list_delete"><?php _e( 'Action', 'hugeit_maps' ); ?></div>
                </div>
                <?php

                $locators = Hugeit_Maps_Query::get_locator( array( 'map_id' => $map->get_id() ) );

                if( $locators ){
                    ?>
                    <ul class="list_exist" id="locator_list_exist">
                        <?php
                        foreach( $locators as $i => $locator ){
                            ?>
                            <li class="edit_list" data-list_id="<?php echo $locator->get_id(); ?>">
                                <div class="list_number"><?php echo $i; ?></div>
                                <div class="edit_list_item"><?php echo esc_html($locator->get_name()); ?></div>
                                <div class="edit_locator_list_delete edit_list_delete">
                                    <form class="edit_list_delete_form" method="post"
                                          action="">
                                        <input type="submit" class="button edit_list_delete_submit" name="edit_list_delete_submit"
                                               value="x"/>
                                        <input type="hidden" class="edit_list_delete_type" name="edit_list_delete_type"
                                               value="locator"/>
                                        <input type="hidden" class="edit_list_delete_table" value="hugeit_maps_stores"/>
                                        <input type="hidden" name="delete_locator_id" class="edit_list_delete_id"
                                               value="<?php echo $locator->get_id(); ?>"/>
                                    </form>
                                    <a href="#" class="button edit_locator_list_item"></a>
                                    <input type="hidden" class="locator_edit_id" name="locator_edit_id"
                                           value="<?php echo $locator->get_id(); ?>"/>
                                </div>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                    <?php
                }else{
                    echo "<p class='empty_locator'>".__('You have 0 stores','hugeit_maps')."</p>";
                }
                ?>
            </div>
            <form action="" method="post">
                <input type="hidden" id="locator_get_id" name="locator_get_id"/>
                <div class="update_list_item hide">
                    <a href="#" id="back_edit_locator" class="cancel left">◄ <?php _e( 'Back', 'hugeit_maps' ); ?></a>
                    <ul>
                        <li class="has_background">
                            <label for="locator_edit_name"><?php _e( 'Name', 'hugeit_maps' ); ?></label>
                            <input type="text" name="locator_edit_name" id="locator_edit_name" placeholder="<?php _e( 'Store Name', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="locator_edit_addr"><?php _e( 'Store Location', 'hugeit_maps' ); ?></label>
                            <input type="text" name="locator_edit_addr" id="locator_edit_addr" placeholder="<?php _e( 'Store Location', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="locator_edit_lat"><?php _e( 'Store Latitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly"  name="locator_edit_lat" id="locator_edit_lat" />
                        </li>
                        <li class="has_background">
                            <label for="locator_edit_lng"><?php _e( 'Store Longitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly"  name="locator_edit_lng" id="locator_edit_lng" />
                        </li>
                    </ul>
                    <div id="new_locator_panel"></div>
                    <div>
                        <input type="submit" class="button-primary" name="locator_edit_submit" id="locator_edit_submit" value="<?php _e( 'Save', 'hugeit_maps' ); ?>"/>
                        <span class="spinner"></span>
                        <a href="#" id="cancel_edit_locator" class="cancel"><?php _e( 'Cancel', 'hugeit_maps' ); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="edit_content map_options hide">
        <form action="" method="post">
            <ul>
                <li>
                    <label for="locator_enabled"><?php _e('Enable Store Locator', 'hugeit_maps'); ?></label>
                    <input type="checkbox" class="locator_enabled" id="locator_enabled" name="locator_enabled"
                           value="1" <?php checked($map->get_locator_enabled(), 1); ?> />
                </li>
            </ul>
            <span class="spinner"></span>
        </form>
    </div>
</li>
