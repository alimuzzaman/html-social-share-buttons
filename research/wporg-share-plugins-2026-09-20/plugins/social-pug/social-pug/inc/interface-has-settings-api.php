<?php
namespace Mediavine\Grow;

/**
 * Interface Has_Settings
 * Classes that use this interface
 */
interface Has_Settings_API {

	/**
	 * @return array get the values from the database
	 */
	public function get_settings();

	/**
	 * Get the settings
	 *
	 * @param array $settings Settings to update;
	 * @return boolean
	 */
	public function update_settings( $settings );

	/**
	 * Get the slug for the api route
	 *
	 * @return string
	 */
	public function get_api_slug();
}
