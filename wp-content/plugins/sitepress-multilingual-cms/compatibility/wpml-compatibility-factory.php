<?php

class WPML_Compatibility_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;

		$hooks = array();

		$hooks['gutenberg'] = new WPML_Compatibility_Gutenberg( new WPML_WP_API() );

		if ( defined( 'FUSION_BUILDER_VERSION' ) ) {
			$hooks['fusion-global-element'] = new WPML_Compatibility_Plugin_Fusion_Global_Element_Hooks(
				$sitepress,
				new WPML_Translation_Element_Factory( $sitepress )
			);
		}

		return $hooks;
	}
}
