<?php
/**
 * Iconset Builder
 *
 * Generates CSS files from PNG icons in assets/iconset/ directories.
 *
 * @package HtmlSocialShare
 * @since 3.0.0
 */

namespace HtmlSocialShare\Build;

/**
 * Class IconsetBuilder
 *
 * Scans assets/iconset/ directories and generates CSS files for each iconset.
 */
class IconsetBuilder {
	/**
	 * Assets directory path
	 *
	 * @var string
	 */
	private $assetsDir;

	/**
	 * Build output directory path
	 *
	 * @var string
	 */
	private $buildDir;

	/**
	 * Supported social networks
	 *
	 * @var array
	 */
	private $supportedNetworks = array(
		'facebook',
		'twitter',
		'linkedin',
		'pinterest',
		'googlepluse',
		'mail',
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->assetsDir = defined( 'HTML_SOCIAL_SHARE_DIR' ) 
			? HTML_SOCIAL_SHARE_DIR . '/assets/iconset' 
			: dirname( dirname( dirname( __FILE__ ) ) ) . '/assets/iconset';
			
		$this->buildDir = defined( 'HTML_SOCIAL_SHARE_DIR' ) 
			? HTML_SOCIAL_SHARE_DIR . '/build/iconsets' 
			: dirname( dirname( dirname( __FILE__ ) ) ) . '/build/iconsets';
	}

	/**
	 * Build all iconsets
	 *
	 * Scans the assets directory and builds CSS for all found iconsets.
	 *
	 * @return array Array of results with iconset names as keys
	 */
	public function buildAll() {
		$results = array();

		if ( ! is_dir( $this->assetsDir ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'IconsetBuilder: Assets directory not found: ' . $this->assetsDir );
			}
			return $results;
		}

		$iconsets = $this->scanIconsets();

		foreach ( $iconsets as $iconsetData ) {
			$iconset = $iconsetData['iconset'];
			$type    = $iconsetData['type'];
			$key     = "{$iconset}_{$type}";

			$result = $this->buildIconset( $key );
			$results[ $key ] = $result;
		}

		return $results;
	}

	/**
	 * Build a specific iconset
	 *
	 * @param string $iconsetKey Iconset key in format "iconset_type"
	 * @return array Result with 'success' boolean and optional 'error' message
	 */
	public function buildIconset( $iconsetKey ) {
		// Parse iconset key
		$parts = $this->parseIconsetKey( $iconsetKey );
		if ( ! $parts ) {
			return array(
				'success' => false,
				'error'   => 'Invalid iconset key format',
			);
		}

		$iconset = $parts['iconset'];
		$type    = $parts['type'];

		// Check directory exists
		$iconsetDir = $this->assetsDir . '/' . $iconsetKey;
		if ( ! is_dir( $iconsetDir ) ) {
			return array(
				'success' => false,
				'error'   => 'Iconset directory not found: ' . $iconsetKey,
			);
		}

		// Scan for PNG files
		$networks = $this->scanNetworks( $iconsetDir );
		if ( empty( $networks ) ) {
			return array(
				'success' => false,
				'error'   => 'No PNG files found in ' . $iconsetKey,
			);
		}

		// Generate CSS
		$css = $this->generateCss( $iconsetKey, $iconset, $type, $networks );

		// Ensure build directory exists
		$outputDir = $this->buildDir . '/' . $iconsetKey;
		if ( ! is_dir( $outputDir ) ) {
			if ( ! wp_mkdir_p( $outputDir ) ) {
				return array(
					'success' => false,
					'error'   => 'Failed to create output directory: ' . $outputDir,
				);
			}
		}

		// Write CSS file
		$outputFile = $outputDir . '/style.css';
		$written = $this->writeFile( $outputFile, $css );

		if ( ! $written ) {
			return array(
				'success' => false,
				'error'   => 'Failed to write CSS file: ' . $outputFile,
			);
		}

		return array(
			'success'  => true,
			'networks' => count( $networks ),
			'file'     => $outputFile,
		);
	}

	/**
	 * Scan assets directory for iconsets
	 *
	 * @return array Array of iconset data with iconset and type
	 */
	private function scanIconsets() {
		$iconsets = array();

		if ( ! is_dir( $this->assetsDir ) ) {
			return $iconsets;
		}

		$dirs = scandir( $this->assetsDir );
		if ( ! $dirs ) {
			return $iconsets;
		}

		foreach ( $dirs as $dir ) {
			if ( $dir === '.' || $dir === '..' ) {
				continue;
			}

			$path = $this->assetsDir . '/' . $dir;
			if ( ! is_dir( $path ) ) {
				continue;
			}

			$parts = $this->parseIconsetKey( $dir );
			if ( $parts ) {
				$iconsets[] = array(
					'key'     => $dir,
					'iconset' => $parts['iconset'],
					'type'    => $parts['type'],
				);
			}
		}

		return $iconsets;
	}

	/**
	 * Parse iconset key into components
	 *
	 * @param string $key Iconset key in format "iconset_type"
	 * @return array|null Array with iconset and type, or null if invalid
	 */
	private function parseIconsetKey( $key ) {
		// Expected format: {iconset}_{type}
		// Examples: default_square, flat_circle, long_shadow_square

		$parts = explode( '_', $key );
		if ( count( $parts ) < 2 ) {
			return null;
		}

		// Last part is type
		$type = array_pop( $parts );
		
		// Rest is iconset name
		$iconset = implode( '_', $parts );

		// Validate type
		if ( ! in_array( $type, array( 'square', 'circle' ), true ) ) {
			return null;
		}

		return array(
			'iconset' => $iconset,
			'type'    => $type,
		);
	}

	/**
	 * Scan iconset directory for PNG files
	 *
	 * @param string $dir Directory path
	 * @return array Array of network names found
	 */
	private function scanNetworks( $dir ) {
		$networks = array();

		if ( ! is_dir( $dir ) ) {
			return $networks;
		}

		foreach ( $this->supportedNetworks as $network ) {
			$file = $dir . '/' . $network . '.png';
			if ( file_exists( $file ) ) {
				$networks[] = $network;
			}
		}

		return $networks;
	}

	/**
	 * Generate CSS content for an iconset
	 *
	 * @param string $iconsetKey Full iconset key (e.g., "default_square")
	 * @param string $iconset Iconset name (e.g., "default")
	 * @param string $type Type (square or circle)
	 * @param array  $networks Available network names
	 * @return string Generated CSS content
	 */
	private function generateCss( $iconsetKey, $iconset, $type, $networks ) {
		$css = "/**\n";
		$css .= " * Iconset: {$iconsetKey}\n";
		$css .= " * Generated by IconsetBuilder\n";
		$css .= " * Networks: " . implode( ', ', $networks ) . "\n";
		$css .= " */\n\n";

		// Base wrapper styles
		$css .= ".zmshbt.{$iconset}.{$type} {\n";
		$css .= "\t/* Container */\n";
		$css .= "}\n\n";

		// Fixed positioning for left/right
		$css .= ".zmshbt.{$iconset}.{$type}.left,\n";
		$css .= ".zmshbt.{$iconset}.{$type}.right {\n";
		$css .= "\tposition: fixed;\n";
		$css .= "\ttop: 30%;\n";
		$css .= "\tz-index: 9999;\n";
		$css .= "\ttransition: all .25s linear;\n";
		$css .= "}\n\n";

		// Left placement
		$css .= ".zmshbt.{$iconset}.{$type}.left {\n";
		$css .= "\tleft: -25px;\n";
		$css .= "}\n\n";

		$css .= ".zmshbt.{$iconset}.{$type}.left:hover {\n";
		$css .= "\tleft: 0;\n";
		$css .= "}\n\n";

		// Right placement
		$css .= ".zmshbt.{$iconset}.{$type}.right {\n";
		$css .= "\tright: -25px;\n";
		$css .= "}\n\n";

		$css .= ".zmshbt.{$iconset}.{$type}.right:hover {\n";
		$css .= "\tright: 0;\n";
		$css .= "}\n\n";

		// Auto-hide functionality
		$css .= ".zmshbt.{$iconset}.{$type}.auto-hide.left,\n";
		$css .= ".zmshbt.{$iconset}.{$type}.auto-hide.right {\n";
		$css .= "\ttransition: all .25s linear .5s;\n";
		$css .= "}\n\n";

		// Icon link base styles
		$css .= ".zmshbt.{$iconset}.{$type} a {\n";
		$css .= "\twidth: 32px;\n";
		$css .= "\theight: 32px;\n";
		$css .= "\tdisplay: block;\n";
		$css .= "\tbackground-size: cover;\n";
		$css .= "\tbackground-position: center;\n";
		$css .= "\tbackground-repeat: no-repeat;\n";
		$css .= "\tmargin: 10px;\n";
		$css .= "\ttransition: all .25s linear;\n";
		$css .= "}\n\n";

		// Inline display for widget and shortcode
		$css .= ".zmshbt.{$iconset}.{$type}.in_widget a,\n";
		$css .= ".zmshbt.{$iconset}.{$type}.in_shortcode a {\n";
		$css .= "\tdisplay: inline-block;\n";
		$css .= "\tmargin: 5px;\n";
		$css .= "}\n\n";

		// Network-specific background images
		foreach ( $networks as $network ) {
			$css .= ".zmshbt.{$iconset}.{$type} a.{$network} {\n";
			$css .= "\tbackground-image: url('../../../assets/iconset/{$iconsetKey}/{$network}.png');\n";
			$css .= "}\n\n";
		}

		// Hover effects
		$css .= ".zmshbt.{$iconset}.{$type} a:hover,\n";
		$css .= ".zmshbt.{$iconset}.{$type} a:active {\n";
		$css .= "\ttransform: scale(1.2);\n";
		$css .= "}\n";

		return $css;
	}

	/**
	 * Write content to file
	 *
	 * @param string $path File path
	 * @param string $content Content to write
	 * @return bool True on success, false on failure
	 */
	private function writeFile( $path, $content ) {
		$result = file_put_contents( $path, $content );

		if ( $result === false ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'IconsetBuilder: Failed to write file: ' . $path );
			}
			return false;
		}

		return true;
	}

	/**
	 * Get assets directory path
	 *
	 * @return string
	 */
	public function getAssetsDir() {
		return $this->assetsDir;
	}

	/**
	 * Get build directory path
	 *
	 * @return string
	 */
	public function getBuildDir() {
		return $this->buildDir;
	}
}
