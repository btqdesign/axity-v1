<?php
/**
 * @var $map Hugeit_Maps_Map
 */

?>
<li class="editing_section">
    <div class="editing_heading">
        <span class="heading_icon"><img src="<?php echo HUGEIT_MAPS_IMAGES_URL.'menu-icons/direction.svg'; ?>" width="20" /></span>
        <?php _e('Directions','hg_gmaps'); ?>
        <div class="help">?
            <div class="help-block">
                <span class="pnt"></span>
                <p><?php _e('You can calculate directions by using the DirectionsService/feature. Directions are displayed as a polyline drawing the route on a map. The right click adds and varies  the strating and finishing point. Hold pressed the left click and drug the marker.','hg_gmaps'); ?></p>
            </div>
        </div>
        <span class="heading_arrow"></span>
    </div>
    <div class="edit_content hide">
        <div id="g_map_direction_options">
            <form action="" method="post">
                <a id="direction_add_button" class="add_button clear" href="#">+<?php _e( 'Add New Direction', 'hugeit_maps' ); ?></a>
                <div class="hidden_edit_content hide">
                    <a href="#" id="back_direction" class="cancel left">◄ <?php _e( 'Back', 'hugeit_maps' ); ?></a>
                    <ul>
                        <li class="has_background">
                            <label for="direction_name"><?php _e( 'Name', 'hugeit_maps' ); ?></label>
                            <input type="text" name="direction_name" id="direction_name" placeholder="<?php _e( 'Optional Name', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="direction_start_addr"><?php _e( 'Start Location', 'hugeit_maps' ); ?></label>
                            <input type="text" name="direction_start_addr" id="direction_start_addr" placeholder="<?php _e( 'Start Address', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="direction_start_lat"><?php _e( 'Start Location Latitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly" name="direction_start_lat" id="direction_start_lat" placeholder="<?php _e( 'Latitude', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="direction_start_lng"><?php _e( 'Start Location Longitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly" name="direction_start_lng" id="direction_start_lng" placeholder="<?php _e( 'Longitude', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="direction_end_addr"><?php _e( 'End Location', 'hugeit_maps' ); ?></label>
                            <input type="text" name="direction_end_addr" id="direction_end_addr" placeholder="<?php _e( 'End Address', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="direction_end_lat"><?php _e( 'End Location Latitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly" name="direction_end_lat" id="direction_end_lat" placeholder="<?php _e( 'Latitude', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="direction_end_lng"><?php _e( 'End Location Longitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly" name="direction_end_lng" id="direction_end_lng" placeholder="<?php _e( 'Longitude', 'hugeit_maps' ); ?>" />
                        </li>
                        <li>
                            <label for="direction_travelmode"><?php _e( 'End Location Longitude', 'hugeit_maps' ); ?></label>
                            <select class="direction_options_input" id="direction_travelmode" name="direction_travelmode">
                                <option value="DRIVING"><?php _e( 'Driving', 'hugeit_maps' ); ?></option>
                                <option value="WALKING"><?php _e( 'Walking', 'hugeit_maps' ); ?></option>
                                <option value="BICYCLING"><?php _e( 'Bicycling', 'hugeit_maps' ); ?></option>
                                <option value="TRANSIT"><?php _e( 'Transit', 'hugeit_maps' ); ?></option>
                            </select>
                        </li>
                        <li>
                            <label for="direction_show_stepss"><?php _e( 'Show Markers For Steps', 'hugeit_maps' ); ?></label>
                            <input type="checkbox" class="direction_options_input" name="direction_show_steps"
                                   id="direction_show_steps" value="yes"/>
                        </li>
                        <li class="has_background">
                            <label for="direction_line_opacity"><?php _e( 'Line Transparency', 'hugeit_maps' ); ?></label>
                            <div class="slider-container" style="float:left; width:55%; height:25px; ">
                                <input type="text" name="direction_line_opacity" id="direction_line_opacity"
                                       class="direction_options_input" data-slider-highlight="true"
                                       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" data-slider="true"
                                       value="0.9"/>
                                <span style="position:absolute; top: 4px;left: 160px;">0.9</span>
                            </div>
                        </li>
                        <li>
                            <label for="direction_line_color"><?php _e( 'Line Color', 'hugeit_maps' ); ?></label>
                            <input type="text" class="jscolor direction_options_input" name="direction_line_color"
                                   id="direction_line_color" value="FF0F0F"/>
                        </li>
                        <li class="has_background" >
                            <label for="direction_line_width"><?php _e( 'Line Width', 'hugeit_maps' ); ?></label>
                            <div class="slider-container" style="float:left; width:55%; height:25px; ">
                                <input type="text" name="direction_line_width" class="direction_options_input" id="direction_line_width"
                                       data-slider-highlight="true" data-slider-values="<?php echo implode( ',', range( 0, 50 ) ); ?>" data-slider="true" value="5"/>
                                <span style="position:absolute; top: 4px;left: 160px;">5</span>
                            </div>
                        </li>
                        <li>
                            <ul class=""></ul>
                        </li>
                    </ul>
                    <div>
                        <input type="submit" class="button-primary" name="direction_submit" id="direction_submit" value="<?php _e( 'Save', 'hugeit_maps' ); ?>"/>
                        <span class="spinner"></span>
                        <a href="#" id="cancel_direction" class="cancel">Cancel</a>
                    </div>
                </div>
            </form>
            <div id="direction_edit_exist_section">
                <div class="edit_list_heading">
                    <div class="list_number"><?php _e( 'ID', 'hugeit_maps' ); ?></div>
                    <div class="edit_list_item"><?php _e( 'Title', 'hugeit_maps' ); ?></div>
                    <div class="edit_list_delete"><?php _e( 'Action', 'hugeit_maps' ); ?></div>
                </div>

                <?php

                $directions = Hugeit_Maps_Query::get_directions( array( 'map_id' => $map->get_id() ) );

                if( $directions ){
                ?>
                <ul class="list_exist" id="polygone_list_exist">
                    <?php
                    foreach( $directions as $i => $direction ){
                        ?>
                        <li class="edit_list" data-list_id="<?php echo $direction->get_id(); ?>">
                            <div class="list_number"><?php echo $i; ?></div>
                            <div class="edit_list_item"><?php echo $direction->get_name(); ?></div>
                            <div class="edit_direction_list_delete edit_list_delete">
                                <form class="edit_list_delete_form" method="post"
                                      action="">
                                    <input type="submit" class="button edit_list_delete_submit" name="edit_list_delete_submit"
                                           value="x"/>
                                    <input type="hidden" class="edit_list_delete_type" name="edit_list_delete_type"
                                           value="direction"/>
                                    <input type="hidden" class="edit_list_delete_table" value="hugeit_maps_directions"/>
                                    <input type="hidden" name="delete_direction_id" class="edit_list_delete_id"
                                           value="<?php echo $direction->get_id(); ?>"/>
                                </form>
                                <a href="#" class="button edit_direction_list_item"></a>
                                <input type="hidden" class="direction_edit_id" name="direction_edit_id"
                                       value="<?php echo $direction->get_id(); ?>"/>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
                }else{
                    echo "<p class='empty_direction'>".__('You have 0 directions on this map','hugeit_maps')."</p>";
                }
                ?>
            </div>
            <form action="" method="post">
                <input type="hidden" id="direction_get_id" name="direction_get_id"/>
                <div class="update_list_item hide">
                    <a href="#" id="back_edit_direction" class="cancel left">◄ <?php _e( 'Back', 'hugeit_maps' ); ?></a>
                    <ul>
                        <li class="has_background">
                            <label for="direction_edit_name"><?php _e( 'Name', 'hugeit_maps' ); ?></label>
                            <input type="text" name="direction_edit_name" id="direction_edit_name" placeholder="<?php _e( 'Optional Name', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="direction_edit_start_addr"><?php _e( 'Start Location', 'hugeit_maps' ); ?></label>
                            <input type="text" name="direction_edit_start_addr" id="direction_edit_start_addr" placeholder="<?php _e( 'Location', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="direction_edit_start_lat"><?php _e( 'Start Location Latitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly" name="direction_edit_start_lat" id="direction_edit_start_lat" />
                        </li>
                        <li class="has_background">
                            <label for="direction_edit_start_lng"><?php _e( 'Start Location Longitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly" name="direction_edit_start_lng" id="direction_edit_start_lng" />
                        </li>
                        <li class="has_background">
                            <label for="direction_edit_end_addr"><?php _e( 'End Location', 'hugeit_maps' ); ?></label>
                            <input type="text" name="direction_edit_end_addr" id="direction_edit_end_addr" placeholder="<?php _e( 'Location', 'hugeit_maps' ); ?>" />
                        </li>
                        <li class="has_background">
                            <label for="direction_edit_end_lat"><?php _e( 'End Location Latitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly" name="direction_edit_end_lat" id="direction_edit_end_lat" />
                        </li>
                        <li class="has_background">
                            <label for="direction_edit_end_lng"><?php _e( 'End Location Longitude', 'hugeit_maps' ); ?></label>
                            <input type="text" readonly="readonly" name="direction_edit_end_lng" id="direction_edit_end_lng" />
                        </li>
                        <li>
                            <label for="direction_edit_travelmode"><?php _e( 'End Location Longitude', 'hugeit_maps' ); ?></label>
                            <select class="direction_edit_options_input" id="direction_edit_travelmode" name="direction_edit_travelmode">
                                <option value="DRIVING"><?php _e( 'Driving', 'hugeit_maps' ); ?></option>
                                <option value="WALKING"><?php _e( 'Walking', 'hugeit_maps' ); ?></option>
                                <option value="BICYCLING"><?php _e( 'Bicycling', 'hugeit_maps' ); ?></option>
                                <option value="TRANSIT"><?php _e( 'Transit', 'hugeit_maps' ); ?></option>
                            </select>
                        </li>
                        <li>
                            <label for="direction_edit_show_stepss"><?php _e( 'Show Markers For Steps', 'hugeit_maps' ); ?></label>
                            <input type="checkbox" class="direction_edit_options_input" name="direction_edit_show_steps"
                                   id="direction_edit_show_steps" value="yes"/>
                        </li>
                        <li class="has_background">
                            <label for="direction_edit_line_opacity"><?php _e( 'Line Transparency', 'hugeit_maps' ); ?></label>
                            <div class="slider-container" style="float:left; width:55%; height:25px; ">
                                <input type="text" name="direction_edit_line_opacity" id="direction_edit_line_opacity"
                                       class="direction_edit_options_input" data-slider-highlight="true"
                                       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" data-slider="true"
                                       value="0.9"/>
                                <span style="position:absolute; top: 4px;left: 160px;">0.9</span>
                            </div>
                        </li>
                        <li>
                            <label for="direction_edit_line_color"><?php _e( 'Line Color', 'hugeit_maps' ); ?></label>
                            <input type="text" class="jscolor direction_edit_options_input" name="direction_edit_line_color"
                                   id="direction_edit_line_color" value="FF0F0F"/>
                        </li>
                        <li class="has_background" >
                            <label for="direction_edit_line_width"><?php _e( 'Line Width', 'hugeit_maps' ); ?></label>
                            <div class="slider-container" style="float:left; width:55%; height:25px; ">
                                <input type="text" name="direction_edit_line_width" class="direction_edit_options_input" id="direction_edit_line_width"
                                       data-slider-highlight="true" data-slider-values="<?php echo implode( ',', range( 0, 50 ) ); ?>" data-slider="true" value="5"/>
                                <span style="position:absolute; top: 4px;left: 160px;">5</span>
                            </div>
                        </li>
                    </ul>
                    <div id="new_direction_panel"></div>
                    <div>
                        <input type="submit" class="button-primary" name="direction_edit_submit" id="direction_edit_submit" value="<?php _e( 'Save', 'hugeit_maps' ); ?>"/>
                        <span class="spinner"></span>
                        <a href="#" id="cancel_edit_direction" class="cancel"><?php _e( 'Cancel', 'hugeit_maps' ); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</li>
