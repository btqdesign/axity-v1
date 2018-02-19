<?php
/**
 * Template for main maps list
 */
global $wpdb;

$new_map_link = admin_url( 'admin.php?page=hugeit_maps&task=create_new_map' );

$new_map_link = wp_nonce_url( $new_map_link, 'hugeit_maps_create_new_map' );

?>
<div class="wrap maps_list_container">
	<h1><?php _e( 'Huge-IT Google maps', 'hugeit_maps' ); ?><a class="page-title-action" href="<?php echo $new_map_link; ?>"><?php _e( 'Add New Map', 'hugeit_maps' ); ?></a></h1>

	<table class="widefat striped fixed maps_table">
		<thead>
			<tr>
				<th scope="col" id="header-id" style="width:30px"><span><?php _e( 'ID', 'hugeit_maps' ); ?></span></span></th>
				<th scope="col" id="header-name" style="width:85px"><span><?php _e( 'Name', 'hugeit_maps' ); ?></span></th>
				<th scope="col" id="header-shortcode" style="width:85px"><span><?php _e( 'Shortcode', 'hugeit_maps' ); ?></span></th>
				<th style="width:40px"><?php _e( 'Delete', 'hugeit_maps' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php

		$maps = Hugeit_Maps_Query::get_maps();
		if( !empty( $maps ) ){

			foreach( $maps as $map ){

				Hugeit_Maps_Template_Loader::get_template( 'admin/maps-list-single-item.php', array( 'map'=>$map ) );

			}

		}else{

			Hugeit_Maps_Template_Loader::get_template( 'admin/maps-list-no-items.php' );

		}

		?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col" class="footer-id" style="width:30px"><span><?php _e( 'ID', 'hugeit_maps' ); ?></th>
				<th scope="col" class="footer-name" style="width:85px"><span><?php _e( 'Name', 'hugeit_maps' ); ?></span></th>
				<th scope="col" class="footer-shortcode" style="width:85px"><span><?php _e( 'Shortcode', 'hugeit_maps' ); ?></span></th>
				<th style="width:40px"><?php _e( 'Delete', 'hugeit_maps' ); ?></th>
			</tr>
		</tfoot>
	</table>
</div>