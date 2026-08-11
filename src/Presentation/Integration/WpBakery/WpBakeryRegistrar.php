<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\WpBakery;

use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\BuilderLabels;

/**
 * Registers the existing WPBakery element and normalizes its historic
 * established shortcode attributes without depending on a callback.
 */
final class WpBakeryRegistrar {
	private $renderer;
	private $settings;
	private $iconSets;
	private $networks;
	private $assets;
	private $config;

	public function __construct(
		RenderFacade $renderer,
		SettingsRepository $settings,
		IconSetRegistry $iconSets,
		NetworkRegistry $networks,
		AssetCollector $assets,
		PluginConfig $config
	) {
		$this->renderer = $renderer;
		$this->settings = $settings;
		$this->iconSets = $iconSets;
		$this->networks = $networks;
		$this->assets = $assets;
		$this->config = $config;
	}

	public function registerHooks() {
		add_action( $this->config->wpBakeryHook(), array( $this, 'registerElement' ) );
	}

	public function registerElement() {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$currentIconSet = $this->currentIconSet();
		vc_map(
			array(
				'name'        => __( 'Html Social Share', 'html-social-share-buttons' ),
				'description' => __( 'Html Social Share', 'html-social-share-buttons' ),
				'base'        => $this->config->wpBakeryBase(),
				'class'       => $this->config->wpBakeryClass(),
				'controls'    => 'full',
				'category'    => __( 'Content', 'html-social-share-buttons' ),
				'params'      => array(
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => '',
						'heading'     => __( 'Title', 'html-social-share-buttons' ),
						'param_name'  => 'title',
						'value'       => __( 'Share this page', 'html-social-share-buttons' ),
						'description' => __( 'Add social share button', 'html-social-share-buttons' ),
					),
					array(
						'type'        => 'dropdown',
						'holder'      => 'div',
						'class'       => '',
						'heading'     => __( 'Iconset', 'html-social-share-buttons' ),
						'param_name'  => 'iconset',
						'value'       => $this->iconSetChoices(),
						'description' => __( 'Select iconset to use', 'html-social-share-buttons' ),
					),
					array(
						'type'        => 'dropdown',
						'holder'      => 'div',
						'class'       => '',
						'heading'     => __( 'Iconset type', 'html-social-share-buttons' ),
						'param_name'  => 'iconset_type',
						'value'       => $currentIconSet->shapes(),
						'description' => __( 'Select iconset type', 'html-social-share-buttons' ),
					),
					array(
						'type'        => 'checkbox',
						'holder'      => 'div',
						'class'       => '',
						'heading'     => __( 'Icons', 'html-social-share-buttons' ),
						'param_name'  => 'icons',
						'value'       => $this->networkChoices( $currentIconSet->id() ),
						'description' => __( 'Select icons', 'html-social-share-buttons' ),
					),
				),
			)
		);
	}

	/** Historical bridge entry point. */
	public function integrate() {
		return $this->registerElement();
	}

	/**
	 * Direct canonical rendering for integrations that deserialize a WPBakery
	 * element without routing through the legacy shortcode callback.
	 */
	public function render( $attributes ) {
		$attributes = is_array( $attributes ) ? $attributes : array();
		$settings = $this->settings->load();
		$icons = $this->normalizeIcons(
			isset( $attributes['icons'] ) ? $attributes['icons'] : $this->defaultNetworks()
		);
		if ( empty( $icons ) ) {
			return '';
		}

		$outcome = $this->renderer->render(
			array(
				'title'         => sanitize_text_field(
					$this->scalar( isset( $attributes['title'] ) ? $attributes['title'] : '', '' )
				),
				'iconset'       => $this->iconSetId(
					isset( $attributes['iconset'] ) ? $attributes['iconset'] : $settings->iconSetId()
				),
				'iconset_type'  => sanitize_key(
					$this->scalar(
						isset( $attributes['iconset_type'] ) ? $attributes['iconset_type'] : $settings->defaultIconShape(),
						'square'
					)
				),
				'icons'         => $icons,
				'url'           => $this->url( isset( $attributes['url'] ) ? $attributes['url'] : '' ),
				'class'         => $this->config->wpBakeryWrapperClass(),
				'profile_links' => $this->profileLinks( $attributes, $settings ),
			)
		);
		$this->assets->collect( $outcome );

		return $outcome->html();
	}

	private function currentIconSet() {
		$settings = $this->settings->load();
		if ( $this->iconSets->has( $settings->iconSetId() ) ) {
			return $this->iconSets->get( $settings->iconSetId() );
		}

		return $this->iconSets->get( 'default' );
	}

	private function iconSetChoices() {
		$choices = array();
		foreach ( $this->iconSets->all() as $iconSet ) {
			$choices[ BuilderLabels::iconSet( $iconSet->id(), $iconSet->label() ) ] = $iconSet->id();
		}

		return $choices;
	}

	private function networkChoices( $iconSetId ) {
		$choices = array();
		$iconSet = $this->iconSets->get( $iconSetId );
		foreach ( $this->networks->all() as $network ) {
			if ( $iconSet->hasIcon( $network->id() ) ) {
				$label = BuilderLabels::wpBakeryNetwork( $network->id(), $network->label() );
				$choices[ $label ] = $network->id();
			}
		}

		return $choices;
	}

	private function defaultNetworks() {
		return array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' );
	}

	private function normalizeIcons( $icons ) {
		if ( is_string( $icons ) ) {
			$icons = explode( ',', $icons );
		}
		if ( ! is_array( $icons ) ) {
			return $this->defaultNetworks();
		}
		if ( empty( $icons ) ) {
			return array();
		}

		$keys = array_keys( $icons );
		$networkIds = range( 0, count( $keys ) - 1 ) === $keys ? $icons : $keys;
		$normalized = array();
		foreach ( $networkIds as $networkId ) {
			if ( ! is_scalar( $networkId ) ) {
				continue;
			}
			$networkId = sanitize_key( (string) $networkId );
			if ( 'twitter' === $networkId ) {
				$networkId = 'x';
			}
			if ( '' !== $networkId && $this->networks->has( $networkId ) ) {
				$normalized[ $networkId ] = 'on';
			}
		}

		return $normalized;
	}

	private function iconSetId( $iconSetId ) {
		$iconSetId = sanitize_key( $this->scalar( $iconSetId, 'default' ) );

		return $this->iconSets->has( $iconSetId ) ? $iconSetId : 'default';
	}

	private function url( $url ) {
		$url = trim( $this->scalar( $url, '' ) );
		return '%%permalink%%' === $url ? '' : esc_url_raw( $url );
	}

	private function profileLinks( array $attributes, $settings ) {
		if ( array_key_exists( 'profile_links', $attributes ) ) {
			return is_array( $attributes['profile_links'] ) ? $attributes['profile_links'] : array();
		}

		return $settings->profileLinks();
	}

	private function scalar( $value, $fallback ) {
		return is_scalar( $value ) ? (string) $value : (string) $fallback;
	}
}
