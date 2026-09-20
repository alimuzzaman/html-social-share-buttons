<?php
namespace Mediavine\Grow;

/**
 * Class Critical_Styles
 * This class helps manage creating the critical css styles to be inserted inline in tools
 * @package Mediavine\Grow
 */
class Critical_Styles {

	/** @var null|Critical_Styles */
	private static $instance = null;

	/**
	 * Get the instance of the class
	 *
	 * @return Critical_Styles|null
	 */
	public static function get_instance() : Critical_Styles {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Init function in case it is needed in the future.
	 */
	public function init() : void {
	}

	/**
	 * Get Style attribute at a location
	 *
	 * @param string $slug Slug identifier for the element to get inline css for
	 * @param string $location Location that this is being called from
	 * @return string
	 */
	public static function get( string $slug, string $location = '' ) : string {
		$styles = apply_filters( 'mv_grow_critical_styles_' . $location, [], $slug );
		if ( ! is_array( $styles ) ) {
			return '';
		}
		$styles = esc_attr( join( ';', $styles ) );
		return empty( $styles ) ? '' : 'style="' . $styles . '"';
	}

	/**
	 * Set the instance of the class
	 *
	 * @since 2.16.0
	 * @param Critical_Styles|null $instance Current Instance of the Class
	 */
	public static function set_instance( ? Critical_Styles $instance ) : void {
		self::$instance = $instance;
	}

	/**
	 * @param mixed $styles Existing Allowed Properties
	 *
	 * @return mixed
	 */
	public static function allowed_properties( $styles ) {
		if ( ! is_array( $styles ) ) {
			return $styles;
		}
		$styles[] = 'position';
		$styles[] = 'float';
		$styles[] = 'right';
		$styles[] = 'left';
		return $styles;
	}


}
