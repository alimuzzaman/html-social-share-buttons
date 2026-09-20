<?php
namespace Mediavine\Grow;

/**
 * Handles the css output needed to allow custom button colors
 */
class Custom_Color {
	/**
	 * Get CSS output of custom color styles for all passed locations.
	 *
	 * @param array $locations Array of location slugs to get custom color styles for
	 * @return string CSS output of custom color styles for all passed locations
	 */
	public static function get_multiple_locations( $locations = [] ) {
		$output = '';
		foreach ( $locations as $location ) {
			$output .= self::get_style( $location );
		}

		return $output;
	}

	/**
	 *
	 *
	 * @param string $location Slug of location to get custom color styles for.
	 * @return string CSS output of custom color styles for the passed location
	 */
	public static function get_style( $location = '' ) {
		if ( empty( $location ) ) {
			return '';
		}

		$location_settings = dpsp_get_location_settings( $location );
		if ( empty( $location_settings['active'] ) ) {
			return '';
		}
		$output = '';

		// Custom colors
		$color        = ! empty( $location_settings['display']['custom_color'] ) ? $location_settings['display']['custom_color'] : false;
		$hover_color  = ! empty( $location_settings['display']['custom_hover_color'] ) ? $location_settings['display']['custom_hover_color'] : false;
		$button_style = ! empty( $location_settings['button_style'] ) ? $location_settings['button_style'] : 1;

		// Have clases with normal line
		$location = str_replace( '_', '-', $location );

		// Handle sticky bar background
		if ( ! empty( $location_settings['display']['custom_background_color'] ) ) {
			$output .= self::sticky_bar( $location_settings );
		}

		$output .= self::style_variation( $button_style, $location, $color, $hover_color );

		if ( $hover_color ) {
			$output .= '.dpsp-networks-btns-wrapper.dpsp-networks-btns-' . $location . ' .dpsp-network-btn {--networkHover: ' . Color_Utilities::opacity( $hover_color, 0.4 ) . '; --networkAccent: ' . Color_Utilities::opacity( $hover_color, 1 ) . ';}';
		}

		return $output;
	}

	/**
	 * Generate styles for custom Sticky Bar Background.
	 *
	 * @param array $settings Sticky bar location settings
	 * @return string Styles for Sticky Bar custom background
	 */
	private static function sticky_bar( $settings ) {
		$bg_color   = $settings['display']['custom_background_color'];
		$text_color = Color_Utilities::get_readable_text( $bg_color );

		return '
				#dpsp-sticky-bar-wrapper { background: ' . $bg_color . '; }
				#dpsp-sticky-bar-wrapper .dpsp-total-share-wrapper { color: ' . $text_color . '; }
				#dpsp-sticky-bar-wrapper .dpsp-total-share-wrapper .dpsp-icon-total-share svg { fill: ' . $text_color . '; }
				';
	}

	/**
	 * Get Custom Color styles for a particular style variation and location.
	 *
	 * @param number $style_variation The style variation to get styles for
	 * @param string $location Slug for the location to get stlyes for
	 * @param string $color The color for the default state
	 * @param string $hover_color The color for when the button is hovered
	 * @return string Styles for the given style variation and location
	 */
	private static function style_variation( $style_variation, $location, $color, $hover_color ) {
		$variations = [
			1 => [
				'color'       => '
								.dpsp-button-style-1 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn .dpsp-network-icon,
								.dpsp-button-style-1 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn {
									--customNetworkColor: %2$s;
									--customNetworkHoverColor: %3$s;
									background: %2$s;
									border-color: %2$s;
								}
							',

				'hover_color' => '
								.dpsp-button-style-1 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover .dpsp-network-icon,
								.dpsp-button-style-1 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus .dpsp-network-icon,
								.dpsp-button-style-1 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover,
								.dpsp-button-style-1 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus {
									border-color: %3$s !important;
									background: %3$s !important;
								}
							',
			],

			2 => [
				'color'       => '
							.dpsp-button-style-2 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn, .dpsp-button-style-2 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn.dpsp-has-count {
								--customNetworkColor: %2$s;
								--customNetworkHoverColor: %3$s;
								background: %2$s;
								border-color: %2$s;
							}
							.dpsp-button-style-2 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn .dpsp-network-icon {
								background: %4$s;
								border-color: %4$s;
							}
						',

				'hover_color' => '
							.dpsp-button-style-2 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover,
							.dpsp-button-style-2 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus,
							.dpsp-button-style-2 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus .dpsp-network-icon,
							.dpsp-button-style-2 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover .dpsp-network-icon,
							.dpsp-button-style-2 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn.dpsp-has-count:focus,
							.dpsp-button-style-2 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn.dpsp-has-count:hover {

								background: %3$s;
								border-color: %3$s;
							}
						',
			],

			3 => [
				'color'       => '
							.dpsp-button-style-3 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:not(:hover):not(:active) {
								--customNetworkColor: %2$s;
								--customNetworkHoverColor: %3$s;
								border-color: %2$s;
								color: %2$s;
							}
							.dpsp-button-style-3 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn .dpsp-network-icon {
								background: %2$s;
								border-color: %2$s;
							}
						',

				'hover_color' => '
							.dpsp-button-style-3 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover .dpsp-network-icon,
							.dpsp-button-style-3 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus .dpsp-network-icon,
							.dpsp-button-style-3 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus,
							.dpsp-button-style-3 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover {
								border-color: %3$s !important;
								background: %3$s !important;
							}
						',
			],
			4 => [
				'color'       => '
							.dpsp-button-style-4 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:not(:active):not(:hover) {
								--customNetworkColor: %2$s;
								--customNetworkHoverColor: %3$s;
								background: %2$s;
								border-color: %2$s;
							}
							.dpsp-button-style-4 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn .dpsp-network-icon {
								border-color: %2$s;
							}
							.dpsp-button-style-4 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:not(:active):not(:hover)  .dpsp-network-icon  .dpsp-network-icon-inner > svg {
								fill: %2$s;
							}
						',

				'hover_color' => '
							.dpsp-button-style-4 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover .dpsp-network-icon,
							.dpsp-button-style-4 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus .dpsp-network-icon,
							.dpsp-button-style-4 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus,
							.dpsp-button-style-4 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover {
								border-color: %3$s !important;
								background: %3$s !important;
							}
						',
			],
			5 => [
				'color'       => '
							.dpsp-button-style-5 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn .dpsp-network-icon,
							.dpsp-button-style-5 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:not(:hover):not(:active) {
								--customNetworkColor: %2$s;
								--customNetworkHoverColor: %3$s;
								border-color: %2$s;
								color: %2$s;
							}
							.dpsp-button-style-5 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn  .dpsp-network-icon  .dpsp-network-icon-inner > svg {
								fill: %2$s;
							}
						',

				'hover_color' => '
							.dpsp-button-style-5 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover,
							.dpsp-button-style-5 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus {
								border-color: %3$s;
								background: %3$s;
							}
						',
			],
			6 => [
				'color'       => '
							.dpsp-button-style-6 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:not(:hover):not(:active) {
								--customNetworkColor: %2$s;
								--customNetworkHoverColor: %3$s;
								color: %2$s;
							}
							.dpsp-button-style-6 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn .dpsp-network-icon {
								border-color: %2$s;
								background: %2$s;
							}
						',

				'hover_color' => '
							.dpsp-button-style-6 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus,
							.dpsp-button-style-6 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover {
								color: %3$s;
							}
							.dpsp-button-style-6 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover .dpsp-network-icon,
							.dpsp-button-style-6 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus .dpsp-network-icon {
								border-color: %3$s;
								background: %3$s;
							}
						',
			],

			7 => [
				'color'       => '
							.dpsp-button-style-7 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:not(:hover):not(:active) {
								--customNetworkColor: %2$s;
								--customNetworkHoverColor: %3$s;
								color: %2$s;
								border-color: %2$s;
							}
							.dpsp-button-style-7 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:not(:active):not(:hover) .dpsp-network-icon {
								border-color: %2$s;
							}
							.dpsp-button-style-7 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:not(:active):not(:hover)  .dpsp-network-icon  .dpsp-network-icon-inner > svg {
								fill: %2$s;
							}
						',

				'hover_color' => '
							.dpsp-button-style-7 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover,
							.dpsp-button-style-7 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus {

								color: %3$s;
								border-color: %3$s;
							}
							.dpsp-button-style-7 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover .dpsp-network-icon,
							.dpsp-button-style-7 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus .dpsp-network-icon {
								border-color: %3$s;
								background: %3$s;
							}
						',
			],
			8 => [
				'color'       => '
							.dpsp-button-style-8 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:not(:hover):not(:active) {
								--customNetworkColor: %2$s;
								--customNetworkHoverColor: %3$s;
								color: %2$s;
							}
							.dpsp-button-style-8 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn  .dpsp-network-icon  .dpsp-network-icon-inner > svg {
								fill: %2$s;
							}
						',

				'hover_color' => '
							.dpsp-button-style-8 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover,
							.dpsp-button-style-8 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus {

								color: %3$s;
							}
							.dpsp-button-style-8 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus .dpsp-network-icon svg,
							.dpsp-button-style-8 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover  .dpsp-network-icon  .dpsp-network-icon-inner > svg {
								fill: %3$s;
							}
						',
			],
		];
		if ( 'sidebar' === $location ) {
			$variations[1] = [
				'color'       => '
					.dpsp-button-style-1 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn {
					--customNetworkColor: %2$s;
					--customNetworkHoverColor: %3$s;
					background: %2$s;
					border-color: %2$s;
					}',

				'hover_color' => '
					.dpsp-button-style-1 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:hover,
					.dpsp-button-style-1 .dpsp-networks-btns-%1$s.dpsp-networks-btns-wrapper .dpsp-network-btn:focus {
					border-color: %3$s !important;
					background: %3$s !important;
			}',
			];
		}

		$output       = '';
		$darker_color = Color_Utilities::darken( $color, 1.1 );
		if ( $color ) {
			$output .= sprintf( $variations[ $style_variation ]['color'], $location, $color, $hover_color, $darker_color );
		}
		if ( $hover_color ) {
			/** @phpstan-ignore-next-line */
			$output .= sprintf( $variations[ $style_variation ]['hover_color'], $location, $color, $hover_color, $darker_color );
		}
		return $output;
	}
}
