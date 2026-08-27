<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend;

/**
 * Collects the assets used by rendered button groups during one request.
 *
 * Rendering can happen from the content filter, a shortcode, or a builder
 * integration before the footer is reached.  Keeping this state in a small
 * request-scoped object lets the frontend controller emit each stylesheet and
 * background rule once without coupling the renderer to WordPress output.
 */
final class AssetCollector {
	private $stylesheets = array();
	private $printedIcons = array();
	private $fallbackStylesheet;
	private $version;
	private $buttonAppearanceStyleHandle;

	public function __construct( $fallbackStylesheet, $version = '3.0.0', $buttonAppearanceStyleHandle = 'hssb-button-appearance' ) {
		$this->fallbackStylesheet = (string) $fallbackStylesheet;
		$this->version = (string) $version;
		$this->buttonAppearanceStyleHandle = (string) $buttonAppearanceStyleHandle;
	}

	/**
	 * @param object $outcome A render outcome exposing stylesheets() and printedIcons().
	 */
	public function collect( $outcome ) {
		if ( ! is_object( $outcome ) ) {
			return;
		}

		if ( method_exists( $outcome, 'stylesheets' ) ) {
			foreach ( (array) $outcome->stylesheets() as $id => $stylesheet ) {
				$id = sanitize_key( (string) $id );
				if ( '' !== $id && is_scalar( $stylesheet ) ) {
					$this->stylesheets[ $id ] = (string) $stylesheet;
				}
			}
		}

		if ( method_exists( $outcome, 'printedIcons' ) ) {
			foreach ( (array) $outcome->printedIcons() as $key => $icon ) {
				if ( is_array( $icon ) ) {
					$this->printedIcons[ (string) $key ] = $icon;
				}
			}
		}
	}

	/**
	 * Create isolated collection state for a public object that historically
	 * owned its own render session. Normal controller rendering continues to
	 * use this request-scoped instance.
	 */
	public function fresh() {
		return new self( $this->fallbackStylesheet, $this->version, $this->buttonAppearanceStyleHandle );
	}

	public function enqueueStyles() {
		$stylesheets = $this->stylesheets;
		if ( empty( $stylesheets ) && '' !== $this->fallbackStylesheet ) {
			$stylesheets = array( 'default' => $this->fallbackStylesheet );
		}

		foreach ( $stylesheets as $id => $stylesheet ) {
			$handle = $this->buttonAppearanceStyleHandle === $id
				? $this->buttonAppearanceStyleHandle
				: 'social-share-' . sanitize_key( $id );
			wp_enqueue_style(
				$handle,
				(string) $stylesheet,
				array(),
				$this->version
			);
		}
	}

	public function inlineIconStyles( $autoHideEnabled ) {
		if ( empty( $this->printedIcons ) && $autoHideEnabled ) {
			return '';
		}

		$css = '';
		foreach ( $this->printedIcons as $iconSet ) {
			$iconSetId = isset( $iconSet['iconset_id'] ) ? $iconSet['iconset_id'] : '';
			$iconSetType = isset( $iconSet['iconset_type'] ) ? $iconSet['iconset_type'] : '';
			$iconSetUrl = isset( $iconSet['iconset_url'] ) ? $iconSet['iconset_url'] : '';
			$className = isset( $iconSet['class'] ) ? $iconSet['class'] : '';
			$image = isset( $iconSet['image'] ) ? $iconSet['image'] : '';

			if ( '' === $iconSetId || '' === $iconSetType || '' === $className || '' === $image ) {
				continue;
			}

			$css .= '.zmshbt.' . esc_attr( $iconSetId ) . '.' . esc_attr( $iconSetType ) .
				' .' . esc_attr( $className ) . " {background-image:url('" .
				esc_url( $iconSetUrl . $iconSetType . '/' . $image ) . "');}";
		}

		if ( ! $autoHideEnabled ) {
			$css .= '.zmshbt.left{left:0 !important;}.zmshbt.right{right:0 !important;}';
		}

		return '' === $css ? '' : '<style>' . $css . '</style>';
	}

	/**
	 * Keep the long-lived public compatibility object byte-compatible without
	 * imposing its historical whitespace on the canonical frontend response.
	 */
	public function historicalInlineIconStyles( $autoHideEnabled ) {
		if ( empty( $this->printedIcons ) ) {
			return '';
		}

		$css = '<style>';
		foreach ( $this->printedIcons as $iconSet ) {
			$iconSetId = isset( $iconSet['iconset_id'] ) ? $iconSet['iconset_id'] : '';
			$iconSetType = isset( $iconSet['iconset_type'] ) ? $iconSet['iconset_type'] : '';
			$iconSetUrl = isset( $iconSet['iconset_url'] ) ? $iconSet['iconset_url'] : '';
			$className = isset( $iconSet['class'] ) ? $iconSet['class'] : '';
			$image = isset( $iconSet['image'] ) ? $iconSet['image'] : '';

			if ( '' === $iconSetId || '' === $iconSetType || '' === $className || '' === $image ) {
				continue;
			}

			$css .= "\n\t\t\t.zmshbt." . esc_attr( $iconSetId ) . '.' . esc_attr( $iconSetType ) .
				' .' . esc_attr( $className ) . " {\n\t\t\t\t\tbackground-image:url('" .
				esc_url( $iconSetUrl . $iconSetType . '/' . $image ) . "');\n\t\t\t}\n\t\t\t";
		}
		if ( ! $autoHideEnabled ) {
			$css .= "\n\t\t\t\t.zmshbt.left{\n\t\t\t\t\tleft: 0 !important;\n\t\t\t\t}\n\t\t\t\t.zmshbt.right {\n\t\t\t\t\tright: 0 !important;\n\t\t\t\t}\n\t\t\t";
		}

		return $css . '</style>';
	}

	public function stylesheets() {
		return $this->stylesheets;
	}

	public function printedIcons() {
		return $this->printedIcons;
	}
}
