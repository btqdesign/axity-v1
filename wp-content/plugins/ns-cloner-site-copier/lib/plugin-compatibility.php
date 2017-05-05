<?php

// Don't drop main slimstat table because it will cause a foreign key error
// NOTE: this may cause problems in clone over mode - will have to address that down the road if necessary
add_filter( 'ns_cloner_do_drop_target_table', 'ns_cloner_slimstat_fix', 10, 3 );
function ns_cloner_slimstat_fix( $do, $table, $cloner ){
	if( strpos( $table, 'slim_stats' ) !== false ){
		$do = false;
	}	
	return $do;
}