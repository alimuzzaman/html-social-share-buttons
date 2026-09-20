<?php
namespace Mediavine\Grow;

class View_Loader {

	/** @var string  */
	public static $plugin_path = DPSP_PLUGIN_DIR;

	/**
	 * Returns the output of the passed view.
	 *
	 * @param string $view_file Relative path to view file from plugin root
	 * @param array $args Array that will be passed to the included view
	 * @return false|string Output from the view
	 */
	public static function get_view( $view_file, $args = [] ) {
		$view_path = self::$plugin_path . $view_file;
		ob_start();

		try {
			include( $view_path );
		} catch ( \Exception $exception ) {
			// @TODO: More robust Exception handling here
			error_log( 'MV Grow: View File not Found ' . $view_path ); // @codingStandardsIgnoreLine — Logging allowed if on purpose
			ob_get_clean();
			return '';
		}

		return ob_get_clean();
	}

	/**
	 * Custom version of wp_kses to allow SVG tags.
	 *
	 * @return array
	 */
	public static function get_allowed_tags() {
		$kses_defaults = wp_kses_allowed_html( 'post' );
		$svg_kses      = [
			'svg'     => [
				'class'               => true,
				'aria-hidden'         => true,
				'preserveaspectratio' => true,
				'aria-labelledby'     => true,
				'version'             => true,
				'xmlns'               => true,
				'width'               => true,
				'height'              => true,
				'viewbox'             => true, // <= Must be lower case!
				'fill'                => true,
			],
			'g'       => [ 'fill' => true ],
			'title'   => [ 'title' => true ],
			'path'    => [
				'd'     => true,
				'fill'  => true,
				'class' => true,
			],
			'rect'    => [
				'x'      => true,
				'y'      => true,
				'height' => true,
				'width'  => true,
				'class'  => true,
				'rx'     => true,
				'ry'     => true,
				'fill'   => true,
			],
			'ellipse' => [
				'x'      => true,
				'y'      => true,
				'height' => true,
				'width'  => true,
				'class'  => true,
				'fill'   => true,
			],
		];

		// Add form tag for Save This block
		if ( ! \Social_Pug::is_free() ) {
			$form_kses      = [
				'form'     => [
					'name' 				=> true,
					'class'         	=> true,
					'aria-hidden'   	=> true,
					'aria-labelledby'   => true,
					'method' 			=> true,
					'action'			=> true,
				],
				'input'    => [
					'type' 				=> true,
					'class'				=> true,
					'name' 				=> true,
					'id'	 			=> true,
					'value' 			=> true,
					'placeholder' 		=> true,
					'style' 			=> true,
					'size'				=> true,
				],
			];

			$kses_defaults = array_merge( $kses_defaults, $form_kses);
		}

		return array_merge( $kses_defaults, $svg_kses );
	}
}
