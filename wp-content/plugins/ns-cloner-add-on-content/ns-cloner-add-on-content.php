<?php
/*
Plugin Name: NS Cloner Add-on: Content and Users
Plugin URI: http://neversettle.it
Description: Adds powerful options to increase flexibilty to the NS Cloner - copy all users, turn off media copy, select which post types to clone, and more.
Author: Never Settle
Version: 1.0.3.5.1
Network: true
Author URI: http://neversettle.it
License: GPLv2 or later
*/

/*
Copyright 2014 Never Settle (email : dev@neversettle.it)

This program is free software; you can redistribute it and/or 
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 

// Support for plugin auto-updates
require_once( plugin_dir_path(__FILE__).'lib/wp-updates-plugin.php' );
new WPUpdatesPluginUpdater_601( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));

// Show notice if cloner isn't active
require_once( plugin_dir_path(__FILE__).'lib/ns-cloner-check.php' );
new ns_cloner_check( "NS Cloner Content and Users" );

// Load the plugin
add_action( 'ns_cloner_before_construct', 'ns_cloner_addon_content_users_init' );
function ns_cloner_addon_content_users_init(){
	require_once( plugin_dir_path(__FILE__).'addons/ns-cloner-addon-content-users.php' );
	new ns_cloner_addon_content_users();
}

?>