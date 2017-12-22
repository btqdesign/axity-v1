<?php

namespace threewp_broadcast;

use \threewp_broadcast\broadcast_data\blog;

class ThreeWP_Broadcast
	extends \plainview\sdk_broadcast\wordpress\base
{
	use \plainview\sdk_broadcast\wordpress\traits\debug;

	use traits\actions;
	use traits\admin_menu;
	use traits\admin_scripts;
	use traits\attachments;
	use traits\broadcast_data;
	use traits\broadcasting;
	use traits\meta_boxes;
	use traits\post_actions;
	use traits\terms_and_taxonomies;
	use traits\savings_calculator;

	/**
		@brief		Broadcasting stack.
		@details

		An array of broadcasting_data objects, the latest being at the end.

		@since		20131120
	**/
	public $broadcasting = [];

	/**
		@brief	Public property used during the broadcast process.
		@see	include/Broadcasting_Data.php
		@since	20130530
		@var	$broadcasting_data
	**/
	public $broadcasting_data = null;

	/**
		@brief		Display Broadcast completely, including menus and post overview columns.
		@since		20131015
		@var		$display_broadcast
	**/
	public $display_broadcast = true;

	/**
		@brief		Display the Broadcast columns in the post overview.
		@details	Disabling this will prevent the user from unlinking posts.
		@since		20131015
		@var		$display_broadcast_columns
	**/
	public $display_broadcast_columns = true;

	/**
		@brief		Display the Broadcast menu
		@since		20131015
		@var		$display_broadcast_menu
	**/
	public $display_broadcast_menu = true;

	/**
		@brief		Add the meta box in the post editor?
		@details	Standard is null, which means the plugin(s) should work it out first.
		@since		20131015
		@var		$display_broadcast_meta_box
	**/
	public $display_broadcast_meta_box = true;

	/**
		@brief	Display information in the menu about the premium pack?
		@see	threewp_broadcast_premium_pack_info()
		@since	20131004
		@var	$display_premium_pack_info
	**/
	public $display_premium_pack_info = true;

	/**
		@brief		An array of incompatible plugins that prevent Broadcast from working.
		@since		2017-01-16 17:14:35
	**/
	public static $incompatible_plugins = [
		'intuitive-custom-post-order/intuitive-custom-post-order.php',
		'post-type-switcher/post-type-switcher.php',
		'taxonomy-terms-order/taxonomy-terms-order.php',
	];

	/**
		@brief		The language domain to use.
		@since		2017-02-21 20:00:41
	**/
	public $language_domain = 'threewp_broadcast';

	/**
		@brief		Caches permalinks looked up during this page view.
		@see		post_link()
		@since		20130923
	**/
	public $permalink_cache;

	public $plugin_version = THREEWP_BROADCAST_VERSION;

	public function _construct()
	{
		if ( ! $this->is_network )
			return;

		$this->add_action( 'add_meta_boxes' );

		if ( $this->get_site_option( 'override_child_permalinks' ) )
		{
			$this->add_filter( 'page_link', 'post_link', 10, 3 );
			$this->add_filter( 'post_link', 10, 3 );
			$this->add_filter( 'post_type_link', 'post_link', 10, 3 );
		}

		$this->attachments_init();
		$this->post_actions_init();
		$this->savings_calculator_init();
		$this->terms_and_taxonomies_init();

		$this->add_action( 'plugins_loaded' );

		$this->add_filter( 'threewp_broadcast_add_meta_box' );
		$this->add_filter( 'threewp_broadcast_admin_menu', 'add_post_row_actions_and_hooks', 100 );

		// This is a normal broadcast action, not a special action object. This is a holdover from the good old days from when Broadcast used normal actions.
		// Don't want to break anyone's plugins.
		$this->add_action( 'threewp_broadcast_broadcast_post' );

		$this->add_action( 'threewp_broadcast_each_linked_post' );
		$this->add_action( 'threewp_broadcast_get_user_writable_blogs', 100 );		// Allow other plugins to do this first.
		$this->add_filter( 'threewp_broadcast_get_post_types', 5 );					// Add our custom post types to the array of broadcastable post types.
		$this->add_action( 'threewp_broadcast_maybe_clear_post', 100 );
		$this->add_filter( 'threewp_broadcast_parse_content' );
		$this->add_action( 'threewp_broadcast_prepare_broadcasting_data' );
		$this->add_filter( 'threewp_broadcast_prepare_meta_box', 5 );
		$this->add_filter( 'threewp_broadcast_prepare_meta_box', 'threewp_broadcast_prepared_meta_box', 100 );
		$this->add_filter( 'threewp_broadcast_preparse_content' );


		if ( $this->get_site_option( 'canonical_url' ) )
			$this->add_action( 'wp_head', 1 );

		$this->admin_menu_trait_init();

		$this->permalink_cache = (object)[];
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Activate / Deactivate
	// --------------------------------------------------------------------------------------------

	public function activate()
	{
		if ( !$this->is_network )
			return;

		$db_ver = $this->get_site_option( 'database_version', 0 );

		// 2016-01-05 Always run the create if not exists.
		$this->query("CREATE TABLE IF NOT EXISTS `". $this->broadcast_data_table() . "` (
		  `blog_id` int(11) NOT NULL COMMENT 'Blog ID',
		  `post_id` int(11) NOT NULL COMMENT 'Post ID',
		  `data` longtext NOT NULL COMMENT 'Serialized BroadcastData',
		  KEY `blog_id` (`blog_id`,`post_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		");

		if ( $db_ver < 1 )
		{
			// Remove old options
			$this->delete_site_option( 'requirewhenbroadcasting' );

			// Removed 1.5
			$this->delete_site_option( 'activity_monitor_broadcasts' );
			$this->delete_site_option( 'activity_monitor_group_changes' );
			$this->delete_site_option( 'activity_monitor_unlinks' );

			// Cats and tags replaced by taxonomy support. Version 1.5
			$this->delete_site_option( 'role_categories' );
			$this->delete_site_option( 'role_categories_create' );
			$this->delete_site_option( 'role_tags' );
			$this->delete_site_option( 'role_tags_create' );
			$db_ver = 1;
		}

		if ( $db_ver < 2 )
		{
			// Convert the array site options to strings.
			foreach( [ 'custom_field_exceptions', 'post_types' ] as $key )
			{
				$value = $this->get_site_option( $key, '' );
				if ( is_array( $value ) )
				{
					$value = array_filter( $value );
					$value = implode( ' ', $value );
				}
				$this->update_site_option( $key, $value );
			}
			$db_ver = 2;
		}

		if ( $db_ver < 3 )
		{
			$this->delete_site_option( 'always_use_required_list' );
			$this->delete_site_option( 'blacklist' );
			$this->delete_site_option( 'requiredlist' );
			$this->delete_site_option( 'role_taxonomies_create' );
			$this->delete_site_option( 'role_groups' );
			$db_ver = 3;
		}

		if ( $db_ver < 4 )
		{
			$exceptions = $this->get_site_option( 'custom_field_exceptions', '' );
			$this->delete_site_option( 'custom_field_exceptions' );
			$whitelist = $this->get_site_option( 'custom_field_whitelist', $exceptions );
			$db_ver = 4;
		}

		// 2016-01-05 This used to be v5, but is now always run.
		$this->create_broadcast_data_id_column();

		if ( $db_ver < 6 )
		{
			$this->query("DROP TABLE IF EXISTS `".$this->wpdb->base_prefix."_3wp_broadcast`");
			$db_ver = 6;
		}

		if ( $db_ver < 7 )
		{
			foreach( [
				'role_broadcast',
				'role_link',
				'role_broadcast_as_draft',
				'role_broadcast_scheduled_posts',
				'role_taxonomies',
				'role_custom_fields',
			] as $old_role_option )
			{
				$old_value = $this->get_site_option( $old_role_option );
				if ( is_array( $old_value ) )
					continue;
				$new_value = static::convert_old_role( $old_value );
				$this->update_site_option( $old_role_option, $new_value );
			}
			$db_ver = 7;
		}

		if ( $db_ver < 8 )
		{
			// Make the table a longtext for those posts with many links.
			$this->query("ALTER TABLE `". $this->broadcast_data_table() . "` CHANGE `data` `data` LONGTEXT");
			$db_ver = 8;
		}

		$this->update_site_option( 'database_version', $db_ver );
	}

	public function uninstall()
	{
		$this->delete_site_option( 'broadcast_internal_custom_fields' );
		$query = sprintf( "DROP TABLE `%s`", $this->broadcast_data_table() );
		$this->query( $query );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Modify the plugin links in the plugins table.
		@since		2017-09-22 01:30:45
	**/
	public function plugin_action_links( $links, $plugin_name )
	{
		if ( $plugin_name != 'threewp-broadcast/ThreeWP_Broadcast.php' )
			return $links;
		if ( is_network_admin() )
			$url = network_admin_url( 'admin.php?page=threewp_broadcast' );
		else
			$url = admin_url( 'admin.php?page=threewp_broadcast' );
		$links []= sprintf( '<a href="%s">%s</a>',
			$url,
			__( 'Settings', 'threewp_broadcast' )
		);
		return $links;
	}

	/**
		@brief		Modify the plugin meta in the plugins table.
		@since		2017-09-22 01:50:49
	**/
	public function plugin_row_meta( $meta, $plugin_name )
	{
		if ( $plugin_name != 'threewp-broadcast/ThreeWP_Broadcast.php' )
			return $meta;
		if ( ! isset( $this->__plugin_pack ) )
		{
			if ( is_network_admin() )
				$url = network_admin_url( 'admin.php?page=threewp_broadcast_premium_pack_info' );
			else
				$url = admin_url( 'admin.php?page=threewp_broadcast_premium_pack_info' );
			$meta []= sprintf( '<a href="%s" title="%s">%s</a>',
				$url,
				__( 'View the add-ons available for Broadcast', 'threewp_broadcast' ),
				__( 'Add-ons', 'threewp_broadcast' )
			);
		}
		return $meta;
	}

	/**
		@brief		Broadcast is ready for broadcasting.
		@since		2015-10-29 12:22:53
	**/
	public function plugins_loaded()
	{
		$this->__loaded = true;
		$action = $this->new_action( 'loaded' );
		$action->execute();
	}

	public function post_link( $link, $post )
	{
		// Don't overwrite the permalink if we're in the editing window.
		// This allows the user to change the permalink.
		if ( $_SERVER[ 'SCRIPT_NAME' ] == '/wp-admin/post.php' )
			return $link;

		if ( isset( $this->_is_getting_permalink ) )
			return $link;

		$this->_is_getting_permalink = true;

		$blog_id = get_current_blog_id();

		// Pages return just the ID. Posts return a proper page object.
		if ( ! is_object( $post ) )
			$post = get_post( $post );

		// Have we already checked this post ID for a link?
		$key = 'b' . $blog_id . '_p' . $post->ID;
		if ( property_exists( $this->permalink_cache, $key ) )
		{
			unset( $this->_is_getting_permalink );
			return $this->permalink_cache->$key;
		}

		$broadcast_data = $this->get_post_broadcast_data( $blog_id, $post->ID );

		$linked_parent = $broadcast_data->get_linked_parent();

		if ( $linked_parent === false)
		{
			$this->permalink_cache->$key = $link;
			unset( $this->_is_getting_permalink );
			return $link;
		}

		switch_to_blog( $linked_parent[ 'blog_id' ] );
		$post = get_post( $linked_parent[ 'post_id' ] );
		$parent_permalink = get_permalink( $post );
		restore_current_blog();

		unset( $this->_is_getting_permalink );

		$action = new actions\override_child_permalink();
		$action->child_permalink = $link;
		$action->parent_permalink = $parent_permalink;
		$action->post = $post;
		$action->returned_permalink = $parent_permalink;
		$action->execute();

		$this->permalink_cache->$key = $action->returned_permalink;

		return $action->returned_permalink;
	}

	/**
		@brief		Execute callbacks on all posts linked to this specific post.
		@since		2015-05-02 21:33:55
	**/
	public function threewp_broadcast_each_linked_post( $action )
	{
		$prefix = 'Each Linked Post: ';

		// First, we need the broadcast data of the post.
		if ( $action->blog_id === null )
			$action->blog_id = get_current_blog_id();

		$this->debug( $prefix . 'Loading broadcast data of post %s on blog %s.', $action->post_id, $action->blog_id );

		$broadcast_data = $this->get_post_broadcast_data( $action->blog_id, $action->post_id );

		// Does this post have a parent?
		$parent = $broadcast_data->get_linked_parent();
		if ( $parent !== false )
		{
			if ( $action->on_parent )
			{
				$this->debug( $prefix . 'Executing callbacks on parent post %s on blog %s.', $parent[ 'post_id' ], $parent[ 'blog_id' ] );
				if ( $this->blog_exists( $parent[ 'blog_id' ] ) )
				{
					switch_to_blog( $parent[ 'blog_id' ] );
					$o = (object)[];
					$o->post_id = $parent[ 'post_id' ];
					$o->post = get_post( $o->post_id );
					$this->debug( $prefix . '' );
					foreach( $action->callbacks as $callback )
						$callback( $o );
					restore_current_blog();
				}
			}
			else
				$this->debug( $prefix . 'Not executing on parent.' );
			$broadcast_data = $this->get_post_broadcast_data( $parent[ 'blog_id' ], $parent[ 'post_id' ] );
		}
		else
			$this->debug( $prefix . 'No linked parent.' );

		if ( $action->on_children )
		{
			$this->debug( $prefix . 'Executing on children.' );
			foreach( $broadcast_data->get_linked_children() as $blog_id => $post_id )
			{
				// Do not bother eaching this child if we started here.
				if ( $blog_id == $action->blog_id )
					continue;
				if ( ! $this->blog_exists( $blog_id ) )
					continue;
				switch_to_blog( $blog_id );
				$o = (object)[];
				$o->post_id = $post_id;
				$o->post = get_post( $post_id );
				$this->debug( $prefix . 'Executing callbacks on child post %s on blog %s.', $post_id, $blog_id );
				foreach( $action->callbacks as $callback )
					$callback( $o );
				restore_current_blog();
			}
		}
		else
			$this->debug( $prefix . 'Not executing on children.' );
		$this->debug( $prefix . 'Finished.' );
	}

	/**
		@brief		Return a collection of blogs that the user is allowed to write to.
		@since		20131003
	**/
	public function threewp_broadcast_get_user_writable_blogs( $action )
	{
		if ( $action->is_finished() )
			return;

		$network_id = get_network()->id;
		$blogs = get_blogs_of_user( $action->user_id );
		foreach( $blogs as $blog)
		{
			// Filter out those blogs thare are not in our network.
			if ( $blog->site_id != $network_id )
				continue;
			$blog = blog::make( $blog );
			$blog->id = $blog->userblog_id;
			if ( ! $this->is_blog_user_writable( $action->user_id, $blog ) )
				continue;
			$action->blogs->set( $blog->id, $blog );
		}

		$action->blogs->sort_logically();
		$action->finish();
	}

	/**
		@brief		Convert the post_type site option to an array in the action.
		@since		2014-02-22 10:33:57
	**/
	public function threewp_broadcast_get_post_types( $action )
	{
		$post_types = $this->get_site_option( 'post_types' );
		$post_types = explode( ' ', $post_types );
		foreach( $post_types as $post_type )
			$action->post_types[ $post_type ] = $post_type;
	}

	/**
		@brief		Decide what to do with the POST.
		@since		2014-03-23 23:08:31
	**/
	public function threewp_broadcast_maybe_clear_post( $action )
	{
		if ( $action->is_finished() )
		{
			$this->debug( 'Not maybe clearing the POST.' );
			return;
		}

		$clear_post = $this->get_site_option( 'clear_post', true );
		if ( $clear_post )
		{

			$this->debug( 'Clearing the POST.' );
			$action->post = [];
		}
		else
			$this->debug( 'Not clearing the POST.' );
	}

	/**
		@brief		Use the correct canonical link.
	**/
	public function wp_head()
	{
		// Only override the canonical if we're looking at a single post.
		$override = false;
		$override |= is_single();
		$override |= is_page();
		if ( ! $override )
			return;

		global $post;
		global $blog_id;

		// Find the parent, if any.
		$broadcast_data = $this->get_post_broadcast_data( $blog_id, $post->ID );
		$linked_parent = $broadcast_data->get_linked_parent();
		if ( $linked_parent === false)
			return;

		// Post has a parent. Get the parent's permalink.
		switch_to_blog( $linked_parent[ 'blog_id' ] );
		$url = get_permalink( $linked_parent[ 'post_id' ] );
		restore_current_blog();

		echo sprintf( '<link rel="canonical" href="%s" />', $url );
		echo "\n";

		// Prevent Wordpress from outputting its own canonical.
		remove_action( 'wp_head', 'rel_canonical' );

		// Remove Canonical Link Added By Yoast WordPress SEO Plugin
		if ( class_exists( '\\WPSEO_Frontend' ) )
		{
			$this->add_filter( 'wpseo_canonical', 'wp_head_remove_wordpress_seo_canonical' );;
			$wpseo_frontend = \WPSEO_Frontend::get_instance();
			remove_action( 'wpseo_head', array( $wpseo_frontend, 'canonical' ), 20 );
		}
	}

	/**
		@brief		Remove Wordpress SEO canonical link so that it doesn't conflict with the parent link.
		@since		2014-01-16 00:36:15
	**/

	public function wp_head_remove_wordpress_seo_canonical()
	{
		// Tip seen here: http://wordpress.org/support/topic/plugin-wordpress-seo-by-yoast-remove-canonical-tags-in-header?replies=10
		return false;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Return the API class.
		@since		2015-06-16 22:21:22
	**/
	public function api()
	{
		return new api();
	}

	/**
		@brief		Checks whether a blog exists.
		@details	Yes, Wordpress' switch_to_blog() doesn't do that check and ALWAYS RETURNS TRUE (!!!!).
		@since		2017-01-18 20:10:26
	**/
	public function blog_exists( $blog_id )
	{
		return get_blog_status( $blog_id, 'blog_id' ) == $blog_id;
	}

	/**
		@brief		Convenience function to return a Plainview SDK Collection.
		@since		2014-10-31 13:21:06
	**/
	public static function collection( $items = [] )
	{
		return new \plainview\sdk_broadcast\collections\Collection( $items );
	}

	/**
		@brief		Convert old role to array of roles.
		@details	Used to convert 'editor' to [ 'editor', 'author', 'contribuor', 'subscriber' ], for example.
		@since		2015-03-17 18:09:27
	**/
	public static function convert_old_role( $role )
	{
		$old_roles = [ 'super_admin', 'administrator', 'editor', 'author', 'contributor', 'subscriber' ];
		foreach( $old_roles as $index => $old_role )
		{
			if ( $old_role != $role )
				continue;
			// The new roles are the rest of the array.
			return array_slice( $old_roles, $index );
		}
		// Didn't find anything? Return the same role, but in an array.
		return [ $role ];
	}

	/**
		@brief		Creates the ID column in the broadcast data table.
		@since		2014-04-20 20:19:45
	**/
	public function create_broadcast_data_id_column()
	{
		$query = sprintf( "ALTER TABLE `%s` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'ID of row' FIRST;",
			$this->broadcast_data_table()
		);
		$this->query( $query );
	}

	/**
		@brief		Enqueue the JS file.
		@since		20131007
	**/
	public function enqueue_js()
	{
		if ( isset( $this->_js_enqueued ) )
			return;
		wp_enqueue_script( 'threewp_broadcast', $this->paths[ 'url' ] . '/js/js.js', '', $this->plugin_version );
		$this->_js_enqueued = true;
	}

	/**
		@brief		Find shortcodes in a string.
		@details	Runs a preg_match_all on a string looking for specific shortcodes.
					Overrides Wordpress' get_shortcode_regex without own shortcode(s).
		@since		2014-02-26 22:05:09
	**/
	public function find_shortcodes( $string, $shortcodes )
	{
		// Make the shortcodes an array
		if ( ! is_array( $shortcodes ) )
			$shortcodes = [ $shortcodes ];

		// We use Wordpress' own function to find shortcodes.

		global $shortcode_tags;
		// Save the old global
		$old_shortcode_tags = $shortcode_tags;
		// Replace the shortcode tags with just our own.
		$shortcode_tags = array_flip( $shortcodes );
		$rx = get_shortcode_regex();
		$shortcode_tags = $old_shortcode_tags;

		// Run the preg_match_all
		$matches = '';
		preg_match_all( '/' . $rx . '/', $string, $matches );

		return $matches;
	}

	/**
		@brief		Return a collection of add-on pack info.
		@since		2016-12-05 14:50:20
	**/
	public function get_addon_packs_info()
	{
		$r = $this->collection();

		$pack = $r->collection( '3rdparty' );
		$pack->set( 'name', '3rd party' );
		$pack->set( 'version_define', 'BROADCAST_3RD_PARTY_PACK_VERSION' );

		$pack = $r->collection( 'control' );
		$pack->set( 'name', 'Control' );
		$pack->set( 'version_define', 'BROADCAST_CONTROL_PACK_VERSION' );

		$pack = $r->collection( 'efficiency' );
		$pack->set( 'name', 'Efficiency' );
		$pack->set( 'version_define', 'BROADCAST_EFFICIENCY_PACK_VERSION' );

		$pack = $r->collection( 'premium' );
		$pack->set( 'name', 'Premium' );
		$pack->set( 'version_define', 'BROADCAST_PREMIUM_PACK_VERSION' );

		$pack = $r->collection( 'utilities' );
		$pack->set( 'name', 'Utilities' );
		$pack->set( 'version_define', 'BROADCAST_UTILITIES_PACK_VERSION' );

		return $r;
	}

	/**
		@brief		Return an array of post types available on this blog.
		@details	Excludes the nav menu item post type.
		@since		2014-11-16 23:10:09
	**/
	public function get_blog_post_types()
	{
		$r = get_post_types();
		unset( $r[ 'nav_menu_item' ] );
		$r = array_keys( $r );
		return $r;
	}


	/**
		@brief		Modify the debug class name, if necessary.
		@since		2017-10-28 18:11:53
	**/
	public function get_debug_class_name( $class_name )
	{
		$count = count( $this->broadcasting );
		if ( $count < 2 )
			return $class_name;
		return $class_name . ' (' . $count . ')';
	}

	/**
		@brief		Return an array of all callbacks of a hook.
		@since		2014-04-30 00:11:30
	**/
	public function get_hooks( $hook )
	{
		global $wp_filter;
		$filters = $wp_filter[ $hook ];
		if ( is_object( $filters ) )
			$filters = $filters->callbacks;
		ksort( $filters );
		$hook_callbacks = [];
		//$wp_filter[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
		foreach( $filters as $priority => $callbacks )
		{
			foreach( $callbacks as $callback )
			{
				$function = $callback[ 'function' ];
				if ( is_string( $function ) )
					$function_name = $function;
				if ( is_array( $function ) )
				{
					$function_name = $function[ 0 ];
					if ( is_object( $function_name ) )
						$function_name = sprintf( '%s::%s', get_class( $function_name ), $function[ 1 ] );
					else
						$function_name = sprintf( '%s::%s', $function_name, $function[ 1 ] );
				}
				if ( is_a( $function, 'Closure' ) )
					$function_name = '[Anonymous function]';
				$hook_callbacks[] = $function_name;
			}
		}
		return $hook_callbacks;
	}

	/**
		@brief		Return a table containing the info of each plugin.
		@since		2016-07-19 13:46:46
	**/
	public function get_plugin_info_array( $plugins )
	{
		$r = [];
		if ( function_exists( 'get_plugin_data' ) )
			foreach( $plugins as $plugin_filename )
			{
				$s = [];

				$plugin_filepath = WP_PLUGIN_DIR . '/' . $plugin_filename;
				if ( !file_exists($plugin_filepath) )
					continue;
				$plugin_data = get_plugin_data( $plugin_filepath );

				$plugin_data = (object)$plugin_data;
				$s []= $plugin_filename;
				$s []= $plugin_data->Name;
				$s []= $plugin_data->Version;
				$s = implode( ', ', $s );
				$r []= $s;
			}
		return $r;
	}

	/**
		@brief		Return a table object containing the system info.
		@since		2016-05-04 21:06:33
	**/
	public function get_system_info_table()
	{
		$table = $this->table();
		// Caption for the blog / PHP information table
		$table->caption()->text_( 'Information' );

		$row = $table->head()->row();
		$row->th()->text_( 'Key' );
		$row->th()->text_( 'Value' );

		if ( $this->debugging() )
		{
			$row = $table->body()->row();
			$row->td()->text_( 'Debugging' );
			$row->td()->text_( 'Enabled' );
		}

		$row = $table->body()->row();
		$row->td()->text_( 'Broadcast version' );
		$row->td()->text( $this->plugin_version );

		global $wp_version;
		$row = $table->body()->row();
		$row->td()->text_( 'Wordpress version' );
		$row->td()->text( $wp_version );

		$row = $table->body()->row();
		$row->td()->text_( 'PHP version' );
		$row->td()->text( phpversion() );

		$row = $table->body()->row();
		$row->td()->text_( 'Wordpress upload directory array' );
		$row->td()->text( '<pre>' . var_export( wp_upload_dir(), true ) . '</pre>' );

		$this->paths[ 'ABSPATH' ] = ABSPATH;
		$this->paths[ 'WP_PLUGIN_DIR' ] = WP_PLUGIN_DIR;
		$row = $table->body()->row();
		$row->td()->text_( 'Plugin paths' );
		$row->td()->text( '<pre>' . var_export( $this->paths(), true ) . '</pre>' );

		$row = $table->body()->row();
		$row->td()->text_( 'PHP maximum execution time' );
		$count = ini_get ( 'max_execution_time' );
		$text = $this->p_( _n( '%d second', '%d seconds', $count, 'threewp_broadcast' ), $count );
		$row->td()->text( $text );

		$row = $table->body()->row();
		$row->td()->text_( 'PHP memory limit' );
		$text = ini_get( 'memory_limit' );
		$row->td()->text( $text );

		$row = $table->body()->row();
		$row->td()->text_( 'Wordpress memory limit' );
		$text = wpautop( sprintf( WP_MEMORY_LIMIT . "

%s

<code>define('WP_MEMORY_LIMIT', '512M');</code>
",		$this->_( 'This can be increased by adding the following to your wp-config.php:' ) ) );
		$row->td()->text( $text );

		$row = $table->body()->row();
		$row->td()->text_( 'Debug code' );
		$text = WP_MEMORY_LIMIT;
		$text = wpautop( sprintf( "%s

<code>ini_set('display_errors','On');</code>
<code>define('WP_DEBUG', true);</code>
",		$this->p( __( 'Add the following lines to your wp-config.php to help find out why errors or blank screens are occurring:' ) ), 'threewp_broadcast' ) );
		$row->td()->text( $text );

		$row = $table->body()->row();
		$row->td()->text_( 'Hooked into save_post' );
		$hooks = $this->get_hooks( 'save_post' );
		$row->td()->text( implode( "<br>\n", $hooks ) );

		$row = $table->body()->row();
		$row->td()->text_( 'Plugins active on blog' );
		$plugins = $this->get_plugin_info_array( get_option( 'active_plugins' ) );
		$row->td()->text( implode( "<br>\n", $plugins ) );

		$row = $table->body()->row();
		$row->td()->text_( 'Plugins active on network' );
		$plugins = get_site_option( 'active_sitewide_plugins' );
		$plugins = $this->get_plugin_info_array( array_keys( $plugins ) );
		$row->td()->text( implode( "<br>\n", $plugins ) );

		foreach( $this->site_options() as $key => $value )
		{
			$row = $table->body()->row();
			$row->td()->text_( 'Broadcast option %s', $key );
			$value = $this->get_site_option( $key );
			$row->td()->text( json_encode( $value ) );
		}

		return $table;
	}

	/**
		@brief		Insert hook into save post action.
		@since		2015-02-10 20:38:22
	**/
	public function hook_save_post()
	{
		$priority = intval( $this->get_site_option( 'save_post_priority' ) );
		$decoys = intval( $this->get_site_option( 'save_post_decoys' ) );
		// See nop() for why this even exists.
		for ( $counter = 0; $counter < $decoys; $counter++ )
			$this->add_action( 'save_post', 'nop', $priority - 1 - $counter );
		$this->add_action( 'save_post', $priority );
	}

	/**
		@brief		Get some standardizing CSS styles.
		@return		string		A string containing the CSS <style> data, including the tags.
		@since		20131031
	**/
	public function html_css()
	{
		return file_get_contents( __DIR__ . '/../html/style.css' );
	}

	public function is_blog_user_writable( $user_id, $blog )
	{
		// Check that the user has write access.
		switch_to_blog( $blog->id );

		global $current_user;
		wp_get_current_user();
		$r = current_user_can( 'edit_posts' );

		restore_current_blog();

		return $r;
	}

	/**
		@brief		Converts a textarea of lines to a single line of space separated words.
		@param		string		$lines		Multiline string.
		@return		string					All of the lines on one line, minus the empty lines.
		@since		20131004
	**/
	public function lines_to_string( $lines )
	{
		$lines = explode( "\n", $lines );
		$r = [];
		foreach( $lines as $line )
			if ( trim( $line ) != '' )
				$r[] = trim( $line );
		return implode( ' ', $r );
	}

	/**
		@brief		Load the user's last used settings from the user meta table.
		@since		2014-10-09 06:27:32
	**/
	public function load_last_used_settings( $user_id )
	{
		$settings = get_user_meta( $user_id, 'broadcast_last_used_settings', true );
		if ( ! is_array( $settings ) )
			// Suggest some settings.
			$settings = [
				'custom_fields' => 'on',
				'link' => 'on',
				'taxonomies' => 'on',
			];
		return $settings;
	}

	/**
		@brief		Do nothing.
		@details	Used as a workaround for plugins that might remove_action in the save_post before us.
					This is a bug in how Wordpress handles filters and actions: https://core.trac.wordpress.org/ticket/17817
		@since		2015-08-26 21:09:28
	**/
	public function nop()
	{
	}

	/**
		@brief		Return the plugin pack instance.
		@since		2015-10-28 14:42:18
	**/
	public function plugin_pack()
	{
		if ( ! isset( $this->__plugin_pack ) )
		{
			$this->__plugin_pack = new premium_pack\ThreeWP_Broadcast_Plugin_Pack();
			if ( $this->__loaded )
				$this->__plugin_pack->plugins_ready = true;
		}
		return $this->__plugin_pack;
	}

	/**
		@brief		Forces changes to the post dates.
		@details	Accepts all four post date columns.
		@since		2017-02-07 14:57:41
	**/
	public function set_post_date( $post_data )
	{
		$sets = [];
		foreach( [ 'post_modified', 'post_modified_gmt', 'post_date', 'post_date_gmt' ] as $key )
			if ( isset( $post_data->$key ) )
				$sets[ $key ] = $post_data->$key;
		if ( count( $sets ) < 1 )
			return;
		global $wpdb;
		$wpdb->update( $wpdb->posts, $sets, [ 'ID' => $post_data->ID ] );
	}

	/**
		@brief		Save the user's last used settings.
		@details	Since v8 the data is stored in the user's meta.
		@since		2014-10-09 06:19:53
	**/
	public function save_last_used_settings( $user_id, $settings )
	{
		update_user_meta( $user_id, 'broadcast_last_used_settings', $settings );
	}

	public function site_options()
	{
		return array_merge( [
			'blogs_to_hide' => 5,								// How many blogs to auto-hide
			'blogs_hide_overview' => 5,							// Use a summary in the overview if more than this amount of children / siblings.
			'canonical_url' => true,							// Override the canonical URLs with the parent post's.
			'clear_post' => true,								// Clear the post before broadcasting.
			'custom_field_blacklist' => '',						// Internal custom fields that should not be broadcasted.
			'custom_field_protectlist' => '',					// Internal custom fields that should not be overwritten on broadcast
			'custom_field_whitelist' => '',						// Internal custom fields that should be broadcasted in spite of being blacklisted.
			'database_version' => 0,							// Version of database and settings
			'debug' => false,									// Display debug information?
			'debug_ips' => '',									// List of IP addresses that can see debug information, when debug is enabled.
			'debug_to_browser' => false,						// Display debug info in the browser?
			'debug_to_file' => false,							// Save debug info to a file.
			'save_post_decoys' => 1,							// How many nop() hooks to insert into the save_post action before Broadcast itself.
			'save_post_priority' => 640,						// Priority of save_post action. Higher = lets other plugins do their stuff first
			'override_child_permalinks' => false,				// Make the child's permalinks link back to the parent item?
			'post_types' => 'post page',						// Custom post types which use broadcasting
			'existing_attachments' => 'use',					// What to do with existing attachments: use, overwrite, randomize
			'role_broadcast' => [ 'super_admin' ],					// Role required to use broadcast function
			'role_link' => [ 'super_admin' ],						// Role required to use the link function
			'role_broadcast_as_draft' => [ 'super_admin' ],			// Role required to broadcast posts as templates
			'role_broadcast_scheduled_posts' => [ 'super_admin' ],	// Role required to broadcast scheduled, future posts
			'role_taxonomies' => [ 'super_admin' ],					// Role required to broadcast the taxonomies
			'role_custom_fields' => [ 'super_admin' ],				// Role required to broadcast the custom fields
			'savings_calculator_data' => '',						// Data for the savings calculator.
			/**
				@brief		List of taxonomy + term slugs to not broadcast.
				@since		2017-07-10 16:16:28
			**/
			'taxonomy_term_blacklist' => '',
			/**
				@brief		List of taxonomy + term slugs to be protected during broadcast.
				@since		2017-07-10 16:16:28
			**/
			'taxonomy_term_protectlist' => '',
		], parent::site_options() );
	}
}
