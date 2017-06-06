<?php
/**
 * @var $map Hugeit_Maps_Map
 */
?>

<div class="hg-search-box <?php echo $map->get_frontdir_align();?>" >
    <!-- search place -->
    <div id="hg-show-directions-<?php echo $map->get_id(); ?>" class="hg-show-directions">
        <div >
            <input type="text" name="map_place_search" class="hg-map_place_search" id="hg-map_place_search_<?php echo $map->get_id(); ?>" placeholder="Search Google Maps">
            <input type="hidden" name="map_place_search_lat" class="hg-map_place_search_lat" id="hg-map_place_search_lat_<?php echo $map->get_id(); ?>" >
            <input type="hidden" name="map_place_search_lng" class="hg-map_place_search_lng" id="hg-map_place_search_lng_<?php echo $map->get_id(); ?>">
        </div>
        <div class="hg-search-button">
            <span class="hg-searchbox-search"></span>
        </div>
        <div class="hg-directions-button">
            <span class="hg-searchbox-directions"></span>
        </div>

        <?php if($map->get_frontdir_align()!=='bottom'){ ?>
            <div class="hg-hide-search-window"></div>
        <?php } ?>
    </div>
    <!-- search place end -->

    <!-- directions -->
    <div class="hg-directions-block" id="hg-directions-block-<?php echo $map->get_id(); ?>" style="width:<?php echo $map->get_dir_window_width();?>px">
        <div class="hg-travel-mode" id="hg-travel-mode-<?php echo $map->get_id(); ?>">
            <ul class="clear-float">
                <?php $travel_modes=array('Driving','Transit','Walking','Bicycling');?>
                <?php foreach($travel_modes as $travel_mode):?>
                    <?php if($travel_mode=='Driving') $class='current'; else $class='';?>
                    <li class="hg-travel_mode  mode-<?php echo $travel_mode;?> <?php echo $class;?>" data-mode="<?php echo $travel_mode;?>"></li>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="hg-dir-row">
            <div class="hg-dir-input-cont">
                <input type="text" name="map_direction_start" class="hg-map_direction_start" id="hg-map_direction_start_<?php echo $map->get_id(); ?>" placeholder="Choose Starting Point or right click on map">
                <input type="hidden" name="map_direction_start_lat" class="hg-map_direction_start_lat" id="hg-map_direction_start_lat_<?php echo $map->get_id(); ?>" >
                <input type="hidden" name="map_direction_start_lng" class="hg-map_direction_start_lng" id="hg-map_direction_start_lng_<?php echo $map->get_id(); ?>">
            </div>
        </div>
        <div class="hg-dir-row">
            <div class="hg-dir-input-cont">
                <input type="text" name="map_direction_end" class="hg-map_direction_end" id="hg-map_direction_end_<?php echo $map->get_id(); ?>" placeholder="Choose Destination">
                <input type="hidden" name="map_direction_end_lat" class="hg-map_direction_end_lat" id="hg-map_direction_end_lat_<?php echo $map->get_id(); ?>" >
                <input type="hidden" name="map_direction_end_lng" class="hg-map_direction_end_lng" id="hg-map_direction_end_lng_<?php echo $map->get_id(); ?>">
            </div>
        </div>
        <div class="hg-widget-directions-right-overlay">
            <button aria-label="Reverse starting point and destination" data-tooltip="Reverse starting point and destination" class="hg-widget-directions-reverse">
                <div class="hg-widget-directions-icon reverse">

                </div>
            </button>
        </div>
        <div class="hg-dir-dist-units">
            <?php _e('Distance Units','hugeit_maps');?>

            <ul class="list-inline pull-right hg-dist-units">
                <li class="checked" data-unit="km"><?php _e('km','hugeit_maps');?></li>
                <li class="" data-unit="mile"><?php _e('mile','hugeit_maps');?></li>
            </ul>
        </div>

        <div class="hg-dir-info-block" id="hg-dir-info-block-<?php echo $map->get_id(); ?>">

        </div>

        <?php if($map->get_frontdir_align()!=='bottom'){ ?>
            <div class="hg-close-dir-window"></div>
            <div class="hg-hide-dir-window"></div>
        <?php } ?>
    </div>
    <!-- end directions -->
</div>