<?php
if(  !class_exists('ns_cloner_check') ):	
class ns_cloner_check {
	
	var $title;
	
	function __construct( $title ){
		$this->title = $title;
		if( is_network_admin() ){
			add_action( 'admin_enqueue_scripts', array($this,'register_warn') );
			add_action( 'network_admin_notices', array($this,'do_warn') );
		}
	}
	
	function register_warn(){
		if( !class_exists('ns_cloner') || !class_exists('ns_cloner_addon') ){
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
		}
	}
	
	function do_warn(){
		if( !class_exists('ns_cloner') || !class_exists('ns_cloner_addon') ){
			// if user already has pre v3 cloner installed, tell them to upgrade
			if( class_exists('ns_cloner_free') ){
				$message = sprintf(
					__( 'Thanks for installing the %s add-on! Now you just need to <a href="%s" class="thickbox">update your free NS Cloner core plugin</a> to the latest version (3.0.0 or higher) in order for the new add-on to work.', 'ns-cloner'),
					$this->title,
					admin_url('/network/plugin-install.php?tab=plugin-information&plugin=ns-cloner-site-copier&section=changelog&TB_iframe=true&width=830&height=919')
				);
			}
			// if user doesn't have any cloner installed, tell them to install
			else{
				$message = sprintf(
					__( 'Thanks for installing the %s add-on! Please be aware that it will not work until you also install/activate the latest free version (3.0.0 or higher) of the <a href="%s" class="thickbox">NS Cloner core plugin</a>.', 'ns-cloner'),
					$this->title,
					admin_url('/network/plugin-install.php?tab=plugin-information&plugin=ns-cloner-site-copier&TB_iframe=true&width=600&height=550')
				);
			}
			echo "<div class='update-nag'>$message</div>\n";
		}
	}
}
endif;