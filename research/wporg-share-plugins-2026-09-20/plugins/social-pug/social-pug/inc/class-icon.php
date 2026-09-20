<?php
namespace Mediavine\Grow;

/**
 * Represents a single Icon
 */
class Icon {

	/** @var int Width of icon that represents pixel width and SVG coordinate width, used for width and viewBox attributes  */
	private $width;

	/** @var int Height of icon that represents pixel height and SVG coordinate height, used for height and viewBox attributes  */
	private $height;

	/** @var string[] Array of SVG path data strings */
	private $paths;

	/** @var int[] Array of integers for SVG offset for viewBox property */
	private $svg_offset;

	/** @var string Identifier string for this icon */
	private $slug;

	/**
	 * Icon constructor.
	 *
	 * @param string $slug Identifier for this icon
	 * @param array  $args Icon Data to regsiter with
	 */
	public function __construct( string $slug = '', array $args = [] ) {
		$this->slug       = $slug;
		$this->width      = $args['width'];
		$this->height     = $args['height'];
		$this->paths      = is_array( $args['paths'] ) ? $args['paths'] : [ $args['paths'] ];
		$this->svg_offset = isset( $args['svg_offset'] ) ? $args['svg_offset'] : [];
	}

	/**
	 * Get the data for this Icon.
	 *
	 * @return array
	 */
	public function get_data() {
		return [
			'height' => $this->height,
			'width'  => $this->width,
			'paths'  => $this->paths,
		];
	}

	/**
	 * Get the slug for this icon.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Turn the data for this icon into an string representing the SVG Element with path inside.
	 *
	 * @return string
	 */
	public function compose_svg() {
		$svg_offset_x = isset( $this->svg_offset['x'] ) ? $this->svg_offset['x'] : 0;
		$svg_offset_y = isset( $this->svg_offset['y'] ) ? $this->svg_offset['y'] : 0;
		$output       = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="' . $svg_offset_x . ' ' . $svg_offset_y . ' ' . absint( $this->width ) . ' ' . absint( $this->height ) . '">';
		foreach ( $this->paths as $path ) {
			$output .= '<path d="' . $path . '"></path>';
		}
		$output .= '</svg>';

		return $output;
	}
}
