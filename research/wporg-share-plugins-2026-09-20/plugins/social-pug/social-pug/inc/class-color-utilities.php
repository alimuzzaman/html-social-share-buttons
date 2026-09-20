<?php
namespace Mediavine\Grow;

class Color_Utilities extends \Social_Pug {

	/** @var null  */
	private static $instance = null;

	/**
	 *
	 *
	 * @return Color_Utilities|\Social_Pug|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Init function in case it is needed in the future.
	 */
	public function init() {
	}

	/**
	 *
	 *
	 * @param $hex string Hexadecimal value to convert to rgb component array
	 * @return array rgb component array
	 */
	public static function hex_to_rgb( $hex ) {
		$hex = ltrim( $hex, '#' );
		if ( strlen( $hex ) === 3 ) {
			$hex_component = str_split( $hex );
		} else {
			$hex_component = [
				$hex[0] . $hex[1],
				$hex[2] . $hex[3],
				$hex[4] . $hex[5],
			];
		}
		$rgb_component = [];
		foreach ( $hex_component as $component ) {
			$rgb_component[] = hexdec( $component );
		}

		return $rgb_component;
	}

	/**
	 * Convert an RGB component array into a hexadecimal string ready for CSS.
	 *
	 * @param array $color Array of decimal values representing an RGB color
	 * @return string Representation of a color as a hexadecimal string ready to be used in css strings
	 */
	public static function rgb_to_hex( $color ) {
		// Join converted components together to create color string
		return '#' . implode(
			'', array_map(
				function ( $component ) {
					// Convert Decimal value to a hex string then make sure it has two characters
					return str_pad( strval( dechex( $component ) ), 2, '0', STR_PAD_LEFT );
				}, $color
			)
		);
	}

	/**
	 * Determine if a given color is light.
	 *
	 * @link https://www.w3.org/TR/AERT/#color-contrast for reference on the luminance constants for RGB values
	 *
	 * @param $color array|string Color to check against, it will be converted to component array format if it is not already in that format
	 * @return bool
	 */
	public static function is_light( $color ) {
		$color = self::convert_color( $color );
		// Apply constants for perceived brightness against RGB values for sRGB color space, add together, then reduce to a value between 0 and 1 to compare against.
		$luminance = ( 0.299 * $color[0] + 0.587 * $color[1] + 0.114 * $color[2] ) / 255;

		return $luminance > 0.5;
	}

	/**
	 * Determine if a given color is considered dark.
	 *
	 * @param $color
	 * @return bool
	 */
	public static function is_dark( $color ) {
		return ! self::is_light( $color );
	}

	/**
	 * If a color passed in needs to be converted to a component array format, do so, otherwise
	 *    return the color passed in.
	 *
	 * @param $color array|string the color to be converted
	 * @return array|string The color passed in in the proper format
	 */
	public static function convert_color( $color ) {
		if ( is_array( $color ) ) {
			// Color is already in a format we want it
			return $color;
		}
		if ( is_string( $color ) && strpos( $color, '#' ) === 0 ) {
			// Color is hexadecimal string
			return self::hex_to_rgb( $color );
		};
		// @TODO Add detection for rgb css strings
		// If it's not a format recognized by the class, return white to fail somewhat gracefully
		return [ 255, 255, 255 ];
	}

	/**
	 * Get an appropriate color for text based on given background color.
	 *
	 * @param $bg_color string|array Background Color to get appropriate text color for
	 * @return string $text_color Hexadecimal string representing appropriately contrasting color for text given the background color
	 */
	public static function get_readable_text( $bg_color ) {
		$bg_color   = self::convert_color( $bg_color );
		$text_color = '#eee';
		if ( self::is_light( $bg_color ) ) {
			$text_color = '#333';
		}

		return $text_color;
	}

	/**
	 *
	 *
	 * @param string|array $color Hexadecimal string or RGB component array representing a color
	 * @param number $darker Amount to darken color by
	 * @return string Hexadecimal representation of darker color ready for css inclusion
	 */
	public static function darken( $color, $darker ) {
		$color  = self::convert_color( $color );
		$darker = ( $darker > 1 ) ? $darker : 1;

		$darkened = array_map(
			function ( $component ) use ( $darker ) {
			return floor( $component / $darker );
			}, $color
		);

		return self::rgb_to_hex( $darkened );
	}

	/**
	 *
	 *
	 * @param string|array $color Hexadecimal string or RGB component array representing a color
	 * @param number $opacity How opaque the color should be, range from 0 to 1
	 * @return string RGBA string ready for css inclusion
	 */
	public static function opacity( $color, $opacity ) {
		$color = self::convert_color( $color );
		return 'rgba(' . implode( ', ', $color ) . ', ' . $opacity . ')';
	}
}
