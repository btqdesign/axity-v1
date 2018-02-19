<?php
/**
 * @var $map Hugeit_Maps_Map
 */

?>
<li class="editing_section">
    <div class="editing_heading">
        <span class="heading_icon"><img src="<?php echo HUGEIT_MAPS_IMAGES_URL.'menu-icons/layers.svg'; ?>" width="20" /></span>
        Layers
        <div class="help">?
            <div class="help-block">
                <span class="pnt"></span>
                <p><?php _e( 'Highlighted road areas on map, highlight the roads and real-time traffic on them, make bicycle road highlighted on your map, transit roads, which connects cities etc...', 'hugeit_maps' ); ?></p>
            </div>
        </div>
        <span class="heading_arrow"></span>
    </div>
    <div class="edit_content map_options hide">
        <form action="" method="post">
            <ul>
                <li class="has_background">
                    <label for="traffic_layer_enable"><?php _e( 'Enable Traffic Layer', 'hugeit_maps' ); ?></label>
                    <input <?php checked( 1, $map->get_traffic_layer() ); ?> type="checkbox" class="map_layers_inputs" id="traffic_layer_enable" name="traffic_layer_enable" value="1"/>
                </li>
                <li>
                    <label for="bicycling_layer_enable"><?php _e( 'Enable Bicycling Layer', 'hugeit_maps' ); ?></label>
                    <input <?php checked( 1, $map->get_bike_layer() ); ?> type="checkbox" class="map_layers_inputs" id="bicycling_layer_enable" name="bicycling_layer_enable" value="1"/>
                </li>
                <li class="has_background">
                    <label for="transit_layer_enable"><?php _e( 'Enable Transit layer', 'hugeit_maps' ); ?></label>
                    <input <?php checked( 1, $map->get_transit_layer() ); ?> type="checkbox" class="map_layers_inputs" id="transit_layer_enable" name="transit_layer_enable" value="1"/>
                </li>
            </ul>
            <span class="spinner"></span>
            <input type="submit" class="button-primary" id="submit_layers" name="submit_layers" value="<?php _e( 'Save', 'hugeit_maps' ); ?>"/>
        </form>
    </div>
</li>
