<?php

/**
 * Settings class to manage settings that are not included in a separate class
 *
 * @since      1.21
 * @author     WP BackItUp <wpbackitup@wpbackitup.com>
 *
 */
class WPBackitup_Settings {

	/**
	 * Get UUID for this site.
	 *
	 * @return mixed|string
	 */
	public static function get_site_id() {
		$site_id = WPBackItUp_Utility::get_option('site_id',false);
		if (false===$site_id) {
			$site_id = WPBackItUp_Utility::generate_uuid4();
			WPBackItUp_Utility::set_option('site_id',$site_id);
		}

		return $site_id;
	}
}