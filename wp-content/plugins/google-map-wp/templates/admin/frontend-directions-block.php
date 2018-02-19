<?php
/**
 * @var $map Hugeit_Maps_Map
 */
?>

<div class="search-box">
    <!-- search place -->
    <div id="show-directions">
        <div >
            <input type="text" name="map_place_search" id="map_place_search" placeholder="Search Google Maps">
            <input type="hidden" name="map_place_search_lat" id="map_place_search_lat" >
            <input type="hidden" name="map_place_search_lng" id="map_place_search_lng">
        </div>
        <div class="search-button">
            <span class="searchbox-search"></span>
        </div>
        <div class="directions-button">
            <span class="searchbox-directions"></span>
        </div>
    </div>
    <!-- search place end -->

    <!-- directions -->
    <div id="directions-block">
        <div id="travel-mode">
            <ul>
                <?php $travel_modes=array('Driving','Transit','Walking','Cycling');?>
                <?php foreach($travel_modes as $travel_mode):?>
                    <?php if($travel_mode=='Driving') $class='current'; else $class='';?>
                    <li class="travel_mode <?php echo $class;?> mode-<?php echo $travel_mode;?>" data-mode="<?php echo $travel_mode;?>"></li>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="dir-row">
            <div class="dir-input-cont">
                <input type="text" name="map_direction_start" id="map_direction_start" placeholder="Choose Starting Point or right click on map">
                <input type="hidden" name="map_direction_start_lat" id="map_direction_start_lat" >
                <input type="hidden" name="map_direction_start_lng" id="map_direction_start_lng">
            </div>
        </div>
        <div class="dir-row">
            <div class="dir-input-cont">
                <input type="text" name="map_direction_end" id="map_direction_end" placeholder="Choose Destination">
                <input type="hidden" name="map_direction_end_lat" id="map_direction_end_lat" >
                <input type="hidden" name="map_direction_end_lng" id="map_direction_end_lng">
            </div>
        </div>

        <div id="dir-info-block">

        </div>
    </div>
    <!-- end directions -->
</div>