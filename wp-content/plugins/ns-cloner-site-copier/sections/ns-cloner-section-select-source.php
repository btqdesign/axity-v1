<?php

class ns_cloner_section_select_source extends ns_cloner_section {
	
	public $modes_supported = array('core');
	public $id = 'select_source';
	public $ui_priority = 100;
	
	function render(){
		$this->open_section_box( $this->id, __("Select Source","ns-cloner") );
		?>
		<label class="ns-cloner-site-search-label">Search by url</label>
		<input type="text" class="ns-cloner-site-search" />
		<label class="ns-cloner-site-select-label">Or select</label>
		<select name="source_id" class="ns-cloner-site-select">
			<?php
				$sites = array();
			 	// update for WP 4.6+ and deprecated wp_get_sites() use get_sites() instead
				if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
					$sites = get_sites(array('number'=>9999));
				} else {
				// handle WP 4.5 and earlier
					$sites = function_exists('wp_get_sites')? wp_get_sites(array('limit'=>9999)) : get_blog_list(0,'all');
				}				
			?>
			<?php 
				foreach( $sites as $site ):
					// update for WP 4.6+ and deprecated wp_get_sites() use get_sites() instead
					if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
						$blog_id = $site->blog_id;
						$blog_domain = $site->domain;
						$blog_path = $site->path;
					} else {
					// handle WP 4.5 and earlier
						$blog_id = $site['blog_id'];
						$blog_domain = $site['domain'];
						$blog_path = $site['path'];
					} 
			?>
			<option value="<?php echo $blog_id; ?>" <?php echo ( $blog_id == get_site_option('ns_cloner_default_template') ? 'selected' : '' ) ?> >
				<?php $title = get_blog_details($blog_id)->blogname; ?>
				<?php $url = is_subdomain_install()? "$blog_domain" : "$blog_domain$blog_path"; ?>
				<?php echo "$blog_id - ".substr($title,0,30)." ($url)"; ?>
		  <?php endforeach; ?>
		</select>
		<label class="ns-cloner-site-default-label">Save as default</label>
		<input class="ns-cloner-site-default" type="checkbox" name="save_default_template" <?php echo ( get_site_option('ns_cloner_default_template') ? 'checked' : '' ) ?> />
		<p class="description ns-cloner-clear"><?php _e( 'Pick an existing source site to clone. If you haven\'t already, now is a great time to set up a "template" site exactly the way you want the new clone site to start out (theme, plugins, settings, etc.).','ns-cloner' ); ?></p>
		<?php
		$this->close_section_box();
	}
	
}
