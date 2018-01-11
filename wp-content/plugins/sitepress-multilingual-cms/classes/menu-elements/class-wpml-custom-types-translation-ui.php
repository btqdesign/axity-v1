<?php

class WPML_Custom_Types_Translation_UI {

	/** @var array */
	private $translation_option_class_names;

	/** @var WPML_Translation_Modes $translation_modes */
	private $translation_modes;

	public function __construct( WPML_Translation_Modes $translation_modes ) {
		$this->translation_modes              = $translation_modes;
		$this->translation_option_class_names = array(
			WPML_CONTENT_TYPE_TRANSLATE                => 'translate',
			WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED => 'display-as-translated',
			WPML_CONTENT_TYPE_DONT_TRANSLATE           => 'dont-translate'
		);
	}

	public function render_custom_types_header_ui( $type_label ) {
		?>
		<header class="wpml-flex-table-header wpml-flex-table-sticky">
			<div class="wpml-flex-table-row">
				<div class="wpml-flex-table-cell name">
					<?php echo $type_label ?>
				</div>
				<?php foreach ( $this->translation_modes->get_options() as $value => $label ) { ?>
					<div
						class="wpml-flex-table-cell text-center <?php echo $this->translation_option_class_names[ $value ]; ?>">
						<?php echo esc_html( $label ) ?>
					</div>
				<?php } ?>
			</div>
		</header>
		<?php
	}

	public function render_row( $content_label, $name, $content_slug, $disabled, $current_translation_mode, $unlocked ) {
		$radio_name    = esc_attr( $name . '[' . $content_slug . ']' );
		$unlocked_name = esc_attr( $name . '_unlocked[' . $content_slug . ']' );
		?>
		<div class="wpml-flex-table-cell name">
			<?php if ( $disabled && ! $unlocked ) { ?>
				<button type="button"
				        class="button-secondary wpml-button-lock js-wpml-sync-lock"
				        title="<?php esc_html_e( 'This setting is controlled by a wpml-config.xml file. Click here to unlock and override this setting.', 'sitepress' ); ?>"
				        data-radio-name="<?php echo $radio_name; ?>"
				        data-unlocked-name="<?php echo $unlocked_name; ?>">
					<i class="otgs-ico-lock"></i>
				</button>
			<?php } ?>

			<input type="hidden" name="<?php echo $unlocked_name; ?>" value="<?php echo $unlocked ? '1' : '0'; ?>">

			<?php echo $content_label; ?>
			(<i><?php echo esc_html( $content_slug ); ?></i>)
		</div>
		<?php foreach ( $this->translation_modes->get_options() as $value => $label ) { ?>
			<div
				class="wpml-flex-table-cell text-center <?php echo $this->translation_option_class_names[ $value ]; ?>"
				data-header="<?php esc_attr( $label ) ?>">
				<input type="radio" name="<?php echo $radio_name; ?>"
				       value="<?php echo esc_attr( $value ); ?>" <?php echo $unlocked ? '' : $disabled; ?>
					<?php checked( $value, $current_translation_mode ) ?> />
			</div>
			<?php
		}
	}

}
