<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Shortcode;

use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetSelectionPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;

/**
 * Canonical implementation of the historical shortcode surface.
 *
 * The legacy global callback is intentionally a small bridge to this class.
 */
final class ShortcodeController {
	private $renderer;
	private $settings;
	private $iconSets;
	private $assets;
	private $config;

	public function __construct(
		RenderFacade $renderer,
		SettingsRepository $settings,
		IconSetRegistry $iconSets,
		AssetCollector $assets,
		PluginConfig $config
	) {
		$this->renderer = $renderer;
		$this->settings = $settings;
		$this->iconSets = $iconSets;
		$this->assets = $assets;
		$this->config = $config;
	}

	public function registerHooks() {
		if ( function_exists( 'add_shortcode' ) ) {
			foreach ( $this->config->shortcodeAliases() as $shortcode ) {
				add_shortcode( $shortcode, array( $this, 'render' ) );
			}
		}
	}

	public function register() {
		$this->registerHooks();
	}

	/**
	 * Preserve the historical shortcode attributes while forwarding a normalized
	 * request directly to the canonical renderer.
	 */
	public function render( $attributes = array(), $content = null, $tag = '' ) {
		$attributes = is_array( $attributes ) ? $attributes : array();
		$defaults = array(
			'title'              => '',
			'iconset'            => 'default',
			'url'                => '',
			'icons'              => array(
				'facebook'  => 'on',
				'x'         => 'on',
				'linkedin'  => 'on',
				'pinterest' => 'on',
				'mail'      => 'on',
			),
			'iconset_type'       => 'square',
			'class'              => 'in_shortcode',
			'profile_links_mode' => 'inherit',
		);
		$shortcode = in_array( $tag, $this->config->shortcodeAliases(), true )
			? $tag
			: $this->config->shortcode();
		$attributes = function_exists( 'shortcode_atts' )
			? shortcode_atts( $defaults, $attributes, $shortcode )
			: array_merge( $defaults, $attributes );

		$options = array(
			'title'              => $this->text( $attributes['title'], '' ),
			'iconset'            => $this->iconSet( $attributes['iconset'], 'default' ),
			'url'                => $this->url( $attributes['url'] ),
			'icons'              => $this->icons( $attributes['icons'] ),
			'iconset_type'       => $this->key( $attributes['iconset_type'], 'square' ),
			'class'              => $this->className( $attributes['class'], 'in_shortcode' ),
			'profile_links_mode' => $this->profileLinksMode( $attributes['profile_links_mode'] ),
			/* Existing shortcode calls inherited these links from the runtime settings. */
			'profile_links'      => $this->settings->load()->profileLinks(),
		);

		$outcome = $this->renderer->render( $options );
		$this->assets->collect( $outcome );

		return $outcome->html();
	}

	/**
	 * Public builder helper retained for existing Elementor/WPBakery bridges.
	 */
	public function resolveBuilderIconSet( $iconSet = 'inherit' ) {
		$iconSet = $this->key( $iconSet, 'inherit' );
		if ( 'inherit' !== $iconSet ) {
			return $this->iconSets->has( $iconSet ) ? $iconSet : 'default';
		}

		$settings = $this->settings->load();
		$inherited = $settings->iconSetId();

		return $this->iconSets->has( $inherited ) ? $inherited : 'default';
	}

	/**
	 * Public builder helper retained for existing integrations.
	 */
	public function builderIconSetOptions() {
		$options = array(
			'inherit' => __( 'Inherit from plugin settings', 'html-social-share-buttons' ),
		);
		$selectedId = $this->settings->load()->iconSetId();
		foreach ( IconSetSelectionPolicy::choices( $this->iconSets, $selectedId ) as $iconSet ) {
			$options[ $iconSet->id() ] = $this->iconSetLabel( $iconSet->id(), $iconSet->label() );
		}

		return $options;
	}

	private function icons( $icons ) {
		if ( ! is_array( $icons ) ) {
			$icons = explode( ',', (string) $icons );
		}

		$isList = ! empty( $icons ) && array_keys( $icons ) === range( 0, count( $icons ) - 1 );
		$icons = $isList ? $icons : array_keys( $icons );
		$normalized = array();
		foreach ( $icons as $icon ) {
			if ( ! is_scalar( $icon ) ) {
				continue;
			}
			$icon = $this->key( $icon, '' );
			$icon = 'twitter' === $icon ? 'x' : $icon;
			if ( '' !== $icon ) {
				$normalized[ $icon ] = 'on';
			}
		}

		return $normalized;
	}

	private function url( $value ) {
		$value = trim( $this->scalar( $value, '' ) );
		if ( '' === $value || '%%permalink%%' === $value ) {
			return '';
		}

		return function_exists( 'esc_url_raw' ) ? esc_url_raw( $value ) : $value;
	}

	private function text( $value, $fallback ) {
		$value = $this->scalar( $value, $fallback );

		return function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : $value;
	}

	private function iconSet( $value, $fallback ) {
		$value = $this->key( $value, $fallback );

		return 'inherit' === $value ? $this->resolveBuilderIconSet( $value ) : $value;
	}

	private function className( $value, $fallback ) {
		$value = $this->scalar( $value, $fallback );

		return function_exists( 'sanitize_html_class' ) ? sanitize_html_class( $value ) : $value;
	}

	private function profileLinksMode( $value ) {
		$value = strtolower( $this->scalar( $value, 'inherit' ) );

		return in_array( $value, array( 'inherit', 'none', 'custom' ), true ) ? $value : 'inherit';
	}

	private function key( $value, $fallback ) {
		$value = $this->scalar( $value, $fallback );

		return function_exists( 'sanitize_key' ) ? sanitize_key( $value ) : strtolower( $value );
	}

	private function scalar( $value, $fallback ) {
		return is_scalar( $value ) ? (string) $value : (string) $fallback;
	}

	private function iconSetLabel( $id, $fallback ) {
		switch ( (string) $id ) {
			case 'default':
				return __( 'Default (legacy)', 'html-social-share-buttons' );
			case 'flat':
				return __( 'Flat', 'html-social-share-buttons' );
			case 'long-shadows':
				return __( 'Long Shadows', 'html-social-share-buttons' );
			case 'prajin':
				return __( 'Prajin', 'html-social-share-buttons' );
			case 'bootstrap-solid':
				return __( 'Bootstrap Solid', 'html-social-share-buttons' );
			case 'tabler-outline':
				return __( 'Tabler Outline', 'html-social-share-buttons' );
			default:
				return (string) $fallback;
		}
	}
}
