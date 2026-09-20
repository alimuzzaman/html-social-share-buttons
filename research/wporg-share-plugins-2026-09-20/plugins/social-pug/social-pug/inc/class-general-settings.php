<?php
use Mediavine\Grow\Settings;
use Mediavine\Grow\Has_Settings_API;

/**
 * Class General_Settings Representation of the General Settings for the Plugin
 */
class General_Settings implements Has_Settings_API {
	private $api_slug = 'general';

	private $settings_slug = 'dpsp_settings';

	public function update_settings( $settings ) {
		return update_option( $this->settings_slug, $settings );
	}

	public function get_settings() {
		return ! empty( $this->settings_slug ) ? Settings::get_setting( $this->settings_slug, [] ) : [];
	}

	public function get_api_slug() {
		return $this->api_slug;
	}
}
